# Alur Approval Transaksi - Stockify

## 📋 Overview
Semua transaksi barang masuk/keluar yang diinput oleh staff **HARUS diapprove oleh manajer** sebelum stok terupdate.

## 🔄 Alur Kerja

### **1. Staff Input Transaksi**

#### Barang Masuk (`/staff/stock/in`)
- Staff mengisi form input barang masuk
- Pilih produk, jumlah, dan catatan
- Klik "Simpan Barang Masuk"
- ✅ Status: **PENDING** (menunggu approval)
- ⚠️ Stok **BELUM** berubah

#### Barang Keluar (`/staff/stock/out`)
- Staff mengisi form input barang keluar
- Sistem validasi ketersediaan stok real-time
- Klik "Simpan Barang Keluar"
- ✅ Status: **PENDING** (menunggu approval)
- ⚠️ Stok **BELUM** berubah

### **2. Manajer Menerima Notifikasi**
- 🔔 Notifikasi otomatis ke semua manajer
- Email: Transaksi baru dari [Nama Staff]
- Link langsung ke halaman approval

### **3. Manajer Review & Approve (`/manajer/approval`)**

#### Lihat Detail Transaksi
- Produk yang diminta
- Jumlah
- Staff yang mengajukan
- Catatan
- Tipe transaksi (masuk/keluar)
- Status (pending/approved/rejected)

#### Action Manajer

**APPROVE ✅**
```
Transaksi IN  → Status: DITERIMA    → Stok +10 unit
Transaksi OUT → Status: DIKELUARKAN → Stok -5 unit
```

**REJECT ❌**
```
Transaksi → Status: DITOLAK → Stok tidak berubah
(Tambah alasan penolakan)
```

### **4. Update Stok Otomatis**
- ✅ Setelah manajer APPROVE:
  - Status berubah ke `diterima` atau `dikeluarkan`
  - Stok produk **otomatis terupdate** via accessor
  - Admin & Manajer monitoring langsung terupdate
  - Transaksi masuk ke riwayat

## 📊 Status Transaksi

| Status | Arti | Stok Berubah? |
|--------|------|---------------|
| `pending` | Menunggu approval manajer | ❌ Belum |
| `diterima` | Barang masuk approved | ✅ Ya (+) |
| `dikeluarkan` | Barang keluar approved | ✅ Ya (-) |
| `ditolak` | Transaksi ditolak | ❌ Tidak |

## 🎯 Keuntungan Sistem Approval

1. **Kontrol Penuh Manajer**
   - Semua transaksi harus review manajer
   - Manajer selalu tahu pergerakan stok
   - Mencegah kesalahan input

2. **Audit Trail Lengkap**
   - Siapa input transaksi
   - Kapan diinput
   - Siapa yang approve/reject
   - Alasan reject (jika ada)

3. **Fleksibilitas Staff**
   - Staff bisa input kapan saja
   - Tidak perlu menunggu manajer online
   - Transaksi tersimpan untuk review

4. **Validasi Ganda**
   - Staff: Input data
   - Manajer: Validasi & approve
   - Sistem: Auto-update stok

## 🚀 Dual Mode Operation

### Mode 1: Staff Input Mandiri (Perlu Approval)
```
Staff Input → PENDING → Manajer Approve → DITERIMA/DIKELUARKAN → Stok Update
```

### Mode 2: Manajer Assignment (Perlu Konfirmasi)
```
Manajer Create + Assign → PENDING → Staff Confirm → DITERIMA/DIKELUARKAN → Stok Update
```

## 📍 URL Penting

- Staff Input Barang Masuk: `/staff/stock/in`
- Staff Input Barang Keluar: `/staff/stock/out`
- Manajer Approval: `/manajer/approval`
- Staff Riwayat: `/staff/transactions`
- Manajer Monitoring: `/manajer/stock`

## 🔐 Keamanan

- ✅ Validasi stok real-time untuk transaksi keluar
- ✅ Notifikasi otomatis ke manajer
- ✅ Log semua aktivitas
- ✅ Hak akses berbasis role
- ✅ CSRF protection
- ✅ Database transaction (rollback on error)

---

**Catatan:** Sistem ini memastikan manajer selalu punya kontrol dan visibilitas penuh atas semua pergerakan stok di gudang! 🎯
