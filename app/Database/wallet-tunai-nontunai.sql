-- =============================================================================
-- 58 VAPEHOUSE - PENYEDERHANAAN DOMPET (TUNAI & NON TUNAI)
-- VERSI PRODUKSI - tanpa TEMPORARY TABLE
-- (aman dijalankan langsung: mysql < file, maupun tempel/import via phpMyAdmin)
--
-- Hasil:
--   * "Petty Cash <outlet>" / "Tunai"  -> dompet "Tunai"     (cash.type = 0)
--   * semua dompet bank/EDC/QRIS/...    -> satu dompet "Non Tunai" (cash.type = 1)
--
-- WAJIB:
--   1. BACKUP database dulu.
--   2. Jalankan STEP 0 dan periksa hasilnya (terutama daftar nama dompet).
--   3. Kode aplikasi baru harus sudah terpasang sebelum aplikasi dipakai lagi.
--   4. Kolom cash.qty bertipe VARCHAR; perhitungan memakai CAST ke DECIMAL.
--
-- CATATAN: ALTER TABLE menyebabkan auto-commit (tidak bisa dibungkus 1 transaksi),
--          jadi BACKUP adalah jaring pengaman utamanya.
-- =============================================================================

-- -----------------------------------------------------------------------------
-- STEP 0 - DRY RUN (HANYA BACA). Periksa sebelum lanjut.
--   Pastikan semua dompet kas bernama "Petty Cash ..." / "Tunai".
--   Jika ada nama dompet kas lain, sesuaikan kondisi di STEP 2.
-- -----------------------------------------------------------------------------
SELECT id, outletid, name, qty FROM `cash` ORDER BY outletid, id;

SELECT c.id AS cashid, c.name AS wallet, c.outletid, p.id AS paymentid, p.name AS payment
FROM `payment` p LEFT JOIN `cash` c ON c.id = p.`cashid`
ORDER BY p.outletid, p.id;

-- -----------------------------------------------------------------------------
-- STEP 1 - Tambah kolom type (0 = Tunai, 1 = Non Tunai)
-- -----------------------------------------------------------------------------
ALTER TABLE `cash`
  ADD COLUMN `type` TINYINT(1) NOT NULL DEFAULT 0 AFTER `outletid`;

-- -----------------------------------------------------------------------------
-- STEP 2 - Tentukan tipe dari nama dompet lama
--   >>> Jika STEP 0 menunjukkan nama dompet kas berbeda, tambahkan di sini. <<<
-- -----------------------------------------------------------------------------
UPDATE `cash` SET `type` = 0 WHERE `name` LIKE 'Petty Cash%' OR `name` = 'Tunai';
UPDATE `cash` SET `type` = 1 WHERE `name` NOT LIKE 'Petty Cash%' AND `name` <> 'Tunai';

-- -----------------------------------------------------------------------------
-- STEP 3 - Jumlahkan saldo dompet sejenis ke dompet kanonik (id terkecil)
-- -----------------------------------------------------------------------------
UPDATE `cash` kan
JOIN (
  SELECT `outletid`, `type`, MIN(`id`) AS `id`,
         SUM(CAST(`qty` AS DECIMAL(20,2))) AS `total`
  FROM `cash`
  GROUP BY `outletid`, `type`
) s ON s.`id` = kan.`id`
SET kan.`qty` = s.`total`;

-- -----------------------------------------------------------------------------
-- STEP 4 - Alihkan SEMUA referensi ke dompet kanonik
--   (dijalankan SEBELUM dompet lama dihapus). Referensi dompet di DB:
--   payment.cashid, cashexp.cashid, trxother.cashid,
--   cashmovement.origin, cashmovement.destination
-- -----------------------------------------------------------------------------
UPDATE `payment` p
JOIN `cash` c ON c.`id` = p.`cashid`
JOIN (SELECT `outletid`,`type`,MIN(`id`) AS `kid` FROM `cash` GROUP BY `outletid`,`type`) k
  ON k.`outletid` = c.`outletid` AND k.`type` = c.`type`
SET p.`cashid` = k.`kid`
WHERE p.`cashid` <> k.`kid`;

UPDATE `cashexp` e
JOIN `cash` c ON c.`id` = e.`cashid`
JOIN (SELECT `outletid`,`type`,MIN(`id`) AS `kid` FROM `cash` GROUP BY `outletid`,`type`) k
  ON k.`outletid` = c.`outletid` AND k.`type` = c.`type`
SET e.`cashid` = k.`kid`
WHERE e.`cashid` <> k.`kid`;

UPDATE `trxother` t
JOIN `cash` c ON c.`id` = t.`cashid`
JOIN (SELECT `outletid`,`type`,MIN(`id`) AS `kid` FROM `cash` GROUP BY `outletid`,`type`) k
  ON k.`outletid` = c.`outletid` AND k.`type` = c.`type`
SET t.`cashid` = k.`kid`
WHERE t.`cashid` <> k.`kid`;

UPDATE `cashmovement` m
JOIN `cash` c ON c.`id` = m.`origin`
JOIN (SELECT `outletid`,`type`,MIN(`id`) AS `kid` FROM `cash` GROUP BY `outletid`,`type`) k
  ON k.`outletid` = c.`outletid` AND k.`type` = c.`type`
SET m.`origin` = k.`kid`
WHERE m.`origin` <> k.`kid`;

UPDATE `cashmovement` m
JOIN `cash` c ON c.`id` = m.`destination`
JOIN (SELECT `outletid`,`type`,MIN(`id`) AS `kid` FROM `cash` GROUP BY `outletid`,`type`) k
  ON k.`outletid` = c.`outletid` AND k.`type` = c.`type`
SET m.`destination` = k.`kid`
WHERE m.`destination` <> k.`kid`;

-- -----------------------------------------------------------------------------
-- STEP 5 - Hapus dompet duplikat (sisakan id terkecil per outlet+type)
-- -----------------------------------------------------------------------------
DELETE c FROM `cash` c
JOIN (SELECT `outletid`,`type`,MIN(`id`) AS `kid` FROM `cash` GROUP BY `outletid`,`type`) k
  ON k.`outletid` = c.`outletid` AND k.`type` = c.`type`
WHERE c.`id` <> k.`kid`;

-- -----------------------------------------------------------------------------
-- STEP 6 - Rename dompet ke label baru
-- -----------------------------------------------------------------------------
UPDATE `cash` SET `name` = 'Tunai'     WHERE `type` = 0;
UPDATE `cash` SET `name` = 'Non Tunai' WHERE `type` = 1;

-- -----------------------------------------------------------------------------
-- STEP 7 - Pastikan SETIAP outlet punya dompet Tunai & Non Tunai
--   (outlet yang belum punya dompet non tunai akan dibuatkan, saldo 0)
-- -----------------------------------------------------------------------------
INSERT INTO `cash` (`outletid`,`type`,`name`,`qty`)
SELECT o.`id`, 0, 'Tunai', '0' FROM `outlet` o
WHERE NOT EXISTS (SELECT 1 FROM `cash` c WHERE c.`outletid` = o.`id` AND c.`type` = 0);

INSERT INTO `cash` (`outletid`,`type`,`name`,`qty`)
SELECT o.`id`, 1, 'Non Tunai', '0' FROM `outlet` o
WHERE NOT EXISTS (SELECT 1 FROM `cash` c WHERE c.`outletid` = o.`id` AND c.`type` = 1);

-- -----------------------------------------------------------------------------
-- STEP 8 - Normalkan format qty (hapus desimal '.00' hasil SUM)
-- -----------------------------------------------------------------------------
UPDATE `cash`
SET `qty` = TRIM(TRAILING '.' FROM TRIM(TRAILING '0' FROM `qty`))
WHERE `qty` LIKE '%.%';

-- -----------------------------------------------------------------------------
-- STEP 9 - Kunci unik (outletid, type). Jalankan setelah STEP 5 & 7.
-- -----------------------------------------------------------------------------
ALTER TABLE `cash` ADD UNIQUE KEY `uq_cash_outlet_type` (`outletid`,`type`);

-- -----------------------------------------------------------------------------
-- STEP 10 - VERIFIKASI (HANYA BACA)
-- -----------------------------------------------------------------------------
-- Harus tepat 1 baris per outlet per type:
SELECT outletid, type, name, qty FROM `cash` ORDER BY outletid, type;

-- Harus 0:
SELECT COUNT(*) AS jml_duplikat FROM (
  SELECT outletid, type, COUNT(*) c FROM `cash` GROUP BY outletid, type HAVING c > 1
) d;

-- Harus sama dengan total saldo SEBELUM migrasi (konservasi uang):
SELECT SUM(CAST(qty AS DECIMAL(20,2))) AS total_saldo FROM `cash`;

-- -----------------------------------------------------------------------------
-- ROLLBACK
--   Pulihkan dari backup database (mysqldump) bila ada masalah.
--   Melepas kolom bila perlu:
--     ALTER TABLE `cash` DROP INDEX `uq_cash_outlet_type`, DROP COLUMN `type`;
-- =============================================================================
