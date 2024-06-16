<?php

namespace App\Controllers;

use App\Models\StockModel;
use App\Models\OutletModel;
use App\Models\ProductModel;
use App\Models\VariantModel;

class Reminder extends BaseController
{
    protected $data;
    protected $db, $builder;
    protected $auth;
    protected $config;

    public function __construct()
    {
        $this->db      = \Config\Database::connect();
        $validation    = \Config\Services::validation();
        $this->config  = config('Auth');
        $this->auth    = service('authentication');
    }


    public function index()
    {
        $pager      = \Config\Services::pager();

        // Calling Model
        $OutletModel    = new OutletModel;
        $StockModel     = new StockModel;
        $VariantModel   = new VariantModel;
        $ProductModel   = new ProductModel;

        // Finding Data
        $data           = $this->data;
        $outlets        = $OutletModel->findAll();

        if ($this->data['outletPick'] === null) {
            $stocks     = $StockModel->orderBy('id', 'DESC')->where('restock !=', '0000-00-00 00:00:00')->where('sale !=', '0000-00-00 00:00:00')->paginate(20, 'reminder');
        } else {
            $stocks     = $StockModel->orderBy('id', 'DESC')->where('restock !=', '0000-00-00 00:00:00')->where('sale !=', '0000-00-00 00:00:00')->where('outletid', $this->data['outletPick'])->paginate(20, 'reminder');
        }

        if (!empty($stocks)) {
            $varid = array();
            foreach ($stocks as $stock) {
                $varid[]        = $stock['variantid'];
            }
            $variants       = $VariantModel->find($varid);

            $prodid = array();
            foreach ($variants as $var) {
                $prodid[] = $var['productid'];
            }
            $products       = $ProductModel->find($prodid);
        } else {
            $variants       = array();
            $products       = array();
        }
    
        // Parsing data to view
        $data['title']          = lang('Global.reminder');
        $data['description']    = lang('Global.reminder');
        $data['variants']       = $variants;
        $data['products']       = $products;
        $data['stocks']         = $stocks;
        $data['outlets']        = $outlets;
        $data['pager']          = $StockModel->pager;

        return view ('Views/reminder', $data);
    }
}
