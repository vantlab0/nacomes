<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\Election;
use App\Models\Position;
use Illuminate\Database\Seeder;

class ElectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create or retrieve active session election
        $election = Election::firstOrCreate(
            ['title' => '2026/2027 NACOMES Executive Elections'],
            [
                'description' => 'Official general elections for the executive leadership of the National Association of Computer Engineering Students (NACOMES).',
                'status' => 'open',
                'results_published' => false,
                'starts_at' => now()->startOfDay(),
                'ends_at' => now()->addDays(2)->endOfDay(),
            ]
        );

        // 2. Positions & Candidates
        $positionsData = [
            [
                'title' => 'Office of the President',
                'description' => 'Chief Executive Officer representing the departmental student body at the faculty academic board and leading association initiatives.',
                'order' => 1,
                'candidates' => [
                    [
                        'name' => 'Adebayo Ogundipe',
                        'matric_no' => 'CPE/2021/1042',
                        'academic_level' => '400 Level',
                        'photo_url' => 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?auto=format&fit=crop&w=600&q=80',
                    ],
                    [
                        'name' => 'Chidinma Eze',
                        'matric_no' => 'CPE/2021/1188',
                        'academic_level' => '400 Level',
                        'photo_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=600&q=80',
                    ],
                ],
            ],
            [
                'title' => 'Vice President',
                'description' => 'Oversees internal student welfare, academic tutorial committees, and coordinates executive departmental programs.',
                'order' => 2,
                'candidates' => [
                    [
                        'name' => 'Fatima Aliyu',
                        'matric_no' => 'CPE/2022/1054',
                        'academic_level' => '300 Level',
                        'photo_url' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&w=600&q=80',
                    ],
                    [
                        'name' => 'Oluwaseun Adeleke',
                        'matric_no' => 'CPE/2022/1210',
                        'academic_level' => '300 Level',
                        'photo_url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=600&q=80',
                    ],
                ],
            ],
            [
                'title' => 'Director of Software & Projects',
                'description' => 'Directs departmental technical workshops, hackathons, open-source projects, and industry tech bootcamps.',
                'order' => 3,
                'candidates' => [
                    [
                        'name' => 'Farouk Olatunji',
                        'matric_no' => 'CPE/2022/1780',
                        'academic_level' => '300 Level',
                        'photo_url' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&w=600&q=80',
                    ],
                    [
                        'name' => 'Kelechi Bello',
                        'matric_no' => 'CPE/2022/1890',
                        'academic_level' => '300 Level',
                        'photo_url' => 'https://images.unsplash.com/photo-1522075469751-3a6694fb2f61?auto=format&fit=crop&w=600&q=80',
                    ],
                ],
            ],
            [
                'title' => 'General Secretary',
                'description' => 'Maintains official correspondence, meeting minutes, secretarial records, and association documentation.',
                'order' => 4,
                'candidates' => [
                    [
                        'name' => 'Emeka Nnamdi',
                        'matric_no' => 'CPE/2022/1301',
                        'academic_level' => '300 Level',
                        'photo_url' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=600&q=80',
                    ],
                    [
                        'name' => 'Zainab Bello',
                        'matric_no' => 'CPE/2022/1411',
                        'academic_level' => '300 Level',
                        'photo_url' => 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=600&q=80',
                    ],
                ],
            ],
            [
                'title' => 'Financial Secretary',
                'description' => 'Manages association dues, budget ledgers, sponsorship funds, and departmental financial accountability.',
                'order' => 5,
                'candidates' => [
                    [
                        'name' => 'Tariq Mohammed',
                        'matric_no' => 'CPE/2022/1550',
                        'academic_level' => '300 Level',
                        'photo_url' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&w=600&q=80',
                    ],
                    [
                        'name' => 'Blessing Okon',
                        'matric_no' => 'CPE/2022/1620',
                        'academic_level' => '300 Level',
                        'photo_url' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=600&q=80',
                    ],
                ],
            ],
            [
                'title' => 'Public Relations Officer',
                'description' => 'Directs communications, student announcements, social channels, and media coverage for NACOMES.',
                'order' => 6,
                'candidates' => [
                    [
                        'name' => 'Aisha Abubakar',
                        'matric_no' => 'CPE/2023/1020',
                        'academic_level' => '200 Level',
                        'photo_url' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=600&q=80',
                    ],
                    [
                        'name' => 'David Okafor',
                        'matric_no' => 'CPE/2023/1145',
                        'academic_level' => '200 Level',
                        'photo_url' => 'https://images.unsplash.com/photo-1492562080023-ab3db95bfbce?auto=format&fit=crop&w=600&q=80',
                    ],
                ],
            ],
        ];

        foreach ($positionsData as $pos) {
            $position = Position::firstOrCreate(
                [
                    'election_id' => $election->id,
                    'title' => $pos['title'],
                ],
                [
                    'description' => $pos['description'],
                    'display_order' => $pos['order'],
                    'is_published' => true,
                ]
            );

            foreach ($pos['candidates'] as $cand) {
                Candidate::firstOrCreate(
                    [
                        'election_id' => $election->id,
                        'position_id' => $position->id,
                        'name' => $cand['name'],
                    ],
                    [
                        'matric_no' => $cand['matric_no'],
                        'academic_level' => $cand['academic_level'],
                        'photo_url' => $cand['photo_url'],
                        'is_published' => true,
                    ]
                );
            }
        }
    }
}
