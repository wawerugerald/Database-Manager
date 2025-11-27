<?php

namespace App\Services;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class DockerComposeService
{
    protected function run(array $cmd, string $cwd = null, int $timeout = 120)
    {
        $process = new Process($cmd, $cwd);
        $process->setTimeout($timeout);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $process->getOutput();
    }

    /**
     * Start the compose project
     */
    public function start(string $composePath, string $project)
    {
        // `docker compose -p <project> up -d`
        $cmd = ['docker', 'compose', '-p', $project, '-f', $composePath, 'up', '-d'];
        return $this->run($cmd, dirname($composePath));
    }

    /**
     * Stop the compose project
     */
    public function stop(string $composePath, string $project)
    {
        $cmd = ['docker', 'compose', '-p', $project, '-f', $composePath, 'down'];
        return $this->run($cmd, dirname($composePath));
    }

    /**
     * Get status via `docker compose ps --format json` (if available)
     * Fallback to `docker ps --filter label=com.docker.compose.project=<project> ...`
     */
    public function status(string $composePath, string $project)
    {
        // Try docker compose ps --format json (Docker Compose v2)
        $cmd = ['docker', 'compose', '-p', $project, '-f', $composePath, 'ps', '--format', 'json'];

        try {
            $output = $this->run($cmd, dirname($composePath));
            $arr = json_decode($output, true);
            // If array of services, check for running state
            $anyRunning = false;
            foreach ($arr as $svc) {
                if (!empty($svc['State']) && strtolower($svc['State']) === 'running') {
                    $anyRunning = true;
                    break;
                }
            }
            return $anyRunning ? 'running' : 'stopped';
        } catch (\Exception $e) {
            // fallback: use docker ps filter on label
            try {
                $cmd2 = ['docker', 'ps', '--filter', 'label=com.docker.compose.project=' . $project, '--format', '{{.Status}}'];
                $out2 = $this->run($cmd2);
                if (trim($out2) === '') return 'stopped';
                return 'running';
            } catch (\Exception $ex) {
                return 'unknown';
            }
        }
    }
}
