# Spesifikasi Field Database — Seroja Coffee

Dokumen ini merangkum tabel dan field yang dibuat oleh migration backend. Migration merupakan sumber kebenaran struktur database; jika schema berubah, migration dan dokumen ini perlu diperbarui bersama.

## Konvensi

- Tipe database di bawah mengacu pada MySQL/MariaDB.
- `Wajib` berarti kolom tidak nullable; kolom auto-increment dan kolom yang diisi otomatis oleh Laravel ditandai demikian.
- `BIGINT UNSIGNED` digunakan untuk ID dan nilai uang agar nilainya tidak negatif.
- `created_at` dan `updated_at` dibuat oleh `$table->timestamps()` dan nullable secara default di Laravel.
- Relasi foreign key yang tidak disebut sebagai constraint hanya memiliki indeks, sesuai migration.

## Ringkasan tabel

| Tabel | Kegunaan |
| --- | --- |
| `users` | Akun pelanggan dan admin |
| `password_reset_tokens` | Token reset password berdasarkan email |
| `sessions` | Sesi login jika session driver menggunakan database |
| `cache`, `cache_locks` | Cache dan penguncian cache jika cache store menggunakan database |
| `jobs`, `job_batches`, `failed_jobs` | Antrean pekerjaan dan pencatatan pekerjaan gagal |
| `products` | Katalog produk |
| `carts`, `cart_items` | Keranjang belanja dan itemnya |
| `orders`, `order_items` | Pesanan, snapshot alamat, dan item pesanan |
| `payments` | Catatan pembayaran serta verifikasi |
| `migrations` | Riwayat migration yang dikelola Laravel |

## Tabel akun dan autentikasi

### `users`

| Field | Tipe MySQL | Wajib | Default | Keterangan / aturan |
| --- | --- | --- | --- | --- |
| `id` | BIGINT UNSIGNED | Ya | Auto-increment | Primary key akun. |
| `name` | VARCHAR(255) | Ya | — | Nama pengguna. |
| `email` | VARCHAR(255) | Ya | — | Email; unique. |
| `phone` | VARCHAR(30) | Tidak | `NULL` | Nomor telepon. |
| `role` | VARCHAR(20) | Ya | `customer` | Peran akun; diindeks. Database belum membatasi nilai dengan ENUM/check constraint. |
| `email_verified_at` | TIMESTAMP | Tidak | `NULL` | Waktu verifikasi email. |
| `password` | VARCHAR(255) | Ya | — | Password dalam bentuk hash, bukan plaintext. |
| `remember_token` | VARCHAR(100) | Tidak | `NULL` | Token untuk fitur remember me Laravel. |
| `created_at` | TIMESTAMP | Tidak | `NULL` | Waktu pembuatan record. |
| `updated_at` | TIMESTAMP | Tidak | `NULL` | Waktu perubahan record. |

### `password_reset_tokens`

| Field | Tipe MySQL | Wajib | Default | Keterangan / aturan |
| --- | --- | --- | --- | --- |
| `email` | VARCHAR(255) | Ya | — | Primary key; email akun yang meminta reset password. |
| `token` | VARCHAR(255) | Ya | — | Token reset password. |
| `created_at` | TIMESTAMP | Tidak | `NULL` | Waktu token dibuat. |

### `sessions`

| Field | Tipe MySQL | Wajib | Default | Keterangan / aturan |
| --- | --- | --- | --- | --- |
| `id` | VARCHAR(255) | Ya | — | Primary key sesi. |
| `user_id` | BIGINT UNSIGNED | Tidak | `NULL` | ID pengguna terautentikasi; diindeks, tetapi migration tidak memasang foreign key constraint. |
| `ip_address` | VARCHAR(45) | Tidak | `NULL` | Alamat IPv4 atau IPv6 klien. |
| `user_agent` | TEXT | Tidak | `NULL` | Informasi browser/klien. |
| `payload` | LONGTEXT | Ya | — | Data sesi yang diserialisasi Laravel. |
| `last_activity` | INT | Ya | — | Waktu aktivitas terakhir sebagai Unix timestamp; diindeks. |

## Tabel cache dan antrean

### `cache`

| Field | Tipe MySQL | Wajib | Default | Keterangan / aturan |
| --- | --- | --- | --- | --- |
| `key` | VARCHAR(255) | Ya | — | Primary key cache. |
| `value` | MEDIUMTEXT | Ya | — | Nilai cache tersimpan. |
| `expiration` | BIGINT | Ya | — | Waktu kedaluwarsa; diindeks. |

### `cache_locks`

| Field | Tipe MySQL | Wajib | Default | Keterangan / aturan |
| --- | --- | --- | --- | --- |
| `key` | VARCHAR(255) | Ya | — | Primary key lock cache. |
| `owner` | VARCHAR(255) | Ya | — | Identitas pemilik lock. |
| `expiration` | BIGINT | Ya | — | Waktu kedaluwarsa lock; diindeks. |

### `jobs`

| Field | Tipe MySQL | Wajib | Default | Keterangan / aturan |
| --- | --- | --- | --- | --- |
| `id` | BIGINT UNSIGNED | Ya | Auto-increment | Primary key pekerjaan. |
| `queue` | VARCHAR(255) | Ya | — | Nama antrean; diindeks. |
| `payload` | LONGTEXT | Ya | — | Data pekerjaan yang diserialisasi. |
| `attempts` | SMALLINT UNSIGNED | Ya | — | Jumlah percobaan pemrosesan. |
| `reserved_at` | INT UNSIGNED | Tidak | `NULL` | Unix timestamp saat pekerjaan diambil worker. |
| `available_at` | INT UNSIGNED | Ya | — | Unix timestamp saat pekerjaan tersedia. |
| `created_at` | INT UNSIGNED | Ya | — | Unix timestamp saat pekerjaan dibuat. |

### `job_batches`

| Field | Tipe MySQL | Wajib | Default | Keterangan / aturan |
| --- | --- | --- | --- | --- |
| `id` | VARCHAR(255) | Ya | — | Primary key batch pekerjaan. |
| `name` | VARCHAR(255) | Ya | — | Nama batch. |
| `total_jobs` | INT | Ya | — | Jumlah seluruh pekerjaan di batch. |
| `pending_jobs` | INT | Ya | — | Jumlah pekerjaan yang belum selesai. |
| `failed_jobs` | INT | Ya | — | Jumlah pekerjaan gagal. |
| `failed_job_ids` | LONGTEXT | Ya | — | Daftar ID pekerjaan yang gagal. |
| `options` | MEDIUMTEXT | Tidak | `NULL` | Opsi batch. |
| `cancelled_at` | INT | Tidak | `NULL` | Unix timestamp pembatalan batch. |
| `created_at` | INT | Ya | — | Unix timestamp pembuatan batch. |
| `finished_at` | INT | Tidak | `NULL` | Unix timestamp selesainya batch. |

### `failed_jobs`

| Field | Tipe MySQL | Wajib | Default | Keterangan / aturan |
| --- | --- | --- | --- | --- |
| `id` | BIGINT UNSIGNED | Ya | Auto-increment | Primary key record kegagalan. |
| `uuid` | VARCHAR(255) | Ya | — | UUID pekerjaan; unique. |
| `connection` | VARCHAR(255) | Ya | — | Nama koneksi queue. |
| `queue` | VARCHAR(255) | Ya | — | Nama antrean. |
| `payload` | LONGTEXT | Ya | — | Data pekerjaan yang gagal. |
| `exception` | LONGTEXT | Ya | — | Detail exception. |
| `failed_at` | TIMESTAMP | Ya | Waktu saat ini | Waktu pekerjaan tercatat gagal. |

Indeks gabungan `connection`, `queue`, dan `failed_at` membantu pencarian record pekerjaan gagal.

## Tabel katalog dan transaksi

### `products`

| Field | Tipe MySQL | Wajib | Default | Keterangan / aturan |
| --- | --- | --- | --- | --- |
| `id` | BIGINT UNSIGNED | Ya | Auto-increment | Primary key produk. |
| `name` | VARCHAR(255) | Ya | — | Nama produk. |
| `description` | TEXT | Tidak | `NULL` | Deskripsi produk. |
| `price` | BIGINT UNSIGNED | Ya | — | Harga dalam rupiah bulat. |
| `stock` | INT UNSIGNED | Ya | `0` | Jumlah stok tersedia. |
| `image_path` | VARCHAR(255) | Tidak | `NULL` | Path/URL gambar, bukan isi file gambar. |
| `is_active` | TINYINT(1) | Ya | `1` | Status aktif; diindeks. |
| `created_at` | TIMESTAMP | Tidak | `NULL` | Waktu pembuatan record. |
| `updated_at` | TIMESTAMP | Tidak | `NULL` | Waktu perubahan record. |
| `deleted_at` | TIMESTAMP | Tidak | `NULL` | Penanda soft delete. |

### `carts`

| Field | Tipe MySQL | Wajib | Default | Keterangan / aturan |
| --- | --- | --- | --- | --- |
| `id` | BIGINT UNSIGNED | Ya | Auto-increment | Primary key keranjang. |
| `user_id` | BIGINT UNSIGNED | Ya | — | Foreign key ke `users.id`; unique sehingga satu pengguna memiliki maksimal satu keranjang. Penghapusan user menghapus keranjangnya. |
| `created_at` | TIMESTAMP | Tidak | `NULL` | Waktu pembuatan record. |
| `updated_at` | TIMESTAMP | Tidak | `NULL` | Waktu perubahan record. |

### `cart_items`

| Field | Tipe MySQL | Wajib | Default | Keterangan / aturan |
| --- | --- | --- | --- | --- |
| `id` | BIGINT UNSIGNED | Ya | Auto-increment | Primary key item keranjang. |
| `cart_id` | BIGINT UNSIGNED | Ya | — | Foreign key ke `carts.id`; item dihapus jika keranjang dihapus. |
| `product_id` | BIGINT UNSIGNED | Ya | — | Foreign key ke `products.id`; item dihapus jika produk dihapus. |
| `quantity` | INT UNSIGNED | Ya | — | Banyaknya produk pada keranjang. |
| `created_at` | TIMESTAMP | Tidak | `NULL` | Waktu pembuatan record. |
| `updated_at` | TIMESTAMP | Tidak | `NULL` | Waktu perubahan record. |

Kombinasi `cart_id` dan `product_id` unique; produk yang sama hanya boleh memiliki satu baris per keranjang.

### `orders`

| Field | Tipe MySQL | Wajib | Default | Keterangan / aturan |
| --- | --- | --- | --- | --- |
| `id` | BIGINT UNSIGNED | Ya | Auto-increment | Primary key pesanan. |
| `order_number` | VARCHAR(40) | Ya | — | Nomor pesanan; unique. |
| `user_id` | BIGINT UNSIGNED | Ya | — | Foreign key ke `users.id`; penghapusan user ditolak jika masih memiliki pesanan. |
| `recipient_name` | VARCHAR(255) | Ya | — | Snapshot nama penerima saat checkout. |
| `recipient_phone` | VARCHAR(30) | Ya | — | Snapshot nomor telepon penerima. |
| `address_line` | VARCHAR(255) | Ya | — | Alamat jalan/detail pengiriman. |
| `city` | VARCHAR(255) | Ya | — | Kota/kabupaten tujuan. |
| `province` | VARCHAR(255) | Ya | — | Provinsi tujuan. |
| `postal_code` | VARCHAR(20) | Ya | — | Kode pos tujuan. |
| `delivery_note` | TEXT | Tidak | `NULL` | Catatan pengiriman. |
| `subtotal` | BIGINT UNSIGNED | Ya | — | Jumlah harga item sebelum biaya pengiriman, rupiah bulat. |
| `shipping_fee` | BIGINT UNSIGNED | Ya | `0` | Biaya pengiriman, rupiah bulat. |
| `total_amount` | BIGINT UNSIGNED | Ya | — | Total tagihan, rupiah bulat. |
| `status` | VARCHAR(30) | Ya | `pending_payment` | Status pesanan; diindeks. Database belum membatasi nilai dengan ENUM/check constraint. |
| `created_at` | TIMESTAMP | Tidak | `NULL` | Waktu pembuatan record. |
| `updated_at` | TIMESTAMP | Tidak | `NULL` | Waktu perubahan record. |

Indeks gabungan `user_id` dan `created_at` mendukung pengambilan riwayat pesanan per pengguna.

### `order_items`

| Field | Tipe MySQL | Wajib | Default | Keterangan / aturan |
| --- | --- | --- | --- | --- |
| `id` | BIGINT UNSIGNED | Ya | Auto-increment | Primary key item pesanan. |
| `order_id` | BIGINT UNSIGNED | Ya | — | Foreign key ke `orders.id`; item dihapus jika pesanan dihapus. |
| `product_id` | BIGINT UNSIGNED | Tidak | `NULL` | Foreign key ke `products.id`; menjadi `NULL` jika produk dihapus. |
| `product_name` | VARCHAR(255) | Ya | — | Snapshot nama produk saat transaksi. |
| `unit_price` | BIGINT UNSIGNED | Ya | — | Snapshot harga satuan saat transaksi, rupiah bulat. |
| `quantity` | INT UNSIGNED | Ya | — | Jumlah produk pada item pesanan. |
| `line_total` | BIGINT UNSIGNED | Ya | — | Total harga baris item, rupiah bulat. |
| `created_at` | TIMESTAMP | Tidak | `NULL` | Waktu pembuatan record. |
| `updated_at` | TIMESTAMP | Tidak | `NULL` | Waktu perubahan record. |

### `payments`

| Field | Tipe MySQL | Wajib | Default | Keterangan / aturan |
| --- | --- | --- | --- | --- |
| `id` | BIGINT UNSIGNED | Ya | Auto-increment | Primary key pembayaran. |
| `order_id` | BIGINT UNSIGNED | Ya | — | Foreign key ke `orders.id`; unique sehingga maksimal satu record pembayaran per pesanan. Pembayaran dihapus jika pesanan dihapus. |
| `amount` | BIGINT UNSIGNED | Ya | — | Jumlah pembayaran dalam rupiah bulat. |
| `method` | VARCHAR(30) | Tidak | `NULL` | Metode pembayaran. |
| `status` | VARCHAR(20) | Ya | `pending` | Status pembayaran; diindeks. Database belum membatasi nilai dengan ENUM/check constraint. |
| `verified_by` | BIGINT UNSIGNED | Tidak | `NULL` | Foreign key ke `users.id`; menjadi `NULL` jika pengguna verifikator dihapus. |
| `verified_at` | TIMESTAMP | Tidak | `NULL` | Waktu pembayaran diverifikasi. |
| `notes` | TEXT | Tidak | `NULL` | Catatan pembayaran/verifikasi. |
| `created_at` | TIMESTAMP | Tidak | `NULL` | Waktu pembuatan record. |
| `updated_at` | TIMESTAMP | Tidak | `NULL` | Waktu perubahan record. |

## Tabel metadata migration Laravel

Laravel membuat tabel `migrations` saat migration pertama kali dijalankan. Tabel ini menyimpan riwayat migration yang sudah diterapkan, bukan data bisnis.

| Field | Tipe MySQL | Wajib | Keterangan |
| --- | --- | --- | --- |
| `id` | INT UNSIGNED | Ya | Primary key auto-increment. |
| `migration` | VARCHAR(255) | Ya | Nama file migration yang diterapkan. |
| `batch` | INT | Ya | Nomor kelompok migration saat dijalankan. |

## Relasi utama

- `users` 1 — 1 `carts`; `carts` 1 — banyak `cart_items`.
- `products` 1 — banyak `cart_items`; kombinasi keranjang dan produk tidak boleh berulang.
- `users` 1 — banyak `orders`; `orders` 1 — banyak `order_items`.
- `products` 1 — banyak `order_items`, tetapi referensi produk pada item pesanan boleh kosong agar snapshot transaksi tetap ada saat produk dihapus.
- `orders` 1 — 0/1 `payments`; `users` 1 — banyak pembayaran yang diverifikasi melalui `verified_by`.

## Catatan implementasi

- Seluruh kolom harga dan total memakai bilangan rupiah bulat, bukan `FLOAT`/`DOUBLE`.
- Nilai seperti `role`, `status`, dan `method` disimpan sebagai string. Daftar nilai yang diizinkan perlu divalidasi di aplikasi; schema saat ini tidak memasang ENUM atau check constraint.
- Tabel `sessions`, `cache`, dan queue adalah tabel infrastruktur Laravel. Pemakaiannya bergantung pada driver yang diatur di `.env`.
- Tabel `migrations` dikelola oleh Laravel dan tidak didefinisikan sebagai migration aplikasi.
- Definisi kolom didokumentasikan sesuai migration; pengaturan collation/charset mengikuti konfigurasi koneksi MySQL.

## Sumber schema

- Migration akun, sesi, cache, dan queue: `database/migrations/0001_01_01_*.php`
- Migration katalog dan transaksi: `database/migrations/2026_10_18_*.php`
- Spesifikasi produk yang lebih rinci: [`spesifikasi-data-produk.md`](./spesifikasi-data-produk.md)
