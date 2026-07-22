# LABPSTI

Sistem informasi laboratorium Program Studi Teknologi Informasi — **UNISA Yogyakarta**.

Mengelola inventaris alat, peminjaman barang, penggunaan lab, buletin lab, serta peminjaman & provisioning Virtual Machine (Proxmox).

---

## Fitur

| Modul | Deskripsi |
|--------|-----------|
| **Landing & lab publik** | Halaman beranda, inventaris publik, kabar berita, status host VM |
| **Inventaris** | CRUD barang lab, stok, kategori, riwayat pinjam |
| **Peminjaman alat** | Pengajuan + ACC admin, PDF surat, pengembalian stok |
| **Penggunaan lab** | Catatan pemakaian lab per kategori |
| **Buletin** | Konten lab (mahasiswa ajukan → admin ACC) |
| **Peminjaman VM** | Request OS, review, kredensial, alokasi resource host |
| **Service request VM** | Port, software, storage, reset, dsb. |
| **Proxmox** | Multi-server, sync host, power VM, clone/create |
| **Laporan** | Harian / mingguan / bulanan + export Excel/PDF |
| **User & notifikasi** | Role admin/mahasiswa, notifikasi in-app |

### Role

- **Admin** — full akses (ACC, Proxmox, user, laporan, inventaris)
- **Mahasiswa** — login SSO UNISA / NIM; ajukan pinjam alat, lab usage, VM, buletin

---

## Stack

| Layer | Teknologi |
|--------|-----------|
| Backend | PHP 8.2+, Laravel 12 |
| Frontend | Blade, Tailwind CSS, Alpine.js, Vite |
| Auth | Session + SSO eksternal UNISA (`loginall.php`) + admin lokal |
| DB | MySQL / MariaDB (sqlite opsional dev) |
| PDF / Excel | DomPDF, PhpSpreadsheet |
| Infra VM | Proxmox API, Ansible playbook (opsional) |

---

## Setup lokal

### Prasyarat

- PHP 8.2+ (ext: `mbstring`, `openssl`, `pdo`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`)
- Composer 2
- Node.js 18+ & npm
- MySQL/MariaDB **atau** SQLite

### Instalasi

```bash
git clone <repo-url> LABPSTI
cd LABPSTI

composer install
cp .env.example .env
php artisan key:generate

# sesuaikan DB di .env, lalu:
php artisan migrate
php artisan db:seed          # opsional — admin default & data demo

npm install
npm run build                # atau: npm run dev
php artisan serve
```

Buka `http://localhost:8000`.

### Akun seed (jika pakai seeder)

| Role | Kredensial tipikal |
|------|--------------------|
| Admin | email `@lab.com` + password dari seeder |
| Mahasiswa | lewat SSO / NIM eksternal |

> Ganti password default seeder sebelum dipakai di lingkungan bersama.

---

## Konfigurasi penting (`.env`)

| Variabel | Kegunaan |
|----------|----------|
| `APP_ENV` / `APP_DEBUG` | Production: `production` / `false` |
| `APP_URL` | URL publik (pakai `https://…` di production) |
| `UNISA_AUTH_LOGIN_URL` | Endpoint login eksternal |
| `SIMLITABMAS_*` | DB view mahasiswa (nama dari NIM) |
| `SESSION_ENCRYPT` | Default `true` |
| `SESSION_SECURE_COOKIE` | `true` di HTTPS |
| `PROXMOX_*` | Fallback legacy; kredensial utama di menu **Host & Proxmox** (DB, encrypted) |

Lihat `.env.example` untuk daftar lengkap.

---

## Deploy

Workflow GitHub Actions (`.github/workflows/deploy.yml`) pull + `composer install` + `npm run build` + `migrate` lewat SSH.

Checklist production singkat:

1. `APP_DEBUG=false`, `APP_ENV=production`
2. HTTPS + `SESSION_SECURE_COOKIE=true`
3. Secret di server env / secret manager — **jangan commit `.env`**
4. `php artisan config:cache` & `route:cache` setelah deploy
5. Rotate password Proxmox/DB jika pernah bocor di mesin dev

---

## Keamanan (ringkas)

Sudah diterapkan di codebase:

- Mass assignment ketat (status/approval/kredensial lewat `forceFill` di controller)
- Rate limit login & reset password
- Security headers (CSP, X-Frame, nosniff, HSTS di HTTPS)
- Session encryption default on
- Validasi ketat username/password VM & path API Proxmox
- Ansible lewat `proc_open` + sanitize input (bukan string shell mentah)
- PDF peminjaman di disk private + cek path traversal
- Password Proxmox & VM di DB: cast `encrypted`

Unit check sanitizer: `php vendor/phpunit/phpunit/phpunit --filter SecurityHelpersTest`

---

## Struktur singkat

```
app/
  Http/Controllers/   # web controllers
  Models/             # Eloquent
  Services/           # ProxmoxService, AnsibleService
  Notifications/      # notifikasi in-app
ansible/              # playbook create/destroy VM
resources/views/      # Blade UI
routes/web.php        # rute publik + auth + admin
```

---

## Perintah berguna

```bash
php artisan migrate
php artisan db:seed
php artisan serve
npm run dev              # HMR Vite
npm run build
php artisan test         # butuh driver DB test (sqlite/mysql)
./vendor/bin/pint        # format PHP
```

---

## Lisensi

Kode aplikasi laboratorium UNISA — penggunaan internal sesuai kebijakan institusi.  
Framework Laravel berlisensi [MIT](https://opensource.org/licenses/MIT).
