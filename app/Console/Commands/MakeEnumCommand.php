<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakeEnumCommand extends Command
{
    protected $signature = 'make:enum {name}';
    protected $description = 'Create a new Enum class';

    protected $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $path = $this->getPath($name);

        if ($this->files->exists($path)) {
            $this->error("Enum class already exists!");
            return;
        }

        $this->makeDirectory($path);
        $this->files->put($path, $this->buildClass($name));

        $this->info("Enum class created successfully: {$name}");
    }

    protected function getPath($name)
    {
        return app_path("Enums/{$name}.php");
    }

    protected function makeDirectory($path)
    {
        if (!$this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0755, true);
        }
    }

    protected function buildClass($name)
    {
        return "<?php\n\nnamespace App\Enums;\n\nenum {$name}\n{\n    // Define your cases here\n    // Example:\n    // case CASE_NAME;\n}\n";
    }
}
