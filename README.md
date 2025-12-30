# 📦 Stockify - Sistem Manajemen Stok Barang

Stockify adalah aplikasi web manajemen stok barang yang dirancang untuk membantu bisnis dalam mengelola inventori secara efisien dengan sistem approval multi-level dan audit trail lengkap.

## 🎯 Fitur Utama

### ✅ Manajemen Produk
- CRUD produk lengkap dengan atribut dinamis (size, color, weight, dll)
- Sistem approval produk (Manajer → Admin)
- Import/Export produk via CSV
- Upload gambar produk
- SKU auto-generate
- Kategori dan supplier management

### ✅ Manajemen Stok
- Transaksi stok masuk/keluar
- Sistem approval transaksi (Staff → Manajer)
- Real-time stock monitoring
- Low stock alerts
- Stock opname dengan adjustment otomatis

### ✅ Sistem Role & Permission
- **Admin**: Full access, user management, approval produk
- **Manajer Gudang**: Manajemen stok, approval transaksi, laporan, pengajuan produk
- **Staff Gudang**: Input transaksi stok masuk/keluar

### ✅ Laporan & Analytics
- Laporan stok komprehensif dengan filter
- Statistik transaksi (harian, mingguan, bulanan, tahunan)
- Distribusi kategori
- Export & print laporan
- Dashboard interaktif per role

### ✅ Activity Logs & Notifications
- Complete audit trail semua aktivitas
- In-app notifications real-time
- Activity log dengan filter advanced

## 🏗️ Arsitektur

Stockify dibangun dengan **Repository Pattern** dan **Service Pattern** untuk memastikan kode yang clean, maintainable, dan testable.

```
┌─────────────────────────────────────────────────────────┐
│                    Presentation Layer                    │
│                  (Views & Controllers)                   │
└────────────────────┬────────────────────────────────────┘
                     │
                     ↓
┌─────────────────────────────────────────────────────────┐
│                    Business Logic Layer                  │
│                      (Services)                          │
│  - ProductService                                        │
│  - StockTransactionService                               │
│  - CategoryService, SupplierService, UserService         │
│  - ActivityLogService, NotificationService               │
└────────────────────┬────────────────────────────────────┘
                     │
                     ↓
┌─────────────────────────────────────────────────────────┐
│                   Data Access Layer                      │
│                    (Repositories)                        │
│  - ProductRepository                                     │
│  - StockTransactionRepository                            │
│  - CategoryRepository, SupplierRepository, UserRepository│
└────────────────────┬────────────────────────────────────┘
                     │
                     ↓
┌─────────────────────────────────────────────────────────┐
│                   Database Layer                         │
│                  (Eloquent Models)                       │
│  Product, Category, Supplier, User, StockTransaction     │
└─────────────────────────────────────────────────────────┘
```

### Pattern Benefits:
- **Separation of Concerns**: Setiap layer memiliki tanggung jawab yang jelas
- **Reusability**: Business logic dapat digunakan kembali
- **Testability**: Mudah untuk unit testing
- **Maintainability**: Perubahan pada satu layer tidak mempengaruhi layer lain

## 📋 Workflow Sistem

### 1️⃣ Workflow Admin

**Alur Kerja:**
1. Login ke sistem sebagai Admin
2. Melihat dashboard dengan statistik keseluruhan sistem
3. **Approval Produk** - Menerima pengajuan produk baru dari manajer
   - Review detail produk (nama, SKU, harga, kategori, supplier, gambar)
   - **Approve** → Produk masuk ke sistem dengan status "approved", notifikasi otomatis ke manajer dan staff (jika ada tugas terkait)
   - **Reject** → Produk ditolak dengan alasan, notifikasi ke manajer, tugas terkait dibatalkan
4. **Manajemen User** - CRUD user (Admin, Manajer, Staff)
   - Buat user baru dengan role spesifik
   - Edit data user (nama, email, password)
   - Hapus user yang tidak aktif
5. **Master Data** - Kelola kategori produk dan supplier
   - CRUD kategori untuk klasifikasi produk
   - CRUD supplier dengan info kontak lengkap
6. **Activity Logs** - Monitor semua aktivitas user dalam sistem
   - Filter by user, action type, model, date range
   - View detail aktivitas dengan properties
   - Cleanup old logs
7. **Import/Export** - Kelola data massal
   - Import produk via CSV (validasi SKU unique, kategori & supplier exist)
   - Export semua produk ke CSV
   - Download template CSV untuk import

### 2️⃣ Workflow Manajer Gudang

**Alur Kerja:**
1. Login ke sistem sebagai Manajer
2. Melihat dashboard dengan statistik stok dan transaksi pending
3. **Pengajuan Produk Baru**:
   - Klik "Ajukan Produk Baru"
   - Isi form lengkap:
     * Informasi dasar: Nama, SKU (opsional - auto generate), Deskripsi
     * Kategori & Supplier (dropdown dari master data)
     * Harga beli & jual
     * Minimum stok untuk alert
     * Upload gambar produk
     * Atribut produk (opsional): size, color, weight, dll
   - **Opsional: Assign Task ke Staff**
     * Pilih jenis transaksi (barang masuk/keluar)
     * Pilih staff yang ditugaskan
     * Input jumlah barang
     * Tambah catatan tugas
   - Submit → Status produk "Pending", menunggu approval admin
   - Notifikasi ke semua admin
4. **Approval Transaksi Staff**:
   - Menu "Approval" menampilkan semua transaksi pending dari staff
   - Filter by status (pending/approved/rejected) dan type (in/out/opname)
   - View detail setiap transaksi:
     * Informasi produk
     * Staff yang mengajukan
     * Jumlah barang
     * Tanggal & catatan
     * Stok saat ini
   - **Approve Transaksi Masuk**:
     * Stok otomatis bertambah
     * Status berubah "Diterima"
     * Notifikasi ke staff
     * Activity log tercatat
   - **Approve Transaksi Keluar**:
     * Validasi stok mencukupi
     * Stok otomatis berkurang
     * Status berubah "Dikeluarkan"
     * Notifikasi ke staff
     * Activity log tercatat
   - **Reject Transaksi**:
     * Input alasan penolakan (wajib)
     * Status "Ditolak"
     * Notifikasi ke staff dengan alasan
5. **Stock Opname**:
   - Review hasil stock opname dari staff
   - Approve → Adjustment otomatis jika ada selisih
   - Reject → Opname ditolak dengan alasan
6. **Laporan Stok**:
   - **Quick Filters**: Hari ini, Minggu ini, Bulan ini, Tahun ini
   - **Custom Filter**: Tanggal mulai - akhir, Filter per kategori
   - **Statistik Ditampilkan**:
     * Total stok keseluruhan
     * Jumlah produk low stock
     * Jumlah produk out of stock
     * Total transaksi masuk (periode dipilih)
     * Total transaksi keluar (periode dipilih)
   - **Distribusi Kategori**: Tabel jumlah produk & total stok per kategori
   - **Transaksi Terbaru**: List 20 transaksi terakhir dalam periode
   - **Actions**: Print laporan, Export to CSV (future)

### 3️⃣ Workflow Staff Gudang

**Alur Kerja:**
1. Login ke sistem sebagai Staff
2. Dashboard menampilkan:
   - Tugas pending yang di-assign manajer
   - Transaksi pending menunggu approval
   - Statistik pribadi (transaksi hari ini & bulan ini)
3. **Input Barang Masuk**:
   - Menu "Barang Masuk"
   - View list transaksi masuk pending
   - **Input Transaksi Baru**:
     * Pilih produk dari dropdown (hanya approved products)
     * Input jumlah barang yang diterima
     * Tambah catatan penerimaan (supplier, kondisi barang, dll)
     * Submit → Status "Pending"
     * Notifikasi ke semua manajer
     * Menunggu approval manajer
   - **Statistik Pribadi**:
     * Jumlah diterima hari ini
     * Total bulan ini
4. **Input Barang Keluar**:
   - Menu "Barang Keluar"
   - View list transaksi keluar pending
   - **Input Transaksi Baru**:
     * Pilih produk - sistem otomatis tampilkan stok tersedia
     * Input jumlah barang keluar
     * **Validasi Real-time**: Warning jika jumlah > stok
     * Tambah catatan pengeluaran (tujuan, keperluan)
     * Submit → Status "Pending" (jika stok cukup)
     * Notifikasi ke semua manajer
     * Menunggu approval manajer
   - **Statistik Pribadi**:
     * Jumlah disiapkan hari ini
     * Total bulan ini
5. **Tugas dari Manajer**:
   - Notifikasi saat produk baru diapprove (jika staff di-assign)
   - View detail tugas:
     * Jenis transaksi (terima/keluarkan)
     * Produk & jumlah
     * Catatan dari manajer
   - Kerjakan tugas sesuai jenis
   - Tugas berubah status dari "pending_product_approval" → "pending"
6. **Cek Stok**:
   - Menu "Cek Stok"
   - Search produk by nama atau SKU
   - View informasi lengkap:
     * Nama, SKU, kategori
     * Stok saat ini
     * Minimum stok
     * Status (low stock alert jika < minimum)
     * Harga
     * Riwayat transaksi produk

## 🔐 Role & Permissions Detail

| Fitur | Admin | Manajer Gudang | Staff Gudang |
|-------|-------|----------------|--------------|
| **Dashboard** |
| View Dashboard | ✅ Full Stats | ✅ Stock Stats | ✅ Task Stats |
| **Produk** |
| View Produk | ✅ All | ✅ Approved Only | ✅ Approved Only |
| Create Produk | ✅ Direct | ✅ Pending Approval | ❌ |
| Edit Produk | ✅ | ❌ | ❌ |
| Delete Produk | ✅ | ❌ | ❌ |
| Approval Produk | ✅ | ❌ | ❌ |
| Import Produk | ✅ CSV | ❌ | ❌ |
| Export Produk | ✅ CSV | ❌ | ❌ |
| **Atribut Produk** |
| View Atribut | ✅ | ✅ | ✅ |
| CRUD Atribut Standalone | ✅ | ❌ | ❌ |
| **Stok Transaksi** |
| View All Transaksi | ✅ | ✅ | ❌ |
| View Own Transaksi | ✅ | ✅ | ✅ |
| Input Transaksi Masuk | ✅ | ✅ | ✅ |
| Input Transaksi Keluar | ✅ | ✅ | ✅ |
| Approval Transaksi | ❌ | ✅ | ❌ |
| **Stock Opname** |
| Input Stock Opname | ❌ | ✅ | ✅ |
| Approval Stock Opname | ❌ | ✅ | ❌ |
| **Master Data** |
| Kategori CRUD | ✅ | ❌ | ❌ |
| Supplier CRUD | ✅ | ❌ | ❌ |
| **User Management** |
| View Users | ✅ | ❌ | ❌ |
| Create User | ✅ | ❌ | ❌ |
| Edit User | ✅ | ❌ | ❌ |
| Delete User | ✅ | ❌ | ❌ |
| **Laporan** |
| Laporan Stok Komprehensif | ✅ | ✅ | ❌ |
| Print Laporan | ✅ | ✅ | ❌ |
| Activity Logs | ✅ | ❌ | ❌ |
| **Notifications** |
| In-App Notifications | ✅ | ✅ | ✅ |
| Mark as Read | ✅ | ✅ | ✅ |

## 🛠️ Teknologi

- **Backend Framework**: Laravel 11
- **Frontend**: Blade Templates
- **CSS Framework**: Tailwind CSS 3.x
- **UI Components**: Flowbite
- **JavaScript**: Alpine.js (dropdown & interactivity)
- **Icons**: Font Awesome 6
- **Database**: MySQL 8.x
- **Authentication**: Laravel Breeze
- **Architecture Pattern**: Repository Pattern + Service Pattern
- **PHP Version**: >= 8.2

## 📁 Struktur Project

```
stockify/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/              # Admin controllers
│   │   │   │   ├── ActivityLogController.php
│   │   │   │   ├── CategoryController.php
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── ProductAttributeController.php
│   │   │   │   ├── ProductController.php
│   │   │   │   ├── StockController.php
│   │   │   │   ├── SupplierController.php
│   │   │   │   └── UserController.php
│   │   │   ├── Manajer/            # Manajer controllers
│   │   │   │   ├── ApprovalController.php
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── ProductController.php
│   │   │   │   ├── ReportController.php
│   │   │   │   └── StockController.php
│   │   │   └── Staff/              # Staff controllers
│   │   │       ├── DashboardController.php
│   │   │       └── StockController.php
│   │   └── Middleware/
│   │       └── RoleMiddleware.php   # Role-based access control
│   ├── Models/                      # Eloquent Models
│   │   ├── ActivityLog.php          # Activity logging
│   │   ├── AppSetting.php
│   │   ├── Category.php
│   │   ├── Notification.php
│   │   ├── Product.php              # With scopes & relationships
│   │   ├── ProductAttribute.php
│   │   ├── StockOpname.php
│   │   ├── StockTransaction.php     # With scopes
│   │   ├── Supplier.php
│   │   └── User.php                 # With role constants
│   ├── Repositories/                # Data Access Layer
│   │   ├── BaseRepository.php       # Abstract base repository
│   │   ├── CategoryRepository.php
│   │   ├── ProductRepository.php    # Complex queries
│   │   ├── StockTransactionRepository.php
│   │   ├── SupplierRepository.php
│   │   └── UserRepository.php
│   ├── Services/                    # Business Logic Layer
│   │   ├── ActivityLogService.php   # Activity logging logic
│   │   ├── CategoryService.php
│   │   ├── NotificationService.php  # Notification logic
│   │   ├── ProductService.php       # Product business logic
│   │   ├── StockTransactionService.php
│   │   ├── SupplierService.php
│   │   └── UserService.php
│   ├── Rules/
│   │   └── SufficientStock.php      # Custom validation rule
│   └── Providers/
│       ├── AppServiceProvider.php
│       └── RepositoryServiceProvider.php  # DI Container bindings
├── database/
│   ├── migrations/
│   │   ├── 2025_12_23_022226_users.php
│   │   ├── 2025_12_23_023047_categories.php
│   │   ├── 2025_12_23_023217_suppliers.php
│   │   ├── 2025_12_23_023521_product.php
│   │   ├── 2025_12_23_031418_stock_transactions.php
│   │   ├── 2025_12_30_013251_create_activity_logs_table.php
│   │   └── ...
│   └── seeders/
│       └── DatabaseSeeder.php       # Seeder with default users
├── resources/
│   ├── views/
│   │   ├── admin/                   # Admin views
│   │   │   ├── activity-logs/
│   │   │   ├── attributes/
│   │   │   ├── categories/
│   │   │   ├── dashboard.blade.php
│   │   │   ├── products/
│   │   │   ├── stocks/
│   │   │   ├── suppliers/
│   │   │   └── users/
│   │   ├── manajer/                 # Manajer views
│   │   │   ├── approval/
│   │   │   ├── dashboard.blade.php
│   │   │   ├── products/
│   │   │   ├── reports/
│   │   │   └── stocks/
│   │   ├── staff/                   # Staff views
│   │   │   ├── dashboard.blade.php
│   │   │   └── stocks/
│   │   ├── layouts/
│   │   │   ├── admin.blade.php      # Admin layout with sidebar
│   │   │   ├── manajer.blade.php    # Manajer layout
│   │   │   ├── staff.blade.php      # Staff layout
│   │   │   └── guest.blade.php      # Guest layout
│   │   └── components/
│   ├── css/
│   │   └── app.css                  # Tailwind CSS
│   └── js/
│       └── app.js
└── routes/
    ├── web.php                       # Main routes with role middleware
    ├── auth.php                      # Authentication routes
    └── console.php
```

## 🚀 Instalasi

### Prerequisites
- PHP >= 8.2
- Composer
- MySQL >= 8.0
- Node.js >= 18.x & NPM
- Git

### Langkah Instalasi

#### 1. Clone Repository
```bash
git clone <repository-url>
cd stockify
```

#### 2. Install PHP Dependencies
```bash
composer install
```

#### 3. Install JavaScript Dependencies
```bash
npm install
```

#### 4. Environment Setup
```bash
# Copy file environment
cp .env.example .env

# Generate application key
php artisan key:generate
```

#### 5. Konfigurasi Database

Edit file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=stockify
DB_USERNAME=root
DB_PASSWORD=your_password
```

**Buat database di MySQL:**
```sql
CREATE DATABASE stockify CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

#### 6. Migrasi Database & Seeder
```bash
# Run migrations
php artisan migrate

# Run seeders (untuk default users)
php artisan db:seed

# Atau run sekaligus
php artisan migrate --seed
```

#### 7. Storage Link
```bash
# Create symbolic link untuk storage
php artisan storage:link
```

#### 8. Build Frontend Assets
```bash
# Development
npm run dev

# Production
npm run build
```

#### 9. Jalankan Aplikasi
```bash
# Development server
php artisan serve

# Atau dengan Laragon/XAMPP, akses via
# http://localhost/stockify/public
```

Aplikasi berjalan di: **http://localhost:8000**

## 👥 Default User Credentials

Setelah menjalankan seeder, gunakan kredensial berikut:

| Role | Email | Password | Akses |
|------|-------|----------|-------|
| **Admin** | admin@stockify.com | password | `/admin/dashboard` |
| **Manajer Gudang** | manajer@stockify.com | password | `/manajer/dashboard` |
| **Staff Gudang 1** | staff1@stockify.com | password | `/staff/dashboard` |
| **Staff Gudang 2** | staff2@stockify.com | password | `/staff/dashboard` |

⚠️ **PENTING**: 
- Ubah password default setelah login pertama kali!
- Untuk production, hapus atau disable seeder default users

## 📊 Database Schema

### Entity Relationship Diagram

```
┌──────────────┐         ┌──────────────┐         ┌──────────────┐
│   Category   │         │   Supplier   │         │     User     │
├──────────────┤         ├──────────────┤         ├──────────────┤
│ id (PK)      │         │ id (PK)      │         │ id (PK)      │
│ name         │         │ name         │         │ name         │
│ description  │         │ contact      │         │ email        │
└──────┬───────┘         │ phone        │         │ password     │
       │                 │ email        │         │ role         │
       │                 │ address      │         └──────┬───────┘
       │                 └──────┬───────┘                │
       │                        │                        │
       └────────────────┐       │       ┌────────────────┘
                        │       │       │
                 ┌──────▼───────▼───────▼─────┐
                 │        Product             │
                 ├────────────────────────────┤
                 │ id (PK)                    │
                 │ category_id (FK)           │
                 │ supplier_id (FK)           │
                 │ name, sku, description     │
                 │ purchase_price, selling_price
                 │ minimum_stock, current_stock
                 │ status (pending/approved/rejected)
                 │ image                      │
                 │ created_by (FK → users)    │
                 │ approved_by (FK → users)   │
                 │ approved_at                │
                 │ rejection_reason           │
                 └──────┬─────────────────────┘
                        │
          ┌─────────────┴─────────────┐
          │                           │
   ┌──────▼──────────┐      ┌─────────▼────────────┐
   │ ProductAttribute│      │ StockTransaction     │
   ├─────────────────┤      ├──────────────────────┤
   │ id (PK)         │      │ id (PK)              │
   │ product_id (FK) │      │ product_id (FK)      │
   │ name            │      │ user_id (FK)         │
   │ value           │      │ assigned_to (FK)     │
   └─────────────────┘      │ assigned_by (FK)     │
                            │ type (in/out)        │
                            │ quantity             │
                            │ date, status         │
                            │ notes                │
                            └──────────────────────┘

   ┌─────────────────┐      ┌──────────────────────┐
   │  ActivityLog    │      │   Notification       │
   ├─────────────────┤      ├──────────────────────┤
   │ id (PK)         │      │ id (PK)              │
   │ user_id (FK)    │      │ user_id (FK)         │
   │ action          │      │ title                │
   │ model           │      │ message              │
   │ model_id        │      │ type                 │
   │ description     │      │ link                 │
   │ properties JSON │      │ is_read              │
   │ ip_address      │      └──────────────────────┘
   │ user_agent      │
   └─────────────────┘
```

### Tables Detail

**users**
- Role: 'admin', 'manajer gudang', 'staff gudang'
- Authentication dengan Laravel Breeze

**products**
- Status: 'pending' (menunggu approval), 'approved' (disetujui), 'rejected' (ditolak)
- Current_stock: Calculated field dari StockTransaction

**stock_transactions**
- Type: 'in' (barang masuk), 'out' (barang keluar)
- Status: 'pending', 'diterima', 'ditolak', 'dikeluarkan', 'pending_product_approval'

**activity_logs**
- Action: 'login', 'logout', 'create', 'update', 'delete', 'approve', 'reject', 'export', 'import'
- Properties: JSON field untuk menyimpan old & new values

## 🔄 API Routes

### Public Routes
```
GET  /                      - Landing page (redirect to dashboard based on role)
GET  /login                 - Login page
POST /login                 - Process login
POST /logout                - Logout
GET  /register              - Register page (disabled)
```

### Admin Routes (`/admin/*`)
```
GET  /admin/dashboard                        - Dashboard admin
GET  /admin/products                         - List produk
GET  /admin/products/create                  - Form create produk
POST /admin/products                         - Store produk
GET  /admin/products/{id}                    - View detail produk
GET  /admin/products/{id}/edit               - Form edit produk
PUT  /admin/products/{id}                    - Update produk
DELETE /admin/products/{id}                  - Delete produk
GET  /admin/products/approval                - List approval produk
POST /admin/products/{id}/approve            - Approve produk
POST /admin/products/{id}/reject             - Reject produk
GET  /admin/products/export                  - Export CSV
GET  /admin/products/import-form             - Form import
POST /admin/products/import                  - Import CSV
GET  /admin/products/download-template       - Download template CSV

GET  /admin/attributes                       - List atribut
GET  /admin/attributes/create                - Form create atribut
POST /admin/attributes                       - Store atribut
GET  /admin/attributes/{id}/edit             - Form edit atribut
PUT  /admin/attributes/{id}                  - Update atribut
DELETE /admin/attributes/{id}                - Delete atribut

GET  /admin/categories                       - List kategori
POST /admin/categories                       - Store kategori
PUT  /admin/categories/{id}                  - Update kategori
DELETE /admin/categories/{id}                - Delete kategori

GET  /admin/suppliers                        - List supplier
POST /admin/suppliers                        - Store supplier
PUT  /admin/suppliers/{id}                   - Update supplier
DELETE /admin/suppliers/{id}                 - Delete supplier

GET  /admin/users                            - List user
GET  /admin/users/create                     - Form create user
POST /admin/users                            - Store user
GET  /admin/users/{id}/edit                  - Form edit user
PUT  /admin/users/{id}                       - Update user
DELETE /admin/users/{id}                     - Delete user

GET  /admin/activity-logs                    - List activity logs
GET  /admin/activity-logs/{id}               - Detail activity log
POST /admin/activity-logs/cleanup            - Cleanup old logs
```

### Manajer Routes (`/manajer/*`)
```
GET  /manajer/dashboard                      - Dashboard manajer
GET  /manajer/products                       - List produk approved
GET  /manajer/products/create                - Form create produk
POST /manajer/products                       - Submit produk untuk approval

GET  /manajer/approval                       - List approval transaksi
POST /manajer/approval/{id}/approve          - Approve transaksi
POST /manajer/approval/{id}/reject           - Reject transaksi
POST /manajer/approval/opname/{id}/approve   - Approve stock opname
POST /manajer/approval/opname/{id}/reject    - Reject stock opname

GET  /manajer/reports                        - Laporan stok
```

### Staff Routes (`/staff/*`)
```
GET  /staff/dashboard                        - Dashboard staff
GET  /staff/stocks/in                        - Form & list barang masuk
POST /staff/stocks/in                        - Submit barang masuk
GET  /staff/stocks/out                       - Form & list barang keluar
POST /staff/stocks/out                       - Submit barang keluar
GET  /staff/stocks/check                     - Cek stok produk
```

## 💡 Best Practices & Coding Standards

### 1. Repository Pattern
Semua akses database **HARUS** melalui Repository, tidak boleh direct query Model di Controller.

```php
// ❌ BAD - Direct Model access in Controller
public function index() {
    $products = Product::with('category')->where('status', 'approved')->get();
    return view('products.index', compact('products'));
}

// ✅ GOOD - Using Repository
public function index() {
    $products = $this->productRepository->getApprovedProducts();
    return view('products.index', compact('products'));
}
```

### 2. Service Pattern
Business logic **HARUS** di Service layer, Controller hanya handle request/response.

```php
// ❌ BAD - Business logic in Controller
public function store(Request $request) {
    DB::beginTransaction();
    try {
        $product = Product::create($request->validated());
        
        // Complex business logic...
        if ($request->has('attributes')) {
            foreach ($request->attributes as $attr) {
                $product->attributes()->create($attr);
            }
        }
        
        DB::commit();
        return redirect()->back()->with('success', 'Success');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', $e->getMessage());
    }
}

// ✅ GOOD - Delegate to Service
public function store(Request $request) {
    try {
        $product = $this->productService->createProduct($request->validated());
        return redirect()->back()->with('success', 'Produk berhasil ditambahkan');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Gagal menambahkan produk');
    }
}
```

### 3. Activity Logging
Log semua aktivitas penting untuk audit trail.

```php
// Create
$this->activityLogService->logCreate('Product', $product->id, 'Admin membuat produk baru: ' . $product->name);

// Update
$this->activityLogService->logUpdate('Product', $product->id, 'Admin mengupdate produk', $oldData, $newData);

// Delete
$this->activityLogService->logDelete('Product', $product->id, 'Admin menghapus produk: ' . $product->name);

// Approve
$this->activityLogService->logApprove('Product', $product->id, 'Admin menyetujui produk: ' . $product->name);

// Reject
$this->activityLogService->logReject('Product', $product->id, 'Admin menolak produk', $reason);
```

### 4. Notifications
Kirim notifikasi untuk semua perubahan status penting.

```php
// To specific user
$this->notificationService->create(
    $userId,
    'Judul Notifikasi',
    'Pesan notifikasi',
    'success', // success, info, warning, danger
    route('target.route')
);

// To all users with specific role
$this->notificationService->createForRole(
    'manajer gudang',
    'Transaksi Baru',
    'Ada transaksi baru menunggu approval',
    'info',
    route('manajer.approval.index')
);
```

### 5. Validation
Gunakan Form Request atau validate di Controller dengan pesan bahasa Indonesia.

```php
$validated = $request->validate([
    'product_id' => 'required|exists:products,id',
    'quantity' => 'required|integer|min:1',
    'notes' => 'nullable|string|max:500',
], [
    'product_id.required' => 'Produk harus dipilih',
    'product_id.exists' => 'Produk tidak ditemukan',
    'quantity.required' => 'Jumlah harus diisi',
    'quantity.integer' => 'Jumlah harus berupa angka',
    'quantity.min' => 'Jumlah minimal 1',
]);
```

### 6. Error Handling
Selalu gunakan try-catch untuk operasi database.

```php
try {
    DB::beginTransaction();
    
    // Your logic here
    
    DB::commit();
    return redirect()->back()->with('success', 'Berhasil');
} catch (\Exception $e) {
    DB::rollBack();
    \Log::error('Error: ' . $e->getMessage());
    return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
}
```

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/ProductTest.php

# Run with coverage
php artisan test --coverage

# Run specific test method
php artisan test --filter testCanCreateProduct
```

## 📝 Troubleshooting

### 1. Route Not Found / 404
```bash
# Clear route cache
php artisan route:clear

# Clear all cache
php artisan optimize:clear

# Verify routes
php artisan route:list
```

### 2. Class Not Found / Autoload Issues
```bash
# Dump autoload
composer dump-autoload

# Clear compiled
php artisan clear-compiled
```

### 3. View Compilation Error / Syntax Error
```bash
# Clear view cache
php artisan view:clear

# Check blade syntax errors in specific file
# Usually error message will show file path and line number
```

### 4. Permission Denied (Storage/Logs)
```bash
# Windows (Command Prompt as Administrator)
icacls storage /grant Users:F /T
icacls bootstrap\cache /grant Users:F /T

# Linux/Mac
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 5. Database Connection Error
- Pastikan MySQL service running
- Cek kredensial di `.env`:
  ```env
  DB_CONNECTION=mysql
  DB_HOST=127.0.0.1
  DB_PORT=3306
  DB_DATABASE=stockify
  DB_USERNAME=root
  DB_PASSWORD=your_password
  ```
- Test koneksi:
  ```bash
  php artisan tinker
  >>> DB::connection()->getPdo();
  ```

### 6. Asset Not Found (CSS/JS)
```bash
# Clear browser cache
# Or force rebuild
npm run build

# Verify public/build exists
# Check vite.config.js configuration
```

### 7. Session/CSRF Token Mismatch
```bash
# Clear session
php artisan session:clear

# Clear config
php artisan config:clear

# Verify APP_URL in .env matches your actual URL
```

## 📈 Performance Optimization

### 1. Database Optimization
- **Eager Loading**: Gunakan `with()` untuk mencegah N+1 query problem
```php
// ❌ BAD - N+1 Problem
$products = Product::all();
foreach ($products as $product) {
    echo $product->category->name; // Additional query for each product
}

// ✅ GOOD - Eager Loading
$products = Product::with('category')->get();
foreach ($products as $product) {
    echo $product->category->name; // No additional queries
}
```

- **Indexing**: Tambahkan index di kolom yang sering di-query
```php
$table->index('status');
$table->index(['category_id', 'status']);
```

### 2. Caching
```bash
# Cache config, routes, views untuk production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Clear cache saat development
php artisan optimize:clear
```

### 3. Query Optimization
- Gunakan `select()` untuk ambil kolom spesifik
- Gunakan `paginate()` untuk large dataset
- Gunakan `chunk()` untuk batch processing

```php
// Select specific columns
$products = Product::select('id', 'name', 'sku', 'current_stock')->get();

// Pagination
$products = Product::paginate(15);

// Chunk for large data
Product::chunk(100, function($products) {
    foreach ($products as $product) {
        // Process
    }
});
```

## 🚀 Deployment

### Production Checklist

1. **Environment**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
```

2. **Optimize Laravel**
```bash
# Cache everything
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Build assets for production
npm run build
```

3. **Database**
```bash
# Run migrations on production
php artisan migrate --force

# DO NOT run seeders in production
```

4. **Security**
- Ubah semua default passwords
- Generate new APP_KEY
- Setup HTTPS (SSL Certificate)
- Setup firewall rules
- Disable directory listing
- Setup backup automation

5. **Web Server Configuration**
- Point document root ke `/public`
- Setup URL rewrite untuk clean URLs
- Configure cron job untuk Laravel scheduler (jika ada)

### Apache .htaccess (Already included in public folder)
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

### Nginx Configuration
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /path/to/stockify/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

## 🔮 Roadmap & Future Features

### Phase 1 - Core Enhancement
- [ ] Email notifications (Laravel Mail)
- [ ] PDF export untuk laporan
- [ ] Advanced analytics dengan charts (Chart.js)
- [ ] Batch operations (bulk approve/reject)

### Phase 2 - Extended Features
- [ ] Multi-warehouse support
- [ ] Barcode/QR code generation & scanning
- [ ] Purchase Order management
- [ ] Vendor/Supplier rating system
- [ ] Product variants (color, size combinations)

### Phase 3 - Integration & Automation
- [ ] RESTful API untuk mobile app
- [ ] Webhook notifications
- [ ] Integration dengan accounting software
- [ ] Auto stock reorder (when low stock)
- [ ] Forecasting & demand prediction

### Phase 4 - Mobile & Advanced
- [ ] Mobile app (React Native / Flutter)
- [ ] Real-time updates (Laravel Echo + Pusher)
- [ ] Advanced reporting (BI Dashboard)
- [ ] Multi-language support (i18n)

## 🤝 Contributing

Kami menerima kontribusi dari komunitas! Berikut cara berkontribusi:

1. Fork repository ini
2. Buat branch baru untuk fitur Anda
   ```bash
   git checkout -b feature/AmazingFeature
   ```
3. Commit perubahan Anda
   ```bash
   git commit -m 'Add some AmazingFeature'
   ```
4. Push ke branch
   ```bash
   git push origin feature/AmazingFeature
   ```
5. Buat Pull Request

### Contribution Guidelines
- Ikuti PSR-12 coding standard
- Tulis unit test untuk fitur baru
- Update dokumentasi jika perlu
- Gunakan conventional commits
- Test di local sebelum submit PR

## 📄 License

Project ini dilisensikan di bawah [MIT License](LICENSE).

## 👨‍💻 Developer & Support

**Developed by**: Your Team Name

**Support**:
- 📧 Email: support@stockify.com
- 📚 Documentation: [Wiki](https://github.com/your-repo/stockify/wiki)
- 🐛 Bug Reports: [GitHub Issues](https://github.com/your-repo/stockify/issues)
- 💬 Discussion: [GitHub Discussions](https://github.com/your-repo/stockify/discussions)

## 🙏 Acknowledgments

- Laravel Framework - [https://laravel.com](https://laravel.com)
- Tailwind CSS - [https://tailwindcss.com](https://tailwindcss.com)
- Flowbite - [https://flowbite.com](https://flowbite.com)
- Font Awesome - [https://fontawesome.com](https://fontawesome.com)
- Alpine.js - [https://alpinejs.dev](https://alpinejs.dev)

---

<p align="center">
  <strong>Stockify</strong> - Solusi Modern untuk Manajemen Stok Barang Anda 📦✨
</p>

<p align="center">
  Made with ❤️ by Your Team
</p>
