@extends('layouts.admin')

@section('title', 'Import Produk')
@section('page-title', 'Import Produk')
@section('breadcrumb', 'Home / Produk / Import')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.products.index') }}" class="text-blue-600 hover:text-blue-700">
        <i class="fas fa-arrow-left mr-2"></i>Kembali ke Daftar Produk
    </a>
</div>

<!-- Instructions -->
<div class="bg-blue-50 border-l-4 border-blue-500 p-6 mb-6 rounded-lg">
    <div class="flex">
        <div class="flex-shrink-0">
            <i class="fas fa-info-circle text-blue-600 text-2xl"></i>
        </div>
        <div class="ml-3">
            <h3 class="text-lg font-semibold text-blue-800 mb-2">Panduan Import Produk</h3>
            <ol class="list-decimal list-inside space-y-2 text-sm text-blue-700">
                <li>Download template CSV dengan klik tombol "Download Template" di bawah</li>
                <li>Isi data produk sesuai format yang tersedia di template</li>
                <li>Pastikan nama <strong>Kategori</strong> dan <strong>Supplier</strong> sudah terdaftar di sistem</li>
                <li>SKU harus unik (tidak boleh duplikat)</li>
                <li>Upload file yang sudah diisi (format: .csv)</li>
                <li>Produk yang diimpor akan langsung disetujui (status: approved)</li>
            </ol>
        </div>
    </div>
</div>

<!-- Download Template -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">
        <i class="fas fa-download mr-2"></i>Download Template
    </h3>
    <p class="text-gray-600 mb-4">Download template CSV untuk memudahkan proses import produk.</p>
    <a href="{{ route('admin.products.download-template') }}"
       class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
        <i class="fas fa-file-csv mr-2"></i>
        Download Template CSV
    </a>
</div>

<!-- Upload Form -->
<div class="bg-white rounded-lg shadow-md p-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">
        <i class="fas fa-upload mr-2"></i>Upload File Import
    </h3>

    <form action="{{ route('admin.products.import') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                File CSV <span class="text-red-600">*</span>
            </label>
            <input type="file"
                   name="file"
                   accept=".csv,.txt"
                   required
                   class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent p-2">
            <p class="mt-1 text-sm text-gray-500">
                Format yang didukung: .csv (max 2MB)
            </p>
            @error('file')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex gap-3">
            <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                <i class="fas fa-upload mr-2"></i>
                Import Produk
            </button>
            <a href="{{ route('admin.products.index') }}"
               class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition">
                <i class="fas fa-times mr-2"></i>
                Batal
            </a>
        </div>
    </form>
</div>

<!-- Import Errors (if any) -->
@if(session('import_errors'))
<div class="bg-red-50 border-l-4 border-red-500 p-6 mt-6 rounded-lg">
    <div class="flex">
        <div class="flex-shrink-0">
            <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
        </div>
        <div class="ml-3 flex-1">
            <h3 class="text-lg font-semibold text-red-800 mb-3">Error Import</h3>
            <div class="max-h-96 overflow-y-auto">
                @foreach(session('import_errors') as $error)
                <div class="mb-3 p-3 bg-white rounded border border-red-200">
                    <p class="font-semibold text-red-700">Baris {{ $error['row'] }}:</p>
                    <ul class="list-disc list-inside text-sm text-red-600 mt-1">
                        @foreach($error['errors'] as $errorMsg)
                            <li>{{ $errorMsg }}</li>
                        @endforeach
                    </ul>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif

<!-- Format Data Template -->
<div class="bg-white rounded-lg shadow-md p-6 mt-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">
        <i class="fas fa-table mr-2"></i>Format Data Template
    </h3>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kolom</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contoh</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Wajib</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr>
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">sku</td>
                    <td class="px-6 py-4 text-sm text-gray-600">Kode unik produk</td>
                    <td class="px-6 py-4 text-sm text-gray-600">PROD001</td>
                    <td class="px-6 py-4 text-sm text-green-600"><i class="fas fa-check"></i> Ya</td>
                </tr>
                <tr class="bg-gray-50">
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">name</td>
                    <td class="px-6 py-4 text-sm text-gray-600">Nama produk</td>
                    <td class="px-6 py-4 text-sm text-gray-600">Laptop Dell XPS 13</td>
                    <td class="px-6 py-4 text-sm text-green-600"><i class="fas fa-check"></i> Ya</td>
                </tr>
                <tr>
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">category</td>
                    <td class="px-6 py-4 text-sm text-gray-600">Nama kategori (harus sudah ada)</td>
                    <td class="px-6 py-4 text-sm text-gray-600">Elektronik</td>
                    <td class="px-6 py-4 text-sm text-green-600"><i class="fas fa-check"></i> Ya</td>
                </tr>
                <tr class="bg-gray-50">
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">supplier</td>
                    <td class="px-6 py-4 text-sm text-gray-600">Nama supplier (harus sudah ada)</td>
                    <td class="px-6 py-4 text-sm text-gray-600">PT Supplier ABC</td>
                    <td class="px-6 py-4 text-sm text-green-600"><i class="fas fa-check"></i> Ya</td>
                </tr>
                <tr>
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">description</td>
                    <td class="px-6 py-4 text-sm text-gray-600">Deskripsi produk</td>
                    <td class="px-6 py-4 text-sm text-gray-600">Laptop premium dengan layar 13 inch</td>
                    <td class="px-6 py-4 text-sm text-gray-600">Tidak</td>
                </tr>
                <tr class="bg-gray-50">
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">purchase_price</td>
                    <td class="px-6 py-4 text-sm text-gray-600">Harga beli (angka tanpa titik/koma)</td>
                    <td class="px-6 py-4 text-sm text-gray-600">15000000</td>
                    <td class="px-6 py-4 text-sm text-green-600"><i class="fas fa-check"></i> Ya</td>
                </tr>
                <tr>
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">selling_price</td>
                    <td class="px-6 py-4 text-sm text-gray-600">Harga jual (angka tanpa titik/koma)</td>
                    <td class="px-6 py-4 text-sm text-gray-600">18000000</td>
                    <td class="px-6 py-4 text-sm text-green-600"><i class="fas fa-check"></i> Ya</td>
                </tr>
                <tr class="bg-gray-50">
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">minimum_stock</td>
                    <td class="px-6 py-4 text-sm text-gray-600">Stok minimum (angka bulat)</td>
                    <td class="px-6 py-4 text-sm text-gray-600">5</td>
                    <td class="px-6 py-4 text-sm text-green-600"><i class="fas fa-check"></i> Ya</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
