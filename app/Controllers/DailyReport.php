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
    public function index()
    {
        if ($this->data['outletPick'] != null) {
            $pager      = \Config\Services::pager();

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
                $startdate = date('Y-m-1');
                $enddate = date('Y-m-t');
            }

            $today                  = date('Y-m-d') . ' 00:00:01';
            if (!empty($input)) {
                if ($startdate === $enddate) {
                    $dailyreports       = $DailyReportModel->orderby('dateopen', 'DESC')->where('dateopen >=', $startdate . "00:00:00")->where('dateopen <=', $enddate . "23:59:59")->where('outletid', $this->data['outletPick'])->paginate(20, 'dailyreport');
                } else {
                    $dailyreports       = $DailyReportModel->orderBy('dateopen', 'DESC')->where('dateopen >=', $startdate)->where('dateopen <=', $enddate)->where('outletid', $this->data['outletPick'])->paginate(20, 'dailyreport');
                }
            } else {
                $dailyreports           = $DailyReportModel->orderBy('dateopen', 'DESC')->where('outletid', $this->data['outletPick'])->paginate(20, 'dailyreport');
            }

            $lastreport             = end($dailyreports);
            $firstreport            = $dailyreports[0];

            $cashs                  = $CashModel->findAll();
            $payments               = $PaymentModel->findAll();
            $transactions           = $TransactionModel->where('date <=', $firstreport['dateclose'])->where('date >=', $lastreport['dateopen'])->where('outletid', $this->data['outletPick'])->find();
            $trxothers              = $TrxotherModel->where('date <=', $firstreport['dateclose'])->where('date >=', $lastreport['dateopen'])->where('outletid', $this->data['outletPick'])->find();

            $trxid = array();
            $memberid = array();
            foreach ($transactions as $transaction) {
                $trxid[] = $transaction['id'];
                $memberid[] = $transaction['memberid'];
            }

            $outlets                = $OutletModel->findAll();
            $users                  = $UserModel->findAll();

            if (!empty($transactions)) {
                $trxdetails             = $TrxdetailModel->whereIn('transactionid', $trxid)->find();
                $trxpayments            = $TrxpaymentModel->whereIn('transactionid', $trxid)->find();
                $customers              = $MemberModel->find($memberid);

                $variantid = array();
                $bundleid = array();
                foreach ($trxdetails as $trxdetail) {
                    $variantid[] = $trxdetail['variantid'];
                    $bundleid[] = $trxdetail['bundleid'];
                }

                $bundles                = $BundleModel->find($bundleid);
                $bundets                = $BundledetailModel->whereIn('bundleid', $bundleid)->find();

                foreach ($bundets as $bundet) {
                    $variantid[] = $bundet['variantid'];
                }

                $variants               = $VariantModel->find($variantid);

                $productid = array();
                foreach ($variants as $variant) {
                    $productid[] = $variant['productid'];
                }

                $products               = $ProductModel->find($productid);

                // Get Cash Transaction
                $pettycash              = $CashModel->where('name', 'Petty Cash ' . $this->data['outletPick'])->first();
                $cashpayment            = $PaymentModel->where('outletid', $this->data['outletPick'])->where('name', 'Cash')->first();
                $cashtrx                = $TransactionModel->where('paymentid', $cashpayment['id'])->find();

                // Get Non Cash Transaction
                $noncash            = $CashModel->notLike('name', 'Petty Cash')->find();
                $noncashid          = array();
                foreach ($noncash as $nocash) {
                    $noncashid[] = $nocash['id'];
                }
                $noncashpayments    = $PaymentModel->whereIn('cashid', $noncashid)->find();
                $noncashpaymentid   = array();
                foreach ($noncashpayments as $noncashpayment) {
                    $noncashpaymentid[]     = $noncashpayment['id'];
                }
                $noncashtrx                 = $TransactionModel->where('outletid', $this->data['outletPick'])->whereIn('paymentid', $noncashpaymentid)->find();
            } else {
                $trxdetails             = array();
                $trxpayments            = array();
                $customers              = array();
                $bundles                = array();
                $bundets                = array();
                $variants               = array();
                $products               = array();
                $pettycash              = array();
                $cashpayment            = array();
                $cashtrx                = array();
                $noncash                = array();
                $noncashpayments        = array();
                $noncashtrx             = array();
            }

            // Parsing Data to View
            $data                       = $this->data;
            $data['title']              = lang('Global.dailyreport');
            $data['description']        = lang('Global.dailyreportListDesc');
            $data['dailyreports']       = $dailyreports;
            $data['cashpayment']        = $cashpayment;
            $data['noncashpayments']    = $noncashpayments;
            $data['cashtrx']            = $cashtrx;
            $data['noncashtrx']         = $noncashtrx;
            $data['cashs']              = $cashs;
            $data['bundles']            = $bundles;
            $data['bundets']            = $bundets;
            $data['users']              = $users;
            $data['transactions']       = $transactions;
            $data['outlets']            = $outlets;
            $data['payments']           = $payments;
            $data['products']           = $products;
            $data['variants']           = $variants;
            $data['customers']          = $customers;
            $data['trxothers']          = $trxothers;
            $data['trxdetails']         = $trxdetails;
            $data['trxpayments']        = $trxpayments;
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
