<?php

use Carbon\Carbon;
use Combindma\Popularity\Tests\Models\Page;
use function Pest\Laravel\assertDatabaseCount;

it('gets the total visit count', function () {
    $page = Page::create();
    $page->visit();
    $page = Page::withTotalVisitCount()->first();
    assertDatabaseCount('visits', 1);
    expect($page->visit_count_total)->toEqual(1);
});

it('gets records by all time popularity', function () {
    $popularPage = Page::create();
    Carbon::setTestNow(now()->subDays(2));
    $popularPage->visit();
    Carbon::setTestNow();
    $popularPage->visit();
    Page::create()->visit();
    Page::create()->visit();

    $pages = Page::popularAlltime()->get();
    expect($pages->count())->toBe(3)
        ->and($pages->first()->visit_count_total)->toEqual(2);
});

it('gets popular records between two dates', function () {
    Page::insert([
        ['title' => 'title'], ['title' => 'title'],
    ]);
    $pages = Page::all();
    $pageId = $pages[0]->id;
    Carbon::setTestNow(Carbon::createFromDate(1990, 12, 03));
    $pages[0]->visit();
    Carbon::setTestNow();
    $pages[0]->visit();
    $pages[1]->visit();

    $pages = Page::popularBetween(Carbon::createFromDate(1990, 12, 01), Carbon::createFromDate(1990, 12, 04))->get();
    expect($pages->count())->toBe(1)
        ->and($pages->first()->visit_count)->toEqual(1)
        ->and($pages->first()->id)->toBe($pageId);
});

it('gets popular records by the last x days', function () {
    Page::insert([
        ['title' => 'title'], ['title' => 'title'],
    ]);
    $pages = Page::all();
    $pageId = $pages[1]->id;
    Carbon::setTestNow(now()->subDays(4));
    $pages[0]->visit();
    Carbon::setTestNow();
    $pages[1]->visit();

    $pages = Page::popularLastDays(2)->get();
    expect($pages->count())->toBe(1)
        ->and($pages->first()->visit_count)->toEqual(1)
        ->and($pages->first()->id)->toBe($pageId);
});

it('gets popular records by this week', function () {
    Page::insert([
        ['title' => 'title'], ['title' => 'title'],
    ]);
    $pages = Page::all();
    $pageId = $pages[1]->id;
    Carbon::setTestNow(now()->subWeek()->subDay());
    $pages[0]->visit();
    Carbon::setTestNow();
    $pages[1]->visit();

    $pages = Page::popularThisWeek()->get();
    expect($pages->count())->toBe(1)
        ->and($pages->first()->visit_count)->toEqual(1)
        ->and($pages->first()->id)->toBe($pageId);
});

it('gets popular records by this month', function () {
    Page::insert([
        ['title' => 'title'], ['title' => 'title'],
    ]);
    $pages = Page::all();
    $pageId = $pages[1]->id;
    Carbon::setTestNow(now()->subMonth()->subDay());
    $pages[0]->visit();
    Carbon::setTestNow();
    $pages[1]->visit();

    $pages = Page::popularThisMonth()->get();
    expect($pages->count())->toBe(1)
        ->and($pages->first()->visit_count)->toEqual(1)
        ->and($pages->first()->id)->toBe($pageId);
});

it('gets popular records by this year', function () {
    Page::insert([
        ['title' => 'title'], ['title' => 'title'],
    ]);
    $pages = Page::all();
    $pageId = $pages[1]->id;
    Carbon::setTestNow(now()->subYear()->subDay());
    $pages[0]->visit();
    Carbon::setTestNow();
    $pages[1]->visit();

    $pages = Page::popularThisYear()->get();
    expect($pages->count())->toBe(1)
        ->and($pages->first()->visit_count)->toEqual(1)
        ->and($pages->first()->id)->toBe($pageId);
});
