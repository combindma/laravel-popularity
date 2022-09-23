<?php

namespace Combindma\Popularity\Traits;

use Carbon\Carbon;
use Combindma\Popularity\Models\Visit;
use Combindma\Popularity\PendingVisit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;

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
    public function visits(): MorphMany
    {
        return $this->morphMany(Visit::class, 'visitable');
    }

    public function scopeWithTotalVisitCount(Builder $query)
    {
        $query->withCount('visits as visit_count_total');
    }

    public function scopePopularAllTime(Builder $query)
    {
        $query->withTotalVisitCount()->orderBy('visit_count_total', 'desc');
    }

    public function scopePopularLastDays(Builder $query, int $days)
    {
        $query->popularBetween(now()->subDays($days), now());
    }

    public function scopePopularLastWeek(Builder $query)
    {
        $query->popularBetween(
            $startOfLastWeek = now()->subDays(7)->startOfWeek(),
            $startOfLastWeek->copy()->endOfWeek()
        );
    }

    public function scopePopularThisWeek(Builder $query)
    {
        $query->popularBetween(now()->startOfWeek(), now()->endOfWeek());
    }

    public function scopePopularLastMonth(Builder $query)
    {
        $query->popularBetween(
            now()->startOfMonth()->subMonthWithoutOverflow(),
            now()->subMonthWithoutOverflow()->endOfMonth()
        );
    }

    public function scopePopularThisMonth(Builder $query)
    {
        $query->popularBetween(now()->startOfMonth(), now()->endOfMonth());
    }

    public function scopePopularThisYear(Builder $query)
    {
        $query->popularBetween(now()->startOfYear(), now()->endOfYear());
    }

    public function scopePopularBetween(Builder $query, Carbon $from, Carbon $to)
    {
        $query->whereHas('visits', $this->betweenScope($from, $to))
            ->withCount([
                'visits as visit_count' => $this->betweenScope($from, $to),
            ]);
    }

    protected function betweenScope(Carbon $from, Carbon $to)
    {
        return function ($query) use ($from, $to) {
            $query->whereBetween('created_at', [$from, $to]);
        };
    }
}
