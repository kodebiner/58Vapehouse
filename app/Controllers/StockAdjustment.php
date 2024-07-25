<?php

namespace App\Controllers;

use CodeIgniter\Controller;

use App\Models\ProductModel;
use App\Models\VariantModel;
use App\Models\OutletModel;
use App\Models\StockModel;
use App\Models\StockAdjustmentModel;

class StockAdjustment extends BaseController
{
    protected $data;
    protected $db, $builder;
    protected $auth;
    protected $config;
    
    public function index()
    {
        $pager      = \Config\Services::pager();

        // Calling Model
        $ProductModel           = new ProductModel;
        $VariantModel           = new VariantModel;
        $OutletModel            = new OutletModel;
        $StockModel             = new StockModel;
        $StockAdjustmentModel   = new StockAdjustmentModel;

        // Populating Data
        $input = $this->request->getGet('daterange');

        if (!empty($input)) {
            $daterange = explode(' - ', $input);
            $startdate = $daterange[0];
            $enddate = $daterange[1];
        } else {
            $startdate  = date('Y-m-1' . ' 00:00:00');
            $enddate    = date('Y-m-t' . ' 23:59:59');
        }

        if ($this->data['outletPick'] === null) {
            // $stockadjust     = $StockAdjustmentModel->orderBy('id', 'DESC')->paginate(20, 'stockadjustment');

            // if (!empty($input)) {
            //     if ($startdate === $enddate) {
                    $stockadjust = $StockAdjustmentModel->orderBy('id', 'DESC')->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->paginate(20, 'stockadjustment');
            //     } else {
            //         $stockadjust = $StockAdjustmentModel->orderBy('id', 'DESC')->where('date >=', $startdate . '00:00:00')->where('date <=', $enddate . '23:59:59')->paginate(20, 'stockadjustment');
            //     }
            // }
        } else {
            // $stockadjust     = $StockAdjustmentModel->orderBy('id', 'DESC')->where('outletid', $this->data['outletPick'])->paginate(20, 'stockadjustment');

            // if (!empty($input)) {
            //     if ($startdate === $enddate) {
                    $stockadjust = $StockAdjustmentModel->orderBy('id', 'DESC')->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->where('outletid', $this->data['outletPick'])->paginate(20, 'stockadjustment');
            //     } else {
            //         $stockadjust = $StockAdjustmentModel->orderBy('id', 'DESC')->where('date >=', $startdate . '00:00:00')->where('date <=', $enddate . '23:59:59')->where('outletid', $this->data['outletPick'])->paginate(20, 'stockadjustment');
            //     }
            // }
        }
        $outlets        = $OutletModel->findAll();
        $productlist    = $ProductModel->findAll();

        if (!empty($stockadjust)) {
            $stockid    = array();
            $varid      = array();
            foreach ($stockadjust as $stockadj) {
                $stockid[]  = $stockadj['stockid'];
                $varid[]    = $stockadj['variantid'];
            }
            $variants       = $VariantModel->find($varid);
            $stocks         = $StockModel->find($stockid);

            $prodid = array();
            foreach ($variants as $var) {
                $prodid[]   = $var['productid'];
            }
            $products       = $ProductModel->find($prodid);
        } else {
            $variants       = array();
            $stocks         = array();
            $products       = array();
        }

        // Parsing Data To View
        $data                   = $this->data;
        $data['title']          = lang('Global.stockadjList');
        $data['description']    = lang('Global.stockadjListDesc');
        $data['products']       = $products;
        $data['variants']       = $variants;
        $data['outlets']        = $outlets;
        $data['stocks']         = $stocks;
        $data['stockadj']       = $stockadjust;
        $data['productlist']    = $productlist;
        $data['pager']          = $StockAdjustmentModel->pager;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);

        return view('Views/stockadjustment', $data);
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
                        'sku'   => $variant['sku'],
                        'name'  => $product['name'] . ' - ' . $variant['name'],
                        'qty'   => $stock['qty'],

                    ];
                }
            }
        }

        die(json_encode($return));
    }

    public function create()
    {
        // Calling Model
        $ProductModel           = new ProductModel;
        $VariantModel           = new VariantModel;
        $OutletModel            = new OutletModel;
        $StockAdjModel          = new StockAdjustmentModel();
        $StockModel             = new StockModel;

        // initialize
        $input = $this->request->getPost();

        // date time stamp
        $date       = date_create();
        $tanggal    = date_format($date, 'Y-m-d H:i:s');

        // Stock Adjusment 
        foreach ($input['totalpcs'] as $varid => $value) {
            $Stocks = $StockModel->where('variantid', $varid)->where('outletid', $input['outlet'])->first();
            if (($Stocks['qty'] === "0") && ($input['type'] === "1")) {
                return redirect()->back()->with('error', lang('Global.alertstock'));
            }
            $adj = [
                'variantid' => $varid,
                'outletid'  => $input['outlet'],
                'stockid'   => $Stocks['id'],
                'type'      => $input['type'],
                'date'      => $tanggal,
                'qty'       => $value,
                'note'      => $input['note'],
            ];
            $StockAdjModel->insert($adj);

            // Update Stock
            $totalstock = $Stocks['qty'];

            if ((int)$input['type'] === 0) {
                $totalstock += $value;
            } else {
                $totalstock -= $value;
            }

            $stok = [
                'id'        => $Stocks['id'],
                'qty'       => $totalstock,
                'restock'   => $tanggal,
            ];
            $StockModel->save($stok);
        }

        // return
        return redirect()->back()->with('message', lang('Global.saved'));
    }
}
