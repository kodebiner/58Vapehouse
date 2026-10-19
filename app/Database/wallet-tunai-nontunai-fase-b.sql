-- =============================================================================
-- 58 VAPEHOUSE - DOMPET TUNAI & NON TUNAI - FASE B
-- Jalankan SETELAH:
--   * FASE A sudah dijalankan (kolom cash.type sudah ada), dan
--   * kode baru sudah ter-deploy (atau tepat bersamaan).
--
-- Menggabungkan dompet, mengalihkan referensi, rename, dan memasang unique key.
-- Jalankan via phpMyAdmin -> tab SQL / Import.
-- WAJIB backup dulu.
-- =============================================================================

-- STEP 2 (ulangi) - pastikan dompet yang dibuat di sela-sela fase A-B bertipe benar
UPDATE `cash` SET `type` = 0 WHERE `name` LIKE 'Petty Cash%' OR `name` = 'Tunai';
UPDATE `cash` SET `type` = 1 WHERE `name` NOT LIKE 'Petty Cash%' AND `name` <> 'Tunai';

-- STEP 3 - Jumlahkan saldo dompet sejenis ke dompet kanonik (id terkecil)
UPDATE `cash` kan
JOIN (
  SELECT `outletid`, `type`, MIN(`id`) AS `id`,
         SUM(CAST(`qty` AS DECIMAL(20,2))) AS `total`
  FROM `cash`
  GROUP BY `outletid`, `type`
) s ON s.`id` = kan.`id`
SET kan.`qty` = s.`total`;

-- STEP 4 - Alihkan semua referensi ke dompet kanonik (sebelum dompet lama dihapus)
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

-- STEP 5 - Hapus dompet duplikat (sisakan id terkecil per outlet+type)
DELETE c FROM `cash` c
JOIN (SELECT `outletid`,`type`,MIN(`id`) AS `kid` FROM `cash` GROUP BY `outletid`,`type`) k
  ON k.`outletid` = c.`outletid` AND k.`type` = c.`type`
WHERE c.`id` <> k.`kid`;

-- STEP 6 - Rename
UPDATE `cash` SET `name` = 'Tunai'     WHERE `type` = 0;
UPDATE `cash` SET `name` = 'Non Tunai' WHERE `type` = 1;

-- STEP 7 - Pastikan setiap outlet punya 2 dompet
INSERT INTO `cash` (`outletid`,`type`,`name`,`qty`)
SELECT o.`id`, 0, 'Tunai', '0' FROM `outlet` o
WHERE NOT EXISTS (SELECT 1 FROM `cash` c WHERE c.`outletid` = o.`id` AND c.`type` = 0);

INSERT INTO `cash` (`outletid`,`type`,`name`,`qty`)
SELECT o.`id`, 1, 'Non Tunai', '0' FROM `outlet` o
WHERE NOT EXISTS (SELECT 1 FROM `cash` c WHERE c.`outletid` = o.`id` AND c.`type` = 1);

-- STEP 8 - Normalisasi format qty
UPDATE `cash`
SET `qty` = TRIM(TRAILING '.' FROM TRIM(TRAILING '0' FROM `qty`))
WHERE `qty` LIKE '%.%';

-- STEP 9 - Kunci unik
ALTER TABLE `cash` ADD UNIQUE KEY `uq_cash_outlet_type` (`outletid`,`type`);

-- STEP 10 - Verifikasi
SELECT outletid, type, name, qty FROM `cash` ORDER BY outletid, type;
SELECT COUNT(*) AS duplikat FROM (
  SELECT outletid, type, COUNT(*) c FROM `cash` GROUP BY outletid, type HAVING c > 1
) d;
SELECT SUM(CAST(qty AS DECIMAL(20,2))) AS total_saldo FROM `cash`;
