<?php

namespace App\Http\Controllers\Api;

use App\Events\ResolutionAdded;
use App\Http\Controllers\Controller;
use App\Models\Resolution;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ResolutionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $q = Resolution::with(['session:id,title', 'sector:id,name', 'track:id,name']);
        foreach (['stage', 'sector_id', 'track_id', 'category'] as $f) {
            if ($v = $request->input($f)) {
                $q->where($f, $v);
            }
        }
        $perPage = (int) $request->integer('per_page', 25);
        return response()->json($q->orderByDesc('recorded_at')->paginate($perPage));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'event_session_id' => ['nullable', 'exists:event_sessions,id'],
            'track_id' => ['nullable', 'exists:tracks,id'],
            'sector_id' => ['nullable', 'exists:sectors,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category' => ['required', 'in:commitment,partnership,policy,keynote,panel'],
            'committed_by' => ['nullable', 'string', 'max:160'],
            'stage' => ['nullable', 'in:commitment,negotiation,signed,fulfilled'],
            'estimated_impact_naira' => ['nullable', 'integer', 'min:0'],
        ]);
        $data['recorded_at'] = $data['recorded_at'] ?? now();
        $resolution = Resolution::create($data);
        ResolutionAdded::dispatch($resolution);
        return response()->json($resolution, 201);
    }

    public function show(Resolution $resolution): JsonResponse
    {
        return response()->json($resolution->load(['session', 'sector', 'track']));
    }

    public function update(Request $request, Resolution $resolution): JsonResponse
    {
        $data = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category' => ['sometimes', 'in:commitment,partnership,policy,keynote,panel'],
            'committed_by' => ['nullable', 'string', 'max:160'],
            'stage' => ['sometimes', 'in:commitment,negotiation,signed,fulfilled'],
            'status' => ['sometimes', 'in:open,in_progress,completed'],
            'estimated_impact_naira' => ['nullable', 'integer', 'min:0'],
        ]);
        $resolution->update($data);
        return response()->json($resolution);
    }

    public function kpis(): JsonResponse
    {
        $today = today();
        return response()->json([
            'total' => Resolution::count(),
            'today' => Resolution::whereDate('recorded_at', $today)->count(),
            'by_stage' => Resolution::selectRaw('stage, count(*) as count')->groupBy('stage')->pluck('count', 'stage'),
            'total_impact_naira' => (int) Resolution::sum('estimated_impact_naira'),
        ]);
    }

    public function byCategory(): JsonResponse
    {
        return response()->json(
            Resolution::selectRaw('category, count(*) as count')->groupBy('category')->get()
        );
    }

    public function bySector(): JsonResponse
    {
        return response()->json(
            Resolution::selectRaw('sector_id, count(*) as count')
                ->with('sector:id,name,color')
                ->groupBy('sector_id')->get()
        );
    }

    public function latest(Request $request): JsonResponse
    {
        $limit = (int) $request->integer('limit', 10);
        return response()->json(
            Resolution::with(['sector:id,name', 'session:id,title'])
                ->orderByDesc('recorded_at')->limit($limit)->get()
        );
    }
}
