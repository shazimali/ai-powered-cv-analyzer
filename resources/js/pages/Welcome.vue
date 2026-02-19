<script setup lang="ts">
import { index as cvAnalyzerIndex } from '@/routes/cv-analyzer';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="AI CV Analyzer - Optimize Your Career" />
    
    <div class="min-h-screen bg-slate-950 text-slate-100 flex flex-col font-sans selection:bg-blue-500/30">
        <!-- Background Decorations -->
        <div class="fixed inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] bg-blue-600/10 blur-[120px] rounded-full"></div>
            <div class="absolute top-[20%] -right-[10%] w-[30%] h-[50%] bg-purple-600/10 blur-[120px] rounded-full"></div>
            <div class="absolute -bottom-[10%] left-[20%] w-[50%] h-[30%] bg-pink-600/10 blur-[120px] rounded-full"></div>
        </div>

        <!-- Navigation -->
        <nav class="relative z-10 flex justify-between items-center px-6 py-6 max-w-7xl mx-auto w-full">
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/20">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <span class="text-xl font-black tracking-tighter bg-gradient-to-r from-white to-slate-400 bg-clip-text text-transparent">CV ANALYZER</span>
            </div>
            <Link :href="cvAnalyzerIndex.url()" class="px-5 py-2.5 bg-white/5 hover:bg-white/10 rounded-full border border-white/10 text-sm font-semibold backdrop-blur-md transition-all">
                Launch App
            </Link>
        </nav>

        <!-- Hero Section -->
        <main class="relative z-10 flex-grow flex flex-col items-center justify-center px-6 text-center max-w-4xl mx-auto py-20">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 text-xs font-bold tracking-widest uppercase mb-8 animate-in fade-in slide-in-from-bottom-4 duration-1000">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                </span>
                AI-Powered Recruitment Intelligence
            </div>

            <h1 class="text-5xl md:text-7xl font-black mb-8 leading-[1.1] tracking-tight animate-in fade-in slide-in-from-bottom-8 duration-1000 delay-100">
                Land your dream job with <br />
                <span class="bg-gradient-to-r from-blue-400 via-purple-400 to-pink-500 bg-clip-text text-transparent">Precision Analysis.</span>
            </h1>

            <p class="text-lg md:text-xl text-slate-400 mb-12 max-w-2xl leading-relaxed animate-in fade-in slide-in-from-bottom-12 duration-1000 delay-200">
                Our advanced AI deconstructs your CV against specific job descriptions to find skill gaps, optimize for ATS, and provide strategic career advice in seconds.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto animate-in fade-in slide-in-from-bottom-16 duration-1000 delay-300">
                <Link :href="cvAnalyzerIndex.url()" class="px-10 py-5 bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl font-black text-lg shadow-xl shadow-blue-500/20 hover:shadow-blue-500/40 hover:scale-[1.02] active:scale-[0.98] transition-all">
                    Analyze Your CV Now
                </Link>
                <a href="#features" class="px-10 py-5 bg-slate-900 border border-slate-800 rounded-2xl font-bold text-lg hover:bg-slate-800 transition-all">
                    Learn More
                </a>
            </div>

            <!-- Dashboard Mockup/Teaser -->
            <div class="mt-24 w-full relative animate-in fade-in slide-in-from-bottom-20 duration-1000 delay-500">
                <div class="absolute inset-0 bg-blue-500/20 blur-[100px] -z-10 rounded-full scale-75 opacity-50"></div>
                <div class="bg-slate-900/40 border border-white/5 rounded-3xl p-2 backdrop-blur-3xl shadow-2xl">
                    <div class="bg-slate-950/80 rounded-[1.25rem] border border-white/5 overflow-hidden aspect-video flex items-center justify-center group overflow-hidden">
                         <div class="text-center p-8 transition-transform duration-700 group-hover:scale-105">
                            <div class="w-20 h-20 bg-slate-900 rounded-3xl flex items-center justify-center text-slate-700 mx-auto mb-6 border border-slate-800">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold mb-2">Ready for Upload</h3>
                            <p class="text-slate-500 max-w-sm">Experience the future of career optimization with our intelligent resume parser.</p>
                         </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Features Section -->
        <section id="features" class="relative z-10 py-32 px-6 max-w-7xl mx-auto w-full grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="p-8 bg-slate-900/40 border border-white/5 rounded-3xl backdrop-blur-sm hover:border-blue-500/30 transition-all">
                <div class="w-12 h-12 bg-blue-500/10 rounded-xl flex items-center justify-center text-blue-400 mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <h4 class="text-xl font-bold mb-3 text-white">ATS Optimization</h4>
                <p class="text-slate-400 leading-relaxed">Ensure your resume passes through Applicant Tracking Systems by identifying and including high-impact keywords.</p>
            </div>

            <div class="p-8 bg-slate-900/40 border border-white/5 rounded-3xl backdrop-blur-sm hover:border-purple-500/30 transition-all">
                <div class="w-12 h-12 bg-purple-500/10 rounded-xl flex items-center justify-center text-purple-400 mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <h4 class="text-xl font-bold mb-3 text-white">Skill Gap Analysis</h4>
                <p class="text-slate-400 leading-relaxed">Understand exactly what skills you're missing for a specific role and get actionable advice on how to bridge the gap.</p>
            </div>

            <div class="p-8 bg-slate-900/40 border border-white/5 rounded-3xl backdrop-blur-sm hover:border-pink-500/30 transition-all">
                <div class="w-12 h-12 bg-pink-500/10 rounded-xl flex items-center justify-center text-pink-400 mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h4 class="text-xl font-bold mb-3 text-white">Global Standards</h4>
                <p class="text-slate-400 leading-relaxed">Tailor your CV for different markets including the USA, UK, UAE, and more with region-specific formatting intelligence.</p>
            </div>
        </section>

        <!-- Footer -->
        <footer class="relative z-10 border-t border-white/5 py-12 px-6">
            <div class="max-w-7xl mx-auto w-full flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex items-center gap-2 grayscale h-6 opacity-50">
                    <div class="w-6 h-6 bg-slate-500 rounded flex items-center justify-center">
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-950" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <span class="text-sm font-bold tracking-tighter">CV ANALYZER</span>
                </div>
                <p class="text-slate-600 text-sm italic">&copy; 2026 AI Recruitment Suite. Built with passion for career growth.</p>
            </div>
        </footer>
    </div>
</template>

<style>
@keyframes fade-in {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slide-in-from-bottom {
    from { transform: translateY(20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

.animate-in {
    animation-fill-mode: both;
}

.fade-in {
    animation-name: fade-in;
}

.slide-in-from-bottom-4 {
    animation-name: slide-in-from-bottom;
    transform: translateY(10px);
}

.slide-in-from-bottom-8 {
    animation-name: slide-in-from-bottom;
    transform: translateY(20px);
}

.slide-in-from-bottom-12 {
    animation-name: slide-in-from-bottom;
    transform: translateY(30px);
}

.slide-in-from-bottom-16 {
    animation-name: slide-in-from-bottom;
    transform: translateY(40px);
}

.slide-in-from-bottom-20 {
    animation-name: slide-in-from-bottom;
    transform: translateY(50px);
}

/* Base styles for the new theme */
html {
    scroll-behavior: smooth;
}

body {
    background-color: #020617; /* slate-950 */
}
</style>
