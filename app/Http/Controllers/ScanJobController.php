<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadScanDocumentRequest;
use App\Models\Courrier;
use App\Models\ScanJob;
use App\Services\ScanJobService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use RuntimeException;

class ScanJobController extends Controller
{
    public function store(Request $request, Courrier $courrier, ScanJobService $service): JsonResponse|RedirectResponse
    {
        $this->authorize('update', $courrier);
        [$job, $token] = $service->create($request->user(), $courrier);
        if ($request->expectsJson()) return response()->json(['job' => $job->only(['public_id','status','expires_at']), 'token' => $token], 201);
        return back()->with('scan_job_created', $job->public_id);
    }

    public function show(Request $request, Courrier $courrier, ScanJob $scanJob): JsonResponse
    {
        $this->authorize('view', $courrier);
        abort_unless($scanJob->courrier_id === $courrier->id && $scanJob->user_id === $request->user()->id, 404);
        return response()->json($scanJob->only(['public_id','status','expires_at','completed_at','original_filename','mime_type','file_size']));
    }

    public function upload(UploadScanDocumentRequest $request, ScanJob $scanJob, ScanJobService $service): JsonResponse
    {
        $this->authorize('update', $scanJob->courrier);
        try { $job = $service->attach($scanJob, $request->user(), $request->validated('token'), $request->file('document')); }
        catch (RuntimeException $exception) { return response()->json(['message' => __('app.scan_job_invalid')], $exception->getMessage() === 'expired_job' ? 410 : 422); }
        return response()->json(['status' => $job->status, 'attachment_url' => $job->courrier->fresh()->pieceJointeUrl()]);
    }

    public function cancel(Request $request, ScanJob $scanJob, ScanJobService $service): RedirectResponse
    {
        $this->authorize('update', $scanJob->courrier);
        try { $service->cancel($scanJob, $request->user()); } catch (RuntimeException) { abort(422); }
        return back()->with('success', __('app.scan_cancelled'));
    }
}
