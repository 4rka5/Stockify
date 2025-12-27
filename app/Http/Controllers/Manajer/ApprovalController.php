<?php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Services\StockTransactionService;
use App\Models\StockTransaction;
use App\Models\StockOpname;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApprovalController extends Controller
{
    protected $stockTransactionService;

    public function __construct(StockTransactionService $stockTransactionService)
    {
        $this->stockTransactionService = $stockTransactionService;
    }

    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');
        $type = $request->get('type'); // 'in', 'out', or 'opname'

        // Get stock transactions
        $transactionQuery = StockTransaction::with(['product', 'user', 'assignedStaff']);

        // Only show transactions created by staff (not by manajer themselves)
        $transactionQuery->whereHas('user', function($q) {
            $q->where('role', 'staff gudang');
        });

        // Filter by status
        if ($status) {
            $transactionQuery->where('status', $status);
        }

        // Filter by type
        if ($type && $type !== 'opname') {
            $transactionQuery->where('type', $type);
        }

        $transactions = $type === 'opname' ? collect() : $transactionQuery->latest('created_at')->get();

        // Get stock opnames
        $opnameQuery = StockOpname::with(['product', 'user']);

        // Only show opnames created by staff
        $opnameQuery->whereHas('user', function($q) {
            $q->where('role', 'staff gudang');
        });

        // Filter by status
        if ($status) {
            $opnameQuery->where('status', $status);
        }

        $opnames = $type === 'opname' || !$type ? $opnameQuery->latest('checked_at')->get() : collect();

        \Log::info('Approval Index Query', [
            'transactions_count' => $transactions->count(),
            'opnames_count' => $opnames->count(),
            'status_filter' => $status,
            'type_filter' => $type
        ]);

        // Combine and sort
        $allItems = $transactions->merge($opnames)->sortByDesc(function($item) {
            return $item instanceof StockOpname ? $item->checked_at : $item->created_at;
        })->values();

        // Paginate manually
        $page = $request->get('page', 1);
        $perPage = 15;
        $items = $allItems->forPage($page, $perPage);
        $total = $allItems->count();

        // Statistics - only count staff transactions
        $pendingTransactions = StockTransaction::whereHas('user', function($q) {
            $q->where('role', 'staff gudang');
        })->where('status', 'pending')->count();

        $pendingOpnames = StockOpname::where('status', 'pending')->count();
        $pendingCount = $pendingTransactions + $pendingOpnames;

        $approvedCount = StockTransaction::whereHas('user', function($q) {
            $q->where('role', 'staff gudang');
        })->whereIn('status', ['diterima', 'dikeluarkan'])->count() +
        StockOpname::where('status', 'approved')->count();

        $rejectedCount = StockTransaction::whereHas('user', function($q) {
            $q->where('role', 'staff gudang');
        })->where('status', 'ditolak')->count() +
        StockOpname::where('status', 'rejected')->count();

        return view('manajer.approval.index', compact(
            'items',
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'total',
            'page',
            'perPage'
        ));
    }

    public function approve($id)
    {
        try {
            $this->stockTransactionService->approveTransaction($id);
            return back()->with('success', 'Transaksi berhasil disetujui');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyetujui transaksi: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, $id)
    {
        $validated = $request->validate([
            'reason' => 'nullable|string|max:500'
        ]);

        try {
            $this->stockTransactionService->rejectTransaction($id, $validated['reason'] ?? null);
            return back()->with('success', 'Transaksi berhasil ditolak');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menolak transaksi: ' . $e->getMessage());
        }
    }

    public function approveOpname($id)
    {
        try {
            DB::beginTransaction();

            $opname = StockOpname::with('product')->findOrFail($id);

            if ($opname->status !== 'pending') {
                return back()->with('error', 'Stock opname sudah diproses sebelumnya');
            }

            // Update opname status
            $opname->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            // Create transaction if there's a difference
            if ($opname->difference != 0) {
                StockTransaction::create([
                    'product_id' => $opname->product_id,
                    'user_id' => $opname->user_id,
                    'type' => $opname->difference > 0 ? 'in' : 'out',
                    'quantity' => abs($opname->difference),
                    'date' => now(),
                    'status' => 'diterima',
                    'notes' => 'Stock opname adjustment: ' . $opname->notes,
                ]);
            }

            DB::commit();
            return back()->with('success', 'Stock opname berhasil disetujui');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyetujui stock opname: ' . $e->getMessage());
        }
    }

    public function rejectOpname(Request $request, $id)
    {
        $validated = $request->validate([
            'reason' => 'nullable|string|max:500'
        ]);

        try {
            $opname = StockOpname::findOrFail($id);

            if ($opname->status !== 'pending') {
                return back()->with('error', 'Stock opname sudah diproses sebelumnya');
            }

            $opname->update([
                'status' => 'rejected',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'notes' => ($opname->notes ? $opname->notes . ' | ' : '') . 'Ditolak: ' . ($validated['reason'] ?? 'Tidak ada alasan'),
            ]);

            return back()->with('success', 'Stock opname berhasil ditolak');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menolak stock opname: ' . $e->getMessage());
        }
    }
}
