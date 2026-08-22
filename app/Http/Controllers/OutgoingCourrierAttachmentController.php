<?php

namespace App\Http\Controllers;

use App\Models\OutgoingCourrier;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class OutgoingCourrierAttachmentController extends Controller
{
    public function show(OutgoingCourrier $outgoingCourrier): BinaryFileResponse
    {
        $this->authorize('view', $outgoingCourrier);

        abort_unless(
            $outgoingCourrier->attachment && Storage::disk('public')->exists($outgoingCourrier->attachment),
            404,
        );

        return Storage::disk('public')->download($outgoingCourrier->attachment);
    }
}
