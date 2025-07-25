<?php

namespace App\Controllers;

use App\Models\BundledetailModel;
use App\Models\BundleModel;
use App\Models\BrandModel;
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
use App\Models\BookingModel;
use App\Models\BookingdetailModel;
use App\Models\PurchaseModel;
use App\Models\PurchasedetailModel;
use App\Models\PresenceModel;
use App\Models\GroupUserModel;
use App\Models\SopModel;
use App\Models\SopDetailModel;
use Myth\Auth\Models\GroupModel;

class Report extends BaseController
{
    protected $data;
    protected $db, $builder;
    protected $auth;
    protected $config;
    
    public function test()
    {
        // Calling Models
        $TransactionModel       = new TransactionModel;
        $TrxdetailModel         = new TrxdetailModel;
        $GconfigModel           = new GconfigModel;
        // Populating Data
        $trxdetails             = $TrxdetailModel->findAll();
        $Gconfig                = $GconfigModel->first();

        $input = $this->request->getGet('daterange');

        if (!empty($input)) {
            $daterange = explode(' - ', $input);
            $startdate = $daterange[0];
            $enddate = $daterange[1];
        } else {
            $startdate  = date('Y-m-1' . ' 00:00:00');
            $enddate    = date('Y-m-t' . ' 23:59:59');
        }

        // if ($startdate === $enddate) {
        //     $transaction = $TransactionModel->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->find();
        // } else {
            $transaction = $TransactionModel->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->find();
        // }

        $summary = array_sum(array_column($transaction, 'value'));
        $discounts = array();
        $transactionarr = array();

        $transactions = array();
        foreach ($transaction as $trx) {
            $discounttrx = array();
            $discounttrxpersen = array();
            $discountvariant = array();
            $discountpoin = array();
            foreach ($trxdetails as $trxdetail) {
                if ($trx['id'] === $trxdetail['transactionid']) {
                    if ($trx['disctype'] === "0") {
                        $discounttrx[]          = $trx['discvalue'];
                    }
                    if ($trx['disctype'] !== "0") {
                        $discounttrxpersen[]    = ((int)$trxdetail['value'] * (int)$trxdetail['qty']) - ((int)$trx['value'] + (int)$trxdetail['discvar']);
                    }
                    $discountvariant[]          = $trxdetail['discvar'];
                    $discountpoin[]             = $trx['pointused'];
                }
            }

            $transactiondisc = array_sum($discounttrx) +  array_sum($discounttrxpersen);
            $variantdisc     = array_sum($discountvariant);
            $poindisc        = array_sum($discountpoin);

            $discounts[] = [
                'trxdisc'       => $transactiondisc,
                'variantdis'    => $variantdisc,
                'poindisc'      => $poindisc,
            ];

            $date = date_create($trx['date']);
            $transactions[] = [
                'date'      =>  date_format($date, "d/m/Y"),
                'value'     => $trx['value'],
            ];
        }


        $transactionarr[] = $transactions;
        $trxvar = array_sum(array_column($discounts, 'variantdis'));
        $trxdis = array_sum(array_column($discounts, 'trxdisc'));
        $dispoint = array_sum(array_column($discounts, 'poindisc'));

        $grossales = (int)$summary + (int)$trxvar + (int)$trxdis + (int)$dispoint;

        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.salesreport');
        $data['description']    = lang('Global.transactionListDesc');
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);
        $data['transactions']   = $transactions;
        $data['transactionarr'] = $transactionarr;
        $data['result']         = $summary;
        $data['gross']          = $grossales;
    }

    public function dailysell()
    {
        // Calling Models
        $TransactionModel       = new TransactionModel;
        $TrxdetailModel         = new TrxdetailModel;

        $transactions       = array();
        $transactionarr     = array();
        $memberdisc         = array();
        $discounttrx        = array();
        $discountvariant    = array();
        $discountpoin       = array();
        $discountglobal     = array();
        $discountmember     = array();
        $marginmodals       = array();
        $margindasars       = array();

        if ($this->data['outletPick'] === null) {
            $transaction = $TransactionModel->where('date >=', date('Y-m-d 00:00:00'))->where('date <=', date('Y-m-d 23:59:59'))->find();
        } else {
            $transaction = $TransactionModel->where('date >=', date('Y-m-d 00:00:00'))->where('date <=', date('Y-m-d 23:59:59'))->where('outletid', $this->data['outletPick'])->find();
        }
        // $summary = array_sum(array_column($transaction, 'value'));

        if (!empty($transaction)) {
            foreach ($transaction as $trx) {
                $time                               = date('H', strtotime($trx['date']));
                $transactions[$time]['date']        = date('H', strtotime($trx['date']));
                $transactions[$time]['val'][]       = $trx['value'];

                // Transaction Discount
                if (!empty($trx['discvalue'])) {
                    $discounttrx[]                      = $trx['discvalue'];
                    $transactions[$time]['trxdisc'][]   = $trx['discvalue'];
                } else {
                    $transactions[$time]['trxdisc'][]   = [];
                }
    
                // Point Used
                $discountpoin[]                     = $trx['pointused'];
                $transactions[$time]['pointdisc'][] = $trx['pointused'];

                // Member Discount
                $memberdisc[]                           = $trx['memberdisc'];
                $transactions[$time]['memberdisc'][]    = $trx['memberdisc'];

                // Discount Variant
                $trxdetails  = $TrxdetailModel->where('transactionid', $trx['id'])->find();
                if (!empty($trxdetails)) {
                    foreach ($trxdetails as $trxdetail) {
                        // Transaction Detail Discount Variant
                        if ($trxdetail['discvar'] != '0') {
                            $discountvariant[]                  = $trxdetail['discvar'];
                            $transactions[$time]['vardisc'][]   = $trxdetail['discvar'];
                        } else {
                            $transactions[$time]['vardisc'][]   = [];
                        }
        
                        // Transaction Detail Discount Global
                        if ($trxdetail['globaldisc'] != '0') {
                            $discountglobal[]                   = $trxdetail['globaldisc'];
                            $transactions[$time]['globdisc'][]  = $trxdetail['globaldisc'];
                        } else {
                            $transactions[$time]['globdisc'][]  = [];
                        }
        
                        // Transaction Detail Discount Member
                        if ($trxdetail['memberdisc'] != '0') {
                            $discountmember[]                   = $trxdetail['memberdisc'];
                            $transactions[$time]['membdisc'][]  = $trxdetail['memberdisc'];
                        } else {
                            $transactions[$time]['membdisc'][]  = [];
                        }

                        // Transaction Detail Margin Modal
                        $marginmodals[]                         = ((int)$trxdetail['marginmodal'] * (int)$trxdetail['qty']);
                        $transactions[$time]['profitmodal'][]   = ((int)$trxdetail['marginmodal'] * (int)$trxdetail['qty']);

                        // Transaction Detail Margin Dasar
                        $margindasars[]                         = ((int)$trxdetail['margindasar'] * (int)$trxdetail['qty']);
                        $transactions[$time]['profitdasar'][]   = ((int)$trxdetail['margindasar'] * (int)$trxdetail['qty']);
                    }
                }
            }
        }

        // Data Chart
        if (!empty($transactions)) {
            foreach ($transactions as $trxdat) {
                $transactionarr[]  = [
                    'waktu'             => $trxdat['date'],
                    'value'             => array_sum($trxdat['val']),
                    'profitmodal'       => array_sum($trxdat['profitmodal']),
                    'profitdasar'       => array_sum($trxdat['profitdasar']),
                    'trxdisc'           => array_sum($trxdat['trxdisc']),
                    'vardisc'           => array_sum($trxdat['vardisc']),
                    'globdisc'          => array_sum($trxdat['globdisc']),
                    'membdisc'          => array_sum($trxdat['membdisc']),
                    'pointdisc'         => array_sum($trxdat['pointdisc']),
                ];
            }
        }

        $transactiondisc    = (int)(array_sum($discounttrx)) + (int)(array_sum($memberdisc));
        $variantdisc        = array_sum($discountvariant);
        $globaldisc         = array_sum($discountglobal);
        $memberdiscitem     = array_sum($discountmember);
        $poindisc           = array_sum($discountpoin);
        $marginmodalsum     = array_sum($marginmodals);
        $margindasarsum     = array_sum($margindasars);
        $salesresult        = array_sum(array_column($transactionarr, 'value'));
        $grossales          = (Int)$salesresult + (Int)$variantdisc + (Int)$globaldisc + (Int)$memberdiscitem + (Int)$transactiondisc + (Int)$poindisc;
        $profitmodal        = (Int)$marginmodalsum - (Int)$transactiondisc - (Int)$poindisc;
        $profitdasar        = (Int)$margindasarsum - (Int)$transactiondisc - (Int)$poindisc;
        
        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = 'Laporan Penjualan Harian';
        $data['description']    = 'Developed by PT. Kodebiner Teknologi Indonesia';
        $data['transactions']   = $transactionarr;
        $data['result']         = $salesresult;
        $data['gross']          = $grossales;
        $data['profitmodal']    = $profitmodal;
        $data['profitdasar']    = $profitdasar;
        $data['trxvardis']      = $variantdisc;
        $data['trxglodis']      = $globaldisc;
        $data['trxmemdis']      = $memberdiscitem;
        $data['trxdisc']        = $transactiondisc;
        $data['poindisc']       = $poindisc;

        return view('Views/report/dailysell', $data);
    }

    public function penjualan()
    {
        // Calling Models
        $TransactionModel       = new TransactionModel;
        $TrxdetailModel         = new TrxdetailModel;
        $VariantModel           = new VariantModel;
        $ProductModel           = new ProductModel;
        $BundleModel            = new BundleModel;

        $input = $this->request->getGet('daterange');

        if (!empty($input)) {
            $daterange = explode(' - ', $input);
            $startdate = strtotime($daterange[0]);
            $enddate = strtotime($daterange[1]);
        } else {
            // $startdate  = strtotime(date('Y-m-1' . ' 00:00:00'));
            // $enddate    = strtotime(date('Y-m-t' . ' 23:59:59'));
            $startdate  = strtotime(date('Y-m-d' . ' 00:00:00'));
            $enddate    = strtotime(date('Y-m-d' . ' 23:59:59'));
        }

        $transactions       = array();
        $profits            = array();
        $transactionarr     = array();
        $memberdisc         = array();
        $discounttrx        = array();
        $discountvariant    = array();
        $discountpoin       = array();
        $discountglobal     = array();
        $discountmember     = array();

        for ($date = $startdate; $date <= $enddate; $date += (86400)) {
            if ($this->data['outletPick'] === null) {
                $transaction = $TransactionModel->where('date >=', date('Y-m-d 00:00:00', $date))->where('date <=', date('Y-m-d 23:59:59', $date))->find();
            } else {
                $transaction = $TransactionModel->where('date >=', date('Y-m-d 00:00:00', $date))->where('date <=', date('Y-m-d 23:59:59', $date))->where('outletid', $this->data['outletPick'])->find();
            }
            $summary = array_sum(array_column($transaction, 'value'));

            $discountforprofit  = [];
            $trxdisc            = [];
            $vardisc            = [];
            $globdisc           = [];
            $membdisc           = [];
            $pointdisc          = [];
            $marginmodals       = [];
            $margindasars       = [];
            foreach ($transaction as $trx) {
                $trxdetails  = $TrxdetailModel->where('transactionid', $trx['id'])->find();

                if (!empty($trx['discvalue'])) {
                    // if ($trx['disctype'] == "0") {
                    //     $discounttrx[]  = $trx['discvalue'];
                    // } else {
                    //     $discounttrxpersen[]  = (int)$trx['value'] * ((int)$trx['discvalue'] / 100);
                    // }
                    $discounttrx[]  = $trx['discvalue'];
                    $trxdisc[]      = $trx['discvalue'];
                }

                // Discount Point Used
                $discountpoin[]             = $trx['pointused'];
                $pointdisc[]                = $trx['pointused'];

                // Member Discount
                $memberdisc[]               = $trx['memberdisc'];
                $trxdisc[]                  = $trx['memberdisc'];
                
                if ($trx['discvalue'] != '0') {
                    $discountforprofit[]   = (int)$trx['discvalue'];
                } else {
                    $discountforprofit[]   = 0;
                }

                if ($trx['memberdisc'] != '0') {
                    $discountforprofit[]   = (int)$trx['memberdisc'];
                } else {
                    $discountforprofit[]   = 0;
                }

                if ($trx['pointused'] != '0') {
                    $discountforprofit[]   = (int)$trx['pointused'];
                } else {
                    $discountforprofit[]   = 0;
                }

                foreach ($trxdetails as $trxdetail) {
                    // if ($trx['disctype'] === "0") {
                    //     $discounttrx[]          = $trx['discvalue'];
                    // }
                    // if ($trx['disctype'] !== "0") {
                    //     $sub =  ((int)$trxdetail['value'] * (int)$trxdetail['qty']);
                    //     $discounttrxpersen[]    =  ((int)$trx['discvalue'] / 100) * (int)$sub;
                    // }
                    // $discountvariant[]          = $trxdetail['discvar'];
    
                    // Transaction Detail Discount Variant
                    if ($trxdetail['discvar'] != 0) {
                        $discountvariant[]      = $trxdetail['discvar'];
                        $vardisc[]              = $trxdetail['discvar'];
                    }

                    // Transaction Detail Discount Global
                    if ($trxdetail['globaldisc'] != '0') {
                        $discountglobal[]       = $trxdetail['globaldisc'];
                        $globdisc[]             = $trxdetail['globaldisc'];
                    }

                    // Transaction Detail Discount Member
                    if ($trxdetail['memberdisc'] != '0') {
                        $discountmember[]       = $trxdetail['memberdisc'];
                        $membdisc[]             = $trxdetail['memberdisc'];
                    }

                    // Transaction Detail Margin Modal
                    $marginmodals[] = ((int)$trxdetail['marginmodal'] * (int)$trxdetail['qty']);

                    // Transaction Detail Margin Dasar
                    $margindasars[] = ((int)$trxdetail['margindasar'] * (int)$trxdetail['qty']);

                    // Data Variant
                    $variantsdata       = $VariantModel->find($trxdetail['variantid']);

                    if (!empty($variantsdata)) {
                        $productsdata   = $ProductModel->find($variantsdata['productid']);

                        // if (!empty($productsdata)) {
                        //     // Transaction Detail Discount Variant
                        //     if ($trxdetail['discvar'] != '0') {
                        //         $discountvariant[]      = $trxdetail['discvar'];
                        //     }
                        //     if ($trxdetail['globaldisc'] != '0') {
                        //         $discountglobal[]       = $trxdetail['globaldisc'];
                        //     }
                        // } else {
                        //     // Transaction Detail Discount Variant
                        //     // if ($trxdetail['discvar'] != '0') {
                        //         $discountvariant[]      = 0;
                        //         // $discountglobal[]       = 0;
                        //     // }
                        //     // if ($trxdetail['globaldisc'] != '0') {
                        //         $discountglobal[]       = 0;
                        //     // }
                        // }
                    } else {
                        $productsdata   = '';
                    }

                    // Data Bundle
                    $bundlesdata    = $BundleModel->find($trxdetail['bundleid']);

                    // if (!empty($bundlesdata)) {
                    //     // Transaction Detail Discount Variant
                    //     if ($trxdetail['discvar'] != '0') {
                    //         $discountvariant[]      = $trxdetail['discvar'];
                    //     }
                    //     if ($trxdetail['globaldisc'] != '0') {
                    //         $discountglobal[]       = $trxdetail['globaldisc'];
                    //     }
                    // } else {
                    //     // Transaction Detail Discount Variant
                    //     // if ($trxdetail['discvar'] != '0') {
                    //         $discountvariant[]      = 0;
                    //     // }
                    //     // if ($trxdetail['globaldisc'] != '0') {
                    //         $discountglobal[]       = 0;
                    //     // }
                    // }
                }
            }

            $totaldisc          = array_sum($discountforprofit);
            $marginmodalsum     = array_sum($marginmodals);
            $margindasarsum     = array_sum($margindasars);
            $profitmodal        = (Int)$marginmodalsum - (Int)$totaldisc;
            $profitdasar        = (Int)$margindasarsum - (Int)$totaldisc;

            $transactions[] = [
                'date'              => date('d/m/y', $date),
                'value'             => $summary,
                'profitmodal'       => $profitmodal,
                'profitdasar'       => $profitdasar,
                'trxdisc'           => array_sum($trxdisc),
                'vardisc'           => array_sum($vardisc),
                'globdisc'          => array_sum($globdisc),
                'membdisc'          => array_sum($membdisc),
                'pointdisc'         => array_sum($pointdisc),
            ];
        }

        // $transactiondisc = (int)(array_sum($discounttrx)) + (int)(array_sum($discounttrxpersen)) + (int)(array_sum($memberdisc));
        $transactiondisc    = (int)(array_sum($discounttrx)) + (int)(array_sum($memberdisc));
        $variantdisc        = array_sum($discountvariant);
        $globaldisc         = array_sum($discountglobal);
        $memberdiscitem     = array_sum($discountmember);
        $poindisc           = array_sum($discountpoin);

        // $totaldisc          = (Int)$variantdisc + (Int)$globaldisc + (Int)$poindisc + (Int)$transactiondisc;
        // $marginmodalsum     = array_sum($marginmodals);
        // $margindasarsum     = array_sum($margindasars);

        // $profits[]     = [
        //     'profitmodal'       => (Int)$marginmodalsum - (Int)$totaldisc,
        //     'profitdasar'       => (Int)$margindasarsum - (Int)$totaldisc,
        // ];

        // $dicount[] = [
        //     'trxdisc'       => $transactiondisc,
        //     'variantdis'    => $variantdisc,
        //     'globaldis'     => $globaldisc,
        //     'poindisc'      => $poindisc,
        // ];

        $transactionarr[] = $transactions;

        // Sales Result
        $salesresult = array_sum(array_column($transactions, 'value'));
        $grossales = (Int)$salesresult + (Int)$variantdisc + (Int)$globaldisc + (Int)$memberdiscitem + (Int)$transactiondisc + (Int)$poindisc;

        // Profit Result
        $keuntunganmodal = array_sum(array_column($transactions, 'profitmodal'));
        $keuntungandasar = array_sum(array_column($transactions, 'profitdasar'));

        // dd($transactions);

        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.salesreport');
        $data['description']    = lang('Global.transactionListDesc');
        $data['startdate']      = $startdate;
        $data['enddate']        = $enddate;
        $data['transactions']   = $transactions;
        // $data['profits']        = $profits;
        $data['transactionarr'] = $transactionarr;
        $data['result']         = $salesresult;
        $data['gross']          = $grossales;
        $data['modals']         = $keuntunganmodal;
        $data['dasars']         = $keuntungandasar;
        $data['trxvardis']      = $variantdisc;
        $data['trxglodis']      = $globaldisc;
        $data['trxmemdis']      = $memberdiscitem;
        $data['trxdisc']        = $transactiondisc;
        $data['poindisc']       = $poindisc;

        return view('Views/report/penjualan', $data);
    }

    public function keuntungan()
    {
        // Calling Models
        $TransactionModel       = new TransactionModel;
        $TrxdetailModel         = new TrxdetailModel;
        $VariantModel           = new VariantModel;
        $ProductModel           = new ProductModel;
        $BundleModel            = new BundleModel;

        $input = $this->request->getGet('daterange');

        if (!empty($input)) {
            $daterange = explode(' - ', $input);
            $startdate = strtotime($daterange[0]);
            $enddate = strtotime($daterange[1]);
        } else {
            // $startdate  = strtotime(date('Y-m-1' . ' 00:00:00'));
            // $enddate    = strtotime(date('Y-m-t' . ' 23:59:59'));
            $startdate  = strtotime(date('Y-m-d' . ' 00:00:00'));
            $enddate    = strtotime(date('Y-m-d' . ' 23:59:59'));
        }

        $transactions   = array();
        $transactionarr = array();
        for ($date = $startdate; $date <= $enddate; $date += (86400)) {
            if ($this->data['outletPick'] === null) {
                $transaction = $TransactionModel->where('date >=', date('Y-m-d 00:00:00', $date))->where('date <=', date('Y-m-d 23:59:59', $date))->find();
            } else {
                $transaction = $TransactionModel->where('date >=', date('Y-m-d 00:00:00', $date))->where('date <=', date('Y-m-d 23:59:59', $date))->where('outletid', $this->data['outletPick'])->find();
            }
            // $variants    = $VariantModel->findAll();

            // $summary = array_sum(array_column($transaction, 'value'));
            $marginmodals   = array();
            $margindasars   = array();
            $discount       = array();

            foreach ($transaction as $trx) {
                $trxdetails     = $TrxdetailModel->where('transactionid', $trx['id'])->find();
                
                if ($trx['discvalue'] != '0') {
                    $discount[]   = (int)$trx['discvalue'];
                } else {
                    $discount[]   = 0;
                }

                if ($trx['memberdisc'] != '0') {
                    $discount[]   = (int)$trx['memberdisc'];
                } else {
                    $discount[]   = 0;
                }

                if ($trx['pointused'] != '0') {
                    $discount[]   = (int)$trx['pointused'];
                } else {
                    $discount[]   = 0;
                }

                foreach ($trxdetails as $trxdetail) {
                    // Transaction Detail Margin Modal
                    $marginmodals[] = ((int)$trxdetail['marginmodal'] * (int)$trxdetail['qty']);

                    // Transaction Detail Margin Dasar
                    $margindasars[] = ((int)$trxdetail['margindasar'] * (int)$trxdetail['qty']);
                }
            }

            $totaldisc      = array_sum($discount);
            $marginmodalsum = array_sum($marginmodals);
            $margindasarsum = array_sum($margindasars);
            $transactions[] = [
                'date'      => date('d/m/y', $date),
                // 'value'     => $summary,
                'modal'     => (Int)$marginmodalsum - (Int)$totaldisc,
                'dasar'     => (Int)$margindasarsum - (Int)$totaldisc,
            ];
        }

        $transactionarr[] = $transactions;

        $keuntunganmodal = array_sum(array_column($transactions, 'modal'));
        $keuntungandasar = array_sum(array_column($transactions, 'dasar'));
        // $trxvalue        = array_sum(array_column($transactions, 'value'));

        // Parsing Data to View
        $data                       = $this->data;
        $data['title']              = lang('Global.profitreport');
        $data['description']        = lang('Global.profitListDesc');
        $data['transactions']       = $transactions;
        $data['modals']             = $keuntunganmodal;
        $data['dasars']             = $keuntungandasar;
        // $data['penjualanDasar']     = $trxvalue;
        // $data['penjualanModal']     = $trxvalue;
        $data['startdate']          = $startdate;
        $data['enddate']            = $enddate;

        return view('Views/report/keuntungan', $data);
    }

    public function payment()
    {
        // Calling Models
        $PaymentModel           = new PaymentModel();
        $TrxpaymentModel        = new TrxpaymentModel();
        $TransactionModel       = new TransactionModel();

        // Populating Data
        $input = $this->request->getGet('daterange');

        if (!empty($input)) {
            $daterange  = explode(' - ', $input);
            $startdate  = $daterange[0];
            $enddate    = $daterange[1];
        } else {
            $startdate  = date('Y-m-d') . ' 00:00:00';
            $enddate    = date('Y-m-d') . ' 23:59:59';
        }

        $inputsearch = $this->request->getGet('search');

        if (!empty($inputsearch)) {
            $payments = $PaymentModel->like('name', $inputsearch)->orderBy('id', 'DESC')->where('outletid', $this->data['outletPick'])->find();
        } else {
            $payments = $PaymentModel->orderBy('id', 'DESC')->where('outletid', $this->data['outletPick'])->find();
        }

        $transactions = $TransactionModel->where('date >=', $startdate)->where('date <=', $enddate)->where('outletid', $this->data['outletPick'])->find();

        $transactiondata = [];

        foreach ($payments as $payment) {
            $transactiondata[$payment['id']] = [
                'name'  => $payment['name'],
                'qty'   => 0,
                'value' => 0,
            ];

            if (!empty($transactions)) {
                foreach ($transactions as $trx) {
                    $trxpayments = $TrxpaymentModel->where('transactionid', $trx['id'])->where('paymentid', $payment['id'])->find();

                    foreach ($trxpayments as $trxpayment) {
                        $transactiondata[$payment['id']]['qty']++;
                        $transactiondata[$payment['id']]['value'] += $trxpayment['value'];
                    }
                }
            }
        }

        // Debt and Redeem Point
        $transactiondata[0] = [
            'name'  => lang('Global.debt'),
            'qty'   => 0,
            'value' => 0,
        ];
        $transactiondata[-1] = [
            'name'  => lang('Global.redeemPoint'),
            'qty'   => 0,
            'value' => 0,
        ];

        if (!empty($transactions)) {
            foreach ($transactions as $trx) {
                $debtpayments = $TrxpaymentModel->where('transactionid', $trx['id'])->where('paymentid', '0')->find();
                $pointpayments = $TrxpaymentModel->where('transactionid', $trx['id'])->where('paymentid', '-1')->find();

                foreach ($debtpayments as $debtpayment) {
                    $transactiondata[0]['qty']++;
                    $transactiondata[0]['value'] += $debtpayment['value'];
                }

                foreach ($pointpayments as $pointpayment) {
                    $transactiondata[-1]['qty']++;
                    $transactiondata[-1]['value'] += $pointpayment['value'];
                }
            }
        }

        // Pastikan semua elemen memiliki key 'value' yang valid
        foreach ($transactiondata as &$item) {
            if (!isset($item['value']) || !is_numeric($item['value'])) {
                $item['value'] = 0;
            }
        }

        // Urutkan berdasarkan value terbesar
        array_multisort(array_column($transactiondata, 'value'), SORT_DESC, $transactiondata);

        $data = $this->data;
        $data['title']          = lang('Global.paymentreport');
        $data['description']    = lang('Global.paymentListDesc');
        $data['payments']       = $transactiondata;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);
        $data['total']          = array_sum(array_column($transactiondata, 'value'));

        return view('Views/report/payment', $data);

        // // $db                     = \Config\Database::connect();
        // $PaymentModel           = new PaymentModel();
        // $TrxpaymentModel        = new TrxpaymentModel();
        // $TransactionModel       = new TransactionModel();

        // // if ($this->data['outletPick'] != null) {
        //     $input = $this->request->getGet('daterange');

        //     if (!empty($input) || $input != null) {
        //         $daterange = explode(' - ', $input);
        //         $startdate = $daterange[0];
        //         $enddate = $daterange[1];
        //     } else {
        //         // $startdate  = date('Y-m-1' . ' 00:00:00');
        //         // $enddate    = date('Y-m-t' . ' 23:59:59');
        //         $startdate  = date('Y-m-d' . ' 00:00:00');
        //         $enddate    = date('Y-m-d' . ' 23:59:59');
        //     }

        //     // $this->db       = \Config\Database::connect();
        //     // $validation     = \Config\Services::validation();
        //     // $this->builder  = $this->db->table('payment');
        //     // $this->config   = config('Auth');
        //     // $this->auth     = service('authentication');
        //     // $pager          = \Config\Services::pager();

        //     $inputsearch    = $this->request->getGet('search');

        //     // if (!empty($inputsearch)) {
        //     //     $payments   = $PaymentModel->like('name', $inputsearch)->orderBy('id', 'DESC')->paginate(20, 'reportpayment');
        //     // } else {
        //     //     $payments   = $PaymentModel->orderBy('id', 'DESC')->paginate(20, 'reportpayment');
        //     // }

        //     // // $newenddate = date('Y-m-d', strtotime($enddate . ' +1 day'));
        //     // $transactionreport   = $db->table('transaction');
        //     // // if ($startdate === $enddate) {
        //     //     $transactionreport->where('date >=', $startdate . " 00:00:00")->where('date <=', $enddate . " 23:59:59");
        //     // // } else {
        //     // //     $transactionreport->where('date >=', $startdate . '00:00:00')->where('date <=', $enddate . '23:59:59');
        //     // // }
        //     // $transaction   = $transactionreport->select('transaction.id as id, payment.id as payid, transaction.date as date, transaction.value as trxvalue, payment.name as payment, trxpayment.value as trxpayval');
        //     // // $transaction   = $transactionreport->where('transaction.paymentid !=', '0');
        //     // $transaction   = $transactionreport->join('trxpayment', 'transaction.id = trxpayment.transactionid', 'left');
        //     // $transaction   = $transactionreport->join('payment', 'trxpayment.paymentid = payment.id', 'left');
        //     // $transaction   = $transactionreport->where('transaction.outletid', $this->data['outletPick']);
        //     // $transaction   = $transactionreport->orderBy('transaction.date', 'DESC');
        //     // $transaction   = $transactionreport->get();
        //     // $transaction   = $transaction->getResultArray();

        //     // $paypay = [];
        //     // $payname = "";
        //     // foreach ($transaction as $trx) {
        //     //     if (!empty($trx['payid'])) {
        //     //         $payname = $trx['payment'];
        //     //     } else {
        //     //         $payname = "DEBT";
        //     //     }
        //     //     $paypay[] = [
        //     //         'id' => $trx['id'],
        //     //         'payid' => $trx['payid'],
        //     //         'name'  => $payname,
        //     //         // 'value' => $trx['trxvalue'],
        //     //         'value' => $trx['trxpayval'],
        //     //         'qty'   => '1',
        //     //     ];
        //     // }
        //     // $paypay = array_values($paypay);

        //     // $paymentval = [];
        //     // foreach ($paypay as $vars) {
        //     //     if (!isset($paymentval[$vars['payid'] . $vars['name']])) {
        //     //         $paymentval[$vars['payid'] . $vars['name']] = $vars;
        //     //     } else {
        //     //         $paymentval[$vars['payid'] . $vars['name']]['value'] += $vars['value'];
        //     //         $paymentval[$vars['payid'] . $vars['name']]['qty'] += $vars['qty'];
        //     //     }
        //     // }
        //     // $paymentval = array_values($paymentval);

        //     // Transaction Data
        //     $transactiondata    = array();

        //     $transactions       = $TransactionModel->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->where('outletid', $this->data['outletPick'])->find();

        //     if (!empty($inputsearch)) {
        //         $payments   = $PaymentModel->like('name', $inputsearch)->orderBy('id', 'DESC')->where('outletid', $this->data['outletPick'])->find();
        //     } else {
        //         $payments   = $PaymentModel->orderBy('id', 'DESC')->where('outletid', $this->data['outletPick'])->find();
        //     }

        //     foreach ($payments as $payment) {
        //         $transactiondata[$payment['id']]['name']    = $payment['name'];
                
        //         $trxtotal           = array();
        //         $trxvalue           = array();
        //         if (!empty($transactions)) {
        //             foreach ($transactions as $trx) {
        //                 $trxpayments    = $TrxpaymentModel->where('transactionid', $trx['id'])->where('paymentid', $payment['id'])->find();
                        
        //                 if (!empty($trxpayments)) {
        //                     foreach ($trxpayments as $trxpayment) {
        //                         $trxtotal[] = $trxpayment['id'];
        //                         $trxvalue[] = $trxpayment['value'];
        //                     }
        //                 }
        //             }
        //         } else {
        //             $trxpayments    = [];
        //             // $debtpayments   = [];
        //             // $pointpayments  = [];
        //             $trxtotal[]     = [];
        //             $trxvalue[]     = [];
        //             // $debttotal[]    = [];
        //             // $debtvalue[]    = [];
        //             // $pointtotal[]   = [];
        //             // $pointvalue[]   = [];
        //         }
        //         $transactiondata[$payment['id']]['qty']         = count($trxtotal);
        //         $transactiondata[$payment['id']]['value']       = array_sum($trxvalue);
        //     }

        //     // Debt And Reedem Point
        //     $transactiondata[0]['name']                 = lang('Global.debt');
        //     $transactiondata[-1]['name']                = lang('Global.redeemPoint');

        //     $debttotal          = [];
        //     $debtvalue          = [];
        //     $pointtotal         = [];
        //     $pointvalue         = [];

        //     if (!empty($transactions)) {
        //         foreach ($transactions as $trx) {
        //             $debtpayments   = $TrxpaymentModel->where('transactionid', $trx['id'])->where('paymentid', '0')->find();
        //             $pointpayments  = $TrxpaymentModel->where('transactionid', $trx['id'])->where('paymentid', '-1')->find();
        
        //             if (!empty($debtpayments)) {
        //                 foreach ($debtpayments as $debtpayment) {
        //                     $debttotal[] = $debtpayment['id'];
        //                     $debtvalue[] = $debtpayment['value'];
        //                 }
        //             }
        
        //             if (!empty($pointpayments)) {
        //                 foreach ($pointpayments as $pointpayment) {
        //                     $pointtotal[]   = $pointpayment['id'];
        //                     $pointvalue[]   = $pointpayment['value'];
        //                 }
        //             }
        //         }
        //         // $transactiondata[0]['qty']                      = count($debttotal);
        //         // $transactiondata[0]['value']                    = array_sum($debtvalue);
        //         // $transactiondata[-1]['qty']                     = count($pointtotal);
        //         // $transactiondata[-1]['value']                   = array_sum($pointvalue);
        //     } else {
        //         $debtpayments   = [];
        //         $pointpayments  = [];
        //         $debttotal[]    = [];
        //         $debtvalue[]    = [];
        //         $pointtotal[]   = [];
        //         $pointvalue[]   = [];
        //     }
        //     $transactiondata[0]['qty']                      = count($debttotal);
        //     $transactiondata[0]['value']                    = array_sum($debtvalue);
        //     $transactiondata[-1]['qty']                     = count($pointtotal);
        //     $transactiondata[-1]['value']                   = array_sum($pointvalue);
        //     array_multisort(array_column($transactiondata, 'value'), SORT_DESC, $transactiondata);

        //     // Parsing Data to View
        //     $data                   = $this->data;
        //     $data['title']          = lang('Global.paymentreport');
        //     $data['description']    = lang('Global.paymentListDesc');
        //     $data['payments']       = $transactiondata;
        //     $data['startdate']      = strtotime($startdate);
        //     $data['enddate']        = strtotime($enddate);
        //     $data['total']          = array_sum(array_column($transactiondata, 'value'));
        //     // $data['pager']          = $PaymentModel->pager;

        //     return view('Views/report/payment', $data);
        // // } else {
        // //     return redirect()->to('cashinout')->with('error', lang('Global.chooseoutlet'));
        // // }
    }

    public function employe()
    {
        // Calling Model
        // $db                 = \Config\Database::connect();
        $TransactionModel   = new TransactionModel();
        $UserModel          = new UserModel();
        $UserGroupModel     = new GroupUserModel();
        $GroupModel         = new GroupModel();
        $OutletModel        = new OutletModel();

        // Populating Data 
        $admin          = $UserModel->findAll();
        // $usergroups     = $UserGroupModel->findAll();
        // $groups         = $GroupModel->findAll();

        $input = $this->request->getGet('daterange');

        if (!empty($input)) {
            $daterange = explode(' - ', $input);
            $startdate = $daterange[0];
            $enddate = $daterange[1];
        } else {
            // $startdate  = date('Y-m-1' . ' 00:00:00');
            // $enddate    = date('Y-m-t' . ' 23:59:59');
            $startdate  = date('Y-m-d' . ' 00:00:00');
            $enddate    = date('Y-m-d' . ' 23:59:59');
        }

        // $addres = '';
        // if ($this->data['outletPick'] === null) {
        //     // if ($startdate === $enddate) {
        //         $transactions = $TransactionModel->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->find();
        //     // } else {
        //     //     $transactions = $TransactionModel->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->find();
        //     // }
        //     $addres = "All Outlets";
        //     $outletname = "58vapehouse";
        // } else {
        //     // if ($startdate === $enddate) {
        //         $transactions = $TransactionModel->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->where('outletid', $this->data['outletPick'])->find();
        //     // } else {
        //     //     $transactions = $TransactionModel->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->where('outletid', $this->data['outletPick'])->find();
        //     // }
        //     $outlets = $OutletModel->find($this->data['outletPick']);
        //     $addres = $outlets['address'];
        //     $outletname = $outlets['name'];
        // }

        // $useradm = [];
        // foreach ($transactions as $transaction) {
        //     foreach ($admin as $adm) {
        //         if ($transaction['userid'] === $adm->id) {
        //             foreach ($usergroups as $userg) {
        //                 if ($adm->id === $userg['user_id']) {
        //                     foreach ($groups as $group) {
        //                         if ($userg['group_id'] === $group->id) {
        //                             $useradm[] = [
        //                                 'id'    => $adm->id,
        //                                 'value' => $transaction['value'],
        //                                 'name'  => $adm->username,
        //                                 'role'  => $group->name,
        //                             ];
        //                         }
        //                     }
        //                 }
        //             }
        //         }
        //     }
        // }

        // $val = array_sum(array_column($useradm, 'value'));

        // $produk = [];
        // foreach ($useradm as $username) {
        //     if (!isset($produk[$username['id']])) {
        //         $produk[$username['id']] = $username;
        //     } else {
        //         $produk[$username['id']]['value'] += $username['value'];
        //     }
        // }
        // $produk = array_values($produk);

        $inputsearch    = $this->request->getGet('search');

        if (!empty($inputsearch)) {
            $admins                 = $UserModel->like('username', $inputsearch)->find();
        } else {
            $admins                 = $UserModel->findAll();
        }
        
        $employeedata           = [];
        foreach ($admins as $admin) {
            $employeedata[$admin->id]['name']  = $admin->username;
            $usergroups         = $UserGroupModel->where('user_id', $admin->id)->find();
            foreach ($usergroups as $usergroup) {
                $groups         = $GroupModel->find($usergroup['group_id']);
                $employeedata[$admin->id]['role']  = $groups->name;
            }

            if ($this->data['outletPick'] === null) {
                $transactions   = $TransactionModel->where('userid', $admin->id)->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->find();
            } else {
                $transactions   = $TransactionModel->where('userid', $admin->id)->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->where('outletid', $this->data['outletPick'])->find();
            }

            $trxvalue           = [];
            if (!empty($transactions)) {
                foreach ($transactions as $trx) {
                    $trxvalue[] = $trx['value'];
                }
            }
            $employeedata[$admin->id]['value']  = array_sum($trxvalue);
        }
        array_multisort(array_column($employeedata, 'value'), SORT_DESC, $employeedata);

        // parsing data to view
        $data                   = $this->data;
        $data['title']          = lang('Global.employereport');
        $data['description']    = lang('Global.employeListDesc');
        $data['employetrx']     = $employeedata;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);

        return view('Views/report/employe', $data);
    }

    public function product()
    {
        // $db             = \Config\Database::connect();
        // $this->db       = \Config\Database::connect();
        // $validation     = \Config\Services::validation();
        // $this->builder  = $this->db->table('transaction');
        // $this->config   = config('Auth');
        // $this->auth     = service('authentication');
        // $pager          = \Config\Services::pager();

        // Calling Models
        $OutletModel        = new OutletModel();
        $ProductModel       = new ProductModel();
        $TransactionModel   = new TransactionModel();
        $TrxdetailModel     = new TrxdetailModel();
        $VariantModel       = new VariantModel();
        $CategoryModel      = new CategoryModel();

        // Populating Data
        $input          = $this->request->getGet();
        // $products       = $ProductModel->findAll();
        
        if (!empty($input['daterange'])) {
            $daterange = explode(' - ', $input['daterange']);
            $startdate = $daterange[0];
            $enddate = $daterange[1];
        } else {
            // $startdate  = date('Y-m-1' . ' 00:00:00');
            // $enddate    = date('Y-m-t' . ' 23:59:59');
            $startdate  = date('Y-m-d' . ' 00:00:00');
            $enddate    = date('Y-m-d' . ' 23:59:59');
        }

        // Populating Data
        // if ($this->data['outletPick'] === null) {
        //     return redirect()->back()->with('error', lang('Global.chooseoutlet'));
        // } else {
        //     $outlet     = $OutletModel->find($this->data['outletPick']);
        //     $outletname = $outlet['name'];

        //     $page    = (int) ($this->request->getGet('page') ?? 1);
        //     $perPage = 30;
        //     $offset = ($page - 1) * $perPage;
        //     $total   = count($products);

        //     $trxpro   = $db->table('transaction');
        //     // if ($startdate === $enddate) {
        //         $trxpro->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59');
        //     // } else {
        //     //     $trxpro->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59');
        //     // }
        //     $protrans   = $trxpro->select('product.id as id, trxdetail.id as trxdetid, transaction.id as trxid, transaction.date as date, transaction.disctype as disctype, transaction.discvalue as discval, transaction.pointused as redempoin, transaction.value as total, product.name as product, category.name as category, variant.name as variant, trxdetail.qty as qty, variant.hargamodal as modal, variant.id as varid, variant.hargajual as jual, trxdetail.value as trxdetval, trxdetail.discvar as discvar, trxdetail.marginmodal as marginmodal, outlet.name as outlet, outlet.address as address, bundle.name as bundle');
        //     $protrans   = $trxpro->join('trxdetail', 'transaction.id = trxdetail.transactionid', 'left');
        //     $protrans   = $trxpro->join('users', 'transaction.userid = users.id', 'left');
        //     $protrans   = $trxpro->join('outlet', 'transaction.outletid = outlet.id', 'left');
        //     $protrans   = $trxpro->join('member', 'transaction.memberid = member.id', 'left');
        //     $protrans   = $trxpro->join('trxpayment', 'trxdetail.transactionid = trxpayment.transactionid', 'left');
        //     $protrans   = $trxpro->join('bundle', 'trxdetail.bundleid = bundle.id', 'left');
        //     $protrans   = $trxpro->join('variant', 'trxdetail.variantid = variant.id', 'left');
        //     $protrans   = $trxpro->join('payment', 'trxpayment.paymentid = payment.id', 'left');
        //     $protrans   = $trxpro->join('product', 'variant.productid = product.id', 'left');
        //     $protrans   = $trxpro->join('category', 'product.catid = category.id', 'left');
        //     $protrans   = $trxpro->where('trxdetail.variantid !=', 0);
        //     $protrans   = $trxpro->where('transaction.outletid', $this->data['outletPick']);
        //     if (!empty($input['search'])) {
        //         $protrans   = $trxpro->like('product.name', $input['search']);
        //     }
        //     $protrans   = $trxpro->orderBy('transaction.date', 'DESC');
        //     $protrans   = $trxpro->get($perPage, $offset);
        //     $protrans   = $protrans->getResultArray();
        //     $pager_links = $pager->makeLinks($page, $perPage, $total, 'front_full');
        // }

        // // Net Sales Code (Penjualan Bersih)
        // $produks = [];
        // foreach ($protrans as $catetrans) {
        //     if (!isset($produks[$catetrans['trxdetid']])) {
        //         $produks[$catetrans['trxdetid']] = $catetrans;
        //     }
        // }
        // $produks = array_values($produks);

        // $categories = [];
        // foreach ($produks as $catetrans) {
        //     if (!isset($categories[$catetrans['id'] . $catetrans['product']])) {
        //         $categories[$catetrans['id'] . $catetrans['product']] = $catetrans;
        //     } else {
        //         $categories[$catetrans['id'] . $catetrans['product']]['qty'] += $catetrans['qty'];
        //     }
        // }
        // $categories = array_values($categories);

        // // total net sales (Total Penjualan Bersih)
        // $trxgross = [];
        // foreach ($produks as $catetrx) {
        //     foreach ($categories as $kate) {
        //         if ($kate['id'] === $catetrx['id']) {
        //             $trxgross[] = [
        //                 'id'        => $catetrx['id'],
        //                 'pro'       => $catetrx['product'],
        //                 'cate'      => $catetrx['category'],
        //                 'netval'    => $catetrx['trxdetval'],
        //                 'value'     => $catetrx['trxdetval'],
        //                 'discvar'   => $catetrx['discvar'],
        //                 'qty'       => $catetrx['qty'],
        //             ];
        //         }
        //     }
        // }

        // // data category
        // $catedata = [];
        // foreach ($trxgross as $vars) {
        //     if (!isset($catedata[$vars['id'] . $vars['pro']])) {
        //         $catedata[$vars['id'] . $vars['pro']] = $vars;
        //     } else {
        //         $catedata[$vars['id'] . $vars['pro']]['value'] = $vars['value'];
        //         $catedata[$vars['id'] . $vars['pro']]['netval'] = $vars['netval'];
        //         $catedata[$vars['id'] . $vars['pro']]['discvar'] += $vars['discvar'];
        //         $catedata[$vars['id'] . $vars['pro']]['qty'] += $vars['qty'];
        //     }
        // }
        // $catedata = array_values($catedata);

        // // Product Result
        // $proresults = array();
        // foreach ($catedata as $cate) {
        //     $proresults[] = [
        //         'id' => $cate['id'],
        //         'pro' => $cate['pro'],
        //         'cate' => $cate['cate'],
        //         'netval' => ($cate['netval'] * $cate['qty']) - $cate['discvar'],
        //         'value' => $cate['netval'] * $cate['qty'],
        //         'qty' => $cate['qty'],
        //     ];
        // }

        // // catching data
        // $alldata = [];
        // foreach ($catedata as $cate) {
        //     foreach ($categories as $kate) {
        //         if ($kate['id'] === $cate['id']) {
        //             $alldata[] = [
        //                 'id'        => $cate['id'],
        //                 'pro'       => $cate['pro'],
        //                 'cate'      => $cate['cate'],
        //                 'netval'    => $cate['netval'],
        //                 'value'     => $cate['value'],
        //                 'qty'       => $kate['qty'],
        //             ];
        //         }
        //     }
        // }

        // // end result data
        // $result = [];
        // foreach ($alldata as $datacate) {
        //     $result[] = [
        //         'id'        => $datacate['id'],
        //         'pro'       => $datacate['pro'],
        //         'cate'      => $datacate['cate'],
        //         'netval'    => $datacate['netval'] * $datacate['qty'],
        //         'value'     => ($datacate['value'] * $datacate['qty']),
        //         'qty'       => $datacate['qty'],
        //     ];
        // }
        // // dd($result);

        // // total net sales
        // $totalnetsales = array_sum(array_column($proresults, 'netval'));

        // // total gross sales category
        // $totalcatgross =  array_sum(array_column($proresults, 'value'));

        // // total cat sales item
        // $totalsalesitem = array_sum(array_column($proresults, 'qty'));

        if ($this->data['outletPick'] === null) {
            $transactions       = $TransactionModel->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->find();
        } else {
            $transactions       = $TransactionModel->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->where('outletid', $this->data['outletPick'])->find();
        }

        $transactiondata    = [];
        $productsales       = [];
        $netval             = [];
        $grossval           = [];
        
        foreach ($transactions as $trx) {
            $trxdetails     = $TrxdetailModel->where('transactionid', $trx['id'])->find();
            $totaltrxdet    = count($trxdetails);

            if ($trx['discvalue'] != null) {
                $discval   = round((int)$trx['discvalue'] / (int)$totaltrxdet);
            } else {
                $discval   = 0;
            }

            if ($trx['memberdisc'] != null) {
                $discmem   = round((int)$trx['memberdisc'] / (int)$totaltrxdet);
            } else {
                $discmem   = 0;
            }

            if ($trx['pointused'] != '0') {
                $discpoin   = round((int)$trx['pointused'] / (int)$totaltrxdet);
            } else {
                $discpoin   = 0;
            }
            
            if (!empty($trxdetails)) {
                foreach ($trxdetails as $trxdet) {
                    $variants       = $VariantModel->find($trxdet['variantid']);
                    
                    if (!empty($variants)) {
                        // if (!empty($input['search'])) {
                        //     $products   = $ProductModel->where('name', $input['search'])->find($productid);
                        // } else {
                            $products   = $ProductModel->find($variants['productid']);
                        // }

                        if (!empty($products)) {
                            $transactiondata[$products['id']]['name']            = $products['name'];
                            $category   = $CategoryModel->find($products['catid']);

                            if (!empty($category)) {
                                $transactiondata[$products['id']]['category']    = $category['name'];
                            }
                            
                            // $transactiondata[$productid]['grossvalue']      = ((Int)$trxdet['value'] * (Int)$trxdet['qty']) + $trxdet['discvar'];
                            // $transactiondata[$productid]['netvalue']        = (((Int)$trxdet['value'] * (Int)$trxdet['qty'])) - (Int)$disc;
                            // $transactiondata[$productid]['qty']             = $trxdet['qty'];
                            $transactiondata[$products['id']]['qty'][]           = $trxdet['qty'];
                            $transactiondata[$products['id']]['netvalue'][]      = (((float)$trxdet['value'] * (Int)$trxdet['qty'])) - ((Int)$discval + (Int)$discmem + (Int)$discpoin);
                            $transactiondata[$products['id']]['grossvalue'][]    = ((float)$trxdet['value'] * (Int)$trxdet['qty']) + (Int)$trxdet['discvar'] + (Int)$trxdet['globaldisc'] + (Int)$trxdet['memberdisc'];

                            // $grossval[$products['id']][]     = ((Int)$trxdet['value'] * (Int)$trxdet['qty']) + $trxdet['discvar'];
                            // $netval[$products['id']][]       = (((Int)$trxdet['value'] * (Int)$trxdet['qty']));
                            // $productsales[$products['id']][] = $trxdet['qty'];
                        } else {
                            $category   = [];
                        }
                    } else {
                        $products   = [];
                        $category   = [];
                        $transactiondata[0]['name']             = 'Kategori / Produk / Variant Terhapus';
                        $transactiondata[0]['category']         = 'Kategori / Produk / Variant Terhapus';
                        $transactiondata[0]['qty'][]            = $trxdet['qty'];
                        $transactiondata[0]['netvalue'][]       = (((float)$trxdet['value'] * (Int)$trxdet['qty'])) - ((Int)$discval + (Int)$discmem + (Int)$discpoin);
                        $transactiondata[0]['grossvalue'][]     = ((float)$trxdet['value'] * (Int)$trxdet['qty']) + (Int)$trxdet['discvar'] + (Int)$trxdet['globaldisc'] + (Int)$trxdet['memberdisc'];

                        // $grossval[]     = ((Int)$trxdet['value'] * (Int)$trxdet['qty']) + $trxdet['discvar'];
                        // $netval[]       = (((Int)$trxdet['value'] * (Int)$trxdet['qty']));
                        // $productsales[] = $trxdet['qty'];
                    }
                }
            } else {
                $variants   = [];
                $products   = [];
                $productid  = '';
                $category   = [];
            }
        }
        
        foreach ($transactiondata as $trxdata) {
            $productsales[] = array_sum($trxdata['qty']);
            $netval[]       = array_sum($trxdata['netvalue']);
            $grossval[]     = array_sum($trxdata['grossvalue']);
        }
        
        $totalsalesitem = array_sum($productsales);
        $totalnetsales  = array_sum($netval);
        $totalcatgross  = array_sum($grossval);
        array_multisort(array_column($transactiondata, 'netvalue'), SORT_DESC, $transactiondata);
        
        // dd($transactiondata);
        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.productreport');
        $data['description']    = lang('Global.productListDesc');
        // $data['transactions']   = $protrans;
        $data['products']       = $transactiondata;
        $data['totalstock']     = $totalsalesitem;
        $data['salestotal']     = $totalnetsales;
        $data['grosstotal']     = $totalcatgross;
        $data['netsales']       = $totalnetsales;
        $data['gross']          = $totalcatgross;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);
        // $data['pager']          = $pager_links;

        return view('Views/report/product', $data);
    }

    public function category()
    {
        // Calling models
        $OutletModel        = new OutletModel();
        $ProductModel       = new ProductModel();
        $TransactionModel   = new TransactionModel();
        $TrxdetailModel     = new TrxdetailModel();
        $VariantModel       = new VariantModel();
        $BundleModel        = new BundleModel();
        $BundledetailModel  = new BundledetailModel();
        $CategoryModel      = new CategoryModel();

        // Populating Data
        $input          = $this->request->getGet();

        // Daterange Filter System
        if (!empty($input['daterange'])) {
            $daterange = explode(' - ', $input['daterange']);
            $startdate = $daterange[0];
            $enddate = $daterange[1];
        } else {
            // $startdate  = date('Y-m-1' . ' 00:00:00');
            // $enddate    = date('Y-m-t' . ' 23:59:59');
            $startdate  = date('Y-m-d' . ' 00:00:00');
            $enddate    = date('Y-m-d' . ' 23:59:59');
        }

        if ($this->data['outletPick'] === null) {
            $transactions       = $TransactionModel->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->find();
        } else {
            $transactions       = $TransactionModel->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->where('outletid', $this->data['outletPick'])->find();
        }

        $transactiondata    = [];
        $productsales       = [];
        $netval             = [];
        $grossval           = [];
        
        foreach ($transactions as $trx) {
            $trxdetails     = $TrxdetailModel->where('transactionid', $trx['id'])->find();
            $totaltrxdet    = count($trxdetails);

            if ($trx['discvalue'] != null) {
                $discval   = round((int)$trx['discvalue'] / (int)$totaltrxdet);
            } else {
                $discval   = 0;
            }

            if ($trx['memberdisc'] != null) {
                $discmem   = round((int)$trx['memberdisc'] / (int)$totaltrxdet);
            } else {
                $discmem   = 0;
            }

            if ($trx['pointused'] != '0') {
                $discpoin   = round((int)$trx['pointused'] / (int)$totaltrxdet);
            } else {
                $discpoin   = 0;
            }
            
            if (!empty($trxdetails)) {
                foreach ($trxdetails as $trxdet) {
                    if (($trxdet['variantid'] != '0') && ($trxdet['bundleid'] == '0')) {
                        // Data Variant
                        $variants       = $VariantModel->find($trxdet['variantid']);
                        
                        if (!empty($variants)) {
                            $products   = $ProductModel->find($variants['productid']);
    
                            if (!empty($products)) {
                                // Search Filter
                                if (!empty($input['search'])) {
                                    $category   = $CategoryModel->where('name', $input['search'])->find($products['catid']);
                                } else {
                                    $category   = $CategoryModel->find($products['catid']);
                                }
    
                                if (!empty($category)) {
                                    $transactiondata[$category['id']]['name']               = $category['name'];
                                    $transactiondata[$category['id']]['qty'][]              = $trxdet['qty'];
                                    $transactiondata[$category['id']]['netvalue'][]         = (((float)$trxdet['value'] * (Int)$trxdet['qty'])) - ((Int)$discval + (Int)$discmem + (Int)$discpoin);
                                    $transactiondata[$category['id']]['grossvalue'][]       = ((float)$trxdet['value'] * (Int)$trxdet['qty']) + (Int)$trxdet['discvar'] + (Int)$trxdet['globaldisc'] + (Int)$trxdet['memberdisc'];

                                }
                            } else {
                                $category   = [];
                            }
                        } else {
                            $products   = [];
                            $category   = [];

                            $transactiondata[0]['name']                             = 'Kategori / Produk / Variant Terhapus';
                            $transactiondata[0]['qty'][]                            = $trxdet['qty'];
                            $transactiondata[0]['netvalue'][]                       = (((float)$trxdet['value'] * (Int)$trxdet['qty'])) - ((Int)$discval + (Int)$discmem + (Int)$discpoin);
                            $transactiondata[0]['grossvalue'][]                     = ((float)$trxdet['value'] * (Int)$trxdet['qty']) + (Int)$trxdet['discvar'] + (Int)$trxdet['globaldisc'] + (Int)$trxdet['memberdisc'];
                        }
                    }

                    if (($trxdet['variantid'] == '0') && ($trxdet['bundleid'] != '0')) {
                        // Data Bundle
                        $bundles        = $BundleModel->find($trxdet['bundleid']);

                        if (!empty($bundles)) {
                            // Data Bundle Detail
                            $bundledets     = $BundledetailModel->where('bundleid', $bundles['id'])->find();
    
                            if (!empty($bundledets)) {
                                foreach ($bundledets as $bundet) {
                                    // Data Variant
                                    $bundlevariants = $VariantModel->find($bundet['variantid']);
                                    
                                    if (!empty($bundlevariants)) {
                                        $bundleproduct   = $ProductModel->find($bundlevariants['productid']);
                
                                        if (!empty($bundleproduct)) {
                                            // Search Filter
                                            if (!empty($input['search'])) {
                                                $category   = $CategoryModel->where('name', $input['search'])->find($bundleproduct['catid']);
                                            } else {
                                                $category   = $CategoryModel->find($bundleproduct['catid']);
                                            }
                
                                            if (!empty($category)) {
                                                $transactiondata[$category['id']]['name']               = $category['name'];
                                                $transactiondata[$category['id']]['qty'][]              = $trxdet['qty'];
                                                $transactiondata[$category['id']]['netvalue'][]         = (((float)$trxdet['value'] * (Int)$trxdet['qty'])) - ((Int)$discval + (Int)$discmem + (Int)$discpoin);
                                                $transactiondata[$category['id']]['grossvalue'][]       = ((float)$trxdet['value'] * (Int)$trxdet['qty']) + (Int)$trxdet['discvar'] + (Int)$trxdet['globaldisc'] + (Int)$trxdet['memberdisc'];
            
                                            }
                                        } else {
                                            $category   = [];
                                        }
                                    } else {
                                        $bundleproduct   = [];
                                        $category   = [];
            
                                        $transactiondata[0]['name']                             = 'Kategori / Produk / Variant Terhapus';
                                        $transactiondata[0]['qty'][]                            = $trxdet['qty'];
                                        $transactiondata[0]['netvalue'][]                       = (((float)$trxdet['value'] * (Int)$trxdet['qty'])) - ((Int)$discval + (Int)$discmem + (Int)$discpoin);
                                        $transactiondata[0]['grossvalue'][]                     = ((float)$trxdet['value'] * (Int)$trxdet['qty']) + (Int)$trxdet['discvar'] + (Int)$trxdet['globaldisc'] + (Int)$trxdet['memberdisc'];
                                    }
                                }
                            }
                        } else {
                            $bundlevariants = [];
                            $bundleproduct  = [];
                            $category       = [];
                        }
                    }
                }
            } else {
                $bundles        = [];
                $bundledets     = [];
                $bundlevariants = [];
                $bundleproduct  = [];
                $category       = [];
            }
        }
        
        foreach ($transactiondata as $trxdata) {
            $productsales[] = array_sum($trxdata['qty']);
            $netval[]       = array_sum($trxdata['netvalue']);
            $grossval[]     = array_sum($trxdata['grossvalue']);
        }
        
        $totalsalesitem     = array_sum($productsales);
        $totalnetsales      = array_sum($netval);
        $totalcatgross      = array_sum($grossval);
        array_multisort(array_column($transactiondata, 'netvalue'), SORT_DESC, $transactiondata);

        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.categoryreport');
        $data['description']    = lang('Global.categoryListDesc');
        $data['catedata']       = $transactiondata;
        $data['netsales']       = $totalnetsales;
        $data['gross']          = $totalcatgross;
        $data['qty']            = $totalsalesitem;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);
        // $data['pager']          = $CategoryModel->pager;

        return view('Views/report/category', $data);
    }

    public function brand()
    {
        // Calling models
        $OutletModel        = new OutletModel();
        $ProductModel       = new ProductModel();
        $TransactionModel   = new TransactionModel();
        $TrxdetailModel     = new TrxdetailModel();
        $VariantModel       = new VariantModel();
        $BundleModel        = new BundleModel();
        $BundledetailModel  = new BundledetailModel();
        $BrandModel         = new BrandModel();

        // Populating Data
        $input          = $this->request->getGet();

        // Daterange Filter System
        if (!empty($input['daterange'])) {
            $daterange = explode(' - ', $input['daterange']);
            $startdate = $daterange[0];
            $enddate = $daterange[1];
        } else {
            // $startdate  = date('Y-m-1' . ' 00:00:00');
            // $enddate    = date('Y-m-t' . ' 23:59:59');
            $startdate  = date('Y-m-d' . ' 00:00:00');
            $enddate    = date('Y-m-d' . ' 23:59:59');
        }

        if ($this->data['outletPick'] === null) {
            $transactions       = $TransactionModel->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->find();
        } else {
            $transactions       = $TransactionModel->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->where('outletid', $this->data['outletPick'])->find();
        }

        $transactiondata    = [];
        $productsales       = [];
        $netval             = [];
        $grossval           = [];
        
        foreach ($transactions as $trx) {
            $trxdetails     = $TrxdetailModel->where('transactionid', $trx['id'])->find();
            $totaltrxdet    = count($trxdetails);

            if ($trx['discvalue'] != null) {
                $discval   = round((int)$trx['discvalue'] / (int)$totaltrxdet);
            } else {
                $discval   = 0;
            }

            if ($trx['memberdisc'] != null) {
                $discmem   = round((int)$trx['memberdisc'] / (int)$totaltrxdet);
            } else {
                $discmem   = 0;
            }

            if ($trx['pointused'] != '0') {
                $discpoin   = round((int)$trx['pointused'] / (int)$totaltrxdet);
            } else {
                $discpoin   = 0;
            }
            
            if (!empty($trxdetails)) {
                foreach ($trxdetails as $trxdet) {
                    if (($trxdet['variantid'] != '0') && ($trxdet['bundleid'] == '0')) {
                        // Data Variant
                        $variants       = $VariantModel->find($trxdet['variantid']);
                        
                        if (!empty($variants)) {
                            $products   = $ProductModel->find($variants['productid']);
    
                            if (!empty($products)) {
                                // Search Filter
                                if (!empty($input['search'])) {
                                    $brand   = $BrandModel->where('name', $input['search'])->find($products['brandid']);
                                } else {
                                    $brand   = $BrandModel->find($products['brandid']);
                                }
    
                                if (!empty($brand)) {
                                    $transactiondata[$brand['id']]['name']               = $brand['name'];
                                    $transactiondata[$brand['id']]['qty'][]              = $trxdet['qty'];
                                    $transactiondata[$brand['id']]['netvalue'][]         = (((float)$trxdet['value'] * (Int)$trxdet['qty'])) - ((Int)$discval + (Int)$discmem + (Int)$discpoin);
                                    $transactiondata[$brand['id']]['grossvalue'][]       = ((float)$trxdet['value'] * (Int)$trxdet['qty']) + (Int)$trxdet['discvar'] + (Int)$trxdet['globaldisc'] + (Int)$trxdet['memberdisc'];

                                }
                            } else {
                                $brand   = [];
                            }
                        } else {
                            $products   = [];
                            $brand      = [];

                            $transactiondata[0]['name']                             = 'Merek / Produk / Variant Terhapus';
                            $transactiondata[0]['qty'][]                            = $trxdet['qty'];
                            $transactiondata[0]['netvalue'][]                       = (((float)$trxdet['value'] * (Int)$trxdet['qty'])) - ((Int)$discval + (Int)$discmem + (Int)$discpoin);
                            $transactiondata[0]['grossvalue'][]                     = ((float)$trxdet['value'] * (Int)$trxdet['qty']) + (Int)$trxdet['discvar'] + (Int)$trxdet['globaldisc'] + (Int)$trxdet['memberdisc'];
                        }
                    }

                    if (($trxdet['variantid'] == '0') && ($trxdet['bundleid'] != '0')) {
                        // Data Bundle
                        $bundles        = $BundleModel->find($trxdet['bundleid']);

                        if (!empty($bundles)) {
                            // Data Bundle Detail
                            $bundledets     = $BundledetailModel->where('bundleid', $bundles['id'])->find();
    
                            if (!empty($bundledets)) {
                                foreach ($bundledets as $bundet) {
                                    // Data Variant
                                    $bundlevariants = $VariantModel->find($bundet['variantid']);
                                    
                                    if (!empty($bundlevariants)) {
                                        $bundleproduct   = $ProductModel->find($bundlevariants['productid']);
                
                                        if (!empty($bundleproduct)) {
                                            // Search Filter
                                            if (!empty($input['search'])) {
                                                $brand   = $BrandModel->where('name', $input['search'])->find($bundleproduct['brandid']);
                                            } else {
                                                $brand   = $BrandModel->find($bundleproduct['brandid']);
                                            }
                
                                            if (!empty($brand)) {
                                                $transactiondata[$brand['id']]['name']               = $brand['name'];
                                                $transactiondata[$brand['id']]['qty'][]              = $trxdet['qty'];
                                                $transactiondata[$brand['id']]['netvalue'][]         = (((float)$trxdet['value'] * (Int)$trxdet['qty'])) - ((Int)$discval + (Int)$discmem + (Int)$discpoin);
                                                $transactiondata[$brand['id']]['grossvalue'][]       = ((float)$trxdet['value'] * (Int)$trxdet['qty']) + (Int)$trxdet['discvar'] + (Int)$trxdet['globaldisc'] + (Int)$trxdet['memberdisc'];
            
                                            }
                                        } else {
                                            $brand   = [];
                                        }
                                    } else {
                                        $bundleproduct   = [];
                                        $brand   = [];
            
                                        $transactiondata[0]['name']                             = 'Merek / Produk / Variant Terhapus';
                                        $transactiondata[0]['qty'][]                            = $trxdet['qty'];
                                        $transactiondata[0]['netvalue'][]                       = (((float)$trxdet['value'] * (Int)$trxdet['qty'])) - ((Int)$discval + (Int)$discmem + (Int)$discpoin);
                                        $transactiondata[0]['grossvalue'][]                     = ((float)$trxdet['value'] * (Int)$trxdet['qty']) + (Int)$trxdet['discvar'] + (Int)$trxdet['globaldisc'] + (Int)$trxdet['memberdisc'];
                                    }
                                }
                            }
                        } else {
                            $bundlevariants = [];
                            $bundleproduct  = [];
                            $brand          = [];
                        }
                    }
                }
            } else {
                $bundles        = [];
                $bundledets     = [];
                $bundlevariants = [];
                $bundleproduct  = [];
                $brand          = [];
            }
        }
        
        foreach ($transactiondata as $trxdata) {
            $productsales[] = array_sum($trxdata['qty']);
            $netval[]       = array_sum($trxdata['netvalue']);
            $grossval[]     = array_sum($trxdata['grossvalue']);
        }
        
        $totalsalesitem     = array_sum($productsales);
        $totalnetsales      = array_sum($netval);
        $totalcatgross      = array_sum($grossval);
        array_multisort(array_column($transactiondata, 'netvalue'), SORT_DESC, $transactiondata);

        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.brandreport');
        $data['description']    = lang('Global.brandListDesc');
        $data['branddata']      = $transactiondata;
        $data['netsales']       = $totalnetsales;
        $data['gross']          = $totalcatgross;
        $data['qty']            = $totalsalesitem;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);

        return view('Views/report/brand', $data);
    }

    public function stockcategory()
    {

        // Calling Data
        $ProductModel   = new ProductModel();
        $BrandModel     = new BrandModel();
        $VariantModel   = new VariantModel();
        $CategoryModel  = new CategoryModel();
        $StockModel     = new StockModel();
        $VariantModel   = new VariantModel();
        $OutletModel    = new OutletModel();

        $variants   = $VariantModel->findAll();
        $brands     = $BrandModel->findAll();
        $products   = $ProductModel->findAll();
        $category   = $CategoryModel->findAll();
        $variants   = $VariantModel->findAll();
        $outlets    = $OutletModel->findAll();

        if ($this->data['outletPick'] === null) {
            $stocks      = $StockModel->findAll();
            foreach ($outlets as $outlet) {
                if ($outlet['id'] === $this->data['outletPick']) {
                    $outletname = $outlet['name'];
                }
            }
        } else {
            $stocks      = $StockModel->where('outletid', $this->data['outletPick'])->find();
            foreach ($outlets as $outlet) {
                $outletname = $outlet['name'];
            }
        }

        $productval = [];
        foreach ($stocks as $stock) {
            foreach ($variants as $variant) {
                foreach ($products as $product) {
                    foreach ($brands as $brand) {
                        foreach ($category as $cat) {
                            if ($product['catid'] === $cat['id'] && $product['brandid'] === $brand['id'] && $variant['productid'] == $product['id'] && $stock['variantid'] === $variant['id']) {
                                $productval[] = [
                                    'id'                => $product['catid'],
                                    'prodname'          => $product['name'],
                                    'catname'           => $cat['name'],
                                    'brandname'         => $brand['name'],
                                    'desc'              => $product['description'],
                                    'varname'           => $variant['name'],
                                    'hargamodal'        => $variant['hargamodal'],
                                    'hargajual'         => $variant['hargajual'],
                                    'hargarekomendasi'  => $variant['hargarekomendasi'],
                                    'stock'             => $stock['qty'],
                                    'whole'             => $variant['hargamodal'] * $stock['qty'],
                                ];
                            }
                        }
                    }
                }
            }
        }

        $produk = [];
        foreach ($productval as $vars) {
            if (!isset($produk[$vars['id'] . $vars['prodname']])) {
                $produk[$vars['id'] . $vars['prodname']] = $vars;
            } else {
                $produk[$vars['id'] . $vars['prodname']]['stock'] += $vars['stock'];
                $produk[$vars['id'] . $vars['prodname']]['whole'] += $vars['whole'];
            }
        }
        $produk = array_values($produk);

        $stock = array_sum(array_column($produk, 'stock'));
        $whole = array_sum(array_column($produk, 'whole'));

        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.category');
        $data['description']    = lang('Global.categoryListDesc');
        $data['products']       = $produk;
        $data['stock']          = $stock;
        $data['whole']          = $whole;

        return view('Views/report/stockcategory', $data);
    }

    public function bundle()
    {
        // Calling models
        $TransactionModel   = new TransactionModel();
        $TrxdetailModel     = new TrxdetailModel();
        $VariantModel       = new VariantModel();
        $BundleModel        = new BundleModel();
        $BundledetailModel  = new BundledetailModel();

        // initialize
        $input = $this->request->getGet();

        if (!empty($input['daterange'])) {
            $daterange = explode(' - ', $input['daterange']);
            $startdate = $daterange[0];
            $enddate = $daterange[1];
        } else {
            // $startdate  = date('Y-m-1' . ' 00:00:00');
            // $enddate    = date('Y-m-t' . ' 23:59:59');
            $startdate  = date('Y-m-d' . ' 00:00:00');
            $enddate    = date('Y-m-d' . ' 23:59:59');
        }

        // ================== Rizal Code ====================== //
        // Populating Data
        // $db                 = \Config\Database::connect();
        // $products   = $ProductModel->findAll();
        // $category   = $CategoryModel->findAll();
        // $variants   = $VariantModel->findAll();
        // $stocks     = $StockModel->findAll();
        // $bundles    = $BundleModel->findAll();
        // $bundets    = $BundledetailModel->findAll();
        // $trxdetails = $TrxdetailModel->findAll();

        // // if ($startdate === $enddate) {
        //     $transactions = $TransactionModel->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->find();
        // // } else {
        // //     $transactions = $TransactionModel->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->find();
        // // }

        // $bund = [];
        // foreach ($transactions as $transaction) {
        //     foreach ($trxdetails as $trxdetail) {
        //         foreach ($bundles as $bundle) {
        //             if ($trxdetail['transactionid'] === $transaction['id'] && $trxdetail['bundleid'] !== "0" && $bundle['id'] === $trxdetail['bundleid']) {
        //                 $bund[] = [
        //                     'id'    => $trxdetail['bundleid'],
        //                     'name'  => $bundle['name'],
        //                     'qty'   => $trxdetail['qty'],
        //                     'price' => $bundle['price'],
        //                     'value' => (int)$trxdetail['qty'] * (int)$bundle['price'],
        //                 ];
        //             }
        //         }
        //     }
        // }

        // // Sum Total Bundle Sold
        // $paket = [];
        // foreach ($bund as $bundval) {

        //     if (!isset($paket[$bundval['id'] . $bundval['name']])) {
        //         $paket[$bundval['id'] . $bundval['name']] = $bundval;
        //     } else {
        //         $paket[$bundval['id'] . $bundval['name']]['value'] += $bundval['value'];
        //         $paket[$bundval['id'] . $bundval['name']]['qty'] += $bundval['qty'];
        //     }
        // }

        // $paket = array_values($paket);

        // Populating Data
        if ($this->data['outletPick'] === null) {
            return redirect()->back()->with('error', lang('Global.chooseoutlet'));
        } else {
            $transactions       = $TransactionModel->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->where('outletid', $this->data['outletPick'])->find();

            $transactiondata    = [];
            
            foreach ($transactions as $trx) {
                $trxdetails     = $TrxdetailModel->where('transactionid', $trx['id'])->where('variantid', '0')->find();
                // $totaltrxdet    = count($trxdetails);
    
                // if ($trx['discvalue'] != null) {
                //     $disc   = floor((int)$trx['discvalue'] / (int)$totaltrxdet);
                // } else {
                //     $disc   = 0;
                // }
    
                // if ($trx['memberdisc'] != null) {
                //     $disc   = floor((int)$trx['memberdisc'] / (int)$totaltrxdet);
                // } else {
                //     $disc   = 0;
                // }
    
                // if ($trx['pointused'] != '0') {
                //     $disc   = floor((int)$trx['pointused'] / (int)$totaltrxdet);
                // } else {
                //     $disc   = 0;
                // }
                
                if (!empty($trxdetails)) {
                    foreach ($trxdetails as $trxdet) {
                        // Data Bundle
                        $bundles        = $BundleModel->find($trxdet['bundleid']);
                        if (!empty($bundles)) {
                            $transactiondata[$bundles['id']]['name']                = $bundles['name'];
                            $transactiondata[$bundles['id']]['qty'][]               = $trxdet['qty'];
                            $transactiondata[$bundles['id']]['value'][]             = (((float)$trxdet['value'] * (Int)$trxdet['qty']));

                            // Data Bundle Detail
                            $bundledets     = $BundledetailModel->find($bundles['id']);
    
                            // Data Variant
                            if (!empty($bundledets)) {
                                $bundlevariants = $VariantModel->find($bundledets['variantid']);
                            } else {
                                $bundlevariants = [];
                            }
                        } else {
                            $bundledets     = [];
                            $bundlevariants = [];

                            $transactiondata[0]['name']                             = 'Bundle Terhapus';
                            $transactiondata[0]['qty'][]                            = $trxdet['qty'];
                            $transactiondata[0]['value'][]                          = (((float)$trxdet['value'] * (Int)$trxdet['qty']));
                        }
                    }
                } else {
                    $bundles            = [];
                    $bundledets         = [];
                    $bundlevariants     = [];
                }
            }
        }
        array_multisort(array_column($transactiondata, 'qty'), SORT_DESC, $transactiondata);

        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.bundlereport');
        $data['description']    = lang('Global.bundleListDesc');
        $data['bundles']        = $transactiondata;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);

        return view('Views/report/bundle', $data);
    }

    public function diskon()
    {
        // Calling Models
        $TransactionModel       = new TransactionModel;
        $TrxdetailModel         = new TrxdetailModel;
        $GconfigModel           = new GconfigModel;
        $VariantModel           = new VariantModel;
        $ProductModel           = new ProductModel;
        $BundleModel            = new BundleModel;

        // Populating Data
        $Gconfig                = $GconfigModel->first();

        $input = $this->request->getGet('daterange');

        if (!empty($input)) {
            $daterange = explode(' - ', $input);
            $startdate = $daterange[0];
            $enddate = $daterange[1];
        } else {
            // $startdate  = date('Y-m-1' . ' 00:00:00');
            // $enddate    = date('Y-m-t' . ' 23:59:59');
            $startdate  = date('Y-m-d' . ' 00:00:00');
            $enddate    = date('Y-m-d' . ' 23:59:59');
        }

        $transactions = array();
        if ($this->data['outletPick'] === null) {
            // if ($startdate === $enddate) {
                $transaction = $TransactionModel->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->find();
            // } else {
            //     $transaction = $TransactionModel->where('date >=', $startdate . '00:00:00')->where('date <=', $enddate . '23:59:59')->find();
            // }
        } else {
            // if ($startdate === $enddate) {
                $transaction = $TransactionModel->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->where('outletid', $this->data['outletPick'])->find();
            // }
            // $transaction = $TransactionModel->where('date >=', $startdate . '00:00:00')->where('date <=', $enddate . '23:59:59')->where('outletid', $this->data['outletPick'])->find();
        }

        $discount           = array();
        $pointused          = array();
        $discounttrx        = array();
        $discounttrxpersen  = array();
        $discountmember     = array();
        $discountvariant    = array();
        $discountglobal     = array();
        $discountpoin       = array();

        foreach ($transaction as $trx) {
            // // if ($trx['discvalue'] != "0") {
            // //     $discounttrx[]          = $trx['discvalue'];
            // //     $discounttrxpersen[]    =  (int)$trx['discvalue'];
            // // }
            // $discounttrx[]              = $trx['discvalue'];

            // if ($trx['memberdisc'] != '0') {
            //     $discountmember[]       = $trx['memberdisc'];
            // }
            
            // if ($trx['pointused'] != '0') {
            //     $discountpoin[]         = $trx['pointused'];
            // }

            // Transaction Point Used Array
            $pointused[]        = $trx['pointused'];

            // Discount Transaction
            if (!empty($trx['discvalue'])) {
                $discount[]  = $trx['discvalue'];
            }

            if ($trx['memberdisc'] != null) {
                $discount[]   = $trx['memberdisc'];
            }
            
            $trxdetails         = $TrxdetailModel->where('transactionid', $trx['id'])->find();
            foreach ($trxdetails as $trxdetail) {
                // Discount Variant
                if ($trxdetail['discvar'] != '0') {
                    $discountvariant[]     = $trxdetail['discvar'];
                }

                // Discount Global
                if ($trxdetail['globaldisc'] != '0') {
                    $discountglobal[]     = $trxdetail['globaldisc'];
                }

                // Discount Member
                if ($trxdetail['memberdisc'] != '0') {
                    $discountmember[]     = $trxdetail['memberdisc'];
                }

                // // Data Variant
                // $variantsdata       = $VariantModel->find($trxdetail['variantid']);

                // if (!empty($variantsdata)) {
                //     $productsdata   = $ProductModel->find($variantsdata['productid']);

                //     if (!empty($productsdata)) {
                //         // Transaction Detail Discount Variant
                //         if ($trxdetail['discvar'] != '0') {
                //             $discountvariant[]     = $trxdetail['discvar'];
                //         }
                //     } else {
                //         // Transaction Detail Discount Variant
                //         if ($trxdetail['discvar'] != '0') {
                //             $discountvariant[]     = 0;
                //         }
                //     }
                // } else {
                //     $productsdata   = '';
                // }

                // // Data Bundle
                // $bundlesdata    = $BundleModel->find($trxdetail['bundleid']);

                // if (!empty($bundlesdata)) {
                //     // Transaction Detail Discount Variant
                //     if ($trxdetail['discvar'] != '0') {
                //         $discountvariant[]     = $trxdetail['discvar'];
                //     }
                // } else {
                //     // Transaction Detail Discount Variant
                //     if ($trxdetail['discvar'] != '0') {
                //         $discountvariant[]     = 0;
                //     }
                // }
            }

            // // $transactiondisc = (int)(array_sum($discounttrx)) + (int)(array_sum($discounttrxpersen)) + (int)(array_sum($discountmember));
            // $transactiondisc = (int)(array_sum($discounttrx)) + (int)(array_sum($discountmember));
            // $variantdisc     = array_sum($discountvariant);
            // $poindisc        = array_sum($discountpoin);

            // $transactions[] = [
            //     'id'            => $trx['id'],
            //     'trxdisc'       => $transactiondisc,
            //     'variantdis'    => $variantdisc,
            //     'poindisc'      => $poindisc,
            // ];
        }

        $transactiondisc    = array_sum($discount);
        $variantdisc        = array_sum($discountvariant);
        $globaldisc         = array_sum($discountglobal);
        $memberdisc         = array_sum($discountmember);
        $poindisc           = array_sum($pointused);

        // $trxvar = array_sum(array_column($transactions, 'variantdis'));
        // $trxdis = array_sum(array_column($transactions, 'trxdisc'));
        // $dispoint = array_sum(array_column($transactions, 'poindisc'));

        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.discountreport');
        $data['description']    = lang('Global.profitListDesc');
        $data['transactions']   = $transactions;
        $data['trxvardis']      = $variantdisc;
        $data['trxglodis']      = $globaldisc;
        $data['trxmemdis']      = $memberdisc;
        $data['trxdisc']        = $transactiondisc;
        $data['poindisc']       = $poindisc;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);


        return view('Views/report/diskon', $data);
    }

    public function presence()
    {
        // calling model
        $PresenceModel  = new PresenceModel;
        $UserModel      = new UserModel;
        $UserGroupModel = new GroupUserModel;
        $GroupModel     = new GroupModel;
        $OutletModel    = new OutletModel;

        // populating data
        // $presences      = $PresenceModel->findAll();
        // $users          = $UserModel->findAll();
        // $usergroups     = $UserGroupModel->findAll();
        // $groups         = $GroupModel->findAll();

        $input = $this->request->getGet('daterange');

        if (!empty($input)) {
            $daterange = explode(' - ', $input);
            $startdate = $daterange[0];
            $enddate = $daterange[1];
        } else {
            // $startdate  = date('Y-m-1' . ' 00:00:00');
            // $enddate    = date('Y-m-t' . ' 23:59:59');
            $startdate  = date('Y-m-d' . ' 00:00:00');
            $enddate    = date('Y-m-d' . ' 23:59:59');
        }

        $addres = '';
        // if ($this->data['outletPick'] === null) {
        //     // if ($startdate === $enddate) {
        //         $presences = $PresenceModel->where('datetime >=', $startdate . ' 00:00:00')->where('datetime <=', $enddate . ' 23:59:59')->paginate(20, 'presence');
        //     // } else {
        //     //     $presences  = $PresenceModel->where('datetime >=', $startdate . ' 00:00:00')->where('datetime <=', $enddate)->find();
        //     // }
        //     $addres = "All Outlets";
        //     $outletname = "58vapehouse";
        // } else {
        //     // if ($startdate === $enddate) {
        //         $presences = $PresenceModel->where('datetime >=', $startdate . ' 00:00:00')->where('datetime <=', $enddate . ' 23:59:59')->paginate(20, 'presence');
        //     // } else {
        //     //     $presences  = $PresenceModel->where('datetime >=', $startdate . ' 00:00:00')->where('datetime <=', $enddate . ' 23:59:59')->find();
        //     // }
        //     $outlets = $OutletModel->find($this->data['outletPick']);
        //     $addres = $outlets['address'];
        //     $outletname = $outlets['name'];
        // }

        $presencedata   = [];
        $presences      = $PresenceModel->where('datetime >=', $startdate . ' 00:00:00')->where('datetime <=', $enddate . ' 23:59:59')->find();
        
        if ($this->data['outletPick'] === null) {
            $addres     = "All Outlets";
            $outletname = "58vapehouse";
        } else {
            $outlets    = $OutletModel->find($this->data['outletPick']);
            $addres     = $outlets['address'];
            $outletname = $outlets['name'];
        }
        
        foreach ($presences as $presence) {
            // Get User Data
            $users          = $UserModel->find($presence['userid']);
            $usergroups     = $UserGroupModel->where('user_id', $users->id)->first();
            $groups         = $GroupModel->find($usergroups['group_id']);

            // Define Time
            $s      = strtotime($presence['datetime']);
            $date   = date('d-m-Y', $s);
            $time   = date('H:i', $s);

            $shift  = $presence['shift'];
            $status = $presence['status'];

            $presencedata[$date.$users->id.$shift]['id']       = $presence['id'];
            $presencedata[$date.$users->id.$shift]['date']     = $date;
            $presencedata[$date.$users->id.$shift]['name']     = $users->name;
            $presencedata[$date.$users->id.$shift]['role']     = $groups->name;
            $presencedata[$date.$users->id.$shift]['shift']    = $presence['shift'];

            $presencedata[$date.$users->id.$shift]['detail'][$status]['time']         = $time;
            $presencedata[$date.$users->id.$shift]['detail'][$status]['photo']        = $presence['photo'];
            $presencedata[$date.$users->id.$shift]['detail'][$status]['geoloc']       = $presence['geoloc'];
            $presencedata[$date.$users->id.$shift]['detail'][$status]['status']       = $presence['status'];

            // $presencedata[$date.$shift]['id']       = $presence['id'];
            // $presencedata[$date.$shift]['date']     = $date;
            // $presencedata[$date.$shift]['name']     = $users->name;
            // $presencedata[$date.$shift]['role']     = $groups->name;
            // $presencedata[$date.$shift]['shift']    = $presence['shift'];

            // $presencedata[$date.$shift]['detail'][$status]['time']         = $time;
            // $presencedata[$date.$shift]['detail'][$status]['photo']        = $presence['photo'];
            // $presencedata[$date.$shift]['detail'][$status]['geoloc']       = $presence['geoloc'];
            // $presencedata[$date.$shift]['detail'][$status]['status']       = $presence['status'];
        }
        // dd($presencedata);

        // parsing data to view
        $data                   = $this->data;
        $data['title']          = lang('Global.presencereport');
        $data['description']    = lang('Global.presenceListDesc');
        $data['presences']      = $presencedata;
        // $data['present']        = $presen;
        // $data['pager']          = $PresenceModel->pager;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);

        return view('Views/report/presence', $data);
    }

    public function presencedetail($id)
    {
        // Calling Model
        $PresenceModel      = new PresenceModel;
        $pager              = \Config\Services::pager();

        $datas = explode('-', $id);

        $iduser = $datas[0];
        $starts = $datas[1];
        $ends   = $datas[2];

        if (!empty($iduser)) {
            // if ($starts === $ends) {
                $presences = $PresenceModel->where('datetime >=', $starts . ' 00:00:00')->where('datetime <=', $ends . ' 23:59:59')->where('userid', $iduser)->orderby('id', 'DESC')->paginate(20, 'reportpresencedet');
            // }
            // $presences  = $PresenceModel->where('datetime >=', $starts . ' 00:00:00')->where('datetime <=', $ends . ' 23:59:59')->where('userid', $iduser)->orderBy('id', 'DESC')->paginate(20, 'reportpresecendet');
        } else {
            // if ($starts === $ends) {
                $presences = $PresenceModel->where('datetime >=', $starts . ' 00:00:00')->where('datetime <=', $ends . ' 23:59:59')->orderby('id', 'DESC')->paginate(20, 'reportpresencedet');
            // } else {
            //     $presences  = $PresenceModel->where('datetime >=', $starts . ' 00:00:00')->where('datetime <=', $ends . ' 23:59:59')->orderBy('id', 'DESC')->paginate(20, 'reportpresencedet');
            // }
        }

        // parsing data to view
        $data                   = $this->data;
        $data['title']          = lang('Global.presence');
        $data['description']    = lang('Global.presencedetailListDesc');
        $data['presences']      = $presences;
        $data['pager']          = $PresenceModel->pager;

        return view('Views/report/presencedetail', $data);
    }

    public function customer()
    {
        // Calling Models
        // $db                 = \Config\Database::connect();
        $TransactionModel   = new TransactionModel();
        $MemberModel        = new MemberModel();
        $DebtModel          = new DebtModel();
        // $OutletModel        = new OutletModel();
        $TrxdetailModel     = new TrxdetailModel();
        $ProductModel       = new ProductModel();
        $VariantModel       = new VariantModel();

        // Populating Data
        // Search Filter
        $inputsearch    = $this->request->getGet('search');
        if (!empty($inputsearch)) {
            $members   = $MemberModel->like('name', $inputsearch)->orderBy('name', 'ASC')->paginate(20, 'member');
        } else {
            $members   = $MemberModel->orderBy('name', 'ASC')->paginate(20, 'member');
        }

        // Daterange Filter
        $input = $this->request->getGet('daterange');
        if (!empty($input)) {
            $daterange = explode(' - ', $input);
            $startdate = $daterange[0];
            $enddate = $daterange[1];
        } else {
            $startdate  = date('2023-01-01' . ' 00:00:00');
            $enddate    = date('Y-m-t' . ' 23:59:59');
        }

        $addres = '';
        // if ($this->data['outletPick'] === null) {
        //     $transactions = $TransactionModel->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->find();
        //     $addres = "All Outlets";
        //     $outletname = "58vapehouse";
        // } else {
        //     $transactions = $TransactionModel->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->where('outletid', $this->data['outletPick'])->find();
        //     $outlets = $OutletModel->find($this->data['outletPick']);
        //     $addres = $outlets['address'];
        //     $outletname = $outlets['name'];
        // }

        $customerdata   = [];
        foreach ($members as $member) {
            $debts      = $DebtModel->where('memberid', $member['id'])->find();
            $debtvalue  = [];
            if (!empty($debts)) {
                foreach ($debts as $debt) {
                    $debtvalue[]    = $debt['value'];
                }
            }
            
            if ($this->data['outletPick'] === null) {
                $transactions   = $TransactionModel->where('memberid', $member['id'])->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->find();
            } else {
                $transactions   = $TransactionModel->where('memberid', $member['id'])->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->where('outletid', $this->data['outletPick'])->find();
            }
            
            $trxvalue   = [];
            if (!empty($transactions)) {
                foreach ($transactions as $trx) {
                    $trxdetails     = $TrxdetailModel->where('transactionid', $trx['id'])->find();
                    $trxvalue[]     = $trx['value'];
                
                    if (!empty($trxdetails)) {
                        foreach ($trxdetails as $trxdet) {
                            $variants       = $VariantModel->find($trxdet['variantid']);
                            
                            if (!empty($variants)) {
                                $products   = $ProductModel->find($variants['productid']);
        
                                if (!empty($products)) {
                                    $customerdata[$member['id']]['product'][$products['id']]['name']            = $products['name'];
                                    $customerdata[$member['id']]['product'][$products['id']]['qty'][]           = $trxdet['qty'];
                                }
                            } else {
                                $products   = [];
                                $customerdata[$member['id']]['product'][0]['name']             = 'Kategori / Produk / Variant Terhapus';
                                $customerdata[$member['id']]['product'][0]['qty'][]            = $trxdet['qty'];
                            }
                        }
                    } else {
                        $variants   = [];
                        $products   = [];
                    }
                }
            } else {
                $customerdata[$member['id']]['product'] = [];
            }
            
            $customerdata[$member['id']]['id']          = $member['id'];
            $customerdata[$member['id']]['name']        = $member['name'];
            $customerdata[$member['id']]['phone']       = $member['phone'];
            $customerdata[$member['id']]['debt']        = array_sum($debtvalue);
            $customerdata[$member['id']]['trx']         = count($transactions);
            $customerdata[$member['id']]['trxvalue']    = array_sum($trxvalue);
        }

        // $this->db           = \Config\Database::connect();
        // $pager              = \Config\Services::pager();
        // $customer = array();
        // foreach ($members as $member) {
        //     $totaltrx = array();
        //     $trxval = array();
        //     $debtval    = array();
        //     foreach ($debts as $debt) {
        //         if ($member['id'] === $debt['memberid']) {
        //             $debtval[]  = $debt['value'];
        //         }
        //     }
        //     foreach ($transactions as $trx) {
        //         if ($member['id'] === $trx['memberid']) {
        //             $totaltrx[] = $trx['memberid'];
        //             $trxval[]   = $trx['value'];
        //         }
        //     }

        //     $customer[] = [
        //         'id'    => $member['id'],
        //         'name'  => $member['name'],
        //         'debt'  => array_sum($debtval),
        //         'trx'   => count($totaltrx),
        //         'value' => array_sum($trxval),
        //         'phone' => $member['phone'],
        //     ];
        // }

        // Parsing Data to View
        $data                       = $this->data;
        $data['title']              = lang('Global.customer');
        $data['description']        = lang('Global.customerListDesc');
        $data['customers']          = $customerdata;
        $data['pager']              = $MemberModel->pager;
        $data['startdate']          = strtotime($startdate);
        $data['enddate']            = strtotime($enddate);

        return view('Views/report/customer', $data);
    }

    public function customerdetail($id)
    {

        // Calling Models
        $BundleModel            = new BundleModel;
        $BundledetModel         = new BundledetailModel;
        $CashModel              = new CashModel;
        $OutletModel            = new OutletModel;
        $UserModel              = new UserModel;
        $MemberModel            = new MemberModel;
        $PaymentModel           = new PaymentModel;
        $ProductModel           = new ProductModel;
        $VariantModel           = new VariantModel;
        $StockModel             = new StockModel;
        $TransactionModel       = new TransactionModel;
        $TrxdetailModel         = new TrxdetailModel;
        $TrxpaymentModel        = new TrxpaymentModel;
        $DebtModel              = new DebtModel;

        // Populating Data
        $bundles                = $BundleModel->findAll();
        $bundets                = $BundledetModel->findAll();
        $cash                   = $CashModel->findAll();
        $outlets                = $OutletModel->findAll();
        $users                  = $UserModel->findAll();
        $customers              = $MemberModel->findAll();
        $payments               = $PaymentModel->findAll();
        $products               = $ProductModel->findAll();
        $variants               = $VariantModel->findAll();
        $stocks                 = $StockModel->findAll();
        $transactions           = $TransactionModel->orderBy('date', 'DESC')->where('memberid', $id)->find();
        $trxdetails             = $TrxdetailModel->findAll();
        $trxpayments            = $TrxpaymentModel->findAll();
        $debts                  = $DebtModel->where('memberid', $id)->find();

        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.trxHistory');
        $data['description']    = lang('Global.trxHistoryListDesc');
        $data['bundles']        = $bundles;
        $data['bundets']        = $bundets;
        $data['cash']           = $cash;
        $data['users']          = $users;
        $data['transactions']   = $transactions;
        $data['outlets']        = $outlets;
        $data['payments']       = $payments;
        $data['customers']      = $customers;
        $data['products']       = $products;
        $data['variants']       = $variants;
        $data['stocks']         = $stocks;
        $data['trxdetails']     = $trxdetails;
        $data['trxpayments']    = $trxpayments;
        $data['debts']          = $debts;

        return view('Views/report/customerdetail', $data);
    }

    public function sop()
    {
        // Calling Data
        $SopModel           = new SopModel();
        $SopDetailModel     = new SopDetailModel();
        $OutletModel        = new OutletModel();
        $UserModel          = new UserModel();
        
        // Populating Data
        // Daterange Filter
        $input = $this->request->getGet('daterange');
        if (!empty($input)) {
            $daterange  = explode(' - ', $input);
            $startdate  = $daterange[0];
            $enddate    = $daterange[1];
        } else {
            // $startdate  = date('Y-m-1' . ' 00:00:00');
            // $enddate    = date('Y-m-t' . ' 23:59:59');
            $startdate  = date('Y-m-d' . ' 00:00:00');
            $enddate    = date('Y-m-d' . ' 23:59:59');
        }

        if ($this->data['outletPick'] === null) {
            $sopdetails = $SopDetailModel->orderby('updated_at', 'ASC')->where('updated_at >=', $startdate . ' 00:00:00')->where('updated_at <=', $enddate . ' 23:59:59')->find();
            $addres     = "All Outlets";
            $outletname = "58vapehouse";
        } else {
            $sopdetails = $SopDetailModel->orderby('updated_at', 'ASC')->where('outletid', $this->data['outletPick'])->where('updated_at >=', $startdate . ' 00:00:00')->where('updated_at <=', $enddate . ' 23:59:59')->find();
            $outlets    = $OutletModel->find($this->data['outletPick']);
            $addres     = $outlets['address'];
            $outletname = $outlets['name'];
        }

        $sopdata        = [];
        $count          = 0;
        foreach ($sopdetails as $sopdet) {
            // Get Data SOP
            $sops       = $SopModel->find($sopdet['sopid']);
            $users      = $UserModel->find($sopdet['userid']);
            $outlet     = $OutletModel->find($sopdet['outletid']);

            if (!empty($outlet)) {
                $outletid   = $outlet['id'];
                $outletname = $outlet['name'];
            } else {
                $outletid   = 0;
                $outletname = 'Semua Outlet';
            }
            
            if (!empty($users)) {
                $username   = $users->firstname.' '.$users->lastname;
            } else {
                $username   = 'Belum Tersedia';
            }

            // Define Time
            $s      = strtotime($sopdet['created_at']);
            $date   = date('d-m-Y', $s);
            $time   = date('H:i', $s);

            $sopdata[$date.$outletid]['id']                               = $count++;
            $sopdata[$date.$outletid]['date']                             = $date;
            $sopdata[$date.$outletid]['outlet']                           = $outletname;
            $sopdata[$date.$outletid]['detail'][$sops['id']]['sop']       = $sops['name'];
            $sopdata[$date.$outletid]['detail'][$sops['id']]['employee']  = $username;
            $sopdata[$date.$outletid]['detail'][$sops['id']]['status']    = $sopdet['status'];
        }

        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = "Laporan SOP";
        $data['description']    = "Laporan SOP yang telah dilakukan";
        // $data['sops']           = $sops;
        $data['sopdetails']     = $sopdata;
        // $data['users']          = $users;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);
        // $data['pager']          = $SopDetailModel->pager;

        return view('Views/report/sop', $data);
    }
}
