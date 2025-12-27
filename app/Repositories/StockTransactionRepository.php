<?php

namespace App\Repositories;

use App\Models\StockTransaction;

class StockTransactionRepository extends BaseRepository
{
    public function __construct(StockTransaction $model)
    {
        parent::__construct($model);
    }

    public function getAllWithRelations()
    {
        return $this->model->with(['product', 'user'])->orderBy('date', 'desc')->get();
    }

    public function paginateWithRelations($perPage = 15)
    {
        return $this->model->with(['product', 'user'])->orderBy('date', 'desc')->paginate($perPage);
    }

    public function findWithRelations($id)
    {
        return $this->model->with(['product', 'user'])->findOrFail($id);
    }

    public function getIncomingTransactions()
    {
        return $this->model->with(['product', 'user'])
            ->incoming()
            ->orderBy('date', 'desc')
            ->get();
    }

    public function getOutgoingTransactions()
    {
        return $this->model->with(['product', 'user'])
            ->outgoing()
            ->orderBy('date', 'desc')
            ->get();
    }

    public function getPendingTransactions()
    {
        return $this->model->with(['product', 'user'])
            ->pending()
            ->orderBy('date', 'desc')
            ->get();
    }

    public function getTransactionsByProduct($productId)
    {
        return $this->model->with(['product', 'user'])
            ->where('product_id', $productId)
            ->orderBy('date', 'desc')
            ->get();
    }

    public function getTransactionsByUser($userId)
    {
        return $this->model->with(['product', 'user'])
            ->where('user_id', $userId)
            ->orderBy('date', 'desc')
            ->get();
    }

    public function getTransactionsByDateRange($startDate, $endDate)
    {
        return $this->model->with(['product', 'user'])
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->get();
    }

    public function getTodayTransactions()
    {
        return $this->model->with(['product', 'user'])
            ->whereDate('created_at', today())
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getTransactionsByTypeAndDateRange($type, $startDate, $endDate)
    {
        return $this->model->with(['product', 'user'])
            ->where('type', $type)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->get();
    }

    public function getRecentTransactions($limit = 10)
    {
        return $this->model->with(['product', 'user'])
            ->orderBy('date', 'desc')
            ->limit($limit)
            ->get();
    }
}
