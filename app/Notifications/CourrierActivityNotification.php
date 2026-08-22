<?php

namespace App\Notifications;

use App\Models\Courrier;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CourrierActivityNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Courrier $courrier,
        private readonly string $event,
        private readonly string $title,
        private readonly string $message,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'event' => $this->event,
            'title' => $this->title,
            'message' => $this->message,
            'courrier_id' => $this->courrier->id,
            'courrier_numero' => $this->courrier->numero,
            'url' => route('courriers.show', $this->courrier),
        ];
    }
}