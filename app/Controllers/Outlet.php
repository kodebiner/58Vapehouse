<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\OutletModel;
use App\Models\ProductModel;
use App\Models\VariantModel;
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
        $data['description']    = lang('Global.outletListDesc');
        $data['roles']          = $GroupModel->findAll();
        $data['outlets']          = $query->getResult();

        return view('Views/outlet', $data);

    }

public function create()

    {
            $validation = \Config\Services::validation();

            // Calling Models
            $OutletModel = new OutletModel;
            $ProductModel = new ProductModel;
            $StockModel = new StockModel;
            $VariantModel = new VariantModel();

            // Populating data
            $input = $this->request->getPost();
            $outlets = $OutletModel->findAll();
            $variants = $VariantModel->findAll();

            $data = [
                'name'    => $input['name'],
                'address'  => $input['address'],
                'maps' => $input['maps'],
            ];
        
            if (! $this->validate([
                'name' => "required|max_length[255]',",
                'address'  => 'required',
                'maps'  => 'required|max_length[255]',
            ])) {
                
               return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }
            
            // Inserting Outlet
            $OutletModel->insert($data);

            //Getting Outlet ID
            $outletID = $OutletMode->getInsertID();

            // Adding stocks
            foreach ($variants as $variant) {
                $stock = [
                    'outletid'  => $outletID,
                    'variantid' => $variant['id'],
                    'qty'       => '0'
                ];
                $StockModel->insert($stock);
            }

            return redirect()->to('outlet');
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
                'name'    => $input['name'],
                'address' => $input['address'],
                'maps' => $input['maps'],
            ];

            // Validasi
            if (! $this->validate([
                'name' => "max_length[255]',",
                'address' => "max_length[255]',",
                'maps' => "max_length[255]',",
            ])) {

            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }
            // Simpan Data
            $outlets->save($data);

            session()->setFlashdata('edit','Data Berhasil Diubah!');
            // tampilkan form edit
            return redirect()->to('outlet');
   
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

        return redirect('outlet')->with('message', lang('Global.deleted'));
    }

}