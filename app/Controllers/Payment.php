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
        $PaymentModel           = new PaymentModel;
        $OutletModel            = new OutletModel;
        $CashModel              = new CashModel;

        // Populating Data
        $outlets                = $OutletModel->findAll();
        $cash                   = $CashModel->findAll();

        // get outlet
        if ($this->data['outletPick'] === null) {
            $payment      = $PaymentModel->orderBy('id', 'DESC')->findAll();
        } else {
            $payment      = $PaymentModel->where('outletid', $this->data['outletPick'])->find();
        }

        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.cashManagement');
        $data['description']    = lang('Global.cashmanListDesc');
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

        // Populating data
        $outlets        = $OutletModel->findAll();
        
        // initialize
        $input          = $this->request->getPost();

        // save data
        $data = [
            'outletid'          => $input['outlet'],
            'name'              => $input['name'],
            'cashid'            => $input['cashid'],

        ];
        // validation
        if (! $this->validate([
            'name'              =>  "required|max_length[255]',",
            'cashid'            =>  "required"
        ])) {
                
           return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        if ($data['outletid'] === null ){
            $outlets      = $OutletModel->find(['id']);
            // foreach ($outlets as $out){
            //     $o = [
            //         'outletid' => $this->request->getVar('id'),
            //     ];
            // }
        } 
        dd($outlets);
        
        $PaymentModel->insert($data);
 
            

        return redirect()->back()->with('message', lang('Global.saved'));
    }

    public function update($id) 
    {
        // calling Model
        $PaymentModel      = new PaymentModel();

        // initialize
        $input = $this->request->getpost();

        // saved data
        $data = [
            'id'                => $id,
            'name'              => $input['name'],
            'cashid'            => $input['cashid'],
        ];

        // validation
        if (! $this->validate([
            'name'              =>  "required|max_length[255]",
                ])
            )
        {      
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // save data
        $PaymentModel->save($data);

        return redirect()->back()->with('massage', lang('global.saved'));

    }

}