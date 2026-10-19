Nama: Kezia Livina
NRP: 5025251207

# 📝 MY TODO LIST

---
## 📌 Deskripsi Proyek
Website Todo List yang dikembangkan lebih lanjut menggunakan **PHP** dan **database MySQL**. Website ini memungkinkan user untuk mengatur daftar kegiatan harian (Personal & Shared), menambahkan lampiran foto, menetapkan due date, serta mengatur notifikasi pengingat.

---
## 🖼️ Tampilan Aplikasi
| Mode Gelap (Dark Mode) |
<img width="1440" height="820" alt="Screenshot 2026-10-19 at 7 26 15 PM" src="https://github.com/user-attachments/assets/847a6105-6d88-490b-8ca1-6ae7cbd6a8aa" />


| Mode Terang (Light Mode) |
<img width="1440" height="818" alt="Screenshot 2026-10-19 at 7 25 04 PM" src="https://github.com/user-attachments/assets/73bfdfee-55f6-4949-a452-54c08481ebf2" />


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
└── hateemel.html     # Referensi antarmuka HTML awal

```

---
##  Preview 
