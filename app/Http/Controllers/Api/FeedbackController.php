<?php

namespace App\Http\Controllers\Api;

use App\Events\FeedbackSubmitted;
use App\Http\Controllers\Controller;
use App\Models\FeedbackSubmission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $q = FeedbackSubmission::with(['session:id,title', 'attendee:id,first_name,last_name']);
        foreach (['event_session_id', 'channel', 'sentiment_label'] as $f) {
            if ($v = $request->input($f)) {
                $q->where($f, $v);
            }
        }
        $perPage = (int) $request->integer('per_page', 25);
        return response()->json($q->orderByDesc('submitted_at')->paginate($perPage));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'event_session_id' => ['nullable', 'exists:event_sessions,id'],
            'attendee_id' => ['nullable', 'exists:attendees,id'],
            'channel' => ['nullable', 'in:qr,mobile,website,other'],
            'star_rating' => ['required', 'integer', 'min:1', 'max:5'],
            'review_text' => ['nullable', 'string'],
            'key_takeaway' => ['nullable', 'string'],
            'sentiment_label' => ['nullable', 'in:positive,neutral,negative'],
            'sentiment_score' => ['nullable', 'numeric', 'between:-1,1'],
        ]);
        $data['submitted_at'] = now();
        $data['channel'] = $data['channel'] ?? 'qr';

        $feedback = FeedbackSubmission::create($data);
        FeedbackSubmitted::dispatch($feedback);
        return response()->json($feedback, 201);
    }

    public function show(FeedbackSubmission $feedback): JsonResponse
    {
        return response()->json($feedback->load(['session:id,title', 'attendee:id,first_name,last_name']));
    }

    public function update(Request $request, FeedbackSubmission $feedback): JsonResponse
    {
        $data = $request->validate([
            'event_session_id' => ['sometimes', 'exists:event_sessions,id'],
            'attendee_id' => ['sometimes', 'exists:attendees,id'],
            'channel' => ['sometimes', 'in:qr,mobile,website,other'],
            'star_rating' => ['sometimes', 'integer', 'min:1', 'max:5'],
            'review_text' => ['nullable', 'string'],
            'key_takeaway' => ['nullable', 'string'],
            'sentiment_label' => ['nullable', 'in:positive,neutral,negative'],
            'sentiment_score' => ['nullable', 'numeric', 'between:-1,1'],
        ]);

        $feedback->update($data);
        return response()->json($feedback);
    }

    public function destroy(FeedbackSubmission $feedback): JsonResponse
    {
        $feedback->delete();
        return response()->json(null, 204);
    }

    public function kpis(): JsonResponse
    {
        $total = FeedbackSubmission::count();
        $avg = round((float) FeedbackSubmission::avg('star_rating'), 2);
        $positive = FeedbackSubmission::where('sentiment_label', 'positive')->count();
        $negative = FeedbackSubmission::where('sentiment_label', 'negative')->count();

        return response()->json([
            'total_submissions' => $total,
            'avg_rating' => $avg,
            'positive_count' => $positive,
            'negative_count' => $negative,
        ]);
    }

    public function latest(Request $request): JsonResponse
    {
        $limit = (int) $request->integer('limit', 10);
        return response()->json(
            FeedbackSubmission::with(['session:id,title', 'attendee:id,first_name,last_name'])
                ->orderByDesc('submitted_at')->limit($limit)->get()
        );
    }
}
