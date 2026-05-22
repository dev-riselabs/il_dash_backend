<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendee;
use App\Models\AttendanceSnapshot;
use App\Models\Deal;
use App\Models\EventSession;
use App\Models\FeedbackSubmission;
use App\Models\InvestmentSignal;
use App\Models\Investor;
use App\Models\SectorInvestmentSummary;
use App\Models\SentimentScore;
use App\Models\Track;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function kpis(Request $request): JsonResponse
    {
        $q = Attendee::query();
        return response()->json([
            'total_attendees' => $q->count(),
            'checked_in' => (clone $q)->whereNotNull('checked_in_at')->count(),
            'new_today' => (clone $q)->where('is_new_today', true)->count(),
        ]);
    }

    public function attendanceTimeseries(Request $request): JsonResponse
    {
        $q = AttendanceSnapshot::orderBy('captured_at');
        if ($d = $request->integer('event_day_id')) {
            $q->where('event_day_id', $d);
        }
        return response()->json($q->get());
    }

    public function byTrack(): JsonResponse
    {
        $data = EventSession::whereNotNull('track_id')
            ->get(['track_id', 'attendance_in_person', 'attendance_virtual'])
            ->groupBy('track_id')
            ->map(function ($sessions, $trackId) {
                $attendance = $sessions->sum(function ($s) {
                    return ($s->attendance_in_person ?? 0) + ($s->attendance_virtual ?? 0);
                });
                return [
                    'track_id' => $trackId,
                    'sessions' => $sessions->count(),
                    'attendance' => $attendance,
                ];
            })
            ->values();

        // Load track names
        $tracks = Track::whereIn('id', $data->pluck('track_id'))->get()->keyBy('id');
        
        return response()->json(
            $data->map(function ($item) use ($tracks) {
                $track = $tracks->get($item['track_id']);
                return [
                    'track_id' => $item['track_id'],
                    'sessions' => $item['sessions'],
                    'attendance' => $item['attendance'],
                    'track' => $track ? [
                        'id' => $track->id,
                        'name' => $track->name,
                        'color' => $track->color,
                    ] : null,
                ];
            })->values()
        );
    }

    public function byRegion(): JsonResponse
    {
        return response()->json(
            Attendee::selectRaw('region, count(*) as count')->groupBy('region')->get()
        );
    }

    public function byGender(): JsonResponse
    {
        return response()->json(
            Attendee::selectRaw('gender, count(*) as count')->groupBy('gender')->get()
        );
    }

    public function byCategory(): JsonResponse
    {
        return response()->json(
            Attendee::selectRaw('category, count(*) as count')->groupBy('category')->get()
        );
    }

    public function sessionRatings(): JsonResponse
    {
        return response()->json(
            EventSession::whereNotNull('average_rating_x10')
                ->orderByDesc('average_rating_x10')
                ->limit(10)->get(['id', 'title', 'average_rating_x10'])
        );
    }

    public function newVsReturning(): JsonResponse
    {
        return response()->json([
            'new' => Attendee::where('is_new_today', true)->count(),
            'returning' => Attendee::where('is_new_today', false)->count(),
        ]);
    }

    public function sentimentTrend(Request $request): JsonResponse
    {
        $days = max(1, (int) $request->integer('days', 7));
        $since = now()->subDays($days);
        return response()->json(
            SentimentScore::where('scope', 'overall')
                ->where('captured_at', '>=', $since)
                ->orderBy('captured_at')->get()
        );
    }

    public function sentimentBySector(): JsonResponse
    {
        return response()->json(
            SentimentScore::where('scope', 'sector')
                ->orderByDesc('captured_at')->get()
        );
    }

    public function heatmapKpis(): JsonResponse
    {
        return response()->json([
            'total_signals' => InvestmentSignal::count(),
            'total_value_naira' => (int) InvestmentSignal::sum('estimated_value_naira'),
            'high_confidence' => InvestmentSignal::where('confidence', 'high')->count(),
        ]);
    }

    public function heatmapSectors(): JsonResponse
    {
        return response()->json(
            SectorInvestmentSummary::with('sector:id,name,color')
                ->orderByDesc('captured_at')->get()
        );
    }

    public function globalMapKpis(): JsonResponse
    {
        return response()->json([
            'countries_count' => Investor::distinct('country')->whereNotNull('country')->count('country'),
            'investors_count' => Investor::count(),
            'deals_count' => Deal::count(),
        ]);
    }

    public function globalMapCountries(): JsonResponse
    {
        return response()->json(
            Investor::selectRaw('country, region, count(*) as investors_count')
                ->whereNotNull('country')->groupBy('country', 'region')->get()
        );
    }
}
