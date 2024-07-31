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
use App\Models\StockMovementModel;
use App\Models\StockMoveDetailModel;
use App\Models\StockmovementModelBackup;

class Home extends BaseController
{
    protected $data;
    protected $db, $builder;
    protected $auth;
    protected $config;
    
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
        $CashModel          = new CashModel();

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
            $startdate  = date('Y-m-1' . ' 00:00:00');
            $enddate    = date('Y-m-t' . ' 23:59:59');
        }

        $firstday       = date('Y-m-1');
        $lastday        = date('Y-m-t');
        $today          = date('Y-m-d');
        $month          = date('Y-m-t');

        if ($this->data['outletPick'] === null) {
            // if ($startdate === $enddate) {
            //     $transactions   = $TransactionModel->where('date >=', $startdate.' 00:00:00')->where('date <=', $enddate.' 23:59:59')->find();
            //     $trxothers      = $TrxotherModel->notLike('description', 'Top Up')->notLike('description', 'Debt')->where('date >=', $startdate.' 00:00:00')->where('date <=', $enddate.' 23:59:59')->find();
            // } else {
                $transactions   = $TransactionModel->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->find();
                $trxothers      = $TrxotherModel->notLike('description', 'Top Up')->notLike('description', 'Debt')->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->find();
            // }

            $trxmonths      = $TransactionModel->where('date >=', $firstday.' 00:00:00')->where('date <=', $lastday.' 23:59:59')->find();
            $todayexpenses  = $TrxotherModel->notLike('description', 'Top Up')->notLike('description', 'Debt')->where('date >=', $today.' 00:00:00')->where('date <=', $today.' 23:59:59')->find();
            $trxtodays      = $TransactionModel->where('date >=', $today.' 00:00:00')->where('date <=', $today.' 23:59:59')->find();
            $stocks         = $StockModel->where('restock !=', '0000-00-00 00:00:00')->where('sale !=', '0000-00-00 00:00:00')->orderBy('sale', 'ASC')->find();
            $cashes         = $CashModel->findAll();
        } else {
            // if ($startdate === $enddate) {
            //     $transactions   = $TransactionModel->where('date >=', $startdate.' 00:00:00')->where('date <=', $enddate.' 23:59:59')->where('outletid', $this->data['outletPick'])->find();
            //     $trxothers      = $TrxotherModel->notLike('description', 'Top Up')->notLike('description', 'Debt')->where('date >=', $startdate.' 00:00:00')->where('date <=', $enddate.' 23:59:59')->where('outletid', $this->data['outletPick'])->find();
            // } else {
                $transactions   = $TransactionModel->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->where('outletid', $this->data['outletPick'])->find();
                $trxothers      = $TrxotherModel->notLike('description', 'Top Up')->notLike('description', 'Debt')->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->where('outletid', $this->data['outletPick'])->find();
            // }

            $trxmonths      = $TransactionModel->where('date >=', $firstday.' 00:00:00')->where('date <=', $lastday.' 23:59:59')->where('outletid', $this->data['outletPick'])->find();
            $todayexpenses  = $TrxotherModel->notLike('description', 'Top Up')->notLike('description', 'Debt')->where('date >=', $today.' 00:00:00')->where('date <=', $today.' 23:59:59')->where('outletid', $this->data['outletPick'])->find();
            $trxtodays      = $TransactionModel->where('date >=', $today.' 00:00:00')->where('date <=', $today.' 23:59:59')->where('outletid', $this->data['outletPick'])->find();
            $stocks         = $StockModel->where('restock !=', '0000-00-00 00:00:00')->where('sale !=', '0000-00-00 00:00:00')->orderBy('sale', 'ASC')->where('outletid', $this->data['outletPick'])->find();
            $cashes         = $CashModel->where('outletid', $this->data['outletPick'])->find();
        }

        // Stock Cycle
        $stockdata  = [];
        if (!empty($stocks)) {
            foreach ($stocks as $stok) {
                $variants       = $VariantModel->find($stok['variantid']);
                $vname          = $variants['name'];

                if (!empty($variants)) {
                    $products   = $ProductModel->find($variants['productid']);
                    
                    if (!empty($products)) {
                        $pname      = $products['name'];
                    }

                    $stockdata[$stok['id']]['name']     = $pname.'-'.$vname;
                    $stockdata[$stok['id']]['restock']  = $stok['restock'];
                    $stockdata[$stok['id']]['sale']     = $stok['sale'];
                    $stockdata[$stok['id']]['qty']      = $stok['qty'];
                }
            }
        } else {
            $variants       = array();
            $products       = array();
        }
        array_multisort(array_column($stockdata, 'sale'), SORT_ASC, $stockdata);
        $stok           = array_slice($stockdata, 0, 3);
        // $stok           = array_slice($stocks, 0, 3);
        // array_multisort(array_column($stok, 'restock'), SORT_DESC, $stok);

        // Transaction Data
        $transactiondata    = array();
        $discount           = array();
        $debtvalue          = array();
        $downpayment        = array();
        $totalmember        = array();
        $trxvalue           = array();
        $pointused          = array();
        $marginmodal        = array();
        $bestproduct        = array();
        $productsale        = array();
        $bestpayment        = array();
        $saly               = array();
        $hour               = array();
        $disc               = array();

        foreach ($transactions as $transaction) {
            // Transaction Value Array
            $trxvalue[]         = (Int)$transaction['value'];

            // Transaction Point Used Array
            $pointused[]        = $transaction['pointused'];

            // Discount Transaction
            if (!empty($transaction['discvalue'])) {
                $discount[]  = $transaction['discvalue'];
            }

            if ($transaction['memberdisc'] != null) {
                $discount[]   = (int)$transaction['memberdisc'];
            }

            // Finding Data
            $debtsdata          = $DebtModel->where('transactionid', $transaction['id'])->find();
            $trxdetailsdata     = $TrxdetailModel->where('transactionid', $transaction['id'])->find();
            $trxpaymentsdata    = $TrxpaymentModel->where('transactionid', $transaction['id'])->where('paymentid !=', '0')->find();

            // Debt Total $ Total Debt Customer & Down Payment
            foreach ($debtsdata as $debt) {
                // Debt Value
                $debtvalue[]        = $debt['value'];

                // Down Payment
                if ($transaction['amountpaid'] != 0) {
                    $downpayment[]  = $transaction['amountpaid'];
                }

                // Debt Member
                $totalmember[]      = $MemberModel->find($debt['memberid']);
            }

            // Trxdetail Array
            $totaltrxdet    = count($trxdetailsdata);
            // if ($transaction['discvalue'] != null) {
            //     $disc   = (int)$transaction['discvalue'] / (int)$totaltrxdet;
            // } else {
            //     $disc   = 0;
            // }

            // if ($transaction['memberdisc'] != null) {
            //     $disc   = (int)$transaction['memberdisc'] / (int)$totaltrxdet;
            // } else {
            //     $disc   = 0;
            // }

            if ($transaction['discvalue'] != null) {
                $disc[]   = (int)$transaction['discvalue'];
            } else {
                $disc[]   = 0;
            }

            if ($transaction['memberdisc'] != null) {
                $disc[]   = (int)$transaction['memberdisc'];
            } else {
                $disc[]   = 0;
            }

            foreach ($trxdetailsdata as $trxdet) {
                // Transaction Detail Margin Modal
                // $marginmodal[]      = ((Int)$trxdet['marginmodal'] * (Int)$trxdet['qty']) - ((int)$disc);
                $marginmodal[]      = ((Int)$trxdet['marginmodal'] * (Int)$trxdet['qty']);
    
                // Transaction Detail Discount Variant
                if ($trxdet['discvar'] != 0) {
                    $discount[]     = $trxdet['discvar'];
                }

                // Transaction Detail Discount Global
                if ($trxdet['globaldisc'] != '0') {
                    $discountglobal[]       = $trxdet['globaldisc'];
                }

                // Data Variant
                $variantsdata       = $VariantModel->find($trxdet['variantid']);

                if (!empty($variantsdata)) {
                    $varid          = $variantsdata['id'];
                    $varname        = $variantsdata['name'];
                    $productsdata   = $ProductModel->find($variantsdata['productid']);

                    if (!empty($productsdata)) {
                        $prodname   = $productsdata['name'];
    
                        // // Transaction Detail Margin Modal
                        // // $marginmodal[]      = ((Int)$trxdet['marginmodal'] * (Int)$trxdet['qty']) - ((int)$disc);
                        // $marginmodal[]      = ((Int)$trxdet['marginmodal'] * (Int)$trxdet['qty']);

                        // // Products Sales
                        // $productsale[]  = $trxdet['qty'];
    
                        // // Transaction Detail Discount Variant
                        // if ($trxdet['discvar'] != 0) {
                        //     $discount[]     = $trxdet['discvar'];
                        // }

                        // // Transaction Detail Margin Modal
                        // $marginmodal[] = ((int)$trxdet['marginmodal'] * (int)$trxdet['qty']);

                        // // Transaction Detail Discount Variant
                        // if ($trxdet['discvar'] != '0') {
                        //     $discount[]     = $trxdet['discvar'];
                        // }
                        // if ($trxdet['globaldisc'] != '0') {
                        //     $discount[]     = $trxdet['globaldisc'];
                        // }
                    } else {
                        $prodname   = '';
    
                        // // Transaction Detail Margin Modal
                        // // $marginmodal[]      = ((Int)$trxdet['marginmodal'] * (Int)$trxdet['qty']) - ((int)$disc);
                        // $marginmodal[]      = 0;
    
                        // // Products Sales
                        // $productsale[]  = 0;
    
                        // // Transaction Detail Discount Variant
                        // if ($trxdet['discvar'] != 0) {
                        //     $discount[]     = 0;
                        // }

                        // // Transaction Detail Margin Modal
                        // $marginmodal[] = 0;
                        
                        // // Transaction Detail Discount Variant
                        // // if ($trxdet['discvar'] != '0') {
                        //     $discount[]      = 0;
                        // // }
                        // // if ($trxdet['globaldisc'] != '0') {
                        //     $discount[]       = 0;
                        // // }
                    }
                } else {
                    $varname        = '';
                    $varid          = '';
                    $productsdata   = '';
                    $prodname       = '';
                }

                // Data Bundle
                $bundlesdata    = $BundleModel->find($trxdet['bundleid']);

                if (!empty($bundlesdata)) {
                    $bundleid       = $bundlesdata['id'];
                    $bundlename     = $bundlesdata['name'];
    
                    // // Transaction Detail Margin Modal
                    // // $marginmodal[]      = ((Int)$trxdet['marginmodal'] * (Int)$trxdet['qty']) - ((int)$disc);
                    // $marginmodal[]      = ((Int)$trxdet['marginmodal'] * (Int)$trxdet['qty']);
    
                    // // Products Sales
                    // $productsale[]  = $trxdet['qty'];
    
                    // // Transaction Detail Discount Variant
                    // if ($trxdet['discvar'] != 0) {
                    //     $discount[]     = $trxdet['discvar'];
                    // }

                    // // Transaction Detail Margin Modal
                    // $marginmodal[] = ((int)$trxdet['marginmodal'] * (int)$trxdet['qty']);

                    // // Transaction Detail Discount Variant
                    // if ($trxdet['discvar'] != '0') {
                    //     $discount[]     = $trxdet['discvar'];
                    // }
                    // if ($trxdet['globaldisc'] != '0') {
                    //     $discount[]     = $trxdet['globaldisc'];
                    // }
                } else {
                    $bundleid       = '';
                    $bundlename     = '';
    
                    // // Transaction Detail Margin Modal
                    // // $marginmodal[]      = ((Int)$trxdet['marginmodal'] * (Int)$trxdet['qty']) - ((int)$disc);
                    // $marginmodal[]      = 0;
    
                    // // Products Sales
                    // $productsale[]  = 0;
    
                    // // Transaction Detail Discount Variant
                    // if ($trxdet['discvar'] != 0) {
                    //     $discount[]     = 0;
                    // }

                    // // Transaction Detail Margin Modal
                    // $marginmodal[] = 0;

                    // // Transaction Detail Discount Variant
                    // // if ($trxdet['discvar'] != '0') {
                    //     $discount[]     = 0;
                    // // }
                    // // if ($trxdet['globaldisc'] != '0') {
                    //     $discount[]     = 0;
                    // // }
                }

                // Best Selling Product
                if ($trxdet['variantid'] != 0) {
                    $bestid     = $varid;
                    $name       = $prodname.' - '.$varname;
                } else {
                    $bestid     = $bundleid;
                    $name       = $bundlename;
                }
    
                // Products Sales
                $productsale[]  = $trxdet['qty'];

                // Product Data For Best Selling
                $bestproduct[]    = [
                    'id'        => $bestid,
                    'name'      => $name,
                    'qty'       => $trxdet['qty'],
                ];
            }
    
            foreach ($trxpaymentsdata as $trxpay) {
                $payments           = $PaymentModel->find($trxpay['paymentid']);
                $bestpayment[] = [
                    'id'    => $payments['id'],
                    'name'  => $payments['name'],
                    'value' => $trxpay['value'],
                ];
            }
            
            // Bussy Day
            $datesale   = date_create($transaction['date']);
            $saledate   = $datesale->format('Y-m-d');
            $hoursale   = $datesale->format('H');
            $hour[]     = date('H', strtotime($transaction['date']));
            $saly[]     = [
                'date'  => $saledate,
                'value' => $transaction['value'],
                'hours' => $hoursale,
                'trx'   => '1',
            ];
        }

        // Total Transaction
        $transactiondata['totaltrx']        = count($transactions);

        // Total Transaction Value
        $transactiondata['totaltrxvalue']   = array_sum($trxvalue);

        // Total Point Used
        $transactiondata['totalpointused']  = array_sum($pointused);

        // Total Discount
        $transactiondata['totaldiscount']   = array_sum($discount);

        // Total Gross
        $transactiondata['gross']           = (Int)$transactiondata['totaltrxvalue'] + (Int)$transactiondata['totalpointused'] + (Int)$transactiondata['totaldiscount'];

        // Total Profit
        $trxdisc                            = array_sum($disc);
        $totalmarginmodal                   = array_sum($marginmodal);
        $transactiondata['profit']          = (Int)$totalmarginmodal - (Int)$trxdisc;

        // Total Debt Value
        $transactiondata['debtvalue']       = array_sum($debtvalue);

        // Total Down Payment
        $transactiondata['dp']              = array_sum($downpayment);

        // Total Member Debt
        $transactiondata['debtmember']      = count($totalmember);

        // Total Product Sale
        $transactiondata['productsale']     = array_sum($productsale);

        // Top 3 Product Sell
        $bestseller = [];
        foreach ($bestproduct as $product) {
            if (!isset($bestseller[$product['id']])) {
                $bestseller[$product['id']] = $product;
            } else {
                $bestseller[$product['id']]['qty'] += $product['qty'];
            }
        }
        array_multisort(array_column($bestseller, 'qty'), SORT_DESC, $bestseller);
        $transactiondata['bestsell'] = array_slice($bestseller, 0, 3);

        // Top 3 Payment Method
        $bestpay = [];
        foreach ($bestpayment as $paymet) {
            if (!isset($bestpay[$paymet['id']])) {
                $bestpay[$paymet['id']] = $paymet;
            } else {
                $bestpay[$paymet['id']]['value'] += $paymet['value'];
            }
        }
        array_multisort(array_column($bestpay, 'value'), SORT_DESC, $bestpay);
        $transactiondata['bestpayment'] = array_slice($bestpay, 0, 3);

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

        $transactiondata['cashin']  = array_sum($cashin);
        $transactiondata['cashout'] = array_sum($cashout);

        // Today's Expenses
        $todayexp = array();
        foreach ($todayexpenses as $todexp) {
            if ($todexp['type'] === "1") {
                $todayexp[] = $todexp['qty'];
            }
        }
        $transactiondata['todayexp']    = array_sum($todayexp);

        // Today's Sales
        $trxtoday = array();
        foreach ($trxtodays as $trxtod) {
            $trxtoday[] = $trxtod['value'];
        }
        $transactiondata['todaytrx']    = array_sum($trxtoday);

        // Month's Sales
        $thismonth = [];
        foreach ($trxmonths as $trxmonth) {
            $thismonth[] = (int)$trxmonth['value'];
        }
        $transactiondata['monthtrx']    = array_sum($thismonth);

        // Average transaction
        $salesresult = array_sum($trxvalue);

        if ($salesresult !== 0) {
            $salestrx                   = count($transactions);
            $averagedays                = $salesresult / $salestrx;
            $sale                       = sprintf("%.2f", $averagedays);
            $doblesale                  = ceil($sale);
            $saleaverage                = sprintf("%.2f", $doblesale);
        } else {
            $averagedays                = "0";
            $sale                       = sprintf("%.2f", $averagedays);
            $doblesale                  = ceil($sale);
            $saleaverage                = sprintf("%.2f", $doblesale);
        }
        $transactiondata['saleaverage'] = $saleaverage;

        // Average per days
        $now                            = date_create($startdate);
        $your_date                      = date_create($enddate)->modify('+1 day');
        $datediff                       = date_diff($now, $your_date);
        $days                           = $datediff->format("%a");
        $dateaverage                    = ceil($salesresult / (int)$days);
        $resultaveragedays              = sprintf("%.2f", $dateaverage);
        $transactiondata['averagedays'] = $resultaveragedays;

        // Bussy Days
        $busies = array_count_values($hour);
        arsort($busies);
        $busyhours = array_slice(array_keys($busies), 0, 1, true);
        if (!empty($busyhours)) {
            $bussytime = $busyhours[0] . ':00';
        } else {
            $bussytime = "00:00";
        }
        $transactiondata['bussytime'] = $bussytime;

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
        $daysale        = array_slice($saledet, 0, 1);
        $bussyday       = 0;
        foreach ($daysale as $days) {
            $datesale   = date_create($days['date']);
            $bussyday   = $datesale->format('l');
        }
        $transactiondata['bussyday'] = $bussyday;
        
        // Outlet Bill
        $money  = [];
        foreach ($cashes as $cash) {
            $money[]    = $cash['qty'];
        }
        $transactiondata['bills']   = array_sum($money);

        // dd($transactiondata);

        // Parsing Data To View
        $data                       = $this->data;
        $data['title']              = lang('Global.dashboard');
        $data['description']        = lang('Global.dashdesc');
        $data['transactiondata']    = $transactiondata;
        // $data['sales']           = $summary;
        // $data['profit']          = $keuntunganmodal;
        $data['products']           = $products;
        $data['variants']           = $variants;
        $data['bundles']            = $bundles;
        $data['bundets']            = $bundets;
        // $data['trxamount']       = $trxamount;
        // $data['qtytrxsum']       = $qtytrxsum;
        // $data['pointusedsum']    = $pointusedsum;
        // $data['gross']           = $gross;
        // $data['cashinsum']       = $cashinsum;
        // $data['cashoutsum']      = $cashoutsum;
        // $data['top3prod']        = $best3;
        // $data['top3paymet']      = $bestpay3;
        $data['stocks']             = $stok;
        // $data['average']         = (int)$saleaverage;
        $data['startdate']          = strtotime($startdate);
        $data['enddate']            = strtotime($enddate);
        // $data['trxdebtval']      = array_sum($trxdebtval);
        // $data['averagedays']     = $resultaveragedays;
        // $data['bussyday']        = $day;
        // $data['bussytime']       = $busy;
        // $data['todayexps']       = $todayexps;
        $data['month']              = $month;
        // $data['sumtrxtoday']     = $sumtrxtoday;

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

    public function sku()
    {
        $ProductModel   = new ProductModel;
        $VariantModel   = new VariantModel;
        $CategoryModel  = new CategoryModel;
        // $variants       = $VariantModel->findAll();

        // $arr    = [];

        // foreach ($variants as $variant) {
        //     $arr[]  = $variant['sku'];
        // }
        
        // $findDuplicate = array_diff_assoc( 
        //     $arr, 
        //     array_unique($arr) 
        // ); 
        // dd($findDuplicate);

        $category       = $CategoryModel->findAll();

        foreach ($category as $cate) {
            $i              = 1;
            $products   = $ProductModel->where('catid', $cate['id'])->find();

            if (!empty($products)) {
                foreach ($products as $prod) {
                    $variants   = $VariantModel->where('productid', $prod['id'])->find();

                    if (!empty($variants)) {
                        foreach ($variants as $variant) {
                            $variantdata = [
                                'id'        => $variant['id'],
                                // 'sku'       => strtoupper($cate['catcode'].substr(md5(hexdec(uniqid(rand(), true))), 0, 6)),
                                'sku'       => strtoupper($cate['catcode'].str_pad($i++, 6, '0', STR_PAD_LEFT)),
                            ];
                            $VariantModel->save($variantdata);
                        }
                    }
                }
            }
            // dd($products);
        }

        // $variants       = $VariantModel->findAll();

        // foreach ($variants as $variant) {
        //     $variantdata = [
        //         'id'        => $variant['id'],
        //         'sku'       => strtoupper(substr(md5(hexdec(uniqid(rand(), true))), 0, 8)),
        //     ];
        //     $VariantModel->save($variantdata);
        // }

        return redirect()->to('product');
    }

    public function stockmove()
    {
        // Calling Models
        $StockMovementModel         = new StockMovementModel;
        $StockMoveDetailModel       = new StockMoveDetailModel;
        $StockMovementModelBackup   = new StockMovementModelBackup;

        // Populating Data
        $oldstockmove               = $StockMovementModelBackup->findAll();
        dd($oldstockmove);
        foreach ($oldstockmove as $old) {
            $datamove   = [
                'id'                => $old['id'],
                'origin'            => $old['origin'],
                'destination'       => $old['destination'],
                'date'              => $old['date'],
                'status'            => 3,
            ];
            $StockMovementModel->insert($datamove);

            // Get Stock Movement ID
            $stockmoveid            = $StockMovementModel->getInsertID();

            $datadetail = [
                'stockmoveid'       => $stockmoveid,
                'variantid'         => $old['variantid'],
                'qty'               => $old['qty'],
            ];
            $StockMoveDetailModel->insert($datadetail);
        }

        // Return
        return redirect()->to('stockmove');
    }

    public function phpinfo()
    {
        phpinfo();
    }
}
