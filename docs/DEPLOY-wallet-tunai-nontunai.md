# Deploy — Penyederhanaan Dompet (Tunai & Non Tunai)

Panduan deploy ke **produksi** untuk server **tanpa CLI/terminal** (cPanel).
Perubahan sudah diuji di lokal dan di DB terpisah: 18 → 14 dompet, konservasi
saldo tetap, 0 duplikat, 0 referensi rusak.

---

## 1. Berkas yang berubah

**Kode (13 file)**
- `app/Controllers/Outlet.php`
- `app/Controllers/CashMan.php`
- `app/Controllers/CashExp.php`
- `app/Controllers/Trxother.php`
- `app/Controllers/DailyReport.php`
- `app/Controllers/Export.php`
- `app/Controllers/Pay.php`
- `app/Models/CashModel.php`
- `app/Views/cashman.php`
- `app/Views/cashexp.php`
- `app/Views/cashmove.php`
- `app/Language/id/Global.php`
- `app/Language/en/Global.php`

**SQL migrasi**
- `app/Database/wallet-tunai-nontunai.sql`

---

## 2. Backup database (WAJIB)

cPanel → **phpMyAdmin** → pilih database → tab **Export** → **Go**.
Simpan file `.sql` hasil export.

> `ALTER TABLE` otomatis commit (tidak bisa dibungkus 1 transaksi),
> jadi backup adalah jaring pengaman utama.

---

## 3. Deploy kode lewat tombol cPanel

1. cPanel → **Git™ Version Control**.
2. Pilih repository 58 Vapehouse.
3. Klik **Pull or Deploy** → **Update from Remote** (ambil commit terbaru dari `main`).
4. Klik **Deploy HEAD Commit**.

> Branch yang dipakai cPanel harus `main` (sama dengan `origin/main`).
> Repo ini **belum punya `.cpanel.yml`**; kalau tombol deploy Anda memerlukan
> file itu, buat dulu sebelum deploy.

**PENTING — urutan:** kode baru butuh kolom `cash.type`. Bila kode sudah
ter-deploy tetapi SQL belum dijalankan, aplikasi akan error. Jadi **siapkan
file SQL di phpMyAdmin lebih dulu**, baru klik deploy, lalu langsung jalankan
SQL. Tidak ada `php spark down` di proyek ini (CI4 4.2 tanpa perintah
maintenance), jadi lakukan di luar jam operasional agar aman.

### Opsi lebih aman — 2 fase (tanpa mematikan aplikasi)

- **Fase A** (kapan saja, kode lama tetap jalan):
  jalankan **STEP 1 + STEP 2** saja dari file SQL (tambah kolom `type` + isi
  tipenya). Kode lama tidak memakai kolom ini.
- **Fase B** (jendela singkat): klik **Deploy HEAD Commit**, lalu jalankan
  **STEP 2 (ulangi) + STEP 3 sampai STEP 10**. Mengulang STEP 2 aman dan
  memastikan dompet yang mungkin dibuat di sela-sela tetap bertipe benar.

---

## 4. Jalankan SQL migrasi

**Jalankan STEP 0 dulu (hanya SELECT)** untuk memeriksa daftar dompet:
buka `app/Database/wallet-tunai-nontunai.sql`, copy bagian **STEP 0** ke
phpMyAdmin → **SQL** → **Go**. Pastikan dompet kas bernama `Petty Cash ...`
(atau `Tunai`), sisanya bank/EDC.

Lalu jalankan seluruh file:

1. phpMyAdmin → pilih database → tab **Import**.
2. **Choose File** → `wallet-tunai-nontunai.sql`.
3. **Go**.

Skrip ini **tanpa TEMPORARY TABLE**, jadi aman dijalankan via phpMyAdmin
(dijalankan sebagai satu sesi).

---

## 5. Verifikasi

### Database (phpMyAdmin → SQL)
```sql
-- harus tepat 1 baris per outlet per type
SELECT outletid, type, name, qty FROM cash ORDER BY outletid, type;

-- harus 0
SELECT COUNT(*) AS duplikat FROM (
  SELECT outletid, type, COUNT(*) c FROM cash GROUP BY outletid, type HAVING c > 1
) d;

-- harus SAMA dengan total saldo sebelum migrasi
SELECT SUM(CAST(qty AS DECIMAL(20,2))) AS total_saldo FROM cash;
```

### Aplikasi (per outlet)
- [ ] Pengelolaan Dompet: tampil **Tunai** & **Non Tunai** saja.
- [ ] Tambah/Edit dompet: pilihan **Tipe Dompet** tersimpan benar.
- [ ] Pengelolaan Kas: Kas Masuk/Keluar menambah/mengurangi dompet Tunai.
- [ ] POS: bayar Tunai → saldo Tunai naik; EDC → saldo Non Tunai naik.
- [ ] Pemindahan Dompet: asal/tujuan berlabel jelas.
- [ ] Pengeluaran Dana: daftar dompet muncul dengan label + nama outlet.
- [ ] Pembayaran: tiap metode terpetakan ke dompet yang benar.
- [ ] Laporan Harian: label Tunai / Non Tunai (bukan "Petty Cash ...").

---

## 6. Rollback

1. phpMyAdmin → database → **Import** file backup dari langkah 2.
2. cPanel → Git Version Control → deploy commit sebelumnya (revert dulu di Git
   bila perlu).

Melepas kolom (bila diperlukan):
```sql
ALTER TABLE `cash` DROP INDEX `uq_cash_outlet_type`, DROP COLUMN `type`;
```

---

## 7. Catatan

- Kode dan SQL harus berbarengan: kode baru butuh kolom `cash.type`,
  sedangkan kode lama bergantung pada nama dompet.
- Modul akuntansi tidak terpengaruh (tidak ada relasi ke tabel `cash`).
- Saldo "Non Tunai" = jumlah seluruh saldo bank/EDC outlet tersebut.
- Outlet yang belum punya dompet Non Tunai otomatis dibuatkan (saldo 0).
- Dompet "All Outlet" (outletid 0) tetap sebagai dompet pusat.
