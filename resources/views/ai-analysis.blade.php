@extends('app')

@section('title', 'AI Analysis')



@section('content')
    <main class="pt-[calc(var(--inset-top)+40px)] px-4">

        <div class="grid gap-2">

            <div class="flex justify-start">
                <h1 class="text-4xl font-black tracking-tight">
                    AI Analysis
                </h1>
            </div>
            <p class="text-zinc-400 text-sm leading-relaxed"> 
                Analyze YouTube content with AI
            </p>

           
            <div class="flex flex-col">
 
                 
 
                <!-- Result -->
                @isset($result)

                    @php
                        $lines = explode("\n", $result);
                    @endphp

                    <ul class="grid gap-3 mt-4">

                        @foreach($lines as $line)

                            @php
                                $parts = explode(':', $line, 2);
                            @endphp

                            @if(count($parts) === 2)

                                <li class="bg-white rounded-2xl shadow-sm border border-zinc-100 p-4 list-none">

                                    <h3 class="font-bold text-zinc-900">
                                        {{ trim($parts[0]) }}
                                    </h3>

                                 
                                    @if(str_contains(strtolower($parts[0]), 'title'))

                                        <div class="space-y-2 mt-2">

                                            @foreach(explode('-', $parts[1]) as $title)

                                                @if(trim($title))

                                                    <div class="bg-zinc-100 rounded-xl p-3 text-sm font-medium">
                                                        {{ trim($title) }}
                                                    </div>

                                                @endif

                                            @endforeach

                                        </div>

                                    @else

                                        <p class="text-zinc-600 text-sm mt-1 leading-relaxed">
                                            {{ trim($parts[1]) }}
                                        </p>

                                    @endif
 


                                </li>

                            @endif

                        @endforeach

                    </ul>

                @endisset
                <!-- End Result -->
 

               

                <!-- AI Analysis Form -->

                <!-- Error --> 
                 @if(session('error')) 
                    <div class="text-red-500 mt-2"> 
                        {{ session('error') }} 
                    </div> 
                @endif

                <form class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4" method="POST" action="/ai-analysis">
                    @csrf
                    <div class="mb-4">
                       
                        <textarea 
                            name="analysis" 
                            id="analysis" 
                            class="w-full h-32 p-2 border border-gray-300 rounded"
                            placeholder="Enter a YouTube topic...
                                Examples:
                                youtube promotion
                                laravel tutorial
                                ai tools
                                minecraft"
                            required
                            maxlength="150"
                        >   {{ old('analysis') }}
                        </textarea>

                        <!-- Error -->
                        @error('analysis') 
                            <p class="text-red-500 text-sm mt-1"> 
                                {{ $message }} 
                            </p> 
                        @enderror
                    </div>

                    <div class="mb-4">
                        <button type="submit" class="rounded-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4">
                            Analyze
                        </button>
                    </div>
                </form>
                <!-- End AI Analysis Form -->
            </div>
            <!-- End Fetching data -->

             
            

        </div>

    </main>
@endsection