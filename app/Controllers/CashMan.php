<?php

namespace App\Controllers;

use App\Models\CashModel;
use App\Models\OutletModel;
use App\Models\UserModel;

class CashMan extends BaseController
{
    public function index()
    {
        // Calling Models
        $CashModel              = new CashModel;
        $OutletModel            = new OutletModel;
        $UserModel              = new UserModel;

        // Populating Data
        $outlets                = $OutletModel->findAll();

        // get outlet
        if ($this->data['outletPick'] === null) {
            $cashman      = $CashModel->orderBy('id', 'DESC')->findAll();
        } else {
            $out =  $this->data['outletPick'];
            $cashman      = $CashModel->where("outletid = {$out} OR outletid='0'")->orderBy('outletid', 'ASC')->find();
        }

        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.walletManList');
        $data['description']    = lang('Global.walletManListDesc');
        $data['cashmans']       = $cashman;
        $data['outlets']        = $outlets;
        
        return view('Views/cashman', $data);
    }

    public function create()
    {

        // Calling Models
        $CashModel      = new CashModel;
        $OutletModel    = new OutletModel;
        $UserModel      = new UserModel;

        // Populating data
        $outlets        = $OutletModel->findAll();
        $userOutlet = $UserModel->where('id',$this->data['outletPick'])->first();
        
        // get outlet
        if ($this->data['outletPick'] === null) {
            $cashman      = $CashModel->findAll();
        } else {
            $cashman      = $CashModel->where('outletid', $this->data['outletPick'])->find();
        }
        
        // initialize
        $input          = $this->request->getPost();

        if ($input['outlet'] ==="0"){
          
                $data = [
                    'outletid'          => 0,
                    'name'              => $input['name'],
                    'qty'               => $input['qty'],
                ];
                $CashModel->insert($data);
            
        }else{
            $data = [
                'outletid'          => $input['outlet'],
                'name'              => $input['name'],
                'qty'               => $input['qty'],
            ];
            $CashModel->insert($data);
        }

        return redirect()->back()->with('message', lang('Global.saved'));
    }

    public function update($id) 
    {
        // calling Model
        $CashModel      = new CashModel;

        // initialize
        $input = $this->request->getpost();

        // saved data
        $data = [
            'id'                => $id,
            'name'              => $input['name'],
            'outletid'          => $input['outlet'],
        ];

        // validation
        if (! $this->validate([
            'name'              =>  "required|max_length[255]',",
            // 'qty'               =>  "required"
                ])
            )
        {      
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // save data
        $CashModel->save($data);

        return redirect()->back()->with('massage', lang('global.saved'));

    }

}