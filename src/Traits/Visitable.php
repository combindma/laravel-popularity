<?php

namespace Combindma\Popularity\Traits;

use Carbon\Carbon;
use Combindma\Popularity\Models\Visit;
use Combindma\Popularity\PendingVisit;
use Illuminate\Database\Eloquent\Builder;

trait Visitable
{
    /**
     * Log a visit into the database if it does not exist on current day
     * (Registers unique visitors)
     */
    public function visit(): PendingVisit
    {
        return new PendingVisit($this);
    }

    /**
     * Setting relationship
     */
    public function visits(): mixed
    {
        return $this->morphMany(Visit::class, 'visitable');
    }

    public function scopeWithTotalVisitCount(Builder $query): Builder
    {
        return $query->withCount('visits as visit_count_total');
    }

    public function scopePopularAllTime(Builder $query): Builder
    {
        return $query->withTotalVisitCount()->orderBy('visit_count_total', 'desc');
    }

    public function scopePopularBetween(Builder $query, Carbon $from, Carbon $to): Builder
    {
        return $query->whereHas('visits', function ($query) use ($from, $to) {
            $query->whereBetween('created_at', [$from, $to]);
        })
            ->withCount([
                'visits as visit_count' => function ($query) use ($from, $to) {
                    $query->whereBetween('created_at', [$from, $to]);
                },
            ]);
    }

    public function scopePopularLastDays(Builder $query, int $days): Builder
    {
        return $query->popularBetween(now()->subDays($days), now());
    }

    public function scopePopularThisWeek(Builder $query): Builder
    {
        return $query->popularBetween(now()->startOfWeek(), now()->endOfWeek());
    }

    public function scopePopularThisMonth(Builder $query): Builder
    {
        return $query->popularBetween(now()->startOfMonth(), now()->endOfMonth());
    }

    public function scopePopularThisYear(Builder $query): Builder
    {
        return $query->popularBetween(now()->startOfYear(), now()->endOfYear());
    }
}
