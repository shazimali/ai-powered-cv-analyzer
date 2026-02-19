<?php

namespace App\Jobs;

use App\Models\CvAnalysis;
use App\Services\DocumentService;
use App\Services\OllamaService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class AnalyzeCVJob implements ShouldQueue
{
    use Queueable;

    public $timeout = 300; // 5 minutes

    /**
     * Create a new job instance.
     */
    public function __construct(protected CvAnalysis $analysis)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(DocumentService $documentService, OllamaService $ollama): void
    {
        try {
            $this->analysis->update(['status' => 'processing']);

            // Extract text
            $cvText = $documentService->extractContent($this->analysis->cv_path);

            $jobDetails = $this->analysis->job_details;
            $userDetails = $this->analysis->user_details;
            $userName = $userDetails['name'] ?: 'Professional Career Analyst';
            $preferences = implode(', ', $userDetails['analysis_preferences'] ?? []);

            // Build the prompt
            $prompt = "
                You are an expert HR Consultant and ATS Specialist. Your task is to analyze a CV against a specific Job Description.
                
                USER DETAILS:
                Name: {$userName}
                Current Level: {$userDetails['current_career_level']}
                Focus Areas: {$preferences}
                Target Country: {$userDetails['target_country']}
                
                JOB CONTEXT:
                Job Title: {$jobDetails['job_title']}
                Target Company/Dept: {$jobDetails['target_company']}
                Industry: {$jobDetails['industry']}
                Experience Level: {$jobDetails['experience_level']}
                
                JOB DESCRIPTION:
                \"\"\"
                {$jobDetails['job_description']}
                \"\"\"
                
                RESUME / CV CONTENT:
                \"\"\"
                {$cvText}
                \"\"\"
                
                Please provide a comprehensive analysis. Structure your response as a professional report (Markdown format).
                
                HEADER:
                Begin the report with exactly these two lines:
                \"## Prepared for: {$jobDetails['target_company']}\"
                \"### Prepared by: {$userName}\"
                
                IMPORTANT: You MUST use these semantic markers for EVERY point:
                - Start EVERY positive point, strength, or matching skill with the exact tag `[GOOD]`.
                - Start EVERY negative point, gap, or improvement area with the exact tag `[BAD]`.
                
                Example:
                - [GOOD] Strong experience in PHP.
                - [BAD] Lacks AWS certification.
                
                Include these sections:
                1. **Overall Match Score** (0-100)
                2. **Key Strengths**
                3. **Missing Keywords & Skills**
                4. **Structure & Formatting Feedback**
                5. **Strategic Recommendations** (Tailored for {$userDetails['target_country']} standards)
                
                Be critical but constructive.
            ";

            $response = $ollama->chat([
                ['role' => 'system', 'content' => "You are an Elite Recruitment Analyst. You must ALWAYS follow the HEADER instructions exactly. Never refer to yourself as 'AI Career Coach' or use generic placeholders."],
                ['role' => 'user', 'content' => $prompt]
            ]);

            $report = $response['message']['content'] ?? 'Failed to generate analysis.';

            $this->analysis->update([
                'status' => 'completed',
                'report' => $report,
            ]);

        } catch (\Exception $e) {
            Log::error('CV Analysis Failed: ' . $e->getMessage());
            $this->analysis->update(['status' => 'failed']);
        }
    }
}
