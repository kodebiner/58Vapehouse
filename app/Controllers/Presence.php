<?php

namespace App\Controllers;

use App\Models\PresenceModel;
use App\Models\UserModel;
use App\Models\SopModel;

class Presence extends BaseController
{
    protected $data;
    protected $db, $builder;
    protected $auth;
    protected $config;
    
    public function index()
    {
        // Calling Models
        $PresenceModel          = new PresenceModel();
        $UserModel              = new UserModel();
        $SopModel               = new SopModel();

        // Populating Data
        $todays                 = date('Y-m-d') .' 00:00:01';
        $checkin                = $PresenceModel->where('userid', $this->data['uid'])->where('datetime >=', $todays)->where('status', '1')->find();
        $checkout               = $PresenceModel->where('userid', $this->data['uid'])->where('datetime >=', $todays)->where('status', '0')->find();
        $users                  = $UserModel->findAll();
        $sops                   = $SopModel->findAll();
        
        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.presence');
        $data['description']    = lang('Global.presenceListDesc');
        $data['checkin']        = $checkin;
        $data['checkout']       = $checkout;
        $data['users']          = $users;
        $data['sops']           = $sops;
        $data['todays']         = $todays;

        return view('Views/presence', $data);
    }

    public function create()
    {
        $validation = \Config\Services::validation();

        // Calling Models
        $PresenceModel      = new PresenceModel();
        $UserModel          = new PresenceModel();

        // Populating Data

        // initialize
        $input              = $this->request->getPost();

        $location           = $input['geoloc'];
        $status             = $input['status'];
        $img                = $input['image'];

        $folderPath         = "img/presence/";
        $image_parts        = explode(";base64,", $img);
        $image_type_aux     = explode("image/", $image_parts[0]);
        $image_type         = $image_type_aux[1];
        $image_base64       = base64_decode($image_parts[1]);
        $fileName           = uniqid() . '.png';
        $file               = $folderPath . $fileName;
        file_put_contents($file, $image_base64);
    
        // get user id
        $auth               = service('authentication');
        $userId             = $auth->id();

        // Shift
        $shift              = $input['shift'];
        // $todays             = date('Y-m-d') .' 00:00:01';
        // $checkin            = $PresenceModel->where('userid', $this->data['uid'])->where('datetime >=', $todays)->where('status', '1')->orderBy('id', 'DESC')->limit(1)->first();
        // if ($status == '1') {
        //     $shift  = $input['shift'];
        // } else {
        //     $shift  = $checkin['shift'];
        // }
        
        if (! $this->validate([
            'geoloc'        => 'required',
            'status'        => 'required|max_length[255]',
        ])) {
                
           return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'userid'        => $userId,
            'shift'         => $shift,
            'datetime'      => date("Y-m-d H:i:s"),
            'photo'         => $fileName,
            'geoloc'        => $location,
            'status'        => $status,
        ];
            
        // Inserting Outlet
        $PresenceModel->insert($data);

        return redirect()->back()->with('message', lang('Global.saved'));
        // return view ('sop', $data);
    }

}