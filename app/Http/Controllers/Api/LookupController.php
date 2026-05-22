<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventDay;
use App\Models\EventSession;
use App\Models\Sector;
use App\Models\Track;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Http\JsonResponse;

class LookupController extends Controller
{
    public function events(): JsonResponse
    {
        return response()->json(Event::orderBy('name')->get(['id', 'name', 'slug', 'description', 'starts_at', 'ends_at', 'status']));
    }

    public function eventDays(): JsonResponse
    {
        return response()->json(EventDay::orderBy('date')->get(['id', 'event_id', 'day_no', 'date', 'label']));
    }

    public function tracks(): JsonResponse
    {
        return response()->json(Track::orderBy('name')->get(['id', 'name', 'slug', 'color']));
    }

    public function sectors(): JsonResponse
    {
        return response()->json(Sector::orderBy('name')->get(['id', 'name', 'slug', 'color']));
    }

    public function venues(): JsonResponse
    {
        return response()->json(Venue::orderBy('name')->get(['id', 'name', 'slug', 'capacity', 'status']));
    }

    public function sessionOptions(): JsonResponse
    {
        return response()->json(EventSession::orderBy('starts_at')->get(['id', 'title', 'starts_at']));
    }

    public function owners(): JsonResponse
    {
        return response()->json(User::orderBy('name')->get(['id', 'name', 'email']));
    }
}
