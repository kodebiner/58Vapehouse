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
use App\Models\TrxotherModel;
use App\Models\TrxpaymentModel;
use App\Models\DebtModel;
use App\Models\DebtInsModel;
use App\Models\DailyReportModel;
use App\Models\CheckpointModel;

class DailyReport extends BaseController
{
    protected $data;
    protected $db, $builder;
    protected $auth;
    protected $config;
    
    public function index()
    {
        // if ($this->data['outletPick'] != null) {
            $pager      = \Config\Services::pager();

            // Calling Models
            $TransactionModel   = new TransactionModel();
            $TrxdetailModel     = new TrxdetailModel();
            $TrxpaymentModel    = new TrxpaymentModel();
            $TrxotherModel      = new TrxotherModel();
            $ProductModel       = new ProductModel();
            $VariantModel       = new VariantModel();
            $BundleModel        = new BundleModel();
            $BundledetailModel  = new BundledetailModel();
            $PaymentModel       = new PaymentModel();
            $DebtModel          = new DebtModel();
            $DebtInsModel       = new DebtInsModel();
            $UserModel          = new UserModel();
            $CashModel          = new CashModel();
            $OutletModel        = new OutletModel();
            $DailyReportModel   = new DailyReportModel();
            $MemberModel        = new MemberModel();
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

            $cashdata       = $CashModel->where('outletid', $this->data['outletPick'])->find();
            $dailyreports   = $DailyReportModel->orderby('dateopen', 'DESC')->where('dateopen >=', $startdate . " 00:00:00")->where('dateopen <=', $enddate . " 23:59:59")->where('outletid', $this->data['outletPick'])->paginate(20, 'dailyreport');

            $dailyreportdata    = [];
            // foreach ($dailyreports as $dayrep) {
            //     // Id
            //     $dailyreportdata[$dayrep['id']]['id']               = $dayrep['id'];

            //     // Outlet
            //     $outlets                                            = $OutletModel->find($this->data['outletPick']);
            //     $dailyreportdata[$dayrep['id']]['outlet']           = $outlets['name'];

            //     // Date
            //     $dailyreportdata[$dayrep['id']]['date']             = date('l, d M Y', strtotime($dayrep['dateopen']));

            //     // Date Open
            //     $dailyreportdata[$dayrep['id']]['dateopen']         = date('l, d M Y, H:i:s', strtotime($dayrep['dateopen']));

            //     // Transaction Data
            //     $transactions       = $TransactionModel->where('date >=', $dayrep['dateopen'])->where('date <=', $dayrep['dateclose'])->where('outletid', $this->data['outletPick'])->find();
            //     $totalproductsell   = [];

            //     // Date Closed
            //     if ($dayrep['dateclose'] != '0000-00-00 00:00:00') {
            //         $dailyreportdata[$dayrep['id']]['dateclose']    = date('l, d M Y, H:i:s', strtotime($dayrep['dateclose']));

            //         // User Close Store
            //         $userclose                                      = $UserModel->find($dayrep['useridclose']);
            //         $dailyreportdata[$dayrep['id']]['userclose']    = $userclose->firstname.' '.$userclose->lastname;

            //         // Transaction Data
            //         foreach ($transactions as $trx) {
            //             // Product Seliing Data
            //             $trxdetails     = $TrxdetailModel->where('transactionid', $trx['id'])->find();
                        
            //             if (!empty($trxdetails)) {
            //                 foreach ($trxdetails as $trxdet) {
            //                     $variants       = $VariantModel->find($trxdet['variantid']);
                                
            //                     if (!empty($variants)) {
            //                         $products   = $ProductModel->find($variants['productid']);
            
            //                         $dailyreportdata[$dayrep['id']]['productsell'][$products['id']]['name']            = $products['name'];
            //                         $dailyreportdata[$dayrep['id']]['productsell'][$products['id']]['qty'][]           = $trxdet['qty'];
            //                     } else {
            //                         $products   = [];

            //                         $dailyreportdata[$dayrep['id']]['productsell'][0]['name']                           = 'Produk / Variant Terhapus';
            //                         $dailyreportdata[$dayrep['id']]['productsell'][0]['name'][]                         = $trxdet['qty'];
            //                     }

            //                     $totalproductsell[]                                                                     = $trxdet['qty'];
            //                 }
            //             } else {
            //                 $variants   = [];
            //                 $products   = [];
            //             }

            //             // Customer Name
            //             if ($trx['memberid'] == '0') {
            //                 $member     = 'Non Member';
            //             } else {
            //                 $members    = $MemberModel->find($trx['memberid']);
            //                 $member     = $members['name'];
            //             }

            //             // Cash, Non-Cash, Debt
            //             $trxpayments    = $TrxpaymentModel->where('transactionid', $trx['id'])->where('paymentid !=', '0')->find();
            //             $debtpayments   = $TrxpaymentModel->where('transactionid', $trx['id'])->where('paymentid', '0')->find();

            //             if (!empty($trxpayments)) {
            //                 foreach ($trxpayments as $trxpayment) {
            //                     $payment        = $PaymentModel->find($trxpayment['paymentid']);
            //                     $cashdata       = $CashModel->find($payment['cashid']);

            //                     if (strcmp($cashdata['name'], 'Petty Cash ' . $outlets['name']) == 0) {
            //                         $cashname   = 'Tunai';
            //                     } else {
            //                         $cashname   = $cashdata['name'];
            //                     }

            //                     // Transaction Summary
            //                     $dailyreportdata[$dayrep['id']]['trxpayments'][$cashdata['id']]['name']                                 = $cashname;
            //                     $dailyreportdata[$dayrep['id']]['trxpayments'][$cashdata['id']]['detail'][$trxpayment['id']]['name']    = $payment['name'];
            //                     $dailyreportdata[$dayrep['id']]['trxpayments'][$cashdata['id']]['detail'][$trxpayment['id']]['value']   = $trxpayment['value'];

            //                     // Detail Transaction
            //                     $dailyreportdata[$dayrep['id']]['payments'][$trx['id']]['detail'][$payment['id']]['name']               = $payment['name'];
            //                     $dailyreportdata[$dayrep['id']]['payments'][$trx['id']]['detail'][$payment['id']]['value']              = $trxpayment['value'];
            //                     $dailyreportdata[$dayrep['id']]['payments'][$trx['id']]['detail'][$payment['id']]['custname']           = $member;
            //                     $dailyreportdata[$dayrep['id']]['payments'][$trx['id']]['detail'][$payment['id']]['time']               = date('H:i:s', strtotime($trx['date']));
            //                     $dailyreportdata[$dayrep['id']]['payments'][$trx['id']]['detail'][$payment['id']]['proof']              = $trx['photo'];
            //                 }
            //             }
            //             if (!empty($debtpayments)) {
            //                 foreach ($debtpayments as $debtpayment) {
            //                     // Transaction Summary
            //                     $dailyreportdata[$dayrep['id']]['trxpayments'][0]['name']                               = 'Kasbon';
            //                     $dailyreportdata[$dayrep['id']]['trxpayments'][0]['detail'][0]['name']                  = 'Kasbon';
            //                     $dailyreportdata[$dayrep['id']]['trxpayments'][0]['detail'][0]['value']                 = $debtpayment['value'];

            //                     // Detail Transaction
            //                     $dailyreportdata[$dayrep['id']]['payments'][$trx['id']]['detail'][0]['name']            = 'Kasbon';
            //                     $dailyreportdata[$dayrep['id']]['payments'][$trx['id']]['detail'][0]['value']           = $debtpayment['value'];
            //                     $dailyreportdata[$dayrep['id']]['payments'][$trx['id']]['detail'][0]['custname']        = $member;
            //                     $dailyreportdata[$dayrep['id']]['payments'][$trx['id']]['detail'][0]['time']            = date('H:i:s', strtotime($trx['date']));
            //                     $dailyreportdata[$dayrep['id']]['payments'][$trx['id']]['detail'][0]['proof']           = $trx['photo'];
            //                 }
            //             }
            //         }

            //         // Actual Cash Close
            //         $dailyreportdata[$dayrep['id']]['cashclose']        = $dayrep['cashclose'];
    
            //         // Actual Non Cash Close
            //         $dailyreportdata[$dayrep['id']]['noncashclose']     = $dayrep['noncashclose'];
    
            //         // Actual Cashier Summary
            //         $dailyreportdata[$dayrep['id']]['actualsummary']    = (Int)$dayrep['cashclose'] + (Int)$dayrep['noncashclose'];
            //     } else {
            //         $dailyreportdata[$dayrep['id']]['dateclose']    = lang('Global.storeNotClosed');

            //         // User Close Store
            //         $dailyreportdata[$dayrep['id']]['userclose']    = lang('Global.storeNotClosed');

            //         // Product Seliing Data
            //         $trxdetails                                     = [];
            //         $dailyreportdata[$dayrep['id']]['productsell']  = [];
            //         $totalproductsell[]                             = [];
            //         $variants                                       = [];
            //         $products                                       = [];

            //         // Payment Methods
            //         $dailyreportdata[$dayrep['id']]['payments']     = [];
            //         $dailyreportdata[$dayrep['id']]['trxpayments']  = [];

            //         // Actual Cash Close
            //         $dailyreportdata[$dayrep['id']]['cashclose']        = '0';
    
            //         // Actual Non Cash Close
            //         $dailyreportdata[$dayrep['id']]['noncashclose']     = '0';
    
            //         // Actual Cashier Summary
            //         $dailyreportdata[$dayrep['id']]['actualsummary']    = (Int)$dayrep['cashclose'] + (Int)$dayrep['noncashclose'];
            //     }

            //     // User Open Store
            //     $useropen                                           = $UserModel->find($dayrep['useridopen']);
            //     $dailyreportdata[$dayrep['id']]['useropen']         = $useropen->firstname.' '.$useropen->lastname;

            //     // Total Prodcuct Sell
            //     $dailyreportdata[$dayrep['id']]['totalproductsell'] = array_sum($totalproductsell);

            //     // Cash Flow
            //     $trxothers  = $TrxotherModel->where('date >=', $dayrep['dateopen'])->where('date <=', $dayrep['dateclose'])->where('outletid', $this->data['outletPick'])->notLike('description', 'Debt')->notLike('description', 'Top Up')->find();
            //     $debtins    = $TrxotherModel->where('date >=', $dayrep['dateopen'])->where('date <=', $dayrep['dateclose'])->where('outletid', $this->data['outletPick'])->Like('description', 'Debt')->find();
            //     $topups     = $TrxotherModel->where('date >=', $dayrep['dateopen'])->where('date <=', $dayrep['dateclose'])->where('outletid', $this->data['outletPick'])->Like('description', 'Top Up')->find();
            //     $withdraws  = $TrxotherModel->where('date >=', $dayrep['dateopen'])->where('date <=', $dayrep['dateclose'])->where('outletid', $this->data['outletPick'])->Like('description', 'Cash Withdraw')->find();

            //     if (!empty($trxothers)) {
            //         foreach ($trxothers as $trxother) {
            //             // User Cashier
            //             $usercashcier   = $UserModel->find($trxother['userid']);

            //             // Cashflow Data
            //             $dailyreportdata[$dayrep['id']]['cashflow'][$trxother['id']]['cashier'] = $usercashcier->firstname.' '.$usercashcier->lastname;
            //             $dailyreportdata[$dayrep['id']]['cashflow'][$trxother['id']]['type']    = $trxother['type'];
            //             $dailyreportdata[$dayrep['id']]['cashflow'][$trxother['id']]['desc']    = $trxother['description'];
            //             $dailyreportdata[$dayrep['id']]['cashflow'][$trxother['id']]['date']    = date('H:i:s', strtotime($trxother['date']));
            //             $dailyreportdata[$dayrep['id']]['cashflow'][$trxother['id']]['qty']     = $trxother['qty'];
            //             $dailyreportdata[$dayrep['id']]['cashflow'][$trxother['id']]['proof']   = $trxother['photo'];
            //         }
            //     } else {
            //         $usercashcier   = [];
            //         $dailyreportdata[$dayrep['id']]['cashflow'] = [];
            //     }

            //     if (!empty($debtins)) {
            //         foreach ($debtins as $debtin) {
            //             // User Cashier
            //             $usercashcier   = $UserModel->find($debtin['userid']);

            //             // Debt Installment Data
            //             $cashdebt       = $CashModel->find($debtin['cashid']);
            //             $dailyreportdata[$dayrep['id']]['debtins'][$cashdebt['id']]['name']                             = $cashdebt['name'];

            //             // Detail Debt Installment
            //             $dailyreportdata[$dayrep['id']]['debtins'][$cashdebt['id']]['detail'][$debtin['id']]['value']   = $debtin['qty'];
            //             $dailyreportdata[$dayrep['id']]['debtins'][$cashdebt['id']]['detail'][$debtin['id']]['cashier'] = $usercashcier->firstname.' '.$usercashcier->lastname;
            //             $dailyreportdata[$dayrep['id']]['debtins'][$cashdebt['id']]['detail'][$debtin['id']]['type']    = $debtin['type'];
            //             $dailyreportdata[$dayrep['id']]['debtins'][$cashdebt['id']]['detail'][$debtin['id']]['desc']    = $debtin['description'];
            //             $dailyreportdata[$dayrep['id']]['debtins'][$cashdebt['id']]['detail'][$debtin['id']]['date']    = date('H:i:s', strtotime($debtin['date']));
            //             $dailyreportdata[$dayrep['id']]['debtins'][$cashdebt['id']]['detail'][$debtin['id']]['qty']     = $debtin['qty'];
            //             $dailyreportdata[$dayrep['id']]['debtins'][$cashdebt['id']]['detail'][$debtin['id']]['proof']   = $debtin['photo'];
            //         }
            //     } else {
            //         $usercashcier   = [];
            //         $dailyreportdata[$dayrep['id']]['debtins'] = [];
            //     }

            //     if (!empty($topups)) {
            //         foreach ($topups as $topup) {
            //             // User Cashier
            //             $usercashcier   = $UserModel->find($topup['userid']);

            //             // Top Up Data
            //             $cashtopup      = $CashModel->find($topup['cashid']);
            //             $dailyreportdata[$dayrep['id']]['topup'][$cashtopup['id']]['name']                              = $cashtopup['name'];

            //             // Detail Top Up
            //             $dailyreportdata[$dayrep['id']]['topup'][$cashtopup['id']]['detail'][$topup['id']]['value']     = $topup['qty'];
            //             $dailyreportdata[$dayrep['id']]['topup'][$cashtopup['id']]['detail'][$topup['id']]['cashier']   = $usercashcier->firstname.' '.$usercashcier->lastname;
            //             $dailyreportdata[$dayrep['id']]['topup'][$cashtopup['id']]['detail'][$topup['id']]['type']      = $topup['type'];
            //             $dailyreportdata[$dayrep['id']]['topup'][$cashtopup['id']]['detail'][$topup['id']]['desc']      = $topup['description'];
            //             $dailyreportdata[$dayrep['id']]['topup'][$cashtopup['id']]['detail'][$topup['id']]['date']      = date('H:i:s', strtotime($topup['date']));
            //             $dailyreportdata[$dayrep['id']]['topup'][$cashtopup['id']]['detail'][$topup['id']]['qty']       = $topup['qty'];
            //             $dailyreportdata[$dayrep['id']]['topup'][$cashtopup['id']]['detail'][$topup['id']]['proof']     = $topup['photo'];
            //         }
            //     } else {
            //         $usercashcier   = [];
            //         $dailyreportdata[$dayrep['id']]['topup'] = [];
            //     }

            //     if (!empty($withdraws)) {
            //         foreach ($withdraws as $withdraw) {
            //             // User Cashier
            //             $usercashcier   = $UserModel->find($withdraw['userid']);

            //             // Withdraw Data
            //             $cashwithdraw   = $CashModel->find($withdraw['cashid']);
            //             $dailyreportdata[$dayrep['id']]['withdraw'][$cashwithdraw['id']]['name']                                = $cashwithdraw['name'];

            //             // Detail Withdraw
            //             $dailyreportdata[$dayrep['id']]['withdraw'][$cashwithdraw['id']]['detail'][$withdraw['id']]['value']    = $withdraw['qty'];
            //             $dailyreportdata[$dayrep['id']]['withdraw'][$cashwithdraw['id']]['detail'][$withdraw['id']]['cashier']  = $usercashcier->firstname.' '.$usercashcier->lastname;
            //             $dailyreportdata[$dayrep['id']]['withdraw'][$cashwithdraw['id']]['detail'][$withdraw['id']]['type']     = $withdraw['type'];
            //             $dailyreportdata[$dayrep['id']]['withdraw'][$cashwithdraw['id']]['detail'][$withdraw['id']]['desc']     = $withdraw['description'];
            //             $dailyreportdata[$dayrep['id']]['withdraw'][$cashwithdraw['id']]['detail'][$withdraw['id']]['date']     = date('H:i:s', strtotime($withdraw['date']));
            //             $dailyreportdata[$dayrep['id']]['withdraw'][$cashwithdraw['id']]['detail'][$withdraw['id']]['qty']      = $withdraw['qty'];
            //             $dailyreportdata[$dayrep['id']]['withdraw'][$cashwithdraw['id']]['detail'][$withdraw['id']]['proof']    = $withdraw['photo'];
            //         }
            //     } else {
            //         $usercashcier   = [];
            //         $dailyreportdata[$dayrep['id']]['withdraw'] = [];
            //     }

            //     // Initial Cash
            //     $dailyreportdata[$dayrep['id']]['initialcash']      = $dayrep['initialcash'];

            //     // Total Cash In
            //     $dailyreportdata[$dayrep['id']]['totalcashin']      = $dayrep['totalcashin'];

            //     // Total Cash Out
            //     $dailyreportdata[$dayrep['id']]['totalcashout']     = $dayrep['totalcashout'];
            // }

            foreach ($dailyreports as $dayrep) {
                // Id
                $dailyreportdata[$dayrep['id']]['id']               = $dayrep['id'];

                // Outlet
                $outlets                                            = $OutletModel->find($this->data['outletPick']);
                $dailyreportdata[$dayrep['id']]['outlet']           = $outlets['name'];

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
                        // Product Seliing Data
                        $trxdetails     = $TrxdetailModel->where('transactionid', $trx['id'])->find();
                        
                        if (!empty($trxdetails)) {
                            foreach ($trxdetails as $trxdet) {
                                $variants       = $VariantModel->find($trxdet['variantid']);
                                
                                if (!empty($variants)) {
                                    $products   = $ProductModel->find($variants['productid']);
            
                                    $dailyreportdata[$dayrep['id']]['productsell'][$products['id']]['name']            = $products['name'];
                                    $dailyreportdata[$dayrep['id']]['productsell'][$products['id']]['qty'][]           = $trxdet['qty'];
                                } else {
                                    $products   = [];

                                    $dailyreportdata[$dayrep['id']]['productsell'][0]['name']                           = 'Produk / Variant Terhapus';
                                    $dailyreportdata[$dayrep['id']]['productsell'][0]['name'][]                         = $trxdet['qty'];
                                }

                                $totalproductsell[]                                                                     = $trxdet['qty'];
                            }
                        } else {
                            $variants   = [];
                            $products   = [];
                        }

                        // Customer Name
                        if ($trx['memberid'] == '0') {
                            $member     = 'Non Member';
                        } else {
                            $members    = $MemberModel->find($trx['memberid']);
                            $member     = $members['name'];
                        }

                        // Cash, Non-Cash, Debt
                        $trxpayments    = $TrxpaymentModel->where('transactionid', $trx['id'])->where('paymentid !=', '0')->where('paymentid !=', '-1')->find();
                        $debtpayments   = $TrxpaymentModel->where('transactionid', $trx['id'])->where('paymentid', '0')->find();
                        $pointpayments  = $TrxpaymentModel->where('transactionid', $trx['id'])->where('paymentid', '-1')->find();

                        if (!empty($trxpayments)) {
                            foreach ($trxpayments as $trxpayment) {
                                $payment        = $PaymentModel->find($trxpayment['paymentid']);
                                $cashdata       = $CashModel->find($payment['cashid']);

                                if (strcmp($cashdata['name'], 'Petty Cash ' . $outlets['name']) == 0) {
                                    $cashname   = 'Tunai';
                                } else {
                                    $cashname   = $cashdata['name'];
                                }

                                // Transaction Summary
                                $dailyreportdata[$dayrep['id']]['trxpayments'][$cashdata['id']]['name']                                 = $cashname;
                                $dailyreportdata[$dayrep['id']]['trxpayments'][$cashdata['id']]['detail'][$trxpayment['id']]['name']    = $payment['name'];
                                $dailyreportdata[$dayrep['id']]['trxpayments'][$cashdata['id']]['detail'][$trxpayment['id']]['value']   = $trxpayment['value'];

                                // Detail Transaction
                                $dailyreportdata[$dayrep['id']]['payments'][$trx['id']]['detail'][$payment['id']]['name']               = $payment['name'];
                                $dailyreportdata[$dayrep['id']]['payments'][$trx['id']]['detail'][$payment['id']]['value']              = $trxpayment['value'];
                                $dailyreportdata[$dayrep['id']]['payments'][$trx['id']]['detail'][$payment['id']]['custname']           = $member;
                                $dailyreportdata[$dayrep['id']]['payments'][$trx['id']]['detail'][$payment['id']]['time']               = date('H:i:s', strtotime($trx['date']));
                                $dailyreportdata[$dayrep['id']]['payments'][$trx['id']]['detail'][$payment['id']]['proof']              = $trx['photo'];
                            }
                        }

                        if (!empty($debtpayments)) {
                            foreach ($debtpayments as $debtpayment) {
                                // Transaction Summary
                                $dailyreportdata[$dayrep['id']]['trxpayments'][0]['name']                                   = 'Kasbon';
                                $dailyreportdata[$dayrep['id']]['trxpayments'][0]['detail'][$debtpayment['id']]['name']     = 'Kasbon';
                                $dailyreportdata[$dayrep['id']]['trxpayments'][0]['detail'][$debtpayment['id']]['value']    = $debtpayment['value'];

                                // Detail Transaction
                                $dailyreportdata[$dayrep['id']]['payments'][$trx['id']]['detail'][0]['name']            = 'Kasbon';
                                $dailyreportdata[$dayrep['id']]['payments'][$trx['id']]['detail'][0]['value']           = $debtpayment['value'];
                                $dailyreportdata[$dayrep['id']]['payments'][$trx['id']]['detail'][0]['custname']        = $member;
                                $dailyreportdata[$dayrep['id']]['payments'][$trx['id']]['detail'][0]['time']            = date('H:i:s', strtotime($trx['date']));
                                $dailyreportdata[$dayrep['id']]['payments'][$trx['id']]['detail'][0]['proof']           = $trx['photo'];
                            }
                        }

                        if (!empty($pointpayments)) {
                            foreach ($pointpayments as $pointpayment) {
                                // Transaction Summary
                                $dailyreportdata[$dayrep['id']]['trxpayments'][-1]['name']                                  = lang('Global.redeemPoint');
                                $dailyreportdata[$dayrep['id']]['trxpayments'][-1]['detail'][$pointpayment['id']]['name']   = lang('Global.redeemPoint');
                                $dailyreportdata[$dayrep['id']]['trxpayments'][-1]['detail'][$pointpayment['id']]['value']  = $pointpayment['value'];

                                // Detail Transaction
                                $dailyreportdata[$dayrep['id']]['payments'][$trx['id']]['detail'][-1]['name']            = lang('Global.redeemPoint');
                                $dailyreportdata[$dayrep['id']]['payments'][$trx['id']]['detail'][-1]['value']           = $pointpayment['value'];
                                $dailyreportdata[$dayrep['id']]['payments'][$trx['id']]['detail'][-1]['custname']        = $member;
                                $dailyreportdata[$dayrep['id']]['payments'][$trx['id']]['detail'][-1]['time']            = date('H:i:s', strtotime($trx['date']));
                                $dailyreportdata[$dayrep['id']]['payments'][$trx['id']]['detail'][-1]['proof']           = $trx['photo'];
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
                            $dailyreportdata[$dayrep['id']]['checkpoint'][$checkpoint['id']]['id']      = $checkpoint['id'];
                            $dailyreportdata[$dayrep['id']]['checkpoint'][$checkpoint['id']]['cashier'] = $checkpointcashier->firstname.' '.$checkpointcashier->lastname;
                            $dailyreportdata[$dayrep['id']]['checkpoint'][$checkpoint['id']]['date']    = $checkpoint['date'];
                            $dailyreportdata[$dayrep['id']]['checkpoint'][$checkpoint['id']]['cash']    = 'Rp '.number_format($checkpoint['cash'], 0, ',', '.');
                            $dailyreportdata[$dayrep['id']]['checkpoint'][$checkpoint['id']]['noncash'] = 'Rp '.number_format($checkpoint['noncash'], 0, ',', '.');
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

                    // Product Seliing Data
                    $trxdetails                                     = [];
                    $dailyreportdata[$dayrep['id']]['productsell']  = [];
                    $totalproductsell[]                             = [];
                    $variants                                       = [];
                    $products                                       = [];

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
                            $dailyreportdata[$dayrep['id']]['checkpoint'][$checkpoint['id']]['cash']    = 'Rp '.number_format($checkpoint['cash'], 0, ',', '.');
                            $dailyreportdata[$dayrep['id']]['checkpoint'][$checkpoint['id']]['noncash'] = 'Rp '.number_format($checkpoint['noncash'], 0, ',', '.');
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
                // $topups     = $TrxotherModel->where('date >=', $dayrep['dateopen'])->where('date <=', $dayrep['dateclose'])->where('outletid', $this->data['outletPick'])->Like('description', 'Top Up')->find();
                // $withdraws  = $TrxotherModel->where('date >=', $dayrep['dateopen'])->where('date <=', $dayrep['dateclose'])->where('outletid', $this->data['outletPick'])->Like('description', 'Cash Withdraw')->find();

                if (!empty($trxothers)) {
                    foreach ($trxothers as $trxother) {
                        // User Cashier
                        $usercashcier   = $UserModel->find($trxother['userid']);

                        // Cashflow Data
                        $dailyreportdata[$dayrep['id']]['cashflow'][$trxother['id']]['cashier'] = $usercashcier->firstname.' '.$usercashcier->lastname;
                        $dailyreportdata[$dayrep['id']]['cashflow'][$trxother['id']]['type']    = $trxother['type'];
                        $dailyreportdata[$dayrep['id']]['cashflow'][$trxother['id']]['desc']    = $trxother['description'];
                        $dailyreportdata[$dayrep['id']]['cashflow'][$trxother['id']]['date']    = date('H:i:s', strtotime($trxother['date']));
                        $dailyreportdata[$dayrep['id']]['cashflow'][$trxother['id']]['qty']     = $trxother['qty'];
                        $dailyreportdata[$dayrep['id']]['cashflow'][$trxother['id']]['proof']   = $trxother['photo'];
                    }
                } else {
                    $usercashcier   = [];
                    $dailyreportdata[$dayrep['id']]['cashflow'] = [];
                }

                // Debt Installment
                $debtins    = $TrxotherModel->where('date >=', $dayrep['dateopen'])->where('date <=', $dayrep['dateclose'])->where('outletid', $this->data['outletPick'])->Like('description', 'Debt')->find();
                // $debtins    = $DebtInsModel->where('transactionid', $trx['id'])->where('outletid', $this->data['outletPick'])->find();
                if (!empty($debtins)) {
                    foreach ($debtins as $debtin) {
                        // User Cashier
                        $usercashcier   = $UserModel->find($debtin['userid']);
                        
                        // // Debt Member
                        // $transaction    = $TransactionModel->find($debtin['transactionid']);
                        // $members        = $MemberModel->find($transaction['memberid']);
    
                        // Debt Installment Data
                        // $paymentins     = $PaymentModel->find($debtin['paymentid']);
                        $cashdebt       = $CashModel->find($debtin['cashid']);
                        $dailyreportdata[$dayrep['id']]['debtins'][$cashdebt['id']]['name']                                = $cashdebt['name'];
    
                        // Detail Debt Installment
                        $dailyreportdata[$dayrep['id']]['debtins'][$cashdebt['id']]['detail'][$debtin['id']]['value']      = $debtin['qty'];
                        $dailyreportdata[$dayrep['id']]['debtins'][$cashdebt['id']]['detail'][$debtin['id']]['cashier']    = $usercashcier->firstname.' '.$usercashcier->lastname;
                        // $dailyreportdata[$dayrep['id']]['debtins'][$cashdebt['id']]['detail'][$debtin['id']]['type']    = $debtin['type'];
                        // $dailyreportdata[$dayrep['id']]['debtins'][$cashdebt['id']]['detail'][$debtin['id']]['desc']    = $debtin['description'];
                        $dailyreportdata[$dayrep['id']]['debtins'][$cashdebt['id']]['detail'][$debtin['id']]['date']       = date('H:i:s', strtotime($debtin['date']));
                        $dailyreportdata[$dayrep['id']]['debtins'][$cashdebt['id']]['detail'][$debtin['id']]['qty']        = $debtin['qty'];
                        // $dailyreportdata[$dayrep['id']]['debtins'][$cashdebt['id']]['detail'][$debtin['id']]['proof']   = $debtin['photo'];
                        $dailyreportdata[$dayrep['id']]['debtins'][$cashdebt['id']]['detail'][$debtin['id']]['member']     = $debtin['description'];
                    }
                }
                else {
                    $debtinst    = $DebtInsModel->where('date >=', $dayrep['dateopen'])->where('date <=', $dayrep['dateclose'])->where('outletid', $this->data['outletPick'])->find();
                    if (!empty($debtinst)) {
                        foreach ($debtinst as $debtinstall) {
                            // User Cashier
                            $usercashcier   = $UserModel->find($debtinstall['userid']);
                            
                            // Debt Member
                            $transaction    = $TransactionModel->find($debtinstall['transactionid']);
                            $members        = $MemberModel->find($transaction['memberid']);
        
                            // Debt Installment Data
                            $paymentins     = $PaymentModel->find($debtinstall['paymentid']);
                            $cashdebt       = $CashModel->find($paymentins['cashid']);
                            $dailyreportdata[$dayrep['id']]['debtins'][$cashdebt['id']]['name']                                = $cashdebt['name'];
        
                            // Detail Debt Installment
                            $dailyreportdata[$dayrep['id']]['debtins'][$cashdebt['id']]['detail'][$debtinstall['id']]['value']      = $debtinstall['qty'];
                            $dailyreportdata[$dayrep['id']]['debtins'][$cashdebt['id']]['detail'][$debtinstall['id']]['cashier']    = $usercashcier->firstname.' '.$usercashcier->lastname;
                            // $dailyreportdata[$dayrep['id']]['debtins'][$cashdebt['id']]['detail'][$debtinstall['id']]['type']    = $debtinstall['type'];
                            // $dailyreportdata[$dayrep['id']]['debtins'][$cashdebt['id']]['detail'][$debtinstall['id']]['desc']    = $debtinstall['description'];
                            $dailyreportdata[$dayrep['id']]['debtins'][$cashdebt['id']]['detail'][$debtinstall['id']]['date']       = date('H:i:s', strtotime($debtinstall['date']));
                            $dailyreportdata[$dayrep['id']]['debtins'][$cashdebt['id']]['detail'][$debtinstall['id']]['qty']        = $debtinstall['qty'];
                            // $dailyreportdata[$dayrep['id']]['debtins'][$cashdebt['id']]['detail'][$debtinstall['id']]['proof']   = $debtinstall['photo'];
                            $dailyreportdata[$dayrep['id']]['debtins'][$cashdebt['id']]['detail'][$debtinstall['id']]['member']     = 'Debt - '.$members['name'].' / '.$members['phone'];
                        }
                    } else {
                        $usercashcier   = [];
                        $dailyreportdata[$dayrep['id']]['debtins'] = [];
                    }
                }

                // // Debt Installment
                // $debtinst    = $DebtInsModel->where('date >=', $dayrep['dateopen'])->where('date <=', $dayrep['dateclose'])->where('outletid', $this->data['outletPick'])->find();
                // if (!empty($debtinst)) {
                //     foreach ($debtinst as $debtin) {
                //         // User Cashier
                //         $usercashcier   = $UserModel->find($debtin['userid']);
                        
                //         // Debt Member
                //         $transaction    = $TransactionModel->find($debtin['transactionid']);
                //         $members        = $MemberModel->find($transaction['memberid']);
    
                //         // Debt Installment Data
                //         $paymentins     = $PaymentModel->find($debtin['paymentid']);
                //         $cashdebt       = $CashModel->find($paymentins['cashid']);
                //         $dailyreportdata[$dayrep['id']]['debtins'][$cashdebt['id']]['name']                                = $cashdebt['name'];
    
                //         // Detail Debt Installment
                //         $dailyreportdata[$dayrep['id']]['debtins'][$cashdebt['id']]['detail'][$debtin['id']]['value']      = $debtin['qty'];
                //         $dailyreportdata[$dayrep['id']]['debtins'][$cashdebt['id']]['detail'][$debtin['id']]['cashier']    = $usercashcier->firstname.' '.$usercashcier->lastname;
                //         // $dailyreportdata[$dayrep['id']]['debtins'][$cashdebt['id']]['detail'][$debtin['id']]['type']    = $debtin['type'];
                //         // $dailyreportdata[$dayrep['id']]['debtins'][$cashdebt['id']]['detail'][$debtin['id']]['desc']    = $debtin['description'];
                //         $dailyreportdata[$dayrep['id']]['debtins'][$cashdebt['id']]['detail'][$debtin['id']]['date']       = date('H:i:s', strtotime($debtin['date']));
                //         $dailyreportdata[$dayrep['id']]['debtins'][$cashdebt['id']]['detail'][$debtin['id']]['qty']        = $debtin['qty'];
                //         // $dailyreportdata[$dayrep['id']]['debtins'][$cashdebt['id']]['detail'][$debtin['id']]['proof']   = $debtin['photo'];
                //         $dailyreportdata[$dayrep['id']]['debtins'][$cashdebt['id']]['detail'][$debtin['id']]['member']     = 'Debt - '.$members['name'].' / '.$members['phone'];
                //     }
                // } else {
                //     $usercashcier   = [];
                //     $dailyreportdata[$dayrep['id']]['debtins'] = [];
                // }

                // if (!empty($topups)) {
                //     foreach ($topups as $topup) {
                //         // User Cashier
                //         $usercashcier   = $UserModel->find($topup['userid']);

                //         // Top Up Data
                //         $cashtopup      = $CashModel->find($topup['cashid']);
                //         $dailyreportdata[$dayrep['id']]['topup'][$cashtopup['id']]['name']                              = $cashtopup['name'];

                //         // Detail Top Up
                //         $dailyreportdata[$dayrep['id']]['topup'][$cashtopup['id']]['detail'][$topup['id']]['value']     = $topup['qty'];
                //         $dailyreportdata[$dayrep['id']]['topup'][$cashtopup['id']]['detail'][$topup['id']]['cashier']   = $usercashcier->firstname.' '.$usercashcier->lastname;
                //         $dailyreportdata[$dayrep['id']]['topup'][$cashtopup['id']]['detail'][$topup['id']]['type']      = $topup['type'];
                //         $dailyreportdata[$dayrep['id']]['topup'][$cashtopup['id']]['detail'][$topup['id']]['desc']      = $topup['description'];
                //         $dailyreportdata[$dayrep['id']]['topup'][$cashtopup['id']]['detail'][$topup['id']]['date']      = date('H:i:s', strtotime($topup['date']));
                //         $dailyreportdata[$dayrep['id']]['topup'][$cashtopup['id']]['detail'][$topup['id']]['qty']       = $topup['qty'];
                //         $dailyreportdata[$dayrep['id']]['topup'][$cashtopup['id']]['detail'][$topup['id']]['proof']     = $topup['photo'];
                //     }
                // } else {
                //     $usercashcier   = [];
                //     $dailyreportdata[$dayrep['id']]['topup'] = [];
                // }

                // if (!empty($withdraws)) {
                //     foreach ($withdraws as $withdraw) {
                //         // User Cashier
                //         $usercashcier   = $UserModel->find($withdraw['userid']);

                //         // Withdraw Data
                //         $cashwithdraw   = $CashModel->find($withdraw['cashid']);
                //         $dailyreportdata[$dayrep['id']]['withdraw'][$cashwithdraw['id']]['name']                                = $cashwithdraw['name'];

                //         // Detail Withdraw
                //         $dailyreportdata[$dayrep['id']]['withdraw'][$cashwithdraw['id']]['detail'][$withdraw['id']]['value']    = $withdraw['qty'];
                //         $dailyreportdata[$dayrep['id']]['withdraw'][$cashwithdraw['id']]['detail'][$withdraw['id']]['cashier']  = $usercashcier->firstname.' '.$usercashcier->lastname;
                //         $dailyreportdata[$dayrep['id']]['withdraw'][$cashwithdraw['id']]['detail'][$withdraw['id']]['type']     = $withdraw['type'];
                //         $dailyreportdata[$dayrep['id']]['withdraw'][$cashwithdraw['id']]['detail'][$withdraw['id']]['desc']     = $withdraw['description'];
                //         $dailyreportdata[$dayrep['id']]['withdraw'][$cashwithdraw['id']]['detail'][$withdraw['id']]['date']     = date('H:i:s', strtotime($withdraw['date']));
                //         $dailyreportdata[$dayrep['id']]['withdraw'][$cashwithdraw['id']]['detail'][$withdraw['id']]['qty']      = $withdraw['qty'];
                //         $dailyreportdata[$dayrep['id']]['withdraw'][$cashwithdraw['id']]['detail'][$withdraw['id']]['proof']    = $withdraw['photo'];
                //     }
                // } else {
                //     $usercashcier   = [];
                //     $dailyreportdata[$dayrep['id']]['withdraw'] = [];
                // }

                // Initial Cash
                $dailyreportdata[$dayrep['id']]['initialcash']      = $dayrep['initialcash'];

                // Total Cash In
                $dailyreportdata[$dayrep['id']]['totalcashin']      = $dayrep['totalcashin'];

                // Total Cash Out
                $dailyreportdata[$dayrep['id']]['totalcashout']     = $dayrep['totalcashout'];
            }
            // dd($dailyreportdata);

            // $lastreport             = end($dailyreports);
            // $firstreport            = $dailyreports[0];
            // if ($firstreport['dateclose'] === '0000-00-00 00:00:00') {
            //     $thefirst = date('Y-m-d H:i:s');
            // } else {
            //     $thefirst = $firstreport['dateclose'];
            // }

            // $cashs                  = $CashModel->foutdAll();
            // $payments               = $PaymentModel->findAll();
            // $transactions           = $TransactionModel->where('date <=', $thefirst)->where('date >=', $lastreport['dateopen'])->where('outletid', $this->data['outletPick'])->find();
            // $trxothers              = $TrxotherModel->where('date <=', $thefirst)->where('date >=', $lastreport['dateopen'])->where('outletid', $this->data['outletPick'])->find();

            // $trxid = array();
            // $memberid = array();
            // foreach ($transactions as $transaction) {
            //     $trxid[] = $transaction['id'];
            //     $memberid[] = $transaction['memberid'];
            // }

            // $outlets                = $OutletModel->findAll();
            // $users                  = $UserModel->findAll();

            // if (!empty($transactions)) {
            //     $trxdetails             = $TrxdetailModel->whereIn('transactionid', $trxid)->find();
            //     $trxpayments            = $TrxpaymentModel->whereIn('transactionid', $trxid)->find();
            //     $customers              = $MemberModel->find($memberid);

            //     $variantid = array();
            //     $bundleid = array();
            //     foreach ($trxdetails as $trxdetail) {
            //         $variantid[] = $trxdetail['variantid'];
            //         $bundleid[] = $trxdetail['bundleid'];
            //     }

            //     $bundles                = $BundleModel->find($bundleid);
            //     $bundets                = $BundledetailModel->whereIn('bundleid', $bundleid)->find();

            //     foreach ($bundets as $bundet) {
            //         $variantid[] = $bundet['variantid'];
            //     }

            //     $variants               = $VariantModel->find($variantid);

            //     $productid = array();
            //     foreach ($variants as $variant) {
            //         $productid[] = $variant['productid'];
            //     }

            //     $products               = $ProductModel->find($productid);

            //     // Get Cash Transaction
            //     $pettycash              = $CashModel->where('name', 'Petty Cash ' . $this->data['outletPick'])->first();
            //     $cashpayment            = $PaymentModel->where('outletid', $this->data['outletPick'])->where('name', 'Cash')->first();
            //     $cashtrx                = $TransactionModel->where('paymentid', $cashpayment['id'])->find();

            //     // Get Non Cash Transaction
            //     $noncash            = $CashModel->notLike('name', 'Petty Cash')->find();
            //     $noncashid          = array();
            //     foreach ($noncash as $nocash) {
            //         $noncashid[] = $nocash['id'];
            //     }
            //     $noncashpayments    = $PaymentModel->whereIn('cashid', $noncashid)->find();
            //     $noncashpaymentid   = array();
            //     foreach ($noncashpayments as $noncashpayment) {
            //         $noncashpaymentid[]     = $noncashpayment['id'];
            //     }
            //     $noncashtrx                 = $TransactionModel->where('outletid', $this->data['outletPick'])->whereIn('paymentid', $noncashpaymentid)->find();
            // } else {
            //     $trxdetails             = array();
            //     $trxpayments            = array();
            //     $customers              = array();
            //     $bundles                = array();
            //     $bundets                = array();
            //     $variants               = array();
            //     $products               = array();
            //     $pettycash              = array();
            //     $cashpayment            = array();
            //     $cashtrx                = array();
            //     $noncash                = array();
            //     $noncashpayments        = array();
            //     $noncashtrx             = array();
            // }

            // Parsing Data to View
            $data                       = $this->data;
            $data['title']              = lang('Global.dailyreport');
            $data['description']        = lang('Global.dailyreportListDesc');
            $data['dailyreports']       = $dailyreportdata;
            // $data['dailyreports']       = $dailyreports;
            // $data['cashpayment']        = $cashpayment;
            // $data['noncashpayments']    = $noncashpayments;
            // $data['cashtrx']            = $cashtrx;
            // $data['noncashtrx']         = $noncashtrx;
            // $data['cashs']              = $cashs;
            // $data['bundles']            = $bundles;
            // $data['bundets']            = $bundets;
            // $data['users']              = $users;
            // $data['transactions']       = $transactions;
            // $data['outlets']            = $outlets;
            // $data['payments']           = $payments;
            // $data['products']           = $products;
            // $data['variants']           = $variants;
            // $data['customers']          = $customers;
            // $data['trxothers']          = $trxothers;
            // $data['trxdetails']         = $trxdetails;
            // $data['trxpayments']        = $trxpayments;
            $data['pager']              = $DailyReportModel->pager;
            $data['startdate']          = strtotime($startdate);
            $data['enddate']            = strtotime($enddate);

            return view('Views/dailyreport', $data);
        // } else {
        //     return redirect()->to('');
        // }
    }

    public function open()
    {
        // Calling Models
        $OutletModel            = new OutletModel();
        $UserModel              = new UserModel();
        $DailyReportModel       = new DailyReportModel();
        $CashModel              = new CashModel();

        // Initialize
        $input                  = $this->request->getPost();

        // Populating Data
        $outlets                = $OutletModel->findAll();
        $users                  = $UserModel->findAll();

        $date                   = date_create();
        $tanggal                = date_format($date, 'Y-m-d H:i:s');

        $datadayrep = [
            'dateopen'      => $tanggal,
            'useridopen'    => $this->data['uid'],
            'outletid'      => $this->data['outletPick'],
            'initialcash'   => $input['initialcash'],
            'totalcashin'   => "0",
            'totalcashout'  => "0"
        ];
        $DailyReportModel->save($datadayrep);

        // Return
        return redirect()->back();
    }

    public function close()
    {
        // Calling Models
        $UserModel              = new UserModel();
        $DailyReportModel       = new DailyReportModel();

        // Initialize
        $input                  = $this->request->getPost();

        // Populating Data
        $users                  = $UserModel->findAll();

        // Creating Daily Report
        $today                  = date('Y-m-d') . ' 00:00:01';
        $dailyreport            = $DailyReportModel->where('outletid', $this->data['outletPick'])->where('dateopen >', $today)->first();
        $date                   = date_create();
        $tanggal                = date_format($date, 'Y-m-d H:i:s');

        $closedayrep = [
            'id'                => $dailyreport['id'],
            'dateclose'         => $tanggal,
            'useridclose'       => $this->data['uid'],
            'cashclose'         => $input['actualcash'],
            'noncashclose'      => $input['actualnoncash'],
        ];
        $DailyReportModel->save($closedayrep);

        // Return
        return redirect()->back();
    }
}
