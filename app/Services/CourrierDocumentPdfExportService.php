<?php

namespace App\Services;

use App\Models\Courrier;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\Response;

class CourrierDocumentPdfExportService
{
    public function __construct(private ArabicPdfTextService $arabic)
    {
    }

    public function download(Courrier $courrier): Response
    {
        $generatedAt = now();

        return Pdf::loadView('courriers.pdf.show', [
            'courrier' => $courrier->loadMissing([
                'service',
                'creator',
                'treatedBy',
                'archivedBy',
                'routings.serviceSource',
                'routings.serviceDestination',
                'routings.routedBy',
            ]),
            'generatedAt' => $generatedAt,
            'logo' => $this->logoDataUri(),
            'arabic' => $this->arabic,
        ])
            ->setPaper('a4')
            ->download('courrier_'.$courrier->id.'_'.$generatedAt->format('Y_m_d_His').'.pdf');
    }

    private function logoDataUri(): ?string
    {
        $path = public_path('assets/commune-jerada.jpg');

        if (! is_file($path)) {
            return null;
        }

        return 'data:image/jpeg;base64,'.base64_encode((string) file_get_contents($path));
    }
}