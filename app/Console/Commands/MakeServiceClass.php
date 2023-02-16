<?php

namespace App\Console\Commands;


use Illuminate\Console\Command;
use Touhidurabir\StubGenerator\Facades\StubGenerator;


class MakeServiceClass extends Command
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
    protected $description = 'Make New Service Class';



    // public function __construct(
    //     protected FileSystem $fileSystem
    // ) {
    // }
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $path = explode('/', $this->argument('name'));
        $fileName = $path[count($path) - 1];
        unset($path[count($path) - 1]);
        $path = implode('\\', $path);
        if (!is_dir(__DIR__ . '/../../Services/' . $path)) mkdir(__DIR__ . '/../../Services/' . $path, 0777, true);
        if (is_file(__DIR__ . '/../../Services/' . $path . '/' . $fileName . '.php')) {
            $this->error("\n" . '  Same service class is already exists in App\Http\Services\\' . $path . '\\' . $fileName . ".php\n");
            return Command::FAILURE;
        }
        StubGenerator::from(
            __DIR__ . '/../../../stubs/service.stub',
            true
        )
            ->to(__DIR__ . '/../../Services/' . $path, false, true)
            ->as($fileName)
            ->withReplacers([
                'namespace' => $path ? "\\$path" : '',
                'class' => $fileName
            ])
            ->ext('php')
            ->save();
        $this->info("\n Service Created Successfully in App\Http\Services\\" . $path . '\\' . $fileName . ".php\n");
        return Command::SUCCESS;
    }
}
