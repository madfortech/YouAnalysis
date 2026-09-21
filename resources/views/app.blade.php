<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        <script src="//unpkg.com/alpinejs" defer></script>

        <title>@yield('title', config('app.name', 'Laravel'))</title>

        @fonts

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="nativephp-safe-area bg-zinc-50 text-zinc-900">
        @yield('content')

        <footer class="mx-auto max-w-lg px-4 pt-4 text-center text-xs text-zinc-400">
            @if (session()->has('guest'))
                <div class="mb-2 flex flex-wrap justify-center gap-x-4 gap-y-1">
                    <a href="/history" class="text-zinc-500 hover:text-zinc-700">History</a>
                    <a href="/confirm-logout" class="text-red-400 hover:text-red-600">Delete Everything</a>
                    <a href="/logout" class="text-zinc-500 hover:text-zinc-700">Logout</a>
                </div>
            @endif

            <div class="flex justify-center gap-x-4">
                <a href="/privacy" class="hover:text-zinc-600">Privacy</a>
                <a href="/terms" class="hover:text-zinc-600">Terms</a>
            </div>

            <p class="mt-2">
                Independent project • Not affiliated with YouTube
            </p>
        </footer>

        <div class="h-24"></div>

        <nav class="fixed inset-x-0 bottom-0 z-50 border-t border-zinc-200 bg-white/95 backdrop-blur pb-[var(--inset-bottom)]">
            <div class="mx-auto grid max-w-lg grid-cols-3">
                <a href="/" class="py-3 text-center text-xs font-semibold {{ request()->is('/') ? 'text-violet-700' : 'text-zinc-400' }}">
                    Home
                </a>
                <a href="/popular" class="py-3 text-center text-xs font-semibold {{ request()->is('popular') ? 'text-violet-700' : 'text-zinc-400' }}">
                    Explore
                </a>
                <a href="/ai-analysis" class="py-3 text-center text-xs font-semibold {{ request()->is('ai-analysis') ? 'text-violet-700' : 'text-zinc-400' }}">
                    Analytics
                </a>
            </div>
        </nav>
    </body>
</html>