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
use App\Models\TrxpaymentModel;
use App\Models\DailyReportModel;

class Pay extends BaseController
{
    protected $data;
    protected $db, $builder;
    protected $auth;
    protected $config;
    
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

        // Getting Inputs
        $input = $this->request->getPost();
        
        // Image Capture
        if (!empty($input['image'])) {

            $img            = $input['image'];
            $folderPath     = "img/tfproof/";
            $image_parts    = explode(";base64,", $img);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type     = $image_type_aux[1];
            $image_base64   = base64_decode($image_parts[1]);
            $fileName       = uniqid() . '.png';
            $file           = $folderPath . $fileName;
            file_put_contents($file, $image_base64);
        } else {
            $fileName = "NULL";
        }

        // Populating Data
        $date           = date('Y-m-d H:i:s');
        $Gconfig        = $GconfigModel->first();
        // $customers      = $MemberModel->findAll();

        // Inserting Transaction
        $varvalues      = array();
        $bundvalues     = array();

        if (!empty($input['qty'])) {
            foreach ($input['qty'] as $varid => $varqty) {
                $variant        = $VariantModel->find($varid);
                $discvar        = (int)$input['varprice'][$varid] * (int)$varqty;

                if (!empty($input['varprice'][$varid])) {
                    $varval  = ((int)$varqty * ((int)$variant['hargamodal'] + (int)$variant['hargajual'])) - (int)$discvar;
                } else {
                    $varval = (int)$varqty * ((int)$variant['hargamodal'] + (int)$variant['hargajual']);
                }

                // $discvar        = (int)$input['varprice'][$varid] * (int)$varqty;
                // $discbargain    = (int)$input['varbargain'][$varid] * (int)$varqty;

                // // Bargain And Varprice Added
                // if (!empty($input['varprice'][$varid]) && !empty($input['varbargain'][$varid]) && $discbargain !== 0) {
                //     $varval  = (int)$discbargain - (int)$discvar;
                //     // Vaprice Added And Null Bargain
                // } elseif (isset($input['varprice'][$varid]) && !isset($input['varbargain'][$varid]) || $discbargain === 0) {
                //     $varval  = ((int)$varqty * ((int)$variant['hargamodal'] + (int)$variant['hargajual'])) - (int)$discvar;
                //     // Bargain Added And Null Varprice
                // } elseif ((empty($input['varprice'][$varid])) && (isset($input['varbargain'][$varid])) && ($discbargain !== 0)) {
                //     $varval  = (int)$discbargain;
                //     // Null Bargain & Varprice
                // } elseif (empty($input['varprice'][$varid]) && empty($input['varbargain'][$varid])) {
                //     $varval = (int)$varqty * ((int)$variant['hargamodal'] + (int)$variant['hargajual']);
                // }

                if (($this->data['gconfig']['globaldisc'] != '0') || ($this->data['gconfig']['globaldisc'] != null)) {
                    if ($this->data['gconfig']['globaldisctype'] === '0') {
                        $globaldisc = (Int)$this->data['gconfig']['globaldisc'] * (int)$varqty;
                    } elseif ($this->data['gconfig']['globaldisctype'] === '1') {
                        $globaldisc = (((int)$this->data['gconfig']['globaldisc'] / 100) * (int)$varval);
                    }
                } else {
                    $globaldisc = 0;
                }

                $varvalues[]    = (Int)$varval - (Int)$globaldisc;
            }
        } else {
            $varvalues[] = '0';
        }

        if (!empty($input['bqty'])) {
            foreach ($input['bqty'] as $bunid => $bundqty) {
                $bundle         = $BundleModel->find($bunid);
                $bundleval      = (Int)$bundqty * (Int)$bundle['price'];

                if (($this->data['gconfig']['globaldisc'] != '0') || ($this->data['gconfig']['globaldisc'] != null)) {
                    if ($this->data['gconfig']['globaldisctype'] === '0') {
                        $globaldisc = (Int)$this->data['gconfig']['globaldisc'] * (int)$bundqty;
                    } elseif ($this->data['gconfig']['globaldisctype'] === '1') {
                        $globaldisc = (((int)$this->data['gconfig']['globaldisc'] / 100) * (int)$bundle['price']) * (int)$bundqty;
                    }
                } else {
                    $globaldisc = 0;
                }

                $bundvalues[]    = (Int)$bundleval - (Int)$globaldisc;
            }
        } else {
            $bundvalues[] = '0';
        }

        $varvalue   = array_sum($varvalues);
        $bundvalue  = array_sum($bundvalues);

        $subtotal = $varvalue + $bundvalue;

        // ===================== Member Discount GConfig =============================== //
        if ($input['customerid'] != '0') {
            $memberid = $input['customerid'];
            if ($this->data['gconfig']['memberdisctype'] === '0') {
                $memberdisc = $this->data['gconfig']['memberdisc'];
            } elseif ($this->data['gconfig']['memberdisctype'] === '1') {
                $memberdisc = ((int)$this->data['gconfig']['memberdisc'] / 100) * (int)$subtotal;
            }
        } else {
            $memberid = '';
            $memberdisc = 0;
        }

        if ((!empty($input['discvalue'])) && ($input['disctype'] === '0')) {
            $discount = $input['discvalue'];
        } elseif ((!empty($input['discvalue'])) && ($input['disctype'] === '1')) {
            $discount = ((int)$input['discvalue'] / 100) * (int)$subtotal;
        } elseif (empty($input['discvalue'])) {
            $discount   = 0;
        }

        if (!empty($input['poin'])) {
            $poin = $input['poin'];
        } else {
            $poin = 0;
        }

        // When Discount Member In Transaction and not dicount member per item
        $value = (int)$subtotal - (int)$memberdisc - (int)$discount - (int)$poin;

        // Single Payment
        if (!empty($input['payment']) && empty($input['duedate'])) {
            $trx = [
                'outletid'      => $this->data['outletPick'],
                'userid'        => $this->data['uid'],
                'memberid'      => $memberid,
                'paymentid'     => $input['payment'],
                'value'         => $value,
                'disctype'      => $input['disctype'],
                'memberdisc'    => $memberdisc,
                'discvalue'     => $discount,
                'date'          => $date,
                'pointused'     => $poin,
                'amountpaid'    => $input['value'],
                'photo'         => $fileName,
            ];
            $TransactionModel->insert($trx);
        }
        // Splitbill Payment
        elseif (!empty($input['firstpayment']) && !empty($input['secpayment']) && empty($input['duedate'])) {
            $trx = [
                'outletid'      => $this->data['outletPick'],
                'userid'        => $this->data['uid'],
                'memberid'      => $memberid,
                'paymentid'     => '0',
                'value'         => $value,
                'disctype'      => $input['disctype'],
                'memberdisc'    => $memberdisc,
                'discvalue'     => $discount,
                'date'          => $date,
                'pointused'     => $poin,
                'amountpaid'    => (int)$input['firstpay'] + (int)$input['secondpay'],
                'photo'         => $fileName,
            ];
            $TransactionModel->insert($trx);
        }
        // Debt
        elseif (!empty($input['duedate'])) {
            $trx = [
                'outletid'      => $this->data['outletPick'],
                'userid'        => $this->data['uid'],
                'memberid'      => $memberid,
                'paymentid'     => '0',
                'value'         => $value,
                'disctype'      => $input['disctype'],
                'memberdisc'    => $memberdisc,
                'discvalue'     => $discount,
                'date'          => $date,
                'pointused'     => $poin,
                'amountpaid'    => $input['value'],
                'photo'         => $fileName,
            ];
            $TransactionModel->insert($trx);
        }
        $trxId = $TransactionModel->getInsertID();

        // Transaction Detail & Stock
        if (!empty($input['qty'])) {
            foreach ($input['qty'] as $varid => $varqty) {
                $variant        = $VariantModel->find($varid);
                $discvar        = (int)$input['varprice'][$varid] * $varqty;
                
                if (!empty($input['varprice'][$varid])) {
                    $varPrices  = (((int)$varqty * ((int)$variant['hargamodal'] + (int)$variant['hargajual'])) - (int)$discvar) / (int)$varqty;
                } else {
                    $varPrices  = ((int)$varqty * ((int)$variant['hargamodal'] + (int)$variant['hargajual'])) / (int)$varqty;
                }

                // $discvar        = (int)$input['varprice'][$varid] * $varqty;
                // $discbargain    = (int)$input['varbargain'][$varid] * $varqty;

                // // Bargain And Varprice Added
                // if (!empty($input['varprice'][$varid]) && !empty($input['varbargain'][$varid]) && $discbargain !== 0) {
                //     $varPrices  = ((int)$discbargain - (int)$discvar) / (int)$varqty;
                //     // Vaprice Added And Null Bargain
                // } elseif (isset($input['varprice'][$varid]) && !isset($input['varbargain'][$varid]) || $discbargain === 0) {
                //     $varPrices  = (((int)$varqty * ((int)$variant['hargamodal'] + (int)$variant['hargajual'])) - (int)$discvar) / (int)$varqty;
                //     // Bargain Added And Null Varprice
                // } elseif ((empty($input['varprice'][$varid])) && (isset($input['varbargain'][$varid])) && ($discbargain !== 0)) {
                //     $varPrices  = (int)$discbargain / (int)$varqty;
                //     // Null Bargain & Varprice
                // } elseif (empty($input['varprice'][$varid]) && empty($input['varbargain'][$varid])) {
                //     $varPrices = ((int)$varqty * ((int)$variant['hargamodal'] + (int)$variant['hargajual'])) / (int)$varqty;
                // } else {
                //     $varPrices = 0;
                // }

                if (($this->data['gconfig']['globaldisc'] != '0') || ($this->data['gconfig']['globaldisc'] != null)) {
                    if ($this->data['gconfig']['globaldisctype'] === '0') {
                        $globaldisc = (Int)$this->data['gconfig']['globaldisc'] * (int)$varqty;
                    } elseif ($this->data['gconfig']['globaldisctype'] === '1') {
                        $globaldisc = (((int)$this->data['gconfig']['globaldisc'] / 100) * (int)$varPrices) * (int)$varqty;
                    }
                } else {
                    $globaldisc = 0;
                }

                $varPrice       = (Int)$varPrices - ((Int)$globaldisc / (Int)$varqty);
                $marginmodal    = (int)$varPrice - (int)$variant['hargamodal'];
                $margindasar    = (int)$varPrice - (int)$variant['hargadasar'];

                $trxvar = [
                    'transactionid' => $trxId,
                    'variantid'     => $varid,
                    'qty'           => $varqty,
                    'value'         => $varPrice,
                    'discvar'       => $discvar,
                    'globaldisc'    => $globaldisc,
                    'margindasar'   => $margindasar,
                    'marginmodal'   => $marginmodal,
                ];
                $TrxdetailModel->save($trxvar);

                $stock = $StockModel->where('outletid', $this->data['outletPick'])->where('variantid', $varid)->first();
                $saleVarStock = [
                    'id'        => $stock['id'],
                    'sale'      => $date,
                    'qty'       => (int)$stock['qty'] - (int)$varqty
                ];
                $StockModel->save($saleVarStock);
            }
        }

        if (!empty($input['bqty'])) {
            foreach ($input['bqty'] as $bunid => $bunqty) {
                $bundle         = $BundleModel->find($bunid);
                $bundleprice    = (int)$bundle['price'];

                // When member discount applied per item
                if (($this->data['gconfig']['globaldisc'] != '0') || ($this->data['gconfig']['globaldisc'] != null)) {
                    if ($this->data['gconfig']['globaldisctype'] === '0') {
                        $globaldisc = (Int)$this->data['gconfig']['globaldisc'] * (int)$bunqty;
                    } elseif ($this->data['gconfig']['globaldisctype'] === '1') {
                        $globaldisc = (((int)$this->data['gconfig']['globaldisc'] / 100) * (int)$bundleprice) * (int)$bunqty;
                    }
                } else {
                    $globaldisc = 0;
                }

                $bundlefinprice = (Int)$bundleprice - ((Int)$globaldisc / (int)$bunqty);

                $trxbun = [
                    'transactionid' => $trxId,
                    'bundleid'      => $bunid,
                    'qty'           => $bunqty,
                    'globaldisc'    => $globaldisc,
                    'value'         => $bundlefinprice
                ];
                $TrxdetailModel->save($trxbun);

                $bundledetail   = $BundledetModel->where('bundleid', $bunid)->find();
                foreach ($bundledetail as $BundleDetail) {
                    $bunstock = $StockModel->where('outletid', $this->data['outletPick'])->where('variantid', $BundleDetail['variantid'])->first();
                    $saleBunStock = [
                        'id'        => $bunstock['id'],
                        'sale'      => $date,
                        'qty'       => (int)$bunstock['qty'] - (int)$bunqty
                    ];
                    $StockModel->save($saleBunStock);
                }
            }
        }

        // Poin Minus
        $cust       = $MemberModel->find($input['customerid']);
        if (!empty($cust)) {
            $point      = [
                'id'    => $cust['id'],
                'poin'  => (int)$cust['poin'] - (int)$poin,
            ];
            $MemberModel->save($point);
        }

        // PPN Value
        $ppn = (int)$value * ((int)$Gconfig['ppn'] / 100);

        // Insert Trx Payment 
        $total = (int)$subtotal - (int)$discount - (int)$input['poin'] - (int)$memberdisc + (int)$ppn;

        // if (($totalvalue == '0') && ($input['payment'] == '-1')) {
        //     $total  = $input['poin'];
        // } else {
        //     $total  = $totalvalue;
        // }

        // Normal Transaction
        if (empty($input['firstpayment']) && empty($input['secpayment']) && isset($input['payment']) && empty($input['duedate'])) {
            $paymethod = [
                'paymentid'     => $input['payment'],
                'transactionid' => $trxId,
                'value'         => $total,
            ];
            $TrxpaymentModel->insert($paymethod);

            // Save Cash
            $payment    = $PaymentModel->find($input['payment']);
            if (!empty($payment)) {
                $cashPlus   = $CashModel->find($payment['cashid']);
    
                $cash = [
                    'id'    => $cashPlus['id'],
                    'qty'   => (int)$cashPlus['qty'] + (int)$total,
                ];
                $CashModel->save($cash);
            }
        }
        
        // Splitbill
        elseif (!empty($input['firstpayment']) && !empty($input['secpayment']) && empty($input['duedate'])) {
            // Insert Transaction First Payment
            $splitpayment1  = $PaymentModel->find($input['firstpayment']);
            if (!empty($splitpayment1)) {
                $paymet = [
                    'paymentid'     => $input['firstpayment'],
                    'transactionid' => $trxId,
                    'value'         => $input['firstpay'],
                ];
                $TrxpaymentModel->insert($paymet);
                
                // Save Cash First Payment
                $cashPlus       = $CashModel->find($splitpayment1['cashid']);
                $cashUp         = (int)$cashPlus['qty'] + (int)$input['firstpay'];
                $cash           = [
                    'id'    => $cashPlus['id'],
                    'qty'   => $cashUp
                ];
                $CashModel->save($cash);
            }

            // Insert Transaction Second Payment
            $splitpayment2  = $PaymentModel->find($input['secpayment']);
            if (!empty($splitpayment2)) {
                $pay = [
                    'paymentid'     => $input['secpayment'],
                    'transactionid' => $trxId,
                    'value'         => $input['secondpay'],
                ];
                $TrxpaymentModel->insert($pay);
    
                // Save Cash Second Payment
                $cashPlus2      = $CashModel->find($splitpayment2['cashid']);
                $cashUp2        = (int)$cashPlus2['qty'] + (int)$input['secondpay'];
                $cash2          = [
                    'id'    => $cashPlus2['id'],
                    'qty'   => $cashUp2,
                ];
                $CashModel->save($cash2);
            }
        }

        // Single Debt Method
        elseif (empty($input['firstpayment']) && empty($input['secpayment']) && $input['value'] == '0' && !empty($input['duedate'])) {
            // Insert Debt
            $debt = [
                'memberid'      => $input['customerid'],
                'transactionid' => $trxId,
                'value'         => $input['debt'],
                'deadline'      => $input['duedate'],
            ];
            $DebtModel->insert($debt);

            // Debt Payment Method
            $paymentmethod = [
                'paymentid'     => '0',
                'transactionid' => $trxId,
                'value'         => $total,
            ];
            $TrxpaymentModel->insert($paymentmethod);
        }
        
        // Debt With Down Payment
        elseif (empty($input['firstpayment']) && empty($input['secpayment']) && $input['value'] != '0' && !empty($input['duedate'])) {
            // Insert Debt
            $debt = [
                'memberid'      => $input['customerid'],
                'transactionid' => $trxId,
                'value'         => $input['debt'],
                'deadline'      => $input['duedate'],
            ];
            $DebtModel->insert($debt);

            // Debt With Down Payment Method
            $debtmethod = [
                'paymentid'     => '0',
                'transactionid' => $trxId,
                'value'         => $input['debt'],
            ];
            $TrxpaymentModel->insert($debtmethod);

            $payment    = $PaymentModel->find($input['payment']);
            if (!empty($payment)) {
                $paymentmethod = [
                    'paymentid'     => $input['payment'],
                    'transactionid' => $trxId,
                    'value'         => $input['value'],
                ];
                $TrxpaymentModel->insert($paymentmethod);
    
                // Insert Cash
                $cashPlus   = $CashModel->find($payment['cashid']);
                $cash = [
                    'id'    => $cashPlus['id'],
                    'qty'   => (int)$cashPlus['qty'] + (int)$total,
                ];
                $CashModel->save($cash);
            }
        }
        
        // Debt With Splitbill Payment
        elseif (isset($input['firstpayment']) && isset($input['secpayment']) && isset($input['duedate'])) {
            // Insert Debt
            $debt = [
                'memberid'      => $input['customerid'],
                'transactionid' => $trxId,
                'value'         => $input['debt'],
                'deadline'      => $input['duedate'],
            ];
            $DebtModel->insert($debt);
            
            // Insert Debt Payment Method
            $paymentmethod = [
                'paymentid'     => "0",
                'transactionid' => $trxId,
                'value'         => ((int)$total - ((int)$input['firstpay'] + (int)$input['secondpay'])),
            ];
            $TrxpaymentModel->insert($paymentmethod);

            // Insert Debt First Payment
            $splitpayment1  = $PaymentModel->find($input['firstpayment']);
            if (!empty($splitpayment1)) {
                $paymet = [
                    'paymentid'     => $input['firstpayment'],
                    'transactionid' => $trxId,
                    'value'         => $input['firstpay'],
                ];
                $TrxpaymentModel->insert($paymet);
    
                // Save Cash Debt First Payment
                $cashPlus       = $CashModel->find($splitpayment1['cashid']);
                $cashUp         = (int)$cashPlus['qty'] + (int)$input['firstpay'];
                $cash           = [
                    'id'    => $cashPlus['id'],
                    'qty'   => $cashUp
                ];
                $CashModel->save($cash);
            }

            // Insert Debt Second Payment
            $splitpayment2  = $PaymentModel->find($input['secpayment']);
            if (!empty($splitpayment2)) {
                $pay = [
                    'paymentid'     => $input['secpayment'],
                    'transactionid' => $trxId,
                    'value'         => $input['secondpay'],
                ];
                $TrxpaymentModel->insert($pay);
    
                // Save Cash Debt Second Payment
                $cashPlus2      = $CashModel->find($splitpayment2['cashid']);
                $cashUp2        = (int)$cashPlus2['qty'] + (int)$input['secondpay'];
                $cash2          = [
                    'id'    => $cashPlus2['id'],
                    'qty'   => $cashUp2,
                ];
                $CashModel->save($cash2);
            }
        }

        // Gconfig poin setup
        $minimTrx    = $Gconfig['poinorder'];
        $poinval     = $Gconfig['poinvalue'];

        // Update Point Member
        $member         = $MemberModel->find($input['customerid']);
        if (!empty($member)) {
            if ($total >= $minimTrx) {
                if ($minimTrx != "0") {
                    $value      = (int)$total / (int)$minimTrx;
                } else {
                    $value      = 0;
                }
                $result         = floor($value);
                $poinresult     = (int)$result * (int)$poinval;
            } else {
                $poinresult = 0;
            }

            $trx            = $member['trx'] + 1;
            $memberPoint    = $member['poin'];
            $poinPlus       = (int)$memberPoint + (int)$poinresult;
            $pointvalue = [
                'id'    => $member['id'],
                'poin'  => $poinPlus,
                'trx'   => $trx,
            ];
            $MemberModel->save($pointvalue);
        }

        // Print Function
        $db                 = \Config\Database::connect();
        $bundles            = $BundleModel->findAll();
        $bundets            = $BundledetModel->findAll();
        $Cash               = $CashModel->findAll();
        $outlets            = $OutletModel->findAll();
        $customers          = $MemberModel->findAll();
        $payments           = $PaymentModel->findAll();
        $products           = $ProductModel->findAll();
        $variants           = $VariantModel->findAll();
        $stocks             = $StockModel->findAll();
        $transactions       = $TransactionModel->find($trxId);
        $trxpayments        = $TrxpaymentModel->where('transactionid', $trxId)->find();
        // $member             = $MemberModel->find($transactions['memberid']);
        // $debt               = $DebtModel->where('memberid', $transactions['memberid'])->first();
        $user               = $UserModel->where('id', $transactions['userid'])->first();

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
        $data['outid']          = $OutletModel->where('id', $this->data['outletPick'])->first();
        $data['bookings']       = $BookingModel->findAll();
        $data['bundleVariants'] = $bundleVariants->getResult();

        if (empty($input['phone'])) {
            $actual_link            = "https://$_SERVER[HTTP_HOST]/pay/copyprint/$trxId";
            $data['link']           =  urlencode($actual_link);
        } elseif (!empty($input['phone'])) {
            $actual_link            = "https://$_SERVER[HTTP_HOST]/pay/copyprint/$trxId";
            $data['link']           = "https://wa.me/62" . $input['phone'] . "?text=" . urlencode($actual_link) . "";
        }

        // Gconfig poin setup
        $minimTrx    = $Gconfig['poinorder'];
        $poinval     = $Gconfig['poinvalue'];

        if (!empty($input['customerid'])) {

            if ($total >= $minimTrx) {
                if ($minimTrx != "0") {
                    $value          = (int)$total / (int)$minimTrx;
                } else {
                    $value          = 0;
                }
                $result             = floor($value);
                $poinresult         = (int)$result * (int)$poinval;
            } else {
                $poinresult = "0";
            }

            $data['cust']           = $MemberModel->where('id', $transactions['memberid'])->first();
            $data['mempoin']        = $member['poin'];
            $data['poinused']       = $input['poin'];
            $data['poinearn']       = $poinresult;
        } else {
            $data['cust']           = "0";
            $data['mempoin']        = "0";
            $data['poinused']       = "0";
            $data['poinearn']       = "0";
        }

        if (!empty($input['value'])) {
            $change             = (int)$input['value'] - (int)$total;

            if ($change > '0') {
                $data['change']     = $change;
            } else {
                $data['change']     = '0';
            }
        } else {
            $data['change']     = "0";
        }

        if (!empty($input['varprice'])) {
            $data['vardiscval']     = $input['varprice'];
        } else {
            $data['vardiscval']     = "0";
        }

        if (!empty($input['firstpay']) && (!empty($input['secondpay']))) {
            $data['pay']            = (int)$input['firstpay'] + (int)$input['secondpay'];
        } elseif (!empty($input['duedate']) && empty($input['value'])) {
            $data['pay']            = "0";
        } else {
            $data['pay']            = $input['value'];
        }

        // if (!empty($input['value'])) {
        //     $data['pay']            = $input['value'];
        // } elseif (!empty($input['firstpay']) && (!empty($input['secondpay']))) {
        //     $data['pay']            = (int)$input['firstpay'] + (int)$input['secondpay'];
        // } elseif (!empty($input['duedate']) && empty($input['value'])) {
        //     $data['pay']            = "0";
        // }

        $data['discount'] = "0";

        if ((!empty($input['discvalue'])) && ($input['disctype'] === '0')) {
            $data['discount'] += $input['discvalue'];
        } elseif ((isset($input['discvalue'])) && ($input['disctype'] === '1')) {
            $data['discount'] += ((int)$input['discvalue'] / 100) * (int)$subtotal;
        } else {
            $data['discount'] += 0;
        }

        if (!empty($input['debt'])) {
            $data['debt']       = $input['debt'];
            // $data['totaldebt']  = $member['kasbon'];
        } else {
            $data['debt']       = "0";
            // $data['totaldebt']  = "0";
        }

        $data['user']           = $user->username;
        $data['date']           = $transactions['date'];
        $data['transactionid']  = $trxId;
        $data['subtotal']       = $subtotal;
        // $data['members']        = $MemberModel->findAll();
        $data['total']          = $total;

        if ($this->data['uid'] != null) {
            $uid    = $this->data['uid'];
        } else {
            $uid    = '0';
        }
        $data['logedin']        = $UserModel->find($uid);

        if (!empty($input['customerid'])) {
            $memberdata         = $MemberModel->find($transactions['memberid']);
            $memberphone        = $memberdata['phone'];

            // Create the WhatsApp link
            $whatsappLink = "https://wa.me/+62{$memberphone}?text=Terimakasih%20telah%20berbelanja%20di%2058%20Vapehouse%2C%20untuk%20detail%20struk%20pembelian%20bisa%20cek%20link%20dibawah%20lur.%20%E2%9C%A8%E2%9C%A8%0A%0A$actual_link%0A%0AJika%20menemukan%20kendala%2C%20kerusakan%20produk%2C%20atau%20ingin%20memberi%20kritik%20%26%20saran%20hubungu%2058%20Customer%20Solution%20kami%20di%20wa.me%2F6288983741558%20";
            
            // Redirect to the WhatsApp link
            // return redirect()->to('');
            return redirect()->to($whatsappLink);
        }

        return redirect()->to('');
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

        if (!empty($input['qty'])) {
            foreach ($input['qty'] as $varid => $varqty) {
                $variant = $VariantModel->find($varid);
                $discvar = (int)$input['varprice'][$varid] * (int)$varqty;

                if (!empty($input['varprice'][$varid])) {
                    $varval    = ((int)$varqty * ((int)$variant['hargamodal'] + (int)$variant['hargajual'])) - (int)$discvar;
                } else {
                    $varval    = (int)$varqty * ((int)$variant['hargamodal'] + (int)$variant['hargajual']);
                }

                if (($this->data['gconfig']['globaldisc'] != '0') || ($this->data['gconfig']['globaldisc'] != null)) {
                    if ($this->data['gconfig']['globaldisctype'] === '0') {
                        $globaldisc = (Int)$this->data['gconfig']['globaldisc'] * (int)$varqty;
                    } elseif ($this->data['gconfig']['globaldisctype'] === '1') {
                        $globaldisc = (((int)$this->data['gconfig']['globaldisc'] / 100) * (int)$varval) * (int)$varqty;
                    }
                } else {
                    $globaldisc = 0;
                }

                $varvalues[]    = (Int)$varval - (Int)$globaldisc;
            }
        } else {
            $varvalues[] = '0';
        }

        if (!empty($input['bqty'])) {
            foreach ($input['bqty'] as $bunid => $bundqty) {
                $bundle         = $BundleModel->find($bunid);
                
                $bundleval      = (Int)$bundqty * (Int)$bundle['price'];

                if (($this->data['gconfig']['globaldisc'] != '0') || ($this->data['gconfig']['globaldisc'] != null)) {
                    if ($this->data['gconfig']['globaldisctype'] === '0') {
                        $globaldisc = (Int)$this->data['gconfig']['globaldisc'] * (int)$bundqty;
                    } elseif ($this->data['gconfig']['globaldisctype'] === '1') {
                        $globaldisc = (((int)$this->data['gconfig']['globaldisc'] / 100) * (int)$bundleval) * (int)$bundqty;
                    }
                } else {
                    $globaldisc = 0;
                }

                $bundvalues[]    = (Int)$bundleval - (Int)$globaldisc;
            }
        } else {
            $bundvalues[] = '0';
        }

        $varvalue = array_sum($varvalues);
        $bundvalue = array_sum($bundvalues);

        $subtotal = $varvalue + $bundvalue;

        // ===================== Member Discount GConfig =============================== //
        if ($input['customerid'] != '0') {
            $memberid = $input['customerid'];
            if ($this->data['gconfig']['memberdisctype'] === '0') {
                $memberdisc = $this->data['gconfig']['memberdisc'];
            } elseif ($this->data['gconfig']['memberdisctype'] === '1') {
                $memberdisc = ((int)$this->data['gconfig']['memberdisc'] / 100) * (int)$subtotal;
            }
        } else {
            $memberid = '';
            $memberdisc = 0;
        }

        if ((!empty($input['discvalue'])) && ($input['disctype'] === '0')) {
            $discount = $input['discvalue'];
        } elseif ((!empty($input['discvalue'])) && ($input['disctype'] === '1')) {
            $discount = ((int)$input['discvalue'] / 100) * (int)$subtotal;
        } else {
            $discount = 0;
        }

        if (!empty($input['poin'])) {
            $poin = $input['poin'];
        } else {
            $poin = 0;
        }

        $value = (int)$subtotal - (int)$memberdisc - (int)$discount - (int)$poin;
        // foreach ($input['varprice'] as $variantprice) {
        //     $varprice = $variantprice;
        // }

        // foreach ($input['varbargain'] as $bargain) {
        //     $varbargain = $bargain;
        // }
        $book = [
            'outletid'      => $this->data['outletPick'],
            'userid'        => $this->data['uid'],
            'memberid'      => $memberid,
            'memberdisc'    => $memberdisc,
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
                $discvar = (int)$input['varprice'][$varid] * (int)$varqty;

                if (!empty($input['varprice'][$varid])) {
                    $varPrices  = (((int)$varqty * ((int)$variant['hargamodal'] + (int)$variant['hargajual'])) - (int)$discvar) / (int)$varqty;
                } else {
                    $varPrices  = ((int)$varqty * ((int)$variant['hargamodal'] + (int)$variant['hargajual'])) / (int)$varqty;
                }

                if (($this->data['gconfig']['globaldisc'] != '0') || ($this->data['gconfig']['globaldisc'] != null)) {
                    if ($this->data['gconfig']['globaldisctype'] === '0') {
                        $globaldisc = (Int)$this->data['gconfig']['globaldisc'] * (int)$varqty;
                    } elseif ($this->data['gconfig']['globaldisctype'] === '1') {
                        $globaldisc = (((int)$this->data['gconfig']['globaldisc'] / 100) * (int)$varPrices) * (int)$varqty;
                    }
                } else {
                    $globaldisc = 0;
                }

                $varPrice   = (Int)$varPrices - (Int)$globaldisc;
                
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
                    'qty'       => (int)$stock['qty'] - (int)$varqty
                ];
                $StockModel->save($saleVarStock);
            }
        }

        if (!empty($input['bqty'])) {
            foreach ($input['bqty'] as $bunid => $bunqty) {
                $bundle         = $BundleModel->find($bunid);
                $bundleprice    = (int)$bundle['price'] * (int)$bunqty;

                // When member discount applied per item
                if (($this->data['gconfig']['globaldisc'] != '0') || ($this->data['gconfig']['globaldisc'] != null)) {
                    if ($this->data['gconfig']['globaldisctype'] === '0') {
                        $globaldisc = (Int)$this->data['gconfig']['globaldisc'] * (int)$bunqty;
                    } elseif ($this->data['gconfig']['globaldisctype'] === '1') {
                        $globaldisc = (((int)$this->data['gconfig']['globaldisc'] / 100) * (int)$bundleprice) * (int)$bunqty;
                    }
                } else {
                    $globaldisc = 0;
                }

                $bundlefinprice = (Int)$bundleprice - (Int)$globaldisc;

                $trxbun = [
                    'bookingid'     => $bookId,
                    'bundleid'      => $bunid,
                    'qty'           => $bunqty,
                    'value'         => $bundlefinprice
                ];
                $BookingdetailModel->save($trxbun);

                $bundledetail = $BundledetModel->where('bundleid', $bunid)->find();
                foreach ($bundledetail as $BundleDetail) {
                    $bunstock = $StockModel->where('outletid', $this->data['outletPick'])->where('variantid', $BundleDetail['variantid'])->first();
                    $saleBunStock = [
                        'id'        => $bunstock['id'],
                        'sale'      => $date,
                        'qty'       => (int)$bunstock['qty'] - (int)$bunqty
                    ];
                    $StockModel->save($saleBunStock);
                }
            }
        }

        // PPN Value
        $ppn = (int)$value * ((int)$Gconfig['ppn'] / 100);

        //Insert Trx Payment 
        $total = (int)$subtotal - (int)$discount - (int)$input['poin'] - (int)$memberdisc + (int)$ppn;

        $db                 = \Config\Database::connect();
        $bundles            = $BundleModel->findAll();
        $bundets            = $BundledetModel->findAll();
        $booking            = $BookingModel->find($bookId);
        $bookingdetails     = $BookingdetailModel->where('bookingid', $bookId)->find();
        $Cash               = $CashModel->findAll();
        $outlets            = $OutletModel->findAll();
        $customers          = $MemberModel->findAll();
        $payments           = $PaymentModel->findAll();
        $products           = $ProductModel->findAll();
        $variants           = $VariantModel->findAll();
        $stocks             = $StockModel->findAll();
        $member             = $MemberModel->where('id', $booking['memberid'])->first();
        $user               = $UserModel->where('id', $booking['userid'])->first();

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
        $data['outid']          = $OutletModel->where('id', $this->data['outletPick'])->first();
        $data['bookings']       = $booking;
        $data['bookingdetails'] = $bookingdetails;
        $data['bundleVariants'] = $bundleVariants->getResult();

        if (!empty($input['customerid'])) {
            $data['cust']           = $MemberModel->where('id', $booking['memberid'])->first();
            $data['mempoin']        = $member['poin'];
            $data['poinused']       = $input['poin'];
        } else {
            $data['cust']           = "0";
            $data['mempoin']        = "0";
            $data['poinused']       = "0";
        }

        if (!empty($input['value'])) {
            $change             = (int)$input['value'] - (int)$total;

            if ($change > '0') {
                $data['change']     = $change;
            } else {
                $data['change']     = '0';
            }
        } else {
            $data['change']     = "0";
        }


        if (!empty($input['value'])) {
            $data['pay']            = "UNPAID";
        } elseif (!empty($input['firstpay']) && (!empty($input['secondpay']))) {
            $data['pay']            = (int)$input['firstpay'] + (int)$input['secondpay'];
        }

        $data['discount'] = "0";

        if ((!empty($input['discvalue'])) && ($input['disctype'] === '0')) {
            $data['discount'] += $input['discvalue'];
        } elseif ((isset($input['discvalue'])) && ($input['disctype'] === '1')) {
            $data['discount'] += ((int)$input['discvalue'] / 100) * (int)$subtotal;
        } else {
            $data['discount'] += 0;
        }

        if (!empty($input['debt'])) {
            $data['debt']       = $input['debt'];
            $data['totaldebt']  = $member['kasbon'];
        } else {
            $data['debt']       = "0";
            $data['totaldebt']  = "0";
        }

        if (!empty($value)) {
            $data['total']          = $value;
        } else {
            $data['total']          = "0";
        }

        // Gconfig poin setup
        $minimTrx    = $Gconfig['poinorder'];
        $poinval     = $Gconfig['poinvalue'];

        if (($minimTrx != "0") && ($value >= $minimTrx)) {
            $subval  = (int)$value / (int)$minimTrx;
            $result = floor($subval);
            $poin   = (int)$result * (int)$poinval;
        }

        if (!empty($booking['memberid'])) {
            $data['cust']           = $MemberModel->where('id', $booking['memberid'])->first();
            $data['mempoin']        = $member['poin'];
            $data['poinearn']       = $poin;
        } else {
            $data['cust']           = "0";
            $data['mempoin']        = "0";
            $data['poinearn']       = "0";
        }

        $data['user']           = $user->username;
        $data['date']           = $booking['created_at'];
        $data['bookingid']      = $booking['id'];
        $data['subtotal']       = $subtotal;
        $data['member']         = $MemberModel->where('id', $booking['memberid'])->first();

        return view('Views/print', $data);
    }

    public function copyprint($id)
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
        // $MemberModel            = new MemberModel();
        // $GconfigModel           = new GconfigModel();

        $db                 = \Config\Database::connect();
        $transactions       = $TransactionModel->find($id);
        $trxdetails         = $TrxdetailModel->where('transactionid', $id)->find();
        $trxpayments        = $TrxpaymentModel->where('transactionid', $id)->find();
        $bundles            = $BundleModel->findAll();
        $bundets            = $BundledetModel->where('id', $id)->find();
        $Cash               = $CashModel->findAll();
        $outlets            = $OutletModel->findAll();
        $users              = $UserModel->findAll();
        $customers          = $MemberModel->findAll();
        $payments           = $PaymentModel->findAll();
        $products           = $ProductModel->findAll();
        $variants           = $VariantModel->findAll();
        $stocks             = $StockModel->findAll();
        $member             = $MemberModel->where('id', $transactions['memberid'])->first();
        $debt               = $DebtModel->where('transactionid', $id)->find();
        $user               = $UserModel->where('id', $transactions['userid'])->first();
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
        $data['trxdetails']     = $TrxdetailModel->where('transactionid', $id)->find();
        $data['trxpayments']    = $trxpayments;
        $data['outid']          = $OutletModel->where('id', $this->data['outletPick'])->first();
        $data['bookings']       = $BookingModel->findAll();
        $data['bundleVariants'] = $bundleVariants->getResult();

        $actual_link            = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $data['links']          =  urlencode($actual_link);

        $data['discount'] = "0";
        if ((!empty($transactions['discvalue'])) && ($transactions['disctype'] === '0')) {
            $data['discount'] += $transactions['discvalue'];
        } elseif ((isset($transactions['discvalue'])) && ($transactions['disctype'] === '1')) {
            foreach ($trxdetails as $trxdetail) {
                if ($trxdetail['transactionid'] === $transactions['id']) {
                    $sub =  ((int)$trxdetail['value'] * (int)$trxdetail['qty']);
                    $data['discount'] += (int)$sub * ((int)$transactions['discvalue'] / 100);
                }
            }
        } else {
            $data['discount'] += 0;
        }

        $prices = array();
        foreach ($trxdetails as $trxdet) {
            if ($trxdet['transactionid'] === $id) {
                $total      = (int)$trxdet['qty'] * (int)$trxdet['value'];
                $prices[]   = $total;
            }
        }
        $sum = array_sum($prices);

        $total = (int)$sum - (int)$data['discount'] - (int)$transactions['pointused'] - (int)$Gconfig['memberdisc'] + (int)$Gconfig['ppn'];

        // Gconfig poin setup
        $minimTrx       = $Gconfig['poinorder'];
        $poinval        = $Gconfig['poinvalue'];

        $poinresult = "";
        if ($total >= $minimTrx) {
            if ($minimTrx != "0") {
                $value          = (int)$total / (int)$minimTrx;
            } else {
                $value          = 0;
            }
            $result         = floor($value);
            $poinresult     = (int)$result * (int)$poinval;
        }

        if (!empty($transactions['memberid'])) {
            $data['cust']           = $MemberModel->where('id', $transactions['memberid'])->first();
            $data['mempoin']        = $member['poin'];
            if (empty($poinresult)) {
                $data['poinearn']   = "0";
            } else {
                $data['poinearn']       = $poinresult;
            }
        } else {
            $data['cust']           = "0";
            $data['mempoin']        = "0";
            $data['poinearn']       = "0";
        }

        if (!empty($transactions['pointused'])) {
            $data['poinused']       = $transactions['pointused'];
        } else {
            $data['poinused']       = "0";
        }

        foreach ($trxdetails as $trxdetail) {
            $trxdetval = $trxdetail['value'];
        }

        if (!empty($transactions['amountpaid'])) {
            $change             = (int)$transactions['amountpaid'] - (int)$transactions['value'];

            if ($change > '0') {
                $data['change']     = $change;
            } else {
                $data['change']     = '0';
            }
        } else {
            $data['change']     = "0";
        }

        if (!empty($trxdetails['discvar'])) {
            $data['vardiscval']     = $trxdetails['discvar']['variantid'];
        } else {
            $data['vardiscval']     = "0";
        }

        if (!empty($transactions['amountpaid'])) {
            $data['pay']            = $transactions['amountpaid'];
        } elseif (empty($transactions['amountpaid'])) {
            foreach ($trxdetails as $trxdetail) {
                if ($trxdetail['transactionid'] == $id) {
                    $data['pay']    = $trxdetail['value'];
                }
            }
        } else {
            $data['pay']            = '0';
        }

        if (!empty($debt)) {
            foreach ($debt as $deb) {
                $data['debt']       = $deb['value'];
                $data['totaldebt']  = $deb['value'];
            }
        } else {
            $data['debt']       = "0";
            $data['totaldebt']  = "0";
        }

        $actual_link            = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $data['link']           =  urlencode($actual_link);

        $data['user']           = $user->username;
        $data['date']           = $transactions['date'];
        $data['transactionid']  = $id;
        $data['subtotal']       = $sum;
        $data['members']        = $MemberModel->findall();
        $data['total']          = $transactions['value'];

        if ($this->data['uid'] != null) {
            $uid    = $this->data['uid'];
        } else {
            $uid    = '0';
        }
        $data['logedin']        = $UserModel->find($uid);

        return view('Views/print', $data);
    }

    public function bookprint($id)
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

        $db                 = \Config\Database::connect();
        $bundles            = $BundleModel->findAll();
        $bundets            = $BundledetModel->findAll();
        $booking            = $BookingModel->find($id);
        $bookingdetails     = $BookingdetailModel->where('bookingid', $id)->find();
        $Cash               = $CashModel->findAll();
        $outlets            = $OutletModel->findAll();
        $users              = $UserModel->findAll();
        $customers          = $MemberModel->findAll();
        $payments           = $PaymentModel->findAll();
        $products           = $ProductModel->findAll();
        $variants           = $VariantModel->findAll();
        $stocks             = $StockModel->findAll();
        $member             = $MemberModel->where('id', $booking['memberid'])->first();
        $debt               = $DebtModel->where('memberid', $booking['memberid'])->first();
        $user               = $UserModel->where('id', $booking['userid'])->first();
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
        $data['outlets']        = $outlets;
        $data['payments']       = $payments;
        $data['customers']      = $customers;
        $data['products']       = $products;
        $data['variants']       = $variants;
        $data['stocks']         = $stocks;
        $data['trxdetails']     = $TrxdetailModel->findAll();
        $data['outid']          = $OutletModel->where('id', $this->data['outletPick'])->first();
        $data['bookings']       = $booking;
        $data['bookingdetails'] = $bookingdetails;
        $data['bundleVariants'] = $bundleVariants->getResult();

        $data['discount'] = "0";
        if ((!empty($booking['discvalue'])) && ($booking['disctype'] === '0')) {
            $data['discount'] += $booking['discvalue'];
        } elseif ((isset($booking['discvalue'])) && ($booking['disctype'] === '1')) {
            foreach ($bookingdetails as $trxdetail) {
                if ($trxdetail['bookingid'] === $booking['id']) {
                    $sub =  ((int)$trxdetail['value'] * (int)$trxdetail['qty']);
                    $data['discount'] += (int)$sub * ((int)$booking['discvalue'] / 100);
                }
            }
        } else {
            $data['discount'] += 0;
        }

        $prices = array();
        foreach ($bookingdetails as $trxdet) {
            if ($trxdet['bookingid'] === $id) {
                $total      = (int)$trxdet['qty'] * (int)$trxdet['value'];
                $prices[]   = $total;
            }

            if (!empty($trxdet['discvar']) && $trxdet['discvar'] !== "0") {
                $data['vardiscval']     = $trxdet['discvar'];
            } else {
                $data['vardiscval']     = "0";
            }
        }
        $sum = array_sum($prices);

        $total = (int)$sum - (int)$data['discount'] - (int)$Gconfig['memberdisc'] + (int)$Gconfig['ppn'];

        // Gconfig poin setup
        $minimTrx    = $Gconfig['poinorder'];
        $poinval     = $Gconfig['poinvalue'];

        if ($total >= $minimTrx) {
            if ($minimTrx != "0") {
                $value          = (int)$total / (int)$minimTrx;
            } else {
                $value          = 0;
            }
            $result         = floor($value);
            $poinresult     = (int)$result * (int)$poinval;
        }

        if (!empty($bookings['memberid'])) {
            $data['cust']           = $MemberModel->where('id', $booking['memberid'])->first();
            $data['mempoin']        = (int)$member['poin'];
            $data['poinearn']       = 'Tidak menggunakan Poin';
        } else {
            $data['cust']           = "0";
            $data['mempoin']        = "0";
            $data['poinearn']       = "0";
        }

        if (!empty($member)) {
            $data['cust']           = $MemberModel->where('id', $booking['memberid'])->first();
            $data['mempoin']        = (int)$member['poin'];
        } else {
            $data['cust']           = "0";
            $data['mempoin']        = "0";
            $data['poinused']       = "0";
        }

        if (!empty($booking['value']) && $booking['value'] != "0") {
            $data['change']     = (int)$booking['value'] - (int)$total;
        } else {
            $data['change']     = "0";
        }

        if ((!empty($booking['discvalue'])) && ($booking['disctype'] === '0')) {
            $data['discount']   = $booking['discvalue'];
            $data['memberdisc'] = $booking['discvalue'];
        } elseif ((!empty($booking['discvalue'])) && ($booking['disctype'] === '1')) {
            $data['discount']   = ((int)$booking['discvalue'] / 100) * (int)$bookingdetails['value'];
            $data['memberdisc'] = ((int)$booking['discvalue'] / 100) * (int)$bookingdetails['value'];
        } else {
            $data['discount'] = 0;
            $data['memberdisc'] = 0;
        }

        $data['debt']       = "0";
        $data['totaldebt']  = "0";

        if (!empty($bookingdetails)) {
            $data['total']          = $booking['value'];
        } else {
            $data['total']          = "0";
        }

        $sub = [];
        foreach ($bookingdetails as $bookingdetail) {
            $sub[] = (int)$bookingdetail['value'] + (int)$bookingdetail['discvar'];
        }
        $subtotal = array_sum($sub);

        $data['pay']            = "UNPAID";
        $data['user']           = $user->username;
        $data['date']           = $booking['created_at'];
        $data['bookingid']      = $booking['id'];
        $data['subtotal']       = $subtotal;
        $data['member']         = $MemberModel->where('id', $booking['memberid'])->first();

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
                    'qty'   => (int)$stock['qty'] + (int)$bookdet['qty'],
                ];
                $StockModel->save($stockdata);
            } else {
                $bundles = $BundledetailModel->where('bundleid', $bookdet['bundleid'])->find();
                foreach ($bundles as $bundle) {
                    $stock = $StockModel->where('outletid', $this->data['outletPick'])->where('variantid', $bundle['variantid'])->first();
                    $stockdata = [
                        'id'    => $stock['id'],
                        'qty'   => (int)$stock['qty'] + (int)$bookdet['qty'],
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
        $PaymentModel           = new PaymentModel;
        $OutletModel            = new OutletModel();

        // Get Data
        $cashinout              = $TrxotherModel->findAll();
        $input                  = $this->request->getPost();
        $payments               = $PaymentModel->where('id', $input['payment'])->first();
        $date                   = date_create();
        $tanggal                = date_format($date, 'Y-m-d H:i:s');
        $member                 = $MemberModel->where('id', $input['customerid'])->first();
        $cash                   = $CashModel->where('id', $payments['cashid'])->first();
        $outlet                 = $OutletModel->find($this->data['outletPick']);
        $pettycash              = $CashModel->where('name', 'Petty Cash ' . $outlet['name'])->first();

        // // Image Capture
        // $img                    = $input['image'];
        // $folderPath             = "img/tfproof/";
        // $image_parts            = explode(";base64,", $img);
        // $image_type_aux         = explode("image/", $image_parts[0]);
        // $image_type             = $image_type_aux[1];
        // $image_base64           = base64_decode($image_parts[1]);
        // $fileName               = uniqid() . '.png';
        // $file                   = $folderPath . $fileName;
        // file_put_contents($file, $image_base64);

        // Cash In 
        $cashin = [
            'userid'            => $this->data['uid'],
            'outletid'          => $this->data['outletPick'],
            'cashid'            => $payments['cashid'],
            'description'       => "Top Up - " . $member['name'] . "/" . $member['phone'],
            'type'              => "0",
            'date'              => $tanggal,
            'qty'               => $input['value'],
            // 'photo'             => $fileName,
        ];
        $TrxotherModel->save($cashin);

        // plus member poin
        $poin                   = (int)$member['poin'] + (int)$input['value'];
        $data = [
            'id'                => $input['customerid'],
            'poin'              => $poin,
        ];
        $MemberModel->save($data);

        $cas = (int)$cash['qty'] + (int)$input['value'];
        $wallet = [
            'id'                => $payments['cashid'],
            'qty'               => $cas,
        ];
        $CashModel->save($wallet);

        // // Find Data for Daily Report
        // $today                  = date('Y-m-d') . ' 00:00:01';
        // $dailyreports           = $DailyReportModel->where('outletid', $this->data['outletPick'])->where('dateopen >', $today)->find();
        // if ($payments['cashid'] === $pettycash) {
        //     foreach ($dailyreports as $dayrep) {
        //         $tcashin = [
        //             'id'            => $dayrep['id'],
        //             'totalcashin'   => (int)$dayrep['totalcashin'] + (int)$input['value'],
        //         ];
        //         $DailyReportModel->save($tcashin);
        //     }
        // }

        // return
        return redirect()->back()->with('message', lang('Global.saved'));
    }

    // public function invoice($id)
    // {
    //     // Calling Models
    //     $BundleModel            = new BundleModel();
    //     $BundledetModel         = new BundledetailModel();
    //     $CashModel              = new CashModel();
    //     $DebtModel              = new DebtModel();
    //     $GconfigModel           = new GconfigModel();
    //     $OutletModel            = new OutletModel();
    //     $UserModel              = new UserModel();
    //     $MemberModel            = new MemberModel();
    //     $PaymentModel           = new PaymentModel();
    //     $ProductModel           = new ProductModel();
    //     $VariantModel           = new VariantModel();
    //     $StockModel             = new StockModel();
    //     $BookingModel           = new BookingModel();
    //     $BookingdetailModel     = new BookingdetailModel();
    //     $TransactionModel       = new TransactionModel();
    //     $TrxdetailModel         = new TrxdetailModel();
    //     $TrxpaymentModel        = new TrxpaymentModel();
    //     $MemberModel            = new MemberModel();
    //     $GconfigModel           = new GconfigModel();

    //     $db                 = \Config\Database::connect();
    //     $transactions       = $TransactionModel->find($id);
    //     $trxdetails         = $TrxdetailModel->where('transactionid', $id)->find();
    //     $trxpayments        = $TrxpaymentModel->where('transactionid', $id)->find();
    //     $bundles            = $BundleModel->findAll();
    //     $bundets            = $BundledetModel->where('id', $id)->find();
    //     $Cash               = $CashModel->findAll();
    //     $outlets            = $OutletModel->findAll();
    //     $users              = $UserModel->findAll();
    //     $customers          = $MemberModel->findAll();
    //     $payments           = $PaymentModel->findAll();
    //     $products           = $ProductModel->findAll();
    //     $variants           = $VariantModel->findAll();
    //     $stocks             = $StockModel->findAll();
    //     $members            = $MemberModel->where('id', $transactions['memberid'])->first();
    //     $debt               = $DebtModel->where('transactionid', $id)->find();
    //     $user               = $UserModel->where('id', $transactions['userid'])->first();
    //     $Gconfig            = $GconfigModel->first();

    //     $bundleBuilder      = $db->table('bundledetail');
    //     $bundleVariants     = $bundleBuilder->select('bundledetail.bundleid as bundleid, variant.id as id, variant.productid as productid, variant.name as name, stock.outletid as outletid, stock.qty as qty');
    //     $bundleVariants     = $bundleBuilder->join('variant', 'bundledetail.variantid = variant.id', 'left');
    //     $bundleVariants     = $bundleBuilder->join('stock', 'stock.variantid = variant.id', 'left');
    //     $bundleVariants     = $bundleBuilder->orderBy('stock.qty', 'ASC');
    //     $bundleVariants     = $bundleBuilder->get();

    //     $actual_link            = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    //     $data                   = $this->data;

    //     $data['discount'] = "0";
    //     if ((!empty($transactions['discvalue'])) && ($transactions['disctype'] === '0')) {
    //         $data['discount'] += $transactions['discvalue'];
    //     } elseif ((isset($transactions['discvalue'])) && ($transactions['disctype'] === '1')) {
    //         foreach ($trxdetails as $trxdetail) {
    //             if ($trxdetail['transactionid'] === $transactions['id']) {
    //                 $sub =  ((int)$trxdetail['value'] * (int)$trxdetail['qty']);
    //                 $data['discount'] += (int)$sub * ((int)$transactions['discvalue'] / 100);
    //             }
    //         }
    //     } else {
    //         $data['discount'] += 0;
    //     }

    //     $prices = array();
    //     foreach ($trxdetails as $trxdet) {
    //         if ($trxdet['transactionid'] === $id) {
    //             $total = (int)$trxdet['qty'] * (int)$trxdet['value'];
    //             $prices[] = $total;
    //         }
    //     }
    //     $sum = array_sum($prices);

    //     $total = (int)$sum - (int)$data['discount'] - (int)$transactions['pointused'] - (int)$Gconfig['memberdisc'] + (int)$Gconfig['ppn'];

    //     // Gconfig poin setup
    //     $minimTrx       = $Gconfig['poinorder'];
    //     $poinval        = $Gconfig['poinvalue'];

    //     if ($total >= $minimTrx) {
    //         if ($minimTrx != "0") {
    //             $value      = (int)$total / (int)$minimTrx;
    //         } else {
    //             $value      = 0;
    //         }
    //         $result         = floor($value);
    //         $poinresult     = (int)$result * (int)$poinval;
    //     }

    //     if (!empty($transactions['memberid'])) {
    //         $data['cust']           = $MemberModel->where('id', $transactions['memberid'])->first();
    //         $data['mempoin']        = $members['poin'];
    //         $data['poinearn']       = $poinresult;
    //     } else {
    //         $data['cust']           = "0";
    //         $data['mempoin']        = "0";
    //         $data['poinearn']       = "0";
    //     }


    //     if (!empty($transactions['pointused'])) {
    //         $data['poinused']       = $transactions['pointused'];
    //     } else {
    //         $data['poinused']       = "0";
    //     }

    //     $sub = [];
    //     foreach ($trxdetails as $trxdetail) {
    //         $trxdetval  = $trxdetail['value'];
    //         $sub[]     = (int)$trxdetail['value'] + (int)$trxdetail['discvar'] * (int)$trxdetail['qty'];
    //     }
    //     $subtotal = (array_sum($sub));

    //     if (!empty($transactions['amountpaid'])) {
    //         $data['change']     = (int)$transactions['amountpaid'] - (int)$transactions['value'];
    //     } else {
    //         $data['change']     = "0";
    //     }

    //     if (!empty($trxdetails['discvar'])) {
    //         $data['vardiscval']     = $trxdetails['discvar']['variantid'];
    //     } else {
    //         $data['vardiscval']     = "0";
    //     }

    //     if (!empty($transactions['amountpaid'])) {
    //         $data['pay'] = $transactions['amountpaid'];
    //     } elseif (empty($transactions['amountpaid'])) {
    //         foreach ($trxdetails as $trxdetail) {
    //             if ($trxdetail['transactionid'] == $id) {
    //                 $data['pay'] = $trxdetail['value'];
    //             }
    //         }
    //     } else {
    //         $data['pay'] = '0';
    //     }

    //     if (!empty($debt['value'])) {
    //         $data['debt']       = $debt['debt'];
    //         $data['totaldebt']  = $debt['value'];
    //     } else {
    //         $data['debt']       = "0";
    //         $data['totaldebt']  = "0";
    //     }

    //     $data['title']          = lang('Global.transaction');
    //     $data['description']    = lang('Global.transactionListDesc');
    //     $data['links']          = $actual_link;
    //     $data['bundles']        = $bundles;
    //     $data['bundets']        = $bundets;
    //     $data['cash']           = $Cash;
    //     $data['transactions']   = $transactions;
    //     $data['outlets']        = $outlets;
    //     $data['payments']       = $payments;
    //     $data['customers']      = $customers;
    //     $data['products']       = $products;
    //     $data['variants']       = $variants;
    //     $data['stocks']         = $stocks;
    //     $data['trxdetails']     = $TrxdetailModel->where('transactionid', $id)->find();
    //     $data['trxpayments']    = $trxpayments;
    //     $data['outid']          = $OutletModel->where('id', $this->data['outletPick'])->first();
    //     $data['bundleVariants'] = $bundleVariants->getResult();
    //     $data['members']        = $MemberModel->findAll();
    //     $data['user']           = $user->username;
    //     $data['date']           = $transactions['date'];
    //     $data['transactionid']  = $id;
    //     $data['subtotal']       = $subtotal;
    //     $data['member']         = $MemberModel->where('id', $transactions['memberid'])->first();
    //     $data['total']          = $total;

    //     return view('Views/invoice', $data);
    // }

    // public function invoicebook($id)
    // {
    //     // Calling Models
    //     $BundleModel            = new BundleModel();
    //     $BundledetModel         = new BundledetailModel();
    //     $CashModel              = new CashModel();
    //     $DebtModel              = new DebtModel();
    //     $GconfigModel           = new GconfigModel();
    //     $OutletModel            = new OutletModel();
    //     $UserModel              = new UserModel();
    //     $MemberModel            = new MemberModel();
    //     $PaymentModel           = new PaymentModel();
    //     $ProductModel           = new ProductModel();
    //     $VariantModel           = new VariantModel();
    //     $StockModel             = new StockModel();
    //     $BookingModel           = new BookingModel();
    //     $BookingdetailModel     = new BookingdetailModel();
    //     $TransactionModel       = new TransactionModel();
    //     $TrxdetailModel         = new TrxdetailModel();
    //     $TrxpaymentModel        = new TrxpaymentModel();
    //     $MemberModel            = new MemberModel();
    //     $GconfigModel           = new GconfigModel();

    //     $db                 = \Config\Database::connect();
    //     $bundles            = $BundleModel->findAll();
    //     $bundets            = $BundledetModel->findAll();
    //     $booking            = $BookingModel->find($id);
    //     $bookingdetails     = $BookingdetailModel->where('bookingid', $id)->find();
    //     $Cash               = $CashModel->findAll();
    //     $outlets            = $OutletModel->findAll();
    //     $users              = $UserModel->findAll();
    //     $customers          = $MemberModel->findAll();
    //     $payments           = $PaymentModel->findAll();
    //     $products           = $ProductModel->findAll();
    //     $variants           = $VariantModel->findAll();
    //     $stocks             = $StockModel->findAll();
    //     $member             = $MemberModel->where('id', $booking['memberid'])->first();
    //     $debt               = $DebtModel->where('memberid', $booking['memberid'])->first();
    //     $user               = $UserModel->where('id', $booking['userid'])->first();
    //     $Gconfig            = $GconfigModel->first();

    //     $bundleBuilder      = $db->table('bundledetail');
    //     $bundleVariants     = $bundleBuilder->select('bundledetail.bundleid as bundleid, variant.id as id, variant.productid as productid, variant.name as name, stock.outletid as outletid, stock.qty as qty');
    //     $bundleVariants     = $bundleBuilder->join('variant', 'bundledetail.variantid = variant.id', 'left');
    //     $bundleVariants     = $bundleBuilder->join('stock', 'stock.variantid = variant.id', 'left');
    //     $bundleVariants     = $bundleBuilder->orderBy('stock.qty', 'ASC');
    //     $bundleVariants     = $bundleBuilder->get();
    //     $data               = $this->data;
    //     if (!empty($member)) {
    //         $data['cust']           = $MemberModel->where('id', $booking['memberid'])->first();
    //         $data['mempoin']        = $member['poin'];
    //     } else {
    //         $data['cust']           = "0";
    //         $data['mempoin']        = "0";
    //         $data['poinused']       = "0";
    //     }

    //     if (!empty($input['value']) && $input['value'] <= "0") {
    //         $data['change']     = (int)$input['value'] - (int)$total;
    //     } else {
    //         $data['change']     = "0";
    //     }

    //     if (!empty($booking['discvar']) && $booking['discvar'] !== "0") {
    //         $data['vardiscval']     = $bookingdetails['value']['variantid'];
    //     } else {
    //         $data['vardiscval']     = "0";
    //     }

    //     foreach ($trxdetails as $trxdetail) {
    //         if ($trxdetail['transactionid'] === $transactions['id']) {
    //             $sub =  ((int)$trxdetail['value'] * (int)$trxdetail['qty']);
    //             $data['discount'] += (int)$sub * ((int)$transactions['discvalue'] / 100);
    //         }
    //     }


    //     if ((!empty($booking['discvalue'])) && ($booking['disctype'] === '0')) {
    //         $data['discount']   = $booking['discvalue'];
    //         $data['memberdisc'] = $booking['discvalue'];
    //     } elseif ((!empty($booking['discvalue'])) && ($booking['disctype'] === '1')) {
    //         $sub = (int)$bookingdetails['value'] * (int)$bookingdetails['qty'];
    //         $data['discount']   = ((int)$booking['discvalue'] / 100) * (int)$sub;
    //         // $data['discount'] = ($booking['discvalue']/100) * $bookingdetails['value'];
    //         $data['memberdisc'] = ((int)$booking['discvalue'] / 100) * (int)$bookingdetails['value'];
    //     } else {
    //         $data['discount'] = 0;
    //         $data['memberdisc'] = 0;
    //     }

    //     if (!empty($input['debt'])) {
    //         $data['debt']       = $input['debt'];
    //         $data['totaldebt']  = $member['kasbon'];
    //     } else {
    //         $data['debt']       = "0";
    //         $data['totaldebt']  = "0";
    //     }

    //     $subtotal = 0;
    //     foreach ($bookingdetails as $bookingdetail) {
    //         $subtotal += $bookingdetail['value'];
    //     }

    //     // Gconfig poin setup
    //     $minimTrx    = $Gconfig['poinorder'];
    //     $poinval     = $Gconfig['poinvalue'];

    //     if (($minimTrx != "0") && ($subtotal  >= $minimTrx)) {
    //         $value  = (int)$subtotal / (int)$minimTrx;
    //         $result = floor($value);
    //         $poin   = (int)$result * $poinval;
    //     }

    //     if (!empty($booking['memberid'])) {
    //         $data['cust']           = $MemberModel->where('id', $booking['memberid'])->first();
    //         $data['mempoin']        = $member['poin'];
    //         $data['poinearn']       = $poin;
    //     } else {
    //         $data['cust']           = "0";
    //         $data['mempoin']        = "0";
    //         $data['poinearn']       = "0";
    //     }

    //     $data['total']          = $booking['value'];
    //     $data['title']          = lang('Global.transaction');
    //     $data['description']    = lang('Global.transactionListDesc');
    //     $data['bundles']        = $bundles;
    //     $data['bundets']        = $bundets;
    //     $data['cash']           = $Cash;
    //     $data['outlets']        = $outlets;
    //     $data['payments']       = $payments;
    //     $data['customers']      = $customers;
    //     $data['products']       = $products;
    //     $data['variants']       = $variants;
    //     $data['stocks']         = $stocks;
    //     $data['trxdetails']     = $TrxdetailModel->findAll();
    //     $data['outid']          = $OutletModel->where('id', $this->data['outletPick'])->first();
    //     $data['bookings']       = $booking;
    //     $data['bookingdetails'] = $bookingdetails;
    //     $data['bundleVariants'] = $bundleVariants->getResult();
    //     $data['members']        = $MemberModel->findAll();

    //     $actual_link            = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    //     $data['links']          = $actual_link;
    //     $data['pay']            = "UNPAID";
    //     $data['user']           = $user->username;
    //     $data['date']           = $booking['created_at'];
    //     $data['bookingid']      = $booking['id'];
    //     $data['subtotal']       = $subtotal;
    //     $data['member']         = $MemberModel->where('id', $booking['memberid'])->first();

    //     return view('Views/invoice', $data);
    // }
}
