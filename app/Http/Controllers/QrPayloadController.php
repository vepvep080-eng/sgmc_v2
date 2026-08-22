<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQrPayloadRequest;
use App\Models\Courrier;
use App\Services\AuditService;
use App\Services\QrPayloadService;
use Illuminate\Http\JsonResponse;

class QrPayloadController extends Controller
{
    public function store(StoreQrPayloadRequest $request, Courrier $courrier, QrPayloadService $qr): JsonResponse
    {
        $details = $qr->inspect($request->validated('payload'));
        app(AuditService::class)->record('qr_read', $courrier, $courrier->service_id, 'Lecture d’un code QR.', null, ['payload_hash' => $details['hash'], 'length' => $details['length'], 'type' => $details['type']]);
        return response()->json($details);
    }
}
