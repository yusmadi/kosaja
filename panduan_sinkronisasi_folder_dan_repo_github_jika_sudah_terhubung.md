# Panduan Sinkronisasi Folder Lokal dengan Repositori GitHub

Panduan ini digunakan jika folder proyek lokal Anda sudah terhubung (*linked*) dengan remote repository GitHub (contoh: branch `main` pada `origin`).

---

## Skenario 1: Mengirim Perubahan Lokal ke GitHub (*Push*)

Gunakan langkah ini jika Anda telah membuat perubahan, menambah, atau mengedit file di komputer lokal dan ingin mengunggahnya ke GitHub:

1. **Buka Terminal / PowerShell** di direktori proyek:
   ```powershell
   cd d:\vibe-coding\KosAja
   ```

2. **Periksa status file yang berubah:**
   ```powershell
   git status
   ```
   *Perintah ini akan menampilkan daftar file yang baru ditambahkan (untracked), dimodifikasi (modified), atau dihapus (deleted).*

3. **Tambahkan perubahan ke Staging Area:**
   ```powershell
   git add .
   ```
   *(Tanda titik `.` menandakan semua file yang berubah akan dimasukkan. Jika hanya ingin file tertentu, gunakan `git add nama_file`).*

4. **Buat commit beserta catatan/pesan:**
   ```powershell
   git commit -m "Deskripsi ringkas perubahan yang dilakukan"
   ```

5. **Kirim perubahan ke GitHub:**
   ```powershell
   git push origin main
   ```

---

## Skenario 2: Mengambil Perubahan Terbaru dari GitHub ke Komputer Lokal (*Pull*)

Gunakan langkah ini jika ada perubahan baru di GitHub (misalnya pengeditan langsung via web browser GitHub atau kolaborasi tim) dan Anda ingin memperbarui folder lokal:

1. **Ambil dan gabungkan perubahan terbaru:**
   ```powershell
   git pull origin main
   ```

---

## Skenario 3: Alur Lengkap Sinkronisasi Rutin (Direkomendasikan)

Untuk menghindari konflik (*merge conflict*), biasakan menggunakan urutan alur kerja berikut:

```powershell
# 1. Selalu tarik perubahan terbaru dari GitHub sebelum mulai bekerja
git pull origin main

# 2. Lakukan koding / perubahan file di komputer Anda...

# 3. Masukkan seluruh perubahan ke staging
git add .

# 4. Simpan ke riwayat commit
git commit -m "Update fitur atau perbaikan terbaru"

# 5. Unggah ke GitHub
git push origin main
```

---

## Tips & Troubleshooting

- **Memeriksa Konfigurasi Remote Saat Ini:**
  ```powershell
  git remote -v
  ```
- **Melihat Riwayat Commit Terakhir:**
  ```powershell
  git log --oneline -n 5
  ```
- **File Sensitif / Kredensial:**
  Pastikan file konfigurasi lokal seperti `.env`, folder kredensial, cache, atau dependensi tidak terunggah sembarangan ke repositori publik. Pastikan file-file tersebut sudah didaftarkan di dalam `.gitignore`.
- **Membatalkan Staging Sebelum Commit:**
  Jika tidak sengaja mengetik `git add .`, Anda bisa membatalkannya dengan:
  ```powershell
  git restore --staged .
  ```
