<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\ProductModel;
use App\Models\BrandModel;
use App\Models\CashModel;
use App\Models\CategoryModel;
use App\Models\VariantModel;
use App\Models\BundleModel;
use App\Models\BundledetailModel;
use App\Models\StockModel;
use App\Models\OldStockModel;
use App\Models\OutletModel;
use App\Models\GroupUserModel;
use Myth\Auth\Models\GroupModel;
use App\Models\DebtModel;
use App\Models\GconfigModel;
use App\Models\MemberModel;
use App\Models\PaymentModel;
use App\Models\TransactionModel;
use App\Models\TrxdetailModel;
use App\Models\TrxpaymentModel;
use App\Models\TrxotherModel;
use App\Models\BookingModel;
use App\Models\BookingdetailModel;
use App\Models\PurchaseModel;
use App\Models\PurchasedetailModel;
use App\Models\PresenceModel;

class export extends BaseController{

public function prod()

    {
        // Calling Data
        $ProductModel   = new ProductModel();
        $BrandModel     = new BrandModel();
        $VariantModel   = new VariantModel();
        $CategoryModel  = new CategoryModel();
        $StockModel     = New StockModel();
        $VariantModel   = New VariantModel();
        $OutletModel    = New OutletModel();

        $variants   = $VariantModel->findAll();
        $brands     = $BrandModel->findAll();
        $products   = $ProductModel->findAll();
        $category   = $CategoryModel->findAll();
        $variants   = $VariantModel->findAll();
        $outlets    = $OutletModel->findAll();

        if ($this->data['outletPick'] === null) {
            $stocks      = $StockModel->findAll();
            foreach ($outlets as $outlet){
                if($outlet['id'] === $this->data['outletPick']){
                    $outletname = $outlet['name'];
                }
            }
        } else {
            $stocks      = $StockModel->where('outletid', $this->data['outletPick'])->find();
            foreach ($outlets as $outlet){
                $outletname = $outlet['name'];
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
                                    'id'                => $product['id'],
                                    'prodname'          => $product['name'],
                                    'catname'           => $cat['name'],
                                    'brandname'         => $brand['name'],
                                    'desc'              => $product['description'],
                                    'varname'           => $variant['name'],
                                    'hargamodal'        => $variant['hargamodal'],
                                    'hargajual'         => $variant['hargajual'],
                                    'hargarekomendasi'  => $variant['hargarekomendasi'],
                                    'stock'             => $stock['qty'],                                
                                ];
                            }
                        }
                    }
                }
            }
        }


        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=Products.xls");

        // export
        echo $outletname;
        echo '<table>';
        echo '<thead>';
        echo '<tr>';
        echo '<th>Nama</th>';
        echo '<th>Merek</th>';
        echo '<th>Kategori</th>';
        echo '<th>Description</th>';
        echo '<th>Harga Jual</th>';
        echo '<th>Harga Modal</th>';
        echo '<th>Harga Rekomendasi</th>';
        echo '<th>Stok</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        foreach ($productval as $product) {
            echo '<tr>';
            echo '<td>'.$product['prodname'].'-'.$product['varname'].'</td>';
            echo '<td>'.$product['brandname'].'</td>';
            echo '<td>'.$product['catname'].'</td>';
            echo '<td>'.$product['desc'].'</td>';
            echo '<td>'.$product['hargajual'].'</td>';
            echo '<td>'.$product['hargamodal'].'</td>';
            echo '<td>'.$product['hargarekomendasi'].'</td>';
            echo '<td>'.$product['stock'].'</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
    }

    public function transaction(){

        // Calling Model
        $ProductModel       = new ProductModel;
        $BundleModel        = new BundleModel;
        $BundledetailModel  = new BundledetailModel;
        $BrandModel         = new BrandModel;
        $CashModel          = new CashModel;
        $DebtModel          = new Debtmodel;
        $VariantModel       = new VariantModel;
        $CategoryModel      = new CategoryModel;
        $StockModel         = New StockModel;
        $VariantModel       = New VariantModel;
        $OutletModel        = New OutletModel;
        $MemberModel        = new MemberModel;
        $UserModel          = new UserModel;
        $PaymentModel       = new PaymentModel;
        $TransactionModel   = new TransactionModel;
        $TrxdetailModel     = new TrxdetailModel;
        $TrxpaymentModel    = new TrxpaymentModel;


        // Populating Data
        $bundles                = $BundleModel->findAll();
        $bundets                = $BundledetailModel->findAll();
        $cash                   = $CashModel->findAll();
        $brands                 = $BrandModel->findAll();
        $products               = $ProductModel->findAll();
        $category               = $CategoryModel->findAll();
        $variants               = $VariantModel->findAll();
        $outlets                = $OutletModel->findAll();
        $trxdetails             = $TrxdetailModel->findAll();
        $trxpayments            = $TrxpaymentModel->findAll();
        $payments               = $PaymentModel->findAll();
        $debts                  = $DebtModel->findAll();
        $users                  = $UserModel->findAll();
        $members                = $MemberModel->findAll();

        $input = $this->request->getVar('daterange');

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

        $trxid = [] ;
        foreach ($transactions as $transaction){
            foreach($trxdetails as $trxdetail){
                foreach ($trxpayments as $trxpayment){

                    if ((!empty($transaction['discvalue'])) && ($transaction['disctype'] === '0')) {
                        $discount = $transaction['discvalue'];
                        $disctype = "0";
                    } elseif ((!empty($transction['discvalue'])) && ($transaction['disctype'] === '1')) {
                        $discount = ($transaction['value'] * $transaction['dicsvalue']/100); 
                      
                    } else {
                        $discount = 0;
                      
                    } 

                    if ($transaction['disctype'] === '1'){
                        $disctype = "%";
                    }else{
                        $disctype = "0";
                    }
                    
                    if($transaction['id'] === $trxpayment['transactionid'] && $transaction['id'] === $trxdetail['transactionid']){
                        $trxid [] = [
                            'outletid'  => $transaction['outletid'],
                            'date'      => $transaction['date'],
                            'trxid'     => $transaction['id'],
                            'trdetid'   => $trxdetail['id'],
                            'trxpayid'  => $trxpayment['id'],
                            'paymentid' => $trxpayment['paymentid'],
                            'userid'    => $transaction['userid'],
                            'memberid'  => $transaction['memberid'],
                            'variantid' => $trxdetail['variantid'],
                            'bundleid'  => $trxdetail['bundleid'],
                            'qty'       => $trxdetail['qty'],
                            'redempoin' => $transaction['pointused'],
                            'trxdisc'   => $transaction['discvalue'],
                            'disctype'  => $disctype,
                            'discvar'   => $trxdetail['discvar'],
                            'total'     => $transaction['value'],
                            'subtotal'  => $transaction['value'] + $discount + $trxdetail['discvar'] + $transaction['pointused'],
                            'subvar'    => $trxdetail['value'] + $trxdetail['discvar'],
                        ];
                    }
                }
            }
        }
        
        $outletname = [];
        foreach ($trxid as $trx){
            foreach ($users as $user){
                foreach ($variants as $variant){
                    if($variant['id'] === $trx['variantid']){
                        $variantname = $variant['name'];
                        $variantprice = $variant['hargamodal'] + $variant['hargajual'];
                        foreach ($products as $product){
                            if($product['id'] === $variant['productid'] && $variant['productid'] !== "0"){
                                $productname = $product['name'];
                            }
                        }
                    }elseif($trx['variantid'] === "0"){
                        $variantname = "0";
                    }
                }
                foreach ($payments as $payment){
                    foreach ($outlets as $outlet){
                            foreach ($members as $member){
                                if ($trx['memberid'] === $member['id']){
                                    $membername = $member['name'];
                                }elseif($trx['memberid'] === "0"){
                                    $membername = "Non Member";
                                }
                                foreach ($bundles as $bundle){
                                    if($trx['bundleid'] === "0"){
                                        $bundlename = "0";
                                    }elseif($trx['bundleid'] !== "0" && isset($trx['bundleid']) && $bundle['id'] === $trx['bundleid']){
                                        $bundlename = $bundle['name'];
                                    }
                                }
                            }
                            if($trx['userid'] === $user->id && $trx['paymentid'] === $payment['id'] && $variant['productid'] === $product['id'] && $outlet['id'] === $trx['outletid']){  
                            $outletname [] = $outlet['name'];
                            $trxval [] = [
                                'id'            => $trx['trxid'],
                                'trxdetid'      => $trx['trdetid'],
                                'trxpayid'      => $trxpayment['id'],
                                'date'          => $trx['date'],
                                'user'          => $user->name,
                                'product'       => $productname,
                                'variant'       => $variantname,
                                'payment'       => $payment['name'],
                                'outlet'        => $outlet['name'],
                                'bundleid'      => $bundlename,
                                'qty'           => $trx['qty'],
                                'redempoin'     => $trx['redempoin'],
                                'trxdisc'       => $trx['trxdisc'],
                                'disctype'      => $trx['disctype'],
                                'discvar'       => $trx['discvar'],
                                'subtotal'      => $trx['subtotal'] ,
                                'subvar'        => $trx['subvar'] ,
                                'total'         => $trx['total'],
                                'membername'    => $membername,
                                'hargavar'      => (isset ($trx['variantid'])) ? $variantprice : $bundleprice,
                            ];
                        }
                    }
                }
            }
        }
        
        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=transaction.xls");

        // export
        echo '<table>';
        echo '<thead>';
        echo '<tr>';
        echo '<th>Date</th>';
        echo '<th>Nama Outlet</th>';
        echo '<th>Nama Kasir</th>';
        echo '<th>Nama Pelanggan</th>';
        echo '<th>Produk</th>';
        echo '<th>Jumlah Produk</th>';
        echo '<th>Harga Produk</th>';
        echo '<th>Subtotal</th>';
        echo '<th>Diskon</th>';
        echo '<th>Tipe Diskon</th>';
        echo '<th>Diskon Variant</th>';
        echo '<th>Redeem Point</th>';
        echo '<th>Total</th>';
        echo '<th>Metode Pembayaran</th>';

        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        foreach ($trxval as $trx) {
            echo '<tr>';
            echo '<td>'.$trx['date'].'</td>';
            echo '<td>'.$trx['outlet'].'</td>';
            echo '<td>'.$trx['user'].'</td>';
            echo '<td>'.$trx['membername'].'</td>';
            echo '<td>'.$trx['product'].'</td>';
            echo '<td>'.$trx['qty'].'</td>';
            echo '<td>'.$trx['hargavar'].'</td>';
            echo '<td>'.$trx['subvar'].'</td>';
            echo '<td>'.$trx['trxdisc'].'</td>';
            echo '<td>'.$trx['disctype'].'</td>';
            echo '<td>'.$trx['discvar'].'</td>';
            echo '<td>'.$trx['redempoin'].'</td>';
            echo '<td>'.$trx['total'].'</td>';
            echo '<td>'.$trx['payment'].'</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
       
    }

    public function sales(){

        // Calling Models
        $TransactionModel       = new TransactionModel;
        $TrxdetailModel         = new TrxdetailModel;
        $VariantModel           = new VariantModel;
        $StockModel             = new StockModel;
        $OutletModel            = new OutletModel;
        $TrxotherModel          = new TrxotherModel;

        $input  = $this->request->getGet('daterange');
        $cash   = $TrxotherModel->findAll();
        
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
        $varvalues = [];
        $outletname = [];
        $adress = [];
        for ($date = $startdate; $date <= $enddate; $date += (86400)) {
            if($this->data['outletPick'] === null ){
                $transaction = $TransactionModel->where('date >=', date('Y-m-d 00:00:00', $date))->where('date <=', date('Y-m-d 23:59:59', $date))->find();
                $stocks = $StockModel->findAll();
                $outletname [] = "All Outlets";
                $adress [] = "58vapehouse";

            }else{
                $transaction = $TransactionModel->where('date >=', date('Y-m-d 00:00:00', $date))->where('date <=', date('Y-m-d 23:59:59', $date))->where('outletid',$this->data['outletPick'])->find();
                $stocks = $StockModel->where('outletid',$this->data['outletPick'])->find();
                $outlets = $OutletModel->where('id',$this->data['outletPick'])->find();
            }

            $trxdetails  = $TrxdetailModel->findAll();
            $summary = array_sum(array_column($transaction, 'value'));
            $variants    = $VariantModel->findAll();

            $marginmodals = array();
            $margindasars = array();
           
           
            foreach ($transaction as $trx){
                foreach ($trxdetails as $trxdetail){
                    if($trx['id'] == $trxdetail['transactionid']){

                        // margin
                        $marginmodal = $trxdetail['marginmodal'];
                        $margindasar = $trxdetail['margindasar'];
                        $marginmodals[] = $marginmodal;
                        $margindasars[] = $margindasar;

                        // discount
                        if ($trx['disctype'] === "0"){
                            $discounttrx[]          = $trx['discvalue'];
                        }
                        if ($trx['disctype'] !== "0"){
                            $sub =  ($trxdetail['value']* $trxdetail['qty']);
                            $discounttrxpersen[]    =  ($trx['discvalue'] / 100) * $sub;
                        }
                        $discountvariant[]          = $trxdetail['discvar'];
                        $discountpoin[]             = $trx['pointused'];

                        foreach ($variants as $variant){
                            if($variant['id'] === $trxdetail['variantid']){
                                foreach ($stocks as $stock){
                                    if($variant['id'] === $stock['variantid']){
                                        $varvalues [] = [
                                            'name'          => $variant['name'],
                                            'hargamodal'    => $variant['hargamodal'],
                                            'hargadasar'    => $variant['hargadasar'],
                                            'hargajual'     => $variant['hargajual'],
                                            'qty'           => $stock['qty'],
                                            'price'         => $variant['hargamodal'] + $variant['hargajual'],
                                        ];
                                    }
                                }
                            }
                        }
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
        
        $transactiondisc = array_sum($discounttrx) +  array_sum($discounttrxpersen);
        $variantdisc     = array_sum($discountvariant);
        $poindisc        = array_sum($discountpoin);
        
        $discount [] = [
            'trxdisc'       => $transactiondisc,
            'variantdis'    => $variantdisc,
            'poindisc'      => $poindisc,
        ];

        $totaltrxdisc = array_sum(array_column($discount,'trxdisc'));
        $totalvardisc = array_sum(array_column($discount,'variantdis'));
        $totalpoindisc = array_sum(array_column($discount,'poindisc'));

        // Total Discount
        $alldisc = (int)$totaltrxdisc + (int)$totalvardisc + (int)$totalpoindisc;
        $transactionarr [] = $transactions;
        
        $date1 = date('Y-m-d',$startdate);
        $date2 = date('Y-m-d',$enddate);
        $day1 = date_create($date1);
        $day2 = date_create($date2);
        $interval = date_diff($day1,$day2)->format("%a");

        // Profit Calculation
        $omset = [];
        $capPrice =[];
        foreach ($varvalues as $varvalue){
            $omset [] = $varvalue['price'] * $varvalue['qty'];
            $capPrice [] = $varvalue['hargamodal'] * $varvalue['qty'];
        }
        
        $omsetestimate  = array_sum($omset);
        $capitalprice   = array_sum($capPrice);
        $profitvalue    = $omsetestimate - $capitalprice;

        $keuntunganmodal = array_sum(array_column($transactions, 'modal'));
        $keuntungandasar = array_sum(array_column($transactions, 'dasar'));
        $trxvalue        = array_sum(array_column($transactions, 'value'));
        
        // Outlet Setup
        if($this->data['outletPick'] !== null){
                foreach ($outlets as $outlet){
                $outletname[] = $outlet['name'];
                $adress [] = $outlet['address'];
            }
        }

        //sales
        $salesresult = array_sum(array_column($transactions, 'value'));
    
        // Groos Sales
        $grossales = $salesresult + $variantdisc +  $transactiondisc +  $poindisc;

        // Set Cash In Cash Out
        $cashin  = [];
        $cashout = [];
        foreach ($cash as $cas){
            if($cas['type'] === "0"){
                $cashin [] = $cas['qty'];
            }elseif($cas['type'] !== "0"){
                $cashout [] = $cas['qty'];
            }
        }
        $casin = array_sum($cashin);
        $casout = array_sum($cashout);
        
        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=sales$date1-$date2.xls");

        echo'<style type="text/css">
        table {
            font-family: arial, sans-serif;
        }
        td, th {
            text-align: left
        }
        </style>';
        echo '<table style="width:50%">';
        echo '<tr>';
            echo'<td colspan="2" style="text-align: center; font-family: sans-serif; font-weight: bold;">'.$outletname[0].'</td>';
        echo '</tr>';
        echo '<tr>';
            echo'<td colspan="2" style="text-align: center; font-family: arial, sans-serif; font-weight: bold;">'. $adress[0].'</td>';
        echo '</tr>';
        echo '<tr>';
            echo'<td colspan="2" style="text-align: center; font-family: arial, sans-serif; font-weight: bold;">Ringkasan Penjualan</td>';
        echo '</tr>';
        echo '<tr>';
            echo'<td colspan="2" style="text-align: center; font-family: arial, sans-serif; font-weight: bold;">'.$date1.'-'.$date2.'</td>';
        echo '</tr>';
        echo '<tr>';
            echo'<td colspan="2" style="text-align: center; font-family: arial, sans-serif; font-weight: bold;"></td>';
        echo '</tr>';
        echo '<tr >';
            echo'<th style="width:30%">Penjualan</th>';
            echo'<td style="width:20%">'.$salesresult.'</td>';
        echo '</tr>';
        echo'<tr>';
            echo'<th style="width:30%">Diskon</th>';
            echo'<td style="width:20%">'.$alldisc.'</td>';
        echo'</tr>';
        echo'<tr>';
            echo'<th style="width:30%">Total Omset</th>';
            echo'<td style="width:20%">'.$omsetestimate.'</td>';
        echo'</tr>';
        echo'<tr>';
            echo'<th style="width:30%">Harga Modal</th>';
            echo'<td style="width:20%">'.$capitalprice.'</td>';
        echo'</tr>';
        echo'<tr>';
            echo'<th style="width:30%">Kas Masuk</th>';
            echo'<td style="width:20%">'.$casin.'</td>';
        echo'</tr>';
        echo'<tr>';
            echo'<th style="width:30%">Kas Keluar</th>';
            echo'<td style="width:20%">'.$casout.'</td>';
        echo'</tr>';
        echo'<tr>';
            echo'<th style="width:30%">Keuntungan</th>';
            echo'<td style="text-align: left; font-family: arial, sans-serif; font-weight: bold;">'.$profitvalue.'</td>';
        echo'</tr>';
        echo'</table>';
    }

    public function product(){

       // Calling models
       $TransactionModel   = new TransactionModel();
       $TrxdetailModel     = new TrxdetailModel();
       $ProductModel       = new ProductModel();
       $CategoryModel      = new CategoryModel();
       $VariantModel       = new VariantModel();
       $StockModel         = new StockModel();
       $BundleModel        = new BundleModel();
       $BundledetailModel  = new BundledetailModel();
       $OutletModel        = new OutletModel();

       $products   = $ProductModel->findAll();
       $category   = $CategoryModel->findAll();
       $variants   = $VariantModel->findAll();
       $stocks     = $StockModel->findAll();
       $bundles    = $BundleModel->findAll();
       $bundets    = $BundledetailModel->findAll();
       $trxdetails = $TrxdetailModel->findAll();
       $outlets    = $OutletModel->findAll();


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

        
        $day1 = date_create($startdate);
        $day2 = date_create($enddate);
        
        if($this->data['outletPick'] === null ){
            $transactions = $TransactionModel->where('date >=', $startdate)->where('date <=', $enddate)->find();
            $outletname = "All Outlets";
            $adress = "58vapehouse";

        }else{
            $transactions = $TransactionModel->where('outletid', $this->data['outletPick'])->where('date >=', $startdate)->where('date <=', $enddate)->find();
            $outlets = $OutletModel->find($this->data['outletPick']);
            $outletname = $outlets['name'];
            $adress = $outlets['address'];
        }


        $productval     = [];
        $variantvalue   = [];
        $variantval     = [];
        $trxvar         = [];
        $diskon         = [];
        $productqty     = [];
        $trxval         = [];
        $bundleval      = [];

        foreach ($transactions as $transaction){
            $discounttrx = array();
            $discounttrxpersen = array();
            $discountvariant = array();
            $discountpoin = array();
            foreach ($trxdetails as $trxdetail){
                foreach($bundles as $bundle){
                    if($transaction['id'] === $trxdetail['transactionid'] && $bundle['id'] === $trxdetail['bundleid']){
                        $bundleval []   = [
                            'id'    => $bundle['id'],
                            'name'  => $bundle['name'],
                            'value' => $trxdetail['value'],
                        ];
                    }
                }
                if($transaction['id'] === $trxdetail['transactionid']){

                    if ($transaction['disctype'] === "0"){

                        $discounttrx[]          = $transaction['discvalue'];

                    }
                    if ($transaction['disctype'] !== "0"){

                        $sub = ($trxdetail['value']) * $trxdetail['qty'];
                        $discounttrxpersen[]    = $sub * ($transaction['discvalue']/100);

                    }
                    $discountvariant[]          = $trxdetail['discvar'];

                    $discountpoin[]             = $transaction['pointused'];

                    foreach ($products as $product) {
                        foreach ($variants as $variant){
                            if(($variant['id'] === $trxdetail['variantid']) && ($variant['productid'] === $product['id'])){
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
                                                    'value'         => $trxdetail['value'] * $trxdetail['qty'],
                                                    'gross'         => $trxdetail['value'] + $trxdetail['discvar'] * $trxdetail['qty'],
                                                    'qty'           => $trxdetail['qty'],
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

        $bundletotal = array_sum(array_column($bundleval,'value'));

        $produk = [];
        foreach ($variantvalue as $vars) {
            if (!isset($produk[$vars['id'].$vars['product']])) {
                $produk[$vars['id'].$vars['product']] = $vars;
            } else {
                $produk[$vars['id'].$vars['product']]['value'] += $vars['value'];
                $produk[$vars['id'].$vars['product']]['qty'] += $vars['qty'];
                $produk[$vars['id'].$vars['product']]['gross'] += $vars['gross'];
            }
        }
        $produk = array_values($produk);

        // disc calculation
        $trxdisc    = array_sum(array_column($diskon,'trxdisc'));
        $poindisc   = array_sum(array_column($diskon,'poindisc'));
        $vardisc    = array_sum(array_column($diskon,'variantdis'));
        $proval     = array_sum(array_column($produk,'value'));

        // if want to get net sales with trx disc and without bundle value
        $netsales = $proval - ($trxdisc + $poindisc);

        // Total Stock
        $stoktotal = array_sum(array_column($produk,'qty'));

        // Total Sales Without trx disc, bundle & poin disc
        $salestotal = array_sum(array_column($produk,'value'));

        // Total Gross
        $grosstotal = array_sum(array_column($produk,'gross'));

        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=productreport.xls");

        // export
        echo '<table>';
        echo '<tr>';
        echo'<td style="text-align: center; font-family: sans-serif; font-weight: bold;">'.$outletname.'</td>';
        echo '</tr>';
        echo '<tr>';
            echo'<td style="text-align: center; font-family: arial, sans-serif; font-weight: bold;">'. $adress.'</td>';
        echo '</tr>';
        echo '<tr>';
            echo'<td style="text-align: center; font-family: arial, sans-serif; font-weight: bold;">Ringkasan Produk</td>';
        echo '</tr>';
        echo '<tr>';
            echo'<td style="text-align: center; font-family: arial, sans-serif; font-weight: bold;">'.$day1.'-'.$day2.'</td>';
        echo '</tr>';
        echo '<thead>';
        echo '<tr>';
        echo '<th>Produk</th>';
        echo '<th>Kategory</th>';
        echo '<th>Jumlah Transaksi</th>';
        echo '<th>Nominal Transaksi</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        foreach ($produk as $product) {
            echo '<tr>';
            echo '<td>'.$product['product'].'</td>';
            echo '<td>'.$product['category'].'</td>';
            echo '<td>'.$product['qty'].'</td>';
            echo '<td>'.$product['value'].'</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';

    }

    public function payment (){
         
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

            $totalvalue = array_sum(array_column($pay,'pvalue'));
            $totalqty = array_sum(array_column($pay,'pqty'));

            header("Content-type: application/vnd-ms-excel");
            header("Content-Disposition: attachment; filename=payment.xls");

            // export
            echo '<table>';
            echo '<thead>';
            echo '<tr>';
            echo '<th>Nama</th>';
            echo '<th>Jumlah Transaksi</th>';
            echo '<th>Nominal Transaksi</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            foreach ($pay as $payment) {
                echo '<tr>';
                echo '<td>'.$payment['name'].'</td>';
                echo '<td>'.$payment['pqty'].'</td>';
                echo '<td>'.$payment['pvalue'].'</td>';
                echo '</tr>';
            }
            
            echo '<tr>';
                echo'<th style="align-text:left; font-family: arial, sans-serif; font-weight: bold;">Total</th>';
                echo'<td style="align-text:left; font-family: arial, sans-serif; font-weight: bold;">'.$totalqty.'</td>';
                echo'<td style="align-text:left; font-family: arial, sans-serif; font-weight: bold;">'.$totalvalue.'</td>';
            echo '</tr>';
            echo '</tbody>';
            echo '</table>';


    } else {
            return redirect()->to('');
        }
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
        $transaction = $TransactionModel->where('date >=', $startdate)->where('date <=', $enddate)->find();
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

            $transactiondisc = array_sum($discounttrx) +  array_sum($discounttrxpersen) ;
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

        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=discount.xls");

        // export
        echo '<table>';
        echo '<thead>';
        echo '<tr>';
        echo '<th>Diskon Transaksi</th>';
        echo '<th>Diskon Variant</th>';
        echo '<th>Diskon Poin</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        foreach ($transactions as $disc) {
            echo '<tr>';
            echo '<td>'.$disc['trxdisc'].'</td>';
            echo '<td>'.$disc['variantdis'].'</td>';
            echo '<td>'.$disc['poindisc'].'</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';


        
    }

    public function profit(){

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

        $transactionarr [] = $transactions;
        
        $keuntunganmodal = array_sum(array_column($transactions, 'modal'));
        $keuntungandasar = array_sum(array_column($transactions, 'dasar'));
        $trxvalue        = array_sum(array_column($transactions, 'value'));

        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=profit.xls");

        // export
        echo '<table>';
        echo '<thead>';
        echo '<tr>';
        echo '<th>Keuntungan Dasar</th>';
        echo '<th>Keuntungan Modal</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        echo '<tr>';
        echo '<td>'.$keuntungandasar.'</td>';
        echo '<td>'.$keuntunganmodal.'</td>';
        echo '</tr>';
        echo '</tbody>';
        echo '</table>';
        
    }

    public function employe (){

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

        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=employe.xls");

        // export
        echo '<table>';
        echo '<thead>';
        echo '<tr>';
        echo '<th>Nama</th>';
        echo '<th>Posisi</th>';
        echo '<th>Nominal Transaksi</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
            foreach ($employetrx as $employe ){
                echo '<tr>';
                echo '<td>'.$employe['name'].'</td>';
                echo '<td>'.$employe['role'].'</td>';
                echo '<td>'.$employe['value'].'</td>';
                echo '</tr>';
            }
        echo '</tbody>';
        echo '</table>';
    }

    public function customer (){
    
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

            header("Content-type: application/vnd-ms-excel");
            header("Content-Disposition: attachment; filename=employe.xls");
    
            // export
            echo '<table>';
            echo '<thead>';
            echo '<tr>';
            echo '<th>Nama</th>';
            echo '<th>Jumlah Transaksi</th>';
            echo '<th>Nominal Transaksi</th>';
            echo '<th>Hutang</th>';
            echo '<th>No Telphone</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
                foreach ($customer as $cust ){
                    echo '<tr>';
                    echo '<td>'.$cust['name'].'</td>';
                    echo '<td>'.$cust['trx'].'</td>';
                    echo '<td>'.$cust['value'].'</td>';
                    echo '<td>'.$cust['debt'].'</td>';
                    echo '<td>'.$cust['phone'].'</td>';
                    echo '</tr>';
                }
            echo '</tbody>';
            echo '</table>';
        }
    }

    public function presence(){

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

        // Sum Total  Presence
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

        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=presence.xls");

        // export
        echo '<table>';
        echo '<thead>';
        echo '<tr>';
        echo '<th>Nama</th>';
        echo '<th>Posisi</th>';
        echo '<th>Kehadiran</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
            foreach ($admin as $adm ){
                echo '<tr>';
                echo '<td>'.$adm['name'].'</td>';
                echo '<td>'.$adm['role'].'</td>';
                echo '<td>'.$presen.'</td>';
                echo '</tr>';
            }
        echo '</tbody>';
        echo '</table>';

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

        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=bundle.xls");

        // export
        echo '<table>';
        echo '<thead>';
        echo '<tr>';
        echo '<th>Nama</th>';
        echo '<th>Jumlah</th>';
        echo '<th>Harga Bundle</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
            foreach ($paket as $bundle ){
                echo '<tr>';
                echo '<td>'.$bundle['name'].'</td>';
                echo '<td>'.$bundle['qty'].'</td>';
                echo '<td>'.$bundle['price'].'</td>';
                echo '</tr>';
            }
        echo '</tbody>';
        echo '</table>';
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
        $BundleModel        = new BundleModel();

        // Populating Data
        $trxdetails = $TrxdetailModel->findAll();
        $products   = $ProductModel->findAll();
        $category   = $CategoryModel->findAll();
        $variants   = $VariantModel->findAll();
        $brands     = $BrandModel->findAll();
        $bundles    = $BundleModel->findAll();

        // Daterange Filter System
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
        $productqty     = [];
        $trxval         = [];
        $bundleval      = [];

        foreach ($transactions as $transaction){
            $discounttrx = array();
            $discounttrxpersen = array();
            $discountvariant = array();
            $discountpoin = array();
            foreach ($trxdetails as $trxdetail){
                foreach($bundles as $bundle){
                    if($transaction['id'] === $trxdetail['transactionid'] && $bundle['id'] === $trxdetail['bundleid']){
                        $bundleval []   = [
                            'id'    => $bundle['id'],
                            'name'  => $bundle['name'],
                            'value' => $trxdetail['value'],
                        ];
                    }
                }
                if($transaction['id'] === $trxdetail['transactionid']){

                    if ($transaction['disctype'] === "0"){

                        $discounttrx[]          = $transaction['discvalue'];

                    }
                    if ($transaction['disctype'] !== "0"){

                        $sub = ($trxdetail['value']) * $trxdetail['qty'];
                        $discounttrxpersen[]    = $sub * ($transaction['discvalue']/100);

                    }
                    $discountvariant[]          = $trxdetail['discvar'];

                    $discountpoin[]             = $transaction['pointused'];

                    foreach ($products as $product) {
                        foreach ($variants as $variant){
                            if(($variant['id'] === $trxdetail['variantid']) && ($variant['productid'] === $product['id'])){
                                foreach ($products as $product){
                                    if($variant['productid'] === $product['id']){
                                        $productval [] = $product['name'];
                                        foreach ($category as $cat){
                                            if($product['catid'] === $cat['id']){
                                                $variantvalue[] = [
                                                    'id'            => $cat['id'],
                                                    'trxid'         => $transaction['id'],
                                                    'product'       => $product['name'],
                                                    'category'      => $cat['name'],
                                                    'value'         => $trxdetail['value'] + $trxdetail['discvar'] * $trxdetail['qty'],
                                                    'qty'           => $trxdetail['qty'],
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

        $bundletotal = array_sum(array_column($bundleval,'value'));

        $produk = [];
        foreach ($variantvalue as $vars) {
            if (!isset($produk[$vars['id'].$vars['product']])) {
                $produk[$vars['id'].$vars['product']] = $vars;
            } else {
                $produk[$vars['id'].$vars['product']]['value'] += $vars['value'];
                $produk[$vars['id'].$vars['product']]['qty'] += $vars['qty'];
            }
        }
        $produk = array_values($produk);
        // Total Stock
        $stoktotal = array_sum(array_column($produk,'qty'));

        // Total Sales
        $salestotal = array_sum(array_column($produk,'value'));

        // Total Gross
        $grosstotal = array_sum(array_column($produk,'value'));

        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=category.xls");

        // export
        echo '<table>';
        echo '<thead>';
        echo '<tr>';
        echo '<th>Nama</th>';
        echo '<th>Jumlah Penjualan</th>';
        echo '<th>Penjualan Kotor</th>';
        echo '<th>Total</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        foreach ($produk as $prod) {
            echo '<tr>';
            echo '<td>'.$prod['category'].'</td>';
            echo '<td>'.$prod['qty'].'</td>';
            echo '<td>'.$prod['value'].'</td>';
            echo '<td>'.$prod['value'].'</td>';
            echo '</tr>';
        }
        
        echo '<tr>';
            echo'<th style="align-text:left; font-family: arial, sans-serif; font-weight: bold;">Jumlah</th>';
            echo'<td style="align-text:left; font-family: arial, sans-serif; font-weight: bold;">'.$stoktotal.'</td>';
            echo'<td style="align-text:left; font-family: arial, sans-serif; font-weight: bold;">'.$salestotal.'</td>';
            echo'<td style="align-text:left; font-family: arial, sans-serif; font-weight: bold;">'.$grosstotal.'</td>';
        echo '</tr>';
        echo '</tbody>';
        echo '</table>';

    }

}
?>