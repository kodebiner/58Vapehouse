<?php

namespace App\Controllers;

use App\Models\OutletModel;
use App\Models\UserModel;
use App\Models\MemberModel;
use App\Models\PaymentModel;
use App\Models\ProductModel;
use App\Models\VariantModel;
use App\Models\TransactionModel;

class Transaction extends BaseController
{
    public function index()
    {
        // Calling Models
        $OutletModel            = new OutletModel();
        $UserModel              = new UserModel();
        $MemberModel            = new MemberModel();
        $PaymentModel           = new PaymentModel();
        $ProductModel           = new ProductModel();
        $VariantModel           = new VariantModel();
        $TransactionModel       = new TransactionModel();

        // Populating Data
        $outlets            = $OutletModel->findAll();
        $users              = $UserModel->findAll();
        $customers          = $MemberModel->findAll();
        $payments           = $PaymentModel->findAll();
        $products           = $ProductModel->findAll();
        $variants           = $VariantModel->findAll();
        $transactions       = $TransactionModel->findAll();

        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.transaction');
        $data['description']    = lang('Global.transactionListDesc');
        $data['transactions']   = $transactions;
        $data['customers']      = $customers;
        $data['products']       = $products;
        $data['variants']       = $variants;

        return view('Views/transaction', $data);
    }

    
    public function create()
    {
        $validation = \Config\Services::validation();

        // Calling Models
        $TransactionModel    = new TransactionModel;

        // Populating data
        $input              = $this->request->getPost();
        $transactions       = $TransactionModel->findAll();

        $data = [
            'name'      => $input['name'],
            'address'   => $input['address'],
            'maps'      => $input['maps'],
        ];
        
        if (! $this->validate([
            'name'      => "required|max_length[255]',",
            'address'   => 'required',
            'maps'      => 'required|max_length[255]',
        ])) {
                
           return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
            
        // Inserting Outlet
        $OutletModel->insert($data);

        //Getting Outlet ID
        $outletID = $OutletModel->getInsertID();

        return redirect()->back()->with('message', lang('Global.saved'));
    }

}