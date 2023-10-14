<?php 

namespace App\Controllers;
use CodeIgniter\Controller;

use App\Models\ProductModel;
use App\Models\VariantModel;
use App\Models\OutletModel;
use App\Models\StockModel;
use App\Models\StockmovementModel;

Class Stockmove extends BaseController
{
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
        $db         = \Config\Database::connect();
        $pager      = \Config\Services::pager();

        // Calling Database
        $ProductModel           = new ProductModel();
        $VariantModel           = new VariantModel();
        $OutletModel            = new OutletModel();
        $StockmovementModel     = new StockmovementModel();

        // Find Data
        $data                   = $this->data;
        $outlets                = $OutletModel->findAll();
        $productlist            = $ProductModel->findAll();

        if ($this->data['outletPick'] === null) {
            $stockmoves         = $StockmovementModel->orderBy('id', 'DESC')->paginate(20, 'stockmove');
        } else {
            $stockmoves         = $StockmovementModel->orderBy('id', 'DESC')->where('origin', $this->data['outletPick'])->paginate(20, 'stockmove');
        }

        if (!empty($stockmoves)) {
            $varid  = array();
            foreach ($stockmoves as $stkmv) {
                $varid[]    = $stkmv['variantid'];
            }
            $variants       = $VariantModel->find($varid);
    
            $productid = array();
            foreach ($variants as $var) {
                $productid[]    = $var['productid'];
            }
            $products           = $ProductModel->find($productid);
        } else {
            $variants           = array();
            $products           = array();
        }

        // Parsing Data To View
        $data['title']          = lang('Global.stockmoveList');
        $data['description']    = lang('Global.stockmoveListDesc');
        $data['stockmoves']     = $stockmoves;
        $data['products']       = $products;
        $data['variants']       = $variants;
        $data['outlets']        = $outlets;
        $data['productlist']    = $productlist;
        $data['pager']          = $StockmovementModel->pager;

        return view ('Views/stockmove', $data);
    }

    public function product()
    {
        // Calling Model
        $VariantModel   = new VariantModel();
        $StockModel     = new StockModel();
        $ProductModel   = new ProductModel();

        // initialize
        $input      = $this->request->getPost('id');

        $product    = $ProductModel->find($input);

        $variants   = $VariantModel->where('productid', $input)->find();

        $variantid = array();
        foreach ($variants as $var) {
            $variantid[]    = $var['id'];
        }

        $stocks     = $StockModel->whereIn('variantid', $variantid)->where('outletid', $this->data['outletPick'])->find();

        $return = array();
        foreach ($stocks as $stock) {
            foreach ($variants as $variant) {
                if ($stock['variantid'] === $variant['id']) {
                    $return[] = [
                        'id'    => $variant['id'],
                        'name'  => $product['name'].' - '.$variant['name'],
                        'qty'   => $stock['qty'],

                    ];
                }
            }
        }
        
        die(json_encode($return));
    }

    public function create()
    {
        // Calling Database
        $Product    = new ProductModel;
        $Variant    = new VariantModel;
        $Outlet     = new OutletModel;
        $Stock      = new StockModel;
        $StockMove  = new StockmovementModel;

        // initialize
        $input = $this->request->getPost();

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