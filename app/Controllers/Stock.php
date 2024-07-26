<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\OutletModel;
use App\Models\StockModel;
use App\Models\OldStockModel;
use App\Models\VariantModel;
use App\Models\SupplierModel;
use App\Models\InventoryModel;
use App\Models\UserModel;
use App\Models\PurchaseModel;
use App\Models\PurchasedetailModel;

class Stock extends BaseController
{
    protected $data;
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

    // Stock
    public function index()
    {
        $db         = \Config\Database::connect();

        // Calling Model
        $StockModel     = new StockModel;
        $VariantModel   = new VariantModel;
        $ProductModel   = new ProductModel;
        $OutletModel    = new OutletModel;
        
        // Find Data
        $data           = $this->data;
        $outlets        = $OutletModel->findAll();

        if ($this->data['outletPick'] === null) {
            $stock          = $StockModel->orderBy('id', 'DESC')->paginate(20, 'stock');
            $stockcount     = count($StockModel->findAll());
            $totalstock     = array_sum(array_column($StockModel->findAll(), 'qty'));
        } else {
            $stock          = $StockModel->orderBy('id', 'DESC')->where('outletid', $this->data['outletPick'])->paginate(20, 'stock');
            $stockcount     = count($StockModel->where('outletid', $this->data['outletPick'])->find());
            $totalstock     = array_sum(array_column($StockModel->where('outletid', $this->data['outletPick'])->find(), 'qty'));
        }

        if (!empty($stock)) {
            $variantid   = array();
            foreach ($stock as $stok) {
                $variantid[]  = $stok['variantid'];
            }
            $variants           = $VariantModel->find($variantid);

            $productid = array();
            foreach ($variants as $variant) {
                $productid[]    = $variant['productid'];
            }
            $products           = $ProductModel->find($productid);
        } else {
            $products = array();
            $variants = array();
        }

        $totalcap = array();
        $capbuilder     = $db->table('stock');
        $stockcap       = $capbuilder->select('stock.qty as qty, variant.hargamodal as price');
        $stockcap       = $capbuilder->join('variant', 'stock.variantid = variant.id', 'left');
        if ($this->data['outletPick'] != null) {
            $stockcap       = $capbuilder->where('stock.outletid', $this->data['outletPick']);
        }
        $stockcap       = $capbuilder->get();
        $caps           = $stockcap->getResult();
        foreach ($caps as $cap) {
            $totalcap[] = (int)$cap->qty * (int)$cap->price;
        }
        $capsum = array_sum($totalcap);

        // Parsing data to view
        $data['title']          = lang('Global.stockList');
        $data['description']    = lang('Global.stockListDesc');
        $data['stocks']         = $stock;
        $data['stockcount']     = $stockcount;
        $data['variants']       = $variants;
        $data['products']       = $products;
        $data['outlets']        = $outlets;
        $data['totalstock']     = $totalstock;
        $data['capsum']         = $capsum;
        $data['pager']          = $StockModel->pager;

        return view ('Views/stock', $data);
    }

    // Stock Cycle
    public function stockcycle()
    {
        // Calling Services
        $pager      = \Config\Services::pager();

        // Calling Model
        $StockModel     = new StockModel;
        $VariantModel   = new VariantModel;
        $ProductModel   = new ProductModel;
        $OutletModel    = new OutletModel;

        // Find Data
        $data           = $this->data;
        
        if ($this->data['outletPick'] === null) {
            $stock      = $StockModel->where('restock !=', '0000-00-00 00:00:00')->where('sale !=', '0000-00-00 00:00:00')->where('qty !=', '0')->orderBy('sale', 'ASC')->paginate(20, 'stockcycle');
        } else {
            $stock      = $StockModel->where('restock !=', '0000-00-00 00:00:00')->where('sale !=', '0000-00-00 00:00:00')->where('qty !=', '0')->orderBy('sale', 'ASC')->where('outletid', $this->data['outletPick'])->paginate(20, 'stockcycle');
        }

        $stockdata  = [];
        // $variants   = $VariantModel->findAll();

        // foreach ($variants as $variant) {
        //     $stockdata[$variant['id']]['id']    = $variant['id'];

        //     $products   = $ProductModel->find($variant['productid']);
        //     $stockdata[$variant['id']]['name']  = $products['name'].'-'.$variant['name'];

        //     if ($this->data['outletPick'] === null) {
        //         $stock      = $StockModel->where('variantid', $variant['id'])->where('restock !=', '0000-00-00 00:00:00')->where('sale !=', '0000-00-00 00:00:00')->find();
        //     } else {
        //         $stock      = $StockModel->where('variantid', $variant['id'])->where('restock !=', '0000-00-00 00:00:00')->where('sale !=', '0000-00-00 00:00:00')->where('outletid', $this->data['outletPick'])->find();
        //     }
        // }
        // dd($stock);
        if (!empty($stock)) {
            foreach ($stock as $stok) {
                $variants       = $VariantModel->find($stok['variantid']);
                $vname          = $variants['name'];
                $outlet         = $OutletModel->find($stok['outletid']);

                if (!empty($variants)) {
                    $products   = $ProductModel->find($variants['productid']);
                    
                    if (!empty($products)) {
                        $pname      = $products['name'];
                    }

                    $stockdata[$stok['id']]['outlet']   = $outlet['name'];
                    $stockdata[$stok['id']]['name']     = $pname.'-'.$vname;
                    $stockdata[$stok['id']]['restock']  = $stok['restock'];
                    $stockdata[$stok['id']]['sale']     = $stok['sale'];
                    $stockdata[$stok['id']]['qty']      = $stok['qty'];
                }
            }
            
            // $varid  = array();
            // foreach ($stock as $stok) {
            //     $varid[]    = $stok['variantid'];
            // }
            // $variants       = $VariantModel->find($varid);
    
            // $prodid = array();
            // foreach ($variants as $var) {
            //     $prodid[]   = $var['productid'];
            // }
            // $products   = $ProductModel->find($prodid);
        } else {
            $variants       = array();
            $products       = array();
        }
        array_multisort(array_column($stockdata, 'sale'), SORT_ASC, $stockdata);

        // Parsing data to view
        $data['title']          = lang('Global.stockCycle');
        $data['description']    = lang('Global.stockCycleDesc');
        // $data['stocks']         = $stock;
        // $data['variants']       = $variants;
        // $data['products']       = $products;
        $data['stocks']         = $stockdata;
        $data['pager']          = $StockModel->pager;

        return view ('Views/stockcycle', $data);
    }

    // Supplier
    public function indexsupplier() 
    {
        // Calling Model
        $SupplierModel = new SupplierModel;

        // Find Data
        $data           = $this->data;
        $suppliers      = $SupplierModel->orderBy('id', 'DESC')->findAll();

        // Parsing data to view
        $data['title']          = lang('Global.supplier');
        $data['description']    = lang('Global.supplierListDesc');
        $data['suppliers']      = $suppliers;

        return view ('Views/supplier', $data);
    }

    public function createsup()
    {
        // Calling Models
        $SupplierModel  = new SupplierModel;

        // Populating data
        $suppliers      = $SupplierModel->findAll();

        // initialize
        $input          = $this->request->getPost();

        // save data
        $data = [
            'name'      => $input['name'],
            'phone'     => $input['phone'],
            'address'   => $input['address'],
            'city'      => $input['city'],
            
        ];
        $SupplierModel->save($data);

        return redirect()->back()->with('message', lang('Global.saved'));
    }

    public function updatesup($id) 
    {
        // calling Model
        $SupplierModel      = new SupplierModel();

        // initialize
        $input              = $this->request->getpost();
        $suppliers          = $SupplierModel->findAll();

        // validation
        $data = [
            'id'            => $id,
            'name'          => $input['name'],
            'phone'         => $input['phone'],
            'address'       => $input['address'],
            'city'          => $input['city'],
        ];
        $SupplierModel->save($data);

        return redirect()->back()->with('massage', lang('global.saved'));
    }

    public function deletesup($id)
    {
        // Calling Model
        $SupplierModel  = new SupplierModel();

        $SupplierModel->delete($id);

        return redirect()->back()->with('error', lang('Global.deleted'));
    }

    public function indexpurchase()
    {
        // Calling Model
        $SupplierModel              = new SupplierModel();
        $ProductModel               = new ProductModel();
        $VariantModel               = new VariantModel();
        $OutletModel                = new OutletModel();
        $UserModel                  = new UserModel();
        $PurchaseModel              = new PurchaseModel();
        $PurchasedetailModel        = new PurchasedetailModel();
        $OldStockModel              = new OldStockModel();
        $StockModel                 = new StockModel();

        // Find Data
        $data                       = $this->data;

        if ($this->data['outletPick'] === null) {
            $purchases              = $PurchaseModel->orderBy('id', 'DESC')->paginate(10, 'purchase');
        } else {
            $purchases              = $PurchaseModel->where('outletid', $this->data['outletPick'])->orderBy('id', 'DESC')->paginate(10, 'purchase');
        }

        $suppliers                  = $SupplierModel->findAll();
        $productlist                = $ProductModel->where('status', '1')->find();

        if (!empty($purchases)) {
            $purchasedata   = array();
            foreach ($purchases as $purchase) {
                $purchasesupplier   = $SupplierModel->find($purchase['supplierid']);
                $purchasedetails    = $PurchasedetailModel->where('purchaseid', $purchase['id'])->find();
                $purchaseoutlet     = $OutletModel->find($purchase['outletid']);
                $purchaseuser       = $UserModel->find($purchase['userid']);

                if (!empty($purchasesupplier)) {
                    $supplier = $purchasesupplier['name'];
                } else {
                    $supplier = '';
                }

                if (!empty($purchasesupplier)) {
                    $supplierid = $purchasesupplier['id'];
                } else {
                    $supplierid = '';
                }

                if (!empty($purchaseoutlet)) {
                    $outlets = $purchaseoutlet['name'];
                } else {
                    $outlets = '';
                }

                if (!empty($purchaseuser)) {
                    $users = $purchaseuser->firstname.' '.$purchaseuser->lastname;
                } else {
                    $users = '';
                }

                $purchasedata[$purchase['id']]['outlet']        = $outlets;
                $purchasedata[$purchase['id']]['supplier']      = $supplier;
                $purchasedata[$purchase['id']]['supplierid']    = $supplierid;
                $purchasedata[$purchase['id']]['user']          = $users;
                $purchasedata[$purchase['id']]['date']          = $purchase['date'];
                $purchasedata[$purchase['id']]['status']        = $purchase['status'];

                $arrayqty       = array();
                $arrayprice     = array();
                if (!empty($purchasedetails)) {
                    foreach ($purchasedetails as $purdet) {
                        $purchasevariants           = $VariantModel->find($purdet['variantid']);
    
                        if (!empty($purchasevariants)) {
                            $hargadasar             = $purchasevariants['hargadasar'];
                            $purchaseproducts       = $ProductModel->find($purchasevariants['productid']);
                            $purchaseoldstocks      = $OldStockModel->where('variantid', $purchasevariants['id'])->first();
                            $purchasestocks         = $StockModel->where('variantid', $purchasevariants['id'])->where('outletid', $purchase['outletid'])->first();
    
                            $varid      = $purchasevariants['id'];
                            $variants   = $purchasevariants['name'];
                            $sku        = $purchasevariants['sku'];
    
                            if (!empty($purchaseproducts)) {
                                $product = $purchaseproducts['name'];
                            } else {
                                $product = '';
                            }
    
                            if (!empty($purchaseoldstocks)) {
                                $hargaold = $purchaseoldstocks['hargadasar'];
                            } else {
                                $hargaold = '';
                            }
    
                            if (!empty($purchasestocks)) {
                                $qty = $purchasestocks['qty'];
                            } else {
                                $qty = '';
                            }
                        } else {
                            $varid      = '';
                            $variants   = '';
                            $product    = '';
                            $hargadasar = '';
                            $hargaold   = '';
                            $qty        = '';
                        }
    
                        $purchasedata[$purchase['id']]['detail'][$purdet['id']]['name']         = $product.' - '.$variants;
                        $purchasedata[$purchase['id']]['detail'][$purdet['id']]['sku']          = $sku;
                        $purchasedata[$purchase['id']]['detail'][$purdet['id']]['productname']  = $product;
                        $purchasedata[$purchase['id']]['detail'][$purdet['id']]['variantname']  = $variants;
                        $purchasedata[$purchase['id']]['detail'][$purdet['id']]['varid']        = $varid;
                        $purchasedata[$purchase['id']]['detail'][$purdet['id']]['hargadasar']   = $hargadasar;
                        $purchasedata[$purchase['id']]['detail'][$purdet['id']]['hargaold']     = $hargaold;
                        $purchasedata[$purchase['id']]['detail'][$purdet['id']]['qty']          = $qty;
                        $purchasedata[$purchase['id']]['detail'][$purdet['id']]['inputqty']     = $purdet['qty'];
                        $purchasedata[$purchase['id']]['detail'][$purdet['id']]['inputprice']   = $purdet['price'];
    
                        $arrayqty[]     = $purdet['qty'];
                        $arrayprice[]   = (Int)$purdet['qty'] * (Int)$purdet['price'];
                    }
                } else {
                    $purchasedata[$purchase['id']]['detail']   = array();
                }
                    
                $purchasedata[$purchase['id']]['totalqty']      = array_sum($arrayqty);
                $purchasedata[$purchase['id']]['totalprice']    = array_sum($arrayprice);
            }
        } else {
            $purchasedata   = array();
        }

        // Parsing data to view
        $data['title']              = lang('Global.purchase');
        $data['description']        = lang('Global.purchaseListDesc');
        $data['purchases']          = $purchases;
        $data['purchasedata']       = $purchasedata;
        $data['suppliers']          = $suppliers;
        $data['productlist']        = $productlist;
        $data['pager']              = $PurchaseModel->pager;

        return view ('Views/purchase', $data);
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

    // Inventory
    public function indexinventory()
    {
        // Calling Model
        $InventoryModel = new InventoryModel;
        $OutletModel    = new OutletModel;

        // Find Data
        $data           = $this->data;

        $outlets        = $OutletModel->findAll();
        if ($this->data['outletPick'] === null) {
            $inventory      = $InventoryModel->orderBy('id', 'DESC')->findAll();
        } else {
            $inventory      = $InventoryModel->where('outletid', $this->data['outletPick'])->orderBy('id', 'DESC')->find();
        }

        // Parsing data to view
        $data['title']          = lang('Global.inventoryList');
        $data['description']    = lang('Global.inventoryListDesc');
        $data['inventory']      = $inventory;
        $data['outlets']        = $outlets;

        return view ('Views/inventory', $data);
    }

    public function createinv()
    {
        // Calling Models
        $InventoryModel  = new InventoryModel;

        // initialize
        $input          = $this->request->getPost();

        // save data
        $data = [
            'name'      => $input['name'],
            'outletid'  => $input['outlet'],
            'qty'       => $input['qty'],
            
        ];
        $InventoryModel->save($data);

        return redirect()->back()->with('message', lang('Global.saved'));
    }

    public function updateinv($id) 
    {
        // calling Model
        $InventoryModel  = new InventoryModel;

        // initialize
        $input              = $this->request->getpost();

        // validation
        $data = [
            'id'            => $id,
            'name'          => $input['name'],
            'outletid'      => $input['outlet'],
            'qty'           => $input['qty'],
        ];
        $InventoryModel->save($data);

        return redirect()->back()->with('massage', lang('global.saved'));
    }

    public function deleteinv($id)
    {
        // Calling Model
        $InventoryModel  = new InventoryModel();

        $InventoryModel->delete($id);

        return redirect()->back()->with('error', lang('Global.deleted'));
    }
}