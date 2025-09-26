# 🚀 AspiraX

**AspiraX** adalah sebuah platform **aspirasi publik berbasis Web3 dan AI** yang menjawab tantangan dalam menjembatani suara masyarakat dengan proses pembuatan kebijakan pemerintah. Platform ini dirancang bukan hanya sebagai wadah penampung aspirasi publik, tetapi juga sebagai **instrumen strategis** untuk mewujudkan tata kelola pemerintahan yang lebih transparan, partisipatif, dan responsif.

AspiraX memiliki **positioning solutif** dengan mengadopsi teknologi **autentikasi pseudo-anonymous blockchain**, sehingga pengguna tidak perlu khawatir mengenai data sensitif. Selain itu, inovasi AspiraX terletak pada **kombinasi AI untuk edukasi + sistem gamifikasi** yang mendorong partisipasi, motivasi, dan keterlibatan masyarakat dalam menyampaikan aspirasi.

Berbeda dengan platform laporan publik (seperti **SP4N Lapor**) atau media sosial umum (seperti **Twitter (X)** dan **Instagram**) yang masih mengharuskan pengguna menyerahkan data pribadi, AspiraX hadir sebagai solusi **aman, privat, dan terdesentralisasi** dengan **login via wallet Web3 (Metamask)**. Dengan teknologi ini, pengguna dapat berpartisipasi secara **anonim** melalui identitas yang dilindungi kriptografi, serta memiliki kendali penuh atas data mereka.

Secara strategis, AspiraX berkontribusi terhadap:

-   **Pemberdayaan aspirasi & suara rakyat**
-   **Peningkatan good governance**
-   **Penguatan sistem pengambilan keputusan berbasis data publik**

AspiraX bukan hanya solusi jangka pendek, tetapi juga membangun **ekosistem digital berkelanjutan** melalui umpan balik masyarakat, integrasi teknologi terbuka, serta dukungan data yang dapat dimanfaatkan untuk mendorong pengambilan kebijakan yang lebih inklusif dan responsif.

---

## ✨ Fitur Utama

-   🔑 **Login Web3 (Metamask)** → User masuk menggunakan wallet Metamask, menjaga privasi & identitas.
-   🤖 **Filter AI Anti-Kata Kasar** → Postingan akan difilter otomatis agar tetap sehat & aman.
-   🎮 **Gamifikasi** → Posting & komentar akan menambah poin. Semakin aktif, semakin "melek" terhadap isu-isu sosial, politik, dan lainnya.
-   🧩 **Berbasis Laravel** → Framework modern yang kuat, elegan, dan mudah dikembangkan.

---

## 🛠️ Teknologi yang Digunakan

-   [Laravel](https://laravel.com/) (Backend utama)
-   [Metamask](https://metamask.io/) (Web3 Authentication)
-   [AI Toxic Filter](https://huggingface.co/) (Model AI untuk filter kata kasar)
-   MySQL / PostgreSQL (Database)
-   TailwindCSS + Alpine.js (Frontend cepat & clean)

---

## 📦 Cara Instalasi

Ikuti langkah berikut untuk menjalankan AspiraX di lokal:

### 1. Clone Repository

```bash
git clone https://github.com/OhanaSama34/aspiraX.git
cd aspiraX
```

### 2. Install Dependency

```bash
composer install
npm install && npm run dev
```

### 3. Konfigurasi Environment

Salin file .env.example menjadi .env:

```bash
cp .env.example .env
```

Lalu sesuaikan:

-   Database (DB_CONNECTION, DB_DATABASE, DB_USERNAME, DB_PASSWORD)
-   App Key:

```bash
php artisan key:generate
```

### 4. Migrasi & Seed Database

```bash
php artisan migrate
```

### 5. Jalankan Server

```bash
php artisan serve
```

Akses di: http://localhost:8000🚀

## 🔐 Login dengan Metamask

1. Pastikan browser sudah terinstall Metamask extension.
2. Klik Login with Metamask di halaman utama.
3. Hubungkan wallet Anda → Selesai!

## 🎯 Sistem Gamifikasi

-   Post → +10 poin
-   Komentar → +5 poin
-   Semakin banyak poin = semakin tinggi level "awareness" user.

## 🤝 Kontribusi

Kami terbuka untuk kontribusi.
Silakan fork repo ini, buat branch baru, lalu pull request.
