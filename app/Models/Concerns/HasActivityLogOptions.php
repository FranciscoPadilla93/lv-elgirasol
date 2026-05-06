<?php

namespace App\Models\Concerns;

use Spatie\Activitylog\LogOptions;

trait HasActivityLogOptions
{
    protected function activityLogOptions(string $logName, array $attributes): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName($logName)
            ->logOnly($attributes)
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $eventName) => "{$logName}.{$eventName}");
    }
}
