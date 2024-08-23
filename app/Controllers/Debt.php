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
use App\Models\DailyReportModel;

class Debt extends BaseController
{
    protected $data;
    protected $db, $builder;
    protected $auth;
    protected $config;
    
    public function indextrx()
    {
        $db         = \Config\Database::connect();
        $pager      = \Config\Services::pager();

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
        $TransactionModel       = new TransactionModel;
        $TrxdetailModel         = new TrxdetailModel;
        $TrxpaymentModel        = new TrxpaymentModel;
        $DebtModel              = new DebtModel;

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
            // if (!empty($input)) {
                $transactions = $TransactionModel->orderBy('date', 'DESC')->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->paginate(20, 'trxhistory');
            // } else {
            //     $transactions = $TransactionModel->orderBy('date', 'DESC')->paginate(20, 'trxhistory');
            // }
        } else {
            // if (!empty($input)) {
                $transactions = $TransactionModel->orderBy('date', 'DESC')->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->where('outletid', $this->data['outletPick'])->paginate(20, 'trxhistory');
            // } else {
            //     $transactions = $TransactionModel->orderBy('date', 'DESC')->where('outletid', $this->data['outletPick'])->paginate(20, 'trxhistory');
            // }
        }

        $transactiondata    = [];

        $trxid = array();
        $memberid = array();
        foreach ($transactions as $transaction) {
            $trxid[] = $transaction['id'];
            if ($transaction['memberid'] != '0') {
                $memberid[] = $transaction['memberid'];
            }
        }

        $bundles                = $BundleModel->findAll();
        $bundets                = $BundledetModel->findAll();
        $cash                   = $CashModel->findAll();
        $outlets                = $OutletModel->findAll();
        $users                  = $UserModel->findAll();
        $payments               = $PaymentModel->findAll();

        if (!empty($memberid)) {
            $customers              = $MemberModel->find($memberid);
        } else {
            $customers              = array();
        }

        if (!empty($trxid)) {
            $trxdetails             = $TrxdetailModel->whereIn('transactionid', $trxid)->find();
            $trxpayments            = $TrxpaymentModel->whereIn('transactionid', $trxid)->find();
            $debts                  = $DebtModel->whereIn('transactionid', $trxid)->find();
            $variantid = array();
            foreach ($trxdetails as $trxdetail) {
                $variantid[] = $trxdetail['variantid'];
            }
            $productbuilder         = $db->table('variant');
            $productarray           = $productbuilder->select('product.name as product, variant.name as variant, variant.id as id');
            $productarray           = $productbuilder->join('product', 'variant.productid = product.id', 'left');
            $productarray           = $productbuilder->whereIn('variant.id', $variantid);
            $productarray           = $productbuilder->get();
            $productsresult         = $productarray->getResult();
            $products = array();
            foreach ($productsresult as $prod) {
                $products[] = [
                    'id'    => $prod->id,
                    'name'  => $prod->product . ' - ' . $prod->variant
                ];
            }
        } else {
            $trxdetails             = array();
            $trxpayments            = array();
            $debts                  = array();
            $products               = array();
        }

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
        $data['trxdetails']     = $trxdetails;
        $data['trxpayments']    = $trxpayments;
        $data['debts']          = $debts;
        $data['pager']          = $TransactionModel->pager;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);

        return view('Views/trxhistory', $data);
    }

    public function indexdebt()
    {
        $pager      = \Config\Services::pager();

        // Calling Models
        $OutletModel            = new OutletModel;
        $MemberModel            = new MemberModel;
        $PaymentModel           = new PaymentModel;
        $TransactionModel       = new TransactionModel;
        $DebtModel              = new DebtModel;

        // Populating Data
        $outlets                = $OutletModel->findAll();
        $payments               = $PaymentModel->findAll();

        $input = $this->request->getGet('daterange');
        if (!empty($input)) {
            $daterange = explode(' - ', $input);
            $startdate = $daterange[0];
            $enddate = $daterange[1];
        } else {
            // $startdate  = date('Y-m-1' . ' 00:00:00');
            // $enddate    = date('Y-m-t' . ' 23:59:59');
            $startdate  = date('Y-m-1');
            $enddate    = date('Y-m-t');
        }

        // Populating Data
        if (!empty($input)) {
            // if ($startdate === $enddate) {
            //     $debts       = $DebtModel->orderby('deadline', 'DESC')->where('value !=', '0')->where('deadline', $startdate . ' 00:00:00')->where('deadline <=', $enddate . ' 23:59:59')->paginate(20, 'debt');
            // } else {
                $debts = $DebtModel->orderBy('deadline', 'DESC')->where('value !=', '0')->where('deadline >=', $startdate)->where('deadline <=', $enddate)->paginate(30, 'debt');
            // }
        } else {
            $debts = $DebtModel->orderBy('deadline', 'DESC')->where('value !=', '0')->paginate(30, 'debt');
        }

        $trxid      = array();
        $memberid   = array();
        $debtlist   = array();
        foreach ($debts as $debt) {
            $trxid[]    = $debt['transactionid'];
            $memberid[] = $debt['memberid'];
            $debtlist[] = $debt['value'];
        }

        $totaldebt  = array_sum($debtlist);

        if (!empty($trxid)) {
            $transactions           = $TransactionModel->find($trxid);
        } else {
            $transactions           = array();
        }

        if (!empty($memberid)) {
            $customers              = $MemberModel->find($memberid);
        } else {
            $customers              = array();
        }

        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.debt');
        $data['description']    = lang('Global.debtListDesc');
        $data['transactions']   = $transactions;
        $data['outlets']        = $outlets;
        $data['customers']      = $customers;
        $data['debts']          = $debts;
        $data['payments']       = $payments;
        $data['totaldebt']      = $totaldebt;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);
        $data['pager']          = $DebtModel->pager;

        return view('Views/debt', $data);
    }

    public function paydebt($id)
    {
        // Validate Data
        $validation = \Config\Services::validation();

        // Calling Models
        $DebtModel              = new DebtModel;
        $CashModel              = new CashModel;
        $TrxotherModel          = new TrxotherModel;
        $PaymentModel           = new PaymentModel;
        $MemberModel            = new MemberModel;
        $DailyReportModel       = new DailyReportModel;

        // Initialize
        $input = $this->request->getPost();

        // Populating Data
        $debts                  = $DebtModel->find($id);
        $payments               = $PaymentModel->where('id', $input['payment'])->first();
        $customers              = $MemberModel->where('id', $debts['memberid'])->first();
        $cash                   = $CashModel->where('id', $payments['cashid'])->first();

        // Date Time Stamp
        $date                   = date_create();
        $tanggal                = date_format($date, 'Y-m-d H:i:s');

        if ($debts['value'] - $input['value'] != "0") {
            $data = [
                'id'            => $id,
                'value'         => $debts['value'] - $input['value'],
                'deadline'      => $input['duedate' . $id],
            ];
        } else {
            $data = [
                'id'            => $id,
                'value'         => $debts['value'] - $input['value'],
                'deadline'      => NULL,
            ];
        }
        // Save Data Debt
        $DebtModel->save($data);

        // Image Capture
        $img                    = $input['image'];
        $folderPath             = "img/tfproof/";
        $image_parts            = explode(";base64,", $img);
        $image_type_aux         = explode("image/", $image_parts[0]);
        $image_type             = $image_type_aux[1];
        $image_base64           = base64_decode($image_parts[1]);
        $fileName               = uniqid() . '.png';
        $file                   = $folderPath . $fileName;
        file_put_contents($file, $image_base64);

        // Trx Other Cash In
        $cashin = [
            'userid'        => $this->data['uid'],
            'outletid'      => $this->data['outletPick'],
            'cashid'        => $payments['cashid'],
            'description'   => "Debt - " . $customers['name'] . '/' . $customers['phone'],
            'type'          => "0",
            'date'          => $tanggal,
            'qty'           => $input['value'],
            'photo'         => $fileName,
        ];
        $TrxotherModel->save($cashin);

        // Input Value to cash
        $wallet = [
            'id'    => $payments['cashid'],
            'qty'   => (int)$cash['qty'] + (int)$input['value'],
        ];
        $CashModel->save($wallet);

        // Find Data for Daily Report
        $today                  = date('Y-m-d') . ' 00:00:01';
        $dailyreports           = $DailyReportModel->where('outletid', $this->data['outletPick'])->where('dateopen >', $today)->find();
        foreach ($dailyreports as $dayrep) {
            $tcashin = [
                'id'            => $dayrep['id'],
                'totalcashin'   => (int)$dayrep['totalcashin'] + (int)$input['value'],
            ];
            $DailyReportModel->save($tcashin);
        }

        // Return
        return redirect()->back()->with('massage', lang('global.saved'));
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

    public function indexdebtins()
    {
        $pager      = \Config\Services::pager();

        // Calling Model
        $TrxotherModel      = new TrxotherModel;
        $OutletModel        = new OutletModel;

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

        if ($this->data['outletPick'] === null) {
            // $trxothers      = $TrxotherModel->orderBy('id', 'DESC')->like('description', 'Debt')->paginate(20, 'debtpay');

            // if (!empty($input)) {
            //     if ($startdate === $enddate) {
                    $trxothers      = $TrxotherModel->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->orderBy('id', 'DESC')->like('description', 'Debt')->paginate(20, 'debtpay');
            //     } else {
            //         $trxothers      = $TrxotherModel->where('date >=', $startdate)->where('date <=', $enddate)->orderBy('id', 'DESC')->like('description', 'Debt')->paginate(20, 'debtpay');
            //     }
            // }
        } else {
            // $trxothers      = $TrxotherModel->orderBy('id', 'DESC')->like('description', 'Debt')->where('outletid', $this->data['outletPick'])->paginate(20, 'debtpay');

            // if (!empty($input)) {
            //     if ($startdate === $enddate) {
                    $trxothers      = $TrxotherModel->where('outletid', $this->data['outletPick'])->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->orderBy('id', 'DESC')->like('description', 'Debt')->paginate(20, 'debtpay');
            //     } else {
            //         $trxothers      = $TrxotherModel->where('date >=', $startdate)->where('date <=', $enddate)->orderBy('id', 'DESC')->like('description', 'Debt')->where('outletid', $this->data['outletPick'])->paginate(20, 'debtpay');
            //     }
            // }
        }

        $outlets            = $OutletModel->findAll();

        // Parsing data to view
        $data                       = $this->data;
        $data['title']              = lang('Global.debtInstallments');
        $data['description']        = lang('Global.debtInstallmentsListDesc');
        $data['trxothers']          = $trxothers;
        $data['outlets']            = $outlets;
        $data['startdate']          = strtotime($startdate);
        $data['enddate']            = strtotime($enddate);
        $data['pager']              = $TrxotherModel->pager;

        return view('Views/debtpay', $data);
    }

    public function refund($id)
    {
        // Conneting To Database
        $db = \Config\Database::connect();
        $gconfig = new GconfigModel();

        // Getting Data Transaction
        $Gconf = $gconfig->first();
        $exported   = $db->table('transaction');

        $transactionhist   = $exported->select('transaction.id as id, variant.id as varid, member.id as memberid, users.id as userid, payment.id as paymentid,
        outlet.id as outletid, bundle.id as bundleid,trxdetail.qty as qty, transaction.value as total, bundle.price as bprice, variant.hargadasar as vprice,
        transaction.date as date, transaction.disctype as disctype, transaction.discvalue as discval,
        transaction.pointused as redempoin, trxpayment.value as payval, member.name as member, member.trx as trx, product.name as product, variant.name as variant,  
        variant.hargamodal as modal, variant.hargajual as jual, trxdetail.value as trxdetval, trxdetail.discvar as discvar, payment.name as payment,
        outlet.name as outlet,outlet.address as address, bundle.name as bundle, users.username as kasir');

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
        $transactionhist   = $exported->where('transaction.id', $id);
        $transactionhist   = $exported->get();
        $transactionhist   = $transactionhist->getResultArray();

        $trxdata = array();
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
        }

        /*======================================= REFUND DATA =============================================================================*/

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
        $DebtModel              = new DebtModel();
        $BookingModel           = new BookingModel();
        $BookingdetailModel     = new BookingdetailModel();
        $DailyReportModel       = new DailyReportModel();

        // Populating Data
        $bundles                = $BundleModel->findAll();

        // Initialize 
        $date = date('Y-m-d H:i:s');

        $variant    = [];
        $bundles    = [];
        $paymentid  = [];
        $point      = '';
        $total      = '';

        foreach ($transactionhist as $trxhist) {

            // Variant
            if (!empty($trxhist['varid']) && !empty($trxhist['qty'])) {
                $variant[$trxhist['varid']] = $trxhist['qty'];
            }

            // Bundle
            if (!empty($trxhist['bundleid']) && !empty($trxhist['qty'])) {
                $bundles[$trxhist['bundleid']] = $trxhist['qty'];
            }

            // Id Payment
            $paymentid[$trxhist['paymentid']] = $trxhist['payval'];
            $total = $trxhist['total'];

            // Point
            $memberid = $trxhist['memberid'];
            $point = $trxhist['redempoin'];
            $trx = $trxhist['trx'];
        }

        // Poin Setup
        $minimumtrx = $Gconf['poinorder'];
        $poinvalue  = $Gconf['poinvalue'];

        $poinresult = "";
        if ($total >= $minimumtrx) {
            if ($minimumtrx != "0") {
                $value      = (int)$total / (int)$minimumtrx;
            } else {
                $value      = 0;
            }
            $result         = floor($value);
            $poinresult     = (int)$result * (int)$poinvalue;
        } else {
            $poinresult = 0;
        }

        // Refund Variant
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

        // Refund Payment
        $debtval = "";
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
        //         } else {
        //             $debtval = $payval;
        //             $debt = $DebtModel->where('memberid', $memberid)->first();
        //             $debtdata = [
        //                 'id'    => $debt['id'],
        //                 'value' => $debt['value'] - $debtval,
        //             ];
        //             $DebtModel->save($debtdata);
        //         }
        //     }
        // }

        // Delete Transaction Payment
        // $trxpay = $TrxpaymentModel->where('transactionid', $id)->find();
        // foreach ($trxpay as $pay) {
        //     $TrxpaymentModel->delete($pay);
        // }
        $TrxpaymentModel->where('transactionid', $id)->delete();

        // Delete Transaction Payment
        // $debt = $DebtModel->where('transactionid', $id)->find();
        // foreach ($debt as $deb) {
        //     $DebtModel->delete($deb);
        // }
        $DebtModel->where('transactionid', $id)->delete();

        // Delete Tansaction Detail
        // $trxdet = $TrxdetailModel->where('transactionid', $id)->find();
        // foreach ($trxdet as $detail) {
        //     $TrxdetailModel->delete($detail);
        // }
        $TrxdetailModel->where('transactionid', $id)->delete();

        // Delete Transaction
        // $trx = $TransactionModel->find($id);
        // $TransactionModel->delete($trx);
        $TransactionModel->delete($id);

        return redirect()->back()->with('massage', lang('global.deleted'));
    }
}
