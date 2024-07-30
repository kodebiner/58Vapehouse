<?php

namespace App\Controllers;

use App\Models\OutletModel;
use App\Models\ProductModel;
use App\Models\VariantModel;
use App\Models\StockModel;
use App\Models\OldStockModel;
use App\Models\StockmovementModel;
use App\Models\StockMoveDetailModel;

class StockMovement extends BaseController
{
    protected $data;
    protected $db, $builder;
    protected $auth;
    protected $config;

    public function index()
    {
        // Calling Model
        $ProductModel               = new ProductModel();
        $VariantModel               = new VariantModel();
        $OutletModel                = new OutletModel();
        $StockModel                 = new StockModel();
        $StockmovementModel         = new StockmovementModel();
        $StockMoveDetailModel       = new StockMoveDetailModel();

        // Populating Data
        $data                       = $this->data;

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
            $stockmovements         = $StockmovementModel->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->orderBy('id', 'DESC')->paginate(20, 'stockmovement');
        } else {
            $stockmovements         = $StockmovementModel->where('origin', $this->data['outletPick'])->orWhere('destination', $this->data['outletPick'])->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->orderBy('id', 'DESC')->paginate(20, 'stockmovement');
        }

        $outlets                    = $OutletModel->findAll();

        $productlist                = $ProductModel->where('status', '1')->find();

        $stockmovedata              = array();
        if (!empty($stockmovements)) {
            foreach ($stockmovements as $stockmove) {
                $stockmovedetails   = $StockMoveDetailModel->where('stockmoveid', $stockmove['id'])->find();
                $dataorigin         = $OutletModel->find($stockmove['origin']);
                $datadestination    = $OutletModel->find($stockmove['destination']);

                if (!empty($dataorigin)) {
                    $origin         = $dataorigin['name'];
                    $originid       = $dataorigin['id'];
                } else {
                    $origin         = '';
                    $originid       = '';
                }

                if (!empty($datadestination)) {
                    $destination    = $datadestination['name'];
                    $destinationid  = $datadestination['id'];
                } else {
                    $destination = '';
                    $destinationid  = '';
                }

                $stockmovedata[$stockmove['id']]['id']              = $stockmove['id'];
                $stockmovedata[$stockmove['id']]['origin']          = $origin;
                $stockmovedata[$stockmove['id']]['originid']        = $originid;
                $stockmovedata[$stockmove['id']]['destination']     = $destination;
                $stockmovedata[$stockmove['id']]['destinationid']   = $destinationid;
                $stockmovedata[$stockmove['id']]['date']            = $stockmove['date'];
                $stockmovedata[$stockmove['id']]['status']          = $stockmove['status'];

                $arrayqty       = array();
                $arrayprice     = array();
                if (!empty($stockmovedetails)) {
                    foreach ($stockmovedetails as $movedet) {
                        $movementvariants           = $VariantModel->find($movedet['variantid']);
    
                        if (!empty($movementvariants)) {
                            $movementproducts       = $ProductModel->find($movementvariants['productid']);
                            $stocks                 = $StockModel->where('variantid', $movementvariants['id'])->where('outletid', $stockmove['origin'])->first();
    
                            if (!empty($movementproducts)) {
                                $product = $movementproducts['name'];
                            } else {
                                $product = '';
                            }
    
                            if (!empty($stocks)) {
                                $qty = $stocks['qty'];
                            } else {
                                $qty = '';
                            }
    
                            $varid      = $movementvariants['id'];
                            $variants   = $movementvariants['name'];
                            $sku        = $movementvariants['sku'];
                            $wholesale  = $movementvariants['hargamodal'];
                        } else {
                            $varid      = '';
                            $variants   = '';
                            $sku        = '';
                            $product    = '';
                            $qty        = '';
                            $wholesale  = '';
                        }
    
                        $stockmovedata[$stockmove['id']]['detail'][$movedet['id']]['name']          = $product.' - '.$variants;
                        $stockmovedata[$stockmove['id']]['detail'][$movedet['id']]['productname']   = $product;
                        $stockmovedata[$stockmove['id']]['detail'][$movedet['id']]['variantname']   = $variants;
                        $stockmovedata[$stockmove['id']]['detail'][$movedet['id']]['sku']           = $sku;
                        $stockmovedata[$stockmove['id']]['detail'][$movedet['id']]['varid']         = $varid;
                        $stockmovedata[$stockmove['id']]['detail'][$movedet['id']]['qty']           = $qty;
                        $stockmovedata[$stockmove['id']]['detail'][$movedet['id']]['wholesale']     = $wholesale;
                        $stockmovedata[$stockmove['id']]['detail'][$movedet['id']]['inputqty']      = $movedet['qty'];
    
                        $arrayqty[]     = $movedet['qty'];
                        $arrayprice[]   = $wholesale;
                    }
                } else {
                    $stockmovedata[$stockmove['id']]['detail']      = array();
                }
                    
                $stockmovedata[$stockmove['id']]['totalqty']        = array_sum($arrayqty);
                $stockmovedata[$stockmove['id']]['totalwholesale']  = array_sum($arrayprice);
            }
        }

        // Parsing data to view
        $data['title']              = lang('Global.stockmoveList');
        $data['description']        = lang('Global.stockmoveListDesc');
        $data['stockmovements']     = $stockmovements;
        $data['stockmovedata']      = $stockmovedata;
        $data['productlist']        = $productlist;
        $data['outlets']            = $outlets;
        $data['pager']              = $StockmovementModel->pager;
        $data['startdate']          = strtotime($startdate);
        $data['enddate']            = strtotime($enddate);

        return view ('Views/stockmove', $data);
    }

    public function product()
    {
        // Calling Model
        $VariantModel   = new VariantModel();
        $StockModel     = new StockModel();
        $ProductModel   = new ProductModel();

        // initialize
        $input      = $this->request->getPost();

        $product    = $ProductModel->find($input['id']);

        $variants   = $VariantModel->where('productid', $input['id'])->find();

        $variantid = array();
        foreach ($variants as $var) {
            $variantid[]    = $var['id'];
        }
        
        if (isset($input['outletid'])) {
            $stocks     = $StockModel->whereIn('variantid', $variantid)->where('outletid', $input['outletid'])->find();
        } else {
            $stocks     = $StockModel->whereIn('variantid', $variantid)->where('outletid', $this->data['outletPick'])->find();
        }

        $return = array();
        foreach ($stocks as $stock) {
            foreach ($variants as $variant) {
                if ($stock['variantid'] === $variant['id']) {
                    $return[] = [
                        'id'        => $variant['id'],
                        'product'   => $product['name'],
                        'variant'   => $variant['name'],
                        'sku'       => $variant['sku'],
                        'wholesale' => $variant['hargamodal'],
                        'name'      => $product['name'].' - '.$variant['name'],
                        'qty'       => $stock['qty'],
                        'price'     => $variant['hargadasar'],
                        'sellprice' => (int)$variant['hargamodal'] + (int)$variant['hargajual'],
                        'msrp'      => $variant['hargarekomendasi']
                    ];
                }
            }
        }
        
        die(json_encode($return));
    }

    public function createpur()
    {
        // Validate Data
        $validation = \Config\Services::validation();

        // Calling Model
        $PurchaseModel              = new PurchaseModel();
        $PurchasedetailModel        = new PurchasedetailModel();

        // Find Data
        $purchasedetails            = $PurchasedetailModel->findAll();

        // initialize
        $input = $this->request->getPost();

        // date time stamp
        $date=date_create();
        $tanggal = date_format($date,'Y-m-d H:i:s');

        $data = [
            'outletid'              => $this->data['outletPick'],
            'userid'                => $this->data['uid'],
            'supplierid'            => $input['supplierid'],
            'date'                  => $tanggal,
            'status'                => "0",
        ];

        // Save Data Purchase
        $PurchaseModel->insert($data);

        // Get Purchase ID
        $purchaseid = $PurchaseModel->getInsertID();

        // Purchase Detail
        foreach ($input['totalpcs'] as $varid => $value) {
            $datadetail   = [
                'purchaseid'    => $purchaseid,
                'variantid'     => $varid,
                'qty'           => $value,
                'price'         => $input['bprice'][$varid],
            ];

            // Save Data Purchase Detail
            $PurchasedetailModel->save($datadetail);
        }

        // return
        return redirect()->back()->with('message', lang('Global.saved'));
    }

    public function updatepur($id)
    {
        // Validate Data
        $validation = \Config\Services::validation();

        // Calling Model
        $PurchaseModel              = new PurchaseModel();
        $PurchasedetailModel        = new PurchasedetailModel();

        // Find Data
        $purchasedetails            = $PurchasedetailModel->findAll();

        // initialize
        $input = $this->request->getPost();

        // date time stamp
        $date       = date_create();
        $tanggal    = date_format($date,'Y-m-d H:i:s');

        $data = [
            'id'                    => $id,
            'outletid'              => $this->data['outletPick'],
            'userid'                => $this->data['uid'],
            'supplierid'            => $input['supplierid'.$id],
            'date'                  => $tanggal,
        ];

        // Save Data Purchase
        $PurchaseModel->save($data);

        // Purchase Detail
        foreach ($input['totalpcs'] as $varid => $value) {
            $datadetail   = [
                'id'            => $varid,
                'qty'           => $value,
                'price'         => $input['bprice'][$varid],
            ];
            if ($datadetail['qty'] === "0") {
                $PurchasedetailModel->delete($varid);
            }

            // Save Data Purchase Detail
            $PurchasedetailModel->save($datadetail);
        }

        if (isset($input['addtotalpcs'])) {
            foreach ($input['addtotalpcs'] as $var => $val) {
                $adddata = [
                    'purchaseid'    => $id,
                    'variantid'     => $var,
                    'qty'           => $val,
                    'price'         => $input['addbprice'][$var],
                ];

                // Save Data Purchase Detail
                $PurchasedetailModel->save($adddata);
            }
        }

        // return
        return redirect()->back()->withInput()->with('message', lang('Global.saved'));
    }

    public function confirmpur($id)
    {
        // Calling Model
        $PurchaseModel              = new PurchaseModel();
        $PurchasedetailModel        = new PurchasedetailModel();
        $StockModel                 = new StockModel;
        $VariantModel               = new VariantModel;
        $OldStockModel              = new OldStockModel;

        // initialize
        $input = $this->request->getPost();

        // date time stamp
        $date       = date_create();
        $tanggal    = date_format($date,'Y-m-d H:i:s');

        $data = [
            'id'                    => $id,
            'date'                  => $tanggal,
            'status'                => "1",
        ];

        $PurchaseModel->save($data);

        foreach ($input['ctotalpcs'][$id] as $key => $value) {
            // Update Purchase Detail
            $purdet = $PurchasedetailModel->where('purchaseid', $id)->where('variantid', $key)->first();
            $purdetdata = [
                'id'                => $purdet['id'],
                'qty'               => $value,
                'price'             => $input['cbprice'][$id][$key]
            ];
            $PurchasedetailModel->save($purdetdata);

            // Update Old Stock
            $variant = $VariantModel->find($key);
            $oldstock = $OldStockModel->where('variantid', $key)->first();
            $oldstockdata = [
                'id'                => $oldstock['id'],
                'hargadasar'        => $variant['hargadasar']
            ];
            $OldStockModel->save($oldstockdata);

            // Finding Total Stock
            $Stocks = $StockModel->where('variantid', $key)->find();
            $totalstock = 0;
            foreach ($Stocks as $stock) {
                $totalstock += $stock['qty'];
            }

            // Update Stock
            $stock = $StockModel->where('variantid', $key)->where('outletid', $this->data['outletPick'])->first();
            $stockdata = [
                'id'                => $stock['id'],
                'qty'               => $stock['qty']+$value,
                'restock'           => $tanggal
            ];
            $StockModel->save($stockdata);

            // Update Variant
            $variantdata = [
                'id'                => $key,
                'hargadasar'        => floor((($variant['hargadasar'] * $totalstock) + ($input['cbprice'][$id][$key] * $value)) / ($totalstock + $value))
            ];
            $VariantModel->save($variantdata);
        }

        // return
        return redirect()->back()->with('message', lang('Global.saved'));
    }

    public function cancelpur($id)
    {
        // calling Model
        $PurchaseModel      = new PurchaseModel();
        
        // search id
        $data['purchases'] = $PurchaseModel->where('id', $id)->first();

        // initialize
        $input              = $this->request->getpost();

        // date time stamp
        $date=date_create();
        $tanggal = date_format($date,'Y-m-d H:i:s');

        // validation
        $data = [
            'id'                    => $id,
            'outletid'              => $this->data['outletPick'],
            'userid'                => $this->data['uid'],
            'date'                  => $tanggal,
            'status'                => "2",
        ];
        $PurchaseModel->save($data);

        return redirect()->back()->with('massage', lang('global.saved'));
    }
}