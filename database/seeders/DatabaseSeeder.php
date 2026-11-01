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
        User::factory()->create([
            'name' => 'Accredited Student',
            'matric_no' => 'CPE/2021/1000',
            'email' => 'student@example.com',
            'email_verified_at' => now(),
            'terms_accepted_at' => now(),
        ]);

        $this->call([
            DepartmentStudentSeeder::class,
            ElectionSeeder::class,
        ]);
    }
}
