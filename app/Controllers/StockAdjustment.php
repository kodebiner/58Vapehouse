<?php 

namespace App\Controllers;
use CodeIgniter\Controller;

use App\Models\ProductModel;
use App\Models\VariantModel;
use App\Models\OutletModel;
use App\Models\StockModel;
use App\Models\StockAdjustmentModel;

Class StockAdjustment extends BaseController{

    public function index(){

        // Calling Model
        $Product    = new ProductModel;
        $Variant    = new VariantModel;
        $Outlet     = new OutletModel;
        $Stock      = new StockModel;
        $StockAdj   = new StockAdjustmentModel;

        //Populating Data
        $stockadjust    = $StockAdj->orderBy('id', 'DESC')->findAll();

        // Parsing Data To View
        $data                   = $this->data;
        $data['title']          = lang('Global.stockadjList');
        $data['description']    = lang('Global.stockadjListDesc');
        $data['products']       = $Product->findAll();
        $data['variants']       = $Variant->findAll();
        $data['outlets']        = $Outlet->findAll();
        $data['stocks']         = $Stock->findAll();
        $data['stockadj']       = $StockAdj->findAll();

        return view ('Views/stockadjustment', $data);

    }
}

?>