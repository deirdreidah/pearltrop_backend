<?php

namespace App\Traits;

use App\Models\AuditTrail;
use Illuminate\Support\Facades\Log;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function ($model) {
            $model->logAudit('CREATE', 'Created successfully', [], $model->getAttributes());
        });

        static::updated(function ($model) {
            $oldValues = array_intersect_key($model->getOriginal(), $model->getChanges());
            $newValues = $model->getChanges();
            
            // Don't log if the only change is updated_at
            if (count($newValues) === 1 && array_key_exists('updated_at', $newValues)) {
                return;
            }

            $model->logAudit('UPDATE', 'Updated successfully', $oldValues, $newValues);
        });

        static::deleted(function ($model) {
            $model->logAudit('DELETE', 'Deleted successfully', $model->getAttributes(), []);
        });
    }

    protected function logAudit($eventType, $message, $oldValues, $newValues)
    {
        try {
            $user = auth()->user();
            
            AuditTrail::create([
                'user_id' => $user ? $user->id : null,
                'user_name' => $user ? $user->name : 'System',
                'event_type' => $eventType,
                'module' => class_basename($this),
                'model_type' => get_class($this),
                'model_id' => $this->id,
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'platform' => request()->header('sec-ch-ua-platform') ?: null,
                'status' => 'success',
                'message' => $message,
            ]);
        } catch (\Exception $e) {
            Log::error('AuditTrail Creation Failed: ' . $e->getMessage());
        }
    }
}
