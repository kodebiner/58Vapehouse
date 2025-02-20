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
use App\Models\DebtInsModel;
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
use App\Models\DailyReportModel;

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

            $exported   = $db->table('stock');
            $stockexp   = $exported->select('stock.qty as qty, variant.hargamodal as hargamodal, variant.hargadasar as hargadasar, variant.hargajual as hargajual, variant.hargarekomendasi as hargarekomendasi, variant.name as varname, product.name as prodname, category.name as catname, brand.name as brandname, variant.sku as sku');
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
        // Calling Models
        $BundleModel            = new BundleModel();
        $OutletModel            = new OutletModel();
        $UserModel              = new UserModel();
        $MemberModel            = new MemberModel();
        $PaymentModel           = new PaymentModel();
        $ProductModel           = new ProductModel();
        $VariantModel           = new VariantModel();
        $TransactionModel       = new TransactionModel();
        $TrxdetailModel         = new TrxdetailModel();
        $TrxpaymentModel        = new TrxpaymentModel();
        $DebtModel              = new DebtModel();
        $DebtInsModel           = new DebtInsModel();
        $GconfigModel           = new GconfigModel();

        $input  = $this->request->getGet('daterange');

        if (!empty($input)) {
            $daterange = explode(' - ', $input);
            $startdate = $daterange[0];
            $enddate = $daterange[1];
        } else {
            $startdate  = date('Y-m-1' . ' 00:00:00');
            $enddate    = date('Y-m-t' . ' 23:59:59');
        }

        // Populating Data
        if ($this->data['outletPick'] === null) {
            $transactions   = $TransactionModel->orderBy('date', 'DESC')->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->find();
            $bizzadd        = "All Outlets";
            $bizzname       = "58vapehouse";
        } else {
            $transactions   = $TransactionModel->orderBy('date', 'DESC')->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->where('outletid', $this->data['outletPick'])->find();
            $outletpick     = $OutletModel->find($this->data['outletPick']);
            $bizzadd        = $outletpick['address'];
            $bizzname       = $outletpick['name'];
        }

        $transactiondata    = [];
        if (!empty($transactions)) {
            foreach ($transactions as $trx) {
                // Outlet
                $outlets        = $OutletModel->find($trx['outletid']);
                $outletname     = $outlets['name'];
    
                // Cashier
                $cashier                                = $UserModel->find($trx['userid']);
    
                // Payment Method
                $payments       = $PaymentModel->find($trx['paymentid']);
                if (!empty($payments)) {
                    $paymentmethod  = $payments['name'];
                } else {
                    if (($trx['amountpaid'] == '0') && ($trx['paymentid'] == "0")) {
                        $paymentmethod  = lang('Global.debt');
                    } elseif ($trx['paymentid'] == "-1") {
                        $paymentmethod  = lang('Global.redeemPoint');
                    } elseif (($trx['amountpaid'] != '0') && ($trx['paymentid'] == "0")) {
                        $paymentmethod  = lang('Global.splitbill');
                    }
                }
    
                // Member
                $members    = $MemberModel->find($trx['memberid']);
                if (!empty($members)) {
                    $membername     = $members['name'];
                } else {
                    $membername     = '';
                }
    
                // Debt Data
                $debts      = $DebtModel->where('transactionid', $trx['id'])->find();
                if (!empty($debts)) {
                    foreach ($debts as $debt) {
                        $debtinst       = $DebtInsModel->where('debtid', $debt['id'])->where('transactionid', $trx['id'])->find();
                        $totaldebtin    = '';
                        if (!empty($debtinst)) {
                            $debtinval  = [];
                            foreach ($debtinst as $debtin) {
                                $debtinval[]    = $debtin['qty'];
                            }
                            $totaldebtin    = array_sum($debtinval);
                        }
                        $statustrx  = (Int)$trx['value'] - ((Int)$trx['amountpaid'] + (Int)$totaldebtin);
                        
                        if ($statustrx != '0') {
                            $paidstatus = '<div class="uk-text-danger" style="border-style: solid; border-color: #f0506e;">' . lang('Global.notpaid') . '</div>';
                        } else {
                            $paidstatus = '<div class="uk-text-success" style="border-style: solid; border-color: #32d296;">' . lang('Global.paid') . '</div>';
                        }
                    }
                } else {
                    $paidstatus = '<div class="uk-text-success" style="border-style: solid; border-color: #32d296;">' . lang('Global.paid') . '</div>';
                }
    
                // Transaction Data
                $transactiondata[$trx['id']]['id']              = $trx['id'];
                $transactiondata[$trx['id']]['date']            = $trx['date'];
                $transactiondata[$trx['id']]['outlet']          = $outletname;
                $transactiondata[$trx['id']]['cashier']         = $cashier->firstname.' '.$cashier->lastname;
                $transactiondata[$trx['id']]['payment']         = $paymentmethod;
                $transactiondata[$trx['id']]['value']           = $trx['value'];
                $transactiondata[$trx['id']]['amountpaid']      = $trx['amountpaid'];
                $transactiondata[$trx['id']]['paidstatus']      = $paidstatus;
                $transactiondata[$trx['id']]['date']            = $trx['date'];
                $transactiondata[$trx['id']]['trxdiscount']     = $trx['discvalue'];
                $transactiondata[$trx['id']]['memberdisc']      = $trx['memberdisc'];
                $transactiondata[$trx['id']]['pointused']       = $trx['pointused'];
                $transactiondata[$trx['id']]['membername']      = $membername;
    
                // Transaction Detail Data
                $subtotal = [];
                $trxdetails = $TrxdetailModel->where('transactionid', $trx['id'])->find();
                $totaldetail = count($trxdetails);
                $transactiondata[$trx['id']]['totaldetail']     = $totaldetail;
                $count  = '0';
                if (!empty($trxdetails)) {
                    foreach ($trxdetails as $trxdet) {
                        $subtotal[] = ((int)$trxdet['qty'] * (int)$trxdet['value']);
    
                        // Variant Data
                        if (($trxdet['variantid'] != '0') && ($trxdet['bundleid'] == '0')) {
                            $variants       = $VariantModel->find($trxdet['variantid']);
                            
                            if (!empty($variants)) {
                                $products   = $ProductModel->find($variants['productid']);
        
                                if (!empty($products)) {
                                    $transactiondata[$trx['id']]['detail'][$trxdet['id']]['sku']            = $variants['sku'];
                                    $transactiondata[$trx['id']]['detail'][$trxdet['id']]['name']           = $products['name'].' - '.$variants['name'];
                                    $transactiondata[$trx['id']]['detail'][$trxdet['id']]['qty']            = $trxdet['qty'];
                                    $transactiondata[$trx['id']]['detail'][$trxdet['id']]['value']          = (Int)$trxdet['value'] + ((Int)$trxdet['discvar'] / (Int)$trxdet['qty']) + ((Int)$trxdet['globaldisc'] / (Int)$trxdet['qty']);
                                    $transactiondata[$trx['id']]['detail'][$trxdet['id']]['total']          = ((Int)$trxdet['value'] + ((Int)$trxdet['discvar'] / (Int)$trxdet['qty']) + ((Int)$trxdet['globaldisc'] / (Int)$trxdet['qty'])) * (Int)$trxdet['qty'];
                                    $transactiondata[$trx['id']]['detail'][$trxdet['id']]['discitem']       = (Int)$trxdet['discvar'] / (Int)$trxdet['qty'];
                                    $transactiondata[$trx['id']]['detail'][$trxdet['id']]['discvar']        = $trxdet['discvar'];
                                    $transactiondata[$trx['id']]['detail'][$trxdet['id']]['globaldiscitem'] = (Int)$trxdet['globaldisc'] / (Int)$trxdet['qty'];
                                    $transactiondata[$trx['id']]['detail'][$trxdet['id']]['globaldisc']     = $trxdet['globaldisc'];
                                    $transactiondata[$trx['id']]['detail'][$trxdet['id']]['type']           = $count++;
                                } else {
                                    $transactiondata[$trx['id']]['detail'][$trxdet['id']]['sku']            = '-';
                                    $transactiondata[$trx['id']]['detail'][$trxdet['id']]['name']           = 'Kategori / Produk / Variant Terhapus';
                                    $transactiondata[$trx['id']]['detail'][$trxdet['id']]['qty']            = $trxdet['qty'];
                                    $transactiondata[$trx['id']]['detail'][$trxdet['id']]['value']          = (Int)$trxdet['value'] + ((Int)$trxdet['discvar'] / (Int)$trxdet['qty']) + ((Int)$trxdet['globaldisc'] / (Int)$trxdet['qty']);
                                    $transactiondata[$trx['id']]['detail'][$trxdet['id']]['total']          = ((Int)$trxdet['value'] + ((Int)$trxdet['discvar'] / (Int)$trxdet['qty']) + ((Int)$trxdet['globaldisc'] / (Int)$trxdet['qty'])) * (Int)$trxdet['qty'];
                                    $transactiondata[$trx['id']]['detail'][$trxdet['id']]['discitem']       = (Int)$trxdet['discvar'] / (Int)$trxdet['qty'];
                                    $transactiondata[$trx['id']]['detail'][$trxdet['id']]['discvar']        = $trxdet['discvar'];
                                    $transactiondata[$trx['id']]['detail'][$trxdet['id']]['globaldiscitem'] = (Int)$trxdet['globaldisc'] / (Int)$trxdet['qty'];
                                    $transactiondata[$trx['id']]['detail'][$trxdet['id']]['globaldisc']     = $trxdet['globaldisc'];
                                    $transactiondata[$trx['id']]['detail'][$trxdet['id']]['type']           = $count++;
                                }
                            } else {
                                $products   = [];
    
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['sku']                = '-';
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['name']               = 'Kategori / Produk / Variant Terhapus';
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['qty']                = $trxdet['qty'];
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['value']              = (Int)$trxdet['value'] + ((Int)$trxdet['discvar'] / (Int)$trxdet['qty']) + ((Int)$trxdet['globaldisc'] / (Int)$trxdet['qty']);
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['total']              = ((Int)$trxdet['value'] + ((Int)$trxdet['discvar'] / (Int)$trxdet['qty']) + ((Int)$trxdet['globaldisc'] / (Int)$trxdet['qty'])) * (Int)$trxdet['qty'];
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['discitem']           = (Int)$trxdet['discvar'] / (Int)$trxdet['qty'];
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['discvar']            = $trxdet['discvar'];
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['globaldiscitem']     = (Int)$trxdet['globaldisc'] / (Int)$trxdet['qty'];
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['globaldisc']         = $trxdet['globaldisc'];
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['type']               = $count++;
                            }
                        }
    
                        // Data Bundle
                        if (($trxdet['variantid'] == '0') && ($trxdet['bundleid'] != '0')) {
                            $bundles        = $BundleModel->find($trxdet['bundleid']);
    
                            if (!empty($bundles)) {
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['sku']                = '-';
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['name']               = $bundles['name'];
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['qty']                = $trxdet['qty'];
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['value']              = (Int)$trxdet['value'] + ((Int)$trxdet['globaldisc'] / (Int)$trxdet['qty']);
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['total']              = ((Int)$trxdet['value'] + ((Int)$trxdet['globaldisc'] / (Int)$trxdet['qty'])) * (Int)$trxdet['qty'];
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['discitem']           = (Int)$trxdet['discvar'] / (Int)$trxdet['qty'];
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['discvar']            = $trxdet['discvar'];
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['globaldiscitem']     = (Int)$trxdet['globaldisc'] / (Int)$trxdet['qty'];
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['globaldisc']         = $trxdet['globaldisc'];
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['type']               = $count++;
                            } else {
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['sku']                = '-';
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['name']               = 'Bundle Terhapus';
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['qty']                = $trxdet['qty'];
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['value']              = (Int)$trxdet['value'] + ((Int)$trxdet['globaldisc'] / (Int)$trxdet['qty']);
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['total']              = ((Int)$trxdet['value'] + ((Int)$trxdet['globaldisc'] / (Int)$trxdet['qty'])) * (Int)$trxdet['qty'];
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['discitem']           = (Int)$trxdet['discvar'] / (Int)$trxdet['qty'];
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['discvar']            = $trxdet['discvar'];
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['globaldiscitem']     = (Int)$trxdet['globaldisc'] / (Int)$trxdet['qty'];
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['globaldisc']         = $trxdet['globaldisc'];
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['type']               = $count++;
                            }
                        }
                    }
                } else {
                    $variants   = [];
                    $products   = [];
                    $bundles    = [];
                    $transactiondata[$trx['id']]['detail']  = [];
                }
    
                $transactiondata[$trx['id']]['totaldetailvalue']    = array_sum($subtotal);
            }
        }

        echo
        '<style type="text/css">
            .cntr {
                text-align: center;
                vertical-align: middle;
                border:1px solid black;
            }

            .middle {
                border:1px solid black;
                vertical-align: middle;
            }

            .th {
                text-align: center;
            }

            .thd {
                text-align: center;
                border:1px solid black;
            }

            th {
                text-align: center;
            }

            td {
                text-align: left;
            }
        </style>';

        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=Riwayat Transaksi $bizzname ($startdate-$enddate).xls");

        // export
        echo '<table>';
            echo '<thead>';
                echo '<tr>';
                    echo '<th colspan="16" class="th">' . $bizzname . '</th>';
                echo '</tr>';
                echo '<tr>';
                    echo '<th colspan="16" class="th">' . $bizzadd . '</th>';
                echo '</tr>';
                echo '<tr>';
                    echo '<th colspan="16" class="th">Ringkasan Transaksi ' . $startdate . ' - ' . $enddate . '</th>';
                echo '</tr>';
                echo '<tr>';
                    echo '<th colspan="16" class="th"></th>';
                echo '</tr>';
                echo '<tr>';
                    echo '<th class="thd">Tanggal</th>';
                    echo '<th class="thd">Jam</th>';
                    echo '<th class="thd">Nama Outlet</th>';
                    echo '<th class="thd">Nama Kasir</th>';
                    echo '<th class="thd">Nama Pelanggan</th>';
                    echo '<th class="thd">SKU</th>';
                    echo '<th class="thd">Produk</th>';
                    echo '<th class="thd">Jumlah Produk</th>';
                    echo '<th class="thd">Harga Produk</th>';
                    echo '<th class="thd">Diskon Varian Dari Transaksi</th>';
                    echo '<th class="thd">Diskon Varian Dari Pengaturan Usaha</th>';
                    echo '<th class="thd">Subtotal</th>';
                    echo '<th class="thd">Diskon Transaksi</th>';
                    echo '<th class="thd">Diskon Member</th>';
                    echo '<th class="thd">Redeem Point</th>';
                    echo '<th class="thd">Total</th>';
                    echo '<th class="thd">Metode Pembayaran</th>';
                echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
                foreach ($transactiondata as $trxdat) {
                    echo '<tr>';
                        echo '<td class="middle" rowspan="'.$trxdat['totaldetail'].'">' . date('d M Y', strtotime($trxdat['date'])) . '</td>';
                        echo '<td class="middle" rowspan="'.$trxdat['totaldetail'].'">' . date('H:i:s', strtotime($trxdat['date'])) . '</td>';
                        echo '<td class="middle" rowspan="'.$trxdat['totaldetail'].'">' . $trxdat['outlet'] . '</td>';
                        echo '<td class="middle" rowspan="'.$trxdat['totaldetail'].'">' . $trxdat['cashier'] . '</td>';
                        echo '<td class="middle" rowspan="'.$trxdat['totaldetail'].'">' . $trxdat['membername'] . '</td>';
                        foreach ($trxdat['detail'] as $detail) {
                            if ($detail['type'] > '0') {
                                echo '<tr>';
                                    echo '<td class="middle">' . $detail['sku'] . '</td>';
                                    echo '<td class="middle">' . $detail['name'] . '</td>';
                                    echo '<td class="cntr">' . $detail['qty'] . '</td>';
                                    echo '<td class="middle">' . $detail['value'] . '</td>';
                                    echo '<td class="middle">' . $detail['discitem'] . '</td>';
                                    echo '<td class="middle">' . $detail['globaldiscitem'] . '</td>';
                                    echo '<td class="middle">' . ($detail['value'] * $detail['qty']) - $detail['discvar'] - $detail['globaldisc'] . '</td>';
                                echo '</tr>';
                            } else {
                                echo '<td class="middle">' . $detail['sku'] . '</td>';
                                echo '<td class="middle">' . $detail['name'] . '</td>';
                                echo '<td class="cntr">' . $detail['qty'] . '</td>';
                                echo '<td class="middle">' . $detail['value'] . '</td>';
                                echo '<td class="middle">' . $detail['discitem'] . '</td>';
                                echo '<td class="middle">' . $detail['globaldiscitem'] . '</td>';
                                echo '<td class="middle">' . ($detail['value'] * $detail['qty']) - $detail['discvar'] - $detail['globaldisc'] . '</td>';
                                echo '<td class="middle" rowspan="'.$trxdat['totaldetail'].'">' . $trxdat['trxdiscount'] . '</td>';
                                echo '<td class="middle" rowspan="'.$trxdat['totaldetail'].'">' . $trxdat['memberdisc'] . '</td>';
                                echo '<td class="middle" rowspan="'.$trxdat['totaldetail'].'">' . $trxdat['pointused'] . '</td>';
                                echo '<td class="middle" rowspan="'.$trxdat['totaldetail'].'">' . $trxdat['value'] . '</td>';
                                echo '<td class="middle" rowspan="'.$trxdat['totaldetail'].'">' . $trxdat['payment'] . '</td>';
                            }
                        }
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
        $OutletModel            = new OutletModel();
        $TrxotherModel          = new TrxotherModel();

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

        foreach ($transaction as $trx) {
            $trxdetails  = $TrxdetailModel->where('transactionid', $trx['id'])->find();

            if (!empty($trx['discvalue'])) {
                $discounttrx[]  = $trx['discvalue'];
            }

            $discountpoin[]             = $trx['pointused'];
            $memberdisc[]               = $trx['memberdisc'];

            foreach ($trxdetails as $trxdetail) {
                // Transaction Detail Discount Variant
                if ($trxdetail['discvar'] != 0) {
                    $discountvariant[]      = $trxdetail['discvar'];
                }

                // Transaction Detail Discount Global
                if ($trxdetail['globaldisc'] != '0') {
                    $discountglobal[]       = $trxdetail['globaldisc'];
                }
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
        header("Content-Disposition: attachment; filename=Laporan Penjualan $outletname ($date1-$date2).xls");

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
                }
            }

            $totaldisc      = array_sum($discount);
            $marginmodalsum = array_sum($marginmodals);
            $margindasarsum = array_sum($margindasars);
            $transactions[] = [
                'date'      => date('d/m/y', $date),
                'modal'     => (Int)$marginmodalsum - (Int)$totaldisc,
                'dasar'     => (Int)$margindasarsum - (Int)$totaldisc,
            ];
        }

        $transactionarr[] = $transactions;

        $keuntunganmodal = array_sum(array_column($transactions, 'modal'));
        $keuntungandasar = array_sum(array_column($transactions, 'dasar'));

        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=Laporan Keuntungan .xls");

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
                
                $trxtotal           = array();
                $trxvalue           = array();
                if (!empty($transactions)) {
                    foreach ($transactions as $trx) {
                        $trxpayments    = $TrxpaymentModel->where('transactionid', $trx['id'])->where('paymentid', $payment['id'])->find();
                        
                        if (!empty($trxpayments)) {
                            foreach ($trxpayments as $trxpayment) {
                                $trxtotal[] = $trxpayment['id'];
                                $trxvalue[] = $trxpayment['value'];
                            }
                        }
                    }
                } else {
                    $trxpayments    = [];
                    $trxtotal[]     = [];
                    $trxvalue[]     = [];
                }
                $transactiondata[$payment['id']]['qty']         = count($trxtotal);
                $transactiondata[$payment['id']]['value']       = array_sum($trxvalue);
            }

            // Debt And Reedem Point
            $transactiondata[0]['name']                 = lang('Global.debt');
            $transactiondata[-1]['name']                = lang('Global.redeemPoint');

            $debttotal          = [];
            $debtvalue          = [];
            $pointtotal         = [];
            $pointvalue         = [];

            if (!empty($transactions)) {
                foreach ($transactions as $trx) {
                    $debtpayments   = $TrxpaymentModel->where('transactionid', $trx['id'])->where('paymentid', '0')->find();
                    $pointpayments  = $TrxpaymentModel->where('transactionid', $trx['id'])->where('paymentid', '-1')->find();
        
                    if (!empty($debtpayments)) {
                        foreach ($debtpayments as $debtpayment) {
                            $debttotal[] = $debtpayment['id'];
                            $debtvalue[] = $debtpayment['value'];
                        }
                    }
        
                    if (!empty($pointpayments)) {
                        foreach ($pointpayments as $pointpayment) {
                            $pointtotal[]   = $pointpayment['id'];
                            $pointvalue[]   = $pointpayment['value'];
                        }
                    }
                }
                $transactiondata[0]['qty']                      = count($debttotal);
                $transactiondata[0]['value']                    = array_sum($debtvalue);
                $transactiondata[-1]['qty']                     = count($pointtotal);
                $transactiondata[-1]['value']                   = array_sum($pointvalue);
            } else {
                $debtpayments   = [];
                $pointpayments  = [];
                $debttotal[]    = [];
                $debtvalue[]    = [];
                $pointtotal[]   = [];
                $pointvalue[]   = [];
            }

            $totalvalue = array_sum(array_column($transactiondata, 'value'));
            $totalqty = array_sum(array_column($transactiondata, 'qty'));

            header("Content-type: application/vnd-ms-excel");
            header("Content-Disposition: attachment; filename=Laporan Pembayaran $outletname ($startdate-$enddate).xls");
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
        $admin              = $UserModel->findAll();

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

        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=Laporan Pegawai ($startdate-$enddate).xls");

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

        $adress = [];
        if ($this->data['outletPick'] === null) {
            $transactions   = $TransactionModel->where('date >=', $startdate . " 00:00:00")->where('date <=', $enddate . " 23:59:59")->find();
            $outletname     = "All Outlets";
            $adress         = "58vapehouse";
        } else {
            $transactions   = $TransactionModel->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->where('outletid', $this->data['outletPick'])->find();
            $outlets        = $OutletModel->find($this->data['outletPick']);
            $outletname     = $outlets['name'];
            $adress         = $outlets['address'];
        }
        
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
                        $products   = $ProductModel->find($variants['productid']);

                        if (!empty($products)) {
                            $transactiondata[$products['id']]['name']           = $products['name'];
                            $category   = $CategoryModel->find($products['catid']);

                            if (!empty($category)) {
                                $transactiondata[$products['id']]['category']    = $category['name'];
                            }

                            $transactiondata[$products['id']]['qty'][]           = $trxdet['qty'];
                            $transactiondata[$products['id']]['netvalue'][]      = (((Int)$trxdet['value'] * (Int)$trxdet['qty'])) - ((Int)$discval + (Int)$discmem + (Int)$discpoin);
                            $transactiondata[$products['id']]['grossvalue'][]    = ((Int)$trxdet['value'] * (Int)$trxdet['qty']) + (Int)$trxdet['discvar'] + (Int)$trxdet['globaldisc'];
                        } else {
                            $category   = [];
                        }
                    } else {
                        $products   = [];
                        $category   = [];
                        $transactiondata[0]['name']         = 'Kategori / Produk / Variant Terhapus';
                        $transactiondata[0]['category']     = 'Kategori / Produk / Variant Terhapus';
                        $transactiondata[0]['qty'][]        = $trxdet['qty'];
                        $transactiondata[0]['netvalue'][]   = (((Int)$trxdet['value'] * (Int)$trxdet['qty'])) - ((Int)$discval + (Int)$discmem + (Int)$discpoin);
                        $transactiondata[0]['grossvalue'][] = ((Int)$trxdet['value'] * (Int)$trxdet['qty']) + (Int)$trxdet['discvar'] + (Int)$trxdet['globaldisc'];
                    }
                }
            } else {
                $variants   = [];
                $products   = [];
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
        header("Content-Disposition: attachment; filename=Laporan Penjualan Per Produk $outletname ($startdate-$enddate).xls");

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
        header("Content-Disposition: attachment; filename=Laporan Penjualan Per Kategori $outletname ($startdate-$enddate).xls");

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
        header("Content-Disposition: attachment; filename=Laporan Penjualan Per Bundle $outletname ($startdate-$enddate).xls");

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
        header("Content-Disposition: attachment; filename=Laporan Diskon $outletname ($startdate-$enddate).xls");

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
        $OutletModel    = new OutletMOdel;

        // Populating Data
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
        $presences      = $PresenceModel->where('datetime >=', $startdate . ' 00:00:00')->where('datetime <=', $enddate . ' 23:59:59')->find();
        
        if ($this->data['outletPick'] === null) {
            $addres     = "All Outlets";
            $outletname = "58vapehouse";
        } else {
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

            $presencedata[$date.$users->id.$shift]['id']       = $presence['id'];
            $presencedata[$date.$users->id.$shift]['date']     = $date;
            $presencedata[$date.$users->id.$shift]['name']     = $users->name;
            $presencedata[$date.$users->id.$shift]['role']     = $groups->name;
            $presencedata[$date.$users->id.$shift]['shift']    = $presence['shift'];

            $presencedata[$date.$users->id.$shift]['detail'][$status]['time']         = $time;
            $presencedata[$date.$users->id.$shift]['detail'][$status]['photo']        = $presence['photo'];
            $presencedata[$date.$users->id.$shift]['detail'][$status]['geoloc']       = $presence['geoloc'];
            $presencedata[$date.$users->id.$shift]['detail'][$status]['status']       = $presence['status'];
        }

        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=Presence Report $outletname ($startdate-$enddate).xls");

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
                        $waktu  = 'Siang (12:00)';
                    } elseif ($presence['shift'] == '2') {
                        $waktu  = 'Sore (16:00)';
                    } elseif ($presence['shift'] == '3') {
                        $waktu  = 'UGM (10:00)';
                    } elseif ($presence['shift'] == '4') {
                        $waktu  = 'Malam (00:00)';
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
                                    $kompensasi  = '12:15';
                                } elseif ($presence['shift'] == '2') {
                                    $kompensasi  = '16:15';
                                } elseif ($presence['shift'] == '3') {
                                    $kompensasi  = '10:15';
                                } elseif ($presence['shift'] == '4') {
                                    $kompensasi  = '00:15';
                                }
                                
                                if (str_replace(":","", $detail['time']) > str_replace(":","", $kompensasi)) {
                                    echo '<td>' . str_replace(":","", $detail['time']) - str_replace(":","", $kompensasi) . '</td>';
                                } else {
                                    echo '<td>0</td>';
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
            $members   = $MemberModel->like('name', $inputsearch)->orderBy('name', 'ASC')->find();
        } else {
            $members   = $MemberModel->orderBy('name', 'ASC')->find();
        }

        // Daterange Filter
        $input = $this->request->getGet('daterange');
        if (!empty($input)) {
            $daterange = explode(' - ', $input);
            $startdate = $daterange[0];
            $enddate = $daterange[1];
        } else {
            $startdate  = date('2023-01-01' . ' 00:00:00');
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

        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=Laporan Pelanggan $outletname ($startdate-$enddate).xls");

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
        // Daterange Filter
        $input = $this->request->getGet('daterange');
        if (!empty($input)) {
            $daterange  = explode(' - ', $input);
            $startdate  = $daterange[0];
            $enddate    = $daterange[1];
        } else {
            $startdate  = date('Y-m-1' . ' 00:00:00');
            $enddate    = date('Y-m-t' . ' 23:59:59');
        }

        if ($this->data['outletPick'] === null) {
            $sopdetails = $SopDetailModel->orderby('updated_at', 'ASC')->where('updated_at >=', $startdate . ' 00:00:00')->where('updated_at <=', $enddate . ' 23:59:59')->find();
            $addres     = "All Outlets";
            $outletname = "58vapehouse";
        } else {
            $sopdetails = $SopDetailModel->orderby('updated_at', 'ASC')->where('outletid', $this->data['outletPick'])->where('updated_at >=', $startdate . ' 00:00:00')->where('updated_at <=', $enddate . ' 23:59:59')->find();
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

    public function dayrep()
    {
        // Calling Models
        $TransactionModel   = new TransactionModel();
        $TrxpaymentModel    = new TrxpaymentModel();
        $TrxotherModel      = new TrxotherModel();
        $PaymentModel       = new PaymentModel();
        $UserModel          = new UserModel();
        $CashModel          = new CashModel();
        $OutletModel        = new OutletModel();
        $DailyReportModel   = new DailyReportModel();
        $MemberModel        = new MemberModel();
        $DebtInsModel       = new DebtInsModel();

        // Populating Data
        $input = $this->request->getGet('daterange');

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

        $cashdata       = $CashModel->where('outletid', $this->data['outletPick'])->find();
        $dailyreports   = $DailyReportModel->orderby('dateopen', 'DESC')->where('dateopen >=', $startdate . " 00:00:00")->where('dateopen <=', $enddate . " 23:59:59")->where('outletid', $this->data['outletPick'])->paginate(35, 'dailyreport');

        // Outlet
        $outlets        = $OutletModel->find($this->data['outletPick']);
        $outletname     = $outlets['name'];
        $address        = $outlets['address'];

        $dailyreportdata    = [];
        foreach ($dailyreports as $dayrep) {
            // Id
            $dailyreportdata[$dayrep['id']]['id']               = $dayrep['id'];

            // Outlet
            $dailyreportdata[$dayrep['id']]['outlet']           = $outletname;

            // Date
            $dailyreportdata[$dayrep['id']]['date']             = date('l, d M Y', strtotime($dayrep['dateopen']));

            // Date Open
            $dailyreportdata[$dayrep['id']]['dateopen']         = date('l, d M Y, H:i:s', strtotime($dayrep['dateopen']));

            // Transaction Data
            $transactions       = $TransactionModel->where('date >=', $dayrep['dateopen'])->where('date <=', $dayrep['dateclose'])->where('outletid', $this->data['outletPick'])->find();
            $totalproductsell   = [];

            // Date Closed
            if ($dayrep['dateclose'] != '0000-00-00 00:00:00') {
                $dailyreportdata[$dayrep['id']]['dateclose']    = date('l, d M Y, H:i:s', strtotime($dayrep['dateclose']));

                // User Close Store
                $userclose                                      = $UserModel->find($dayrep['useridclose']);
                $dailyreportdata[$dayrep['id']]['userclose']    = $userclose->firstname.' '.$userclose->lastname;

                // Transaction Data
                foreach ($transactions as $trx) {
                    // Cash, Non-Cash, Debt
                    $trxpayments    = $TrxpaymentModel->where('transactionid', $trx['id'])->where('paymentid !=', '0')->find();
                    $debtpayments   = $TrxpaymentModel->where('transactionid', $trx['id'])->where('paymentid', '0')->find();
                    $pointpayments  = $TrxpaymentModel->where('transactionid', $trx['id'])->where('paymentid', '-1')->find();

                    if (!empty($trxpayments)) {
                        foreach ($trxpayments as $trxpayment) {
                            $payment        = $PaymentModel->find($trxpayment['paymentid']);
                            if (!empty($payment)) {
                                $cashdata       = $CashModel->find($payment['cashid']);
    
                                if (strcmp($cashdata['name'], 'Petty Cash ' . $outlets['name']) == 0) {
                                    // Transaction Summary
                                    $dailyreportdata[$dayrep['id']]['trxpayments'][0]['name']                               = 'Tunai';
                                    $dailyreportdata[$dayrep['id']]['trxpayments'][0]['detail'][$trxpayment['id']]['type']  = '0';
                                    $dailyreportdata[$dayrep['id']]['trxpayments'][0]['detail'][$trxpayment['id']]['value'] = $trxpayment['value'];
                                } else {
                                    // Transaction Summary
                                    $dailyreportdata[$dayrep['id']]['trxpayments'][1]['name']                               = 'Non-Tunai';
                                    $dailyreportdata[$dayrep['id']]['trxpayments'][1]['detail'][$trxpayment['id']]['type']  = '1';
                                    $dailyreportdata[$dayrep['id']]['trxpayments'][1]['detail'][$trxpayment['id']]['value'] = $trxpayment['value'];
                                }
                            }
                        }
                    }

                    if (!empty($debtpayments)) {
                        foreach ($debtpayments as $debtpayment) {
                            // Transaction Summary
                            $dailyreportdata[$dayrep['id']]['trxpayments'][2]['name']               = 'Kasbon';
                            $dailyreportdata[$dayrep['id']]['trxpayments'][2]['detail'][$debtpayment['id']]['type']  = '2';
                            $dailyreportdata[$dayrep['id']]['trxpayments'][2]['detail'][$debtpayment['id']]['value'] = $debtpayment['value'];
                        }
                    }

                    if (!empty($pointpayments)) {
                        foreach ($pointpayments as $pointpayment) {
                            // Transaction Summary
                            $dailyreportdata[$dayrep['id']]['trxpayments'][3]['name']               = lang('Global.redeemPoint');
                            $dailyreportdata[$dayrep['id']]['trxpayments'][3]['detail'][$pointpayment['id']]['type']  = '3';
                            $dailyreportdata[$dayrep['id']]['trxpayments'][3]['detail'][$pointpayment['id']]['value'] = $pointpayment['value'];
                        }
                    }
                }

                // Actual Cash Close
                $dailyreportdata[$dayrep['id']]['cashclose']        = $dayrep['cashclose'];

                // Actual Non Cash Close
                $dailyreportdata[$dayrep['id']]['noncashclose']     = $dayrep['noncashclose'];

                // Actual Cashier Summary
                $dailyreportdata[$dayrep['id']]['actualsummary']    = (Int)$dayrep['cashclose'] + (Int)$dayrep['noncashclose'];
            } else {
                $dailyreportdata[$dayrep['id']]['dateclose']    = lang('Global.storeNotClosed');

                // User Close Store
                $dailyreportdata[$dayrep['id']]['userclose']    = lang('Global.storeNotClosed');

                // Payment Methods
                $dailyreportdata[$dayrep['id']]['payments']     = [];
                $dailyreportdata[$dayrep['id']]['trxpayments']  = [];

                // Actual Cash Close
                $dailyreportdata[$dayrep['id']]['cashclose']        = '0';

                // Actual Non Cash Close
                $dailyreportdata[$dayrep['id']]['noncashclose']     = '0';

                // Actual Cashier Summary
                $dailyreportdata[$dayrep['id']]['actualsummary']    = (Int)$dayrep['cashclose'] + (Int)$dayrep['noncashclose'];
            }

            // User Open Store
            $useropen                                           = $UserModel->find($dayrep['useridopen']);
            $dailyreportdata[$dayrep['id']]['useropen']         = $useropen->firstname.' '.$useropen->lastname;

            // Total Prodcuct Sell
            $dailyreportdata[$dayrep['id']]['totalproductsell'] = array_sum($totalproductsell);

            // Cash Flow
            $trxothers  = $TrxotherModel->where('date >=', $dayrep['dateopen'])->where('date <=', $dayrep['dateclose'])->where('outletid', $this->data['outletPick'])->notLike('description', 'Debt')->notLike('description', 'Top Up')->find();
            $debtins    = $TrxotherModel->where('date >=', $dayrep['dateopen'])->where('date <=', $dayrep['dateclose'])->where('outletid', $this->data['outletPick'])->Like('description', 'Debt')->find();
            $topups     = $TrxotherModel->where('date >=', $dayrep['dateopen'])->where('date <=', $dayrep['dateclose'])->where('outletid', $this->data['outletPick'])->Like('description', 'Top Up')->find();
            $withdraws  = $TrxotherModel->where('date >=', $dayrep['dateopen'])->where('date <=', $dayrep['dateclose'])->where('outletid', $this->data['outletPick'])->Like('description', 'Cash Withdraw')->find();

            if (!empty($trxothers)) {
                foreach ($trxothers as $trxother) {
                    // Cashflow Data
                    $dailyreportdata[$dayrep['id']]['cashflow'][$trxother['id']]['type']    = $trxother['type'];
                    $dailyreportdata[$dayrep['id']]['cashflow'][$trxother['id']]['qty']     = $trxother['qty'];
                }
            } else {
                $dailyreportdata[$dayrep['id']]['cashflow'] = [];
            }

            // Debt Installment
            if (!empty($debtins)) {
                foreach ($debtins as $debtin) {
                    // Debt Installment Data
                    $cashdebt       = $CashModel->find($debtin['cashid']);

                    if (strcmp($cashdebt['name'], 'Petty Cash ' . $outlets['name']) == 0) {
                        // Transaction Summary
                        $dailyreportdata[$dayrep['id']]['debtins'][0]['name']                               = 'Tunai';
                        $dailyreportdata[$dayrep['id']]['debtins'][0]['detail'][$debtin['id']]['type']      = '0';
                        $dailyreportdata[$dayrep['id']]['debtins'][0]['detail'][$debtin['id']]['value']     = $debtin['qty'];
                    } else {
                        // Transaction Summary
                        $dailyreportdata[$dayrep['id']]['debtins'][1]['name']                               = 'Non-Tunai';
                        $dailyreportdata[$dayrep['id']]['debtins'][1]['detail'][$debtin['id']]['type']      = '1';
                        $dailyreportdata[$dayrep['id']]['debtins'][1]['detail'][$debtin['id']]['value']     = $debtin['qty'];
                    }
                }
            }
            else {
                $debtinst    = $DebtInsModel->where('date >=', $dayrep['dateopen'])->where('date <=', $dayrep['dateclose'])->where('outletid', $this->data['outletPick'])->find();
                if (!empty($debtinst)) {
                    foreach ($debtinst as $debtinstall) {
                        // Debt Installment Data
                        $paymentins     = $PaymentModel->find($debtinstall['paymentid']);
                        $cashdebtins    = $CashModel->find($paymentins['cashid']);
    
                        if (strcmp($cashdebtins['name'], 'Petty Cash ' . $outlets['name']) == 0) {
                            $dailyreportdata[$dayrep['id']]['debtins'][0]['name']                               = 'Tunai';
                            $dailyreportdata[$dayrep['id']]['debtins'][0]['detail'][$debtinstall['id']]['type']      = '0';
                            $dailyreportdata[$dayrep['id']]['debtins'][0]['detail'][$debtinstall['id']]['value']     = $debtinstall['qty'];
                        } else {
                            $dailyreportdata[$dayrep['id']]['debtins'][1]['name']                               = 'Non-Tunai';
                            $dailyreportdata[$dayrep['id']]['debtins'][1]['detail'][$debtinstall['id']]['type']      = '1';
                            $dailyreportdata[$dayrep['id']]['debtins'][1]['detail'][$debtinstall['id']]['value']     = $debtinstall['qty'];
                        }
                    }
                } else {
                    $dailyreportdata[$dayrep['id']]['debtins'] = [];
                }
            }

            if (!empty($topups)) {
                foreach ($topups as $topup) {
                    // Top Up Data
                    $cashtopup      = $CashModel->find($topup['cashid']);

                    if (strcmp($cashtopup['name'], 'Petty Cash ' . $outlets['name']) == 0) {
                        // Transaction Summary
                        $dailyreportdata[$dayrep['id']]['topup'][0]['name']                             = 'Tunai';
                        $dailyreportdata[$dayrep['id']]['topup'][0]['detail'][$topup['id']]['type']     = '0';
                        $dailyreportdata[$dayrep['id']]['topup'][0]['detail'][$topup['id']]['value']    = $topup['qty'];
                    } else {
                        // Transaction Summary
                        $dailyreportdata[$dayrep['id']]['topup'][1]['name']                             = 'Non-Tunai';
                        $dailyreportdata[$dayrep['id']]['topup'][1]['detail'][$topup['id']]['type']     = '1';
                        $dailyreportdata[$dayrep['id']]['topup'][1]['detail'][$topup['id']]['value']    = $topup['qty'];
                    }
                }
            } else {
                $dailyreportdata[$dayrep['id']]['topup'] = [];
            }

            if (!empty($withdraws)) {
                foreach ($withdraws as $withdraw) {
                    // Withdraw Data
                    $cashwithdraw   = $CashModel->find($withdraw['cashid']);
                    $dailyreportdata[$dayrep['id']]['withdraw'][$cashwithdraw['id']]['name']                                = $cashwithdraw['name'];

                    // Detail Withdraw
                    $dailyreportdata[$dayrep['id']]['withdraw'][$cashwithdraw['id']]['detail'][$withdraw['id']]['value']    = $withdraw['qty'];
                }
            } else {
                $dailyreportdata[$dayrep['id']]['withdraw'] = [];
            }

            // Initial Cash
            $dailyreportdata[$dayrep['id']]['initialcash']      = $dayrep['initialcash'];

            // Total Cash In
            $dailyreportdata[$dayrep['id']]['totalcashin']      = $dayrep['totalcashin'];

            // Total Cash Out
            $dailyreportdata[$dayrep['id']]['totalcashout']     = $dayrep['totalcashout'];
        }
        
        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=Laporan Harian $date1-$date2.xls");

        echo '<table>';
            echo '<thead>';
                echo '<tr>';
                    echo '<th colspan="17" style="align-text:center;">Laporan Harian</th>';
                echo '</tr>';
                echo '<tr>';
                    echo '<th colspan="17" style="align-text:center;">' . $outletname . '</th>';
                echo '</tr>';
                echo '<tr>';
                    echo '<th colspan="17" style="align-text:center;">' . $address . '</th>';
                echo '</tr>';
                echo '<tr>';
                    echo '<th colspan="17" style="align-text:center;">' . date('d M Y', strtotime($startdate)) . ' - ' . date('d M Y', strtotime($enddate)) . '</th>';
                echo '</tr>';
                echo '<tr>';
                    echo '<th colspan="17" style="align-text:center;"></th>';
                echo '</tr>';
                echo '<tr>';
                    echo '<th rowspan="2">Tanggal</th>';
                    echo '<th colspan="3">Arus Kas</th>';
                    echo '<th colspan="4">Penjualan</th>';
                    echo '<th colspan="2">Angsuran Hutang</th>';
                    echo '<th colspan="2">Top Up</th>';
                    echo '<th rowspan="2">Tarik Tunai</th>';
                    echo '<th colspan="2">Penerimaan Sistem</th>';
                    echo '<th colspan="2">Penerimaan Aktual</th>';
                    echo '<th rowspan="2">Selisih</th>';
                echo '</tr>';
                echo '<tr>';
                    echo '<th>Modal</th>';
                    echo '<th>Masuk</th>';
                    echo '<th>Keluar</th>';
                    echo '<th>Tunai</th>';
                    echo '<th>Non-Tunai</th>';
                    echo '<th>Kasbon</th>';
                    echo '<th>Tukar Poin</th>';
                    echo '<th>Tunai</th>';
                    echo '<th>Non-Tunai</th>';
                    echo '<th>Tunai</th>';
                    echo '<th>Non-Tunai</th>';
                    echo '<th>Tunai</th>';
                    echo '<th>Non-Tunai</th>';
                    echo '<th>Tunai</th>';
                    echo '<th>Non-Tunai</th>';
                echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
                foreach ($dailyreportdata as $dayrep) {
                    echo '<tr>';
                        // Date
                        echo '<td>' . date('l, d M Y', strtotime($dayrep['date'])) . '</td>';
                        // Date End

                        // Cashflow
                        echo '<td>' . $dayrep['initialcash'] . '</td>';
                        $totalcashin    = [];
                        $totalcashout   = [];
                        foreach ($dayrep['cashflow'] as $cashflow) {
                            if ($cashflow['type'] == '0') {
                                $totalcashin[]  = $cashflow['qty'];
                            } else {
                                $totalcashout[] = $cashflow['qty'];
                            }
                        }
                        $summarycashin  = array_sum($totalcashin);
                        $summarycashout = array_sum($totalcashout);
                        echo '<td>' . $summarycashin . '</td>';
                        echo '<td>' . $summarycashout . '</td>';
                        // Cashflow End

                        // Transaction Data
                        $totaltrxcash       = [];
                        $totaltrxnoncash    = [];
                        $totaltrxdebt       = [];
                        $totaltrxpoin       = [];
                        foreach ($dayrep['trxpayments'] as $trxpayment) {
                            $trxcash    = [];
                            $trxnoncash = [];
                            $trxdebt    = [];
                            $trxpoin    = [];
                            foreach ($trxpayment['detail'] as $detail) {
                                if ($detail['type'] == '0') {
                                    $trxcash[] = $detail['value'];
                                }
                                if ($detail['type'] == '1') {
                                    $trxnoncash[]  = $detail['value'];
                                }
                                if ($detail['type'] == '2') {
                                    $trxdebt[] = $detail['value'];
                                }
                                if ($detail['type'] == '3') {
                                    $trxpoin[] = $detail['value'];
                                }
                                $paymethodval[] = $detail['value'];
                            }
                            $arraytrxcash       = array_sum($trxcash);
                            $totaltrxcash[]     = $arraytrxcash;
                            $arraytrxnoncash    = array_sum($trxnoncash);
                            $totaltrxnoncash[]  = $arraytrxnoncash;
                            $arraytrxdebt       = array_sum($trxdebt);
                            $totaltrxdebt[]     = $arraytrxdebt;
                            $arraytrxpoin       = array_sum($trxpoin);
                            $totaltrxpoin[]     = $arraytrxpoin;
                        }
                        $totalcash      = array_sum($totaltrxcash);
                        $totalnoncash   = array_sum($totaltrxnoncash);
                        $totaldebt      = array_sum($totaltrxdebt);
                        $totalpoin      = array_sum($totaltrxpoin);
                        echo '<td>' . $totalcash . '</td>';
                        echo '<td>' . $totalnoncash . '</td>';
                        echo '<td>' . $totaldebt . '</td>';
                        echo '<td>' . $totalpoin . '</td>';
                        // Transaction Data End

                        // Debt Installment
                        $totaldebtcash      = [];
                        $totaldebtnoncash   = [];
                        foreach ($dayrep['debtins'] as $debtins) {
                            $debtcash       = [];
                            $debtnoncash    = [];
                            foreach ($debtins['detail'] as $debtdetail) {
                                if ($debtdetail['type'] == '0') {
                                    $debtcash[]    = $debtdetail['value'];
                                } else {
                                    $debtnoncash[] = $debtdetail['value'];
                                }
                            }
                            $arraydebtcash      = array_sum($debtcash);
                            $totaldebtcash[]    = $arraydebtcash;
                            $arraydebtnoncash   = array_sum($debtnoncash);
                            $totaldebtnoncash[] = $arraydebtnoncash;
                        }
                        $totaldebtcashvalue     = array_sum($totaldebtcash);
                        $totaldebtnoncashvalue  = array_sum($totaldebtnoncash);
                        echo '<td>' . $totaldebtcashvalue . '</td>';
                        echo '<td>' . $totaldebtnoncashvalue . '</td>';
                        // Debt Installment End
                        
                        // Top Up
                        $totaltopupcash      = [];
                        $totaltopupnoncash   = [];
                        foreach ($dayrep['topup'] as $topup) {
                            $topupcash       = [];
                            $topupnoncash    = [];
                            foreach ($topup['detail'] as $topupdetail) {
                                if ($topupdetail['type'] == '0') {
                                    $topupcash[]    = $topupdetail['value'];
                                } else {
                                    $topupnoncash[] = $topupdetail['value'];
                                }
                            }
                            $arraytopupcash      = array_sum($topupcash);
                            $totaltopupcash[]    = $arraytopupcash;
                            $arraytopupnoncash   = array_sum($topupnoncash);
                            $totaltopupnoncash[] = $arraytopupnoncash;
                        }
                        $totaltopupcashvalue     = array_sum($totaltopupcash);
                        $totaltopupnoncashvalue  = array_sum($totaltopupnoncash);
                        echo '<td>' . $totaltopupcashvalue . '</td>';
                        echo '<td>' . $totaltopupnoncashvalue . '</td>';
                        // Top Up End

                        // Cash Withdraw
                        $totalwithdraw      = [];
                        foreach ($dayrep['withdraw'] as $withdraw) {
                            $datawithdraw       = [];
                            foreach ($withdraw['detail'] as $withdrawdetail) {
                                $datawithdraw[]    = $withdrawdetail['value'];
                            }
                            $arraydatawithdraw      = array_sum($datawithdraw);
                            $totalwithdraw[]    = $arraydatawithdraw;
                        }
                        $totalwithdrawvalue     = array_sum($totalwithdraw);
                        echo '<td>' . $totalwithdrawvalue . '</td>';
                        // Cash Withdraw End

                        // System Receive
                        $systemreceivecash      = (Int)$totalcash + ((Int)$dayrep['initialcash'] + ((Int)$summarycashin - (Int)$summarycashout)) + (Int)$totaldebtcashvalue + (Int)$totaltopupcashvalue;
                        $systemreceivenoncash   = (Int)$totalnoncash + (Int)$totaldebtnoncashvalue + (Int)$totaltopupnoncashvalue + (Int)$totalwithdrawvalue;
                        $systemreceivetotal     = (Int)$systemreceivecash + (Int)$systemreceivenoncash;
                        echo '<td>' . $systemreceivecash . '</td>';
                        echo '<td>' . $systemreceivenoncash . '</td>';
                        // System Receive End

                        // Actual Receive
                        echo '<td>' . $dayrep['cashclose'] . '</td>';
                        echo '<td>' . $dayrep['noncashclose'] . '</td>';
                        // Actual Receive End

                        // Difference
                        $totaldifference    = (Int)$dayrep['actualsummary'] - (Int)$systemreceivetotal;
                        echo '<td>' . $totaldifference . '</td>';
                        // Difference End
                    echo '</tr>';
                }
            echo '</tbody>';
        echo '</table>';
    }
}
