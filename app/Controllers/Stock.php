<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\ProductModel;
use App\Models\OutletModel;
use App\Models\AreaModel;
use App\Models\StockModel;

class Stock extends BaseController

{
    protected $db, $builder;
    protected $auth;
    protected $config;

    public function __construct()

        {
            $this->db      = \Config\Database::connect();
            $validation    = \Config\Services::validation();
            $this->builder =   $this->db->table('stock');
            $this->config = config('Auth');
            $this->auth   = service('authentication');
        }


     public function index()

        {
            // Calling Model
            $StockModel     = new StockModel;
            $OutletModel    = new OutletModel;
            $ProductModel   = new ProductModel;
            
            // Find Data
            $stocks         = $StockModel->findAll();
            $outlets        = $OutletModel->findAll();
            $products       = $ProductModel->findAll();
            
            // Parsing data to view
            $data                   = $this->data;
            $data['title']          = lang('Global.stockList');
            $data['description']    = lang('Global.stockListDesc');
            $data['stocks']         = $stocks;
            $data['outlets']        = $outlets;
            $data['products']       = $products;

            return view ('Views/stock', $data);
        }

    public function tampil ($id)

    {
            // Calling Model 
            $StockModel     = new StockModel;
            $OutletModel    = new OutletModel;
            $ProductModel   = new ProductModel;

            // Finding Data
            $products   = $ProductModel->findAll();
            $stocks     = $StockModel->where('outlet_id', $id)->find();
            $outlets    = $OutletModel->find($id);

            // Parsing Data To View
            $data               = $this->data;
            $data['title']      = 'Form Tambah Stock';            
            $data['stocks']     = $stocks;
            $data['outlets']    = $outlets;           
            $data['products']   = $products;

            return view ('user/kelolastock',$data);
    }


    public function editstock($id)

    {
        {
            // Calling Model
            $stocks = new StockModel();
            $OutletModel = new OutletModel;
            $ProductModel = new ProductModel;
            
            $input = $this->request->getPost(); 
            
            $outlets = $OutletModel->findAll();
            $data['outlets'] = $outlets;

            $products = $ProductModel->findAll();
            $data['products'] = $products;

          
            
            $data['stocks'] = $stocks->where('id', $id)->first();
            
           

            $data = [
                'id' =>$id,
                'stock'    => $input['stock'],
            ];
            // Validasi
            if (! $this->validate([
                'stock' => "required|max_length[255]',",
            ])) {
                
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }
            // Simpan Data
            $stocks->save($data);
            
            session()->setFlashdata('edit','Data Berhasil Diubah!');
            // Kembali Ke Tampilan awal
            return redirect()->back();
        }
   
    }
}
