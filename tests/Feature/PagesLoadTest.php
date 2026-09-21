<?php

use App\NativeComponents\AiAnalysis;
use App\NativeComponents\ConfirmLogout;
use App\NativeComponents\History;
use App\NativeComponents\Home;
use App\NativeComponents\Popular;
use App\NativeComponents\Privacy;
use App\NativeComponents\Terms;
use App\NativeComponents\Watch;
use Illuminate\Support\Facades\Http;
use Native\Mobile\Testing\Native;

function fakeEmptyYouTube(): void
{
    Http::fake([
        'https://www.googleapis.com/*' => Http::response(['items' => []], 200),
    ]);
}

test('home renders for visitors', function () {
    Native::test(Home::class, platform: 'android')
        ->assertSee('You Analysis')
        ->assertSee('Continue as Guest');
});

test('home renders guest quick actions when session has guest', function () {
    session(['guest' => 'Guest']);

    Native::test(Home::class, platform: 'android')
        ->assertSee('Start a new analysis')
        ->assertSee('Explore popular videos')
        ->assertSee('View history')
        ->assertSee('Logout');
});

test('continue as guest starts a guest session and navigates to ai analysis', function () {
    Native::test(Home::class, platform: 'android')
        ->tap('Continue as Guest')
        ->assertNavigatedTo('/ai-analysis');

    $this->assertTrue(session()->has('guest'));
});

test('home logout ends the guest session', function () {
    session(['guest' => 'Guest']);

    Native::test(Home::class, platform: 'android')
        ->tap('Logout');

    $this->assertFalse(session()->has('guest'));
});

test('popular page renders the heading and region selector', function () {
    fakeEmptyYouTube();

    Native::test(Popular::class, platform: 'android')
        ->assertSee('Popular last 24 hours');
});

test('popular page displays fetched videos within the last 24 hours', function () {
    fakeEmptyYouTube();

    Native::test(Popular::class, platform: 'android')
        ->set('videos', [[
            'id' => 'abc123',
            'thumbnail' => 'https://i.ytimg.com/vi/abc123/hqdefault.jpg',
            'title' => 'Fresh Video Please Show',
            'channel' => 'Test Channel',
            'publishedAt' => now()->subHours(2)->format('d M Y, h:i A'),
            'views' => 1000,
            'likes' => 100,
            'comments' => 10,
        ]])
        ->assertSee('Fresh Video Please Show')
        ->assertSee('Test Channel')
        ->assertSee('1,000')
        ->assertDontSee('No videos were found');
});

test('tapping a popular video navigates to the watch screen', function () {
    fakeEmptyYouTube();

    Native::test(Popular::class, platform: 'android')
        ->set('videos', [[
            'id' => 'abc123',
            'thumbnail' => 'https://i.ytimg.com/vi/abc123/hqdefault.jpg',
            'title' => 'Fresh Video Please Show',
            'channel' => 'Test Channel',
            'publishedAt' => now()->subHours(2)->format('d M Y, h:i A'),
            'views' => 1000,
            'likes' => 100,
            'comments' => 10,
        ]])
        ->tap('Fresh Video Please Show')
        ->assertNavigatedTo('/watch/abc123');
});

test('watch page renders the video player and details', function () {
    Native::test(Watch::class, params: ['id' => 'abc123'], data: [
        'video' => [
            'id' => 'abc123',
            'title' => 'Fresh Video Please Show',
            'channel' => 'Test Channel',
            'publishedAt' => now()->subHours(2)->format('d M Y, h:i A'),
            'views' => 1000,
            'likes' => 100,
            'comments' => 10,
        ],
    ], platform: 'android')
        ->assertSee('Fresh Video Please Show')
        ->assertSee('Test Channel')
        ->assertSee('1,000')
        ->assertDontSee('No video selected')
        ->assertElement('webview', fn ($n) => ($n['props']['src'] ?? null) === 'https://www.youtube.com/watch?v=abc123')
        ->assertElement('webview', fn ($n) => ! str_contains($n['props']['src'] ?? '', '/embed/'))
        ->assertElement('webview', fn ($n) => ($n['props']['javascript'] ?? null) === true)
        ->assertElement('webview', fn ($n) => ($n['props']['dom_storage'] ?? null) === true);
});

test('ai analysis redirects to home when not a guest', function () {
    Native::test(AiAnalysis::class, platform: 'android')
        ->assertNavigatedTo('/');
});

test('ai analysis renders for guests', function () {
    session(['guest' => 'Guest']);

    Native::test(AiAnalysis::class, platform: 'android')
        ->assertSee('AI Channel Analysis')
        ->assertSee('Recent/Current Trend')
        ->assertSee('Seasonality')
        ->assertSee('Long-Term Historical Trend')
        ->assertSee('Analyze');
});

test('ai analysis time range buttons switch the selected period', function () {
    session(['guest' => 'Guest']);

    Native::test(AiAnalysis::class, platform: 'android')
        ->assertSet('timeRange', '3_months')
        ->tap('Yearly Trend + Seasonality')
        ->assertSet('timeRange', '1_year')
        ->tap('Long-Term Historical Trend')
        ->assertSet('timeRange', '5_years')
        ->tap('Recent/Current Trend')
        ->assertSet('timeRange', '3_months');
});

test('ai analysis topic input truncates beyond 155 words', function () {
    session(['guest' => 'Guest']);

    $longText = implode(' ', array_fill(0, 200, 'word'));

    $component = Native::test(AiAnalysis::class, platform: 'android');
    $component->call('onTopicChanged', $longText)->instance();

    expect($component->instance()->wordCount())->toBe(155)
        ->and($component->instance()->topic)->toBe(implode(' ', array_fill(0, 155, 'word')));
});

test('ai analysis runs analysis and renders the ai result', function () {
    session(['guest' => 'Guest']);

    Http::fake([
        'https://www.googleapis.com/youtube/v3/search*' => Http::response([
            'items' => [
                ['id' => ['videoId' => 'v1'], 'snippet' => ['publishedAt' => now()->subDays(10)->toIso8601String()]],
                ['id' => ['videoId' => 'v2'], 'snippet' => ['publishedAt' => now()->subDays(5)->toIso8601String()]],
            ],
        ], 200),
        'https://www.googleapis.com/youtube/v3/videos*' => Http::response([
            'items' => [
                [
                    'id' => 'v1',
                    'snippet' => [
                        'title' => 'Cricket Video One',
                        'description' => 'First cricket video',
                        'channelTitle' => 'Cricket Channel',
                        'publishedAt' => now()->subDays(10)->toIso8601String(),
                    ],
                    'contentDetails' => ['duration' => 'PT10M'],
                    'statistics' => ['viewCount' => '5000', 'likeCount' => '500', 'commentCount' => '50'],
                ],
                [
                    'id' => 'v2',
                    'snippet' => [
                        'title' => 'Cricket Video Two',
                        'description' => 'Second cricket video',
                        'channelTitle' => 'Cricket Channel',
                        'publishedAt' => now()->subDays(5)->toIso8601String(),
                    ],
                    'contentDetails' => ['duration' => 'PT8M'],
                    'statistics' => ['viewCount' => '3000', 'likeCount' => '300', 'commentCount' => '30'],
                ],
            ],
        ], 200),
        'https://api.groq.com/*' => Http::response([
            'choices' => [['message' => ['content' => "Competition Level:\nMedium - several active creators\n\nTrend:\nCricket content grows steadily"]]],
        ], 200),
    ]);

    Native::test(AiAnalysis::class, platform: 'android')
        ->set('topic', 'cricket match analysis')
        ->tap('Analyze')
        ->assertSet('analyzing', false)
        ->assertSee('Analysis result')
        ->assertSee('Competition Level')
        ->assertSee('Medium')
        ->assertSee('Trend');
});

test('ai analysis falls back to free OpenRouter models when groq is rate limited', function () {
    session(['guest' => 'Guest']);
    config()->set('services.openrouter.key', 'test-key');

    Http::fake([
        'https://www.googleapis.com/youtube/v3/search*' => Http::response([
            'items' => [
                ['id' => ['videoId' => 'v1'], 'snippet' => ['publishedAt' => now()->subDays(1)->toIso8601String()]],
            ],
        ], 200),
        'https://www.googleapis.com/youtube/v3/videos*' => Http::response([
            'items' => [
                [
                    'id' => 'v1',
                    'snippet' => [
                        'title' => 'Cricket Video One',
                        'description' => 'First cricket video',
                        'channelTitle' => 'Cricket Channel',
                        'publishedAt' => now()->subDays(1)->toIso8601String(),
                    ],
                    'contentDetails' => ['duration' => 'PT10M'],
                    'statistics' => ['viewCount' => '5000', 'likeCount' => '500', 'commentCount' => '50'],
                ],
            ],
        ], 200),
        'https://api.groq.com/*' => Http::response(['error' => 'rate limit exceeded'], 429),
        'https://openrouter.ai/api/v1/chat/completions' => Http::response([
            'choices' => [['message' => ['content' => "Competition Level:\nMedium - free model fallback worked\n\nTrend:\nGrowing"]]],
        ], 200),
    ]);

    Native::test(AiAnalysis::class, platform: 'android')
        ->set('topic', 'cricket match analysis')
        ->tap('Analyze')
        ->assertSet('analyzing', false)
        ->assertSee('free model fallback worked');

    Http::assertSent(
        fn ($request) => $request->url() === 'https://openrouter.ai/api/v1/chat/completions'
            && $request['models'][0] === 'google/gemma-4-31b-it:free'
    );
});

test('ai analysis uses the selected period cutoff in the search', function () {
    session(['guest' => 'Guest']);
    $expectedAfter = now()->subYear()->toRfc3339String();

    Http::fake([
        'https://www.googleapis.com/youtube/v3/search*' => function ($request) use ($expectedAfter) {
            Http::assertSent(fn ($req) => $req['publishedAfter'] === $expectedAfter && $req['order'] === 'date');

            return Http::response(['items' => []], 200);
        },
        'https://www.googleapis.com/youtube/v3/videos*' => Http::response(['items' => []], 200),
        'https://api.groq.com/*' => Http::response(['choices' => [['message' => ['content' => 'result']]]], 200),
    ]);

    Native::test(AiAnalysis::class, platform: 'android')
        ->set('topic', 'cricket match analysis')
        ->tap('Yearly Trend + Seasonality')
        ->tap('Analyze')
        ->assertSet('analyzing', false);
});

test('history page renders', function () {
    Native::test(History::class, platform: 'android')
        ->assertSee('Analysis history');
});

test('history renders saved analyses', function () {
    session([
        'analysis_history' => [
            ['topic' => 'Cricket', 'time' => '20 Sep 2026 10:00 AM', 'result' => "Competition Level:\nHigh"],
        ],
    ]);

    Native::test(History::class, platform: 'android')
        ->assertSee('Cricket')
        ->assertSee('Competition Level');
});

test('privacy page renders', function () {
    Native::test(Privacy::class, platform: 'android')
        ->assertSee('Privacy Policy');
});

test('terms page renders', function () {
    Native::test(Terms::class, platform: 'android')
        ->assertSee('Terms of Service');
});

test('confirm logout page renders', function () {
    Native::test(ConfirmLogout::class, platform: 'android')
        ->assertSee('Delete Everything?');
});

test('confirm logout delete everything clears session and returns home', function () {
    session(['guest' => 'Guest', 'analysis_history' => []]);

    Native::test(ConfirmLogout::class, platform: 'android')
        ->call('deleteEverything')
        ->assertReplacedWith('/');

    $this->assertFalse(session()->has('guest'));
});

test('guest login redirects to ai analysis', function () {
    $this->post('/guest-login')
        ->assertRedirect('/ai-analysis');
});
