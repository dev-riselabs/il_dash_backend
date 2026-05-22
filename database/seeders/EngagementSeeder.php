<?php

namespace Database\Seeders;

use App\Models\AttendanceSnapshot;
use App\Models\Attendee;
use App\Models\EventDay;
use App\Models\EventSession;
use App\Models\FeedbackSubmission;
use App\Models\LivePoll;
use App\Models\Resolution;
use App\Models\Sector;
use App\Models\SentimentScore;
use App\Models\SessionInsight;
use App\Models\SessionQuote;
use App\Models\Track;
use Illuminate\Database\Seeder;

class EngagementSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedFeedback();
        $this->seedResolutions();
        $this->seedQuotesAndInsights();
        $this->seedLivePolls();
        $this->seedAttendanceSnapshots();
        $this->seedSentimentScores();
    }

    private function seedFeedback(): void
    {
        $sessions = EventSession::all();
        $attendeeIds = Attendee::pluck('id')->all();
        $labels = ['positive', 'neutral', 'negative'];
        $takeaways = [
            'Actionable insights on capital allocation.',
            'Brilliant panel - really opened up new perspectives.',
            'Wished the Q&A was longer.', 'Solid keynote, clear vision.',
            'Some slides were hard to read from the back.',
            'Game-changer for our SME strategy.',
            'Great moderation kept the discussion sharp.',
        ];

        foreach ($sessions as $session) {
            $count = random_int(15, 60);
            for ($i = 0; $i < $count; $i++) {
                $rating = random_int(2, 5);
                $label = $rating >= 4 ? 'positive' : ($rating === 3 ? 'neutral' : 'negative');
                FeedbackSubmission::create([
                    'attendee_id' => $attendeeIds[array_rand($attendeeIds)],
                    'event_session_id' => $session->id,
                    'channel' => fake()->randomElement(['qr', 'mobile', 'website']),
                    'star_rating' => $rating,
                    'review_text' => fake()->sentence(10),
                    'key_takeaway' => fake()->randomElement($takeaways),
                    'sentiment_label' => $label,
                    'sentiment_score' => $label === 'positive' ? fake()->randomFloat(3, 0.3, 0.95)
                        : ($label === 'neutral' ? fake()->randomFloat(3, -0.15, 0.15)
                        : fake()->randomFloat(3, -0.95, -0.3)),
                    'submitted_at' => $session->starts_at->copy()->addMinutes(random_int(20, 90)),
                ]);
            }
        }
    }

    private function seedResolutions(): void
    {
        $sessions = EventSession::all();
        $sectorIds = Sector::pluck('id')->all();
        $trackIds = Track::pluck('id')->all();
        $categories = ['commitment', 'partnership', 'policy', 'keynote', 'panel'];
        $stages = ['commitment', 'negotiation', 'signed', 'fulfilled'];
        $orgs = ['Federal Ministry of Finance', 'Sterling Bank', 'AfDB', 'World Bank', 'Dangote Group',
            'Andela', 'Flutterwave', 'IFC Africa', 'Reliance HMO', 'AgriTech Hub'];
        $titles = [
            '$50M Agritech Investment Pledge', 'MoU on Cross-Border Payments',
            'National SME Credit Guarantee Scheme', 'Public-Private Healthcare Compact',
            'Digital Skills Fund of $20M', 'Renewable Energy Off-Grid Partnership',
            'Women-Led Business Capital Fund', 'Logistics Corridor Joint Venture',
            'Affordable Housing Bond Issuance', 'EdTech Curriculum Reform Pledge',
        ];

        foreach ($titles as $i => $title) {
            Resolution::create([
                'event_session_id' => $sessions[$i % $sessions->count()]->id,
                'track_id' => $trackIds[$i % count($trackIds)],
                'sector_id' => $sectorIds[$i % count($sectorIds)],
                'title' => $title,
                'description' => "Commitment to {$title} announced during the session.",
                'category' => $categories[$i % count($categories)],
                'committed_by' => $orgs[$i % count($orgs)],
                'stage' => $stages[$i % count($stages)],
                'status' => fake()->randomElement(['open', 'in_progress', 'completed']),
                'estimated_impact_naira' => random_int(500, 50_000) * 1_000_000_00, // kobo
                'recorded_at' => now()->subMinutes(random_int(10, 4000)),
            ]);
        }
    }

    private function seedQuotesAndInsights(): void
    {
        $quotes = [
            'Africa\'s growth story is no longer aspirational - it is operational.',
            'Capital follows clarity, not noise.',
            'The next billion users will be served by African platforms.',
            'Policy certainty is the most underrated investment incentive.',
            'We must build local capacity before exporting solutions.',
        ];
        foreach (EventSession::take(8)->get() as $session) {
            $first = $session->speakers()->first();
            if (!$first) {
                continue;
            }
            SessionQuote::create([
                'event_session_id' => $session->id, 'speaker_id' => $first->id,
                'quote_text' => fake()->randomElement($quotes),
                'said_at' => $session->starts_at->copy()->addMinutes(random_int(10, 60)),
            ]);
            foreach (range(1, 3) as $order) {
                SessionInsight::create([
                    'event_session_id' => $session->id,
                    'body' => fake()->sentence(14),
                    'kind' => $order === 1 ? 'theme' : 'insight',
                    'order' => $order,
                ]);
            }
        }
    }

    private function seedLivePolls(): void
    {
        foreach (EventSession::where('status', 'live')->orWhere('status', 'completed')->take(5)->get() as $session) {
            LivePoll::create([
                'event_session_id' => $session->id,
                'prompt' => 'How would you rate this session so far?',
                'options' => ['Excellent', 'Good', 'Average', 'Not Good'],
                'status' => $session->status === 'live' ? 'open' : 'closed',
                'opened_at' => $session->starts_at,
                'closed_at' => $session->status === 'live' ? null : $session->ends_at,
            ]);
        }
    }

    private function seedAttendanceSnapshots(): void
    {
        foreach (EventDay::all() as $day) {
            $base = random_int(800, 1500);
            for ($h = 8; $h <= 18; $h++) {
                $captured = $day->date->copy()->setTime($h, 0);
                $inP = $base + random_int(-200, 400);
                $virt = $base + random_int(100, 700);
                AttendanceSnapshot::create([
                    'event_day_id' => $day->id, 'captured_at' => $captured,
                    'in_person' => max(0, $inP), 'virtual' => max(0, $virt),
                    'total' => max(0, $inP) + max(0, $virt),
                ]);
            }
        }
    }

    private function seedSentimentScores(): void
    {
        for ($d = 6; $d >= 0; $d--) {
            $cap = now()->subDays($d);
            $pos = fake()->randomFloat(2, 55, 78);
            $neg = fake()->randomFloat(2, 5, 18);
            $neu = round(100 - $pos - $neg, 2);
            SentimentScore::create([
                'scope' => 'overall', 'scope_id' => null, 'captured_at' => $cap,
                'positive_pct' => $pos, 'neutral_pct' => $neu, 'negative_pct' => $neg,
                'score_0_100' => (int) round($pos - $neg / 2 + 30),
            ]);
        }
        foreach (Sector::all() as $sector) {
            $pos = fake()->randomFloat(2, 45, 80);
            $neg = fake()->randomFloat(2, 5, 20);
            SentimentScore::create([
                'scope' => 'sector', 'scope_id' => $sector->id, 'captured_at' => now(),
                'positive_pct' => $pos, 'neutral_pct' => round(100 - $pos - $neg, 2),
                'negative_pct' => $neg, 'score_0_100' => (int) round($pos - $neg / 2 + 25),
            ]);
        }
    }
}
