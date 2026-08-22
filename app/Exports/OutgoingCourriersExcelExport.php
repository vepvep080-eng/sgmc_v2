<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OutgoingCourriersExcelExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct(private Collection $courriers) {}
    public function collection(): Collection { return $this->courriers; }
    public function headings(): array { return ['Numero', 'Objet', 'Destinataire', 'Organisme', 'Service', 'Date envoi', 'Mode envoi', 'Priorite', 'Statut']; }
    public function map($courrier): array { return [$courrier->numero, $courrier->objet, $courrier->destinataire, $courrier->organisme_destinataire, $courrier->service?->name, $courrier->date_envoi?->format('d/m/Y'), $courrier->mode_envoi, $courrier->priorityLabel(), $courrier->statusLabel()]; }
}
