<?php 

namespace App\Controllers;
use CodeIgniter\Controller;

use App\Models\ProductModel;
use App\Models\VariantModel;
use App\Models\OutletModel;
use App\Models\StockModel;
use App\Models\MemberModel;
use App\Models\StockAdjustmentModel;

Class StockAdjustment extends BaseController{

    public function index(){

        // Calling Model
        $Product    = new ProductModel;
        $Variant    = new VariantModel;
        $Outlet     = new OutletModel;
        $Stock      = new StockModel;
        $customers  = new MemberModel;
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
        $data['customers']      = $customers->findAll();
        $data['stockadj']       = $StockAdj->findAll();

        return view ('Views/stockadjustment', $data);

    }

    public function create(){

        // Calling Model
        $ProductModel           = new ProductModel;
        $VariantModel           = new VariantModel;
        $OutletModel            = new OutletModel;
        $StockAdjModel          = new StockAdjustmentModel();
        $StockModel             = new StockModel;
        
        // initialize
        $input = $this->request->getPost();
  
        // // Date
        $tanggal = date("Y-m-d H:i:s");

        if ((int)$input['type'] === 0){
            $hasil = "+".$input['qty'];    
        } else {
            $hasil = "-".$input['qty'];
        }

        $Stocks = $StockModel->where('variantid',$input['variant'])->where('outletid',$input['outlet'])->first();
        // Stock Adjusment 
        $adj = [
            'variantid' => $input['variant'],
            'outletid'  => $input['outlet'],
            'stockid'   => $Stocks['id'],
            'type'      => $input['type'],
            'date'      => date("Y-m-d H:i:s"),
            'qty'       => $hasil,
            'note'      => $input['note'],
        ];
        
        $StockAdjModel->insert($adj);
        
        // Update Stock
        $Stocks = $StockModel->where('variantid', $input['variant'])->where('outletid',$input['outlet'])->first();
        $totalstock = $Stocks['qty'];

        if ((int)$input['type'] === 0) {
                $totalstock += $input['qty'];
        } else {
                $totalstock -= $input['qty']; 
        }

            $stok = [
                'id'     => $Stocks['id'],
                'qty'    => $totalstock,
            ];  
        $StockModel->save($stok);
        
        // return
        return redirect()->back()->with('message', lang('Global.saved'));
    }

}

?>