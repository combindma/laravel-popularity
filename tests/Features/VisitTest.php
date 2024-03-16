<?php

use Carbon\Carbon;
use Combindma\Popularity\Models\Visit;
use Combindma\Popularity\Tests\Models\Page;
use Combindma\Popularity\Tests\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;

it('creates a visit with the default ip address', function () {
    $page = Page::create();
    $page->visit()->withIp();
    $visit = Visit::first();
    assertDatabaseCount('visits', 1);
    expect($page->visits->count())->toBe(1)
        ->and($visit->data)->toMatchArray(['ip' => request()->ip()]);
});

it('creates a visit with the given ip address', function () {
    $page = Page::create();
    $page->visit()->withIp('my-ip-address');
    $visit = Visit::first();
    assertDatabaseCount('visits', 1);
    expect($page->visits->count())->toBe(1)
        ->and($visit->data)->toMatchArray(['ip' => 'my-ip-address']);
});

it('creates a visit with custom data', function () {
    $page = Page::create();
    $page->visit()->withData([
        'item' => 'value',
    ]);
    $visit = Visit::first();
    assertDatabaseCount('visits', 1);
    expect($page->visits->count())->toBe(1)
        ->and($visit->data)->toMatchArray(['item' => 'value']);
});

it('creates a visit with the default user', function () {
    $page = Page::create();
    $user = new User(['id' => fake()->randomNumber()]);
    actingAs($user);
    $page->visit()->withUser();
    $visit = Visit::first();
    assertDatabaseCount('visits', 1);
    expect($page->visits->count())->toBe(1)
        ->and($visit->data)->toMatchArray(['user_id' => $user->id]);
});

it('creates a visit with the given user', function () {
    $page = Page::create();
    $user = new User(['id' => fake()->randomNumber()]);
    $page->visit()->withUser($user->id);
    $visit = Visit::first();
    assertDatabaseCount('visits', 1);
    expect($page->visits->count())->toBe(1)
        ->and($visit->data)->toMatchArray(['user_id' => $user->id]);
});

it('creates a visit with custom data, ip address and logged user', function () {
    $page = Page::create();
    $user = new User(['id' => fake()->randomNumber()]);
    actingAs($user);
    $page->visit()->withIp()->withUser()->withData(['item' => 'value']);
    $visit = Visit::first();
    assertDatabaseCount('visits', 1);
    expect($page->visits->count())->toBe(1)
        ->and($visit->data)->toMatchArray([
            'ip' => request()->ip(),
            'user_id' => $user->id,
            'item' => 'value',
        ]);
});

it('does not create duplicate visits with the same data', function () {
    $page = Page::create();
    $page->visit()->withIp()->withData(['item' => 'value']);
    $page->visit()->withIp()->withData(['item' => 'value']);
    $visit = Visit::latest()->first();
    assertDatabaseCount('visits', 1);
    expect($page->visits->count())->toBe(1)
        ->and($visit->data)->toMatchArray([
            'ip' => request()->ip(),
            'item' => 'value',
        ]);
});

it('creates duplicate visits with the same data after a default daily timeframe', function () {
    $page = Page::create();
    $page->visit()->withIp();
    Carbon::setTestNow(now()->addDay()->addHour());
    $page->visit()->withIp();
    $visit = Visit::latest()->first();
    assertDatabaseCount('visits', 2);
    expect($page->visits->count())->toBe(2)
        ->and($visit->data)->toMatchArray([
            'ip' => request()->ip(),
        ]);
});

it('does not create visits within the timeframe', function () {
    $page = Page::create();
    Carbon::setTestNow(now()->subDays(2));
    $page->visit();
    Carbon::setTestNow();
    $page->visit();
    $page->visit();
    assertDatabaseCount('visits', 2);
    expect($page->visits->count())->toBe(2);
});

it('creates visits after a hourly timeframe', function () {
    $page = Page::create();
    $page->visit()->hourlyInterval()->withIp();
    Carbon::setTestNow(now()->addHour()->addMinute());
    $page->visit()->hourlyInterval()->withIp();
    $visit = Visit::latest()->first();
    assertDatabaseCount('visits', 2);
    expect($page->visits->count())->toBe(2)
        ->and($visit->data)->toMatchArray(['ip' => request()->ip()]);
});

it('creates visits after a daily timeframe', function () {
    $page = Page::create();
    $page->visit()->dailyInterval()->withIp();
    Carbon::setTestNow(now()->addDay()->addHour());
    $page->visit()->dailyInterval()->withIp();
    $visit = Visit::latest()->first();
    assertDatabaseCount('visits', 2);
    expect($page->visits->count())->toBe(2)
        ->and($visit->data)->toMatchArray(['ip' => request()->ip()]);
});

it('creates visits after a weekly timeframe', function () {
    $page = Page::create();
    $page->visit()->weeklyInterval()->withIp();
    Carbon::setTestNow(now()->addWeek()->addHour());
    $page->visit()->weeklyInterval()->withIp();
    $visit = Visit::latest()->first();
    assertDatabaseCount('visits', 2);
    expect($page->visits->count())->toBe(2)
        ->and($visit->data)->toMatchArray(['ip' => request()->ip()]);
});

it('creates visits after a monthly timeframe', function () {
    $page = Page::create();
    $page->visit()->monthlyInterval()->withIp();
    Carbon::setTestNow(now()->addMonth()->addHour());
    $page->visit()->monthlyInterval()->withIp();
    $visit = Visit::latest()->first();
    assertDatabaseCount('visits', 2);
    expect($page->visits->count())->toBe(2)
        ->and($visit->data)->toMatchArray(['ip' => request()->ip()]);
});
