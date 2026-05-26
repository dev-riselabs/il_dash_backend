<?php

use App\Http\Controllers\Api\ActionController;
use App\Http\Controllers\Api\AlertController;
use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Api\AttendeeController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DealController;
use App\Http\Controllers\Api\DemoRequestController;
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
Route::post('/auth/signup', [AuthController::class, 'signup']);
Route::post('/auth/signup-admin', [AuthController::class, 'signupAdmin']);

// ============================================================================
// PUBLIC FORM SUBMISSIONS (NO AUTHENTICATION REQUIRED)
// ============================================================================

// Demo Request Form
Route::post('/demo-request', [DemoRequestController::class, 'store']);

// ============================================================================
// PUBLIC ROUTES - READ-ONLY ACCESS FOR ALL USERS (NO AUTHENTICATION)
// ============================================================================

// Lookups (shared dropdowns / select options)
Route::get('/lookups/events', [LookupController::class, 'events']);
Route::get('/lookups/event-days', [LookupController::class, 'eventDays']);
Route::get('/lookups/tracks', [LookupController::class, 'tracks']);
Route::get('/lookups/sectors', [LookupController::class, 'sectors']);
Route::get('/lookups/venues', [LookupController::class, 'venues']);
Route::get('/lookups/sessions/options', [LookupController::class, 'sessionOptions']);
Route::get('/lookups/owners', [LookupController::class, 'owners']);

// Dashboard aggregators (Overview / Executive / Command Center / Programme / Intelligence) - Public read
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

// Analytics / Sentiment / Heatmap / Global Map - Public read
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

// Attendees - Public read only
Route::get('/attendees', [AttendeeController::class, 'index']);
Route::get('/attendees/{attendee}', [AttendeeController::class, 'show']);

// Speakers - Public read only
Route::get('/speakers', [SpeakerController::class, 'index']);
Route::get('/speakers/top-engagement', [SpeakerController::class, 'topEngagement']);
Route::get('/speakers/{speaker}', [SpeakerController::class, 'show']);

// Sessions - Public read only
Route::get('/sessions', [EventSessionController::class, 'index']);
Route::get('/sessions/{session}', [EventSessionController::class, 'show']);

// Quotes - Public read only
Route::get('/quotes', [QuoteController::class, 'index']);

// Feedback - Public read only
Route::get('/feedback/kpis', [FeedbackController::class, 'kpis']);
Route::get('/feedback/latest', [FeedbackController::class, 'latest']);
Route::get('/feedback', [FeedbackController::class, 'index']);
Route::get('/feedback/{feedback}', [FeedbackController::class, 'show']);

// Resolutions - Public read only
Route::get('/resolutions', [ResolutionController::class, 'index']);
Route::get('/resolutions/kpis', [ResolutionController::class, 'kpis']);
Route::get('/resolutions/by-category', [ResolutionController::class, 'byCategory']);
Route::get('/resolutions/by-sector', [ResolutionController::class, 'bySector']);
Route::get('/resolutions/latest', [ResolutionController::class, 'latest']);
Route::get('/resolutions/{resolution}', [ResolutionController::class, 'show']);

// Investors - Public read only
Route::get('/investors', [InvestorController::class, 'index']);
Route::get('/investors/recent', [InvestorController::class, 'recent']);
Route::get('/investors/by-region', [InvestorController::class, 'byRegion']);
Route::get('/investors/by-country', [InvestorController::class, 'byCountry']);
Route::get('/investors/{investor}', [InvestorController::class, 'show']);

// Deals - Public read only
Route::get('/deals', [DealController::class, 'index']);
Route::get('/deals/{deal}', [DealController::class, 'show']);

// Incidents - Public read only
Route::get('/incidents', [IncidentController::class, 'index']);
Route::get('/incidents/{incident}', [IncidentController::class, 'show']);

// Alerts - Public read only
Route::get('/alerts', [AlertController::class, 'index']);
Route::get('/alerts/kpis', [AlertController::class, 'kpis']);
Route::get('/alerts/by-severity', [AlertController::class, 'bySeverity']);
Route::get('/alerts/over-time', [AlertController::class, 'overTime']);
Route::get('/alerts/recently-resolved', [AlertController::class, 'recentlyResolved']);

// Actions - Public read only
Route::get('/actions', [ActionController::class, 'index']);
Route::get('/actions/kpis', [ActionController::class, 'kpis']);

// Reports - Public read only
Route::get('/reports', [ReportController::class, 'index']);
Route::get('/reports/kpis', [ReportController::class, 'kpis']);
Route::get('/reports/{report}', [ReportController::class, 'show']);
Route::get('/reports/{report}/download', [ReportController::class, 'download']);

// Social listening - Public read only
Route::get('/social/kpis', [SocialController::class, 'kpis']);
Route::get('/social/mentions', [SocialController::class, 'mentions']);
Route::get('/social/mentions-timeseries', [SocialController::class, 'mentionsTimeseries']);
Route::get('/social/by-platform', [SocialController::class, 'byPlatform']);
Route::get('/social/sentiment-breakdown', [SocialController::class, 'sentimentBreakdown']);
Route::get('/social/themes', [SocialController::class, 'themes']);
Route::get('/social/hashtags', [SocialController::class, 'hashtags']);

// ============================================================================
// PROTECTED ROUTES - ADMIN-ONLY ACCESS (REQUIRES AUTHENTICATION)
// ============================================================================

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

    // Attendees - Create, Update, Delete (Admin only)
    Route::post('/attendees', [AttendeeController::class, 'store']);
    Route::patch('/attendees/{attendee}', [AttendeeController::class, 'update']);
    Route::delete('/attendees/{attendee}', [AttendeeController::class, 'destroy']);
    Route::post('/attendees/{attendee}/check-in', [AttendeeController::class, 'checkIn']);
    Route::post('/attendees/{attendee}/check-out', [AttendeeController::class, 'checkOut']);

    // Speakers - Create, Update, Delete (Admin only)
    Route::post('/speakers', [SpeakerController::class, 'store']);
    Route::patch('/speakers/{speaker}', [SpeakerController::class, 'update']);
    Route::delete('/speakers/{speaker}', [SpeakerController::class, 'destroy']);

    // Sessions - Create, Update, Delete (Admin only)
    Route::post('/sessions', [EventSessionController::class, 'store']);
    Route::patch('/sessions/{session}', [EventSessionController::class, 'update']);
    Route::patch('/sessions/{session}/status', [EventSessionController::class, 'updateStatus']);
    Route::delete('/sessions/{session}', [EventSessionController::class, 'destroy']);

    // Quotes - Create, Delete (Admin only)
    Route::post('/quotes', [QuoteController::class, 'store']);
    Route::delete('/quotes/{quote}', [QuoteController::class, 'destroy']);

    // Feedback - Create, Update, Delete (Admin only)
    Route::post('/feedback', [FeedbackController::class, 'store']);
    Route::patch('/feedback/{feedback}', [FeedbackController::class, 'update']);
    Route::delete('/feedback/{feedback}', [FeedbackController::class, 'destroy']);

    // Resolutions - Create, Update (Admin only)
    Route::post('/resolutions', [ResolutionController::class, 'store']);
    Route::patch('/resolutions/{resolution}', [ResolutionController::class, 'update']);

    // Investors - Create, Update (Admin only)
    Route::post('/investors', [InvestorController::class, 'store']);
    Route::patch('/investors/{investor}', [InvestorController::class, 'update']);

    // Deals - Create, Update (Admin only)
    Route::post('/deals', [DealController::class, 'store']);
    Route::patch('/deals/{deal}', [DealController::class, 'update']);
    Route::patch('/deals/{deal}/stage', [DealController::class, 'updateStage']);
    Route::delete('/deals/{deal}', [DealController::class, 'destroy']);


    // Incidents - Create, Update (Admin only)
    Route::post('/incidents', [IncidentController::class, 'store']);
    Route::patch('/incidents/{incident}', [IncidentController::class, 'update']);
    Route::patch('/incidents/{incident}/status', [IncidentController::class, 'updateStatus']);

    // Alerts - Create, Update (Admin only)
    Route::post('/alerts', [AlertController::class, 'store']);
    Route::post('/alerts/{alert}/read', [AlertController::class, 'markRead']);
    Route::post('/alerts/{alert}/resolve', [AlertController::class, 'resolve']);

    // Actions - Create, Update, Delete (Admin only)
    Route::post('/actions', [ActionController::class, 'store']);
    Route::patch('/actions/{action}', [ActionController::class, 'update']);
    Route::patch('/actions/{action}/status', [ActionController::class, 'updateStatus']);
    Route::delete('/actions/{action}', [ActionController::class, 'destroy']);

    // Reports - Generate (Admin only)
    Route::post('/reports/generate', [ReportController::class, 'generate']);

    // Demo Requests - Admin Dashboard (Admin only)
    Route::get('/demo-requests', [DemoRequestController::class, 'index']);
    Route::get('/demo-requests/stats', [DemoRequestController::class, 'stats']);
    Route::get('/demo-requests/{demoRequest}', [DemoRequestController::class, 'show']);
});
