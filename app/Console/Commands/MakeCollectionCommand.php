<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeCollectionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:collection {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new collection class for extending model TModelCollection';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $namespace = 'App\\Collections';
        $class = $name;

        $stub = File::get(base_path('stubs/CollectionClass.stub'));
        $content = str_replace(
            ['{{ namespace }}', '{{ class }}'],
            [$namespace, $class],
            $stub
        );

        $path = app_path("Collections/{$name}Collection.php");

        if (!File::exists(dirname($path))) {
            File::makeDirectory(dirname($path), 0755, true);
        }

        File::put($path, $content);

        $modelPath = app_path("Models/{$name}.php");
        if (File::exists($modelPath)) {
            $modelContent = File::get($modelPath);

            $modelCodeImport = "use App\\Collections\\{$class}Collection;";

            if (strpos($modelContent, $modelCodeImport) === false) {
                // Add import statement to model
                $modelContent = preg_replace(
                    '/namespace App\\\Models;/',
                    "namespace App\\Models;\n\n" . $modelCodeImport,
                    $modelContent
                );

                File::put($modelPath, $modelContent);
            }

            // Add newCollection method to model
            $modelCodeTemplate = "
    public function newCollection(array \$models = [])
    {
        return new {$class}Collection(\$models);
    }
            ";

            $modelContent = preg_replace(
                '/\}\s*$/',
                $modelCodeTemplate . "\n}",
                $modelContent
            );

            File::put($modelPath, $modelContent);
            $this->info("newCollection method added to {$name} model.");
        } else {
            $this->info("Model {$name} does not exist.");
            $this->info("Please add newCollection method to {$name} model manually.");
            $this->info('public function newCollection(array $models = [])');
            $this->info("{");
            $this->info("    return new {$class}(\$models);");
            $this->info("}");
        }

        $this->info("Collection {$path} created successfully.");
    }
}
