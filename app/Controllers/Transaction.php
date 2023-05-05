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
        $data['products']       = $products;
        $data['variants']       = $variants;

        return view('Views/Transaction/transaction', $data);
    }
}