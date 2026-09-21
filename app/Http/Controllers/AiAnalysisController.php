<?php

namespace App\Http\Controllers;

use App\Services\Groq;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
                        'part' => 'snippet',
                        'q' => $topic,
                        'type' => 'video',
                        'maxResults' => 50,
                        'order' => 'relevance',
                        'key' => config('services.youtube.key'),
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
                        'part' => 'snippet,statistics,contentDetails',
                        'id' => $videoIds,
                        'key' => config('services.youtube.key'),
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

                        'title' => $video['snippet']['title'] ?? '',

                        'description' => $video['snippet']['description'] ?? '',

                        'channel' => $video['snippet']['channelTitle'] ?? '',

                        'publishedAt' => $video['snippet']['publishedAt'] ?? '',

                        'duration' => $video['contentDetails']['duration'] ?? '',

                        'views' => (int) ($video['statistics']['viewCount'] ?? 0),

                        'likes' => (int) ($video['statistics']['likeCount'] ?? 0),

                        'comments' => (int) ($video['statistics']['commentCount'] ?? 0),

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
                            'title' => $video['snippet']['title'] ?? '',
                            'channel' => $video['snippet']['channelTitle'] ?? '',
                            'views' => $video['statistics']['viewCount'] ?? 0,
                            'likes' => $video['statistics']['likeCount'] ?? 0,
                        ];

                    })
                    ->take(5)
                    ->values();
            }

            $averageViews = (int) $videos->avg('views');

            $averageLikes = (int) $videos->avg('likes');

            $averageComments = (int) $videos->avg('comments');

            $bestUploadHour = $videos
                ->groupBy(function ($video) {
                    return Carbon::parse($video['publishedAt'])->format('H:00');
                })
                ->sortByDesc(function ($group) {
                    return $group->count();
                })
                ->keys()
                ->first();

            $bestUploadDay = $videos
                ->groupBy(function ($video) {
                    return Carbon::parse($video['publishedAt'])->format('l');
                })
                ->sortByDesc(function ($group) {
                    return $group->count();
                })
                ->keys()
                ->first();

            /**
             * AI prompt
             */
            $prompt = "

                You are an expert YouTube Growth Strategist and SEO Analyst.

                Analyze ONLY the YouTube API data provided below.

                Do NOT invent statistics.
                Do NOT make assumptions.
                Base every recommendation on the supplied videos.

                Topic:
                {$topic}

                Summary

                Average Views: {$averageViews}

                Average Likes: {$averageLikes}

                Average Comments: {$averageComments}

                Best Upload Hour: {$bestUploadHour}

                Best Upload Day: {$bestUploadDay}

                Top Videos Data:

                {$videos->toJson(JSON_PRETTY_PRINT)}

                Task:

                Analyze the competitors and provide practical recommendations.

                Return using this exact format.

                Competition Level:
                (Low / Medium / High with one-line reason)

                Growth Probability:
                (Low / Medium / High with reason)

                Top Competitor Pattern:
                (What successful videos have in common)

                Recommended Content Type:
                (Tutorial / Shorts / Review / Comparison / Case Study / Guide)

                Content Idea:
                (What video should be created)

                Why This Content Can Win:
                (Explain the opportunity)

                Best SEO Title:

                5 Alternative Titles:

                SEO Description:

                Top SEO Keywords:

                Suggested Tags:

                Suggested Hashtags:

                Thumbnail Text:

                Thumbnail Design Idea:

                Video Hook (First 15 Seconds):

                Recommended Video Duration:

                Best Upload Time:

                Target Audience:

                Competitor Weakness:

                Content Gap:

                Final Strategy:
                (Explain exactly how this video can outperform the competitors.)

            ";

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
                'time' => now()->format('d M Y h:i A'),
                'result' => $result,
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
                    'topic' => $topic,
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
                    'topic' => $topic,
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
