<?php

namespace App\Controllers;

use App\Models\PresenceModel;
use App\Models\UserModel;


class Presence extends BaseController
{
    public function index()
    {
        // Calling Models
        $PresenceModel      = new PresenceModel();
        $UserModel          = new PresenceModel();

        // Populating Data
        $presence    = $PresenceModel->findAll();
        $users       = $UserModel->findAll();

        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.presence');
        $data['description']    = lang('Global.presenceListDesc');
        $data['presences']      = $presence;
        $data['users']          = $users;

        return view('Views/presence', $data);
    }

    public function create()
    {
        $validation = \Config\Services::validation();

        // Calling Models
        $PresenceModel      = new PresenceModel();
        $UserModel          = new PresenceModel();

        // Populating Data
        $presence    = $PresenceModel->findAll();
        $users       = $UserModel->findAll();

        // initialize
        $input = $this->request->getPost();

        $location = $input['geoloc'];
        $status = $input['status'];
        $img = $input['image'];
        $folderPath = "img/profile";
    
        $image_parts = explode(";base64,", $img);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
    
        $image_base64 = base64_decode($image_parts[1]);
        $fileName = uniqid() . '.png';
    
        $file = $folderPath . $fileName;
        file_put_contents($file, $image_base64);
    

        // get user id
        $auth = service('authentication');
        $userId = $auth->id();

        $data = [
            'userid'      => $userId,
            'datetime'    => date("Y-m-d H:i:s"),
            'photo'       => $fileName,
            'geoloc'      => $location,
            'status'      => $status,
        ];
        
        if (! $this->validate([
            'geoloc'   => 'required',
            'status'      => 'required|max_length[255]',
        ])) {
                
           return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
            
        // Inserting Outlet
        $PresenceModel->insert($data);

        return redirect()->back()->with('message', lang('Global.saved'));
    }

}