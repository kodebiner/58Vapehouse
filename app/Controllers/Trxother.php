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
use App\models\TrxpaymentModel;
use App\Models\DebtModel;
use App\Models\DailyReportModel;

class Trxother extends BaseController

{
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
        // Calling Model
        $TransactionModel   = new TransactionModel;
        $TrxdetailModel     = new TrxdetailModel;
        $TrxpaymentModel    = new TrxpaymentModel;
        $TrxotherModel      = new TrxotherModel;
        $ProductModel       = new ProductModel;
        $VariantModel       = new VariantModel;
        $BundleModel        = new BundleModel;
        $BundledetailModel  = new BundledetailModel;
        $PaymentModel       = new PaymentModel;
        $DebtModel          = new DebtModel;
        $UserModel          = new UserModel;
        $CashModel          = new CashModel;
        $OutletModel        = new OutletModel;
        $DailyReportModel   = new DailyReportModel;

        // Find Data
        $auth               = service('authentication');
        $users              = $UserModel->findAll();
        $userId             = $auth->id();
        $GroupUser          = $this->GroupUserModel->where('user_id', $this->userId)->first();
        $roleid             = $GroupUser['group_id'];
        $user               = $UserModel->where('id',$userId)->first();
        $userOutlet         = $user->outletid;
        $outlets            = $OutletModel->findAll();
        $cash               = $CashModel->findAll();
        $cashpayments       = $PaymentModel->like('name', 'Cash')->where('outletid', $this->data['outletPick'])->first();
        
        $noncashpayments    = $PaymentModel->notLike('name', 'Cash')->find();
        
        // Operator 
        if ($roleid === 4) {
            $trxothers  = $TrxotherModel->orderBy('date', 'DESC')->notLike('description', 'Top Up')->notLike('description', 'Debt')->where('outletid',$userOutlet)->find();
        } else {
            if ($this->data['outletPick'] === null) {
                $trxothers  = $TrxotherModel->orderBy('date', 'DESC')->notLike('description', 'Top Up')->notLike('description', 'Debt')->find();
            } else {
                $trxothers  = $TrxotherModel->orderBy('date', 'DESC')->notLike('description', 'Top Up')->notLike('description', 'Debt')->where('outletid', $this->data['outletPick'])->find();
            }
        }

        // Find Data for Daily Report
        $today                  = date('Y-m-d') .' 00:00:01';
        $dailyreport            = $DailyReportModel->where('dateopen >', $today)->where('outletid', $this->data['outletPick'])->first();

        // Cash Flow
        if (!empty($dailyreport)) {
            $cashflow           = (($dailyreport['initialcash'] + $dailyreport['totalcashin']) - $dailyreport['totalcashout']);
            $outlet             = $OutletModel->find($this->data['outletPick']);
            // Get Transaction Cash
            $pettycash          = $CashModel->where('name', 'Petty Cash '.$outlet['name'])->first();
            $cashpayment        = $PaymentModel->where('cashid', $pettycash['id'])->first();
            $cashtrx            = $TransactionModel->where('date >', $dailyreport['dateopen'])->where('paymentid', $cashpayment['id'])->find();
            $cashtrxvalue       = array_sum(array_column($cashtrx, 'value'));

            // Get Transaction Non Cash
            $noncash            = $CashModel->notLike('name', 'Petty Cash')->find();
            $noncashid          = array();
            foreach ($noncash as $nocash) {
                $noncashid[] = $nocash['id'];
            }
            $noncashpayments    = $PaymentModel->whereIn('cashid', $noncashid)->find();
            $noncashpaymentid   = array();
            foreach ($noncashpayments as $noncashpayment) {
                $noncashpaymentid[] = $noncashpayment['id'];
            }
            $noncashtrx         = $TransactionModel->where('date >', $dailyreport['dateopen'])->where('outletid', $this->data['outletPick'])->whereIn('paymentid', $noncashpaymentid)->find();
            $noncashtrxvalue    = array_sum(array_column($noncashtrx, 'value'));

            // Expected Cash
            $expectedcash       = ($cashflow + $cashtrxvalue);

            // Total System Receipts
            $totalsystemrec     = $expectedcash + $noncashtrxvalue;
        }
        
        
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
        $data['cashflow']           = $cashflow;
        $data['cashtrxvalue']       = $cashtrxvalue;
        $data['expectedcash']       = $expectedcash;
        $data['noncashtrxvalue']    = $noncashtrxvalue;
        $data['totalsystemrec']     = $totalsystemrec;

        return view ('Views/cash', $data);
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
        $tanggal            = date_format($date,'Y-m-d H:i:s');

        // Image Capture
        $img                = $input['image'];
        $folderPath         = "img";
        $image_parts        = explode(";base64,", $img);
        $image_type_aux     = explode("image/", $image_parts[0]);
        $image_type         = $image_type_aux[1];
        $image_base64       = base64_decode($image_parts[1]);
        $fileName           = uniqid() . '.png';
        $file               = $folderPath . $fileName;
        file_put_contents($file, $image_base64);

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
        if ( $input['cash'] === "0" ){
            $cas = $input['quantity'] + $cash['qty'];
        } else {
            $cas =  $cash['qty'] - $input['quantity'] ;
        }

        $wallet = [
            'id'    => $cash['id'],
            'qty'   => $cas,
        ];
        $CashModel->save($wallet);

        // Find Data for Daily Report
        $today              = date('Y-m-d') .' 00:00:01';
        $dailyreports       = $DailyReportModel->where('dateopen >', $today)->find();
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
        $DailyReportModel   = new DailyReportModel;
        
        // initialize
        $input          = $this->request->getPost();
        $cash           = $CashModel->like('name', 'Cash')->where('outletid', $this->data['outletPick'])->first();

        // Get Date
        $date           = date_create();
        $tanggal        = date_format($date,'Y-m-d H:i:s');

        // Image Capture
        $img            = $input['image'];
        $folderPath     = "img";
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
            'description'   => lang('Global.withdraw')." - ".$input['name'],
            'type'          => "1",
            'date'          => $tanggal,
            'qty'           => $input['value'],
            'photo'         => $fileName,
        ];
        // Save Data Cash
        $TrxotherModel->save($data);

        // Minus Cash Wallet
        $cas =  $cash['qty'] - $input['value'] ;

        $wallet = [
            'id'    => $cash['id'],
            'qty'   => $cas,
        ];
        $CashModel->save($wallet);

        // Find Data for Daily Report
        $today              = date('Y-m-d') .' 00:00:01';
        $dailyreports       = $DailyReportModel->where('dateopen >', $today)->find();

        foreach ($dailyreports as $dayrep) {
            $tcashout = [
                'id'            => $dayrep['id'],
                'totalcashout'  => $dayrep['totalcashout'] + $input['value'],
            ];
            $DailyReportModel->save($tcashout);
        }

        // return
        return redirect()->back()->with('message', lang('Global.saved'));
    }
}
