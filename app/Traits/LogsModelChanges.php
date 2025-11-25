<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

trait LogsModelChanges
{
    public static function bootLogsModelChanges()
    {
        static::created(function ($model) {
            $model->logAudit('create', null, $model->toArray());
        });

        static::updated(function ($model) {
            $model->logAudit('update', $model->getOriginal(), $model->getChanges());
        });

        static::deleted(function ($model) {
            $model->logAudit('delete', $model->toArray(), null);
        });
    }

    protected function logAudit($action, $before, $after)
    {
        AuditLog::create([
            'model_type' => get_class($this),
            'model_id' => $this->getKey(),
            'user_id' => Auth::id(),
            'action' => $action,
            'before_changes' => $before,
            'after_changes' => $after,
        ]);
    }
}
