<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\OutletModel;
use App\Models\ProductModel;
use App\Models\StockModel;
use App\Models\GroupUserModel;
use Myth\Auth\Models\GroupModel;

class Outlet extends BaseController
{

    protected $db, $builder;
    protected $auth;
    protected $config;

public function __construct()
    {
        $this->db      = \Config\Database::connect();
        $validation = \Config\Services::validation();
        $this->builder =   $this->db->table('outlet');
        $this->config = config('Auth');
        $this->auth   = service('authentication');
        
    }

public function index()

    {
        $GroupModel = new GroupModel();
        $query =   $this->builder->get();
        $data                   = $this->data;
        $data['title']          = lang('Global.outlet');
        $data['description']    = lang('Global.outletDesc');
        $data['roles']          = $GroupModel->findAll();
        $data['outlets']          = $query->getResult();

        return view('Views/outlet', $data);

    }

public function create()

    {  

            $validation = \Config\Services::validation();
            $OutletModel = new OutletModel;
            $ProductModel = new ProductModel;
            $StockModel = new StockModel;
            $input = $this->request->getPost();
            $outlets = $OutletModel->findAll();
            $data = [
                'nama'    => $input['name'],
                'alamat'  => $input['addres'],
                'area_id' => $input['maps'],
            ];
        
            if (! $this->validate([
                'nama' => "required|max_length[255]',",
                'alamat'  => 'required',
                'area'  => 'required|max_length[255]',
            ])) {
                
               return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }
            
            $OutletModel->insert($data);
          
            // save outlet & product id
    
            $outlet_id = $OutletModel->getInsertID();

            $products = $ProductModel->findAll();
            foreach ($products as $product) {
                $stock = [
                    'outlet_id' => $outlet_id,
                    'product_id' => $product['id']
                ];

                $StockModel->save($stock);
            }

            session()->setFlashdata('pesan','Data Berhasil Ditambahkan!');

            return redirect()->to('user/outlet');
    }

public function update($id)

    {
        // ambil data yang akan diedit
        $outlets = new OutletModel();
        $data['outlet'] = $outlets->where('id', $id)->first();
        $input = $this->request->getPost();
        
        
            $validation =  \Config\Services::validation();
            $data = [
                'id' => $id,
                'nama'    => $input['nama'],
                'alamat' => $input['alamat'],
                'area_id' => $input['area'],
            ];
            // Validasi
            if (! $this->validate([
                'nama' => "required|max_length[255]',",
                'alamat' => "required|max_length[255]',",
                'area' => "required|max_length[255]',",
            ])) {
                
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }
            // Simpan Data
            $outlets->save($data);

            session()->setFlashdata('edit','Data Berhasil Diubah!');
            // tampilkan form edit
            return redirect()->to('user/outlet');
   
    }

public function delete($id)

    {
        $outlets = new OutletModel();
        $StockModel = new StockModel();

        $outlets->delete($id);

        $stocks = $StockModel->findAll();
        foreach ($stocks as $stock) {
            if ($stock['outlet_id'] === $id) {
                $stock_id = $stock['id'];
                $StockModel->delete($stock_id);
            }
        }

        return redirect('user/outlet');
    }

}