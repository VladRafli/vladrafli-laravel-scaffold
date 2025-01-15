<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeContractCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:contract {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new contract class for facade implementation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $namespace = 'App\\Contracts';
        $class = $name;

        $stub = File::get(base_path('stubs/ContractClass.stub'));
        $content = str_replace(
            ['{{ namespace }}', '{{ class }}'],
            [$namespace, $class],
            $stub
        );

        $path = app_path("Contracts/{$name}Contract.php");

        if (!File::exists(dirname($path))) {
            File::makeDirectory(dirname($path), 0755, true);
        }

        File::put($path, $content);

        $this->info("Contract {$path} created successfully.");
    }
}
