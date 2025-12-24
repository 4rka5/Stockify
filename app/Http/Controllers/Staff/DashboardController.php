<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Services\StockTransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected $stockTransactionService;

    public function __construct(StockTransactionService $stockTransactionService)
    {
        $this->stockTransactionService = $stockTransactionService;
    }

    public function index()
    {
        $pendingIncoming = $this->stockTransactionService->getPendingTransactions()
            ->where('type', 'in');
        $pendingOutgoing = $this->stockTransactionService->getPendingTransactions()
            ->where('type', 'out');
        $myTransactions = $this->stockTransactionService->getTransactionsByUser(Auth::id())
            ->take(10);

        return view('staff.dashboard', compact(
            'pendingIncoming',
            'pendingOutgoing',
            'myTransactions'
        ));
    }
}
