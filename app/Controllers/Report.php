<?php

namespace App\Controllers;

use App\Models\BundledetailModel;
use App\Models\BundleModel;
use App\Models\CategoryModel;
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
use App\models\TrxpaymentModel;
use App\Models\BookingModel;
use App\Models\BookingdetailModel;
use App\Models\PurchaseModel;
use App\Models\PurchasedetailModel;
use App\Models\PresenceModel;

use App\Models\GroupUserModel;
use Myth\Auth\Models\GroupModel;

class Report extends BaseController
{

    public function penjualan()
    {
        // Calling models
        $TransactionModel = new TransactionModel();

        // Populating Data
        $input = $this->request->getGet();

        if (!empty($input)) {
            $startdate = strtotime($input['startdate']);
            $enddata = strtotime($input['enddate']);
        } else {
            $startdate = strtotime(date('Y-m-1'));
            $enddate = strtotime(date('Y-m-t'));
        }

        $transactions = array();
        for ($date = $startdate; $date <= $enddate; $date += (86400)) {
            $transaction = $TransactionModel->where('date >=', date('Y-m-d 00:00:00', $date))->where('date <=', date('Y-m-d 23:59:59', $date))->find();
            $summary = array_sum(array_column($transaction, 'value'));
            $transactions[] = [
                'date'      => date('d/m/y', $date),
                'value'     => $summary
            ];
        }

        $result = array_sum(array_column($transactions, 'value'));

        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.transaction');
        $data['description']    = lang('Global.transactionListDesc');
        $data['transactions']   = $transactions;
        $data['result']         = $result;

        return view('Views/report/penjualan', $data);
    }

    public function keuntungan(){

        // Calling Models
        $TransactionModel       = new TransactionModel;
        $TrxdetailModel         = new TrxdetailModel;
        $VariantModel           = new VariantModel;

        $input = $this->request->getGet();
        
        if (!empty($input)) {
            $startdate = strtotime($input['startdate']);
            $enddata = strtotime($input['enddate']);
        } else {
            $startdate = strtotime(date('Y-m-1'));
            $enddate = strtotime(date('Y-m-t'));
        }
        
        $transactions = array();
        for ($date = $startdate; $date <= $enddate; $date += (86400)) {
            $transaction = $TransactionModel->where('date >=', date('Y-m-d 00:00:00', $date))->where('date <=', date('Y-m-d 23:59:59', $date))->find();
            $trxdetails  = $TrxdetailModel->findAll();
            $variants    = $VariantModel->findAll();
    
            $summary = array_sum(array_column($transaction, 'value'));
            $marginmodals = array();
            $margindasars = array();

            foreach ($transaction as $trx){
                foreach ($trxdetails as $trxdetail){
                    if($trx['id'] === $trxdetail['transactionid']){
                        $marginmodal = $trxdetail['marginmodal'];
                        $margindasar = $trxdetail['margindasar'];
                        $marginmodals[] = $marginmodal;
                        $margindasars[] = $margindasar;
                    }
                }
                
            }    

            $marginmodalsum = array_sum($marginmodals);
            $margindasarsum = array_sum($margindasars);

            $transactions[] = [
                'date'      => date('d/m/y', $date),
                'value'     => $summary,
                'modal'     => $marginmodalsum,
                'dasar'     => $margindasarsum,
            ];  
            
        }
        
        $keuntunganmodal = array_sum(array_column($transactions, 'modal'));
        $keuntungandasar = array_sum(array_column($transactions, 'dasar'));
        $trxvalue        = array_sum(array_column($transactions, 'value'));
        // Parsing Data to View
        $data                       = $this->data;
        $data['title']              = lang('Global.transaction');
        $data['description']        = lang('Global.transactionListDesc');
        $data['transactions']       = $transactions;
        $data['modals']             = $keuntunganmodal;
        $data['dasars']             = $keuntungandasar;
        $data['penjualanDasar']     = $trxvalue;
        $data['penjualanModal']     = $trxvalue;

        return view('Views/report/keuntungan', $data);
    }
    
    public function diskon(){

        // Calling Models
        $TransactionModel       = new TransactionModel;
        $TrxdetailModel         = new TrxdetailModel;
        $GconfigModel           = new GconfigModel;
        // Populating Data
        $input = $this->request->getGet();
        $trxdetails             = $TrxdetailModel->findAll();
        $Gconfig                = $GconfigModel->first();


        if (!empty($input)) {
            $startdate = strtotime($input['startdate']);
            $enddata = strtotime($input['enddate']);
        } else {
            $startdate = strtotime(date('Y-m-1'));
            $enddate = strtotime(date('Y-m-t'));
        }

        $transactions = array();
        for ($date = $startdate; $date <= $enddate; $date += (86400)) {
            $transaction = $TransactionModel->where('date >=', date('Y-m-d 00:00:00', $date))->where('date <=', date('Y-m-d 23:59:59', $date))->find();
            foreach ($transaction as $trx){
                $discounttrx = array();
                $discounttrxpersen = array();
                $discountvariant = array();
                $discountpoin = array();
                $discountmember = array();
                foreach ($trxdetails as $trxdetail){
                    if($trx['id'] === $trxdetail['transactionid']){
                        if ($trx['disctype'] === "0"){
                            $discounttrx[]          = $trx['discvalue'];
                        }
                        if ($trx['disctype'] !== "0"){
                            $discounttrxpersen[]    = $trx['value'] - ($trx['value'] - $trx['discvalue']/100);
                        }
                        if(!empty($trx['memberid']) && $trx['memberid'] !== "0"){
                            if($Gconfig['memberdisctype'] === "0"){
                                $discountmember[]       = $Gconfig['memberdisc'];
                            }else{
                                $discountmember[]       = $trx['value'] - ($trx['value'] - $Gconfig['memberdisc']/100);
                            }
                        }
                        $discountvariant[]          = $trxdetail['discvar'];
                        $discountpoin[]             = $trx['pointused'];
                        
                    }
                }
                $transactiondisc = array_sum($discounttrx) +  array_sum($discounttrxpersen) ;
                $variantdisc     = array_sum($discountvariant);
                $poindisc        = array_sum($discountpoin);
                $memberdisc      = array_sum($discountmember);

                $transactions[] = [
                    'date'          => date('d/m/y', $date),
                    'trxdisc'       => $transactiondisc,
                    'variantdis'    => $variantdisc,
                    'poindisc'      => $poindisc,
                    'memberdisc'    => $memberdisc,
                ];  
            }    
            
        }

        $trxvar = array_sum(array_column($transactions, 'variantdis'));
        $trxdis = array_sum(array_column($transactions, 'trxdisc'));
        $dispoint = array_sum(array_column($transactions, 'poindisc'));
        $discountmember = array_sum(array_column($transactions,'memberdisc'));
       

        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.transaction');
        $data['description']    = lang('Global.transactionListDesc');
        $data['transactions']   = $transactions;
        $data['trxvardis']      = $trxvar;
        $data['trxdisc']        = $trxdis;
        $data['poindisc']       = $dispoint;
        $data['memberdis']      = $discountmember;


        return view('Views/report/diskon', $data);
    }

    public function payment(){
        
        $db      = \Config\Database::connect();
        $PaymentModel           = new PaymentModel;
        $TrxpaymentModel        = new TrxpaymentModel;
        $TransactionModel       = new TransactionModel;

        // Populating Data
        $input          = $this->request->getGet();
        $trxpayments    = $TrxpaymentModel->findall();
        $payments       = $PaymentModel->findAll();

        if (!empty($input)) {
            $startdate = strtotime($input['startdate']);
            $enddata = strtotime($input['enddate']);
        } else {
            $startdate = strtotime(date('Y-m-1'));
            $enddate = strtotime(date('Y-m-t'));
        }

        $transactions = array();
        for ($date = $startdate; $date <= $enddate; $date += (86400)) {
            $transaction = $TransactionModel->where('date >=', date('Y-m-d 00:00:00', $date))->where('date <=', date('Y-m-d 23:59:59', $date))->find();
            foreach ($transaction as $trx){

                $builder = $db->table('trxpayment');
                // $builder = $db->table('trxpayment')->where('transactionid',$trx['id'])->find();
                // $builder = $db->table('trxpayment')->where($trx['id']);
                $builder->selectCount('id','total_payment','payval');
                // $builder->selectCount('id','total_payment','payval')->where('transactionid',$trx['id']);
                $builder->select('paymentid');
                $builder->selectSum('value');
                $builder->groupBy('paymentid');
                $query = $builder->get();
                $pay = $query->getResult();

        
                $totalpay = array();
                foreach ($pay as $p ){
                    $totalpay[] =[
                        'payqty'    => $p->total_payment,
                        'payid'     => $p->paymentid,
                        'payvalue'  => $p->value,
                    ]; 
                }

                $paymethod = array();
                foreach ($payments as $paymet){
                    foreach ($totalpay as $totpay){
                        if($paymet['id'] === $totpay['payid']){
                        $paymethod[] = [
                        'qty'       => $totpay['payqty'],
                        'pid'       => $totpay['payid'],
                        'pvalue'    => $totpay['payvalue'],
                        'pname' => $paymet['name'],
                        ];
                        }
                    }
                }
            }   
        }
        
        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.transaction');
        $data['description']    = lang('Global.transactionListDesc');
        $data['payments']       = $paymethod;
        

        return view('Views/report/payment', $data);
    }

    public function product(){

        // Calling models
        $db                 = \Config\Database::connect();
        $TransactionModel   = new TransactionModel();
        $TrxdetailModel     = new TrxdetailModel();
        $ProductModel       = new ProductModel();
        $CategoryModel      = new CategoryModel();
        $VariantModel       = new VariantModel();
        $StockModel         = new StockModel();
        $BundleModel        = new BundleModel();
        $BundledetailModel  = new BundledetailModel();

        $products   = $ProductModel->findAll();
        $category   = $CategoryModel->findAll();
        $variants   = $VariantModel->findAll();
        $stocks     = $StockModel->findAll();
        $bundles    = $BundleModel->findAll();
        $bundets    = $BundledetailModel->findAll();


        // Populating Data
        $input = $this->request->getGet();

        if (!empty($input)) {
            $startdate = strtotime($input['startdate']);
            $enddata = strtotime($input['enddate']);
        } else {
            $startdate = strtotime(date('Y-m-1'));
            $enddate = strtotime(date('Y-m-t'));
        }

        $transactions = array();
        for ($date = $startdate; $date <= $enddate; $date += (86400)) {
            $transaction = $TransactionModel->where('date >=', date('Y-m-d 00:00:00', $date))->where('date <=', date('Y-m-d 23:59:59', $date))->find();
            
            $builder = $db->table('trxdetail');
            // $builder = $db->table('trxpayment')->where('transactionid',$trx)->find();
            $builder->selectCount('variantid','variantsales','salesvalue','bundleid');
            // $builder->selectCount('id','total_payment','payval')->where('transactionid',$trx['id']);
            $builder->select('variantid');
            $builder->select('bundleid');
            $builder->selectSum('value');
            $builder->groupBy('variantid');
            $query   = $builder->get();
            $variantval = $query->getResult();


            $varvalue = array();
            foreach ($variantval as $varval) {
                $varvalue[] = [
                    'id'        => $varval->variantid,
                    'value'     => $varval->value,
                    'sold'      => $varval->variantsales,
                    'bundleid'  => $varval->bundleid,
                ];
            }

            $var = array();
            foreach ($varvalue as $varv){
                foreach ($variants as $varian){
                    if($varian['id'] === $varv['id']){
                        foreach ($products as $product){
                            if($varian['productid'] === $product['id']){
                                foreach ($category as $cate){
                                    if($cate['id'] === $product['catid']){
                                        $var[] = [
                                            'id'        => $varv['id'],
                                            'value'     => $varv['value'],
                                            'sold'      => $varv['sold'],
                                            'bundleid'  => $varv['bundleid'],
                                            'productid' => $product['id'],
                                            'product'   => $product['name'],
                                            'category'  => $cate['name'],
                                        ];
                                    }
                                }
                            }
                        }
                    }
                }
            }

            // Sum Total Product Sold
            $produk = [];
            foreach ($var as $vars) {
                if (!isset($produk[$vars['productid'].$vars['product']])) {
                    $produk[$vars['productid'].$vars['product']] = $vars;
                } else {
                    $produk[$vars['productid'].$vars['product']]['sold'] += $vars['sold'];
                }
            }
            $produk = array_values($produk);

            

            $summary = array_sum(array_column($transaction, 'value'));
            $transactions[] = [
                'date'      => date('d/m/y', $date),
                'value'     => $summary
            ];
        }

        $result = array_sum(array_column($transactions, 'value'));


        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.transaction');
        $data['description']    = lang('Global.transactionListDesc');
        $data['transactions']   = $transactions;
        $data['products']       = $produk; 

        return view('Views/report/product', $data);
    }

    public function presence()
    {
        // calling model
        $PresenceModel  = new PresenceModel;
        $UserModel      = new UserModel;
        $UserGroupModel = new GroupUserModel;
        $GroupModel     = new GroupModel; 


        // populating data
        $presences  = $PresenceModel->findAll();
        $users      = $UserModel->findAll();
        $usergroups = $UserGroupModel->findAll();
        $groups     = $GroupModel->findAll();


        $absen = array();
        foreach ($presences as $presence ){
            foreach ($users as $user){
                if ($presence['userid'] === $user->id){
                    foreach ($usergroups as $ugroups){
                        if($ugroups['user_id'] === $user->id){
                            foreach ($groups as $group){
                                if ($ugroups['group_id'] === $group->id){
                                    $absen [] = [
                                        'id'        => $user->id,
                                        'name'      => $user->username,
                                        'date'      => $presence['datetime'],
                                        'status'    => $presence['status'],
                                        'role'      => $group->name,
                                    ];
                                }
                            }
                        }
                    }
                }
            }
        }

        // Sum Total Product Sold
        $admin = [];
        foreach ($absen as $abs) {
            $present = array();
            foreach ($absen as $abs){
                if ($abs['status'] === '1'){
                    $present[] = $abs['status'];
                }
            }
            $presen = count($present);
            if (!isset($admin[$abs['id'].$abs['name']])) {
                $admin[$abs['id'].$abs['name']] = $abs;
            } else {
                $admin[$abs['id'].$abs['name']]['status'] += $abs['status'];
            }
        }
        $admin = array_values($admin);

        // parsing data to view
        $data                   = $this->data;
        $data['title']          = lang('Global.transaction');
        $data['description']    = lang('Global.transactionListDesc'); 
        $data['presences']      = $admin;
        $data['present']        = $presen;

        return view('Views/report/presence',$data);
    }

    public function presencedetail($id)
    {
        // Calling Model
        $PresenceModel     = new PresenceModel;

        // Populating Data
        $presences         = $PresenceModel->where('userid',$id)->find();

        // parsing data to view
        $data                   = $this->data;
        $data['title']          = lang('Global.transaction');
        $data['description']    = lang('Global.transactionListDesc'); 
        $data['presences']      = $presences;

        return view('Views/report/presencedetail',$data);
    }

    public function employe(){

        // Calling Model
        $db                 = \Config\Database::connect();
        $TransactionModel   = new TransactionModel;
        $UserModel          = new UserModel;
        $UserGroupModel     = new GroupUserModel;
        $GroupModel         = new GroupModel; 

        // Populating Data 
        $transactions   = $TransactionModel->findAll();
        $admin          = $UserModel->findAll();
        $usergroups     = $UserGroupModel->findAll();
        $groups         = $GroupModel->findAll();

        foreach ($transactions as $transactions){
            $users   = $UserModel->find($transactions['userid']);
            $builder = $db->table('transaction')->where('userid',$users->id);
            $builder->selectCount('userid','value');
            $builder->select('userid');
            $builder->selectSum('value');
            $builder->groupBy('userid');
            $query   = $builder->get();
            $employe = $query->getResult();
        }
       
        $employetrx = array();
        foreach ($employe as $employ){
            foreach ($admin as $user ){
                if($employ->userid === $user->id){
                    foreach ($usergroups as $ugroups){
                        if($ugroups['user_id'] === $user->id){
                            foreach ($groups as $group){
                                if ($ugroups['group_id'] === $group->id){
                                    $employetrx [] = [
                                        'id'        => $user->id,
                                        'name'      => $user->username,
                                        'role'      => $group->name,
                                        'value'     => $employ->value,
                                    ];
                                }
                            }
                        }
                    }
                }
            }
        }

        // parsing data to view
        $data                   = $this->data;
        $data['title']          = lang('Global.transaction');
        $data['description']    = lang('Global.transactionListDesc'); 
        $data['employetrx']     = $employetrx;

        return view('Views/report/employe',$data);
    }
}