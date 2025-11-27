<?php
return [
    // Path where per-instance compose files live (absolute or relative to project root)
    'compose_base_path' => env('DBM_COMPOSE_PATH', base_path('docker')),
    // How often (in seconds) scheduler checks statuses
    'status_check_interval' => env('DBM_STATUS_INTERVAL', 60),
    // Notification channels default
    'notify_channels' => ['mail','database','broadcast'],
];
