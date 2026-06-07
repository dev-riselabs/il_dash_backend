<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SessionInsight;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SessionInsightController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $q = SessionInsight::query();

        if ($eventSessionId = $request->integer('event_session_id')) {
            $q->where('event_session_id', $eventSessionId);
        }

        if ($kind = $request->string('kind')->toString()) {
            $q->where('kind', $kind);
        }

        if ($search = $request->string('search')->toString()) {
            $q->where('body', 'like', "%{$search}%");
        }

        $perPage = (int) $request->integer('per_page', 25);
        return response()->json($q->orderBy('order')->paginate($perPage));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'event_session_id' => ['required', 'exists:event_sessions,id'],
            'body' => ['required', 'string'],
            'kind' => ['sometimes', 'string', 'in:insight,theme'],
            'order' => ['sometimes', 'integer', 'min:0'],
        ]);

        // Set default kind if not provided
        if (!isset($data['kind'])) {
            $data['kind'] = 'insight';
        }

        // Set default order if not provided
        if (!isset($data['order'])) {
            // Get the max order for this session and increment
            $maxOrder = SessionInsight::where('event_session_id', $data['event_session_id'])
                ->max('order') ?? -1;
            $data['order'] = $maxOrder + 1;
        }

        return response()->json(SessionInsight::create($data), 201);
    }

    public function show(SessionInsight $insight): JsonResponse
    {
        return response()->json($insight);
    }

    public function update(Request $request, SessionInsight $insight): JsonResponse
    {
        $data = $request->validate([
            'body' => ['sometimes', 'string'],
            'kind' => ['sometimes', 'string', 'in:insight,theme'],
            'order' => ['sometimes', 'integer', 'min:0'],
        ]);

        $insight->update($data);
        return response()->json($insight);
    }

    public function destroy(SessionInsight $insight): JsonResponse
    {
        $insight->delete();
        return response()->json(null, 204);
    }
}
