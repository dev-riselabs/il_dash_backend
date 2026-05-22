<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use App\Models\Attendee;
use App\Models\Deal;
use App\Models\EventSession;
use App\Models\FeedbackSubmission;
use App\Models\Incident;
use App\Models\LivePoll;
use App\Models\Resolution;
use App\Models\SecurityStatsSnapshot;
use App\Models\SessionQuote;
use App\Models\Speaker;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function overviewKpis(): JsonResponse
    {
        return response()->json([
            'total_attendance' => Attendee::whereNotNull('checked_in_at')->count(),
            'speakers_count' => Speaker::count(),
            'active_deals' => Deal::whereIn('stage', ['discussion', 'negotiation', 'commitment'])->count(),
            'resolutions_today' => Resolution::whereDate('recorded_at', today())->count(),
        ]);
    }

    public function programmeFlow(): JsonResponse
    {
        $base = EventSession::with(['track:id,name', 'venue:id,name'])->orderBy('starts_at');
        return response()->json([
            'live' => (clone $base)->where('status', 'live')->get(),
            'next' => (clone $base)->where('status', 'next')->limit(3)->get(),
            'completed' => (clone $base)->where('status', 'completed')->orderByDesc('ends_at')->limit(3)->get(),
            'upcoming' => (clone $base)->where('status', 'upcoming')->limit(5)->get(),
        ]);
    }

    public function liveSession(): JsonResponse
    {
        $session = EventSession::with(['speakers', 'insights', 'quotes.speaker', 'venue', 'track', 'resources'])
            ->where('status', 'live')->first();
        return response()->json(['session' => $session]);
    }

    public function resolutionsTicker(Request $request): JsonResponse
    {
        $limit = (int) $request->integer('limit', 8);
        return response()->json(
            Resolution::with(['sector:id,name'])->orderByDesc('recorded_at')->limit($limit)->get()
        );
    }

    public function topFeedback(Request $request): JsonResponse
    {
        $limit = (int) $request->integer('limit', 5);
        return response()->json(
            FeedbackSubmission::with(['session:id,title', 'attendee:id,first_name,last_name'])
                ->orderByDesc('star_rating')->orderByDesc('submitted_at')
                ->limit($limit)->get()
        );
    }

    public function currentLivePoll(): JsonResponse
    {
        $poll = LivePoll::with('responses')->where('status', 'open')->latest()->first();
        if (!$poll) {
            return response()->json(['poll' => null]);
        }
        $tally = $poll->responses->groupBy('option')->map->count();
        return response()->json(['poll' => $poll, 'tally' => $tally]);
    }

    public function executiveKpis(): JsonResponse
    {
        return response()->json([
            'attendance' => Attendee::count(),
            'sessions_completed' => EventSession::where('status', 'completed')->count(),
            'sessions_live' => EventSession::where('status', 'live')->count(),
            'commitments_value_naira' => (int) Resolution::sum('estimated_impact_naira'),
            'deals_value_naira' => (int) Deal::sum('value_naira'),
        ]);
    }

    public function commandCenterKpis(): JsonResponse
    {
        $snapshot = SecurityStatsSnapshot::latest('captured_at')->first();
        return response()->json([
            'sessions_live' => EventSession::where('status', 'live')->count(),
            'incidents_open' => Incident::whereIn('status', ['open', 'responding'])->count(),
            'alerts_unread' => Alert::where('status', 'unread')->count(),
            'safety_level' => $snapshot?->safety_level ?? 'high',
            'personnel_on_duty' => $snapshot?->personnel_on_duty ?? 0,
        ]);
    }

    public function programmeKpis(): JsonResponse
    {
        return response()->json([
            'total_sessions' => EventSession::count(),
            'live' => EventSession::where('status', 'live')->count(),
            'completed' => EventSession::where('status', 'completed')->count(),
            'delayed' => EventSession::where('status', 'delayed')->count(),
            'cancelled' => EventSession::where('status', 'cancelled')->count(),
        ]);
    }

    public function intelligenceKpis(): JsonResponse
    {
        $total = FeedbackSubmission::count();
        $positive = FeedbackSubmission::where('sentiment_label', 'positive')->count();
        $negative = FeedbackSubmission::where('sentiment_label', 'negative')->count();
        $neutral = $total - $positive - $negative;
        return response()->json([
            'total_signals' => $total,
            'positive_pct' => $total ? round($positive / $total * 100, 1) : 0,
            'negative_pct' => $total ? round($negative / $total * 100, 1) : 0,
            'neutral_pct' => $total ? round($neutral / $total * 100, 1) : 0,
            'quotes_count' => SessionQuote::count(),
        ]);
    }
}
