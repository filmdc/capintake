<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\NpiGoal;
use App\Models\NpiIndicator;
use Illuminate\Database\Seeder;

class NpiSeeder extends Seeder
{
    public function run(): void
    {
        $goals = [
            [
                'goal_number' => 1,
                'name' => 'Employment',
                'description' => 'Low-income people become more self-sufficient through employment.',
                'indicators' => [
                    ['code' => '1.1', 'name' => 'Unemployed and obtained employment', 'description' => 'Number of unemployed participants who obtained employment.'],
                    ['code' => '1.2', 'name' => 'Employed and maintained employment for 90 days', 'description' => 'Number of employed participants who maintained or increased employment.'],
                    ['code' => '1.3', 'name' => 'Employed and obtained an increase in income/benefits', 'description' => 'Number of employed participants who obtained an increase in income or benefits.'],
                ],
            ],
            [
                'goal_number' => 2,
                'name' => 'Education and Cognitive Development',
                'description' => 'Low-income people, including children, become more self-sufficient through education and cognitive development.',
                'indicators' => [
                    ['code' => '2.1', 'name' => 'Obtained a high school diploma or equivalent', 'description' => 'Number of participants who obtained a high school diploma or equivalency (GED).'],
                    ['code' => '2.2', 'name' => 'Obtained a post-secondary degree or certification', 'description' => 'Number of participants who completed a post-secondary education program or received a degree.'],
                    ['code' => '2.3', 'name' => 'Children 0-5 who demonstrated school readiness', 'description' => 'Number of children (0-5) who demonstrated improved readiness for school or school-related achievement.'],
                    ['code' => '2.4', 'name' => 'Youth who achieved educational outcomes', 'description' => 'Number of youth who improved academic performance or progressed toward educational goals.'],
                ],
            ],
            [
                'goal_number' => 3,
                'name' => 'Income and Asset Building',
                'description' => 'Low-income people become more self-sufficient through income and asset building.',
                'indicators' => [
                    ['code' => '3.1', 'name' => 'Obtained federal or state income tax credit', 'description' => 'Number of participants who obtained federal or state income tax credits (EITC, CTC, etc.).'],
                    ['code' => '3.2', 'name' => 'Obtained non-emergency TANF assistance', 'description' => 'Number of participants who accessed non-emergency Temporary Assistance for Needy Families.'],
                    ['code' => '3.3', 'name' => 'Obtained other federal, state, or local benefits', 'description' => 'Number of participants who obtained or maintained other federal/state/local benefits (SNAP, Medicaid, etc.).'],
                    ['code' => '3.4', 'name' => 'Increased savings or assets', 'description' => 'Number of participants who increased their savings or built assets.'],
                ],
            ],
            [
                'goal_number' => 4,
                'name' => 'Housing',
                'description' => 'Low-income people become more self-sufficient through housing stability.',
                'indicators' => [
                    ['code' => '4.1', 'name' => 'Obtained safe and affordable housing', 'description' => 'Number of participants who obtained safe, affordable, and stable housing.'],
                    ['code' => '4.2', 'name' => 'Maintained safe and affordable housing for 180 days', 'description' => 'Number of participants who maintained housing for at least 180 days.'],
                    ['code' => '4.3', 'name' => 'Avoided eviction or foreclosure', 'description' => 'Number of participants who avoided eviction or utility shutoff through assistance.'],
                ],
            ],
            [
                'goal_number' => 5,
                'name' => 'Health and Social/Behavioral Development',
                'description' => 'Low-income people, including children, become more self-sufficient through health and social/behavioral development.',
                'indicators' => [
                    ['code' => '5.1', 'name' => 'Obtained health care services', 'description' => 'Number of participants who obtained access to health care services or health insurance.'],
                    ['code' => '5.2', 'name' => 'Obtained health insurance coverage', 'description' => 'Number of uninsured participants who obtained health insurance.'],
                    ['code' => '5.3', 'name' => 'Demonstrated improved physical health and well-being', 'description' => 'Number of participants who improved their physical health or well-being.'],
                    ['code' => '5.4', 'name' => 'Demonstrated improved mental/behavioral health', 'description' => 'Number of participants who demonstrated improved mental or behavioral health and development.'],
                ],
            ],
            [
                'goal_number' => 6,
                'name' => 'Civic Engagement and Community Involvement',
                'description' => 'Low-income people, including children, achieve their potential through civic engagement and community involvement.',
                'indicators' => [
                    ['code' => '6.1', 'name' => 'Increased community involvement', 'description' => 'Number of participants who contributed volunteer hours or engaged in community activities.'],
                    ['code' => '6.2', 'name' => 'Increased civic participation', 'description' => 'Number of participants who engaged in civic participation activities.'],
                    ['code' => '6.3', 'name' => 'Youth involved in leadership or community service', 'description' => 'Number of youth involved in leadership development or community service programs.'],
                ],
            ],
            [
                'goal_number' => 7,
                'name' => 'Services Supporting Multiple Domains',
                'description' => 'Low-income people, especially vulnerable populations, achieve outcomes across multiple domains.',
                'indicators' => [
                    ['code' => '7.1', 'name' => 'Emergency assistance for immediate needs', 'description' => 'Number of individuals receiving emergency assistance (food, shelter, clothing, utilities, etc.).'],
                    ['code' => '7.2', 'name' => 'Emergency food assistance', 'description' => 'Number of individuals who received emergency food assistance.'],
                    ['code' => '7.3', 'name' => 'Emergency fuel/utility assistance', 'description' => 'Number of individuals who received emergency fuel or utility assistance.'],
                    ['code' => '7.4', 'name' => 'Emergency rent/mortgage assistance', 'description' => 'Number of individuals who received emergency rent or mortgage assistance.'],
                    ['code' => '7.5', 'name' => 'Emergency medical care assistance', 'description' => 'Number of individuals who received emergency medical or prescription assistance.'],
                    ['code' => '7.6', 'name' => 'Emergency temporary shelter', 'description' => 'Number of individuals who received emergency temporary shelter or were prevented from becoming homeless.'],
                ],
            ],
        ];

        foreach ($goals as $goalData) {
            $indicators = $goalData['indicators'];
            unset($goalData['indicators']);

            $goal = NpiGoal::create($goalData);

            foreach ($indicators as $indicator) {
                $goal->indicators()->create([
                    'indicator_code' => $indicator['code'],
                    'name' => $indicator['name'],
                    'description' => $indicator['description'],
                ]);
            }
        }
    }
}
