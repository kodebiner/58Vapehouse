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

class export extends BaseController
{

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

            $exported   = $db->table('stock');
            $stockexp   = $exported->select('stock.qty as qty, variant.hargamodal as hargamodal, variant.hargadasar as hargadasar, variant.hargajual as hargajual, variant.hargarekomendasi as hargarekomendasi, variant.name as varname, product.name as prodname, category.name as catname, brand.name as brandname');
            $stockexp   = $exported->join('variant', 'stock.variantid = variant.id', 'left');
            $stockexp   = $exported->join('product', 'variant.productid = product.id', 'left');
            $stockexp   = $exported->join('category', 'product.catid = category.id', 'left');
            $stockexp   = $exported->join('brand', 'product.brandid = brand.id', 'left');
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

        $db         = \Config\Database::connect();

        // Calling Model
        $ProductModel       = new ProductModel;
        $BundleModel        = new BundleModel;
        $BundledetailModel  = new BundledetailModel;
        $BrandModel         = new BrandModel;
        $CashModel          = new CashModel;
        $DebtModel          = new Debtmodel;
        $VariantModel       = new VariantModel;
        $CategoryModel      = new CategoryModel;
        $StockModel         = new StockModel;
        $VariantModel       = new VariantModel;
        $OutletModel        = new OutletModel;
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
            $outlet = 'All Outlets';
        } else {
            $transactions = $TransactionModel->where('date >=', $startdate)->where('date <=', $enddate)->where('outletid', $this->data['outletPick'])->find();
            $outlet = $OutletModel->find($this->data['outletPick']);
        }

        $exported   = $db->table('transaction');
        $exported->where('date >=', $startdate)->where('date <=', $enddate);
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
        ?>
            <style>
                .cntr{
                    text-align: center;
                }
                th{
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
        echo '<th colspan="16" class="cntr">' . $outlet['name'] . '</th>';
        echo '</tr>';
        echo '<tr>';
        echo '<th colspan="16" class="cntr">' . $outlet['address'] . '</th>';
        echo '</tr>';
        echo '<tr>';
        echo '<th colspan="16" class="cntr">Ringkasan Transaksi '. $startdate . ' - ' . $enddate .'</th>';
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

        // $trxdata = array();
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

            // $trxdata[] = [
            //     'kode'          => date(strtotime($trxhist['date'])),
            //     'tanggal'       => date('l, d M Y', strtotime($trxhist['date'])),
            //     'jam'           => date('H:i:s', strtotime($trxhist['date'])),
            //     'kasir'         => $trxhist['kasir'],
            //     'pembeli'       => $trxhist['member'],
            //     'produk'        => $trxhist['product'],
            //     'quantity'      => $trxhist['qty'],
            //     'hargapro'      => $trxhist['trxdetval'] + $trxhist['discvar'],
            //     'subtotal'      => ($trxhist['trxdetval'] + $trxhist['discvar']) * $trxhist['qty'],
            //     // 'subtotal'      => $trxhist['total'] + $discount + $trxhist['discvar'] + $trxhist['redempoin'],
            //     'diskonpro'     => $trxhist['discval'],
            //     'typedisc'      => $disctype,
            //     'redempoin'     => $trxhist['redempoin'],
            //     'total'         => $trxhist['total'],
            //     'payment'       => $trxhist['payment'],

            // ];
        }

        // dd($trxdata);

        echo '</tbody>';
        echo '</table>';
    }

    public function sales()
    {

        // Calling Models
        $TransactionModel       = new TransactionModel;
        $TrxdetailModel         = new TrxdetailModel;
        $VariantModel           = new VariantModel;
        $StockModel             = new StockModel;
        $OutletModel            = new OutletModel;
        $TrxotherModel          = new TrxotherModel;
        $BundleModel            = new BundleModel;

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
        $bundval = [];
        $omsetvalue = [];
        for ($date = $startdate; $date <= $enddate; $date += (86400)) {
            if ($this->data['outletPick'] === null) {
                $transaction = $TransactionModel->where('date >=', date('Y-m-d 00:00:00', $date))->where('date <=', date('Y-m-d 23:59:59', $date))->find();
                $stocks = $StockModel->findAll();
                $outletname[] = "All Outlets";
                $adress[] = "58vapehouse";
            } else {
                $transaction = $TransactionModel->where('date >=', date('Y-m-d 00:00:00', $date))->where('date <=', date('Y-m-d 23:59:59', $date))->where('outletid', $this->data['outletPick'])->find();
                $stocks = $StockModel->where('outletid', $this->data['outletPick'])->find();
                $outlets = $OutletModel->where('id', $this->data['outletPick'])->find();
            }

            $trxdetails  = $TrxdetailModel->findAll();
            $summary     = array_sum(array_column($transaction, 'value'));
            $variants    = $VariantModel->findAll();
            $bundles     = $BundleModel->findAll();

            $marginmodals = array();
            $margindasars = array();

            foreach ($transaction as $trx) {
                foreach ($trxdetails as $trxdetail) {
                    if ($trx['id'] == $trxdetail['transactionid']) {

                        $omsetvalue[] = ($trxdetail['value'] + $trxdetail['discvar']) * $trxdetail['qty'];
                        // margin
                        $marginmodal = $trxdetail['marginmodal'];
                        $margindasar = $trxdetail['margindasar'];
                        $marginmodals[] = $marginmodal;
                        $margindasars[] = $margindasar;

                        // discount
                        if ($trx['disctype'] === "0") {
                            $discounttrx[]          = $trx['discvalue'];
                        }
                        if ($trx['disctype'] !== "0") {
                            $sub =  ($trxdetail['value'] * $trxdetail['qty']);
                            $discounttrxpersen[]    =  ($trx['discvalue'] / 100) * $sub;
                        }
                        $discountvariant[]          = $trxdetail['discvar'];
                        $discountpoin[]             = $trx['pointused'];

                        foreach ($bundles as $bundle) {
                            if ($trxdetail['bundleid'] === $bundle['id']) {
                                $bundval[] = [
                                    'id' => $bundle['id'],
                                    'name' => $bundle['name'],
                                    'price' => $trxdetail['value'],
                                ];
                            }
                        }

                        foreach ($variants as $variant) {
                            if ($variant['id'] === $trxdetail['variantid']) {
                                foreach ($stocks as $stock) {
                                    if ($variant['id'] === $stock['variantid']) {
                                        $varvalues[] = [
                                            'name'          => $variant['name'],
                                            'hargamodal'    => $variant['hargamodal'],
                                            'hargadasar'    => $variant['hargadasar'],
                                            'hargajual'     => $variant['hargajual'],
                                            'qty'           => $stock['qty'],
                                            'trxqty'        => $trxdetail['qty'],
                                            'omset'         => ($trxdetail['value'] * $trxdetail['qty']) - $trxdetail['discvar'],
                                            'modalprice'    => (($trxdetail['value'] * $trxdetail['qty']) - ($trxdetail['discvar'])) - $trxdetail['marginmodal'] * $trxdetail['qty'],
                                            'basicprice'    => (($trxdetail['value'] * $trxdetail['qty']) - ($trxdetail['discvar'])) - $trxdetail['margindasar'] * $trxdetail['qty'],
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

        $discount[] = [
            'trxdisc'       => $transactiondisc,
            'variantdis'    => $variantdisc,
            'poindisc'      => $poindisc,
        ];

        $totaltrxdisc = array_sum(array_column($discount, 'trxdisc'));
        $totalvardisc = array_sum(array_column($discount, 'variantdis'));
        $totalpoindisc = array_sum(array_column($discount, 'poindisc'));

        // bundle total price
        $bundleprice = array_sum(array_column($bundval, 'price'));

        // Total Discount
        $alldisc = (int)$totaltrxdisc + (int)$totalvardisc + (int)$totalpoindisc;
        $transactionarr[] = $transactions;

        $date1 = date('Y-m-d', $startdate);
        $date2 = date('Y-m-d', $enddate);
        $day1 = date_create($date1);
        $day2 = date_create($date2);
        $interval = date_diff($day1, $day2)->format("%a");

        //sales
        $salesresult = array_sum(array_column($transactions, 'value'));
        $grossales = $salesresult + $alldisc;

        // Profit Calculation
        $modalprice     = array_sum(array_column($varvalues, 'modalprice'));
        $basicprice     = array_sum(array_column($varvalues, 'basicprice'));
        $omsetbaru      = array_sum(array_column($varvalues, 'omset'));
        $profitvalue    = $omsetbaru - $modalprice;

        $keuntunganmodal = array_sum(array_column($transactions, 'modal'));
        $keuntungandasar = array_sum(array_column($transactions, 'dasar'));
        $trxvalue        = array_sum(array_column($transactions, 'value'));

        // Outlet Setup
        if ($this->data['outletPick'] !== null) {
            foreach ($outlets as $outlet) {
                $outletname[] = $outlet['name'];
                $adress[] = $outlet['address'];
            }
        }

        // Set Cash In Cash Out
        $cashin  = [];
        $cashout = [];
        foreach ($cash as $cas) {
            if ($cas['type'] === "0") {
                $cashin[] = $cas['qty'];
            } elseif ($cas['type'] !== "0") {
                $cashout[] = $cas['qty'];
            }
        }
        $casin = array_sum($cashin);
        $casout = array_sum($cashout);

        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=sales$date1-$date2.xls");

        echo '<style type="text/css">

        </style>';
        echo '<table  style="width: 30%;">';
        echo '<tr>';
        echo '<th colspan="2" style="align-text:center;">Ringkasan Penjualan</th>';
        echo '</tr>';
        echo '<tr>';
        echo '<th colspan="2" style="align-text:center;">' . $outletname[0] . '</th>';
        echo '</tr>';
        echo '<tr>';
        echo '<th colspan="2" style="align-text:center;">' . $adress[0] . '</th>';
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
        echo '<td style="text-align: right;">' . $omsetbaru . '</td>';
        echo '</tr>';
        echo '<tr>';
        echo '<th style="text-align: left;">Harga Modal</th>';
        echo '<td style="text-align: right;">' . $modalprice . '</td>';
        echo '</tr>';
        echo '<tr>';
        echo '<th style="text-align: left;">Harga Dasar</th>';
        echo '<td style="text-align: right;">' . $basicprice . '</td>';
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

    public function product()
    {

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
        $input = $this->request->getVar('daterange');

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

        $adress = [];
        if ($this->data['outletPick'] === null) {
            $transactions = $TransactionModel->where('date >=', $startdate)->where('date <=', $enddate)->find();
            $outletname = "All Outlets";
            $adress = "58vapehouse";
        } else {
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

        foreach ($transactions as $transaction) {
            $discounttrx = array();
            $discounttrxpersen = array();
            $discountvariant = array();
            $discountpoin = array();
            foreach ($trxdetails as $trxdetail) {
                foreach ($bundles as $bundle) {
                    if ($transaction['id'] === $trxdetail['transactionid'] && $bundle['id'] === $trxdetail['bundleid']) {
                        $bundleval[]   = [
                            'id'    => $bundle['id'],
                            'name'  => $bundle['name'],
                            'value' => $trxdetail['value'],
                        ];
                    }
                }
                if ($transaction['id'] === $trxdetail['transactionid']) {

                    if ($transaction['disctype'] === "0") {

                        $discounttrx[]          = $transaction['discvalue'];
                    }
                    if ($transaction['disctype'] !== "0") {

                        $sub = ($trxdetail['value']) * $trxdetail['qty'];
                        $discounttrxpersen[]    = $sub * ($transaction['discvalue'] / 100);
                    }
                    $discountvariant[]          = $trxdetail['discvar'];

                    $discountpoin[]             = $transaction['pointused'];

                    foreach ($products as $product) {
                        foreach ($variants as $variant) {
                            if (($variant['id'] === $trxdetail['variantid']) && ($variant['productid'] === $product['id'])) {
                                foreach ($products as $product) {
                                    if ($variant['productid'] === $product['id']) {
                                        $productval[] = $product['name'];
                                        foreach ($category as $cat) {
                                            if ($product['catid'] === $cat['id']) {
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

        $bundletotal = array_sum(array_column($bundleval, 'value'));

        $produk = [];
        foreach ($variantvalue as $vars) {
            if (!isset($produk[$vars['id'] . $vars['product']])) {
                $produk[$vars['id'] . $vars['product']] = $vars;
            } else {
                $produk[$vars['id'] . $vars['product']]['value'] += $vars['value'];
                $produk[$vars['id'] . $vars['product']]['qty'] += $vars['qty'];
                $produk[$vars['id'] . $vars['product']]['gross'] += $vars['gross'];
            }
        }
        $produk = array_values($produk);

        // disc calculation
        $trxdisc    = array_sum(array_column($diskon, 'trxdisc'));
        $poindisc   = array_sum(array_column($diskon, 'poindisc'));
        $vardisc    = array_sum(array_column($diskon, 'variantdis'));
        $proval     = array_sum(array_column($produk, 'value'));

        // if want to get net sales with trx disc and without bundle value
        $netsales = $proval - ($trxdisc + $poindisc);

        // Total Stock
        $stoktotal = array_sum(array_column($produk, 'qty'));

        // Total Sales Without trx disc, bundle & poin disc
        $salestotal = array_sum(array_column($produk, 'value'));

        // Total Gross
        $grosstotal = array_sum(array_column($produk, 'gross'));

        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=productreport$startdate-$enddate.xls");

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
        echo '<th>Produk</th>';
        echo '<th>Kategory</th>';
        echo '<th>Jumlah Transaksi</th>';
        echo '<th>Nominal Transaksi</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        foreach ($produk as $product) {
            echo '<tr>';
            echo '<td>' . $product['product'] . '</td>';
            echo '<td>' . $product['category'] . '</td>';
            echo '<td>' . $product['qty'] . '</td>';
            echo '<td>' . $product['value'] . '</td>';
            echo '</tr>';
        }
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
                $startdate = date('Y-m-1');
                $enddate = date('Y-m-t');
            }

            $day1 = date_create($startdate);
            $day2 = date_create($enddate);

            $payments = $PaymentModel->findAll();
            $trxpayments = $TrxpaymentModel->findAll();
            $transactions = $TransactionModel->where('outletid', $this->data['outletPick'])->where('date >=', $startdate)->where('date <=', $enddate)->find();
            $outlets = $OutletModel->find($this->data['outletPick']);
            $outletname = $outlets['name'];
            $adress = $outlets['address'];
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

            $totalvalue = array_sum(array_column($pay, 'pvalue'));
            $totalqty = array_sum(array_column($pay, 'pqty'));

            header("Content-type: application/vnd-ms-excel");
            header("Content-Disposition: attachment; filename=payment.xls");
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
            echo '<th colspan="3" style="align-text:center;">Ringkasan Pembayran</th>';
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
            foreach ($pay as $payment) {
                echo '<tr>';
                echo '<td>' . $payment['name'] . '</td>';
                echo '<td>' . $payment['pqty'] . '</td>';
                echo '<td>' . $payment['pvalue'] . '</td>';
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

    public function diskon()
    {

        // Calling Models
        $TransactionModel       = new TransactionModel;
        $TrxdetailModel         = new TrxdetailModel;
        $GconfigModel           = new GconfigModel;
        $OutletModel            = new OutletModel;
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

        $diskon = [];
        $addres = '';
        if ($this->data['outletPick'] === null) {
            $transaction = $TransactionModel->where('date >=', $startdate)->where('date <=', $enddate)->find();
            $addres = "All Outlets";
            $outletname = "58vapehouse";
        } else {
            $transaction = $TransactionModel->where('date >=', $startdate)->where('date <=', $enddate)->where('outletid', $this->data['outletPick'])->find();
            $outlets = $OutletModel->find($this->data['outletPick']);
            $addres = $outlets['address'];
            $outletname = $outlets['name'];
        }
        foreach ($transaction as $trx) {
            $discounttrx = array();
            $discounttrxpersen = array();
            $discountvariant = array();
            $discountpoin = array();
            foreach ($trxdetails as $trxdetail) {
                if ($trx['id'] === $trxdetail['transactionid']) {
                    if ($trx['disctype'] === "0") {
                        $discounttrx[]          = $trx['discvalue'];
                    }
                    if ($trx['disctype'] !== "0") {
                        $sub =  ($trxdetail['value'] * $trxdetail['qty']);
                        $discounttrxpersen[]    =  $sub * ($trx['discvalue'] / 100);
                    }
                    $discountvariant[]          = $trxdetail['discvar'];
                    $discountpoin[]             = $trx['pointused'];
                }
            }

            $transactiondisc = array_sum($discounttrx) +  array_sum($discounttrxpersen);
            $variantdisc     = array_sum($discountvariant);
            $poindisc        = array_sum($discountpoin);

            $diskon[] = [
                'id'            => $trx['id'],
                'trxdisc'       => $transactiondisc,
                'variantdis'    => $variantdisc,
                'poindisc'      => $poindisc,
            ];
        }

        $trxvar = array_sum(array_column($diskon, 'variantdis'));
        $trxdis = array_sum(array_column($diskon, 'trxdisc'));
        $dispoint = array_sum(array_column($diskon, 'poindisc'));

        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=discount$startdate-$enddate.xls");

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
        echo '<th>Diskon Poin</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        foreach ($diskon as $disc) {
            echo '<tr>';
            echo '<td >' . $disc['trxdisc'] . '</td>';
            echo '<td >' . $disc['variantdis'] . '</td>';
            echo '<td>' . $disc['poindisc'] . '</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
    }

    public function profit()
    {

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

            foreach ($transaction as $trx) {
                foreach ($trxdetails as $trxdetail) {
                    if ($trx['id'] === $trxdetail['transactionid']) {
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

        $transactionarr[] = $transactions;

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
        echo '<td>' . $keuntungandasar . '</td>';
        echo '<td>' . $keuntunganmodal . '</td>';
        echo '</tr>';
        echo '</tbody>';
        echo '</table>';
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
        $usergroups     = $UserGroupModel->findAll();
        $groups         = $GroupModel->findAll();

        $input = $this->request->getGet('daterange');

        if (!empty($input)) {
            $daterange = explode(' - ', $input);
            $startdate = $daterange[0];
            $enddate = $daterange[1];
        } else {
            $startdate = date('Y-m-1');
            $enddate = date('Y-m-t');
        }

        $addres = '';
        if ($this->data['outletPick'] === null) {
            $transactions = $TransactionModel->where('date >=', $startdate)->where('date <=', $enddate)->find();
            $addres = "All Outlets";
            $outletname = "58vapehouse";
        } else {
            $transactions = $TransactionModel->where('date >=', $startdate)->where('date <=', $enddate)->where('outletid', $this->data['outletPick'])->find();
            $outlets = $OutletModel->find($this->data['outletPick']);
            $addres = $outlets['address'];
            $outletname = $outlets['name'];
        }

        $useradm = [];
        foreach ($transactions as $transaction) {
            foreach ($admin as $adm) {
                if ($transaction['userid'] === $adm->id) {
                    foreach ($usergroups as $userg) {
                        if ($adm->id === $userg['user_id']) {
                            foreach ($groups as $group) {
                                if ($userg['group_id'] === $group->id) {
                                    $useradm[] = [
                                        'id'    => $adm->id,
                                        'value' => $transaction['value'],
                                        'name'  => $adm->username,
                                        'role'  => $group->name,
                                    ];
                                }
                            }
                        }
                    }
                }
            }
        }

        $produk = [];
        foreach ($useradm as $vars) {
            if (!isset($produk[$vars['id'] . $vars['role']])) {
                $produk[$vars['id'] . $vars['name'] . $vars['role']] = $vars;
            } else {
                $produk[$vars['id'] . $vars['name'] . $vars['role']]['value'] += $vars['value'];
            }
        }
        $produk = array_values($produk);

        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=employe$startdate-$enddate.xls");

        // export
        echo '<table>';
        echo '<thead>';
        echo '<tr>';
        echo '<th colspan="3" style="align-text:center;">Ringkasan Admin</th>';
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
        foreach ($produk as $employe) {
            echo '<tr>';
            echo '<td>' . $employe['name'] . '</td>';
            echo '<td>' . $employe['role'] . '</td>';
            echo '<td>' . $employe['value'] . '</td>';
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
        $OutletModel        = new OutletMOdel;

        // Populating Data
        $members            = $MemberModel->findAll();
        $debts              = $DebtModel->findAll();

        $input = $this->request->getGet('daterange');

        if (!empty($input)) {
            $daterange = explode(' - ', $input);
            $startdate = $daterange[0];
            $enddate = $daterange[1];
        } else {
            $startdate = date('Y-m-1');
            $enddate = date('Y-m-t');
        }

        $addres = '';
        if ($this->data['outletPick'] === null) {
            $transactions = $TransactionModel->where('date >=', $startdate)->where('date <=', $enddate)->find();
            $addres = "All Outlets";
            $outletname = "58vapehouse";
        } else {
            $transactions = $TransactionModel->where('date >=', $startdate)->where('date <=', $enddate)->where('outletid', $this->data['outletPick'])->find();
            $outlets = $OutletModel->find($this->data['outletPick']);
            $addres = $outlets['address'];
            $outletname = $outlets['name'];
        }
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

            $customer[] = [
                'id'    => $member['id'],
                'name'  => $member['name'],
                'debt'  => array_sum($debtval),
                'trx'   => count($totaltrx),
                'value' => array_sum($trxval),
                'phone' => $member['phone'],
            ];
        }

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
        echo '<th colspan="5" style="align-text:center;">' . $startdate . ' - ' . $enddate . '</th>';
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
        foreach ($customer as $cust) {
            echo '<tr>';
            echo '<td>' . $cust['name'] . '</td>';
            echo '<td>' . $cust['trx'] . '</td>';
            echo '<td>' . $cust['value'] . '</td>';
            echo '<td>' . $cust['debt'] . '</td>';
            echo '<td>' . $cust['phone'] . '</td>';
            echo '</tr>';
        }
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
        $presences  = $PresenceModel->findAll();
        $users      = $UserModel->findAll();
        $usergroups = $UserGroupModel->findAll();
        $groups     = $GroupModel->findAll();

        // Calling Models
        $db                 = \Config\Database::connect();
        $TransactionModel   = new TransactionModel;
        $MemberModel        = new MemberModel;
        $DebtModel          = new DebtModel;
        $OutletModel        = new OutletMOdel;

        // Populating Data
        $members            = $MemberModel->findAll();
        $debts              = $DebtModel->findAll();

        $input = $this->request->getGet('daterange');

        if (!empty($input)) {
            $daterange = explode(' - ', $input);
            $startdate = $daterange[0];
            $enddate = $daterange[1];
        } else {
            $startdate = date('Y-m-1');
            $enddate = date('Y-m-t');
        }

        $addres = '';
        if ($this->data['outletPick'] === null) {
            $presences  = $PresenceModel->where('datetime >=', $startdate)->where('datetime <=', $enddate)->find();
            $addres = "All Outlets";
            $outletname = "58vapehouse";
        } else {
            $presences  = $PresenceModel->where('datetime >=', $startdate)->where('datetime <=', $enddate)->find();
            $outlets = $OutletModel->find($this->data['outletPick']);
            $addres = $outlets['address'];
            $outletname = $outlets['name'];
        }


        $absen = array();
        foreach ($presences as $presence) {
            foreach ($users as $user) {
                if ($presence['userid'] === $user->id) {
                    foreach ($usergroups as $ugroups) {
                        if ($ugroups['user_id'] === $user->id) {
                            foreach ($groups as $group) {
                                if ($ugroups['group_id'] === $group->id) {
                                    $absen[] = [
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
            foreach ($absen as $abs) {
                if ($abs['status'] === '1') {
                    $present[] = $abs['status'];
                }
            }
            $presen = count($present);
            if (!isset($admin[$abs['id'] . $abs['name']])) {
                $admin[$abs['id'] . $abs['name']] = $abs;
            } else {
                $admin[$abs['id'] . $abs['name']]['status'] += $abs['status'];
            }
        }
        $admin = array_values($admin);

        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=presence$startdate-$enddate.xls");

        // export
        echo '<table>';
        echo '<thead>';
        echo '<tr>';
        echo '<th colspan="3" style="align-text:center;">Ringkasan Pelanggan</th>';
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
        echo '<th>Kehadiran</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        foreach ($admin as $adm) {
            echo '<tr>';
            echo '<td>' . $adm['name'] . '</td>';
            echo '<td>' . $adm['role'] . '</td>';
            echo '<td>' . $presen . '</td>';
            echo '</tr>';
        }
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

        $addres = '';
        if ($this->data['outletPick'] === null) {
            $transactions = $TransactionModel->where('date >=', $startdate)->where('date <=', $enddate)->find();
            $addres = "All Outlets";
            $outletname = "58vapehouse";
        } else {
            $transactions = $TransactionModel->where('date >=', $startdate)->where('date <=', $enddate)->where('outletid', $this->data['outletPick'])->find();
            $outlets = $OutletModel->find($this->data['outletPick']);
            $addres = $outlets['address'];
            $outletname = $outlets['name'];
        }

        $bund = [];
        foreach ($transactions as $transaction) {
            foreach ($trxdetails as $trxdetail) {
                foreach ($bundles as $bundle) {
                    if ($trxdetail['transactionid'] === $transaction['id'] && $trxdetail['bundleid'] !== "0" && $bundle['id'] === $trxdetail['bundleid']) {
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

            if (!isset($paket[$bundval['id'] . $bundval['name']])) {
                $paket[$bundval['id'] . $bundval['name']] = $bundval;
            } else {
                $paket[$bundval['id'] . $bundval['name']]['value'] += $bundval['value'];
                $paket[$bundval['id'] . $bundval['name']]['qty'] += $bundval['qty'];
            }
        }

        $paket = array_values($paket);

        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=bundle$startdate-$enddate.xls");

        // export
        echo '<table>';
        echo '<thead>';
        echo '<tr>';
        echo '<th colspan="3" style="align-text:center;">Ringkasan Paket Terjual</th>';
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
        foreach ($paket as $bundle) {
            echo '<tr>';
            echo '<td>' . $bundle['name'] . '</td>';
            echo '<td>' . $bundle['qty'] . '</td>';
            echo '<td>' . $bundle['price'] . '</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
    }

    public function category()
    {
        // Calling models
        $TransactionModel   = new TransactionModel();
        $TrxdetailModel     = new TrxdetailModel();
        $ProductModel       = new ProductModel();
        $CategoryModel      = new CategoryModel();
        $VariantModel       = new VariantModel();
        $StockModel         = new StockModel();
        $BrandModel         = new BrandModel();
        $BundleModel        = new BundleModel();
        $OutletModel        = new OutletModel();

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
        $addres = '';
        if ($this->data['outletPick'] === null) {
            $transactions = $TransactionModel->where('date >=', $startdate)->where('date <=', $enddate)->find();
            $addres = "All Outlets";
            $outletname = "58vapehouse";
        } else {
            $transactions = $TransactionModel->where('date >=', $startdate)->where('date <=', $enddate)->where('outletid', $this->data['outletPick'])->find();
            $outlets = $OutletModel->find($this->data['outletPick']);
            $addres = $outlets['address'];
            $outletname = $outlets['name'];
        }

        $productval     = [];
        $variantvalue   = [];
        $variantval     = [];
        $trxvar         = [];
        $diskon         = [];
        $productqty     = [];
        $trxval         = [];
        $bundleval      = [];

        foreach ($transactions as $transaction) {
            $discounttrx = array();
            $discounttrxpersen = array();
            $discountvariant = array();
            $discountpoin = array();
            foreach ($trxdetails as $trxdetail) {
                foreach ($bundles as $bundle) {
                    if ($transaction['id'] === $trxdetail['transactionid'] && $bundle['id'] === $trxdetail['bundleid']) {
                        $bundleval[]   = [
                            'id'    => $bundle['id'],
                            'name'  => $bundle['name'],
                            'value' => $trxdetail['value'],
                        ];
                    }
                }
                if ($transaction['id'] === $trxdetail['transactionid']) {

                    if ($transaction['disctype'] === "0") {

                        $discounttrx[]          = $transaction['discvalue'];
                    }
                    if ($transaction['disctype'] !== "0") {

                        $sub = ($trxdetail['value']) * $trxdetail['qty'];
                        $discounttrxpersen[]    = $sub * ($transaction['discvalue'] / 100);
                    }
                    $discountvariant[]          = $trxdetail['discvar'];

                    $discountpoin[]             = $transaction['pointused'];

                    foreach ($products as $product) {
                        foreach ($variants as $variant) {
                            if (($variant['id'] === $trxdetail['variantid']) && ($variant['productid'] === $product['id'])) {
                                foreach ($products as $product) {
                                    if ($variant['productid'] === $product['id']) {
                                        $productval[] = $product['name'];
                                        foreach ($category as $cat) {
                                            if ($product['catid'] === $cat['id']) {
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

        $bundletotal = array_sum(array_column($bundleval, 'value'));

        $produk = [];
        foreach ($variantvalue as $vars) {
            if (!isset($produk[$vars['id'] . $vars['product']])) {
                $produk[$vars['id'] . $vars['product']] = $vars;
            } else {
                $produk[$vars['id'] . $vars['product']]['value'] += $vars['value'];
                $produk[$vars['id'] . $vars['product']]['qty'] += $vars['qty'];
            }
        }
        $produk = array_values($produk);
        // Total Stock
        $stoktotal = array_sum(array_column($produk, 'qty'));

        // Total Sales
        $salestotal = array_sum(array_column($produk, 'value'));

        // Total Gross
        $grosstotal = array_sum(array_column($produk, 'value'));

        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=category$startdate-$enddate.xls");

        // export
        echo '<table>';
        echo '<thead>';
        echo '<tr>';
        echo '<th colspan="4" style="align-text:center;">Ringkasan Kategori Produk Terjual</th>';
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
        foreach ($produk as $prod) {
            echo '<tr>';
            echo '<td>' . $prod['category'] . '</td>';
            echo '<td>' . $prod['qty'] . '</td>';
            echo '<td>' . $prod['value'] . '</td>';
            echo '<td>' . $prod['value'] . '</td>';
            echo '</tr>';
        }

        echo '<tr>';
        echo '<th style="align-text:left; font-family: arial, sans-serif; font-weight: bold;">Jumlah</th>';
        echo '<td style="align-text:left; font-family: arial, sans-serif; font-weight: bold;">' . $stoktotal . '</td>';
        echo '<td style="align-text:left; font-family: arial, sans-serif; font-weight: bold;">' . $salestotal . '</td>';
        echo '<td style="align-text:left; font-family: arial, sans-serif; font-weight: bold;">' . $grosstotal . '</td>';
        echo '</tr>';
        echo '</tbody>';
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
}
