<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\GconfigModel;
use App\Models\ProductModel;
use App\Models\PromoModel;

class Upload extends BaseController
{
    protected $data;
    protected $db, $builder;
    protected $auth;
    protected $config;
    
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

            // Removing old file
            if ($this->data['account']->photo != NULL) {
                unlink(FCPATH.'/img/profile/'.$this->data['account']->photo);
            }
    
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

    public function productcreate()
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
            $filename = $input->getRandomName();
            $truename = preg_replace('/\\.[^.\\s]{3,4}$/', '', $filename);
            $input->move(FCPATH.'/img/product/', $filename);

            // Resizing Product Image
            $image->withFile(FCPATH.'/img/product/'.$filename)
                ->fit(300, 300, 'center')
                ->crop(300, 300, 0, 0)
                ->flatten(255, 255, 255)
                ->convert(IMAGETYPE_JPEG)
                ->save(FCPATH.'/img/product/'.$truename.'.jpg');
            
            // Creating Thumbnail
            $image->withFile(FCPATH.'/img/product/'.$filename)
                ->fit(300, 300, 'center')
                ->crop(300, 300, 0, 0)
                ->flatten(255, 255, 255)
                ->convert(IMAGETYPE_JPEG)
                ->save(FCPATH.'/img/product/thumb-'.$truename.'.jpg', 40);
            
            // Removing uploaded if it's not the same filename
            if ($filename != $truename.'.jpg') {
                unlink(FCPATH.'/img/product/'.$filename);
            }

            // Getting True Filename
            $returnFile = $truename.'.jpg';

            // Returning Message
            die(json_encode($returnFile));
        }
    }

    public function removeproductcreate()
    {
        // Removing File
        $input = $this->request->getPost('photo');
        unlink(FCPATH.'img/product/thumb-'.$input);
        unlink(FCPATH.'img/product/'.$input);

        // Return Message
        die(json_encode(array('message' => lang('Global.deleted'))));
    }

    public function productedit($id)
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
            $filename = $input->getRandomName();
            $truename = preg_replace('/\\.[^.\\s]{3,4}$/', '', $filename);
            $input->move(FCPATH.'/img/product/', $filename);

            // Resizing Product Image
            $image->withFile(FCPATH.'/img/product/'.$filename)
                ->fit(300, 300, 'center')
                ->crop(300, 300, 0, 0)
                ->flatten(255, 255, 255)
                ->convert(IMAGETYPE_JPEG)
                ->save(FCPATH.'/img/product/'.$truename.'.jpg');
            
            // Creating Thumbnail
            $image->withFile(FCPATH.'/img/product/'.$filename)
                ->fit(300, 300, 'center')
                ->crop(300, 300, 0, 0)
                ->flatten(255, 255, 255)
                ->convert(IMAGETYPE_JPEG)
                ->save(FCPATH.'/img/product/thumb-'.$truename.'.jpg', 40);
            
            // Removing uploaded if it's not the same filename
            if ($filename != $truename.'.jpg') {
                unlink(FCPATH.'/img/product/'.$filename);
            }

            // Getting True Filename
            $returnFile = $truename.'.jpg';

            // Calling Models
            $ProductModel = new ProductModel();

            // Updating Product Photo
            $product = $ProductModel->find($id);
            if (!empty($product['photo'])) {
                unlink(FCPATH.'/img/product/'.$product['photo']);
                unlink(FCPATH.'/img/product/'.$product['thumbnail']);
            }
            $update = [
                'id'        => $id,
                'photo'     => $truename.'.jpg',
                'thumbnail' => 'thumb-'.$truename.'.jpg'
            ];
            $ProductModel->save($update);

            // Returning Message
            die(json_encode($returnFile));
        }
    }

    public function removeproductedit($id)
    {
        // Calling Models
        $ProductModel = new ProductModel();

        // Updating product
        $update = [
            'id'        => $id,
            'photo'     => NULL,
            'thumbnail' => NULL
        ];
        $ProductModel->save($update);

        // Removing File
        $input = $this->request->getPost('photo');
        unlink(FCPATH.'/img/product/'.$input);
        unlink(FCPATH.'/img/product/thumb-'.$input);

        // Return Message
        die(json_encode(array('message' => lang('Global.deleted'))));
    }

    public function promocreate()
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
            $filename = $input->getRandomName();
            $truename = preg_replace('/\\.[^.\\s]{3,4}$/', '', $filename);
            $input->move(FCPATH.'/img/promo/', $filename);

            // Resizing Promo Image
            $image->withFile(FCPATH.'/img/promo/'.$filename)
                ->fit(854, 480, 'center')
                ->crop(854, 480, 0, 0)
                ->flatten(255, 255, 255)
                ->convert(IMAGETYPE_JPEG)
                ->save(FCPATH.'/img/promo/'.$truename.'.jpg');
            
            // Removing uploaded if it's not the same filename
            if ($filename != $truename.'.jpg') {
                unlink(FCPATH.'/img/promo/'.$filename);
            }

            // Getting True Filename
            $returnFile = $truename.'.jpg';

            // Returning Message
            die(json_encode($returnFile));
        }
    }

    public function removepromocreate()
    {
        // Removing File
        $input = $this->request->getPost('photo');
        unlink(FCPATH.'img/promo/'.$input);

        // Return Message
        die(json_encode(array('message' => lang('Global.deleted'))));
    }

    public function promoedit($id)
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
            $filename = $input->getRandomName();
            $truename = preg_replace('/\\.[^.\\s]{3,4}$/', '', $filename);
            $input->move(FCPATH.'/img/promo/', $filename);

            // Resizing Promo Image
            $image->withFile(FCPATH.'/img/promo/'.$filename)
                ->fit(854, 480, 'center')
                ->crop(854, 480, 0, 0)
                ->flatten(255, 255, 255)
                ->convert(IMAGETYPE_JPEG)
                ->save(FCPATH.'/img/promo/'.$truename.'.jpg');

            // Removing uploaded if it's not the same filename
            if ($filename != $truename.'.jpg') {
                unlink(FCPATH.'/img/promo/'.$filename);
            }

            // Getting True Filename
            $returnFile = $truename.'.jpg';

            // Calling Models
            $PromoModel = new PromoModel();

            // Updating Promo Photo
            $promo = $PromoModel->find($id);
            if (!empty($promo['photo'])) {
                unlink(FCPATH.'/img/promo/'.$promo['photo']);
            }
            $update = [
                'id'        => $id,
                'photo'     => $truename.'.jpg',
            ];
            $PromoModel->save($update);

            // Returning Message
            die(json_encode($returnFile));
        }
    }

    public function removepromoedit($id)
    {
        // Calling Models
        $PromoModel = new PromoModel();

        // Updating Promo
        $update = [
            'id'        => $id,
            'photo'     => NULL,
        ];
        $PromoModel->save($update);

        // Removing File
        $input = $this->request->getPost('photo');
        unlink(FCPATH.'/img/promo/'.$input);

        // Return Message
        die(json_encode(array('message' => lang('Global.deleted'))));
    }
}
