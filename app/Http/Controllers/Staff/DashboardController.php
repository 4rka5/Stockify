<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\StockTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // Get transactions assigned to current staff
        $pendingIncoming = StockTransaction::with(['product', 'user'])
            ->where('type', 'in')
            ->where('status', 'pending')
            ->where('assigned_to', $userId)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $pendingOutgoing = StockTransaction::with(['product', 'user'])
            ->where('type', 'out')
            ->where('status', 'pending')
            ->where('assigned_to', $userId)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $myTransactions = StockTransaction::with(['product'])
            ->where('assigned_to', $userId)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('staff.dashboard', compact(
            'pendingIncoming',
            'pendingOutgoing',
            'myTransactions'
        ));
    }
}
