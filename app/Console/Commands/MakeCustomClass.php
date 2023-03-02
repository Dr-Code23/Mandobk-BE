<?php

namespace App\Console\Commands;

use App\Traits\StringTrait;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;

class MakeCustomClass extends Command
{
    use StringTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature ='make:custom_class';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description ='Make Custom Class Like Trait , Service ... etc';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $className = trim($this->ask("What is Class Name"));

        if(!$className){
            $this->error('Class Name Cannot Be Empty');
            return CommandAlias::FAILURE;
        }
        $nameSpace = trim($this->ask("What is Class NameSpace "));
        if(!$nameSpace){
            $this->error('Name Space Cannot Be Empty');
        }
        $stubName = strtolower($this->ask(
            'Which Stub Name You Want ? , if null , class name will be used'
        )?:$className);

        $overrideIfExists = $this->choice(
            'Override Files If Exists' ,
            [ 'no','yes'] , 0);


        // Check If The File Exists
        if(
            is_file(__DIR__.'/../../../stubs/'.$stubName.'.stub') ||
            is_file(__DIR__.'/../../../'.$nameSpace.'/'.$className.'.php')
        ){
            if($overrideIfExists){
                $this->error('File Exists');
                return CommandAlias::FAILURE;
            }
        }

        // Start Generating The Stub File
        $handle = fopen(__DIR__.'/../../../stubs/'.$stubName.'.stub' , 'w');

        $stubContent = str_replace(
            '{{nameSpace}}' ,
            $nameSpace ,
            file_get_contents(__DIR__ . '/../../../stubs/standard.stub')
        );
        fwrite($handle , $stubContent);
        fclose($handle);

        // Start Creating The Command PHP File
        $handle = fopen(__DIR__.'/../../../'.$nameSpace.'/'.$className.'.php', 'w');

        fwrite(
            $handle,
            str_replace(

            )
        );
        fclose($handle);
        return 1;
    }
}
