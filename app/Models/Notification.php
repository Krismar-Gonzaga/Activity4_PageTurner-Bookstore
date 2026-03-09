<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends MorphModel
{
    use HasFactory;

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    public function notifiable()
    {
        return $this->morphTo();
    }

    public function markAsRead()
    {
        if (is_null($this->read_at)) {
            $this->forceFill(['read_at' => $this->freshTimestamp()])->save();
        }
    }

    public function markAsUnread()
    {
        if (!is_null($this->read_at)) {
            $this->forceFill(['read_at' => null])->save();
        }
    }

    public function getReadAttribute()
    {
        return !is_null($this->read_at);
    }

    public function getUnreadAttribute()
    {
        return is_null($this->read_at);
    }
}