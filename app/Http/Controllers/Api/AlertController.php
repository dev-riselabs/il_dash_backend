<?php

namespace App\Http\Controllers\Api;

use App\Events\AlertCreated;
use App\Http\Controllers\Controller;
use App\Models\Alert;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $q = Alert::query();
        foreach (['status', 'severity', 'source'] as $f) {
            if ($v = $request->input($f)) {
                $q->where($f, $v);
            }
        }
        $perPage = (int) $request->integer('per_page', 25);
        return response()->json($q->orderByDesc('created_at')->paginate($perPage));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'severity' => ['required', 'in:info,low,medium,high,critical,warning'],
            'source' => ['nullable', 'string', 'max:64'],
            'source_ref_id' => ['nullable', 'integer'],
        ]);
        $alert = Alert::create($data);
        AlertCreated::dispatch($alert);
        return response()->json($alert, 201);
    }

    public function markRead(Alert $alert): JsonResponse
    {
        $alert->update(['status' => 'read']);
        return response()->json($alert);
    }

    public function resolve(Alert $alert): JsonResponse
    {
        $alert->update(['status' => 'resolved', 'resolved_at' => now()]);
        return response()->json($alert);
    }

    public function kpis(): JsonResponse
    {
        return response()->json([
            'unread' => Alert::where('status', 'unread')->count(),
            'critical_open' => Alert::where('status', '!=', 'resolved')->where('severity', 'critical')->count(),
            'resolved_today' => Alert::where('status', 'resolved')->whereDate('resolved_at', today())->count(),
            'total' => Alert::count(),
        ]);
    }

    public function bySeverity(): JsonResponse
    {
        return response()->json(
            Alert::selectRaw('severity, count(*) as count')->groupBy('severity')->get()
        );
    }

    public function overTime(Request $request): JsonResponse
    {
        $days = max(1, (int) $request->integer('days', 7));
        $since = now()->subDays($days);
        return response()->json(
            Alert::where('created_at', '>=', $since)
                ->selectRaw('date(created_at) as date, count(*) as count')
                ->groupBy('date')->orderBy('date')->get()
        );
    }

    public function recentlyResolved(Request $request): JsonResponse
    {
        $limit = (int) $request->integer('limit', 10);
        return response()->json(
            Alert::where('status', 'resolved')->orderByDesc('resolved_at')->limit($limit)->get()
        );
    }
}
