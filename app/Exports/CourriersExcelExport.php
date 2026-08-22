<?php

namespace App\Exports;

use App\Models\Courrier;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CourriersExcelExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    public function __construct(private Collection $courriers)
    {
    }

    public function collection(): Collection
    {
        return $this->courriers;
    }

    public function headings(): array
    {
        return [
            "Num\xC3\xA9ro",
            'Objet',
            "Exp\xC3\xA9diteur",
            'Service',
            "Date de r\xC3\xA9ception",
            "Priorit\xC3\xA9",
            'Statut',
            'Date de traitement',
            "Date d'archivage",
        ];
    }

    public function map($courrier): array
    {
        return [
            $courrier->numero,
            $courrier->objet,
            $courrier->expediteur,
            $courrier->service?->name,
            $courrier->date_reception?->format('d/m/Y'),
            $courrier->priorityLabel(),
            $courrier->statusLabel(),
            $courrier->traite_le?->format('d/m/Y H:i'),
            $courrier->archived_at?->format('d/m/Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}