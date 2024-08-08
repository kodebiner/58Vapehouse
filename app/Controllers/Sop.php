<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\SopModel;
use App\Models\SopDetailModel;


class Sop extends BaseController
{
    protected $data;
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
        // Validate Data
        $validation = \Config\Services::validation();

        // Calling Model   
        $SopModel       = new SopModel;
        $SopDetailModel = new SopDetailModel;
        $userModel      = new UserModel;

        // initialize
        $input      = $this->request->getPost();

        $data = [
            'name'      => $input['name'],
            'shift'     => $input['shift'],
        ];
        
        $SopModel->save($data);
        return redirect()->back()->with('message', lang('Global.saved'));
    }

    public function update($id)
    {
        // Calling Model   
        $SopModel       = new SopModel;
        $SopDetailModel = new SopDetailModel;

        // initialize
        $input      = $this->request->getPost();

        $data = [
            'id'        => $id,
            'name'      => $input['name'],
            'shift'     => $input['shift'],
        ];
        
        // Insert Sop Data
        $SopModel->save($data);

        // Return
        return redirect()->back()->with('message', lang('Global.saved'));
    }

    Public function delete($id)
    {
        // Calling Model   
        $SopModel       = new SopModel;
        $SopDetailModel = new SopDetailModel;

        // Populating & Removing Sop Detail Data
        $sopdetails = $SopDetailModel->where('sopid', $id)->find();
        foreach ($sopdetails as $sopdet) {
            // Removing Sop Detail
            $SopDetailModel->delete($sopdet['id']);
        }
        
        // Delete Sop
        $SopModel->delete($id);

        // Return
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
        $sopdetails                 = $SopDetailModel->where('outletid', $this->data['outletPick'])->where('created_at >', $today)->find();
        
        if (empty($sopdetails)) {
            foreach ($sops as $sop) {
                $datasop = [
                    'sopid'         => $sop['id'],
                    'outletid'      => $this->data['outletPick'],
                    'status'        => "0"
                ];
                $SopDetailModel->save($datasop);
            }
        }

        $sopdet = $SopDetailModel->where('outletid', $this->data['outletPick'])->where('created_at >', $today)->find();
        
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

        foreach ($input['status'] as $key => $value) {
            $sopdata = [
                'id'        => $key,
                'userid'    => $this->data['uid'],
                'status'    => '1'
            ];
            $SopDetailModel->save($sopdata);
        }

        return redirect()->back()->with('message', lang('Global.saved'));
    }
}