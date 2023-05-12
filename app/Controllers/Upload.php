<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\GconfigModel;

class Upload extends BaseController
{
    public function profile()
    {
        $image      = \Config\Services::image();
        $validation = \Config\Services::validation();
        $input = $this->request->getFile('uploads');

        // Validation Rules
        $rules = [
            'uploads'   => 'uploaded[uploads]|is_image[uploads]|max_size[uploads,2048]|ext_in[uploads,png,jpg,jpeg]',
        ];

        // Validating
        if (! $this->validate($rules)) {
            http_response_code(400);
            die(json_encode(array('message' => $this->validator->getErrors())));
        }

        if ($input->isValid() && ! $input->hasMoved()) {
            // Saving uploaded file
            $filename = $this->data['uid'].'-'.$input->getRandomName();
            $truename = preg_replace('/\\.[^.\\s]{3,4}$/', '', $filename);
            $input->move(FCPATH.'/img/profile/', $filename);

            // Resizing Profile
            $image->withFile(FCPATH.'/img/profile/'.$filename)
                ->fit(300, 300, 'center')
                ->crop(300, 300, 0, 0)
                ->flatten(255, 255, 255)
                ->convert(IMAGETYPE_JPEG)
                ->save(FCPATH.'/img/profile/'.$truename.'.jpg');
            if ($filename != $truename.'.jpg') {
                unlink(FCPATH.'/img/profile/'.$filename);
            }

            // Getting True Filename
            $returnFile = $truename.'.jpg';

            // Calling Models
            $UserModel = new UserModel();
            
            // Calling Entities
            $updateUser = new \App\Entities\User();
    
            // Updating User Profile
            $updateUser->id         = $this->data['uid'];
            $updateUser->photo      = $returnFile;
            $UserModel->save($updateUser);

            // Returning Message
            die(json_encode($returnFile));
        }
    }

    public function removeprofile()
    {
        // Calling Models
        $UserModel = new UserModel();
        
        // Calling Entities
        $updateUser = new \App\Entities\User();

        // Updating User Profile
        $updateUser->id         = $this->data['uid'];
        $updateUser->photo      = NULL;
        $UserModel->save($updateUser);

        // Removing File
        $input = $this->request->getPost('photo');
        unlink(FCPATH.'/img/profile/'.$input);

        // Return Message
        die(json_encode(array('message' => lang('Global.deleted'))));
    }

    public function logo()
    {
        $image      = \Config\Services::image();
        $validation = \Config\Services::validation();
        $input = $this->request->getFile('uploads');

        // Validation Rules
        $rules = [
            'uploads'   => 'uploaded[uploads]|is_image[uploads]|max_size[uploads,2048]|ext_in[uploads,png,jpg,jpeg]',
        ];

        // Validating
        if (! $this->validate($rules)) {
            http_response_code(400);
            die(json_encode(array('message' => $this->validator->getErrors())));
        }

        if ($input->isValid() && ! $input->hasMoved()) {
            // Getting file extensions
            $ext = $input->guessExtension();

            // Saving uploaded file
            $filename = 'logo';
            $input->move(FCPATH.'img/', $filename);

            // Resizing Profile
            $image->withFile(FCPATH.'img/'.$filename)
                ->resize(1000, 250, true, 'auto')
                ->save(FCPATH.'img/'.$filename.'.'.$ext, 50);

            // Calling Models
            $GconfigModel = new GconfigModel();
    
            // Updating User Profile
            $update = [
                'id'    => '1',
                'logo'  => $filename.'.'.$ext
            ];
            $GconfigModel->save($update);

            // Returning Message
            die(json_encode($filename.'.'.$ext));
        }
    }

    public function removelogo()
    {
        // Calling Models
        $GconfigModel = new GconfigModel();

        // Updating User Profile
        $update = [
            'id'    => '1',
            'logo'  => ''
        ];
        $GconfigModel->save($update);

        // Removing File
        $input = $this->request->getPost('logo');
        unlink(FCPATH.'img/'.$input);

        // Return Message
        die(json_encode(array('message' => lang('Global.deleted'))));
    }
}
