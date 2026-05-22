<?php

use App\Http\Controllers\Api\ActionController;
use App\Http\Controllers\Api\AlertController;
use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Api\AttendeeController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DealController;
use App\Http\Controllers\Api\EventSessionController;
use App\Http\Controllers\Api\FeedbackController;
use App\Http\Controllers\Api\IncidentController;
use App\Http\Controllers\Api\InvestorController;
use App\Http\Controllers\Api\LookupController;
use App\Http\Controllers\Api\QuoteController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\ResolutionController;
use App\Http\Controllers\Api\SocialController;
use App\Http\Controllers\Api\SpeakerController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

// Public health check
Route::get('/health', fn () => response()->json([
    'status' => 'ok',
    'service' => 'il-dash-api',
    'time' => now()->toIso8601String(),
]));

// Auth (Sanctum SPA cookie-based)
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/signup-admin', [AuthController::class, 'signupAdmin']);

Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // User Management (Super admin only)
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::get('/users/{user}', [UserController::class, 'show']);
    Route::patch('/users/{user}', [UserController::class, 'update']);
    Route::delete('/users/{user}', [UserController::class, 'destroy']);
    Route::patch('/users/{user}/role', [UserController::class, 'changeRole']);

    // Lookups (shared dropdowns / select options)
    Route::get('/lookups/events', [LookupController::class, 'events']);
    Route::get('/lookups/event-days', [LookupController::class, 'eventDays']);
    Route::get('/lookups/tracks', [LookupController::class, 'tracks']);
    Route::get('/lookups/sectors', [LookupController::class, 'sectors']);
    Route::get('/lookups/venues', [LookupController::class, 'venues']);
    Route::get('/lookups/sessions/options', [LookupController::class, 'sessionOptions']);
    Route::get('/lookups/owners', [LookupController::class, 'owners']);

    // Dashboard aggregators (Overview / Executive / Command Center / Programme / Intelligence)
    Route::get('/overview/kpis', [DashboardController::class, 'overviewKpis']);
    Route::get('/overview/programme-flow', [DashboardController::class, 'programmeFlow']);
    Route::get('/overview/live-session', [DashboardController::class, 'liveSession']);
    Route::get('/overview/resolutions-ticker', [DashboardController::class, 'resolutionsTicker']);
    Route::get('/overview/top-feedback', [DashboardController::class, 'topFeedback']);
    Route::get('/overview/live-poll/current', [DashboardController::class, 'currentLivePoll']);
    Route::get('/executive/kpis', [DashboardController::class, 'executiveKpis']);
    Route::get('/command-center/kpis', [DashboardController::class, 'commandCenterKpis']);
    Route::get('/programme/kpis', [DashboardController::class, 'programmeKpis']);
    Route::get('/intelligence/kpis', [DashboardController::class, 'intelligenceKpis']);

    // Analytics / Sentiment / Heatmap / Global Map
    Route::get('/analytics/kpis', [AnalyticsController::class, 'kpis']);
    Route::get('/analytics/attendance-timeseries', [AnalyticsController::class, 'attendanceTimeseries']);
    Route::get('/analytics/by-track', [AnalyticsController::class, 'byTrack']);
    Route::get('/analytics/by-region', [AnalyticsController::class, 'byRegion']);
    Route::get('/analytics/by-gender', [AnalyticsController::class, 'byGender']);
    Route::get('/analytics/by-category', [AnalyticsController::class, 'byCategory']);
    Route::get('/analytics/session-ratings', [AnalyticsController::class, 'sessionRatings']);
    Route::get('/analytics/new-vs-returning', [AnalyticsController::class, 'newVsReturning']);
    Route::get('/sentiment/trend', [AnalyticsController::class, 'sentimentTrend']);
    Route::get('/sentiment/by-sector', [AnalyticsController::class, 'sentimentBySector']);
    Route::get('/heatmap/kpis', [AnalyticsController::class, 'heatmapKpis']);
    Route::get('/heatmap/sectors', [AnalyticsController::class, 'heatmapSectors']);
    Route::get('/global-map/kpis', [AnalyticsController::class, 'globalMapKpis']);
    Route::get('/global-map/countries', [AnalyticsController::class, 'globalMapCountries']);

    // Attendees
    Route::get('/attendees', [AttendeeController::class, 'index']);
    Route::post('/attendees', [AttendeeController::class, 'store']);
    Route::get('/attendees/{attendee}', [AttendeeController::class, 'show']);
    Route::patch('/attendees/{attendee}', [AttendeeController::class, 'update']);
    Route::delete('/attendees/{attendee}', [AttendeeController::class, 'destroy']);
    Route::post('/attendees/{attendee}/check-in', [AttendeeController::class, 'checkIn']);
    Route::post('/attendees/{attendee}/check-out', [AttendeeController::class, 'checkOut']);

    // Speakers
    Route::get('/speakers', [SpeakerController::class, 'index']);
    Route::post('/speakers', [SpeakerController::class, 'store']);
    Route::get('/speakers/top-engagement', [SpeakerController::class, 'topEngagement']);
    Route::get('/speakers/{speaker}', [SpeakerController::class, 'show']);
    Route::patch('/speakers/{speaker}', [SpeakerController::class, 'update']);
    Route::delete('/speakers/{speaker}', [SpeakerController::class, 'destroy']);

    // Sessions (programme)
    Route::get('/sessions', [EventSessionController::class, 'index']);
    Route::post('/sessions', [EventSessionController::class, 'store']);
    Route::get('/sessions/{session}', [EventSessionController::class, 'show']);
    Route::patch('/sessions/{session}', [EventSessionController::class, 'update']);
    Route::patch('/sessions/{session}/status', [EventSessionController::class, 'updateStatus']);
    Route::delete('/sessions/{session}', [EventSessionController::class, 'destroy']);

    // Quotes
    Route::get('/quotes', [QuoteController::class, 'index']);
    Route::post('/quotes', [QuoteController::class, 'store']);
    Route::delete('/quotes/{quote}', [QuoteController::class, 'destroy']);

    // Feedback
    Route::get('/feedback/kpis', [FeedbackController::class, 'kpis']);
    Route::get('/feedback/latest', [FeedbackController::class, 'latest']);
    Route::get('/feedback', [FeedbackController::class, 'index']);
    Route::post('/feedback', [FeedbackController::class, 'store']);
    Route::get('/feedback/{feedback}', [FeedbackController::class, 'show']);
    Route::patch('/feedback/{feedback}', [FeedbackController::class, 'update']);
    Route::delete('/feedback/{feedback}', [FeedbackController::class, 'destroy']);

    // Resolutions
    Route::get('/resolutions', [ResolutionController::class, 'index']);
    Route::post('/resolutions', [ResolutionController::class, 'store']);
    Route::get('/resolutions/kpis', [ResolutionController::class, 'kpis']);
    Route::get('/resolutions/by-category', [ResolutionController::class, 'byCategory']);
    Route::get('/resolutions/by-sector', [ResolutionController::class, 'bySector']);
    Route::get('/resolutions/latest', [ResolutionController::class, 'latest']);
    Route::get('/resolutions/{resolution}', [ResolutionController::class, 'show']);
    Route::patch('/resolutions/{resolution}', [ResolutionController::class, 'update']);

    // Investors
    Route::get('/investors', [InvestorController::class, 'index']);
    Route::post('/investors', [InvestorController::class, 'store']);
    Route::get('/investors/recent', [InvestorController::class, 'recent']);
    Route::get('/investors/by-region', [InvestorController::class, 'byRegion']);
    Route::get('/investors/by-country', [InvestorController::class, 'byCountry']);
    Route::get('/investors/{investor}', [InvestorController::class, 'show']);
    Route::patch('/investors/{investor}', [InvestorController::class, 'update']);

    // Deals
    Route::get('/deals', [DealController::class, 'index']);
    Route::post('/deals', [DealController::class, 'store']);
    Route::get('/deals/{deal}', [DealController::class, 'show']);
    Route::patch('/deals/{deal}', [DealController::class, 'update']);
    Route::patch('/deals/{deal}/stage', [DealController::class, 'updateStage']);

    // Incidents
    Route::get('/incidents', [IncidentController::class, 'index']);
    Route::post('/incidents', [IncidentController::class, 'store']);
    Route::get('/incidents/{incident}', [IncidentController::class, 'show']);
    Route::patch('/incidents/{incident}', [IncidentController::class, 'update']);
    Route::patch('/incidents/{incident}/status', [IncidentController::class, 'updateStatus']);

    // Alerts
    Route::get('/alerts', [AlertController::class, 'index']);
    Route::post('/alerts', [AlertController::class, 'store']);
    Route::get('/alerts/kpis', [AlertController::class, 'kpis']);
    Route::get('/alerts/by-severity', [AlertController::class, 'bySeverity']);
    Route::get('/alerts/over-time', [AlertController::class, 'overTime']);
    Route::get('/alerts/recently-resolved', [AlertController::class, 'recentlyResolved']);
    Route::post('/alerts/{alert}/read', [AlertController::class, 'markRead']);
    Route::post('/alerts/{alert}/resolve', [AlertController::class, 'resolve']);

    // Actions (Next Action Tracker)
    Route::get('/actions', [ActionController::class, 'index']);
    Route::post('/actions', [ActionController::class, 'store']);
    Route::get('/actions/kpis', [ActionController::class, 'kpis']);
    Route::patch('/actions/{action}', [ActionController::class, 'update']);
    Route::patch('/actions/{action}/status', [ActionController::class, 'updateStatus']);
    Route::delete('/actions/{action}', [ActionController::class, 'destroy']);

    // Reports
    Route::get('/reports', [ReportController::class, 'index']);
    Route::post('/reports/generate', [ReportController::class, 'generate']);
    Route::get('/reports/kpis', [ReportController::class, 'kpis']);
    Route::get('/reports/{report}', [ReportController::class, 'show']);
    Route::get('/reports/{report}/download', [ReportController::class, 'download']);

    // Social listening
    Route::get('/social/kpis', [SocialController::class, 'kpis']);
    Route::get('/social/mentions', [SocialController::class, 'mentions']);
    Route::get('/social/mentions-timeseries', [SocialController::class, 'mentionsTimeseries']);
    Route::get('/social/by-platform', [SocialController::class, 'byPlatform']);
    Route::get('/social/sentiment-breakdown', [SocialController::class, 'sentimentBreakdown']);
    Route::get('/social/themes', [SocialController::class, 'themes']);
    Route::get('/social/hashtags', [SocialController::class, 'hashtags']);
});
