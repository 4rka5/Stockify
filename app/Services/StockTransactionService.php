<?php

namespace App\Services;

use App\Repositories\StockTransactionRepository;
use App\Repositories\ProductRepository;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;
use Exception;

class StockTransactionService
{
    protected $stockTransactionRepository;
    protected $productRepository;
    protected $notificationService;

    public function __construct(
        StockTransactionRepository $stockTransactionRepository,
        ProductRepository $productRepository,
        NotificationService $notificationService
    ) {
        $this->stockTransactionRepository = $stockTransactionRepository;
        $this->productRepository = $productRepository;
        $this->notificationService = $notificationService;
    }

    public function getAllTransactions()
    {
        return $this->stockTransactionRepository->getAllWithRelations();
    }

    public function paginateTransactions($perPage = 15)
    {
        return $this->stockTransactionRepository->paginateWithRelations($perPage);
    }

    public function getTransactionById($id)
    {
        return $this->stockTransactionRepository->findWithRelations($id);
    }

    public function createTransaction(array $data)
    {
        try {
            DB::beginTransaction();

            // Set default date if not provided
            if (!isset($data['date'])) {
                $data['date'] = now();
            }

            $transaction = $this->stockTransactionRepository->create($data);

            DB::commit();
            return $transaction;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Failed to create transaction: ' . $e->getMessage());
        }
    }

    public function updateTransaction($id, array $data)
    {
        try {
            DB::beginTransaction();

            $transaction = $this->stockTransactionRepository->update($id, $data);

            DB::commit();
            return $transaction;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Failed to update transaction: ' . $e->getMessage());
        }
    }

    public function deleteTransaction($id)
    {
        try {
            DB::beginTransaction();

            $this->stockTransactionRepository->delete($id);

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Failed to delete transaction: ' . $e->getMessage());
        }
    }

    public function approveTransaction($id)
    {
        try {
            DB::beginTransaction();

            $transaction = $this->stockTransactionRepository->findOrFail($id);
            $product = $this->productRepository->findOrFail($transaction->product_id);

            // Determine status based on transaction type
            if ($transaction->type === 'in') {
                $status = 'diterima';
            } elseif ($transaction->type === 'out') {
                // Validasi stok untuk transaksi keluar
                $currentStock = $product->current_stock;

                if ($currentStock < $transaction->quantity) {
                    throw new Exception("Stok tidak mencukupi! Stok tersedia: {$currentStock} unit, diminta: {$transaction->quantity} unit");
                }

                $status = 'dikeluarkan';
            } else {
                // Fallback untuk type lama (masuk/keluar)
                $status = $transaction->type === 'masuk' ? 'diterima' : 'dikeluarkan';
            }

            $this->stockTransactionRepository->update($id, ['status' => $status]);

            // Send notification to staff who created the transaction
            if ($transaction->user_id) {
                $typeText = $transaction->type === 'in' ? 'barang masuk' : 'barang keluar';
                $this->notificationService->create(
                    $transaction->user_id,
                    'Transaksi Disetujui',
                    'Transaksi ' . $typeText . ' untuk produk ' . $product->name . ' sebanyak ' . $transaction->quantity . ' unit telah disetujui oleh Manajer',
                    'success',
                    route('staff.transactions.index')
                );
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Failed to approve transaction: ' . $e->getMessage());
        }
    }

    public function rejectTransaction($id, $reason = null)
    {
        try {
            DB::beginTransaction();

            $transaction = $this->stockTransactionRepository->findOrFail($id);
            $product = $this->productRepository->findOrFail($transaction->product_id);

            $updateData = ['status' => 'ditolak'];

            // Add reason to notes if provided
            if ($reason) {
                $existingNotes = $transaction->notes;
                $updateData['notes'] = $existingNotes
                    ? $existingNotes . ' | Ditolak: ' . $reason
                    : 'Ditolak: ' . $reason;
            }

            $this->stockTransactionRepository->update($id, $updateData);

            // Send notification to staff who created the transaction
            if ($transaction->user_id) {
                $typeText = $transaction->type === 'in' ? 'barang masuk' : 'barang keluar';
                $rejectMessage = 'Transaksi ' . $typeText . ' untuk produk ' . $product->name . ' sebanyak ' . $transaction->quantity . ' unit telah ditolak oleh Manajer';
                if ($reason) {
                    $rejectMessage .= '. Alasan: ' . $reason;
                }
                $this->notificationService->create(
                    $transaction->user_id,
                    'Transaksi Ditolak',
                    $rejectMessage,
                    'danger',
                    route('staff.transactions.index')
                );
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Failed to reject transaction: ' . $e->getMessage());
        }
    }

    public function getIncomingTransactions()
    {
        return $this->stockTransactionRepository->getIncomingTransactions();
    }

    public function getOutgoingTransactions()
    {
        return $this->stockTransactionRepository->getOutgoingTransactions();
    }

    public function getPendingTransactions()
    {
        return $this->stockTransactionRepository->getPendingTransactions();
    }

    public function getTransactionsByProduct($productId)
    {
        return $this->stockTransactionRepository->getTransactionsByProduct($productId);
    }

    public function getTransactionsByUser($userId)
    {
        return $this->stockTransactionRepository->getTransactionsByUser($userId);
    }

    public function getTransactionsByDateRange($startDate, $endDate)
    {
        return $this->stockTransactionRepository->getTransactionsByDateRange($startDate, $endDate);
    }

    public function getTodayTransactions()
    {
        return $this->stockTransactionRepository->getTodayTransactions();
    }

    public function getMonthlyTransactionsByType($type)
    {
        $startDate = now()->startOfMonth();
        $endDate = now()->endOfMonth();

        return $this->stockTransactionRepository->getTransactionsByTypeAndDateRange($type, $startDate, $endDate);
    }

    public function getRecentActivities($limit = 10)
    {
        return $this->stockTransactionRepository->getRecentTransactions($limit);
    }

    public function getProductCurrentStock($productId)
    {
        $product = $this->productRepository->findOrFail($productId);
        return $product->current_stock;
    }
}
