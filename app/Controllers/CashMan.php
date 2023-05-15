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
        $CashModel              = new CashModel();
        $OutletModel            = new OutletModel;
        $UserModel              = new UserModel;

        // Populating Data
        $cashman                = $CashModel->orderBy('id', 'DESC')->findAll();
        $outlets                = $OutletModel->findAll();
        $users                  = $UserModel->findAll();

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
        $validation = \Config\Services::validation();

        // Calling Models
        $CashModel      = new CashModel;

        // Populating data
        $input          = $this->request->getPost();
        $cashmans       = $CashModel->findAll();

        // Date
        $dates = date("Y-m-d H:i:s");

        // Type Cashin / Cashout
        // masih bingung perhitungan untuk cash karena berhubungan dengan pencatatan keuangan lainnya

        $data = [
            'name'      => $input['name'],
            'type'      => $input['type'],
            'qty'       => '0',
        ];
        
        if (! $this->validate([
            'name'      => "required|max_length[255]',",
            'type'     => 'required',
        ])) {
                
           return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
            
        // Inserting Customer
        $CashModel->insert($data);

        return redirect()->back()->with('message', lang('Global.saved'));
    }
}