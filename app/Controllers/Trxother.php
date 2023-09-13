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
        $auth           = service('authentication');
        $users          = $UserModel->findAll();
        $userId         = $auth->id();
        $GroupUser      = $this->GroupUserModel->where('user_id', $this->userId)->first();
        $roleid         = $GroupUser['group_id'];
        $user           = $UserModel->where('id',$userId)->first();
        $userOutlet     = $user->outletid;
        $outlets        = $OutletModel->findAll();
        $cash           = $CashModel->findAll();
        
        // Operator 
        if ($roleid === 4) {
            $trxothers  = $TrxotherModel->orderBy('date', 'DESC')->notLike('description', 'Top Up')->notLike('description', 'Debt')->where('outletid',$userOutlet)->find();
        } else {
            if ($this->data['outletPick'] === null) {
                $trxothers  = $TrxotherModel->orderBy('date', 'DESC')->notLike('description', 'Top Up')->notLike('description', 'Debt')->find();
            } else {
                $trxothers  = $TrxotherModel->orderBy('date', 'DESC')->notLike('description', 'Top Up')->notLike('description', 'Debt')->where('outletid', $this->data['outletPick'])->find();
            }
        }
        
        // Parsing data to view
        $data                   = $this->data;
        $data['title']          = lang('Global.cashinout');
        $data['description']    = lang('Global.cashinoutListDesc');
        $data['trxothers']      = $trxothers;
        $data['users']          = $users;
        $data['cash']           = $cash;
        $data['outlets']        = $outlets;

        return view ('Views/cash', $data);
    }

    public function create()
    {
        // Calling Model
        $TrxotherModel  = new TrxotherModel;
        $UserModel      = new UserModel;
        $CashModel      = new CashModel;
        
        // initialize
        $input          = $this->request->getPost();
        $cash           = $CashModel->like('name', 'Cash')->where('outletid', $this->data['outletPick'])->first();

        // Get Date
        $date           = date_create();
        $tanggal        = date_format($date,'Y-m-d H:i:s');

        // Image Capture
        $img            = $input['image'];
        $folderPath     = "img";
        $image_parts    = explode(";base64,", $img);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type     = $image_type_aux[1];
        $image_base64   = base64_decode($image_parts[1]);
        $fileName       = uniqid() . '.png';
        $file           = $folderPath . $fileName;
        file_put_contents($file, $image_base64);

        // Data Input
        $data  = [
            'userid'        => $this->data['uid'],
            'outletid'      => $this->data['outletPick'],
            'cashid'        => $cash['id'],
            'description'   => $input['description'],
            'type'          => $input['cash'],
            'date'          => $tanggal,
            'qty'           => $input['quantity'],
            'photo'         => $fileName,
        ];
        // Save Data Cash
        $TrxotherModel->save($data);

        // Plus & Minus Cash Wallet
        if ( $input['cash'] === "0" ){
            $cas = $input['quantity'] + $cash['qty'];
        } else {
            $cas =  $cash['qty'] - $input['quantity'] ;
        }

        $wallet = [
            'id'    => $cash['id'],
            'qty'   => $cas,
        ];
        $CashModel->save($wallet);

        // return
        return redirect()->back()->with('message', lang('Global.saved'));

    }

    public function withdraw()
    {
        // Calling Model
        $TrxotherModel  = new TrxotherModel;
        $UserModel      = new UserModel;
        $CashModel      = new CashModel;
        
        // initialize
        $input          = $this->request->getPost();
        $cash           = $CashModel->like('name', 'Cash')->where('outletid', $this->data['outletPick'])->first();

        // Get Date
        $date           = date_create();
        $tanggal        = date_format($date,'Y-m-d H:i:s');

        // Image Capture
        $img            = $input['image'];
        $folderPath     = "img";
        $image_parts    = explode(";base64,", $img);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type     = $image_type_aux[1];
        $image_base64   = base64_decode($image_parts[1]);
        $fileName       = uniqid() . '.png';
        $file           = $folderPath . $fileName;
        file_put_contents($file, $image_base64);

        // Data Input
        $data  = [
            'userid'        => $this->data['uid'],
            'outletid'      => $this->data['outletPick'],
            'cashid'        => $cash['id'],
            'description'   => lang('Global.withdraw')." - ".$input['name'],
            'type'          => "1",
            'date'          => $tanggal,
            'qty'           => $input['value'],
            'photo'         => $fileName,
        ];
        // Save Data Cash
        $TrxotherModel->save($data);

        // Minus Cash Wallet
        $cas =  $cash['qty'] - $input['value'] ;

        $wallet = [
            'id'    => $cash['id'],
            'qty'   => $cas,
        ];
        $CashModel->save($wallet);

        // return
        return redirect()->back()->with('message', lang('Global.saved'));

    }
}
