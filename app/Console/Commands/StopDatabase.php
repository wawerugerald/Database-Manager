<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class StopDatabase extends Command
{
    protected $signature = 'db:stop {type : mysql|postgresql|mongodb|mariadb}';
    protected $description = 'Stop a database service';

    public function handle()
    {
        $type = $this->argument('type');
        $service = $this->resolveServiceName($type);

        $process = new Process(["sudo", "systemctl", "stop", $service]);
        $process->run();

        if ($process->isSuccessful()) {
            $this->info("Database service '{$service}' stopped successfully.");
        } else {
            $this->error("Failed to stop '{$service}'.");
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
