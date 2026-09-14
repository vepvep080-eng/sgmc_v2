<?php

namespace App\Services;

use App\Models\Courrier;
use App\Models\OutgoingCourrier;
use App\Models\Role;
use App\Models\Routing;
use App\Models\User;
use App\Notifications\CourrierActivityNotification;
use App\Notifications\OutgoingCourrierActivityNotification;
use Illuminate\Support\Facades\Notification;

class CourrierNotificationService
{
    public function routed(Routing $routing): void
    {
        $courrier = $routing->courrier()->with('service')->first();

        if ($courrier === null) {
            return;
        }

        // Étape 1 (vers département) : notifier le chef du département ciblé.
        if ($routing->service_destination_id === null && $routing->department_id !== null) {
            $managerId = \App\Models\Department::whereKey($routing->department_id)->value('manager_id');

            $recipients = User::query()
                ->where('is_active', true)
                ->when($managerId, fn ($q) => $q->whereKey($managerId), fn ($q) => $q->whereRaw('1 = 0'))
                ->get();

            $this->send(
                $recipients,
                $courrier,
                'routed',
                'Nouveau courrier reçu',
                "Le courrier {$courrier->numero} a été envoyé à votre département."
            );

            return;
        }

        $recipients = User::query()
            ->where('is_active', true)
            ->where(function ($query) use ($routing) {
                $query->when(
                    $routing->recipient_user_id,
                    fn ($query, $recipientId) => $query->whereKey($recipientId),
                    fn ($query) => $query->where('service_id', $routing->service_destination_id)
                        ->whereHas('role', fn ($role) => $role->where('slug', Role::CHEF_SERVICE))
                );
            })
            ->get();

        $this->send(
            $recipients,
            $courrier,
            'routed',
            'Nouveau courrier routé',
            "Le courrier {$courrier->numero} vous a été adressé."
        );
    }

    public function processed(Courrier $courrier): void
    {
        $this->send(
            $this->managementRecipients(),
            $courrier,
            'processed',
            'Courrier traité',
            "Le courrier {$courrier->numero} a été traité."
        );
    }

    public function archived(Courrier $courrier): void
    {
        $this->send(
            $this->managementRecipients(),
            $courrier,
            'archived',
            'Courrier archivé',
            "Le courrier {$courrier->numero} a été archivé."
        );
    }

    public function outgoingSent(OutgoingCourrier $courrier): void
    {
        $this->sendOutgoing(
            $this->managementRecipients(),
            $courrier,
            'sent',
            'Courrier départ envoyé',
            "Le courrier départ {$courrier->numero} a été envoyé."
        );
    }

    public function outgoingArchived(OutgoingCourrier $courrier): void
    {
        $this->sendOutgoing(
            $this->managementRecipients(),
            $courrier,
            'archived',
            'Courrier départ archivé',
            "Le courrier départ {$courrier->numero} a été archivé."
        );
    }

    private function managementRecipients()
    {
        return User::query()
            ->where('is_active', true)
            ->whereHas('role', fn ($query) => $query->whereIn('slug', [Role::ADMIN, Role::BUREAU_ORDRE]))
            ->get();
    }

    private function send($recipients, Courrier $courrier, string $event, string $title, string $message): void
    {
        if ($recipients->isNotEmpty()) {
            Notification::send($recipients, new CourrierActivityNotification($courrier, $event, $title, $message));
        }
    }

    private function sendOutgoing($recipients, OutgoingCourrier $courrier, string $event, string $title, string $message): void
    {
        if ($recipients->isNotEmpty()) {
            Notification::send($recipients, new OutgoingCourrierActivityNotification($courrier, $event, $title, $message));
        }
    }
}
