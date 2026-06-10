<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\Groq;
use Carbon\Carbon;

class AiAnalysisController extends Controller
{
    /**
     * Show AI analysis page
     */
    public function index()
    {
        return view('ai-analysis');
    }

    /**
     * Show analysis history
     */
    public function history()
    {
        $history = session(
            'analysis_history',
            []
        );

        return view(
            'history',
            [
                'history' => array_reverse($history),
            ]
        );
    }

    /**
     * Analyze YouTube topic using
     * real YouTube data + AI
     */
    public function analyze(
        Request $request,
        Groq $ai
    ) {

        /**
         * Validate input
         */
        $validated = $request->validate([
            'analysis' => 'required|string|min:3|max:150',
        ]);

        /**
         * Clean topic
         */
        $topic = trim(
            strip_tags(
                $validated['analysis']
            )
        );

        try {

            /**
             * Search YouTube videos
             */
            $youtube = Http::timeout(60)
                ->get(
                    'https://www.googleapis.com/youtube/v3/search',
                    [
                        'part'       => 'snippet',
                        'q'          => $topic,
                        'type'       => 'video',
                        'maxResults' => 10,
                        'key'        => env('YOU_ANALYSIS_API_KEY'),
                    ]
                );

            /**
             * Failed search request
             */
            if (! $youtube->successful()) {

                return back()
                    ->withInput()
                    ->with([
                        'error' => 'Failed to fetch YouTube data.',
                    ]);
            }

            /**
             * Get video IDs
             */
            $videoIds = collect(
                $youtube->json('items')
            )
            ->pluck('id.videoId')
            ->filter()
            ->implode(',');

            /**
             * Empty results
             */
            if (empty($videoIds)) {

                return back()
                    ->withInput()
                    ->with([
                        'error' => 'No videos found for analysis.',
                    ]);
            }

            /**
             * Fetch video statistics
             */
            $stats = Http::timeout(60)
                ->get(
                    'https://www.googleapis.com/youtube/v3/videos',
                    [
                        'part' => 'snippet,statistics',
                        'id'   => $videoIds,
                        'key'  => env('YOU_ANALYSIS_API_KEY'),
                    ]
                );

            /**
             * Failed statistics request
             */
            if (! $stats->successful()) {

                return back()
                    ->withInput()
                    ->with([
                        'error' => 'Failed to fetch video statistics.',
                    ]);
            }

            /**
             * Last 24 hour filter
             */
            $last24Hours = Carbon::now()
                ->subHours(24);

            /**
             * Prepare video data
             */
            $videos = collect(
                $stats->json('items')
            )
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
            ->map(function ($video) {

                return [
                    'title'   => $video['snippet']['title'] ?? '',
                    'channel' => $video['snippet']['channelTitle'] ?? '',
                    'views'   => $video['statistics']['viewCount'] ?? 0,
                    'likes'   => $video['statistics']['likeCount'] ?? 0,
                ];

            })
            ->take(5)
            ->values();

            /**
             * Fallback if no fresh videos
             */
            if ($videos->isEmpty()) {

                $videos = collect(
                    $stats->json('items')
                )
                ->sortByDesc(function ($video) {

                    return (int)
                        ($video['statistics']['viewCount'] ?? 0);

                })
                ->map(function ($video) {

                    return [
                        'title'   => $video['snippet']['title'] ?? '',
                        'channel' => $video['snippet']['channelTitle'] ?? '',
                        'views'   => $video['statistics']['viewCount'] ?? 0,
                        'likes'   => $video['statistics']['likeCount'] ?? 0,
                    ];

                })
                ->take(5)
                ->values();
            }

            /**
             * AI prompt
             */
            $prompt = "
            You are a professional YouTube growth strategist.

            Analyze this YouTube topic using REAL YouTube trend data.

            Topic:
            {$topic}

            Trending Video Data:
            {$videos->toJson()}

            Return concise bullet points only.

            Include:
            - Growth probability
            - Competition level
            - Viral potential
            - Best content angle
            - Target audience
            - Best video duration
            - Best upload time (IST)
            - SEO keywords
            - Suggested hashtags
            - 3 viral title ideas
            - Thumbnail concept
            - Competitor strategy
            - Content gap opportunity

            Important:
            - Use provided YouTube data
            - Keep response concise
            - No markdown
            - No fake claims
            - No invented statistics
            ";

            /**
             * AI response
             */
            $result = $ai->analyze($prompt);

            /**
             * Empty AI response
             */
            if (empty($result)) {

                $result = 'No AI response generated.';
            }

            /**
             * Save history
             */
            $history = session(
                'analysis_history',
                []
            );

            $history[] = [
                'topic' => $topic,
                'time'  => now()->format('d M Y h:i A'),
                'result' => $result
            ];

            session([
                'analysis_history' => $history,
            ]);

            /**
             * Return view
             */
            return view(
                'ai-analysis',
                [
                    'result' => $result,
                    'topic'  => $topic,
                    'videos' => $videos,
                ]
            );

        } catch (\Throwable $e) {

            /**
             * Log error
             */
            Log::error(
                'AI Analysis Failed',
                [
                    'message' => $e->getMessage(),
                    'topic'   => $topic,
                ]
            );

            return back()
                ->withInput()
                    ->with([
                        'error' => 'AI analysis failed. Please try again later.',
                    ]);
        }
    }
}