<?php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Models\StockTransaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        // Build query for all transactions
        $query = StockTransaction::with(['product', 'product.category', 'user', 'assignedStaff']);

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
            $query->where(function($q) use ($search) {
                $q->whereHas('product', function($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%")
                       ->orWhere('sku', 'like', "%{$search}%");
                })
                ->orWhereHas('user', function($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%");
                });
            });
        }

        $transactions = $query->latest('created_at')->paginate(15);

        // Statistics
        $totalTransactions = StockTransaction::count();
        $totalIn = StockTransaction::where('type', 'in')
            ->whereIn('status', ['diterima'])
            ->count();
        $totalOut = StockTransaction::where('type', 'out')
            ->whereIn('status', ['dikeluarkan'])
            ->count();
        $pending = StockTransaction::where('status', 'pending')->count();

        return view('manajer.transactions.index', compact(
            'transactions',
            'totalTransactions',
            'totalIn',
            'totalOut',
            'pending'
        ));
    }

    public function show($id)
    {
        $transaction = StockTransaction::with([
            'product',
            'product.category',
            'user',
            'assignedStaff',
            'supplier'
        ])->findOrFail($id);

        return view('manajer.transactions.show', compact('transaction'));
    }
}
