<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property CarbonInterface|Carbon $last_downloaded_at
 * @property CarbonInterface|Carbon $expires_at
 * @property int $property_id
 * @property string $aws_uri
 */
class PixVisionReport extends Model
{
    use SoftDeletes;
    protected $table = 'pixvision_reports';

    protected $fillable = [
        'property_id',
        'aws_uri',
        'last_downloaded_at',
        'expires_at',
    ];

    protected function updateLastDownloadDate(): void
    {
        $this->last_downloaded_at = now();
        $this->save();
    }
}
