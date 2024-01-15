<?php

namespace App\Controllers;

use App\Models\BundledetailModel;
use App\Models\BundleModel;
use App\Models\CategoryModel;
use App\Models\CashModel;
use App\Models\DebtModel;
use App\Models\GconfigModel;
use App\Models\OutletModel;
use App\Models\UserModel;
use App\Models\MemberModel;
use App\Models\PaymentModel;
use App\Models\ProductModel;
use App\Models\StockModel;
use App\Models\VariantModel;
use App\Models\TransactionModel;
use App\Models\TrxdetailModel;
use App\Models\TrxpaymentModel;
use App\Models\TrxotherModel;
use App\Models\BookingModel;
use App\Models\BookingdetailModel;
use App\Models\PurchaseModel;
use App\Models\PurchasedetailModel;
use App\Models\PresenceModel;
use App\Models\GroupUserModel;
use App\Models\OldStockModel;
use Myth\Auth\Models\GroupModel;

class Home extends BaseController
{
    public function index()
    {
        // Calling models
        $db                 = \Config\Database::connect();
        $TransactionModel   = new TransactionModel();
        $TrxdetailModel     = new TrxdetailModel();
        $TrxpaymentModel    = new TrxpaymentModel();
        $TrxotherModel      = new TrxotherModel();
        $PaymentModel       = new PaymentModel();
        $ProductModel       = new ProductModel();
        $VariantModel       = new VariantModel();
        $StockModel         = new StockModel();
        $BundleModel        = new BundleModel();
        $BundledetailModel  = new BundledetailModel();
        $DebtModel          = new DebtModel();
        $MemberModel        = new MemberModel();
        $PurchaseModel      = new PurchaseModel();
        $PurchasedetModel   = new PurchasedetailModel();

        // Populating Data
        $input          = $this->request->getGet('daterange');
        // $trxdetails     = $TrxdetailModel->findAll();
        // $trxpayments    = $TrxpaymentModel->findAll();
        $products       = $ProductModel->findAll();
        $variants       = $VariantModel->findAll();
        // $debts          = $DebtModel->findAll();
        // $customers      = $MemberModel->findAll();
        $bundles        = $BundleModel->findAll();
        $bundets        = $BundledetailModel->findAll();
        $purchasedet    = $PurchasedetModel->findAll();
        $payments       = $PaymentModel->findAll();

        // Populating Data
        if (!empty($input)) {
            $daterange  = explode(' - ', $input);
            $startdate  = $daterange[0];
            $enddate    = $daterange[1];
        } else {
            $startdate  = date('Y-m-1');
            $enddate    = date('Y-m-t');
        }

        $today          = date('Y-m-d');
        $month          = date('Y-m-t');

        if ($this->data['outletPick'] === null) {
            if ($startdate === $enddate) {
                $transactions   = $TransactionModel->where('date >=', $startdate.' 00:00:00')->where('date <=', $enddate.' 23:59:59')->find();
                $trxothers      = $TrxotherModel->notLike('description', 'Top Up')->notLike('description', 'Debt')->where('date >=', $startdate.' 00:00:00')->where('date <=', $enddate.' 23:59:59')->find();
            } else {
                $transactions   = $TransactionModel->where('date >=', $startdate)->where('date <=', $enddate)->find();
                $trxothers      = $TrxotherModel->notLike('description', 'Top Up')->notLike('description', 'Debt')->where('date >=', $startdate)->where('date <=', $enddate)->find();
            }

            $todayexpenses  = $TrxotherModel->notLike('description', 'Top Up')->notLike('description', 'Debt')->where('date >=', $today.' 00:00:00')->where('date <=', $today.' 23:59:59')->find();
            $trxtodays      = $TransactionModel->where('date >=', $today.' 00:00:00')->where('date <=', $today.' 23:59:59')->find();
            $stocks         = $StockModel->where('restock !=', '0000-00-00 00:00:00')->where('sale !=', '0000-00-00 00:00:00')->findAll();
            $payments       = $PaymentModel->findAll();
        } else {
            if ($startdate === $enddate) {
                $transactions   = $TransactionModel->where('date >=', $startdate.' 00:00:00')->where('date <=', $enddate.' 23:59:59')->where('outletid', $this->data['outletPick'])->find();
                $trxothers      = $TrxotherModel->notLike('description', 'Top Up')->notLike('description', 'Debt')->where('date >=', $startdate.' 00:00:00')->where('date <=', $enddate.' 23:59:59')->where('outletid', $this->data['outletPick'])->find();
            } else {
                $transactions   = $TransactionModel->where('date >=', $startdate)->where('date <=', $enddate)->where('outletid', $this->data['outletPick'])->find();
                $trxothers      = $TrxotherModel->notLike('description', 'Top Up')->notLike('description', 'Debt')->where('date >=', $startdate)->where('date <=', $enddate)->where('outletid', $this->data['outletPick'])->find();
            }

            $todayexpenses  = $TrxotherModel->notLike('description', 'Top Up')->notLike('description', 'Debt')->where('date >=', $today.' 00:00:00')->where('date <=', $today.' 23:59:59')->where('outletid', $this->data['outletPick'])->find();
            $trxtodays      = $TransactionModel->where('date >=', $today.' 00:00:00')->where('date <=', $today.' 23:59:59')->where('outletid', $this->data['outletPick'])->find();
            $stocks         = $StockModel->where('restock !=', '0000-00-00 00:00:00')->where('sale !=', '0000-00-00 00:00:00')->where('outletid', $this->data['outletPick'])->find();
            $payments       = $PaymentModel->where('outletid', $this->data['outletPick'])->find();
        }

        // Stock Cycle
        $stok           = array_slice($stocks, 0, 3);
        array_multisort(array_column($stok, 'restock'), SORT_DESC, $stok);

        // Sales Value
        $sales = array();
        $id = [];

        // Discount
        $discvar    = array();
        $discval    = array();
        $trxsid     = array();

        // bestseller array
        $bestssell  = array();
        $bestpay    = array();
        
        // Debt Total
        $trxdebtval = array();
        
        // Customer Debt
        $custdebt = array();

        // Sales Profit
        $qtytrx = array();
        $marginmodals = array();
        $margindasars = array();

        // Transaction Data
        $transactiondata    = array();
        foreach ($transactions as $transaction) {
            // Transaction Data Array
            $sales[] = [
                'id'        => $transaction['id'],
                'date'      => $transaction['date'],
                'value'     => $transaction['value'],
                'pointused' => $transaction['pointused'],
                'disctype'  => $transaction['disctype'],
                'discvalue' => $transaction['discvalue'],
            ];
            $id[] = $transaction['id'];
        }

        // Finding Data
        $trxdetails     = $TrxdetailModel->whereIn('transactionid', $id)->find();
        $trxpayments    = $TrxpaymentModel->whereIn('transactionid', $id)->find();
        $debts          = $DebtModel->whereIn('transactionid', $id)->find();

        // Trxdetail Array
        foreach ($trxdetails as $trxdet) {
            // Discount Total
            $subtotals = (Int)$trxdet['qty'] * (Int)$trxdet['value'];
            $discvar[] = $trxdet['discvar'];

            if ($transaction['disctype'] === "0") {
                $discval[] = $transaction['discvalue'];
            } else {
                $discval[] = (int)$subtotals * ((int)$transaction['discvalue'] / 100);
            }

            // Best seller 
            $bestssell[] = [
                'variantid' => $trxdet['variantid'],
                'bundleid'  => $trxdet['bundleid'],
                'qty'       => $trxdet['qty'],
            ];

            // Sales Profit
            $marginmodal    = (Int)$trxdet['marginmodal'] * (Int)$trxdet['qty'];
            $margindasar    = (Int)$trxdet['margindasar'] * (Int)$trxdet['qty'];
            $qtytrx[]       = (Int)$trxdet['qty'];
            $marginmodals[] = $marginmodal;
            $margindasars[] = $margindasar;
        }

        foreach ($trxpayments as $trxpay) {
            foreach ($payments as $pay) {
                if (($trxpay['paymentid'] === $pay['id']) && $trxpay['paymentid'] !== "0") {
                    $bestpay[] = [
                        'id'    => $pay['id'],
                        'name'  => $pay['name'],
                        'qty'   => "1",
                        'value' =>  $trxpay['value'],
                    ];
                }
            }
        }

        // Debt Total $ Total Debt Customer & Down Payment
        $memberid = array();
        foreach ($debts as $debt) {
            $trxdebtval[]   = $debt['value'];
            $memberid[]     = $debt['memberid'];
        }
        $customers      = $MemberModel->find($memberid);

        $discvarsum = array_sum($discvar);
        $discvalsum = array_sum($discval);
        $totaldisc  = $discvalsum + $discvarsum;
        $trxamount  = count($id);

        $marginmodalsum = array_sum($marginmodals);
        $margindasarsum = array_sum($margindasars);
        $summary        = array_sum(array_column($sales, 'value'));

        $transactions[] = [
            'value'     => $summary,
            'modal'     => $marginmodalsum,
            'dasar'     => $margindasarsum,
        ];

        $keuntunganmodal = array_sum(array_column($transactions, 'modal'));

        // Products Sales
        $qtytrxsum      = array_sum($qtytrx);

        // Sales Details
        $pointusedsum   = array_sum(array_column($sales, 'pointused'));
        $gross          = $summary + $pointusedsum + $totaldisc;

        // Cashin Cash Out
        $cashin     = [];
        $cashout    = [];
        foreach ($trxothers as $trxother) {
            if ($trxother['type'] === "0") {
                $cashin[] = $trxother['qty'];
            } else {
                $cashout[] = $trxother['qty'];
            }
        }

        $cashinsum = array_sum($cashin);
        $cashoutsum = array_sum($cashout);

        // Today's Expenses
        $todayexp = array();
        foreach ($todayexpenses as $todexp) {
            if ($todexp['type'] === "1") {
                $todayexp[] = $todexp['qty'];
            }
        }
        $todayexps  = array_sum($todayexp);

        // Today's Sales
        $trxtoday = array();
        foreach ($trxtodays as $trxtod) {
            $trxtoday[] = $trxtod['value'];
        }
        $sumtrxtoday    = array_sum($trxtoday);

        $salesresult = array_sum(array_column($sales, 'value'));

        // Average transaction
        if ($salesresult !== 0) {
            $salestrx       = count($sales);
            $averagedays    = $salesresult / $salestrx;
            $sale           = sprintf("%.2f", $averagedays);
            $doblesale      = ceil($sale);
            $saleaverage    = sprintf("%.2f", $doblesale);
        } else {
            $averagedays    = "0";
            $sale           = sprintf("%.2f", $averagedays);
            $doblesale      = ceil($sale);
            $saleaverage    = sprintf("%.2f", $doblesale);
        }

        // Average per days
        $now                = date_create($startdate);
        $your_date          = date_create($enddate)->modify('+1 day');
        $datediff           = date_diff($now, $your_date);
        $days               = $datediff->format("%a");
        $dateaverage        = ceil($salesresult / (int)$days);
        $resultaveragedays  = sprintf("%.2f", $dateaverage);

        // Bussy Days
        $saly = [];
        $hour = [];
        foreach ($sales as $sale) {
            $datesale   = date_create($sale['date']);
            $saledate   = $datesale->format('Y-m-d');
            $hoursale   = $datesale->format('H');
            $hour[]     = date('H', strtotime($sale['date']));
            $saly[]     = [
                'date'  => $saledate,
                'value' => $sale['value'],
                'hours' => $hoursale,
                'trx'   => '1',
            ];
        }
        $busies = array_count_values($hour);
        arsort($busies);
        $busyhours = array_slice(array_keys($busies), 0, 1, true);
        if (!empty($busyhours)) {
            $busy = $busyhours[0] . ':00';
        } else {
            $busy = "00:00";
        }

        $saledet = [];
        foreach ($saly as $sels) {
            if (!isset($saledet[$sels['date']])) {
                $saledet[$sels['date']] = $sels;
            } else {
                $saledet[$sels['date']]['value'] += $sels['value'];
                $saledet[$sels['date']]['trx'] += $sels['trx'];
            }
        }
        $saledet = array_values($saledet);

        array_multisort(array_column($saledet, 'trx'), SORT_DESC, $saledet);
        $daysale    = array_slice($saledet, 0, 1);
        $day        = 0;
        foreach ($daysale as $days) {
            $datesale   = date_create($days['date']);
            $day        = $datesale->format('l');
        }
        
        $bestpayment = [];
        foreach ($bestpay as $best) {
            if (!isset($bestpayment[$best['id']])) {
                $bestpayment[$best['id']] = $best;
            } else {
                $bestpayment[$best['id']]['qty'] += $best['qty'];
                $bestpayment[$best['id']]['value'] += $best['value'];
            }
        }
        $bestpayment = array_values($bestpayment);
        array_multisort(array_column($bestpayment, 'value'), SORT_DESC, $bestpayment);
        $bestpay3       = array_slice($bestpayment, 0, 3);

        $bestseller = [];
        foreach ($bestssell as $best) {
            if (!isset($bestseller[$best['variantid']])) {
                $bestseller[$best['variantid']] = $best;
            } else {
                $bestseller[$best['variantid']]['qty'] += $best['qty'];
            }
        }
        $bestseller = array_values($bestseller);
        array_multisort(array_column($bestseller, 'qty'), SORT_DESC, $bestseller);
        $best3      = array_slice($bestseller, 0, 3);

        $customerdebt = array();
        foreach ($custdebt as $cusdeb) {
            if (!isset($customerdebt[$cusdeb['memberid']])) {
                $customerdebt[$cusdeb['memberid']] = $cusdeb;
            }
        }

        $data                   = $this->data;
        $data['title']          = lang('Global.dashboard');
        $data['description']    = lang('Global.dashdesc');
        $data['sales']          = $summary;
        $data['profit']         = $keuntunganmodal;
        $data['products']       = $products;
        $data['variants']       = $variants;
        $data['bundles']        = $bundles;
        $data['bundets']        = $bundets;
        $data['trxamount']      = $trxamount;
        $data['qtytrxsum']      = $qtytrxsum;
        $data['pointusedsum']   = $pointusedsum;
        $data['totaldisc']      = $totaldisc;
        $data['gross']          = $gross;
        $data['cashinsum']      = $cashinsum;
        $data['cashoutsum']     = $cashoutsum;
        $data['top3prod']       = $best3;
        $data['top3paymet']     = $bestpay3;
        $data['customers']      = $customers;
        $data['stocks']         = $stok;
        $data['average']        = (int)$saleaverage;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);
        $data['trxdebtval']     = array_sum($trxdebtval);
        $data['debts']          = $debts;
        $data['totalcustdebt']  = count($customers);
        $data['averagedays']    = $resultaveragedays;
        $data['bussyday']       = $day;
        $data['bussytime']      = $busy;
        $data['todayexps']      = $todayexps;
        $data['month']          = $month;
        $data['sumtrxtoday']    = $sumtrxtoday;

        // if ($this->data['outletPick'] != null) {
        //     $data['payments']       = $PaymentModel->where('outletid', $this->data['outletPick'])->find();
        // } else {
            $data['payments']       = $payments;
        // }

        return view('dashboard', $data);
    }

    public function outletses($id)
    {
        $session = \Config\Services::session();

        if ($id === '0') {
            $session->remove('outlet');
        } else {
            if ($session->get('outlet') != null) {
                $session->remove('outlet');
            }
            $session->set('outlet', $id);
        }

        return redirect()->to('');
    }

    public function ownership()
    {
        $authorize = service('authorization');

        $authorize->removeUserFromGroup(3, 1);

        $authorize->addUserToGroup(3, 'operator');
    }

    public function trial()
    {
        $VariantModel   = new VariantModel;
        $OldStockModel = new OldStockModel;

        $variants = $VariantModel->findAll();

        foreach ($variants as $variant) {
            if ($variant['id'] != "2358") {
                // $variantdata = [
                //     'id'        => $variant['id'],
                //     'hargadasar' => $variant['hargamodal'],
                //     'hargarekomendasi' => (Int)$variant['hargamodal'] + (Int)$variant['hargajual'],
                // ];
                // $VariantModel->save($variantdata);

                $data = [
                    'variantid' => $variant['id'],
                    'hargadasar' => $variant['hargamodal'],
                    'hargamodal' => $variant['hargamodal'],
                ];
                $OldStockModel->insert($data);
            }
        }
    }

    public function phpinfo()
    {
        phpinfo();
    }
}
