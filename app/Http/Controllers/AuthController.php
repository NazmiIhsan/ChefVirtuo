<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (session()->has('lecturer')) {
            return redirect()->route('dashboard');
        }

        return view('auth.login', [
            'firebaseConfig' => config('firebase.web'),
        ]);
    }

    public function storeSession(Request $request, FirebaseService $firebase): RedirectResponse
    {
        $validated = $request->validate([
            'idToken' => ['required', 'string'],
        ]);

        try {
            $lecturer = $firebase->verifyFirebaseIdToken($validated['idToken']);
        } catch (\Throwable $exception) {
            Log::warning('ChefVirtuo Firebase login failed', ['message' => $exception->getMessage()]);

            return redirect()
                ->route('login')
                ->withErrors(['auth' => 'Google authentication failed. Please try again.']);
        }

        $allowedEmails = config('firebase.lecturer_emails', []);
        $email = strtolower($lecturer['email'] ?? '');

        if (! in_array($email, $allowedEmails, true)) {
            Log::notice('ChefVirtuo lecturer login rejected', [
                'email' => $email,
                'allowed_emails' => $allowedEmails,
            ]);

            $message = 'This Google account is not registered as a ChefVirtuo lecturer.';

            if (config('app.debug') && $email !== '') {
                $message .= " Add {$email} to LECTURER_EMAILS in .env.";
            }

            return redirect()
                ->route('login')
                ->withErrors(['auth' => $message]);
        }

        session()->regenerate();
        session(['lecturer' => [
            'name' => $lecturer['name'] ?? 'ChefVirtuo Lecturer',
            'email' => $email,
            'photo' => $lecturer['photo'] ?? null,
        ]]);

        return redirect()->route('dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('lecturer');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
