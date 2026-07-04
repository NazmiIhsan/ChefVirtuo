@extends('layouts.app')

@section('title', 'ChefVirtuo Dashboard')

@section('content')
<div class="min-h-screen bg-[linear-gradient(135deg,#fff8ea_0%,#fffdf6_48%,#edf6e8_100%)] px-4 py-5 sm:px-6 lg:px-8">
    <nav class="mx-auto mb-6 flex max-w-7xl flex-col gap-4 rounded-3xl border border-white/80 bg-white/75 px-5 py-4 shadow-glass backdrop-blur-xl sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-4">
            <div class="grid h-14 w-14 shrink-0 place-items-center rounded-2xl bg-white shadow-lg shadow-black/5">
                <img src="{{ asset('images/chefvirtuo-logo.png') }}" alt="ChefVirtuo logo" class="h-14 w-14 rounded-xl object-contain">
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.28em] text-moss">ChefVirtuo</p>
                <h1 class="text-xl font-black tracking-tight sm:text-2xl">Dashboard</h1>
            </div>
        </div>

        <div class="flex items-center justify-between gap-3 sm:justify-end">
            <div class="text-right">
                <p class="text-sm font-bold">{{ $lecturer['name'] ?? 'Lecturer' }}</p>
                <p class="max-w-48 truncate text-xs text-black/55">{{ $lecturer['email'] ?? '' }}</p>
            </div>
            @if (! empty($lecturer['photo']))
                <img src="{{ $lecturer['photo'] }}" alt="Lecturer profile" class="h-11 w-11 rounded-full ring-2 ring-gold/60">
            @else
                <div class="grid h-11 w-11 place-items-center rounded-full bg-ink text-sm font-black text-white">L</div>
            @endif
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="rounded-full bg-ink px-4 py-2 text-sm font-bold text-white shadow-lg shadow-black/10 transition hover:-translate-y-0.5 hover:bg-black">Logout</button>
            </form>
        </div>
    </nav>

    <main class="mx-auto max-w-7xl space-y-6">
        <section class="rounded-3xl border border-white/80 bg-white/70 p-6 shadow-glass backdrop-blur-xl">
            <div class="grid gap-5 lg:grid-cols-[1fr_0.55fr] lg:items-end">
                <div>
                    {{-- <p class="text-sm font-black uppercase tracking-[0.22em] text-gold">Lecturer Monitoring System</p> --}}
                    <h2 class="mt-2 max-w-3xl text-3xl font-black tracking-tight sm:text-4xl">Equipments and Ingredients Quiz Dashbaord</h2>
                    {{-- <p class="mt-3 max-w-2xl text-sm leading-6 text-black/60">Firestore collection: <span class="font-bold text-ink">quizResults</span></p> --}}
                </div>
                {{-- <div class="rounded-2xl bg-ink p-5 text-white shadow-xl shadow-black/10">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.2em] text-sage">Live Source</p>
                            <p id="syncStatus" class="mt-2 text-lg font-black">Connected via Laravel</p>
                        </div>
                        <span class="h-3 w-3 rounded-full bg-moss shadow-[0_0_0_6px_rgba(121,182,122,0.18)]"></span>
                    </div>
                </div> --}}
            </div>
        </section>

        @if ($firebaseError)
            <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-semibold text-red-700">
                {{ $firebaseError }}
            </div>
        @endif

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <article class="rounded-3xl border border-white/80 bg-white/80 p-6 shadow-xl shadow-black/5">
                <div class="mb-5 h-2 w-16 rounded-full bg-gold"></div>
                <p class="text-sm font-bold uppercase tracking-[0.18em] text-black/45">Total Students</p>
                <p id="totalStudents" class="mt-3 text-4xl font-black tracking-tight">{{ $stats['total_students'] }}</p>
            </article>
            <article class="rounded-3xl border border-white/80 bg-white/80 p-6 shadow-xl shadow-black/5">
                <div class="mb-5 h-2 w-16 rounded-full bg-moss"></div>
                <p class="text-sm font-bold uppercase tracking-[0.18em] text-black/45">Average Score</p>
                <p class="mt-3 text-4xl font-black tracking-tight"><span id="averageScore">{{ $stats['average_score'] }}</span>%</p>
            </article>
            <article class="rounded-3xl border border-white/80 bg-white/80 p-6 shadow-xl shadow-black/5">
                <div class="mb-5 h-2 w-16 rounded-full bg-ink"></div>
                <p class="text-sm font-bold uppercase tracking-[0.18em] text-black/45">Highest Score</p>
                <p class="mt-3 text-4xl font-black tracking-tight"><span id="highestScore">{{ $stats['highest_score'] }}</span>%</p>
            </article>
            <article class="rounded-3xl border border-white/80 bg-white/80 p-6 shadow-xl shadow-black/5">
                <div class="mb-5 h-2 w-16 rounded-full bg-sage"></div>
                <p class="text-sm font-bold uppercase tracking-[0.18em] text-black/45">Lowest Score</p>
                <p class="mt-3 text-4xl font-black tracking-tight"><span id="lowestScore">{{ $stats['lowest_score'] }}</span>%</p>
            </article>
        </section>

        <section class="grid gap-5 xl:grid-cols-[1.35fr_0.85fr]">
            <div class="rounded-3xl border border-white/80 bg-white/80 p-5 shadow-glass backdrop-blur-xl">
                <div class="mb-5">
                    <div>
                        <p class="text-sm font-bold uppercase tracking-[0.22em] text-moss">Quiz Results</p>
                        <h2 class="mt-1 text-2xl font-black tracking-tight">Student Performance Records</h2>
                    </div>
                </div>

                <div class="overflow-x-auto rounded-2xl border border-black/5">
                    <table class="min-w-full divide-y divide-black/5 text-left text-sm">
                        <thead class="bg-ink text-xs uppercase tracking-[0.16em] text-white">
                            <tr>
                                <th class="px-5 py-4">Student ID</th>
                                <th class="px-5 py-4">Module</th>
                                <th class="px-5 py-4">Score</th>
                                <th class="px-5 py-4">Percentage</th>
                                <th class="px-5 py-4">Status</th>
                                <th class="px-5 py-4">Timestamp</th>
                                <th class="px-5 py-4">Details</th>
                            </tr>
                        </thead>
                        <tbody id="resultsTableBody" class="divide-y divide-black/5 bg-white/70">
                            @forelse ($results as $result)
                                <tr class="transition hover:bg-gold/10">
                                    <td class="px-5 py-4 font-black">{{ $result['studentID'] }}</td>
                                    <td class="px-5 py-4">{{ $result['module'] }}</td>
                                    <td class="px-5 py-4">{{ (int) $result['score'] }}/{{ (int) $result['totalQuestions'] }}</td>
                                    <td class="px-5 py-4 font-bold">{{ $result['percentage'] }}%</td>
                                    <td class="px-5 py-4">
                                        <span class="rounded-full px-3 py-1 text-xs font-black {{ $result['status'] === 'PASS' ? 'bg-moss/20 text-green-800' : 'bg-red-100 text-red-700' }}">{{ $result['status'] }}</span>
                                    </td>
                                    <td class="px-5 py-4 text-black/60">{{ $result['timestamp'] }}</td>
                                    <td class="px-5 py-4">
                                        <button type="button" data-modal-target="answer-modal-{{ $result['id'] ?: $loop->index }}" class="open-answer-modal rounded-full bg-ink px-4 py-2 text-xs font-black text-white shadow-lg shadow-black/10 transition hover:-translate-y-0.5 hover:bg-black">
                                            View Details
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-5 py-10 text-center font-semibold text-black/50">No quiz results found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div id="resultsPagination" class="mt-5 hidden flex-col gap-3 sm:flex-row sm:items-center sm:justify-between" aria-label="Quiz results pagination">
                    <p id="paginationSummary" class="text-sm font-semibold text-black/55"></p>
                    <div class="flex items-center gap-2">
                        <button id="previousPage" type="button" class="rounded-full border border-black/10 bg-white px-4 py-2 text-sm font-black text-ink transition hover:border-ink hover:bg-ink hover:text-white disabled:cursor-not-allowed disabled:opacity-40 disabled:hover:border-black/10 disabled:hover:bg-white disabled:hover:text-ink">
                            Previous
                        </button>
                        <div id="paginationPages" class="flex items-center gap-2"></div>
                        <button id="nextPage" type="button" class="rounded-full border border-black/10 bg-white px-4 py-2 text-sm font-black text-ink transition hover:border-ink hover:bg-ink hover:text-white disabled:cursor-not-allowed disabled:opacity-40 disabled:hover:border-black/10 disabled:hover:bg-white disabled:hover:text-ink">
                            Next
                        </button>
                    </div>
                </div>
            </div>

            <aside class="space-y-5">
                <div class="rounded-3xl border border-white/80 bg-white/80 p-5 shadow-glass backdrop-blur-xl">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-bold uppercase tracking-[0.18em] text-moss">Pass Rate</p>
                            <h2 class="mt-1 text-xl font-black">Pass / Fail Distribution</h2>
                        </div>
                        <span class="rounded-full bg-gold/20 px-3 py-1 text-xs font-black text-ink">>= 50%</span>
                    </div>
                    <div class="mt-4 h-72">
                        <canvas id="passFailChart"></canvas>
                    </div>
                </div>
            </aside>
        </section>

    </main>
</div>

@foreach ($results as $result)
    @php
        $correctCount = collect($result['answers'])->where('isCorrect', true)->count();
        $wrongCount = collect($result['answers'])->where('isCorrect', false)->count();
    @endphp

    <div id="answer-modal-{{ $result['id'] ?: $loop->index }}" class="answer-modal pointer-events-none fixed inset-0 z-50 hidden items-center justify-center bg-ink/45 px-4 py-6 opacity-0 backdrop-blur-sm transition duration-200" aria-hidden="true">
        <div class="modal-panel max-h-[90vh] w-full max-w-4xl scale-95 overflow-hidden rounded-3xl border border-white/80 bg-white/85 shadow-glass backdrop-blur-2xl transition duration-200">
            <div class="flex items-start justify-between gap-5 border-b border-black/5 bg-white/55 px-6 py-5">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.24em] text-moss">Answer Analytics</p>
                    <h2 class="mt-1 text-2xl font-black tracking-tight">{{ $result['studentID'] }}</h2>
                    <p class="mt-1 text-sm font-semibold text-black/55">{{ $result['module'] }} • {{ $result['timestamp'] }}</p>
                </div>
                <button type="button" class="close-answer-modal grid h-10 w-10 shrink-0 place-items-center rounded-full bg-ink text-lg font-black text-white transition hover:-translate-y-0.5 hover:bg-black" aria-label="Close answer details">×</button>
            </div>

            <div class="max-h-[72vh] overflow-y-auto p-6">
                <div class="mb-5 grid gap-3 sm:grid-cols-2">
                    <div class="rounded-2xl border border-green-200 bg-green-50/85 p-4">
                        <p class="text-xs font-black uppercase tracking-[0.18em] text-green-700">Total Correct</p>
                        <p class="mt-2 text-3xl font-black text-green-800">{{ $correctCount }}</p>
                    </div>
                    <div class="rounded-2xl border border-red-200 bg-red-50/85 p-4">
                        <p class="text-xs font-black uppercase tracking-[0.18em] text-red-700">Total Wrong</p>
                        <p class="mt-2 text-3xl font-black text-red-700">{{ $wrongCount }}</p>
                    </div>
                </div>

                <div class="space-y-4">
                    @forelse($result['answers'] as $answer)
                        <article class="rounded-2xl border border-black/5 bg-white/75 p-5 shadow-lg shadow-black/5">
                            <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <p class="text-xs font-black uppercase tracking-[0.18em] text-gold">Question {{ $loop->iteration }}</p>
                                    <h3 class="mt-1 text-lg font-black leading-snug">{{ $answer['question'] }}</h3>
                                </div>
                                <span class="w-fit rounded-full px-3 py-1 text-xs font-black {{ $answer['isCorrect'] ? 'bg-moss/20 text-green-800' : 'bg-red-100 text-red-700' }}">
                                    {{ $answer['isCorrect'] ? 'CORRECT' : 'WRONG' }}
                                </span>
                            </div>
                            <div class="grid gap-3 md:grid-cols-2">
                                <div class="rounded-2xl bg-cream/80 p-4">
                                    <p class="text-xs font-black uppercase tracking-[0.16em] text-black/45">Student Answer</p>
                                    <p class="mt-2 font-bold">{{ $answer['studentAnswer'] }}</p>
                                </div>
                                <div class="rounded-2xl bg-sage/70 p-4">
                                    <p class="text-xs font-black uppercase tracking-[0.16em] text-black/45">Correct Answer</p>
                                    <p class="mt-2 font-bold">{{ $answer['correctAnswer'] }}</p>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="rounded-2xl border border-dashed border-black/15 bg-white/55 px-5 py-8 text-center font-semibold text-black/55">
                            No detailed answers were recorded for this quiz result.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endforeach

<div id="liveAnswerModal" class="answer-modal pointer-events-none fixed inset-0 z-50 hidden items-center justify-center bg-ink/45 px-4 py-6 opacity-0 backdrop-blur-sm transition duration-200" aria-hidden="true">
    <div class="modal-panel max-h-[90vh] w-full max-w-4xl scale-95 overflow-hidden rounded-3xl border border-white/80 bg-white/85 shadow-glass backdrop-blur-2xl transition duration-200">
        <div class="flex items-start justify-between gap-5 border-b border-black/5 bg-white/55 px-6 py-5">
            <div>
                <p class="text-xs font-black uppercase tracking-[0.24em] text-moss">Answer Analytics</p>
                <h2 id="liveAnswerModalStudent" class="mt-1 text-2xl font-black tracking-tight"></h2>
                <p id="liveAnswerModalMeta" class="mt-1 text-sm font-semibold text-black/55"></p>
            </div>
            <button type="button" class="close-answer-modal grid h-10 w-10 shrink-0 place-items-center rounded-full bg-ink text-lg font-black text-white transition hover:-translate-y-0.5 hover:bg-black" aria-label="Close answer details">×</button>
        </div>
        <div id="liveAnswerModalBody" class="max-h-[72vh] overflow-y-auto p-6"></div>
    </div>
</div>

<script>
    window.chefVirtuoDashboard = {
        firebaseConfig: @json($firebaseConfig),
        chartData: @json($chartData),
        filters: @json($filters),
        initialResults: @json($allResults->values()),
    };
</script>

<script>
    const dashboardState = window.chefVirtuoDashboard;
    const chartColors = {
        ink: '#15130f',
        gold: '#f4b63f',
        moss: '#79b67a',
        sage: '#dfead4',
        red: '#ef4444'
    };

    const passFailChart = new Chart(document.getElementById('passFailChart'), {
        type: 'doughnut',
        data: {
            labels: ['PASS', 'FAIL'],
            datasets: [{ data: dashboardState.chartData.passFail, backgroundColor: [chartColors.moss, chartColors.red], borderWidth: 0 }]
        },
        options: { responsive: true, maintainAspectRatio: false, cutout: '68%' }
    });

    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, (char) => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;',
        }[char]));
    }

    function formatNumber(value) {
        return Number.isInteger(value) ? String(value) : value.toFixed(1);
    }

    function normalizeTimestamp(value) {
        if (value && typeof value.toDate === 'function') {
            return value.toDate();
        }

        if (value && typeof value.seconds === 'number') {
            return new Date(value.seconds * 1000);
        }

        const parsed = new Date(value ?? Date.now());
        return Number.isNaN(parsed.getTime()) ? new Date() : parsed;
    }

    function formatTimestamp(value) {
        return new Intl.DateTimeFormat('en-MY', {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        }).format(normalizeTimestamp(value));
    }

    function normalizeResult(data, id = '') {
        const score = Number(data.score ?? 0);
        const totalQuestions = Math.max(Number(data.totalQuestions ?? 0), 1);
        const percentage = Number(data.percentage ?? ((score / totalQuestions) * 100));
        const timestampDate = normalizeTimestamp(data.timestamp);
        const storedStatus = String(data.status || '').toUpperCase();
        const answers = Array.isArray(data.answers) ? data.answers.map((answer) => ({
            question: String(answer?.question || 'Untitled question'),
            studentAnswer: String(answer?.studentAnswer || 'No answer recorded'),
            correctAnswer: String(answer?.correctAnswer || 'No correct answer recorded'),
            isCorrect: Boolean(answer?.isCorrect),
        })) : [];

        return {
            id,
            studentID: String(data.studentID || 'Unknown'),
            module: String(data.module || 'Unassigned'),
            score,
            totalQuestions,
            percentage: Number(percentage.toFixed(1)),
            status: ['PASS', 'FAIL'].includes(storedStatus) ? storedStatus : (percentage >= 50 ? 'PASS' : 'FAIL'),
            answers,
            timestamp: formatTimestamp(data.timestamp),
            timestamp_sort: Math.floor(timestampDate.getTime() / 1000),
        };
    }

    function passesFilters(result) {
        const filters = dashboardState.filters;

        if (filters.search && ! result.studentID.toLowerCase().includes(filters.search.toLowerCase())) {
            return false;
        }

        if (filters.status && result.status !== filters.status) {
            return false;
        }

        return true;
    }

    function calculateStats(results) {
        const students = new Set(results.map((result) => result.studentID));
        const percentages = results.map((result) => result.percentage);
        const sum = percentages.reduce((total, percentage) => total + percentage, 0);

        return {
            totalStudents: students.size,
            averageScore: percentages.length ? sum / percentages.length : 0,
            highestScore: percentages.length ? Math.max(...percentages) : 0,
            lowestScore: percentages.length ? Math.min(...percentages) : 0,
        };
    }

    function renderStats(results) {
        const stats = calculateStats(results);
        document.getElementById('totalStudents').textContent = stats.totalStudents;
        document.getElementById('averageScore').textContent = formatNumber(Number(stats.averageScore.toFixed(1)));
        document.getElementById('highestScore').textContent = formatNumber(Number(stats.highestScore.toFixed(1)));
        document.getElementById('lowestScore').textContent = formatNumber(Number(stats.lowestScore.toFixed(1)));
    }

    function renderTable(results) {
        const tableBody = document.getElementById('resultsTableBody');
        const filteredResults = results.filter(passesFilters);

        dashboardState.filteredResults = filteredResults;

        if (! filteredResults.length) {
            tableBody.innerHTML = '<tr><td colspan="7" class="px-5 py-10 text-center font-semibold text-black/50">No quiz results found.</td></tr>';
            renderPagination(0);
            return;
        }

        const totalPages = Math.ceil(filteredResults.length / dashboardState.perPage);
        dashboardState.currentPage = Math.min(Math.max(dashboardState.currentPage, 1), totalPages);
        const startIndex = (dashboardState.currentPage - 1) * dashboardState.perPage;
        const pageResults = filteredResults.slice(startIndex, startIndex + dashboardState.perPage);

        tableBody.innerHTML = pageResults.map((result) => {
            const badgeClass = result.status === 'PASS' ? 'bg-moss/20 text-green-800' : 'bg-red-100 text-red-700';

            return `
                <tr class="transition hover:bg-gold/10">
                    <td class="px-5 py-4 font-black">${escapeHtml(result.studentID)}</td>
                    <td class="px-5 py-4">${escapeHtml(result.module)}</td>
                    <td class="px-5 py-4">${escapeHtml(formatNumber(result.score))}/${escapeHtml(formatNumber(result.totalQuestions))}</td>
                    <td class="px-5 py-4 font-bold">${escapeHtml(formatNumber(result.percentage))}%</td>
                    <td class="px-5 py-4"><span class="rounded-full px-3 py-1 text-xs font-black ${badgeClass}">${result.status}</span></td>
                    <td class="px-5 py-4 text-black/60">${escapeHtml(result.timestamp)}</td>
                    <td class="px-5 py-4">
                        <button type="button" data-result-id="${escapeHtml(result.id)}" class="open-live-answer-modal rounded-full bg-ink px-4 py-2 text-xs font-black text-white shadow-lg shadow-black/10 transition hover:-translate-y-0.5 hover:bg-black">
                            View Details
                        </button>
                    </td>
                </tr>
            `;
        }).join('');

        renderPagination(filteredResults.length);
    }

    function renderPagination(totalResults) {
        const pagination = document.getElementById('resultsPagination');
        const summary = document.getElementById('paginationSummary');
        const pages = document.getElementById('paginationPages');
        const previousButton = document.getElementById('previousPage');
        const nextButton = document.getElementById('nextPage');
        const totalPages = Math.ceil(totalResults / dashboardState.perPage);

        if (! totalResults) {
            pagination.classList.add('hidden');
            return;
        }

        pagination.classList.remove('hidden');
        pagination.classList.add('flex');

        const firstResult = (dashboardState.currentPage - 1) * dashboardState.perPage + 1;
        const lastResult = Math.min(dashboardState.currentPage * dashboardState.perPage, totalResults);
        summary.textContent = `Showing ${firstResult}–${lastResult} of ${totalResults} results`;

        previousButton.disabled = dashboardState.currentPage === 1;
        nextButton.disabled = dashboardState.currentPage === totalPages;

        pages.innerHTML = Array.from({ length: totalPages }, (_, index) => index + 1).map((page) => `
            <button type="button" data-page="${page}" aria-label="Go to page ${page}" aria-current="${page === dashboardState.currentPage ? 'page' : 'false'}" class="pagination-page grid h-10 min-w-10 place-items-center rounded-full px-3 text-sm font-black transition ${page === dashboardState.currentPage ? 'bg-ink text-white shadow-lg shadow-black/10' : 'bg-white text-ink hover:bg-gold/25'}">
                ${page}
            </button>
        `).join('');
    }

    function updatePassFailChart(results) {
        passFailChart.data.datasets[0].data = [
            results.filter((result) => result.status === 'PASS').length,
            results.filter((result) => result.status === 'FAIL').length,
        ];
        passFailChart.update();
    }

    function renderDashboard(results) {
        const sortedResults = [...results].sort((a, b) => b.timestamp_sort - a.timestamp_sort);
        dashboardState.currentResults = sortedResults;
        renderStats(sortedResults);
        renderTable(sortedResults);
        updatePassFailChart(sortedResults);
    }

    dashboardState.currentResults = dashboardState.initialResults || [];
    dashboardState.filteredResults = [];
    dashboardState.currentPage = 1;
    dashboardState.perPage = 7;

    renderDashboard(dashboardState.currentResults);

    function showModal(modal) {
        if (! modal) {
            return;
        }

        modal.classList.remove('hidden', 'pointer-events-none');
        modal.classList.add('flex');
        requestAnimationFrame(() => {
            modal.classList.remove('opacity-0');
            modal.querySelector('.modal-panel')?.classList.remove('scale-95');
        });
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');
    }

    function hideModal(modal) {
        if (! modal) {
            return;
        }

        modal.classList.add('opacity-0');
        modal.querySelector('.modal-panel')?.classList.add('scale-95');
        modal.setAttribute('aria-hidden', 'true');
        window.setTimeout(() => {
            modal.classList.add('hidden', 'pointer-events-none');
            modal.classList.remove('flex');
            if (! document.querySelector('.answer-modal:not(.hidden)')) {
                document.body.classList.remove('overflow-hidden');
            }
        }, 180);
    }

    function renderLiveAnswerModal(result) {
        const answers = result.answers || [];
        const correctCount = answers.filter((answer) => answer.isCorrect).length;
        const wrongCount = answers.filter((answer) => ! answer.isCorrect).length;

        document.getElementById('liveAnswerModalStudent').textContent = result.studentID;
        document.getElementById('liveAnswerModalMeta').textContent = `${result.module} • ${result.timestamp}`;

        document.getElementById('liveAnswerModalBody').innerHTML = `
            <div class="mb-5 grid gap-3 sm:grid-cols-2">
                <div class="rounded-2xl border border-green-200 bg-green-50/85 p-4">
                    <p class="text-xs font-black uppercase tracking-[0.18em] text-green-700">Total Correct</p>
                    <p class="mt-2 text-3xl font-black text-green-800">${correctCount}</p>
                </div>
                <div class="rounded-2xl border border-red-200 bg-red-50/85 p-4">
                    <p class="text-xs font-black uppercase tracking-[0.18em] text-red-700">Total Wrong</p>
                    <p class="mt-2 text-3xl font-black text-red-700">${wrongCount}</p>
                </div>
            </div>
            <div class="space-y-4">
                ${answers.length ? answers.map((answer, index) => `
                    <article class="rounded-2xl border border-black/5 bg-white/75 p-5 shadow-lg shadow-black/5">
                        <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <p class="text-xs font-black uppercase tracking-[0.18em] text-gold">Question ${index + 1}</p>
                                <h3 class="mt-1 text-lg font-black leading-snug">${escapeHtml(answer.question)}</h3>
                            </div>
                            <span class="w-fit rounded-full px-3 py-1 text-xs font-black ${answer.isCorrect ? 'bg-moss/20 text-green-800' : 'bg-red-100 text-red-700'}">
                                ${answer.isCorrect ? 'CORRECT' : 'WRONG'}
                            </span>
                        </div>
                        <div class="grid gap-3 md:grid-cols-2">
                            <div class="rounded-2xl bg-cream/80 p-4">
                                <p class="text-xs font-black uppercase tracking-[0.16em] text-black/45">Student Answer</p>
                                <p class="mt-2 font-bold">${escapeHtml(answer.studentAnswer)}</p>
                            </div>
                            <div class="rounded-2xl bg-sage/70 p-4">
                                <p class="text-xs font-black uppercase tracking-[0.16em] text-black/45">Correct Answer</p>
                                <p class="mt-2 font-bold">${escapeHtml(answer.correctAnswer)}</p>
                            </div>
                        </div>
                    </article>
                `).join('') : '<div class="rounded-2xl border border-dashed border-black/15 bg-white/55 px-5 py-8 text-center font-semibold text-black/55">No detailed answers were recorded for this quiz result.</div>'}
            </div>
        `;
    }

    document.addEventListener('click', (event) => {
        const modalButton = event.target.closest('.open-answer-modal');
        const liveModalButton = event.target.closest('.open-live-answer-modal');
        const closeButton = event.target.closest('.close-answer-modal');
        const modalBackdrop = event.target.classList.contains('answer-modal') ? event.target : null;
        const pageButton = event.target.closest('.pagination-page');

        if (pageButton) {
            dashboardState.currentPage = Number(pageButton.dataset.page);
            renderTable(dashboardState.currentResults);
        }

        if (event.target.closest('#previousPage') && dashboardState.currentPage > 1) {
            dashboardState.currentPage -= 1;
            renderTable(dashboardState.currentResults);
        }

        if (event.target.closest('#nextPage')) {
            const totalPages = Math.ceil(dashboardState.filteredResults.length / dashboardState.perPage);
            if (dashboardState.currentPage < totalPages) {
                dashboardState.currentPage += 1;
                renderTable(dashboardState.currentResults);
            }
        }

        if (modalButton) {
            showModal(document.getElementById(modalButton.dataset.modalTarget));
        }

        if (liveModalButton) {
            const result = (dashboardState.currentResults || []).find((item) => item.id === liveModalButton.dataset.resultId);
            if (result) {
                renderLiveAnswerModal(result);
                showModal(document.getElementById('liveAnswerModal'));
            }
        }

        if (closeButton) {
            hideModal(closeButton.closest('.answer-modal'));
        }

        if (modalBackdrop) {
            hideModal(modalBackdrop);
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            document.querySelectorAll('.answer-modal:not(.hidden)').forEach(hideModal);
        }
    });
</script>

<script type="module">
    import { initializeApp } from 'https://www.gstatic.com/firebasejs/10.12.5/firebase-app.js';
    import { getFirestore, collection, onSnapshot } from 'https://www.gstatic.com/firebasejs/10.12.5/firebase-firestore.js';

    const firebaseConfig = dashboardState.firebaseConfig;
    const syncStatus = document.getElementById('syncStatus');

    if (firebaseConfig.api_key && firebaseConfig.project_id) {
        const app = initializeApp({
            apiKey: firebaseConfig.api_key,
            authDomain: firebaseConfig.auth_domain,
            projectId: firebaseConfig.project_id,
            storageBucket: firebaseConfig.storage_bucket,
            messagingSenderId: firebaseConfig.messaging_sender_id,
            appId: firebaseConfig.app_id,
        });

        const db = getFirestore(app);

        onSnapshot(collection(db, 'quizResults'), (snapshot) => {
            const results = snapshot.docs.map((doc) => normalizeResult(doc.data(), doc.id));
            dashboardState.currentPage = 1;
            renderDashboard(results);
            if (syncStatus) syncStatus.textContent = `Live: ${results.length} records`;
        }, () => {
            if (syncStatus) syncStatus.textContent = 'Live updates unavailable';
        });
    }
</script>
@endsection
