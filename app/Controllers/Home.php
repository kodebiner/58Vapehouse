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

            foreach ($trxdetails as $trxdet) {
                if ($trxdet['transactionid'] === $transaction['id']) {
                    $discvar  = $trxdet['discvar'];
                    $subtotal = $trxdet['qty'] * $trxdet['value'];
                }
                
                $trxdisc = array();
                if ($transaction['disctype'] === 0) {
                    $trxdisc[] = $transaction['discvalue'];
                } else {
                    $trxdisc[] = $transaction['discvalue'] * $subtotal;
                }
            }
            dd($subtotal);

            $trxdisc = array();
            foreach ($sales as $sale) {
                if ($sale['disctype'] === 0) {
                    $trxdisc[] = $sale['discvalue'];
                } else {
                    
                }
            }
        }

        $trxamount = count($id);

        // Profit Value
        $qtytrx = array();
        $discvariant = array();
        foreach ($transactions as $trx){
            foreach ($trxdetails as $trxdetail){
                if($trx['id'] === $trxdetail['transactionid']){
                    $marginmodal    = $trxdetail['marginmodal'];
                    $margindasar    = $trxdetail['margindasar'];
                    $qtytrx[]       = $trxdetail['qty'];
                    $discvariant[]  = $trxdetail['discvar'];
                    $marginmodals[] = $marginmodal;
                    $margindasars[] = $margindasar;
                }
            }
        }

        $discvariantsum = array_sum($discvariant);
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

        // // Data Produk
        // $builder = $db->table('trxdetail')->WhereIn('transactionid',$trxid);
        // // $builder = $db->table('trxpayment')->where('transactionid',$trx)->find();
        // $builder->selectCount('variantid','variantsales','salesvalue','bundleid');
        // // $builder->selectCount('id','total_payment','payval')->where('transactionid',$trx['id']);
        // $builder->select('variantid');
        // $builder->select('bundleid');
        // $builder->selectSum('value');
        // $builder->groupBy('variantid');
        // $query   = $builder->get();
        // $variantval = $query->getResult();

        // $varvalue = array();
        // foreach ($variantval as $varval) {
        //     $varvalue[] = [
        //         'id'        => $varval->variantid,
        //         'value'     => $varval->value,
        //         'sold'      => $varval->variantsales,
        //         'bundleid'  => $varval->bundleid,
        //     ];
        // }

        // $var = array();
        // foreach ($varvalue as $varv){
        //     foreach ($variants as $varian){
        //         if($varian['id'] === $varv['id']){
        //             foreach ($products as $product){
        //                 if($varian['productid'] === $product['id']){
        //                     foreach ($category as $cate){
        //                         if($cate['id'] === $product['catid']){
        //                             $var[] = [
        //                                 'id'        => $varv['id'],
        //                                 'value'     => $varv['value'],
        //                                 'sold'      => $varv['sold'],
        //                                 'bundleid'  => $varv['bundleid'],
        //                                 'productid' => $product['id'],
        //                                 'product'   => $product['name'],
        //                                 'category'  => $cate['name'],
        //                             ];
        //                         }
        //                     }
        //                 }
        //             }
        //         }
        //     }
        // }

        // // Sum Total Product Sold
        // $produk = [];
        // foreach ($var as $vars) {
        //     if (!isset($produk[$vars['productid'].$vars['product']])) {
        //         $produk[$vars['productid'].$vars['product']] = $vars;
        //     } else {
        //         $produk[$vars['productid'].$vars['product']]['sold'] += $vars['sold'];
        //     }
        // }
        // $produk = array_values($produk);

        // dd($produk);


        $data                   = $this->data;
        $data['title']          = lang('Global.dashboard');
        $data['description']    = lang('Global.dashdesc');
        $data['sales']          = $summary;
        $data['profit']         = $keuntungandasar;
        $data['trxamount']      = $trxamount;
        $data['qtytrxsum']      = $qtytrxsum;
        $data['pointusedsum']   = $pointusedsum;
        
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
