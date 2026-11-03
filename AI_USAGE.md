# AI Usage Disclosure

## 1. AI Tools Used

Saya menggunakan **ChatGPT (OpenAI)** sebagai alat bantu selama proses pengerjaan technical test.

AI digunakan untuk membantu:

- Memahami kebutuhan dan alur implementasi fitur.
- Mendiskusikan pendekatan teknis menggunakan Laravel dan Vue.js.
- Membantu mengidentifikasi potensi masalah pada proses transaksi database.
- Meninjau kode dan membantu proses debugging.
- Memberikan saran terkait struktur kode dan penanganan error.

AI digunakan sebagai alat bantu, bukan sebagai pengganti pemahaman dan pengambilan keputusan teknis selama pengerjaan.

## 2. Main Prompts Used

Berikut beberapa prompt utama yang digunakan selama pengerjaan:

**Prompt 1: Penanganan stok bersamaan**

> Bagaimana cara mencegah stok minus saat dua order masuk bersamaan menggunakan Laravel DB transaction dan lockForUpdate()?

**Prompt 2: Implementasi riwayat order**

> Riwayat order. GET /api/orders dengan filter status dan rentang tanggal, serta GET /api/orders/{id} beserta item-nya.

**Prompt 3: Implement Tampilan Product dan Customer**

> Tampilan table, create dan update pada Product dan tampilan pada table customer

**Prompt 4: Dokumentasi keputusan teknis**

> DECISIONS.md — jawab dengan kata-kata Anda sendiri dan sebutkan file atau fungsi terkait:
> a. Bagaimana Anda mencegah stok minus saat dua order masuk bersamaan?

## 3. Code Written or Modified Independently

Saya menulis, mengintegrasikan, dan menyesuaikan kode sendiri berdasarkan kebutuhan aplikasi. Beberapa bagian yang saya kerjakan meliputi:

- **OrderController:** Implementasi proses pembuatan order, validasi request, perhitungan subtotal dan pajak, pengecekan stok, penyimpanan order, serta pengurangan stok.
- **Database Transaction:** Implementasi `DB::transaction()` dan `lockForUpdate()` untuk menjaga konsistensi stok.
- **Frontend Vue:** Implementasi halaman pengelolaan produk, pelanggan, dan riwayat order serta integrasi dengan backend API.
- **Debugging:** Memeriksa error, menelusuri penyebab masalah, dan melakukan penyesuaian kode.

Saran dan contoh kode dari AI saya pelajari, evaluasi, dan sesuaikan kembali dengan struktur project serta kebutuhan aplikasi sebelum digunakan.

## 4. Verification

Saya melakukan pemeriksaan terhadap implementasi dengan menjalankan aplikasi secara lokal, menguji endpoint API menggunakan Postman, dan memeriksa hasil proses melalui database.

AI-generated suggestions were reviewed and adapted before being incorporated into the project.
