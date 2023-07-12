<?php

namespace App\Controllers;

use App\Models\BundledetailModel;
use App\Models\BundleModel;
use App\Models\CashModel;
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
        $stocks = $StockModel->findall();

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
            }

            //Insert Trx Payment 
            $total = $varPrice + $bunPrice;
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

            //Insert Poin Member

            //Insert Cashout For Change Money
          
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
            dd($totalValue);
            
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
            // $TransactionModel->save($data);
            
            // transaction id
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
                $fprice = $price * $qty;
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
            
            // save bundle item
            if (!empty($input['bqty'])){
                $bundles = $input['bqty'];
                foreach ($bundles as $y => $value){
                    $bundId = $y;
                    $qty    = $value;
                }
                $value = $BundleModel->where('id',$bundId)->first();
                $price = $value['price'];
                $fprice = $price * $qty;
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
                       
            // insert poin Member

        }
        
    }

    
}