<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\ProductModel;
use App\Models\OutletModel;
use App\Models\AreaModel;
use App\Models\StockModel;
use App\Models\TotalStockModel;
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
            $data           = $this->data;
            $products       = $ProductModel->findAll();
            $outlets        = $OutletModel->findAll();
            $variants       = $VariantModel->findAll();


            if ($this->data['outletPick'] === null) {
                $stock      = $StockModel->findAll();
            } else {
                $stock      = $StockModel->where('outletid', $this->data['outletPick'])->find();
            }


            // Parsing data to view
            $data['title']          = lang('Global.stockList');
            $data['description']    = lang('Global.stockListDesc');
            $data['stocks']         = $stock;
            $data['variants']       = $variants;
            $data['products']       = $products;
            $data['outlets']        = $outlets;

            return view ('Views/stock', $data);
        }


    public function create($id)
    {
            // Calling Model
            $StockModel     = new StockModel;
            $VariantModel   = new VariantModel;
            $TotalModel     = new TotalStockModel;
            
            // Finding Data

            // initialize
            $input = $this->request->getPost(); 

            // parsing data to view
            $data['stocks']      = $StockModel;
            $data['variants']    = $VariantModel;
            $data['total']       = $TotalModel;

            $stocks = $StockModel->where('variantid',$id)->where('outletid',$id)->first();
            $stk = [
                'id'     => $id,
                'qty'    => $input['qty'],
            ];

            // Validation
            if (! $this->validate([
                'stock' => "required|max_length[255]',",
            ])) {
                
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            // Save Data Stok
            $StockModel->save($stk);

            //update total stock & variant
            $varian     = $VariantModel->where($VariantModel['id']===$stocks['variantid'])->find();
            $var        = [
                'id'            => $varian,
                'hargadasar'    => 'hargadasar',
                'hargamodal'    => 'hargamodal',
            ];
  
            $VariantModel->save($var);

            // return
            return redirect()->back()->with('message', lang('Global.saved'));
    }


    public function delete()

    {
        
    }

 

}
