<?php

namespace App\Controllers;

use App\Models\TrxotherModel;
use App\Models\UserModel;
use App\Models\CashModel;
use App\Models\OutletModel;

class Trxother extends BaseController

{
    protected $db, $builder;
    protected $auth;
    protected $config;

    public function __construct()
        {
            $this->db      = \Config\Database::connect();
            $validation    = \Config\Services::validation();
            $this->builder = $this->db->table('trxother');
            $this->config  = config('Auth');
            $this->auth    = service('authentication');
        }


     public function index()
        {
            // Calling Model
            $TrxotherModel  = new TrxotherModel;
            $UserModel      = new UserModel;
            $CashModel      = new CashModel;
            $OutletModel    = new OutletModel;
            

            // Find Data
            $data           = $this->data;
            $trxothers      = $TrxotherModel->findAll();
            $users          = $UserModel->findAll();

            if ($this->data['outletPick'] === null) {
                $cashinout  = $TrxotherModel->orderBy('id', 'DESC')->findAll();
            } else {
                $cashinout  = $TrxotherModel->orderBy('id', 'DESC')->where('outletid', $this->data['outletPick'])->find();
            }

            // Parsing data to view
            $data['title']          = lang('Global.cashin');
            $data['description']    = lang('Global.cashin');
            $data['trxothers']      = $cashinout;
            $data['users']          = $users;
            $data['cash']           = $CashModel->findAll();
            $data['outlets']        = $OutletModel->findAll();

            return view ('Views/cash', $data);
        }


    public function create()
    {
        // Calling Model
        $TrxotherModel  = new TrxotherModel;
        $UserModel      = new UserModel;
        
        // initialize
        $input = $this->request->getPost();
        $date=date_create();
        $tanggal = date_format($date,'Y-m-d H:i:s');

        $data  = [
            'userid'        =>$this->data['uid'],
            'outletid'      =>$this->data['outletPick'],
            'cashid'        =>$input['cashid'],
            'description'   =>$input['description'],
            'type'          =>$input['cash'],
            'date'          =>$tanggal,
            'qty'           =>$input['quantity'],
        ];
        // Save Data Stok
        $TrxotherModel->save($data);

        // return
        return redirect()->back()->with('message', lang('Global.saved'));

    }


 

}
