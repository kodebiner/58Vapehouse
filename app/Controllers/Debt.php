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
use App\models\TrxpaymentModel;
use App\Models\DebtModel;
use App\Models\DailyReportModel;

class Debt extends BaseController
{
    public function indextrx()
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
            $transactions = $TransactionModel->orderBy('date', 'DESC')->where('date >=', $startdate)->where('date <=', $enddate)->find();
        } else {
            $transactions = $TransactionModel->orderBy('date', 'DESC')->where('date >=', $startdate)->where('date <=', $enddate)->where('outletid',$this->data['outletPick'])->find();
        }

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
        $trxdetails             = $TrxdetailModel->findAll();
        $trxpayments            = $TrxpaymentModel->findAll();
        $debts                  = $DebtModel->findAll();

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
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);

        return view('Views/trxhistory', $data);
    }

    public function indexdebt()
    {
        // Calling Models
        $OutletModel            = new OutletModel;
        $MemberModel            = new MemberModel;
        $TransactionModel       = new TransactionModel;
        $DebtModel              = new DebtModel;

        // Populating Data
        $outlets                = $OutletModel->findAll();
        $customers              = $MemberModel->findAll();

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
            $transactions = $TransactionModel->where('deadline <=', $enddate)->find();
        } else {
            $transactions = $TransactionModel->where('outletid',$this->data['outletPick'])->find();
        }

        $debts = $DebtModel->orderBy('deadline', 'DESC')->where('deadline >=', $startdate)->where('deadline <=', $enddate)->find();

        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.debt');
        $data['description']    = lang('Global.debtListDesc');
        $data['transactions']   = $transactions;
        $data['outlets']        = $outlets;
        $data['customers']      = $customers;
        $data['debts']          = $debts;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);


        return view('Views/debt', $data);
    }

    public function paydebt($id)
    {
        // Validate Data
        $validation = \Config\Services::validation();

        // Calling Models
        $DebtModel              = new DebtModel;
        $CashModel              = new CashModel;
        $TransactionModel       = new TransactionModel;
        $TrxotherModel          = new TrxotherModel;
        $PaymentModel           = new PaymentModel;
        $MemberModel            = new MemberModel;
        $DailyReportModel       = new DailyReportModel;

        // Populating Data
        $debts                  = $DebtModel->find($id);
        $cash                   = $CashModel->like('name', 'Cash')->where('outletid', $this->data['outletPick'])->first();
        $payments               = $PaymentModel->like('name', 'Cash')->where('cashid', $cash['id'])->where('outletid', $this->data['outletPick'])->first();
        $customers              = $MemberModel->where('id', $debts['memberid'])->first();

        // Date Time Stamp
        $date                   = date_create();
        $tanggal                = date_format($date,'Y-m-d H:i:s');

        // Initialize
        $input = $this->request->getPost();

        if ($debts['value'] - $input['value'] != "0") {
            $data = [
                'id'            => $id,
                'value'         => $debts['value'] - $input['value'],
                'deadline'      => $input['duedate'.$id],
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
            'cashid'        => $cash['id'],
            'description'   => "Debt - ".$customers['name'].'/'.$customers['phone'] ,
            'type'          => "0",
            'date'          => $tanggal,
            'qty'           => $input['value'],
            'photo'         => $fileName,
        ];
        $TrxotherModel->save($cashin);
        
        // Input Value to cash
        $wallet = [
            'id'    => $cash['id'],
            'qty'   => $input['value'] + $cash['qty'],
        ];
        $CashModel->save($wallet);

        // Find Data for Daily Report
        $today                  = date('Y-m-d') .' 00:00:01';
        $dailyreports           = $DailyReportModel->where('outletid', $this->data['outletPick'])->where('dateopen >', $today)->find();
        foreach ($dailyreports as $dayrep) {
            $tcashin = [
                'id'            => $dayrep['id'],
                'totalcashin'   => $dayrep['totalcashin'] + $input['value'],
            ];
            $DailyReportModel->save($tcashin);
        }

        // Return
        return redirect()->back()->with('massage', lang('global.saved'));
    }
    
    public function indextopup()
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
        $TrxotherModel          = new TrxotherModel;
        $TrxpaymentModel        = new TrxpaymentModel;

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
        
        if ($this->data['outletPick'] === null) {
            $trxothers      = $TrxotherModel->where('date >=', $startdate)->where('date <=', $enddate)->orderBy('id', 'DESC')->like('description', 'Top Up')->find();
        } else {
            $trxothers      = $TrxotherModel->where('date >=', $startdate)->where('date <=', $enddate)->where('outletid',$this->data['outletPick'])->orderBy('id', 'DESC')->like('description', 'Top Up')->where('outletid', $this->data['outletPick'])->find();
        }

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
        $transactions           = $TransactionModel->findAll();
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
        $data['customers']      = $customers;
        $data['products']       = $products;
        $data['variants']       = $variants;
        $data['stocks']         = $stocks;
        $data['trxothers']      = $trxothers;
        $data['trxpayments']    = $trxpayments;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);

        return view('Views/topup', $data);
    }
    
    public function indexdebtins()
    {
        // Calling Model
        $TrxotherModel      = new TrxotherModel;
        $PaymentModel       = new PaymentModel;
        $DebtModel          = new DebtModel;
        $UserModel          = new UserModel;
        $OutletModel        = new OutletModel;

        // Find Data
        $users              = $UserModel->findAll();
        $outlets            = $OutletModel->findAll();

        $input = $this->request->getGet('daterange');
        
        if (!empty($input)) {
            $daterange = explode(' - ', $input);
            $startdate = $daterange[0];
            $enddate = $daterange[1];
        } else {
            $startdate = date('Y-m-1');
            $enddate = date('Y-m-t');
        }
        
        if ($this->data['outletPick'] === null) {
            $trxothers      = $TrxotherModel->where('date >=', $startdate)->where('date <=', $enddate)->orderBy('id', 'DESC')->like('description', 'Debt')->find();
        } else {
            $trxothers      = $TrxotherModel->where('date >=', $startdate)->where('date <=', $enddate)->where('outletid',$this->data['outletPick'])->orderBy('id', 'DESC')->like('description', 'Debt')->where('outletid', $this->data['outletPick'])->find();
        }
        
        // Parsing data to view
        $data                       = $this->data;
        $data['title']              = lang('Global.debtInstallments');
        $data['description']        = lang('Global.debtInstallmentsListDesc');
        $data['trxothers']          = $trxothers;
        $data['users']              = $users;
        $data['outlets']            = $outlets;
        $data['startdate']          = strtotime($startdate);
        $data['enddate']            = strtotime($enddate);

        return view('Views/debtpay', $data);
    }

    public function create()
    {

        // Calling Models
        $CashModel      = new CashModel;
        $CashmoveModel  = new CashmovementModel;

        // Populating data
        $Cash        =  $CashModel->findAll();
        
        // initialize
        $input          = $this->request->getPost();
        
        // save data
        $data = [
            'description'       => $input['description'],
            'origin'            => $input['origin'],
            'destination'       => $input['destination'],
            'qty'               => $input['qty'],
            'date'              => date("Y-m-d H:i:s"),
            
        ];
        
        // validation
        if (! $this->validate([
            'description'       =>  "required|max_length[255]',",
            'qty'               =>  "required"
            ])) {
                
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }
            
            // Inserting Cash Movement
            $CashmoveModel->insert($data);
            
            // insert minus qty origin
            $cashmin    = $CashModel->where('id',$input['origin'])->first();
            $cashqty    = $cashmin['qty']-$input['qty'];
            
            $quantity = [
                'id'    =>$cashmin['id'],
                'qty'   =>$cashqty,
            ];
            
            $CashModel->save($quantity);
            
            // insert plus qty origin
            $cashplus    = $CashModel->where('id',$input['destination'])->first();
            $cashqty    = $cashplus['qty']+$input['qty'];
            
            $quant = [
                'id'    =>$cashplus['id'],
                'qty'   =>$cashqty,
            ];

            $CashModel->save($quant);

        return redirect()->back()->with('message', lang('Global.saved'));
    }

}