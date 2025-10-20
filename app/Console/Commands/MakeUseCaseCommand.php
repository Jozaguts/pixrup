<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use phpDocumentor\Reflection\Types\This;

class MakeUseCaseCommand extends Command
{
    protected $signature = 'make:usecase {context} {name}';
    protected $description = 'Generate a new Use Case class following Hexagonal Architecture';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $context = ucfirst($this->argument('context'));
        $name = ucfirst($this->argument('name'));
        $basePath = base_path("app/Application/{$context}/UseCases");

        if (!File::exists($basePath)) {
            File::makeDirectory($basePath, 0755, true);
            $this->info("Created directory: {$basePath}");
        }
        $filePath = "{$basePath}/{$name}.php";

        if (File::exists($filePath)) {
            $this->error("UseCase {$name} already exists in {$context}.");
            return $this::FAILURE;
        }
        $stub = File::get(base_path('stubs/usecase.stub'));
        $content = str_replace(
            ['{{ context }}', '{{ name }}'],
            [$context, $name],
            $stub
        );
        File::put($filePath, $content);
        $this->info("✅ UseCase {$name} created successfully in context {$context}.");
        return $this::SUCCESS;
    }
}
