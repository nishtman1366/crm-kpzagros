<?php

namespace App\Notifications\Profiles;

use App\Channels\IpPanel;
use App\Exceptions\NotificationException;
use App\Models\Notifications\NotificationType;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminNotification extends Notification
{
    use Queueable;

    private $notificationId;
    private $options;
    private $pattern;
    private $title;
    private $body;

    /**
     * Create a new notification instance.
     *
     * @param $notificationId
     * @param array $options
     */
    public function __construct($notificationId, $options = [])
    {
        $this->notificationId = $notificationId;
        $this->options = $options;
        $notificationType = NotificationType::find($notificationId);
        if (is_null($notificationType)) throw new NotificationException();

        $this->pattern = $notificationType->pattern;
        $this->title = $notificationType->title;
        $this->body = $notificationType->body;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [IpPanel::class, 'database'];
    }

    public function toIpPanel($notifiable)
    {
        return [
            'patternCode' => $this->pattern,
            'patternValues' => $this->options,
            'error' => '',
            'message' => '',
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'title' => $this->title,
            'body' => $this->body
        ];
    }
}
