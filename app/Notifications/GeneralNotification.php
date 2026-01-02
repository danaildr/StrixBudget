<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GeneralNotification extends Notification
{

    /**
     * Заглавие на известието
     *
     * @var string
     */
    public $title;

    /**
     * Съобщение на известието
     *
     * @var string
     */
    public $message;

    /**
     * URL за пренасочване при клик върху известието
     *
     * @var string|null
     */
    public $url;

    /**
     * Икона на известието (име на икона от Heroicons)
     *
     * @var string
     */
    public $icon;

    /**
     * Създаване на ново известие
     *
     * @param string $title
     * @param string $message
     * @param string|null $url
     * @param string $icon
     * @return void
     */
    public function __construct($title, $message, $url = null, $icon = 'bell')
    {
        $this->title = $title;
        $this->message = $message;
        $this->url = $url;
        $this->icon = $icon;
    }

    /**
     * Връщане на каналите за изпращане на известието
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Връщане на масив с данни за базата данни
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'url' => $this->url,
            'icon' => $this->icon,
        ];
    }
}
