<?php

namespace App\Controllers;

use App\Models\PaymentModel;
use App\Models\OutletModel;
use App\Models\CashModel;

class Payment extends BaseController
{
    public function index()
    {
        // Calling Models
        $PaymentModel           = new PaymentModel();
        $OutletModel            = new OutletModel();
        $CashModel              = new CashModel();

        // Populating Data
        $outlets                = $OutletModel->findAll();
        $cash                   = $CashModel->findAll();

        // get outlet
        $payment = $PaymentModel->where('outletid', '0')->orWhere('outletid', $this->data['outletPick'])->find();
        
        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.paymentList');
        $data['description']    = lang('Global.paymentListDesc');
        $data['payments']       = $payment;
        $data['outlets']        = $outlets;
        $data['cash']           = $cash;

        return view('Views/payment', $data);
    }

    public function create()
    {

        // Calling Models
        $PaymentModel   = new PaymentModel;
        $OutletModel    = new OutletModel;
        $CashModel      = new CashModel;

        // Populating data
        $outlets        = $OutletModel->findAll();
        
        // initialize
        $input          = $this->request->getPost();

        // Getting outlet id
        $cash = $CashModel->find($input['cashid']);
        // save data
        $data = [
            'name'              => $input['name'],
            'cashid'            => $input['cashid'],
            'outletid'          => $cash['outletid'],
            
        ];
        $PaymentModel->save($data);

        return redirect()->back()->with('message', lang('Global.saved'));
    }

    public function update($id) 
    {
        // calling Model
        $PaymentModel      = new PaymentModel();
        $OutletModel       = new OutletModel();
        $CashModel         = new CashModel();

        // initialize
        $input = $this->request->getpost();
        $outlets = $OutletModel->findAll();

        // Getting Outlet Id
        $cash = $CashModel->find($input['cashid']);

        // validation
        $data = [
            'id'                    => $id,
            'outletid'              => $cash['outletid'],
            'name'                  => $input['name'],
            'cashid'                => $input['cashid'],
        ];
        $PaymentModel->save($data);

        return redirect()->back()->with('massage', lang('global.saved'));
    }

    public function delete($id)
    {

        // calling Model
        $PaymentModel  = new PaymentModel();
        $OutletModel   = new OutletModel();
        $PaymentModel->delete($id);

        return redirect()->back()->with('error', lang('Global.deleted'));
    }

}