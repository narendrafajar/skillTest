# Test Case Stafbook Fullstack Developer_Narendra Fajar Pamungkas

## Deskripsi Proyek

Project ini adalah pengembangan sebuah section input produk untuk kebutuhan penjualan di Green Mart.  
Fitur utamanya meliputi:  
- Dynamic List untuk Nama Produk  
- Dynamic List untuk Deskripsi Produk  
- Attachment Section untuk upload gambar produk di setiap kategori

## Tech Stack

- Laravel 12  
- Alpine.js  
- SweetAlert2  

## Fitur Utama

- Tambah maksimal 5 produk secara dinamis  
- Setiap produk dapat memiliki maksimal 3 kategori  
- Upload gambar per kategori dengan validasi format (JPG/JPEG/PNG)  
- Fitur hapus gambar dengan konfirmasi menggunakan SweetAlert2  
- Tombol tambah kategori/produsen otomatis disable saat mencapai batas maksimal  
- Konfirmasi penyimpanan dan pembatalan input produk dengan modal dialog  
- Data produk dikirim sebagai JSON dan diproses di backend Laravel  
- Backend simpan data ke database dengan kode produk dan kategori unik berdasarkan tanggal

## Cara Menjalankan Proyek

1. Clone repository  
2. Jalankan perintah:  
   ```bash
   composer install
   cp .env.example .env
   php artisan key:generate
   php artisan migrate
   php artisan serve
3. Buka browser dan akses http://localhost:8000
4. Gunakan UI untuk menambahkan produk, kategori, dan upload gambar sesuai kebutuhan

## Catatan Tambahan
1. Backend disesuaikan dengan skenario dummy, tidak ada fitur hapus data dari database secara aktual
2. Proyek ini dikerjakan selama 1,5 hari sebagai bagian dari technical skill test di PT. Staffbook Teknologi Asia
3. Implementasi frontend menggunakan Alpine.js untuk responsivitas dan interaksi dinamis tanpa reload halaman
4. Validasi file gambar dilakukan di client-side sebelum upload

## Credit
Dibuat oleh Narendra Fajar Pamungkas, A.Md.Kom untuk technical skill test di PT. Staffbook Teknologi Asia.

Thanks for checking out the project!
If you want to reach out, feel free to contact me.  