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
use App\models\TrxpaymentModel;
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


        // populating data
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
    
        // Calling models
        $TransactionModel = new TransactionModel();
        $TrxdetailModel = new TrxdetailModel();

        // Populating Data
        $input = $this->request->getGet('daterange');
        $trxdetails = $TrxdetailModel->findAll();

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
        
        // Sales Value
        $trx = array();
        foreach ($transactions as $transaction){
            $summary = array_sum(array_column($transaction, 'value'));
            $trx[] = [
                'id'        => $transaction['id'],
                'date'      => $transaction['date'],
                'value'     => $transaction['value'],
            ];
        }

        // Result Penjualan
        $result = array_sum(array_column($trx, 'value'));

        // discount
        $trxdisc = [];
        foreach ($transactions as $trx){
            $transactionval = [];
            $discounttrx = array();
            $discounttrxpersen = array();
            $discountvariant = array();
            $discountpoin = array();
            foreach ($trxdetails as $trxdetail){
                $transactionval [] = $trxdetail['value'];
                if($trx['id'] === $trxdetail['transactionid']){
                    if ($trx['disctype'] === "0"){
                        $discounttrx[]          = $trx['discvalue'];
                    }elseif ($trx['disctype'] !== "0"){
                        $discounttrxpersen[]    = round($trx['value'] - ($trx['value'] * $trx['discvalue']/100));
                    }
                    $discountvariant[]          = $trxdetail['discvar'];
                    $discountpoin[]             = $trx['pointused'];
                    
                }
            }

            $transactiondisc = array_sum($discounttrx) +  array_sum($discounttrxpersen) ;
            $variantdisc     = array_sum($discountvariant);
            $poindisc        = array_sum($discountpoin);
            $trxvalue        = array_sum($transactionval);

            $trxdisc[] = [
                'trxdisc'       => $transactiondisc,
                'variantdis'    => $variantdisc,
                'poindisc'      => $poindisc,
            ];  
        }    
            
        $trxvar = array_sum(array_column($trxdisc, 'variantdis'));
        $trxdis = array_sum(array_column($trxdisc, 'trxdisc'));
        $discpoint = array_sum(array_column($trxdisc, 'poindisc'));

        
        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=sales.xls");

        // export
        echo '<table>';
        echo '<thead>';
        echo '<tr>';
        echo '<th>Date</th>';
        echo '<th>Penjualan</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        foreach ($transactions as $transaction) {
            echo '<tr>';
            echo '<td>'.$transaction['date'].'</td>';
            echo '<td>'.$transaction['value'].'</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';

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
        $input = $this->request->getGet('daterange');
            
        if (!empty($input)) {
            $daterange = explode(' - ', $input);
            $startdate = $daterange[0];
            $enddate = $daterange[1];
        } else {
            $startdate = date('Y-m-1');
            $enddate = date('Y-m-t');
        }


        $trxid = array();
        $transactions = $TransactionModel->where('date >=', $startdate)->where('date <=', $enddate)->find();
        
        foreach ($transactions as $transaction){
            $trxid[] = $transaction['id'];
        }

        $builder = $db->table('trxdetail')->WhereIn('transactionid',$trxid);
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

        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=productreport.xls");

        // export
        echo '<table>';
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
            echo '<td>'.$product['sold'].'</td>';
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

}
?>