# Spesifikasi Data Produk — Seroja Coffee

**Issue:** [#23 — Membuat Dokumen Spesifikasi Data Produk](https://github.com/isaaidthat/Kel-3-PU-MI3C/issues/23)  
**PIC:** Adinda  
**Tujuan:** Menjadi acuan pembuatan migration, model, validasi, dan API katalog produk.

## 1. Gambaran umum

Data produk disimpan pada tabel `products` di database MySQL. Laravel menjadi sumber data tepercaya untuk nama, harga, stok, foto, dan status produk. Frontend hanya menampilkan data yang dikirim API; harga atau stok yang dikirim frontend tidak boleh dianggap sebagai nilai yang benar saat transaksi.

## 2. Spesifikasi field

| Field | Tipe Laravel / database | Wajib | Nilai awal | Aturan dasar dan kegunaan |
| --- | --- | --- | --- | --- |
| `id` | `$table->id()` / unsigned BIGINT | Otomatis | Otomatis | Primary key produk. Dibuat oleh database. |
| `name` | `$table->string('name')` / VARCHAR(255) | Ya | — | Nama produk yang ditampilkan. Tidak boleh kosong setelah spasi dipangkas. Batas maksimal 255 karakter. |
| `description` | `$table->text('description')->nullable()` / TEXT | Tidak | `NULL` | Deskripsi produk. Boleh kosong; jika diisi, batasi panjang sesuai kebutuhan API/UI. |
| `image_path` | `$table->string('image_path')->nullable()` / VARCHAR(255) | Tidak | `NULL` | Path atau URL relatif gambar produk, bukan data gambar biner. Gambar dapat dikelola melalui Laravel Storage. |
| `price` | `$table->unsignedBigInteger('price')` / BIGINT UNSIGNED | Ya | — | Harga dalam rupiah bulat, tanpa pemisah atau simbol mata uang. Harus bilangan bulat lebih besar dari atau sama dengan 0. Contoh Rp85.000 disimpan sebagai `85000`. |
| `stock` | `$table->unsignedInteger('stock')` / INT UNSIGNED | Ya | `0` | Stok tersedia. Harus bilangan bulat lebih besar dari atau sama dengan 0. Digunakan untuk mencegah pemesanan melebihi stok. |
| `is_active` | `$table->boolean('is_active')` / TINYINT(1) | Ya | `true` | Menentukan apakah produk ditampilkan dan dapat dipesan. Nilai hanya boolean `true` atau `false`. |
| `created_at` | `$table->timestamps()` / TIMESTAMP | Otomatis | Waktu pembuatan | Waktu record dibuat oleh Laravel. |
| `updated_at` | `$table->timestamps()` / TIMESTAMP | Otomatis | Waktu perubahan | Diperbarui oleh Laravel saat record berubah. |
| `deleted_at` | `$table->softDeletes()` / TIMESTAMP nullable | Otomatis | `NULL` | Penanda soft delete. Produk yang pernah dipesan sebaiknya diarsipkan, bukan dihapus permanen. |

`stock`, `id`, timestamp, dan soft delete merupakan field pendukung di luar daftar inti issue. Stok sudah dibutuhkan untuk validasi ketersediaan saat pelanggan berbelanja; field sistem menjaga integritas serta riwayat data.

## 3. Aturan bisnis dan validasi

- Nama produk wajib diisi dan maksimal 255 karakter.
- Deskripsi dan gambar boleh tidak tersedia.
- `price` dan `stock` harus bernilai numerik bulat non-negatif. Backend menyimpan harga sebagai bilangan rupiah, bukan `float`.
- `is_active` hanya menerima boolean. Produk nonaktif tidak muncul di katalog publik dan tidak bisa dipesan.
- `image_path` menyimpan referensi lokasi file, bukan file gambar itu sendiri. Jika menerima upload, validasi tipe MIME dan ukuran file perlu diterapkan pada endpoint upload.
- Saat checkout, backend mengambil harga dan stok terkini dari database, menghitung ulang total, lalu memastikan stok cukup. Jangan mempercayai harga dari request frontend.
- Nama produk tidak dibuat unique pada rancangan saat ini; produk berbeda dapat memiliki nama serupa jika kebutuhan bisnis mengizinkan.

## 4. Representasi API katalog

Field publik yang dikirim API:

```json
{
  "data": {
    "id": 1,
    "name": "Seroja Arabica",
    "description": "Kopi Arabica Seroja Coffee.",
    "price": 85000,
    "stock": 20,
    "image_path": null
  }
}
```

Endpoint daftar menggunakan pagination Laravel, sehingga respons daftar juga berisi `links` dan `meta`. Endpoint katalog hanya menampilkan produk aktif yang belum di-soft-delete. `created_at`, `updated_at`, dan `deleted_at` adalah field internal dan tidak dikirim oleh resource produk saat ini.

## 5. Kesesuaian dengan implementasi

Spesifikasi ini sesuai dengan:

- Migration: `database/migrations/2026_10_18_000001_create_products_table.php`
- Model: `app/Models/Product.php`
- API resource: `app/Http/Resources/ProductResource.php`
- Endpoint: `GET /api/products` dan `GET /api/products/{id}`

Migration menjadi sumber kebenaran untuk struktur kolom. Jika kebutuhan kelompok berubah, migration baru dan dokumen ini perlu diperbarui bersama-sama.
