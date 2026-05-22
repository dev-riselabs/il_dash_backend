<?php

namespace App\Http\Controllers\Api;

use App\Events\IncidentReported;
use App\Events\IncidentStatusChanged;
use App\Http\Controllers\Controller;
use App\Models\Incident;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IncidentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $q = Incident::with(['venue:id,name', 'reporter:id,name']);
        foreach (['status', 'severity', 'type', 'venue_id'] as $f) {
            if ($v = $request->input($f)) {
                $q->where($f, $v);
            }
        }
        $perPage = (int) $request->integer('per_page', 25);
        return response()->json($q->orderByDesc('occurred_at')->paginate($perPage));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'type' => ['required', 'string', 'max:64'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'venue_id' => ['nullable', 'exists:venues,id'],
            'occurred_at' => ['required', 'date'],
            'severity' => ['required', 'in:low,medium,high,critical'],
        ]);
        $data['reported_by'] = $request->user()?->id;
        $incident = Incident::create($data);
        IncidentReported::dispatch($incident);
        return response()->json($incident, 201);
    }

    public function show(Incident $incident): JsonResponse
    {
        return response()->json($incident->load(['venue', 'reporter']));
    }

    public function update(Request $request, Incident $incident): JsonResponse
    {
        $data = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'severity' => ['sometimes', 'in:low,medium,high,critical'],
            'status' => ['sometimes', 'in:open,responding,resolved'],
        ]);
        $statusChanged = array_key_exists('status', $data) && $data['status'] !== $incident->status;
        if (($data['status'] ?? null) === 'resolved' && !$incident->resolved_at) {
            $data['resolved_at'] = now();
        }
        $incident->update($data);
        if ($statusChanged) {
            IncidentStatusChanged::dispatch($incident);
        }
        return response()->json($incident);
    }

    public function updateStatus(Request $request, Incident $incident): JsonResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:open,responding,resolved'],
        ]);
        $changed = $data['status'] !== $incident->status;
        if ($data['status'] === 'resolved' && !$incident->resolved_at) {
            $data['resolved_at'] = now();
        }
        $incident->update($data);
        if ($changed) {
            IncidentStatusChanged::dispatch($incident);
        }
        return response()->json($incident);
    }
}
