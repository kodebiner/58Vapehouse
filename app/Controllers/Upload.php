<?php

namespace App\Controllers;

use CodeIgniter\Files\File;

class Upload extends BaseController
{
    public function profile()
    {
        $image      = \Config\Services::image('imagick');
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
                ->flatten(255, 255, 255)
                ->convert(IMAGETYPE_JPEG)
                ->save(FCPATH.'/img/profile/'.$truename.'.jpg');
            unlink(FCPATH.'/img/profile/'.$filename);

            // Returning file
            //$returnFile = $truename.'jpg';
            die(json_encode($truename));
        }
    }
}
