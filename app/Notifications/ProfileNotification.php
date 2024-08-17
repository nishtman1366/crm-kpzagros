<?php

namespace App\Notifications;

use App\Channels\SmsIr;
use App\Libraries\TemplateEngine;
use App\Models\Notifications\Type;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class ProfileNotification extends Notification
{
    use Queueable;

    private $notificationId;
    private $options;
    private $pattern;
    private $title;
    private $body;
    private $systemMessage;

    /**
     * Create a new notification instance.
     *
     * @param Type $type
     * @param array $options
     * @param bool $systemMessage
     */
    public function __construct(Type $type, array $options = [], $systemMessage = true)
    {

        $this->options = $options;
        $this->pattern = $type->pattern;
        $this->title = $type->title;
        $this->body = $type->body;
        $this->systemMessage = $systemMessage;
    }

    public function viaQueues()
    {
        Log::channel('daily')->error(SmsIr::class);

        return [
            SmsIr::class => 'notificationsQueue',
            'database' => 'notificationsQueue',
        ];
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $channels = [SmsIr::class];
        if ($this->systemMessage === true) {
            $channels[] = 'database';
        }
        Log::channel('daily')->error(json_encode($channels));

        return $channels;
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
        Log::channel('daily')->error(json_encode($values));

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
