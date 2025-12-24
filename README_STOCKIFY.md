# Stockify - Aplikasi Manajemen Stok Barang

Stockify adalah aplikasi web yang dirancang untuk membantu bisnis dalam mengelola stok barang secara efisien dan akurat dengan mengimplementasikan **Repository Pattern** dan **Service Pattern**.

## 📋 Fitur Utama

- ✅ Manajemen Produk (CRUD lengkap dengan atribut dan kategori)
- ✅ Manajemen Stok (Barang masuk/keluar, monitoring real-time)
- ✅ Manajemen Pengguna (3 Role: Admin, Manajer Gudang, Staff Gudang)
- ✅ Sistem Persetujuan Transaksi
- ✅ Laporan Stok dan Transaksi
- ✅ Alert Stok Menipis
- ✅ Dashboard Interaktif untuk setiap role

## 🛠️ Teknologi

- **Backend**: Laravel 11
- **Frontend**: Tailwind CSS + Flowbite
- **Database**: MySQL
- **Pattern**: Repository Pattern + Service Pattern

## 📁 Struktur Arsitektur

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/          # Controllers untuk Admin
│   │   ├── Manajer/        # Controllers untuk Manajer
│   │   └── Staff/          # Controllers untuk Staff
│   └── Middleware/
│       └── RoleMiddleware.php
├── Models/                 # Eloquent Models
├── Repositories/          # Data Access Layer
│   ├── BaseRepository.php
│   ├── CategoryRepository.php
│   ├── ProductRepository.php
│   ├── StockTransactionRepository.php
│   ├── SupplierRepository.php
│   └── UserRepository.php
└── Services/              # Business Logic Layer
    ├── CategoryService.php
    ├── ProductService.php
    ├── StockTransactionService.php
    ├── SupplierService.php
    └── UserService.php
```

## 🚀 Instalasi

### Prerequisites
- PHP >= 8.2
- Composer
- MySQL
- Node.js & NPM

### Langkah Instalasi

1. **Clone Repository**
```bash
git clone <repository-url>
cd stockify
```

2. **Install Dependencies**
```bash
composer install
npm install
```

3. **Environment Setup**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Database Configuration**

Edit file `.env` dan sesuaikan konfigurasi database:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=stockify
DB_USERNAME=root
DB_PASSWORD=
```

5. **Run Migration & Seeder**
```bash
php artisan migrate
php artisan db:seed
```

6. **Build Assets**
```bash
npm run dev
```

7. **Run Application**
```bash
php artisan serve
```

Aplikasi akan berjalan di `http://localhost:8000`

## 👥 Default User Credentials

Setelah menjalankan seeder, gunakan kredensial berikut untuk login:

### Admin
- Email: `admin@stockify.com`
- Password: `password`

### Manajer Gudang
- Email: `manajer@stockify.com`
- Password: `password`

### Staff Gudang
- Email: `staff1@stockify.com`
- Password: `password`

## 📊 Database Schema

### Tables
- `users` - Data pengguna dengan role
- `categories` - Kategori produk
- `suppliers` - Data supplier
- `products` - Data produk
- `product_attributes` - Atribut produk (size, color, dll)
- `stock_transactions` - Transaksi stok masuk/keluar

## 🎯 Role & Permissions

### Admin
- Full access ke semua fitur
- Manajemen User (CRUD)
- Manajemen Kategori, Supplier, Produk
- View semua laporan dan aktivitas

### Manajer Gudang
- Manajemen Stok (Barang masuk/keluar)
- Approve/Reject transaksi dari Staff
- View produk (read-only)
- Generate laporan stok
- Kelola staff gudang

### Staff Gudang
- Input barang masuk
- Input barang keluar
- Cek stok produk
- View riwayat transaksi sendiri

## 🏗️ Pattern Implementation

### Repository Pattern
Repository bertanggung jawab untuk semua interaksi dengan database. Setiap repository meng-extend `BaseRepository` yang menyediakan method CRUD dasar.

**Contoh:**
```php
// ProductRepository.php
public function getAllWithRelations()
{
    return $this->model->with(['category', 'supplier', 'attributes'])->get();
}
```

### Service Pattern
Service layer berisi business logic dan menggunakan repository untuk akses data. Semua transaksi database di-handle di service layer.

**Contoh:**
```php
// ProductService.php
public function createProduct(array $data)
{
    DB::beginTransaction();
    try {
        // Business logic here
        $product = $this->productRepository->create($data);
        DB::commit();
        return $product;
    } catch (Exception $e) {
        DB::rollBack();
        throw new Exception('Failed to create product: ' . $e->getMessage());
    }
}
```

### Controller Implementation
Controller hanya bertugas menerima request, memanggil service, dan mengembalikan response.

**Contoh:**
```php
// ProductController.php
public function store(Request $request)
{
    $validated = $request->validate([...]);
    
    try {
        $this->productService->createProduct($validated);
        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil ditambahkan');
    } catch (Exception $e) {
        return back()->with('error', 'Gagal menambahkan produk');
    }
}
```

## 🔐 Middleware

### RoleMiddleware
Middleware untuk membatasi akses berdasarkan role user:

```php
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Admin routes
});
```

## 📝 API Routes

### Admin Routes
- `/admin/dashboard` - Dashboard admin
- `/admin/products` - CRUD Produk
- `/admin/categories` - CRUD Kategori
- `/admin/suppliers` - CRUD Supplier
- `/admin/users` - CRUD User
- `/admin/transactions` - View semua transaksi

### Manajer Routes
- `/manajer/dashboard` - Dashboard manajer
- `/manajer/stock` - Manajemen stok
- `/manajer/stock/{id}/approve` - Approve transaksi
- `/manajer/stock/{id}/reject` - Reject transaksi
- `/manajer/reports` - Laporan

### Staff Routes
- `/staff/dashboard` - Dashboard staff
- `/staff/stock/in` - Input barang masuk
- `/staff/stock/out` - Input barang keluar
- `/staff/stock/check` - Cek stok

## 🎨 Frontend

Aplikasi menggunakan **Tailwind CSS** untuk styling dan **Flowbite** untuk komponen UI interaktif seperti:
- Dropdown menus
- Modals
- Alerts
- Tables
- Forms

## 📦 Dependencies

### PHP Packages (composer.json)
- laravel/framework: ^11.0
- laravel/tinker
- laravel/breeze (untuk authentication)

### JavaScript Packages (package.json)
- tailwindcss
- flowbite
- vite

## 🧪 Testing

```bash
php artisan test
```

## 📖 Dokumentasi Tambahan

Untuk dokumentasi lebih lengkap, lihat folder `docs/` atau kunjungi wiki project.

## 🤝 Contributing

1. Fork repository
2. Buat branch baru (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

## 📄 License

Project ini menggunakan MIT License.

## 👨‍💻 Developer

Developed by **[Your Team Name]**

---

**Stockify** - Solusi Modern untuk Manajemen Stok Barang Anda
