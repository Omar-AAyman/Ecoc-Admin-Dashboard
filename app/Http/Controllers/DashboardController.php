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
        $tanks = $this->tankService->getTanks($request->user());
        return view('dashboard.index', compact('tanks'));
    }
}
