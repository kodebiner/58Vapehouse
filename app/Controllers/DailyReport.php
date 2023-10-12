<?php namespace App\Controllers;

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
            $today                  = date('Y-m-d') .' 00:00:01';
            if ($this->data['outletPick'] === null) {
                $dailyreports       = $DailyReportModel->orderBy('dateopen', 'DESC')->findAll();
            } else {
                $dailyreports       = $DailyReportModel->orderBy('dateopen', 'DESC')->where('outletid', $this->data['outletPick'])->find();
            }

            $cashs                  = $CashModel->findAll();
            $payments               = $PaymentModel->findAll();
            $transactions           = $TransactionModel->findAll();
            $variants               = $VariantModel->findAll();
            $bundles                = $BundleModel->findAll();
            $bundets                = $BundledetailModel->findAll();
            $outlets                = $OutletModel->findAll();
            $users                  = $UserModel->findAll();
            $products               = $ProductModel->findAll();
            $variants               = $VariantModel->findAll();
            $customers              = $MemberModel->findAll();
            $trxothers              = $TrxotherModel->findAll();
            $trxpayments            = $TrxpaymentModel->findAll();
            $trxdetails             = $TrxdetailModel->findAll();
            $outlet                 = $OutletModel->find($this->data['outletPick']);

            // Get Cash Transaction
            $pettycash              = $CashModel->where('name', 'Petty Cash '.$outlet['name'])->first();
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

            return view('Views/dailyreport', $data);
        } else {
            return redirect()->to('');
        }
    }

    public function open() {
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
        $tanggal                = date_format($date,'Y-m-d H:i:s');
        
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

    public function close() {
        // Calling Models
        $UserModel              = new UserModel();
        $DailyReportModel       = new DailyReportModel();

        // Initialize
        $input                  = $this->request->getPost();

        // Populating Data
        $users                  = $UserModel->findAll();

        // Creating Daily Report
        $today                  = date('Y-m-d') .' 00:00:01';
        $dailyreport            = $DailyReportModel->where('outletid', $this->data['outletPick'])->where('dateopen >', $today)->first();
        $date                   = date_create();
        $tanggal                = date_format($date,'Y-m-d H:i:s');
        
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