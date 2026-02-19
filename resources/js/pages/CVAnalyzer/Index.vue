<script setup lang="ts">
import { home } from '@/routes';
import { analyze, status as getStatus } from '@/routes/cv-analyzer';
import { Head, Link, useForm } from '@inertiajs/vue3';
import axios from 'axios';
import { marked } from 'marked';
import { computed, nextTick, onUnmounted, ref, watch } from 'vue';

interface Props {
    flash?: {
        analysis_report?: string;
    };
    analysis_report?: string;
}

const props = defineProps<Props>();

const analysisUuid = ref<string | null>(null);
const analysisStatus = ref<string | null>(null);
const internalReport = ref<string | null>(null);
const pollingInterval = ref<ReturnType<typeof setInterval> | null>(null);

const report = computed(() => internalReport.value || props.analysis_report || props.flash?.analysis_report);
const renderedReport = computed(() => {
    if (!report.value) return '';
    
    // Replace markers with styled spans before/after markdown parsing
    // Using global, case-insensitive regex to handle variations
    let content = report.value;
    content = content.replace(/\[GOOD\]/gi, '<span class="status-marker marker-good">GOOD</span>');
    content = content.replace(/\[BAD\]/gi, '<span class="status-marker marker-bad">BAD</span>');
    
    return marked(content);
});

const resultsSection = ref<HTMLElement | null>(null);

watch(report, (newVal) => {
    if (newVal) {
        nextTick(() => {
            resultsSection.value?.scrollIntoView({ behavior: 'smooth' });
        });
    }
});

onUnmounted(() => {
    if (pollingInterval.value) clearInterval(pollingInterval.value);
});

const form = useForm({
    cv: null as File | null,
    job_title: '',
    job_description: '',
    target_company: '',
    industry: '',
    experience_level: '',
    analysis_preferences: [] as string[],
    target_country: '',
    current_career_level: '',
    name: '',
    email: '',
});

const isDragging = ref(false);
const fileInput = ref<HTMLInputElement | null>(null);

const handleFileSelect = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        form.cv = target.files[0] as any;
    }
};

const handleDrop = (event: DragEvent) => {
    isDragging.value = false;
    if (event.dataTransfer?.files && event.dataTransfer.files[0]) {
        form.cv = event.dataTransfer.files[0] as any;
    }
};

const startPolling = (uuid: string) => {
    analysisUuid.value = uuid;
    pollingInterval.value = setInterval(async () => {
        try {
            const response = await axios.get(getStatus.url({ uuid }));
            analysisStatus.value = response.data.status;
            
            if (response.data.status === 'completed') {
                internalReport.value = response.data.report;
                if (pollingInterval.value) clearInterval(pollingInterval.value);
                form.processing = false;
            } else if (response.data.status === 'failed') {
                if (pollingInterval.value) clearInterval(pollingInterval.value);
                form.processing = false;
                alert('Analysis failed. Please try again.');
            }
        } catch (error) {
            console.error('Polling error:', error);
        }
    }, 3000);
};

const submit = async () => {
    if (form.processing) return;
    
    if (!form.cv) {
        form.errors.cv = 'The CV field is required.';
        return;
    }

    form.processing = true;
    form.errors = {};
    internalReport.value = null;
    analysisStatus.value = 'pending';

    const formData = new FormData();
    formData.append('cv', form.cv);
    formData.append('job_title', form.job_title);
    formData.append('job_description', form.job_description);
    formData.append('target_company', form.target_company);
    formData.append('industry', form.industry);
    formData.append('experience_level', form.experience_level);
    formData.append('target_country', form.target_country);
    formData.append('current_career_level', form.current_career_level);
    formData.append('name', form.name);
    formData.append('email', form.email);
    form.analysis_preferences.forEach(pref => formData.append('analysis_preferences[]', pref));

    try {
        const response = await axios.post(analyze.url(), formData);
        startPolling(response.data.uuid);
    } catch (error: any) {
        form.processing = false;
        analysisStatus.value = null;
        if (error.response?.data?.errors) {
            form.errors = error.response.data.errors;
        } else {
            alert('An error occurred while starting the analysis.');
        }
    }
};

const industries = [
    'Information Technology',
    'Finance & Banking',
    'Healthcare',
    'Marketing & Sales',
    'Education',
    'Engineering',
    'Other'
];

const experienceLevels = [
    'Entry Level (0 to 2 years)',
    'Mid Level (2 to 5 years)',
    'Senior Level (5 to 10 years)',
    'Lead / Manager Level'
];

const careerLevels = [
    'Student / Fresh Graduate',
    'Junior',
    'Mid Level',
    'Senior',
    'Manager / Lead'
];

const countries = [
    'USA', 'UK', 'Pakistan', 'UAE', 'Canada', 'Australia', 'Germany', 'Other'
];

const preferenceOptions = [
    'ATS Compatibility',
    'Skills Match',
    'Grammar & Language',
    'CV Structure & Formatting',
    'Missing Keywords',
    'Overall Score'
];
</script>

<template>
    <Head title="CV Analyzer" />

    <div class="min-h-screen bg-slate-950 text-slate-100 p-4 md:p-8 selection:bg-blue-500/30">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-12 gap-4">
                <div>
                    <h1 class="text-4xl font-extrabold bg-gradient-to-r from-blue-400 via-purple-400 to-pink-500 bg-clip-text text-transparent mb-2">
                        AI CV Analyzer
                    </h1>
                    <p class="text-slate-400 text-sm md:text-base">Optimized for global standards and ATS compatibility.</p>
                </div>
                    <Link :href="home.url()" class="px-5 py-2 bg-slate-900/50 hover:bg-slate-800 rounded-xl transition-all border border-slate-800 text-sm font-medium backdrop-blur-sm">
                    Back to Home
                </Link>
            </div>

            <form @submit.prevent="submit" class="space-y-8">
                <!-- Section 1: CV Upload -->
                <div class="bg-slate-900/40 border border-slate-800/60 rounded-3xl p-6 md:p-8 backdrop-blur-2xl shadow-2xl relative overflow-hidden group">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-purple-500/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    
                    <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-blue-500/20 flex items-center justify-center text-blue-400 text-sm">01</span>
                        CV / Resume Upload
                    </h2>

                    <div 
                        @dragover.prevent="isDragging = true"
                        @dragleave.prevent="isDragging = false"
                        @drop.prevent="handleDrop"
                        :class="[
                            'border-2 border-dashed rounded-2xl p-10 text-center transition-all cursor-pointer relative',
                            isDragging ? 'border-blue-500 bg-blue-500/10' : 'border-slate-800 hover:border-slate-700 hover:bg-slate-800/20'
                        ]"
                        @click="() => fileInput?.click()"
                    >
                        <input 
                            ref="fileInput"
                            type="file" 
                            class="hidden" 
                            accept=".pdf,.docx"
                            @change="handleFileSelect"
                        >
                        
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-16 h-16 bg-slate-800 rounded-2xl flex items-center justify-center text-slate-400 mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                            </div>
                            <p v-if="!form.cv" class="text-lg font-medium">Drag and drop your CV here</p>
                            <p v-else class="text-lg font-medium text-blue-400">{{ form.cv?.name }}</p>
                            <p class="text-slate-500 text-sm">Supported formats: <span class="text-slate-300">PDF, DOCX</span> • Max: <span class="text-slate-300">5MB</span></p>
                        </div>
                    </div>
                    <div v-if="form.errors.cv" class="mt-4 text-red-500 text-sm">{{ form.errors.cv }}</div>
                </div>

                <!-- Section 2: Job Details -->
                <div class="bg-slate-900/40 border border-slate-800/60 rounded-3xl p-6 md:p-8 backdrop-blur-2xl shadow-2xl relative overflow-hidden group">
                    <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-purple-500/20 flex items-center justify-center text-purple-400 text-sm">02</span>
                        Job Details
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-400 ml-1">Job Title</label>
                            <input 
                                v-model="form.job_title"
                                type="text"
                                placeholder="e.g. Senior Laravel Developer"
                                class="w-full bg-slate-950/50 border border-slate-800 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 outline-none transition-all"
                            >
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-400 ml-1">Target Company / Department</label>
                            <input 
                                v-model="form.target_company"
                                type="text"
                                placeholder="e.g. Google HR Dept"
                                class="w-full bg-slate-950/50 border border-slate-800 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 outline-none transition-all"
                            >
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-400 ml-1">Industry / Field</label>
                            <select 
                                v-model="form.industry"
                                class="w-full bg-slate-950/50 border border-slate-800 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 outline-none transition-all appearance-none"
                            >
                                <option value="" disabled>Select Industry</option>
                                <option v-for="item in industries" :key="item" :value="item">{{ item }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-2 mb-6">
                        <label class="text-sm font-semibold text-slate-400 ml-1">Job Description</label>
                        <textarea 
                            v-model="form.job_description"
                            rows="6"
                            placeholder="Paste the full job description here..."
                            class="w-full bg-slate-950/50 border border-slate-800 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 outline-none transition-all resize-none"
                        ></textarea>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-400 ml-1">Experience Level Applying For</label>
                        <select 
                            v-model="form.experience_level"
                            class="w-full bg-slate-950/50 border border-slate-800 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 outline-none transition-all appearance-none"
                        >
                            <option value="" disabled>Select Level</option>
                            <option v-for="level in experienceLevels" :key="level" :value="level">{{ level }}</option>
                        </select>
                    </div>
                </div>

                <!-- Section 3: Analysis Preferences -->
                <div class="bg-slate-900/40 border border-slate-800/60 rounded-3xl p-6 md:p-8 backdrop-blur-2xl shadow-2xl relative overflow-hidden group">
                    <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-pink-500/20 flex items-center justify-center text-pink-400 text-sm">03</span>
                        Analysis Preferences
                    </h2>

                    <div class="mb-8">
                        <label class="text-sm font-semibold text-slate-400 ml-1 mb-4 block">What to focus on?</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            <label v-for="pref in preferenceOptions" :key="pref" class="flex items-center gap-3 p-4 bg-slate-950/30 border border-slate-800/50 rounded-2xl cursor-pointer hover:bg-slate-800/30 transition-all border-l-4" :class="form.analysis_preferences.includes(pref) ? 'border-l-blue-500 bg-blue-500/5' : 'border-l-transparent'">
                                <input 
                                    type="checkbox" 
                                    :value="pref" 
                                    v-model="form.analysis_preferences"
                                    class="w-5 h-5 rounded border-slate-700 bg-slate-900 text-blue-500 focus:ring-blue-500/50"
                                >
                                <span class="text-sm font-medium">{{ pref }}</span>
                            </label>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-400 ml-1">Target Country</label>
                            <select 
                                v-model="form.target_country"
                                class="w-full bg-slate-950/50 border border-slate-800 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 outline-none transition-all appearance-none"
                            >
                                <option value="" disabled>Select Country</option>
                                <option v-for="country in countries" :key="country" :value="country">{{ country }}</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-400 ml-1">Current Career Level</label>
                            <select 
                                v-model="form.current_career_level"
                                class="w-full bg-slate-950/50 border border-slate-800 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 outline-none transition-all appearance-none"
                            >
                                <option value="" disabled>Select Your Current Level</option>
                                <option v-for="level in careerLevels" :key="level" :value="level">{{ level }}</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Personal Preferences -->
                <div class="bg-slate-900/40 border border-slate-800/60 rounded-3xl p-6 md:p-8 backdrop-blur-2xl shadow-2xl relative overflow-hidden group">
                    <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-green-500/20 flex items-center justify-center text-green-400 text-sm">04</span>
                        Personal Preferences
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-400 ml-1">Your Name</label>
                            <input 
                                v-model="form.name"
                                type="text"
                                placeholder="e.g. Ahmad"
                                class="w-full bg-slate-950/50 border border-slate-800 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 outline-none transition-all"
                            >
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-400 ml-1">Email Address</label>
                            <input 
                                v-model="form.email"
                                type="email"
                                placeholder="you@example.com"
                                class="w-full bg-slate-950/50 border border-slate-800 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 outline-none transition-all"
                            >
                        </div>
                    </div>
                    <p class="mt-4 text-xs text-slate-500 italic">We'll use this to personalize the report and optionally send it to you.</p>
                </div>

                <!-- Analysis Results Section -->
                <div v-if="renderedReport" ref="resultsSection" class="bg-indigo-900/40 border border-indigo-500/30 rounded-3xl p-6 md:p-8 backdrop-blur-2xl shadow-2xl relative overflow-hidden group animate-in fade-in slide-in-from-bottom-5 duration-700">
                    <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/10 to-blue-500/10 opacity-100 transition-opacity"></div>
                    
                    <h2 class="text-2xl font-black mb-8 flex items-center gap-3 text-indigo-100 relative z-10">
                        <span class="w-10 h-10 rounded-xl bg-indigo-500/20 flex items-center justify-center text-indigo-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                        Analysis Report
                    </h2>

                    <div 
                        class="prose prose-invert max-w-none relative z-10 report-content"
                        v-html="renderedReport"
                    ></div>
                </div>

                <!-- Submit Button -->
                <div class="pt-6">
                    <button 
                        type="submit"
                        :disabled="form.processing"
                        class="w-full py-5 bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 rounded-2xl font-black text-xl shadow-lg shadow-blue-500/20 hover:shadow-blue-500/40 hover:scale-[1.01] active:scale-[0.99] transition-all disabled:opacity-50 disabled:hover:scale-100 uppercase tracking-wider relative overflow-hidden"
                    >
                        <span :class="{ 'opacity-0': form.processing }">
                            {{ renderedReport ? 'Re-Analyze CV' : 'Start Comprehensive Analysis' }}
                        </span>
                        
                        <div v-if="form.processing" class="absolute inset-0 flex items-center justify-center gap-3">
                            <div class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                            <span v-if="analysisStatus === 'pending'">Analysis pending...</span>
                            <span v-else-if="analysisStatus === 'processing'">AI is analyzing your profile...</span>
                            <span v-else>Preparing analysis...</span>
                        </div>
                    </button>
                    <p v-if="form.processing" class="text-center mt-3 text-xs text-slate-500 animate-pulse">
                        <span v-if="analysisStatus === 'pending'">Waiting for processor...</span>
                        <span v-else-if="analysisStatus === 'processing'">This usually takes 1-2 minutes for deep analysis.</span>
                        <span v-else>Starting background analyst...</span>
                    </p>
                </div>
            </form>

            <!-- Footer -->
            <div class="mt-16 text-center text-slate-600 text-sm">
                <p>&copy; 2026 AI Chatbot Suite. All rights reserved.</p>
            </div>
        </div>
    </div>
</template>

<style scoped>
@reference "../../../css/app.css";

/* Custom styled scrollbar for the textarea if needed */
textarea::-webkit-scrollbar {
    width: 8px;
}
textarea::-webkit-scrollbar-track {
    background: transparent;
}
textarea::-webkit-scrollbar-thumb {
    background: #1e293b;
    border-radius: 4px;
}
textarea::-webkit-scrollbar-thumb:hover {
    background: #334155;
}

/* Report content styling */
.report-content :deep(h1) { @apply text-2xl font-bold mb-4 text-indigo-300 mt-6; }
.report-content :deep(h2) { @apply text-xl font-bold mb-3 text-indigo-400 mt-6; }
.report-content :deep(h3) { @apply text-lg font-bold mb-2 text-indigo-200 mt-4; }
.report-content :deep(p) { @apply mb-4 text-slate-300 leading-relaxed; }
.report-content :deep(ul) { @apply list-disc list-inside mb-4 space-y-2 text-slate-300; }
.report-content :deep(li) { @apply ml-4 uppercase text-xs tracking-wide font-medium text-slate-400; }
.report-content :deep(li) { @apply normal-case text-base tracking-normal font-normal text-slate-300; }
.report-content :deep(strong) { @apply text-indigo-200 font-semibold; }
.report-content :deep(blockquote) { @apply border-l-4 border-indigo-500/50 pl-4 italic my-4 text-slate-400; }

.report-content :deep(.status-marker) {
    @apply inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black mr-2 tracking-tighter;
}
.report-content :deep(.marker-good) {
    @apply bg-green-500/20 text-green-400 border border-green-500/30;
}
.report-content :deep(.marker-bad) {
    @apply bg-red-500/20 text-red-400 border border-red-500/30;
}
</style>
