<?php

namespace App\Controllers;

use App\Models\BundledetailModel;
use App\Models\BundleModel;
use App\Models\BrandModel;
use App\Models\CategoryModel;
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
use App\Models\TransactionModel;
use App\Models\TrxdetailModel;
use App\Models\TrxpaymentModel;
use App\Models\BookingModel;
use App\Models\BookingdetailModel;
use App\Models\PurchaseModel;
use App\Models\PurchasedetailModel;
use App\Models\PresenceModel;
use App\Models\GroupUserModel;
use Myth\Auth\Models\GroupModel;

class Report extends BaseController
{
    public function test()
    {
        // Calling Models
        $TransactionModel       = new TransactionModel;
        $TrxdetailModel         = new TrxdetailModel;
        $GconfigModel           = new GconfigModel;
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

        $transaction = $TransactionModel->where('date >=', $startdate)->where('date <=', $enddate)->find();

        $summary = array_sum(array_column($transaction, 'value'));
        $discounts = array();
        $transactionarr = array();

        $transactions = array();
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
                        $discounttrxpersen[]    = ((int)$trxdetail['value'] * (int)$trxdetail['qty']) - ((int)$trx['value'] + (int)$trxdetail['discvar']);
                    }
                    $discountvariant[]          = $trxdetail['discvar'];
                    $discountpoin[]             = $trx['pointused'];
                }
            }

            $transactiondisc = array_sum($discounttrx) +  array_sum($discounttrxpersen);
            $variantdisc     = array_sum($discountvariant);
            $poindisc        = array_sum($discountpoin);

            $discounts[] = [
                'trxdisc'       => $transactiondisc,
                'variantdis'    => $variantdisc,
                'poindisc'      => $poindisc,
            ];

            $date = date_create($trx['date']);
            $transactions[] = [
                'date'      =>  date_format($date, "d/m/Y"),
                'value'     => $trx['value'],
            ];
        }


        $transactionarr[] = $transactions;
        $trxvar = array_sum(array_column($discounts, 'variantdis'));
        $trxdis = array_sum(array_column($discounts, 'trxdisc'));
        $dispoint = array_sum(array_column($discounts, 'poindisc'));

        $grossales = (int)$summary + (int)$trxvar + (int)$trxdis + (int)$dispoint;

        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.salesreport');
        $data['description']    = lang('Global.transactionListDesc');
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);
        $data['transactions']   = $transactions;
        $data['transactionarr'] = $transactionarr;
        $data['result']         = $summary;
        $data['gross']          = $grossales;
    }

    public function penjualan()
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
        $discount = array();
        $discounttrx = array();
        $discounttrxpersen = array();
        $discountvariant = array();
        $discountpoin = array();
        for ($date = $startdate; $date <= $enddate; $date += (86400)) {
            if ($this->data['outletPick'] === null) {
                $transaction = $TransactionModel->where('date >=', date('Y-m-d 00:00:00', $date))->where('date <=', date('Y-m-d 23:59:59', $date))->find();
            } else {
                $transaction = $TransactionModel->where('date >=', date('Y-m-d 00:00:00', $date))->where('date <=', date('Y-m-d 23:59:59', $date))->where('outletid', $this->data['outletPick'])->find();
            }
            $trxdetails  = $TrxdetailModel->findAll();
            $summary = array_sum(array_column($transaction, 'value'));
            $variants    = $VariantModel->findAll();

            foreach ($transaction as $trx) {
                foreach ($trxdetails as $trxdetail) {
                    if ($trx['id'] == $trxdetail['transactionid']) {
                        if ($trx['disctype'] === "0") {
                            $discounttrx[]          = $trx['discvalue'];
                        }
                        if ($trx['disctype'] !== "0") {
                            $sub =  ((int)$trxdetail['value'] * (int)$trxdetail['qty']);
                            $discounttrxpersen[]    =  ((int)$trx['discvalue'] / 100) * (int)$sub;
                        }
                        $discountvariant[]          = $trxdetail['discvar'];
                        $discountpoin[]             = $trx['pointused'];
                    }
                }
            }
            $transactions[] = [
                'date'      => date('d/m/y', $date),
                'value'     => $summary,
            ];
        }


        $transactiondisc = (int)(array_sum($discounttrx)) + (int)(array_sum($discounttrxpersen));
        $variantdisc     = array_sum($discountvariant);
        $poindisc        = array_sum($discountpoin);

        $dicount[] = [
            'trxdisc'       => $transactiondisc,
            'variantdis'    => $variantdisc,
            'poindisc'      => $poindisc,
        ];

        $transactionarr[] = $transactions;

        $salesresult = array_sum(array_column($transactions, 'value'));

        $grossales = $salesresult + $variantdisc +  $transactiondisc +  $poindisc;

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

        return view('Views/report/penjualan', $data);
    }

    public function keuntungan()
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
            if ($this->data['outletPick'] === null) {
                $transaction = $TransactionModel->where('date >=', date('Y-m-d 00:00:00', $date))->where('date <=', date('Y-m-d 23:59:59', $date))->find();
            } else {
                $transaction = $TransactionModel->where('date >=', date('Y-m-d 00:00:00', $date))->where('date <=', date('Y-m-d 23:59:59', $date))->where('outletid', $this->data['outletPick'])->find();
            }
            $trxdetails  = $TrxdetailModel->findAll();
            $variants    = $VariantModel->findAll();

            $summary = array_sum(array_column($transaction, 'value'));
            $marginmodals = array();
            $margindasars = array();

            foreach ($transaction as $trx) {
                foreach ($trxdetails as $trxdetail) {
                    if ($trx['id'] === $trxdetail['transactionid']) {
                        $marginmodal = (int)$trxdetail['marginmodal'] * (int)$trxdetail['qty'];
                        $margindasar = (int)$trxdetail['margindasar'] * (int)$trxdetail['qty'];
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

        // Parsing Data to View
        $data                       = $this->data;
        $data['title']              = lang('Global.profitreport');
        $data['description']        = lang('Global.profitListDesc');
        $data['transactions']       = $transactions;
        $data['modals']             = $keuntunganmodal;
        $data['dasars']             = $keuntungandasar;
        $data['penjualanDasar']     = $trxvalue;
        $data['penjualanModal']     = $trxvalue;
        $data['startdate']          = $startdate;
        $data['enddate']            = $enddate;

        return view('Views/report/keuntungan', $data);
    }

    public function diskon()
    {

        // Calling Models
        $TransactionModel       = new TransactionModel;
        $TrxdetailModel         = new TrxdetailModel;
        $GconfigModel           = new GconfigModel;
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

        $transactions = array();
        if ($this->data['outletPick'] === null) {
            $transaction = $TransactionModel->where('date >=', $startdate)->where('date <=', $enddate)->find();
        } else {
            $transaction = $TransactionModel->where('date >=', $startdate)->where('date <=', $enddate)->where('outletid', $this->data['outletPick'])->find();
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
                        $sub =  ((int)$trxdetail['value'] * (int)$trxdetail['qty']);
                        $discounttrxpersen[]    =  (int)$sub * ((int)$trx['discvalue'] / 100);
                    }
                    $discountvariant[]          = $trxdetail['discvar'];
                    $discountpoin[]             = $trx['pointused'];
                }
            }

            $transactiondisc = (int)(array_sum($discounttrx)) + (int)(array_sum($discounttrxpersen));
            $variantdisc     = array_sum($discountvariant);
            $poindisc        = array_sum($discountpoin);

            $transactions[] = [
                'id'            => $trx['id'],
                'trxdisc'       => $transactiondisc,
                'variantdis'    => $variantdisc,
                'poindisc'      => $poindisc,
            ];
        }

        $trxvar = array_sum(array_column($transactions, 'variantdis'));
        $trxdis = array_sum(array_column($transactions, 'trxdisc'));
        $dispoint = array_sum(array_column($transactions, 'poindisc'));


        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.discountreport');
        $data['description']    = lang('Global.profitListDesc');
        $data['transactions']   = $transactions;
        $data['trxvardis']      = $trxvar;
        $data['trxdisc']        = $trxdis;
        $data['poindisc']       = $dispoint;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);


        return view('Views/report/diskon', $data);
    }

    public function payment()
    {

        $db                     = \Config\Database::connect();
        $PaymentModel           = new PaymentModel;
        $TrxpaymentModel        = new TrxpaymentModel;
        $TransactionModel       = new TransactionModel;

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

            $this->db       = \Config\Database::connect();
            $validation     = \Config\Services::validation();
            $this->builder  = $this->db->table('payment');
            $this->config   = config('Auth');
            $this->auth     = service('authentication');
            $pager          = \Config\Services::pager();


            $inputsearch    = $this->request->getGet('search');

            if (!empty($inputsearch)) {
                $payments   = $PaymentModel->like('name', $inputsearch)->orderBy('id', 'DESC')->paginate(20, 'reportpayment');
            } else {
                $payments   = $PaymentModel->orderBy('id', 'DESC')->paginate(20, 'reportpayment');
            }

            $trxpayments = $TrxpaymentModel->findAll();
            $transactions = $TransactionModel->where('outletid', $this->data['outletPick'])->where('date >=', $startdate)->where('date <=', $enddate)->find();
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

            $payresult = array_sum(array_column($pay, 'pvalue'));

            // Parsing Data to View
            $data                   = $this->data;
            $data['title']          = lang('Global.paymentreport');
            $data['description']    = lang('Global.paymentListDesc');
            $data['payments']       = $pay;
            $data['startdate']      = strtotime($startdate);
            $data['enddate']        = strtotime($enddate);
            $data['total']          = $payresult;
            $data['pager']          = $PaymentModel->pager;

            return view('Views/report/payment', $data);
        } else {
            return redirect()->to('');
        }
    }

    public function product()
    {

        // Calling models
        $db                 = \Config\Database::connect();
        $ProductModel       = new ProductModel();

        $this->db       = \Config\Database::connect();
        $validation     = \Config\Services::validation();
        $this->builder  = $this->db->table('product');
        $this->config   = config('Auth');
        $this->auth     = service('authentication');
        $pager          = \Config\Services::pager();

        // Search Filter
        $inputsearch    = $this->request->getGet('search');
        if (!empty($inputsearch)) {
            $products   = $ProductModel->like('name', $inputsearch)->orderBy('id', 'DESC')->paginate(20, 'product');
        } else {
            $products   = $ProductModel->orderBy('id', 'DESC')->paginate(20, 'product');
        }

        // Populating Data
        $input = $this->request->getGet('daterange');

        if (!empty($input)) {
            $daterange = explode(' - ', $input);
            $startdate = $daterange[0];
            $enddate = $daterange[1];
        } else {
            $startdate = date('Y-m-1');
            $enddate = date('Y-m-t');
        }

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

            $trxpro   = $db->table('transaction');
            $trxpro->where('date >=', $startdate)->where('date <=', $enddate);
            $protrans   = $trxpro->select('product.id as id, transaction.id as trxid, transaction.date as date, transaction.disctype as disctype, transaction.discvalue as discval, transaction.pointused as redempoin, transaction.value as total, product.name as product, category.name as category, variant.name as variant, trxdetail.qty as qty, variant.hargamodal as modal, variant.id as varid, variant.hargajual as jual, trxdetail.value as trxdetval, trxdetail.discvar as discvar, trxdetail.marginmodal as marginmodal, outlet.name as outlet, outlet.address as address, bundle.name as bundle');
            $protrans   = $trxpro->join('trxdetail', 'transaction.id = trxdetail.transactionid', 'left');
            $protrans   = $trxpro->join('users', 'transaction.userid = users.id', 'left');
            $protrans   = $trxpro->join('outlet', 'transaction.outletid = outlet.id', 'left');
            $protrans   = $trxpro->join('member', 'transaction.memberid = member.id', 'left');
            $protrans   = $trxpro->join('trxpayment', 'trxdetail.transactionid = trxpayment.transactionid', 'left');
            $protrans   = $trxpro->join('bundle', 'trxdetail.bundleid = bundle.id', 'left');
            $protrans   = $trxpro->join('variant', 'trxdetail.variantid = variant.id', 'left');
            $protrans   = $trxpro->join('payment', 'trxpayment.paymentid = payment.id', 'left');
            $protrans   = $trxpro->join('product', 'variant.productid = product.id', 'left');
            $protrans   = $trxpro->join('category', 'product.catid = category.id', 'left');
            $protrans   = $trxpro->where('trxdetail.variantid !=', 0);
            $protrans   = $trxpro->where('transaction.outletid', $this->data['outletPick']);
            $protrans   = $trxpro->orderBy('transaction.date', 'DESC');
            $protrans   = $trxpro->get();
            $protrans   = $protrans->getResultArray();
        }

        $prods = [];
        $transval = [];
        $margin = [];
        foreach ($protrans as $proval) {
            $prods[] = [
                'id'            => $proval['id'],
                'trxid'         => $proval['trxid'],
                'product'       => $proval['product'],
                'category'      => $proval['category'],
                'value'         => (int)$proval['total'],
                'marginmo'      => $proval['marginmodal'],
                'qty'           => $proval['qty'],
                'gross'         => (int)$proval['trxdetval'] * (int)$proval['qty'],
            ];

            $margin[] = [
                'id'        => $proval['trxid'],
                'proid'     => $proval['id'],
                'margin'    => ($proval['trxdetval'] + $proval['discvar']) * $proval['qty'],
            ];

            $transval[] = [
                'id'    => $proval['trxid'],
                'proid' => $proval['id'],
                'value' => $proval['trxdetval'],
            ];
        }

        $totalgross = array_sum(array_column($margin, 'margin'));

        // total discvar transaction value
        $margintotal = [];
        foreach ($margin as $marginval) {
            if (!isset($margintotal[$marginval['proid'] . $marginval['margin']])) {
                $margintotal[$marginval['proid'] . $marginval['margin']] = $marginval;
            } else {
                $margintotal[$marginval['proid'] . $marginval['margin']]['margin'] += $marginval['margin'];
            }
        }
        $margintotal = array_values($margintotal);

        // total discont transaction value
        $totaltrx = [];
        foreach ($transval as $trans) {
            if (!isset($totaltrx[$trans['id'] . $trans['value']])) {
                $totaltrx[$trans['id'] . $trans['value']] = $trans;
            } else {
                $totaltrx[$trans['id'] . $trans['value']]['value'] = $trans['value'];
            }
        }
        $totaltrx = array_values($totaltrx);

        // Gross Value
        $grossval = [];
        foreach ($margintotal as $margin) {
            $grossval[] = [
                'id' => $margin['id'],
                'proid' => $margin['proid'],
                'value' => $margin['margin'],
            ];
        }

        // Gross Result
        $gross = [];
        foreach ($grossval as $vars) {
            if (!isset($gross[$vars['proid'] . $vars['value']])) {
                $gross[$vars['proid']] = $vars;
            } else {
                $gross[$vars['proid']]['value'] += $vars['value'];
            }
        }
        $gross = array_values($gross);

        // Produk Value
        $produks = [];
        foreach ($prods as $vars) {
            if (!isset($produks[$vars['id'] . $vars['product']])) {
                $produks[$vars['id'] . $vars['product']] = $vars;
            } else {
                $produks[$vars['id'] . $vars['product']]['value'] += $vars['value'];
                $produks[$vars['id'] . $vars['product']]['qty'] += $vars['qty'];
            }
        }
        $produks = array_values($produks);

        // groos total
        $grossval = array_sum(array_column($gross, 'value'));

        // Total Stock
        $stoktotal = array_sum(array_column($produks, 'qty'));

        // Total Sales Without trx disc, bundle & poin disc
        $salestotal = array_sum(array_column($produks, 'value'));


        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.productreport');
        $data['description']    = lang('Global.productListDesc');
        $data['transactions']   = $protrans;
        $data['products']       = $produks;
        $data['totalstock']     = $stoktotal;
        $data['salestotal']     = $salestotal;
        $data['grosstotal']     = $totalgross;
        $data['netsales']       = $salestotal;
        $data['gross']          = $gross;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);
        $data['pager']          = $ProductModel->pager;

        return view('Views/report/product', $data);
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
        $presences  = $PresenceModel->findAll();
        $users      = $UserModel->findAll();
        $usergroups = $UserGroupModel->findAll();
        $groups     = $GroupModel->findAll();

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

        // Sum Total Presence
        $admin = [];
        $presen = '';
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

        // parsing data to view
        $data                   = $this->data;
        $data['title']          = lang('Global.presencereport');
        $data['description']    = lang('Global.presenceListDesc');
        $data['presences']      = $admin;
        $data['present']        = $presen;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);

        return view('Views/report/presence', $data);
    }

    public function presencedetail($id)
    {
        // Calling Model
        $PresenceModel      = new PresenceModel;
        $pager              = \Config\Services::pager();

        $datas = explode('-', $id);

        $iduser = $datas[0];
        $starts = $datas[1];
        $ends   = $datas[2];

        if (!empty($iduser)) {
            $presences  = $PresenceModel->where('datetime >=', $starts)->where('datetime <=', $ends)->where('userid', $iduser)->orderBy('id', 'DESC')->paginate(20, 'reportpresecendet');
        } else {
            $presences  = $PresenceModel->where('datetime >=', $starts)->where('datetime <=', $ends)->orderBy('id', 'DESC')->paginate(20, 'reportpresencedet');
        }

        // parsing data to view
        $data                   = $this->data;
        $data['title']          = lang('Global.presence');
        $data['description']    = lang('Global.presencedetailListDesc');
        $data['presences']      = $presences;
        $data['pager']          = $PresenceModel->pager;

        return view('Views/report/presencedetail', $data);
    }

    public function employe()
    {

        // Calling Model
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

        // parsing data to view
        $data                   = $this->data;
        $data['title']          = lang('Global.employereport');
        $data['description']    = lang('Global.employeListDesc');
        $data['employetrx']     = $produk;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);

        return view('Views/report/employe', $data);
    }

    public function customer()
    {

        // Calling Models
        $db                 = \Config\Database::connect();
        $TransactionModel   = new TransactionModel;
        $MemberModel        = new MemberModel;
        $DebtModel          = new DebtModel;
        $OutletModel        = new OutletModel;

        $this->db       = \Config\Database::connect();
        $pager          = \Config\Services::pager();

        // Search Filter
        $inputsearch    = $this->request->getGet('search');
        if (!empty($inputsearch)) {
            $members   = $MemberModel->like('name', $inputsearch)->orderBy('id', 'DESC')->paginate(20, 'reportmember');
        } else {
            $members   = $MemberModel->orderBy('id', 'DESC')->paginate(20, 'reportmember');
        }

        // Populating Data
        $debts              = $DebtModel->findAll();

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

            // Parsing Data to View
            $data                       = $this->data;
            $data['title']              = lang('Global.customer');
            $data['description']        = lang('Global.customerListDesc');
            $data['customers']          = $customer;
            $data['startdate']          = strtotime($startdate);
            $data['enddate']            = strtotime($enddate);

            return view('Views/report/customer', $data);
        } else {
            return redirect()->to('');
        }
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

        $transactions = $TransactionModel->where('date >=', $startdate)->where('date <=', $enddate)->find();

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
                            'value' => (int)$trxdetail['qty'] * (int)$bundle['price'],
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

        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.bundlereport');
        $data['description']    = lang('Global.bundleListDesc');
        $data['bundles']        = $paket;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);

        return view('Views/report/bundle', $data);
    }

    public function category()
    {

        // Calling models
        $CategoryModel      = new CategoryModel();
        $OutletModel        = new OutletModel();

        // $TransactionModel   = new TransactionModel();
        // $TrxdetailModel     = new TrxdetailModel();
        // $ProductModel       = new ProductModel();
        // $VariantModel       = new VariantModel();
        // $StockModel         = new StockModel();
        // $BrandModel         = new BrandModel();
        // $BundleModel        = new BundleModel();

        // Populating Data
        // $trxdetails = $TrxdetailModel->findAll();
        // $products   = $ProductModel->findAll();
        // $category   = $CategoryModel->findAll();
        // $variants   = $VariantModel->findAll();
        // $brands     = $BrandModel->findAll();
        // $bundles    = $BundleModel->findAll();

        $db                 = \Config\Database::connect();

        // Search Category
        $this->db       = \Config\Database::connect();
        $validation     = \Config\Services::validation();
        $this->builder  = $this->db->table('category');
        $this->config   = config('Auth');
        $this->auth     = service('authentication');
        $pager          = \Config\Services::pager();

        // Search Filter
        $inputsearch    = $this->request->getGet('search');
        if (!empty($inputsearch)) {
            $category   = $CategoryModel->like('name', $inputsearch)->orderBy('id', 'DESC')->paginate(20, 'reportcategory');
        } else {
            $category   = $CategoryModel->orderBy('id', 'DESC')->paginate(20, 'reportcategory');
        }


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
        if ($this->data['outletPick'] === null) {
            return redirect()->back()->with('error', lang('Global.chooseoutlet'));
        } else {
            $outlet     = $OutletModel->find($this->data['outletPick']);
            $outletname = $outlet['name'];

            $trxpro   = $db->table('transaction');
            $trxpro->where('date >=', $startdate)->where('date <=', $enddate);
            $protrans   = $trxpro->select('product.id as id, transaction.id as trxid, trxdetail.id as trxdetid, category.id as catid, transaction.date as date, transaction.disctype as disctype, transaction.discvalue as discval, transaction.pointused as redempoin, transaction.value as total, product.name as product, category.name as category, variant.name as variant, trxdetail.qty as qty, variant.hargamodal as modal, variant.id as varid, variant.hargajual as jual, trxdetail.value as trxdetval, trxdetail.discvar as discvar, trxdetail.marginmodal as marginmodal, outlet.name as outlet, outlet.address as address, bundle.name as bundle');
            $protrans   = $trxpro->join('trxdetail', 'transaction.id = trxdetail.transactionid', 'left');
            $protrans   = $trxpro->join('users', 'transaction.userid = users.id', 'left');
            $protrans   = $trxpro->join('outlet', 'transaction.outletid = outlet.id', 'left');
            $protrans   = $trxpro->join('member', 'transaction.memberid = member.id', 'left');
            $protrans   = $trxpro->join('trxpayment', 'trxdetail.transactionid = trxpayment.transactionid', 'left');
            $protrans   = $trxpro->join('bundle', 'trxdetail.bundleid = bundle.id', 'left');
            $protrans   = $trxpro->join('variant', 'trxdetail.variantid = variant.id', 'left');
            $protrans   = $trxpro->join('payment', 'trxpayment.paymentid = payment.id', 'left');
            $protrans   = $trxpro->join('product', 'variant.productid = product.id', 'left');
            $protrans   = $trxpro->join('category', 'product.catid = category.id', 'left');
            $protrans   = $trxpro->where('trxdetail.variantid !=', 0);
            $protrans   = $trxpro->where('transaction.outletid', $this->data['outletPick']);
            $protrans   = $trxpro->orderBy('transaction.date', 'DESC');
            $protrans   = $trxpro->get();
            $protrans   = $protrans->getResultArray();
        }

        // Net Sales Code (Penjualan Bersih)
        $kategori = [];
        foreach ($protrans as $catetrans) {
            if (!isset($kategori[$catetrans['trxid']])) {
                $kategori[$catetrans['trxid']] = $catetrans;
            }
        }
        $kategori = array_values($kategori);

        // total net sales (Total Penjualan Bersih)
        // Gross Sales Code (Penjualan Kotor)
        $trxgross = [];
        foreach ($protrans as $catetrx) {
            $trxgross[] = [
                'catid'     => $catetrx['catid'],
                'cate'      => $catetrx['category'],
                'netval'    => $catetrx['trxdetval'] * $catetrx['qty'],
                'value'     => ($catetrx['trxdetval'] + $catetrx['discvar']) * $catetrx['qty'],
                'qty'       => $catetrx['qty'],
            ];
        }

        // data all category
        $catedata = [];
        foreach ($trxgross as $vars) {
            if (!isset($catedata[$vars['catid'] . $vars['cate']])) {
                $catedata[$vars['catid'] . $vars['cate']] = $vars;
            } else {
                $catedata[$vars['catid'] . $vars['cate']]['value'] += $vars['value'];
                $catedata[$vars['catid'] . $vars['cate']]['qty'] += $vars['qty'];
                $catedata[$vars['catid'] . $vars['cate']]['netval'] += $vars['netval'];
            }
        }
        $catedata = array_values($catedata);


        $totalnetsales = array_sum(array_column($catedata, 'netval'));

        // total gross sales category
        $totalcatgross =  array_sum(array_column($trxgross, 'value'));
        // total cat sales item
        $totalsalesitem = array_sum(array_column($trxgross, 'qty'));

        // ================================ Old Code ============================== //
        // // Filter Data Outlet & daterange 
        // if ($this->data['outletPick'] === null) {
        //     $transactions = $TransactionModel->where('date >=', $startdate)->where('date <=', $enddate)->find();
        // } else {
        //     $transactions = $TransactionModel->where('date >=', $startdate)->where('date <=', $enddate)->where('outletid', $this->data['outletPick'])->find();
        // }

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
        //                                             'id'            => $product['catid'],
        //                                             'trxid'         => $transaction['id'],
        //                                             'product'       => $product['name'],
        //                                             'category'      => $cat['name'],
        //                                             'value'         => ((int)$trxdetail['value'] + (int)$trxdetail['discvar']) * (int)$trxdetail['qty'],
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
        //     if (!isset($produk[$vars['id'] . $vars['category']])) {
        //         $produk[$vars['id'] . $vars['category']] = $vars;
        //     } else {
        //         $produk[$vars['id'] . $vars['category']]['value'] += $vars['value'];
        //         $produk[$vars['id'] . $vars['category']]['qty'] += $vars['qty'];
        //     }
        // }
        // $produk = array_values($produk);


        // // Total Stock
        // $stoktotal = array_sum(array_column($produk, 'qty'));

        // // Total Sales
        // $salestotal = array_sum(array_column($produk, 'value'));

        // // Total Gross
        // $grosstotal = array_sum(array_column($produk, 'value'));
        // ================================ End Old Code ============================== //

        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.categoryreport');
        $data['description']    = lang('Global.categoryListDesc');
        $data['catedata']       = $catedata;
        $data['netsales']       = $totalnetsales;
        $data['gross']          = $totalcatgross;
        $data['qty']            = $totalsalesitem;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);
        $data['pager']          = $CategoryModel->pager;

        return view('Views/report/category', $data);
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
            foreach ($outlets as $outlet) {
                if ($outlet['id'] === $this->data['outletPick']) {
                    $outletname = $outlet['name'];
                }
            }
        } else {
            $stocks      = $StockModel->where('outletid', $this->data['outletPick'])->find();
            foreach ($outlets as $outlet) {
                $outletname = $outlet['name'];
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

        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.category');
        $data['description']    = lang('Global.categoryListDesc');
        $data['products']       = $produk;
        $data['stock']          = $stock;
        $data['whole']          = $whole;

        return view('Views/report/stockcategory', $data);
    }
}
