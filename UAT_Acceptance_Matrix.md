# Matriks Penerimaan Pengguna (User Acceptance Testing / UAT) — KosAja MVP

Dokumen ini merangkum skenario pengujian fungsional dan kriteria penerimaan (*Acceptance Criteria*) untuk rilis MVP KosAja yang telah diverifikasi secara menyeluruh bersama pemangku kepentingan (*Stakeholders*).

Tanggal Pengujian: 9 Oktober 2026  
Status Kelulusan: **100% LULUS (20 dari 20 Kriteria Terpenuhi)**

---

## 1. Persona Penyewa (Tenant Acceptance)

| ID UAT | Fitur & Skenario | Kriteria Penerimaan (Acceptance Criteria) | Status |
|---|---|---|:---:|
| **UAT-TEN-01** | Pencarian & Filter Syariat | Listing dapat dicari berdasarkan kata kunci, wilayah, dan difilter murni isolasi gender (`putra`, `putri`, `campur_keluarga`). Listing non-aktif atau draf tidak tampil. | **PASS** |
| **UAT-TEN-02** | Gender Separation Guard | Sistem menolak secara otomatis calon penyewa laki-laki yang mencoba mengajukan sewa di kos khusus putri (kepatuhan syariat). | **PASS** |
| **UAT-TEN-03** | Pengajuan Sewa (Application) | Formulir pengajuan sewa menyimpan data durasi, jadwal pembayaran, status pernikahan (lajang/pasutri), dan catatan permohonan. | **PASS** |
| **UAT-TEN-04** | Checkout & Kalkulasi Gateway | Kalkulasi harga transparan menjumlahkan Biaya Sewa + Biaya Layanan Gateway Digital (Rp 4.500) tanpa biaya tersembunyi. | **PASS** |
| **UAT-TEN-05** | Kontrak Sewa Digital | Kontrak digital otomatis diterbitkan setelah pembayaran sukses, memuat pasal hak & kewajiban resmi antara pemilik dan penyewa. | **PASS** |
| **UAT-TEN-06** | Dasbor Tagihan Aktif | Penyewa dapat melihat jadwal tagihan sewa berulang, countdown masa tenggang (*Grace Period*), dan tombol bayar instan (BSI VA/QRIS). | **PASS** |
| **UAT-TEN-07** | Pelaporan Dispute & Komplain | Penyewa dapat melaporkan kendala fasilitas kamar langsung dari dasbor dengan status investigasi yang transparan. | **PASS** |

---

## 2. Persona Pemilik / Pengelola (Provider Acceptance)

| ID UAT | Fitur & Skenario | Kriteria Penerimaan (Acceptance Criteria) | Status |
|---|---|---|:---:|
| **UAT-PROV-01** | Onboarding KYC & Rekening | Mitra dapat melengkapi profil identitas, foto KTP, dan rekening bank settlement (BSI/BCA) dengan status verifikasi admin. | **PASS** |
| **UAT-PROV-02** | Manajemen Properti Fisik | Pemilik dapat mendaftarkan gedung kos, alamat, target gender, aturan jam malam, dan fasilitas gedung. | **PASS** |
| **UAT-PROV-03** | Manajemen Unit Kamar | Pemilik dapat menambahkan kamar fisik, menetapkan nomor/nama kamar, lantai, ukuran, dan status hunian. | **PASS** |
| **UAT-PROV-04** | Aturan 1 Unit = 1 Listing | Iklan dipublikasikan berbasis kamar individual guna mencegah duplikasi atau *overbooking*. | **PASS** |
| **UAT-PROV-05** | Review Pengajuan Masuk | Pemilik dapat menyetujui (*Approve*) atau menolak (*Reject*) pengajuan sewa. Saat disetujui, kamar otomatis terkunci (*Reserved*). | **PASS** |
| **UAT-PROV-06** | Dasbor Okupansi & Kapasitas | Pemilik dapat memantau rasio okupansi kamar (Terisi vs Kosong) serta sisa kuota paket listing platform (5 listing Free Tier). | **PASS** |

---

## 3. Persona Administrator Backoffice

| ID UAT | Fitur & Skenario | Kriteria Penerimaan (Acceptance Criteria) | Status |
|---|---|---|:---:|
| **UAT-ADM-01** | IAM & Kontrol Akses (RBAC) | Hak akses terisolasi secara ketat antara `super_admin`, `admin_daerah`, `provider`, dan `tenant`. | **PASS** |
| **UAT-ADM-02** | Moderasi Listing Iklan | Admin berwenang menyetujui, menolak dengan catatan perbaikan, atau membekukan (*suspend*) iklan yang melanggar aturan. | **PASS** |
| **UAT-ADM-03** | Mediasi Dispute & Refund | Admin dapat menginvestigasi aduan dan menerbitkan putusan *Full Refund*, *Partial Refund*, atau *No Refund* dengan pencatatan keuangan akurat. | **PASS** |
| **UAT-ADM-04** | Immutable Audit Trail | Seluruh tindakan finansial, perubahan status kontrak, dan persetujuan listing tercatat dalam log audit yang tidak dapat dihapus/diubah. | **PASS** |

---

## 4. Sistem & Automasi Pekerja Latar (Worker & Automation)

| ID UAT | Fitur & Skenario | Kriteria Penerimaan (Acceptance Criteria) | Status |
|---|---|---|:---:|
| **UAT-SYS-01** | Timer Kedaluwarsa Reservasi | Unit kamar otomatis dilepaskan kembali ke status `AVAILABLE` jika pembayaran tidak diselesaikan dalam jendela kedaluwarsa 24 jam. | **PASS** |
| **UAT-SYS-02** | Grace Period & Overdue Worker | Tagihan yang melewati jatuh tempo diberi masa tenggang 3 hari (*Grace Period*), kemudian bertransisi otomatis ke `OVERDUE` pada hari ke-4. | **PASS** |
| **UAT-SYS-03** | Notifikasi WhatsApp Gateway | Pesan notifikasi terkirim otomatis saat pengajuan disetujui, pembayaran lunas, pengingat H-3 sebelum jatuh tempo, dan putusan dispute. | **PASS** |

---

## 5. Kesimpulan Stakeholder Sign-Off

Berdasarkan hasil pengujian otomatis dan simulasi alur persona pengguna pada lingkungan staging:
- **Tingkat Kelulusan**: **100% (20/20 skenario PASS)**.
- **Rekomendasi**: Seluruh fitur fungsional MVP KosAja dinyatakan **DITERIMA (ACCEPTED)** dan memenuhi **Definition of Done (DoD)** untuk dilanjutkan ke tahap Production Pilot.
