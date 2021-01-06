<?php

namespace App\Http\Controllers\Notifications;

use App\Http\Controllers\Controller;
use App\Models\Notifications\Event;
use App\Models\User;
use App\Notifications\ProfileNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    public static function handleProfileNotifications($type, $container, $user)
    {
        $events = Event::with('type')
            ->where('event_type', $type)
            ->where('status', 1)
            ->where('event', $container->status)
            ->get();
        $users = [];
        foreach ($events as $event) {
            $notifiableUser = null;
            if ($event->level == 'SUPERUSER') {
                $notifiableUser = User::find(1);
            } elseif ($event->level == 'ADMIN') {
                $notifiableUser = User::where('level', 'ADMIN')->get()->first();
            } elseif ($event->level == 'AGENT') {
                $profileUser = $container->user;
                if ($profileUser->isSuperuser() || $profileUser->isAdmin() || $profileUser->isAgent()) {
                    $notifiableUser = $profileUser;
                } elseif ($profileUser->isMarketer()) {
                    $notifiableUser = $profileUser->parent;
                }
            } elseif ($event->level == 'MARKETER') {
                $notifiableUser = $container->user;
            } elseif ($event->level == 'CUSTOMER') {
                if ($type == 'PROFILES') {
                    $notifiableUser = $container->customer;
                } elseif ($type == 'REPAIRS') {
                    $notifiableUser = $container;
                }

            }

            if (!is_null($notifiableUser) && $notifiableUser->id !== $user->id && !in_array($notifiableUser->id, $users)) {
                $options = static::getContainerOptions($container, $type);
                $notifiableUser->notifyNow(new ProfileNotification($event->type, $options));
                $users[] = $notifiableUser->id;
            }
        }
    }

    public static function getContainerOptions($container, $type)
    {
        $options = [];
        if ($type == 'PROFILES') {
            $options = [
                'customer_name' => (string)$container->customer->fullName,
                'customer_mobile' => (string)$container->customer->mobile,
                'marketer_name' => (string)$container->user->name,
                'marketer_mobile' => (string)$container->user->mobile,
                'serial' => is_null($container->device) ? '' : (string)$container->device->serial,
                'device_type' => is_null($container->deviceType) ? '' : (string)$container->deviceType->name,
                'psp_name' => is_null($container->psp) ? '' : (string)$container->psp->name,
                'terminal_id' => (string)$container->terminal_id,
                'merchant_id' => (string)$container->merchant_id,
                'status' => (string)$container->statusText,
            ];
        } elseif ($type == 'REPAIRS') {
            $options = [
                'customer_name' => (string)$container->name,
                'marketer_name' => 'تقی پرنده',
                'customer_mobile' => (string)$container->mobile,
                'serial' => (string)$container->serial,
                'device_type' => is_null($container->deviceType) ? '' : (string)$container->deviceType->name,
                'psp_name' => is_null($container->psp) ? '' : (string)$container->psp->name,
                'location' => is_null($container->location) ? '' : (string)$container->location->name,
                'price' => (string)$container->price,
                'tracking_code' => (string)$container->tracking_code,
                'status' => (string)$container->statusText,
            ];
        }

        return $options;
    }
}
