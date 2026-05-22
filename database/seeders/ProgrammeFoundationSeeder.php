<?php

namespace Database\Seeders;

use App\Models\Attendee;
use App\Models\Event;
use App\Models\EventDay;
use App\Models\EventSession;
use App\Models\Sector;
use App\Models\Speaker;
use App\Models\Track;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProgrammeFoundationSeeder extends Seeder
{
    public function run(): void
    {
        $event = Event::firstOrCreate(
            ['slug' => 'il-summit-2026'],
            [
                'name' => 'Investment & Leadership Summit 2026',
                'description' => 'Africa\'s premier investment & leadership convening.',
                'location' => 'Lagos, Nigeria',
                'starts_at' => Carbon::parse('2026-05-10 09:00:00'),
                'ends_at' => Carbon::parse('2026-05-12 18:00:00'),
                'status' => 'live',
            ]
        );

        $days = [];
        foreach (range(1, 3) as $i) {
            $date = Carbon::parse('2026-05-10')->addDays($i - 1);
            $days[] = EventDay::firstOrCreate(
                ['event_id' => $event->id, 'day_no' => $i],
                ['date' => $date, 'label' => "Day {$i} ({$date->format('M j, Y')})"]
            );
        }

        $trackDefs = [
            ['Investment & Capital', '#3B82F6'],
            ['Policy & Governance', '#10B981'],
            ['Innovation & Tech', '#8B5CF6'],
            ['SME & Startups', '#F59E0B'],
            ['Sustainability', '#22C55E'],
        ];
        $tracks = [];
        foreach ($trackDefs as [$name, $color]) {
            $tracks[] = Track::firstOrCreate(
                ['event_id' => $event->id, 'slug' => Str::slug($name)],
                ['name' => $name, 'color' => $color]
            );
        }

        $sectorDefs = [
            ['Agriculture', '#22C55E'], ['Energy', '#F59E0B'], ['FinTech', '#3B82F6'],
            ['Healthcare', '#EF4444'], ['Education', '#8B5CF6'], ['Manufacturing', '#6366F1'],
            ['Real Estate', '#EAB308'], ['Logistics', '#06B6D4'],
        ];
        $sectors = [];
        foreach ($sectorDefs as [$name, $color]) {
            $sectors[] = Sector::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'color' => $color]
            );
        }

        $venueDefs = [
            ['Main Auditorium', 1200], ['Hall A', 400], ['Hall B', 400],
            ['Innovation Lab', 150], ['Deal Room', 80], ['Press Lounge', 60],
        ];
        $venues = [];
        foreach ($venueDefs as [$name, $cap]) {
            $venues[] = Venue::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'capacity' => $cap, 'status' => 'open']
            );
        }

        $speakerDefs = [
            ['Adaeze', 'Okonkwo', 'CEO', 'Sterling Bank', 'Nigeria'],
            ['Tunde', 'Adeyemi', 'Founder', 'Paystack Africa', 'Nigeria'],
            ['Amina', 'Mohammed', 'Minister', 'Federal Ministry of Finance', 'Nigeria'],
            ['Kwame', 'Mensah', 'Partner', 'Ventures Africa', 'Ghana'],
            ['Zanele', 'Dlamini', 'Director', 'African Development Bank', 'South Africa'],
            ['Chinwe', 'Eze', 'CTO', 'Flutterwave', 'Nigeria'],
            ['Olu', 'Akande', 'CEO', 'Dangote Group', 'Nigeria'],
            ['Fatima', 'Bello', 'Founder', 'AgriTech Hub', 'Nigeria'],
            ['Samuel', 'Owusu', 'VP', 'IFC Africa', 'Ghana'],
            ['Linda', 'Ngozi', 'Director', 'World Bank Lagos', 'Nigeria'],
            ['David', 'Nwankwo', 'CEO', 'Andela', 'Nigeria'],
            ['Esther', 'Adisa', 'Founder', 'Reliance HMO', 'Nigeria'],
        ];
        $speakers = [];
        foreach ($speakerDefs as [$fn, $ln, $title, $org, $country]) {
            $speakers[] = Speaker::firstOrCreate(
                ['email' => strtolower("{$fn}.{$ln}@example.com")],
                ['first_name' => $fn, 'last_name' => $ln, 'job_title' => $title,
                 'organization' => $org, 'country' => $country,
                 'bio' => "{$title} at {$org}. Recognized leader driving impact across Africa."]
            );
        }

        $this->seedAttendees($sectors);
        $this->seedSessions($event, $days, $tracks, $venues, $sectors, $speakers);
    }

    private function seedAttendees(array $sectors): void
    {
        $regions = ['West Africa', 'East Africa', 'Southern Africa', 'North Africa', 'Europe', 'North America', 'Asia'];
        $categories = ['Investor', 'Government', 'Private Sector', 'SME/Startup', 'Media', 'Academia'];
        $genders = ['male', 'female', 'other'];
        $countries = ['Nigeria', 'Ghana', 'Kenya', 'South Africa', 'Egypt', 'UK', 'USA', 'UAE', 'India'];

        for ($i = 1; $i <= 200; $i++) {
            $fn = fake()->firstName();
            $ln = fake()->lastName();
            Attendee::firstOrCreate(
                ['email' => strtolower("{$fn}.{$ln}.{$i}@example.com")],
                [
                    'first_name' => $fn, 'last_name' => $ln,
                    'job_title' => fake()->jobTitle(),
                    'organization' => fake()->company(),
                    'country' => fake()->randomElement($countries),
                    'region' => fake()->randomElement($regions),
                    'gender' => fake()->randomElement($genders),
                    'category' => fake()->randomElement($categories),
                    'checked_in_at' => fake()->boolean(70) ? now()->subMinutes(random_int(5, 600)) : null,
                    'is_new_today' => fake()->boolean(35),
                ]
            );
        }
    }

    private function seedSessions(Event $event, array $days, array $tracks, array $venues, array $sectors, array $speakers): void
    {
        $types = ['plenary', 'panel', 'keynote', 'roundtable', 'showcase'];
        $titles = [
            'Unlocking Capital for African SMEs', 'The Future of FinTech in Africa',
            'Climate-Smart Agriculture Investment', 'Public-Private Partnerships in Healthcare',
            'Bridging the Digital Skills Gap', 'Sovereign Wealth & Long-Term Capital',
            'Renewable Energy: Powering Tomorrow', 'Logistics & Trade Corridors',
            'Impact Investing for Sustainable Development', 'Building Pan-African Unicorns',
            'Manufacturing Renaissance', 'EdTech Beyond the Classroom',
            'Real Estate as an Asset Class', 'Cross-Border Payments at Scale',
            'Women Leading Africa\'s Capital Markets',
        ];

        $now = now();
        foreach ($titles as $idx => $title) {
            $day = $days[$idx % count($days)];
            $start = Carbon::parse($day->date)->setTime(9 + ($idx % 6), 0);
            $status = match (true) {
                $start->lt($now->copy()->subHour()) => 'completed',
                $start->lt($now->copy()->addMinutes(30)) => 'live',
                $start->lt($now->copy()->addHours(2)) => 'next',
                default => 'upcoming',
            };
            $session = EventSession::create([
                'event_id' => $event->id, 'event_day_id' => $day->id,
                'track_id' => $tracks[$idx % count($tracks)]->id,
                'venue_id' => $venues[$idx % count($venues)]->id,
                'sector_id' => $sectors[$idx % count($sectors)]->id,
                'title' => $title,
                'description' => 'Conversation on '.$title.' with leading practitioners.',
                'type' => $types[$idx % count($types)],
                'status' => $status,
                'starts_at' => $start, 'ends_at' => $start->copy()->addMinutes(75),
                'ai_summary' => $status === 'completed' ? "Key takeaway: {$title} unlocks new pathways for capital." : null,
                'attendance_in_person' => random_int(80, 600),
                'attendance_virtual' => random_int(120, 900),
                'average_rating_x10' => $status === 'completed' ? random_int(35, 49) : null,
            ]);
            $session->speakers()->sync([
                $speakers[$idx % count($speakers)]->id => ['role' => 'keynote'],
                $speakers[($idx + 1) % count($speakers)]->id => ['role' => 'panelist'],
                $speakers[($idx + 2) % count($speakers)]->id => ['role' => 'moderator'],
            ]);
        }
    }
}
