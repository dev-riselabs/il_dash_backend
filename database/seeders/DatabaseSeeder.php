<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);

        $admin = User::firstOrCreate(
            ['email' => 'admin@risenetworks.org'],
            [
                'name' => 'IL-DASH Admin',
                'password' => bcrypt('Password123!'),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('admin');

        $this->call([
            ProgrammeFoundationSeeder::class,
            EngagementSeeder::class,
            InvestmentSeeder::class,
            OperationsSeeder::class,
            SocialSeeder::class,
        ]);
    }
}
