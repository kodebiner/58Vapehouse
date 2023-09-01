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
class Pay extends BaseController
{
    public function create()
    {
        // Calling Models
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
                    
                $trxvar = [
                    'transactionid' => $trxId,
                    'variantid'     => $varid,
                    'qty'           => $varqty,
                    'value'         => $varPrice,
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

        if (!empty($input['poin'])){
            foreach ($customers as $customer){
                $cust       = $MemberModel->where('id',$input['customerid'])->first();
                $custPoin   = $cust['poin'];
                $poinUsed   = $input['poin'];

                if (!empty($poinUsed)){
                    $poin   = $custPoin - $poinUsed;
                } else {
                    $poin   = $custPoin;
                }
                $point = [
                    'id' => $cust['id'],
                    'poin' => $poin,
                ];
                $MemberModel->save($point);
            }
        }
        
        // PPN Value
        $ppn = $value * ($Gconfig['ppn']/100);

        
        //Insert Trx Payment 
        $total = $subtotal - $discount - (int)$input['poin'] - $memberdisc + $ppn;
        if(!isset($input['firstpayment'])&& !isset($input['secpayment']) && isset($input['payment'])){
            $paymet = [
                'paymentid'     => $input['payment'],
                'transactionid' => $trxId,
                'value'         => $total,
            ];
            $TrxpaymentModel->save($paymet);

        }elseif(isset($input['firstpayment']) && isset($input['secpayment']) && !isset($input['payment'])){
            // Split Payment Method
            // First payment
            $paymet = [
                'paymentid'     => $input['firstpayment'],
                'transactionid' => $trxId,
                'value'         => $input['firstpay'],
            ];
            $TrxpaymentModel->save($paymet);

            // Second Payment
            $pay = [
                'paymentid' => $input['secpayment'],
                'transactionid' =>$trxId,
                'value'         =>$input['secondpay'],
            ];
            $TrxpaymentModel->save($pay);
        }
        
        // Insert Cash
        if (!isset($input['duedate']) && $input['payment'] !== 0){
            $cashPlus   = $CashModel->where('id',$input['payment'])->first();
            $cashUp = $varvalue + $bundvalue + $cashPlus['qty'];
            $cash = [
                'id'    => $cashPlus['id'],
                'qty'   => $cashUp,
            ];
            $CashModel->save($cash);
        }elseif(!isset($input['payment'])){
            // Insert First Payment
            $cashPlus   = $CashModel->where('id', $input['firstpayment'])->first();
            $cashUp     = $cashPlus['qty'] + $input['firstpay'];
            $cash       = [
                'id'    => $cashPlus['id'],
                'qty'   => $cashUp
            ];
            $CashModel->save($cash);
            // Insert Second Payment
            $cashPlus2  = $CashModel->where('id',$input['secpayment'])->first();
            $cashUp2     = $cashPlus2['qty']+ $cashPlus2['qty'];
            $cash2       = [
                'id'    => $cashPlus2['id'],
                'qty'   => $cashUp2,
            ];
            $CashModel->save($cash2);
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
            $member      = $MemberModel->where('id',$input['customerid'])->first();
            $trx = $member['trx'] + 1 ;
            $memberPoint = $member['poin'];
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
                'value'         => $input['debt'],
                'deadline'      => $input['duedate'],
            ];
            $DebtModel->save($debt);
        }

        $db      = \Config\Database::connect();

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
        $trxpayments        = $TrxpaymentModel->findAll();
        $member             = $MemberModel->where('id',$transactions['memberid'])->first();

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
        $data['trxdetails']     = $TrxdetailModel->findAll();
        $data['trxpayments']    = $trxpayments;
        $data['outid']          = $OutletModel->where('id',$input['outlet'])->first();
        $data['bookings']       = $BookingModel->findAll();
        $data['bundleVariants'] = $bundleVariants->getResult();
        
        
        if (!empty($input['customerid'])){
            $data['cust']           = $MemberModel->where('id',$transactions['memberid'])->first();
            $data['mempoin']        = $member['poin'];
            $data['poinused']       = $input['poin'];
        }else{
            $data['cust']           = "0";
            $data['mempoin']        = "0";
            $data['poinused']       = "0";
        }

        if (!empty ($input['cashamount'])){
            $data['change']     = $input['cashamount'] - $input['value'];
        }else{
            $data['change']     = "0";
        }

         
        $data['user']           = $user->username;
        $data['date']           = $transactions['date'];
        $data['transactionid']  = $trxId;
        $data['vardiscval']     = $input['varprice'];
        $data['subtotal']       = $subtotal;
        $data['pay']            = $input['cashamount'];
        $data['member']         = $MemberModel->where('id',$transactions['memberid'])->first();
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
                    if (!empty($input['varprice'][$varid]) && !empty($input['varbargain'][$varid]) && $discbargain !== 0){
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
        return redirect()->back()->with('message', lang('Global.saved'));
    }

    public function bookingdelete($id)
    {
        // Calling Model
        $BookingModel       = new BookingModel();
        $BookingdetailModel = new BookingdetailModel();

        // Populating & Removing Booking Detail Data
        $bookingdetails = $BookingdetailModel->where('bookingid', $id)->find();
        foreach ($bookingdetails as $bookdet) {
            // Removing Variant
            $BookingdetailModel->delete($bookdet['id']);
        }

        // Removing Product Data
        $BookingModel->delete($id);

        return redirect()->back()->with('error', lang('Global.deleted'));
    }

    function invoice($id)
    {
		$transaksiModel = new \App\Models\TransaksiModel();
		$transaksi = $transaksiModel->find($id);

		$memberModel = new \App\Models\MemberModel();
		$pembeli = $memberModel->find($transaksi->memberid);

		$variantModel = new \App\Models\VariantModel();
		$variant = $variantModel->find($transaksi->variantid);

		$html = view('transaksi/invoice',[
			'transaksi'=> $transaksi,
			'pembeli' => $pembeli,
			'barang' => $barang,
		]);

		$pdf = new TCPDF('L', PDF_UNIT, 'A5', true, 'UTF-8', false);

		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('58 Vape House');
		$pdf->SetTitle('Invoice');
		$pdf->SetSubject('Invoice');

		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);

		$pdf->addPage();

		// output the HTML content
		$pdf->writeHTML($html, true, false, true, false, '');
		//line ini penting
		$this->response->setContentType('application/pdf');
		//Close and output PDF document
		$pdf->Output('invoice.pdf', 'I');
    }

    public function topup(){
        
        // Declaration Model
        $MemberModel    = new MemberModel;
        $TrxotherModel  = new TrxotherModel;
        $CashModel      = new CashModel;

        // Get Data
        $cashin = $TrxotherModel->findAll();
        $cash   = $CashModel->findAll();
        $input = $this->request->getPost();
        $date=date_create();
        $tanggal = date_format($date,'Y-m-d H:i:s');
        $member = $MemberModel->where('id',$input['customerid'])->first();
        
        // member poin
        $poin = $member['poin'] + $input['value'];
        $data=[
            'poin' => $poin,
        ];
        $MemberModel->save($data);
        
        // Save Cash 
        $cash = $CashModel->where('id',$input['cashid'])->where('outletid',$this->data['outletPick'])->first();
        $data  = [
            'userid'        =>$this->data['uid'],
            'outletid'      =>$this->data['outletPick'],
            'cashid'        =>$input['payment'],
            'description'   =>$input['description'],
            'type'          =>$input['cash'],
            'date'          =>$tanggal,
            'qty'           =>$input['value'],
        ];
        $TrxotherModel->save($data);
        
        $cas = $input['value'] + $cash['qty'];
        $wallet = [
            'id'    => $cash['id'],
            'qty'   => $cas,
        ];
        $CashModel->save($wallet);

        // return
        return redirect()->back()->with('message', lang('Global.saved'));
    }
}
?>