<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class StartDatabase extends Command
{
    protected $signature = 'db:start {type : mysql|postgresql|mongodb|mariadb}';
    protected $description = 'Start a database service';

    public function handle()
    {
        $type = $this->argument('type');
        $service = $this->resolveServiceName($type);

        $process = new Process(["sudo", "systemctl", "start", $service]);
        $process->run();

        if ($process->isSuccessful()) {
            $this->info("Database service '{$service}' started successfully.");
        } else {
            $this->error("Failed to start '{$service}'.");
            $this->error($process->getErrorOutput());
        }
    }

    private function resolveServiceName(string $type): string
    {
        return match ($type) {
            'mysql'      => 'mysql',
            'mariadb'    => 'mariadb',
            'postgresql' => 'postgresql',
            'mongodb'    => 'mongod',
            default      => throw new \Exception("Unsupported database: {$type}")
        };
    }
}
