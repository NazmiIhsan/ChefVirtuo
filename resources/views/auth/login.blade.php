@extends('layouts.app')

@section('title', 'ChefVirtuo Lecturer Login')

@section('content')
<main class="grid min-h-screen place-items-center px-6 py-10">
    <section class="w-full max-w-5xl overflow-hidden rounded-[2rem] border border-white/70 bg-white/55 shadow-glass backdrop-blur-xl">
        <div class="grid lg:grid-cols-[1.05fr_0.95fr]">
            <div class="flex min-h-[560px] flex-col justify-between bg-ink p-10 text-white">
                <div class="flex items-center gap-4">
                    <div class="grid h-16 w-16 place-items-center rounded-2xl bg-white">
                        <img src="{{ asset('images/chefvirtuo-logo.png') }}" alt="ChefVirtuo logo" class="h-12 w-12 rounded-xl object-contain">
                    </div>
                    <div>
                        <p class="text-sm uppercase tracking-[0.28em] text-gold">VR Culinary Training</p>
                        <h1 class="text-3xl font-black tracking-tight">ChefVirtuo</h1>
                    </div>
                </div>

                <div class="max-w-md">
                    <p class="mb-4 inline-flex rounded-full bg-white/10 px-4 py-2 text-sm font-semibold text-sage ring-1 ring-white/15">Lecturer Analytics Portal</p>
                    <h2 class="text-5xl font-black leading-tight tracking-tight">Monitor culinary mastery from VR quiz data.</h2>
                    <p class="mt-5 text-lg leading-8 text-white/72">Track student readiness, module outcomes, pass rates, and performance trends from the ChefVirtuo VR training application.</p>
                </div>

                <div class="grid grid-cols-3 gap-3 text-sm text-white/70">
                    <div class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/10">
                        <p class="text-2xl font-black text-gold">VR</p>
                        <p>simulation learning</p>
                    </div>
                    <div class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/10">
                        <p class="text-2xl font-black text-gold">TVET</p>
                        <p>student tracking</p>
                    </div>
                    <div class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/10">
                        <p class="text-2xl font-black text-gold">Live</p>
                        <p>Firestore results</p>
                    </div>
                </div>
            </div>

            <div class="flex flex-col justify-center p-8 sm:p-12">
                <div class="mb-8">
                    <p class="text-sm font-bold uppercase tracking-[0.25em] text-moss">Secure Access</p>
                    <h2 class="mt-3 text-3xl font-black tracking-tight">Sign in with Google</h2>
                    <p class="mt-3 text-sm leading-6 text-black/60">Only approved lecturer emails configured in Laravel can access the dashboard.</p>
                </div>

                @if ($errors->any())
                    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div id="firebaseLoginError" class="mb-6 hidden rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700"></div>

                <button id="googleLogin" type="button" class="group flex w-full items-center justify-center gap-3 rounded-2xl bg-ink px-5 py-4 text-base font-bold text-white shadow-xl shadow-black/10 transition duration-200 hover:-translate-y-0.5 hover:bg-black disabled:cursor-not-allowed disabled:opacity-70 disabled:hover:translate-y-0">
                    <span class="grid h-8 w-8 place-items-center rounded-full bg-white text-lg font-black text-ink">G</span>
                    <span id="googleLoginText">Continue with Google</span>
                </button>

                <form id="firebaseSessionForm" method="POST" action="{{ route('auth.firebase.session') }}" class="hidden">
                    @csrf
                    <input type="hidden" name="idToken" id="idToken">
                </form>
            </div>
        </div>
    </section>
</main>

<script type="module">
    import { initializeApp } from 'https://www.gstatic.com/firebasejs/10.12.5/firebase-app.js';
import {
    getAuth,
    GoogleAuthProvider,
    signInWithPopup,
    signInWithRedirect,
    getRedirectResult
} from 'https://www.gstatic.com/firebasejs/10.12.5/firebase-auth.js';
    if (window.location.hostname === '127.0.0.1') {
        window.location.replace(window.location.href.replace('127.0.0.1', 'localhost'));
    }

    const firebaseConfig = @json($firebaseConfig);
    const loginButton = document.getElementById('googleLogin');
    const loginText = document.getElementById('googleLoginText');
    const errorBox = document.getElementById('firebaseLoginError');

    const showLoginError = (message) => {
        errorBox.textContent = message;
        errorBox.classList.remove('hidden');
    };

    const app = initializeApp({
        apiKey: firebaseConfig.api_key,
        authDomain: firebaseConfig.auth_domain,
        projectId: firebaseConfig.project_id,
        storageBucket: firebaseConfig.storage_bucket,
        messagingSenderId: firebaseConfig.messaging_sender_id,
        appId: firebaseConfig.app_id,
    });

    getRedirectResult(auth)
    .then(async (result) => {
        if (!result) return;

        document.getElementById('idToken').value =
            await result.user.getIdToken();

        document.getElementById('firebaseSessionForm').submit();
    })
    .catch(error => {
        console.error(error);
    });

    loginButton.addEventListener('click', async () => {
        errorBox.classList.add('hidden');
        loginButton.disabled = true;
        loginText.textContent = 'Opening Google...';

        try {
            const auth = getAuth(app);
            const provider = new GoogleAuthProvider();
            const isIOSPWA =
    window.matchMedia('(display-mode: standalone)').matches &&
    /iPad|iPhone|iPod/.test(navigator.userAgent);
if (isIOSPWA) {
    await signInWithRedirect(auth, provider);
    return;
}

const result = await signInWithPopup(auth, provider);            document.getElementById('idToken').value = await result.user.getIdToken();
            document.getElementById('firebaseSessionForm').submit();
        } catch (error) {
            const fallbackMessage = 'Google sign-in could not start. Please try again.';
            const messages = {
                'auth/popup-blocked': 'Your browser blocked the Google sign-in popup. Allow popups for this site, then try again.',
                'auth/popup-closed-by-user': 'The Google sign-in popup was closed before login finished.',
                'auth/unauthorized-domain': 'Firebase has not authorized this local address. Add 127.0.0.1 to Firebase Authentication authorized domains, or open the app at localhost:8000.',
                'auth/operation-not-allowed': 'Google sign-in is not enabled in Firebase Authentication. Enable Google as a sign-in provider in Firebase.',
            };

            showLoginError(messages[error.code] ?? error.message ?? fallbackMessage);
            loginButton.disabled = false;
            loginText.textContent = 'Continue with Google';
        }
    });
</script>
@endsection
