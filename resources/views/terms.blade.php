@extends('app')

@section('title', 'Terms of Service')


@section('content')
    <main class="pt-[calc(var(--inset-top)+40px)] px-4">

        <div class="grid gap-2">

            <div class="flex justify-start text-left">
                <h1 class="text-4xl font-black tracking-tight">
                    Terms of Service
                </h1>
            </div>

            <p class="text-left text-zinc-400 text-sm"> 
                Welcome to YouAnalysis.

                This Privacy Policy explains how information is collected, used, and protected while using YouAnalysis.
            </p>

            <ul class="list-disc pl-5 shadow-md mt-4 space-y-2 text-sm text-black leading-[2.3]">
    
                <li>
                    Information is collected to improve platform functionality and user experience.
                </li>

                <li>
                    Temporary session data may be stored for analysis history.
                </li>

                <li>
                    No personal passwords or private YouTube account credentials are collected.
                </li>

                <li>
                    This platform uses YouTube API Services.
                </li>

                <li>
                    Session data can be cleared anytime using the “Delete Everything” option.
                </li>

                <li>
                    This Privacy Policy may be updated periodically without prior notice.
                </li>

            </ul>

        </div>
    </main>
@endsection