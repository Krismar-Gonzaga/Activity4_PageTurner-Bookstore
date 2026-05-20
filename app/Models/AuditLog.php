<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $table = 'audit_logs';
    
    protected $fillable = [
        'id',
        'user_id',
        'event',
        'auditable_type',
        'auditable_id',
        'old_values',
        'new_values',
        'metadata',
        'checksum',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    protected $keyType = 'string';
    public $incrementing = false;

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) \Str::uuid();
            }
            $model->checksum = static::generateChecksum($model);
        });
    }

    protected static function generateChecksum($model): string
    {
        $data = [
            'id' => $model->id,
            'user_id' => $model->user_id,
            'event' => $model->event,
            'auditable_type' => $model->auditable_type,
            'auditable_id' => $model->auditable_id,
            'old_values' => $model->old_values,
            'new_values' => $model->new_values,
            'created_at' => optional($model->created_at)->toIso8601String(),
        ];

        return hash('sha256', json_encode($data));
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForEvent($query, $event)
    {
        return $query->where('event', $event);
    }

    public function scopeForAuditable($query, $type, $id = null)
    {
        $query->where('auditable_type', $type);
        if ($id) {
            $query->where('auditable_id', $id);
        }
        return $query;
    }

    public function scopeByDateRange($query, $from, $to)
    {
        return $query->whereBetween('created_at', [$from, $to]);
    }

    public function verifyIntegrity(): bool
    {
        $expectedChecksum = static::generateChecksum($this);
        return hash_equals($expectedChecksum, $this->checksum);
    }
}