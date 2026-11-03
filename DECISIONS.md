# Keputusan Teknis (Technical Decisions)

## A. Bagaimana mencegah stok minus saat dua order masuk bersamaan?

Saya menggunakan database transaction dan row-level locking untuk mencegah stok produk menjadi minus ketika beberapa order dibuat secara bersamaan.

Pada fungsi `store()` di `OrderController.php`, saya menggunakan `DB::transaction()` untuk memastikan seluruh proses pembuatan order berjalan sebagai satu kesatuan. Saya juga menggunakan `lockForUpdate()` ketika mengambil data produk dari database.

```php
$products = Product::whereIn('id', $productIds)
    ->orderBy('id')
    ->lockForUpdate()
    ->get()
    ->keyBy('id');
```

Dengan `lockForUpdate()`, baris produk yang sedang diproses akan dikunci sampai transaksi selesai. Jika ada request lain yang ingin mengubah stok produk yang sama, request tersebut harus menunggu transaksi sebelumnya selesai.

Setelah mendapatkan data produk, aplikasi memeriksa apakah stok mencukupi sebelum membuat order dan mengurangi stok.

Jika stok tidak mencukupi, aplikasi akan melempar `ValidationException` sehingga seluruh perubahan dalam transaksi dibatalkan melalui rollback.

Dengan cara ini, dua order tidak dapat menggunakan stok tersedia yang sama secara bersamaan hingga menyebabkan stok menjadi minus.

## B. Mengapa struktur folder dan lapisan kode dibuat seperti ini, dan apa yang akan diubah jika aplikasi membesar?

Saya menggunakan struktur MVC bawaan Laravel agar setiap bagian aplikasi memiliki tanggung jawab yang jelas dan lebih mudah dikelola.

Pembagian kode yang digunakan saat ini:

- **Routes (`routes/api.php`):** Mendefinisikan endpoint API dan middleware yang digunakan.
- **Controllers (`app/Http/Controllers`):** Menangani request, validasi, dan mengatur alur proses aplikasi.
- **Models (`app/Models`):** Merepresentasikan tabel database dan mengatur relasi antar model.
- **Migrations (`database/migrations`):** Mengelola struktur tabel database.
- **Seeders (`database/seeders`):** Menyediakan data awal untuk kebutuhan pengembangan dan testing.

Menurut saya, struktur ini sudah cukup untuk ukuran aplikasi saat ini dan tidak membuat kode terlalu kompleks.

Jika aplikasi semakin besar, saya akan memisahkan business logic dari controller ke dalam service class, menggunakan Form Request untuk validasi, API Resource untuk menyeragamkan format response, serta menambahkan automated testing untuk proses bisnis yang penting.

## C. Satu bug atau masalah yang ditemui saat pengerjaan dan bagaimana menemukannya.

Salah satu masalah yang saya perhatikan saat mengerjakan fitur pembuatan order adalah potensi terjadinya race condition ketika beberapa request membuat order untuk produk yang sama secara bersamaan.

Jika aplikasi hanya melakukan pengecekan stok kemudian menguranginya tanpa penguncian database, dua request bisa saja membaca jumlah stok yang sama sebelum salah satunya melakukan update.

Saya mengidentifikasi potensi masalah ini ketika meninjau alur pengecekan dan pengurangan stok pada fungsi `store()` di `OrderController.php`.

Untuk mengatasinya, saya menggunakan `lockForUpdate()` di dalam `DB::transaction()` agar proses pengecekan dan pengurangan stok berjalan secara aman terhadap request lain.

Saya juga menguji kondisi ketika jumlah pesanan melebihi stok yang tersedia untuk memastikan order tidak dibuat dan stok tidak berkurang ketika validasi gagal.

## D. Bagian yang belum selesai atau masih lemah, serta langkah selanjutnya.

Fitur utama seperti pengelolaan produk, pelanggan, pembuatan order, dan riwayat order sudah diimplementasikan. Namun, masih ada beberapa bagian yang dapat ditingkatkan:

1. **Automated Testing:** Menambah test untuk proses pembuatan order, validasi stok, rollback transaksi, dan pengambilan detail order.
2. **Struktur Kode:** Memisahkan business logic yang semakin kompleks ke dalam service class agar controller tidak terlalu banyak menangani proses bisnis.
3. **Error Handling:** Memastikan seluruh endpoint memiliki format response error yang konsisten.

Langkah selanjutnya yang menjadi prioritas saya adalah menambah automated testing, terutama untuk memastikan konsistensi stok dan mekanisme rollback transaksi karena keduanya merupakan bagian penting dalam proses pembuatan order.
