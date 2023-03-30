<?php

namespace Kenepa\ResourceLock\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ResourceLock extends Model
{
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('resource-lock.models.User'));
    }

    public function lockable(): MorphTo
    {
        return $this->morphTo();
    }
}
