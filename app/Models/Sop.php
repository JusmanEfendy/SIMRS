<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sop extends Model
{
    use SoftDeletes;

    /**
     * Fields to track for history logging.
     */
    protected static array $trackableFields = [
        'sop_name', 'sk_number', 'id_unit', 'type_sop', 'file_path',
        'approval_date', 'start_date', 'expired', 'desc', 'status', 'days_left', 'feedback'
    ];

    /**
     * Temporary storage for pending history logs (not persisted to database).
     */
    protected static array $pendingHistoryLogs = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'id_unit', 'id_unit');
    }

    /**
     * Get all history logs for this SOP.
     */
    public function histories(): HasMany
    {
        return $this->hasMany(SopHistory::class)->orderBy('created_at', 'desc');
    }

    /**
     * Log a history entry for this SOP.
     */
    public function logHistory(string $action, string $description, ?array $oldValues = null, ?array $newValues = null, ?array $changedFields = null): SopHistory
    {
        return $this->histories()->create([
            'user_id' => auth()->id() ?? $this->user_id,
            'action' => $action,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'changed_fields' => $changedFields,
        ]);
    }

    protected static function boot()
    {
        parent::boot();

        // Generate SOP ID on create
        static::creating(function ($model) {
            $model->id_sop = 'SOP' . now()->format('YmdHis');
        });

        // Log when SOP is created
        static::created(function ($model) {
            $model->logHistory(
                'created',
                "SOP \"{$model->sop_name}\" telah dibuat",
                null,
                $model->only(static::$trackableFields),
                null
            );
        });

        // Log when SOP is updated
        static::updating(function ($model) {
            $original = $model->getOriginal();
            $changedFields = [];
            $oldValues = [];
            $newValues = [];

            foreach (static::$trackableFields as $field) {
                if ($model->isDirty($field)) {
                    $changedFields[] = $field;
                    $oldValues[$field] = $original[$field] ?? null;
                    $newValues[$field] = $model->$field;
                }
            }

            if (!empty($changedFields)) {
                // Store changes temporarily in static array (not saved to database)
                static::$pendingHistoryLogs[$model->id] = [
                    'changedFields' => $changedFields,
                    'oldValues' => $oldValues,
                    'newValues' => $newValues,
                ];
            }
        });

        static::updated(function ($model) {
            if (isset(static::$pendingHistoryLogs[$model->id])) {
                $log = static::$pendingHistoryLogs[$model->id];
                
                // Determine action type
                $action = in_array('status', $log['changedFields']) ? 'status_changed' : 'updated';
                
                // Build description
                $fieldLabels = [
                    'sop_name' => 'Nama SOP',
                    'sk_number' => 'Nomor SK',
                    'id_unit' => 'Unit',
                    'type_sop' => 'Tipe SOP',
                    'status' => 'Status',
                    'file_path' => 'File',
                    'approval_date' => 'Tanggal Pengesahan',
                    'start_date' => 'Tanggal Berlaku',
                    'expired' => 'Tanggal Kadaluarsa',
                    'desc' => 'Deskripsi',
                    'days_left' => 'Sisa Hari',
                    'feedback' => 'Feedback',
                ];
                
                $changedLabels = array_map(fn($f) => $fieldLabels[$f] ?? $f, $log['changedFields']);
                $description = "SOP \"{$model->sop_name}\" diperbarui: " . implode(', ', $changedLabels);
                
                $model->logHistory(
                    $action,
                    $description,
                    $log['oldValues'],
                    $log['newValues'],
                    $log['changedFields']
                );
                
                // Clean up
                unset(static::$pendingHistoryLogs[$model->id]);
            }
        });

        // Log when SOP is deleted
        static::deleted(function ($model) {
            $model->logHistory(
                'deleted',
                "SOP \"{$model->sop_name}\" telah dihapus",
                null,
                null,
                null
            );
        });

        // Log when SOP is restored
        static::restored(function ($model) {
            $model->logHistory(
                'restored',
                "SOP \"{$model->sop_name}\" telah dipulihkan",
                null,
                null,
                null
            );
        });
    }
}

