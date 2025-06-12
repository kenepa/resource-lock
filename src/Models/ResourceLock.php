<?php

namespace Kenepa\ResourceLock\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Kenepa\ResourceLock\ResourceLockPlugin;

class ResourceLock extends Model
{
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(ResourceLockPlugin::get()->getUserModel());
    }

    public function lockable(): MorphTo
    {
        return $this->morphTo();
    }

    public function isExpired(): bool
    {
        $expiredDate = (new Carbon($this->updated_at))->addSeconds(ResourceLockPlugin::get()->getLockTimeout());

        return Carbon::now()->greaterThan($expiredDate);
    }
}
