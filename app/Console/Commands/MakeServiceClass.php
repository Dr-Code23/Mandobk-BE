<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;

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

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $path = explode('/', $this->argument('name'));
        $fileName = $path[count($path) - 1];
        unset($path[count($path) - 1]);
        $path = implode('\\', $path);
        if (! is_dir(__DIR__.'/../../Services/'.$path)) {
            mkdir(__DIR__.'/../../Services/'.$path, 0777, true);
        }
        if (is_file(__DIR__.'/../../Services/'.$path.'/'.$fileName.'.php')) {
            $this->error(
                "\n  Same service class is already exists in App\Http\Services\\$path\\$fileName.php\n"
            );

            return CommandAlias::FAILURE;
        }
        $stubContent = str_replace(
            '{{namespace}}',
            $path ? "\\$path" : '',
            str_replace(
                '{{class}}',
                $fileName,
                file_get_contents(__DIR__.'/../../../stubs/service.stub')
            )
        );

        // Make The Class
        $handle = fopen(__DIR__.'/../../Services/'.$path."/$fileName.php", 'w');
        fwrite($handle, $stubContent);
        fclose($handle);

        $this->info(
            "\n Service Created Successfully in App\Http\Services\\".$path.'\\'.$fileName.".php\n"
        );

        return CommandAlias::SUCCESS;
    }
}
