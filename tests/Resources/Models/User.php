<?php

namespace Kenepa\ResourceLock\Tests\Resources\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'users';

    protected $guarded = [];
}