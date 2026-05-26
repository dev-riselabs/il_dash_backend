<?php

namespace App\Http\Controllers\Api;

use App\Events\DealStageChanged;
use App\Http\Controllers\Controller;
use App\Models\Deal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DealController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $q = Deal::with(['investor:id,name,logo_url', 'sector:id,name,color', 'owner:id,name']);
        foreach (['stage', 'sector_id', 'investor_id', 'owner_id'] as $f) {
            if ($v = $request->input($f)) {
                $q->where($f, $v);
            }
        }
        $perPage = (int) $request->integer('per_page', 25);
        return response()->json($q->orderByDesc('updated_at')->paginate($perPage));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'investor_id' => ['nullable', 'exists:investors,id'],
            'sector_id' => ['nullable', 'exists:sectors,id'],
            'stage' => ['nullable', 'in:discussion,negotiation,commitment,closed_won,closed_lost'],
            'value_naira' => ['nullable', 'integer', 'min:0'],
            'owner_id' => ['nullable', 'exists:users,id'],
        ]);
        $data['opened_at'] = $data['opened_at'] ?? now();
        return response()->json(Deal::create($data), 201);
    }

    public function show(Deal $deal): JsonResponse
    {
        return response()->json($deal->load(['investor', 'sector', 'owner']));
    }

    public function update(Request $request, Deal $deal): JsonResponse
    {
        $data = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'investor_id' => ['nullable', 'exists:investors,id'],
            'sector_id' => ['nullable', 'exists:sectors,id'],
            'stage' => ['sometimes', 'in:discussion,negotiation,commitment,closed_won,closed_lost'],
            'value_naira' => ['nullable', 'integer', 'min:0'],
            'owner_id' => ['nullable', 'exists:users,id'],
        ]);
        $stageChanged = array_key_exists('stage', $data) && $data['stage'] !== $deal->stage;
        $deal->update($data);
        if ($stageChanged) {
            DealStageChanged::dispatch($deal);
        }
        return response()->json($deal);
    }

    public function updateStage(Request $request, Deal $deal): JsonResponse
    {
        $data = $request->validate([
            'stage' => ['required', 'in:discussion,negotiation,commitment,closed_won,closed_lost'],
        ]);
        $changed = $data['stage'] !== $deal->stage;
        $deal->update($data);
        if ($changed) {
            DealStageChanged::dispatch($deal);
        }
        return response()->json($deal);
    }

    public function destroy(Deal $deal): JsonResponse
    {
        $deal->delete();
        return response()->json(['message' => 'Deal deleted successfully']);
    }
}
