<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request, FirebaseService $firebase): View
    {
        $filters = [
            'search' => trim((string) $request->query('search', '')),
            'status' => trim((string) $request->query('status', '')),
        ];

        try {
            $allResults = collect($firebase->quizResults())
                ->map(fn (array $result): array => $this->normalizeResult($result))
                ->sortByDesc('timestamp_sort')
                ->values();

            $firebaseError = null;
        } catch (\Throwable $exception) {
            Log::error('ChefVirtuo Firestore quiz result retrieval failed', [
                'message' => $exception->getMessage(),
            ]);

            $allResults = collect();
            $firebaseError = config('app.debug')
                ? $exception->getMessage()
                : 'Quiz results are temporarily unavailable.';
        }

        $results = $allResults
            ->filter(fn (array $result): bool => $this->passesFilters($result, $filters))
            ->values();

        $percentages = $allResults->pluck('percentage');

        $stats = [
            'total_students' => $allResults->pluck('studentID')->unique()->count(),
            'average_score' => $percentages->isNotEmpty() ? round((float) $percentages->avg(), 1) : 0,
            'highest_score' => $percentages->isNotEmpty() ? round((float) $percentages->max(), 1) : 0,
            'lowest_score' => $percentages->isNotEmpty() ? round((float) $percentages->min(), 1) : 0,
        ];

        $passCount = $allResults->where('status', 'PASS')->count();
        $failCount = $allResults->where('status', 'FAIL')->count();

        return view('dashboard', [
            'lecturer' => session('lecturer'),
            'firebaseConfig' => config('firebase.web'),
            'results' => $results,
            'allResults' => $allResults,
            'stats' => $stats,
            'filters' => $filters,
            'firebaseError' => $firebaseError,
            'chartData' => [
                'studentLabels' => $allResults->pluck('studentID')->values(),
                'scores' => $allResults->pluck('percentage')->values(),
                'passFail' => [$passCount, $failCount],
            ],
        ]);
    }

    private function normalizeResult(array $result): array
    {
        $score = (float) ($result['score'] ?? 0);
        $totalQuestions = max((float) ($result['totalQuestions'] ?? 1), 1);
        $percentage = isset($result['percentage'])
            ? (float) $result['percentage']
            : round(($score / $totalQuestions) * 100, 1);

        $timestamp = $result['timestamp'] ?? now();
        $timestampCarbon = $this->parseTimestamp($timestamp);

        return [
            'id' => (string) ($result['id'] ?? ''),
            'studentID' => (string) ($result['studentID'] ?? 'Unknown'),
            'module' => (string) ($result['module'] ?? 'Unassigned'),
            'score' => $score,
            'totalQuestions' => $totalQuestions,
            'percentage' => round($percentage, 1),
            'status' => $this->normalizeStatus($result['status'] ?? null, $percentage),
            'answers' => $this->normalizeAnswers($result['answers'] ?? []),
            'timestamp' => $timestampCarbon->format('d M Y, h:i A'),
            'timestamp_iso' => $timestampCarbon->toIso8601String(),
            'timestamp_sort' => $timestampCarbon->timestamp,
        ];
    }

    private function normalizeAnswers(mixed $answers): array
    {
        if (! is_array($answers)) {
            return [];
        }

        return collect($answers)
            ->filter(fn (mixed $answer): bool => is_array($answer))
            ->map(fn (array $answer): array => [
                'question' => (string) ($answer['question'] ?? 'Untitled question'),
                'studentAnswer' => (string) ($answer['studentAnswer'] ?? 'No answer recorded'),
                'correctAnswer' => (string) ($answer['correctAnswer'] ?? 'No correct answer recorded'),
                'isCorrect' => (bool) ($answer['isCorrect'] ?? false),
            ])
            ->values()
            ->all();
    }

    private function normalizeStatus(mixed $status, float $percentage): string
    {
        $status = strtoupper(trim((string) $status));

        if (in_array($status, ['PASS', 'FAIL'], true)) {
            return $status;
        }

        return $percentage >= 50 ? 'PASS' : 'FAIL';
    }

    private function parseTimestamp(mixed $timestamp): Carbon
    {
        if ($timestamp instanceof \DateTimeInterface) {
            return Carbon::instance($timestamp);
        }

        if (is_numeric($timestamp)) {
            return now()->setTimestamp((int) $timestamp);
        }

        return rescue(fn () => Carbon::parse($timestamp), now(), false);
    }

    private function passesFilters(array $result, array $filters): bool
    {
        if ($filters['search'] !== '' && ! str_contains(strtolower($result['studentID']), strtolower($filters['search']))) {
            return false;
        }

        if ($filters['status'] !== '' && $result['status'] !== $filters['status']) {
            return false;
        }

        return true;
    }
}
