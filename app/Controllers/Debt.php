<?php

namespace App\Controllers;

use App\Models\BundledetailModel;
use App\Models\BundleModel;
use App\Models\BookingModel;
use App\Models\BookingdetailModel;
use App\Models\CashModel;
use App\Models\CashmovementModel;
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

class Debt extends BaseController
{
    protected $data;
    protected $db, $builder;
    protected $auth;
    protected $config;
    
    public function indextrx()
    {
        // Calling Models (only those used in this function)
        $BundleModel            = new BundleModel();
        $OutletModel            = new OutletModel();
        $UserModel              = new UserModel();
        $MemberModel            = new MemberModel();
        $PaymentModel           = new PaymentModel();
        $ProductModel           = new ProductModel();
        $VariantModel           = new VariantModel();
        $TransactionModel       = new TransactionModel();
        $TrxdetailModel         = new TrxdetailModel();
        $DebtModel              = new DebtModel();
        $DebtInsModel           = new DebtInsModel();
        $GconfigModel           = new GconfigModel();
        $DailyReportModel       = new DailyReportModel();

        $input  = $this->request->getGet('daterange');

        if (!empty($input)) {
            $daterange = explode(' - ', $input);
            $startdate = $daterange[0] . ' 00:00:00';
            $enddate   = $daterange[1] . ' 23:59:59';
        } else {
            $startdate  = date('Y-m-1') . ' 00:00:00';
            $enddate    = date('Y-m-t') . ' 23:59:59';
        }

        // Populating Data
        if ($this->data['outletPick'] === null) {
            $transactions = $TransactionModel->orderBy('date', 'DESC')->where('date >=', $startdate)->where('date <=', $enddate)->findAll();
        } else {
            $transactions = $TransactionModel->orderBy('date', 'DESC')->where('date >=', $startdate)->where('date <=', $enddate)->where('outletid', $this->data['outletPick'])->findAll();
        }

        // Logo Gconfig
        $gconfig    = $GconfigModel->first();
        if (($gconfig['logo'] != null) && ($gconfig['bizname'] != null)) {
            $bizlogo    = $gconfig['logo'];
            $bizname    = $gconfig['bizname'];
        } else {
            $bizlogo    = 'binary111-logo-icon.svg';
            $bizname    = 'PT. Kodebiner Teknologi Indonesia';
        }

        // --- Batch load all related data ---
        $trxIds = array_column($transactions, 'id');

        // Outlets
        $outletIds = array_unique(array_column($transactions, 'outletid'));
        $outletRows = !empty($outletIds) ? $OutletModel->find($outletIds) : [];
        $outletIndex = [];
        foreach ($outletRows as $o) {
            $outletIndex[$o['id']] = $o;
        }

        // Users
        $userIds = array_unique(array_column($transactions, 'userid'));
        $userRows = !empty($userIds) ? $UserModel->find($userIds) : [];
        $userIndex = [];
        foreach ($userRows as $u) {
            $userIndex[$u->id] = $u;
        }

        // Payments (filter out non-positive IDs like '0' or '-1')
        $paymentIds = array_unique(array_column($transactions, 'paymentid'));
        $validPaymentIds = array_filter($paymentIds, function ($id) { return is_numeric($id) && $id > 0; });
        $paymentRows = !empty($validPaymentIds) ? $PaymentModel->find($validPaymentIds) : [];
        $paymentIndex = [];
        foreach ($paymentRows as $p) {
            $paymentIndex[$p['id']] = $p;
        }

        // Members
        $memberIds = array_unique(array_column($transactions, 'memberid'));
        $validMemberIds = array_filter($memberIds, function ($id) { return is_numeric($id) && $id > 0; });
        $memberRows = !empty($validMemberIds) ? $MemberModel->find($validMemberIds) : [];
        $memberIndex = [];
        foreach ($memberRows as $m) {
            $memberIndex[$m['id']] = $m;
        }

        // Transaction details (batched)
        $allDetails = !empty($trxIds) ? $TrxdetailModel->whereIn('transactionid', $trxIds)->find() : [];
        $detailByTrx = [];
        foreach ($allDetails as $d) {
            $detailByTrx[$d['transactionid']][] = $d;
        }

        // Variants & Products from all details
        $allVariantIds = [];
        $allBundleIds  = [];
        foreach ($allDetails as $d) {
            if ($d['variantid'] != '0') $allVariantIds[] = $d['variantid'];
            if ($d['bundleid'] != '0') $allBundleIds[] = $d['bundleid'];
        }
        $allVariantIds = array_unique($allVariantIds);
        $allBundleIds = array_unique($allBundleIds);

        $variantRows = !empty($allVariantIds) ? $VariantModel->find($allVariantIds) : [];
        $variantIndex = [];
        foreach ($variantRows as $v) {
            $variantIndex[$v['id']] = $v;
        }

        $productIds = array_unique(array_column($variantRows, 'productid'));
        $productRows = !empty($productIds) ? $ProductModel->find($productIds) : [];
        $productIndex = [];
        foreach ($productRows as $pr) {
            $productIndex[$pr['id']] = $pr;
        }

        $bundleRows = !empty($allBundleIds) ? $BundleModel->find($allBundleIds) : [];
        $bundleIndex = [];
        foreach ($bundleRows as $b) {
            $bundleIndex[$b['id']] = $b;
        }

        // Debts (batched)
        $debtRows = !empty($trxIds) ? $DebtModel->whereIn('transactionid', $trxIds)->find() : [];

        // Debt installments (batched) — mapped by transactionid for O(1) lookup
        $allDebtIds = !empty($debtRows) ? array_unique(array_column($debtRows, 'id')) : [];
        $debtInstRows = !empty($allDebtIds) ? $DebtInsModel->whereIn('debtid', $allDebtIds)->find() : [];
        $debtInstByTrx = [];
        foreach ($debtInstRows as $di) {
            $debtInstByTrx[$di['transactionid']][] = $di;
        }

        // Daily reports (batched by outlet, filtered 1 month before start)
        $reportStart    = date('Y-m-d', strtotime('-1 month', strtotime($startdate)));
        $endDate        = date('Y-m-d', strtotime($enddate));
        $dailyReports = !empty($outletIds) ? $DailyReportModel
            ->whereIn('outletid', $outletIds)
            ->where('dateopen >=', $reportStart . ' 00:00:00')
            ->where('dateopen <=', $endDate . ' 23:59:59')
            ->find() : [];
        // Index daily reports by outletid for faster lookup
        $dailyReportByOutlet = [];
        foreach ($dailyReports as $dr) {
            $dailyReportByOutlet[$dr['outletid']][] = $dr;
        }

        // --- Build transaction data using indexed arrays ---
        $transactiondata = [];
        foreach ($transactions as $trx) {
            $trxId  = $trx['id'];
            $outletId   = $trx['outletid'];
            $userId     = $trx['userid'];

            // Outlet
            $outletRow   = $outletIndex[$outletId] ?? null;
            $outletname  = $outletRow['name'] ?? '';
            $bizaddress  = $outletRow['address'] ?? '';
            $bizinstagram = $outletRow['instagram'] ?? '';
            $bizphone   = $outletRow['phone'] ?? '';

            // Cashier
            $cashier   = $userIndex[$userId] ?? null;
            $cashierName = $cashier ? $cashier->firstname . ' ' . $cashier->lastname : '';

            // Payment Method
            $payRow = $paymentIndex[$trx['paymentid']] ?? null;
            if ($payRow) {
                $paymentmethod = $payRow['name'];
            } else {
                if (($trx['amountpaid'] == '0') && ($trx['paymentid'] == "0")) {
                    $paymentmethod = lang('Global.debt');
                } elseif ($trx['paymentid'] == "-1") {
                    $paymentmethod = lang('Global.redeemPoint');
                } elseif (($trx['amountpaid'] != '0') && ($trx['paymentid'] == "0")) {
                    $paymentmethod = lang('Global.splitbill');
                } else {
                    $paymentmethod = '';
                }
            }

            // Member
            $memberRow   = $memberIndex[$trx['memberid']] ?? null;
            $membername  = $memberRow['name'] ?? '';
            $memberphone = $memberRow['phone'] ?? '';
            $memberpoin  = $memberRow['poin'] ?? '';

            // Debt Data — aggregate installments directly (no per-debt loop)
            $trxDebtInsts   = $debtInstByTrx[$trxId] ?? [];
            $totaldebtin    = !empty($trxDebtInsts) ? array_sum(array_column($trxDebtInsts, 'qty')) : 0;
            $statustrx      = ((int)$trx['amountpaid'] + (int)$totaldebtin) - (int)$trx['value'];

            if (($statustrx != 0) && ($statustrx < 0)) {
                $paidstatus = '<div class="uk-text-danger" style="border-style: solid; border-color: #f0506e;">' . lang('Global.notpaid') . '</div>';
            } else {
                $paidstatus = '<div class="uk-text-success" style="border-style: solid; border-color: #32d296;">' . lang('Global.paid') . '</div>';
            }

            // Transaction Data
            $transactiondata[$trxId]['id']              = $trxId;
            $transactiondata[$trxId]['date']            = $trx['date'];
            $transactiondata[$trxId]['outlet']          = $outletname;
            $transactiondata[$trxId]['cashier']         = $cashierName;
            $transactiondata[$trxId]['payment']         = $paymentmethod;
            $transactiondata[$trxId]['value']           = $trx['value'];
            $transactiondata[$trxId]['amountpaid']      = $trx['amountpaid'];
            $transactiondata[$trxId]['paidstatus']      = $paidstatus;
            $transactiondata[$trxId]['bizlogo']         = $bizlogo;
            $transactiondata[$trxId]['bizname']         = $bizname;
            $transactiondata[$trxId]['bizaddress']      = $bizaddress;
            $transactiondata[$trxId]['bizinstagram']    = $bizinstagram;
            $transactiondata[$trxId]['bizphone']        = $bizphone;
            $transactiondata[$trxId]['invoice']         = (strtotime($trx['date']));
            $transactiondata[$trxId]['trxdiscount']     = $trx['discvalue'];
            $transactiondata[$trxId]['memberid']        = $trx['memberid'];
            $transactiondata[$trxId]['memberdisc']      = $trx['memberdisc'];
            $transactiondata[$trxId]['pointused']       = $trx['pointused'];
            $transactiondata[$trxId]['membername']      = $membername;
            $transactiondata[$trxId]['memberphone']     = $memberphone;
            $transactiondata[$trxId]['memberpoin']      = $memberpoin;

            // Check if transaction was made outside store hours
            $trxDate = date('Y-m-d', strtotime($trx['date']));
            $outsidehours = true;
            $outletReports = $dailyReportByOutlet[$outletId] ?? [];
            foreach ($outletReports as $dr) {
                if ($dr['dateopen'] >= $trxDate . ' 00:00:00'
                    && $dr['dateopen'] <= $trx['date']
                    && ($dr['dateclose'] >= $trx['date'] || $dr['dateclose'] == '0000-00-00 00:00:00')) {
                    $outsidehours = false;
                    break;
                }
            }
            $transactiondata[$trxId]['outsidehours'] = $outsidehours;

            // Transaction Detail Data
            $subtotal = [];
            $sumDiscVar = 0;
            $sumGlobalDisc = 0;
            $sumMemberDisc = 0;
            $trxdetails = $detailByTrx[$trxId] ?? [];
            if (!empty($trxdetails)) {
                foreach ($trxdetails as $trxdet) {
                    $qty        = (int)$trxdet['qty'];
                    $safeQty    = max(1, $qty);
                    $value      = (float)$trxdet['value'];
                    $discvar    = (int)$trxdet['discvar'];
                    $globaldisc = (int)$trxdet['globaldisc'];
                    $memberdisc = (int)$trxdet['memberdisc'];
                    $subtotal[] = ($qty * $value);
                    $sumDiscVar += $discvar;
                    $sumGlobalDisc += $globaldisc;
                    $sumMemberDisc += $memberdisc;

                    // Variant Data
                    if (($trxdet['variantid'] != '0') && ($trxdet['bundleid'] == '0')) {
                        $variant   = $variantIndex[$trxdet['variantid']] ?? null;

                        if ($variant) {
                            $product = $productIndex[$variant['productid']] ?? null;

                            if ($product) {
                                $detailValue = $value + ($discvar / $safeQty) + ($globaldisc / $safeQty) + ($memberdisc / $safeQty);
                                $transactiondata[$trxId]['detail'][$trxdet['id']]['name']           = $product['name'] . ' - ' . $variant['name'];
                                $transactiondata[$trxId]['detail'][$trxdet['id']]['qty']            = $qty;
                                $transactiondata[$trxId]['detail'][$trxdet['id']]['value']          = $detailValue;
                                $transactiondata[$trxId]['detail'][$trxdet['id']]['total']          = $detailValue * $qty;
                                $transactiondata[$trxId]['detail'][$trxdet['id']]['discitem']       = $discvar / $safeQty;
                                $transactiondata[$trxId]['detail'][$trxdet['id']]['discvar']        = $discvar;
                                $transactiondata[$trxId]['detail'][$trxdet['id']]['globaldiscitem'] = $globaldisc / $safeQty;
                                $transactiondata[$trxId]['detail'][$trxdet['id']]['globaldisc']     = $globaldisc;
                                $transactiondata[$trxId]['detail'][$trxdet['id']]['memberdiscitem'] = $memberdisc / $safeQty;
                                $transactiondata[$trxId]['detail'][$trxdet['id']]['memberdisc']     = $memberdisc;
                            } else {
                                $detailValue = $value + ($discvar / $safeQty) + ($globaldisc / $safeQty) + ($memberdisc / $safeQty);
                                $transactiondata[$trxId]['detail'][$trxdet['id']]['name']           = 'Kategori / Produk / Variant Terhapus';
                                $transactiondata[$trxId]['detail'][$trxdet['id']]['qty']            = $qty;
                                $transactiondata[$trxId]['detail'][$trxdet['id']]['value']          = $detailValue;
                                $transactiondata[$trxId]['detail'][$trxdet['id']]['total']          = $detailValue * $qty;
                                $transactiondata[$trxId]['detail'][$trxdet['id']]['discitem']       = $discvar / $safeQty;
                                $transactiondata[$trxId]['detail'][$trxdet['id']]['discvar']        = $discvar;
                                $transactiondata[$trxId]['detail'][$trxdet['id']]['globaldiscitem'] = $globaldisc / $safeQty;
                                $transactiondata[$trxId]['detail'][$trxdet['id']]['globaldisc']     = $globaldisc;
                                $transactiondata[$trxId]['detail'][$trxdet['id']]['memberdiscitem'] = $memberdisc / $safeQty;
                                $transactiondata[$trxId]['detail'][$trxdet['id']]['memberdisc']     = $memberdisc;
                            }
                        } else {
                            $detailValue = $value + ($discvar / $safeQty) + ($globaldisc / $safeQty) + ($memberdisc / $safeQty);
                            $transactiondata[$trxId]['detail'][$trxdet['id']]['name']               = 'Kategori / Produk / Variant Terhapus';
                            $transactiondata[$trxId]['detail'][$trxdet['id']]['qty']                = $qty;
                            $transactiondata[$trxId]['detail'][$trxdet['id']]['value']              = $detailValue;
                            $transactiondata[$trxId]['detail'][$trxdet['id']]['total']              = $detailValue * $qty;
                            $transactiondata[$trxId]['detail'][$trxdet['id']]['discitem']           = $discvar / $safeQty;
                            $transactiondata[$trxId]['detail'][$trxdet['id']]['discvar']            = $discvar;
                            $transactiondata[$trxId]['detail'][$trxdet['id']]['globaldiscitem']     = $globaldisc / $safeQty;
                            $transactiondata[$trxId]['detail'][$trxdet['id']]['globaldisc']         = $globaldisc;
                            $transactiondata[$trxId]['detail'][$trxdet['id']]['memberdiscitem']     = $memberdisc / $safeQty;
                            $transactiondata[$trxId]['detail'][$trxdet['id']]['memberdisc']         = $memberdisc;
                        }
                    }

                    // Bundle Data
                    if (($trxdet['variantid'] == '0') && ($trxdet['bundleid'] != '0')) {
                        $bundle = $bundleIndex[$trxdet['bundleid']] ?? null;

                        if ($bundle) {
                            $detValue = $value + ($globaldisc / $safeQty) + ($memberdisc / $safeQty);
                            $transactiondata[$trxId]['detail'][$trxdet['id']]['name']               = $bundle['name'];
                            $transactiondata[$trxId]['detail'][$trxdet['id']]['qty']                = $qty;
                            $transactiondata[$trxId]['detail'][$trxdet['id']]['value']              = $detValue;
                            $transactiondata[$trxId]['detail'][$trxdet['id']]['total']              = $detValue * $qty;
                            $transactiondata[$trxId]['detail'][$trxdet['id']]['discitem']           = $discvar / $safeQty;
                            $transactiondata[$trxId]['detail'][$trxdet['id']]['discvar']            = $discvar;
                            $transactiondata[$trxId]['detail'][$trxdet['id']]['globaldiscitem']     = $globaldisc / $safeQty;
                            $transactiondata[$trxId]['detail'][$trxdet['id']]['globaldisc']         = $globaldisc;
                            $transactiondata[$trxId]['detail'][$trxdet['id']]['memberdiscitem']     = $memberdisc / $safeQty;
                            $transactiondata[$trxId]['detail'][$trxdet['id']]['memberdisc']         = $memberdisc;
                        } else {
                            $detValue = $value + ($globaldisc / $safeQty) + ($memberdisc / $safeQty);
                            $transactiondata[$trxId]['detail'][$trxdet['id']]['name']               = 'Bundle Terhapus';
                            $transactiondata[$trxId]['detail'][$trxdet['id']]['qty']                = $qty;
                            $transactiondata[$trxId]['detail'][$trxdet['id']]['value']              = $detValue;
                            $transactiondata[$trxId]['detail'][$trxdet['id']]['total']              = $detValue * $qty;
                            $transactiondata[$trxId]['detail'][$trxdet['id']]['discitem']           = $discvar / $safeQty;
                            $transactiondata[$trxId]['detail'][$trxdet['id']]['discvar']            = $discvar;
                            $transactiondata[$trxId]['detail'][$trxdet['id']]['globaldiscitem']     = $globaldisc / $safeQty;
                            $transactiondata[$trxId]['detail'][$trxdet['id']]['globaldisc']         = $globaldisc;
                            $transactiondata[$trxId]['detail'][$trxdet['id']]['memberdiscitem']     = $memberdisc / $safeQty;
                            $transactiondata[$trxId]['detail'][$trxdet['id']]['memberdisc']         = $memberdisc;
                        }
                    }
                }
            } else {
                $transactiondata[$trxId]['detail']  = [];
            }

            $transactiondata[$trxId]['totaldetailvalue']    = array_sum($subtotal);
            $transactiondata[$trxId]['discvar_total']       = $sumDiscVar;
            $transactiondata[$trxId]['globaldisc_total']    = $sumGlobalDisc;
            $transactiondata[$trxId]['memberdisc_total']    = $sumMemberDisc;
            $transactiondata[$trxId]['has_discvar']         = $sumDiscVar > 0;
            $transactiondata[$trxId]['has_globaldisc']      = $sumGlobalDisc > 0;
            $transactiondata[$trxId]['has_memberdisc']      = $sumMemberDisc > 0;

            // Pre-formatted date (avoid strtotime+date in view)
            $transactiondata[$trxId]['date_formatted']      = date('l, d M Y, H:i:s', strtotime($trx['date']));

            // Pre-compute poin earned (avoids computation in view)
            $trxValue = (int)$trx['value'];
            if (!empty($gconfig['poinorder']) && $gconfig['poinorder'] != '0') {
                $pointearn = floor($trxValue / (int)$gconfig['poinorder']) * (int)$gconfig['poinvalue'];
            } else {
                $pointearn = $trxValue * (int)$gconfig['poinvalue'];
            }
            $transactiondata[$trxId]['pointearn'] = $pointearn;
        }

        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.trxHistory');
        $data['description']    = lang('Global.trxHistoryListDesc');
        $data['transactions']   = $transactiondata;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);
        $data['can_refund']     = in_groups('owner') && ($this->data['outletPick'] != null);

        return view('Views/trxhistory', $data);
    }

    public function indexdebt()
    {
        $pager      = \Config\Services::pager();

        // Calling Models
        $OutletModel            = new OutletModel();
        $MemberModel            = new MemberModel();
        $PaymentModel           = new PaymentModel();
        $TransactionModel       = new TransactionModel();
        $TrxpaymentModel        = new TrxpaymentModel();
        $DebtModel              = new DebtModel();
        $DailyReportModel       = new DailyReportModel();

        // Populating Data
        // $outlets                = $OutletModel->findAll();
        $payments               = $PaymentModel->findAll();
        $today                  = date('Y-m-d') . ' 00:00:01';
        $dailyreport            = $DailyReportModel->where('dateopen >', $today)->where('outletid', $this->data['outletPick'])->first();

        // $input = $this->request->getGet('daterange');
        // if (!empty($input)) {
        //     $daterange = explode(' - ', $input);
        //     $startdate = $daterange[0];
        //     $enddate = $daterange[1];
        // } else {
        //     // $startdate  = date('Y-m-1' . ' 00:00:00');
        //     // $enddate    = date('Y-m-t' . ' 23:59:59');
        //     $startdate  = date('2023-01-01');
        //     $enddate    = date('Y-m-t');
        // }

        // Populating Data
        // if (!empty($input)) {
            // if ($startdate === $enddate) {
            //     $debts       = $DebtModel->orderby('deadline', 'DESC')->where('value !=', '0')->where('deadline', $startdate . ' 00:00:00')->where('deadline <=', $enddate . ' 23:59:59')->paginate(20, 'debt');
            // } else {
                // $debts = $DebtModel->orderBy('deadline', 'DESC')->where('value !=', '0')->where('deadline >=', $startdate)->where('deadline <=', $enddate)->paginate(30, 'debt');
                // $debts = $DebtModel->where('value !=', '0')->where('deadline <=', $enddate)->find();
            // }
        // } else {
            // $debts = $DebtModel->where('value !=', '0')->paginate(50, 'debt');
            // $debts = $DebtModel->where('value !=', '0')->find();
        // }

        if ($this->data['outletPick'] === null) {
            $transactions   = $TransactionModel->where('paymentid', '0')->findAll();
            // $payments       = $PaymentModel->findAll();
        } else {
            $transactions   = $TransactionModel->where('paymentid', '0')->where('outletid', $this->data['outletPick'])->find();
            // $payments       = $PaymentModel->where('outletid', $this->data['outletPick'])->find();
        }

        $debtdata   = [];
        $debttotal  = [];
        foreach ($transactions as $trx) {
            $trxpayments    = $TrxpaymentModel->where('paymentid', '0')->where('transactionid', $trx['id'])->find();
            $outlet         = $OutletModel->find($trx['outletid']);
            if (!empty($trxpayments)) {
                foreach ($trxpayments as $trxpay) {
                    $debts          = $DebtModel->where('value !=', '0')->where('transactionid', $trxpay['transactionid'])->find();
                    if (!empty($debts)) {
                        foreach ($debts as $debt) {
                            $members        = $MemberModel->find($debt['memberid']);
                            $debttotal[]    = $debt['value'];
            
                            $debtdata[$trx['id']]['id']         = $debt['id'];
                            $debtdata[$trx['id']]['name']       = $members['name'].' - '.$members['phone'];
                            $debtdata[$trx['id']]['outlet']     = $outlet['name'];
                            $debtdata[$trx['id']]['value']      = $debt['value'];
                            $debtdata[$trx['id']]['trxdate']    = $trx['date'];
                            $debtdata[$trx['id']]['deadline']   = $debt['deadline'];
                        }
                    }
                }
            }
        }
        // foreach ($debts as $debt) {
        //     $transaction    = $TransactionModel->find($debt['transactionid']);
        //     $members        = $MemberModel->find($debt['memberid']);

        //     if (!empty($transaction)) {
        //         $debttotal[]    = $debt['value'];
        //         $outlet         = $OutletModel->find($transaction['outletid']);

        //         $debtdata[$transaction['id']]['id']         = $debt['id'];
        //         $debtdata[$transaction['id']]['name']       = $members['name'].' - '.$members['phone'];
        //         $debtdata[$transaction['id']]['outlet']     = $outlet['name'];
        //         $debtdata[$transaction['id']]['value']      = $debt['value'];
        //         $debtdata[$transaction['id']]['trxdate']    = $transaction['date'];
        //         $debtdata[$transaction['id']]['deadline']   = $debt['deadline'];
        //     }
        // }
        array_multisort(array_column($debtdata, 'trxdate'), SORT_DESC, $debtdata);
        $totaldebt  = array_sum($debttotal);

        $page       = (int) ($this->request->getGet('page') ?? 1);
        $perPage    = 20;
        $total      = count($debtdata);

        // $trxid      = array();
        // $memberid   = array();
        // $debtlist   = array();
        // foreach ($debts as $debt) {
        //     $trxid[]    = $debt['transactionid'];
        //     $memberid[] = $debt['memberid'];
        //     $debtlist[] = $debt['value'];
        // }

        // $totaldebt  = array_sum($debtlist);

        // if (!empty($trxid)) {
        //     $transactions           = $TransactionModel->find($trxid);
        // } else {
        //     $transactions           = array();
        // }

        // if (!empty($memberid)) {
        //     $customers              = $MemberModel->find($memberid);
        // } else {
        //     $customers              = array();
        // }

        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.debt');
        $data['description']    = lang('Global.debtListDesc');
        // $data['transactions']   = $transactions;
        // $data['outlets']        = $outlets;
        // $data['customers']      = $customers;
        // $data['debts']          = $debts;
        $data['dailyreport']    = $dailyreport;
        $data['debts']          = array_slice($debtdata, ($page*20)-20, $page*20);
        $data['payments']       = $payments;
        $data['totaldebt']      = $totaldebt;
        // $data['startdate']      = strtotime($startdate);
        // $data['enddate']        = strtotime($enddate);
        // $data['pager']          = $DebtModel->pager;
        $data['pager_links']    = $pager->makeLinks($page, $perPage, $total, 'front_full');

        return view('Views/debt', $data);
    }

    public function paydebt($id)
    {
        // Calling Models
        $DebtModel              = new DebtModel();
        $DebtInsModel           = new DebtInsModel();
        $CashModel              = new CashModel();
        $PaymentModel           = new PaymentModel();

        // Populating Data
        $input                  = $this->request->getPost();
        $debts                  = $DebtModel->find($id);

        // Date Time Stamp
        $date                   = date_create();
        $tanggal                = date_format($date, 'Y-m-d H:i:s');

        // Save Data Debt
        if ($debts['value'] - $input['value'] != "0") {
            $data = [
                'id'            => $id,
                'value'         => $debts['value'] - $input['value'],
                // 'deadline'      => $input['duedate' . $id],
            ];
        } else {
            $data = [
                'id'            => $id,
                'value'         => $debts['value'] - $input['value'],
                'deadline'      => NULL,
            ];
        }
        $DebtModel->save($data);

        // Debt Installment
        $debtins = [
            'debtid'        => $id,
            'transactionid' => $debts['transactionid'],
            'userid'        => $this->data['uid'],
            'outletid'      => $this->data['outletPick'],
            'paymentid'     => $input['payment'],
            'date'          => $tanggal,
            'qty'           => $input['value'],
        ];
        $DebtInsModel->save($debtins);

        // Input Value to cash
        $payments   = $PaymentModel->find($input['payment']);
        $cash       = $CashModel->find($payments['cashid']);
        $wallet     = [
            'id'    => $cash['id'],
            'qty'   => (int)$cash['qty'] + (int)$input['value'],
        ];
        $CashModel->save($wallet);

        // Return
        return redirect()->back()->with('massage', lang('global.saved'));
    }

    public function refundins($id)
    {
        // Calling Models
        $DebtModel              = new DebtModel();
        $DebtInsModel           = new DebtInsModel();
        $CashModel              = new CashModel();
        $PaymentModel           = new PaymentModel();

        // Populating Data
        $debtinst               = $DebtInsModel->find($id);

        if (!empty($debtinst)) {
            // Refund Debt
            $debt               = $DebtModel->find($debtinst['debtid']);
            if (!empty($debt)) {
                $datadebt       = [
                    'id'        => $debtinst['debtid'],
                    'value'     => (Int)$debt['value'] + (Int)$debtinst['qty'],
                ];
                $DebtModel->save($datadebt);
            }

            // Refund Cash
            $payment            = $PaymentModel->find($debtinst['paymentid']);
            if (!empty($payment)) {
                $cash           = $CashModel->find($payment['cashid']);
                if (!empty($cash)) {
                    $datawallet = [
                        'id'    => $cash['id'],
                        'qty'   => (int)$cash['qty'] - (int)$debtinst['qty'],
                    ];
                    $CashModel->save($datawallet);
                }
            }
        }

        // Delete Debt Installment Data
        $DebtInsModel->delete($id);

        // Return
        return redirect()->back()->with('massage', lang('global.saved'));
    }

    public function indexdebtins()
    {
        $pager      = \Config\Services::pager();

        // Calling Model
        $TrxotherModel      = new TrxotherModel();
        $TransactionModel   = new TransactionModel();
        $MemberModel        = new MemberModel();
        $DebtInsModel       = new DebtInsModel();
        $OutletModel        = new OutletModel();

        // Find Data
        $input = $this->request->getGet('daterange');

        if (!empty($input)) {
            $daterange = explode(' - ', $input);
            $startdate = $daterange[0];
            $enddate = $daterange[1];
        } else {
            $startdate  = date('Y-m-1' . ' 00:00:00');
            $enddate    = date('Y-m-t' . ' 23:59:59');
        }

        // $outlets            = $OutletModel->findAll();
        if ($this->data['outletPick'] === null) {
            // $trxothers      = $TrxotherModel->orderBy('id', 'DESC')->like('description', 'Debt')->paginate(20, 'debtpay');

            // if (!empty($input)) {
            //     if ($startdate === $enddate) {
                    $trxothers          = $TrxotherModel->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->like('description', 'Debt')->find();
                    $debtinstallments   = $DebtInsModel->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->find();
            //     } else {
            //         $trxothers      = $TrxotherModel->where('date >=', $startdate)->where('date <=', $enddate)->orderBy('id', 'DESC')->like('description', 'Debt')->paginate(20, 'debtpay');
            //     }
            // }
        } else {
            // $trxothers      = $TrxotherModel->orderBy('id', 'DESC')->like('description', 'Debt')->where('outletid', $this->data['outletPick'])->paginate(20, 'debtpay');

            // if (!empty($input)) {
            //     if ($startdate === $enddate) {
                    $trxothers          = $TrxotherModel->where('outletid', $this->data['outletPick'])->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->like('description', 'Debt')->find();
                    $debtinstallments   = $DebtInsModel->where('outletid', $this->data['outletPick'])->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->find();
            //     } else {
            //         $trxothers      = $TrxotherModel->where('date >=', $startdate)->where('date <=', $enddate)->orderBy('id', 'DESC')->like('description', 'Debt')->where('outletid', $this->data['outletPick'])->paginate(20, 'debtpay');
            //     }
            // }
        }
        $debtinsdata    = [];
        foreach ($trxothers as $trxot) {
            $outlet                                         = $OutletModel->find($trxot['outletid']);
            $debtinsdata[$trxot['date']]['id']              = $trxot['id'];
            $debtinsdata[$trxot['date']]['date']            = $trxot['date'];
            $debtinsdata[$trxot['date']]['outlet']          = $outlet['name'];
            $debtinsdata[$trxot['date']]['description']     = $trxot['description'];
            $debtinsdata[$trxot['date']]['qty']             = $trxot['qty'];
        }

        foreach ($debtinstallments as $debt) {
            $transaction    = $TransactionModel->find($debt['transactionid']);
            $members        = $MemberModel->find($transaction['memberid']);

            if (!empty($transaction)) {
                $outlets                                        = $OutletModel->find($debt['outletid']);
                $debtinsdata[$debt['date']]['id']               = $debt['id'];
                $debtinsdata[$debt['date']]['date']             = $debt['date'];
                $debtinsdata[$debt['date']]['description']      = 'Debt - '.$members['name'].' / '.$members['phone'];
                $debtinsdata[$debt['date']]['outlet']           = $outlets['name'];
                $debtinsdata[$debt['date']]['qty']              = $debt['qty'];
            }
        }
        array_multisort(array_column($debtinsdata, 'date'), SORT_DESC, $debtinsdata);

        $page       = (int) ($this->request->getGet('page') ?? 1);
        $perPage    = 20;
        $total      = count($debtinsdata);

        // Parsing data to view
        $data                       = $this->data;
        $data['title']              = lang('Global.debtInstallments');
        $data['description']        = lang('Global.debtInstallmentsListDesc');
        $data['trxothers']          = array_slice($debtinsdata, ($page*20)-20, $page*20);
        // $data['trxothers']          = $debtinsdata;
        // $data['trxothers']          = $trxothers;
        // $data['outlets']            = $outlets;
        $data['pager_links']        = $pager->makeLinks($page, $perPage, $total, 'front_full');
        $data['startdate']          = strtotime($startdate);
        $data['enddate']            = strtotime($enddate);
        // $data['pager']              = $TrxotherModel->pager;

        return view('Views/debtpay', $data);
    }

    public function indextopup()
    {
        $pager      = \Config\Services::pager();

        // Calling Models
        $OutletModel            = new OutletModel;
        $TrxotherModel          = new TrxotherModel;

        // Populating Data
        $input = $this->request->getGet();

        if (!empty($input['daterange'])) {
            $daterange = explode(' - ', $input['daterange']);
            $startdate = $daterange[0];
            $enddate = $daterange[1];
        } else {
            $startdate  = date('Y-m-1' . ' 00:00:00');
            $enddate    = date('Y-m-t' . ' 23:59:59');
        }

        if ($this->data['outletPick'] === null) {
            // $trxothers      = $TrxotherModel->orderBy('id', 'DESC')->like('description', 'Top Up')->paginate(20, 'topup');
            // if (!empty($input['daterange'])) {
            //     if ($startdate === $enddate) {
                    $trxothers      = $TrxotherModel->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->orderBy('id', 'DESC')->like('description', 'Top Up')->paginate(20, 'topup');
            //     } else {
            //         $trxothers      = $TrxotherModel->where('date >=', $startdate)->where('date <=', $enddate)->orderBy('id', 'DESC')->like('description', 'Top Up')->paginate(20, 'topup');
            //     }
            // }
        } else {
            // $trxothers      = $TrxotherModel->where('outletid', $this->data['outletPick'])->orderBy('id', 'DESC')->like('description', 'Top Up')->paginate(20, 'topup');

            // if (!empty($input['daterange'])) {
            //     if ($startdate === $enddate) {
                    $trxothers      = $TrxotherModel->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->orderBy('id', 'DESC')->like('description', 'Top Up')->where('outletid', $this->data['outletPick'])->paginate(20, 'topup');
            //     } else {
            //         $trxothers      = $TrxotherModel->where('date >=', $startdate)->where('date <=', $enddate)->orderBy('id', 'DESC')->like('description', 'Top Up')->where('outletid', $this->data['outletPick'])->paginate(20, 'topup');
            //     }
            // }
        }

        $outlets                = $OutletModel->findAll();

        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.topup');
        $data['description']    = lang('Global.topupListDesc');
        $data['outlets']        = $outlets;
        $data['trxothers']      = $trxothers;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);
        $data['pager']          = $TrxotherModel->pager;

        return view('Views/topup', $data);
    }

    public function refund($id)
    {
        // Calling Models
        $TransactionModel       = new TransactionModel();
        $TrxdetailModel         = new TrxdetailModel();
        $BundledetailModel      = new BundledetailModel();
        $StockModel             = new StockModel();
        $TrxpaymentModel        = new TrxpaymentModel();
        $PaymentModel           = new PaymentModel();
        $DebtModel              = new DebtModel();
        $CashModel              = new CashModel();
        $MemberModel            = new MemberModel();
        $GconfigModel           = new GconfigModel();

        // Populating Data
        $transactions           = $TransactionModel->find($id);
        $trxdetails             = $TrxdetailModel->where('transactionid', $id)->find();
        if (!empty($trxdetails)) {
            foreach ($trxdetails as $trxdet) {
                // Refund Variant
                $stock          = $StockModel->where('outletid', $transactions['outletid'])->where('variantid', $trxdet['variantid'])->first();
                if (!empty($stock)) {
                    $saleVarStock = [
                        'id'        => $stock['id'],
                        'qty'       => (int)$stock['qty'] + (int)$trxdet['qty'],
                    ];
                    $StockModel->save($saleVarStock);
                }

                // Refund Bundle
                $bundledetail = $BundledetailModel->where('bundleid', $trxdet['bundleid'])->find();
                if (!empty($bundledetail)) {
                    foreach ($bundledetail as $bundet) {
                        $bunstock = $StockModel->where('outletid', $transactions['outletid'])->where('variantid', $bundet['variantid'])->first();
                        if (!empty($bunstock)) {
                            $saleBunStock = [
                                'id'        => $bunstock['id'],
                                'qty'       => (int)$bunstock['qty'] + (int)$trxdet['qty'],
                            ];
                            $StockModel->save($saleBunStock);
                        }
                    }
                }
            }
        }

        // Refund Member Poin
        $member         = $MemberModel->find($transactions['memberid']);
        $Gconf          = $GconfigModel->first();
        $minimumtrx     = $Gconf['poinorder'];
        $poinvalue      = $Gconf['poinvalue'];

        if (!empty($member)) {
            if ($transactions['value'] >= $minimumtrx) {
                if ($minimumtrx != '0') {
                    $value      = (int)$transactions['value'] / (int)$minimumtrx;
                } else {
                    $value      = 0;
                }
                $result         = floor($value);
                $poinresult     = (int)$result * (int)$poinvalue;
            } else {
                $poinresult     = 0;
            }

            if ($transactions['pointused'] != '0') {
                $point = [
                    'id'    => $member['id'],
                    'poin'  => ((int)$member['poin'] + (int)$transactions['pointused']) - $poinresult,
                    'trx'   => (int)$member['trx'] - 1,
                ];
                $MemberModel->save($point);
            } else {
                $point = [
                    'id'    => $member['id'],
                    'poin'  => (int)$member['poin'] - $poinresult,
                    'trx'   => (int)$member['trx'] - 1,
                ];
                $MemberModel->save($point);
            }
        }

        // Refund Payment
        $trxpayments    = $TrxpaymentModel->where('transactionid', $id)->find();
        if (!empty($trxpayments)) {
            foreach ($trxpayments as $trxpay) {
                $payment    = $PaymentModel->find($trxpay['paymentid']);
                if (!empty($payment)) {
                    $cash   = $CashModel->find($payment['cashid']);
                    if (!empty($cash)) {
                        $paymentdata = [
                            'id'    => $cash['id'],
                            'qty'   => $cash['qty'] - $trxpay['value'],
                        ];
                        $CashModel->save($paymentdata);
                    }
                }
            }
        }

        // Delete Debt
        $DebtModel->where('transactionid', $id)->delete();

        // Delete Transaction Payment
        $TrxpaymentModel->where('transactionid', $id)->delete();

        // Delete Tansaction Detail
        $TrxdetailModel->where('transactionid', $id)->delete();

        // Delete Transaction
        $TransactionModel->delete($id);

        return redirect()->back()->with('massage', lang('global.deleted'));

        // // Conneting To Database
        // $db = \Config\Database::connect();
        // $gconfig = new GconfigModel();

        // // Getting Data Transaction
        // $Gconf = $gconfig->first();
        // $exported   = $db->table('transaction');

        // $transactionhist   = $exported->select('transaction.id as id, variant.id as varid, member.id as memberid, users.id as userid, payment.id as paymentid,
        // outlet.id as outletid, bundle.id as bundleid,trxdetail.qty as qty, transaction.value as total, bundle.price as bprice, variant.hargadasar as vprice,
        // transaction.date as date, transaction.disctype as disctype, transaction.discvalue as discval,
        // transaction.pointused as redempoin, trxpayment.value as payval, member.name as member, member.trx as trx, product.name as product, variant.name as variant,  
        // variant.hargamodal as modal, variant.hargajual as jual, trxdetail.value as trxdetval, trxdetail.discvar as discvar, payment.name as payment,
        // outlet.name as outlet,outlet.address as address, bundle.name as bundle, users.username as kasir');

        // $transactionhist   = $exported->join('trxdetail', 'transaction.id = trxdetail.transactionid', 'left');
        // $transactionhist   = $exported->join('users', 'transaction.userid = users.id', 'left');
        // $transactionhist   = $exported->join('outlet', 'transaction.outletid = outlet.id', 'left');
        // $transactionhist   = $exported->join('member', 'transaction.memberid = member.id', 'left');
        // $transactionhist   = $exported->join('trxpayment', 'trxdetail.transactionid = trxpayment.transactionid', 'left');
        // $transactionhist   = $exported->join('bundle', 'trxdetail.bundleid = bundle.id', 'left');
        // $transactionhist   = $exported->join('variant', 'trxdetail.variantid = variant.id', 'left');
        // $transactionhist   = $exported->join('payment', 'trxpayment.paymentid = payment.id', 'left');
        // $transactionhist   = $exported->join('product', 'variant.productid = product.id', 'left');
        // $transactionhist   = $exported->where('transaction.outletid', $this->data['outletPick']);
        // $transactionhist   = $exported->where('transaction.id', $id);
        // $transactionhist   = $exported->get();
        // $transactionhist   = $transactionhist->getResultArray();

        // $trxdata = array();
        // foreach ($transactionhist as $trxhist) {

        //     if ((!empty($trxhist['discval'])) && ($trxhist['disctype'] === '0')) {
        //         $discount = $trxhist['discval'];
        //         $disctype = "0";
        //     } elseif ((!empty($trxhist['discval'])) && ($trxhist['disctype'] === '1')) {
        //         $discount = ($trxhist['trxdetval'] * $trxhist['discval'] / 100);
        //     } else {
        //         $discount = 0;
        //     }

        //     if ($trxhist['disctype'] === '1') {
        //         $disctype = "%";
        //     } else {
        //         $disctype = "Rp";
        //     }

        //     if (!empty($trxhist['member'])) {
        //         $membername = $trxhist['member'];
        //     } else {
        //         $membername = "Non Member";
        //     }

        //     if (!empty($trxhist['product'])) {
        //         $product = $trxhist['product'];
        //     } else {
        //         $product = $trxhist['bundle'];
        //     }

        //     if (!empty($trxhist['discvar'])) {
        //         $discvar = $trxhist['discvar'];
        //     } else {
        //         $discvar = "0";
        //     }
        // }

        // /*======================================= REFUND DATA =============================================================================*/

        // // Calling Models
        // $BundleModel            = new BundleModel();
        // $BundledetModel         = new BundledetailModel();
        // $CashModel              = new CashModel();
        // $OutletModel            = new OutletModel();
        // $UserModel              = new UserModel();
        // $MemberModel            = new MemberModel();
        // $PaymentModel           = new PaymentModel();
        // $ProductModel           = new ProductModel();
        // $VariantModel           = new VariantModel();
        // $StockModel             = new StockModel();
        // $TransactionModel       = new TransactionModel();
        // $TrxdetailModel         = new TrxdetailModel();
        // $TrxpaymentModel        = new TrxpaymentModel();
        // $DebtModel              = new DebtModel();
        // $BookingModel           = new BookingModel();
        // $BookingdetailModel     = new BookingdetailModel();
        // $DailyReportModel       = new DailyReportModel();

        // // Populating Data
        // $bundles                = $BundleModel->findAll();

        // // Initialize 
        // $date = date('Y-m-d H:i:s');

        // $variant    = [];
        // $bundles    = [];
        // $paymentid  = [];
        // $point      = '';
        // $total      = '';

        // foreach ($transactionhist as $trxhist) {

        //     // Variant
        //     if (!empty($trxhist['varid']) && !empty($trxhist['qty'])) {
        //         $variant[$trxhist['varid']] = $trxhist['qty'];
        //     }

        //     // Bundle
        //     if (!empty($trxhist['bundleid']) && !empty($trxhist['qty'])) {
        //         $bundles[$trxhist['bundleid']] = $trxhist['qty'];
        //     }

        //     // Id Payment
        //     $paymentid[$trxhist['paymentid']] = $trxhist['payval'];
        //     $total = $trxhist['total'];

        //     // Point
        //     $memberid = $trxhist['memberid'];
        //     $point = $trxhist['redempoin'];
        //     $trx = $trxhist['trx'];
        // }

        // // Poin Setup
        // $minimumtrx = $Gconf['poinorder'];
        // $poinvalue  = $Gconf['poinvalue'];

        // $poinresult = "";
        // if ($total >= $minimumtrx) {
        //     if ($minimumtrx != "0") {
        //         $value      = (int)$total / (int)$minimumtrx;
        //     } else {
        //         $value      = 0;
        //     }
        //     $result         = floor($value);
        //     $poinresult     = (int)$result * (int)$poinvalue;
        // } else {
        //     $poinresult = 0;
        // }

        // // Refund Variant
        // if (!empty($variant)) {
        //     foreach ($variant as $varid => $varqty) {
        //         $stock = $StockModel->where('outletid', $this->data['outletPick'])->where('variantid', $varid)->first();
        //         $saleVarStock = [
        //             'id'        => $stock['id'],
        //             'sale'      => $date,
        //             'qty'       => (int)$stock['qty'] + (int)$varqty
        //         ];
        //         $StockModel->save($saleVarStock);
        //     }
        // }

        // // Refund Bundle
        // if (!empty($bundles)) {
        //     foreach ($bundles as $bunid => $bunqty) {
        //         $bundledetail = $BundledetModel->where('bundleid', $bunid)->find();
        //         foreach ($bundledetail as $BundleDetail) {
        //             if (!empty($BundleDetail['variantid'])) {
        //                 $bunstock = $StockModel->where('outletid', $this->data['outletPick'])->where('variantid', $BundleDetail['variantid'])->first();
        //                 $saleBunStock = [
        //                     'id'        => $bunstock['id'],
        //                     'sale'      => $date,
        //                     'qty'       => (int)$bunstock['qty'] + (int)$bunqty,
        //                 ];
        //                 $StockModel->save($saleBunStock);
        //             }
        //         }
        //     }
        // }

        // // Refund Member Poin
        // $pointres = '';
        // if (!empty($memberid)) {
        //     $cust       = $MemberModel->find($memberid);
        //     if (!empty($point) && $point != "0") {
        //         $pointres   = ((int)$cust['poin'] + (int)$point) - $poinresult;
        //         $point = [
        //             'id'    => $cust['id'],
        //             'poin'  => $pointres,
        //             'trx'   => (int)$cust['trx'] - 1,
        //         ];
        //         $MemberModel->save($point);
        //     } else {
        //         $point = [
        //             'id'    => $cust['id'],
        //             'poin'  => (int)$cust['poin'] - $poinresult,
        //             'trx'   => (int)$cust['trx'] - 1,
        //         ];
        //         $MemberModel->save($point);
        //     }
        // }

        // // Refund Payment
        // $debtval = "";
        // if (!empty($paymentid)) {
        //     foreach ($paymentid as $payid => $payval) {
        //         if (!empty($payid)) {
        //             $pay = $PaymentModel->find($payid);
        //             $cash = $CashModel->where('id', $pay['cashid'])->find();
        //             foreach ($cash as $cas) {
        //                 $paymentdata = [
        //                     'id'    => $cas['id'],
        //                     'qty'   => $cas['qty'] - $payval,
        //                 ];
        //                 $CashModel->save($paymentdata);
        //             }
        //         }
        //         // else {
        //         //     $debtval = $payval;
        //         //     $debt = $DebtModel->where('memberid', $memberid)->first();
        //         //     $debtdata = [
        //         //         'id'    => $debt['id'],
        //         //         'value' => $debt['value'] - $debtval,
        //         //     ];
        //         //     $DebtModel->save($debtdata);
        //         // }
        //     }
        // }

        // // Delete Transaction Payment
        // // $trxpay = $TrxpaymentModel->where('transactionid', $id)->find();
        // // foreach ($trxpay as $pay) {
        // //     $TrxpaymentModel->delete($pay);
        // // }
        // $TrxpaymentModel->where('transactionid', $id)->delete();

        // // Delete Transaction Payment
        // // $debt = $DebtModel->where('transactionid', $id)->find();
        // // foreach ($debt as $deb) {
        // //     $DebtModel->delete($deb);
        // // }
        // $DebtModel->where('transactionid', $id)->delete();

        // // Delete Tansaction Detail
        // // $trxdet = $TrxdetailModel->where('transactionid', $id)->find();
        // // foreach ($trxdet as $detail) {
        // //     $TrxdetailModel->delete($detail);
        // // }
        // $TrxdetailModel->where('transactionid', $id)->delete();

        // // Delete Transaction
        // // $trx = $TransactionModel->find($id);
        // // $TransactionModel->delete($trx);
        // $TransactionModel->delete($id);

        // return redirect()->back()->with('massage', lang('global.deleted'));
    }

    public function invoice($id)
    {
        // Calling Models
        $DebtModel              = new DebtModel();
        $DebtInsModel           = new DebtInsModel();
        $BundleModel            = new BundleModel();
        $OutletModel            = new OutletModel();
        $UserModel              = new UserModel();
        $MemberModel            = new MemberModel();
        $ProductModel           = new ProductModel();
        $VariantModel           = new VariantModel();
        $TransactionModel       = new TransactionModel();
        $TrxdetailModel         = new TrxdetailModel();

        // Populating Data
        $debt                   = $DebtModel->find($id);
        $debtdata               = [];
        $transaction            = $TransactionModel->find($debt['transactionid']);
        $members                = $MemberModel->find($debt['memberid']);

        if (!empty($transaction)) {
            $outlet             = $OutletModel->find($transaction['outletid']);
            $user               = $UserModel->find($transaction['userid']);

            $debtdata[$transaction['id']]['id']         = $debt['id'];
            $debtdata[$transaction['id']]['deadline']   = $debt['deadline'];
            $debtdata[$transaction['id']]['value']      = $debt['value'];
            $debtdata[$transaction['id']]['name']       = $members['name'].' - '.$members['phone'];
            $debtdata[$transaction['id']]['phone']      = $members['phone'];
            $debtdata[$transaction['id']]['outlet']     = $outlet['name'];
            $debtdata[$transaction['id']]['address']    = $outlet['address'];
            $debtdata[$transaction['id']]['outletig']   = $outlet['instagram'];
            $debtdata[$transaction['id']]['outletwa']   = $outlet['phone'];
            $debtdata[$transaction['id']]['cashier']    = $user->firstname.' '.$user->lastname;
            $debtdata[$transaction['id']]['trxdate']    = $transaction['date'];
            $debtdata[$transaction['id']]['trxvalue']   = $transaction['value'];
            $debtdata[$transaction['id']]['trxdisc']    = $transaction['discvalue'];
            $debtdata[$transaction['id']]['trxpoin']    = $transaction['pointused'];
            $debtdata[$transaction['id']]['trxpaid']    = $transaction['amountpaid'];
            $debtdata[$transaction['id']]['trxmemdisc'] = $transaction['memberdisc'];
            $debtdata[$transaction['id']]['debtval']    = $debt['value'];

            $trxdetails     = $TrxdetailModel->where('transactionid', $transaction['id'])->find();
            $total          = [];
            if (!empty($trxdetails)) {
                foreach ($trxdetails as $trxdet) {
                    // Data Variant
                    if ($trxdet['variantid'] != '0') {
                        $variants       = $VariantModel->find($trxdet['variantid']);
                        
                        if (!empty($variants)) {
                            $products   = $ProductModel->find($variants['productid']);
    
                            if (!empty($products)) {
                                $debtdata[$transaction['id']]['detailvar'][$variants['id']]['name']            = $products['name'].' - '.$variants['name'];
                                $debtdata[$transaction['id']]['detailvar'][$variants['id']]['qty']             = $trxdet['qty'];
                                $debtdata[$transaction['id']]['detailvar'][$variants['id']]['discvar']         = $trxdet['discvar'];
                                $debtdata[$transaction['id']]['detailvar'][$variants['id']]['globaldisc']      = $trxdet['globaldisc'];
                                $debtdata[$transaction['id']]['detailvar'][$variants['id']]['memberdisc']      = $trxdet['memberdisc'];
                                $debtdata[$transaction['id']]['detailvar'][$variants['id']]['value']           = (float)$trxdet['value'] + ((Int)$trxdet['discvar'] / (Int)$trxdet['qty']) + ((Int)$trxdet['globaldisc'] / (Int)$trxdet['qty']) + ((Int)$trxdet['memberdisc'] / (Int)$trxdet['qty']);
                            }
                        } else {
                            $products   = [];
                            $debtdata[$transaction['id']]['detailvar'][0]['name']                              = 'Produk / Variant Terhapus';
                            $debtdata[$transaction['id']]['detailvar'][0]['qty']                               = $trxdet['qty'];
                            $debtdata[$transaction['id']]['detailvar'][0]['discvar']                           = $trxdet['discvar'];
                            $debtdata[$transaction['id']]['detailvar'][0]['globaldisc']                        = $trxdet['globaldisc'];
                            $debtdata[$transaction['id']]['detailvar'][0]['memberdisc']                        = $trxdet['memberdisc'];
                            $debtdata[$transaction['id']]['detailvar'][0]['value']                             = (float)$trxdet['value'] + ((Int)$trxdet['discvar'] / (Int)$trxdet['qty']) + ((Int)$trxdet['globaldisc'] / (Int)$trxdet['qty']) + ((Int)$trxdet['memberdisc'] / (Int)$trxdet['qty']);
                        }
                    } else {
                        $debtdata[$transaction['id']]['detailvar']  = [];
                    }

                    // Data Bundle
                    if ($trxdet['bundleid'] != '0') {
                        $bundles        = $BundleModel->find($trxdet['bundleid']);
                        if (!empty($bundles)) {
                            $debtdata[$transaction['id']]['detailbun'][$bundles['id']]['name']          = $bundles['name'];
                            $debtdata[$transaction['id']]['detailbun'][$bundles['id']]['qty']           = $trxdet['qty'];
                            $debtdata[$transaction['id']]['detailbun'][$bundles['id']]['globaldisc']    = $trxdet['globaldisc'];
                            $debtdata[$transaction['id']]['detailbun'][$bundles['id']]['memberdisc']    = $trxdet['memberdisc'];
                            $debtdata[$transaction['id']]['detailbun'][$bundles['id']]['value']         = (float)$trxdet['value'] + ((Int)$trxdet['globaldisc'] / (Int)$trxdet['qty']) + ((Int)$trxdet['memberdisc'] / (Int)$trxdet['qty']);
                        } else {
                            $debtdata[$transaction['id']]['detailbun'][0]['name']                       = 'Bundle Terhapus';
                            $debtdata[$transaction['id']]['detailbun'][0]['qty']                        = $trxdet['qty'];
                            $debtdata[$transaction['id']]['detailbun'][0]['globaldisc']                 = $trxdet['globaldisc'];
                            $debtdata[$transaction['id']]['detailbun'][0]['memberdisc']                 = $trxdet['memberdisc'];
                            $debtdata[$transaction['id']]['detailbun'][0]['value']                      = (float)$trxdet['value'] + ((Int)$trxdet['globaldisc'] / (Int)$trxdet['qty']) + ((Int)$trxdet['memberdisc'] / (Int)$trxdet['qty']);
                        }
                    } else {
                        $debtdata[$transaction['id']]['detailbun']  = [];
                    }
                    $total[]    = (float)$trxdet['value'] * (Int)$trxdet['qty'];
                }
            }
            $debtdata[$transaction['id']]['subtotal']   = array_sum($total);

            $debtinstallment    = $DebtInsModel->where('debtid', $id)->where('transactionid', $transaction['id'])->find();
            if (!empty($debtinstallment)) {
                foreach ($debtinstallment as $debtins) {
                    $debtdata[$transaction['id']]['installment'][$debtins['id']]['date']    = $debtins['date'];
                    $debtdata[$transaction['id']]['installment'][$debtins['id']]['value']   = $debtins['qty'];
                }
            }
        }

        $data                   = $this->data;
        $data['debts']          = $debtdata;
        $actual_link            = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $data['links']          = urlencode($actual_link);

        return view('Views/debtinst', $data);
    }
}
