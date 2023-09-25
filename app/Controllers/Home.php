<?php

namespace App\Controllers;

use App\Models\BundledetailModel;
use App\Models\BundleModel;
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
use App\models\TrxpaymentModel;
use App\Models\BookingModel;
use App\Models\BookingdetailModel;
use App\Models\PurchaseModel;
use App\Models\PurchasedetailModel;
use App\Models\PresenceModel;
use App\Models\GroupUserModel;
use Myth\Auth\Models\GroupModel;

class Home extends BaseController
{
    public function index()
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
        $input = $this->request->getGet('daterange');

        $trxdetails = $TrxdetailModel->findAll();
        $products   = $ProductModel->findAll();
        $category   = $CategoryModel->findAll();
        $variants   = $VariantModel->findAll();
        $stocks     = $StockModel->findAll();
        $bundles    = $BundleModel->findAll();
        $bundets    = $BundledetailModel->findAll();

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
            $transactions = $TransactionModel->where('date >=', $startdate)->where('date <=', $enddate)->find();
        } else {
            $transactions = $TransactionModel->where('date >=', $startdate)->where('date <=', $enddate)->where('outletid',$this->data['outletPick'])->find();
        }

        // Sales Value
        $sales = array();
        $id = [];
        foreach ($transactions as $transaction){
            $sales[] = [
                'id'        => $transaction['id'],
                'date'      => $transaction['date'],
                'value'     => $transaction['value'],
                'pointused' => $transaction['pointused'],
                'disctype'  => $transaction['disctype'],
                'discvalue' => $transaction['discvalue'],
            ];
            $id[] = $transaction['id'];
        }

        $subtotal = array();
        foreach ($trxdetails as $trxdeta) {
            $subtotal[] = ($trxdeta['value'] * $trxdeta['qty']) + $trxdeta['discvar'];
        }
        $subsum = array_sum($subtotal);

        $discvar = array();
        $discval = array();
        foreach ($transactions as $trxs) {
            foreach ($trxdetails as $trxdets) {
                if ($trxdets['transactionid'] === $trxs['id']) {
                    $subtotals = $trxdets['qty'] * $trxdets['value'];
                    $discvar[] = $trxdets['discvar'];

                    if ($trxs['disctype'] === "0") {
                        $discval[] = $trxs['discvalue'];
                    } else {
                        $discval[] = $subtotals * $trxs['discvalue'];
                    }
                }
            }
        }
        $discvarsum = array_sum($discvar);
        $discvalsum = array_sum($discval);
        $totaldisc  = $discvarsum + $discvalsum;

        $trxamount = count($id);

        // Profit Value
        $qtytrx = array();
        foreach ($transactions as $trx){
            foreach ($trxdetails as $trxdetail){
                if($trx['id'] === $trxdetail['transactionid']){
                    $marginmodal    = $trxdetail['marginmodal'];
                    $margindasar    = $trxdetail['margindasar'];
                    $qtytrx[]       = $trxdetail['qty'];
                    $marginmodals[] = $marginmodal;
                    $margindasars[] = $margindasar;
                }
            }
        }
        
        $marginmodalsum = array_sum($marginmodals);
        $margindasarsum = array_sum($margindasars);

        $summary = array_sum(array_column($sales,'value'));

        $transactions[] = [
            'value'     => $summary,
            'modal'     => $marginmodalsum,
            'dasar'     => $margindasarsum,
        ];  
    
        $keuntunganmodal = array_sum(array_column($transactions, 'modal'));
        $keuntungandasar = array_sum(array_column($transactions, 'dasar'));
        $trxvalue        = array_sum(array_column($transactions, 'value'));

        // Products Sales
        $qtytrxsum = array_sum($qtytrx);

        // Sales Details
        $pointusedsum = array_sum(array_column($sales, 'pointused'));
        $gross           = $subsum;

        $data                   = $this->data;
        $data['title']          = lang('Global.dashboard');
        $data['description']    = lang('Global.dashdesc');
        $data['sales']          = $summary;
        $data['profit']         = $keuntungandasar;
        $data['trxamount']      = $trxamount;
        $data['qtytrxsum']      = $qtytrxsum;
        $data['pointusedsum']   = $pointusedsum;
        $data['totaldisc']      = $totaldisc;
        $data['gross']          = $gross;
        
        return view('dashboard', $data);
    }

    public function outletses($id)
    {
        $session = \Config\Services::session();

        if ($id === '0') {
            $session->remove('outlet');
        } else {
            if ($session->get('outlet') != null) {
                $session->remove('outlet');
            }
            $session->set('outlet', $id);
        }

        return redirect()->back();
    }

    public function trial()
    {
        phpinfo();
    }
}
