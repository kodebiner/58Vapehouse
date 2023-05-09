<?php

namespace App\Controllers;

use CodeIgniter\Files\File;

class Upload extends BaseController
{
    public function profile()
    {
        $input = $this->request->getFile('uploads');

        // Validation Rules
        $rules = [
            'uploads'   => 'uploaded[uploads]|is_image[uploads]|max_size[uploads,2048]|max_dims[uploads,500,500]|ext_in[uploads,png,jpg,jpeg]',
        ];

        // Validating
        if (! $this->validate($rules)) {
            http_response_code(400);
            die(json_encode(array('message' => $this->validator->getErrors())));
        }

        
    }
}
