<?php

namespace App\NativeComponents;

use App\Services\Groq;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Native\Mobile\Edge\NativeComponent;

class AiAnalysis extends NativeComponent
{
    public string $topic = '';

    public string $timeRange = '3_months';

    public ?string $error = null;

    public ?string $result = null;

    /** @var list<array<string, mixed>> */
    public array $videos = [];

    public bool $analyzing = false;

    public function mount(): void
    {
        if (! session()->has('guest')) {
            $this->navigate('/');
        }
    }

    public function setTimeRange(string $range): void
    {
        if (in_array($range, ['3_months', '1_year', '5_years'], true)) {
            $this->timeRange = $range;
        }
    }

    public function wordCount(): int
    {
        return count(preg_split('/\s+/', trim($this->topic), -1, PREG_SPLIT_NO_EMPTY));
    }

    public function onTopicChanged(string $value): void
    {
        $words = preg_split('/\s+/', trim($value), -1, PREG_SPLIT_NO_EMPTY);

        if (count($words) > 155) {
            $this->topic = implode(' ', array_slice($words, 0, 155));

            return;
        }

        $this->topic = $value;
    }

    public function timeRangeStart(): Carbon
    {
        return match ($this->timeRange) {
            '1_year' => Carbon::now()->subYear(),
            '5_years' => Carbon::now()->subYears(5),
            default => Carbon::now()->subMonths(3),
        };
    }

    public function timeRangeLabel(): string
    {
        return match ($this->timeRange) {
            '1_year' => ' (Yearly Trend + Seasonality)',
            '5_years' => ' (Long-Term Historical Trend)',
            default => ' (Recent/Current Trend)',
        };
    }

    public function analyze(): void
    {
        $topic = trim(strip_tags($this->topic));

        if (mb_strlen($topic) < 3) {
            $this->error = 'Enter a topic of at least 3 characters.';

            return;
        }

        if ($this->wordCount() > 155) {
            $this->error = 'Enter a topic of at most 155 words.';

            return;
        }

        $this->analyzing = true;
        $this->error = null;
        $this->result = null;

        $publishedAfter = $this->timeRangeStart();

        try {
            $youtube = Http::timeout(60)
                ->get('https://www.googleapis.com/youtube/v3/search', [
                    'part' => 'snippet',
                    'q' => $topic,
                    'type' => 'video',
                    'maxResults' => 50,
                    'order' => 'date',
                    'publishedAfter' => $publishedAfter->toRfc3339String(),
                    'key' => config('services.youtube.key'),
                ]);

            if (! $youtube->successful()) {
                $this->error = 'Failed to fetch YouTube data.';

                return;
            }

            $videoIds = collect($youtube->json('items'))
                ->pluck('id.videoId')
                ->filter()
                ->implode(',');

            if (empty($videoIds)) {
                $this->error = 'No videos found for analysis.';

                return;
            }

            $stats = Http::timeout(60)
                ->get('https://www.googleapis.com/youtube/v3/videos', [
                    'part' => 'snippet,statistics,contentDetails',
                    'id' => $videoIds,
                    'key' => config('services.youtube.key'),
                ]);

            if (! $stats->successful()) {
                $this->error = 'Failed to fetch video statistics.';

                return;
            }

            $last24Hours = Carbon::now()->subHours(24);

            $videos = collect($stats->json('items'))
                ->filter(function (array $video) use ($publishedAfter) {
                    return Carbon::parse($video['snippet']['publishedAt'])
                        ->greaterThanOrEqualTo($publishedAfter);
                })
                ->sortByDesc(function (array $video) {
                    return (int) ($video['statistics']['viewCount'] ?? 0);
                })
                ->map(function (array $video) {
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

            if ($videos->isEmpty()) {
                $videos = collect($stats->json('items'))
                    ->sortByDesc(function (array $video) {
                        return (int) ($video['statistics']['viewCount'] ?? 0);
                    })
                    ->map(function (array $video) {
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
                ->groupBy(function (array $video) {
                    return Carbon::parse($video['publishedAt'])->format('H:00');
                })
                ->sortByDesc(fn ($group) => $group->count())
                ->keys()
                ->first();

            $bestUploadDay = $videos
                ->groupBy(function (array $video) {
                    return Carbon::parse($video['publishedAt'])->format('l');
                })
                ->sortByDesc(fn ($group) => $group->count())
                ->keys()
                ->first();

            $prompt = "
                You are an expert YouTube Growth Strategist and SEO Analyst.

                Analyze ONLY the YouTube API data provided below.

                Do NOT invent statistics.
                Do NOT make assumptions.
                Base every recommendation on the supplied videos.

                This data covers {$this->timeRangeLabel()}.

                Topic:
                {$topic}

                Summary

                Videos Analyzed: {$videos->count()}

                Average Views: {$averageViews}

                Average Likes: {$averageLikes}

                Average Comments: {$averageComments}

                Best Upload Hour: {$bestUploadHour}

                Best Upload Day: {$bestUploadDay}

                Top Videos Data:

                {$videos->toJson(JSON_PRETTY_PRINT)}

                Task:

                Analyze the competitors and provide practical recommendations.

                Find and explain the key trends visible in this data.

                Return using this exact format.

                Competition Level:
                (Low / Medium / High with one-line reason)

                Growth Probability:
                (Low / Medium / High with reason)

                Top Competitor Pattern:
                (What successful videos have in common)

                Trend:
                (Identify the key trend in this data and explain how it built up)

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

            $result = app(Groq::class)->analyze($prompt);

            if (empty($result)) {
                $result = 'No AI response generated.';
            }

            $history = session('analysis_history', []);
            $history[] = [
                'topic' => $topic,
                'time' => now()->format('d M Y h:i A'),
                'result' => $result,
            ];

            session(['analysis_history' => $history]);

            $this->result = $result;
            $this->videos = $videos->toArray();
        } catch (\Throwable $e) {
            Log::error('AI Analysis Failed', [
                'message' => $e->getMessage(),
                'topic' => $topic,
            ]);

            $this->error = 'AI analysis failed. Please try again later.';
        } finally {
            $this->analyzing = false;
        }
    }

    public function openHistory(): void
    {
        $this->navigate('/history');
    }

    public function parsedResult(): array
    {
        if ($this->result === null || $this->result === '') {
            return [];
        }

        $lines = explode("\n", $this->result);

        $rows = [];

        foreach ($lines as $line) {
            $parts = explode(':', $line, 2);

            if (count($parts) !== 2 || trim($parts[0]) === '') {
                if ($rows !== [] && trim($line) !== '') {
                    $last = array_key_last($rows);
                    $rows[$last]['value'] = trim($rows[$last]['value'].' '.trim($line));
                }

                continue;
            }

            $label = trim($parts[0]);
            $value = trim($parts[1]);

            if (str_contains(strtolower($label), 'title')) {
                $items = array_values(array_filter(array_map(
                    fn ($t) => trim($t),
                    explode(',', $value)
                )));
            } else {
                $items = [];
            }

            $rows[] = [
                'label' => $label,
                'value' => $value,
                'items' => $items,
            ];
        }

        return $rows;
    }

    public function navTitle(): string
    {
        return 'AI Analysis';
    }

    public function render(): View
    {
        return view('native.ai-analysis');
    }
}
