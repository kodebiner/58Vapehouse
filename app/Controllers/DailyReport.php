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
use App\models\TrxpaymentModel;
use App\Models\DebtModel;
use App\Models\DailyReportModel;

class DailyReport extends BaseController
{
    public function index()
    {
        // Calling Models
        $BundleModel            = new BundleModel();
        $BundledetModel         = new BundledetailModel();
        $CashModel              = new CashModel();
        $OutletModel            = new OutletModel();
        $UserModel              = new UserModel();
        $PaymentModel           = new PaymentModel();
        $ProductModel           = new ProductModel();
        $VariantModel           = new VariantModel();
        $StockModel             = new StockModel();
        $TransactionModel       = new TransactionModel();
        $TrxotherModel          = new TrxotherModel();
        $TrxpaymentModel        = new TrxpaymentModel();
        $DebtModel              = new DebtModel();

        // Populating Data
        $bundles                = $BundleModel->findAll();
        $bundets                = $BundledetModel->findAll();
        $cash                   = $CashModel->findAll();
        $outlets                = $OutletModel->findAll();
        $users                  = $UserModel->findAll();
        $payments               = $PaymentModel->findAll();
        $products               = $ProductModel->findAll();
        $variants               = $VariantModel->findAll();
        $stocks                 = $StockModel->findAll();
        $transactions           = $TransactionModel->findAll();
        $trxothers              = $TrxotherModel->findAll();
        $trxpayments            = $TrxpaymentModel->findAll();

        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.topup');
        $data['description']    = lang('Global.topupListDesc');
        $data['bundles']        = $bundles;
        $data['bundets']        = $bundets;
        $data['cash']           = $cash;
        $data['users']          = $users;
        $data['transactions']   = $transactions;
        $data['outlets']        = $outlets;
        $data['payments']       = $payments;
        $data['products']       = $products;
        $data['variants']       = $variants;
        $data['stocks']         = $stocks;
        $data['trxothers']      = $trxothers;
        $data['trxpayments']    = $trxpayments;

        return view('Views/topup', $data);
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

        // Creating Daily Report
        $today                  = date('Y-m-d') .' 00:00:01';
        $dailyreports           = $DailyReportModel->where('dateopen >', $today)->where('outletid', $this->data['outletPick'])->find();
        $date                   = date_create();
        $tanggal                = date_format($date,'Y-m-d H:i:s');
        
        if (empty($dailyreports)) {
            $datadayrep = [
                'dateopen'      => $tanggal,
                'useridopen'    => $this->data['uid'],
                'outletid'      => $this->data['outletPick'],
                'initialcash'   => $input['initialcash'],
            ];
            $DailyReportModel->save($datadayrep);

            // Insert Cash
            $cash               = $CashModel->where('outletid', $this->data['outletPick'])->first();
            $datacash = [
                'id'            => $cash['id'],
                'qty'           => $input['initialcash'] + $cash['qty'],
            ];
            $CashModel->save($datacash);
        }

        $dayrep = $DailyReportModel->where('dateopen >', $today)->find();

        // Return
        return redirect()->back();
    }

    public function close($id) {
        // Calling Models
        $UserModel              = new UserModel();
        $DailyReportModel       = new DailyReportModel();

        // Initialize
        $input                  = $this->request->getPost();

        // Populating Data
        $users                  = $UserModel->findAll();

        // Creating Daily Report
        $today                  = date('Y-m-d') .' 00:00:01';
        $dailyreports           = $DailyReportModel->where('dateopen >', $today)->find();
        $date                   = date_create();
        $tanggal                = date_format($date,'Y-m-d H:i:s');
        
        $closedayrep = [
            'id'                => $id,
            'dateclose'         => $tanggal,
            'useridclose'       => $this->data['uid'],
        ];
        $DailyReportModel->save($closedayrep);

        // Return
        return redirect()->back();
    }
}