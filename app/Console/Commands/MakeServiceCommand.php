<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeServiceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new service class';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $namespace = 'App\\Http\\Services';
        $class = $name;

        $stub = File::get(base_path('stubs/ServiceClass.stub'));
        $content = str_replace(
            ['{{ namespace }}', '{{ class }}'],
            [$namespace, $class],
            $stub
        );

        $path = app_path("Http/Services/{$name}Service.php");

        if (!File::exists(dirname($path))) {
            File::makeDirectory(dirname($path), 0755, true);
        }

        File::put($path, $content);

        $this->info("Service {$path} created successfully.");
    }
}
