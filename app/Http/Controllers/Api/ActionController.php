<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Action;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $q = Action::with(['sector:id,name,color', 'owner:id,name']);
        foreach (['status', 'owner_id', 'sector_id', 'related_to'] as $f) {
            if ($v = $request->input($f)) {
                $q->where($f, $v);
            }
        }
        $perPage = (int) $request->integer('per_page', 25);
        return response()->json($q->orderBy('due_at')->paginate($perPage));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'related_to' => ['nullable', 'in:session,incident,deal,resolution'],
            'related_id' => ['nullable', 'integer'],
            'sector_id' => ['nullable', 'exists:sectors,id'],
            'owner_id' => ['nullable', 'exists:users,id'],
            'status' => ['nullable', 'in:pending,in_progress,done,blocked'],
            'due_at' => ['nullable', 'date'],
        ]);
        return response()->json(Action::create($data), 201);
    }

    public function update(Request $request, Action $action): JsonResponse
    {
        $data = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'sector_id' => ['nullable', 'exists:sectors,id'],
            'owner_id' => ['nullable', 'exists:users,id'],
            'status' => ['sometimes', 'in:pending,in_progress,done,blocked'],
            'due_at' => ['nullable', 'date'],
        ]);
        $action->update($data);
        return response()->json($action);
    }

    public function destroy(Action $action): JsonResponse
    {
        $action->delete();
        return response()->json(null, 204);
    }

    public function updateStatus(Request $request, Action $action): JsonResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,in_progress,done,blocked'],
        ]);
        $action->update($data);
        return response()->json($action);
    }

    public function kpis(): JsonResponse
    {
        return response()->json([
            'total' => Action::count(),
            'pending' => Action::where('status', 'pending')->count(),
            'in_progress' => Action::where('status', 'in_progress')->count(),
            'done' => Action::where('status', 'done')->count(),
            'overdue' => Action::where('status', '!=', 'done')->where('due_at', '<', now())->count(),
        ]);
    }
}
