<?php

namespace App\Controllers;

use App\Models\OutletModel;
use App\Models\ProductModel;
use App\Models\StockModel;
use App\Models\OldStockModel;
use App\Models\VariantModel;
use App\Models\CashModel;
use App\Models\PaymentModel;
use App\Models\UserModel;
use App\Models\OutletaccessModel;
use App\Models\GroupUserModel;

class Outlet extends BaseController
{
    public function index()
    {
        // Calling Models
        $OutletModel    = new OutletModel();

        // Populating Data
        $outlets    = $OutletModel->orderBy('id', 'ASC')->findAll();

        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.outlet');
        $data['description']    = lang('Global.outletListDesc');
        $data['outlets']        = $outlets;

        return view('Views/outlet', $data);
    }

    public function create()
    {
        $validation = \Config\Services::validation();

        // Calling Models
        $OutletModel            = new OutletModel;
        $StockModel             = new StockModel;
        $VariantModel           = new VariantModel;
        $CashModel              = new CashModel;
        $PaymentModel           = new PaymentModel;
        $OutletAccessModel      = new OutletaccessModel;
        $GroupUserModel         = new GroupUserModel;

        // Populating data
        $input                  = $this->request->getPost();
        $outlets                = $OutletModel->findAll();
        $stocks                 = $StockModel->findAll();

        $data = [
            'name'      => $input['name'],
            'address'   => $input['address'],
            'maps'      => $input['maps'],
            'instagram' => $input['instagram'],
            'phone'     => $input['phone'],
            'facebook'  => $input['facebook'],
        ];
            
        // Inserting Outlet
        $OutletModel->insert($data);

        // Getting Outlet ID
        $outletID = $OutletModel->getInsertID();

        // Insert Wallet
        $cash     = $CashModel->findAll();
        $wallet = [
            'outletid'  => $outletID,
            'name'      => "Petty Cash ".$input['name'],
            'qty'       => "0",
        ];
        // Insert Cash Data
        $CashModel->insert($wallet);

        // Getting Cash ID
        $cashid = $CashModel->getInsertID();

        // Insert Payment
        $payments   = $PaymentModel->findAll();
        $paymet = [
            'cashid'    => $cashid,
            'name'      => "Cash",
            'outletid'  => $outletID,
        ];
        // Insert Payment Data
        $PaymentModel->insert($paymet);

        // insert variants
        $variants   = $VariantModel->findAll();
        foreach ($variants as $variant ){
            $stock = [
                'outletid'  => $outletID,
                'variantid' => $variant['id'],
                'qty'       => '0',
                
            ];
            $StockModel->save($stock);
        }
        // Finding Owners
        $group = $GroupUserModel->where('group_id', '2')->find();
        
        $userarr = array();
        foreach ($group as $gr) {
            $userarr[] = $gr['user_id'];
        }
        
        // Owner Outlet 
        foreach ($userarr as $user){
            $outletAcc = [
                'userid'   => $user,
                'outletid' => $outletID,
            ];
            $OutletAccessModel->save($outletAcc);
        }

        return redirect()->back()->with('message', lang('Global.saved'));
    }

    public function update($id)
    {
        // Calling Models
        $outlets    = new OutletModel();

        // Populating Data
        $data['outlet'] = $outlets->where('id', $id)->first();
        $input          = $this->request->getPost();
        $validation     = \Config\Services::validation();

        $data = [
            'id'        => $id,
            'name'      => $input['name'],
            'address'   => $input['address'],
            'maps'      => $input['maps'],
            'instagram' => $input['instagram'],
            'phone'     => $input['phone'],
            'facebook'  => $input['facebook'],
        ];

        // Validasi
        if (! $this->validate([
            'name'      => "max_length[255]",
            'address'   => "max_length[255]",
            'maps'      => "max_length[255]",
            'instagram' => "max_length[255]",
            'phone'     => "max_length[255]",
            'facebook'  => "max_length[255]",
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        // Simpan Data
        $outlets->save($data);
        return redirect()->back()->with('message', lang('Global.saved'));
    }

    public function delete($id)
    {
        // Calling Model
        $OutletModel            = new OutletModel();
        $StockModel             = new StockModel;
        $CashModel              = new CashModel;
        $PaymentModel           = new PaymentModel;
        $OutletAccessModel      = new OutletaccessModel;
        $GroupUserModel         = new GroupUserModel;

        // Delete Stock
        $stocks = $StockModel->where('outletid',$id)->find();
        foreach ($stocks as $stock) {
            // Delete Stock
            $StockModel->delete($stock['id']);
        }

        // Delete Payment
        $payments = $PaymentModel->where('outletid', $id)->find();
        foreach ($payments as $payment) {
            $PaymentModel->delete($payment['id']);
        }

        // Delete Cash
        $cash = $CashModel->where('outletid', $id)->find();
        foreach ($cash as $cas) {
            $CashModel->delete($cas['id']);
        }
        
        // Outlet Access 
        $accessId = $OutletAccessModel->where('outletid', $id)->find();
        foreach ($accessId as $access){
            $OutletAccessModel->delete($access['id']);
        }

        // Delete Outlet
        $OutletModel->delete($id);
        return redirect()->back()->with('error', lang('Global.deleted'));
    }
}