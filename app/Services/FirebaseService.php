<?php

namespace App\Services;

use Google\Auth\Credentials\ServiceAccountCredentials;
use GuzzleHttp\Client;
use Kreait\Firebase\Factory;
use RuntimeException;

class FirebaseService
{
    private const QUIZ_RESULTS_COLLECTION = 'quizResults';

    public function verifyFirebaseIdToken(string $idToken): array
    {
        $auth = $this->factory()->createAuth();
        $verifiedToken = $auth->verifyIdToken($idToken);
        $claims = $verifiedToken->claims();

        return [
            'name' => $claims->get('name'),
            'email' => $claims->get('email'),
            'photo' => $claims->get('picture'),
        ];
    }

    public function quizResults(): array
    {
        $projectId = config('firebase.project_id');

        if (! $projectId) {
            throw new RuntimeException('FIREBASE_PROJECT_ID is not configured.');
        }

        $response = (new Client([
            'base_uri' => 'https://firestore.googleapis.com/v1/',
            'timeout' => 15,
        ]))->get(sprintf(
            'projects/%s/databases/(default)/documents/%s',
            rawurlencode($projectId),
            self::QUIZ_RESULTS_COLLECTION
        ), [
            'headers' => [
                'Authorization' => 'Bearer '.$this->accessToken(),
                'Accept' => 'application/json',
            ],
        ]);

        $payload = json_decode((string) $response->getBody(), true, flags: JSON_THROW_ON_ERROR);

        return collect($payload['documents'] ?? [])
            ->map(function ($document): array {
                $data = $this->decodeFields($document['fields'] ?? []);

                return [
                    'id' => basename((string) ($document['name'] ?? '')),
                    'studentID' => (string) ($data['studentID'] ?? ''),
                    'module' => (string) ($data['module'] ?? ''),
                    'score' => (float) ($data['score'] ?? 0),
                    'totalQuestions' => (float) ($data['totalQuestions'] ?? 0),
                    'percentage' => isset($data['percentage']) ? (float) $data['percentage'] : null,
                    'status' => (string) ($data['status'] ?? ''),
                    'answers' => $this->normalizeAnswers($data['answers'] ?? []),
                    'timestamp' => $this->formatTimestamp($data['timestamp'] ?? null),
                ];
            })
            ->values()
            ->all();
    }

    private function factory(): Factory
    {
        $projectId = config('firebase.project_id');
        $credentials = $this->credentialsPath();

        if (! $projectId) {
            throw new RuntimeException('FIREBASE_PROJECT_ID is not configured.');
        }

        return (new Factory())
            ->withProjectId($projectId)
            ->withServiceAccount($credentials);
    }

    private function accessToken(): string
    {
        $credentials = new ServiceAccountCredentials(
            ['https://www.googleapis.com/auth/datastore'],
            $this->credentialsPath()
        );

        $token = $credentials->fetchAuthToken();

        if (empty($token['access_token'])) {
            throw new RuntimeException('Unable to fetch a Firebase service account access token.');
        }

        return $token['access_token'];
    }

    private function decodeFields(array $fields): array
    {
        return collect($fields)
            ->map(fn (array $value): mixed => $this->decodeValue($value))
            ->all();
    }

    private function decodeValue(array $value): mixed
    {
        if (array_key_exists('stringValue', $value)) {
            return $value['stringValue'];
        }

        if (array_key_exists('integerValue', $value)) {
            return (int) $value['integerValue'];
        }

        if (array_key_exists('doubleValue', $value)) {
            return (float) $value['doubleValue'];
        }

        if (array_key_exists('booleanValue', $value)) {
            return (bool) $value['booleanValue'];
        }

        if (array_key_exists('timestampValue', $value)) {
            return $value['timestampValue'];
        }

        if (array_key_exists('mapValue', $value)) {
            return $this->decodeFields($value['mapValue']['fields'] ?? []);
        }

        if (array_key_exists('arrayValue', $value)) {
            return collect($value['arrayValue']['values'] ?? [])
                ->map(fn (array $item): mixed => $this->decodeValue($item))
                ->all();
        }

        return null;
    }

    private function credentialsPath(): string
    {
        $credentials = config('firebase.credentials');

        if (! $credentials) {
            throw new RuntimeException('FIREBASE_CREDENTIALS is not configured.');
        }

        $path = base_path($credentials);

        if (! is_file($path)) {
            throw new RuntimeException("Firebase service account file was not found at {$path}.");
        }

        return $path;
    }

    private function normalizeAnswers(mixed $answers): array
    {
        if (! is_array($answers)) {
            return [];
        }

        return collect($answers)
            ->filter(fn (mixed $answer): bool => is_array($answer))
            ->map(fn (array $answer): array => [
                'question' => (string) ($answer['question'] ?? ''),
                'studentAnswer' => (string) ($answer['studentAnswer'] ?? ''),
                'correctAnswer' => (string) ($answer['correctAnswer'] ?? ''),
                'isCorrect' => (bool) ($answer['isCorrect'] ?? false),
            ])
            ->values()
            ->all();
    }

    private function formatTimestamp(mixed $timestamp): ?string
    {
        if ($timestamp instanceof \DateTimeInterface) {
            return $timestamp->format(DATE_ATOM);
        }

        if (is_numeric($timestamp)) {
            return now()->setTimestamp((int) $timestamp)->format(DATE_ATOM);
        }

        return is_string($timestamp) && $timestamp !== '' ? $timestamp : null;
    }
}
