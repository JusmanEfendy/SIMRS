<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SopHistory extends Model
{
    protected $fillable = [
        'sop_id',
        'user_id',
        'action',
        'description',
        'old_values',
        'new_values',
        'changed_fields',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'changed_fields' => 'array',
    ];

    /**
     * Get the SOP that this history belongs to.
     */
    public function sop(): BelongsTo
    {
        return $this->belongsTo(Sop::class)->withTrashed();
    }

    /**
     * Get the user who made the change.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get action label in Indonesian.
     */
    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            'created' => 'Dibuat',
            'updated' => 'Diperbarui',
            'status_changed' => 'Status Berubah',
            'deleted' => 'Dihapus',
            'restored' => 'Dipulihkan',
            default => ucfirst($this->action),
        };
    }

    /**
     * Get action color for badge.
     */
    public function getActionColorAttribute(): string
    {
        return match ($this->action) {
            'created' => 'success',
            'updated' => 'info',
            'status_changed' => 'warning',
            'deleted' => 'danger',
            'restored' => 'success',
            default => 'gray',
        };
    }

    /**
     * Get action icon.
     */
    public function getActionIconAttribute(): string
    {
        return match ($this->action) {
            'created' => 'heroicon-o-plus-circle',
            'updated' => 'heroicon-o-pencil',
            'status_changed' => 'heroicon-o-arrow-path',
            'deleted' => 'heroicon-o-trash',
            'restored' => 'heroicon-o-arrow-uturn-left',
            default => 'heroicon-o-document',
        };
    }

    /**
     * Scope to filter by directorate via unit relationship.
     */
    public function scopeForDirectorate($query, $dirId)
    {
        return $query->whereHas('sop.unit', function ($q) use ($dirId) {
            $q->where('dir_id', $dirId);
        });
    }
}
