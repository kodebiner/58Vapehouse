<?php 

namespace App\Controllers;
use CodeIgniter\Controller;

use App\Models\ProductModel;
use App\Models\VariantModel;
use App\Models\OutletModel;
use App\Models\StockModel;
use App\Models\StockmovementModel;

Class Stockmove extends BaseController{


    public function __construct()
    {
            $this->db      = \Config\Database::connect();
            $validation    = \Config\Services::validation();
            $this->builder = $this->db->table('stock');
            $this->config  = config('Auth');
            $this->auth    = service('authentication');
        
    }


    public function index(){

        // Calling Database
        $Product    = new ProductModel;
        $Variant    = new VariantModel;
        $Outlet     = new OutletModel;
        $Stock      = new StockModel;
        $StockMove  = new StockmovementModel;

        // Parsing Data To View
        $data                   = $this->data;
        $data['title']          = lang('Global.stockMove');
        $data['description']    = lang('Global.stockMoveDesc');
        $data['products']       = $Product->findAll();
        $data['variants']       = $Variant->findAll();
        $data['outlets']        = $Outlet->findAll();
        $data['stocks']         = $Stock->findAll();
        $data['stockmoves']     = $StockMove->findAll();

        return view ('Views/stockmove', $data);

    }

    public function create() {

        // Calling Database
        $Product    = new ProductModel;
        $Variant    = new VariantModel;
        $Outlet     = new OutletModel;
        $Stock      = new StockModel;
        $StockMove  = new StockmovementModel;

        
   


    }


}

?>