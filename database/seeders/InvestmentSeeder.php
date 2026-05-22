<?php

namespace Database\Seeders;

use App\Models\Deal;
use App\Models\InvestmentSignal;
use App\Models\Investor;
use App\Models\Sector;
use App\Models\SectorInvestmentSummary;
use App\Models\User;
use Illuminate\Database\Seeder;

class InvestmentSeeder extends Seeder
{
    public function run(): void
    {
        $sectors = Sector::all();
        $owner = User::first();

        $investorDefs = [
            ['African Development Bank', 'institutional', 'Ivory Coast', 'Africa'],
            ['IFC Africa', 'institutional', 'USA', 'North America'],
            ['Norfund', 'sovereign', 'Norway', 'Europe'],
            ['British International Investment', 'sovereign', 'UK', 'Europe'],
            ['Helios Investment Partners', 'VC', 'UK', 'Europe'],
            ['TLcom Capital', 'VC', 'UK', 'Europe'],
            ['Partech Africa', 'VC', 'France', 'Europe'],
            ['EchoVC Partners', 'VC', 'USA', 'North America'],
            ['Sterling Bank', 'bank', 'Nigeria', 'Africa'],
            ['Access Holdings', 'bank', 'Nigeria', 'Africa'],
            ['Dangote Group', 'corporate', 'Nigeria', 'Africa'],
            ['Saudi Public Investment Fund', 'sovereign', 'Saudi Arabia', 'Asia'],
            ['Nigeria Sovereign Investment Authority', 'sovereign', 'Nigeria', 'Africa'],
            ['Mubadala Investment', 'sovereign', 'UAE', 'Asia'],
            ['Goldman Sachs Africa', 'institutional', 'USA', 'North America'],
        ];

        $investors = collect();
        foreach ($investorDefs as [$name, $type, $country, $region]) {
            $investors->push(Investor::firstOrCreate(
                ['name' => $name],
                [
                    'type' => $type, 'country' => $country, 'region' => $region,
                    'logo_url' => null,
                    'sectors_of_interest' => $sectors->random(min(3, $sectors->count()))->pluck('slug')->all(),
                ]
            ));
        }

        $confidences = ['high', 'medium', 'low'];
        for ($i = 0; $i < 80; $i++) {
            InvestmentSignal::create([
                'investor_id' => $investors->random()->id,
                'sector_id' => $sectors->random()->id,
                'event_session_id' => null,
                'confidence' => $confidences[array_rand($confidences)],
                'estimated_value_naira' => random_int(50, 5000) * 1_000_000_00,
                'note' => fake()->sentence(10),
                'recorded_at' => now()->subMinutes(random_int(10, 6000)),
            ]);
        }

        foreach ($sectors as $sector) {
            $count = InvestmentSignal::where('sector_id', $sector->id)->count();
            $sum = (int) InvestmentSignal::where('sector_id', $sector->id)->sum('estimated_value_naira');
            SectorInvestmentSummary::create([
                'sector_id' => $sector->id,
                'signals_count' => $count,
                'estimated_value_naira' => $sum,
                'trend_percent' => fake()->randomFloat(2, -15, 35),
                'captured_at' => now(),
            ]);
        }

        $stages = ['discussion', 'negotiation', 'commitment', 'closed_won', 'closed_lost'];
        $titles = [
            'Series B - Pan-African Agritech', 'Project Finance - Solar Mini-Grids',
            'Bridge Round - Healthcare SaaS', 'Acquisition - Logistics Platform',
            'Series A - EdTech Curriculum', 'Debt Facility - SME Lending',
            'Series C - Cross-Border Payments', 'Joint Venture - Affordable Housing',
            'Project Bond - Industrial Park', 'Pre-Series A - Climate Tech',
            'Series A - Insurance Platform', 'Growth Equity - Manufacturing',
            'Sovereign Co-Investment - Renewables', 'Mezzanine - Real Estate REIT',
            'Convertible - Mobility Startup',
        ];
        foreach ($titles as $i => $title) {
            Deal::create([
                'title' => $title,
                'investor_id' => $investors->random()->id,
                'sector_id' => $sectors->random()->id,
                'stage' => $stages[$i % count($stages)],
                'value_naira' => random_int(200, 8000) * 1_000_000_00,
                'owner_id' => $owner?->id,
                'opened_at' => now()->subDays(random_int(0, 21)),
            ]);
        }
    }
}
