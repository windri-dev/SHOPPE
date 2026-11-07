# SHOPPE - CSS Architecture & Style Guide

Direktori ini mengimplementasikan arsitektur CSS modular berstandar industri (*Feature-First & Component-Based*) untuk kemudahan pemeliharaan (*maintainability*), skalabilitas, dan performa tinggi.

---

## 📁 Struktur Direktori

```text
assets/css/
├── variables.css            # Token desain global (warna, font, spacing, radius, z-index)
├── reset.css                # Box-sizing & normalisasi browser dasar
├── components.css           # Master orchestrator (@import seluruh komponen & halaman)
├── style.css                # Layout beranda & utility responsive media queries
├── README.md                # Dokumentasi panduan arsitektur (file ini)
│
├── components/              # Komponen UI Global (Dapat digunakan kembali di semua halaman)
│   ├── base.css             # .container, sistem tombol (.btn, .btn-*), dan badges
│   ├── header.css           # Navigasi utama, logo SHOPPE, dan search overlay dropdown
│   ├── hero.css             # Hero carousel banner & slider controls
│   ├── product-card.css     # Kartu produk, grid, hover actions, dan quick-add
│   ├── drawers.css          # Cart drawer (Shopping Bag) & Mobile nav drawer
│   ├── modals.css           # Quick view popup modal & Toast notification
│   └── footer.css           # Footer navigasi, newsletter form, & copyright
│
└── pages/                   # Styling Spesifik Halaman (Feature-First)
    ├── shop.css             # Katalog belanja, sidebar filter, sorting, pagination
    ├── product.css          # Halaman detail produk, varian, galeri thumbnail, review tab
    ├── cart.css             # Halaman keranjang belanja, kupon, ongkos kirim, responsivitas
    ├── checkout.css         # Formulir checkout billing, ringkasan order, metode bayar
    ├── order-details.css    # Halaman status order received & rincian pesanan
    ├── account.css          # Login/Register form tabs & User dashboard pesanan
    ├── blog.css             # Halaman daftar artikel blog & blog single detail
    ├── contact.css          # Halaman formulir hubungi kami & alamat
    └── static-pages.css     # Our story / about, privacy policy, dan 404 error
```

---

## 🛠️ Panduan Pengembangan

1. **Menambah / Mengubah Token Desain**:
   Edit file [`variables.css`](variables.css). Selalu gunakan CSS variables (misal: `var(--color-black)`, `var(--color-accent)`) dan hindari *hardcoding* warna hex di modul komponen.

2. **Menambah Komponen Baru**:
   - Jika komponen bersifat global (muncul di banyak halaman), tambahkan di folder `components/`.
   - Daftarkan file baru tersebut di [`components.css`](components.css) menggunakan `@import url('components/nama-komponen.css');`.

3. **Menambah Halaman Baru**:
   - Buat file CSS baru di folder `pages/` (misal `pages/faq.css`).
   - Daftarkan di bagian `/* Feature-First Page Styles */` pada [`components.css`](components.css).

---

## 🎨 Token Desain Utama
- **Primary Color**: `#000000` (`--color-black`)
- **Accent Color**: `#A18A68` (`--color-accent`)
- **Background Neutral**: `#FFFFFF` (`--color-white`) / `#F0F0F0` (`--color-gray-100`)
- **Font Family**: DM Sans, sans-serif (`--font-primary`)
