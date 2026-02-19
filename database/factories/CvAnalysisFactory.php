<?php

namespace Database\Factories;

use App\Models\CvAnalysis;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CvAnalysis>
 */
class CvAnalysisFactory extends Factory
{
    protected $model = CvAnalysis::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'status' => 'pending',
            'report' => null,
            'user_details' => [
                'name' => $this->faker->name,
                'email' => $this->faker->safeEmail,
                'target_country' => 'USA',
                'current_career_level' => 'Senior',
                'analysis_preferences' => ['ATS Compatibility', 'Skills Match'],
            ],
            'job_details' => [
                'job_title' => $this->faker->jobTitle,
                'job_description' => $this->faker->paragraphs(3, true),
                'target_company' => $this->faker->company,
                'industry' => 'Information Technology',
                'experience_level' => 'Senior Level (5 to 10 years)',
            ],
            'cv_path' => 'cvs/fake_cv.pdf',
        ];
    }

    /**
     * Indicate that the analysis is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'report' => '# Analysis Report' . "\n\n" . '[GOOD] Strength 1' . "\n" . '[BAD] Gap 1',
        ]);
    }
}
