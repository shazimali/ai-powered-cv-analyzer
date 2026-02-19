<?php

namespace App\Http\Controllers;

use App\Services\DocumentService;
use App\Services\OllamaService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CVAnalyzerController extends Controller
{
    protected DocumentService $documentService;
    protected OllamaService $ollama;

    public function __construct(DocumentService $documentService, OllamaService $ollama)
    {
        $this->documentService = $documentService;
        $this->ollama = $ollama;
    }

    public function index()
    {
        return Inertia::render('CVAnalyzer/Index');
    }

    public function analyze(Request $request)
    {
        $validated = $request->validate([
            'cv' => 'required|file|mimes:pdf,docx|max:5120',
            'job_title' => 'required|string|max:255',
            'job_description' => 'required|string',
            'target_company' => 'nullable|string|max:255',
            'industry' => 'required|string',
            'experience_level' => 'required|string',
            'analysis_preferences' => 'nullable|array',
            'target_country' => 'required|string',
            'current_career_level' => 'required|string',
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
        ]);

        // Store the file
        $path = $request->file('cv')->store('cvs');

        // Create Analysis record
        $analysis = \App\Models\CvAnalysis::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'status' => 'pending',
            'cv_path' => $path,
            'user_details' => [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'target_country' => $validated['target_country'],
                'current_career_level' => $validated['current_career_level'],
                'analysis_preferences' => $validated['analysis_preferences'],
            ],
            'job_details' => [
                'job_title' => $validated['job_title'],
                'job_description' => $validated['job_description'],
                'target_company' => $validated['target_company'] ?? 'the Employer',
                'industry' => $validated['industry'],
                'experience_level' => $validated['experience_level'],
            ],
        ]);

        // Dispatch background job
        \App\Jobs\AnalyzeCVJob::dispatch($analysis);

        return response()->json([
            'uuid' => $analysis->uuid,
            'message' => 'Analysis started in background.',
        ]);
    }

    public function status($uuid)
    {
        $analysis = \App\Models\CvAnalysis::where('uuid', $uuid)->firstOrFail();

        return response()->json([
            'status' => $analysis->status,
            'report' => $analysis->report,
        ]);
    }
}
