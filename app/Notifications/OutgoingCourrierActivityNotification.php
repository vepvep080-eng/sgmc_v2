<?php

namespace App\Notifications;

use App\Models\OutgoingCourrier;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OutgoingCourrierActivityNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly OutgoingCourrier $courrier,
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
            'outgoing_courrier_id' => $this->courrier->id,
            'outgoing_courrier_numero' => $this->courrier->numero,
            'url' => route('courriers-depart.show', $this->courrier),
        ];
    }
}
