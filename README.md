Nama: Kezia Livina
NRP: 5025251207

# 📝 MY TODO LIST

---
## 📌 Deskripsi Proyek
Website Todo List yang dikembangkan lebih lanjut menggunakan **PHP** dan **database MySQL**. Website ini memungkinkan user untuk mengatur daftar kegiatan harian (Personal & Shared), menambahkan lampiran foto, menetapkan due date, serta mengatur notifikasi pengingat.

---
## 🖼️ Tampilan Aplikasi
| Mode Gelap (Dark Mode) |
<img width="1440" height="856" alt="Screenshot 2026-11-02 at 5 39 56 PM" src="https://github.com/user-attachments/assets/9f3364eb-0414-4743-b16a-bdb961e4c337" />

| Mode Terang (Light Mode) |
<img width="1440" height="856" alt="Screenshot 2026-11-02 at 5 40 29 PM" src="https://github.com/user-attachments/assets/dfd37f73-1f81-47a1-9e3b-bdbb8bd7a4a2" />


---
## ✨ Fitur Utama
- **MENU CATEGORY** : Memilih kategori tugas antara **Personal** atau **Shared** melalui sidebar menu.
- **ADD NEW** : Menambahkan daftar tugas baru yang disertai dengan judul, deskripsi, due date, gambar/foto, serta notifikasi waktu
- **EDIT** : Mengubah judul, deskripsi, due date, waktu notifikasi, serta mengganti/menambahkan foto pada daftar tugas yang sudah tersimpan sebelumnya
- **DELETE** : Menghapus daftar tugas yang sudah tidak diperlukan lagi 
- **COMPLETE/ INCOMPLETE** : Menandai tugas yang sudah selesai atau mengembalikannya ke status belum selesai
- **UPLOAD IMAGE** : Insert gambar yang disimpan di foler `uploads/` dan disimpan di database MySQL 
- **SCHEDULED NOTIFICATIONS** : Pengiriman notifikasi pengingat secara otomatis melalui (`sw.js`)
- **MODE** : Fitur untuk memilih mode mana yang ingin digunakan (dark mode/ light mode)
- **TODO DETAIL** : Melihat detail suatu TODO yang ingin kita tinjau
- **DATABASE INTEGRATION (MySQL)** : Seluruh data tugas disimpan secara permanen di database MySQL melalui koneksi PHP (`config.php`), sehingga data tersimpan aman dan tidak hilang saat halaman di-refresh.
- **PROGRESSIVE WEB APP (PWA)** : 
  - **Web App Manifest (`manifest.json`)**: Mengonfigurasi identitas aplikasi, ikon, tema, dan mode *standalone*.
  - **Service Worker (`sw.js`)**: Mengatur caching aset dan penanganan background task.
  - **Custom Install Button**: Tombol untuk menginstall aplikasi ke desktop maupun mobile.

---
## 📁 Struktur File

```
todolist/
├── index.php         # Halaman utama aplikasi, CRUD, dan tampilan
├── config.php        # Konfigurasi koneksi database MySQL
├── menu-bar.php      # Sidebar kategori tugas (Personal, Shared)
├── cantik.css        # Modifikasi tampilan web (warna, tema gelap/terang, layout)
├── interaction.js    # Logika JavaScript interaktif dan Service Worker
├── sw.js             # Service Worker untuk menangani pendaftaran & penjadwalan notifikasi
├── data.sql          # Skrip struktur tabel database dan data dummy
├── uploads/          # Folder penyimpanan berkas foto yang di-upload
├── E05.png           # Ikon app untuk PWA
├── manifest.json     # Konfigurasi PWA
└── hateemel.html     # Referensi antarmuka HTML awal


```

---
## ⚙️ Langkah-langkah

1. Siapkan web server (seperti XAMPP) yang mendukung **PHP** dan **MySQL/MariaDB**.
2. Impor `data.sql` melalui **phpMyAdmin** (`http://localhost/phpmyadmin`):
   - Buat database baru dengan nama `todo_db`.
     <img width="1440" height="331" alt="Screenshot 2026-10-19 at 7 43 32 PM" src="https://github.com/user-attachments/assets/1e749bd7-0afd-434e-a2b9-27ac3b3bc95f" />
   - Pilih database `todo_db`, lalu buka tab **Import** dan pilih file **`data.sql`**.
     <img width="1440" height="820" alt="Screenshot 2026-10-19 at 7 44 17 PM" src="https://github.com/user-attachments/assets/185328b0-d0cf-47da-a7e4-d6c0e15a5fe3" />
   - Klik **Go** untuk membuat tabel `todos` beserta data dummy.
3. Sesuaikan koneksi di `config.php` bila perlu:
   - **Host**: `localhost` 
   - **Database Name**: `todo_db`
   - **User**: `root`
4. Jalankan folder ini melalui Apache/XAMPP (misalnya `http://localhost/todolist/index.php`):

---
## 📸 Lampiran

<img width="1440" height="820" alt="Screenshot 2026-10-19 at 7 46 55 PM" src="https://github.com/user-attachments/assets/98faa495-ea67-4d23-802e-232b4e356375" />

<img width="1440" height="820" alt="Screenshot 2026-10-19 at 7 46 37 PM" src="https://github.com/user-attachments/assets/91f24dbf-b784-4e6c-80d0-b0935a52bcca" />

<img width="1440" height="856" alt="Screenshot 2026-11-02 at 5 45 51 PM" src="https://github.com/user-attachments/assets/641ee545-ff27-4f68-b849-5a2539c7696c" />

<img width="1440" height="900" alt="Screenshot 2026-11-02 at 5 48 40 PM" src="https://github.com/user-attachments/assets/e18c110a-5180-4ce2-9a3e-8804ef8c06fa" />




