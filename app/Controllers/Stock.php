<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\ProductModel;
use App\Models\OutletModel;
use App\Models\AreaModel;
use App\Models\StockModel;
use App\Models\VariantModel;

class Stock extends BaseController

{
    protected $db, $builder;
    protected $auth;
    protected $config;

    public function __construct()

        {
            $this->db      = \Config\Database::connect();
            $validation    = \Config\Services::validation();
            $this->builder = $this->db->table('stock');
            $this->config  = config('Auth');
            $this->auth    = service('authentication');
        }


     public function index()

        {
            // Calling Model
            $StockModel     = new StockModel;
            $VariantModel   = new VariantModel;
            $ProductModel   = new ProductModel;
            $OutletModel    = new OutletModel;
            
            // Find Data
            $stocks         = $StockModel->findAll();
            $variants       = $VariantModel->findAll();
            $products       = $ProductModel->findAll();
            $outlets        = $OutletModel->findAll();

            // Parsing data to view
            $data                   = $this->data;
            $data['title']          = lang('Global.stockList');
            $data['description']    = lang('Global.stockListDesc');
            $data['stocks']         = $stocks;
            $data['variants']       = $variants;
            $data['products']       = $products;
            $data['outlets']        = $outlets;

            return view ('Views/stock', $data);
        }


    public function create()
    
    {
            // Calling Model
            $StockModel      = new StockModel;
            $VariantModel    = new VariantModel;
            $ProductModel    = new ProductModel;
            
            // Finding Data
            $stocks          = $StockModel->findAll();
            $variant         = $VariantModel->findAll();
            $products        = $ProductModel->findAll();

            // initialize
            $input = $this->request->getPost(); 

            // parsing data to view
            $data['stocks']      = $stocks;
            $data['variants']    = $variant;
            $data['products']    = $products;

          
            $data = [
                'stock'    => $input['stock'],
            ];
            // Validasi
            if (! $this->validate([
                'stock' => "required|max_length[255]',",
            ])) {
                
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            // Save Data
            $StockModel->save($data);
    
            // Kembali Ke Tampilan awal
            session()->setFlashdata('edit','Data Berhasil Diubah!');
            return redirect()->back();
    }

    public function edit($id) 
    {
        $StockModel     = new StockModel();
        $ProductModel   = new ProductModel();
        $VariantModel   = new VariantModel();

        $stock      = $StockModel->where( 'id', $id)->first();
        $product    = $ProductModel->findAll();

    }

    public function delete()

    {

    }


}
