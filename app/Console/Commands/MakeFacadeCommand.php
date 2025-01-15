<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeFacadeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:facade {name} {service_name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new facade class based on a service class';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $namespace = 'App\\Facades';
        $serviceName = $this->argument('service_name');
        $class = $name;

        $stub = File::get(base_path('stubs/FacadeClass.stub'));
        $content = str_replace(
            ['{{ namespace }}', '{{ class }}', '{{ serviceName }}'],
            [$namespace, $class, $serviceName],
            $stub
        );

        $path = app_path("Facades/{$name}Facade.php");

        if (!File::exists(dirname($path))) {
            File::makeDirectory(dirname($path), 0755, true);
        }

        File::put($path, $content);

        $this->info("Facade {$path} created successfully.");
    }
}
