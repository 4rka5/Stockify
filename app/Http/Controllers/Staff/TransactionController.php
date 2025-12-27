<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\StockTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        // Build query for user's transactions (assigned OR created by user)
        $query = StockTransaction::with(['product', 'product.category', 'user', 'assignedStaff'])
            ->where(function($q) {
                $q->where('assigned_to', Auth::id())
                  ->orWhere('user_id', Auth::id());
            })
            ->orderBy('created_at', 'desc');

        // Date filter
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay()
            ]);
        }

        // Type filter
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('product', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $transactions = $query->paginate(15);

        // Statistics for current user (assigned OR created)
        $totalTransactions = StockTransaction::where(function($q) {
            $q->where('assigned_to', Auth::id())
              ->orWhere('user_id', Auth::id());
        })->count();

        $totalIn = StockTransaction::where(function($q) {
            $q->where('assigned_to', Auth::id())
              ->orWhere('user_id', Auth::id());
        })
            ->where('type', 'in')
            ->where('status', 'diterima')
            ->count();

        $totalOut = StockTransaction::where(function($q) {
            $q->where('assigned_to', Auth::id())
              ->orWhere('user_id', Auth::id());
        })
            ->where('type', 'out')
            ->where('status', 'dikeluarkan')
            ->count();

        $pending = StockTransaction::where(function($q) {
            $q->where('assigned_to', Auth::id())
              ->orWhere('user_id', Auth::id());
        })
            ->where('status', 'pending')
            ->count();

        return view('staff.transactions.index', compact('transactions', 'totalTransactions', 'totalIn', 'totalOut', 'pending'));
    }

    public function show($id)
    {
        $transaction = StockTransaction::with(['product', 'product.category', 'user', 'assignedStaff'])
            ->where(function($q) {
                $q->where('assigned_to', Auth::id())
                  ->orWhere('user_id', Auth::id());
            })
            ->findOrFail($id);

        return view('staff.transactions.show', compact('transaction'));
    }
}
