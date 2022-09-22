<?php

namespace Combindma\Popularity\Tests\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * @property string $id
 * @property string $name
 * @property string $email
 * @property string $phone
 * @property string $address
 * @property string $city
 * @property string $postcode
 * @property string $state
 */
class User extends Authenticatable
{
    protected $fillable = [
        'id',
        'name',
        'email',
        'password',
        'phone',
        'address',
        'city',
        'postcode',
        'state',
    ];
}
