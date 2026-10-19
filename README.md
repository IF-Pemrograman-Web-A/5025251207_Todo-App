Nama: Kezia Livina
NRP: 5025251207

# 📝 MY TODO LIST

---
## 📌 Deskripsi Proyek
Website Todo List yang dikembangkan lebih lanjut menggunakan **PHP** dan **database MySQL**. Website ini memungkinkan user untuk mengatur daftar kegiatan harian (Personal & Shared), menambahkan lampiran foto, menetapkan due date, serta mengatur notifikasi pengingat.

---
## 🖼️ Tampilan Aplikasi
| Mode Gelap (Dark Mode) |


| Mode Terang (Light Mode) |


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
my-todo-list/
├── hateemel.html     # Halaman utama web
├── cantik.css      # Modifikasi tampilan web (warna, hiasan)
├── interaction.js     # Logika JavaScript & penerapan DOM
├── sw.js             # Service Worker untuk menangani pendaftaran & penjadwalan notifikasi
└── README.md      # Dokumentasi proyek

```
