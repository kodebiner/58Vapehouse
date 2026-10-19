-- =============================================================================
-- 58 VAPEHOUSE - DOMPET TUNAI & NON TUNAI - FASE A
-- Aman dijalankan KAPAN SAJA, termasuk saat kode lama masih aktif.
-- Hanya: tambah kolom cash.type + isi tipenya. Tidak mengubah nama/hapus dompet.
--
-- Jalankan via phpMyAdmin -> tab SQL (copy-paste) atau Import.
-- WAJIB backup dulu.
-- =============================================================================

-- STEP 1 - Tambah kolom type (0 = Tunai, 1 = Non Tunai)
ALTER TABLE `cash`
  ADD COLUMN `type` TINYINT(1) NOT NULL DEFAULT 0 AFTER `outletid`;

-- STEP 2 - Tentukan tipe dari nama dompet lama
UPDATE `cash` SET `type` = 0 WHERE `name` LIKE 'Petty Cash%' OR `name` = 'Tunai';
UPDATE `cash` SET `type` = 1 WHERE `name` NOT LIKE 'Petty Cash%' AND `name` <> 'Tunai';

-- Verifikasi (hanya baca)
SELECT outletid, type, name, qty FROM `cash` ORDER BY outletid, type;
