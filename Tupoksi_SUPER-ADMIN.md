# Sistem Aplikasi KosAja dan Tugas Pokok dan Fungsi (Tupoksi) SUPER ADMIN
**Berdasarkan Cetak Biru Arsitektur (PRD, Business Blueprint & System Implementation) KosAja**

---

## BAGIAN 1: Kedudukan & Filosofi Peran Super Admin

Dalam arsitektur tata kelola platform **KosAja**, **Super Administrator (Super Admin)** menempati posisi puncak sebagai **Regulator Tertinggi, Pemegang Kedaulatan Finansial, dan Pengawas Sistem Skala Nasional (*Ultimate Platform Governor & Central Custodian*)**.

Apabila **Admin Daerah** diibaratkan sebagai **"Komandan Operasional Wilayah"** yang bertugas di garis depan (memverifikasi kamar fisik, memoderasi listing lokal, dan memediasi aduan warga di satu kota/kabupaten), maka **Super Admin** adalah **"Dewan Manajemen & Otoritas Pusat"**. 

Super Admin memegang wewenang atas kebijakan makro (*macro-governance*), pengelolaan kas penampung (*escrow custodian*), pengaturan integrasi sistem, penegakan kepatuhan syariat secara nasional, serta manajemen risiko platform.

---

## BAGIAN 2: Tugas Pokok SUPER ADMIN

> **Menetapkan dan mengendalikan kebijakan platform secara global, mengelola kedaulatan transaksi finansial dan pencairan dana (*escrow & settlements*), mengatur manajemen hak akses personel (*IAM*), mengawasi kepatuhan tata kelola multi-region seluruh Indonesia, serta bertindak sebagai mahkamah arbitrase tertinggi atas eskalasi sengketa regional.**

---

## BAGIAN 3: Rincian Fungsi Utama (12 Pilar Otoritas Global SA-01 s.d SA-12)

Berdasarkan modul arsitektur yang telah diimplementasikan pada aplikasi KosAja:

### 1. Dasbor Eksekutif & Pemantauan KPI Global (`/admin/dashboard` — SA-01)
* **Fungsi:** Mengawasi kesehatan bisnis dan stabilitas ekosistem platform secara nasional secara *real-time*.
* **Cakupan Pengawasan:**
  * Metrik Finansial Makro: *Gross Transaction Volume* (GTV / GMV), total dana sewa berputar, dan penerimaan fee platform.
  * Metrik Okupansi & Kapasitas Nasional: Total kamar terisi vs kosong di seluruh provinsi/kota di Indonesia.
  * Notifikasi Peringatan Eksekutif: Antrean sengketa yang dieskalasi dan antrean permohonan pencairan dana (*settlements*) yang menunggu otorisasi.

### 2. Manajemen Identitas & Hak Akses Personel (`/admin/users` — SA-02)
* **Fungsi:** Mengelola akun internal dan menetapkan batas kewenangan pengguna (*Identity & Access Management / IAM*).
* **Kewenangan Eksklusif:**
  * Merekrut, mengaktifkan, membekukan, atau menghapus akun **Admin Daerah**.
  * Menetapkan batasan wilayah operasional (*Scope Assignment*) bagi Admin Daerah ke `region_id` tertentu (contoh: Sleman, Kota Yogyakarta, Banda Aceh).
  * Mengendalikan mekanisme *Role-Based Access Control* (RBAC) dan *Attribute-Based Access Control* (ABAC) agar tidak terjadi tumpang tindih wewenang antar-wilayah.

### 3. Pengawasan Mutu & Kurasi Legalitas Mitra Nasional (`/admin/providers` — SA-03)
* **Fungsi:** Memantau standar verifikasi identitas (KYC) mitra pemilik properti di seluruh Indonesia.
* **Kewenangan:**
  * Melakukan intervensi atau verifikasi langsung bagi wilayah baru yang belum memiliki Admin Daerah definitif (*blank spot area*).
  * Mengaudit kepatuhan Admin Daerah dalam memeriksa keaslian KTP dan data rekening bank mitra.

### 4. Master Wilayah & Ekspansi Regional (`/admin/regions` — SA-04)
* **Fungsi:** Mengatur topologi dan hierarki teritorial operasional KosAja.
* **Kewenangan:**
  * Menambah, memperbarui, atau menonaktifkan unit wilayah administratif (Provinsi, Kota, dan Kabupaten).
  * Membuka zona ekspansi pasar baru di kota-kota strategis di Indonesia.

### 5. Pengawasan Katalog Listing Nasional (`/admin/listings` — SA-05)
* **Fungsi:** Mengawasi seluruh iklan hunian yang tayang di aplikasi KosAja secara terpusat.
* **Kewenangan:**
  * Melakukan penindakan darurat (*emergency take-down*) terhadap listing yang terbukti melakukan pelanggaran berat (misal: sarang asusila, penipuan terstruktur, atau pelanggaran hukum positif).
  * Menjaga kepatuhan aturan isolasi gender syariat murni di tingkat nasional.

### 6. Pengelolaan Transaksi Finansial Global (`/admin/transactions` — SA-06)
* **Fungsi:** Mengelola buku besar (*general ledger*) dan rekonsiliasi seluruh mutasi pembayaran sewa yang masuk melalui payment gateway (Midtrans, QRIS, Virtual Account bank).
* **Kewenangan:**
  * Memvalidasi status pembayaran transaksi (*Pending*, *Success*, *Expired*, *Failed*).
  * Melakukan sinkronisasi otomatis (*webhook replay*) apabila terjadi kendala teknis antara bank gateway dan aplikasi.

### 7. Otorisasi Pencairan Dana Mitra / Settlement (`/admin/settlements` — SA-07)
* **Fungsi (Checker):** Bertindak sebagai *custodian* (pemegang kunci) rekening penampung platform (*escrow account*) dan pemutus akhir pencairan dana sewa ke mitra.
* **Kewenangan:**
  * Menelaah catatan hasil verifikasi operasional lapangan yang telah direkomendasikan oleh Admin Daerah (**Maker**).
  * Menyetujui dan mengotorisasi transfer (*Authorize & Execute Payout*) dana bersih ke rekening resmi mitra pemilik properti.
  * Menerbitkan nomor referensi transfer perbankan resmi (`payout_reference`, contoh: `PAYOUT-20261026-001`).
  * Memastikan dana hanya dicairkan ke rekening yang telah berstatus *KYC Verified*.
  * Otorisasi langsung (*bypass*) secara transparan apabila properti berada di wilayah yang belum memiliki Admin Daerah definitif.

### 8. Eksekusi Pengembalian Dana Konsumen / Refunds (`/admin/refunds` — SA-08)
* **Fungsi:** Mengendalikan pelepasan dana penampung untuk pengembalian hak penyewa (*consumer protection*).
* **Kewenangan:**
  * Mengeksekusi transfer *refund* (baik *Full Refund* maupun *Partial Refund*) ke rekening penyewa berdasarkan amar putusan mediasi sengketa yang telah berkekuatan final.

### 9. Arbitrase Tertinggi & Eskalasi Sengketa (`/admin/disputes` — SA-09)
* **Fungsi:** Bertindak sebagai **Mahkamah Banding Terakhir** atas perselisihan antara penyewa dan pemilik kos yang tidak dapat didamaikan oleh Admin Daerah.
* **Kewenangan:**
  * Mengambil alih sengketa pelik yang berpotensi implikasi hukum.
  * Menerbitkan putusan arbitrase yang mengikat secara mutlak (*final and binding*) bagi kedua belah pihak.

### 10. Kebijakan Monetisasi & Paket Listing (`/admin/monetization` — SA-10)
* **Fungsi:** Merumuskan dan mengonfigurasi model bisnis komersial platform KosAja.
* **Kewenangan:**
  * Menetapkan durasi masa uji coba gratis bagi mitra baru (*default: 30 hari atau hingga transaksi pertama*).
  * Mengatur kuota gratis kamar (*default: 5 unit listing pertama*).
  * Menentukan besaran komisi platform (*Platform Fee / Service Fee*) dan skema paket langganan berbayar (*Subscription Tier*).

### 11. Pengaturan Konfigurasi Platform & SLA (`/admin/configuration` — SA-11)
* **Fungsi:** Mengatur parameter operasional dan automasi sistem latar (*background worker*).
* **Kewenangan:**
  * Menetapkan masa berlaku pembayaran reservasi (*Timer Kedaluwarsa 24 Jam*).
  * Menentukan batas masa tenggang sewa (*Grace Period 3 Hari*) sebelum status kontrak berubah menjadi *Overdue*.
  * Mengelola kredensial API payment gateway, gateway notifikasi WhatsApp resmi, dan parameter teknis aplikasi.

### 12. Audit Forensik & Kepatuhan Global (`/admin/audit` — SA-12)
* **Fungsi:** Menjamin transparansi penuh dan mencegah kecurangan (*fraud*) internal.
* **Kewenangan:**
  * Memeriksa log jejak digital (*immutable audit trail*) atas setiap aksi administratif dan finansial yang dilakukan oleh seluruh pengguna (Super Admin lain, Admin Daerah, Mitra, dan Penyewa).
  * Melakukan investigasi forensik digital jika terdeteksi anomali transaksi atau pelanggaran integritas.

---

## BAGIAN 4: Matriks Diferensiasi Wewenang: Super Admin vs Admin Daerah

| Parameter Otoritas | Super Admin | Admin Daerah |
| :--- | :---: | :---: |
| **Cakupan Wilayah (Scope)** | Nasional (Seluruh Indonesia) | 1 Wilayah Tugas Terbatas (`region_id`) |
| **Penerimaan Personel (IAM)** | Mengangkat & Mengatur Admin Daerah | Tidak Memiliki Akses |
| **Verifikasi Fisik & KYC Mitra** | Pengawasan Global / Intervensi Khusus | Pelaksana Operasional Utama di Daerah |
| **Moderasi Iklan & Standar Kamar** | Pengawasan Mutu & Takedown Darurat | Kurator Harian Utama (Publish/Revisi) |
| **Mediasi Sengketa Sewa** | Tingkat Banding / Eskalasi Akhir | Tingkat Pertama (Musyawarah & Mediasi) |
| **Penetapan Biaya Komisi & Paket** | Otoritas Penuh & Mutlak | Tidak Memiliki Akses |
| **Akses Rekening Penampung (*Escrow*)** | Otoritas Penuh Pencairan & Refund | Tidak Memiliki Akses Langsung |
| **Konfigurasi Master Wilayah & SLA** | Berwenang Penuh | Tidak Memiliki Akses |

---

## BAGIAN 5: Prinsip Tata Kelola Pemisahan Tugas (*Segregation of Duties*)

Pemisahan peran antara Super Admin dan Admin Daerah didesain untuk menegakkan prinsip **Good Corporate Governance (GCG)**:
1. **Mencegah Fraud & Kolusi:** Admin Daerah yang berinteraksi langsung dengan mitra tidak memegang kunci brankas transfer uang kas (*settlements*), sehingga risiko pembuatan properti fiktif untuk pencairan dana dapat dicegah.
2. **Efisiensi & Skalabilitas:** Super Admin tidak terbebani urusan teknis memeriksa foto kamar satu per satu, sehingga dapat fokus pada ekspansi pasar nasional, integrasi finansial, dan stabilitas infrastruktur sistem.
