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

class Transaction extends BaseController
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

        // Populating Data
        $bundles            = $BundleModel->findAll();
        $bundets            = $BundledetModel->findAll();
        $Cash               = $CashModel->findAll();
        $outlets            = $OutletModel->findAll();
        $users              = $UserModel->findAll();
        $customers          = $MemberModel->findAll();
        $payments           = $PaymentModel->findAll();
        $products           = $ProductModel->findAll();
        $variants           = $VariantModel->findAll();
        $stocks             = $StockModel->findAll();
        $transactions       = $TransactionModel->findAll();
        $trxdetails         = $TrxdetailModel->findAll();
        $trxpayments        = $TrxpaymentModel->findAll();

        $bundleBuilder      = $db->table('bundledetail');
        $bundleVariants     = $bundleBuilder->select('bundledetail.bundleid as bundleid, variant.id as id, variant.productid as productid, variant.name as name, stock.outletid as outletid, stock.qty as qty');
        $bundleVariants     = $bundleBuilder->join('variant', 'bundledetail.variantid = variant.id', 'left');
        $bundleVariants     = $bundleBuilder->join('stock', 'stock.variantid = variant.id', 'left');
        $bundleVariants     = $bundleBuilder->orderBy('stock.qty', 'ASC');
        $bundleVariants     = $bundleBuilder->get();


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
        $data['customers']      = $customers;
        $data['products']       = $products;
        $data['variants']       = $variants;
        $data['stocks']         = $stocks;
        $data['trxdetails']     = $trxdetails;
        $data['trxpayments']    = $trxpayments;
        $data['bundleVariants'] = $bundleVariants->getResult();

        return view('Views/transaction', $data);
    }

    public function create() 
    {
        // Calling Models
        $BundleModel            = new BundleModel();
        $BundledetModel         = new BundledetailModel();
        $CashModel              = new CashModel();
        $GconfigModel           = new GconfigModel();
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

        // Populating Data
        $stocks      = $StockModel->findall();
        $Gconfig     = $GconfigModel->first();

        // initialize
        $input = $this->request->getPost();

        // date time stamp
        $date=date_create();
        $tanggal = date_format($date,'Y-m-d H:i:s');

        
        if (!empty($input['payment'])){
            // This Single Payment Control
            // validation form
            // Insert Data
            $data = [
                'outletid'  => $this->data['outletPick'],
                'userid'    => $this->data['uid'],
                'memberid'  => $input['customerid'],
                'paymentid' => $input['payment'],
                'value'     => $input['value'],
                'disctype'  => $input['disctype'],
                'discvalue' => $input['discvalue'],
                'date'      => $tanggal,  
            ];
            // save data transaction
            $TransactionModel->save($data);
            
            // tranasaction id
            $trxId = $TransactionModel->getInsertID();
            
            // save variants item
            if (!empty($input["qty"])) {
                $variant = $input["qty"];
                foreach ($variant as $vId => $val){
                    $varId = $vId;
                    $qty  = $val;
                  
                }
                $value = $VariantModel->where('id',$vId)->first();
                $price = $value['hargamodal']+$value['hargajual'];
                $varPrice = $price * $qty;
                $data = [
                    'transactionid' => $trxId,
                    'variantid'     => $varId,
                    'bundleid'      => "0",
                    'qty'           => $qty,
                    // 'description'   => $input['description'],
                    'value'         => $varPrice,
                ];
                $TrxdetailModel->save($data);
                
                // Minus Stock
                $stok= $StockModel->where('variantid', $varId)->where('outletid',$this->data['outletPick'])->first();
                $newStock = $stok['qty'] - $qty;
                $data = [
                    'id' => $stok['id'],
                    'qty' => $newStock,
                ];
                $StockModel->save($data);
            }else {
                $varPrice = "0";
            }
            
            // save bundle item
            if (!empty($input['bqty'])){
                $bundles = $input['bqty'];
                foreach ($bundles as $y => $value){
                    $bundId = $y;
                    $qty    = $value;
                }
                $value = $BundleModel->where('id',$bundId)->first();
                $price = $value['price'];
                $bunPrice = $price * $qty;
                $data = [
                    'transactionid' => $trxId,
                    'variantid'     => "0",
                    'bundleid'      => $y,
                    'qty'           => $qty,
                    // 'description'   => $input['description'],
                    'value'         => $bunPrice,
                ];
                $TrxdetailModel->save($data);

                // minus stock
                $bundet = $BundledetModel->where('bundleid',$y)->find();
                foreach ($bundet as $bun => $val){
                    $bunid = $val['bundleid'];
                    $varid = $val['variantid'];
                    foreach ($stocks as $stock){
                        $stock = $StockModel->where('variantid', $varid)->where('outletid',$this->data['outletPick'])->first();
                        $newStock = $stock['qty']-$qty;
                        $stok = [
                            'id' => $stock['id'],
                            'qty' => $newStock,
                        ];
                        $StockModel->save($stok);
                    }
                }
            } else{
                $bunPrice = "0";
            }

            // initialize
            $variant = $input["qty"];
            foreach ($variant as $vId => $val){
                $varId = $vId;
                $qty  = $val;
            }
            $value = $VariantModel->where('id',$vId)->first();
            $price = $value['hargamodal']+$value['hargajual'];
            $varPrice = $price * $qty;

            //Discount Price
            if (!empty($input['discvalue'])){
                if ($input['disctype'] === "0"){
                    // $sumPrice = $varPrice + $bunPrice;
                    $discPrice = $input['discvalue'];
                    // $discPrice = $sumPrice - $discount;
                } else{
                    //Discount Percent 
                    $sumPrice   = $varPrice + $bunPrice;
                    $discPrice   = ($sumPrice * $input['discvalue'])/100;
                    // $discPrice  = $sumPrice - $discount;
                }
            } else{
                $discPrice = "0";
            }
            
            //Discount Point Member
            if (!empty($input['customerid'])){
                $discPoint   = $input['poin'];
                $member      = $MemberModel->where('id',$input['customerid'])->first();
                $memberPoint = $member['poin'];
                // Used Poin 
                if (!empty($input['poin'])){
                    $point       = $memberPoint - $discPoint;
                } else{
                    // Not Apply Point
                    $point  = $memberPoint;
                }
            } 
   
            //Minus Member Point
            $data = [
                'id' => $member['id'],
                'poin' => $point,
            ];
            $MemberModel->save($data);

            // $member Disc
            $memberdisc = $Gconfig['memberdisc'];
            // subtotal
            $subtotal = $varPrice + $bunPrice; 
            // ppn
            $ppn = ($subtotal*$Gconfig['ppn'])/100;

            
            //Insert Trx Payment 
            $total = $subtotal - $discPrice - $discPoint + $ppn - $memberdisc;
            $data = [
                'paymentid'     => $input['payment'],
                'transactionid' => $trxId,
                'value'         => $total
            ];
            $TrxpaymentModel->save($data);
            
            // Insert Cash
            $cashPlus   = $CashModel->where('id',$input['payment'])->first();
            $cashUpdate = $varPrice + $bunPrice + $cashPlus['qty'];
            $data = [
                'id'    => $cashPlus['id'],
                'qty'   => $cashUpdate,
            ];
            $CashModel->save($data);
            
            // Gconfig setup
          
            $minimTrx    = $Gconfig['poinorder'];
            $poinval     = $Gconfig['memberdisc'];
            
            if ( $total >= $minimTrx ){
                $value  = $total / $minimTrx;
                $result = floor($value);
                $poin   = $result * $poinval;
            }else{
                $poin = "0";
            }
            
            //Update Point Member
            $poinPlus = $memberPoint + $poin;
            $data = [
                'id'    => $member['id'],
                'poin'  => $poinPlus,
            ];
            $MemberModel->save($data);
            //ppn

        } else{
            // This Split Bill Control

            // Variants Value
            if (!empty($input["qty"])) {
                $variant = $input["qty"];
                foreach ($variant as $vId => $val){
                    $varId = $vId;
                    $qty  = $val;
                }
                $value = $VariantModel->where('id',$vId)->first();
                $price = $value['hargamodal']+$value['hargajual'];
                $varPrice = $price * $qty;
            }else{
                $varPrice = "0";
            }

            // Bundle Value
            if (!empty($input['bqty'])){
                $bundles = $input['bqty'];
                foreach ($bundles as $y => $value){
                    $bundId = $y;
                    $qty    = $value;
                }
                $value = $BundleModel->where('id',$bundId)->first();
                $price = $value['price'];
                $bunPrice = $price * $qty;
            }else{
                $bunPrice = "0";
            }
            
            $totalValue = $varPrice + $bunPrice;
            
            // Insert Data
            $data = [
                'outletid'  => $this->data['outletPick'],
                'userid'    => $this->data['uid'],
                'memberid'  => $input['customerid'],
                'paymentid' => "0",
                'value'     => $totalValue,
                'disctype'  => $input['disctype'],
                'discvalue' => $input['discvalue'],
                'date'      => $tanggal,  
            ];
            // save data transaction
            $TransactionModel->save($data);
            
            // transaction id
            $trxId = $TransactionModel->getInsertID();
            
            // variants item
            if (!empty($input["qty"])) {
                $variant = $input["qty"];
                foreach ($variant as $vId => $val){
                    $varId = $vId;
                    $qty  = $val;
                }
                $value = $VariantModel->where('id',$vId)->first();
                $price = $value['hargamodal']+$value['hargajual'];
                $fprice = $price * $qty;
                // save transaction detail
                $data = [
                    'transactionid' => $trxId,
                    'variantid'     => $varId,
                    'bundleid'      => "0",
                    'qty'           => $qty,
                    // 'description'   => $input['description'],
                    'value'         => $fprice,
                ];
                $TrxdetailModel->save($data);
                
                // Minus Stock
                $stok= $StockModel->where('variantid', $varId)->where('outletid',$this->data['outletPick'])->first();
                $newStock = $stok['qty'] - $qty;
                $data = [
                    'id' => $stok['id'],
                    'qty' => $newStock,
                ];
                $StockModel->save($data);
            }
            
            // bundle item
            if (!empty($input['bqty'])){
                $bundles = $input['bqty'];
                foreach ($bundles as $y => $value){
                    $bundId = $y;
                    $qty    = $value;
                }
                $value = $BundleModel->where('id',$bundId)->first();
                $price = $value['price'];
                $fprice = $price * $qty;
                // save transaction detail
                $data = [
                    'transactionid' => $trxId,
                    'variantid'     => "0",
                    'bundleid'      => $y,
                    'qty'           => $qty,
                    // 'description'   => $input['description'],
                    'value'         => $fprice,
                ];
                $TrxdetailModel->save($data);
                
                // minus stock
                $bundet = $BundledetModel->where('bundleid',$y)->find();
                foreach ($bundet as $bun => $val){
                    $bunid = $val['bundleid'];
                    $varid = $val['variantid'];
                    foreach ($stocks as $stock){
                        $stock = $StockModel->where('variantid', $varid)->where('outletid',$this->data['outletPick'])->first();
                            $newStock = $stock['qty']-$qty;
                            $stok = [
                                'id' => $stock['id'],
                                'qty' => $newStock,
                            ];
                            $StockModel->save($stok);
                    }
                }
            }

            //Discount Price
            if (!empty($input['discvalue'])){
                if ($input['disctype'] === "0"){
                    // $sumPrice = $varPrice + $bunPrice;
                    $discPrice = $input['discvalue'];
                    // $discPrice = $sumPrice - $discount;
                } else{
                    //Discount Percent 
                    $sumPrice   = $varPrice + $bunPrice;
                    $discPrice   = ($sumPrice * $input['discvalue'])/100;
                    // $discPrice  = $sumPrice - $discount;
                }
            } else{
                $discPrice = "0";
            }

            //Discount Point Member
            if (!empty($input['customerid'])){
                $discPoint   = $input['poin'];
                $member      = $MemberModel->where('id',$input['customerid'])->first();
                $memberPoint = $member['poin'];
                // Used Poin 
                if (!empty($input['poin'])){
                    $point       = $memberPoint - $discPoint;
                } else{
                // Not Apply Point
                    $point  = $memberPoint;
                }
            } 
   
            //Minus Member Point
            $data = [
                'id' => $member['id'],
                'poin' => $point,
            ];
            $MemberModel->save($data);

            //Insert First Trx Payment 
            $data = [
                'paymentid'     => $input['firstpayment'],
                'transactionid' => $trxId,
                'value'         => $input['firstpay']
            ];
            $TrxpaymentModel->save($data);

            //Insert Second Trx Payment 
            $data = [
                'paymentid'     => $input['secondpayment'],
                'transactionid' => $trxId,
                'value'         => $input['secpay']
            ];
            $TrxpaymentModel->save($data);

            // Insert First Cash 
            $cashPlus   = $CashModel->where('id',$input['firstpayment'])->first();
            $cashUpdate = $input['firstpay'] + $cashPlus['qty'];
            $data = [
                'id'    => $cashPlus['id'],
                'qty'   => $cashUpdate,
            ];
            $CashModel->save($data);

            // Insert Second Cash 
            $cashPlus   = $CashModel->where('id',$input['secondpayment'])->first();
            $cashUpdate = $input['secpay'] + $cashPlus['qty'];
            $data = [
                'id'    => $cashPlus['id'],
                'qty'   => $cashUpdate,
            ];
            $CashModel->save($data);
        }

        return redirect()->back()->with('massage', lang('global.saved'));
        
    }

    public function pay() {
        // Calling Models
        $TransactionModel   = new TransactionModel();
        $TrxdetailModel     = new TrxdetailModel();
        $PaymentModel       = new PaymentModel();
        $TrxpaymentModel    = new TrxpaymentModel();
        $VariantModel       = new VariantModel();
        $BundleModel        = new BundleModel();

        // Getting Inputs
        $input = $this->request->getPost();

        // Populating Data

        // Conditions

        // Inserting Transaction

        $varvalues = array();
        $bundvalues = array();

        foreach ($input['qty'] as $varid => $varqty) {
            $variant = $VariantModel->find($varid);
            $varvalues[] = $varqty * ($variant['hargamodal'] + $variant['hargajual']);
        }

        foreach ($input['bqty'] as $bunid => $bundqty) {
            $bundle = $BundleModel->find($bunid);
            $bundvalues[] = $bundqty * $bundle['price'];
        }

        $varvalue = array_sum($varvalues);
        $bundvalue = array_sum($bundvalues);

        $subtotal = $varvalue + $bundvalue;

        if ($input['customerid'] != '0') {
            $memberid = $input['customerid'];
            if ($this->data['gconfig']['memberdisctype'] === '0') {
                $memberdisc = $this->data['gconfig']['memberdisc'];
            } elseif ($this->data['gconfig']['memberdisctype'] === '1') {
                $memberdisc = ($this->data['gconfig']['memberdisc']/100) * $subtotal;
            }
        } else {
            $memberid = '';
            $memberdisc = '0';
        }

        if ((!empty($input['discvalue'])) && ($input['disctype'] === '0')) {
            $discount = $input['discvalue'];
        } elseif ((!empty($input['discvalue'])) && ($input['disctype'] === '1')) {
            $discount = ($input['discvalue']/100) * $subtotal;
        } else {
            $discount = '0';
        }

        $value = $subtotal - $memberdisc - $discount;
        // Lanjutkan disini

        if (!empty($input['value'])) {
            $paymentid = $input['payment'];
        } else {
            $paymentid = '0';
        }

        $trx = [
            'outletid'      => $this->data['outletpick'],
            'userid'        => $this->data['uid'],
            'memberid'      => $memberid,
            'paymentid'     => $paymentid,
            'value'         => $value,
            'disctype'      => $input['disctype'],
            'discvalue'     => $input['discvalue'],
            'date'          => date('Y-m-d H:i:s')
        ];
        $TransactionModel->insert($trx);
        $trxId = $TransactionModel->getInsertID();
    }

    
}