<?php

namespace App\Jobs;

use App\Models\PixVisionReport;
use App\Models\Property;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use App\Infrastructure\PixVision\UseCases\GeneratePixVisionReport as GeneratePixVisionReportUseCase;
use RuntimeException;

class GeneratePixVisionReport implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected Property $property, protected  User $user)
    {

    }

    /**
     * Execute the job.
     * @throws Exception
     */
    public function handle(): void
    {
        $useCase =  new GeneratePixVisionReportUseCase($this->property);

        $reportData = $useCase->execute();
        $html = view('report.index', $reportData)
            ->render();

        $dompdf = Pdf::loadHTML($html)
            ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
        ])
            ->setPaper('A4');

        $dompdf->render();
        $canvas = $dompdf->getCanvas();
        $img = $reportData['logo'];// watermark image
        $this->setWaterMark($canvas, $img);
        $tmpPath = storage_path('app/tmp');
        if (!is_dir($tmpPath) && !mkdir($tmpPath, 0755, true) && !is_dir($tmpPath)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $tmpPath));
        }

        $reportName = "report_{$this->property->id}.pdf";
        $localPath = "{$tmpPath}/{$reportName}";
        $dompdf->save($localPath); //save to temporal local path;
        $s3Path = "reports/{$this->user->id}/{$reportName}";
        $url = Storage::disk('s3')->url($s3Path);
        $awsPath = Storage::disk('s3')->put(
            $s3Path,
            file_get_contents($localPath),
            [
                'ContentType' => 'application/pdf',
                'CacheControl' => 'max-age=7776000'
            ]
        );
        if(!$awsPath){
            throw new RuntimeException('Failed to upload report to S3');
        }

       PixVisionReport::create([
            'property_id' => $this->property->id,
            'aws_uri' => $url,
            'expires_at' => now()->addDays(7),
       ]);
       @unlink($localPath);
    }

    protected function setWaterMark($canvas, $img): void
    {
        $width = $canvas->get_width();
        $height = $canvas->get_height();
        $canvas->page_script(function ($pageNumber, $pageCount, $canvas) use ($img, $width, $height) {
            $canvas->save(); // save current state
            $canvas->set_opacity(0.1);
            $canvas->translate($width / 2, $height / 2);
            $canvas->image($img, -($width / 4), -($height / 6), $width / 2, $height / 3);
            $canvas->restore(); // restore state
        });
    }
}
