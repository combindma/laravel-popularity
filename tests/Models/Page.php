<?php

namespace Combindma\Popularity\Tests\Models;

use Combindma\Popularity\Traits\Visitable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property string $title
 */
class Page extends Model
{
    use Visitable;

    protected $fillable = [
        'id',
        'title',
    ];
}
