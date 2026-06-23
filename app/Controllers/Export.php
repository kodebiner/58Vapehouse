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
use App\Models\OutletModel;
use App\Models\GroupUserModel;
use Myth\Auth\Models\GroupModel;
use App\Models\DebtModel;
use App\Models\DebtInsModel;
use App\Models\MemberModel;
use App\Models\PaymentModel;
use App\Models\TransactionModel;
use App\Models\TrxdetailModel;
use App\Models\TrxpaymentModel;
use App\Models\TrxotherModel;
use App\Models\PresenceModel;
use App\Models\SopModel;
use App\Models\SopDetailModel;
use App\Models\DailyReportModel;
use App\Models\CheckpointModel;
use App\Models\PurchaseModel;
use App\Models\PurchasedetailModel;
use App\Models\SupplierModel;
use DateTime;

class export extends BaseController
{
    protected $data;
    protected $db, $builder;
    protected $auth;
    protected $config;

    public function prod()
    {
        // Calling Model
        $CategoryModel  = new CategoryModel();
        $ProductModel   = new ProductModel();
        $BrandModel     = new BrandModel();
        $VariantModel   = new VariantModel();
        $OutletModel    = new OutletModel();
        $StockModel     = new StockModel();

        // Populating Data
        $products   = $ProductModel->findAll();

        if ($this->data['outletPick'] === null) {
            $bizzadd        = "All Outlets";
            $bizzname       = "58 Vapehouse AOT";
        } else {
            $outletpick     = $OutletModel->find($this->data['outletPick']);
            $bizzadd        = $outletpick['address'];
            $bizzname       = $outletpick['name'];
        }

        $productdata    = [];
        foreach ($products as $product) {
            $variants   = $VariantModel->where('productid', $product['id'])->find();
            $categories = $CategoryModel->find($product['catid']);
            $brands     = $BrandModel->find($product['brandid']);

            if (!empty($variants)) {
                foreach ($variants as $variant) {
                    if ($this->data['outletPick'] === null) {
                        $stocks      = $StockModel->where('variantid', $variant['id'])->find();
                    } else {
                        $stocks      = $StockModel->where('variantid', $variant['id'])->where('outletid', $this->data['outletPick'])->find();
                    }

                    $toqty = 0;
                    if (!empty($stocks)) {
                        foreach ($stocks as $stock) {
                            $toqty += $stock['qty'];
                            $restock    = $stock['restock'];
                        }
                    }

                    if (!empty($brands)) {
                        if ($brands['status'] == '1') {
                            $brandstatus = 'Aktif';
                        } else {
                            $brandstatus = 'Tidak Aktif';
                        }
                        
                        $brandname  = $brands['name'] . ' (' . $brandstatus . ')';
                    } else {
                        $brandname  = 'Tidak Ada Brand';
                    }
        
                    if (!empty($categories)) {
                        if ($categories['status'] == '1') {
                            $catstatus = 'Aktif';
                        } else {
                            $catstatus = 'Tidak Aktif';
                        }
                        $catname  = $categories['name'].' ('.$catstatus.')';
                    } else {
                        $catname  = 'Tidak Ada Kategori';
                    }

                    $productdata[$variant['id']]['name']            = $product['name'].' '.$variant['name'];
                    $productdata[$variant['id']]['sku']             = $variant['sku'];
                    $productdata[$variant['id']]['sellprice']       = (Int)$variant['hargamodal'] + (Int)$variant['hargajual'];
                    $productdata[$variant['id']]['baseprice']       = $variant['hargadasar'];
                    $productdata[$variant['id']]['capitalprice']    = $variant['hargamodal'];
                    $productdata[$variant['id']]['msrp']            = $variant['hargarekomendasi'];
                    $productdata[$variant['id']]['status']          = $product['status'];
                    $productdata[$variant['id']]['photo']           = $product['photo'];
                    $productdata[$variant['id']]['link']            = $product['link'];
                    $productdata[$variant['id']]['brand']           = $brandname;
                    $productdata[$variant['id']]['category']        = $catname;
                    $productdata[$variant['id']]['qty']             = $toqty;
                    $productdata[$variant['id']]['restock']         = $restock;
                }
            }
        }
        array_multisort(array_column($productdata, 'name'), SORT_ASC, $productdata);

        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=Product $bizzname.xls");

        // export
        echo '<table>';
            echo '<tr>';
                echo '<th colspan="12" style="align-text:center;">Produk</th>';
            echo '</tr>';
            echo '<tr>';
                echo '<th colspan="12" style="align-text:center;">' . $bizzname . '</th>';
            echo '</tr>';
            echo '<tr>';
                echo '<th colspan="12" class="th">' . $bizzadd . '</th>';
            echo '</tr>';
            echo '<thead>';
                echo '<tr>';
                echo '<th>SKU</th>';
                echo '<th>Status</th>';
                echo '<th>Keterangan</th>';
                echo '<th>Nama</th>';
                echo '<th>Merek</th>';
                echo '<th>Kategori</th>';
                echo '<th>Harga Jual</th>';
                echo '<th>Harga Dasar</th>';
                echo '<th>Harga Modal</th>';
                echo '<th>Harga Rekomendasi</th>';
                echo '<th>Stok</th>';
                echo '<th>Umur Produk</th>';
                echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
                foreach ($productdata as $prods) {
                    echo '<tr>';
                        echo '<td>' . $prods['sku'] . '</td>';
                        if ($prods['status'] == '1') {
                            echo '<td>Aktif</td>';
                        } else {
                            echo '<td>Tidak Aktif</td>';
                        }
                        echo '<td>';
                            if ($prods['photo'] == null) {
                                echo '<div style="color: red;">Belum Ada Foto</div>';
                            } if ($prods['link'] == null) {
                                echo '<div style="color: red;">Belum Ada Link Tokopedia</div>';
                            }
                        echo '</td>';
                        echo '<td>' . $prods['name'] . '</td>';
                        echo '<td>' . $prods['brand'] . '</td>';
                        echo '<td>' . $prods['category'] . '</td>';
                        echo '<td>' . $prods['sellprice'] . '</td>';
                        echo '<td>' . $prods['baseprice'] . '</td>';
                        echo '<td>' . $prods['capitalprice'] . '</td>';
                        echo '<td>' . $prods['msrp'] . '</td>';
                        echo '<td>' . $prods['qty'] . '</td>';
                        echo '<td>';
                            if ($prods['qty'] == 0) {
                                echo '-';
                            } else {
                                // Check if restock is null or empty or zero-date string
                                if (empty($prods['restock']) || $prods['restock'] == '0000-00-00 00:00:00') {
                                    echo '-';
                                } else {
                                    $origin         = new DateTime($prods['restock']);
                                    $target         = new DateTime('now');
                                    $interval       = $origin->diff($target);
                                    $formatday      = substr($interval->format('%R%a'), 1);
                                    echo $formatday . ' ' . lang('Global.day');
                                }
                            }
                        echo '</td>';
                    echo '</tr>';
                }
            echo '</tbody>';
        echo '</table>';
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
        $BrandModel             = new BrandModel();
        $CategoryModel          = new CategoryModel();
        $VariantModel           = new VariantModel();
        $TransactionModel       = new TransactionModel();
        $TrxdetailModel         = new TrxdetailModel();
        $DebtModel              = new DebtModel();
        $DebtInsModel           = new DebtInsModel();

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
                    $membername     = $members['name'].' / '.$members['phone'];
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
                        $subtotal[] = ((int)$trxdet['qty'] * (float)$trxdet['value']);
    
                        // Variant Data
                        if (($trxdet['variantid'] != '0') && ($trxdet['bundleid'] == '0')) {
                            $variants       = $VariantModel->find($trxdet['variantid']);
                            
                            if (!empty($variants)) {
                                $products   = $ProductModel->find($variants['productid']);
        
                                if (!empty($products)) {
                                    $brands     = $BrandModel->find($products['brandid']);
                                    $category   = $CategoryModel->find($products['catid']);

                                    if (!empty($brands)) {
                                        if ($brands['status'] == '1') {
                                            $brandstatus = 'Aktif';
                                        } else {
                                            $brandstatus = 'Tidak Aktif';
                                        }
                                        
                                        $brandname  = $brands['name'] . ' (' . $brandstatus . ')';
                                    } else {
                                        $brandname  = 'Merek Terhapus';
                                    }

                                    if (!empty($category)) {
                                        if ($category['status'] == '1') {
                                            $catstatus = 'Aktif';
                                        } else {
                                            $catstatus = 'Tidak Aktif';
                                        }
                                        $catname  = $category['name'].' ('.$catstatus.')';
                                    } else {
                                        $catname    = 'Kategori Terhapus';
                                    }
                                    
                                    $transactiondata[$trx['id']]['detail'][$trxdet['id']]['sku']            = $variants['sku'];
                                    $transactiondata[$trx['id']]['detail'][$trxdet['id']]['name']           = $products['name'].' - '.$variants['name'];
                                    $transactiondata[$trx['id']]['detail'][$trxdet['id']]['brand']          = $brandname;
                                    $transactiondata[$trx['id']]['detail'][$trxdet['id']]['category']       = $catname;
                                    $transactiondata[$trx['id']]['detail'][$trxdet['id']]['qty']            = $trxdet['qty'];
                                    $transactiondata[$trx['id']]['detail'][$trxdet['id']]['value']          = (float)$trxdet['value'] + ((Int)$trxdet['discvar'] / (Int)$trxdet['qty']) + ((Int)$trxdet['globaldisc'] / (Int)$trxdet['qty']) + ((Int)$trxdet['memberdisc'] / (Int)$trxdet['qty']);
                                    $transactiondata[$trx['id']]['detail'][$trxdet['id']]['total']          = ((float)$trxdet['value'] + ((Int)$trxdet['discvar'] / (Int)$trxdet['qty']) + ((Int)$trxdet['globaldisc'] / (Int)$trxdet['qty']) + ((Int)$trxdet['memberdisc'] / (Int)$trxdet['qty'])) * (Int)$trxdet['qty'];
                                    $transactiondata[$trx['id']]['detail'][$trxdet['id']]['discitem']       = (Int)$trxdet['discvar'] / (Int)$trxdet['qty'];
                                    $transactiondata[$trx['id']]['detail'][$trxdet['id']]['discvar']        = $trxdet['discvar'];
                                    $transactiondata[$trx['id']]['detail'][$trxdet['id']]['globaldiscitem'] = (Int)$trxdet['globaldisc'] / (Int)$trxdet['qty'];
                                    $transactiondata[$trx['id']]['detail'][$trxdet['id']]['globaldisc']     = $trxdet['globaldisc'];
                                    $transactiondata[$trx['id']]['detail'][$trxdet['id']]['memberdiscitem'] = (Int)$trxdet['memberdisc'] / (Int)$trxdet['qty'];
                                    $transactiondata[$trx['id']]['detail'][$trxdet['id']]['memberdisc']     = $trxdet['memberdisc'];
                                    $transactiondata[$trx['id']]['detail'][$trxdet['id']]['type']           = $count++;
                                } else {
                                    $transactiondata[$trx['id']]['detail'][$trxdet['id']]['sku']            = '-';
                                    $transactiondata[$trx['id']]['detail'][$trxdet['id']]['name']           = 'Kategori / Produk / Variant Terhapus';
                                    $transactiondata[$trx['id']]['detail'][$trxdet['id']]['brand']          = 'Merek Terhapus';
                                    $transactiondata[$trx['id']]['detail'][$trxdet['id']]['category']       = 'Kategori Terhapus';
                                    $transactiondata[$trx['id']]['detail'][$trxdet['id']]['qty']            = $trxdet['qty'];
                                    $transactiondata[$trx['id']]['detail'][$trxdet['id']]['value']          = (float)$trxdet['value'] + ((Int)$trxdet['discvar'] / (Int)$trxdet['qty']) + ((Int)$trxdet['globaldisc'] / (Int)$trxdet['qty']) + ((Int)$trxdet['memberdisc'] / (Int)$trxdet['qty']);
                                    $transactiondata[$trx['id']]['detail'][$trxdet['id']]['total']          = ((float)$trxdet['value'] + ((Int)$trxdet['discvar'] / (Int)$trxdet['qty']) + ((Int)$trxdet['globaldisc'] / (Int)$trxdet['qty']) + ((Int)$trxdet['memberdisc'] / (Int)$trxdet['qty'])) * (Int)$trxdet['qty'];
                                    $transactiondata[$trx['id']]['detail'][$trxdet['id']]['discitem']       = (Int)$trxdet['discvar'] / (Int)$trxdet['qty'];
                                    $transactiondata[$trx['id']]['detail'][$trxdet['id']]['discvar']        = $trxdet['discvar'];
                                    $transactiondata[$trx['id']]['detail'][$trxdet['id']]['globaldiscitem'] = (Int)$trxdet['globaldisc'] / (Int)$trxdet['qty'];
                                    $transactiondata[$trx['id']]['detail'][$trxdet['id']]['globaldisc']     = $trxdet['globaldisc'];
                                    $transactiondata[$trx['id']]['detail'][$trxdet['id']]['memberdiscitem'] = (Int)$trxdet['memberdisc'] / (Int)$trxdet['qty'];
                                    $transactiondata[$trx['id']]['detail'][$trxdet['id']]['memberdisc']     = $trxdet['memberdisc'];
                                    $transactiondata[$trx['id']]['detail'][$trxdet['id']]['type']           = $count++;
                                }
                            } else {
                                $products   = [];
    
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['sku']                = '-';
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['name']               = 'Kategori / Produk / Variant Terhapus';
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['brand']              = 'Merek Terhapus';
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['category']           = 'Kategori Terhapus';
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['qty']                = $trxdet['qty'];
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['value']              = (float)$trxdet['value'] + ((Int)$trxdet['discvar'] / (Int)$trxdet['qty']) + ((Int)$trxdet['globaldisc'] / (Int)$trxdet['qty']) + ((Int)$trxdet['memberdisc'] / (Int)$trxdet['qty']);
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['total']              = ((float)$trxdet['value'] + ((Int)$trxdet['discvar'] / (Int)$trxdet['qty']) + ((Int)$trxdet['globaldisc'] / (Int)$trxdet['qty']) + ((Int)$trxdet['memberdisc'] / (Int)$trxdet['qty'])) * (Int)$trxdet['qty'];
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['discitem']           = (Int)$trxdet['discvar'] / (Int)$trxdet['qty'];
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['discvar']            = $trxdet['discvar'];
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['globaldiscitem']     = (Int)$trxdet['globaldisc'] / (Int)$trxdet['qty'];
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['globaldisc']         = $trxdet['globaldisc'];
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['memberdiscitem']     = (Int)$trxdet['memberdisc'] / (Int)$trxdet['qty'];
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['memberdisc']         = $trxdet['memberdisc'];
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['type']               = $count++;
                            }
                        }
    
                        // Data Bundle
                        if (($trxdet['variantid'] == '0') && ($trxdet['bundleid'] != '0')) {
                            $bundles        = $BundleModel->find($trxdet['bundleid']);
    
                            if (!empty($bundles)) {
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['sku']                = '-';
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['name']               = $bundles['name'];
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['brand']              = 'Merek Terhapus';
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['category']           = 'Kategori Terhapus';
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['qty']                = $trxdet['qty'];
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['value']              = (float)$trxdet['value'] + ((Int)$trxdet['globaldisc'] / (Int)$trxdet['qty']) + ((Int)$trxdet['memberdisc'] / (Int)$trxdet['qty']);
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['total']              = ((float)$trxdet['value'] + ((Int)$trxdet['globaldisc'] / (Int)$trxdet['qty']) + ((Int)$trxdet['memberdisc'] / (Int)$trxdet['qty'])) * (Int)$trxdet['qty'];
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['discitem']           = (Int)$trxdet['discvar'] / (Int)$trxdet['qty'];
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['discvar']            = $trxdet['discvar'];
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['globaldiscitem']     = (Int)$trxdet['globaldisc'] / (Int)$trxdet['qty'];
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['globaldisc']         = $trxdet['globaldisc'];
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['memberdiscitem']     = (Int)$trxdet['memberdisc'] / (Int)$trxdet['qty'];
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['memberdisc']         = $trxdet['memberdisc'];
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['type']               = $count++;
                            } else {
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['sku']                = '-';
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['name']               = 'Bundle Terhapus';
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['brand']              = 'Merek Terhapus';
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['category']           = 'Kategori Terhapus';
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['qty']                = $trxdet['qty'];
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['value']              = (float)$trxdet['value'] + ((Int)$trxdet['globaldisc'] / (Int)$trxdet['qty']) + ((Int)$trxdet['memberdisc'] / (Int)$trxdet['qty']);
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['total']              = ((float)$trxdet['value'] + ((Int)$trxdet['globaldisc'] / (Int)$trxdet['qty']) + ((Int)$trxdet['memberdisc'] / (Int)$trxdet['qty'])) * (Int)$trxdet['qty'];
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['discitem']           = (Int)$trxdet['discvar'] / (Int)$trxdet['qty'];
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['discvar']            = $trxdet['discvar'];
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['globaldiscitem']     = (Int)$trxdet['globaldisc'] / (Int)$trxdet['qty'];
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['globaldisc']         = $trxdet['globaldisc'];
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['memberdiscitem']     = (Int)$trxdet['memberdisc'] / (Int)$trxdet['qty'];
                                $transactiondata[$trx['id']]['detail'][$trxdet['id']]['memberdisc']         = $trxdet['memberdisc'];
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
                    echo '<th class="thd">Merek</th>';
                    echo '<th class="thd">Kategori</th>';
                    echo '<th class="thd">Jumlah Produk</th>';
                    echo '<th class="thd">Harga Produk</th>';
                    echo '<th class="thd">Diskon Varian Dari Transaksi</th>';
                    echo '<th class="thd">Diskon Varian Dari Pengaturan Usaha</th>';
                    echo '<th class="thd">Diskon Member Per Item</th>';
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
                                    echo '<td class="middle">' . $detail['brand'] . '</td>';
                                    echo '<td class="middle">' . $detail['category'] . '</td>';
                                    echo '<td class="cntr">' . $detail['qty'] . '</td>';
                                    echo '<td class="middle">' . $detail['value'] . '</td>';
                                    echo '<td class="middle">' . $detail['discitem'] . '</td>';
                                    echo '<td class="middle">' . $detail['globaldiscitem'] . '</td>';
                                    echo '<td class="middle">' . $detail['memberdiscitem'] . '</td>';
                                    echo '<td class="middle">' . ($detail['value'] * $detail['qty']) - $detail['discvar'] - $detail['globaldisc'] - $detail['memberdisc'] . '</td>';
                                echo '</tr>';
                            } else {
                                echo '<td class="middle">' . $detail['sku'] . '</td>';
                                echo '<td class="middle">' . $detail['name'] . '</td>';
                                echo '<td class="middle">' . $detail['brand'] . '</td>';
                                echo '<td class="middle">' . $detail['category'] . '</td>';
                                echo '<td class="cntr">' . $detail['qty'] . '</td>';
                                echo '<td class="middle">' . $detail['value'] . '</td>';
                                echo '<td class="middle">' . $detail['discitem'] . '</td>';
                                echo '<td class="middle">' . $detail['globaldiscitem'] . '</td>';
                                echo '<td class="middle">' . $detail['memberdiscitem'] . '</td>';
                                echo '<td class="middle">' . ($detail['value'] * $detail['qty']) - $detail['discvar'] - $detail['globaldisc'] - $detail['memberdisc'] . '</td>';
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
        $discountmember     = array();
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

                // Transaction Detail Discount Member
                if ($trxdetail['memberdisc'] != '0') {
                    $discountmember[]       = $trxdetail['memberdisc'];
                }

                // Transaction Detail Margin Modal
                $marginmodals[] = ((int)$trxdetail['marginmodal'] * (int)$trxdetail['qty']);

                // Transaction Detail Margin Dasar
                $margindasars[] = ((int)$trxdetail['margindasar'] * (int)$trxdetail['qty']);
            }
        }

        // Getting Discount Data
        $transactiondisc    = (int)(array_sum($discounttrx)) + (int)(array_sum($memberdisc));
        $variantdisc        = array_sum($discountvariant);
        $globaldisc         = array_sum($discountglobal);
        $memberdiscitem     = array_sum($discountmember);

        // Total Point Used
        $poindisc           = array_sum($discountpoin);

        // Getting Margin  Data
        $marginmodalsum     = array_sum($marginmodals);
        $margindasarsum     = array_sum($margindasars);

        // Total Discount
        $alldisc            = (Int)$globaldisc + (Int)$memberdiscitem + (Int)$variantdisc + (Int)$transactiondisc;

        // Total Sales
        $salesresult        = array_sum(array_column($transaction, 'value'));

        // Gross Sales
        $grossales          = (Int)$salesresult + (Int)$variantdisc + (Int)$globaldisc + (Int)$memberdiscitem + (Int)$transactiondisc + (Int)$poindisc;

        // Profit Calculation
        $profitmodal        = (Int)$marginmodalsum - (Int)$transactiondisc - (Int)$poindisc;
        $profitdasar        = (Int)$margindasarsum - (Int)$transactiondisc - (Int)$poindisc;

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
                echo '<th style="text-align: left;">Diskon Transaksi</th>';
                echo '<td style="text-align: right;">' . $transactiondisc . '</td>';
            echo '</tr>';
            echo '<tr>';
                echo '<th style="text-align: left;">Diskon Variant</th>';
                echo '<td style="text-align: right;">' . $variantdisc . '</td>';
            echo '</tr>';
            echo '<tr>';
                echo '<th style="text-align: left;">Diskon Global</th>';
                echo '<td style="text-align: right;">' . $globaldisc . '</td>';
            echo '</tr>';
            echo '<tr>';
                echo '<th style="text-align: left;">Diskon Member Per Item</th>';
                echo '<td style="text-align: right;">' . $memberdiscitem . '</td>';
            echo '</tr>';
            echo '<tr>';
                echo '<th style="text-align: left;">Diskon Tukar Poin</th>';
                echo '<td style="text-align: right;">' . $poindisc . '</td>';
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
                echo '<th style="text-align: left;">Keuntungan Modal</th>';
                echo '<td style="text-align: right; font-family: arial, sans-serif; font-weight: bold;">' . $profitmodal . '</td>';
            echo '</tr>';
            echo '<tr>';
                echo '<th style="text-align: left;">Keuntungan Dasar</th>';
                echo '<td style="text-align: right; font-family: arial, sans-serif; font-weight: bold;">' . $profitdasar . '</td>';
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

                if ($trx['pointused'] != '0') {
                    $discount[]   = (int)$trx['pointused'];
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
        $PaymentModel           = new PaymentModel;
        $TrxpaymentModel        = new TrxpaymentModel;
        $TransactionModel       = new TransactionModel;
        $OutletModel            = new OutletModel;

        if ($this->data['outletPick'] != null) {
            $input          = $this->request->getGet();
            $daterange      = $input['daterange'] ?? date('Y-m-d') . ' - ' . date('Y-m-d');
            
            [$startdate, $enddate] = explode(' - ', $daterange);
            $startdate = date('Y-m-d', strtotime($startdate));
            $enddate   = date('Y-m-d', strtotime($enddate));

            $search = $input['search'] ?? '';

            // Transaction Data
            $transactiondata    = array();
            $outlets            = $OutletModel->find($this->data['outletPick']);
            $outletname         = $outlets['name'];
            $adress             = $outlets['address'];
            $payments           = $PaymentModel->orderBy('id', 'DESC')->where('outletid', $this->data['outletPick'])->findAll();
            $transactions       = $TransactionModel->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->where('outletid', $this->data['outletPick'])->findAll();
            $transactionIds     = array_column($transactions, 'id');

            foreach ($payments as $payment) {
                $transactiondata[$payment['id']] = [
                    'name'  => $payment['name'],
                    'qty'   => 0,
                    'value' => 0,
                ];
            }

            // Debt dan Redeem Point
            $transactiondata[0] = [
                'name'  => lang('Global.debt'),
                'qty'   => 0,
                'value' => 0,
            ];

            $transactiondata[-1] = [
                'name'  => lang('Global.redeemPoint'),
                'qty'   => 0,
                'value' => 0,
            ];

            $trxpayments = [];

            if (!empty($transactionIds)) {
                $trxpayments = $TrxpaymentModel
                ->select('paymentid, COUNT(*) as qty, COALESCE(SUM(value),0) as value')
                ->whereIn('transactionid', $transactionIds)
                ->groupBy('paymentid')
                ->findAll();
            }

            foreach ($trxpayments as $trxpayment) {
                $paymentId = (int)$trxpayment['paymentid'];

                if (!isset($transactiondata[$paymentId])) {
                    continue;
                }

                $transactiondata[$paymentId]['qty'] = (int)$trxpayment['qty'];
                $transactiondata[$paymentId]['value'] = (float)$trxpayment['value'];
            }

            if (!empty($search)) {
                $transactiondata = array_filter($transactiondata, function ($item) use ($search) {
                    return stripos($item['name'], $search) !== false;
                });
            }

            array_multisort(
                array_column($transactiondata, 'value'),
                SORT_DESC,
                $transactiondata
            );

            $totalvalue = array_sum(array_column($transactiondata, 'value'));
            $totalqty = array_sum(array_column($transactiondata, 'qty'));

            header("Content-type: application/vnd.ms-excel");
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
        $TransactionModel   = new TransactionModel;
        $UserModel          = new UserModel;
        $UserGroupModel     = new GroupUserModel;
        $GroupModel         = new GroupModel;
        $OutletModel        = new OutletModel;

        // Populating Data 
        $input          = $this->request->getGet();
        $daterange      = $input['daterange'] ?? date('Y-m-d') . ' - ' . date('Y-m-d');
        $search         = trim($input['search'] ?? '');
        $searchName     = $search !== '' ? $search : 'Semua Pegawai';
        
        [$startdate, $enddate] = explode(' - ', $daterange);
        $startdate = date('Y-m-d', strtotime($startdate));
        $enddate   = date('Y-m-d', strtotime($enddate));

        if (!empty($search)) {
            $admins = $UserModel
                ->like('username', $search)
                ->findAll();
        } else {
            $admins = $UserModel->findAll();
        }

        // User Group Map
        $userGroupMap = [];
        foreach ($UserGroupModel->findAll() as $usergroup) {
            $userGroupMap[$usergroup['user_id']] = $usergroup;
        }

        // Group Map
        $groupMap = [];
        foreach ($GroupModel->findAll() as $group) {
            $groupMap[$group->id] = $group;
        }

        $trxBuilder = $TransactionModel
            ->select('userid, SUM(value) as total')
            ->where('date >=', $startdate . ' 00:00:00')
            ->where('date <=', $enddate . ' 23:59:59');

        $addres     = "All Outlets";
        $outletname = "58vapehouse";

        if ($this->data['outletPick'] !== null) {
            $trxBuilder->where('outletid', $this->data['outletPick']);

            $outlet         = $OutletModel->find($this->data['outletPick']);
            $addres         = $outlet['address'];
            $outletname     = $outlet['name'];
        }

        $trxMap = [];

        foreach ($trxBuilder->groupBy('userid')->findAll() as $trx) {
            $trxMap[$trx['userid']] = (float) $trx['total'];
        }

        $employeedata           = [];
        foreach ($admins as $admin) {
            // Default Data
            $employeedata[$admin->id] = [
                'name'  => $admin->username,
                'role'  => '-',
                'value' => 0,
            ];
            
            $usergroups         = $userGroupMap[$admin->id] ?? null;
            if (!empty($usergroups)) {
                $group = $groupMap[$usergroups['group_id']] ?? null;
                if ($group) {
                    $employeedata[$admin->id]['role'] = $group->name;
                }
            }

            $employeedata[$admin->id]['value'] = $trxMap[$admin->id] ?? 0;
        }
        uasort($employeedata, function ($a, $b) {
            return $b['value'] <=> $a['value'];
        });

        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=Laporan Pegawai $searchName ($startdate-$enddate).xls");

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
        $OutletModel        = new OutletModel();

        $outlets    = $OutletModel->findAll();

        // Populating Data
        $input      = $this->request->getGet();
        $daterange  = $input['daterange'] ?? date('Y-m-d') . ' - ' . date('Y-m-d');
        $search     = trim($input['search'] ?? '');
        $searchName = $search !== '' ? $search : 'Semua Produk';
        
        [$startdate, $enddate] = explode(' - ', $daterange);
        $startdate = date('Y-m-d', strtotime($startdate));
        $enddate   = date('Y-m-d', strtotime($enddate));

        // Variant Map
        $variantMap = [];
        foreach ($VariantModel->findAll() as $variant) {
            $variantMap[$variant['id']] = $variant;
        }

        // Product Map
        $productMap = [];
        foreach ($ProductModel->findAll() as $product) {
            $productMap[$product['id']] = $product;
        }

        // Category Map
        $categoryMap = [];
        foreach ($CategoryModel->findAll() as $category) {
            $categoryMap[$category['id']] = $category;
        }

        $adress = [];
        if ($this->data['outletPick'] === null) {
            $transactions   = $TransactionModel->where('date >=', $startdate . " 00:00:00")->where('date <=', $enddate . " 23:59:59")->findAll();
            $outletname     = "All Outlets";
            $adress         = "58vapehouse";
        } else {
            $transactions   = $TransactionModel->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->where('outletid', $this->data['outletPick'])->findAll();
            $outlets        = $OutletModel->find($this->data['outletPick']);
            $outletname     = $outlets['name'];
            $adress         = $outlets['address'];
        }

        $transactionIds = array_column($transactions, 'id');

        // Get TrxDetails
        $trxdetails = [];
        if (!empty($transactionIds)) {
            $trxdetails = $TrxdetailModel
                ->whereIn('transactionid', $transactionIds)
                ->findAll();
        }

        // TrxDetail Map
        $trxDetailMap = [];
        foreach ($trxdetails as $detail) {
            $trxDetailMap[$detail['transactionid']][] = $detail;
        }
        
        $transactiondata    = [];
        
        foreach ($transactions as $trx) {
            $trxdetails     = $trxDetailMap[$trx['id']] ?? [];
            $totaltrxdet    = max(count($trxdetails), 1);
            $discval        = round(((int)$trx['discvalue']) / $totaltrxdet);
            $discmem        = round(((int)$trx['memberdisc']) / $totaltrxdet);
            $discpoin       = round(((int)$trx['pointused']) / $totaltrxdet);
            
            if (!empty($trxdetails)) {
                foreach ($trxdetails as $trxdet) {
                    $variants = $variantMap[$trxdet['variantid']] ?? null;
                    
                    if (!empty($variants)) {
                        $products = $productMap[$variants['productid']] ?? null;

                        if (!empty($products)) {
                            $category = $categoryMap[$products['catid']] ?? null;
                            
                            if ($search !== '') {
                                $productMatch = stripos($products['name'], $search) !== false;

                                $categoryMatch = false;
                                if (!empty($category)) {
                                    $categoryMatch = stripos($category['name'], $search) !== false;
                                }

                                if (!$productMatch && !$categoryMatch) {
                                    continue;
                                }
                            }

                            $transactiondata[$products['id']]['name']           = $products['name'];
                            
                            if (!empty($category)) {
                                $catstatus = ((int)$category['status'] === 1)
                                    ? 'Aktif'
                                    : 'Tidak Aktif';

                                $transactiondata[$products['id']]['category']
                                    = $category['name'] . ' (' . $catstatus . ')';
                            } else {
                                $transactiondata[$products['id']]['category']
                                    = 'Kategori Terhapus';
                            }

                            $transactiondata[$products['id']]['sku']
                                = $variants['sku'];
                            $transactiondata[$products['id']]['qty']
                                = ($transactiondata[$products['id']]['qty'] ?? 0)
                                + $trxdet['qty'];

                            $transactiondata[$products['id']]['netvalue']
                                = ($transactiondata[$products['id']]['netvalue'] ?? 0)
                                + (((float)$trxdet['value'] * (Int)$trxdet['qty'])) - ((Int)$discval + (Int)$discmem + (Int)$discpoin);;

                            $transactiondata[$products['id']]['grossvalue']
                                = ($transactiondata[$products['id']]['grossvalue'] ?? 0)
                                + ((float)$trxdet['value'] * (Int)$trxdet['qty']) + (Int)$trxdet['discvar'] + (Int)$trxdet['globaldisc'] + (Int)$trxdet['memberdisc'];
                        }
                    } else {
                        $transactiondata[0]['sku']          = 'Kategori / Produk / Variant Terhapus';
                        $transactiondata[0]['name']         = 'Kategori / Produk / Variant Terhapus';
                        $transactiondata[0]['category']     = 'Kategori / Produk / Variant Terhapus';
                        $transactiondata[0]['qty']
                            = ($transactiondata[0]['qty'] ?? 0)
                            + $trxdet['qty'];

                        $transactiondata[0]['netvalue']
                            = ($transactiondata[0]['netvalue'] ?? 0)
                            + (((float)$trxdet['value'] * (Int)$trxdet['qty'])) - ((Int)$discval + (Int)$discmem + (Int)$discpoin);

                        $transactiondata[0]['grossvalue']
                            = ($transactiondata[0]['grossvalue'] ?? 0)
                            + ((float)$trxdet['value'] * (Int)$trxdet['qty']) + (Int)$trxdet['discvar'] + (Int)$trxdet['globaldisc'] + (Int)$trxdet['memberdisc'];
                    }
                }
            }
        }
        
        $totalsalesitem = 0;
        $totalnetsales  = 0;
        $totalcatgross  = 0;
        
        foreach ($transactiondata as $trxdata) {
            $totalsalesitem += $trxdata['qty'];
            $totalnetsales  += $trxdata['netvalue'];
            $totalcatgross  += $trxdata['grossvalue'];
        }
        
        $sortValues = array_column(
            $transactiondata,
            'netvalue'
        );

        array_multisort(
            $sortValues,
            SORT_DESC,
            $transactiondata
        );

        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=Laporan Penjualan $searchName $outletname ($startdate-$enddate).xls");

        // export
        echo '<table>';
            echo '<thead>';
                echo '<tr>';
                    echo '<th colspan="5" style="align-text:center;">Ringkasan Produk</th>';
                echo '</tr>';
                echo '<tr>';
                    echo '<th colspan="5" style="align-text:center;">' . $outletname . '</th>';
                echo '</tr>';
                echo '<tr>';
                    echo '<th colspan="5" style="align-text:center;">' . $adress . '</th>';
                echo '</tr>';
                echo '<tr>';
                    echo '<th colspan="5" style="align-text:center;">' . $startdate . ' - ' . $enddate . '</th>';
                echo '</tr>';
                echo '<tr>';
                    echo '<th colspan="5" style="align-text:center;"></th>';
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
                        echo '<td>' . $value['sku'] . '</td>';
                        echo '<td>' . $value['name'] . '</td>';
                        echo '<td>' . $value['category'] . '</td>';
                        echo '<td>' . $value['qty'] . '</td>';
                        echo '<td>' . $value['netvalue'] . '</td>';
                    echo '</tr>';
                }
            echo '</tbody>';
            echo '<tfoot>';
                echo '<tr>';
                    echo '<td colspan="3" style="text-align:center;font-weight:700;">Total</th>';
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

        // Populating Data
        $input      = $this->request->getGet();
        $daterange  = $input['daterange'] ?? date('Y-m-d') . ' - ' . date('Y-m-d');
        $search     = trim($input['search'] ?? '');
        $searchName = $search !== '' ? $search : 'Semua Kategori';
        
        [$startdate, $enddate] = explode(' - ', $daterange);
        $startdate = date('Y-m-d', strtotime($startdate));
        $enddate   = date('Y-m-d', strtotime($enddate));

        // Variant Map
        $variantMap = [];
        foreach ($VariantModel->findAll() as $variant) {
            $variantMap[$variant['id']] = $variant;
        }

        // Product Map
        $productMap = [];
        foreach ($ProductModel->findAll() as $product) {
            $productMap[$product['id']] = $product;
        }

        // Category Map
        $categoryMap = [];
        foreach ($CategoryModel->findAll() as $category) {
            $categoryMap[$category['id']] = $category;
        }

        if ($this->data['outletPick'] === null) {
            $transactions = $TransactionModel->where('date >=', $startdate . " 00:00:00")->where('date <=', $enddate . " 23:59:59")->findAll();
            $addres = "All Outlets";
            $outletname = "58vapehouse";
        } else {
            $transactions = $TransactionModel->where('outletid', $this->data['outletPick'])->where('date >=', $startdate . " 00:00:00")->where('date <=', $enddate . " 23:59:59")->findAll();
            $outlets = $OutletModel->find($this->data['outletPick']);
            $addres = $outlets['address'];
            $outletname = $outlets['name'];
        }

        $transactionIds = array_column($transactions, 'id');

        // Get TrxDetails
        $trxdetails = [];
        if (!empty($transactionIds)) {
            $trxdetails = $TrxdetailModel
                ->whereIn('transactionid', $transactionIds)
                ->findAll();
        }

        // TrxDetail Map
        $trxDetailMap = [];
        foreach ($trxdetails as $detail) {
            $trxDetailMap[$detail['transactionid']][] = $detail;
        }
        
        // Bundle Map
        $bundleMap = [];
        foreach ($BundleModel->findAll() as $bundle) {
            $bundleMap[$bundle['id']] = $bundle;
        }

        // Bundle Detail Map
        $bundleDetailMap = [];
        foreach ($BundledetailModel->findAll() as $detail) {
            $bundleDetailMap[$detail['bundleid']][] = $detail;
        }

        $transactiondata    = [];
        
        foreach ($transactions as $trx) {
            $trxdetails     = $trxDetailMap[$trx['id']] ?? [];
            $totaltrxdet    = max(count($trxdetails), 1);
            $discval        = round(((int)$trx['discvalue']) / $totaltrxdet);
            $discmem        = round(((int)$trx['memberdisc']) / $totaltrxdet);
            $discpoin       = round(((int)$trx['pointused']) / $totaltrxdet);
            
            if (!empty($trxdetails)) {
                foreach ($trxdetails as $trxdet) {
                    if (($trxdet['variantid'] != '0') && ($trxdet['bundleid'] == '0')) {
                        // Data Variant
                        $variants = $variantMap[$trxdet['variantid']] ?? null;
                        
                        if (!empty($variants)) {
                            $products = $productMap[$variants['productid']] ?? null;
    
                            if (!empty($products)) {
                                // Search Filter
                                $category = $categoryMap[$products['catid']] ?? null;

                                if (!empty($category) && $search !== '') {
                                    if (stripos($category['name'], $search) === false) {
                                        continue;
                                    }
                                }
                            
                                if (!empty($category)) {
                                    $catstatus = ((int)$category['status'] === 1)
                                        ? 'Aktif'
                                        : 'Tidak Aktif';
                                    $key = $category['id'];
                                    $name = $category['name'] . ' (' . $catstatus . ')';
                                } else {
                                    $key = 0;
                                    $name = 'Kategori / Produk / Variant Terhapus';
                                }

                                $transactiondata[$key]['name'] = $name;
                                $transactiondata[$key]['qty']
                                    = ($transactiondata[$key]['qty'] ?? 0)
                                    + $trxdet['qty'];

                                $transactiondata[$key]['netvalue']
                                    = ($transactiondata[$key]['netvalue'] ?? 0)
                                    + (((float)$trxdet['value'] * (Int)$trxdet['qty'])) - ((Int)$discval + (Int)$discmem + (Int)$discpoin);

                                $transactiondata[$key]['grossvalue']
                                    = ($transactiondata[$key]['grossvalue'] ?? 0)
                                    + ((float)$trxdet['value'] * (Int)$trxdet['qty']) + (Int)$trxdet['discvar'] + (Int)$trxdet['globaldisc'] + (Int)$trxdet['memberdisc'];
                            }
                        } else {
                            $transactiondata[0]['name']                             = 'Kategori / Produk / Variant Terhapus';
                            $transactiondata[0]['qty']
                                = ($transactiondata[0]['qty'] ?? 0)
                                + $trxdet['qty'];

                            $transactiondata[0]['netvalue']
                                = ($transactiondata[0]['netvalue'] ?? 0)
                                + (((float)$trxdet['value'] * (Int)$trxdet['qty'])) - ((Int)$discval + (Int)$discmem + (Int)$discpoin);

                            $transactiondata[0]['grossvalue']
                                = ($transactiondata[0]['grossvalue'] ?? 0)
                                + ((float)$trxdet['value'] * (Int)$trxdet['qty']) + (Int)$trxdet['discvar'] + (Int)$trxdet['globaldisc'] + (Int)$trxdet['memberdisc'];
                        }
                    }

                    if (($trxdet['variantid'] == '0') && ($trxdet['bundleid'] != '0')) {
                        // Data Bundle
                        $bundle = $bundleMap[$trxdet['bundleid']] ?? null;

                        if (!empty($bundle)) {
                            $bundleDetails = $bundleDetailMap[$bundle['id']] ?? [];

                            if (!empty($bundleDetails)) {
                                foreach ($bundleDetails as $bundleDetail) {
                                    $variant = $variantMap[$bundleDetail['variantid']] ?? null;
                                    
                                    if (!empty($variant)) {
                                        $product = $productMap[$variant['productid']] ?? null;

                                        if (!empty($product)) {
                                            // Search Filter
                                            $category = $categoryMap[$product['catid']] ?? null;

                                            if (!empty($category) && $search !== '') {
                                                if (stripos($category['name'], $search) === false) {
                                                    continue;
                                                }
                                            }

                                            if (!empty($category)) {
                                                $catstatus = ((int)$category['status'] === 1)
                                                    ? 'Aktif'
                                                    : 'Tidak Aktif';
                                                $key = $category['id'];
                                                $name = $category['name'] . ' (' . $catstatus . ')';
                                            } else {
                                                $key = 0;
                                                $name = 'Kategori / Produk / Variant Terhapus';
                                            }

                                            $transactiondata[$key]['name'] = $name;
                                            $transactiondata[$key]['qty']
                                                = ($transactiondata[$key]['qty'] ?? 0)
                                                + $trxdet['qty'];

                                            $transactiondata[$key]['netvalue']
                                                = ($transactiondata[$key]['netvalue'] ?? 0)
                                                + (((float)$trxdet['value'] * (Int)$trxdet['qty'])) - ((Int)$discval + (Int)$discmem + (Int)$discpoin);

                                            $transactiondata[$key]['grossvalue']
                                                = ($transactiondata[$key]['grossvalue'] ?? 0)
                                                + ((float)$trxdet['value'] * (Int)$trxdet['qty']) + (Int)$trxdet['discvar'] + (Int)$trxdet['globaldisc'] + (Int)$trxdet['memberdisc'];
                                        } else {
                                            $transactiondata[0]['name'] = 'Kategori / Produk / Variant Terhapus';

                                            $transactiondata[0]['qty']
                                                = ($transactiondata[0]['qty'] ?? 0)
                                                + $trxdet['qty'];

                                            $transactiondata[0]['netvalue']
                                                = ($transactiondata[0]['netvalue'] ?? 0)
                                                + (((float)$trxdet['value'] * (int)$trxdet['qty']))
                                                - ((int)$discval + (int)$discmem + (int)$discpoin);

                                            $transactiondata[0]['grossvalue']
                                                = ($transactiondata[0]['grossvalue'] ?? 0)
                                                + ((float)$trxdet['value'] * (int)$trxdet['qty'])
                                                + (int)$trxdet['discvar']
                                                + (int)$trxdet['globaldisc']
                                                + (int)$trxdet['memberdisc'];
                                        }
                                    } else {
                                        $transactiondata[0]['name']                             = 'Kategori / Produk / Variant Terhapus';
                                        $transactiondata[0]['qty']
                                            = ($transactiondata[0]['qty'] ?? 0)
                                            + $trxdet['qty'];

                                        $transactiondata[0]['netvalue']
                                            = ($transactiondata[0]['netvalue'] ?? 0)
                                            + (((float)$trxdet['value'] * (Int)$trxdet['qty'])) - ((Int)$discval + (Int)$discmem + (Int)$discpoin);

                                        $transactiondata[0]['grossvalue']
                                            = ($transactiondata[0]['grossvalue'] ?? 0)
                                            + ((float)$trxdet['value'] * (Int)$trxdet['qty']) + (Int)$trxdet['discvar'] + (Int)$trxdet['globaldisc'] + (Int)$trxdet['memberdisc'];
                                    }
                                }
                            }
                        } else {
                            $category       = [];
                        }
                    }
                }
            }
        }
        
        $totalsalesitem = 0;
        $totalnetsales  = 0;
        $totalcatgross  = 0;
        
        foreach ($transactiondata as $trxdata) {
            $totalsalesitem += $trxdata['qty'];
            $totalnetsales  += $trxdata['netvalue'];
            $totalcatgross  += $trxdata['grossvalue'];
        }
        
        $sortValues = array_column(
            $transactiondata,
            'netvalue'
        );

        array_multisort(
            $sortValues,
            SORT_DESC,
            $transactiondata
        );

        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=Laporan Penjualan $searchName $outletname ($startdate-$enddate).xls");

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
        echo '<th>Penjualan Bersih</th>';
        echo '<th>Penjualan Kotor</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
            foreach ($transactiondata as $key => $prod) {
                echo '<tr>';
                    echo '<td>' . $prod['name'] . '</td>';
                    echo '<td>' . $prod['qty'] . '</td>';
                    echo '<td>' . $prod['netvalue'] . '</td>';
                    echo '<td>' . $prod['grossvalue'] . '</td>';
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

    public function brand()
    {
        // Calling models
        $OutletModel        = new OutletModel();
        $TransactionModel   = new TransactionModel();
        $TrxdetailModel     = new TrxdetailModel();
        $VariantModel       = new VariantModel();
        $ProductModel       = new ProductModel();
        $BundleModel        = new BundleModel();
        $BundledetailModel  = new BundledetailModel();
        $BrandModel         = new BrandModel();

        // Populating Data
        $input          = $this->request->getGet();
        $daterange      = $input['daterange'] ?? date('Y-m-d') . ' - ' . date('Y-m-d');
        $search         = trim($input['search'] ?? '');
        $searchName     = $search !== '' ? $search : 'Semua Merek';
        
        [$startdate, $enddate] = explode(' - ', $daterange);
        $startdate = date('Y-m-d', strtotime($startdate));
        $enddate   = date('Y-m-d', strtotime($enddate));

        // Variant Map
        $variantMap = [];
        foreach ($VariantModel->findAll() as $variant) {
            $variantMap[$variant['id']] = $variant;
        }

        // Product Map
        $productMap = [];
        foreach ($ProductModel->findAll() as $product) {
            $productMap[$product['id']] = $product;
        }

        // Brand Map
        $brandMap = [];
        foreach ($BrandModel->findAll() as $brand) {
            $brandMap[$brand['id']] = $brand;
        }

        if ($this->data['outletPick'] === null) {
            $transactions = $TransactionModel->where('date >=', $startdate . " 00:00:00")->where('date <=', $enddate . " 23:59:59")->findAll();
            $addres = "All Outlets";
            $outletname = "58vapehouse";
        } else {
            $transactions = $TransactionModel->where('outletid', $this->data['outletPick'])->where('date >=', $startdate . " 00:00:00")->where('date <=', $enddate . " 23:59:59")->findAll();
            $outlets = $OutletModel->find($this->data['outletPick']);
            $addres = $outlets['address'];
            $outletname = $outlets['name'];
        }

        $transactionIds = array_column($transactions, 'id');

        // Get TrxDetails
        $trxdetails = [];
        if (!empty($transactionIds)) {
            $trxdetails = $TrxdetailModel
                ->whereIn('transactionid', $transactionIds)
                ->findAll();
        }

        // TrxDetail Map
        $trxDetailMap = [];
        foreach ($trxdetails as $detail) {
            $trxDetailMap[$detail['transactionid']][] = $detail;
        }
        
        // Bundle Map
        $bundleMap = [];
        foreach ($BundleModel->findAll() as $bundle) {
            $bundleMap[$bundle['id']] = $bundle;
        }

        // Bundle Detail Map
        $bundleDetailMap = [];
        foreach ($BundledetailModel->findAll() as $detail) {
            $bundleDetailMap[$detail['bundleid']][] = $detail;
        }

        $transactiondata    = [];
        
        foreach ($transactions as $trx) {
            $trxdetails     = $trxDetailMap[$trx['id']] ?? [];
            $totaltrxdet    = max(count($trxdetails), 1);
            $discval        = round(((int)$trx['discvalue']) / $totaltrxdet);
            $discmem        = round(((int)$trx['memberdisc']) / $totaltrxdet);
            $discpoin       = round(((int)$trx['pointused']) / $totaltrxdet);
            
            if (!empty($trxdetails)) {
                foreach ($trxdetails as $trxdet) {
                    if (($trxdet['variantid'] != '0') && ($trxdet['bundleid'] == '0')) {
                        // Data Variant
                        $variants       = $variantMap[$trxdet['variantid']] ?? null;
                        
                        if (!empty($variants)) {
                            $products   = $productMap[$variants['productid']] ?? null;
    
                            if (!empty($products)) {
                                // Search Filter
                                $brand = $brandMap[$products['brandid']] ?? null;

                                if (!empty($brand) && $search !== '') {
                                    if (stripos($brand['name'], $search) === false) {
                                        continue;
                                    }
                                }
                            
                                if (!empty($brand)) {
                                    $catstatus = ((int)$brand['status'] === 1)
                                        ? 'Aktif'
                                        : 'Tidak Aktif';
                                    $key = $brand['id'];
                                    $name = $brand['name'] . ' (' . $catstatus . ')';
                                } else {
                                    $key = 0;
                                    $name = 'Kategori / Produk / Variant Terhapus';
                                }

                                $transactiondata[$key]['name'] = $name;
                                $transactiondata[$key]['qty']
                                    = ($transactiondata[$key]['qty'] ?? 0)
                                    + $trxdet['qty'];

                                $transactiondata[$key]['netvalue']
                                    = ($transactiondata[$key]['netvalue'] ?? 0)
                                    + (((float)$trxdet['value'] * (Int)$trxdet['qty'])) - ((Int)$discval + (Int)$discmem + (Int)$discpoin);

                                $transactiondata[$key]['grossvalue']
                                    = ($transactiondata[$key]['grossvalue'] ?? 0)
                                    + ((float)$trxdet['value'] * (Int)$trxdet['qty']) + (Int)$trxdet['discvar'] + (Int)$trxdet['globaldisc'] + (Int)$trxdet['memberdisc'];
                            }
                        } else {
                            $transactiondata[0]['name']                             = 'Kategori / Produk / Variant Terhapus';
                            $transactiondata[0]['qty']
                                = ($transactiondata[0]['qty'] ?? 0)
                                + $trxdet['qty'];

                            $transactiondata[0]['netvalue']
                                = ($transactiondata[0]['netvalue'] ?? 0)
                                + (((float)$trxdet['value'] * (Int)$trxdet['qty'])) - ((Int)$discval + (Int)$discmem + (Int)$discpoin);

                            $transactiondata[0]['grossvalue']
                                = ($transactiondata[0]['grossvalue'] ?? 0)
                                + ((float)$trxdet['value'] * (Int)$trxdet['qty']) + (Int)$trxdet['discvar'] + (Int)$trxdet['globaldisc'] + (Int)$trxdet['memberdisc'];
                        }
                    }

                    if (($trxdet['variantid'] == '0') && ($trxdet['bundleid'] != '0')) {
                        // Data Bundle
                        $bundles        = $bundleMap[$trxdet['bundleid']] ?? null;

                        if (!empty($bundles)) {
                            // Data Bundle Detail
                            $bundledets     = $bundleDetailMap[$bundle['id']] ?? [];
    
                            if (!empty($bundledets)) {
                                foreach ($bundledets as $bundet) {
                                    // Data Variant
                                    $variant = $variantMap[$bundet['variantid']] ?? null;
                                    
                                    if (!empty($variant)) {
                                        $product   = $productMap[$variant['productid']] ?? null;
                
                                        if (!empty($product)) {
                                            // Search Filter
                                            $brand = $brandMap[$product['brandid']] ?? null;

                                            if (!empty($brand) && $search !== '') {
                                                if (stripos($brand['name'], $search) === false) {
                                                    continue;
                                                }
                                            }

                                            if (!empty($brand)) {
                                                $brandstatus = ((int)$brand['status'] === 1)
                                                    ? 'Aktif'
                                                    : 'Tidak Aktif';
                                                $key = $brand['id'];
                                                $name = $brand['name'] . ' (' . $brandstatus . ')';
                                            } else {
                                                $key = 0;
                                                $name = 'Merek / Kategori / Produk / Variant Terhapus';
                                            }

                                            $transactiondata[$key]['name'] = $name;
                                            $transactiondata[$key]['qty']
                                                = ($transactiondata[$key]['qty'] ?? 0)
                                                + $trxdet['qty'];

                                            $transactiondata[$key]['netvalue']
                                                = ($transactiondata[$key]['netvalue'] ?? 0)
                                                + (((float)$trxdet['value'] * (Int)$trxdet['qty'])) - ((Int)$discval + (Int)$discmem + (Int)$discpoin);

                                            $transactiondata[$key]['grossvalue']
                                                = ($transactiondata[$key]['grossvalue'] ?? 0)
                                                + ((float)$trxdet['value'] * (Int)$trxdet['qty']) + (Int)$trxdet['discvar'] + (Int)$trxdet['globaldisc'] + (Int)$trxdet['memberdisc'];
                                        }
                                    } else {
                                        $transactiondata[0]['name'] = 'Merek / Kategori / Produk / Variant Terhapus';

                                        $transactiondata[0]['qty']
                                            = ($transactiondata[0]['qty'] ?? 0)
                                            + $trxdet['qty'];

                                        $transactiondata[0]['netvalue']
                                            = ($transactiondata[0]['netvalue'] ?? 0)
                                            + (((float)$trxdet['value'] * (int)$trxdet['qty']))
                                            - ((int)$discval + (int)$discmem + (int)$discpoin);

                                        $transactiondata[0]['grossvalue']
                                            = ($transactiondata[0]['grossvalue'] ?? 0)
                                            + ((float)$trxdet['value'] * (int)$trxdet['qty'])
                                            + (int)$trxdet['discvar']
                                            + (int)$trxdet['globaldisc']
                                            + (int)$trxdet['memberdisc'];
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        
        $totalsalesitem = 0;
        $totalnetsales  = 0;
        $totalcatgross  = 0;

        foreach ($transactiondata as $trxdata) {
            $totalsalesitem += $trxdata['qty'];
            $totalnetsales  += $trxdata['netvalue'];
            $totalcatgross  += $trxdata['grossvalue'];
        }
        
        $sortValues = array_column(
            $transactiondata,
            'netvalue'
        );

        array_multisort(
            $sortValues,
            SORT_DESC,
            $transactiondata
        );

        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=Laporan Penjualan $searchName $outletname ($startdate-$enddate).xls");

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
                    echo '<td>' . $prod['qty'] . '</td>';
                    echo '<td>' . $prod['netvalue'] . '</td>';
                    echo '<td>' . $prod['grossvalue'] . '</td>';
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
                        if ($brands['status'] == '1') {
                            $brandstatus = 'Aktif';
                        } else {
                            $brandstatus = 'Tidak Aktif';
                        }
                        
                        $brandname  = $brands['name'] . ' (' . $brandstatus . ')';
                        foreach ($category as $cat) {
                            if ($category['status'] == '1') {
                                $catstatus = 'Aktif';
                            } else {
                                $catstatus = 'Tidak Aktif';
                            }
                            $catname  = $category['name'].' ('.$catstatus.')';
                            if ($product['catid'] === $cat['id'] && $product['brandid'] === $brand['id'] && $variant['productid'] == $product['id'] && $stock['variantid'] === $variant['id']) {
                                $productval[] = [
                                    'id'                => $product['catid'],
                                    'prodname'          => $product['name'],
                                    'catname'           => $catname,
                                    'brandname'         => $brandname,
                                    'desc'              => $product['description'],
                                    'varname'           => $variant['name'],
                                    'hargamodal'        => $variant['hargamodal'],
                                    'hargajual'         => $variant['hargajual'],
                                    'hargarekomendasi'  => $variant['hargarekomendasi'],
                                    'stock'             => $stock['qty'],
                                    'whole'             => (Int)$variant['hargamodal'] * (Int)$stock['qty'],
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
        $TransactionModel   = new TransactionModel();
        $TrxdetailModel     = new TrxdetailModel();
        $VariantModel       = new VariantModel();
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
            
        foreach ($transactions as $trx) {
            $trxdetails     = $TrxdetailModel->where('transactionid', $trx['id'])->where('variantid', '0')->find();
            
            if (!empty($trxdetails)) {
                foreach ($trxdetails as $trxdet) {
                    // Data Bundle
                    $bundles        = $BundleModel->find($trxdet['bundleid']);
                    if (!empty($bundles)) {
                        $transactiondata[$bundles['id']]['name']                = $bundles['name'];
                        $transactiondata[$bundles['id']]['qty'][]               = $trxdet['qty'];
                        $transactiondata[$bundles['id']]['value'][]             = (((float)$trxdet['value'] * (Int)$trxdet['qty']));
                    } else {
                        $transactiondata[0]['name']                             = 'Bundle Terhapus';
                        $transactiondata[0]['qty'][]                            = $trxdet['qty'];
                        $transactiondata[0]['value'][]                          = (((float)$trxdet['value'] * (Int)$trxdet['qty']));
                    }
                }
            } else {
                $bundles            = [];
            }
        }
        
        foreach ($transactiondata as $trxdata) {
            $productsales[] = array_sum($trxdata['qty']);
            $netval[]       = array_sum($trxdata['value']);
        }
        
        $totalsalesitem     = array_sum($productsales);
        $totalnetsales      = array_sum($netval);
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
            echo '</tr>';
        echo '</tfoot>';
        echo '</table>';
    }

    public function diskon()
    {
        // Calling Models
        $TransactionModel       = new TransactionModel;
        $TrxdetailModel         = new TrxdetailModel;
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
        $discountmember     = array();

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

                // Discount Member
                if ($trxdetail['memberdisc'] != '0') {
                    $discountmember[]     = $trxdetail['memberdisc'];
                }
            }
        }

        $transactiondisc    = array_sum($discount);
        $variantdisc        = array_sum($discountvariant);
        $globaldisc         = array_sum($discountglobal);
        $memberdiscitem     = array_sum($discountmember);
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
                echo '<th>Diskon Member</th>';
                echo '<th>Diskon Poin</th>';
            echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
            echo '<tr>';
                echo '<td >' . $transactiondisc . '</td>';
                echo '<td >' . $variantdisc . '</td>';
                echo '<td >' . $globaldisc . '</td>';
                echo '<td >' . $memberdiscitem . '</td>';
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
        
        if ($this->data['outletPick'] === null) {
            $presences  = $PresenceModel->where('datetime >=', $startdate . ' 00:00:00')->where('datetime <=', $enddate . ' 23:59:59')->find();
            $addres     = "All Outlets";
            $outletname = "58vapehouse";
        } else {
            $presences  = $PresenceModel->where('outletid', $this->data['outletPick'])->where('datetime >=', $startdate . ' 00:00:00')->where('datetime <=', $enddate . ' 23:59:59')->find();
            $outlets    = $OutletModel->find($this->data['outletPick']);
            $addres     = $outlets['address'];
            $outletname = $outlets['name'];
        }
        
        foreach ($presences as $presence) {
            // Get User Data
            $users          = $UserModel->find($presence['userid']);
            $usergroups     = $UserGroupModel->where('user_id', $users->id)->first();
            $groups         = $GroupModel->find($usergroups['group_id']);
            $outlet         = $OutletModel->find($presence['outletid']);

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
            $presencedata[$date.$users->id.$shift]['outlet']   = $outlet['name'];

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
                    echo '<th>Outlet</th>';
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
                        echo '<td>' . $presence['outlet'] . '</td>';
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
        $TransactionModel   = new TransactionModel;
        $MemberModel        = new MemberModel;
        $DebtModel          = new DebtModel;
        $OutletModel        = new OutletModel;
        $TrxdetailModel     = new TrxdetailModel;
        $ProductModel       = new ProductModel;
        $VariantModel       = new VariantModel;

        // Populating Data
        $input      = $this->request->getGet();
        $search     = trim($input['search'] ?? '');
        $searchName = $search !== '' ? $search : 'Semua Pelanggan';

        $daterange  = $input['daterange'] ?? date('2023-01-01') . ' - ' . date('Y-m-d');
        [$startdate, $enddate] = explode(' - ', $daterange);
        $startdate  = date('Y-m-d', strtotime($startdate));
        $enddate    = date('Y-m-d', strtotime($enddate));

        // Member with Pagination
        $memberQuery = $MemberModel->orderBy('name', 'ASC');
        if ($search !== '') {
            $memberQuery->like('name', $search);
        }
        $members    = $memberQuery->paginate(20, 'member');
        $memberIds  = array_column($members, 'id');
        if (empty($memberIds)) {
            $memberIds = [0];
        }

        // Debt Map
        $debts = $DebtModel
            ->whereIn('memberid', $memberIds)
            ->findAll();
        $debtMap = [];
        foreach ($debts as $debt) {
            $debtMap[$debt['memberid']][] = $debt;
        }

        $addres     = "All Outlets";
        $outletname = "58vapehouse";
        
        // Transaction Query
        $trxQuery = $TransactionModel
            ->whereIn('memberid', $memberIds)
            ->where('date >=', $startdate . ' 00:00:00')
            ->where('date <=', $enddate . ' 23:59:59');
        if ($this->data['outletPick'] !== null) {
            $trxQuery->where('outletid', $this->data['outletPick']);

            $outlet         = $OutletModel->find($this->data['outletPick']);
            $addres         = $outlet['address'];
            $outletname     = $outlet['name'];
        }
        $transactions = $trxQuery->findAll();
        
        // Transaction Map
        $transactionMap = [];
        foreach ($transactions as $trx) {
            $transactionMap[$trx['memberid']][] = $trx;
        }
        $transactionIds = array_column($transactions, 'id');
        
        // Trx Detail Map
        $trxDetailMap = [];
        if (!empty($transactionIds)) {
            $trxdetails = $TrxdetailModel
                ->whereIn('transactionid', $transactionIds)
                ->findAll();
            foreach ($trxdetails as $detail) {
                $trxDetailMap[$detail['transactionid']][] = $detail;
            }
        }
        
        // Variant Map
        $variantMap = [];
        foreach ($VariantModel->findAll() as $variant) {
            $variantMap[$variant['id']] = $variant;
        }
        
        // Product Map
        $productMap = [];
        foreach ($ProductModel->findAll() as $product) {
            $productMap[$product['id']] = $product;
        }
        
        // Build Customer Data
        $customerdata = [];
        foreach ($members as $member) {
            $memberId = $member['id'];
            $customerdata[$memberId] = [
                'id'       => $memberId,
                'name'     => $member['name'],
                'phone'    => $member['phone'],
                'debt'     => 0,
                'trx'      => 0,
                'trxvalue' => 0,
                'product'  => []
            ];

            // Total Debt
            foreach ($debtMap[$memberId] ?? [] as $debt) {
                $customerdata[$memberId]['debt'] += $debt['value'];
            }

            // Transaction
            foreach ($transactionMap[$memberId] ?? [] as $trx) {
                $customerdata[$memberId]['trx']++;
                $customerdata[$memberId]['trxvalue'] += $trx['value'];

                // Detail Produk
                foreach ($trxDetailMap[$trx['id']] ?? [] as $detail) {
                    $variant = $variantMap[$detail['variantid']] ?? null;

                    if ($variant !== null) {
                        $product = $productMap[$variant['productid']] ?? null;

                        if ($product !== null) {
                            $productId = $product['id'];
                            if (!isset($customerdata[$memberId]['product'][$productId])) {
                                $customerdata[$memberId]['product'][$productId] = [
                                    'name' => $product['name'],
                                    'qty'  => 0
                                ];
                            }

                            $customerdata[$memberId]['product'][$productId]['qty']
                                += $detail['qty'];
                        } else {
                            // Product terhapus
                            if (!isset($customerdata[$memberId]['product'][0])) {
                                $customerdata[$memberId]['product'][0] = [
                                    'name' => 'Kategori / Produk Terhapus',
                                    'qty'  => 0
                                ];
                            }

                            $customerdata[$memberId]['product'][0]['qty']
                                += $detail['qty'];
                        }
                    } else {
                        // Variant terhapus
                        if (!isset($customerdata[$memberId]['product'][0])) {
                            $customerdata[$memberId]['product'][0] = [
                                'name' => 'Kategori / Produk / Variant Terhapus',
                                'qty'  => 0
                            ];
                        }

                        $customerdata[$memberId]['product'][0]['qty']
                            += $detail['qty'];
                    }
                }
            }
        }
        uasort($customerdata, function ($a, $b) {
            return $a['name'] <=> $b['name'];
        });

        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=Laporan Pelanggan $searchName $outletname ($startdate-$enddate).xls");

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
            $outletname = "58vapehouse";
        } else {
            $sopdetails = $SopDetailModel->orderby('updated_at', 'ASC')->where('outletid', $this->data['outletPick'])->where('updated_at >=', $startdate . ' 00:00:00')->where('updated_at <=', $enddate . ' 23:59:59')->find();
            $outlets    = $OutletModel->find($this->data['outletPick']);
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
        $DebtInsModel       = new DebtInsModel();
        $CheckpointModel    = new CheckpointModel();

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
        $count              = '0';
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

                // Checkpoint
                $checkpoints  = $CheckpointModel->where('date >=', $dayrep['dateopen'])->where('date <=', $dayrep['dateclose'])->where('outletid', $this->data['outletPick'])->find();

                if (!empty($checkpoints)) {
                    foreach ($checkpoints as $checkpoint) {
                        // User Cashier
                        $checkpointcashier   = $UserModel->find($checkpoint['userid']);

                        // Checkpoint Data
                        $dailyreportdata[$dayrep['id']]['checkpoint'][$checkpoint['id']]['cashier'] = $checkpointcashier->firstname.' '.$checkpointcashier->lastname;
                        $dailyreportdata[$dayrep['id']]['checkpoint'][$checkpoint['id']]['date']    = $checkpoint['date'];
                        $dailyreportdata[$dayrep['id']]['checkpoint'][$checkpoint['id']]['cash']    = $checkpoint['cash'];
                        $dailyreportdata[$dayrep['id']]['checkpoint'][$checkpoint['id']]['noncash'] = $checkpoint['noncash'];
                        $dailyreportdata[$dayrep['id']]['checkpoint'][$checkpoint['id']]['diff']    = $checkpoint['diff'];
                        $dailyreportdata[$dayrep['id']]['checkpoint'][$checkpoint['id']]['type']    = $count++;
                    }
                } else {
                    $checkpointcashier   = [];
                    $dailyreportdata[$dayrep['id']]['checkpoint'] = [];
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

                // Checkpoint
                $datenow        = date('Y-m-d H:i:s');
                $checkpoints    = $CheckpointModel->where('date >=', $dayrep['dateopen'])->where('date <=', $datenow)->where('outletid', $this->data['outletPick'])->find();

                if (!empty($checkpoints)) {
                    foreach ($checkpoints as $checkpoint) {
                        // User Cashier
                        $checkpointcashier   = $UserModel->find($checkpoint['userid']);

                        // Checkpoint Data
                        $dailyreportdata[$dayrep['id']]['checkpoint'][$checkpoint['id']]['id']      = $checkpoint['id'];
                        $dailyreportdata[$dayrep['id']]['checkpoint'][$checkpoint['id']]['cashier'] = $checkpointcashier->firstname.' '.$checkpointcashier->lastname;
                        $dailyreportdata[$dayrep['id']]['checkpoint'][$checkpoint['id']]['date']    = $checkpoint['date'];
                        $dailyreportdata[$dayrep['id']]['checkpoint'][$checkpoint['id']]['cash']    = $checkpoint['cash'];
                        $dailyreportdata[$dayrep['id']]['checkpoint'][$checkpoint['id']]['noncash'] = $checkpoint['noncash'];
                        $dailyreportdata[$dayrep['id']]['checkpoint'][$checkpoint['id']]['diff']    = $checkpoint['diff'];
                        $dailyreportdata[$dayrep['id']]['checkpoint'][$checkpoint['id']]['type']    = $count++;
                    }
                } else {
                    $checkpointcashier   = [];
                    $dailyreportdata[$dayrep['id']]['checkpoint'] = [];
                }

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
                    echo '<th colspan="2">Penerimaan Sistem</th>';
                    echo '<th colspan="2">Penerimaan Aktual</th>';
                    echo '<th rowspan="2">Selisih</th>';
                    echo '<th colspan="5">Checkpoint</th>';
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
                    echo '<th>Jam</th>';
                    echo '<th>Kasir</th>';
                    echo '<th>Tunai</th>';
                    echo '<th>Non-Tunai</th>';
                    echo '<th>Selisih Checkpoint</th>';
                echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
                foreach ($dailyreportdata as $dayrep) {
                    $totalcheckpoint    = count($dayrep['checkpoint']);
                    echo '<tr>';
                        // Date
                        echo '<td rowspan="'.$totalcheckpoint.'" style="vertical-align:middle;">' . date('l, d M Y', strtotime($dayrep['date'])) . '</td>';
                        
                        // Cashflow
                        echo '<td rowspan="'.$totalcheckpoint.'" style="vertical-align:middle;" style="vertical-align:middle;">' . $dayrep['initialcash'] . '</td>';
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
                        echo '<td rowspan="'.$totalcheckpoint.'" style="vertical-align:middle;">' . $summarycashin . '</td>';
                        echo '<td rowspan="'.$totalcheckpoint.'" style="vertical-align:middle;">' . $summarycashout . '</td>';

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
                        echo '<td rowspan="'.$totalcheckpoint.'" style="vertical-align:middle;">' . $totalcash . '</td>';
                        echo '<td rowspan="'.$totalcheckpoint.'" style="vertical-align:middle;">' . $totalnoncash . '</td>';
                        echo '<td rowspan="'.$totalcheckpoint.'" style="vertical-align:middle;">' . $totaldebt . '</td>';
                        echo '<td rowspan="'.$totalcheckpoint.'" style="vertical-align:middle;">' . $totalpoin . '</td>';

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
                        echo '<td rowspan="'.$totalcheckpoint.'" style="vertical-align:middle;">' . $totaldebtcashvalue . '</td>';
                        echo '<td rowspan="'.$totalcheckpoint.'" style="vertical-align:middle;">' . $totaldebtnoncashvalue . '</td>';

                        // System Receive
                        $systemreceivecash      = (Int)$totalcash + ((Int)$dayrep['initialcash'] + ((Int)$summarycashin - (Int)$summarycashout)) + (Int)$totaldebtcashvalue;
                        $systemreceivenoncash   = (Int)$totalnoncash + (Int)$totaldebtnoncashvalue;
                        $systemreceivetotal     = (Int)$systemreceivecash + (Int)$systemreceivenoncash;
                        echo '<td rowspan="'.$totalcheckpoint.'" style="vertical-align:middle;">' . $systemreceivecash . '</td>';
                        echo '<td rowspan="'.$totalcheckpoint.'" style="vertical-align:middle;">' . $systemreceivenoncash . '</td>';

                        // Actual Receive
                        echo '<td rowspan="'.$totalcheckpoint.'" style="vertical-align:middle;">' . $dayrep['cashclose'] . '</td>';
                        echo '<td rowspan="'.$totalcheckpoint.'" style="vertical-align:middle;">' . $dayrep['noncashclose'] . '</td>';

                        // Difference
                        $totaldifference    = (Int)$dayrep['actualsummary'] - (Int)$systemreceivetotal;
                        echo '<td rowspan="'.$totalcheckpoint.'" style="vertical-align:middle;">' . $totaldifference . '</td>';

                        // Checkpoint
                        foreach ($dayrep['checkpoint'] as $checkpoint) {
                            if ($checkpoint['type'] > '0') {
                                echo '<tr>';
                                    echo '<td style="vertical-align:middle;">' . date('H:i', strtotime($checkpoint['date'])) . '</td>';
                                    echo '<td style="vertical-align:middle;">' . $checkpoint['cashier'] . '</td>';
                                    echo '<td style="vertical-align:middle;">' . $checkpoint['cash'] . '</td>';
                                    echo '<td style="vertical-align:middle;">' . $checkpoint['noncash'] . '</td>';
                                    echo '<td style="vertical-align:middle;">' . $checkpoint['diff'] . '</td>';
                                echo '</tr>';
                            } else {
                                echo '<td style="vertical-align:middle;">' . date('H:i', strtotime($checkpoint['date'])) . '</td>';
                                echo '<td style="vertical-align:middle;">' . $checkpoint['cashier'] . '</td>';
                                echo '<td style="vertical-align:middle;">' . $checkpoint['cash'] . '</td>';
                                echo '<td style="vertical-align:middle;">' . $checkpoint['noncash'] . '</td>';
                                echo '<td style="vertical-align:middle;">' . $checkpoint['diff'] . '</td>';
                            }
                        }
                    echo '</tr>';
                }
            echo '</tbody>';
        echo '</table>';
    }

    public function dailysell()
    {
        // Calling Models
        $TransactionModel       = new TransactionModel;
        $TrxdetailModel         = new TrxdetailModel;
        $OutletModel            = new OutletModel();

        $transactions       = array();
        $transactionarr     = array();
        $memberdisc         = array();
        $discounttrx        = array();
        $discountvariant    = array();
        $discountpoin       = array();
        $discountglobal     = array();
        $discountmember     = array();
        $marginmodals       = array();
        $margindasars       = array();

        if ($this->data['outletPick'] === null) {
            $transaction    = $TransactionModel->where('date >=', date('Y-m-d 00:00:00'))->where('date <=', date('Y-m-d 23:59:59'))->find();
            $address        = "58vapehouse";
            $outletname     = "All Outlets";
        } else {
            $transaction    = $TransactionModel->where('date >=', date('Y-m-d 00:00:00'))->where('date <=', date('Y-m-d 23:59:59'))->where('outletid', $this->data['outletPick'])->find();
            $outlets        = $OutletModel->find($this->data['outletPick']);
            $address        = $outlets['address'];
            $outletname     = $outlets['name'];
        }

        if (!empty($transaction)) {
            foreach ($transaction as $trx) {
                $time                               = date('H', strtotime($trx['date']));
                $transactions[$time]['date']        = date('H', strtotime($trx['date']));
                $transactions[$time]['val'][]       = $trx['value'];

                // Transaction Discount
                if (!empty($trx['discvalue'])) {
                    $discounttrx[]  = $trx['discvalue'];
                }
    
                // Point Used
                $discountpoin[]             = $trx['pointused'];

                // Member Discount
                $memberdisc[]               = $trx['memberdisc'];

                // Discount Variant
                $trxdetails  = $TrxdetailModel->where('transactionid', $trx['id'])->find();
                if (!empty($trxdetails)) {
                    foreach ($trxdetails as $trxdetail) {
                        // Transaction Detail Discount Variant
                        if ($trxdetail['discvar'] != 0) {
                            $discountvariant[]      = $trxdetail['discvar'];
                        }
        
                        // Transaction Detail Discount Global
                        if ($trxdetail['globaldisc'] != '0') {
                            $discountglobal[]       = $trxdetail['globaldisc'];
                        }
        
                        // Transaction Detail Discount Member
                        if ($trxdetail['memberdisc'] != '0') {
                            $discountmember[]       = $trxdetail['memberdisc'];
                        }

                        // Transaction Detail Margin Modal
                        $marginmodals[]                         = ((int)$trxdetail['marginmodal'] * (int)$trxdetail['qty']);
                        $transactions[$time]['profitmodal'][]   = ((int)$trxdetail['marginmodal'] * (int)$trxdetail['qty']);

                        // Transaction Detail Margin Dasar
                        $margindasars[]                         = ((int)$trxdetail['margindasar'] * (int)$trxdetail['qty']);
                        $transactions[$time]['profitdasar'][]   = ((int)$trxdetail['margindasar'] * (int)$trxdetail['qty']);
                    }
                }
            }
        }

        // Data Chart
        if (!empty($transactions)) {
            foreach ($transactions as $trxdat) {
                $transactionarr[]  = [
                    'waktu'         => $trxdat['date'],
                    'value'         => array_sum($trxdat['val']),
                    'profitmodal'   => array_sum($trxdat['profitmodal']),
                    'profitdasar'   => array_sum($trxdat['profitdasar']),
                ];
            }
        } else {
            $transactionarr[]  = [
                'waktu'         => date('Y-m-d'.'H:i:s'),
                'value'         => 0,
                'profitmodal'   => 0,
                'profitdasar'   => 0,
            ];
        }

        $transactiondisc    = (int)(array_sum($discounttrx)) + (int)(array_sum($memberdisc)) ?? 0;
        $variantdisc        = array_sum($discountvariant) ?? 0;
        $globaldisc         = array_sum($discountglobal) ?? 0;
        $memberdiscitem     = array_sum($discountmember) ?? 0;
        $poindisc           = array_sum($discountpoin) ?? 0;
        $marginmodalsum     = array_sum($marginmodals) ?? 0;
        $margindasarsum     = array_sum($margindasars) ?? 0;
        $salesresult        = array_sum(array_column($transactionarr, 'value'));
        $grossales          = (Int)$salesresult + (Int)$variantdisc + (Int)$globaldisc + (Int)$memberdiscitem + (Int)$transactiondisc + (Int)$poindisc;
        $profitmodal        = (Int)$marginmodalsum - (Int)$transactiondisc - (Int)$poindisc;
        $profitdasar        = (Int)$margindasarsum - (Int)$transactiondisc - (Int)$poindisc;

        // Total Discount
        $alldisc            = (Int)$globaldisc + (Int)$memberdiscitem + (Int)$variantdisc + (Int)$transactiondisc;

        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=Laporan Penjualan Harian $outletname (".date('d-m-Y').").xls");

        echo '<style type="text/css">

        </style>';
        echo '<table  style="width: 30%;">';
            echo '<tr>';
                echo '<th colspan="2" style="align-text:center;">Ringkasan Penjualan Harian</th>';
            echo '</tr>';
            echo '<tr>';
                echo '<th colspan="2" style="align-text:center;">' . $outletname . '</th>';
            echo '</tr>';
            echo '<tr>';
                echo '<th colspan="2" style="align-text:center;">' . $address . '</th>';
            echo '</tr>';
            echo '<tr>';
                echo '<th colspan="2" style="align-text:center;">' . date('d-m-Y') . '</th>';
            echo '</tr>';
            echo '<tr>';
                echo '<th colspan="2" style="align-text:center;"></th>';
            echo '</tr>';

            echo '<tr>';
                echo '<th>Jam</th>';
                echo '<th>Penjualan</th>';
            echo '</tr>';
            
            foreach ($transactionarr as $trxdat) {
                echo '<tr>';
                    echo '<td style="text-align: center;">'.$trxdat['waktu'].'</td>';
                    echo '<td style="text-align: center;">'.$trxdat['value'].'</td>';
                echo '</tr>';
            }

            echo '<tr>';
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
                echo '<th style="text-align: left;">Keuntungan Modal</th>';
                echo '<td style="text-align: right;">' . $profitmodal . '</td>';
            echo '</tr>';
            echo '<tr>';
                echo '<th style="text-align: left;">Keuntungan Dasar</th>';
                echo '<td style="text-align: right;">' . $profitdasar . '</td>';
            echo '</tr>';
        echo '</table>';
    }

    public function purchase()
    {
        $OutletModel                = new OutletModel();

        $input  = $this->request->getGet('daterange');

        if (!empty($input)) {
            $daterange  = explode(' - ', $input);
            $startdate  = $daterange[0];
            $enddate    = $daterange[1];
        } else {
            $startdate  = date('Y-m-01');
            $enddate    = date('Y-m-d');
        }
        $startdate  = date('Y-m-d', strtotime($startdate));
        $enddate    = date('Y-m-d', strtotime($enddate));

        if ($this->data['outletPick'] === null) {
            $address        = "58vapehouse";
            $outletnames    = "All Outlets";
        } else {
            $outlets        = $OutletModel->find($this->data['outletPick']);
            $address        = $outlets['address'];
            $outletnames    = $outlets['name'];
        }

        $db = \Config\Database::connect();

        $builder = $db->table('purchasedetail');
        $builder->select('
            purchase.date,
            COALESCE(outlet.name, "") as outlet_name,
            COALESCE(supplier.name, "") as supplier_name,
            COALESCE(variant.sku, "") as sku,
            COALESCE(product.name, "") as prod_name,
            COALESCE(variant.name, "") as variant_name,
            purchasedetail.qty,
            purchasedetail.price
        ');
        $builder->join('purchase', 'purchase.id = purchasedetail.purchaseid');
        $builder->join('outlet', 'outlet.id = purchase.outletid', 'left');
        $builder->join('supplier', 'supplier.id = purchase.supplierid', 'left');
        $builder->join('variant', 'variant.id = purchasedetail.variantid', 'left');
        $builder->join('product', 'product.id = variant.productid', 'left');
        $builder->where('purchase.status', '1');
        $builder->where('purchase.date >=', $startdate . ' 00:00:00');
        $builder->where('purchase.date <=', $enddate . ' 23:59:59');
        if ($this->data['outletPick'] !== null) {
            $builder->where('purchase.outletid', $this->data['outletPick']);
        }
        $builder->orderBy('purchase.date', 'DESC');

        $rows = $builder->get()->getResultArray();

        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=Pembelian Barang $outletnames ($startdate-$enddate).xls");

        echo '<table>';
            echo '<thead>';
                echo '<tr>';
                    echo '<th colspan="9" style="align-text:center;">Ringkasan Pembelian Barang</th>';
                echo '</tr>';
                echo '<tr>';
                    echo '<th colspan="9" style="align-text:center;">' . $outletnames . '</th>';
                echo '</tr>';
                echo '<tr>';
                    echo '<th colspan="9" style="align-text:center;">' . $address . '</th>';
                echo '</tr>';
                echo '<tr>';
                    echo '<th colspan="9" style="align-text:center;">' . $startdate. ' - ' . $enddate . '</th>';
                echo '</tr>';
                echo '<tr>';
                    echo '<th colspan="9" style="align-text:center;"></th>';
                echo '</tr>';
                echo '<tr>';
                    echo '<th>Tanggal</th>';
                    echo '<th>Outlet</th>';
                    echo '<th>Pemasok</th>';
                    echo '<th>SKU</th>';
                    echo '<th>Product</th>';
                    echo '<th>Variant</th>';
                    echo '<th>Jumlah Barang</th>';
                    echo '<th>Harga Beli</th>';
                    echo '<th>Total</th>';
                echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
                foreach ($rows as $row) {
                    $total  = (int)$row['qty'] * (int)$row['price'];
                    echo '<tr>';
                        echo '<td>' . date('d M Y', strtotime($row['date'])) . '</td>';
                        echo '<td>' . $row['outlet_name'] . '</td>';
                        echo '<td>' . $row['supplier_name'] . '</td>';
                        echo '<td>' . $row['sku'] . '</td>';
                        echo '<td>' . $row['prod_name'] . '</td>';
                        echo '<td>' . $row['variant_name'] . '</td>';
                        echo '<td>' . $row['qty'] . '</td>';
                        echo '<td>' . $row['price'] . '</td>';
                        echo '<td>' . $total . '</td>';
                    echo '</tr>';
                }
            echo '</tbody>';
        echo '</table>';
    }
}
