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
        $data['title']          = lang('Global.stockmoveList');
        $data['description']    = lang('Global.stockmoveListDesc');
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

        // initialize
        $input = $this->request->getPost();

        // // rules
        // $rule = [
        //     'variant'            => 'required|max_length[255]',
        //     'origin'             => 'required|max_length[255]',
        //     'destination'        => 'required',
        //     'qty'                => 'required',
        // ];
                
        // // Validation
        // if (! $this->validate($rule)) {
        //     return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        // }

        // date time stamp
        $date=date_create();
        $tanggal = date_format($date,'Y-m-d H:i:s');

        // Stock Movement
        foreach ($input['totalpcs'] as $varid => $value) {
            $data   = [
                'variantid'     => $varid,
                'qty'           => $value,
                'origin'        => $input['origin'],
                'destination'   => $input['destination'],
                'date'          => $tanggal,
            ];
    
            // insert data Stockmove
            $StockMove->insert($data);

            // Minus Stock
            $Stocks     = $Stock->where('variantid', $varid)->where('outletid',$input['origin'])->find(); 
            $minstock   = $value;
            foreach ($Stocks as $stock) {
                $hasilmin = $stock['qty'] -= $minstock;
                $stok = [
                    'id'    => $stock['id'],
                    'qty'   => $hasilmin,
                ];
            }
            $Stock->save($stok);
    
            // Plus Stock
            $Stocks = $Stock->where('variantid', $varid)->where('outletid',$input['destination'])->find();
            $plusstock = $value;
            foreach ($Stocks as $stock) {
                $hasilplus = $stock['qty'] += $plusstock;
                $stok = [
                    'id'    => $stock['id'],
                    'qty'   =>  $hasilplus,
                ];
            }
            $Stock->save($stok);
        }

        return redirect()->back()->with('message', lang('Global.saved'));
    }
}

?>