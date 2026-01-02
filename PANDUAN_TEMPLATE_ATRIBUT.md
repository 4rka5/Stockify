# Panduan Penggunaan Template Atribut Produk

## Tentang Fitur Ini

Fitur **Template Atribut** memungkinkan admin membuat template atribut yang dapat digunakan kembali saat menambah produk. Ini membuat proses input data lebih cepat dan konsisten.

## Cara Kerja

### 1. Admin Membuat Template Atribut

**Lokasi:** `Admin > Master Data > Template Atribut`

**Langkah-langkah:**
1. Klik menu **Master Data** di sidebar
2. Pilih **Template Atribut**
3. Klik tombol **Tambah Template**
4. Isi form:
   - **Nama Template**: Contoh: Ukuran, Warna, Bahan, Kapasitas
   - **Deskripsi**: Penjelasan singkat (opsional)
   - **Status**: Centang "Aktifkan template" agar bisa dipilih saat input produk
5. Klik **Simpan Template**

**Contoh Template yang Berguna:**
- Ukuran (untuk produk pakaian, sepatu)
- Warna (untuk produk dengan variasi warna)
- Bahan (untuk produk tekstil)
- Kapasitas (untuk botol, wadah)
- Varian Rasa (untuk makanan/minuman)
- Voltase (untuk produk elektronik)

### 2. Admin/Manajer Menggunakan Template saat Menambah Produk

**Lokasi:** `Admin/Manajer > Produk > Tambah Produk`

**Langkah-langkah:**
1. Isi data produk seperti biasa (nama, kategori, harga, dll)
2. Pada bagian **Atribut Produk**:
   - Klik tombol **Tambah Atribut**
   - Pilih template dari dropdown (contoh: "Ukuran")
   - Isi nilai untuk template tersebut (contoh: "XL")
   - Atau pilih "Input Manual" jika tidak ada template yang sesuai
3. Tambahkan atribut lain sesuai kebutuhan
4. Simpan produk

**Keuntungan Menggunakan Template:**
- ✅ Lebih cepat - tinggal pilih dan isi nilai
- ✅ Konsisten - nama atribut sama untuk semua produk
- ✅ Rapi - tidak ada typo atau penulisan berbeda-beda
- ✅ Mudah dicari - data terstruktur dengan baik

## Mengelola Template Atribut

### Melihat Daftar Template
- Buka **Admin > Master Data > Template Atribut**
- Lihat semua template yang sudah dibuat
- Cek berapa produk yang menggunakan template tersebut

### Mengedit Template
1. Klik icon **Edit** (pensil) pada template
2. Ubah nama, deskripsi, atau status
3. Klik **Update Template**

**Catatan:** Perubahan nama template akan mempengaruhi tampilan di semua produk yang menggunakannya.

### Menonaktifkan Template
Jika template tidak lagi digunakan:
1. Edit template tersebut
2. Hilangkan centang "Aktifkan template"
3. Simpan

Template nonaktif tidak akan muncul saat menambah produk baru, tapi data produk lama tetap ada.

### Menghapus Template
⚠️ **Hati-hati:** Menghapus template akan menghilangkan link ke produk, tapi nilai atribut pada produk tetap tersimpan.

## Tips Penggunaan

1. **Buat template untuk atribut yang sering digunakan**
   - Contoh: Jika toko banyak jual pakaian, buat template Ukuran, Warna, Bahan

2. **Gunakan nama template yang jelas dan singkat**
   - ✅ Baik: "Ukuran", "Warna", "Kapasitas"
   - ❌ Kurang baik: "Size produk", "Macam-macam warna"

3. **Tambahkan deskripsi untuk template yang mungkin ambigu**
   - Contoh: Template "Ukuran" → Deskripsi: "Ukuran pakaian (S, M, L, XL, XXL)"

4. **Input manual tetap bisa digunakan**
   - Untuk atribut unik yang jarang dipakai, tidak perlu buat template
   - Pilih "Input Manual" dan langsung ketik nama dan nilai

## Contoh Kasus Penggunaan

### Kasus 1: Toko Pakaian
**Template yang dibuat admin:**
- Ukuran
- Warna
- Bahan
- Model

**Saat manajer menambah produk "Kemeja Pria":**
1. Tambah atribut → Pilih "Ukuran" → Isi "L"
2. Tambah atribut → Pilih "Warna" → Isi "Biru"
3. Tambah atribut → Pilih "Bahan" → Isi "Katun"
4. Tambah atribut → Pilih "Model" → Isi "Slim Fit"

### Kasus 2: Toko Elektronik
**Template yang dibuat admin:**
- Voltase
- Watt
- Garansi
- Brand

**Saat admin menambah produk "Kipas Angin":**
1. Tambah atribut → Pilih "Voltase" → Isi "220V"
2. Tambah atribut → Pilih "Watt" → Isi "45W"
3. Tambah atribut → Pilih "Garansi" → Isi "1 Tahun"
4. Tambah atribut → Input Manual → Nama: "Jumlah Kecepatan" → Nilai: "3"

## FAQ

**Q: Apakah wajib menggunakan template?**
A: Tidak. Template hanya untuk mempermudah. Anda tetap bisa input manual seperti biasa.

**Q: Bisa tidak admin membuat template dan manajer juga membuat template?**
A: Saat ini hanya admin yang bisa membuat/edit template. Manajer hanya bisa memilih dan menggunakan template yang sudah dibuat admin.

**Q: Kalau template diedit, apakah data produk lama ikut berubah?**
A: Nama template akan berubah di semua produk yang menggunakan template tersebut, tapi nilai yang sudah diisi tidak berubah.

**Q: Bisa tidak satu produk pakai beberapa template?**
A: Bisa. Satu produk bisa memiliki banyak atribut, baik dari template maupun input manual.

---

**Dibuat:** 2 Januari 2026  
**Versi:** 1.0  
**Sistem:** Stockify - Sistem Manajemen Stok
