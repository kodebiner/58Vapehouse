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
use App\Models\TrxpaymentModel;
use App\Models\TrxotherModel;
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
        $TrxpaymentModel    = new TrxpaymentModel();
        $TrxotherModel      = new TrxotherModel();
        $PaymentModel       = new PaymentModel();
        $ProductModel       = new ProductModel();
        $CategoryModel      = new CategoryModel();
        $VariantModel       = new VariantModel();
        $StockModel         = new StockModel();
        $BundleModel        = new BundleModel();
        $BundledetailModel  = new BundledetailModel();
        $DebtModel          = new DebtModel();
        $MemberModel        = new MemberModel();
        $StocksModel        = new StockModel();
        $CashModel          = new CashModel();

        // Populating Data
        $input = $this->request->getGet('daterange');
        $trxdetails     = $TrxdetailModel->findAll();
        $trxpayments    = $TrxpaymentModel->findAll();
        $products       = $ProductModel->findAll();
        $category       = $CategoryModel->findAll();
        $variants       = $VariantModel->findAll();
        $debts          = $DebtModel->findAll();
        $customers      = $MemberModel->findAll();
        $bundles        = $BundleModel->findAll();
        $bundets        = $BundledetailModel->findAll();
        $members        = $MemberModel->findAll();
       
       

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
            $trxothers  = $TrxotherModel->where('date >=', $startdate)->where('date <=', $enddate)->find();
            $stocks         = $StockModel->findAll();
            array_multisort(array_column($stocks, 'restock'), SORT_DESC, $stocks);
        } else {
            $transactions = $TransactionModel->where('date >=', $startdate)->where('date <=', $enddate)->where('outletid',$this->data['outletPick'])->find();
            $trxothers  = $TrxotherModel->where('date >=', $startdate)->where('date <=', $enddate)->where('outletid',$this->data['outletPick'])->find();
            $stocks         = $StockModel->where('outletid',$this->data['outletPick'])->find();
            array_multisort(array_column($stocks, 'restock'), SORT_DESC, $stocks);

        }
    
        // Stock Cycle
        $stok           = array_slice($stocks, 0, 3);
        array_multisort(array_column($stok, 'restock'), SORT_DESC, $stok);

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
        
        $salesresult = array_sum(array_column($sales, 'value'));

        // Average trnasaction
        if($salesresult !== 0){
            $salestrx = count($sales);
            $averagedays = $salesresult / $salestrx;
            $sale = sprintf("%.2f", $averagedays);
            $doblesale = ceil($sale);
            $saleaverage = sprintf("%.2f", $doblesale);
        }else{
            $averagedays = "0";
            $sale = sprintf("%.2f", $averagedays);
            $doblesale = ceil($sale);
            $saleaverage = sprintf("%.2f", $doblesale);
        }

        // Average per days
        $now = date_create($startdate); 
        $your_date = date_create($enddate)->modify('+1 day');
        $datediff = date_diff($now,$your_date);
        $days = $datediff->format("%a");
        $dateaverage = ceil($salesresult / (int)$days);
        $resultaveragedays = sprintf("%.2f", $dateaverage);

         // Bussy Days
        $saly = [];
        foreach ($sales as $sale){
        $datesale = date_create($sale['date']);
        $saledate = $datesale->format('Y-m-d');
        $hoursale = $datesale->format('H');
            $saly [] = [
                'date'  => $saledate,
                'value' => $sale['value'],
                'hours' => $hoursale,
                'trx'   => '1',
            ];
        }
        
        $saledet = [];
        foreach ($saly as $sels) {
            if (!isset($saledet[$sels['date']])) {
                $saledet[$sels['date']] = $sels;
            } else {
                $saledet[$sels['date']]['value'] += $sels['value'];
                $saledet[$sels['date']]['trx'] += $sels['trx'];
            }
        }
        $saledet = array_values($saledet);

        array_multisort(array_column($saledet, 'trx'), SORT_DESC, $saledet);
        $daysale = array_slice($saledet, 0, 1);
        $day = 0;
        foreach ($daysale as $days){
            $datesale = date_create($days['date']);
            $day = $datesale->format('l');
        }

        // Bussy Hours
        $hour = [];
        foreach ($sales as $sale){
        $datesale = date_create($sale['date']);
        $saledate = $datesale->format('Y-m-d-H');
        $hoursale = $datesale->format('H');
            $hour [] = [
                'value'     => $sale['value'],
                'datesale'  => $saledate,
                'hours'     => $hoursale,
                'qty'       => '1',
            ];
        }

        $s = [];
        foreach ($hour as $key){
           $s[] = $key['hours'];
        }

        // time transaction by value
        $hoursdata = [];
        foreach ($hour as $sels) {
            if (!isset($hoursdata[$sels['hours']])) {
                $hoursdata[$sels['hours']] = $sels;
            } else {
                $hoursdata[$sels['hours']]['qty'] += $sels['qty'];
            }
        }
        $hoursdata = array_values($hoursdata);
        array_multisort(array_column($hoursdata, 'qty'), SORT_DESC, $hoursdata);
        $time = [];
        foreach ($hoursdata as $data){
            $datesal        = strtotime($data['datesale']);
            $datesalling    = date('H:i',$datesal);
            $time [] = [
                'value' => $data['value'],
                'hours' => $datesalling,
                'x'     => $data['hours'],
                'sum'   => $data['qty'],
            ]; 
        }

        $timehours = array_slice($time, 0, 1);
        $timeH = 0;
        foreach ($timehours as $t){
            if (isset($t)){
                $timeH = $t['hours'];
            }else{
                $timeH = "0";
            }
        }
      
        // Discount
        $discvar    = array();
        $discval    = array();
        $trxsid     = array();

        // bestseller array
        $bestssell  = array();
        $bestpay    = array();

        foreach ($transactions as $trxs) {

            // Discount Total
            foreach ($trxdetails as $trxdets) {
                if ($trxdets['transactionid'] === $trxs['id']) {
                    $subtotals = $trxdets['qty'] * $trxdets['value'];
                    $discvar[] = $trxdets['discvar'];

                    if ($trxs['disctype'] === "0") {
                        $discval[] = $trxs['discvalue'];
                    } else {
                        $discval[] = $subtotals * ($trxs['discvalue']/100);
                    }
                    // Best seller 
                    $bestssell [] = [
                        'variantid' => $trxdets['variantid'],
                        'bundleid'  => $trxdets['bundleid'],
                        'qty'       => $trxdets['qty'],
                    ];

                }
            
            }
            $trxsid[] = $trxs['id'];
            
            // Best Payments
            $payments           = $PaymentModel->where('outletid', $trxs['outletid'])->find();
           
            foreach ($trxpayments as $trxpay) {
                if ($trxs['id'] == $trxpay['transactionid']){
                    foreach($payments as $pay){
                        if ($trxpay['paymentid'] === $pay['id'] && $trxpay['paymentid'] !== "0"){
                            $bestpay [] = [
                                'id'    => $pay['id'],
                                'name'  => $pay['name'],
                                'qty'   => "1",
                            ];
                        }else{
                            $bestpay [] = [
                                'id'    => $pay['id'],
                                'name'  => $pay['name'],
                                'qty'   => "0",
                            ];
                        }
                    }
                }
            }
        }
        
        $bestpayment = [];
        foreach ($bestpay as $best) {
            if (!isset($bestpayment[$best['id']])) {
                $bestpayment[$best['id']] = $best;
            } else {
                $bestpayment[$best['id']]['qty'] += $best['qty'];
            }
        }
        $bestpayment = array_values($bestpayment);
        array_multisort(array_column($bestpayment, 'qty'), SORT_DESC, $bestpayment);
        $bestpay3       = array_slice($bestpayment, 0, 3);

        $bestseller = [];
        foreach ($bestssell as $best) {
            if (!isset($bestseller[$best['variantid']])) {
                $bestseller[$best['variantid']] = $best;
            } else {
                $bestseller[$best['variantid']]['qty'] += $best['qty'];
            }
        }
        $bestseller = array_values($bestseller);
        array_multisort(array_column($bestseller, 'qty'), SORT_DESC, $bestseller);
        $best3       = array_slice($bestseller, 0, 3);
        
        $discvarsum = array_sum($discvar);
        $discvalsum = array_sum($discval);
        $totaldisc  = $discvalsum + $discvarsum;
        $trxamount = count($id);

        // Debt Total
        $debtpayment = array();
        $debttrx = array();
        $debtpaymentsid = array();
        $trxdebtval = array();
        foreach ($transactions as $trx) {
            if ($trx['paymentid'] === "0") {
                $trxdebtval[]   = $trx;
            }
        }

        // Customer Debt
        $custdebt = array();
        foreach ($transactions as $trx) {
            foreach ($customers as $customer) {
                foreach ($debts as $debt) {
                    if (($debt['memberid'] === $customer['id']) && ($debt['transactionid'] === $trx['id'])) {
                        $custdebt[] = $debt;
                    }
                }
            }
        }

        $customerdebt = array();
        foreach ($custdebt as $cusdeb) {
            if (!isset($customerdebt[$cusdeb['memberid']])) {
                $customerdebt[$cusdeb['memberid']] = $cusdeb;
            }
        }
        $totalcustdebt = count($customerdebt);

        // // Best Selling Product
        // if (!empty($transactions)) {
        //     foreach ($transactions as $trxs) {
        //         $trxvars        = $TrxdetailModel->orderBy('qty', 'DESC')->whereIn('transactionid', $trxsid)->find();
        //     }
        // } else {
        //     $trxvars        = $TrxdetailModel->orderBy('qty', 'DESC')->findAll();
        // }
        // $top3prod       = array_slice($trxvars, 0, 3);
        
        // // Popular Payment Method
        // if (!empty($transactions)) {
        //     $paymentcount = array();

        //     $trxid = array();
        //     foreach ($transactions as $transaction) {
        //         $trxid[] = $transaction['id'];
        //     }

        //     $cashpayments = $PaymentModel->like('name', 'Cash')->find();
        //     $cashid = array();
        //     foreach ($cashpayments as $cashpayment) {
        //         $cashid[] = $cashpayment['id'];
        //     }            

        //     $paymentcount[] = [
        //         'name'      => 'Cash',
        //         'qty'       => count($TrxpaymentModel->whereIn('transactionid', $trxid)->whereIn('paymentid', $cashid)->find())
        //     ];

        //     $noncashpayments      = $PaymentModel->notLike('name', 'Cash')->find();
        //     foreach ($noncashpayments as $noncashpayment) {
        //         $paymentcount[] = [
        //             'name'      => $noncashpayment['name'],
        //             'qty'       => count($TrxpaymentModel->whereIn('transactionid', $trxid)->where('paymentid', $noncashpayment['id'])->find())
        //         ];
        //     }

        //     array_multisort(array_column($paymentcount, 'qty'), SORT_DESC, $paymentcount);
        // } else {
        //     $paymentcount = array();

        //     $trxid = array();
        //     foreach ($transactions as $transaction) {
        //         $trxid[] = $transaction['id'];
        //     }

        //     $cashpayments = $PaymentModel->like('name', 'Cash')->find();
        //     $cashid = array();
        //     foreach ($cashpayments as $cashpayment) {
        //         $cashid[] = $cashpayment['id'];
        //     }            

        //     $paymentcount[] = [
        //         'name'      => 'Cash',
        //         'qty'       => count($TrxpaymentModel->whereIn('paymentid', $cashid)->find())
        //     ];

        //     $noncashpayments      = $PaymentModel->notLike('name', 'Cash')->find();
        //     foreach ($noncashpayments as $noncashpayment) {
        //         $paymentcount[] = [
        //             'name'      => $noncashpayment['name'],
        //             'qty'       => count($TrxpaymentModel->where('paymentid', $noncashpayment['id'])->find())
        //         ];
        //     }

        //     array_multisort(array_column($paymentcount, 'qty'), SORT_DESC, $paymentcount);
        // }
        // $top3paymet = array_slice($paymentcount, 0, 3);

        $qtytrx = array();
        $marginmodals = array();
        $margindasars = array();
        foreach ($transactions as $trx){
            foreach ($trxdetails as $trxdetail){
                if($trx['id'] === $trxdetail['transactionid'] && $trxdetail['variantid'] !== "0"){
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
        $gross           = $summary + $pointusedsum + $totaldisc;

        // Cashin Cash Out
        $cashin = [];
        $cashout = [];
        foreach ($trxothers as $trxother) {
            if ($trxother['type'] === "0") {
                $cashin[] = $trxother['qty'];
            } else {
                $cashout[] = $trxother['qty'];
            }
        }

        $cashinsum = array_sum($cashin);
        $cashoutsum = array_sum($cashout);

        $data                   = $this->data;
        $data['title']          = lang('Global.dashboard');
        $data['description']    = lang('Global.dashdesc');
        $data['sales']          = $summary;
        $data['profit']         = $keuntungandasar;
        $data['products']       = $products;
        $data['variants']       = $variants;
        $data['bundles']        = $bundles;
        $data['bundets']        = $bundets;
        $data['trxamount']      = $trxamount;
        $data['qtytrxsum']      = $qtytrxsum;
        $data['pointusedsum']   = $pointusedsum;
        $data['totaldisc']      = $totaldisc;
        $data['gross']          = $gross;
        $data['cashinsum']      = $cashinsum;
        $data['cashoutsum']     = $cashoutsum;
        $data['top3prod']       = $best3;
        $data['top3paymet']     = $bestpay3;
        $data['customers']      = $customers;
        $data['stocks']         = $stok;
        $data['average']        = (int)$saleaverage;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);
        $data['trxdebtval']     = $trxdebtval;
        $data['debts']          = $debts;
        $data['totalcustdebt']  = $totalcustdebt;
        $data['averagedays']    = $resultaveragedays;
        $data['bussyday']       = $day;
        $data['bussytime']      = $timeH;
        $data['payments']   = $PaymentModel->where('outletid', $this->data['outletPick'])->find();
       
        
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
        
        return redirect()->to('');
    }

    public function ownership() {
        $authorize = service('authorization');

        $authorize->removeUserFromGroup(1, 1);

        $authorize->addUserToGroup(1, 'owner');
    }

    public function trial()
    {
        phpinfo();
    }
}
