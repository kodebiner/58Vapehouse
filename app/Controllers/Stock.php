<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\OutletModel;
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


    public function restock()
    {
            // Calling Model
            $StockModel     = new StockModel;
            $VariantModel   = new VariantModel;
            $TotalModel     = new TotalStockModel;

            // initialize
            $input = $this->request->getPost();

            // Finding Total Stock
            $Stocks = $StockModel->where('variantid', $input['variant'])->find();
            $totalstock = 0;
            foreach ($Stocks as $stock) {
                $totalstock += $stock['qty'];
            }

            // Finding new pric
            $variant    = $VariantModel->find($input['variant']);
            $hargadasar = (($variant['hargadasar']*$totalstock)+($input['hargadasar']*$input['qty']))/($totalstock+$input['qty']);
            $hargamodal = (($variant['hargamodal']*$totalstock)+($input['hargamodal']*$input['qty']))/($totalstock+$input['qty']);

            // Updating variant
            $var        = [
                'id'            => $input['variant'],
                'hargadasar'    => $hargadasar,
                'hargamodal'    => $hargamodal,
                
            ];  
            $VariantModel->save($var);

            // date time stamp
            $date=date_create();
            $tanggal = date_format($date,'Y-m-d H:i:s');

            $stocks = $StockModel->where('variantid',$input['variant'])->where('outletid',$input['outlet'])->first();
            $stk = [
                'id'         => $stocks['id'],
                'qty'        => $input['qty'],
                'restock'    => $tanggal,
            ];



            // Save Data Stok
            $StockModel->save($stk);

            // return
            return redirect()->back()->with('message', lang('Global.saved'));
    }
 

}
