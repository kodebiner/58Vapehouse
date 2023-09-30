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
    public function test(){
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
            $startdate = date('Y-m-1');
            $enddate = date('Y-m-t');
        }
 
        $transaction = $TransactionModel->where('date >=', $startdate)->where('date <=', $enddate)->find();

        $summary = array_sum(array_column($transaction, 'value'));
        $discounts = array();
        $transactionarr = array();
        
        $transactions = array();
        foreach ($transaction as $trx){
            $discounttrx = array();
            $discounttrxpersen = array();
            $discountvariant = array();
            $discountpoin = array();
            foreach ($trxdetails as $trxdetail){
                if($trx['id'] === $trxdetail['transactionid']){
                    if ($trx['disctype'] === "0"){
                        $discounttrx[]          = $trx['discvalue'];
                    }
                    if ($trx['disctype'] !== "0"){
                        $discounttrxpersen[]    = ($trxdetail['value'] * $trxdetail['qty']) - ($trx['value'] + $trxdetail['discvar']);
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
                'date'      =>  date_format($date,"d/m/Y"),
                'value'     => $trx['value'],
            ];  
        }


        $transactionarr [] = $transactions;
        $trxvar = array_sum(array_column($discounts, 'variantdis'));
        $trxdis = array_sum(array_column($discounts, 'trxdisc'));
        $dispoint = array_sum(array_column($discounts, 'poindisc'));

        $grossales = $summary + $trxvar +  $trxdis +  $dispoint ;

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

    public function penjualan()
    {
        // Calling Models
        $TransactionModel       = new TransactionModel;
        $TrxdetailModel         = new TrxdetailModel;
        $VariantModel           = new VariantModel;

        $input = $this->request->getGet('daterange');
        
        if (!empty($input)) {
            $daterange = explode(' - ', $input);
            $startdate = strtotime($daterange[0]);
            $enddate = strtotime($daterange[1]);
        } else {
            $startdate = strtotime(date('Y-m-1'));
            $enddate = strtotime(date('Y-m-t'));
        }
        
        $transactions = array();
        $transactionarr = array();
        $discount = array();
        $discounttrx = array();
        $discounttrxpersen = array();
        $discountvariant = array();
        $discountpoin = array();
        for ($date = $startdate; $date <= $enddate; $date += (86400)) {
            if($this->data['outletPick'] === null ){
                $transaction = $TransactionModel->where('date >=', date('Y-m-d 00:00:00', $date))->where('date <=', date('Y-m-d 23:59:59', $date))->find();
            }else{
                $transaction = $TransactionModel->where('date >=', date('Y-m-d 00:00:00', $date))->where('date <=', date('Y-m-d 23:59:59', $date))->where('outletid',$this->data['outletPick'])->find();
            }
            $trxdetails  = $TrxdetailModel->findAll();
            $summary = array_sum(array_column($transaction, 'value'));
            $variants    = $VariantModel->findAll();
   
            foreach ($transaction as $trx){
                foreach ($trxdetails as $trxdetail){
                    if($trx['id'] == $trxdetail['transactionid']){
                        if ($trx['disctype'] === "0"){
                            $discounttrx[]          = $trx['discvalue'];
                        }
                        if ($trx['disctype'] !== "0"){
                            $discounttrxpersen[]    = ($trxdetail['value'] * $trxdetail['qty']) * $trx['discvalue'];
                        }
                        $discountvariant[]          = $trxdetail['discvar'];
                        $discountpoin[]             = $trx['pointused'];
                    }
                    
                }
            }    
            $transactions[] = [
                'date'      => date('d/m/y', $date),
                'value'     => $summary,
            ];  
        }

        
        $transactiondisc = array_sum($discounttrx) +  array_sum($discounttrxpersen);
        $variantdisc     = array_sum($discountvariant);
        $poindisc        = array_sum($discountpoin);
        
        $dicount [] = [
            'trxdisc'       => $transactiondisc,
            'variantdis'    => $variantdisc,
            'poindisc'      => $poindisc,
        ];

        $transactionarr [] = $transactions;
        
        $salesresult = array_sum(array_column($transactions, 'value'));
      
        $grossales = $salesresult + $variantdisc +  $transactiondisc +  $poindisc ;

        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.salesreport');
        $data['description']    = lang('Global.transactionListDesc');
        $data['startdate']      = $startdate;
        $data['enddate']        = $enddate;
        $data['transactions']   = $transactions;
        $data['transactionarr'] = $transactionarr;
        $data['result']         = $salesresult;
        $data['gross']          = $grossales;

        return view('Views/report/penjualan', $data);
    }

    public function keuntungan(){

        // Calling Models
        $TransactionModel       = new TransactionModel;
        $TrxdetailModel         = new TrxdetailModel;
        $VariantModel           = new VariantModel;

        $input = $this->request->getGet('daterange');
        
        if (!empty($input)) {
            $daterange = explode(' - ', $input);
            $startdate = strtotime($daterange[0]);
            $enddate = strtotime($daterange[1]);
        } else {
            $startdate = strtotime(date('Y-m-1'));
            $enddate = strtotime(date('Y-m-t'));
        }
        
        $transactions = array();
        $transactionarr = array();
        for ($date = $startdate; $date <= $enddate; $date += (86400)) {
            if($this->data['outletPick'] === null ){
                $transaction = $TransactionModel->where('date >=', date('Y-m-d 00:00:00', $date))->where('date <=', date('Y-m-d 23:59:59', $date))->find();
            }else{
                $transaction = $TransactionModel->where('date >=', date('Y-m-d 00:00:00', $date))->where('date <=', date('Y-m-d 23:59:59', $date))->where('outletid',$this->data['outletPick'])->find();
            }
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

        $transactionarr [] = $transactions;
        
        $keuntunganmodal = array_sum(array_column($transactions, 'modal'));
        $keuntungandasar = array_sum(array_column($transactions, 'dasar'));
        $trxvalue        = array_sum(array_column($transactions, 'value'));
        
        // Parsing Data to View
        $data                       = $this->data;
        $data['title']              = lang('Global.profitreport');
        $data['description']        = lang('Global.profitListDesc');
        $data['transactions']       = $transactions;
        $data['modals']             = $keuntunganmodal;
        $data['dasars']             = $keuntungandasar;
        $data['penjualanDasar']     = $trxvalue;
        $data['penjualanModal']     = $trxvalue;
        $data['startdate']          = $startdate;
        $data['enddate']            = $enddate;
        
        return view('Views/report/keuntungan', $data);
    }
    
    public function diskon(){

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
            $startdate = date('Y-m-1');
            $enddate = date('Y-m-t');
        }

        $transactions = array();
        if ($this->data['outletPick'] === null) {
            $transaction = $TransactionModel->where('date >=', $startdate)->where('date <=', $enddate)->find();
        } else {
            $transaction = $TransactionModel->where('date >=', $startdate)->where('date <=', $enddate)->where('outletid',$this->data['outletPick'])->find();
        }
        foreach ($transaction as $trx){
            dd($transaction);
            $discounttrx = array();
            $discounttrxpersen = array();
            $discountvariant = array();
            $discountpoin = array();
            foreach ($trxdetails as $trxdetail){
                if($trx['id'] === $trxdetail['transactionid']){
                    if ($trx['disctype'] === "0"){
                        $discounttrx[]          = $trx['discvalue'];
                    }
                    if ($trx['disctype'] !== "0"){
                        $discounttrxpersen[]    = ($trxdetail['value'] * $trxdetail['qty']) - ($trx['value'] + $trxdetail['discvar']);
                    }
                    $discountvariant[]          = $trxdetail['discvar'];
                    $discountpoin[]             = $trx['pointused'];
                    
                }
            }

            $transactiondisc = array_sum($discounttrx) +  array_sum($discounttrxpersen);
            $variantdisc     = array_sum($discountvariant);
            $poindisc        = array_sum($discountpoin);

            $transactions[] = [
                'id'            => $trx['id'],
                'trxdisc'       => $transactiondisc,
                'variantdis'    => $variantdisc,
                'poindisc'      => $poindisc,
            ];  
        }    
            
        $trxvar = array_sum(array_column($transactions, 'variantdis'));
        $trxdis = array_sum(array_column($transactions, 'trxdisc'));
        $dispoint = array_sum(array_column($transactions, 'poindisc'));

        dd($transactions);

        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.discountreport');
        $data['description']    = lang('Global.profitListDesc');
        $data['transactions']   = $transactions;
        $data['trxvardis']      = $trxvar;
        $data['trxdisc']        = $trxdis;
        $data['poindisc']       = $dispoint;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);


        return view('Views/report/diskon', $data);
    }

    public function payment(){
        
        $db                     = \Config\Database::connect();
        $PaymentModel           = new PaymentModel;
        $TrxpaymentModel        = new TrxpaymentModel;
        $TransactionModel       = new TransactionModel;

        if ($this->data['outletPick'] != null) {
            $input = $this->request->getGet('daterange');
            
            if (!empty($input)) {
                $daterange = explode(' - ', $input);
                $startdate = $daterange[0];
                $enddate = $daterange[1];
            } else {
                $startdate = date('Y-m-1');
                $enddate = date('Y-m-t');
            }

            $payments = $PaymentModel->findAll();
            $trxpayments = $TrxpaymentModel->findAll();
            $transactions = $TransactionModel->where('outletid', $this->data['outletPick'])->where('date >=', $startdate)->where('date <=', $enddate)->find();
            $pay = array();
            foreach ($payments as $payment) {
                $qty = array();
                foreach ($trxpayments as $trxpayment) {
                    foreach ($transactions as $transaction) {
                        if (($trxpayment['paymentid'] === $payment['id']) && ($trxpayment['transactionid'] === $transaction['id'])) {
                            $qty[] = $trxpayment['value'];
                        }
                    }
                }
                $pay[] = [
                    'pvalue'    => array_sum($qty),
                    'pqty'      => count($qty),
                    'name'      => $payment['name']
                ];
            }

           $payresult = array_sum(array_column($pay,'pvalue'));
        
            // Parsing Data to View
            $data                   = $this->data;
            $data['title']          = lang('Global.paymentreport');
            $data['description']    = lang('Global.paymentListDesc');
            $data['payments']       = $pay;
            $data['startdate']      = strtotime($startdate);
            $data['enddate']        = strtotime($enddate);
            $data['total']          = $payresult;

            return view('Views/report/payment', $data);
        } else {
            return redirect()->to('');
        }
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
        $trxdetails = $TrxdetailModel->findAll();


        // Populating Data
        $input = $this->request->getGet('daterange');
            
        if (!empty($input)) {
            $daterange = explode(' - ', $input);
            $startdate = $daterange[0];
            $enddate = $daterange[1];
        } else {
            $startdate = date('Y-m-1');
            $enddate = date('Y-m-t');
        }


        // Populating Data
        if ($this->data['outletPick'] === null) {
            $transactions = $TransactionModel->where('date >=', $startdate)->where('date <=', $enddate)->find();
        } else {
            $transactions = $TransactionModel->where('date >=', $startdate)->where('date <=', $enddate)->where('outletid',$this->data['outletPick'])->find();
        }

        $productval     = [];
        $variantvalue   = [];
        $variantval     = [];
        $trxvar         = [];
        $diskon         = [];
        $productqty = [];
        foreach ($transactions as $transaction){
            
            $discounttrx = array();
            $discounttrxpersen = array();
            $discountvariant = array();
            $discountpoin = array();
            foreach ($trxdetails as $trxdetail){
                if($transaction['id'] === $trxdetail['transactionid']){
                    if ($transaction['disctype'] === "0"){
                        $discounttrx[]          = $transaction['discvalue'];
                    }
                    if ($transaction['disctype'] !== "0"){
                        $discounttrxpersen[]    = ($trxdetail['value'] * $trxdetail['qty']) * $transaction['discvalue'];
                        // $discounttrxpersen[]    = ($trxdetail['value'] * $trxdetail['qty']) - ($transaction['value'] + $trxdetail['discvar']);
                    }
                    $discountvariant[]          = $trxdetail['discvar'];

                    $discountpoin[]             = $transaction['pointused'];

                    foreach ($products as $product) {
                        foreach ($variants as $variant){
                            if(($variant['id'] === $trxdetail['variantid']) && ($variant['productid'] === $product['id'])){
                                
                                $variantval[]  = [
                                    'id'    => $variant['id'],
                                    'name'  => $product['name'].' - '.$variant['name'],
                                    'value' => $trxdetail['value'],
                                    'qty'   => $trxdetail['qty'],
                                ];
                            
                                foreach ($products as $product){
                                    if($variant['productid'] === $product['id']){
                                        $productval [] = $product['name'];
                                        foreach ($category as $cat){
                                            if($product['catid'] === $cat['id']){

                                                $variantvalue[] = [
                                                    'id'            => $product['id'],
                                                    'trxid'         => $transaction['id'],
                                                    'product'       => $product['name'],
                                                    'category'      => $cat['name'],
                                                    'qty'           => $trxdetail['qty'],
                                                    // 'trxdisc'       => $transactiondisc,
                                                    // 'variantdis'    => $variantdisc,
                                                    // 'poindisc'      => $poindisc,
                                                    // 'gross'         => $transaction['value'] +  $transactiondisc +  $variantdisc  +  $poindisc,
                                                ];
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $transactiondisc = array_sum($discounttrx) +  array_sum($discounttrxpersen);
            $variantdisc     = array_sum($discountvariant);
            $poindisc        = array_sum($discountpoin);

            $diskon[] = [
                'id'            => $transaction['id'],
                'trxdisc'       => $transactiondisc,
                'value'         => $transaction['value'],
                'variantdis'    => $variantdisc,
                'poindisc'      => $poindisc,
            ];  
            
        }

        // dd($variantvalue);
        foreach ($variantvalue as $val){
            foreach ($diskon as $disc){
              if ($val['trxid'] == $disc['id']){
                $productqty [] = [
                    'id'        => $val['id'],
                    'product'   => $val['product'],
                    'category'  => $val['category'],
                    'value'     => $disc['value'],
                    'gross'     => $disc['value'] + $disc['trxdisc'] + $disc['variantdis'] + $disc['poindisc'],
                    'qty'       => $val['qty'],
                ];
              } 
            }
        }

        // dd($productqty);

        // dd(array_sum(array_column($diskon,'value')));

        // dd(array_sum(array_column($productqty,'value')));
        // dd($productqty);

        $produk = [];
        foreach ($productqty as $vars) {
            if (!isset($produk[$vars['id'].$vars['product']])) {
                $produk[$vars['id'].$vars['product']] = $vars;
            } else {
                $produk[$vars['id'].$vars['product']]['value'] += $vars['value'];
                $produk[$vars['id'].$vars['product']]['qty'] += $vars['qty'];
                $produk[$vars['id'].$vars['product']]['gross'] += $vars['gross'];
            }
        }
        $produk = array_values($produk);

        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.productreport');
        $data['description']    = lang('Global.productListDesc');
        $data['transactions']   = $transactions;
        $data['products']       = $produk;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);

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
        $data['title']          = lang('Global.presencereport');
        $data['description']    = lang('Global.presenceListDesc'); 
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
        $data['title']          = lang('Global.presence');
        $data['description']    = lang('Global.presencedetailListDesc'); 
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
        $data['title']          = lang('Global.employereport');
        $data['description']    = lang('Global.employeListDesc'); 
        $data['employetrx']     = $employetrx;

        return view('Views/report/employe',$data);
    }

    public function customer(){
        
        // Calling Models
        $db                 = \Config\Database::connect();
        $TransactionModel   = new TransactionModel;
        $MemberModel        = new MemberModel;
        $DebtModel          = new DebtModel;


        // Populating Data
        $members            = $MemberModel->findAll();
        $debts              = $DebtModel->findAll();

        if ($this->data['outletPick'] != null) {
            $input = $this->request->getGet('daterange');
            
            if (!empty($input)) {
                $daterange = explode(' - ', $input);
                $startdate = $daterange[0];
                $enddate = $daterange[1];
            } else {
                $startdate = date('Y-m-1');
                $enddate = date('Y-m-t');
            }

            $transactions = $TransactionModel->where('date >=', $startdate)->where('date <=', $enddate)->find();

            
            $customer = array();
            foreach ($members as $member) {
                $totaltrx = array();
                $trxval = array();
                $debtval    = array();
                foreach ($debts as $debt) {
                    if ($member['id'] === $debt['memberid']) {
                        $debtval[]  = $debt['value'];
                    }
                }
                foreach ($transactions as $trx) {
                    if ($member['id'] === $trx['memberid']) {
                        $totaltrx[] = $trx['memberid'];
                        $trxval[]   = $trx['value'];
                    }
                }
                
                $customer[] =[
                    'id'    => $member['id'],
                    'name'  => $member['name'],
                    'debt'  => array_sum($debtval),
                    'trx'   => count($totaltrx),
                    'value' => array_sum($trxval),
                    'phone' => $member['phone'],
                ];
            }
        
            // Parsing Data to View
            $data                       = $this->data;
            $data['title']              = lang('Global.customer');
            $data['description']        = lang('Global.customerListDesc');
            $data['customers']          = $customer;
            $data['startdate']          = strtotime($startdate);
            $data['enddate']            = strtotime($enddate);

            return view('Views/report/customer', $data);
        } else {
            return redirect()->to('');
        }
    }

    public function customerdetail($id){

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
        $transactions           = $TransactionModel->orderBy('date', 'DESC')->where('memberid',$id)->find();
        $trxdetails             = $TrxdetailModel->findAll();
        $trxpayments            = $TrxpaymentModel->findAll();
        $debts                  = $DebtModel->where('memberid',$id)->find();

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
        
        return view('Views/report/customerdetail',$data);
    }

    public function bundle(){

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

        // Populating Data
        $products   = $ProductModel->findAll();
        $category   = $CategoryModel->findAll();
        $variants   = $VariantModel->findAll();
        $stocks     = $StockModel->findAll();
        $bundles    = $BundleModel->findAll();
        $bundets    = $BundledetailModel->findAll();
        $trxdetails = $TrxdetailModel->findAll();

        // initialize
        $input = $this->request->getGet('daterange');
            
        if (!empty($input)) {
            $daterange = explode(' - ', $input);
            $startdate = $daterange[0];
            $enddate = $daterange[1];
        } else {
            $startdate = date('Y-m-1');
            $enddate = date('Y-m-t');
        }
       
        $transactions = $TransactionModel->where('date >=', $startdate)->where('date <=', $enddate)->find();

        $bund = [];
        foreach($transactions as $transaction){
            foreach ($trxdetails as $trxdetail){
                foreach ($bundles as $bundle){
                    if($trxdetail['transactionid'] === $transaction['id'] && $trxdetail['bundleid'] !=="0" && $bundle['id'] === $trxdetail['bundleid']){
                        $bund[] = [
                            'id'    => $trxdetail['bundleid'],
                            'name'  => $bundle['name'],
                            'qty'   => $trxdetail['qty'],
                            'price' => $bundle['price'],
                            'value' => $trxdetail['qty'] * $bundle['price'],
                        ]; 
                    }
                }
            }
        }
        
        // Sum Total Bundle Sold
        $paket = [];
        foreach ($bund as $bundval) {
    
            if (!isset($paket[$bundval['id'].$bundval['name']])) {
                $paket[$bundval['id'].$bundval['name']] = $bundval;
            } else {
                $paket[$bundval['id'].$bundval['name']]['value'] += $bundval['value'];
                $paket[$bundval['id'].$bundval['name']]['qty'] += $bundval['qty'];
            }
        }

        $paket = array_values($paket);

        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.bundlereport');
        $data['description']    = lang('Global.bundleListDesc');
        $data['bundles']        = $paket;

        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);

        return view('Views/report/bundle', $data);
    }

    public function category(){

        // Calling models
        $TransactionModel   = new TransactionModel();
        $TrxdetailModel     = new TrxdetailModel();
        $ProductModel       = new ProductModel();
        $CategoryModel      = new CategoryModel();
        $VariantModel       = new VariantModel();
        $StockModel         = new StockModel();
        $BrandModel         = new BrandModel(); 

        // Populating Data
        $trxdetails = $TrxdetailModel->findAll();
        $products   = $ProductModel->findAll();
        $category   = $CategoryModel->findAll();
        $variants   = $VariantModel->findAll();
        $brands     = $BrandModel->findAll();
        
        // Outlet Filter Data
        if ($this->data['outletPick'] === null) {
            $transactions = $TransactionModel->findAll();
        } else {
            $transactions = $TransactionModel->where('outletid',$this->data['outletPick'])->find();
        }

        $categoryvalue =[];
        foreach ($transactions as $transaction){
            foreach ($trxdetails as $trxdetail){
                if ($transaction['id'] === $trxdetail['transactionid']){
                    foreach ($variants as $variant){
                        if($trxdetail['variantid'] === $variant['id']){
                            foreach ($products as $product){
                                foreach ($brands as $brand){
                                    foreach ($category as $cat){
        
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $productval = [];
        foreach ($stocks as $stock){
            foreach ($variants as $variant){
                    foreach ($products as $product){
                        foreach ($brands as $brand){
                            foreach ($category as $cat){
                                if($product['catid'] === $cat['id'] && $product['brandid'] === $brand['id'] && $variant['productid'] == $product['id'] && $stock['variantid'] === $variant['id']){
                                $productval [] = [
                                    'id'                => $cat['id'],
                                    'catname'           => $cat['name'],
                                    'prodname'          => $product['name'],
                                    'desc'              => $product['description'],
                                    'stock'             => $stock['qty'],                                
                                    'hargamodal'        => $stock['qty'] * ($variant['hargajual'] + $variant['hargamodal']),                                
                                    'hargadasar'        => $stock['qty'] * ($variant['hargajual'] + $variant['hargadasar']),                                
                                ];
                            }
                        }
                    }
                }
            }
        }

        $produk = [];
        foreach ($productval as $vars) {
            if (!isset($produk[$vars['id'].$vars['catname']])) {
                $produk[$vars['id'].$vars['catname']] = $vars;
            } else {
                $produk[$vars['id'].$vars['catname']]['stock'] += $vars['stock'];
                $produk[$vars['id'].$vars['catname']]['hargamodal'] += $vars['hargamodal'];
                $produk[$vars['id'].$vars['catname']]['hargadasar'] += $vars['hargadasar'];
            }
        }
        $produk = array_values($produk);


        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.productreport');
        $data['description']    = lang('Global.productListDesc');
        $data['products']       = $produk;

        return view('Views/report/category', $data);
    }
}