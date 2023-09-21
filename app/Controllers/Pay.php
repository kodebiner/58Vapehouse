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
use App\Models\BookingModel;
use App\Models\BookingdetailModel;
use App\Models\TransactionModel;
use App\Models\TrxotherModel;
use App\Models\TrxdetailModel;
use App\models\TrxpaymentModel;
use App\models\DailyReportModel;

class Pay extends BaseController
{
    public function create()
    {
        // Calling Models
        $db                     = \Config\Database::connect();
        $BundleModel            = new BundleModel();
        $BundledetModel         = new BundledetailModel();
        $BookingModel           = new BookingModel();
        $BookingdetailModel     = new BookingdetailModel();
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
        $MemberModel            = new MemberModel();
        $TransactionModel       = new TransactionModel();
        
        // Getting Inputs
        $input = $this->request->getPost();
        // Image Capture
        if (!empty($input['image'])){

            $img            = $input['image'];
            $folderPath     = "img/tfproof";
            $image_parts    = explode(";base64,", $img);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type     = $image_type_aux[1];
            $image_base64   = base64_decode($image_parts[1]);
            $fileName       = uniqid() . '.png';
            $file           = $folderPath . $fileName;
            file_put_contents($file, $image_base64);
        }else{
            $fileName = "NULL";
        }
        
        // Populating Data
        $date           = date('Y-m-d H:i:s');
        $Gconfig        = $GconfigModel->first();
        $customers      = $MemberModel->findAll();
        
        // Inserting Transaction
        $varvalues = array();
        $bundvalues = array();
        
        if (!empty($input['qty'])) {
            foreach ($input['qty'] as $varid => $varqty) {
                $variant = $VariantModel->find($varid);

                $discvar = (int)$input['varprice'][$varid]  * $varqty;
                $discbargain = (int)$input['varbargain'][$varid]* $varqty;
                // Bargain And Varprice Added
                if (!empty($input['varprice'][$varid]) && !empty($input['varbargain'][$varid]) && $discbargain !== 0){
                    $varvalues[]  = $discbargain - $discvar;
                    // Vaprice Added And Null Bargain
                }elseif(isset($input['varprice'][$varid]) && !isset($input['varbargain'][$varid]) || $discbargain === 0){
                    $varvalues[]  = ($varqty * ($variant['hargamodal'] + $variant['hargajual'])) - $discvar;
                    // Bargain Added And Null Varprice
                }elseif((empty($input['varprice'][$varid])) && (isset($input['varbargain'][$varid])) && ($discbargain !== 0)){
                    $varvalues[]  = $discbargain;
                    // Null Bargain & Varprice
                }elseif(empty($input['varprice'][$varid]) && empty($input['varbargain'][$varid])){
                    $varvalues[] = $varqty * ($variant['hargamodal'] + $variant['hargajual']);
                }
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
        
        if (!empty($input['payment']) && empty($input['duedate'])) {
            $paymentid = $input['payment'];
        } else {
            $paymentid = '0';
        }
        
        if (!empty($input['value'])) {
            // Single Payment
            $trx = [
                'outletid'      => $this->data['outletPick'],
                'userid'        => $this->data['uid'],
                'memberid'      => $memberid,
                'paymentid'     => $paymentid,
                'value'         => $value,
                'disctype'      => $input['disctype'],
                'discvalue'     => $input['discvalue'],
                'date'          => $date,
                'pointused'     => $poin,
                'amountpaid'    => $input['value'],
                'photo'         => $fileName,
            ];
            $TransactionModel->insert($trx);
        } elseif (!empty($input['firstpay'])) {
            // Splitbill Payment
            $trx = [
                'outletid'      => $this->data['outletPick'],
                'userid'        => $this->data['uid'],
                'memberid'      => $memberid,
                'paymentid'     => $paymentid,
                'value'         => $value,
                'disctype'      => $input['disctype'],
                'discvalue'     => $input['discvalue'],
                'date'          => $date,
                'pointused'     => $poin,
                'amountpaid'    => (Int)$input['firstpay'] + (Int)$input['secondpay'],
                'photo'         => $fileName,
            ];
            $TransactionModel->insert($trx);
        } else {
            // Debt
            $trx = [
                'outletid'      => $this->data['outletPick'],
                'userid'        => $this->data['uid'],
                'memberid'      => $memberid,
                'paymentid'     => $paymentid,
                'value'         => $value,
                'disctype'      => $input['disctype'],
                'discvalue'     => $input['discvalue'],
                'date'          => $date,
                'pointused'     => $poin,
                'amountpaid'    => "0",
                'photo'         => $fileName,
            ];
            $TransactionModel->insert($trx);
        }
        $trxId = $TransactionModel->getInsertID();
        
        // Transaction Detail & Stock
        if (!empty($input['qty'])) {
            foreach ($input['qty'] as $varid => $varqty) {
                $variant = $VariantModel->find($varid);

                    $discvar = (int)$input['varprice'][$varid] * $varqty;
                    $discbargain = (int)$input['varbargain'][$varid]* $varqty;
                    
                    // Bargain And Varprice Added
                    if (!empty($input['varprice'][$varid]) && !empty($input['varbargain'][$varid]) && $discbargain !== 0){
                        $varPrice  = ($discbargain - $discvar)/$varqty;
                        // Vaprice Added And Null Bargain
                    }elseif(isset($input['varprice'][$varid]) && !isset($input['varbargain'][$varid]) || $discbargain === 0){
                        $varPrice  = (($varqty * ($variant['hargamodal'] + $variant['hargajual'])) - $discvar) / $varqty;
                        // Bargain Added And Null Varprice
                    }elseif((empty($input['varprice'][$varid])) && (isset($input['varbargain'][$varid])) && ($discbargain !== 0)){
                        $varPrice  = $discbargain / $varqty;
                        // Null Bargain & Varprice
                    }elseif(empty($input['varprice'][$varid]) && empty($input['varbargain'][$varid])){
                        $varPrice = ($varqty * ($variant['hargamodal'] + $variant['hargajual'])) / $varqty;
                    }else{
                        $varPrice = 0;
                    }

                    $marginmodal = $varPrice - $variant['hargamodal'];
                    $margindasar = $varPrice - $variant['hargadasar'];

                    
                $trxvar = [
                    'transactionid' => $trxId,
                    'variantid'     => $varid,
                    'qty'           => $varqty,
                    'value'         => $varPrice,
                    'discvar'       => $discvar,
                    'margindasar'   => $margindasar,
                    'marginmodal'   => $marginmodal,
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

        // Poin Minus
        if (!empty($input['poin'])){
            foreach ($customers as $customer){
                $cust       = $MemberModel->where('id',$input['customerid'])->first();

                if (!empty($input['poin'])){
                    $poin   = $cust['poin'] - $input['poin'];
                } else {
                    $poin   = $cust['poin'];
                }
                $point = [
                    'id'    => $cust['id'],
                    'poin'  => $poin,
                ];
                $MemberModel->save($point);

            }
        }
        
        // PPN Value
        $ppn = $value * ($Gconfig['ppn']/100);
        
        //Insert Trx Payment 
        $total = $subtotal - $discount - (int)$input['poin'] - $memberdisc + $ppn;
        
        // Debt Transaction
        if (!empty($input['duedate']) && !empty($input['payment']) && empty($input['value'])) {
            // Insert Debt
            $debt = [
                'memberid'      => $input['customerid'],
                'transactionid' => $trxId,
                'value'         => $input['debt'],
                'deadline'      => $input['duedate'],
            ];
            $DebtModel->save($debt);
        } elseif (!empty($input['duedate']) && !empty($input['payment']) && !empty($input['value'])) {
            // Debt & Down Payment

            // Insert Debt
            $debt = [
                'memberid'      => $input['customerid'],
                'transactionid' => $trxId,
                'value'         => $input['debt'],
                'deadline'      => $input['duedate'],
            ];
            $DebtModel->save($debt);
            
            // Insert Cash
            $payment    = $PaymentModel->where('id',$input['payment'])->first();
            $cashPlus   = $CashModel->where('id',$payment['cashid'])->first();

            $cash = [
                'id'    => $cashPlus['id'],
                'qty'   => $total + $cashPlus['qty'],
            ];
            $CashModel->save($cash);
        } elseif (!empty($input['duedate']) && !isset($input['payment']) && isset($input['firstpayment'])) {
            // Debt and Down Payment with Split Payment

            // Insert Debt
            $debt = [
                'memberid'      => $input['customerid'],
                'transactionid' => $trxId,
                'value'         => $input['debt'],
                'deadline'      => $input['duedate'],
            ];
            $DebtModel->save($debt);

            // Insert First Payment
            $payment    = $PaymentModel->where('id',$input['firstpayment'])->first();
            $cashPlus   = $CashModel->find($payment['cashid']);
            $cashUp     = $cashPlus['qty'] + $input['firstpay'];
            $cash       = [
                'id'    => $cashPlus['id'],
                'qty'   => $cashUp
            ];
            $CashModel->save($cash);

            // Insert Second Payment
            $payment     = $PaymentModel->where('id',$input['secpayment'])->first();
            $cashPlus2   = $CashModel->find($payment['cashid']);
            $cashUp2     = $cashPlus2['qty']+ $input['secondpay'];
            $cash2       = [
                'id'    => $cashPlus2['id'],
                'qty'   => $cashUp2,
            ];
            $CashModel->save($cash2);
        } else {
            // Normal Transaction

            // Insert Cash
            if (!empty($input['payment'])) {
                $payment    = $PaymentModel->where('id',$input['payment'])->first();
                $cashPlus   = $CashModel->where('id',$payment['cashid'])->first();
    
                $cash = [
                    'id'    => $cashPlus['id'],
                    'qty'   => $total + $cashPlus['qty'],
                ];
                $CashModel->save($cash);
    
            } elseif (!isset($input['payment']) && isset($input['firstpayment'])) {
                // Normal Transaction with Split Payment
                
                // Insert First Payment
                $payment    = $PaymentModel->where('id',$input['firstpayment'])->first();
                $cashPlus   = $CashModel->find($payment['cashid']);
                $cashUp     = $cashPlus['qty'] + $input['firstpay'];
                $cash       = [
                    'id'    => $cashPlus['id'],
                    'qty'   => $cashUp
                ];
                $CashModel->save($cash);

                // Insert Second Payment
                $payment    = $PaymentModel->where('id',$input['secpayment'])->first();
                $cashPlus2  = $CashModel->find($payment['cashid']);
                $cashUp2    = $cashPlus2['qty']+ $input['secondpay'];
                $cash2      = [
                    'id'    => $cashPlus2['id'],
                    'qty'   => $cashUp2,
                ];
                $CashModel->save($cash2);
            }
        }
        
        // Transaction Payment
        if (!isset($input['firstpayment']) && !isset($input['secpayment']) && isset($input['payment']) && !isset($input['duedate'])) {
            // Single Payment Method
            $payment = $PaymentModel->find($input['payment']);
            $paymethod = [
                'paymentid'     => $input['payment'],
                'transactionid' => $trxId,
                'value'         => $total,
            ];
            $TrxpaymentModel->save($paymethod);

        } elseif (isset($input['firstpayment']) && isset($input['secpayment']) && !isset($input['payment']) && !isset($input['duedate'])) {
            // Split Payment Method
            // First payment
            $firstpayment = $PaymentModel->find();
            $paymet = [
                'paymentid'     => $input['firstpayment'],
                'transactionid' => $trxId,
                'value'         => $input['firstpay'],
            ];
            $TrxpaymentModel->save($paymet);

            // Second Payment
            $secpayment = $PaymentModel->find();
            $pay = [
                'paymentid'     => $input['secpayment'],
                'transactionid' => $trxId,
                'value'         => $input['secondpay'],
            ];
            $TrxpaymentModel->save($pay);
        } elseif (!isset($input['firstpayment']) && !isset($input['secpayment']) && $input['value'] === "0" && isset($input['payment']) && isset($input['duedate'])) {
            // Debt Payment Method
            $paymentmethod = [
                'paymentid'     => "0",
                'transactionid' => $trxId,
                'value'         => $total,
            ];
            $TrxpaymentModel->save($paymentmethod);
        } elseif (!isset($input['firstpayment']) && !isset($input['secpayment']) && $input['value'] !== "0" && isset($input['payment']) && isset($input['duedate'])) {
            // Debt With Down Payment Method
            $paymentmethod = [
                'paymentid'     => "0",
                'transactionid' => $trxId,
                'value'         => $total - $input['value'],
            ];
            $TrxpaymentModel->save($paymentmethod);

            $paymentmethod = [
                'paymentid'     => $input['payment'],
                'transactionid' => $trxId,
                'value'         => $input['value'],
            ];
            $TrxpaymentModel->save($paymentmethod);
        } elseif (isset($input['firstpayment']) && isset($input['secpayment']) && !isset($input['payment']) && isset($input['duedate'])) {
            // Split Payment & Debt Method
            $paymentmethod = [
                'paymentid'     => "0",
                'transactionid' => $trxId,
                'value'         => ($total - ($input['firstpay'] + $input['secondpay'])),
            ];
            $TrxpaymentModel->save($paymentmethod);

            // First payment
            $firstpayment = $PaymentModel->find();
            $paymet = [
                'paymentid'     => $input['firstpayment'],
                'transactionid' => $trxId,
                'value'         => $input['firstpay'],
            ];
            $TrxpaymentModel->save($paymet);

            // Second Payment
            $secpayment = $PaymentModel->find();
            $pay = [
                'paymentid'     => $input['secpayment'],
                'transactionid' => $trxId,
                'value'         => $input['secondpay'],
            ];
            $TrxpaymentModel->save($pay);
        }

        // Gconfig poin setup
        $minimTrx    = $Gconfig['poinorder'];
        $poinval     = $Gconfig['poinvalue'];
        
        if ($total  >= $minimTrx) {
            $value  = $total / $minimTrx;
            $result = floor($value);
            $poin   = (int)$result * $poinval;
        } else {
            $poin = "0";
        }

        // Update Point Member
        if (!empty($input['customerid'])){
            $member      = $MemberModel->where('id',$input['customerid'])->first();
            $trx = $member['trx'] + 1 ;
            $memberPoint = $member['poin'];
            $poinPlus = (int)$memberPoint + $poin;           
            $poin = [
                'id'    => $member['id'],
                'poin'  => $poinPlus,
                'trx'   => $trx,
            ];
            $MemberModel->save($poin);
        }

        // Print Function
        $db                 = \Config\Database::connect();
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
        $transactions       = $TransactionModel->find($trxId);
        $trxdetails         = $TrxdetailModel->where('transactionid', $trxId)->find();
        $trxpayments        = $TrxpaymentModel->where('transactionid',$trxId)->find();
        $member             = $MemberModel->where('id',$transactions['memberid'])->first();
        $debt               = $DebtModel->where('memberid',$transactions['memberid'])->first();
        $user               = $UserModel->where('id',$transactions['userid'])->first();
        $trxdata            = $TransactionModel->where('id',$trxId)->first();
        
        $bundleBuilder      = $db->table('bundledetail');
        $bundleVariants     = $bundleBuilder->select('bundledetail.bundleid as bundleid, variant.id as id, variant.productid as productid, variant.name as name, stock.outletid as outletid, stock.qty as qty');
        $bundleVariants     = $bundleBuilder->join('variant', 'bundledetail.variantid = variant.id', 'left');
        $bundleVariants     = $bundleBuilder->join('stock', 'stock.variantid = variant.id', 'left');
        $bundleVariants     = $bundleBuilder->orderBy('stock.qty', 'ASC');
        $bundleVariants     = $bundleBuilder->get();

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
        $data['trxdetails']     = $TrxdetailModel->where('transactionid', $trxId)->find();
        $data['trxpayments']    = $trxpayments;
        $data['outid']          = $OutletModel->where('id',$this->data['outletPick'])->first();
        $data['bookings']       = $BookingModel->findAll();
        $data['bundleVariants'] = $bundleVariants->getResult();
        $actual_link            = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $data['link']          =  urlencode($actual_link);
        
        // Gconfig poin setup
        $minimTrx    = $Gconfig['poinorder'];
        $poinval     = $Gconfig['poinvalue'];
        
        if ($total  >= $minimTrx) {
            $value  = $total / $minimTrx;
            $result = floor($value);
            $poin   = (int)$result * $poinval;
        }
        
        if (!empty($input['customerid'])){
            $data['cust']           = $MemberModel->where('id',$transactions['memberid'])->first();
            $data['mempoin']        = $member['poin'];
            $data['poinused']       = $input['poin'];
            $data['poinearn']       = $poin;
        }else{
            $data['cust']           = "0";
            $data['mempoin']        = "0";
            $data['poinused']       = "0";
            $data['poinearn']       = "0";
        }

        if (!empty ($input['value'])){
            $data['change']     = $input['value'] - $total;
        }else{
            $data['change']     = "0";
        }

        if (!empty($input['varprice'])){
            $data['vardiscval']     = $input['varprice'];
        }else{
            $data['vardiscval']     = "0";
        }

        if (!empty($input['value'])){
            $data['pay']            = $input['value'];
        } elseif (!empty($input['firstpay']) && (!empty($input['secondpay']))) {
            $data['pay']            = $input['firstpay'] + $input['secondpay'];
        } elseif(!empty($input['duedate']) && empty($input['value'])){
            $data['pay']            = "0";
        }

        $data['discount'] = "0";

        if ((!empty($input['discvalue'])) && ($input['disctype'] === '0')) {
            $data['discount'] += $input['discvalue'];
        } elseif ((isset($input['discvalue'])) && ($input['disctype'] === '1')) {
            $data['discount'] += ($input['discvalue'] / 100) * $subtotal;
        } else {
            $data['discount'] += 0;
        }
    

        if(!empty($input['debt'])){
            $data['debt']       = $input['debt'];
            $data['totaldebt']  = $member['kasbon'];
        }else{
            $data['debt']       = "0";
            $data['totaldebt']  = "0";
        }
         
        $data['user']           = $user->username;
        $data['date']           = $transactions['date'];
        $data['transactionid']  = $trxId;
        $data['subtotal']       = $subtotal;
        $data['members']        = $MemberModel->findAll();
        $data['total']          = $total;

        return view('Views/print', $data);
    }

    public function save()
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
        $BookingModel           = new BookingModel();
        $BookingdetailModel     = new BookingdetailModel();
        $TransactionModel       = new TransactionModel();
        $TrxdetailModel         = new TrxdetailModel();
        $TrxpaymentModel        = new TrxpaymentModel();
        $MemberModel            = new MemberModel();

        // Getting Inputs
        $input = $this->request->getPost();

        // Populating Data
        $date           = date('Y-m-d H:i:s');
        $Gconfig        = $GconfigModel->first();
        $customers      = $MemberModel->findAll();
        
        // Inserting Transaction
        $varvalues = array();
        $bundvalues = array();
        
        // dd($input);
        if (!empty($input['qty'])) {
            foreach ($input['qty'] as $varid => $varqty) {
                $variant = $VariantModel->find($varid);

                $discvar = (int)$input['varprice'][$varid]  * $varqty;
                $discbargain = (int)$input['varbargain'][$varid]* $varqty;
                // Bargain And Varprice Added
                if (!empty($input['varprice'][$varid]) && !empty($input['varbargain'][$varid]) && $discbargain !== 0){
                    $varvalues[]  = $discbargain - $discvar;
                    // Vaprice Added And Null Bargain
                }elseif(isset($input['varprice'][$varid]) && !isset($input['varbargain'][$varid]) || $discbargain === 0){
                    $varvalues[]  = ($varqty * ($variant['hargamodal'] + $variant['hargajual'])) - $discvar;
                    // Bargain Added And Null Varprice
                }elseif(!isset($input['varprice'][$varid]) && isset($input['varbargain'][$varid])){
                    $varvalues[]  = $discbargain;
                    // Null Bargain & Varprice
                }elseif(empty($input['varprice'][$varid]) && empty($input['varbargain'][$varid])){
                    $varvalues[] = $varqty * ($variant['hargamodal'] + $variant['hargajual']);
                }
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
        foreach ($input['varprice'] as $variantprice){
            $varprice = $variantprice;
        }

        foreach($input['varbargain'] as $bargain){
            $varbargain = $bargain;
        }
        $book = [
            'outletid'      => $this->data['outletPick'],
            'userid'        => $this->data['uid'],
            'memberid'      => $memberid,
            'value'         => $value,
            'disctype'      => $input['disctype'],
            'discvalue'     => $input['discvalue'],
        ];
        $BookingModel->insert($book);
        $bookId = $BookingModel->getInsertID();
        
        // Booking Detail & Stock
        if (!empty($input['qty'])) {
            foreach ($input['qty'] as $varid => $varqty) {
                $variant = $VariantModel->find($varid);

                    $discvar = (int)$input['varprice'][$varid] * $varqty;
                    $discbargain = (int)$input['varbargain'][$varid]* $varqty;
                    // Bargain And Varprice Added
                    if (isset($input['varprice'][$varid]) && isset($input['varbargain'][$varid]) && $discbargain !== 0){
                        $varPrice  = ($discbargain - $discvar)/$varqty;
                        // Vaprice Added And Null Bargain
                    }elseif(isset($input['varprice'][$varid]) && !isset($input['varbargain'][$varid]) || $discbargain === 0){
                        $varPrice  = (($varqty * ($variant['hargamodal'] + $variant['hargajual'])) - $discvar) / $varqty;
                        // Bargain Added And Null Varprice
                    }elseif(!isset($input['varprice'][$varid]) && isset($input['varbargain'][$varid])){
                        $varPrice  = $discbargain / $varqty;
                        // Null Bargain & Varprice
                    }elseif(empty($input['varprice'][$varid]) && empty($input['varbargain'][$varid])){
                        $varPrice = ($varqty * ($variant['hargamodal'] + $variant['hargajual'])) / $varqty;
                    }
                $trxvar = [
                    'bookingid'     => $bookId,
                    'variantid'     => $varid,
                    'qty'           => $varqty,
                    'value'         => $varPrice,
                    'discvar'       => $discvar,
                ];
                $BookingdetailModel->save($trxvar);

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
                    'bookingid'     => $bookId,
                    'bundleid'      => $bunid,
                    'qty'           => $bunqty,
                    'value'         => $bundle['price'] * $bunqty
                ];
                $BookingdetailModel->save($trxbun);

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
        $db                 = \Config\Database::connect();
        $bundles            = $BundleModel->findAll();
        $bundets            = $BundledetModel->findAll();
        $booking            = $BookingModel->find($bookId);
        $bookingdetails     = $BookingdetailModel->where('bookingid',$bookId)->find();
        $Cash               = $CashModel->findAll();
        $outlets            = $OutletModel->findAll();
        $users              = $UserModel->findAll();
        $customers          = $MemberModel->findAll();
        $payments           = $PaymentModel->findAll();
        $products           = $ProductModel->findAll();
        $variants           = $VariantModel->findAll();
        $stocks             = $StockModel->findAll();
        $member             = $MemberModel->where('id',$booking['memberid'])->first();
        $debt               = $DebtModel->where('memberid',$booking['memberid'])->first();
        $user               = $UserModel->where('id',$booking['userid'])->first();
        $trxdata            = $TransactionModel->where('id',$bookId)->first();
        
        $bundleBuilder      = $db->table('bundledetail');
        $bundleVariants     = $bundleBuilder->select('bundledetail.bundleid as bundleid, variant.id as id, variant.productid as productid, variant.name as name, stock.outletid as outletid, stock.qty as qty');
        $bundleVariants     = $bundleBuilder->join('variant', 'bundledetail.variantid = variant.id', 'left');
        $bundleVariants     = $bundleBuilder->join('stock', 'stock.variantid = variant.id', 'left');
        $bundleVariants     = $bundleBuilder->orderBy('stock.qty', 'ASC');
        $bundleVariants     = $bundleBuilder->get();
        
        $data                   = $this->data;
        $data['title']          = lang('Global.transaction');
        $data['description']    = lang('Global.transactionListDesc');
        $data['bundles']        = $bundles;
        $data['bundets']        = $bundets;
        $data['cash']           = $Cash;
        $data['outlets']        = $outlets;
        $data['payments']       = $payments;
        $data['customers']      = $customers;
        $data['products']       = $products;
        $data['variants']       = $variants;
        $data['stocks']         = $stocks;
        $data['trxdetails']     = $TrxdetailModel->findAll();
        $data['outid']          = $OutletModel->where('id',$this->data['outletPick'])->first();
        $data['bookings']       = $booking;
        $data['bookingdetails'] = $bookingdetails;
        $data['bundleVariants'] = $bundleVariants->getResult();
        
        
        if (!empty($input['customerid'])){
            $data['cust']           = $MemberModel->where('id',$booking['memberid'])->first();
            $data['mempoin']        = $member['poin'];
            $data['poinused']       = $input['poin'];
        }else{
            $data['cust']           = "0";
            $data['mempoin']        = "0";
            $data['poinused']       = "0";
        }

        if (!empty ($input['value']) && $input['value'] <= "0"){
            $data['change']     = $input['value'] - $total;
        }else{
            $data['change']     = "0";
        }


        if (!empty($input['value'])){
            $data['pay']            = "UNPAID";
        } elseif (!empty($input['firstpay']) && (!empty($input['secondpay']))) {
            $data['pay']            = $input['firstpay'] + $input['secondpay'];
        }

        $data['discount'] = "0";

        if ((!empty($input['discvalue'])) && ($input['disctype'] === '0')) {
            $data['discount'] += $input['discvalue'];
        } elseif ((isset($input['discvalue'])) && ($input['disctype'] === '1')) {
            $data['discount'] += ($input['discvalue'] / 100) * $subtotal;
        } else {
            $data['discount'] += 0;
        }
    

        if(!empty($input['debt'])){
            $data['debt']       = $input['debt'];
            $data['totaldebt']  = $member['kasbon'];
        }else{
            $data['debt']       = "0";
            $data['totaldebt']  = "0";
        }

        if(!empty($value)){
            $data['total']          = $value;
        }else{
            $data['total']          = "0";
        }
         
        $data['user']           = $user->username;
        $data['date']           = $booking['created_at'];
        $data['bookingid']      = $booking['id'];
        $data['subtotal']       = $subtotal;
        $data['member']         = $MemberModel->where('id',$booking['memberid'])->first();
        
        return view('Views/print', $data);
    }

    public function copyprint($id){

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
        $BookingModel           = new BookingModel();
        $BookingdetailModel     = new BookingdetailModel();
        $TransactionModel       = new TransactionModel();
        $TrxdetailModel         = new TrxdetailModel();
        $TrxpaymentModel        = new TrxpaymentModel();
        $MemberModel            = new MemberModel();
        $GconfigModel           = new GconfigModel();

        $db                 = \Config\Database::connect();
        $transactions       = $TransactionModel->find($id);
        $trxdetails         = $TrxdetailModel->where('transactionid', $id)->find();
        $trxpayments        = $TrxpaymentModel->where('transactionid',$id)->find();
        $bundles            = $BundleModel->findAll();
        $bundets            = $BundledetModel->where('id',$id)->find();
        $Cash               = $CashModel->findAll();
        $outlets            = $OutletModel->findAll();
        $users              = $UserModel->findAll();
        $customers          = $MemberModel->findAll();
        $payments           = $PaymentModel->findAll();
        $products           = $ProductModel->findAll();
        $variants           = $VariantModel->findAll();
        $stocks             = $StockModel->findAll();
        $member             = $MemberModel->where('id',$transactions['memberid'])->first();
        $debt               = $DebtModel->where('transactionid',$id)->find();
        $user               = $UserModel->where('id',$transactions['userid'])->first();
        $Gconfig            = $GconfigModel->first();
        
        $bundleBuilder      = $db->table('bundledetail');
        $bundleVariants     = $bundleBuilder->select('bundledetail.bundleid as bundleid, variant.id as id, variant.productid as productid, variant.name as name, stock.outletid as outletid, stock.qty as qty');
        $bundleVariants     = $bundleBuilder->join('variant', 'bundledetail.variantid = variant.id', 'left');
        $bundleVariants     = $bundleBuilder->join('stock', 'stock.variantid = variant.id', 'left');
        $bundleVariants     = $bundleBuilder->orderBy('stock.qty', 'ASC');
        $bundleVariants     = $bundleBuilder->get();

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
        $data['trxdetails']     = $TrxdetailModel->where('transactionid',$id)->find();
        $data['trxpayments']    = $trxpayments;
        $data['outid']          = $OutletModel->where('id',$this->data['outletPick'])->first();
        $data['bookings']       = $BookingModel->findAll();
        $data['bundleVariants'] = $bundleVariants->getResult();

        $actual_link            = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $data['links']          =  urlencode($actual_link);


        $data['discount'] = "0";
        if ((!empty($transactions['discvalue'])) && ($transactions['disctype'] === '0')) {
            $data['discount'] += $transactions['discvalue'];
        } elseif ((isset($transactions['discvalue'])) && ($transactions['disctype'] === '1')) {
            $data['discount'] += ($transactions['discvalue'] / 100) * $subtotal;
        } else {
            $data['discount'] += 0;
        }

        $prices = array();
        foreach ($trxdetails as $trxdet) {
            if ($trxdet['transactionid'] === $id) {
                $total = $trxdet['qty'] * $trxdet['value'];
                $prices [] = $total;
            } 
        }
        $sum = array_sum($prices);

        $total = $sum - $data['discount'] - $transactions['pointused'] - $Gconfig['memberdisc'] + $Gconfig['ppn']; 

        // Gconfig poin setup
        $minimTrx    = $Gconfig['poinorder'];
        $poinval     = $Gconfig['poinvalue'];
        
        if ($total  >= $minimTrx) {
            $value  = $total / $minimTrx;
            $result = floor($value);
            $poin   = (int)$result * $poinval;
        }

        if (!empty($transactions['memberid'])){
            $data['cust']           = $MemberModel->where('id',$transactions['memberid'])->first();
            $data['mempoin']        = $member['poin'];
            $data['poinearn']       = $poin;
        }else{
            $data['cust']           = "0";
            $data['mempoin']        = "0";
            $data['poinearn']       = "0";  
        }
        
     
        if(!empty($transactions['pointused'])){
            $data['poinused']       = $transactions['pointused'];
        }else{
            $data['poinused']       = "0";
        }
        
        foreach ($trxdetails as $trxdetail){
            $trxdetval = $trxdetail['value'];
        }
        if (!empty ($transactions['amountpaid'])){
            $data['change']     = $transactions['amountpaid'] - $transactions['value'];
        }else{
            $data['change']     = "0";
        }
        
        if (!empty($trxdetails['discvar'])){
            $data['vardiscval']     = $trxdetails['discvar']['variantid'];
        }else{
            $data['vardiscval']     = "0";
        }

        if (!empty($transactions['amountpaid'])){
            $data['pay']            = $transactions['amountpaid'];
        } elseif (empty($transactions['amountpaid'])) {
            foreach ($trxdetails as $trxdetail){
                if($trxdetail['transactionid']== $id){
                    $data['pay']            = $trxdetail['value'];
                }
            }
        }else{
            $data['pay']            = '0';
        }

        if(!empty($debt['value'])){
            $data['debt']       = $debt['debt'];
            $data['totaldebt']  = $debt['value'];
        }else{
            $data['debt']       = "0";
            $data['totaldebt']  = "0";
        }
         
        $actual_link            = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $data['link']          =  urlencode($actual_link);

        $data['user']           = $user->username;
        $data['date']           = $transactions['date'];
        $data['transactionid']  = $id;
        $data['subtotal']       = $trxdetail['value'];
        $data['members']        = $MemberModel->findall();
        $data['total']          = $total;

        return view('Views/print', $data);
       
    }

    public function bookprint($id){

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
        $BookingModel           = new BookingModel();
        $BookingdetailModel     = new BookingdetailModel();
        $TransactionModel       = new TransactionModel();
        $TrxdetailModel         = new TrxdetailModel();
        $TrxpaymentModel        = new TrxpaymentModel();
        $MemberModel            = new MemberModel();
        
        $db                 = \Config\Database::connect();
        $bundles            = $BundleModel->findAll();
        $bundets            = $BundledetModel->findAll();
        $booking            = $BookingModel->find($id);
        $bookingdetails     = $BookingdetailModel->where('bookingid',$id)->find();
        $Cash               = $CashModel->findAll();
        $outlets            = $OutletModel->findAll();
        $users              = $UserModel->findAll();
        $customers          = $MemberModel->findAll();
        $payments           = $PaymentModel->findAll();
        $products           = $ProductModel->findAll();
        $variants           = $VariantModel->findAll();
        $stocks             = $StockModel->findAll();
        $member             = $MemberModel->where('id',$booking['memberid'])->first();
        $debt               = $DebtModel->where('memberid',$booking['memberid'])->first();
        $user               = $UserModel->where('id',$booking['userid'])->first();
        
        $bundleBuilder      = $db->table('bundledetail');
        $bundleVariants     = $bundleBuilder->select('bundledetail.bundleid as bundleid, variant.id as id, variant.productid as productid, variant.name as name, stock.outletid as outletid, stock.qty as qty');
        $bundleVariants     = $bundleBuilder->join('variant', 'bundledetail.variantid = variant.id', 'left');
        $bundleVariants     = $bundleBuilder->join('stock', 'stock.variantid = variant.id', 'left');
        $bundleVariants     = $bundleBuilder->orderBy('stock.qty', 'ASC');
        $bundleVariants     = $bundleBuilder->get();
        
        $data                   = $this->data;
        $data['title']          = lang('Global.transaction');
        $data['description']    = lang('Global.transactionListDesc');
        $data['bundles']        = $bundles;
        $data['bundets']        = $bundets;
        $data['cash']           = $Cash;
        $data['outlets']        = $outlets;
        $data['payments']       = $payments;
        $data['customers']      = $customers;
        $data['products']       = $products;
        $data['variants']       = $variants;
        $data['stocks']         = $stocks;
        $data['trxdetails']     = $TrxdetailModel->findAll();
        $data['outid']          = $OutletModel->where('id',$this->data['outletPick'])->first();
        $data['bookings']       = $booking;
        $data['bookingdetails'] = $bookingdetails;
        $data['bundleVariants'] = $bundleVariants->getResult();
        
        
        if (!empty($member)){
            $data['cust']           = $MemberModel->where('id',$booking['memberid'])->first();
            $data['mempoin']        = $member['poin'];
        }else{
            $data['cust']           = "0";
            $data['mempoin']        = "0";
            $data['poinused']       = "0";
        }

        if (!empty ($input['value']) && $input['value'] <= "0"){
            $data['change']     = $input['value'] - $total;
        }else{
            $data['change']     = "0";
        }

        if (!empty($booking['discvar']) && $booking['discvar'] !== "0"){
            $data['vardiscval']     = $bookingdetails['value']['variantid'];
        }else{
            $data['vardiscval']     = "0";
        }
 
        
        if ((!empty($booking['discvalue'])) && ($booking['disctype'] === '0')) {
            $data['discount'] = $booking['discvalue'];
            $data['memberdisc'] = $booking['discvalue'];
        } elseif ((!empty($booking['discvalue'])) && ($booking['disctype'] === '1')) {
            $data['discount'] = ($booking['discvalue']/100) * $bookingdetails['value'];
            $data['memberdisc'] = ($booking['discvalue'] / 100) * $bookingdetails['value'];
        } else {
            $data['discount'] = 0;
            $data['memberdisc'] = 0;
        }

        if(!empty($input['debt'])){
            $data['debt']       = $input['debt'];
            $data['totaldebt']  = $member['kasbon'];
        }else{
            $data['debt']       = "0";
            $data['totaldebt']  = "0";
        }

        if(!empty($bookingdetails)){
            $data['total']          = $booking['value'];
        }else{
            $data['total']          = "0";
        }

        $subtotal = 0;
        foreach ($bookingdetails as $bookingdetail) {
            $subtotal += $bookingdetail['value'];
        }

        $data['pay']            = "UNPAID";
        $data['user']           = $user->username;
        $data['date']           = $booking['created_at'];
        $data['bookingid']      = $booking['id'];
        $data['subtotal']       = $subtotal;
        $data['member']         = $MemberModel->where('id',$booking['memberid'])->first();
        
        return view('Views/print', $data);
    }

    public function bookingdelete($id)
    {
        // Calling Model
        $BookingModel       = new BookingModel();
        $BookingdetailModel = new BookingdetailModel();
        $StockModel         = new StockModel();
        $BundleModel        = new BundleModel();
        $BundledetailModel  = new BundledetailModel();

        // Populating & Removing Booking Detail Data
        $bookingdetails = $BookingdetailModel->where('bookingid', $id)->find();
        foreach ($bookingdetails as $bookdet) {
            // Restore Stock
            if ($bookdet['variantid'] != '0') {
                $stock = $StockModel->where('outletid', $this->data['outletPick'])->where('variantid', $bookdet['variantid'])->first();
                $stockdata = [
                    'id'    => $stock['id'],
                    'qty'   => $stock['qty'] + $bookdet['qty'],
                ];
                $StockModel->save($stockdata);
            } else {
                $bundles = $BundledetailModel->where('bundleid', $bookdet['bundleid'])->find();
                foreach ($bundles as $bundle) {
                    $stock = $StockModel->where('outletid', $this->data['outletPick'])->where('variantid', $bundle['variantid'])->first();
                    $stockdata = [
                        'id'    => $stock['id'],
                        'qty'   => $stock['qty'] + $bookdet['qty'],
                    ];
                    $StockModel->save($stockdata);
                }
            }

            // Removing Booking Detail
            $BookingdetailModel->delete($bookdet['id']);
        }

        // Removing Booking Data
        $BookingModel->delete($id);

        return redirect()->back()->with('error', lang('Global.deleted'));
    }

    public function topup()
    {
        // Declaration Model
        $MemberModel            = new MemberModel;
        $TrxotherModel          = new TrxotherModel;
        $CashModel              = new CashModel;
        $DailyReportModel       = new DailyReportModel;

        // Get Data
        $cashinout              = $TrxotherModel->findAll();
        $input                  = $this->request->getPost();
        $cash                   = $CashModel->like('name', 'Cash')->where('outletid', $this->data['outletPick'])->first();
        $date                   = date_create();
        $tanggal                = date_format($date,'Y-m-d H:i:s');
        $member                 = $MemberModel->where('id',$input['customerid'])->first();
        $poin                   = $member['poin'] + $input['value'];

        // Image Capture
        $img                    = $input['image'];
        $folderPath             = "img/tfproof";
        $image_parts            = explode(";base64,", $img);
        $image_type_aux         = explode("image/", $image_parts[0]);
        $image_type             = $image_type_aux[1];
        $image_base64           = base64_decode($image_parts[1]);
        $fileName               = uniqid() . '.png';
        $file                   = $folderPath . $fileName;
        file_put_contents($file, $image_base64);
        
        // Cash In 
        $cashin = [
            'userid'            => $this->data['uid'],
            'outletid'          => $this->data['outletPick'],
            'cashid'            => $cash['id'],
            'description'       => "Top Up - ".$member['name'] ,
            'type'              => "0",
            'date'              => $tanggal,
            'qty'               => $input['value'],
            'photo'             => $fileName,
        ];
        $TrxotherModel->save($cashin);
        
        // plus member poin
        $data=[
            'id'                => $input['customerid'],
            'poin'              => $poin,
        ];
        $MemberModel->save($data);
        
        $cas = $input['value'] + $cash['qty'];
        $wallet = [
            'id'                => $cash['id'],
            'qty'               => $cas,
        ];
        $CashModel->save($wallet);

        // Find Data for Daily Report
        $today                  = date('Y-m-d') .' 00:00:01';
        $dailyreports           = $DailyReportModel->where('outletid', $this->data['outletPick'])->where('dateopen >', $today)->find();
        foreach ($dailyreports as $dayrep) {
            $tcashin = [
                'id'            => $dayrep['id'],
                'totalcashin'   => $dayrep['totalcashin'] + $input['value'],
            ];
            $DailyReportModel->save($tcashin);
        }

        // return
        return redirect()->back()->with('message', lang('Global.saved'));
    }

    public function invoice($id){
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
        $BookingModel           = new BookingModel();
        $BookingdetailModel     = new BookingdetailModel();
        $TransactionModel       = new TransactionModel();
        $TrxdetailModel         = new TrxdetailModel();
        $TrxpaymentModel        = new TrxpaymentModel();
        $MemberModel            = new MemberModel();
        $GconfigModel           = new GconfigModel();

        $db                 = \Config\Database::connect();
        $transactions       = $TransactionModel->find($id);
        $trxdetails         = $TrxdetailModel->where('transactionid', $id)->find();
        $trxpayments        = $TrxpaymentModel->where('transactionid',$id)->find();
        $bundles            = $BundleModel->findAll();
        $bundets            = $BundledetModel->where('id',$id)->find();
        $Cash               = $CashModel->findAll();
        $outlets            = $OutletModel->findAll();
        $users              = $UserModel->findAll();
        $customers          = $MemberModel->findAll();
        $payments           = $PaymentModel->findAll();
        $products           = $ProductModel->findAll();
        $variants           = $VariantModel->findAll();
        $stocks             = $StockModel->findAll();
        $members            = $MemberModel->where('id',$transactions['memberid'])->first();
        $debt               = $DebtModel->where('transactionid',$id)->find();
        $user               = $UserModel->where('id',$transactions['userid'])->first();
        $Gconfig            = $GconfigModel->first();
        
        $bundleBuilder      = $db->table('bundledetail');
        $bundleVariants     = $bundleBuilder->select('bundledetail.bundleid as bundleid, variant.id as id, variant.productid as productid, variant.name as name, stock.outletid as outletid, stock.qty as qty');
        $bundleVariants     = $bundleBuilder->join('variant', 'bundledetail.variantid = variant.id', 'left');
        $bundleVariants     = $bundleBuilder->join('stock', 'stock.variantid = variant.id', 'left');
        $bundleVariants     = $bundleBuilder->orderBy('stock.qty', 'ASC');
        $bundleVariants     = $bundleBuilder->get();

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
        $data['trxdetails']     = $TrxdetailModel->where('transactionid',$id)->find();
        $data['trxpayments']    = $trxpayments;
        $data['outid']          = $OutletModel->where('id',$this->data['outletPick'])->first();
        $data['bookings']       = $BookingModel->findAll();
        $data['bundleVariants'] = $bundleVariants->getResult();
        $data['members']        = $MemberModel->findAll();

        $actual_link            = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $data['links']          = $actual_link;

        $data['discount'] = "0";
        if ((!empty($transactions['discvalue'])) && ($transactions['disctype'] === '0')) {
            $data['discount'] += $transactions['discvalue'];
        } elseif ((isset($transactions['discvalue'])) && ($transactions['disctype'] === '1')) {
            $data['discount'] += ($transactions['discvalue'] / 100) * $subtotal;
        } else {
            $data['discount'] += 0;
        }

        $prices = array();
        foreach ($trxdetails as $trxdet) {
            if ($trxdet['transactionid'] === $id) {
                $total = $trxdet['qty'] * $trxdet['value'];
                $prices [] = $total;
            } 
        }
        $sum = array_sum($prices);

        $total = $sum - $data['discount'] - $transactions['pointused'] - $Gconfig['memberdisc'] + $Gconfig['ppn']; 

        // Gconfig poin setup
        $minimTrx    = $Gconfig['poinorder'];
        $poinval     = $Gconfig['poinvalue'];
        
        if ($total  >= $minimTrx) {
            $value  = $total / $minimTrx;
            $result = floor($value);
            $poin   = (int)$result * $poinval;
        }

        if (!empty($transactions['memberid'])){
            $data['cust']           = $MemberModel->where('id',$transactions['memberid'])->first();
            $data['mempoin']        = $members['poin'];
            $data['poinearn']       = $poin;
        }else{
            $data['cust']           = "0";
            $data['mempoin']        = "0";
            $data['poinearn']       = "0";  
        }
        
    
        if(!empty($transactions['pointused'])){
            $data['poinused']       = $transactions['pointused'];
        }else{
            $data['poinused']       = "0";
        }
        
        foreach ($trxdetails as $trxdetail){
            $trxdetval = $trxdetail['value'];
        }
        if (!empty ($transactions['amountpaid'])){
            $data['change']     = $transactions['amountpaid'] - $transactions['value'];
        }else{
            $data['change']     = "0";
        }
        
        if (!empty($trxdetails['discvar'])){
            $data['vardiscval']     = $trxdetails['discvar']['variantid'];
        }else{
            $data['vardiscval']     = "0";
        }

        if (!empty($transactions['amountpaid'])){
            $data['pay']            = $transactions['amountpaid'];
        } elseif (empty($transactions['amountpaid'])) {
            foreach ($trxdetails as $trxdetail){
                if($trxdetail['transactionid']== $id){
                    $data['pay']            = $trxdetail['value'];
                }
            }
        }else{
            $data['pay']            = '0';
        }

        if(!empty($debt['value'])){
            $data['debt']       = $debt['debt'];
            $data['totaldebt']  = $debt['value'];
        }else{
            $data['debt']       = "0";
            $data['totaldebt']  = "0";
        }

        $data['user']           = $user->username;
        $data['date']           = $transactions['date'];
        $data['transactionid']  = $id;
        $data['subtotal']       = $trxdetail['value'];
        $data['member']         = $MemberModel->where('id',$transactions['memberid'])->first();
        $data['total']          = $total;

        return view('Views/invoice', $data);
    }
}
?>