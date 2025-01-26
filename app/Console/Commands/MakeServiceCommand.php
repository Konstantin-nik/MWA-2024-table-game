<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

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
    protected $description = 'Create a new Service class';

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
            $this->error('Service class already exists!');

            return;
        }

        $this->makeDirectory($path);
        $this->files->put($path, $this->buildClass($name));

        $this->info("Service class created successfully: {$name}");
    }

    protected function getPath($name)
    {
        return app_path("Services/{$name}.php");
    }

    protected function makeDirectory($path)
    {
        if (! $this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0755, true);
        }
    }

    protected function buildClass($name)
    {
        return "<?php\n\nnamespace App\Services;\n\nclass {$name}\n{\n    // Your service logic here\n}\n";
    }
}
