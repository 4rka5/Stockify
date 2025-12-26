<?php

namespace App\Services;

use App\Repositories\StockTransactionRepository;
use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\DB;
use Exception;

class StockTransactionService
{
    protected $stockTransactionRepository;
    protected $productRepository;

    public function __construct(
        StockTransactionRepository $stockTransactionRepository,
        ProductRepository $productRepository
    ) {
        $this->stockTransactionRepository = $stockTransactionRepository;
        $this->productRepository = $productRepository;
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
            
            // Validasi stok untuk transaksi keluar
            if ($transaction->type === 'keluar') {
                $product = $this->productRepository->findOrFail($transaction->product_id);
                $currentStock = $product->current_stock;
                
                if ($currentStock < $transaction->quantity) {
                    throw new Exception("Stok tidak mencukupi! Stok tersedia: {$currentStock} unit, diminta: {$transaction->quantity} unit");
                }
            }

            $status = $transaction->type === 'masuk' ? 'diterima' : 'dikeluarkan';

            $this->stockTransactionRepository->update($id, ['status' => $status]);

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Failed to approve transaction: ' . $e->getMessage());
        }
    }

    public function rejectTransaction($id)
    {
        try {
            DB::beginTransaction();

            $this->stockTransactionRepository->update($id, ['status' => 'ditolak']);

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
