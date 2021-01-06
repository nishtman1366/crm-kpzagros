<?php

namespace App\Notifications;

use App\Channels\IpPanel;
use App\Libraries\TemplateEngine;
use App\Models\Notifications\Type;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProfileNotification extends Notification
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
     * @param Type $type
     * @param array $options
     */
    public function __construct(Type $type, $options = [])
    {
        $this->options = $options;
        $this->pattern = $type->pattern;
        $this->title = $type->title;
        $this->body = $type->body;
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
        $values = [];
        foreach ($this->options as $key => $value) {
            $search = strpos($this->body, $key);
            if ($search) {
                $values[$key] = $value;
            }
        }
        return [
            'patternCode' => $this->pattern,
            'patternValues' => $values,
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
        $body = TemplateEngine::getTemplate($this->body)
            ->setValues($this->options)
            ->render();
        return [
            'title' => $this->title,
            'body' => $body
        ];
    }
}
