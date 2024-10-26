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
use App\Models\DailyReportModel;

class DailyReport extends BaseController
{
    protected $data;
    protected $db, $builder;
    protected $auth;
    protected $config;
    
    public function index()
    {
        if ($this->data['outletPick'] != null) {
            $pager      = \Config\Services::pager();

            // LAST WORKING ON FINDING TOTAL PRODUCT SELL

            // Calling Models
            $TransactionModel   = new TransactionModel;
            $TrxdetailModel     = new TrxdetailModel;
            $TrxpaymentModel    = new TrxpaymentModel;
            $TrxotherModel      = new TrxotherModel;
            $ProductModel       = new ProductModel;
            $VariantModel       = new VariantModel;
            $BundleModel        = new BundleModel;
            $BundledetailModel  = new BundledetailModel;
            $PaymentModel       = new PaymentModel;
            $DebtModel          = new DebtModel;
            $UserModel          = new UserModel;
            $CashModel          = new CashModel;
            $OutletModel        = new OutletModel;
            $DailyReportModel   = new DailyReportModel;
            $MemberModel        = new MemberModel;

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

            $today                  = date('Y-m-d') . ' 00:00:01';
            // if (!empty($input)) {
            //     if ($startdate === $enddate) {
                    $dailyreports       = $DailyReportModel->orderby('dateopen', 'DESC')->where('dateopen >=', $startdate . " 00:00:00")->where('dateopen <=', $enddate . " 23:59:59")->where('outletid', $this->data['outletPick'])->paginate(20, 'dailyreport');
            //     } else {
            //         $dailyreports       = $DailyReportModel->orderBy('dateopen', 'DESC')->where('dateopen >=', $startdate . '00:00:00')->where('dateopen <=', $enddate . '23:59:59')->where('outletid', $this->data['outletPick'])->paginate(20, 'dailyreport');
            //     }
            // } else {
            //     $dailyreports           = $DailyReportModel->orderBy('dateopen', 'DESC')->where('outletid', $this->data['outletPick'])->paginate(20, 'dailyreport');
            // }

            $dailyreportdata    = [];
            $payments           = $PaymentModel->where('outletid', $this->data['outletPick'])->find();
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

                // Date Closed
                if ($dayrep['dateclose'] != '0000-00-00 00:00:00') {
                    $dailyreportdata[$dayrep['id']]['dateclose']    = date('l, d M Y, H:i:s', strtotime($dayrep['dateclose']));

                    // User Close Store
                    $userclose                                      = $UserModel->find($dayrep['useridclose']);
                    $dailyreportdata[$dayrep['id']]['userclose']    = $userclose->firstname.' '.$userclose->lastname;
                } else {
                    $dailyreportdata[$dayrep['id']]['dateclose']    = lang('Global.storeNotClosed');

                    // User Close Store
                    $dailyreportdata[$dayrep['id']]['userclose']    = lang('Global.storeNotClosed');
                }

                // Total Cash In
                $dailyreportdata[$dayrep['id']]['totalcashin']      = $dayrep['totalcashin'];

                // Total Cash Out
                $dailyreportdata[$dayrep['id']]['totalcashout']     = $dayrep['totalcashout'];

                // Actual Cash Close
                $dailyreportdata[$dayrep['id']]['cashclose']        = $dayrep['cashclose'];

                // Actual Non Cash Close
                $dailyreportdata[$dayrep['id']]['noncashclose']     = $dayrep['noncashclose'];

                // User Open Store
                $useropen                                           = $UserModel->find($dayrep['useridopen']);
                $dailyreportdata[$dayrep['id']]['useropen']         = $useropen->firstname.' '.$useropen->lastname;

                // Transaction Data
                $transactions       = $TransactionModel->where('date >=', $dayrep['dateopen'])->where('date <=', $dayrep['dateclose'])->where('outletid', $this->data['outletPick'])->find();
                
                $totalproductsell   = [];
                foreach ($transactions as $trx) {
                    // Product Seliing Data
                    $trxdetails     = $TrxdetailModel->where('transactionid', $trx['id'])->find();
                    
                    if (!empty($trxdetails)) {
                        foreach ($trxdetails as $trxdet) {
                            $variants       = $VariantModel->find($trxdet['variantid']);
                            
                            if (!empty($variants)) {
                                $products   = $ProductModel->find($variants['productid']);
                                
                                $dailyreportdata[$dayrep['id']]['productsell'][$products['id']]['name']             = $products['name'];
                                $dailyreportdata[$dayrep['id']]['productsell'][$products['id']]['qty'][]            = $trxdet['qty'];
                            } else {
                                $products   = [];
                                $dailyreportdata[$dayrep['id']]['productsell'][0]['name']                           = 'Kategori / Produk / Variant Terhapus';
                                $dailyreportdata[$dayrep['id']]['productsell'][0]['qty'][]                          = $trxdet['qty'];
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
                    $dailyreportdata[$dayrep['id']]['payments'][$trx['id']]['custname']            = $member;

                    // Transaction Date
                    $dailyreportdata[$dayrep['id']]['payments'][$trx['id']]['time']                = date('H:i:s', strtotime($trx['date']));

                    // // Payment Methods
                    // $trxpayments    = $TrxpaymentModel->where('transactionid', $trx['id'])->where('paymentid', $payment['id'])->find();
                    // $debtpayments   = $TrxpaymentModel->where('transactionid', $trx['id'])->where('paymentid', '0')->find();
                    // $dailyreportdata['payments'][$trx['id']]['custname']      = $payment['name'];
                    // if (!empty($trxpayments)) {
                    //     foreach ($trxpayments as $trxpayment) {
                    //         // $dailyreportdata['payments'][$payment['id']]['value'][]             = $trxpayment['value'];
                    //         $dailyreportdata['payments']['detail'][$trx['id']]['name']      = $payment['name'];
                    //         $dailyreportdata['payments']['detail'][$trx['id']]['value']     = $trxpayment['value'];
                    //     }
                    // }
                    // if (!empty($debtpayments)) {
                    //     foreach ($debtpayments as $debtpayment) {
                    //         // $dailyreportdata['payments'][0]['value'][]                          = $debtpayment['value'];
                    //         $dailyreportdata['payments']['detail'][$trx['id']]['name']                   = $payment['name'];
                    //         $dailyreportdata['payments']['detail'][$trx['id']]['value']                  = $debtpayment['value'];
                    //     }
                    // }
                }

                // Total Prodcuct Sell
                $dailyreportdata[$dayrep['id']]['totalproductsell']     = array_sum($totalproductsell);

                // // Payment Methods
                // foreach ($payments as $payment) {
                //     $dailyreportdata['payments'][$payment['id']]['name']    = $payment['name'];
                //     $dailyreportdata['payments'][0]['name']                 = 'Debt';
                    
                //     $trxtotal           = array();
                //     $trxvalue           = array();
                //     $debttotal          = array();
                //     $debtvalue          = array();
                //     if (!empty($transactions)) {
                //         foreach ($transactions as $trx) {
                //             $trxpayments    = $TrxpaymentModel->where('transactionid', $trx['id'])->where('paymentid', $payment['id'])->find();
                //             $debtpayments   = $TrxpaymentModel->where('transactionid', $trx['id'])->where('paymentid', '0')->find();

                //             $members        = $MemberModel->find($trx['memberid']);
                //             dd($members);
                //             $dailyreportdata['payments'][$trx['id']]['custname']      = $payment['name'];
                //             if (!empty($trxpayments)) {
                //                 foreach ($trxpayments as $trxpayment) {
                //                     // $dailyreportdata['payments'][$payment['id']]['value'][]             = $trxpayment['value'];
                //                     $dailyreportdata['payments']['detail'][$trx['id']]['name']      = $payment['name'];
                //                     $dailyreportdata['payments']['detail'][$trx['id']]['value']     = $trxpayment['value'];
                //                 }
                //             }
                //             if (!empty($debtpayments)) {
                //                 foreach ($debtpayments as $debtpayment) {
                //                     // $dailyreportdata['payments'][0]['value'][]                          = $debtpayment['value'];
                //                     $dailyreportdata['payments']['detail'][$trx['id']]['name']                   = $payment['name'];
                //                     $dailyreportdata['payments']['detail'][$trx['id']]['value']                  = $debtpayment['value'];
                //                 }
                //             }
                //         }
                //     } else {
                //         $trxpayments    = [];
                //         $debtpayments   = [];
                //         $trxtotal[]     = [];
                //         $trxvalue[]     = [];
                //         $debttotal[]    = [];
                //         $debtvalue[]    = [];
                //     }
                // }
            }
            // dd($dailyreportdata);

            // $lastreport             = end($dailyreports);
            // $firstreport            = $dailyreports[0];
            // if ($firstreport['dateclose'] === '0000-00-00 00:00:00') {
            //     $thefirst = date('Y-m-d H:i:s');
            // } else {
            //     $thefirst = $firstreport['dateclose'];
            // }

            // $cashs                  = $CashModel->findAll();
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
        } else {
            return redirect()->to('');
        }
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
