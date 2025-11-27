<?php

namespace App\Http\Controllers;

use App\Models\DatabaseInstance;
use App\Services\DockerComposeService;
use Illuminate\Http\Request;
use App\Events\DatabaseStatusChanged;
use App\Notifications\DatabaseStatusNotification;
use Illuminate\Support\Facades\Notification;

class DatabaseController extends Controller
{
    protected $docker;

    public function __construct(DockerComposeService $docker)
    {
        $this->docker = $docker;
    }

    public function index()
    {
        $instances = DatabaseInstance::all();
        return view('dashboard', compact('instances'));
    }

    public function status(Request $request, $id)
    {
        $instance = DatabaseInstance::findOrFail($id);
        $status = $this->docker->status($instance->compose_path, $instance->project);
        $instance->update(['status' => $status, 'last_message' => now()]);
        return response()->json(['status' => $status]);
    }

    public function start(Request $request, $id)
    {
        $instance = DatabaseInstance::findOrFail($id);
        $instance->update(['status' => 'starting']);

        try {
            $this->docker->start($instance->compose_path, $instance->project);

            // small wait then fetch actual status
            sleep(2);
            $status = $this->docker->status($instance->compose_path, $instance->project);
            $instance->update(['status' => $status, 'last_message' => 'Started at '.now()]);

            // fire event + notification
            event(new DatabaseStatusChanged($instance));
            // optionally notify admins - using Notification::route or real user model
            // Notification::route('mail', config('mail.from.address'))->notify(new DatabaseStatusNotification($instance));
            return response()->json(['status' => $status]);
        } catch (\Exception $e) {
            $instance->update(['status' => 'error', 'last_message' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function stop(Request $request, $id)
    {
        $instance = DatabaseInstance::findOrFail($id);
        $instance->update(['status' => 'stopping']);

        try {
            $this->docker->stop($instance->compose_path, $instance->project);

            sleep(1);
            $status = $this->docker->status($instance->compose_path, $instance->project);
            $instance->update(['status' => $status, 'last_message' => 'Stopped at '.now()]);

            event(new DatabaseStatusChanged($instance));
            return response()->json(['status' => $status]);
        } catch (\Exception $e) {
            $instance->update(['status' => 'error', 'last_message' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
