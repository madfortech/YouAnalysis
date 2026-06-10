@extends('app')

@section('title', 'Popular')


@section('content')
    <main class="pt-[calc(var(--inset-top)+40px)] px-4">

        <div class="grid gap-2">

            <div class="flex justify-start">
                <h1 class="text-4xl font-black tracking-tight">
                    Popular last 24 hours
                </h1>
                <p class="text-zinc-400 text-sm leading-relaxed"> 
                    Most popular YouTube videos published within the last 24 hours, ranked by total view count.
                </p>
            </div>

            <!-- Fetching data -->
            <div class="flex flex-col">
 
                @foreach($videos as $video)
                    <div class="max-w-sm rounded overflow-hidden shadow-lg border-b mb-4">

                        <iframe 
                            class="aspect-video w-full"
                            src="https://www.youtube.com/embed/{{ $video['id'] }}">
                        </iframe>

                        <div class="px-6 py-4">

                            <div class="font-bold text-xl mb-2">
                                {{ $video['snippet']['title'] }}
                            </div>

                            <p class="text-sm text-gray-400 mt-2">
                                {{ $video['snippet']['channelTitle'] }}
                            </p>

                            <p class="text-sm text-gray-500 mt-2">
                                Published
                                {{ \Carbon\Carbon::parse($video['snippet']['publishedAt'])->format('d M Y, h:i A') }}
                            </p>

                        </div>

                        <div class="px-6 py-4">

                            <span class="inline-block bg-gray-800 rounded-full px-3 py-1 text-sm font-semibold text-white mr-2">
                                {{ number_format($video['statistics']['viewCount'] ?? 0) }}
                                Views
                            </span>

                            <span class="inline-block bg-gray-800 rounded-full px-3 py-1 text-sm font-semibold text-white mr-2 mt-2">
                                {{ number_format($video['statistics']['likeCount'] ?? 0) }}
                                Likes
                            </span>

                            <span class="inline-block bg-gray-800 rounded-full px-3 py-1 text-sm font-semibold text-white mt-2">
                                {{ number_format($video['statistics']['commentCount'] ?? 0) }}
                                Comments
                            </span>

                        </div>

                    </div>
                @endforeach

            </div>
            <!-- End Fetching data -->

             
            

        </div>

    </main>
@endsection