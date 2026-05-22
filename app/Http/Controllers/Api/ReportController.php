<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $q = Report::with(['day:id,label', 'generator:id,name']);
        if ($kind = $request->input('kind')) {
            $q->where('kind', $kind);
        }
        $perPage = (int) $request->integer('per_page', 25);
        return response()->json($q->orderByDesc('generated_at')->paginate($perPage));
    }

    public function generate(Request $request): JsonResponse
    {
        $data = $request->validate([
            'kind' => ['required', 'in:executive_summary,attendance,investment,sentiment'],
            'event_day_id' => ['nullable', 'exists:event_days,id'],
            'range_start' => ['nullable', 'date'],
            'range_end' => ['nullable', 'date', 'after_or_equal:range_start'],
            'name' => ['nullable', 'string', 'max:200'],
        ]);
        $data['generated_by'] = $request->user()?->id;
        $data['generated_at'] = now();
        $data['name'] = $data['name'] ?? ucwords(str_replace('_', ' ', $data['kind'])).' '.now()->format('Y-m-d H:i');
        // file_url is intentionally null until a queued job populates it.
        return response()->json(Report::create($data), 201);
    }

    public function show(Report $report): JsonResponse
    {
        return response()->json($report);
    }

    public function download(Report $report): JsonResponse
    {
        return response()->json([
            'url' => $report->file_url,
            'available' => (bool) $report->file_url,
        ]);
    }

    public function kpis(): JsonResponse
    {
        return response()->json([
            'total' => Report::count(),
            'this_week' => Report::where('generated_at', '>=', now()->subWeek())->count(),
            'by_kind' => Report::selectRaw('kind, count(*) as count')->groupBy('kind')->pluck('count', 'kind'),
        ]);
    }
}
