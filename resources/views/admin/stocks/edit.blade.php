@extends('layouts.admin')

@section('title', 'Edit Transaksi Stok')
@section('page-title', 'Edit Transaksi Stok')
@section('breadcrumb', 'Home / Transaksi Stok / Edit')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.stock-transactions.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 transition">
        <i class="fas fa-arrow-left mr-2"></i>
        Kembali ke Daftar Transaksi
    </a>
</div>

<div class="bg-white rounded-lg shadow-md p-6">
    <div class="mb-6">
        <h3 class="text-xl font-semibold text-gray-800">Form Edit Transaksi Stok</h3>
        <p class="text-sm text-gray-600 mt-1">Perbarui informasi transaksi stok</p>
    </div>

    <form action="{{ route('admin.stock-transactions.update', $transaction->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Left Column -->
            <div class="space-y-6">
                <!-- Product -->
                <div>
                    <label for="product_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Produk <span class="text-red-500">*</span>
                    </label>
                    <select id="product_id" name="product_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('product_id') border-red-500 @enderror"
                        required>
                        <option value="">Pilih Produk</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ old('product_id', $transaction->product_id) == $product->id ? 'selected' : '' }}>
                                {{ $product->name }} (Stok: {{ $product->current_stock }})
                            </option>
                        @endforeach
                    </select>
                    @error('product_id')
                        <p class="mt-1 text-sm text-red-600">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Type -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                        Tipe Transaksi <span class="text-red-500">*</span>
                    </label>
                    <select id="type" name="type"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('type') border-red-500 @enderror"
                        required>
                        <option value="">Pilih Tipe</option>
                        <option value="in" {{ old('type', $transaction->type) == 'in' ? 'selected' : '' }}>Stok Masuk</option>
                        <option value="out" {{ old('type', $transaction->type) == 'out' ? 'selected' : '' }}>Stok Keluar</option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Quantity -->
                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">
                        Jumlah <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="quantity" name="quantity" value="{{ old('quantity', $transaction->quantity) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('quantity') border-red-500 @enderror"
                        placeholder="Masukkan jumlah"
                        min="1"
                        required>
                    @error('quantity')
                        <p class="mt-1 text-sm text-red-600">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
                <!-- Date -->
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Transaksi <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" id="date" name="date"
                        value="{{ old('date', \Carbon\Carbon::parse($transaction->date)->format('Y-m-d\TH:i')) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('date') border-red-500 @enderror"
                        required>
                    @error('date')
                        <p class="mt-1 text-sm text-red-600">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select id="status" name="status"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('status') border-red-500 @enderror"
                        required>
                        <option value="pending" {{ old('status', $transaction->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="diterima" {{ old('status', $transaction->status) == 'diterima' ? 'selected' : '' }}>Diterima</option>
                        <option value="dikeluarkan" {{ old('status', $transaction->status) == 'dikeluarkan' ? 'selected' : '' }}>Dikeluarkan</option>
                        <option value="ditolak" {{ old('status', $transaction->status) == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                        Catatan
                    </label>
                    <textarea id="notes" name="notes" rows="4"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('notes') border-red-500 @enderror"
                        placeholder="Catatan transaksi (opsional)">{{ old('notes', $transaction->notes) }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200 mt-6">
            <a href="{{ route('admin.stock-transactions.index') }}" class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition">
                <i class="fas fa-times mr-2"></i>
                Batal
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                <i class="fas fa-save mr-2"></i>
                Update Transaksi
            </button>
        </div>
    </form>
</div>

<!-- Warning Card -->
<div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
    <div class="flex">
        <div class="flex-shrink-0">
            <i class="fas fa-exclamation-triangle text-yellow-400 text-xl"></i>
        </div>
        <div class="ml-3">
            <h3 class="text-sm font-medium text-yellow-800">Peringatan</h3>
            <div class="mt-2 text-sm text-yellow-700">
                <p>Perubahan pada transaksi ini akan mempengaruhi stok produk. Pastikan data yang diubah sudah benar.</p>
            </div>
        </div>
    </div>
</div>
@endsection
