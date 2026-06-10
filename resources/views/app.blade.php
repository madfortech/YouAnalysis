<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        <script src="//unpkg.com/alpinejs" defer></script>

        <title>{{ config('app.name', 'Laravel') }}</title>

        @fonts

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
           
        @endif
    </head>
    <body class="nativephp-safe-area">
        <!-- <header class="w-full lg:max-w-4xl max-w-[335px] text-sm mb-6 not-has-[nav]:hidden">
            @if (Route::has('login'))
                <nav class="flex items-center justify-end gap-4">
                    @auth
                        <a
                            href="{{ url('/dashboard') }}"
                            class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal"
                        >
                            Dashboard
                        </a>
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] border border-transparent hover:border-[#19140035] dark:hover:border-[#3E3E3A] rounded-sm text-sm leading-normal"
                        >
                            Log in
                        </a>

                        @if (Route::has('register'))
                            <a
                                href="{{ route('register') }}"
                                class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal">
                                Register
                            </a>
                        @endif
                    @endauth
                </nav>
            @endif
        </header> -->

        <native:side-nav gestures-enabled="true">
            <native:side-nav-header
                title="YouAnalysis"
                subtitle="AI YouTube Intelligence"
                icon="emoji_objects"
            />

            @if(session()->has('guest'))
            <native:side-nav-group heading="{{ session('guest') }}" :expanded="false">
                <native:side-nav-item
                    id="delete_forever"
                    label="Delete Everything"
                    icon="delete_forever"
                    url="/confirm-logout"
                />
                <native:side-nav-item
                    id="logout"
                    label="Logout"
                    icon="logout"
                    url="/logout"
                />
                <native:side-nav-item
                    id="history"
                    label="History"
                    icon="history"
                    url="/history"
                />
            </native:side-nav-group>
            @endif

            <native:horizontal-divider />

            <native:side-nav-item
                id="privacy"
                label="Privacy Policy"
                icon="privacy_tip"
                url="/privacy"
            />

            <native:side-nav-item
                id="terms"
                label="Terms of Service"
                icon="policy"
                url="/terms"
            />
        </native:side-nav>


        <native:bottom-nav label-visibility="labeled">
            <native:bottom-nav-item
                id="home"
                icon="home"
                label="Home"
                url="/"
                :active="true"
            />
            <native:bottom-nav-item
                id="Explore"
                icon="explore"
                label="Explore"
                url="/popular"
            />
            <native:bottom-nav-item
                id="Analytics"
                icon="analytics"
                label="Analytics"   
                url="/ai-analysis"
            />
            <!-- Bottom nav item for settings -->
            

        </native:bottom-nav>
        
        @yield('content')
        
      

        @if (Route::has('login'))
            <div class="h-14.5 hidden lg:block"></div>
        @endif
    </body>
</html>
