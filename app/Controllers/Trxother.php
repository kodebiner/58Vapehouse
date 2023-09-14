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
        $dailyreports           = $DailyReportModel->where('outletid', $this->data['outletPick'])->where('dateopen >', $today)->find();

        // From Trx Other
        $topups                 = $TrxotherModel->like('description', 'Top Up')->where('date >', $today)->find();
        $debts                  = $TrxotherModel->like('description', 'Debt')->where('date >', $today)->find();
        $cashinout              = $TrxotherModel->notLike('description', 'Top Up')->notLike('description', 'Debt')->where('date >', $today)->where('outletid', $this->data['outletPick'])->find();
        
        // Parsing data to view
        $data                   = $this->data;
        $data['title']          = lang('Global.cashinout');
        $data['description']    = lang('Global.cashinoutListDesc');
        $data['trxothers']      = $trxothers;
        $data['topups']         = $topups;
        $data['debts']          = $debts;
        $data['cashinout']      = $cashinout;
        $data['users']          = $users;
        $data['cash']           = $cash;
        $data['outlets']        = $outlets;
        $data['dailyreports']   = $dailyreports;
        $data['today']          = $today;

        // Get Transaction ID
        $transactions                   = $TransactionModel->where('date >', $today)->where('outletid', $this->data['outletPick'])->find();
        $data['transactions']           = $transactions;
        
        // Get Trx Detail ID
        foreach ($transactions as $trx) {
            $trxdetails                 = $TrxdetailModel->where('transactionid', $trx['id'])->find();
            $data['trxdetails']         = $trxdetails;
            
            // Get Variant ID & Bunlde ID
            foreach ($trxdetails as $trxdet) {
                // Get Variant ID
                $variants               = $VariantModel->where('id', $trxdet['variantid'])->find();
                $data['variants']       = $variants;
                
                // Get Product ID
                foreach ($variants as $variant) {
                    $products           = $ProductModel->where('id', $variant['productid'])->find();
                    $data['products']   = $products;
                }

                // Get Bundle ID
                $bundles                = $BundleModel->where('id', $trxdet['bundleid'])->find();
                $data['bundles']        = $bundles;
                
                    // Get Bundle Detail ID
                foreach ($bundles as $bundle) {
                    $bundets            = $BundledetaiModel->where('bundleid', $bundle['id'])->find();
                    $data['bundets']    = $bundets;
                }
            }
            
            // Get Trx Payment
            $trxpayments                = $TrxpaymentModel->where('transactionid', $trx['id'])->find();
            $data['trxpayments']        = $trxpayments;

            // Get Payment ID
            foreach ($trxpayments as $trxpay) {
                $payments               = $PaymentModel->where('id', $trxpay['paymentid'])->find();
                $data['payments']       = $payments;
            }
        }

        return view ('Views/cash', $data);
    }

    public function create()
    {
        // Calling Model
        $TrxotherModel  = new TrxotherModel;
        $UserModel      = new UserModel;
        $CashModel      = new CashModel;
        
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
            'description'   => $input['description'],
            'type'          => $input['cash'],
            'date'          => $tanggal,
            'qty'           => $input['quantity'],
            'photo'         => $fileName,
        ];
        // Save Data Cash
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

        // return
        return redirect()->back()->with('message', lang('Global.saved'));

    }

    public function withdraw()
    {
        // Calling Model
        $TrxotherModel  = new TrxotherModel;
        $UserModel      = new UserModel;
        $CashModel      = new CashModel;
        
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

        // return
        return redirect()->back()->with('message', lang('Global.saved'));

    }
}
