<?php

namespace App\Controllers;

use App\Models\BundledetailModel;
use App\Models\BundleModel;
use App\Models\BrandModel;
use App\Models\CategoryModel;
use App\Models\CashModel;
use App\Models\DebtModel;
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
use App\Models\PresenceModel;
use App\Models\GroupUserModel;
use App\Models\SopModel;
use App\Models\SopDetailModel;
use Myth\Auth\Models\GroupModel;

class Report extends BaseController
{
    protected $data;
    protected $db, $builder;
    protected $auth;
    protected $config;

    public function dailysell()
    {
        // Calling Models
        $TransactionModel       = new TransactionModel;
        $TrxdetailModel         = new TrxdetailModel;

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
            $transaction = $TransactionModel->where('date >=', date('Y-m-d 00:00:00'))->where('date <=', date('Y-m-d 23:59:59'))->find();
        } else {
            $transaction = $TransactionModel->where('date >=', date('Y-m-d 00:00:00'))->where('date <=', date('Y-m-d 23:59:59'))->where('outletid', $this->data['outletPick'])->find();
        }

        if (!empty($transaction)) {
            foreach ($transaction as $trx) {
                $time                               = date('H', strtotime($trx['date']));
                $transactions[$time]['date']        = date('H', strtotime($trx['date']));
                $transactions[$time]['val'][]       = $trx['value'];

                // Transaction Discount
                if (!empty($trx['discvalue'])) {
                    $discounttrx[]                      = $trx['discvalue'];
                    $transactions[$time]['trxdisc'][]   = $trx['discvalue'];
                } else {
                    $transactions[$time]['trxdisc'][]   = [];
                }
    
                // Point Used
                $discountpoin[]                     = $trx['pointused'];
                $transactions[$time]['pointdisc'][] = $trx['pointused'];

                // Member Discount
                $memberdisc[]                           = $trx['memberdisc'];
                $transactions[$time]['memberdisc'][]    = $trx['memberdisc'];

                // Discount Variant
                $trxdetails  = $TrxdetailModel->where('transactionid', $trx['id'])->find();
                if (!empty($trxdetails)) {
                    foreach ($trxdetails as $trxdetail) {
                        // Transaction Detail Discount Variant
                        if ($trxdetail['discvar'] != '0') {
                            $discountvariant[]                  = $trxdetail['discvar'];
                            $transactions[$time]['vardisc'][]   = $trxdetail['discvar'];
                        } else {
                            $transactions[$time]['vardisc'][]   = [];
                        }
        
                        // Transaction Detail Discount Global
                        if ($trxdetail['globaldisc'] != '0') {
                            $discountglobal[]                   = $trxdetail['globaldisc'];
                            $transactions[$time]['globdisc'][]  = $trxdetail['globaldisc'];
                        } else {
                            $transactions[$time]['globdisc'][]  = [];
                        }
        
                        // Transaction Detail Discount Member
                        if ($trxdetail['memberdisc'] != '0') {
                            $discountmember[]                   = $trxdetail['memberdisc'];
                            $transactions[$time]['membdisc'][]  = $trxdetail['memberdisc'];
                        } else {
                            $transactions[$time]['membdisc'][]  = [];
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
                    'waktu'             => $trxdat['date'],
                    'value'             => array_sum($trxdat['val']),
                    'profitmodal'       => array_sum($trxdat['profitmodal']),
                    'profitdasar'       => array_sum($trxdat['profitdasar']),
                    'trxdisc'           => array_sum($trxdat['trxdisc']),
                    'vardisc'           => array_sum($trxdat['vardisc']),
                    'globdisc'          => array_sum($trxdat['globdisc']),
                    'membdisc'          => array_sum($trxdat['membdisc']),
                    'pointdisc'         => array_sum($trxdat['pointdisc']),
                ];
            }
        } else {
            $transactionarr[]  = [
                'waktu'             => date('H', strtotime(date('Y-m-d' . 'H:i:s'))),
                'value'             => 0,
                'profitmodal'       => 0,
                'profitdasar'       => 0,
                'trxdisc'           => 0,
                'vardisc'           => 0,
                'globdisc'          => 0,
                'membdisc'          => 0,
                'pointdisc'         => 0,
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
        
        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = 'Laporan Penjualan Harian';
        $data['description']    = 'Developed by PT. Kodebiner Teknologi Indonesia';
        $data['transactions']   = $transactionarr;
        $data['result']         = $salesresult;
        $data['gross']          = $grossales;
        $data['profitmodal']    = $profitmodal;
        $data['profitdasar']    = $profitdasar;
        $data['trxvardis']      = $variantdisc;
        $data['trxglodis']      = $globaldisc;
        $data['trxmemdis']      = $memberdiscitem;
        $data['trxdisc']        = $transactiondisc;
        $data['poindisc']       = $poindisc;

        return view('Views/report/dailysell', $data);
    }

    public function penjualan()
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
            $startdate  = strtotime(date('Y-m-d' . ' 00:00:00'));
            $enddate    = strtotime(date('Y-m-d' . ' 23:59:59'));
        }

        $transactions       = array();
        $transactionarr     = array();
        $memberdisc         = array();
        $discounttrx        = array();
        $discountvariant    = array();
        $discountpoin       = array();
        $discountglobal     = array();
        $discountmember     = array();

        for ($date = $startdate; $date <= $enddate; $date += (86400)) {
            if ($this->data['outletPick'] === null) {
                $transaction = $TransactionModel->where('date >=', date('Y-m-d 00:00:00', $date))->where('date <=', date('Y-m-d 23:59:59', $date))->find();
            } else {
                $transaction = $TransactionModel->where('date >=', date('Y-m-d 00:00:00', $date))->where('date <=', date('Y-m-d 23:59:59', $date))->where('outletid', $this->data['outletPick'])->find();
            }
            $summary = array_sum(array_column($transaction, 'value'));

            $discountforprofit  = [];
            $trxdisc            = [];
            $vardisc            = [];
            $globdisc           = [];
            $membdisc           = [];
            $pointdisc          = [];
            $marginmodals       = [];
            $margindasars       = [];
            foreach ($transaction as $trx) {
                $trxdetails  = $TrxdetailModel->where('transactionid', $trx['id'])->find();

                if (!empty($trx['discvalue'])) {
                    $discounttrx[]  = $trx['discvalue'];
                    $trxdisc[]      = $trx['discvalue'];
                }

                // Discount Point Used
                $discountpoin[]             = $trx['pointused'];
                $pointdisc[]                = $trx['pointused'];

                // Member Discount
                $memberdisc[]               = $trx['memberdisc'];
                $trxdisc[]                  = $trx['memberdisc'];
                
                if ($trx['discvalue'] != '0') {
                    $discountforprofit[]   = (int)$trx['discvalue'];
                } else {
                    $discountforprofit[]   = 0;
                }

                if ($trx['memberdisc'] != '0') {
                    $discountforprofit[]   = (int)$trx['memberdisc'];
                } else {
                    $discountforprofit[]   = 0;
                }

                if ($trx['pointused'] != '0') {
                    $discountforprofit[]   = (int)$trx['pointused'];
                } else {
                    $discountforprofit[]   = 0;
                }

                foreach ($trxdetails as $trxdetail) {
    
                    // Transaction Detail Discount Variant
                    if ($trxdetail['discvar'] != 0) {
                        $discountvariant[]      = $trxdetail['discvar'];
                        $vardisc[]              = $trxdetail['discvar'];
                    }

                    // Transaction Detail Discount Global
                    if ($trxdetail['globaldisc'] != '0') {
                        $discountglobal[]       = $trxdetail['globaldisc'];
                        $globdisc[]             = $trxdetail['globaldisc'];
                    }

                    // Transaction Detail Discount Member
                    if ($trxdetail['memberdisc'] != '0') {
                        $discountmember[]       = $trxdetail['memberdisc'];
                        $membdisc[]             = $trxdetail['memberdisc'];
                    }

                    // Transaction Detail Margin Modal
                    $marginmodals[] = ((int)$trxdetail['marginmodal'] * (int)$trxdetail['qty']);

                    // Transaction Detail Margin Dasar
                    $margindasars[] = ((int)$trxdetail['margindasar'] * (int)$trxdetail['qty']);
                }
            }

            $totaldisc          = array_sum($discountforprofit);
            $marginmodalsum     = array_sum($marginmodals);
            $margindasarsum     = array_sum($margindasars);
            $profitmodal        = (Int)$marginmodalsum - (Int)$totaldisc;
            $profitdasar        = (Int)$margindasarsum - (Int)$totaldisc;

            $transactions[] = [
                'date'              => date('d/m/y', $date),
                'value'             => $summary,
                'profitmodal'       => $profitmodal,
                'profitdasar'       => $profitdasar,
                'trxdisc'           => array_sum($trxdisc),
                'vardisc'           => array_sum($vardisc),
                'globdisc'          => array_sum($globdisc),
                'membdisc'          => array_sum($membdisc),
                'pointdisc'         => array_sum($pointdisc),
            ];
        }

        $transactiondisc    = (int)(array_sum($discounttrx)) + (int)(array_sum($memberdisc));
        $variantdisc        = array_sum($discountvariant);
        $globaldisc         = array_sum($discountglobal);
        $memberdiscitem     = array_sum($discountmember);
        $poindisc           = array_sum($discountpoin);

        $transactionarr[] = $transactions;

        // Sales Result
        $salesresult = array_sum(array_column($transactions, 'value'));
        $grossales = (Int)$salesresult + (Int)$variantdisc + (Int)$globaldisc + (Int)$memberdiscitem + (Int)$transactiondisc + (Int)$poindisc;

        // Profit Result
        $keuntunganmodal = array_sum(array_column($transactions, 'profitmodal'));
        $keuntungandasar = array_sum(array_column($transactions, 'profitdasar'));

        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.salesreport');
        $data['description']    = lang('Global.transactionListDesc');
        $data['startdate']      = $startdate;
        $data['enddate']        = $enddate;
        $data['transactions']   = $transactions;
        $data['transactionarr'] = $transactionarr;
        $data['result']         = $salesresult;
        $data['gross']          = $grossales;
        $data['modals']         = $keuntunganmodal;
        $data['dasars']         = $keuntungandasar;
        $data['trxvardis']      = $variantdisc;
        $data['trxglodis']      = $globaldisc;
        $data['trxmemdis']      = $memberdiscitem;
        $data['trxdisc']        = $transactiondisc;
        $data['poindisc']       = $poindisc;

        return view('Views/report/penjualan', $data);
    }

    public function keuntungan()
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
            $startdate  = strtotime(date('Y-m-d' . ' 00:00:00'));
            $enddate    = strtotime(date('Y-m-d' . ' 23:59:59'));
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

        // Parsing Data to View
        $data                       = $this->data;
        $data['title']              = lang('Global.profitreport');
        $data['description']        = lang('Global.profitListDesc');
        $data['transactions']       = $transactions;
        $data['modals']             = $keuntunganmodal;
        $data['dasars']             = $keuntungandasar;
        $data['startdate']          = $startdate;
        $data['enddate']            = $enddate;

        return view('Views/report/keuntungan', $data);
    }

    public function payment()
    {
        // Calling Models
        $PaymentModel           = new PaymentModel();
        $TrxpaymentModel        = new TrxpaymentModel();
        $TransactionModel       = new TransactionModel();

        // Populating Data
        $input          = $this->request->getGet();
        $daterange      = $input['daterange'] ?? date('Y-m-d') . ' - ' . date('Y-m-d');
        
        [$startdate, $enddate] = explode(' - ', $daterange);
        $startdate = date('Y-m-d', strtotime($startdate));
        $enddate   = date('Y-m-d', strtotime($enddate));

        $search = $input['search'] ?? '';

        $payments = $PaymentModel->orderBy('id', 'DESC')->where('outletid', $this->data['outletPick'])->findAll();

        $transactions = $TransactionModel->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->where('outletid', $this->data['outletPick'])->findAll();

        $transactiondata = [];

        foreach ($payments as $payment) {
            $transactiondata[$payment['id']] = [
                'name'  => $payment['name'],
                'qty'   => 0,
                'value' => 0,
            ];
        }

        // Debt and Redeem Point
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

        $transactionIds = array_column($transactions, 'id');

        $trxpayments = [];

        if (!empty($transactionIds)) {
            $trxpayments = $TrxpaymentModel
                ->select('paymentid, COUNT(*) as qty, COALESCE(SUM(value),0) as value')
                ->whereIn('transactionid', $transactionIds)
                ->groupBy('paymentid')
                ->findAll();
        }
        
        foreach ($trxpayments as $row) {
            $paymentId = (int)$row['paymentid'];

            if (!isset($transactiondata[$paymentId])) {
                continue;
            }

            $transactiondata[$paymentId]['qty'] = (int)$row['qty'];
            $transactiondata[$paymentId]['value'] = (float)$row['value'];
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

        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.paymentreport');
        $data['description']    = lang('Global.paymentListDesc');
        $data['payments']       = $transactiondata;
        $data['daterange']      = $daterange;
        $data['search']         = $search;
        $data['total']          = array_sum(array_column($transactiondata, 'value'));

        return view('Views/report/payment', $data);
    }

    public function employe()
    {
        // Calling Model
        $TransactionModel   = new TransactionModel();
        $UserModel          = new UserModel();
        $UserGroupModel     = new GroupUserModel();
        $GroupModel         = new GroupModel();

        // Populating Data
        $input          = $this->request->getGet();
        $daterange      = $input['daterange'] ?? date('Y-m-d') . ' - ' . date('Y-m-d');
        $search         = trim($input['search'] ?? '');
        
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

        if ($this->data['outletPick'] !== null) {
            $trxBuilder->where('outletid', $this->data['outletPick']);
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

        // parsing data to view
        $data                   = $this->data;
        $data['title']          = lang('Global.employereport');
        $data['description']    = lang('Global.employeListDesc');
        $data['employetrx']     = $employeedata;
        $data['daterange']      = $daterange;
        $data['search']         = $search;

        return view('Views/report/employe', $data);
    }

    public function product()
    {
        // Calling Models
        $ProductModel       = new ProductModel();
        $TransactionModel   = new TransactionModel();
        $TrxdetailModel     = new TrxdetailModel();
        $VariantModel       = new VariantModel();
        $CategoryModel      = new CategoryModel();

        // Populating Data
        $input          = $this->request->getGet();
        $daterange      = $input['daterange'] ?? date('Y-m-d') . ' - ' . date('Y-m-d');
        $search         = trim($input['search'] ?? '');
        
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
            $transactions       = $TransactionModel->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->findAll();
        } else {
            $transactions       = $TransactionModel->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->where('outletid', $this->data['outletPick'])->findAll();
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
                    $variants       = $variantMap[$trxdet['variantid']] ?? null;
                    
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

                            $transactiondata[$products['id']]['name']            = $products['name'];
                            
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
                            
                            $transactiondata[$products['id']]['qty']
                                = ($transactiondata[$products['id']]['qty'] ?? 0)
                                + $trxdet['qty'];

                            $transactiondata[$products['id']]['netvalue']
                                = ($transactiondata[$products['id']]['netvalue'] ?? 0)
                                + (((float)$trxdet['value'] * (Int)$trxdet['qty'])) - ((Int)$discval + (Int)$discmem + (Int)$discpoin);

                            $transactiondata[$products['id']]['grossvalue']
                                = ($transactiondata[$products['id']]['grossvalue'] ?? 0)
                                + ((float)$trxdet['value'] * (Int)$trxdet['qty']) + (Int)$trxdet['discvar'] + (Int)$trxdet['globaldisc'] + (Int)$trxdet['memberdisc'];
                        }
                    } else {
                        $transactiondata[0]['name']             = 'Kategori / Produk / Variant Terhapus';
                        $transactiondata[0]['category']         = 'Kategori / Produk / Variant Terhapus';
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
        
        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.productreport');
        $data['description']    = lang('Global.productListDesc');
        $data['products']       = $transactiondata;
        $data['totalstock']     = $totalsalesitem;
        $data['salestotal']     = $totalnetsales;
        $data['grosstotal']     = $totalcatgross;
        $data['netsales']       = $totalnetsales;
        $data['gross']          = $totalcatgross;
        $data['daterange']      = $daterange;
        $data['search']         = $search;

        return view('Views/report/product', $data);
    }

    public function category()
    {
        // Calling models
        $ProductModel       = new ProductModel();
        $TransactionModel   = new TransactionModel();
        $TrxdetailModel     = new TrxdetailModel();
        $VariantModel       = new VariantModel();
        $BundleModel        = new BundleModel();
        $BundledetailModel  = new BundledetailModel();
        $CategoryModel      = new CategoryModel();

        // Populating Data
        $input          = $this->request->getGet();
        $daterange      = $input['daterange'] ?? date('Y-m-d') . ' - ' . date('Y-m-d');
        $search         = trim($input['search'] ?? '');
        
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
            $transactions       = $TransactionModel->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->findAll();
        } else {
            $transactions       = $TransactionModel->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->where('outletid', $this->data['outletPick'])->findAll();
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

        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.categoryreport');
        $data['description']    = lang('Global.categoryListDesc');
        $data['catedata']       = $transactiondata;
        $data['netsales']       = $totalnetsales;
        $data['gross']          = $totalcatgross;
        $data['qty']            = $totalsalesitem;
        $data['daterange']      = $daterange;
        $data['search']         = $search;

        return view('Views/report/category', $data);
    }

    public function brand()
    {
        // Calling models
        $ProductModel       = new ProductModel();
        $TransactionModel   = new TransactionModel();
        $TrxdetailModel     = new TrxdetailModel();
        $VariantModel       = new VariantModel();
        $BundleModel        = new BundleModel();
        $BundledetailModel  = new BundledetailModel();
        $BrandModel         = new BrandModel();

        // Populating Data
        $input          = $this->request->getGet();
        $daterange      = $input['daterange'] ?? date('Y-m-d') . ' - ' . date('Y-m-d');
        $search         = trim($input['search'] ?? '');
        
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
            $transactions       = $TransactionModel->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->findAll();
        } else {
            $transactions       = $TransactionModel->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->where('outletid', $this->data['outletPick'])->findAll();
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
                            $products = $productMap[$variants['productid']] ?? null;
    
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
                        $bundle = $bundleMap[$trxdet['bundleid']] ?? null;

                        if (!empty($bundles)) {
                            // Data Bundle Detail
                            $bundledets = $bundleDetailMap[$bundle['id']] ?? [];
    
                            if (!empty($bundledets)) {
                                foreach ($bundledets as $bundet) {
                                    // Data Variant
                                    $variant = $variantMap[$bundet['variantid']] ?? null;
                                    
                                    if (!empty($variant)) {
                                        $product = $productMap[$variant['productid']] ?? null;

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

        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.brandreport');
        $data['description']    = lang('Global.brandListDesc');
        $data['branddata']      = $transactiondata;
        $data['netsales']       = $totalnetsales;
        $data['gross']          = $totalcatgross;
        $data['qty']            = $totalsalesitem;
        $data['daterange']      = $daterange;
        $data['search']         = $search;

        return view('Views/report/brand', $data);
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
            $stocks      = $StockModel->findAll();
        } else {
            $stocks      = $StockModel->where('outletid', $this->data['outletPick'])->find();
        }

        $productval = [];
        foreach ($stocks as $stock) {
            foreach ($variants as $variant) {
                foreach ($products as $product) {
                    foreach ($brands as $brand) {
                        if ($brand['status'] == '1') {
                            $brandstatus = 'Aktif';
                        } else {
                            $brandstatus = 'Tidak Aktif';
                        }
                        
                        $brandname  = $brand['name'] . ' (' . $brandstatus . ')';
                        
                        foreach ($category as $cat) {
                            if ($cat['status'] == '1') {
                                $catstatus = 'Aktif';
                            } else {
                                $catstatus = 'Tidak Aktif';
                            }
                            $catname  = $cat['name'].' ('.$catstatus.')';
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

        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.category');
        $data['description']    = lang('Global.categoryListDesc');
        $data['products']       = $produk;
        $data['stock']          = $stock;
        $data['whole']          = $whole;

        return view('Views/report/stockcategory', $data);
    }

    public function bundle()
    {
        // Calling models
        $TransactionModel   = new TransactionModel();
        $TrxdetailModel     = new TrxdetailModel();
        $VariantModel       = new VariantModel();
        $BundleModel        = new BundleModel();
        $BundledetailModel  = new BundledetailModel();

        // initialize
        $input = $this->request->getGet();

        if (!empty($input['daterange'])) {
            $daterange = explode(' - ', $input['daterange']);
            $startdate = $daterange[0];
            $enddate = $daterange[1];
        } else {
            $startdate  = date('Y-m-d' . ' 00:00:00');
            $enddate    = date('Y-m-d' . ' 23:59:59');
        }

        // Populating Data
        if ($this->data['outletPick'] === null) {
            return redirect()->back()->with('error', lang('Global.chooseoutlet'));
        } else {
            $transactions       = $TransactionModel->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->where('outletid', $this->data['outletPick'])->find();

            $transactiondata    = [];
            
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
        }
        array_multisort(array_column($transactiondata, 'qty'), SORT_DESC, $transactiondata);

        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.bundlereport');
        $data['description']    = lang('Global.bundleListDesc');
        $data['bundles']        = $transactiondata;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);

        return view('Views/report/bundle', $data);
    }

    public function diskon()
    {
        // Calling Models
        $TransactionModel       = new TransactionModel;
        $TrxdetailModel         = new TrxdetailModel;

        // Populating Data
        $input = $this->request->getGet('daterange');

        if (!empty($input)) {
            $daterange = explode(' - ', $input);
            $startdate = $daterange[0];
            $enddate = $daterange[1];
        } else {
            $startdate  = date('Y-m-d' . ' 00:00:00');
            $enddate    = date('Y-m-d' . ' 23:59:59');
        }

        $transactions = array();
        if ($this->data['outletPick'] === null) {
            $transaction = $TransactionModel->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->find();
        } else {
            $transaction = $TransactionModel->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->where('outletid', $this->data['outletPick'])->find();
        }

        $discount           = array();
        $pointused          = array();
        $discountmember     = array();
        $discountvariant    = array();
        $discountglobal     = array();

        foreach ($transaction as $trx) {
            // Transaction Point Used Array
            $pointused[]        = $trx['pointused'];

            // Discount Transaction
            if (!empty($trx['discvalue'])) {
                $discount[]  = $trx['discvalue'];
            }

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
        $memberdisc         = array_sum($discountmember);
        $poindisc           = array_sum($pointused);

        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.discountreport');
        $data['description']    = lang('Global.profitListDesc');
        $data['transactions']   = $transactions;
        $data['trxvardis']      = $variantdisc;
        $data['trxglodis']      = $globaldisc;
        $data['trxmemdis']      = $memberdisc;
        $data['trxdisc']        = $transactiondisc;
        $data['poindisc']       = $poindisc;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);
        
        return view('Views/report/diskon', $data);
    }

    public function presence()
    {
        // calling model
        $PresenceModel  = new PresenceModel;
        $UserModel      = new UserModel;
        $UserGroupModel = new GroupUserModel;
        $GroupModel     = new GroupModel;
        $OutletModel    = new OutletModel;

        // populating data
        $input = $this->request->getGet('daterange');

        if (!empty($input)) {
            $daterange = explode(' - ', $input);
            $startdate = $daterange[0];
            $enddate = $daterange[1];
        } else {
            $startdate  = date('Y-m-d' . ' 00:00:00');
            $enddate    = date('Y-m-d' . ' 23:59:59');
        }

        $presencedata   = [];
        
        if ($this->data['outletPick'] === null) {
            $presences  = $PresenceModel->where('datetime >=', $startdate . ' 00:00:00')->where('datetime <=', $enddate . ' 23:59:59')->find();
        } else {
            $presences  = $PresenceModel->where('outletid', $this->data['outletPick'])->where('datetime >=', $startdate . ' 00:00:00')->where('datetime <=', $enddate . ' 23:59:59')->find();
        }
        
        foreach ($presences as $presence) {
            // Get User Data
            $users          = $UserModel->find($presence['userid']);
            $usergroups     = $UserGroupModel->where('user_id', $users->id)->first();
            $groups         = $GroupModel->find($usergroups['group_id']);
            $outlets        = $OutletModel->find($presence['outletid']);

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
            $presencedata[$date.$users->id.$shift]['outlet']   = $outlets['name'];

            $presencedata[$date.$users->id.$shift]['detail'][$status]['time']         = $time;
            $presencedata[$date.$users->id.$shift]['detail'][$status]['photo']        = $presence['photo'];
            $presencedata[$date.$users->id.$shift]['detail'][$status]['geoloc']       = $presence['geoloc'];
            $presencedata[$date.$users->id.$shift]['detail'][$status]['status']       = $presence['status'];
        }

        // parsing data to view
        $data                   = $this->data;
        $data['title']          = lang('Global.presencereport');
        $data['description']    = lang('Global.presenceListDesc');
        $data['presences']      = $presencedata;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);

        return view('Views/report/presence', $data);
    }

    public function presencedetail($id)
    {
        // Calling Model
        $PresenceModel      = new PresenceModel;

        $datas = explode('-', $id);

        $iduser = $datas[0];
        $starts = $datas[1];
        $ends   = $datas[2];

        if (!empty($iduser)) {
            $presences = $PresenceModel->where('datetime >=', $starts . ' 00:00:00')->where('datetime <=', $ends . ' 23:59:59')->where('userid', $iduser)->orderby('id', 'DESC')->paginate(20, 'reportpresencedet');
        } else {
            $presences = $PresenceModel->where('datetime >=', $starts . ' 00:00:00')->where('datetime <=', $ends . ' 23:59:59')->orderby('id', 'DESC')->paginate(20, 'reportpresencedet');
        }

        // parsing data to view
        $data                   = $this->data;
        $data['title']          = lang('Global.presence');
        $data['description']    = lang('Global.presencedetailListDesc');
        $data['presences']      = $presences;
        $data['pager']          = $PresenceModel->pager;

        return view('Views/report/presencedetail', $data);
    }

    public function customer()
    {
        // Calling Models
        $TransactionModel   = new TransactionModel();
        $MemberModel        = new MemberModel();
        $DebtModel          = new DebtModel();
        $TrxdetailModel     = new TrxdetailModel();
        $ProductModel       = new ProductModel();
        $VariantModel       = new VariantModel();

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
            $startdate  = date('2023-01-01' . ' 00:00:00');
            $enddate    = date('Y-m-t' . ' 23:59:59');
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
            } else {
                $transactions   = $TransactionModel->where('memberid', $member['id'])->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->where('outletid', $this->data['outletPick'])->find();
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

        // Parsing Data to View
        $data                       = $this->data;
        $data['title']              = lang('Global.customer');
        $data['description']        = lang('Global.customerListDesc');
        $data['customers']          = $customerdata;
        $data['pager']              = $MemberModel->pager;
        $data['startdate']          = strtotime($startdate);
        $data['enddate']            = strtotime($enddate);

        return view('Views/report/customer', $data);
    }

    public function customerdetail($id)
    {
        // Calling Models
        $BundleModel            = new BundleModel;
        $BundledetModel         = new BundledetailModel;
        $CashModel              = new CashModel;
        $OutletModel            = new OutletModel;
        $UserModel              = new UserModel;
        $MemberModel            = new MemberModel;
        $PaymentModel           = new PaymentModel;
        $ProductModel           = new ProductModel;
        $VariantModel           = new VariantModel;
        $StockModel             = new StockModel;
        $TransactionModel       = new TransactionModel;
        $TrxdetailModel         = new TrxdetailModel;
        $TrxpaymentModel        = new TrxpaymentModel;
        $DebtModel              = new DebtModel;

        // Populating Data
        $bundles                = $BundleModel->findAll();
        $bundets                = $BundledetModel->findAll();
        $cash                   = $CashModel->findAll();
        $outlets                = $OutletModel->findAll();
        $users                  = $UserModel->findAll();
        $customers              = $MemberModel->findAll();
        $payments               = $PaymentModel->findAll();
        $products               = $ProductModel->findAll();
        $variants               = $VariantModel->findAll();
        $stocks                 = $StockModel->findAll();
        $transactions           = $TransactionModel->orderBy('date', 'DESC')->where('memberid', $id)->find();
        $trxdetails             = $TrxdetailModel->findAll();
        $trxpayments            = $TrxpaymentModel->findAll();
        $debts                  = $DebtModel->where('memberid', $id)->find();

        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.trxHistory');
        $data['description']    = lang('Global.trxHistoryListDesc');
        $data['bundles']        = $bundles;
        $data['bundets']        = $bundets;
        $data['cash']           = $cash;
        $data['users']          = $users;
        $data['transactions']   = $transactions;
        $data['outlets']        = $outlets;
        $data['payments']       = $payments;
        $data['customers']      = $customers;
        $data['products']       = $products;
        $data['variants']       = $variants;
        $data['stocks']         = $stocks;
        $data['trxdetails']     = $trxdetails;
        $data['trxpayments']    = $trxpayments;
        $data['debts']          = $debts;

        return view('Views/report/customerdetail', $data);
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
            $startdate  = date('Y-m-d' . ' 00:00:00');
            $enddate    = date('Y-m-d' . ' 23:59:59');
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
                $username   = 'Belum Tersedia';
            }

            // Define Time
            $s      = strtotime($sopdet['created_at']);
            $date   = date('d-m-Y', $s);

            $sopdata[$date.$outletid]['id']                               = $count++;
            $sopdata[$date.$outletid]['date']                             = $date;
            $sopdata[$date.$outletid]['outlet']                           = $outletname;
            $sopdata[$date.$outletid]['detail'][$sops['id']]['sop']       = $sops['name'];
            $sopdata[$date.$outletid]['detail'][$sops['id']]['employee']  = $username;
            $sopdata[$date.$outletid]['detail'][$sops['id']]['status']    = $sopdet['status'];
        }

        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = "Laporan SOP";
        $data['description']    = "Laporan SOP yang telah dilakukan";
        $data['sopdetails']     = $sopdata;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);

        return view('Views/report/sop', $data);
    }
}
