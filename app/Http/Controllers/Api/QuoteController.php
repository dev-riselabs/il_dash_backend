<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SessionQuote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $q = SessionQuote::with(['session:id,title', 'speaker:id,first_name,last_name']);
        foreach (['event_session_id', 'speaker_id'] as $f) {
            if ($v = $request->input($f)) {
                $q->where($f, $v);
            }
        }
        if ($search = $request->string('search')->toString()) {
            $q->where('quote_text', 'like', "%{$search}%");
        }
        $perPage = (int) $request->integer('per_page', 25);
        return response()->json($q->orderByDesc('said_at')->paginate($perPage));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'event_session_id' => ['required', 'exists:event_sessions,id'],
            'speaker_id' => ['nullable', 'exists:speakers,id'],
            'quote_text' => ['required', 'string'],
            'said_at' => ['required', 'date'],
        ]);
        return response()->json(SessionQuote::create($data), 201);
    }

    public function destroy(SessionQuote $quote): JsonResponse
    {
        $quote->delete();
        return response()->json(null, 204);
    }
}
