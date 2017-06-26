<?php
/**
 * Created by PhpStorm.
 * User: jeremydillenbourg
 * Date: 26/06/2017
 * Time: 09:48
 */

namespace Famousinteractive\ContentApi\Commands;
use Illuminate\Console\Command;

class Collector extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'famousContentApi:collect';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Collect all the fitrans and send them to the platform';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $extensionToScan = [];
        $directoryToScan = [];

        $this->info('Collector fitrans() : START !');
        $extensionToScan[] = $this->ask('Extension to scan :', 'php');

        do {
            if($continue = $this->confirm('Continue to add extension ? ')) {
                $directoryToScan[] = $this->ask('Extra extension to scan :');
            }
        } while($continue == true);

        $this->info('This script will now ask you the directories to scan. It\'ll crawl them recursivly');
        $directoryToScan[] = $this->ask('Path to app/Http (set -1 to bypass it):',app_path('Http'));
        $directoryToScan[] = $this->ask('Path to the view (set -1 to bypass it):',resource_path('views'));
        do {
            if($continue = $this->confirm('Continue to add directory ? ')) {
                $directoryToScan[] = $this->ask('Add extra path to scan :',base_path());
            }
        } while ($continue == true);

        foreach($directoryToScan as $k=>$directory) {
            if(!empty($directory)) {
                $this->alert('Start scanning ' . $directory);

                if (is_dir($directory)) {
                    $collector = new \Famousinteractive\ContentApi\Library\Collector($directory, $extensionToScan);

                    foreach($collector->collect() as $fileName=>$fileContents) {

                        $this->info('File ' . $fileName);

                        foreach($fileContents as $k=>$v) {

                            if ($v['success']) {

                                $this->info('Collecting ' . $v['trans'] . ' with key ' . $v['key']);

                                if ($v['isVariable']) {
                                    $continue = $this->confirm('This key ' . $v['key'] . ' has a variable part, do you want to set it manually ? (If no, we\'ll remove it from the list');

                                    while ($continue) {
                                        $newKey = $this->ask('This key ' . $v['key'] . ' has a variable part, please set the values for this keys. Use "-1" to quit');

                                        if(empty($newKey) || $newKey == -1) {
                                            $continue = false;
                                        } else {
                                            $collector->setTransList($newKey, $v['default']);
                                            $this->info($newKey . ' added !');
                                        }
                                    }
                                } else {
                                    $collector->setTransList($v['key'], $v['default']);
                                    $this->info($v['key'] . ' added');
                                }

                            } else {
                                $this->warn($v['message']);
                            }
                        }
                    }

                    $this->alert('Scanning done !');

                    if($this->confirm('Do you want to check all key before sending ?')) {
                        foreach($collector->getTransList() as $v) {
                            $this->info( $v['key'] . ' | default : '.$v['default']);
                        }
                    }

                    if($this->confirm('Do you want to sent the list of key to the platform ? ')) {
                        $this->alert( 'Sending ' .  ($collector->push() ? 'done' : 'failed') );
                    }

                } else {
                    $this->warn('The directory is not readable or don\'t exists');
                }
            }
        }
    }

}