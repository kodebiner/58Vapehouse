<?php

namespace App\Controllers;

use App\Models\BundledetailModel;
use App\Models\BundleModel;
use App\Models\CashModel;
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
use App\Models\TrxotherModel;
use App\Models\TrxpaymentModel;
use App\Models\DebtModel;
use App\Models\DailyReportModel;

class Trxother extends BaseController
{
    protected $data;
    protected $db, $builder;
    protected $auth;
    protected $config;

    public function __construct()
    {
        $this->db      = \Config\Database::connect();
        $validation    = \Config\Services::validation();
        $this->builder = $this->db->table('trxother');
        $this->config  = config('Auth');
        $this->auth    = service('authentication');
    }

    public function index()
    {
        $pager      = \Config\Services::pager();

        // Calling Model
        $TransactionModel   = new TransactionModel;
        $TrxpaymentModel    = new TrxpaymentModel;
        $TrxotherModel      = new TrxotherModel;
        $PaymentModel       = new PaymentModel;
        $UserModel          = new UserModel;
        $CashModel          = new CashModel;
        $OutletModel        = new OutletModel;
        $DailyReportModel   = new DailyReportModel;

        // Find Data
        $auth               = service('authentication');
        $users              = $UserModel->findAll();
        $outlet             = $OutletModel->findAll();
        $cash               = $CashModel->findAll();
        $payments           = $PaymentModel->notLike('name', 'Cash')->find();

        $input  = $this->request->getGet('daterange');

        if (!empty($input)) {
            $daterange = explode(' - ', $input);
            $startdate = $daterange[0];
            $enddate = $daterange[1];
        } else {
            $startdate  = date('Y-m-1' . ' 00:00:00');
            $enddate    = date('Y-m-t' . ' 23:59:59');
        }

        if ($this->data['outletPick'] === null) {
            // $trxothers  = $TrxotherModel->orderBy('date', 'DESC')->notLike('description', 'Top Up')->notLike('description', 'Debt')->paginate(20, 'cashinout');
            // if (!empty($input)) {
            //     if ($startdate === $enddate) {
                    $trxothers  = $TrxotherModel->orderBy('date', 'DESC')->notLike('description', 'Top Up')->notLike('description', 'Debt')->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->paginate(20, 'cashinout');
            //     } else {
            //         $trxothers  = $TrxotherModel->orderBy('date', 'DESC')->notLike('description', 'Top Up')->notLike('description', 'Debt')->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->paginate(20, 'cashinout');
            //     }
            // }
        } else {
            // $trxothers  = $TrxotherModel->orderBy('date', 'DESC')->notLike('description', 'Top Up')->notLike('description', 'Debt')->where('outletid', $this->data['outletPick'])->paginate(20, 'cashinout');
            // if (!empty($input)) {
            //     if ($startdate === $enddate) {
                    $trxothers  = $TrxotherModel->orderBy('date', 'DESC')->notLike('description', 'Top Up')->notLike('description', 'Debt')->where('outletid', $this->data['outletPick'])->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->paginate(20, 'cashinout');
            //     } else {
            //         $trxothers  = $TrxotherModel->orderBy('date', 'DESC')->notLike('description', 'Top Up')->notLike('description', 'Debt')->where('outletid', $this->data['outletPick'])->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->paginate(20, 'cashinout');
            //     }
            // }
        }

        // Find Data for Daily Report
        $today                  = date('Y-m-d') . ' 00:00:01';
        $dailyreport            = $DailyReportModel->where('dateopen >', $today)->where('outletid', $this->data['outletPick'])->first();

        // Cash Flow
        $dailyreportdata    = [];
        // foreach ($dailyreport as $dayrep) {
        if (!empty($dailyreport)) {
            // Id
            $dailyreportdata['id']              = $dailyreport['id'];

            // Outlet
            $outlets                            = $OutletModel->find($this->data['outletPick']);
            $dailyreportdata['outlet']          = $outlets['name'];

            // Date
            $dailyreportdata['date']            = date('l, d M Y', strtotime($dailyreport['dateopen']));

            // Date Open
            $dailyreportdata['dateopen']        = date('l, d M Y, H:i:s', strtotime($dailyreport['dateopen']));

            // Date Close
            $dailyreportdata['dateclose']       = $dailyreport['dateclose'];

            // Transaction Data
            $transactions       = $TransactionModel->where('date >', $dailyreport['dateopen'])->where('outletid', $this->data['outletPick'])->find();

            // Date Closed
            // if ($dailyreport['dateclose'] != '0000-00-00 00:00:00') {
                // $dailyreportdata['dateclose']    = date('l, d M Y, H:i:s', strtotime($dailyreport['dateclose']));

                // // User Close Store
                // $userclose                                      = $UserModel->find($dailyreport['useridclose']);
                // $dailyreportdata['userclose']    = $userclose->firstname.' '.$userclose->lastname;

                // Transaction Data
                foreach ($transactions as $trx) {
                    // Cash, Non-Cash, Debt
                    $trxpayments    = $TrxpaymentModel->where('transactionid', $trx['id'])->where('paymentid !=', '0')->find();
                    $debtpayments   = $TrxpaymentModel->where('transactionid', $trx['id'])->where('paymentid', '0')->find();
                    $pointpayments  = $TrxpaymentModel->where('transactionid', $trx['id'])->where('paymentid', '-1')->find();

                    if (!empty($trxpayments)) {
                        foreach ($trxpayments as $trxpayment) {
                            $payment        = $PaymentModel->find($trxpayment['paymentid']);

                            if (!empty($payment)) {
                                $cashdata       = $CashModel->find($payment['cashid']);

                                if (strcmp($cashdata['name'], 'Petty Cash ' . $outlets['name']) == 0) {
                                    $cashname   = 'Tunai';
                                } else {
                                    $cashname   = $cashdata['name'];
                                }

                                // Transaction Summary
                                $dailyreportdata['trxpayments'][$cashdata['id']]['name']                                 = $cashname;
                                $dailyreportdata['trxpayments'][$cashdata['id']]['detail'][$trxpayment['id']]['name']    = $payment['name'];
                                $dailyreportdata['trxpayments'][$cashdata['id']]['detail'][$trxpayment['id']]['value']   = $trxpayment['value'];

                                // Detail Transaction
                                $dailyreportdata['payments'][$trx['id']]['detail'][$payment['id']]['name']               = $payment['name'];
                                $dailyreportdata['payments'][$trx['id']]['detail'][$payment['id']]['value']              = $trxpayment['value'];
                            }
                        }
                    }

                    if (!empty($debtpayments)) {
                        foreach ($debtpayments as $debtpayment) {
                            // Transaction Summary
                            $dailyreportdata['trxpayments'][0]['name']                               = 'Kasbon';
                            $dailyreportdata['trxpayments'][0]['detail'][0]['name']                  = 'Kasbon';
                            $dailyreportdata['trxpayments'][0]['detail'][0]['value']                 = $debtpayment['value'];

                            // Detail Transaction
                            $dailyreportdata['payments'][$trx['id']]['detail'][0]['name']            = 'Kasbon';
                            $dailyreportdata['payments'][$trx['id']]['detail'][0]['value']           = $debtpayment['value'];
                        }
                    }

                    if (!empty($pointpayments)) {
                        foreach ($pointpayments as $pointpayment) {
                            // Transaction Summary
                            $dailyreportdata['trxpayments'][-1]['name']                              = lang('Global.redeemPoint');
                            $dailyreportdata['trxpayments'][-1]['detail'][-1]['name']                = lang('Global.redeemPoint');
                            $dailyreportdata['trxpayments'][-1]['detail'][-1]['value']               = $pointpayment['value'];

                            // Detail Transaction
                            $dailyreportdata['payments'][$trx['id']]['detail'][-1]['name']            = lang('Global.redeemPoint');
                            $dailyreportdata['payments'][$trx['id']]['detail'][-1]['value']           = $pointpayment['value'];
                        }
                    }
                }

                // Actual Cash Close
                $dailyreportdata['cashclose']        = $dailyreport['cashclose'];

                // Actual Non Cash Close
                $dailyreportdata['noncashclose']     = $dailyreport['noncashclose'];

                // Actual Cashier Summary
                $dailyreportdata['actualsummary']    = (Int)$dailyreport['cashclose'] + (Int)$dailyreport['noncashclose'];
            // } else {
            //     $dailyreportdata['dateclose']    = lang('Global.storeNotClosed');

            //     // User Close Store
            //     $dailyreportdata['userclose']    = lang('Global.storeNotClosed');

            //     // Payment Methods
            //     $dailyreportdata['payments']     = [];
            //     $dailyreportdata['trxpayments']  = [];

            //     // Actual Cash Close
            //     $dailyreportdata['cashclose']        = '0';

            //     // Actual Non Cash Close
            //     $dailyreportdata['noncashclose']     = '0';

            //     // Actual Cashier Summary
            //     $dailyreportdata['actualsummary']    = (Int)$dailyreport['cashclose'] + (Int)$dailyreport['noncashclose'];
            // }

            // Cash Flow
            $trxother   = $TrxotherModel->where('date >', $dailyreport['dateopen'])->where('outletid', $this->data['outletPick'])->notLike('description', 'Debt')->notLike('description', 'Top Up')->find();
            $debtins    = $TrxotherModel->where('date >', $dailyreport['dateopen'])->where('outletid', $this->data['outletPick'])->Like('description', 'Debt')->find();
            $topups     = $TrxotherModel->where('date >', $dailyreport['dateopen'])->where('outletid', $this->data['outletPick'])->Like('description', 'Top Up')->find();
            $withdraws  = $TrxotherModel->where('date >', $dailyreport['dateopen'])->where('outletid', $this->data['outletPick'])->Like('description', 'Cash Withdraw')->find();

            if (!empty($trxother)) {
                foreach ($trxother as $trxot) {
                    // User Cashier
                    $usercashcier   = $UserModel->find($trxot['userid']);

                    // Cashflow Data
                    $dailyreportdata['cashflow'][$trxot['id']]['cashier'] = $usercashcier->firstname.' '.$usercashcier->lastname;
                    $dailyreportdata['cashflow'][$trxot['id']]['type']    = $trxot['type'];
                    $dailyreportdata['cashflow'][$trxot['id']]['desc']    = $trxot['description'];
                    $dailyreportdata['cashflow'][$trxot['id']]['date']    = date('H:i:s', strtotime($trxot['date']));
                    $dailyreportdata['cashflow'][$trxot['id']]['qty']     = $trxot['qty'];
                    $dailyreportdata['cashflow'][$trxot['id']]['proof']   = $trxot['photo'];
                }
            } else {
                $usercashcier   = [];
                $dailyreportdata['cashflow'] = [];
            }

            if (!empty($debtins)) {
                foreach ($debtins as $debtin) {
                    // User Cashier
                    $usercashcier   = $UserModel->find($debtin['userid']);

                    // Debt Installment Data
                    $cashdebt       = $CashModel->find($debtin['cashid']);
                    $dailyreportdata['debtins'][$cashdebt['id']]['name']                             = $cashdebt['name'];

                    // Detail Debt Installment
                    $dailyreportdata['debtins'][$cashdebt['id']]['detail'][$debtin['id']]['value']   = $debtin['qty'];
                    $dailyreportdata['debtins'][$cashdebt['id']]['detail'][$debtin['id']]['cashier'] = $usercashcier->firstname.' '.$usercashcier->lastname;
                    $dailyreportdata['debtins'][$cashdebt['id']]['detail'][$debtin['id']]['type']    = $debtin['type'];
                    $dailyreportdata['debtins'][$cashdebt['id']]['detail'][$debtin['id']]['desc']    = $debtin['description'];
                    $dailyreportdata['debtins'][$cashdebt['id']]['detail'][$debtin['id']]['date']    = date('H:i:s', strtotime($debtin['date']));
                    $dailyreportdata['debtins'][$cashdebt['id']]['detail'][$debtin['id']]['qty']     = $debtin['qty'];
                    $dailyreportdata['debtins'][$cashdebt['id']]['detail'][$debtin['id']]['proof']   = $debtin['photo'];
                }
            } else {
                $usercashcier   = [];
                $dailyreportdata['debtins'] = [];
            }

            if (!empty($topups)) {
                foreach ($topups as $topup) {
                    // User Cashier
                    $usercashcier   = $UserModel->find($topup['userid']);

                    // Top Up Data
                    $cashtopup      = $CashModel->find($topup['cashid']);
                    $dailyreportdata['topup'][$cashtopup['id']]['name']                              = $cashtopup['name'];

                    // Detail Top Up
                    $dailyreportdata['topup'][$cashtopup['id']]['detail'][$topup['id']]['value']     = $topup['qty'];
                    $dailyreportdata['topup'][$cashtopup['id']]['detail'][$topup['id']]['cashier']   = $usercashcier->firstname.' '.$usercashcier->lastname;
                    $dailyreportdata['topup'][$cashtopup['id']]['detail'][$topup['id']]['type']      = $topup['type'];
                    $dailyreportdata['topup'][$cashtopup['id']]['detail'][$topup['id']]['desc']      = $topup['description'];
                    $dailyreportdata['topup'][$cashtopup['id']]['detail'][$topup['id']]['date']      = date('H:i:s', strtotime($topup['date']));
                    $dailyreportdata['topup'][$cashtopup['id']]['detail'][$topup['id']]['qty']       = $topup['qty'];
                    $dailyreportdata['topup'][$cashtopup['id']]['detail'][$topup['id']]['proof']     = $topup['photo'];
                }
            } else {
                $usercashcier   = [];
                $dailyreportdata['topup'] = [];
            }

            if (!empty($withdraws)) {
                foreach ($withdraws as $withdraw) {
                    // User Cashier
                    $usercashcier   = $UserModel->find($withdraw['userid']);

                    // Withdraw Data
                    $cashwithdraw   = $CashModel->find($withdraw['cashid']);
                    $dailyreportdata['withdraw'][$cashwithdraw['id']]['name']                                = $cashwithdraw['name'];

                    // Detail Withdraw
                    $dailyreportdata['withdraw'][$cashwithdraw['id']]['detail'][$withdraw['id']]['value']    = $withdraw['qty'];
                    $dailyreportdata['withdraw'][$cashwithdraw['id']]['detail'][$withdraw['id']]['cashier']  = $usercashcier->firstname.' '.$usercashcier->lastname;
                    $dailyreportdata['withdraw'][$cashwithdraw['id']]['detail'][$withdraw['id']]['type']     = $withdraw['type'];
                    $dailyreportdata['withdraw'][$cashwithdraw['id']]['detail'][$withdraw['id']]['desc']     = $withdraw['description'];
                    $dailyreportdata['withdraw'][$cashwithdraw['id']]['detail'][$withdraw['id']]['date']     = date('H:i:s', strtotime($withdraw['date']));
                    $dailyreportdata['withdraw'][$cashwithdraw['id']]['detail'][$withdraw['id']]['qty']      = $withdraw['qty'];
                    $dailyreportdata['withdraw'][$cashwithdraw['id']]['detail'][$withdraw['id']]['proof']    = $withdraw['photo'];
                }
            } else {
                $usercashcier   = [];
                $dailyreportdata['withdraw'] = [];
            }

            // Initial Cash
            $dailyreportdata['initialcash']      = $dailyreport['initialcash'];

            // Total Cash In
            $dailyreportdata['totalcashin']      = $dailyreport['totalcashin'];

            // Total Cash Out
            $dailyreportdata['totalcashout']     = $dailyreport['totalcashout'];
        }
        // }
        // dd($dailyreportdata);

        // Parsing data to view
        $data                       = $this->data;
        $data['title']              = lang('Global.cashinout');
        $data['description']        = lang('Global.cashinoutListDesc');
        $data['trxothers']          = $trxothers;
        $data['users']              = $users;
        $data['cash']               = $cash;
        $data['outlets']            = $outlet;
        $data['dailyreport']        = $dailyreportdata;
        // $data['dailyreport']        = $dailyreport;
        $data['today']              = $today;
        $data['payments']           = $payments;
        $data['pager']              = $TrxotherModel->pager;
        $data['startdate']          = strtotime($startdate);
        $data['enddate']            = strtotime($enddate);

        // if (!empty($dailyreport)) {
        //     $cashflow           = (($dailyreport['initialcash'] + $dailyreport['totalcashin']) - $dailyreport['totalcashout']);
        //     $data['cashflow']   = $cashflow;

        //     $outlet             = $OutletModel->find($this->data['outletPick']);
            
        //     // Get Payment Method
        //     $pettycash          = $CashModel->where('name', 'Petty Cash ' . $outlet['name'])->first();
        //     $cashpayment        = $PaymentModel->where('cashid', $pettycash['id'])->first();
            
        //     // Get Trx Other
        //     $trxother           = $TrxotherModel->where('date >', $dailyreport['dateopen'])->where('outletid', $this->data['outletPick'])->find();

        //     // Get Transaction Cash
        //     $cashtrx            = $TransactionModel->where('date >', $dailyreport['dateopen'])->where('outletid', $this->data['outletPick'])->find();
        //     $noncashtrx         = $TransactionModel->where('date >', $dailyreport['dateopen'])->where('outletid', $this->data['outletPick'])->find();

        //     $trxcashid          = array();
        //     foreach ($cashtrx as $cashtr) {
        //         $trxcashid[]    = $cashtr['id'];
        //     }

        //     if (!empty($cashtrx)) {
        //         $trxpaycash         = $TrxpaymentModel->where('paymentid', $cashpayment['id'])->whereIn('transactionid', $trxcashid)->find();
        //         $cashtrxvalue       = array_sum(array_column($trxpaycash, 'value'));
        //     } else {
        //         $cashtrxvalue       = '0';
        //     }

        //     $data['cashtrxvalue']       = $cashtrxvalue;

        //     // Get Transaction Non Cash
        //     $noncash            = $CashModel->notLike('name', 'Petty Cash')->find();

        //     $noncashid          = array();
        //     foreach ($noncash as $nocash) {
        //         $noncashid[]    = $nocash['id'];
        //     }

        //     if (!empty($noncashid)) {
        //         $noncashpayments    = $PaymentModel->whereIn('cashid', $noncashid)->find();
        //     } else {
        //         $noncashpayments    = array();
        //     }

        //     $noncashpaymentid   = array();
        //     foreach ($noncashpayments as $noncashpayment) {
        //         $noncashpaymentid[]     = $noncashpayment['id'];
        //     }

        //     $trxnoncashid               = array();
        //     foreach ($noncashtrx as $noncashtr) {
        //         $trxnoncashid[] = $noncashtr['id'];
        //     }

        //     if (!empty($trxnoncashid) && !empty($noncashpaymentid)) {
        //         $trxpaynoncash          = $TrxpaymentModel->whereIn('transactionid', $trxnoncashid)->whereIn('paymentid', $noncashpaymentid)->find();
        //     } else {
        //         $trxpaynoncash          = array();
        //     }

        //     $noncashtrxvalue            = array_sum(array_column($trxpaynoncash, 'value'));
        //     $data['noncashtrxvalue']    = $noncashtrxvalue;

        //     // Expected Cash
        //     $expectedcash               = ($cashflow + $cashtrxvalue);
        //     $data['expectedcash']       = $expectedcash;

        //     // Total System Receipts
        //     $totalsystemrec             = $expectedcash + $noncashtrxvalue;
        //     $data['totalsystemrec']     = $totalsystemrec;
        // }

        return view('Views/cash', $data);
    }

    public function create()
    {
        // Calling Model
        $TrxotherModel      = new TrxotherModel;
        $UserModel          = new UserModel;
        $CashModel          = new CashModel;
        $DailyReportModel   = new DailyReportModel;

        // initialize
        $input              = $this->request->getPost();
        $cash               = $CashModel->like('name', 'Cash')->where('outletid', $this->data['outletPick'])->first();

        // Get Date
        $date               = date_create();
        $tanggal            = date_format($date, 'Y-m-d H:i:s');

        // Image Capture
        if (!empty($input['image'])) {
            $img                = $input['image'];
            $folderPath         = "img/tfproof/";
            $image_parts        = explode(";base64,", $img);
            $image_type_aux     = explode("image/", $image_parts[0]);
            $image_type         = $image_type_aux[1];
            $image_base64       = base64_decode($image_parts[1]);
            $fileName           = uniqid() . '.png';
            $file               = $folderPath . $fileName;
            file_put_contents($file, $image_base64);
        } else {
            $fileName = "NULL";
        }

        // Data Input
        $data  = [
            'userid'        => $this->data['uid'],
            'outletid'      => $this->data['outletPick'],
            'cashid'        => $cash['id'],
            'description'   => $input['description'],
            'type'          => $input['cash'],
            'date'          => $tanggal,
            'qty'           => $input['quantity'],
            'photo'         => $fileName,
        ];
        // Save Data Trx Other
        $TrxotherModel->save($data);

        // Plus & Minus Cash Wallet
        if ($input['cash'] === "0") {
            $cas = $input['quantity'] + $cash['qty'];
        } else {
            $cas =  $cash['qty'] - $input['quantity'];
        }

        $wallet = [
            'id'    => $cash['id'],
            'qty'   => $cas,
        ];
        $CashModel->save($wallet);

        // Find Data for Daily Report
        $today              = date('Y-m-d') . ' 00:00:01';
        $dailyreports       = $DailyReportModel->where('outletid', $this->data['outletPick'])->where('dateopen >', $today)->find();
        foreach ($dailyreports as $dayrep) {
            if ($input['cash'] === "0") {
                $tcashin = [
                    'id'            => $dayrep['id'],
                    'totalcashin'   => $dayrep['totalcashin'] + $input['quantity'],
                ];
                $DailyReportModel->save($tcashin);
            } else {
                $tcashout = [
                    'id'            => $dayrep['id'],
                    'totalcashout'  => $dayrep['totalcashout'] + $input['quantity'],
                ];
                $DailyReportModel->save($tcashout);
            }
        }

        // return
        return redirect()->back()->with('message', lang('Global.saved'));
    }

    public function withdraw()
    {
        // Calling Model
        $TrxotherModel  = new TrxotherModel;
        $UserModel      = new UserModel;
        $CashModel      = new CashModel;
        $PaymentModel   = new PaymentModel;
        $DailyReportModel   = new DailyReportModel;

        // initialize
        $input          = $this->request->getPost();
        $cash           = $CashModel->like('name', 'Cash')->where('outletid', $this->data['outletPick'])->first();

        $payment        = $PaymentModel->where('id', $input['payment'])->first();
        $idcashplus     = $CashModel->where('id', $payment['cashid'])->first();

        // Get Date
        $date           = date_create();
        $tanggal        = date_format($date, 'Y-m-d H:i:s');

        // Image Capture
        $img            = $input['image'];
        $folderPath     = "img/tfproof/";
        $image_parts    = explode(";base64,", $img);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type     = $image_type_aux[1];
        $image_base64   = base64_decode($image_parts[1]);
        $fileName       = uniqid() . '.png';
        $file           = $folderPath . $fileName;
        file_put_contents($file, $image_base64);

        // Data Input
        $data  = [
            'userid'        => $this->data['uid'],
            'outletid'      => $this->data['outletPick'],
            'cashid'        => $cash['id'],
            'description'   => lang('Global.withdraw') . " - " . $input['name'],
            'type'          => "1",
            'date'          => $tanggal,
            'qty'           => $input['value'],
            'photo'         => $fileName,
        ];
        // Save Data Cash
        $TrxotherModel->save($data);

        // Minus Cash Wallet
        $cas =  $cash['qty'] - $input['value'];

        $wallet = [
            'id'    => $cash['id'],
            'qty'   => $cas,
        ];
        $CashModel->save($wallet);

        // Plus Cash 
        $datacash = [
            'id'    => $idcashplus['id'],
            'qty'   => $input['value'] + $idcashplus['qty'],
        ];
        $CashModel->save($datacash);

        // Find Data for Daily Report
        $today              = date('Y-m-d') . ' 00:00:01';
        $dailyreports       = $DailyReportModel->where('dateopen >', $today)->find();

        foreach ($dailyreports as $dayrep) {
            $tcashout = [
                'id'            => $dayrep['id'],
                'totalcashout'  => (Int)$dayrep['totalcashout'] + (Int)$input['value'],
            ];
            $DailyReportModel->save($tcashout);
        }

        // return
        return redirect()->back()->with('message', lang('Global.saved'));
    }
}
