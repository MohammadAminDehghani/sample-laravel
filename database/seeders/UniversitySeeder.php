<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UniversitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $universitiesData = [
            [
                'name' => 'Concordia',
                'country' => 'Canada',
                'city' => 'Montreal',
                'rank' => '', // You can fill in the rank if available
                'url' => 'https://www.concordia.ca',
                'faculties' => [
                    [
                        'name' => 'Faculty of Arts and Science',
                        'url' => 'https://www.concordia.ca/artsci.html',
                        'departments' => [
                            'Biology' => 'https://www.concordia.ca/artsci/biology.html',
                            'Chemistry and Biochemistry' => 'https://www.concordia.ca/artsci/chemistry.html',
                            'Economics' => 'https://www.concordia.ca/artsci/economics.html',
                            'Education' => 'https://www.concordia.ca/artsci/education.html',
                            'English' => 'https://www.concordia.ca/artsci/english.html',
                            'Geography, Planning and Environment' => 'https://www.concordia.ca/artsci/geography-planning-environment.html',
                            'History' => 'https://www.concordia.ca/artsci/history.html',
                            'Mathematics and Statistics' => 'https://www.concordia.ca/artsci/math-stats.html',
                            'Philosophy' => 'https://www.concordia.ca/artsci/philosophy.html',
                            'Physics' => 'https://www.concordia.ca/artsci/physics.html',
                            'Political Science' => 'https://www.concordia.ca/artsci/polisci.html',
                            'Psychology' => 'https://www.concordia.ca/artsci/psychology.html',
                            'Religion' => 'https://www.concordia.ca/artsci/religions-cultures.html',
                            'Sociology and Anthropology' => 'https://www.concordia.ca/artsci/sociology-anthropology.html',
                        ],
                    ],
                    [
                        'name' => 'Gina Cody School of Engineering and Computer Science',
                        'url' => 'https://www.concordia.ca/ginacody.html',
                        'departments' => [
                            'Building, Civil and Environmental Engineering' => 'https://www.concordia.ca/ginacody/building-civil-environmental-eng.html',
                            'Centre for Engineering in Society' => 'https://www.concordia.ca/ginacody/engineering-in-society.html',
                            'Computer Science and Software Engineering' => 'https://www.concordia.ca/ginacody/computer-science-software-eng.html',
                            'Electrical and Computer Engineering' => 'https://www.concordia.ca/ginacody/electrical-computer-eng.html',
                            'Mechanical, Industrial and Aerospace Engineering' => 'https://www.concordia.ca/ginacody/mechanical-industrial-aerospace-eng.html',
                        ],
                    ],
                    [
                        'name' => 'Faculty of Fine Arts',
                        'url' => 'https://www.concordia.ca/finearts.html',
                        'departments' => [
                            'Art Education' => 'https://www.concordia.ca/finearts/art-education.html',
                            'Art History' => 'https://www.concordia.ca/finearts/art-history.html',
                            'Cinema' => 'https://www.concordia.ca/finearts/cinema.html',
                            'Contemporary Dance' => 'https://www.concordia.ca/finearts/dance.html',
                            'Creative Arts Therapies' => 'https://www.concordia.ca/finearts/creative-arts-therapies.html',
                            'Design and Computation Arts' => 'https://www.concordia.ca/finearts/design.html',
                            'Mel Hoppenheim School of Cinema' => 'https://www.concordia.ca/finearts/cinema.html',
                            'Music' => 'https://www.concordia.ca/finearts/music.html',
                            'Studio Arts' => 'https://www.concordia.ca/finearts/studio-arts.html',
                            'Theatre' => 'https://www.concordia.ca/finearts/theatre.html',
                        ],
                    ],
                    [
                        'name' => 'John Molson School of Business',
                        'url' => 'https://www.concordia.ca/jmsb.html',
                        'departments' => [
                            'Accountancy' => 'https://www.concordia.ca/jmsb/about/departments/accountancy.html',
                            'Finance' => 'https://www.concordia.ca/jmsb/about/departments/finance.html',
                            'Management' => 'https://www.concordia.ca/jmsb/about/departments/management.html',
                            'Marketing' => 'https://www.concordia.ca/jmsb/about/departments/marketing.html',
                            'Supply Chain and Business Technology Management' => 'https://www.concordia.ca/jmsb/about/departments/supply-chain-business-technology-management.html',
                        ],
                    ],
                ],
            ],
        ];

        foreach ($universitiesData as $universityData) {
            $university = \App\Models\University::factory()->create([
                'name' => $universityData['name'],
                'country' => $universityData['country'],
                'city' => $universityData['city'],
                'rank' => $universityData['rank'],
                'url' => $universityData['url'],
            ]);

            foreach ($universityData['faculties'] as $facultyData) {
                $faculty = \App\Models\Faculty::factory()->create([
                    'name' => $facultyData['name'],
                    'university_id' => $university->id,
                    'url' => $facultyData['url'],
                ]);

                foreach ($facultyData['departments'] as $departmentName => $departmentUrl) {
                    \App\Models\Department::factory()->create([
                        'name' => $departmentName,
                        'faculty_id' => $faculty->id,
                        'url' => $departmentUrl,
                    ]);
                }
            }
        }
    }
}
