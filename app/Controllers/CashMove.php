<?php

namespace App\Controllers;

use App\Models\CashModel;
use App\Models\CashmovementModel;
use App\Models\OutletModel;
use App\Models\UserModel;

class CashMove extends BaseController
{
    public function index()
    {
        // Calling Models
        $CashModel              = new CashModel;
        $CashmoveModel          = new CashmovementModel;
        $OutletModel            = new OutletModel;

        // Populating Data
        $outlets                = $OutletModel->findAll();
        $cash                   = $CashModel->findAll();
        $cashmoves              = $CashmoveModel->findAll();


        // get outlet
        if ($this->data['outletPick'] === null) {
            $cashman      = $CashModel->orderBy('id', 'DESC')->findAll();
        } else {
            $cashman      = $CashModel->where('outletid', $this->data['outletPick'])->find();
        }

        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.cashManagement');
        $data['description']    = lang('Global.cashmanListDesc');
        $data['cashmoves']      = $cashmoves;
        $data['cashmans']       = $cashman;
        $data['outlets']        = $outlets;

        return view('Views/cashmove', $data);
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