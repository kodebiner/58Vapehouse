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
        $cashman                = $CashModel->orderBy('id', 'DESC')->findAll();
        $outlets                = $OutletModel->findAll();
        $users                  = $UserModel->findAll();

        // get outlet
        if ($this->data['outletPick'] === null) {
            $cashman      = $CashModel->findAll();
        } else {
            $cashman      = $CashModel->where('outletid', $this->data['outletPick'])->find();
        }

        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.cashManagement');
        $data['description']    = lang('Global.cashmanListDesc');
        $data['cashmans']       = $cashman;
        $data['outlets']        = $outlets;
        $data['users']          = $users;

        return view('Views/cashman', $data);
    }

    public function create()
    {

        // Calling Models
        $CashModel      = new CashModel;
        $OutletModel    = new OutletModel;

        // Populating data
        $outlets        = $OutletModel->findAll();
        
        // get outlet
        if ($this->data['outletPick'] === null) {
            $cashman      = $CashModel->findAll();
        } else {
            $cashman      = $CashModel->where('outletid', $this->data['outletPick'])->find();
        }
        
        // get user id
        $auth = service('authentication');
        $userId = $auth->id();


        // initialize
        $input          = $this->request->getPost();

        // Date
        $dates = date("Y-m-d H:i:s");

        // save data
        $data = [
            'outletid'  => $input['outlet'],
            'name'      => $input['name'],
            'type'      => $input['type'],
            'qty'       => $input['qty'],
            'userid'    => $userId,
            'date'      => $dates,
        ];

        // validation
        if (! $this->validate([
            'name'      =>  "required|max_length[255]',",
            'type'      =>  'required',
            'qty'       =>  "required"
        ])) {
                
           return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
            
        // Inserting CashFlow
        $CashModel->insert($data);

        return redirect()->back()->with('message', lang('Global.saved'));
    }

    public function update($id) {

        // calling Model
        $CashModel      = new CashModel;
        $OutletModel    = new OutletModel;

        // get user id
        $auth = service('authentication');
        $userId = $auth->id();

        // initialize
        $input = $this->request->getpost();

        // saved data
        $data = [
            'id'        => $id,
            'userid'    => $userId,
            'name'      => $input['name'],
            'outletid'  => $input['outletid'],
            'type'      => $input['type'],
            'date'      => date("Y-m-d H:i:s"),

        ];

        // validation
        if (! $this->validate([
            'name'      =>  "required|max_length[255]',",
            'type'      =>  'required',
            'qty'       =>  "required"
                ])
            )
        {      
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // save data
        $CashModel->save($data);

        return redirect()->back()->with('massage', lang('global.saved'));

    }

    public function delete($id) {

        // calling model
        $CashModel = new CashModel;

        // deleted
        $cash = $CashModel->where('id',$id)->first();
        $CashModel->delete($cash);

    }
}