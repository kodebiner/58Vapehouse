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
use App\Models\StockMovementModel;
use App\Models\StockMoveDetailModel;
use App\Models\PresenceModel;
use App\Models\SopModel;
use App\Models\SopDetailModel;

class export extends BaseController
{
    protected $data;
    protected $db, $builder;
    protected $auth;
    protected $config;

    public function prod()
    {
        // Calling Services & Libraries
        $db         = \Config\Database::connect();

        // Calling Models
        $OutletModel = new OutletModel();

        // Populating Data
        if ($this->data['outletPick'] === null) {
            return redirect()->back()->with('error', lang('Global.chooseoutlet'));
        } else {
            $outlet     = $OutletModel->find($this->data['outletPick']);
            $outletname = $outlet['name'];
            
            // if ($this->data['outletPick'] != null) {
            //     $outlet     = $OutletModel->find($this->data['outletPick']);
            //     $outletname = $outlet['name'];
            // } else {
            //     $outletname = lang('Global.allOutlets');
            // }

            $exported   = $db->table('stock');
            $stockexp   = $exported->select('stock.qty as qty, variant.hargamodal as hargamodal, variant.hargadasar as hargadasar, variant.hargajual as hargajual, variant.hargarekomendasi as hargarekomendasi, variant.name as varname, product.name as prodname, category.name as catname, brand.name as brandname, variant.sku as sku');
            $stockexp   = $exported->join('variant', 'stock.variantid = variant.id', 'left');
            $stockexp   = $exported->join('product', 'variant.productid = product.id', 'left');
            $stockexp   = $exported->join('category', 'product.catid = category.id', 'left');
            $stockexp   = $exported->join('brand', 'product.brandid = brand.id', 'left');
            // if ($this->data['outletPick'] != null) {
            //     $stockexp   = $exported->where('stock.outletid', $this->data['outletPick']);
            // }
            $stockexp   = $exported->where('stock.outletid', $this->data['outletPick']);
            $stockexp   = $exported->orderBy('product.name', 'ASC');
            $stockexp   = $exported->get();
            $productval = $stockexp->getResultArray();


            header("Content-type: application/vnd-ms-excel");
            header("Content-Disposition: attachment; filename=product-$outletname.xls");

            // export
            echo $outletname;
            echo '<table>';
            echo '<thead>';
            echo '<tr>';
            echo '<th>SKU</th>';
            echo '<th>Nama</th>';
            echo '<th>Merek</th>';
            echo '<th>Kategori</th>';
            echo '<th>Harga Jual</th>';
            echo '<th>Harga Dasar</th>';
            echo '<th>Harga Modal</th>';
            echo '<th>Harga Rekomendasi</th>';
            echo '<th>Stok</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            foreach ($productval as $product) {
                $hargajual = (int)$product['hargamodal'] + (int)$product['hargajual'];
                echo '<tr>';
                echo '<td>' . $product['sku'] . '</td>';
                echo '<td>' . $product['prodname'] . '-' . $product['varname'] . '</td>';
                echo '<td>' . $product['brandname'] . '</td>';
                echo '<td>' . $product['catname'] . '</td>';
                echo '<td>' . $hargajual . '</td>';
                echo '<td>' . $product['hargadasar'] . '</td>';
                echo '<td>' . $product['hargamodal'] . '</td>';
                echo '<td>' . $product['hargarekomendasi'] . '</td>';
                echo '<td>' . $product['qty'] . '</td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
        }
    }

    public function transaction()
    {
        // Conecting To Database
        $db = \Config\Database::connect();

        // Calling Model
        $OutletModel        = new OutletModel;
        $TransactionModel   = new TransactionModel;

        // Populating Data
        $input = $this->request->getVar('daterange');

        if (!empty($input)) {
            $daterange = explode(' - ', $input);
            $startdate = $daterange[0];
            $enddate = $daterange[1];
        } else {
            $startdate  = date('Y-m-1' . ' 00:00:00');
            $enddate    = date('Y-m-t' . ' 23:59:59');
        }

        // Populating Data
        // if ($this->data['outletPick'] === null) {
        //     $transactions = $TransactionModel->where('date >=', $startdate)->where('date <=', $enddate)->find();
        //     $outlet = 'All Outlets';
        // } else {
        //     $transactions = $TransactionModel->where('date >=', $startdate)->where('date <=', $enddate)->where('outletid', $this->data['outletPick'])->find();
        //     $outlet = $OutletModel->find($this->data['outletPick']);
        // }


        $exported   = $db->table('transaction');
        // if ($startdate === $enddate) {
            $exported->where('date >=', $startdate . " 00:00:00")->where('date <=', $enddate . " 23:59:59");
        // } else {
        //     $exported->where('date >=', $startdate)->where('date <=', $enddate);
        // }
        $transactionhist   = $exported->select('transaction.date as date, transaction.disctype as disctype, transaction.discvalue as discval, transaction.pointused as redempoin, transaction.value as total, member.name as member, product.name as product, variant.name as variant, trxdetail.qty as qty, variant.hargamodal as modal, variant.hargajual as jual, trxdetail.value as trxdetval, trxdetail.discvar as discvar, payment.name as payment, outlet.name as outlet, outlet.address as address, bundle.name as bundle, users.username as kasir');
        $transactionhist   = $exported->join('trxdetail', 'transaction.id = trxdetail.transactionid', 'left');
        $transactionhist   = $exported->join('users', 'transaction.userid = users.id', 'left');
        $transactionhist   = $exported->join('outlet', 'transaction.outletid = outlet.id', 'left');
        $transactionhist   = $exported->join('member', 'transaction.memberid = member.id', 'left');
        $transactionhist   = $exported->join('trxpayment', 'trxdetail.transactionid = trxpayment.transactionid', 'left');
        $transactionhist   = $exported->join('bundle', 'trxdetail.bundleid = bundle.id', 'left');
        $transactionhist   = $exported->join('variant', 'trxdetail.variantid = variant.id', 'left');
        $transactionhist   = $exported->join('payment', 'trxpayment.paymentid = payment.id', 'left');
        $transactionhist   = $exported->join('product', 'variant.productid = product.id', 'left');
        $transactionhist   = $exported->where('transaction.outletid', $this->data['outletPick']);
        $transactionhist   = $exported->orderBy('transaction.date', 'DESC');
        $transactionhist   = $exported->get();
        $transactionhist   = $transactionhist->getResultArray();

        $otname = $transactionhist['0']['outlet'];
        $otaddress = $transactionhist['0']['address'];

        ?>
            <style>
                .cntr {
                    text-align: center;
                }

                th {
                    text-align: center;
                }

                td {
                    text-align: left;
                }
            </style>
        <?php

        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=transaction$startdate-$enddate.xls");

        // export
        echo '<table>';
        echo '<thead>';
        echo '<tr>';
        echo '<th colspan="16" class="cntr">' . $otname . '</th>';
        echo '</tr>';
        echo '<tr>';
        echo '<th colspan="16" class="cntr">' . $otaddress . '</th>';
        echo '</tr>';
        echo '<tr>';
        echo '<th colspan="16" class="cntr">Ringkasan Transaksi ' . $startdate . ' - ' . $enddate . '</th>';
        echo '</tr>';
        echo '<tr>';
        echo '<th colspan="16" class="cntr"></th>';
        echo '</tr>';
        echo '<tr>';
        echo '<th>Kode</th>';
        echo '<th>Tanggal</th>';
        echo '<th>Jam</th>';
        echo '<th>Nama Outlet</th>';
        echo '<th>Nama Kasir</th>';
        echo '<th>Nama Pelanggan</th>';
        echo '<th>Produk</th>';
        echo '<th>Jumlah Produk</th>';
        echo '<th>Harga Produk</th>';
        echo '<th>Subtotal</th>';
        echo '<th>Diskon Variant</th>';
        echo '<th>Nominal Diskon</th>';
        echo '<th>Tipe Diskon</th>';
        echo '<th>Redeem Point</th>';
        echo '<th>Total</th>';
        echo '<th>Metode Pembayaran</th>';

        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        foreach ($transactionhist as $trxhist) {

            if ((!empty($trxhist['discval'])) && ($trxhist['disctype'] === '0')) {
                $discount = $trxhist['discval'];
                $disctype = "0";
            } elseif ((!empty($trxhist['discval'])) && ($trxhist['disctype'] === '1')) {
                $discount = ($trxhist['trxdetval'] * $trxhist['discval'] / 100);
            } else {
                $discount = 0;
            }

            if ($trxhist['disctype'] === '1') {
                $disctype = "%";
            } else {
                $disctype = "Rp";
            }

            if (!empty($trxhist['member'])) {
                $membername = $trxhist['member'];
            } else {
                $membername = "Non Member";
            }

            if (!empty($trxhist['product'])) {
                $product = $trxhist['product'];
            } else {
                $product = $trxhist['bundle'];
            }

            if (!empty($trxhist['discvar'])) {
                $discvar = $trxhist['discvar'];
            } else {
                $discvar = "0";
            }


            echo '<tr>';
            echo '<td>' . date(strtotime($trxhist['date'])) . '</td>';
            echo '<td>' . date('d M Y', strtotime($trxhist['date'])) . '</td>';
            echo '<td>' . date('H:i:s', strtotime($trxhist['date'])) . '</td>';
            echo '<td>' . $trxhist['outlet'] . '</td>';
            echo '<td>' . $trxhist['kasir'] . '</td>';
            echo '<td>' . $membername . '</td>';
            echo '<td>' . $product . '</td>';
            echo '<td class="cntr">' . $trxhist['qty'] . '</td>';
            echo '<td>' . $trxhist['trxdetval'] + $trxhist['discvar'] . '</td>';
            echo '<td>' . ($trxhist['trxdetval'] + $trxhist['discvar']) * $trxhist['qty'] . '</td>';
            echo '<td>' . $discvar . '</td>';
            echo '<td>' . $trxhist['discval'] . '</td>';
            echo '<td class="cntr">' . $disctype . '</td>';
            echo '<td>' . $trxhist['redempoin'] . '</td>';
            echo '<td>' . $trxhist['total'] . '</td>';
            echo '<td>' . $trxhist['payment'] . '</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
    }

    public function sales()
    {

        // Calling Models
        $TransactionModel       = new TransactionModel();
        $TrxdetailModel         = new TrxdetailModel();
        $VariantModel           = new VariantModel();
        $ProductModel           = new ProductModel();
        $BundleModel            = new BundleModel();
        $OutletModel            = new OutletModel();
        $TrxotherModel          = new TrxotherModel();
        $BundleModel            = new BundleModel();

        $input  = $this->request->getVar('daterange');

        if (!empty($input)) {
            $daterange = explode(' - ', $input);
            $startdate = $daterange[0];
            $enddate = $daterange[1];
        } else {
            $startdate  = date('Y-m-1' . ' 00:00:00');
            $enddate    = date('Y-m-t' . ' 23:59:59');
        }
        $date1 = date('Y-m-d', strtotime($startdate));
        $date2 = date('Y-m-d', strtotime($enddate));

        $discount           = array();
        $memberdisc         = array();
        $discounttrx        = array();
        $discountvariant    = array();
        $discountglobal     = array();
        $discountpoin       = array();
        $marginmodals       = array();
        $margindasars       = array();

        if ($this->data['outletPick'] === null) {
            $transaction    = $TransactionModel->where('date >=', $startdate . " 00:00:00")->where('date <=', $enddate . " 23:59:59")->find();
            $trxothers      = $TrxotherModel->notLike('description', 'Top Up')->notLike('description', 'Debt')->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->find();
            $address        = "All Outlets";
            $outletname     = "58vapehouse";
        } else {
            $transaction    = $TransactionModel->where('date >=', $startdate . " 00:00:00")->where('date <=', $enddate . " 23:59:59")->where('outletid', $this->data['outletPick'])->find();
            $trxothers      = $TrxotherModel->notLike('description', 'Top Up')->notLike('description', 'Debt')->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->where('outletid', $this->data['outletPick'])->find();
            $outlets        = $OutletModel->find($this->data['outletPick']);
            $address        = $outlets['address'];
            $outletname     = $outlets['name'];
        }

        // $cash   = $TrxotherModel->where('date >=', $startdate . " 00:00:00")->where('date <=', $enddate . " 23:59:59")->find();

        // $db = \Config\Database::connect();

        // $exported           = $db->table('transaction');
        // $transactionarr     = $exported->select('transaction.id as id, outlet.name as outlet, outlet.address as address, trxdetail.id as trxdetid, trxdetail.value as trxdetval, trxdetail.marginmodal as marginmodal, trxdetail.margindasar as margindasar, trxdetail.qty as qty, transaction.date as date, transaction.disctype as disctype, trxdetail.discvar as discvar, transaction.discvalue as discval, transaction.pointused as redempoin, transaction.value as total, transaction.memberdisc as memberdisc');
        // $transactionarr     = $exported->join('trxdetail', 'transaction.id = trxdetail.transactionid', 'left');
        // $transactionarr     = $exported->join('variant', 'trxdetail.variantid = variant.id', 'left');
        // $transactionarr     = $exported->join('outlet', 'transaction.outletid = outlet.id', 'left');
        // $transactionarr     = $exported->where('transaction.outletid', $this->data['outletPick']);
        // // if ($startdate === $enddate) {
        //     $transactionarr   = $exported->where('date >=', $startdate . " 00:00:00")->where('date <=', $enddate . " 23:59:59");
        // // } else {
        // //     $transactionarr   = $exported->where('date >=', $startdate)->where('date <=', $enddate);
        // // }
        // $transactionarr     = $exported->orderBy('transaction.date', 'DESC');
        // $transactionarr     = $exported->get();
        // $transactionarr     = $transactionarr->getResultArray();

        // if (!empty($transactionarr)) {
        //     // transaction detail result
        //     $trxvalue       = [];
        //     $trxdiscount    = [];
        //     $trxredpoin     = [];
        //     $hargamodal     = [];
        //     $hargadasar     = [];
        //     foreach ($transactionarr as $transaction) {
        //         $trxvalue[] = [
        //             'id'    => $transaction['id'],
        //             'value' => $transaction['total'],
        //         ];
        //         if (!empty($transaction['discvalue'])) {
        //             // if ($transaction['disctype'] == "0") {
        //                 $trxdiscount[]  = $transaction['discvalue'];
        //             // } else {
        //             //     $trxdiscount[]  = (int)$transaction['value'] * ((int)$transaction['discvalue'] / 100);
        //             // }
        //         }
        //         if ($transaction['discvar'] != '0') {
        //             $trxdiscount[]     = $transaction['discvar'];
        //         }
        //         if ($transaction['memberdisc'] != '0') {
        //             $trxdiscount[]     = $transaction['memberdisc'];
        //         }
        //         $trxredpoin[]   = $transaction['redempoin'];
        //         $hargamodal[]   = (int)$transaction['qty'] * (int)$transaction['marginmodal'];
        //         $hargadasar[]   = (int)$transaction['qty'] * (int)$transaction['margindasar'];
        //     }
        //     $totaldiscount  = array_sum($trxdiscount);
        //     $totalredpoin   = array_sum($trxredpoin);
        //     $totalmodal     = array_sum($hargamodal);
        //     $totaldasar     = array_sum($hargadasar);
            
        //     $trxval = [];
        //     foreach ($trxvalue as $transactionval) {
        //         if (!isset($trxval[$transactionval['id']])) {
        //             $trxval[$transactionval['id']] = $transactionval;
        //         }
        //     }
        //     $trxval = array_values($trxval);

        //     // total penjualan
        //     $totalsales = array_sum(array_column($trxval, 'value'));

        //     $discount = array();
        //     $discounttrx = array();
        //     $discounttrxpersen = array();
        //     $discountvariant = array();
        //     $discountpoin = array();
        //     $omsetcalculation = array();
        //     $outletname = '';

        //     foreach ($trxval as $trxid) {
        //         foreach ($transactionarr as $transaction) {
        //             if ($trxid['id'] == $transaction['id']) {
        //                 $omsetvalue[] = ($transaction['trxdetval'] + $transaction['discvar']) * $transaction['qty'];

        //                 // outlet
        //                 $outletname = $transaction['outlet'];
        //                 $address = $transaction['address'];

        //                 // margin
        //                 $marginmodal = $transaction['marginmodal'];
        //                 $margindasar = $transaction['margindasar'];
        //                 $marginmodals[] = $marginmodal;
        //                 $margindasars[] = $margindasar;

        //                 // discount
        //                 if ($transaction['disctype'] === "0") {
        //                     $discounttrx[]          = (int)$transaction['discval'];
        //                 }
        //                 if ($transaction['disctype'] !== "0") {
        //                     $sub =  ($transaction['trxdetval'] * $transaction['qty']);
        //                     $discounttrxpersen[]    =  ((int)$transaction['discval'] / 100) * $sub;
        //                 }
        //                 $discountvariant[]          = $transaction['discvar'];
        //                 $discountpoin[]             = $transaction['redempoin'];

        //                 $omsetcalculation[] = [
        //                     'omset'         => (($transaction['trxdetval'] * $transaction['qty']) - $transaction['discvar']),
        //                     'modalprice'    => (($transaction['trxdetval'] * $transaction['qty']) - ($transaction['discvar'])) - $transaction['marginmodal'] * $transaction['qty'],
        //                     'basicprice'    => (($transaction['trxdetval'] * $transaction['qty']) - ($transaction['discvar'])) - $transaction['margindasar'] * $transaction['qty'],
        //                 ];
        //             }
        //         }

        //         // margin 
        //         $marginmodalsum = array_sum($marginmodals);
        //         $margindasarsum = array_sum($margindasars);
        //     }

        //     // discount sum
        //     $transactiondisc = array_sum($discounttrx) +  array_sum($discounttrxpersen);
        //     $variantdisc     = array_sum($discountvariant);
        //     $poindisc        = array_sum($discountpoin);

        //     // Discount Setup
        //     $discount[] = [
        //         'trxdisc'       => $transactiondisc,
        //         'variantdis'    => $variantdisc,
        //         'poindisc'      => $poindisc,
        //     ];

        //     $totaltrxdisc = array_sum(array_column($discount, 'trxdisc'));
        //     $totalvardisc = array_sum(array_column($discount, 'variantdis'));
        //     $totalpoindisc = array_sum(array_column($discount, 'poindisc'));

        //     // total discount
        //     // $alldisc = (int)$totaltrxdisc + (int)$totalvardisc + (int)$totalpoindisc;
        //     $alldisc    = $totaldiscount;

        //     $date1 = date('Y-m-d', strtotime($startdate));
        //     $date2 = date('Y-m-d', strtotime($startdate));
        //     $day1 = date_create($date1);
        //     $day2 = date_create($date2);
        //     $interval = date_diff($day1, $day2)->format("%a");

        //     // Profit Calculation
        //     // $modalprice     = array_sum(array_column($omsetcalculation, 'modalprice'));
        //     // $omsetbaru      = array_sum(array_column($omsetcalculation, 'omset'));
        //     // $basicprice     = array_sum(array_column($omsetcalculation, 'basicprice'));
        //     $modalprice     = $totalmodal;
        //     $basicprice     = $totaldasar;
        //     $omsetbaru      = (int)$totalsales + (int)$totaldiscount + (int)$totalredpoin;
        //     $profitvalue    = $omsetbaru - $modalprice;
        // } else {

        //     $outlet = $OutletModel->find($this->data['outletPick']);
        //     $outletname = $outlet['name'];
        //     $address = $outlet['address'];
        //     $date1 = date('Y-m-d', strtotime($startdate));
        //     $date2 = date('Y-m-d', strtotime($startdate));
        //     $day1 = date_create($date1);
        //     $day2 = date_create($date2);

        //     // Profit Calculation
        //     $totalsales = 0;
        //     $alldisc = 0;
        //     $modalprice     = 0;
        //     $basicprice     = 0;
        //     $omsetbaru      = 0;
        //     $profitvalue    = 0;
        // }

        // Set Cash In Cash Out
        // $cashin  = [];
        // $cashout = [];
        // foreach ($cash as $cas) {
        //     if ($cas['type'] === "0") {
        //         $cashin[] = $cas['qty'];
        //     } elseif ($cas['type'] !== "0") {
        //         $cashout[] = $cas['qty'];
        //     }
        // }
        // $casin = array_sum($cashin);
        // $casout = array_sum($cashout);

        foreach ($transaction as $trx) {
            $trxdetails  = $TrxdetailModel->where('transactionid', $trx['id'])->find();

            if (!empty($trx['discvalue'])) {
                // if ($trx['disctype'] == "0") {
                //     $discounttrx[]  = $trx['discvalue'];
                // } else {
                //     $discounttrxpersen[]  = (int)$trx['value'] * ((int)$trx['discvalue'] / 100);
                // }
                $discounttrx[]  = $trx['discvalue'];
            }

            $discountpoin[]             = $trx['pointused'];
            $memberdisc[]               = $trx['memberdisc'];

            foreach ($trxdetails as $trxdetail) {
                // if ($trx['disctype'] === "0") {
                //     $discounttrx[]          = $trx['discvalue'];
                // }
                // if ($trx['disctype'] !== "0") {
                //     $sub =  ((int)$trxdetail['value'] * (int)$trxdetail['qty']);
                //     $discounttrxpersen[]    =  ((int)$trx['discvalue'] / 100) * (int)$sub;
                // }
                // $discountvariant[]          = $trxdetail['discvar'];

                // Transaction Detail Discount Variant
                if ($trxdetail['discvar'] != 0) {
                    $discountvariant[]      = $trxdetail['discvar'];
                }

                // Transaction Detail Discount Global
                if ($trxdetail['globaldisc'] != '0') {
                    $discountglobal[]       = $trxdetail['globaldisc'];
                }

                // Data Variant
                $variantsdata       = $VariantModel->find($trxdetail['variantid']);

                if (!empty($variantsdata)) {
                    $productsdata   = $ProductModel->find($variantsdata['productid']);

                    // if (!empty($productsdata)) {
                    //     // Transaction Detail Discount Variant
                    //     if ($trxdetail['discvar'] != '0') {
                    //         $discountvariant[]      = $trxdetail['discvar'];
                    //     }
                    //     if ($trxdetail['globaldisc'] != '0') {
                    //         $discountglobal[]       = $trxdetail['globaldisc'];
                    //     }
                    // } else {
                    //     // Transaction Detail Discount Variant
                    //     // if ($trxdetail['discvar'] != '0') {
                    //         $discountvariant[]      = 0;
                    //         // $discountglobal[]       = 0;
                    //     // }
                    //     // if ($trxdetail['globaldisc'] != '0') {
                    //         $discountglobal[]       = 0;
                    //     // }
                    // }
                } else {
                    $productsdata   = '';
                }

                // Data Bundle
                $bundlesdata    = $BundleModel->find($trxdetail['bundleid']);

                // if (!empty($bundlesdata)) {
                //     // Transaction Detail Discount Variant
                //     if ($trxdetail['discvar'] != '0') {
                //         $discountvariant[]      = $trxdetail['discvar'];
                //     }
                //     if ($trxdetail['globaldisc'] != '0') {
                //         $discountglobal[]       = $trxdetail['globaldisc'];
                //     }
                // } else {
                //     // Transaction Detail Discount Variant
                //     // if ($trxdetail['discvar'] != '0') {
                //         $discountvariant[]      = 0;
                //     // }
                //     // if ($trxdetail['globaldisc'] != '0') {
                //         $discountglobal[]       = 0;
                //     // }
                // }
            }
        }

        // Getting Discount Data
        $transactiondisc    = (int)(array_sum($discounttrx)) + (int)(array_sum($memberdisc));
        $variantdisc        = array_sum($discountvariant);
        $globaldisc         = array_sum($discountglobal);

        // Total Point Used
        $poindisc           = array_sum($discountpoin);

        // Getting Margin  Data
        $marginmodalsum     = array_sum($marginmodals);
        $margindasarsum     = array_sum($margindasars);

        // Total Discount
        $alldisc            = (Int)$globaldisc + (Int)$variantdisc + (Int)$transactiondisc;

        // Total Sales
        $salesresult        = array_sum(array_column($transaction, 'value'));

        // Gross Sales
        $grossales          = (Int)$salesresult + (Int)$variantdisc + (Int)$globaldisc + (Int)$transactiondisc + (Int)$poindisc;

        // Profit Calculation
        $profitvalue        = (Int)$marginmodalsum - (Int)$transactiondisc;

        // Cashin and Cash Out
        $cashin     = [];
        $cashout    = [];
        foreach ($trxothers as $trxother) {
            if ($trxother['type'] === "0") {
                $cashin[] = $trxother['qty'];
            } else {
                $cashout[] = $trxother['qty'];
            }
        }
        $casin      = array_sum($cashin);
        $casout     = array_sum($cashout);

        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=sales$date1-$date2.xls");

        echo '<style type="text/css">

        </style>';
        echo '<table  style="width: 30%;">';
        echo '<tr>';
        echo '<th colspan="2" style="align-text:center;">Ringkasan Penjualan</th>';
        echo '</tr>';
        echo '<tr>';
        echo '<th colspan="2" style="align-text:center;">' . $outletname . '</th>';
        echo '</tr>';
        echo '<tr>';
        echo '<th colspan="2" style="align-text:center;">' . $address . '</th>';
        echo '</tr>';
        echo '<tr>';
        echo '<th colspan="2" style="align-text:center;">' . $date1 . ' - ' . $date2 . '</th>';
        echo '</tr>';
        echo '<tr>';
        echo '<th colspan="2" style="align-text:center;"></th>';
        echo '</tr>';

        echo '<tr >';
        echo '<th style="text-align: left;">Penjualan</th>';
        echo '<td style="text-align: right;">' . $salesresult . '</td>';
        echo '</tr>';
        echo '<tr>';
        echo '<th style="text-align: left;">Diskon</th>';
        echo '<td style="text-align: right;">' . $alldisc . '</td>';
        echo '</tr>';
        echo '<tr>';
        echo '<th style="text-align: left;">Total Omset</th>';
        echo '<td style="text-align: right;">' . $grossales . '</td>';
        echo '</tr>';
        echo '<tr>';
        echo '<th style="text-align: left;">Harga Modal</th>';
        echo '<td style="text-align: right;">' . $marginmodalsum . '</td>';
        echo '</tr>';
        echo '<tr>';
        echo '<th style="text-align: left;">Harga Dasar</th>';
        echo '<td style="text-align: right;">' . $margindasarsum . '</td>';
        echo '</tr>';
        echo '<tr>';
        echo '<th style="text-align: left;">Kas Masuk</th>';
        echo '<td style="text-align: right;">' . $casin . '</td>';
        echo '</tr>';
        echo '<tr>';
        echo '<th style="text-align: left;">Kas Keluar</th>';
        echo '<td style="text-align: right;">' . $casout . '</td>';
        echo '</tr>';
        echo '<tr>';
        echo '<th style="text-align: left;">Keuntungan</th>';
        echo '<td style="text-align: right; font-family: arial, sans-serif; font-weight: bold;">' . $profitvalue . '</td>';
        echo '</tr>';
        echo '</table>';
    }

    public function profit()
    {
        // Calling Models
        $TransactionModel       = new TransactionModel;
        $TrxdetailModel         = new TrxdetailModel;
        $VariantModel           = new VariantModel;
        $ProductModel           = new ProductModel;
        $BundleModel            = new BundleModel;

        $input = $this->request->getGet('daterange');

        if (!empty($input)) {
            $daterange = explode(' - ', $input);
            $startdate = strtotime($daterange[0]);
            $enddate = strtotime($daterange[1]);
        } else {
            $startdate  = strtotime(date('Y-m-1' . ' 00:00:00'));
            $enddate    = strtotime(date('Y-m-t' . ' 23:59:59'));
        }

        $transactions   = array();
        $transactionarr = array();
        for ($date = $startdate; $date <= $enddate; $date += (86400)) {
            if ($this->data['outletPick'] === null) {
                $transaction = $TransactionModel->where('date >=', date('Y-m-d 00:00:00', $date))->where('date <=', date('Y-m-d 23:59:59', $date))->find();
            } else {
                $transaction = $TransactionModel->where('date >=', date('Y-m-d 00:00:00', $date))->where('date <=', date('Y-m-d 23:59:59', $date))->where('outletid', $this->data['outletPick'])->find();
            }
            // $variants    = $VariantModel->findAll();

            // $summary = array_sum(array_column($transaction, 'value'));
            $marginmodals   = array();
            $margindasars   = array();
            $discount       = array();

            foreach ($transaction as $trx) {
                $trxdetails     = $TrxdetailModel->where('transactionid', $trx['id'])->find();
                
                if ($trx['discvalue'] != '0') {
                    $discount[]   = (int)$trx['discvalue'];
                } else {
                    $discount[]   = 0;
                }

                if ($trx['memberdisc'] != '0') {
                    $discount[]   = (int)$trx['memberdisc'];
                } else {
                    $discount[]   = 0;
                }

                foreach ($trxdetails as $trxdetail) {
                    // Transaction Detail Margin Modal
                    $marginmodals[] = ((int)$trxdetail['marginmodal'] * (int)$trxdetail['qty']);

                    // Transaction Detail Margin Dasar
                    $margindasars[] = ((int)$trxdetail['margindasar'] * (int)$trxdetail['qty']);

                    // Data Variant
                    $variantsdata       = $VariantModel->find($trxdetail['variantid']);

                    if (!empty($variantsdata)) {
                        $productsdata   = $ProductModel->find($variantsdata['productid']);

                        // if (!empty($productsdata)) {
                        //     // Transaction Detail Margin Modal
                        //     $marginmodals[] = ((int)$trxdetail['marginmodal'] * (int)$trxdetail['qty']);

                        //     // Transaction Detail Margin Dasar
                        //     $margindasars[] = ((int)$trxdetail['margindasar'] * (int)$trxdetail['qty']);
                        // } else {
                        //     // Transaction Detail Margin Modal
                        //     $marginmodals[] = 0;

                        //     // Transaction Detail Margin Dasar
                        //     $margindasars[] = 0;
                        // }
                    } else {
                        $productsdata   = '';
                    }

                    // Data Bundle
                    $bundlesdata    = $BundleModel->find($trxdetail['bundleid']);

                    // if (!empty($bundlesdata)) {
                    //     // Transaction Detail Margin Modal
                    //     $marginmodals[] = ((int)$trxdetail['marginmodal'] * (int)$trxdetail['qty']);

                    //     // Transaction Detail Margin Dasar
                    //     $margindasars[] = ((int)$trxdetail['margindasar'] * (int)$trxdetail['qty']);
                    // } else {
                    //     // Transaction Detail Margin Modal
                    //     $marginmodals[] = 0;

                    //     // Transaction Detail Margin Dasar
                    //     $margindasars[] = 0;
                    // }
                }
            }

            $totaldisc      = array_sum($discount);
            $marginmodalsum = array_sum($marginmodals);
            $margindasarsum = array_sum($margindasars);
            $transactions[] = [
                'date'      => date('d/m/y', $date),
                // 'value'     => $summary,
                'modal'     => (Int)$marginmodalsum - (Int)$totaldisc,
                'dasar'     => (Int)$margindasarsum - (Int)$totaldisc,
            ];
        }

        $transactionarr[] = $transactions;

        $keuntunganmodal = array_sum(array_column($transactions, 'modal'));
        $keuntungandasar = array_sum(array_column($transactions, 'dasar'));
        // $trxvalue        = array_sum(array_column($transactions, 'value'));

        // for ($date = $startdate; $date <= $enddate; $date += (86400)) {
        //     if ($this->data['outletPick'] === null) {
        //         // if ($startdate === $enddate) {
        //             $transaction = $TransactionModel->where('date >=', date('Y-m-d 00:00:00', $date))->where('date <=', date('Y-m-d 23:59:59', $date))->find();
        //         // } else {
        //         //     $transaction = $TransactionModel->where('date >=', date('Y-m-d  00:00:00', $date))->where('date <=', date('Y-m-d  23:59:59', $date))->find();
        //         // }
        //     } else {
        //         // if ($startdate === $enddate) {
        //             $transaction = $TransactionModel->where('date >=', date('Y-m-d 00:00:00', $date))->where('date <=', date('Y-m-d 23:59:59', $date))->where('outletid', $this->data['outletPick'])->find();
        //         // } else {
        //         //     $transaction = $TransactionModel->where('date >=', date('Y-m-d  00:00:00', $date))->where('date <=', date('Y-m-d  23:59:59', $date))->where('outletid', $this->data['outletPick'])->find();
        //         // }
        //     }
        //     $trxdetails  = $TrxdetailModel->findAll();
        //     $variants    = $VariantModel->findAll();

        //     $summary = array_sum(array_column($transaction, 'value'));
        //     $marginmodals = array();
        //     $margindasars = array();
        //     // dd($transaction);

        //     foreach ($transaction as $trx) {
        //         foreach ($trxdetails as $trxdetail) {
        //             if ($trx['id'] === $trxdetail['transactionid']) {
        //                 $marginmodal = (int)$trxdetail['marginmodal'] * (int)$trxdetail['qty'];
        //                 $margindasar = (int)$trxdetail['margindasar'] * (int)$trxdetail['qty'];
        //                 $marginmodals[] = $marginmodal;
        //                 $margindasars[] = $margindasar;
        //             }
        //         }
        //     }

        //     $marginmodalsum = array_sum($marginmodals);
        //     $margindasarsum = array_sum($margindasars);

        //     $transactions[] = [
        //         'date'      => date('d/m/y', $date),
        //         'value'     => $summary,
        //         'modal'     => $marginmodalsum,
        //         'dasar'     => $margindasarsum,
        //     ];
        // }

        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=profit.xls");

        // export
        echo '<table>';
        echo '<thead>';
        echo '<tr>';
        echo '<th>Keuntungan Modal</th>';
        echo '<th>Keuntungan Dasar</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        echo '<tr>';
        echo '<td>' . $keuntunganmodal . '</td>';
        echo '<td>' . $keuntungandasar . '</td>';
        echo '</tr>';
        echo '</tbody>';
        echo '</table>';
    }

    public function payment()
    {
        $db                     = \Config\Database::connect();
        $PaymentModel           = new PaymentModel;
        $TrxpaymentModel        = new TrxpaymentModel;
        $TransactionModel       = new TransactionModel;
        $OutletModel            = new OutletModel;

        if ($this->data['outletPick'] != null) {
            $input = $this->request->getGet('daterange');

            if (!empty($input)) {
                $daterange = explode(' - ', $input);
                $startdate = $daterange[0];
                $enddate = $daterange[1];
            } else {
                $startdate  = date('Y-m-1' . ' 00:00:00');
                $enddate    = date('Y-m-t' . ' 23:59:59');
            }

            // $day1 = date_create($startdate);
            // $day2 = date_create($enddate);

            // $payments = $PaymentModel->findAll();
            // $trxpayments = $TrxpaymentModel->findAll();
            // // if ($startdate === $enddate) {
            //     $transactions = $TransactionModel->where('outletid', $this->data['outletPick'])->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->find();
            // // } else {
            // //     $transactions = $TransactionModel->where('outletid', $this->data['outletPick'])->where('date >=', $startdate . '00:00:00')->where('date <=', $enddate . '23:59:59')->find();
            // // }
            // $outlets = $OutletModel->find($this->data['outletPick']);
            // $outletname = $outlets['name'];
            // $adress = $outlets['address'];
            // $pay = array();
            // foreach ($payments as $payment) {
            //     $qty = array();
            //     foreach ($trxpayments as $trxpayment) {
            //         foreach ($transactions as $transaction) {
            //             if (($trxpayment['paymentid'] === $payment['id']) && ($trxpayment['transactionid'] === $transaction['id'])) {
            //                 $qty[] = $trxpayment['value'];
            //             }
            //         }
            //     }
            //     $pay[] = [
            //         'pvalue'    => array_sum($qty),
            //         'pqty'      => count($qty),
            //         'name'      => $payment['name']
            //     ];
            // }

            // $totalvalue = array_sum(array_column($pay, 'pvalue'));
            // $totalqty = array_sum(array_column($pay, 'pqty'));

            // Transaction Data
            $transactiondata    = array();
            $outlets            = $OutletModel->find($this->data['outletPick']);
            $outletname         = $outlets['name'];
            $adress             = $outlets['address'];

            $transactions       = $TransactionModel->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->where('outletid', $this->data['outletPick'])->find();

            if (!empty($inputsearch)) {
                $payments   = $PaymentModel->like('name', $inputsearch)->orderBy('id', 'DESC')->where('outletid', $this->data['outletPick'])->find();
            } else {
                $payments   = $PaymentModel->orderBy('id', 'DESC')->where('outletid', $this->data['outletPick'])->find();
            }

            foreach ($payments as $payment) {
                $transactiondata[$payment['id']]['name']    = $payment['name'];
                $transactiondata[0]['name']                 = 'Debt';
                $trxtotal           = array();
                $trxvalue           = array();
                $debttotal          = array();
                $debtvalue          = array();
                if (!empty($transactions)) {
                    foreach ($transactions as $trx) {
                        $trxpayments    = $TrxpaymentModel->where('transactionid', $trx['id'])->where('paymentid', $payment['id'])->find();
                        $debtpayments   = $TrxpaymentModel->where('transactionid', $trx['id'])->where('paymentid', '0')->find();
                        if (!empty($trxpayments)) {
                            foreach ($trxpayments as $trxpayment) {
                                $trxtotal[] = $trxpayment['id'];
                                $trxvalue[] = $trxpayment['value'];
                            }
                        }
                        if (!empty($debtpayments)) {
                            foreach ($debtpayments as $debtpayment) {
                                $debttotal[] = $debtpayment['id'];
                                $debtvalue[] = $debtpayment['value'];
                            }
                        }
                    }
                } else {
                    $trxpayments    = [];
                    $debtpayments   = [];
                    $trxtotal[]     = [];
                    $trxvalue[]     = [];
                    $debttotal[]    = [];
                    $debtvalue[]    = [];
                }
                $transactiondata[$payment['id']]['qty']         = count($trxtotal);
                $transactiondata[$payment['id']]['value']       = array_sum($trxvalue);
                $transactiondata[0]['qty']                      = count($debttotal);
                $transactiondata[0]['value']                    = array_sum($debtvalue);
            }

            $totalvalue = array_sum(array_column($transactiondata, 'value'));
            $totalqty = array_sum(array_column($transactiondata, 'qty'));

            header("Content-type: application/vnd-ms-excel");
            header("Content-Disposition: attachment; filename=Laporan Pembayaran $startdate-$enddate.xls");
            echo '<style type="text/css">
            caption {
                font-family: arial, sans-serif;
                text-align: center
            }
            </style>';

            // export
            echo '<table>';
            echo '<thead>';
            echo '<tr>';
            echo '<th colspan="3" style="align-text:center;">Ringkasan Pembayaran</th>';
            echo '</tr>';
            echo '<tr>';
            echo '<th colspan="3" style="align-text:center;">' . $outletname . '</th>';
            echo '</tr>';
            echo '<tr>';
            echo '<th colspan="3" style="align-text:center;">' . $adress . '</th>';
            echo '</tr>';
            echo '<tr>';
            echo '<th colspan="3" style="align-text:center;">' . $startdate . ' - ' . $enddate . '</th>';
            echo '</tr>';
            echo '<tr>';
            echo '<th colspan="3" style="align-text:center;"></th>';
            echo '</tr>';
            echo '<tr>';
            echo '<th>Nama</th>';
            echo '<th>Jumlah Transaksi</th>';
            echo '<th>Nominal Transaksi</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            foreach ($transactiondata as $payment) {
                echo '<tr>';
                echo '<td>' . $payment['name'] . '</td>';
                echo '<td>' . $payment['qty'] . '</td>';
                echo '<td>' . $payment['value'] . '</td>';
                echo '</tr>';
            }

            echo '<tr>';
            echo '<th style="align-text:left; font-family: arial, sans-serif; font-weight: bold;">Total</th>';
            echo '<td style="align-text:left; font-family: arial, sans-serif; font-weight: bold;">' . $totalqty . '</td>';
            echo '<td style="align-text:left; font-family: arial, sans-serif; font-weight: bold;">' . $totalvalue . '</td>';
            echo '</tr>';
            echo '</tbody>';
            echo '</table>';
        } else {
            return redirect()->to('');
        }
    }

    public function employe()
    {

        $db                 = \Config\Database::connect();
        $TransactionModel   = new TransactionModel;
        $UserModel          = new UserModel;
        $UserGroupModel     = new GroupUserModel;
        $GroupModel         = new GroupModel;
        $OutletModel        = new OutletModel;

        // Populating Data 
        $admin          = $UserModel->findAll();
        // $usergroups     = $UserGroupModel->findAll();
        // $groups         = $GroupModel->findAll();

        $input = $this->request->getGet('daterange');

        if (!empty($input)) {
            $daterange = explode(' - ', $input);
            $startdate = $daterange[0];
            $enddate = $daterange[1];
        } else {
            $startdate  = date('Y-m-1' . ' 00:00:00');
            $enddate    = date('Y-m-t' . ' 23:59:59');
        }

        $admins                 = $UserModel->findAll();
        $employeedata           = [];
        foreach ($admins as $admin) {
            $employeedata[$admin->id]['name']  = $admin->username;
            $usergroups         = $UserGroupModel->where('user_id', $admin->id)->find();
            foreach ($usergroups as $usergroup) {
                $groups         = $GroupModel->find($usergroup['group_id']);
                $employeedata[$admin->id]['role']  = $groups->name;
            }

            if ($this->data['outletPick'] === null) {
                $transactions   = $TransactionModel->where('userid', $admin->id)->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->find();
                $addres = "All Outlets";
                $outletname = "58vapehouse";
            } else {
                $transactions   = $TransactionModel->where('userid', $admin->id)->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->where('outletid', $this->data['outletPick'])->find();
                $outlets        = $OutletModel->find($this->data['outletPick']);
                $addres         = $outlets['address'];
                $outletname     = $outlets['name'];
            }

            $trxvalue           = [];
            if (!empty($transactions)) {
                foreach ($transactions as $trx) {
                    $trxvalue[] = $trx['value'];
                }
            }
            $employeedata[$admin->id]['value']  = array_sum($trxvalue);
        }

        // $addres = '';
        // if ($this->data['outletPick'] === null) {
        //     // if ($startdate === $enddate) {
        //         $transactions = $TransactionModel->where('date >=', $startdate . " 00:00:00")->where('date <=', $enddate . " 23:59:59")->find();
        //     // } else {
        //     //     $transactions = $TransactionModel->where('date >=', $startdate)->where('date <=', $enddate)->find();
        //     // }
        //     $addres = "All Outlets";
        //     $outletname = "58vapehouse";
        // } else {
        //     // if ($startdate === $enddate) {
        //         $transactions = $TransactionModel->where('date >=', $startdate . " 00:00:00")->where('date <=', $enddate . " 23:59:59")->where("outletid", $this->data['outletPick'])->find();
        //     // } else {
        //     //     $transactions = $TransactionModel->where('date >=', $startdate)->where('date <=', $enddate)->where('outletid', $this->data['outletPick'])->find();
        //     // }
        //     $outlets = $OutletModel->find($this->data['outletPick']);
        //     $addres = $outlets['address'];
        //     $outletname = $outlets['name'];
        // }

        // $useradm = [];
        // foreach ($transactions as $transaction) {
        //     foreach ($admin as $adm) {
        //         if ($transaction['userid'] === $adm->id) {
        //             foreach ($usergroups as $userg) {
        //                 if ($adm->id === $userg['user_id']) {
        //                     foreach ($groups as $group) {
        //                         if ($userg['group_id'] === $group->id) {
        //                             $useradm[] = [
        //                                 'id'    => $adm->id,
        //                                 'value' => $transaction['value'],
        //                                 'name'  => $adm->username,
        //                                 'role'  => $group->name,
        //                             ];
        //                         }
        //                     }
        //                 }
        //             }
        //         }
        //     }
        // }

        // $val = array_sum(array_column($useradm, 'value'));

        // $produk = [];
        // foreach ($useradm as $username) {
        //     if (!isset($produk[$username['id']])) {
        //         $produk[$username['id']] = $username;
        //     } else {
        //         $produk[$username['id']]['value'] += $username['value'];
        //     }
        // }
        // $produk = array_values($produk);

        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=Laporan Pegawai $startdate-$enddate.xls");

        // export
        echo '<table>';
        echo '<thead>';
        echo '<tr>';
        echo '<th colspan="3" style="align-text:center;">Ringkasan Pegawai</th>';
        echo '</tr>';
        echo '<tr>';
        echo '<th colspan="3" style="align-text:center;">' . $outletname . '</th>';
        echo '</tr>';
        echo '<tr>';
        echo '<th colspan="3" style="align-text:center;">' . $addres . '</th>';
        echo '</tr>';
        echo '<tr>';
        echo '<th colspan="3" style="align-text:center;">' . $startdate . ' - ' . $enddate . '</th>';
        echo '</tr>';
        echo '<tr>';
        echo '<th colspan="3" style="align-text:center;"></th>';
        echo '</tr>';
        echo '<tr>';
        echo '<th>Nama</th>';
        echo '<th>Posisi</th>';
        echo '<th>Nominal Transaksi</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        foreach ($employeedata as $employe) {
            echo '<tr>';
            echo '<td>' . $employe['name'] . '</td>';
            echo '<td>' . $employe['role'] . '</td>';
            echo '<td>' . $employe['value'] . '</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
    }

    public function product()
    {
        // Calling models
        $TransactionModel   = new TransactionModel();
        $TrxdetailModel     = new TrxdetailModel();
        $ProductModel       = new ProductModel();
        $CategoryModel      = new CategoryModel();
        $VariantModel       = new VariantModel();
        $BundleModel        = new BundleModel();
        $OutletModel        = new OutletModel();

        $outlets    = $OutletModel->findAll();

        // Populating Data
        $input = $this->request->getVar('daterange');

        if (!empty($input)) {
            $daterange = explode(' - ', $input);
            $startdate = $daterange[0];
            $enddate = $daterange[1];
        } else {
            $startdate  = date('Y-m-1' . ' 00:00:00');
            $enddate    = date('Y-m-t' . ' 23:59:59');
        }

        // $day1 = date_create($startdate);
        // $day2 = date_create($enddate);

        $adress = [];
        if ($this->data['outletPick'] === null) {
            // if ($startdate === $enddate) {
            $transactions   = $TransactionModel->where('date >=', $startdate . " 00:00:00")->where('date <=', $enddate . " 23:59:59")->find();
            // } else {
            //     $transactions = $TransactionModel->where('date >=', $startdate)->where('date <=', $enddate)->find();
            // }
            $outletname = "All Outlets";
            $adress = "58vapehouse";
        } else {
            // if ($startdate === $enddate) {
            //     $transactions = $TransactionModel->where('date >=', $startdate)->where('date <=', $enddate)->find();
            // } else {
                // $transactions = $TransactionModel->where('outletid', $this->data['outletPick'])->where('date >=', $startdate . " 00:00:00")->where('date <=', $enddate . " 23:59:59")->find();
            $transactions   = $TransactionModel->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->where('outletid', $this->data['outletPick'])->find();
            // }
            $outlets        = $OutletModel->find($this->data['outletPick']);
            $outletname     = $outlets['name'];
            $adress         = $outlets['address'];
        }

        // $productval     = [];
        // $variantvalue   = [];
        // $variantval     = [];
        // $trxvar         = [];
        // $diskon         = [];
        // $productqty     = [];
        // $trxval         = [];
        // $bundleval      = [];

        // foreach ($transactions as $transaction) {
        //     $discounttrx = array();
        //     $discounttrxpersen = array();
        //     $discountvariant = array();
        //     $discountpoin = array();
        //     foreach ($trxdetails as $trxdetail) {
        //         foreach ($bundles as $bundle) {
        //             if ($transaction['id'] === $trxdetail['transactionid'] && $bundle['id'] === $trxdetail['bundleid']) {
        //                 $bundleval[]   = [
        //                     'id'    => $bundle['id'],
        //                     'name'  => $bundle['name'],
        //                     'value' => $trxdetail['value'],
        //                 ];
        //             }
        //         }
        //         if ($transaction['id'] === $trxdetail['transactionid']) {

        //             if ($transaction['disctype'] === "0") {

        //                 $discounttrx[]          = $transaction['discvalue'];
        //             }
        //             if ($transaction['disctype'] !== "0") {

        //                 $sub = ($trxdetail['value']) * $trxdetail['qty'];
        //                 $discounttrxpersen[]    = (int)$sub * ((int)$transaction['discvalue'] / 100);
        //             }
        //             $discountvariant[]          = $trxdetail['discvar'];

        //             $discountpoin[]             = $transaction['pointused'];

        //             foreach ($products as $product) {
        //                 foreach ($variants as $variant) {
        //                     if (($variant['id'] === $trxdetail['variantid']) && ($variant['productid'] === $product['id'])) {
        //                         foreach ($products as $product) {
        //                             if ($variant['productid'] === $product['id']) {
        //                                 $productval[] = $product['name'];
        //                                 foreach ($category as $cat) {
        //                                     if ($product['catid'] === $cat['id']) {
        //                                         $variantvalue[] = [
        //                                             'id'            => $product['id'],
        //                                             'trxid'         => $transaction['id'],
        //                                             'product'       => $product['name'],
        //                                             'category'      => $cat['name'],
        //                                             'value'         => $trxdetail['value'] * $trxdetail['qty'],
        //                                             'gross'         => $trxdetail['value'] + $trxdetail['discvar'] * $trxdetail['qty'],
        //                                             'qty'           => $trxdetail['qty'],
        //                                         ];
        //                                     }
        //                                 }
        //                             }
        //                         }
        //                     }
        //                 }
        //             }
        //         }
        //     }

        //     $transactiondisc = array_sum($discounttrx) +  array_sum($discounttrxpersen);
        //     $variantdisc     = array_sum($discountvariant);
        //     $poindisc        = array_sum($discountpoin);

        //     $diskon[] = [
        //         'id'            => $transaction['id'],
        //         'trxdisc'       => $transactiondisc,
        //         'value'         => $transaction['value'],
        //         'variantdis'    => $variantdisc,
        //         'poindisc'      => $poindisc,
        //     ];
        // }

        // $bundletotal = array_sum(array_column($bundleval, 'value'));

        // $produk = [];
        // foreach ($variantvalue as $vars) {
        //     if (!isset($produk[$vars['id'] . $vars['product']])) {
        //         $produk[$vars['id'] . $vars['product']] = $vars;
        //     } else {
        //         $produk[$vars['id'] . $vars['product']]['value'] += $vars['value'];
        //         $produk[$vars['id'] . $vars['product']]['qty'] += $vars['qty'];
        //         $produk[$vars['id'] . $vars['product']]['gross'] += $vars['gross'];
        //     }
        // }
        // $produk = array_values($produk);

        // // disc calculation
        // $trxdisc    = array_sum(array_column($diskon, 'trxdisc'));
        // $poindisc   = array_sum(array_column($diskon, 'poindisc'));
        // $vardisc    = array_sum(array_column($diskon, 'variantdis'));
        // $proval     = array_sum(array_column($produk, 'value'));

        // // if want to get net sales with trx disc and without bundle value
        // $netsales = $proval - ($trxdisc + $poindisc);

        // // Total Stock
        // $stoktotal = array_sum(array_column($produk, 'qty'));

        // // Total Sales Without trx disc, bundle & poin disc
        // $salestotal = array_sum(array_column($produk, 'value'));

        // // Total Gross
        // $grosstotal = array_sum(array_column($produk, 'gross'));

        // dd($transactiondata);
        // foreach ($transactiondata as $product => $value) {
        //     dd($value);
        // }
        
        $transactiondata    = [];
        $productsales       = [];
        $netval             = [];
        $grossval           = [];
        
        foreach ($transactions as $trx) {
            $trxdetails     = $TrxdetailModel->where('transactionid', $trx['id'])->where('bundleid', '0')->find();
            $totaltrxdet    = count($trxdetails);
    
            if ($trx['discvalue'] != null) {
                $discval   = round((int)$trx['discvalue'] / (int)$totaltrxdet);
            } else {
                $discval   = 0;
            }

            if ($trx['memberdisc'] != null) {
                $discmem   = round((int)$trx['memberdisc'] / (int)$totaltrxdet);
            } else {
                $discmem   = 0;
            }

            if ($trx['pointused'] != '0') {
                $discpoin   = round((int)$trx['pointused'] / (int)$totaltrxdet);
            } else {
                $discpoin   = 0;
            }
            
            if (!empty($trxdetails)) {
                foreach ($trxdetails as $trxdet) {
                    $variants       = $VariantModel->find($trxdet['variantid']);
                    
                    if (!empty($variants)) {
                        // $productid  = $variants['productid'];
                        // if (!empty($input['search'])) {
                        //     $products   = $ProductModel->where('name', $input['search'])->find($productid);
                        // } else {
                            $products   = $ProductModel->find($variants['productid']);
                        // }

                        if (!empty($products)) {
                            $transactiondata[$products['id']]['name']           = $products['name'];
                            $category   = $CategoryModel->find($products['catid']);

                            if (!empty($category)) {
                                $transactiondata[$products['id']]['category']    = $category['name'];
                            }
                            
                            // $transactiondata[$productid]['grossvalue']      = ((Int)$trxdet['value'] * (Int)$trxdet['qty']) + $trxdet['discvar'];
                            // $transactiondata[$productid]['netvalue']        = (((Int)$trxdet['value'] * (Int)$trxdet['qty'])) - (Int)$disc;
                            // $transactiondata[$productid]['qty']             = $trxdet['qty'];
                            $transactiondata[$products['id']]['qty'][]           = $trxdet['qty'];
                            $transactiondata[$products['id']]['netvalue'][]      = (((Int)$trxdet['value'] * (Int)$trxdet['qty'])) - ((Int)$discval + (Int)$discmem + (Int)$discpoin);
                            $transactiondata[$products['id']]['grossvalue'][]    = ((Int)$trxdet['value'] * (Int)$trxdet['qty']) + (Int)$trxdet['discvar'] + (Int)$trxdet['globaldisc'];

                            // $grossval[$products['id']][]     = ((Int)$trxdet['value'] * (Int)$trxdet['qty']) + $trxdet['discvar'];
                            // $netval[$products['id']][]       = (((Int)$trxdet['value'] * (Int)$trxdet['qty']));
                            // $productsales[$products['id']][] = $trxdet['qty'];
                        } else {
                            $category   = [];
                        }
                    } else {
                        $products   = [];
                        $productid  = '';
                        $category   = [];
                        $transactiondata[0]['name']         = 'Kategori / Produk / Variant Terhapus';
                        $transactiondata[0]['category']     = 'Kategori / Produk / Variant Terhapus';
                        $transactiondata[0]['qty'][]        = $trxdet['qty'];
                        $transactiondata[0]['netvalue'][]   = (((Int)$trxdet['value'] * (Int)$trxdet['qty'])) - ((Int)$discval + (Int)$discmem + (Int)$discpoin);
                        $transactiondata[0]['grossvalue'][] = ((Int)$trxdet['value'] * (Int)$trxdet['qty']) + (Int)$trxdet['discvar'] + (Int)$trxdet['globaldisc'];

                        // $grossval[]     = ((Int)$trxdet['value'] * (Int)$trxdet['qty']) + $trxdet['discvar'];
                        // $netval[]       = (((Int)$trxdet['value'] * (Int)$trxdet['qty']));
                        // $productsales[] = $trxdet['qty'];
                    }
                }
            } else {
                $variants   = [];
                $products   = [];
                $productid  = '';
                $category   = [];
            }
        }
        
        foreach ($transactiondata as $trxdata) {
            $productsales[] = array_sum($trxdata['qty']);
            $netval[] = array_sum($trxdata['netvalue']);
            $grossval[] = array_sum($trxdata['grossvalue']);
        }
        
        $totalsalesitem = array_sum($productsales);
        $totalnetsales  = array_sum($netval);
        $totalcatgross  = array_sum($grossval);
        array_multisort(array_column($transactiondata, 'qty'), SORT_DESC, $transactiondata);

        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=Laporan Penjualan Per Produk $startdate-$enddate.xls");

        // export
        echo '<table>';
            echo '<thead>';
                echo '<tr>';
                    echo '<th colspan="4" style="align-text:center;">Ringkasan Produk</th>';
                echo '</tr>';
                echo '<tr>';
                    echo '<th colspan="4" style="align-text:center;">' . $outletname . '</th>';
                echo '</tr>';
                echo '<tr>';
                    echo '<th colspan="4" style="align-text:center;">' . $adress . '</th>';
                echo '</tr>';
                echo '<tr>';
                    echo '<th colspan="4" style="align-text:center;">' . $startdate . ' - ' . $enddate . '</th>';
                echo '</tr>';
                echo '<tr>';
                    echo '<th colspan="4" style="align-text:center;"></th>';
                echo '</tr>';
                echo '<tr>';
                    echo '<th>SKU</th>';
                    echo '<th>Produk</th>';
                    echo '<th>Kategory</th>';
                    echo '<th>Jumlah Terjual</th>';
                    echo '<th>Nominal Transaksi</th>';
                echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
                foreach ($transactiondata as $product => $value) {
                    echo '<tr>';
                        echo '<td>' . $value['name'] . '</td>';
                        echo '<td>' . $value['category'] . '</td>';
                        echo '<td>' . array_sum($value['qty']) . '</td>';
                        echo '<td>' . array_sum($value['netvalue']) . '</td>';
                    echo '</tr>';
                }
            echo '</tbody>';
            echo '<tfoot>';
                echo '<tr>';
                    echo '<td colspan="2" style="text-align:center;font-weight:700;">Total</th>';
                    echo '<td style="font-weight:700;">' . $totalsalesitem . '</td>';
                    echo '<td style="font-weight:700;">' . $totalnetsales . '</td>';
                echo '</tr>';
            echo '</tfoot>';
        echo '</table>';
    }

    public function category()
    {
        // Calling models
        $OutletModel        = new OutletModel();
        $TransactionModel   = new TransactionModel();
        $TrxdetailModel     = new TrxdetailModel();
        $VariantModel       = new VariantModel();
        $ProductModel       = new ProductModel();
        $BundleModel        = new BundleModel();
        $BundledetailModel  = new BundledetailModel();
        $CategoryModel      = new CategoryModel();

        // Daterange Filter System
        $input = $this->request->getGet('daterange');

        if (!empty($input)) {
            $daterange = explode(' - ', $input);
            $startdate = $daterange[0];
            $enddate = $daterange[1];
        } else {
            $startdate  = date('Y-m-1' . ' 00:00:00');
            $enddate    = date('Y-m-t' . ' 23:59:59');
        }

        // =========================================== Rizal Code =========================================== //

        // // Populating Data
        // $trxdetails = $TrxdetailModel->findAll();
        // $products   = $ProductModel->findAll();
        // $category   = $CategoryModel->findAll();
        // $variants   = $VariantModel->findAll();
        // $brands     = $BrandModel->findAll();
        // $bundles    = $BundleModel->findAll();

        // $productval     = [];
        // $variantvalue   = [];
        // $variantval     = [];
        // $trxvar         = [];
        // $diskon         = [];
        // $productqty     = [];
        // $trxval         = [];
        // $bundleval      = [];

        // foreach ($transactions as $transaction) {
        //     $discounttrx = array();
        //     $discounttrxpersen = array();
        //     $discountvariant = array();
        //     $discountpoin = array();
        //     foreach ($trxdetails as $trxdetail) {
        //         foreach ($bundles as $bundle) {
        //             if ($transaction['id'] === $trxdetail['transactionid'] && $bundle['id'] === $trxdetail['bundleid']) {
        //                 $bundleval[]   = [
        //                     'id'    => $bundle['id'],
        //                     'name'  => $bundle['name'],
        //                     'value' => $trxdetail['value'],
        //                 ];
        //             }
        //         }
        //         if ($transaction['id'] === $trxdetail['transactionid']) {

        //             if ($transaction['disctype'] === "0") {

        //                 $discounttrx[]          = $transaction['discvalue'];
        //             }
        //             if ($transaction['disctype'] !== "0") {

        //                 $sub = ($trxdetail['value']) * $trxdetail['qty'];
        //                 $discounttrxpersen[]    = $sub * ($transaction['discvalue'] / 100);
        //             }
        //             $discountvariant[]          = $trxdetail['discvar'];

        //             $discountpoin[]             = $transaction['pointused'];

        //             foreach ($products as $product) {
        //                 foreach ($variants as $variant) {
        //                     if (($variant['id'] === $trxdetail['variantid']) && ($variant['productid'] === $product['id'])) {
        //                         foreach ($products as $product) {
        //                             if ($variant['productid'] === $product['id']) {
        //                                 $productval[] = $product['name'];
        //                                 foreach ($category as $cat) {
        //                                     if ($product['catid'] === $cat['id']) {
        //                                         $variantvalue[] = [
        //                                             'id'            => $cat['id'],
        //                                             'trxid'         => $transaction['id'],
        //                                             'product'       => $product['name'],
        //                                             'category'      => $cat['name'],
        //                                             'value'         => $trxdetail['value'] + $trxdetail['discvar'] * $trxdetail['qty'],
        //                                             'qty'           => $trxdetail['qty'],
        //                                         ];
        //                                     }
        //                                 }
        //                             }
        //                         }
        //                     }
        //                 }
        //             }
        //         }
        //     }

        //     $transactiondisc = array_sum($discounttrx) +  array_sum($discounttrxpersen);
        //     $variantdisc     = array_sum($discountvariant);
        //     $poindisc        = array_sum($discountpoin);

        //     $diskon[] = [
        //         'id'            => $transaction['id'],
        //         'trxdisc'       => $transactiondisc,
        //         'value'         => $transaction['value'],
        //         'variantdis'    => $variantdisc,
        //         'poindisc'      => $poindisc,
        //     ];
        // }

        // $bundletotal = array_sum(array_column($bundleval, 'value'));

        // $produk = [];
        // foreach ($variantvalue as $vars) {
        //     if (!isset($produk[$vars['id'] . $vars['product']])) {
        //         $produk[$vars['id'] . $vars['product']] = $vars;
        //     } else {
        //         $produk[$vars['id'] . $vars['product']]['value'] += $vars['value'];
        //         $produk[$vars['id'] . $vars['product']]['qty'] += $vars['qty'];
        //     }
        // }
        // $produk = array_values($produk);
        // // Total Stock
        // $stoktotal = array_sum(array_column($produk, 'qty'));

        // // Total Sales
        // $salestotal = array_sum(array_column($produk, 'value'));

        // // Total Gross
        // $grosstotal = array_sum(array_column($produk, 'value'));

        // Populating Data
        $addres = '';
        if ($this->data['outletPick'] === null) {
            $transactions = $TransactionModel->where('date >=', $startdate . " 00:00:00")->where('date <=', $enddate . " 23:59:59")->find();
            $addres = "All Outlets";
            $outletname = "58vapehouse";
        } else {
            $transactions = $TransactionModel->where('outletid', $this->data['outletPick'])->where('date >=', $startdate . " 00:00:00")->where('date <=', $enddate . " 23:59:59")->find();
            $outlets = $OutletModel->find($this->data['outletPick']);
            $addres = $outlets['address'];
            $outletname = $outlets['name'];
        }

        $transactiondata    = [];
        $productsales       = [];
        $netval             = [];
        $grossval           = [];
        
        foreach ($transactions as $trx) {
            $trxdetails     = $TrxdetailModel->where('transactionid', $trx['id'])->find();
            $totaltrxdet    = count($trxdetails);
    
            if ($trx['discvalue'] != null) {
                $discval   = round((int)$trx['discvalue'] / (int)$totaltrxdet);
            } else {
                $discval   = 0;
            }

            if ($trx['memberdisc'] != null) {
                $discmem   = round((int)$trx['memberdisc'] / (int)$totaltrxdet);
            } else {
                $discmem   = 0;
            }

            if ($trx['pointused'] != '0') {
                $discpoin   = round((int)$trx['pointused'] / (int)$totaltrxdet);
            } else {
                $discpoin   = 0;
            }
            
            if (!empty($trxdetails)) {
                foreach ($trxdetails as $trxdet) {
                    if (($trxdet['variantid'] != '0') && ($trxdet['bundleid'] == '0')) {
                        // Data Variant
                        $variants       = $VariantModel->find($trxdet['variantid']);
                        
                        if (!empty($variants)) {
                            $products   = $ProductModel->find($variants['productid']);
    
                            if (!empty($products)) {
                                // Search Filter
                                if (!empty($input['search'])) {
                                    $category   = $CategoryModel->where('name', $input['search'])->find($products['catid']);
                                } else {
                                    $category   = $CategoryModel->find($products['catid']);
                                }
    
                                if (!empty($category)) {
                                    $transactiondata[$category['id']]['name']               = $category['name'];
                                    $transactiondata[$category['id']]['qty'][]              = $trxdet['qty'];
                                    $transactiondata[$category['id']]['netvalue'][]         = (((Int)$trxdet['value'] * (Int)$trxdet['qty'])) - ((Int)$discval + (Int)$discmem + (Int)$discpoin);
                                    $transactiondata[$category['id']]['grossvalue'][]       = ((Int)$trxdet['value'] * (Int)$trxdet['qty']) + $trxdet['discvar'];

                                }
                            } else {
                                $category   = [];
                            }
                        } else {
                            $products   = [];
                            $category   = [];

                            $transactiondata[0]['name']                             = 'Kategori / Produk / Variant Terhapus';
                            $transactiondata[0]['qty'][]                            = $trxdet['qty'];
                            $transactiondata[0]['netvalue'][]                       = (((Int)$trxdet['value'] * (Int)$trxdet['qty'])) - ((Int)$discval + (Int)$discmem + (Int)$discpoin);
                            $transactiondata[0]['grossvalue'][]                     = ((Int)$trxdet['value'] * (Int)$trxdet['qty']) + $trxdet['discvar'];
                        }
                    }

                    if (($trxdet['variantid'] == '0') && ($trxdet['bundleid'] != '0')) {
                        // Data Bundle
                        $bundles        = $BundleModel->find($trxdet['bundleid']);

                        if (!empty($bundles)) {
                            // Data Bundle Detail
                            $bundledets     = $BundledetailModel->where('bundleid', $bundles['id'])->find();
    
                            if (!empty($bundledets)) {
                                foreach ($bundledets as $bundet) {
                                    // Data Variant
                                    $bundlevariants = $VariantModel->find($bundet['variantid']);
                                    
                                    if (!empty($bundlevariants)) {
                                        $bundleproduct   = $ProductModel->find($bundlevariants['productid']);
                
                                        if (!empty($bundleproduct)) {
                                            // Search Filter
                                            if (!empty($input['search'])) {
                                                $category   = $CategoryModel->where('name', $input['search'])->find($bundleproduct['catid']);
                                            } else {
                                                $category   = $CategoryModel->find($bundleproduct['catid']);
                                            }
                
                                            if (!empty($category)) {
                                                $transactiondata[$category['id']]['name']               = $category['name'];
                                                $transactiondata[$category['id']]['qty'][]              = $trxdet['qty'];
                                                $transactiondata[$category['id']]['netvalue'][]         = (((Int)$trxdet['value'] * (Int)$trxdet['qty'])) - ((Int)$discval + (Int)$discmem + (Int)$discpoin);
                                                $transactiondata[$category['id']]['grossvalue'][]       = ((Int)$trxdet['value'] * (Int)$trxdet['qty']) + $trxdet['discvar'];
            
                                            }
                                        } else {
                                            $category   = [];
                                        }
                                    } else {
                                        $bundleproduct   = [];
                                        $category   = [];
            
                                        $transactiondata[0]['name']                             = 'Kategori / Produk / Variant Terhapus';
                                        $transactiondata[0]['qty'][]                            = $trxdet['qty'];
                                        $transactiondata[0]['netvalue'][]                       = (((Int)$trxdet['value'] * (Int)$trxdet['qty'])) - ((Int)$discval + (Int)$discmem + (Int)$discpoin);
                                        $transactiondata[0]['grossvalue'][]                     = ((Int)$trxdet['value'] * (Int)$trxdet['qty']) + $trxdet['discvar'];
                                    }
                                }
                            }
                        } else {
                            $bundlevariants = [];
                            $bundleproduct  = [];
                            $category       = [];
                        }
                    }
                }
            } else {
                $bundles        = [];
                $bundledets     = [];
                $bundlevariants = [];
                $bundleproduct  = [];
                $category       = [];
            }
        }
        
        foreach ($transactiondata as $trxdata) {
            $productsales[] = array_sum($trxdata['qty']);
            $netval[]       = array_sum($trxdata['netvalue']);
            $grossval[]     = array_sum($trxdata['grossvalue']);
        }
        
        $totalsalesitem     = array_sum($productsales);
        $totalnetsales      = array_sum($netval);
        $totalcatgross      = array_sum($grossval);
        array_multisort(array_column($transactiondata, 'qty'), SORT_DESC, $transactiondata);

        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=Laporan Penjualan Per Kategori $startdate-$enddate.xls");

        // export
        echo '<table>';
        echo '<thead>';
        echo '<tr>';
        echo '<th colspan="4" style="align-text:center;">Ringkasan Kategori</th>';
        echo '</tr>';
        echo '<tr>';
        echo '<th colspan="4" style="align-text:center;">' . $outletname . '</th>';
        echo '</tr>';
        echo '<tr>';
        echo '<th colspan="4" style="align-text:center;">' . $addres . '</th>';
        echo '</tr>';
        echo '<tr>';
        echo '<th colspan="4" style="align-text:center;">' . $startdate . ' - ' . $enddate . '</th>';
        echo '</tr>';
        echo '<tr>';
        echo '<th colspan="4" style="align-text:center;"></th>';
        echo '</tr>';
        echo '<tr>';
        echo '<th>Nama</th>';
        echo '<th>Jumlah Penjualan</th>';
        echo '<th>Penjualan Kotor</th>';
        echo '<th>Total</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        foreach ($transactiondata as $key => $prod) {
            echo '<tr>';
                echo '<td>' . $prod['name'] . '</td>';
                echo '<td>' . array_sum($prod['qty']) . '</td>';
                echo '<td>' . array_sum($prod['netvalue']) . '</td>';
                echo '<td>' . array_sum($prod['grossvalue']) . '</td>';
            echo '</tr>';
        }

        // echo '<tr>';
        // echo '<th style="align-text:left; font-family: arial, sans-serif; font-weight: bold;">Jumlah</th>';
        // echo '<td style="align-text:left; font-family: arial, sans-serif; font-weight: bold;">' . $totalsalesitem . '</td>';
        // echo '<td style="align-text:left; font-family: arial, sans-serif; font-weight: bold;">' . $totalnetsales . '</td>';
        // echo '<td style="align-text:left; font-family: arial, sans-serif; font-weight: bold;">' . $totalcatgross . '</td>';
        // echo '</tr>';
        echo '</tbody>';
        echo '<tfoot>';
            echo '<tr>';
                echo '<td style="text-align:center;font-weight:700;">Total</th>';
                echo '<td style="font-weight:700;">' . $totalsalesitem . '</td>';
                echo '<td style="font-weight:700;">' . $totalnetsales . '</td>';
                echo '<td style="font-weight:700;">' . $totalcatgross . '</td>';
            echo '</tr>';
        echo '</tfoot>';
        echo '</table>';
    }

    public function stockcategory()
    {

        // Calling Data
        $ProductModel   = new ProductModel();
        $BrandModel     = new BrandModel();
        $VariantModel   = new VariantModel();
        $CategoryModel  = new CategoryModel();
        $StockModel     = new StockModel();
        $VariantModel   = new VariantModel();
        $OutletModel    = new OutletModel();

        $variants   = $VariantModel->findAll();
        $brands     = $BrandModel->findAll();
        $products   = $ProductModel->findAll();
        $category   = $CategoryModel->findAll();
        $variants   = $VariantModel->findAll();
        $outlets    = $OutletModel->findAll();

        if ($this->data['outletPick'] === null) {
            $stocks = $StockModel->findAll();
            foreach ($outlets as $outlet) {
                if ($outlet['id'] === $this->data['outletPick']) {
                    $outletname = "58VapeHouse";
                    $addres     = "All Outlets";
                }
            }
        } else {
            $stocks = $StockModel->where('outletid', $this->data['outletPick'])->find();
            foreach ($outlets as $outlet) {
                $outletname = $outlet['name'];
                $addres     = $outlet['address'];
            }
        }

        $productval = [];
        foreach ($stocks as $stock) {
            foreach ($variants as $variant) {
                foreach ($products as $product) {
                    foreach ($brands as $brand) {
                        foreach ($category as $cat) {
                            if ($product['catid'] === $cat['id'] && $product['brandid'] === $brand['id'] && $variant['productid'] == $product['id'] && $stock['variantid'] === $variant['id']) {
                                $productval[] = [
                                    'id'                => $product['catid'],
                                    'prodname'          => $product['name'],
                                    'catname'           => $cat['name'],
                                    'brandname'         => $brand['name'],
                                    'desc'              => $product['description'],
                                    'varname'           => $variant['name'],
                                    'hargamodal'        => $variant['hargamodal'],
                                    'hargajual'         => $variant['hargajual'],
                                    'hargarekomendasi'  => $variant['hargarekomendasi'],
                                    'stock'             => $stock['qty'],
                                    'whole'             => $variant['hargamodal'] * $stock['qty'],
                                ];
                            }
                        }
                    }
                }
            }
        }

        $produk = [];
        foreach ($productval as $vars) {
            if (!isset($produk[$vars['id'] . $vars['prodname']])) {
                $produk[$vars['id'] . $vars['prodname']] = $vars;
            } else {
                $produk[$vars['id'] . $vars['prodname']]['stock'] += $vars['stock'];
                $produk[$vars['id'] . $vars['prodname']]['whole'] += $vars['whole'];
            }
        }
        $produk = array_values($produk);

        $stock = array_sum(array_column($produk, 'stock'));
        $whole = array_sum(array_column($produk, 'whole'));

        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=stockcategory.xls");

        // export
        echo '<table>';
        echo '<thead>';
        echo '<tr>';
        echo '<th colspan="4" style="align-text:center;">Ringkasan Stok Kategori Produk</th>';
        echo '</tr>';
        echo '<tr>';
        echo '<th colspan="4" style="align-text:center;">' . $outletname . '</th>';
        echo '</tr>';
        echo '<tr>';
        echo '<th colspan="4" style="align-text:center;">' . $addres . '</th>';
        echo '</tr>';
        echo '<tr>';
        echo '<th colspan="4" style="align-text:center;"></th>';
        echo '</tr>';
        echo '<tr>';
        echo '<th>Nama</th>';
        echo '<th>Jumlah Penjualan</th>';
        echo '<th>Penjualan Kotor</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        foreach ($produk as $prod) {
            echo '<tr>';
            echo '<td>' . $prod['prodname'] . '</td>';
            echo '<td>' . $prod['stock'] . '</td>';
            echo '<td>' . $prod['whole'] . '</td>';
            echo '</tr>';
        }

        echo '<tr>';
        echo '<th style="align-text:left; font-family: arial, sans-serif; font-weight: bold;">Total</th>';
        echo '<td style="align-text:left; font-family: arial, sans-serif; font-weight: bold;">' . $stock . '</td>';
        echo '<td style="align-text:left; font-family: arial, sans-serif; font-weight: bold;">' . $whole . '</td>';
        echo '</tr>';
        echo '</tbody>';
        echo '</table>';
    }

    public function bundle()
    {
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
        $OutletModel        = new OutletModel();

        // initialize
        $input = $this->request->getGet();

        if (!empty($input['daterange'])) {
            $daterange = explode(' - ', $input['daterange']);
            $startdate = $daterange[0];
            $enddate = $daterange[1];
        } else {
            $startdate  = date('Y-m-1' . ' 00:00:00');
            $enddate    = date('Y-m-t' . ' 23:59:59');
        }

        $addres = '';
        if ($this->data['outletPick'] === null) {
            $transactions   = $TransactionModel->where('date >=', $startdate . " 00:00:00")->where('date <=', $enddate . " 23:59:59")->find();
            $addres         = "All Outlets";
            $outletname     = "58vapehouse";
        } else {
            $transactions   = $TransactionModel->where('outletid', $this->data['outletPick'])->where('date >=', $startdate . " 00:00:00")->where('date <=', $enddate . " 23:59:59")->find();
            $outlets        = $OutletModel->find($this->data['outletPick']);
            $addres         = $outlets['address'];
            $outletname     = $outlets['name'];
        }

        // ================== Rizal Code ====================== //
        // // Populating Data
        // $products   = $ProductModel->findAll();
        // $category   = $CategoryModel->findAll();
        // $variants   = $VariantModel->findAll();
        // $stocks     = $StockModel->findAll();
        // $bundles    = $BundleModel->findAll();
        // $bundets    = $BundledetailModel->findAll();
        // $trxdetails = $TrxdetailModel->findAll();
        // $bund = [];
        // foreach ($transactions as $transaction) {
        //     foreach ($trxdetails as $trxdetail) {
        //         foreach ($bundles as $bundle) {
        //             if ($trxdetail['transactionid'] === $transaction['id'] && $trxdetail['bundleid'] !== "0" && $bundle['id'] === $trxdetail['bundleid']) {
        //                 $bund[] = [
        //                     'id'    => $trxdetail['bundleid'],
        //                     'name'  => $bundle['name'],
        //                     'qty'   => $trxdetail['qty'],
        //                     'price' => $bundle['price'],
        //                     'value' => $trxdetail['qty'] * $bundle['price'],
        //                 ];
        //             }
        //         }
        //     }
        // }

        // // Sum Total Bundle Sold
        // $paket = [];
        // foreach ($bund as $bundval) {

        //     if (!isset($paket[$bundval['id'] . $bundval['name']])) {
        //         $paket[$bundval['id'] . $bundval['name']] = $bundval;
        //     } else {
        //         $paket[$bundval['id'] . $bundval['name']]['value'] += $bundval['value'];
        //         $paket[$bundval['id'] . $bundval['name']]['qty'] += $bundval['qty'];
        //     }
        // }

        // $paket = array_values($paket);

        $transactiondata    = [];
        $productsales       = [];
        $netval             = [];
        $grossval           = [];
            
        foreach ($transactions as $trx) {
            $trxdetails     = $TrxdetailModel->where('transactionid', $trx['id'])->where('variantid', '0')->find();
            // $totaltrxdet    = count($trxdetails);

            // if ($trx['discvalue'] != null) {
            //     $disc   = floor((int)$trx['discvalue'] / (int)$totaltrxdet);
            // } else {
            //     $disc   = 0;
            // }

            // if ($trx['memberdisc'] != null) {
            //     $disc   = floor((int)$trx['memberdisc'] / (int)$totaltrxdet);
            // } else {
            //     $disc   = 0;
            // }

            // if ($trx['pointused'] != '0') {
            //     $disc   = floor((int)$trx['pointused'] / (int)$totaltrxdet);
            // } else {
            //     $disc   = 0;
            // }
            
            if (!empty($trxdetails)) {
                foreach ($trxdetails as $trxdet) {
                    // Data Bundle
                    $bundles        = $BundleModel->find($trxdet['bundleid']);
                    if (!empty($bundles)) {
                        $transactiondata[$bundles['id']]['name']                = $bundles['name'];
                        $transactiondata[$bundles['id']]['qty'][]               = $trxdet['qty'];
                        $transactiondata[$bundles['id']]['value'][]             = (((Int)$trxdet['value'] * (Int)$trxdet['qty']));

                        // Data Bundle Detail
                        $bundledets     = $BundledetailModel->find($bundles['id']);

                        // Data Variant
                        if (!empty($bundledets)) {
                            $bundlevariants = $VariantModel->find($bundledets['variantid']);
                        } else {
                            $bundlevariants = [];
                        }
                    } else {
                        $bundledets     = [];
                        $bundlevariants = [];
                        
                        $transactiondata[0]['name']                             = 'Bundle Terhapus';
                        $transactiondata[0]['qty'][]                            = $trxdet['qty'];
                        $transactiondata[0]['value'][]                          = (((Int)$trxdet['value'] * (Int)$trxdet['qty']));
                    }
                }
            } else {
                $bundles            = [];
                $bundledets         = [];
                $bundlevariants     = [];
            }
        }
        
        foreach ($transactiondata as $trxdata) {
            $productsales[] = array_sum($trxdata['qty']);
            $netval[]       = array_sum($trxdata['value']);
            // $grossval[]     = array_sum($trxdata['grossvalue']);
        }
        
        $totalsalesitem     = array_sum($productsales);
        $totalnetsales      = array_sum($netval);
        // $totalcatgross      = array_sum($grossval);
        array_multisort(array_column($transactiondata, 'qty'), SORT_DESC, $transactiondata);

        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=Laporan Penjualan Per Bundle $startdate-$enddate.xls");

        // export
        echo '<table>';
        echo '<thead>';
            echo '<tr>';
                echo '<th colspan="3" style="align-text:center;">Ringkasan Bundle</th>';
            echo '</tr>';
            echo '<tr>';
                echo '<th colspan="3" style="align-text:center;">' . $outletname . '</th>';
            echo '</tr>';
            echo '<tr>';
                echo '<th colspan="3" style="align-text:center;">' . $addres . '</th>';
            echo '</tr>';
            echo '<tr>';
                echo '<th colspan="3" style="align-text:center;">' . $startdate . ' - ' . $enddate . '</th>';
            echo '</tr>';
            echo '<tr>';
                echo '<th colspan="3" style="align-text:center;"></th>';
            echo '</tr>';
            echo '<tr>';
                echo '<th>Nama</th>';
                echo '<th>Jumlah</th>';
                echo '<th>Harga Bundle</th>';
            echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        foreach ($transactiondata as $bundle) {
            echo '<tr>';
                echo '<td>' . $bundle['name'] . '</td>';
                echo '<td>' . array_sum($bundle['qty']) . '</td>';
                echo '<td>' . array_sum($bundle['value']) . '</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '<tfoot>';
            echo '<tr>';
                echo '<td style="text-align:center;font-weight:700;">Total</th>';
                echo '<td style="font-weight:700;">' . $totalsalesitem . '</td>';
                echo '<td style="font-weight:700;">' . $totalnetsales . '</td>';
                // echo '<td style="font-weight:700;">' . $totalcatgross . '</td>';
            echo '</tr>';
        echo '</tfoot>';
        echo '</table>';
    }

    public function diskon()
    {
        // Calling Models
        $TransactionModel       = new TransactionModel;
        $TrxdetailModel         = new TrxdetailModel;
        $GconfigModel           = new GconfigModel;
        $OutletModel            = new OutletModel;

        $input = $this->request->getGet('daterange');

        if (!empty($input)) {
            $daterange = explode(' - ', $input);
            $startdate = $daterange[0];
            $enddate = $daterange[1];
        } else {
            $startdate  = date('Y-m-1' . ' 00:00:00');
            $enddate    = date('Y-m-t' . ' 23:59:59');
        }

        $addres = '';
        if ($this->data['outletPick'] === null) {
            $transaction    = $TransactionModel->where('date >=', $startdate . " 00:00:00")->where('date <=', $enddate . " 23:59:59")->find();
            $addres         = "All Outlets";
            $outletname     = "58vapehouse";
        } else {
            $transaction    = $TransactionModel->where('outletid', $this->data['outletPick'])->where('date >=', $startdate . " 00:00:00")->where('date <=', $enddate . " 23:59:59")->find();
            $outlets        = $OutletModel->find($this->data['outletPick']);
            $addres         = $outlets['address'];
            $outletname     = $outlets['name'];
        }
        
        // ======================================== Rizal Code ===================================== //
        // // Populating Data
        // $trxdetails             = $TrxdetailModel->findAll();
        // $Gconfig                = $GconfigModel->first();
        // $diskon = [];
        // foreach ($transaction as $trx) {
        //     $discounttrx = array();
        //     $discounttrxpersen = array();
        //     $discountvariant = array();
        //     $discountpoin = array();
        //     foreach ($trxdetails as $trxdetail) {
        //         if ($trx['id'] === $trxdetail['transactionid']) {
        //             if ($trx['disctype'] === "0") {
        //                 $discounttrx[]          = $trx['discvalue'];
        //             }
        //             if ($trx['disctype'] !== "0") {
        //                 $sub =  ($trxdetail['value'] * $trxdetail['qty']);
        //                 $discounttrxpersen[]    =  $sub * ($trx['discvalue'] / 100);
        //             }
        //             $discountvariant[]          = $trxdetail['discvar'];
        //             $discountpoin[]             = $trx['pointused'];
        //         }
        //     }

        //     $transactiondisc = array_sum($discounttrx) +  array_sum($discounttrxpersen);
        //     $variantdisc     = array_sum($discountvariant);
        //     $poindisc        = array_sum($discountpoin);

        //     $diskon[] = [
        //         'id'            => $trx['id'],
        //         'trxdisc'       => $transactiondisc,
        //         'variantdis'    => $variantdisc,
        //         'poindisc'      => $poindisc,
        //     ];
        // }

        // $trxvar = array_sum(array_column($diskon, 'variantdis'));
        // $trxdis = array_sum(array_column($diskon, 'trxdisc'));
        // $dispoint = array_sum(array_column($diskon, 'poindisc'));

        $discount           = array();
        $pointused          = array();
        $discountvariant    = array();
        $discountglobal     = array();

        foreach ($transaction as $trx) {
            // Transaction Point Used Array
            $pointused[]        = $trx['pointused'];

            // Discount Transaction
            if (!empty($trx['discvalue'])) {
                $discount[]  = $trx['discvalue'];
            }

            // Discount Member
            if ($trx['memberdisc'] != null) {
                $discount[]   = $trx['memberdisc'];
            }
            
            $trxdetails         = $TrxdetailModel->where('transactionid', $trx['id'])->find();
            foreach ($trxdetails as $trxdetail) {
                // Discount Variant
                if ($trxdetail['discvar'] != '0') {
                    $discountvariant[]     = $trxdetail['discvar'];
                }

                // Discount Global
                if ($trxdetail['globaldisc'] != '0') {
                    $discountglobal[]     = $trxdetail['globaldisc'];
                }
            }
        }

        $transactiondisc    = array_sum($discount);
        $variantdisc        = array_sum($discountvariant);
        $globaldisc         = array_sum($discountglobal);
        $poindisc           = array_sum($pointused);

        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=Laporan Diskon $startdate-$enddate.xls");

        // export
        echo '<table>';
        echo '<thead>';
            echo '<tr>';
                echo '<th colspan="3" style="align-text:center;">Ringkasan Pembayaran</th>';
            echo '</tr>';
            echo '<tr>';
                echo '<th colspan="3" style="align-text:center;">' . $outletname . '</th>';
            echo '</tr>';
            echo '<tr>';
                echo '<th colspan="3" style="align-text:center;">' . $addres . '</th>';
            echo '</tr>';
            echo '<tr>';
                echo '<th colspan="3" style="align-text:center;">' . $startdate . ' - ' . $enddate . '</th>';
            echo '</tr>';
            echo '<tr>';
                echo '<th colspan="3" style="align-text:center;"></th>';
            echo '</tr>';
            echo '<tr>';
                echo '<th>Diskon Transaksi</th>';
                echo '<th>Diskon Variant</th>';
                echo '<th>Diskon Global</th>';
                echo '<th>Diskon Poin</th>';
            echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        // foreach ($diskon as $disc) {
        //     echo '<tr>';
        //     echo '<td >' . $disc['trxdisc'] . '</td>';
        //     echo '<td >' . $disc['variantdis'] . '</td>';
        //     echo '<td>' . $disc['poindisc'] . '</td>';
        //     echo '</tr>';
        // }
            echo '<tr>';
                echo '<td >' . $transactiondisc . '</td>';
                echo '<td >' . $variantdisc . '</td>';
                echo '<td >' . $globaldisc . '</td>';
                echo '<td >' . $poindisc . '</td>';
            echo '</tr>';
        echo '</tbody>';
        echo '</table>';
    }

    public function presence()
    {
        // calling model
        $PresenceModel  = new PresenceModel;
        $UserModel      = new UserModel;
        $UserGroupModel = new GroupUserModel;
        $GroupModel     = new GroupModel;

        // populating data
        // $presences  = $PresenceModel->findAll();
        // $users      = $UserModel->findAll();
        // $usergroups = $UserGroupModel->findAll();
        // $groups     = $GroupModel->findAll();

        // Calling Models
        // $db                 = \Config\Database::connect();
        // $TransactionModel   = new TransactionModel;
        // $MemberModel        = new MemberModel;
        // $DebtModel          = new DebtModel;
        $OutletModel        = new OutletMOdel;

        // Populating Data
        // $members            = $MemberModel->findAll();
        // $debts              = $DebtModel->findAll();

        $input = $this->request->getGet('daterange');

        if (!empty($input)) {
            $daterange = explode(' - ', $input);
            $startdate = $daterange[0];
            $enddate = $daterange[1];
        } else {
            $startdate  = date('Y-m-1' . ' 00:00:00');
            $enddate    = date('Y-m-t' . ' 23:59:59');
        }

        $presencedata   = [];
        if ($this->data['outletPick'] === null) {
            $presences  = $PresenceModel->where('datetime >=', $startdate . ' 00:00:00')->where('datetime <=', $enddate . ' 23:59:59')->find();
            $addres     = "All Outlets";
            $outletname = "58vapehouse";
        } else {
            $presences  = $PresenceModel->where('datetime >=', $startdate . ' 00:00:00')->where('datetime <=', $enddate . ' 23:59:59')->find();
            $outlets    = $OutletModel->find($this->data['outletPick']);
            $addres     = $outlets['address'];
            $outletname = $outlets['name'];
        }
        
        foreach ($presences as $presence) {
            // Get User Data
            $users          = $UserModel->find($presence['userid']);
            $usergroups     = $UserGroupModel->where('user_id', $users->id)->first();
            $groups         = $GroupModel->find($usergroups['group_id']);

            // Define Time
            $s      = strtotime($presence['datetime']);
            $date   = date('d-m-Y', $s);
            $time   = date('H:i', $s);

            $shift  = $presence['shift'];
            $status = $presence['status'];

            $presencedata[$date.$shift]['id']       = $presence['id'];
            $presencedata[$date.$shift]['date']     = $date;
            $presencedata[$date.$shift]['name']     = $users->name;
            $presencedata[$date.$shift]['role']     = $groups->name;
            $presencedata[$date.$shift]['shift']    = $presence['shift'];

            $presencedata[$date.$shift]['detail'][$status]['time']         = $time;
            $presencedata[$date.$shift]['detail'][$status]['photo']        = $presence['photo'];
            $presencedata[$date.$shift]['detail'][$status]['geoloc']       = $presence['geoloc'];
            $presencedata[$date.$shift]['detail'][$status]['status']       = $presence['status'];
        }

        // $addres = '';
        // $presences = $PresenceModel->where('datetime >=', $startdate . " 00:00:00")->where('datetime <=', $enddate . " 23:59:59")->find();
        // if ($this->data['outletPick'] === null) {
        //     // if ($startdate === $enddate) {
        //     //     $presences = $PresenceModel->where('date >=', $startdate . " 00:00:00")->where('date <=', $enddate . " 23:59:59")->find();
        //     // } else {
        //     //     $presences  = $PresenceModel->where('datetime >=', $startdate)->where('datetime <=', $enddate)->find();
        //     // }
        //     $addres = "All Outlets";
        //     $outletname = "58vapehouse";
        // } else {
        //     // if ($startdate === $enddate) {
        //     //     $presences = $PresenceModel->where('date >=', $startdate . " 00:00:00")->where('date <=', $enddate . " 23:59:59")->find();
        //     // } else {
        //     //     $presences  = $PresenceModel->where('datetime >=', $startdate)->where('datetime <=', $enddate)->find();
        //     // }
        //     $outlets = $OutletModel->find($this->data['outletPick']);
        //     $addres = $outlets['address'];
        //     $outletname = $outlets['name'];
        // }


        // $absen = array();
        // foreach ($presences as $presence) {
        //     foreach ($users as $user) {
        //         if ($presence['userid'] === $user->id) {
        //             foreach ($usergroups as $ugroups) {
        //                 if ($ugroups['user_id'] === $user->id) {
        //                     foreach ($groups as $group) {
        //                         if ($ugroups['group_id'] === $group->id) {
        //                             $absen[] = [
        //                                 'id'        => $user->id,
        //                                 'name'      => $user->username,
        //                                 'date'      => $presence['datetime'],
        //                                 'status'    => $presence['status'],
        //                                 'role'      => $group->name,
        //                             ];
        //                         }
        //                     }
        //                 }
        //             }
        //         }
        //     }
        // }

        // // Sum Total  Presence
        // $admin = [];
        // foreach ($absen as $abs) {
        //     $present = array();
        //     foreach ($absen as $abs) {
        //         if ($abs['status'] === '1') {
        //             $present[] = $abs['status'];
        //         }
        //     }
        //     $presen = count($present);
        //     if (!isset($admin[$abs['id'] . $abs['name']])) {
        //         $admin[$abs['id'] . $abs['name']] = $abs;
        //     } else {
        //         $admin[$abs['id'] . $abs['name']]['status'] += $abs['status'];
        //     }
        // }
        // $admin = array_values($admin);

        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=Presence Report $startdate-$enddate.xls");

        // export
        echo '<table>';
            echo '<thead>';
                echo '<tr>';
                    echo '<th colspan="9" style="align-text:center;">Laporan Presensi</th>';
                echo '</tr>';
                echo '<tr>';
                    echo '<th colspan="9" style="align-text:center;">' . $outletname . '</th>';
                echo '</tr>';
                echo '<tr>';
                    echo '<th colspan="9" style="align-text:center;">' . $addres . '</th>';
                echo '</tr>';
                echo '<tr>';
                    echo '<th colspan="9" style="align-text:center;">' . $startdate . ' - ' . $enddate . '</th>';
                echo '</tr>';
                echo '<tr>';
                    echo '<th colspan="9" style="align-text:center;"></th>';
                echo '</tr>';
                echo '<tr>';
                    echo '<th>Tanggal</th>';
                    echo '<th>Nama</th>';
                    echo '<th>Posisi</th>';
                    echo '<th>Shift</th>';
                    echo '<th>Jam Masuk</th>';
                    echo '<th>Keterlambatan</th>';
                    echo '<th>Lokasi Masuk</th>';
                    echo '<th>Jam Keluar</th>';
                    echo '<th>Lokasi Keluar</th>';
                echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
                foreach ($presencedata as $presence) {
                    if ($presence['shift'] == '0') {
                        $waktu  = 'Pagi (09:00)';
                    } elseif ($presence['shift'] == '1') {
                        $waktu  = 'Siang (12:00 - 16:00)';
                    } elseif ($presence['shift'] == '2') {
                        $waktu  = 'Sore (16:00)';
                    }
                    echo '<tr>';
                        echo '<td>' . date('l, d M Y', strtotime($presence['date'])) . '</td>';
                        echo '<td>' . $presence['name'] . '</td>';
                        echo '<td>' . $presence['role'] . '</td>';
                        echo '<td>' . $waktu . '</td>';
                        foreach ($presence['detail'] as $detail) {
                            echo '<td>' . $detail['time'] . '</td>';
                            if ($detail['status'] == '1') {
                                if ($presence['shift'] == '0') {
                                    $kompensasi  = '09:15';
                                } elseif ($presence['shift'] == '1') {
                                    $kompensasi  = '16:15';
                                } elseif ($presence['shift'] == '2') {
                                    $kompensasi  = '16:15';
                                }
                                
                                if (str_replace(":","", $detail['time']) > str_replace(":","", $kompensasi)) {
                                    echo '<td>' . str_replace(":","", $detail['time']) - str_replace(":","", $kompensasi) . '</td>';
                                } else {
                                    echo '<td></td>';
                                }
                            }
                            echo '<td>' . $detail['geoloc'] . '</td>';
                        }
                    echo '</tr>';
                }
            echo '</tbody>';
        echo '</table>';
    }

    public function customer()
    {
        // Calling Models
        $db                 = \Config\Database::connect();
        $TransactionModel   = new TransactionModel;
        $MemberModel        = new MemberModel;
        $DebtModel          = new DebtModel;
        $OutletModel        = new OutletModel;
        $TrxdetailModel     = new TrxdetailModel;
        $ProductModel       = new ProductModel;
        $VariantModel       = new VariantModel;

        // Populating Data
        // Search Filter
        $inputsearch    = $this->request->getGet('search');
        if (!empty($inputsearch)) {
            $members   = $MemberModel->like('name', $inputsearch)->orderBy('name', 'ASC')->paginate(20, 'member');
        } else {
            $members   = $MemberModel->orderBy('name', 'ASC')->paginate(20, 'member');
        }

        // Daterange Filter
        $input = $this->request->getGet('daterange');
        if (!empty($input)) {
            $daterange = explode(' - ', $input);
            $startdate = $daterange[0];
            $enddate = $daterange[1];
        } else {
            $startdate  = date('Y-m-1' . ' 00:00:00');
            $enddate    = date('Y-m-t' . ' 23:59:59');
        }

        $addres = '';
        if ($this->data['outletPick'] === null) {
            $transactions = $TransactionModel->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->find();
            $addres = "All Outlets";
            $outletname = "58vapehouse";
        } else {
            $transactions = $TransactionModel->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->where('outletid', $this->data['outletPick'])->find();
            $outlets = $OutletModel->find($this->data['outletPick']);
            $addres = $outlets['address'];
            $outletname = $outlets['name'];
        }

        $customerdata   = [];
        foreach ($members as $member) {
            $debts      = $DebtModel->where('memberid', $member['id'])->find();
            $debtvalue  = [];
            if (!empty($debts)) {
                foreach ($debts as $debt) {
                    $debtvalue[]    = $debt['value'];
                }
            }
            
            if ($this->data['outletPick'] === null) {
                $transactions   = $TransactionModel->where('memberid', $member['id'])->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->find();
                $addres         = "All Outlets";
                $outletname     = "58vapehouse";
            } else {
                $transactions   = $TransactionModel->where('memberid', $member['id'])->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->where('outletid', $this->data['outletPick'])->find();
                $outlets        = $OutletModel->find($this->data['outletPick']);
                $addres         = $outlets['address'];
                $outletname     = $outlets['name'];
            }
            
            $trxvalue   = [];
            if (!empty($transactions)) {
                foreach ($transactions as $trx) {
                    $trxdetails     = $TrxdetailModel->where('transactionid', $trx['id'])->find();
                    $trxvalue[]     = $trx['value'];
                
                    if (!empty($trxdetails)) {
                        foreach ($trxdetails as $trxdet) {
                            $variants       = $VariantModel->find($trxdet['variantid']);
                            
                            if (!empty($variants)) {
                                $products   = $ProductModel->find($variants['productid']);
        
                                if (!empty($products)) {
                                    $customerdata[$member['id']]['product'][$products['id']]['name']            = $products['name'];
                                    $customerdata[$member['id']]['product'][$products['id']]['qty'][]           = $trxdet['qty'];
                                }
                            } else {
                                $products   = [];
                                $customerdata[$member['id']]['product'][0]['name']             = 'Kategori / Produk / Variant Terhapus';
                                $customerdata[$member['id']]['product'][0]['category']         = 'Kategori / Produk / Variant Terhapus';
                                $customerdata[$member['id']]['product'][0]['qty'][]            = $trxdet['qty'];
                            }
                        }
                    } else {
                        $variants   = [];
                        $products   = [];
                    }
                }
            } else {
                $customerdata[$member['id']]['product'] = [];
            }
            
            $customerdata[$member['id']]['id']          = $member['id'];
            $customerdata[$member['id']]['name']        = $member['name'];
            $customerdata[$member['id']]['phone']       = $member['phone'];
            $customerdata[$member['id']]['debt']        = array_sum($debtvalue);
            $customerdata[$member['id']]['trx']         = count($transactions);
            $customerdata[$member['id']]['trxvalue']    = array_sum($trxvalue);
        }

        $datestart  = date('d M Y', strtotime($startdate));
        $dateend    = date('d M Y', strtotime($enddate));

        // // Populating Data
        // $members            = $MemberModel->findAll();
        // $debts              = $DebtModel->findAll();

        // $input = $this->request->getGet('daterange');

        // if (!empty($input)) {
        //     $daterange = explode(' - ', $input);
        //     $startdate = $daterange[0];
        //     $enddate = $daterange[1];
        // } else {
        //     $startdate  = date('Y-m-1' . ' 00:00:00');
        //     $enddate    = date('Y-m-t' . ' 23:59:59');
        // }

        // $addres = '';
        // if ($this->data['outletPick'] === null) {
        //     // if ($startdate === $enddate) {
        //         $transactions = $TransactionModel->where('date >=', $startdate . " 00:00:00")->where('date <=', $enddate . " 23:59:59")->find();
        //     // } else {
        //     //     $transactions = $TransactionModel->where('date >=', $startdate)->where('date <=', $enddate)->find();
        //     // }
        //     $addres = "All Outlets";
        //     $outletname = "58vapehouse";
        // } else {
        //     // if ($startdate === $enddate) {
        //         $transactions = $TransactionModel->where('outletid', $this->data['outletPick'])->where('date >=', $startdate . " 00:00:00")->where('date <=', $enddate . " 23:59:59")->find();
        //     // } else {
        //     //     $transactions = $TransactionModel->where('date >=', $startdate)->where('date <=', $enddate)->where('outletid', $this->data['outletPick'])->find();
        //     // }
        //     $outlets = $OutletModel->find($this->data['outletPick']);
        //     $addres = $outlets['address'];
        //     $outletname = $outlets['name'];
        // }
        // $customer = array();
        // foreach ($members as $member) {
        //     $totaltrx = array();
        //     $trxval = array();
        //     $debtval    = array();
        //     foreach ($debts as $debt) {
        //         if ($member['id'] === $debt['memberid']) {
        //             $debtval[]  = $debt['value'];
        //         }
        //     }
        //     foreach ($transactions as $trx) {
        //         if ($member['id'] === $trx['memberid']) {
        //             $totaltrx[] = $trx['memberid'];
        //             $trxval[]   = $trx['value'];
        //         }
        //     }

        //     $customer[] = [
        //         'id'    => $member['id'],
        //         'name'  => $member['name'],
        //         'debt'  => array_sum($debtval),
        //         'trx'   => count($totaltrx),
        //         'value' => array_sum($trxval),
        //         'phone' => $member['phone'],
        //     ];
        // }

        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=customer$startdate-$enddate.xls");

        // export
        echo '<table>';
            echo '<thead>';
                echo '<tr>';
                    echo '<th colspan="5" style="align-text:center;">Ringkasan Pelanggan</th>';
                echo '</tr>';
                echo '<tr>';
                    echo '<th colspan="5" style="align-text:center;">' . $outletname . '</th>';
                echo '</tr>';
                echo '<tr>';
                        echo '<th colspan="5" style="align-text:center;">' . $addres . '</th>';
                echo '</tr>';
                echo '<tr>';
                    echo '<th colspan="5" style="align-text:center;">' . $datestart . ' - ' . $dateend . '</th>';
                echo '</tr>';
                echo '<tr>';
                    echo '<th colspan="5" style="align-text:center;"></th>';
                echo '</tr>';
                echo '<tr>';
                    echo '<th>Nama</th>';
                    echo '<th>Jumlah Transaksi</th>';
                    echo '<th>Nominal Transaksi</th>';
                    echo '<th>Hutang</th>';
                    echo '<th>No Telphone</th>';
                echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
                foreach ($customerdata as $cust) {
                    echo '<tr>';
                        echo '<td>' . $cust['name'] . '</td>';
                        echo '<td>' . $cust['trx'] . '</td>';
                        echo '<td>' . $cust['trxvalue'] . '</td>';
                        echo '<td>' . $cust['debt'] . '</td>';
                        echo '<td>' . $cust['phone'] . '</td>';
                    echo '</tr>';
                }
            echo '</tbody>';
        echo '</table>';
    }

    public function customerlist()
    {
        // Calling Models
        $MemberModel        = new MemberModel;

        // Populating Data
        $members            = $MemberModel->findAll();
        $outletname         = "58vapehouse";

        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=Customer List.xls");

        // export
        echo '<table>';
            echo '<thead>';
                echo '<tr>';
                    echo '<th colspan="2" style="align-text:center;">Daftar Member</th>';
                echo '</tr>';
                echo '<tr>';
                    echo '<th colspan="2" style="align-text:center;">' . $outletname . '</th>';
                echo '</tr>';
                echo '<tr>';
                    echo '<th colspan="2" style="align-text:center;"></th>';
                echo '</tr>';
                echo '<tr>';
                    echo '<th>Nama</th>';
                    echo '<th>Nomor Telephone</th>';
                echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
                foreach ($members as $cust) {
                    echo '<tr>';
                        echo '<td>' . $cust['name'] . '</td>';
                        echo '<td>+62' . $cust['phone'] . '</td>';
                    echo '</tr>';
                }
            echo '</tbody>';
        echo '</table>';
    }

    public function sop()
    {
        // Calling Data
        $SopModel           = new SopModel();
        $SopDetailModel     = new SopDetailModel();
        $OutletModel        = new OutletModel();
        $UserModel          = new UserModel();
        
        // Populating Data
        $input = $this->request->getGet('daterange');

        if (!empty($input['daterange'])) {
            $daterange  = explode(' - ', $input['daterange']);
            $startdate  = $daterange[0];
            $enddate    = $daterange[1];
        } else {
            $startdate  = date('Y-m-1');
            $enddate    = date('Y-m-t');
        }

        if ($this->data['outletPick'] === null) {
            $sopdetails = $SopDetailModel->orderby('updated_at', 'DESC')->where('updated_at >=', $startdate . ' 00:00:00')->where('updated_at <=', $enddate . ' 23:59:59')->find();
            $addres     = "All Outlets";
            $outletname = "58vapehouse";
        } else {
            $sopdetails = $SopDetailModel->orderby('updated_at', 'DESC')->where('outletid', $this->data['outletPick'])->where('updated_at >=', $startdate . ' 00:00:00')->where('updated_at <=', $enddate . ' 23:59:59')->find();
            $outlets    = $OutletModel->find($this->data['outletPick']);
            $addres     = $outlets['address'];
            $outletname = $outlets['name'];
        }

        $sopdata        = [];
        $count          = 0;
        foreach ($sopdetails as $sopdet) {
            // Get Data SOP
            $sops       = $SopModel->find($sopdet['sopid']);
            $users      = $UserModel->find($sopdet['userid']);
            $outlet     = $OutletModel->find($sopdet['outletid']);

            if (!empty($outlet)) {
                $outletid   = $outlet['id'];
                $outletname = $outlet['name'];
            } else {
                $outletid   = 0;
                $outletname = 'Semua Outlet';
            }
            
            if (!empty($users)) {
                $username   = $users->firstname.' '.$users->lastname;
            } else {
                $username   = '!';
            }
            
            if ($sopdet['status'] == '0') {
                $status     = 'Belum Dilakukan';
            } else {
                $status     = 'Telah Dilakukan Oleh';
            }

            // Define Time
            $s      = strtotime($sopdet['created_at']);
            $date   = date('d-m-Y', $s);
            $time   = date('H:i', $s);

            $sopdata[$outletid]['id']                                                   = $count++;
            $sopdata[$outletid]['outlet']                                               = $outletname;
            $sopdata[$outletid]['date'][$date]['datename']                              = $date;
            $sopdata[$outletid]['sop'][$sops['id']]['name']                             = $sops['name'];
            $sopdata[$outletid]['sop'][$sops['id']]['detail'][$date]['employee']        = $username;
            $sopdata[$outletid]['sop'][$sops['id']]['detail'][$date]['status']          = $status;
        }
        
        $datestart  = date('d M Y', strtotime($startdate));
        $dateend    = date('d M Y', strtotime($enddate));
        $i = 1;
        
        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=SOP Report $startdate-$enddate.xls");

        // export
        echo '<table>';
            echo '<thead>';
                echo '<tr>';
                    echo '<th colspan="9" style="align-text:center;">Laporan SOP</th>';
                echo '</tr>';
                echo '<tr>';
                    echo '<th colspan="9" style="align-text:center;">' . $datestart . ' - ' . $dateend . '</th>';
                echo '</tr>';
                echo '<tr>';
                    echo '<th colspan="9" style="align-text:center;"></th>';
                echo '</tr>';
            echo '</thead>';
        echo '</table>';
        foreach ($sopdata as $sopdat) {
            echo '<table>';
                echo '<thead>';
                    echo '<tr>';
                        echo '<th style="border:1px solid black">No.</th>';
                        echo '<th colspan="5" style="border:1px solid black">'.$sopdat['outlet'].'</th>';
                        foreach ($sopdat['date'] as $date) {
                            echo '<th style="border:1px solid black">'.$date['datename'].'</th>';
                        }
                    echo '</tr>';
                echo '</thead>';
                echo '<tbody>';
                    foreach ($sopdat['sop'] as $sop) {
                        echo '<tr>';
                        echo '<td style="text-align:center; border:1px solid black">'.$i++.'</td>';
                            echo '<td colspan="5" style="border:1px solid black">'.$sop['name'].'</td>';
                            foreach ($sop['detail'] as $detail) {
                                echo '<td style="border:1px solid black">'.$detail['status'].' '.$detail['employee'].'</td>';
                            }
                        echo '</tr>';
                    }
                echo '</tbody>';
            echo '</table>';
            echo '<table>';
            echo '</table>';
        }
    }
}
