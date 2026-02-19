<?php

namespace Tests\Feature;

use App\Jobs\AnalyzeCVJob;
use App\Models\CvAnalysis;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CVAnalyzerTest extends TestCase
{
    use RefreshDatabase;

    public function test_cv_analyzer_page_is_accessible()
    {
        $response = $this->get(route('cv-analyzer.index'));

        $response->assertStatus(200);
    }

    public function test_cv_analyzer_submission_validation()
    {
        $response = $this->postJson(route('cv-analyzer.analyze'), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'cv', 'job_title', 'job_description', 'industry', 'experience_level', 'target_country', 'current_career_level'
        ]);
    }

    public function test_successful_cv_submission_dispatches_job()
    {
        Queue::fake();
        Storage::fake('local');

        $file = UploadedFile::fake()->create('resume.pdf', 500);

        $data = [
            'cv' => $file,
            'job_title' => 'Senior Developer',
            'job_description' => 'We need a senior developer with PHP experience.',
            'industry' => 'Information Technology',
            'experience_level' => 'Senior Level (5 to 10 years)',
            'target_company' => 'Google',
            'target_country' => 'USA',
            'current_career_level' => 'Senior',
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'analysis_preferences' => ['ATS Compatibility'],
        ];

        $response = $this->postJson(route('cv-analyzer.analyze'), $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('cv_analyses', [
            'status' => 'pending',
            'user_details->name' => 'John Doe',
            'job_details->target_company' => 'Google',
        ]);

        Queue::assertPushed(AnalyzeCVJob::class);
    }

    public function test_can_check_analysis_status()
    {
        $analysis = CvAnalysis::factory()->completed()->create();

        $response = $this->getJson(route('cv-analyzer.status', ['uuid' => $analysis->uuid]));

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'completed',
            'report' => $analysis->report,
        ]);
    }
}
