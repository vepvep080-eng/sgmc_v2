<?php

namespace App\Http\Controllers;

use App\Http\Requests\AgentStatusRequest;
use App\Models\Courrier;
use App\Models\ScanJob;
use App\Services\ScanJobService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Handles the SGMC-side of the Scanner Agent communication contract.
 *
 * The agent is a localhost Windows component that:
 *  - receives short-lived scan jobs via the browser
 *  - validates tokens against SGMC
 *  - discovers and scans physical documents
 *  - uploads results back to SGMC
 *
 * All endpoints require an authenticated, active user with update
 * permission on the target courrier.
 */
class ScannerAgentController extends Controller
{
    public function __construct(
        private readonly ScanJobService $scanJob,
    ) {
    }

    /**
     * Validate a job token and return job metadata for the agent.
     *
     * The agent sends the token in the Authorization header (Bearer).
     * SGMC validates the token and returns the job payload.
     */
    public function payload(Request $request, ScanJob $scanJob): JsonResponse
    {
        $token = $this->extractBearerToken($request);

        if ($token === null) {
            return response()->json(['message' => 'missing_token'], 401);
        }

        try {
            $payload = $this->scanJob->validateToken($scanJob, $token);

            return response()->json($payload);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * Report agent connection status for a scan job.
     *
     * The agent sends the token in the Authorization header (Bearer).
     * SGMC validates the token and updates the job status.
     */
    public function status(AgentStatusRequest $request, ScanJob $scanJob): JsonResponse
    {
        $token = $this->extractBearerToken($request);

        if ($token === null) {
            return response()->json(['message' => 'missing_token'], 401);
        }

        // Validate the token is valid for this job (derives courrier/user from job)
        try {
            $this->scanJob->validateToken($scanJob, $token);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        $status = $request->validated('status');

        if ($status === 'connected') {
            $this->scanJob->markAgentConnected($scanJob, [
                'name' => $request->validated('scanner_name'),
                'manufacturer' => $request->validated('scanner_manufacturer'),
                'model' => $request->validated('scanner_model'),
                'connection' => $request->validated('scanner_connection'),
            ]);
        } else {
            $this->scanJob->markAgentDisconnected($scanJob, $request->validated('error_message'));
        }

        return response()->json([
            'status' => $scanJob->fresh()->status,
            'agent_status' => $status,
        ]);
    }

    /**
     * Extract a Bearer token from the Authorization header.
     */
    private function extractBearerToken(Request $request): ?string
    {
        $header = $request->header('Authorization', '');

        if (str_starts_with($header, 'Bearer ')) {
            return substr($header, 7);
        }

        return null;
    }
}
