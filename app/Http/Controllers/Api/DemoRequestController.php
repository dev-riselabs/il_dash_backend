<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\DemoRequestConfirmation;
use App\Models\DemoRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class DemoRequestController extends Controller
{
    /**
     * Store a new demo request
     */
    public function store(Request $request)
    {
        try {
            // Validate the incoming request
            $validated = $request->validate([
                // Section A: Basic Details
                'full_name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'organization' => 'required|string|max:255',
                'job_title' => 'required|string|max:255',
                'phone_number' => 'required|string|max:20',
                'country' => 'required|string|max:100',

                // Section B: Event Details
                'event_type' => 'required|string|max:100',
                'event_name' => 'nullable|string|max:255',
                'event_date' => 'required|date',
                'event_location' => 'required|string|max:255',
                'estimated_attendees' => 'required|string|max:100',

                // Section C: Needs & Intent
                'primary_objectives' => 'nullable|array',
                'primary_objectives.*' => 'string',
                'deployment_timeline' => 'nullable|array',
                'deployment_timeline.*' => 'string',

                // Section D: Qualifier
                'budget_range' => 'nullable|string|max:100',

                // Section E: Final Input
                'additional_notes' => 'nullable|string|max:2000',
            ]);

            // Add metadata
            $validated['submitted_at'] = now();
            $validated['ip_address'] = $request->ip();
            $validated['user_agent'] = $request->userAgent();

            // Create the demo request
            $demoRequest = DemoRequest::create($validated);

            // Send confirmation email to the user
            try {
                Mail::to($validated['email'])->send(
                    new DemoRequestConfirmation($demoRequest)
                );
            } catch (\Exception $e) {
                // Log email error but don't fail the request
                \Log::error('Failed to send demo request confirmation email', [
                    'demo_request_id' => $demoRequest->id,
                    'email' => $validated['email'],
                    'error' => $e->getMessage(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Demo request submitted successfully. Check your email for confirmation.',
                'data' => $demoRequest,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error creating demo request', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while submitting your request. Please try again.',
            ], 500);
        }
    }

    /**
     * Get all demo requests (for admin dashboard)
     */
    public function index()
    {
        $requests = DemoRequest::latest('submitted_at')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $requests,
        ]);
    }

    /**
     * Get a single demo request
     */
    public function show(DemoRequest $demoRequest)
    {
        return response()->json([
            'success' => true,
            'data' => $demoRequest,
        ]);
    }

    /**
     * Get demo request statistics
     */
    public function stats()
    {
        $stats = [
            'total_requests' => DemoRequest::count(),
            'requests_this_month' => DemoRequest::whereMonth('submitted_at', now()->month)
                ->whereYear('submitted_at', now()->year)
                ->count(),
            'requests_this_week' => DemoRequest::whereDate('submitted_at', '>=', now()->subWeek())
                ->count(),
            'top_countries' => DemoRequest::select('country')
                ->selectRaw('count(*) as count')
                ->groupBy('country')
                ->orderByDesc('count')
                ->limit(5)
                ->get(),
            'top_organizations' => DemoRequest::select('organization')
                ->selectRaw('count(*) as count')
                ->groupBy('organization')
                ->orderByDesc('count')
                ->limit(5)
                ->get(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
