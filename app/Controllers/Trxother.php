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
        $outlets            = $OutletModel->findAll();
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

        // Parsing data to view
        $data                       = $this->data;
        $data['title']              = lang('Global.cashinout');
        $data['description']        = lang('Global.cashinoutListDesc');
        $data['trxothers']          = $trxothers;
        $data['users']              = $users;
        $data['cash']               = $cash;
        $data['outlets']            = $outlets;
        $data['dailyreport']        = $dailyreport;
        $data['today']              = $today;
        $data['payments']           = $payments;
        $data['pager']              = $TrxotherModel->pager;
        $data['startdate']          = strtotime($startdate);
        $data['enddate']            = strtotime($enddate);

        $trxdate = array();
        foreach ($trxothers as $trxot) {
            $trxdate[]    = $trxot['date'];
        }

        // Cash Flow
        if (!empty($dailyreport)) {
            $cashflow           = (($dailyreport['initialcash'] + $dailyreport['totalcashin']) - $dailyreport['totalcashout']);
            $data['cashflow']   = $cashflow;

            $outlet             = $OutletModel->find($this->data['outletPick']);
            
            // Get Payment Method
            $pettycash          = $CashModel->where('name', 'Petty Cash ' . $outlet['name'])->first();
            $cashpayment        = $PaymentModel->where('cashid', $pettycash['id'])->first();
            
            // Get Trx Other
            $trxother           = $TrxotherModel->where('date >', $dailyreport['dateopen'])->where('outletid', $this->data['outletPick'])->find();

            // Get Transaction Cash
            $cashtrx            = $TransactionModel->where('date >', $dailyreport['dateopen'])->where('outletid', $this->data['outletPick'])->find();
            $noncashtrx         = $TransactionModel->where('date >', $dailyreport['dateopen'])->where('outletid', $this->data['outletPick'])->find();

            $trxcashid          = array();
            foreach ($cashtrx as $cashtr) {
                $trxcashid[]    = $cashtr['id'];
            }

            if (!empty($cashtrx)) {
                $trxpaycash         = $TrxpaymentModel->where('paymentid', $cashpayment['id'])->whereIn('transactionid', $trxcashid)->find();
                $cashtrxvalue       = array_sum(array_column($trxpaycash, 'value'));
            } else {
                $cashtrxvalue       = '0';
            }

            $data['cashtrxvalue']       = $cashtrxvalue;

            // Get Transaction Non Cash
            $noncash            = $CashModel->notLike('name', 'Petty Cash')->find();

            $noncashid          = array();
            foreach ($noncash as $nocash) {
                $noncashid[]    = $nocash['id'];
            }

            if (!empty($noncashid)) {
                $noncashpayments    = $PaymentModel->whereIn('cashid', $noncashid)->find();
            } else {
                $noncashpayments    = array();
            }

            $noncashpaymentid   = array();
            foreach ($noncashpayments as $noncashpayment) {
                $noncashpaymentid[]     = $noncashpayment['id'];
            }

            $trxnoncashid               = array();
            foreach ($noncashtrx as $noncashtr) {
                $trxnoncashid[] = $noncashtr['id'];
            }

            if (!empty($trxnoncashid) && !empty($noncashpaymentid)) {
                $trxpaynoncash          = $TrxpaymentModel->whereIn('transactionid', $trxnoncashid)->whereIn('paymentid', $noncashpaymentid)->find();
            } else {
                $trxpaynoncash          = array();
            }

            $noncashtrxvalue            = array_sum(array_column($trxpaynoncash, 'value'));
            $data['noncashtrxvalue']    = $noncashtrxvalue;

            // Expected Cash
            $expectedcash               = ($cashflow + $cashtrxvalue);
            $data['expectedcash']       = $expectedcash;

            // Total System Receipts
            $totalsystemrec             = $expectedcash + $noncashtrxvalue;
            $data['totalsystemrec']     = $totalsystemrec;
        }

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
