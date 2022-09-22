<?php

namespace Combindma\Popularity\Traits;

trait SetsPendingIntervals
{
    public function hourlyInterval(): self
    {
        $this->interval = now()->subHour();

        return $this;
    }

    public function dailyInterval(): self
    {
        $this->interval = now()->subDay();

        return $this;
    }

    public function weeklyInterval(): self
    {
        $this->interval = now()->subWeek();

        return $this;
    }

    public function monthlyInterval(): self
    {
        $this->interval = now()->subMonth();

        return $this;
    }
}
