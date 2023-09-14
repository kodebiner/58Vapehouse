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
use App\models\TrxpaymentModel;
use App\Models\BookingModel;
use App\Models\BookingdetailModel;
use App\Models\PurchaseModel;
use App\Models\PurchasedetailModel;

class Report extends BaseController
{
    public function index()
    {
        $db      = \Config\Database::connect();

        // Calling Models
        $BundleModel            = new BundleModel();
        $BundledetModel         = new BundledetailModel();
        $CashModel              = new CashModel();
        $OutletModel            = new OutletModel();
        $UserModel              = new UserModel();
        $MemberModel            = new MemberModel();
        $PaymentModel           = new PaymentModel();
        $ProductModel           = new ProductModel();
        $VariantModel           = new VariantModel();
        $StockModel             = new StockModel();
        $TransactionModel       = new TransactionModel();
        $TrxdetailModel         = new TrxdetailModel();
        $TrxpaymentModel        = new TrxpaymentModel();
        $BookingModel           = new BookingModel();
        $BookingdetailModel     = new BookingdetailModel();

        // Populating Data
        $bundles                = $BundleModel->findAll();
        $bundets                = $BundledetModel->findAll();
        $Cash                   = $CashModel->findAll();
        $outlets                = $OutletModel->findAll();
        $users                  = $UserModel->findAll();
        $customers              = $MemberModel->findAll();
        $payments               = $PaymentModel->findAll();
        $products               = $ProductModel->findAll();
        $variants               = $VariantModel->findAll();
        $stocks                 = $StockModel->findAll();
        $transactions           = $TransactionModel->findAll();
        $trxdetails             = $TrxdetailModel->findAll();
        $trxpayments            = $TrxpaymentModel->findAll();
        $bookings               = $BookingModel->where('status', '0')->orderBy('created_at', 'DESC')->findAll();
        $bookingdetails         = $BookingdetailModel->findAll();

        $bundleBuilder          = $db->table('bundledetail');
        $bundleVariants         = $bundleBuilder->select('bundledetail.bundleid as bundleid, variant.id as id, variant.productid as productid, variant.name as name, stock.outletid as outletid, stock.qty as qty');
        $bundleVariants         = $bundleBuilder->join('variant', 'bundledetail.variantid = variant.id', 'left');
        $bundleVariants         = $bundleBuilder->join('stock', 'stock.variantid = variant.id', 'left');
        $bundleVariants         = $bundleBuilder->orderBy('stock.qty', 'ASC');
        $bundleVariants         = $bundleBuilder->get();


        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.transaction');
        $data['description']    = lang('Global.transactionListDesc');
        $data['bundles']        = $bundles;
        $data['bundets']        = $bundets;
        $data['cash']           = $Cash;
        $data['transactions']   = $transactions;
        $data['outlets']        = $outlets;
        $data['payments']       = $payments;
        $data['members']        = $MemberModel->findAll();
        $data['customers']      = $customers;
        $data['products']       = $products;
        $data['variants']       = $variants;
        $data['stocks']         = $stocks;
        $data['trxdetails']     = $trxdetails;
        $data['trxpayments']    = $trxpayments;
        $data['bundleVariants'] = $bundleVariants->getResult();
        $data['bookings']       = $bookings;
        $data['bookingdetails'] = $bookingdetails;

        return view('Views/report', $data);
    }

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

        $input = $this->request->getGet();
        $trxdetails             = $TrxdetailModel->findAll();

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
                $marginmodalsum = array_sum($marginmodals);
                $margindasarsum = array_sum($margindasars);

                $transactions[] = [
                    'date'      => date('d/m/y', $date),
                    'value'     => $summary,
                    'modal'     => $marginmodalsum,
                    'dasar'     => $margindasarsum,
                ];  
            }    
            
        }

        $keuntunganmodal = array_sum(array_column($transactions, 'modal'));
        $keuntungandasar = array_sum(array_column($transactions, 'dasar'));
        $trxvalue        = array_sum(array_column($transactions, 'value'));

        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.transaction');
        $data['description']    = lang('Global.transactionListDesc');
        $data['transactions']   = $transactions;
        $data['modals']         = $keuntunganmodal;
        $data['dasars']         = $keuntungandasar;
        $data['totaldasar']     = $trxvalue;

        return view('Views/report/keuntungan', $data);
    }

    public function keuntungandasar(){
        
        // Calling Models
        $TransactionModel       = new TransactionModel;
        $TrxdetailModel         = new TrxdetailModel;

        $input = $this->request->getGet();
        $trxdetails             = $TrxdetailModel->findAll();

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
                $margindasars = array();
                foreach ($trxdetails as $trxdetail){
                    if($trx['id'] === $trxdetail['transactionid']){
                        $margindasar = $trxdetail['margindasar'];
                        $margindasars[] = $margindasar;
                    }
                }
                $margindasarsum = array_sum($margindasars);

                $transactions[] = [
                    'date'      => date('d/m/y', $date),
                    'dasar'     => $margindasarsum,
                ];  
            }    
            
        }
        

        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.transaction');
        $data['description']    = lang('Global.transactionListDesc');
        $data['transactions']   = $transactions;

        return view('Views/report/keuntungandasar', $data);
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
                $builder->selectCount('id','total_payment','payval');
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
                
                $paymethod= array();
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
}