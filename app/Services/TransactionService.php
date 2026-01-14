<?php

namespace App\Services;

use Config\Database;
use App\Models\{
    TransactionModel,
    TrxdetailModel,
    TrxpaymentModel,
    StockModel,
    MemberModel,
    DebtModel,
    PaymentModel,
    CashModel,
    VariantModel,
    BundleModel,
    BundledetailModel,
    GconfigModel
};
use App\Libraries\PriceCalculator;

class TransactionService
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    protected function acquireLock(string $key, int $timeout = 5): void
    {
        $result = $this->db
            ->query("SELECT GET_LOCK(?, ?) AS l", [$key, $timeout])
            ->getRowArray();

        if (empty($result) || (int)$result['l'] !== 1) {
            throw new \Exception('Transaksi sedang diproses, silakan tunggu');
        }
    }


    protected function releaseLock(string $key): void
    {
        $this->db->query("SELECT RELEASE_LOCK(?)", [$key]);
    }

    public function createTransaction(array $input, int $userId, int $outletId): int
    {
        $lockKey = "trx_{$userId}_{$outletId}";
        $this->acquireLock($lockKey);

        $this->db->transBegin();

        try {
            // ========================
            // 2. LOAD CONFIG
            // ========================
            $config = (new GconfigModel())->first();
            $calculator = new PriceCalculator($config);

            // ========================
            // 3. HITUNG ITEM
            // ========================
            $items = $calculator->calculate($input);
            if (
                empty($items['variants']) &&
                empty($items['bundles'])
            ) {
                throw new \Exception('Item transaksi kosong');
            }

            if ($items['total'] <= 0) {
                throw new \Exception('Total transaksi tidak valid');
            }
            // if ($items['subtotal'] <= 0) {
            //     throw new \Exception('Item kosong');
            // }

            // ========================
            // 4. INSERT TRANSACTION
            // ========================
            if (!empty($input['payment']) && empty($input['duedate'])) {
                $payment = $input['payment'];
            } elseif (!empty($input['firstpayment']) && !empty($input['secpayment']) && empty($input['duedate'])) {
                $payment = 0;
            } elseif (!empty($input['duedate'])) {
                $payment = 0;
            }
            $trxModel = new TransactionModel();
            
            $fileName = null;
            if (!empty($input['image'])) {
                $img = $input['image'];
                $folderPath = "img/tfproof/";
                if (!is_dir($folderPath)) {
                    mkdir($folderPath, 0755, true);
                }
                $image_parts = explode(";base64,", $img);
                if (count($image_parts) === 2) {
                    $image_base64 = base64_decode($image_parts[1]);
                    $fileName = uniqid() . '.png';
                    $file = $folderPath . $fileName;
                    file_put_contents($file, $image_base64);
                }
            }

            $trxModel->insert([
                'outletid'      => $outletId,
                'userid'        => $userId,
                'memberid'      => $input['customerid'] ?? 0,
                'paymentid'     => $payment,
                'value'         => $items['total'],
                'disctype'      => $input['disctype'] ?? 0,
                'discvalue'     => $items['discount'],
                'pointused'     => $items['poin'],
                'amountpaid'    => 0,
                'date'          => date('Y-m-d H:i:s'),
                'photo'         => $fileName,
            ]);

            $trxId = $trxModel->getInsertID();
            if (!$trxId) {
                throw new \Exception('Gagal membuat transaksi');
            }

            // ========================
            // 5. DETAIL + STOCK
            // ========================
            $this->insertDetails($trxId, $items, $outletId);

            // ========================
            // 6. PAYMENT
            // ========================
            $this->handlePayment($trxId, $items['total'], $input, $input['customerid'] ?? null);

            // ========================
            // 7. MEMBER POINT
            // ========================
            $this->handleMemberPoint($trxId, $input, $items['total'], $config);

            $this->db->transCommit();
            return $trxId;

        } catch (\Throwable $e) {
            // if ($fileName != null) {
            //     if ($fileName && file_exists($folderPath . $fileName)) {
            //         unlink($folderPath . $fileName);
            //     }
            // }
            $this->db->transRollback();
            throw $e;
        } finally {
            $this->releaseLock($lockKey);
        }
    }

    // ==================================================
    // STOCK UPDATE (ATOMIC)
    // ==================================================
    protected function reduceStock(int $variantId, int $qty, int $outletId)
    {
        // cek stock existence dulu
        $stock = $this->db->query(
            "SELECT qty FROM stock WHERE outletid = ? AND variantid = ? FOR UPDATE",
            [$outletId, $variantId]
        )->getRowArray();

        if (!$stock) {
            throw new \Exception("Stock belum diset untuk variant ID {$variantId} di outlet {$outletId}");
        }

        if ($stock['qty'] < $qty) {
            throw new \Exception('Stock tidak mencukupi');
        }

        $this->db->query(
            "UPDATE stock SET qty = qty - ?, sale = NOW()
            WHERE outletid = ? AND variantid = ?",
            [$qty, $outletId, $variantId]
        );
    }

    protected function insertDetails(int $trxId, array $calc, int $outletId)
    {
        $detail = new TrxdetailModel();
        $BundledetModel = new BundledetailModel();

        foreach ($calc['variants'] as $v) {
            $detail->insert([
                'transactionid' => $trxId,
                'variantid'     => $v['id'],
                'qty'           => $v['qty'],
                'value'         => $v['value'],
                'discvar'       => $v['discvar'],
                'globaldisc'    => $v['globaldisc'],
                'memberdisc'    => $v['memberdisc'],
                'marginmodal'   => $v['marginmodal'],
                'margindasar'   => $v['margindasar'],
            ]);

            $this->reduceStock($v['id'], $v['qty'], $outletId);
        }

        foreach ($calc['bundles'] as $b) {
            $detail->insert([
                'transactionid' => $trxId,
                'bundleid'      => $b['id'],
                'qty'           => $b['qty'],
                'value'         => $b['value'],
                'globaldisc'    => $b['globaldisc'],
                'memberdisc'    => $b['memberdisc'],
            ]);

            $bundleItems = $BundledetModel->where('bundleid', $b['id'])->findAll();
            foreach ($bundleItems as $item) {
                $this->reduceStock(
                    $item['variantid'],
                    $item['qty'] * $b['qty'],
                    $outletId
                );
            }
        }
    }

    protected function handlePayment(int $trxId,int $total,array $input,?int $memberId = null)
    {
        if (!empty($input['duedate']) && empty($memberId)) {
            throw new \Exception('Kasbon wajib menggunakan member');
        }

        $trxModel           = new TransactionModel();
        $trxPaymentModel    = new TrxpaymentModel();
        $paymentModel       = new PaymentModel();
        $cashModel          = new CashModel();
        $debtModel          = new DebtModel();

        $payments  = [];
        $totalPaid = 0;

        /**
         * 1️⃣ Normalisasi payment
         */
        if (!empty($input['firstpayment']) && !empty($input['secpayment'])) {
            // split payment (boleh DP / full)
            $payments[] = [
                'payid' => (int)$input['firstpayment'],
                'value' => (int)$input['firstpay'],
            ];
            $payments[] = [
                'payid' => (int)$input['secpayment'],
                'value' => (int)$input['secondpay'],
            ];
        }
        elseif (!empty($input['payments']) && is_array($input['payments'])) {
            $payments = $input['payments'];
        }
        elseif (!empty($input['payment'])) {
            // single payment (DP / full)
            $payments[] = [
                'payid' => (int)$input['payment'],
                'value' => (int)($input['value'] ?? $total),
            ];
        }

        /**
         * 2️⃣ Proses payment → trxpayment + cash
         */
        foreach ($payments as $p) {
            $payId = (int)($p['payid'] ?? 0);
            $value = (int)($p['value'] ?? 0);

            if ($payId <= 0 || $value <= 0) {
                continue;
            }

            // Clamp overpay
            // if ($totalPaid + $value > $total) {
            //     $value = $total - $totalPaid;
            // }
            if ($value <= 0) break;

            $trxPaymentModel->insert([
                'paymentid'     => $payId,
                'transactionid' => $trxId,
                'value'         => $value,
            ]);

            $payment = $paymentModel->find($payId);
            if ($payment && !empty($payment['cashid'])) {
                $cashModel->query(
                    "UPDATE cash SET qty = qty + ? WHERE id = ?",
                    [$value, $payment['cashid']]
                );
            }

            $totalPaid += $value;
        }

        /**
         * 3️⃣ Debt handling (HANYA jika ada duedate)
         */
        if (!empty($input['duedate']) && $totalPaid < $total) {

            if (!$memberId) {
                throw new \Exception('Kasbon hanya berlaku untuk member!');
            }

            $debtValue = $total - $totalPaid;

            $debtModel->insert([
                'memberid'      => $memberId,
                'transactionid' => $trxId,
                'value'         => $debtValue,
                'deadline'      => $input['duedate'],
            ]);

            // trxpayment debt
            $trxPaymentModel->insert([
                'paymentid'     => 0, // DEBT
                'transactionid' => $trxId,
                'value'         => $debtValue,
            ]);
        }

        $totalPaid = $trxPaymentModel
            ->selectSum('value')
            ->where('transactionid', $trxId)
            ->where('paymentid !=', 0)
            ->get()->getRow()->value ?? 0;

        $trxModel->update($trxId, [
            'amountpaid' => $totalPaid
        ]);

        // if ($totalPaid > $total) {
        //     throw new \Exception('Overpayment detected');
        // }
    }

    protected function handleMemberPoint(int $trxId, array $input, int $total, array $config)
    {
        if (empty($input['customerid'])) return;

        $model = new MemberModel();
        $member = $model->find($input['customerid']);
        if (!$member) return;

        // deduct
        $used = (int)($input['poin'] ?? 0);

        // earn
        if ($config['poinorder'] && $total >= $config['poinorder']) {
            $earn = floor($total / $config['poinorder']) * $config['poinvalue'];
            $newPoint = max(0, $member['poin'] - $used) + $earn;
            $this->db->query(
                "UPDATE member SET poin = ?, trx = trx + 1 WHERE id = ?",
                [$newPoint, $member['id']]
            );
        }
    }
}