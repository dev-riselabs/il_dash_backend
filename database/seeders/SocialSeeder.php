<?php

namespace Database\Seeders;

use App\Models\MentionsTimeseries;
use App\Models\SocialHashtag;
use App\Models\SocialMention;
use App\Models\SocialTheme;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SocialSeeder extends Seeder
{
    public function run(): void
    {
        $themeDefs = ['Investment', 'Leadership', 'Innovation', 'Policy', 'Diversity', 'Sustainability', 'Youth'];
        $themes = collect();
        foreach ($themeDefs as $name) {
            $themes->push(SocialTheme::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'mention_count' => 0]
            ));
        }

        $hashtagDefs = ['#ILSummit2026', '#InvestInAfrica', '#FintechAfrica', '#AgriTech',
            '#WomenInLeadership', '#PolicyMatters', '#StartupAfrica', '#GreenEnergy',
            '#TradeAfrica', '#CapitalForGrowth'];
        $hashtags = collect();
        foreach ($hashtagDefs as $tag) {
            $hashtags->push(SocialHashtag::firstOrCreate(['tag' => $tag], ['mention_count' => 0]));
        }

        $platforms = ['twitter', 'linkedin', 'facebook', 'instagram', 'youtube'];
        $labels = ['positive', 'neutral', 'negative'];
        $locations = ['Lagos, NG', 'Accra, GH', 'Nairobi, KE', 'Cape Town, ZA', 'London, UK',
            'New York, US', 'Dubai, AE', 'Cairo, EG'];
        $sampleContent = [
            'Brilliant insights from the keynote at #ILSummit2026 - Africa is open for business.',
            'Excited about the new $50M agritech pledge announced today!',
            'Panel on cross-border payments was the highlight of Day 1. #FintechAfrica',
            'Wish the Q&A was longer but the moderator did a great job.',
            'Inspiring to see so many women leading capital markets conversations.',
            'Concerns about policy ambiguity - hope tomorrow\'s session addresses it.',
            'The Innovation Lab booth is buzzing - must-see startups.',
            'Hot take: the future of African manufacturing is closer than people think.',
        ];

        foreach (range(1, 120) as $i) {
            $platform = $platforms[array_rand($platforms)];
            $label = $labels[array_rand($labels)];
            $theme = $themes->random();
            $mentionHashtags = $hashtags->random(random_int(1, 3));
            SocialMention::create([
                'platform' => $platform,
                'author_handle' => '@'.fake()->userName(),
                'author_name' => fake()->name(),
                'author_avatar' => null,
                'author_verified' => fake()->boolean(20),
                'post_id' => (string) fake()->uuid(),
                'content' => $sampleContent[array_rand($sampleContent)],
                'posted_at' => now()->subMinutes(random_int(5, 7 * 24 * 60)),
                'likes' => random_int(2, 1500),
                'retweets' => random_int(0, 600),
                'comments' => random_int(0, 300),
                'impressions' => random_int(500, 200_000),
                'reach' => random_int(300, 150_000),
                'sentiment_label' => $label,
                'sentiment_score' => $label === 'positive' ? fake()->randomFloat(3, 0.3, 0.95)
                    : ($label === 'neutral' ? fake()->randomFloat(3, -0.15, 0.15)
                    : fake()->randomFloat(3, -0.95, -0.3)),
                'theme_id' => $theme->id,
                'hashtags' => $mentionHashtags->pluck('tag')->all(),
                'location' => $locations[array_rand($locations)],
            ]);
        }

        // Update aggregate counters.
        foreach ($themes as $theme) {
            $theme->update(['mention_count' => SocialMention::where('theme_id', $theme->id)->count()]);
        }
        foreach ($hashtags as $tag) {
            $tag->update([
                'mention_count' => SocialMention::query()
                    ->whereJsonContains('hashtags', $tag->tag)->count(),
            ]);
        }

        // Mentions timeseries: hourly buckets per platform for the last 7 days.
        for ($d = 6; $d >= 0; $d--) {
            for ($h = 0; $h < 24; $h += 4) {
                $cap = now()->subDays($d)->setTime($h, 0);
                foreach ($platforms as $p) {
                    MentionsTimeseries::create([
                        'captured_at' => $cap, 'platform' => $p,
                        'mentions_count' => random_int(5, 60),
                    ]);
                }
            }
        }
    }
}
