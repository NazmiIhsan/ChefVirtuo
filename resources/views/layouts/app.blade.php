<!doctype html>
<html lang="en">
<head>
    <meta name="theme-color" content="#111111">
<link rel="manifest" href="{{ asset('manifest.json') }}">
<link rel="apple-touch-icon" href="{{ asset('images/icons/icon-192.png') }}">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="apple-mobile-web-app-title" content="ChefVirtuo">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ChefVirtuo')</title>
    <link rel="icon" href="{{ asset('images/chefvirtuo-logo.png') }}" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        cream: '#fff8ea',
                        ink: '#15130f',
                        gold: '#f4b63f',
                        moss: '#79b67a',
                        sage: '#dfead4'
                    },
                    boxShadow: {
                        glass: '0 24px 80px rgba(31, 25, 16, 0.12)'
                    }
                }
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="min-h-screen bg-cream text-ink antialiased">
    @yield('content')
    <script>
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js');
}
</script>
</body>
</html>
