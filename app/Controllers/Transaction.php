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
        $TrxdetailModel         = new TransactionModel();
        $TrxpaymentModel        = new TransactionModel();

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
        $TransactionModel       = new TransactionModel;
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
        $TrxdetailModel         = new TransactionModel();
        $TrxpaymentModel        = new TransactionModel();


      

        // Populating Data

        // initialize
        $input = $this->request->getPost();
        $auth = service('authentication');
        $userId = $this->userId = $auth->id();

        // date time stamp
        $date=date_create();
        $tanggal = date_format($date,'Y-m-d H:i:s');

        $outlet  = $OutletModel->where('id', $this->data['outletPick'])->first();
        
        // Insert Data
        $data = [
            
            'outletid'  => $outlet['id'],
            'userid'    => $userId,
            'memberid'  => $input['customerid'],
            'paymentid' => $input['payment'],
            'value'     => $input['value'],
            'disctype'  => $input['disctype'],
            'discvalue' => $input['discvalue'],
            'date'      => $tanggal,
            
        ];
        
        // save data transaction
        // $TransactionModel->save($data);
        
        // tranasaction id
        $trxId = $TransactionModel->getInsertID();

        // variants item
        $variant = $input["qty"];
        if ($variant < 1) {
            foreach ($variant as $x => $val){
                $varId = $x;
                $qty  = $val;
            }
        }else{
        // if variant more than one
            foreach ($variant as $varian){
                $variant = $input["qty"];
                foreach ($variant as $x => $val){
                    $varId = $x;
                    $qty  = $val;
                }
            }
        }

        dd($varId);

        // bundle item
        $bundles = $input['bqty'];
        if ($bundles !=0){
            foreach ($bundles as $y => $value){
                $bundId = $y;
                $qty    = $value;
            }
        }


        // Get Data
        $data = [
            'transactionid' => $trxId,
            'variantid'     => $varId,
            'bundleid'      => $input['qty'],
            'qty'           => $qty,
            // 'description'   => $input['description'],
            'value'         => $input['value'],
        ];
        dd($data);
        


    }
}