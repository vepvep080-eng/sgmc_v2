<?php

namespace App\Http\Controllers;

use App\Models\Courrier;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CourrierAttachmentController extends Controller
{
    public function show(Courrier $courrier, string $attachment): BinaryFileResponse
    {
        $this->authorize('view', $courrier);

        $path = match ($attachment) {
            'piece_jointe' => $courrier->piece_jointe,
            'traitement_piece_jointe' => $courrier->traitement_piece_jointe,
            default => null,
        };

        abort_unless($path && Storage::disk('public')->exists($path), 404);

        return Storage::disk('public')->download($path);
    }
}
