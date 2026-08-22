<?php

namespace App\Services;

use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\Response;

class CourrierPdfExportService
{
    public function __construct(private CourrierQueryService $courrierQuery)
    {
    }

    public function download(User $user, array $filters): Response
    {
        $generatedAt = now();

        return Pdf::loadView('courriers.pdf.index', [
            'courriers' => $this->courrierQuery->export($user, $filters),
            'generatedAt' => $generatedAt,
            'generatedBy' => $user,
            'logo' => $this->logoDataUri(),
        ])
            ->setPaper('a4', 'landscape')
            ->download('courriers_'.$generatedAt->format('Y_m_d_His').'.pdf');
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
