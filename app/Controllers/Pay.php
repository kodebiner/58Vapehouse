<?php

namespace App\Controllers;

use App\Models\BundledetailModel;
use App\Models\BundleModel;
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

class Pay extends BaseController
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
        $DebtModel              = new DebtModel();
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

        // Getting Inputs
        $input = $this->request->getPost();

        // Populating Data
        $date        = date('Y-m-d H:i:s');
        $Gconfig     = $GconfigModel->first();
        
        // Inserting Transaction
        $varvalues = array();
        $bundvalues = array();
        
        if (!empty($input['qty'])) {
            foreach ($input['qty'] as $varid => $varqty) {
                $variant = $VariantModel->find($varid);
                $varvalues[] = $varqty * ($variant['hargamodal'] + $variant['hargajual']);
            }
        } else {
            $varvalues[] = '0';
        }
        
        if (!empty($input['bqty'])) {
            foreach ($input['bqty'] as $bunid => $bundqty) {
                $bundle = $BundleModel->find($bunid);
                $bundvalues[] = $bundqty * $bundle['price'];
            }
        } else {
            $bundvalues[] = '0';
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
            $memberdisc = 0;
        }
        
        if ((!empty($input['discvalue'])) && ($input['disctype'] === '0')) {
            $discount = $input['discvalue'];
        } elseif ((!empty($input['discvalue'])) && ($input['disctype'] === '1')) {
            $discount = ($input['discvalue']/100) * $subtotal;
        } else {
            $discount = 0;
        }

        if (!empty($input['poin'])) {
            $poin = $input['poin'];
        } else {
            $poin = 0;
        }
        
        $value = $subtotal - $memberdisc - $discount - $poin;

        if (!empty($input['value'])) {
            $paymentid = $input['payment'];
        } else {
            $paymentid = '0';
        }
        
        $trx = [
            'outletid'      => $this->data['outletPick'],
            'userid'        => $this->data['uid'],
            'memberid'      => $memberid,
            'paymentid'     => $paymentid,
            'value'         => $value,
            'disctype'      => $input['disctype'],
            'discvalue'     => $input['discvalue'],
            'date'          => $date
        ];
        $TransactionModel->insert($trx);
        $trxId = $TransactionModel->getInsertID();
        
        // Transaction Detail & Stock
        if (!empty($input['qty'])) {
            foreach ($input['qty'] as $varid => $varqty) {
                $variant = $VariantModel->find($varid);
                $trxvar = [
                    'transactionid' => $trxId,
                    'variantid'     => $varid,
                    'qty'           => $varqty,
                    'value'         => ($variant['hargamodal'] + $variant['hargajual']) * $varqty
                ];
                $TrxdetailModel->save($trxvar);

                $stock = $StockModel->where('outletid', $this->data['outletPick'])->where('variantid', $varid)->first();
                $saleVarStock = [
                    'id'        => $stock['id'],
                    'sale'      => $date,
                    'qty'       => $stock['qty'] - $varqty
                ];
                $StockModel->save($saleVarStock);
                
            }
        }
        
        if (!empty($input['bqty'])) {
            foreach ($input['bqty'] as $bunid => $bunqty) {
                $bundle = $BundleModel->find($bunid);
                $trxbun = [
                    'transactionid' => $trxId,
                    'bundleid'      => $bunid,
                    'qty'           => $bunqty,
                    'value'         => $bundle['price'] * $bunqty
                ];
                $TrxdetailModel->save($trxbun);

                $bundledetail = $BundledetModel->where('bundleid', $bunid)->find();
                foreach ($bundledetail as $BundleDetail) {
                    $bunstock = $StockModel->where('outletid', $this->data['outletPick'])->where('variantid', $BundleDetail['variantid'])->first();
                    $saleBunStock = [
                        'id'        => $bunstock['id'],
                        'sale'      => $date,
                        'qty'       => $bunstock['qty'] - $bunqty
                    ];
                    $StockModel->save($saleBunStock);
                    
                }
            }
        }
        
       
        // Member Point
        if ($input['customerid'] != '0') {
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
            $poin = [
                'id' => $member['id'],
                'poin' => $point,
            ];
            $MemberModel->save($poin);
        }
        
        $ppn = $value * ($Gconfig['ppn']/100);

        //Insert Trx Payment 
        $total = $subtotal - $discount - (int)$input['poin'] - $memberdisc + $ppn;
        $paymet = [
            'paymentid'     => $input['payment'],
            'transactionid' => $trxId,
            'value'         => $total,
        ];
        $TrxpaymentModel->save($paymet);
        
        // Insert Cash
        if (empty($input['duedate'])){
            $cashPlus   = $CashModel->where('id',$input['payment'])->first();
            // dd($cashPlus);
            $cashUp = $varvalue + $bundvalue + $cashPlus['qty'];
            $cash = [
                'id'    => $cashPlus['id'],
                'qty'   => $cashUp,
            ];
            $CashModel->save($cash);
        }

        // Gconfig setup
        $minimTrx    = $Gconfig['poinorder'];
        $poinval     = $Gconfig['memberdisc'];
        
        if ($total  >= $minimTrx){
            $value  = $total / $minimTrx;
            $result = floor($value);
            $poin   = (int)$result * $poinval;
            
        }else{
            $poin = "0";
        }

        //Update Point Member
        if (!empty($input['customerid'])){
            $trx = $member['trx'] + 1 ;
            $poinPlus = $memberPoint + $poin;               
            $poin = [
                'id'    => $member['id'],
                'poin'  => $poinPlus,
                'trx'   => $trx,
            ];
            $MemberModel->save($poin);
        }
        
        if(!empty($input['duedate'])) {
            $debt = [
                'memberid'      => $input['customerid'],
                'transactionid' => $trxId,
                'value'         => $input['value'],
                'deadline'      => $input['duedate'],
            ];
            $DebtModel->save($debt);
        } 
        return redirect()->back()->with('message', lang('Global.saved'));
    }
}

?>