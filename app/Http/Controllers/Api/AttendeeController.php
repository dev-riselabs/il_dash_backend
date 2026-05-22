<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendeeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $q = Attendee::query();

        if ($search = $request->string('search')->toString()) {
            $q->where(function ($w) use ($search) {
                $w->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('organization', 'like', "%{$search}%");
            });
        }

        foreach (['category', 'gender', 'region', 'country'] as $f) {
            if ($v = $request->string($f)->toString()) {
                $q->where($f, $v);
            }
        }

        $perPage = (int) $request->integer('per_page', 25);
        return response()->json($q->orderByDesc('created_at')->paginate($perPage));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:120'],
            'last_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'unique:attendees,email'],
            'job_title' => ['nullable', 'string', 'max:160'],
            'organization' => ['nullable', 'string', 'max:160'],
            'country' => ['nullable', 'string', 'max:80'],
            'region' => ['nullable', 'string', 'max:80'],
            'gender' => ['nullable', 'in:male,female,other'],
            'category' => ['nullable', 'string', 'max:64'],
            'event_id' => ['nullable', 'exists:events,id'],
            'track_id' => ['nullable', 'exists:tracks,id'],
            'sector_id' => ['nullable', 'exists:sectors,id'],
        ]);

        $attendee = Attendee::create($data + ['is_new_today' => true]);
        return response()->json($attendee, 201);
    }

    public function show(Attendee $attendee): JsonResponse
    {
        return response()->json($attendee);
    }

    public function update(Request $request, Attendee $attendee): JsonResponse
    {
        $data = $request->validate([
            'first_name' => ['sometimes', 'string', 'max:120'],
            'last_name' => ['sometimes', 'string', 'max:120'],
            'email' => ['sometimes', 'email', 'unique:attendees,email,'.$attendee->id],
            'job_title' => ['nullable', 'string', 'max:160'],
            'organization' => ['nullable', 'string', 'max:160'],
            'country' => ['nullable', 'string', 'max:80'],
            'region' => ['nullable', 'string', 'max:80'],
            'gender' => ['nullable', 'in:male,female,other'],
            'category' => ['nullable', 'string', 'max:64'],
            'event_id' => ['nullable', 'exists:events,id'],
            'track_id' => ['nullable', 'exists:tracks,id'],
            'sector_id' => ['nullable', 'exists:sectors,id'],
        ]);

        $attendee->update($data);
        return response()->json($attendee);
    }

    public function destroy(Attendee $attendee): JsonResponse
    {
        $attendee->delete();
        return response()->json(null, 204);
    }

    public function checkIn(Attendee $attendee): JsonResponse
    {
        $attendee->update(['checked_in_at' => now()]);
        return response()->json($attendee);
    }

    public function checkOut(Attendee $attendee): JsonResponse
    {
        $attendee->update(['checked_out_at' => now()]);
        return response()->json($attendee);
    }
}
