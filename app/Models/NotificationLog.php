<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    protected $fillable = [
        'workspace_id',
        'price_change_id',
        'channel',
        'status',
        'recipient',
        'error_message',
        'sent_at'
    ];

    protected $casts = [
        'sent_at' => 'datetime'
    ];

    /**
     * Workspace owning this log.
     */
    public function workspace()
    {
        // GP - 19-08-2026 code comment - NotificationLog workspace relation
        return $this->belongsTo(Workspace::class);
    }

    /**
     * Price change triggering this notification.
     */
    public function change()
    {
        // GP - 19-08-2026 code comment - NotificationLog change relation
        return $this->belongsTo(PriceChange::class, 'price_change_id');
    }
}
