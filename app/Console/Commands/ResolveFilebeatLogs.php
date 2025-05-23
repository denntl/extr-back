<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ResolveFilebeatLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:resolve-filebeat-logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $loggers = config('logging.channels');


        foreach ($loggers as $name => $logger) {
            if (!isset($logger['driver']) || !in_array($logger['driver'], ['single', 'daily'])) {
                continue;
            }

            $filepath = $logger['path'];
            $symlinkPath = dirname($filepath) . "/filebeat-$name.log";

            if ($logger['driver'] === 'daily') {
                $date = now()->format('Y-m-d');
                $dailyLogFileName = str_replace('.log', "-$date.log", basename($filepath));

                $filepath = dirname($filepath) . "/$dailyLogFileName";
            }

            if (!is_dir(dirname($filepath))) {
                mkdir(dirname($filepath), 0777, true);
            }

            // create log file if it is not exists
            if (!file_exists($filepath)) {
                file_put_contents($filepath, '');
                chmod($filepath, 0777);
            }

            // Remove the symlink if it exists
            if (file_exists($symlinkPath)) {
                unlink($symlinkPath);
            }

            symlink($filepath, $symlinkPath);
        }
    }
}
