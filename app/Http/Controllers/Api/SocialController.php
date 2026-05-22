<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MentionsTimeseries;
use App\Models\SocialHashtag;
use App\Models\SocialMention;
use App\Models\SocialTheme;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SocialController extends Controller
{
    public function kpis(): JsonResponse
    {
        $total = SocialMention::count();
        $positive = SocialMention::where('sentiment_label', 'positive')->count();
        return response()->json([
            'total_mentions' => $total,
            'positive_pct' => $total ? round($positive / $total * 100, 1) : 0,
            'total_reach' => (int) SocialMention::sum('reach'),
            'total_impressions' => (int) SocialMention::sum('impressions'),
        ]);
    }

    public function mentions(Request $request): JsonResponse
    {
        $q = SocialMention::with('theme:id,name');
        foreach (['platform', 'sentiment_label', 'theme_id', 'location'] as $f) {
            if ($v = $request->input($f)) {
                $q->where($f, $v);
            }
        }
        $perPage = (int) $request->integer('per_page', 25);
        return response()->json($q->orderByDesc('posted_at')->paginate($perPage));
    }

    public function mentionsTimeseries(Request $request): JsonResponse
    {
        $days = max(1, (int) $request->integer('days', 7));
        $since = now()->subDays($days);
        $q = MentionsTimeseries::where('captured_at', '>=', $since)->orderBy('captured_at');
        if ($p = $request->input('platform')) {
            $q->where('platform', $p);
        }
        return response()->json($q->get());
    }

    public function byPlatform(): JsonResponse
    {
        return response()->json(
            SocialMention::selectRaw('platform, count(*) as count')->groupBy('platform')->get()
        );
    }

    public function sentimentBreakdown(): JsonResponse
    {
        return response()->json(
            SocialMention::selectRaw('sentiment_label, count(*) as count')->groupBy('sentiment_label')->get()
        );
    }

    public function themes(): JsonResponse
    {
        return response()->json(SocialTheme::orderByDesc('mention_count')->get());
    }

    public function hashtags(): JsonResponse
    {
        return response()->json(SocialHashtag::orderByDesc('mention_count')->limit(20)->get());
    }
}
