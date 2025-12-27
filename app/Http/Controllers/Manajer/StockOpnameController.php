<?php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Models\StockOpname;
use App\Services\StockTransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockOpnameController extends Controller
{
    protected $stockTransactionService;

    public function __construct(StockTransactionService $stockTransactionService)
    {
        $this->stockTransactionService = $stockTransactionService;
    }

    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');

        $query = StockOpname::with(['product', 'product.category', 'user']);

        if ($status) {
            $query->where('status', $status);
        }

        $opnames = $query->latest('checked_at')->paginate(15);

        // Statistics
        $pendingCount = StockOpname::where('status', 'pending')->count();
        $approvedCount = StockOpname::where('status', 'approved')->count();
        $rejectedCount = StockOpname::where('status', 'rejected')->count();

        return view('manajer.stock-opname.index', compact('opnames', 'pendingCount', 'approvedCount', 'rejectedCount'));
    }

    public function approve($id)
    {
        try {
            DB::beginTransaction();

            $opname = StockOpname::with('product')->findOrFail($id);

            if ($opname->status !== 'pending') {
                return back()->with('error', 'Stock opname sudah diproses sebelumnya');
            }

            // Create adjustment transaction
            $transactionData = [
                'product_id' => $opname->product_id,
                'type' => $opname->difference > 0 ? 'in' : 'out',
                'quantity' => abs($opname->difference),
                'date' => now(),
                'status' => $opname->difference > 0 ? 'diterima' : 'dikeluarkan',
                'user_id' => Auth::id(),
                'notes' => 'Stock Opname oleh ' . $opname->user->name . ': ' . ($opname->notes ?? 'Penyesuaian stok fisik vs sistem')
            ];

            $this->stockTransactionService->createTransaction($transactionData);

            // Update opname status
            $opname->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            DB::commit();
            return back()->with('success', 'Stock opname berhasil disetujui dan stok telah disesuaikan');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyetujui stock opname: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, $id)
    {
        $validated = $request->validate([
            'reason' => 'nullable|string|max:500'
        ]);

        try {
            $opname = StockOpname::findOrFail($id);

            if ($opname->status !== 'pending') {
                return back()->with('error', 'Stock opname sudah diproses sebelumnya');
            }

            $notes = $opname->notes;
            if ($validated['reason']) {
                $notes = $notes ? $notes . ' | Ditolak: ' . $validated['reason'] : 'Ditolak: ' . $validated['reason'];
            }

            $opname->update([
                'status' => 'rejected',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'notes' => $notes,
            ]);

            return back()->with('success', 'Stock opname berhasil ditolak');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menolak stock opname: ' . $e->getMessage());
        }
    }
}
