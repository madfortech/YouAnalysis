<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class PopularController extends Controller
{
    /**
     * Get supported YouTube regions
     */
    private function getRegions(): Collection
    {
        try {

            $response = Http::timeout(60)
                ->get(
                    'https://www.googleapis.com/youtube/v3/i18nRegions',
                    [
                        'part' => 'snippet',
                        'order' => 'relevance',
                        'maxResults' => 100,
                        'hl' => 'en_US',
                        'key' => config('services.youtube.key'),
                    ]
                );

            if (! $response->successful()) {
                return collect([]);
            }

            return collect($response->json('items'))
                ->map(function ($region) {

                    return [
                        'code' => $region['id'],
                        'name' => $region['snippet']['name'],
                    ];

                })
                ->sortBy('name')
                ->values();

        } catch (\Exception $e) {

            \Log::error(
                'YouTube Region API Error',
                [
                    'message' => $e->getMessage(),
                ]
            );

            return collect([]);
        }
    }

    /**
     * Show trending videos
     */
    public function index(Request $request)
    {
        $regionCode = $request->get('region', 'IN');

        $regions = $this->getRegions();

        /**
         * Safety check
         */
        if (
            $regions->isNotEmpty() &&
            ! $regions->pluck('code')->contains($regionCode)
        ) {
            $regionCode = 'IN';
        }

        try {

            /**
             * Trending videos
             */
            $response = Http::timeout(60)
                ->get(
                    'https://www.googleapis.com/youtube/v3/videos',
                    [
                        'part' => 'snippet,statistics',
                        'chart' => 'mostPopular',
                        'regionCode' => $regionCode,
                        'maxResults' => 50,
                        'key' => config('services.youtube.key'),
                    ]
                );

            /**
             * Failed response
             */
            if (! $response->successful()) {

                return view(
                    'popular',
                    [
                        'videos' => collect([]),
                        'regions' => $regions,
                        'region' => $regionCode,
                    ]
                );
            }

            /**
             * Videos collection
             */
            $videos = collect(
                $response->json('items')
            );

            /**
             * Last 24 hours filter
             */
            $last24Hours = Carbon::now()
                ->subHours(24);

            $videos = $videos
                ->filter(function ($video) use ($last24Hours) {

                    return Carbon::parse(
                        $video['snippet']['publishedAt']
                    )->greaterThanOrEqualTo(
                        $last24Hours
                    );

                })
                ->sortByDesc(function ($video) {

                    return (int)
                        ($video['statistics']['viewCount'] ?? 0);

                })
                ->values();

            /**
             * Return view
             */
            return view(
                'popular',
                [
                    'videos' => $videos,
                    'regions' => $regions,
                    'region' => $regionCode,
                ]
            );

        } catch (\Exception $e) {

            \Log::error(
                'YouTube Popular API Error',
                [
                    'message' => $e->getMessage(),
                ]
            );

            return view(
                'popular',
                [
                    'videos' => collect([]),
                    'regions' => $regions,
                    'region' => $regionCode,
                ]
            );
        }
    }
}
