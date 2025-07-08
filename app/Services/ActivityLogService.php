<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ActivityLogService
{
    /**
     * Log an activity for any model operation.
     *
     * @param User $user
     * @param string $action
     * @param string $description
     * @param Model $model
     * @param array $oldData
     * @param array $newData
     * @return void
     */
    public function logActivity(User $user, string $action, string $description, Model $model, array $oldData = [], array $newData = [])
    {
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => $action,
            'description' => $description,
            'model_type' => get_class($model),
            'model_id' => $model->getKey(),
            'details' => json_encode(['old' => $oldData, 'new' => $newData]),
        ]);
    }

    /**
     * Get filtered activity logs query.
     *
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getActivityLogsQuery(array $filters = [])
    {
        $query = ActivityLog::with('user')->orderBy('created_at', 'desc');

        if (!empty($filters['action'])) {
            $query->where('action', $filters['action']);
        }
        if (!empty($filters['model_type'])) {
            $query->where('model_type', $filters['model_type']);
        }
        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }
        if (!empty($filters['search'])) {
            $query->where('description', 'like', '%' . $filters['search'] . '%');
        }
        if (!empty($filters['from'])) {
            $query->whereDate('created_at', '>=', $filters['from']);
        }
        if (!empty($filters['to'])) {
            $query->whereDate('created_at', '<=', $filters['to']);
        }

        return $query;
    }
}