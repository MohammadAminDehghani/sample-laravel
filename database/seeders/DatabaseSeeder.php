<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            UniversitySeeder::class,
        ]);

        $universities = [
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
                            'Building, Civil and Environmental Engineering' => 'https://www.concordia.ca/ginacody/bcee.html',
                            'Centre for Engineering in Society' => 'https://www.concordia.ca/ginacody/ces.html',
                            'Computer Science and Software Engineering' => 'https://www.concordia.ca/ginacody/computer-science-software-engineering.html',
                            'Electrical and Computer Engineering' => 'https://www.concordia.ca/ginacody/electrical-computer-engineering.html',
                            'Mechanical, Industrial and Aerospace Engineering' => 'https://www.concordia.ca/ginacody/mechanical-industrial-aerospace-engineering.html',
                        ],
                    ],
                    [
                        'name' => 'Faculty of Fine Arts',
                        'url' => 'https://www.concordia.ca/finearts.html',
                        'departments' => [
                            'Art Education' => 'https://www.concordia.ca/finearts/art-education.html',
                            'Art History' => 'https://www.concordia.ca/finearts/art-history.html',
                            'Cinema' => 'https://www.concordia.ca/finearts/cinema.html',
                            'Contemporary Dance' => 'https://www.concordia.ca/finearts/contemporary-dance.html',
                            'Creative Arts Therapies' => 'https://www.concordia.ca/finearts/creative-arts-therapies.html',
                            'Design and Computation Arts' => 'https://www.concordia.ca/finearts/design-computation-arts.html',
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
                            'Accountancy' => 'https://www.concordia.ca/jmsb/accountancy.html',
                            'Finance' => 'https://www.concordia.ca/jmsb/finance.html',
                            'Management' => 'https://www.concordia.ca/jmsb/management.html',
                            'Marketing' => 'https://www.concordia.ca/jmsb/marketing.html',
                            'Supply Chain and Business Technology Management' => 'https://www.concordia.ca/jmsb/scbtm.html',
                        ],
                    ],
                    [
                        'name' => 'School of Graduate Studies',
                        'url' => 'https://www.concordia.ca/gradstudies.html',
                        'departments' => [
                            'Graduate Studies in Arts and Science' => 'https://www.concordia.ca/gradstudies/artsci.html',
                            'Graduate Studies in Engineering and Computer Science' => 'https://www.concordia.ca/gradstudies/ginacody.html',
                            'Graduate Studies in Fine Arts' => 'https://www.concordia.ca/gradstudies/finearts.html',
                            'Graduate Studies in Business' => 'https://www.concordia.ca/gradstudies/jmsb.html',
                            'Individualized Programs' => 'https://www.concordia.ca/gradstudies/individualized.html',
                        ],
                    ],
                    [
                        'name' => 'School of Health',
                        'url' => 'https://www.concordia.ca/schoolofhealth.html',
                        'departments' => [
                            'Department of Health, Kinesiology, and Applied Physiology' => 'https://www.concordia.ca/artsci/health-kinesiology-applied-physiology.html',
                            'Department of Exercise Science' => 'https://www.concordia.ca/artsci/exercise-science.html',
                            'Department of Health, Leisure, and Human Performance' => 'https://www.concordia.ca/artsci/health-leisure-human-performance.html',
                            'School of Community and Public Health' => 'https://www.concordia.ca/artsci/community-public-health.html',
                        ],
                    ],
                ],
            ],
        ];


    }
}
