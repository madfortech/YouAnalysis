@extends('app')

@section('title', 'History')


@section('content')
    <main class="pt-[calc(var(--inset-top)+40px)] px-4">

        <div class="grid gap-2">

            <div class="flex justify-start text-left">
                <h1 class="text-4xl font-black tracking-tight">
                    History
                </h1>
            </div>

            <p class="text-left text-zinc-400 text-sm"> 
                View your previous AI analysis activity 
            </p>

            <ul class="list-disc shadow-md mt-4 overflow-y-auto h-96">
                @forelse($history as $item)
                
                    <li class="list-none rounded-3xl border border-zinc-100 bg-white p-4 shadow-sm mb-2">

                        <div class="flex items-center justify-between">

                            <h2 class="font-bold text-lg text-zinc-900">
                                {{ $item['topic'] }}
                            </h2>

                            <small class="text-xs text-zinc-400">
                                {{ $item['time'] }}
                            </small>

                        </div>

                        <!-- Result -->
                        <div class="mt-3 space-y-3">

                            @php
                                $lines = explode("\n", $item['result'] ?? '');
                            @endphp

                            @foreach($lines as $line)

                                @php
                                    $line = str_replace(['*', '-'], '', $line);

                                    $parts = explode(':', $line, 2);
                                @endphp

                                @if(count($parts) === 2)

                                    <div>

                                        <h3 class="font-bold text-zinc-900">
                                            {{ trim($parts[0]) }}
                                        </h3>

                                         
                                        @if(str_contains(strtolower($parts[0]), 'title'))

                                            <div class="space-y-2 mt-2">

                                                @foreach(explode(',', $parts[1]) as $title)

                                                    <div class="bg-zinc-100 rounded-xl p-3 text-sm font-medium">
                                                        {{ trim($title) }}
                                                    </div>

                                                @endforeach

                                            </div>

                                        @else

                                            <p class="text-sm text-zinc-600 leading-relaxed mt-1">
                                                {{ trim($parts[1]) }}
                                            </p>

                                        @endif
 


                                    </div>

                                @endif

                            @endforeach

                        </div>
                        <!-- End Result -->

                    </li>
                @empty
                <li>
                    No history found
                </li>
                @endforelse
            </ul>

        </div>
    </main>
@endsection