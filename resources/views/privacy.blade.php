@extends('app')

@section('title', 'Privacy Policy')


@section('content')
    <main class="pt-[calc(var(--inset-top)+40px)] px-4">

        <div class="grid gap-2">

            <div class="flex justify-start text-left">
                <h1 class="text-4xl font-black tracking-tight">
                    Privacy Policy
                </h1>
            </div>

            <p class="text-left text-zinc-400 text-sm leading-relaxed">
                This Privacy Policy explains how information is collected,
                used, and protected while using YouAnalysis.
            </p>

            <ul class="list-disc shadow-md mt-4 text-sm text-black leading-[2.3]">
                
                <li>
                    Search queries may be processed to generate AI-powered
                    YouTube analytics and insights.
                </li>

                <li>
                    Temporary session data may be stored for analysis history
                    and platform functionality.
                </li>

                <li>
                    No passwords, payment details, or private YouTube account
                    credentials are collected.
                </li>

                <li>
                    This platform uses YouTube API Services and related
                    third-party technologies.
                </li>

                <li>
                    Session data can be removed anytime using the
                    “Delete Everything” option.
                </li>

                <li>
                    This Privacy Policy may be updated periodically without
                    prior notice.
                </li>
                
            </ul>

        </div>
    </main>
@endsection