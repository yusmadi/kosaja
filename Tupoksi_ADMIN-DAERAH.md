# Sistem Aplikasi KosAja dan Tugas Pokok dan Fungsi (Tupoksi) ADMIN DAERAH
**Berdasarkan Cetak Biru Arsitektur (PRD & Business Blueprint) KosAja**

---

## BAGIAN 1: Gambaran Umum Sistem Aplikasi KosAja

**KosAja** bukan sekadar situs pasang iklan kos atau kontrakan biasa (*listing board* pasif), melainkan **Platform Tata Kelola Ekosistem & Transaksi Sewa Hunian Terpadu** yang berakar pada nilai kepatuhan syariat hunian, transparansi informasi, dan keamanan transaksi finansial (*escrow payment*).

### 1. Nilai & Karakteristik Utama KosAja:
* **Prinsip Syariat & Keamanan Hunian:**
  Menerapkan pemisahan gender secara ketat (*strict gender isolation*):
  * Kos Khusus Putra (Ikhwan)
  * Kos Khusus Putri (Akhwat)
  * Hunian Campur Keluarga / Pasutri Sah (wajib menyertakan bukti identitas sah seperti Buku Nikah / Kartu Keluarga).
* **Hierarki Aset Riil (Anti-Overbooking):**
  Menggunakan model data bertingkat:
  $$\text{Penyedia (Mitra)} \longrightarrow \text{Properti (Gedung)} \longrightarrow \text{Unit (Kamar Fisik)} \longrightarrow \text{Listing (Iklan)}$$
  Setiap 1 iklan mewakili 1 kamar fisik yang nyata, sehingga meniadakan risiko kamar ganda atau *overbooking*.
* **Siklus Transaksi Terintegrasi (*End-to-End*):**
  Mencakup seluruh alur perjalanan sewa:
  $$\text{Pencarian} \to \text{Pengajuan (Application)} \to \text{Persetujuan Pemilik} \to \text{Reservasi Terkunci} \to \text{Pembayaran Gateway} \to \text{Kontrak Digital Otomatis} \to \text{Tagihan Bulanan Berulang} \to \text{Masa Tenggang (Grace Period)}$$
* **Arsitektur Multi-Region & RBAC/ABAC:**
  Platform dirancang berbasis wilayah teritorial (Kota/Kabupaten di seluruh Indonesia) dengan pembatasan hak akses berbasis peran (*Role-Based Access Control*) dan atribut wilayah (*Attribute-Based Access Control*).

---

## BAGIAN 2: Tugas Pokok dan Fungsi (Tupoksi) ADMIN DAERAH

Jika **Super Admin** berperan sebagai regulator pusat, pengelola kebijakan platform global, dan pengawas keuangan sentral, maka **Admin Daerah** adalah **"Kepala Balai Operasional / Komandan Wilayah"**.

Admin Daerah bertugas menjaga integritas, keamanan, ketertiban norma hunian, dan kepuasan pengguna pada wilayah tugasnya (`region_id` tertentu, seperti Sleman, Kota Yogyakarta, Banda Aceh, dll.).

---

### A. TUGAS POKOK
> **Menjalankan operasional harian, memoderasi kelayakan iklan dan legalitas mitra di wilayahnya, mengawal kepatuhan syariat hunian, serta memediasi aduan/sengketa antara penyewa dan pemilik kos agar ekosistem sewa berjalan tertib, aman, dan berkeadilan.**

---

### B. RINCIAN FUNGSI UTAMA (8 Pilar Operasional)

#### 1. Gerbang Kurasi Legalitas Mitra (`/admin/providers` — ADM-02)
* **Fungsi:** Memeriksa keabsahan identitas (foto e-KTP), nomor kontak WhatsApp, dan kecocokan rekening bank mitra pemilik kos baru yang beroperasi di wilayahnya.
* **Kewenangan:** Menyetujui (*Approve*) akun mitra agar bisa aktif secara komersial, atau Menolak (*Reject*) dengan mencantumkan alasan tertulis jika dokumen buram atau tidak valid.

#### 2. Moderasi Listing & Standar Hunian (`/admin/listings` — ADM-04)
* **Fungsi:** Menginspeksi setiap iklan kamar baru sebelum tayang secara publik di platform KosAja.
* **Parameter Pemeriksaan:**
  * Foto kamar layak, sopan, dan representatif (bukan foto palsu/comotan).
  * Kategori gender sesuai aturan (contoh: kos putri tidak boleh campur laki-laki).
  * Ketentuan aturan bersama (jam malam, larangan miras/judi).
* **Kewenangan:** Menyetujui tayang (*Publish*), meminta revisi perbaikan (*Request Changes*), atau menolak listing (*Reject*).

#### 3. Penanganan Laporan Pelanggaran Warga (`/admin/reports` — ADM-06)
* **Fungsi:** Menerima dan menindaklanjuti pengaduan dari penyewa atau masyarakat terkait:
  * Pelanggaran syariat/norma kesusilaan di lingkungan kos.
  * Fasilitas hunian rusak berat yang ditelantarkan pemilik.
  * Indikasi penipuan atau perlakuan tidak adil.
* **Kewenangan:** Melakukan verifikasi lapangan dan menentukan tindakan peringatan.

#### 4. Mediasi Sengketa & Putusan Pengembalian Dana (`/admin/disputes` — ADM-08)
* **Fungsi:** Menjadi wasit netral tingkat pertama saat terjadi konflik antara penyewa dan pemilik kos (misal: penyewa membatalkan sewa karena kamar bocor parah atau kunci tidak diserahkan).
* **Kewenangan:**
  * Menerbitkan putusan mediasi:
    * *Full Refund* (dana dikembalikan penuh ke penyewa).
    * *Partial Refund* (dibagi proporsional).
    * *No Refund* (hak penuh pemilik).
  * Melakukan eskalasi (*Escalate*) ke Super Admin apabila masalah menyangkut perselisihan hukum berat yang tidak selesai di tingkat daerah.

#### 5. Penegakan Disiplin & Sanksi Wilayah (`/admin/restrictions` — ADM-10)
* **Fungsi:** Menjaga wibawa ekosistem dari pihak-pihak yang tidak kooperatif.
* **Kewenangan:**
  * Memberikan surat peringatan sistem (*System Warning*).
  * Membekukan (*Suspend*) sementara listing iklan yang bermasalah.
  * Menonaktifkan akun mitra yang melanggar aturan berat.
  * Mencabut sanksi (*Lift Restriction*) apabila mitra telah memenuhi kewajiban perbaikan.

#### 6. Pengawasan Antrean Kerja Harian (`/admin/dashboard` — ADM-01)
* **Fungsi:** Memantau dasbor antrean terpusat untuk memastikan tidak ada dokumen KYC mitra atau listing iklan yang tertahan melebihi batas waktu (SLA 1x24 jam).

#### 7. Analisis Perkembangan Pasar Lokal (`/admin/regional-reports` — ADM-11)
* **Fungsi:** Menganalisis pertumbuhan okupansi kos, fluktuasi harga pasar sewa di kotanya, dan tren kebutuhan kamar (putra/putri/keluarga) untuk bahan evaluasi bisnis daerah.

#### 8. Akuntabilitas & Rekam Jejak Digital (`/admin/audit` — ADM-12)
* **Fungsi:** Seluruh klik persetujuan, penolakan, penjatuhan sanksi, dan mediasi yang dilakukan Admin Daerah tercatat otomatis secara permanen (*immutable*) dengan stempel waktu dan ID admin untuk mencegah penyalahgunaan wewenang.

#### 9. Verifikasi Operasional Settlement Sewa Mitra (`/admin/settlements` — ADM-09)
* **Fungsi (Maker):** Memeriksa secara faktual kesiapan kamar fisik, serah terima kunci, status check-in penyewa, dan memastikan tidak ada sengketa fasilitas sebelum dana sewa dicairkan.
* **Kewenangan:**
  * Memberikan rekomendasi persetujuan pencairan (*Verify & Recommend Payout*) dengan catatan konfirmasi lapangan.
  * Menahan sementara pencairan (*Hold Payout*) jika ditemukan kendala fasilitas atau aduan sengketa aktif.

---

### C. BATASAN WEWENANG (Bukan Wewenang Admin Daerah)

Untuk menjaga prinsip tata kelola yang bersih (*Good Corporate Governance* & *Maker-Checker Protocol*), Admin Daerah:
1. **Tidak Memiliki Akses Eksekusi Kas Langsung:** Admin Daerah bertindak sebagai **Maker** (verifikator kelayakan operasional), sedangkan eksekusi transfer dana dari rekening penampung (*escrow*) perbankan tetap dipegang oleh Super Admin sebagai **Checker**.
2. **Kebijakan Finansial Global:** Tidak berwenang menentukan tarif komisi platform, memotong biaya sistem, atau mengubah konfigurasi payment gateway.
3. **Manajemen Akun Internal:** Tidak berwenang merekrut atau menghapus akun Admin Daerah lainnya (hak eksklusif Super Admin).
4. **Konfigurasi Wilayah Baru:** Tidak berwenang menambah atau menghapus batas wilayah provinsi/kota di database.
