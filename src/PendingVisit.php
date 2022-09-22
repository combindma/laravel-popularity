<?php

namespace Combindma\Popularity;

use Carbon\Carbon;
use Combindma\Popularity\Models\Visit;
use Combindma\Popularity\Traits\SetsPendingIntervals;
use Illuminate\Database\Eloquent\Model;

class PendingVisit
{
    use SetsPendingIntervals;

    protected array $attributes = [];

    protected Carbon $interval;

    public function __construct(protected Model $model)
    {
        $this->dailyInterval();
    }

    public function withIp(string $ip = null): self
    {
        $this->attributes['ip'] = $ip ?? request()->ip();

        return $this;
    }

    public function withData(array $data): self
    {
        $this->attributes = array_merge($this->attributes, $data);

        return $this;
    }

    public function withUser(int $userId = null): self
    {
        $this->attributes['user_id'] = $userId ?? auth()->id();

        return $this;
    }

    protected function buildJsonColumns(): array
    {
        return collect($this->attributes)->mapWithKeys(function ($value, $index) {
            return ['data->'.$index => $value];
        })->toArray();
    }

    protected function shouldBeLoggedAgain(Visit $visit): bool
    {
        return ! $visit->wasRecentlyCreated && $visit->created_at->lt($this->interval);
    }

    public function __destruct()
    {
        $visit = $this->model->visits()->latest()->firstOrCreate($this->buildJsonColumns(), [
            'data' => $this->attributes,
        ]);

        $visit->when($this->shouldBeLoggedAgain($visit), function () use ($visit) {
            $visit->replicate()->save();
        });
    }
}
