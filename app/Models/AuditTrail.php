<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Log;

class AuditTrail extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_name',
        'event_type',      // ✅ Use event_type instead of action
        'module',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'platform',
        'status',
        'message',         // ✅ Use message instead of description
    ];

    protected $casts = [
        'old_values' => 'json',
        'new_values' => 'json',
    ];

    // ✅ Auto-sync for backward compatibility
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($audit) {
            // Set defaults if empty
            if (empty($audit->event_type)) {
                $audit->event_type = 'UPDATE';
            }
            
            if (empty($audit->status)) {
                $audit->status = 'success';
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    public function getChangesAttribute(): array
    {
        $changes = [];

        try {
            $old = $this->old_values ?? [];
            $new = $this->new_values ?? [];
            
            $eventType = $this->event_type ?? '';

            if (in_array(strtolower($eventType), ['updated', 'update'])) {
                foreach ($new as $key => $value) {
                    if (in_array($key, ['created_at', 'updated_at', 'deleted_at'])) {
                        continue;
                    }

                    if (!array_key_exists($key, $old) || $old[$key] != $value) {
                        $changes[$key] = [
                            'old' => $old[$key] ?? null,
                            'new' => $value
                        ];
                    }
                }
            } elseif (in_array(strtolower($eventType), ['created', 'create'])) {
                foreach ($new as $key => $value) {
                    if (!in_array($key, ['created_at', 'updated_at', 'deleted_at'])) {
                        $changes[$key] = [
                            'old' => null,
                            'new' => $value
                        ];
                    }
                }
            } elseif (in_array(strtolower($eventType), ['deleted', 'delete'])) {
                foreach ($old as $key => $value) {
                    if (!in_array($key, ['created_at', 'updated_at', 'deleted_at'])) {
                        $changes[$key] = [
                            'old' => $value,
                            'new' => null
                        ];
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Error processing audit trail changes: ' . $e->getMessage());
        }

        return $changes;
    }

    // Backward compatibility accessors
    public function getActionAttribute()
    {
        return $this->attributes['event_type'] ?? null;
    }

    public function getDescriptionAttribute()
    {
        return $this->attributes['message'] ?? null;
    }

    // Other methods remain the same...
    public function getOldValuesFormattedAttribute(): array
    {
        $values = $this->old_values ?? [];
        $formatted = [];

        foreach ($values as $key => $value) {
            if (!in_array($key, ['created_at', 'updated_at', 'deleted_at'])) {
                $formatted[$key] = is_array($value) ? json_encode($value) : $value;
            }
        }

        return $formatted;
    }

    public function getModelNameAttribute(): string
    {
        try {
            if (!$this->model_type || !$this->model_id) {
                return '';
            }

            $model = $this->model_type::find($this->model_id);

            if ($model && method_exists($model, 'getAuditTitle')) {
                return $model->getAuditTitle();
            }

            return class_basename($this->model_type) . " #{$this->model_id}";
        } catch (\Exception $e) {
            return class_basename($this->model_type) . " #{$this->model_id}";
        }
    }

    public function getNewValuesFormattedAttribute(): array
    {
        $values = $this->new_values ?? [];
        $formatted = [];

        foreach ($values as $key => $value) {
            if (!in_array($key, ['created_at', 'updated_at', 'deleted_at'])) {
                $formatted[$key] = is_array($value) ? json_encode($value) : $value;
            }
        }

        return $formatted;
    }

    public function getChangesDetailedAttribute(): array
    {
        $changes = $this->changes;
        $result = [];

        foreach ($changes as $field => $change) {
            $old = $change['old'] ?? null;
            $new = $change['new'] ?? null;

            $old = is_array($old) ? json_encode($old) : (is_null($old) ? 'NULL' : $old);
            $new = is_array($new) ? json_encode($new) : (is_null($new) ? 'NULL' : $new);

            $result[$field] = "{$old} → {$new}";
        }

        return $result;
    }
}