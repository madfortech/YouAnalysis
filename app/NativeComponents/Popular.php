<?php

namespace App\NativeComponents;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Native\Mobile\Edge\NativeComponent;

class Popular extends NativeComponent
{
    /** Displayed region name; the YouTube region code is derived from it. */
    public string $region = 'India';

    /** @var array<string, string> Map of region code => region name. */
    public array $regions = [];

    /** @var list<array<string, mixed>> */
    public array $videos = [];

    public function mount(): void
    {
        $this->loadRegions();
        $this->loadVideos();
    }

    public function regionNames(): array
    {
        return array_values($this->regions) ?: ['India'];
    }

    public function updatedRegion(): void
    {
        $this->loadVideos();
    }

    protected function currentRegionCode(): string
    {
        foreach ($this->regions as $code => $name) {
            if ($name === $this->region) {
                return $code;
            }
        }

        return 'IN';
    }

    protected function loadRegions(): void
    {
        try {
            $response = Http::timeout(60)
                ->get('https://www.googleapis.com/youtube/v3/i18nRegions', [
                    'part' => 'snippet',
                    'order' => 'relevance',
                    'maxResults' => 100,
                    'hl' => 'en_US',
                    'key' => config('services.youtube.key'),
                ]);

            if ($response->successful()) {
                $this->regions = collect($response->json('items'))
                    ->mapWithKeys(fn ($region) => [
                        $region['id'] => $region['snippet']['name'],
                    ])
                    ->sort()
                    ->all();
            }
        } catch (\Throwable $e) {
            Log::error('YouTube Region API Error', ['message' => $e->getMessage()]);
        }
    }

    protected function loadVideos(): void
    {
        try {
            $response = Http::timeout(60)
                ->get('https://www.googleapis.com/youtube/v3/videos', [
                    'part' => 'snippet,statistics',
                    'chart' => 'mostPopular',
                    'regionCode' => $this->currentRegionCode(),
                    'maxResults' => 50,
                    'key' => config('services.youtube.key'),
                ]);

            if (! $response->successful()) {
                Log::error('YouTube Popular API Error', [
                    'status' => $response->status(),
                    'response' => $response->json(),
                ]);

                $this->videos = [];

                return;
            }

            $last24Hours = Carbon::now()->subHours(24);

            $this->videos = collect($response->json('items'))
                ->filter(function (array $video) use ($last24Hours) {
                    return Carbon::parse($video['snippet']['publishedAt'])
                        ->greaterThanOrEqualTo($last24Hours);
                })
                ->sortByDesc(function (array $video) {
                    return (int) ($video['statistics']['viewCount'] ?? 0);
                })
                ->map(function (array $video) {
                    $id = $video['id'];

                    return [
                        'id' => $id,
                        'youtubeUrl' => "https://www.youtube.com/watch?v={$id}",
                        'thumbnail' => "https://i.ytimg.com/vi/{$id}/hqdefault.jpg",
                        'title' => $video['snippet']['title'] ?? '',
                        'channel' => $video['snippet']['channelTitle'] ?? '',
                        'publishedAt' => Carbon::parse($video['snippet']['publishedAt'])
                            ->format('d M Y, h:i A'),
                        'views' => (int) ($video['statistics']['viewCount'] ?? 0),
                        'likes' => (int) ($video['statistics']['likeCount'] ?? 0),
                        'comments' => (int) ($video['statistics']['commentCount'] ?? 0),
                    ];
                })
                ->values()
                ->all();
        } catch (\Throwable $e) {
            Log::error('YouTube Popular API Error', ['message' => $e->getMessage()]);
            $this->videos = [];
        }
    }

    public function openVideo(string $id): void
    {
        $video = collect($this->videos)->firstWhere('id', $id);

        $this->navigate('/watch/'.$id, $video ? ['video' => $video] : []);
    }

    public function navTitle(): string
    {
        return 'Explore';
    }

    public function render(): View
    {
        return view('native.popular');
    }
}
