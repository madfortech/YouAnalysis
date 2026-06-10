@extends('app')

@section('title', 'Confirm Logout')


@section('content')
    <main class="pt-[calc(var(--inset-top)+40px)] px-4">

        <div class="grid gap-2">

            <div class="flex justify-center text-center">
                <h1 class="text-4xl font-black tracking-tight">
                    Delete Everything?
                </h1>
            </div>

            <p class="text-center text-zinc-400 text-sm">
                This will permanently remove your session and all saved data from this device.
            </p>

            <div class="mt-6 space-y-3"> 
                <a href="/delete-everything" class=" block w-full rounded-2xl bg-red-600 py-3 text-center     text-white           
                font-semibold"> 
                    Yes, Delete Everything 
                </a> 
                <a href="/ai-analysis" class=" block w-full rounded-2xl bg-zinc-300 py-3 text-center text-black  
                    font-semibold"> 
                    Cancel 
                </a> 
            </div>
           

        </div>
    </main>
@endsection