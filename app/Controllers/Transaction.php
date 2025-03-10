<?php

namespace App\Controllers;

use App\Models\BundledetailModel;
use App\Models\BundleModel;
use App\Models\CashModel;
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
use App\Models\TrxpaymentModel;
use App\Models\BookingModel;
use App\Models\BookingdetailModel;
use App\Models\DailyReportModel;

class Transaction extends BaseController
{
    protected $data;
    protected $db, $builder;
    protected $auth;
    protected $config;
    
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
        $BookingModel           = new BookingModel();
        $BookingdetailModel     = new BookingdetailModel();
        $DailyReportModel       = new DailyReportModel();

        // Populating Data
        $bundles                = $BundleModel->findAll();
        $bundets                = $BundledetModel->findAll();
        $Cash                   = $CashModel->findAll();
        $outlets                = $OutletModel->findAll();
        $users                  = $UserModel->findAll();
        $customers              = $MemberModel->findAll();
        $payments               = $PaymentModel->findAll();
        $products               = $ProductModel->orderBy('name', 'ASC')->where('status', '1')->find();
        $variants               = $VariantModel->findAll();
        $stocks                 = $StockModel->findAll();
        $transactions           = $TransactionModel->findAll();
        $trxdetails             = $TrxdetailModel->findAll();
        $trxpayments            = $TrxpaymentModel->findAll();
        $bookings               = $BookingModel->where('outletid', $this->data['outletPick'])->where('status', '0')->orderBy('created_at', 'DESC')->findAll();
        // $bookingdetails         = $BookingdetailModel->findAll();

        // Bundle Data
        $bundleBuilder          = $db->table('bundledetail');
        $bundleVariants         = $bundleBuilder->select('bundledetail.bundleid as bundleid, variant.id as id, variant.productid as productid, variant.name as name, stock.outletid as outletid, stock.qty as qty');
        $bundleVariants         = $bundleBuilder->join('variant', 'bundledetail.variantid = variant.id', 'left');
        $bundleVariants         = $bundleBuilder->join('stock', 'stock.variantid = variant.id', 'left');
        $bundleVariants         = $bundleBuilder->orderBy('stock.qty', 'ASC');
        $bundleVariants         = $bundleBuilder->get();

        // Booking Data
        $bookingdata            = [];
        foreach ($bookings as $booking) {
            // Booking Data
            $bookingdata[$booking['id']]['bookid']          = $booking['id'];
            $bookingdata[$booking['id']]['bookvalue']       = $booking['value'];
            $bookingdata[$booking['id']]['bookdate']        = $booking['created_at'];
            
            // Customer Data
            $bookcustomer       = $MemberModel->find($booking['memberid']);
            if ($booking['memberid'] != '0') {
                $bookingdata[$booking['id']]['custid']      = $bookcustomer['id'];    
                $bookingdata[$booking['id']]['custname']    = $bookcustomer['name'].' / '.$bookcustomer['phone'];
            } else {
                $bookingdata[$booking['id']]['custid']      = '0';    
                $bookingdata[$booking['id']]['custname']    = 'Non Member';
            }

            // Booking Detail Data
            $bookingdetails     = $BookingdetailModel->where('bookingid', $booking['id'])->find();
            foreach ($bookingdetails as $bookdet) {

                // Detail Booking Not Bundle
                if (($bookdet['variantid'] != '0') && ($bookdet['bundleid'] == '0')) {
                    // Data Variant
                    $bookvar       = $VariantModel->find($bookdet['variantid']);
                    // $bookingdata[$booking['id']]['bookvarid']               = $bookvar['id'];
                    
                    if (!empty($bookvar)) {
                        // Data Stock
                        if ($this->data['outletPick'] != null) {
                            $stocks     = $StockModel->where('variantid', $bookvar['id'])->where('outletid', $this->data['outletPick'])->find();
                        } else {
                            $stocks     = $StockModel->where('variantid', $bookvar['id'])->find();
                        }
                        $bookprod   = $ProductModel->find($bookvar['productid']);

                        if (!empty($bookprod)) {
                            $bookingdata[$booking['id']]['variantdata'][$bookdet['variantid'].$bookdet['bundleid']]['bookvarid']            = $bookvar['id'];
                            $bookingdata[$booking['id']]['variantdata'][$bookdet['variantid'].$bookdet['bundleid']]['bookvarprice']         = (Int)$bookvar['hargamodal'] + (Int)$bookvar['hargajual'];
                            $bookingdata[$booking['id']]['variantdata'][$bookdet['variantid'].$bookdet['bundleid']]['prodname']             = $bookprod['name'].' - '.$bookvar['name'];
                            $bookingdata[$booking['id']]['variantdata'][$bookdet['variantid'].$bookdet['bundleid']]['bookdetqty']           = $bookdet['qty'];
                            $bookingdata[$booking['id']]['variantdata'][$bookdet['variantid'].$bookdet['bundleid']]['bookdetvalue']         = $bookdet['value'];
                            
                            foreach ($stocks as $stock) {
                                $bookingdata[$booking['id']]['variantdata'][$bookdet['variantid'].$bookdet['bundleid']]['stock']            = $stock['qty'];
                            }
                        }
                    }
                }
                
                if (($bookdet['variantid'] == '0') && ($bookdet['bundleid'] != '0')) {
                    // Data Bundle
                    $bookbundles        = $BundleModel->find($bookdet['bundleid']);

                    if (!empty($bundles)) {
                        // Data Bundle Detail
                        $bookbundledets = $BundledetModel->where('bundleid', $bookbundles['id'])->find();

                        if (!empty($bookbundledets)) {
                            foreach ($bookbundledets as $bundet) {
                                // Data Variant
                                $bundlevariants         = $VariantModel->find($bundet['variantid']);
                                
                                if (!empty($bundlevariants)) {
                                    // Data Stock
                                    if ($this->data['outletPick'] != null) {
                                        $bundleStocks   = $StockModel->where('variantid', $bundlevariants['id'])->where('outletid', $this->data['outletPick'])->find();
                                    } else {
                                        $bundleStocks   = $StockModel->where('variantid', $bundlevariants['id'])->find();
                                    }

                                    $bundleproduct      = $ProductModel->find($bundlevariants['productid']);
            
                                    if (!empty($bundleproduct)) {
                                        $bookingdata[$booking['id']]['bundledata'][$bookdet['variantid'].$bookdet['bundleid']]['bookbundid']                = $bookbundles['id'];
                                        $bookingdata[$booking['id']]['bundledata'][$bookdet['variantid'].$bookdet['bundleid']]['bookbundprice']             = (Int)$bookbundles['price'];
                                        $bookingdata[$booking['id']]['bundledata'][$bookdet['variantid'].$bookdet['bundleid']]['bundname']                  = $bookbundles['name'];
                                        $bookingdata[$booking['id']]['bundledata'][$bookdet['variantid'].$bookdet['bundleid']]['bookbundqty']               = $bookdet['qty'];
                                        $bookingdata[$booking['id']]['bundledata'][$bookdet['variantid'].$bookdet['bundleid']]['bookbundvalue']             = $bookdet['value'];

                                        foreach ($bundleStocks as $bundstok) {
                                            $bookingdata[$booking['id']]['bundledata'][$bookdet['variantid'].$bookdet['bundleid']]['stock']                 = $bundstok['qty'];
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        // Find Data for Daily Report
        $today                  = date('Y-m-d') . ' 00:00:01';
        $dailyreport            = $DailyReportModel->where('dateopen >', $today)->where('outletid', $this->data['outletPick'])->first();
        $closed                 = $DailyReportModel->where('dateopen >', $today)->where('dateclose !=', '0000-00-00 00:00:00')->where('outletid', $this->data['outletPick'])->first();

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
        $data['members']        = $MemberModel->findAll();
        $data['customers']      = $customers;
        $data['products']       = $products;
        $data['variants']       = $variants;
        $data['stocks']         = $stocks;
        $data['trxdetails']     = $trxdetails;
        $data['trxpayments']    = $trxpayments;
        $data['bundleVariants'] = $bundleVariants->getResult();
        // $data['bookings']       = $bookings;
        $data['bookings']       = $bookingdata;
        // $data['bookingdetails'] = $bookingdetails;
        $data['dailyreport']    = $dailyreport;
        $data['closed']         = $closed;

        return view('Views/transaction', $data);
    }

    // public function create()
    // {
    //     // Calling Models
    //     $BundleModel            = new BundleModel();
    //     $BundledetModel         = new BundledetailModel();
    //     $CashModel              = new CashModel();
    //     $GconfigModel           = new GconfigModel();
    //     $OutletModel            = new OutletModel();
    //     $UserModel              = new UserModel();
    //     $MemberModel            = new MemberModel();
    //     $PaymentModel           = new PaymentModel();
    //     $ProductModel           = new ProductModel();
    //     $VariantModel           = new VariantModel();
    //     $StockModel             = new StockModel();
    //     $TransactionModel       = new TransactionModel();
    //     $TrxdetailModel         = new TrxdetailModel();
    //     $TrxpaymentModel        = new TrxpaymentModel();

    //     // Populating Data
    //     $stocks      = $StockModel->findall();
    //     $Gconfig     = $GconfigModel->first();

    //     // initialize
    //     $input = $this->request->getPost();

    //     // date time stamp
    //     $date = date_create();
    //     $tanggal = date_format($date, 'Y-m-d H:i:s');

    //     // Image Capture
    //     $img            = $input['image'];
    //     $folderPath     = "img";
    //     $image_parts    = explode(";base64,", $img);
    //     $image_type_aux = explode("image/", $image_parts[0]);
    //     $image_type     = $image_type_aux[1];
    //     $image_base64   = base64_decode($image_parts[1]);
    //     $fileName       = uniqid() . '.png';
    //     $file           = $folderPath . $fileName;
    //     file_put_contents($file, $image_base64);


    //     if (!empty($input['payment'])) {
    //         // This Single Payment Control
    //         // validation form
    //         // Insert Data
    //         $data = [
    //             'outletid'  => $this->data['outletPick'],
    //             'userid'    => $this->data['uid'],
    //             'memberid'  => $input['customerid'],
    //             'paymentid' => $input['payment'],
    //             'value'     => $input['value'],
    //             'disctype'  => $input['disctype'],
    //             'discvalue' => $input['discvalue'],
    //             'date'      => $tanggal,
    //             'photo'     => $fileName,
    //         ];
    //         // save data transaction

    //         $TransactionModel->save($data);

    //         // tranasaction id
    //         $trxId = $TransactionModel->getInsertID();

    //         // save variants item
    //         if (!empty($input["qty"])) {
    //             $variant = $input["qty"];
    //             foreach ($variant as $vId => $val) {
    //                 $varId = $vId;
    //                 $qty  = $val;
    //             }
    //             $value = $VariantModel->where('id', $vId)->first();
    //             $price = $value['hargamodal'] + $value['hargajual'];
    //             $varPrice = $price * $qty;
    //             $data = [
    //                 'transactionid' => $trxId,
    //                 'variantid'     => $varId,
    //                 'bundleid'      => "0",
    //                 'qty'           => $qty,
    //                 'value'         => $varPrice,
    //             ];
    //             $TrxdetailModel->save($data);

    //             // Minus Stock
    //             $stok = $StockModel->where('variantid', $varId)->where('outletid', $this->data['outletPick'])->first();
    //             $newStock = $stok['qty'] - $qty;
    //             $data = [
    //                 'id' => $stok['id'],
    //                 'qty' => $newStock,
    //             ];
    //             $StockModel->save($data);
    //         } else {
    //             $varPrice = "0";
    //         }

    //         // save bundle item
    //         if (!empty($input['bqty'])) {
    //             $bundles = $input['bqty'];
    //             foreach ($bundles as $y => $value) {
    //                 $bundId = $y;
    //                 $qty    = $value;
    //             }
    //             $value = $BundleModel->where('id', $bundId)->first();
    //             $price = $value['price'];
    //             $bunPrice = $price * $qty;
    //             $data = [
    //                 'transactionid' => $trxId,
    //                 'variantid'     => "0",
    //                 'bundleid'      => $y,
    //                 'qty'           => $qty,
    //                 'value'         => $bunPrice,
    //             ];
    //             $TrxdetailModel->save($data);

    //             // minus stock
    //             $bundet = $BundledetModel->where('bundleid', $y)->find();
    //             foreach ($bundet as $bun => $val) {
    //                 $bunid = $val['bundleid'];
    //                 $varid = $val['variantid'];
    //                 foreach ($stocks as $stock) {
    //                     $stock = $StockModel->where('variantid', $varid)->where('outletid', $this->data['outletPick'])->first();
    //                     $newStock = $stock['qty'] - $qty;
    //                     $stok = [
    //                         'id' => $stock['id'],
    //                         'qty' => $newStock,
    //                     ];
    //                     $StockModel->save($stok);
    //                 }
    //             }
    //         } else {
    //             $bunPrice = "0";
    //         }

    //         //Discount Price
    //         if (!empty($input['discvalue'])) {
    //             if ($input['disctype'] === "0") {
    //                 $discPrice = $input['discvalue'];
    //             } else {
    //                 $sumPrice   = $varPrice + $bunPrice;
    //                 $discPrice   = ($sumPrice * $input['discvalue']) / 100;
    //             }
    //         } else {
    //             $discPrice = "0";
    //         }

    //         //Discount Point Member
    //         if (!empty($input['customerid'])) {
    //             $discPoint   = $input['poin'];
    //             $member      = $MemberModel->where('id', $input['customerid'])->first();
    //             $memberPoint = $member['poin'];
    //             // Used Poin 
    //             if (!empty($input['poin'])) {
    //                 $point       = $memberPoint - $discPoint;
    //             } else {
    //                 // Not Apply Point
    //                 $point  = $memberPoint;
    //             }
    //         }

    //         //Minus Member Point
    //         $data = [
    //             'id' => $member['id'],
    //             'poin' => $point,
    //         ];
    //         $MemberModel->save($data);

    //         // $member Disc
    //         $memberdisc = $Gconfig['memberdisc'];
    //         // subtotal
    //         $subtotal = $varPrice + $bunPrice;
    //         // ppn
    //         $ppn = ($subtotal * $Gconfig['ppn']) / 100;

    //         //Insert Trx Payment 
    //         $total = $subtotal - $discPrice - $discPoint + $ppn - $memberdisc;
    //         $data = [
    //             'paymentid'     => $input['payment'],
    //             'transactionid' => $trxId,
    //             'value'         => $total,
    //         ];
    //         $TrxpaymentModel->save($data);

    //         // Insert Cash
    //         $cashPlus   = $CashModel->where('id', $input['payment'])->first();
    //         $cashUpdate = $varPrice + $bunPrice + $cashPlus['qty'];
    //         $data = [
    //             'id'    => $cashPlus['id'],
    //             'qty'   => $cashUpdate,
    //         ];
    //         $CashModel->save($data);

    //         // Gconfig setup
    //         $minimTrx    = $Gconfig['poinorder'];
    //         $poinval     = $Gconfig['memberdisc'];

    //         if ($total >= $minimTrx) {
    //             $value  = $total / $minimTrx;
    //             $result = floor($value);
    //             $poin   = $result * $poinval;
    //         } else {
    //             $poin = "0";
    //         }

    //         //Update Point Member
    //         if (!empty($input['memberid'])) {
    //             $trx = $member['trx'] + 1;
    //             $poinPlus = $memberPoint + $poin;
    //             $data = [
    //                 'id'    => $member['id'],
    //                 'poin'  => $poinPlus,
    //                 'trx'   => $trx,
    //             ];
    //             $MemberModel->save($data);
    //         }

    //         //Update debt
    //         if (!empty($input['duedate'])) {
    //             $data = [
    //                 'memberid'      => $input['customerid'],
    //                 'transationid'  => $trxId,
    //                 'deadline'      => $input['duedate'],
    //             ];
    //         }
    //     } else {

    //         // Variants Value
    //         if (!empty($input["qty"])) {
    //             $variant = $input["qty"];
    //             foreach ($variant as $vId => $val) {
    //                 $varId = $vId;
    //                 $qty  = $val;
    //             }
    //             $value = $VariantModel->where('id', $vId)->first();
    //             $price = $value['hargamodal'] + $value['hargajual'];
    //             $varPrice = $price * $qty;
    //         } else {
    //             $varPrice = "0";
    //         }

    //         // Bundle Value
    //         if (!empty($input['bqty'])) {
    //             $bundles = $input['bqty'];
    //             foreach ($bundles as $y => $value) {
    //                 $bundId = $y;
    //                 $qty    = $value;
    //             }
    //             $value = $BundleModel->where('id', $bundId)->first();
    //             $price = $value['price'];
    //             $bunPrice = $price * $qty;
    //         } else {
    //             $bunPrice = "0";
    //         }

    //         $totalValue = $varPrice + $bunPrice;

    //         // Insert Data
    //         $data = [
    //             'outletid'  => $this->data['outletPick'],
    //             'userid'    => $this->data['uid'],
    //             'memberid'  => $input['customerid'],
    //             'paymentid' => "0",
    //             'value'     => $totalValue,
    //             'disctype'  => $input['disctype'],
    //             'discvalue' => $input['discvalue'],
    //             'date'      => $tanggal,
    //         ];
    //         // save data transaction
    //         $TransactionModel->save($data);

    //         // transaction id
    //         $trxId = $TransactionModel->getInsertID();

    //         // variants item
    //         if (!empty($input["qty"])) {
    //             $variant = $input["qty"];
    //             foreach ($variant as $vId => $val) {
    //                 $varId = $vId;
    //                 $qty  = $val;
    //             }
    //             $value = $VariantModel->where('id', $vId)->first();
    //             $price = $value['hargamodal'] + $value['hargajual'];
    //             $fprice = $price * $qty;
    //             // save transaction detail
    //             $data = [
    //                 'transactionid' => $trxId,
    //                 'variantid'     => $varId,
    //                 'bundleid'      => "0",
    //                 'qty'           => $qty,
    //                 // 'description'   => $input['description'],
    //                 'value'         => $fprice,
    //             ];
    //             $TrxdetailModel->save($data);

    //             // Minus Stock
    //             $stok = $StockModel->where('variantid', $varId)->where('outletid', $this->data['outletPick'])->first();
    //             $newStock = $stok['qty'] - $qty;
    //             $data = [
    //                 'id' => $stok['id'],
    //                 'qty' => $newStock,
    //             ];
    //             $StockModel->save($data);
    //         }

    //         // bundle item
    //         if (!empty($input['bqty'])) {
    //             $bundles = $input['bqty'];
    //             foreach ($bundles as $y => $value) {
    //                 $bundId = $y;
    //                 $qty    = $value;
    //             }
    //             $value = $BundleModel->where('id', $bundId)->first();
    //             $price = $value['price'];
    //             $fprice = $price * $qty;
    //             // save transaction detail
    //             $data = [
    //                 'transactionid' => $trxId,
    //                 'variantid'     => "0",
    //                 'bundleid'      => $y,
    //                 'qty'           => $qty,
    //                 'value'         => $fprice,
    //             ];
    //             $TrxdetailModel->save($data);

    //             // minus stock
    //             $bundet = $BundledetModel->where('bundleid', $y)->find();
    //             foreach ($bundet as $bun => $val) {
    //                 $bunid = $val['bundleid'];
    //                 $varid = $val['variantid'];
    //                 foreach ($stocks as $stock) {
    //                     $stock = $StockModel->where('variantid', $varid)->where('outletid', $this->data['outletPick'])->first();
    //                     $newStock = $stock['qty'] - $qty;
    //                     $stok = [
    //                         'id' => $stock['id'],
    //                         'qty' => $newStock,
    //                     ];
    //                     $StockModel->save($stok);
    //                 }
    //             }
    //         }

    //         //Discount Price
    //         if (!empty($input['discvalue'])) {
    //             if ($input['disctype'] === "0") {
    //                 $discPrice = $input['discvalue'];
    //             } else {
    //                 //Discount Percent 
    //                 $sumPrice   = $varPrice + $bunPrice;
    //                 $discPrice   = ($sumPrice * $input['discvalue']) / 100;
    //             }
    //         } else {
    //             $discPrice = "0";
    //         }

    //         //Discount Point Member
    //         if (!empty($input['customerid'])) {
    //             $discPoint   = $input['poin'];
    //             $member      = $MemberModel->where('id', $input['customerid'])->first();
    //             $memberPoint = $member['poin'];
    //             // Used Poin 
    //             if (!empty($input['poin'])) {
    //                 $point       = $memberPoint - $discPoint;
    //             } else {
    //                 // Not Apply Point
    //                 $point  = $memberPoint;
    //             }
    //         }

    //         //Minus Member Point
    //         $data = [
    //             'id' => $member['id'],
    //             'poin' => $point,
    //         ];
    //         $MemberModel->save($data);

    //         //Insert First Trx Payment 
    //         $data = [
    //             'paymentid'     => $input['firstpayment'],
    //             'transactionid' => $trxId,
    //             'value'         => $input['firstpay']
    //         ];
    //         $TrxpaymentModel->save($data);

    //         //Insert Second Trx Payment 
    //         $data = [
    //             'paymentid'     => $input['secondpayment'],
    //             'transactionid' => $trxId,
    //             'value'         => $input['secpay']
    //         ];
    //         $TrxpaymentModel->save($data);

    //         // Insert First Cash 
    //         $cashPlus   = $CashModel->where('id', $input['firstpayment'])->first();
    //         $cashUpdate = $input['firstpay'] + $cashPlus['qty'];
    //         $data = [
    //             'id'    => $cashPlus['id'],
    //             'qty'   => $cashUpdate,
    //         ];
    //         $CashModel->save($data);

    //         // Insert Second Cash 
    //         $cashPlus   = $CashModel->where('id', $input['secondpayment'])->first();
    //         $cashUpdate = $input['secpay'] + $cashPlus['qty'];
    //         $data = [
    //             'id'    => $cashPlus['id'],
    //             'qty'   => $cashUpdate,
    //         ];
    //         $CashModel->save($data);
    //     }
    //     return redirect()->back()->with('massage', lang('global.saved'));
    // }

    // public function pay()
    // {
    //     // Calling Models
    //     $TransactionModel   = new TransactionModel();
    //     $TrxdetailModel     = new TrxdetailModel();
    //     $PaymentModel       = new PaymentModel();
    //     $TrxpaymentModel    = new TrxpaymentModel();
    //     $VariantModel       = new VariantModel();
    //     $BundleModel        = new BundleModel();
    //     $BundleDetailModel  = new BundledetailModel();
    //     $StockModel         = new StockModel();

    //     // Getting Inputs
    //     $input = $this->request->getPost();

    //     // Populating Data
    //     $date = date('Y-m-d H:i:s');

    //     // Conditions

    //     // Inserting Transaction
    //     $varvalues = array();
    //     $bundvalues = array();

    //     if (!empty($input['qty'])) {
    //         foreach ($input['qty'] as $varid => $varqty) {
    //             $variant = $VariantModel->find($varid);
    //             $varvalues[] = $varqty * ($variant['hargamodal'] + $variant['hargajual']);
    //         }
    //     } else {
    //         $varvalues[] = '0';
    //     }

    //     if (!empty($input['bqty'])) {
    //         foreach ($input['bqty'] as $bunid => $bundqty) {
    //             $bundle = $BundleModel->find($bunid);
    //             $bundvalues[] = $bundqty * $bundle['price'];
    //         }
    //     } else {
    //         $bundvalues[] = '0';
    //     }

    //     $varvalue = array_sum($varvalues);
    //     $bundvalue = array_sum($bundvalues);

    //     $subtotal = $varvalue + $bundvalue;

    //     if ($input['customerid'] != '0') {
    //         $memberid = $input['customerid'];
    //         if ($this->data['gconfig']['memberdisctype'] === '0') {
    //             $memberdisc = $this->data['gconfig']['memberdisc'];
    //         } elseif ($this->data['gconfig']['memberdisctype'] === '1') {
    //             $memberdisc = ($this->data['gconfig']['memberdisc'] / 100) * $subtotal;
    //         }
    //     } else {
    //         $memberid = '';
    //         $memberdisc = '0';
    //     }

    //     if ((!empty($input['discvalue'])) && ($input['disctype'] === '0')) {
    //         $discount = $input['discvalue'];
    //     } elseif ((!empty($input['discvalue'])) && ($input['disctype'] === '1')) {
    //         $discount = ($input['discvalue'] / 100) * $subtotal;
    //     } else {
    //         $discount = '0';
    //     }

    //     if (!empty($input['poin'])) {
    //         $poin = $input['poin'];
    //     } else {
    //         $poin = '0';
    //     }

    //     $value = $subtotal - $memberdisc - $discount - $poin;

    //     if (!empty($input['value'])) {
    //         $paymentid = $input['payment'];
    //     } else {
    //         $paymentid = '0';
    //     }

    //     $trx = [
    //         'outletid'      => $this->data['outletPick'],
    //         'userid'        => $this->data['uid'],
    //         'memberid'      => $memberid,
    //         'paymentid'     => $paymentid,
    //         'value'         => $value,
    //         'disctype'      => $input['disctype'],
    //         'discvalue'     => $input['discvalue'],
    //         'date'          => $date
    //     ];
    //     $TransactionModel->insert($trx);
    //     $trxId = $TransactionModel->getInsertID();

    //     // Transaction Detail & Stock
    //     if (!empty($input['qty'])) {
    //         foreach ($input['qty'] as $varid => $varqty) {
    //             $variant = $VariantModel->find($varid);
    //             $trxvar = [
    //                 'transactionid' => $trxid,
    //                 'variantid'     => $varid,
    //                 'qty'           => $varqty,
    //                 'value'         => ($variant['hargamodal'] + $variant['hargajual']) * $varqty
    //             ];
    //             $TrxdetailModel->save($trxvar);

    //             $stock = $StockModel->where('outletid', $this->data['outletPick'])->where('variantid', $varid)->first();
    //             $saleVarStock = [
    //                 'id'        => $stock['id'],
    //                 'sale'      => $date,
    //                 'qty'       => $stock['qty'] - $varqty
    //             ];
    //             $StockModel->save($saleVarStock);
    //         }
    //     }

    //     if (!empty($input['bqty'])) {
    //         foreach ($input['bqty'] as $bunid => $bunqty) {
    //             $bundle = $BundleModel->find($bunid);
    //             $trxbun = [
    //                 'transactionid' => $trxid,
    //                 'bundleid'      => $bunid,
    //                 'qty'           => $bunqty,
    //                 'value'         => $bundle['price'] * $bunqty
    //             ];
    //             $TrxdetailModel->save($trxbun);

    //             $bundledetail = $BundleDetailModel->where('bundleid', $bunid)->find();
    //             foreach ($bundledetail as $BundleDetail) {
    //                 $bunstock = $StockModel->where('outletid', $this->data['outletPick'])->where('variantid', $BundleDetail['variantid'])->first();
    //                 $saleBunStock = [
    //                     'id'        => $bunstock['id'],
    //                     'sale'      => $date,
    //                     'qty'       => $bunstock['qty'] - $bunqty
    //                 ];
    //                 $StockModel->save($saleBunStock);
    //             }
    //         }
    //     }

    //     //Minus Member Point
    //     if ($input['poin'] != '0') {
    //         $data = [
    //             'id' => $input['customer'],
    //             'poin' => $point,
    //         ];
    //         $MemberModel->save($data);
    //     }

    //     // $member Disc
    //     $memberdisc = $Gconfig['memberdisc'];
    //     // subtotal
    //     $subtotal = $varPrice + $bunPrice;
    //     // ppn
    //     $ppn = ($subtotal * $Gconfig['ppn']) / 100;

    //     //Insert Trx Payment 
    //     $total = $subtotal - $discPrice - $discPoint - $memberdisc + $ppn;
    //     $data = [
    //         'paymentid'     => $input['payment'],
    //         'transactionid' => $trxId,
    //         'value'         => $total,
    //     ];
    //     $TrxpaymentModel->save($data);

    //     // Insert Cash
    //     $cashPlus   = $CashModel->where('id', $input['payment'])->first();
    //     $cashUpdate = $varPrice + $bunPrice + $cashPlus['qty'];
    //     $data = [
    //         'id'    => $cashPlus['id'],
    //         'qty'   => $cashUpdate,
    //     ];
    //     $CashModel->save($data);

    //     // Gconfig setup
    //     $minimTrx    = $Gconfig['poinorder'];
    //     $poinval     = $Gconfig['memberdisc'];

    //     if ($total >= $minimTrx) {
    //         $value  = $total / $minimTrx;
    //         $result = floor($value);
    //         $poin   = $result * $poinval;
    //     } else {
    //         $poin = "0";
    //     }

    //     //Update Point Member
    //     if (!empty($input['memberid'])) {
    //         $trx = $member['trx'] + 1;
    //         $poinPlus = $memberPoint + $poin;
    //         $data = [
    //             'id'    => $member['id'],
    //             'poin'  => $poinPlus,
    //             'trx'   => $trx,
    //         ];
    //         $MemberModel->save($data);
    //     }

    //     //Update debt
    //     if (!empty($input['duedate'])) {
    //         $data = [
    //             'memberid'      => $input['customerid'],
    //             'transationid'  => $trxId,
    //             'deadline'      => $input['duedate'],
    //         ];
    //     }
    // }

    // public function restorestock()
    // {
    //     // Calling Model
    //     $StockModel = new StockModel();
    //     $BookingModel = new BookingModel();

    //     // Populating Data
    //     $input = $this->request->getPost();
    //     $outletId = $this->data['outletPick'];

    //     // Restore Stock
    //     $stocks = $StockModel->where('outletid', $outletId)->find();
    //     foreach ($stocks as $stock) {
    //         if (isset($input['datavar'])) {
    //             foreach ($input['datavar'] as $varkey => $varvalue) {
    //                 if ($varkey == $stock['variantid']) {
    //                     $varstock = [
    //                         'id'        => $stock['id'],
    //                         'qty'       => $stock['qty'] + $varvalue
    //                     ];
    //                     $StockModel->save($varstock);
    //                 }
    //             }
    //         }
    //         if (isset($input['databund'])) {
    //             foreach ($input['databund'] as $bunkey => $bunvalue) {
    //                 if ($bunkey == $stock['variantid']) {
    //                     $bunstock = [
    //                         'id'        => $stock['id'],
    //                         'qty'       => $stock['qty'] + $bunvalue
    //                     ];
    //                     $StockModel->save($bunstock);
    //                 }
    //             }
    //         }
    //     }

    //     // Set Booking Status
    //     $bookingdata = [
    //         'id'        => $input['bookingid'],
    //         'status'    => true
    //     ];
    //     $BookingModel->save($bookingdata);

    //     // Return message
    //     die(json_encode('success'));
    // }

}
