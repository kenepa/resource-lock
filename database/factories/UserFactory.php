<?php

namespace Kenepa\ResourceLock\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Kenepa\ResourceLock\Tests\Resources\Models\User;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
        ];
    }
}
