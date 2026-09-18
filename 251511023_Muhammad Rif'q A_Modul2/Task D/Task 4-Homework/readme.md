# Interactive Profile Card

Interactive Profile Card adalah sebuah aplikasi web sederhana berbasis HTML, CSS, dan Vanilla JavaScript yang menampilkan kartu profil pengguna. Aplikasi ini mendemonstrasikan pemuatan data secara asinkron (AJAX) dari file JSON lokal, manipulasi DOM, dan penanganan state (status).

## 🚀 Fitur Utama

- **Asynchronous Data Fetching:** Memuat data profil (nama, gelar, bio, dan keterampilan) dari file `profile.json` secara asinkron menggunakan Fetch API.
- **State Management:** Memiliki tampilan antarmuka yang adaptif untuk berbagai kondisi:
  - *Loading*: Menampilkan pesan saat data sedang dimuat.
  - *Error*: Menampilkan pesan error dan tombol "Coba Lagi" jika data gagal dimuat.
  - *Empty*: Menangani kondisi jika data JSON kosong.
  - *Content*: Menampilkan kartu profil jika data berhasil dimuat.
- **Dark/Light Mode:** Fitur ganti tema dinamis menggunakan CSS Variables.
- **Interactive Details:** Tombol "Lihat Detail" untuk memunculkan/menyembunyikan bio, dilengkapi dengan atribut aksesibilitas `aria-expanded`.
- **Skill Management (CRUD sederhana pada DOM):**
  - Menambah keterampilan baru melalui form input.
  - Validasi input (mencegah input kosong dan mencegah penambahan keterampilan yang sudah ada/duplikat).
  - Menghapus keterampilan dari daftar.

## 🛠️ Teknologi yang Digunakan

- HTML5
- CSS3 (Custom Properties / CSS Variables)
- Vanilla JavaScript (ES6+, Async/Await)

## 📁 Struktur File

- `index.html`: Kerangka utama halaman web.
- `style.css`: Gaya tata letak dan warna, termasuk pengaturan tema gelap/terang.
- `app.js`: Logika utama aplikasi, menangani interaksi pengguna dan pemuatan data.
- `profile.json`: Sumber data lokal (Database tiruan).

## 💻 Cara Menjalankan Proyek

**PENTING:** Karena proyek ini menggunakan fungsi `fetch()` untuk mengambil file `profile.json` lokal, Anda **tidak bisa** menjalankannya hanya dengan mengklik ganda file `index.html` (protokol `file:///`). Browser modern akan memblokirnya karena alasan keamanan (CORS policy). 

Anda harus menjalankannya melalui **Local Web Server**. Berikut adalah beberapa cara mudah untuk melakukannya:

### Opsi 1: Menggunakan Visual Studio Code (Rekomendasi)
1. Buka folder proyek ini di Visual Studio Code.
2. Pastikan Anda sudah menginstal ekstensi **Live Server** (oleh Ritwick Dey).
3. Klik kanan pada file `index.html`.
4. Pilih **"Open with Live Server"**.
5. Browser default Anda akan terbuka otomatis di alamat `http://127.0.0.1:5500`.

### Opsi 2: Menggunakan Python (Terminal/Command Prompt)
Jika komputer Anda sudah terinstal Python, Anda bisa menggunakan server bawaannya:
1. Buka terminal atau command prompt.
2. Arahkan direktori (`cd`) ke folder proyek ini.
3. Jalankan perintah berikut:
   - Python 3: `python -m http.server 8000`
   - Python 2: `python -m SimpleHTTPServer 8000`
4. Buka browser dan kunjungi `http://localhost:8000`.

### Opsi 3: Menggunakan Node.js (http-server)
Jika Anda memiliki Node.js dan npm terinstal:
1. Buka terminal di folder proyek ini.
2. Jalankan perintah: `npx http-server`
3. Buka URL yang muncul di terminal (biasanya `http://localhost:8080`) pada browser Anda.

## 👤 Penulis Data

Data profil bawaan pada JSON diatur untuk:
**Muhammad Rif'q A** - *Frontend Web Developer & IOT Programmer*