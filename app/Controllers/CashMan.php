<?php

namespace App\Controllers;

use App\Models\CashModel;
use App\Models\OutletModel;
use App\Models\UserModel;

class CashMan extends BaseController
{
    protected $data;
    protected $db, $builder;
    protected $auth;
    protected $config;
    
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

        // Resolve dompet type (0 = Tunai, 1 = Non Tunai)
        $type           = ($input['type'] === '1') ? CashModel::TYPE_NON_CASH : CashModel::TYPE_CASH;
        $outletid       = ($input['outlet'] === "0") ? 0 : (int) $input['outlet'];

        $data = [
            'outletid'          => $outletid,
            'type'              => $type,
            'name'              => CashModel::typeLabel($type),
            'qty'               => $input['qty'],
        ];
        $CashModel->insert($data);

        return redirect()->back()->with('message', lang('Global.saved'));
    }

    public function update($id) 
    {
        // calling Model
        $CashModel      = new CashModel;

        // initialize
        $input = $this->request->getpost();

        // Resolve dompet type (0 = Tunai, 1 = Non Tunai)
        $type  = ($input['type'] === '1') ? CashModel::TYPE_NON_CASH : CashModel::TYPE_CASH;

        // saved data
        $data = [
            'id'                => $id,
            'outletid'          => (int) $input['outlet'],
            'type'              => $type,
            'name'              => CashModel::typeLabel($type),
            'qty'               => $input['qty'],
        ];

        // validation
        if (! $this->validate([
            'outlet'            =>  "required",
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