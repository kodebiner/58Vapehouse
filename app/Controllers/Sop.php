<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\SopModel;
use App\Models\SopDetailModel;


class Sop extends BaseController
{

    protected $db, $builder;
    protected $auth;
    protected $config;

    public function __construct()
    {
        $this->db       = \Config\Database::connect();
        $validation     = \Config\Services::validation();
    }

    public function index()
    {
        // Calling Model   
        $sopModel       = new SopModel;
        $SopDetailModel = new SopDetailModel;
        $userModel      = new UserModel;
        
        // if ($this->data['outletPick'] === null) {
        //     $sop      = $sopModel->findAll();
        // } else {
        //     $sop      = $sopModel->where('outletid', $this->data['outletPick'])->find();
        // }

        // Checking filter
        $input = $this->request->getPost();

        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.sop');
        $data['description']    = lang('Global.sop');
        $data['sops']           = $sopModel->findAll();
        $data['sopdetails']     = $SopDetailModel->findAll();
        $data['users']          = $userModel->findAll();

        return view('Views/sop', $data);
    }


    public function create()
    {
        // Calling Model   
        $sopModel       = new SopModel;
        $SopDetailModel = new SopDetailModel;
        $userModel      = new UserModel;

        // initialize
        $input      = $this->request->getPost();

        $data = [
            'name'      => $input['name'],
            'shift'     => $input['shift'],
        ];
        
        $sopModel->save($data);
        return redirect()->back()->with('message', lang('Global.saved'));
    }

    public function edit($id){

        // Calling Model   
        $sopModel       = new SopModel;
        $SopDetailModel = new SopDetailModel;
        $userModel      = new UserModel;

        // initialize
        $input      = $this->request->getPost();

        $data = [
            'id'        => $id,
            'name'      => $input['name'],
            'shift'     => $input['shift'],
        ];
        
        $SopModel->save($data);
        return redirect()->back()->with('message', lang('Global.saved'));
    }

    Public function delete($id){
        // Calling Model   
        $sopModel       = new SopModel;
        $SopDetailModel = new SopDetailModel;
        $userModel      = new UserModel;

        // initialize
        $input      = $this->request->getPost();

        $data = [
            'id'        => $id,
            'name'      => $input['name'],
            'shift'     => $input['shift'],
        ];
        
        $SopModel->save($data);
        return redirect()->back()->with('message', lang('Global.deleted'));
    }

    public function todolist()
    {
        // Calling Model   
        $SopModel                   = new SopModel;
        $SopDetailModel             = new SopDetailModel;

        // initialize
        $sops                       = $SopModel->findAll();
        $today                      = date('Y-m-d') .' 00:00:01';
        $sopdetails                 = $SopDetailModel->where('created_at >', $today)->find();
        
        if (empty($sopdetails)) {
            foreach ($sops as $sop) {
                $datasop = [
                    'sopid'         => $sop['id'],
                    'status'        => "0"
                ];
                $SopDetailModel->save($datasop);
            }
        }

        $sopdet = $SopDetailModel->where('created_at >', $today)->find();
        
        // Parsing Data to View
        $data                       = $this->data; 
        $data['title']              = lang('Global.dosop');
        $data['description']        = lang('Global.dosopListDesc');
        $data['sops']               = $sops;
        $data['sopdetails']         = $sopdet;

        // Return
        return view ('Views/dosop', $data);
    }

    public function updatetodo()
    {
        // Calling Model   
        $sopModel       = new SopModel;
        $SopDetailModel = new SopDetailModel;
        $userModel      = new UserModel;

        // initialize
        $input          = $this->request->getPost();
        // $sopdetails     = $SopDetailModel->where('created_at >', $today)->find();
        
        // $date=date_create();
        // $tanggal = date_format($date,'Y-m-d H:i:s');

        foreach ($input['status'] as $key => $value) {
            $sopdata = [
                'id'        => $key,
                'userid'    => $this->data['uid'],
                'status'    => '1'
            ];
            $SopDetailModel->save($sopdata);
        }

        // $x = $input['sopid'];
        // $y = $input['status'];
        
        // foreach ($x as $sopid) {
        //     foreach ($y as $status) {
        //         $data = [
        //             'sopid'      => $sopid,
        //             'userid'     => $this->data['uid'],
        //             'status'     => $status,
        //             'date'       => $tanggal,
        //         ];
        //         $SopDetailModel->save($data);
        //     }
        // }
        
        return redirect()->back()->with('message', lang('Global.saved'));
    }
}