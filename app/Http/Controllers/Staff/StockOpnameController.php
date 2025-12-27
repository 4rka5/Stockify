<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\StockOpname;
use App\Models\Product;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockOpnameController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        $search = $request->get('search');
        $category = $request->get('category');

        $query = Product::with(['category', 'stockTransactions']);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($category) {
            $query->where('category_id', $category);
        }

        $products = $query->orderBy('name')->paginate(15);
        $categories = \App\Models\Category::orderBy('name')->get();

        // Get recent opnames by this staff
        $recentOpnames = StockOpname::with(['product', 'approver'])
            ->where('user_id', Auth::id())
            ->whereNotNull('checked_at')
            ->latest('checked_at')
            ->take(5)
            ->get();

        // Get assigned tasks (opnames with status 'assigned')
        $assignedTasks = StockOpname::with(['product', 'assignedBy'])
            ->where('user_id', Auth::id())
            ->where('status', 'assigned')
            ->latest('assigned_at')
            ->get();

        return view('staff.stock-opname.index', compact('products', 'categories', 'recentOpnames', 'assignedTasks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'opname_data' => 'required|array',
            'opname_data.*.product_id' => 'required|exists:products,id',
            'opname_data.*.physical_stock' => 'required|integer|min:0',
            'opname_data.*.notes' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $createdCount = 0;
            $opnameIds = [];

            foreach ($validated['opname_data'] as $data) {
                $product = Product::with('stockTransactions')->findOrFail($data['product_id']);
                $systemStock = $product->current_stock;
                $physicalStock = $data['physical_stock'];
                $difference = $physicalStock - $systemStock;

                // Check if this is an assigned task
                $existingTask = StockOpname::where('product_id', $data['product_id'])
                    ->where('user_id', Auth::id())
                    ->where('status', 'assigned')
                    ->first();

                if ($existingTask) {
                    // Update existing assigned task
                    $existingTask->update([
                        'system_stock' => $systemStock,
                        'physical_stock' => $physicalStock,
                        'difference' => $difference,
                        'notes' => $data['notes'] ?? $existingTask->notes,
                        'checked_at' => now(),
                        'status' => 'pending',
                    ]);
                    $opnameIds[] = $existingTask->id;
                } else {
                    // Create new opname
                    $opname = StockOpname::create([
                        'product_id' => $data['product_id'],
                        'user_id' => Auth::id(),
                        'system_stock' => $systemStock,
                        'physical_stock' => $physicalStock,
                        'difference' => $difference,
                        'notes' => $data['notes'] ?? null,
                        'checked_at' => now(),
                        'status' => 'pending',
                    ]);
                    $opnameIds[] = $opname->id;
                }

                $createdCount++;
            }

            DB::commit();

            if ($createdCount > 0) {
                // Kirim notifikasi ke semua manajer
                $managers = User::where('role', 'manajer')->get();
                foreach ($managers as $manager) {
                    $this->notificationService->createNotification(
                        $manager->id,
                        'Stock Opname Baru',
                        "Staff " . auth()->user()->name . " telah mengirim {$createdCount} produk hasil stock opname untuk disetujui.",
                        'manajer.approval.index'
                    );
                }

                \Log::info('Stock Opname Created', [
                    'staff_id' => Auth::id(),
                    'count' => $createdCount,
                    'opname_ids' => $opnameIds
                ]);

                return back()->with('success', "✅ Berhasil! {$createdCount} produk telah dikirim ke manajer untuk disetujui.");
            } else {
                return back()->with('info', 'Tidak ada produk yang dipilih untuk dicek.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan stock opname: ' . $e->getMessage());
        }
    }

    public function history()
    {
        $opnames = StockOpname::with(['product', 'product.category', 'approver'])
            ->where('user_id', Auth::id())
            ->latest('checked_at')
            ->paginate(15);

        return view('staff.stock-opname.history', compact('opnames'));
    }
}
