<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class DatabaseStatusNotification extends Notification
{
    protected $instance;

    public function __construct($instance)
    {
        $this->instance = $instance;
    }

    public function via($notifiable)
    {
        return config('dbmanager.notify_channels', ['mail','broadcast']);
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Database status changed: ' . $this->instance->name)
            ->line("Instance: {$this->instance->name} ({$this->instance->type})")
            ->line("Status: {$this->instance->status}")
            ->line("Time: " . now())
            ->line('View dashboard for details');
    }

    public function toArray($notifiable)
    {
        return [
            'id' => $this->instance->id,
            'name' => $this->instance->name,
            'type' => $this->instance->type,
            'status' => $this->instance->status,
            'last_message' => $this->instance->last_message,
        ];
    }
}
