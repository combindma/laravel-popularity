<?php

namespace Combindma\Popularity\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Visit extends Model
{
    protected $fillable = [
        'data',
    ];

    protected $casts = [
        'data' => 'json',
    ];

    public function visitable(): MorphTo
    {
        return $this->morphTo();
    }
}
