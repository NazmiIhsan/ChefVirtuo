@extends('layouts.app')

@section('title', 'ChefVirtuo Dashboard')

@section('content')
<div class="min-h-screen px-5 py-6 lg:px-8">
    <nav class="mx-auto mb-8 flex max-w-7xl items-center justify-between rounded-3xl border border-white/70 bg-white/60 px-5 py-4 shadow-glass backdrop-blur-xl">
        <div class="flex items-center gap-4">
            <div class="grid h-14 w-14 place-items-center rounded-2xl bg-white shadow-lg shadow-black/5">
                <img src="{{ asset('images/chefvirtuo-logo.png') }}" alt="ChefVirtuo logo" class="max-h-11 max-w-11" onerror="this.outerHTML='<span class=&quot;text-lg font-black&quot;>CV</span>'">
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.28em] text-moss">ChefVirtuo</p>
                <h1 class="text-xl font-black tracking-tight sm:text-2xl">Lecturer Performance Dashboard</h1>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <div class="hidden text-right sm:block">
                <p class="text-sm font-bold">{{ $lecturer['name'] ?? 'Lecturer' }}</p>
                <p class="text-xs text-black/55">{{ $lecturer['email'] ?? '' }}</p>
            </div>
            @if (! empty($lecturer['photo']))
                <img src="{{ $lecturer['photo'] }}" alt="Lecturer profile" class="h-11 w-11 rounded-full ring-2 ring-gold/60">
            @else
                <div class="grid h-11 w-11 place-items-center rounded-full bg-ink text-sm font-black text-white">L</div>
            @endif
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="rounded-full bg-ink px-4 py-2 text-sm font-bold text-white transition hover:-translate-y-0.5 hover:bg-black">Logout</button>
            </form>
        </div>
    </nav>

    <main class="mx-auto max-w-7xl space-y-8">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ([
                ['label' => 'Total Students', 'value' => $stats['total_students'], 'suffix' => '', 'accent' => 'bg-gold'],
                ['label' => 'Average Score', 'value' => $stats['average_score'], 'suffix' => '%', 'accent' => 'bg-moss'],
                ['label' => 'Highest Score', 'value' => $stats['highest_score'], 'suffix' => '%', 'accent' => 'bg-ink'],
                ['label' => 'Lowest Score', 'value' => $stats['lowest_score'], 'suffix' => '%', 'accent' => 'bg-sage'],
            ] as $card)
                <article class="group rounded-3xl border border-white/70 bg-white/65 p-6 shadow-xl shadow-black/5 backdrop-blur transition duration-200 hover:-translate-y-1 hover:shadow-2xl">
                    <div class="mb-5 h-2 w-16 rounded-full {{ $card['accent'] }}"></div>
                    <p class="text-sm font-bold uppercase tracking-[0.18em] text-black/45">{{ $card['label'] }}</p>
                    <p class="mt-3 text-4xl font-black tracking-tight">{{ $card['value'] }}{{ $card['suffix'] }}</p>
                </article>
            @endforeach
        </section>

        <section class="grid gap-5 xl:grid-cols-[1.35fr_0.85fr]">
            <div class="rounded-3xl border border-white/70 bg-white/70 p-5 shadow-glass backdrop-blur-xl">
                <div class="mb-5 flex flex-col justify-between gap-4 lg:flex-row lg:items-center">
                    <div>
                        <p class="text-sm font-bold uppercase tracking-[0.22em] text-moss">Quiz Results</p>
                        <h2 class="mt-1 text-2xl font-black tracking-tight">Student Performance Records</h2>
                    </div>
                    <form method="GET" class="grid gap-3 sm:grid-cols-4">
                        <input name="search" value="{{ $filters['search'] }}" placeholder="Search Student ID" class="rounded-2xl border border-black/10 bg-white/80 px-4 py-3 text-sm outline-none transition focus:border-gold focus:ring-4 focus:ring-gold/20">
                        <select name="module" class="rounded-2xl border border-black/10 bg-white/80 px-4 py-3 text-sm outline-none transition focus:border-gold focus:ring-4 focus:ring-gold/20">
                            <option value="">All modules</option>
                            @foreach ($modules as $module)
                                <option value="{{ $module }}" @selected($filters['module'] === $module)>{{ $module }}</option>
                            @endforeach
                        </select>
                        <select name="status" class="rounded-2xl border border-black/10 bg-white/80 px-4 py-3 text-sm outline-none transition focus:border-gold focus:ring-4 focus:ring-gold/20">
                            <option value="">All status</option>
                            <option value="PASS" @selected($filters['status'] === 'PASS')>PASS</option>
                            <option value="FAIL" @selected($filters['status'] === 'FAIL')>FAIL</option>
                        </select>
                        <button class="rounded-2xl bg-gold px-5 py-3 text-sm font-black text-ink shadow-lg shadow-gold/20 transition hover:-translate-y-0.5 hover:bg-yellow-400">Apply</button>
                    </form>
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
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-black/5 bg-white/60">
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
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-10 text-center font-semibold text-black/50">No quiz results match these filters.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <aside class="space-y-5">
                <div class="rounded-3xl border border-white/70 bg-white/70 p-5 shadow-glass backdrop-blur-xl">
                    <h2 class="text-lg font-black">Pass / Fail Distribution</h2>
                    <div class="mt-4 h-72">
                        <canvas id="passFailChart"></canvas>
                    </div>
                </div>
                <div class="rounded-3xl border border-white/70 bg-ink p-5 text-white shadow-glass">
                    <p class="text-sm font-bold uppercase tracking-[0.2em] text-gold">Realtime Source</p>
                    <p class="mt-3 text-2xl font-black">Firestore: quizResults</p>
                    <p class="mt-3 text-sm leading-6 text-white/65">The dashboard service reads ChefVirtuo quiz documents and computes lecturer-ready analytics in Laravel.</p>
                </div>
            </aside>
        </section>

        <section class="grid gap-5 lg:grid-cols-2">
            <div class="rounded-3xl border border-white/70 bg-white/70 p-5 shadow-glass backdrop-blur-xl">
                <h2 class="text-lg font-black">Student Score Bar Chart</h2>
                <div class="mt-4 h-80">
                    <canvas id="scoreChart"></canvas>
                </div>
            </div>
            <div class="rounded-3xl border border-white/70 bg-white/70 p-5 shadow-glass backdrop-blur-xl">
                <h2 class="text-lg font-black">Module Performance Comparison</h2>
                <div class="mt-4 h-80">
                    <canvas id="moduleChart"></canvas>
                </div>
            </div>
        </section>
    </main>
</div>

<script type="module">
    import { initializeApp } from 'https://www.gstatic.com/firebasejs/10.12.5/firebase-app.js';
    import { getFirestore, collection, onSnapshot } from 'https://www.gstatic.com/firebasejs/10.12.5/firebase-firestore.js';

    const firebaseConfig = @json($firebaseConfig);

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
        let firstSnapshot = true;

        onSnapshot(collection(db, 'quizResults'), () => {
            if (firstSnapshot) {
                firstSnapshot = false;
                return;
            }

            window.setTimeout(() => window.location.reload(), 600);
        });
    }
</script>

<script>
    const chartData = @json($chartData);
    const chartColors = {
        ink: '#15130f',
        gold: '#f4b63f',
        moss: '#79b67a',
        sage: '#dfead4',
        red: '#ef4444'
    };

    new Chart(document.getElementById('scoreChart'), {
        type: 'bar',
        data: {
            labels: chartData.studentLabels,
            datasets: [{ label: 'Score %', data: chartData.scores, backgroundColor: chartColors.gold, borderRadius: 12 }]
        },
        options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, max: 100 } } }
    });

    new Chart(document.getElementById('passFailChart'), {
        type: 'doughnut',
        data: {
            labels: ['PASS', 'FAIL'],
            datasets: [{ data: chartData.passFail, backgroundColor: [chartColors.moss, chartColors.red], borderWidth: 0 }]
        },
        options: { responsive: true, maintainAspectRatio: false, cutout: '68%' }
    });

    new Chart(document.getElementById('moduleChart'), {
        type: 'bar',
        data: {
            labels: chartData.moduleLabels,
            datasets: [{ label: 'Average %', data: chartData.moduleScores, backgroundColor: chartColors.moss, borderRadius: 12 }]
        },
        options: { responsive: true, maintainAspectRatio: false, indexAxis: 'y', scales: { x: { beginAtZero: true, max: 100 } } }
    });
</script>
@endsection
