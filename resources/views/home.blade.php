@extends('app')

@section('title', 'Home')


@section('content')
    <main class="pt-[calc(var(--inset-top)+40px)] px-4">

        <div class="grid gap-2">

            <div class="flex justify-center text-center">
                <h1 class="text-4xl font-black tracking-tight">
                    You Analysis
                </h1>
            </div>

            <p class="text-center text-zinc-400 text-sm">
                AI-powered YouTube analytics platform built using the YouTube API.
                Analyze channels, videos, trends, engagement, and audience insights
                with intelligent AI-driven reports.
            </p>

            <div class="mt-2 text-center">
                <span class="text-xs text-zinc-500">
                    Independent project • Not affiliated with YouTube
                </span>
            </div>

            @if(! session()->has('guest'))
                <form
                    method="POST"
                    action="/guest-login"
                    class="mt-8"
                    >

                    @csrf

                    <button
                        type="submit"
                        class="
                            w-full
                            rounded-2xl
                            bg-violet-700
                            py-3
                            text-white
                            font-semibold
                        "
                        >
                            Continue as Guest
                        </button>

                </form>
            @endif

        </div>
    </main>
@endsection