<?php

namespace App\Http\Controllers;

use App\Services\ActivityLogService;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    protected $activityLogService;

    public function __construct(ActivityLogService $activityLogService)
    {
        $this->middleware('restrict.to.role:super_admin,ceo');
        $this->activityLogService = $activityLogService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['action', 'model_type', 'user_id', 'search', 'from', 'to']);
        $logs = $this->activityLogService->getActivityLogsQuery($filters)->paginate(10);
        $users = User::whereNull('deleted_at')->get(['id', 'first_name', 'last_name']);
        $modelTypes = [
            'App\\Models\\Tank' => 'Tank',
            'App\\Models\\User' => 'User',
            'App\\Models\\Vessel' => 'Vessel',
            'App\\Models\\Transaction' => 'Transaction',
            'App\\Models\\Shipment' => 'Shipment',
            'App\\Models\\Delivery' => 'Delivery',
            'App\\Models\\Product' => 'Product',
        ];
        $actions = [
            'tank.created',
            'tank.updated',
            'tank.reset',
            'tank.deleted',
            'user.created',
            'user.updated',
            'user.reactivated',
            'user.deleted',
            'client.created',
            'client.updated',
            'client.reactivated',
            'client.deleted',
            'profile.updated',
            'vessel.created',
            'vessel.updated',
            'vessel.deleted',
            'transaction.created',
            'shipment.created',
            'delivery.created',
            'product.created',
            'product.updated',
            'product.deleted',
        ];

        return view('activity-logs.index', compact('logs', 'filters', 'users', 'modelTypes', 'actions'));
    }

    public function show($id)
    {
        $log = $this->activityLogService->getActivityLogsQuery()->findOrFail($id);
        return response()->json([
            'id' => $log->id,
            'user' => $log->user ? $log->user->first_name . ' ' . $log->user->last_name : 'N/A',
            'action' => $log->action,
            'description' => $log->description,
            'model_type' => str_replace('App\\Models\\', '', $log->model_type),
            'model_id' => $log->model_id,
            'details' => json_decode($log->details, true) ?: ['old' => [], 'new' => []],
            'created_at' => $log->created_at->format('Y-m-d H:i:s'),
        ]);
    }
}
