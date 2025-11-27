<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DatabaseInstance;
use App\Services\DockerComposeService;
use App\Events\DatabaseStatusChanged;
use App\Notifications\DatabaseStatusNotification;
use Illuminate\Support\Facades\Notification;

class CheckDatabasesStatus extends Command
{
    protected $signature = 'dbmanager:check-status';
    protected $description = 'Check all database instances and emit events if their status changed';
    protected $docker;

    public function __construct(DockerComposeService $docker)
    {
        parent::__construct();
        $this->docker = $docker;
    }

    public function handle()
    {
        $instances = DatabaseInstance::all();
        foreach ($instances as $instance) {
            $old = $instance->status;
            $new = $this->docker->status($instance->compose_path, $instance->project);
            if ($new !== $old) {
                $instance->update([
                    'status' => $new,
                    'last_message' => "Status changed from {$old} to {$new} at " . now()
                ]);
                event(new DatabaseStatusChanged($instance));
                // optionally notify
                // Notification::route('mail','ops@example.com')->notify(new DatabaseStatusNotification($instance));
                $this->info("Instance {$instance->name}: {$old} -> {$new}");
            }
        }
        return 0;
    }
}
