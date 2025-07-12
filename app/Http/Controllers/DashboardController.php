<?php

namespace App\Http\Controllers;

use App\Services\TankService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $tankService;

    public function __construct(TankService $tankService)
    {
        $this->middleware('auth');
        $this->tankService = $tankService;
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $stats = $this->tankService->getDashboardStats($user);
        return view('dashboard.index', [
            'totalTanks' => $stats['totalTanks'],
            'avgCapacityUtilization' => $stats['avgCapacityUtilization'],
            'activeRentals' => $stats['activeRentals'],
            'completedRentals' => $stats['completedRentals'],
            'totalDischarge' => $stats['totalDischarge'],
            'totalLoad' => $stats['totalLoad'],
            'tanks' => $stats['tanks'],
            'performanceTrends' => $stats['performanceTrends'],
        ]);
    }
}
