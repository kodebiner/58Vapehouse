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
        $pager      = \Config\Services::pager();

        // Calling Model
        $StockModel = new StockModel();

        // Populating Data
        $search     = $this->request->getGet('search');
        $stocks     = $StockModel->getStockList(
            $search,
            $this->data['outletPick']
        );
        $summary    = $StockModel->getStockSummary(
            $search,
            $this->data['outletPick']
        );
        $pager = $StockModel->pager;

        // Parsing data to view
        $data                   = $this->data;
        $data['title']          = lang('Global.stockList');
        $data['description']    = lang('Global.stockListDesc');
        $data['stocks']         = $stocks;
        $data['totalstock']     = $summary['totalstock'] ?? 0;
        $data['capsum']         = $summary['capsum'] ?? 0;
        $data['stockcount']     = $summary['stockcount'] ?? 0;
        $data['pager_links']    = $pager->links('default', 'front_full');;

        return view('Views/stock', $data);
    }

    // public function index()
    // {
    //     $pager      = \Config\Services::pager();
    //     $db         = \Config\Database::connect();

    //     // Calling Model
    //     $StockModel     = new StockModel;
    //     $VariantModel   = new VariantModel;
    //     $ProductModel   = new ProductModel;
    //     $OutletModel    = new OutletModel;

    //     // Populating Data
    //     $input      = $this->request->getGet();
    //     if (!empty($input['search'])) {
    //         $products   = $ProductModel->like('name', $input['search'])->find();
    //     } else {
    //         $products   = $ProductModel->findAll();
    //     }

    //     $stockdata  = [];
    //     if (!empty($products)) {
    //         foreach ($products as $product) {
    //             $variants   = $VariantModel->where('productid', $product['id'])->find();
    //             if (!empty($variants)) {
    //                 foreach ($variants as $variant) {
    //                     if ($this->data['outletPick'] === null) {
    //                         $stocks         = $StockModel->where('variantid', $variant['id'])->find();
    //                         // $stockcount     = count($StockModel->where('variantid', $variant['id'])->find());
    //                         // $totalstock     = array_sum(array_column($StockModel->where('variantid', $variant['id'])->find(), 'qty'));
    //                     } else {
    //                         $stocks         = $StockModel->where('variantid', $variant['id'])->where('outletid', $this->data['outletPick'])->find();
    //                         // $stockcount     = count($StockModel->where('variantid', $variant['id'])->where('outletid', $this->data['outletPick'])->find());
    //                         // $totalstock     = array_sum(array_column($StockModel->where('variantid', $variant['id'])->where('outletid', $this->data['outletPick'])->find(), 'qty'));
    //                     }

    //                     if (!empty($stocks)) {
    //                         foreach ($stocks as $stock) {
    //                             $outlet     = $OutletModel->find($stock['outletid']);

    //                             $stockdata[$stock['id']]['id']          = $variant['id'];
    //                             $stockdata[$stock['id']]['outlet']      = $outlet['name'];
    //                             $stockdata[$stock['id']]['name']        = $product['name'].' - '.$variant['name'];
    //                             $stockdata[$stock['id']]['qty']         = $stock['qty'];
    //                             $stockdata[$stock['id']]['price']       = $variant['hargamodal'];
    //                         }
    //                     }
    //                 }
    //             }
    //         }
    //     }
    //     array_multisort(array_column($stockdata, 'id'), SORT_DESC, $stockdata);

    //     $totalcap       = [];
    //     $totalstocks    = [];
    //     $totalproduct   = [];
    //     foreach ($stockdata as $stockdat) {
    //         $totalstocks[]  = $stockdat['qty'];
    //         $totalproduct[] = $stockdat['id'];
    //         $totalcap[]     = (int)$stockdat['qty'] * (int)$stockdat['price'];
    //     }
    //     $stockcount     = count($totalproduct);
    //     $totalstock     = array_sum($totalstocks);
    //     $capsum         = array_sum($totalcap);

    //     $page       = (int) ($this->request->getGet('page') ?? 1);
    //     $perPage    = 20;
    //     $total      = count($stockdata);

    //     // Parsing data to view
    //     $data                       = $this->data;
    //     $data['title']              = lang('Global.stockList');
    //     $data['description']        = lang('Global.stockListDesc');
    //     $data['stocks']             = array_slice($stockdata, ($page*20)-20, $page*20);
    //     $data['totalstock']         = $totalstock;
    //     $data['stockcount']         = $stockcount;
    //     $data['capsum']             = $capsum;
    //     $data['pager_links']        = $pager->makeLinks($page, $perPage, $total, 'front_full');

    //     // $db         = \Config\Database::connect();
    //     // // Find Data
    //     // $data           = $this->data;
    //     // $outlets        = $OutletModel->findAll();

    //     // if ($this->data['outletPick'] === null) {
    //     //     $stock          = $StockModel->orderBy('id', 'DESC')->paginate(20, 'stock');
    //     //     $stockcount     = count($StockModel->findAll());
    //     //     $totalstock     = array_sum(array_column($StockModel->findAll(), 'qty'));
    //     // } else {
    //     //     $stock          = $StockModel->orderBy('id', 'DESC')->where('outletid', $this->data['outletPick'])->paginate(20, 'stock');
    //     //     $stockcount     = count($StockModel->where('outletid', $this->data['outletPick'])->find());
    //     //     $totalstock     = array_sum(array_column($StockModel->where('outletid', $this->data['outletPick'])->find(), 'qty'));
    //     // }

    //     // if (!empty($stock)) {
    //     //     $variantid   = array();
    //     //     foreach ($stock as $stok) {
    //     //         $variantid[]  = $stok['variantid'];
    //     //     }
    //     //     $variants           = $VariantModel->find($variantid);

    //     //     $productid = array();
    //     //     foreach ($variants as $variant) {
    //     //         $productid[]    = $variant['productid'];
    //     //     }
    //     //     $products           = $ProductModel->find($productid);
    //     // } else {
    //     //     $products = array();
    //     //     $variants = array();
    //     // }

    //     // $totalcap = array();
    //     // $capbuilder     = $db->table('stock');
    //     // $stockcap       = $capbuilder->select('stock.qty as qty, variant.hargamodal as price');
    //     // $stockcap       = $capbuilder->join('variant', 'stock.variantid = variant.id', 'left');
    //     // if ($this->data['outletPick'] != null) {
    //     //     $stockcap       = $capbuilder->where('stock.outletid', $this->data['outletPick']);
    //     // }
    //     // $stockcap       = $capbuilder->get();
    //     // $caps           = $stockcap->getResult();
    //     // foreach ($caps as $cap) {
    //     //     $totalcap[] = (int)$cap->qty * (int)$cap->price;
    //     // }
    //     // $capsum = array_sum($totalcap);

    //     // // Parsing data to view
    //     // $data['title']          = lang('Global.stockList');
    //     // $data['description']    = lang('Global.stockListDesc');
    //     // $data['stocks']         = $stock;
    //     // $data['stockcount']     = $stockcount;
    //     // $data['variants']       = $variants;
    //     // $data['products']       = $products;
    //     // $data['outlets']        = $outlets;
    //     // $data['totalstock']     = $totalstock;
    //     // $data['capsum']         = $capsum;
    //     // $data['pager']          = $StockModel->pager;

    //     return view ('Views/stock', $data);
    // }

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
        $suppliers          = $SupplierModel->find($id);

        if ($input['name'] != $suppliers['name']) {
            $name = $input['name'];
        } else {
            $name = $suppliers['name'];
        }
        if ($input['phone'] != $suppliers['phone']) {
            $phone = $input['phone'];
        } else {
            $phone = $suppliers['phone'];
        }
        if ($input['address'] != $suppliers['address']) {
            $address = $input['address'];
        } else {
            $address = $suppliers['address'];
        }
        if (!empty($input['email'])) {
            $email = $input['email'];
        } else {
            $email = $suppliers['email'];
        }
        if (!empty($input['city'])) {
            $city = $input['city'];
        } else {
            $city = $suppliers['city'];
        }

        // validation
        $data = [
            'id'            => $id,
            'name'          => $name,
            'phone'         => $phone,
            'email'         => $email,
            'address'       => $address,
            'city'          => $city,
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
        $SupplierModel              = new SupplierModel();
        $ProductModel               = new ProductModel();

        $input                      = $this->request->getGet();
        $daterange                  = $input['daterange'] ?? date('Y-m-01') . ' - ' . date('Y-m-d');

        [$startdate, $enddate]      = explode(' - ', $daterange);
        $startdate                  = date('Y-m-d', strtotime($startdate));
        $enddate                    = date('Y-m-d', strtotime($enddate));

        $db = $this->db;
        $purchaseBuilder = $db->table('purchase');
        $purchaseBuilder->select('
            purchase.*,
            COALESCE(supplier.name, "") as supplier_name,
            COALESCE(supplier.id, "") as supplier_id,
            COALESCE(outlet.name, "") as outlet_name,
            COALESCE(CONCAT(users.firstname, " ", users.lastname), "") as user_name
        ');
        $purchaseBuilder->join('supplier', 'supplier.id = purchase.supplierid', 'left');
        $purchaseBuilder->join('outlet', 'outlet.id = purchase.outletid', 'left');
        $purchaseBuilder->join('users', 'users.id = purchase.userid', 'left');
        $purchaseBuilder->where('purchase.date >=', $startdate . ' 00:00:00');
        $purchaseBuilder->where('purchase.date <=', $enddate . ' 23:59:59');
        if ($this->data['outletPick'] !== null) {
            $purchaseBuilder->where('purchase.outletid', $this->data['outletPick']);
        }
        $purchaseBuilder->orderBy('purchase.date', 'DESC');

        $purchases = $purchaseBuilder->get()->getResultArray();

        $suppliers                  = $SupplierModel->findAll();
        $productlist                = $ProductModel->where('status', '1')->find();

        $purchasedata = array();
        if (!empty($purchases)) {
            $purchaseIds = array_column($purchases, 'id');

            $detailBuilder = $db->table('purchasedetail');
            $detailBuilder->select('
                purchasedetail.*,
                COALESCE(variant.id, "") as var_id,
                COALESCE(variant.name, "") as variant_name,
                COALESCE(variant.sku, "") as sku,
                COALESCE(variant.hargadasar, "") as hargadasar,
                COALESCE(product.name, "") as product_name,
                COALESCE(oldstock.hargadasar, "") as hargaold,
                COALESCE(stock.qty, "") as stock_qty
            ');
            $detailBuilder->join('purchase', 'purchase.id = purchasedetail.purchaseid');
            $detailBuilder->join('variant', 'variant.id = purchasedetail.variantid', 'left');
            $detailBuilder->join('product', 'product.id = variant.productid', 'left');
            $detailBuilder->join('oldstock', 'oldstock.variantid = variant.id', 'left');
            $detailBuilder->join('stock', 'stock.variantid = variant.id AND stock.outletid = purchase.outletid', 'left');

            $allDetails = array();
            $detailBuilder->whereIn('purchasedetail.purchaseid', $purchaseIds);
            $allDetails = $detailBuilder->get()->getResultArray();

            $detailsByPurchase = array();
            foreach ($allDetails as $det) {
                $detailsByPurchase[$det['purchaseid']][] = $det;
            }

            foreach ($purchases as $purchase) {
                $pid = $purchase['id'];
                $purchasedata[$pid]['outlet']        = $purchase['outlet_name'];
                $purchasedata[$pid]['supplier']      = $purchase['supplier_name'];
                $purchasedata[$pid]['supplierid']    = $purchase['supplier_id'];
                $purchasedata[$pid]['user']          = $purchase['user_name'];
                $purchasedata[$pid]['date']          = $purchase['date'];
                $purchasedata[$pid]['status']        = $purchase['status'];

                $pdetails = $detailsByPurchase[$pid] ?? array();

                $arrayqty       = array();
                $arrayprice     = array();
                if (!empty($pdetails)) {
                    foreach ($pdetails as $purdet) {
                        $detid = $purdet['id'];
                        $prod  = $purdet['product_name'];
                        $vname = $purdet['variant_name'];

                        $purchasedata[$pid]['detail'][$detid]['name']         = ($prod ? $prod . ' - ' : '') . $vname;
                        $purchasedata[$pid]['detail'][$detid]['sku']          = $purdet['sku'];
                        $purchasedata[$pid]['detail'][$detid]['productname']  = $prod;
                        $purchasedata[$pid]['detail'][$detid]['variantname']  = $vname;
                        $purchasedata[$pid]['detail'][$detid]['varid']        = $purdet['var_id'];
                        $purchasedata[$pid]['detail'][$detid]['hargadasar']   = $purdet['hargadasar'];
                        $purchasedata[$pid]['detail'][$detid]['hargaold']     = $purdet['hargaold'];
                        $purchasedata[$pid]['detail'][$detid]['qty']          = $purdet['stock_qty'];
                        $purchasedata[$pid]['detail'][$detid]['inputqty']     = $purdet['qty'];
                        $purchasedata[$pid]['detail'][$detid]['inputprice']   = $purdet['price'];

                        $arrayqty[]     = $purdet['qty'];
                        $arrayprice[]   = (Int)$purdet['qty'] * (Int)$purdet['price'];
                    }
                } else {
                    $purchasedata[$pid]['detail'] = array();
                }

                $purchasedata[$pid]['totalqty']      = array_sum($arrayqty);
                $purchasedata[$pid]['totalprice']    = array_sum($arrayprice);
            }
        }

        $data                       = $this->data;
        $data['title']              = lang('Global.purchase');
        $data['description']        = lang('Global.purchaseListDesc');
        $data['purchases']          = $purchases;
        $data['purchasedata']       = $purchasedata;
        $data['suppliers']          = $suppliers;
        $data['productlist']        = $productlist;
        $data['startdate']          = strtotime($startdate);
        $data['enddate']            = strtotime($enddate);

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

        $purchase   = $PurchaseModel->find($id);

        foreach ($input['ctotalpcs'][$id] as $key => $value) {
            // Update Purchase Detail
            $purdet = $PurchasedetailModel->where('purchaseid', $id)->where('variantid', $key)->first();
            $purdetdata = [
                'id'                => $purdet['id'],
                'qty'               => $value,
                'price'             => $input['cbprice'][$id][$key]
            ];
            if ($purdetdata['qty'] === "0") {
                $PurchasedetailModel->delete($purdet['id']);
            }

            // Save Data Purchase Detail
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
            $stock = $StockModel->where('variantid', $key)->where('outletid', $purchase['outletid'])->first();
            $stockdata = [
                'id'                => $stock['id'],
                'qty'               => $stock['qty'] + $value,
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

    public function printpur($id)
    {
        $PurchaseModel              = new PurchaseModel();
        $PurchasedetailModel        = new PurchasedetailModel();
        $ProductModel               = new ProductModel();
        $VariantModel               = new VariantModel();
        $OutletModel                = new OutletModel();
        $SupplierModel              = new SupplierModel();
        $UserModel                  = new UserModel();

        $purchase                   = $PurchaseModel->find($id);

        $purchasedata               = array();
        if (!empty($purchase)) {
            $purchasedetails        = $PurchasedetailModel->where('purchaseid', $purchase['id'])->find();
            $purchaseoutlet         = $OutletModel->find($purchase['outletid']);
            $purchasesupplier       = $SupplierModel->find($purchase['supplierid']);
            $purchaseuser           = $UserModel->find($purchase['userid']);

            if (!empty($purchaseoutlet)) {
                $outlet     = $purchaseoutlet['name'];
                $address    = $purchaseoutlet['address'];
                $phone      = $purchaseoutlet['phone'];
            } else {
                $outlet     = '';
                $address    = '';
                $phone      = '';
            }

            if (!empty($purchasesupplier)) {
                $supplier   = $purchasesupplier['name'];
            } else {
                $supplier   = '';
            }

            if (!empty($purchaseuser)) {
                $user       = $purchaseuser->firstname.' '.$purchaseuser->lastname;
            } else {
                $user       = '';
            }

            $purchasedata['id']         = $purchase['id'];
            $purchasedata['outlet']     = $outlet;
            $purchasedata['address']    = $address;
            $purchasedata['phone']      = $phone;
            $purchasedata['supplier']   = $supplier;
            $purchasedata['user']       = $user;
            $purchasedata['date']       = $purchase['date'];
            $purchasedata['status']     = $purchase['status'];

            $arrayqty       = array();
            $arrayprice     = array();
            if (!empty($purchasedetails)) {
                foreach ($purchasedetails as $purdet) {
                    $purchasevariants       = $VariantModel->find($purdet['variantid']);

                    if (!empty($purchasevariants)) {
                        $purchaseproducts   = $ProductModel->find($purchasevariants['productid']);

                        if (!empty($purchaseproducts)) {
                            $product = $purchaseproducts['name'];
                        } else {
                            $product = '';
                        }

                        $variants   = $purchasevariants['name'];
                        $sku        = $purchasevariants['sku'];
                    } else {
                        $variants   = '';
                        $sku        = '';
                        $product    = '';
                    }

                    $purchasedata['detail'][$purdet['id']]['name']         = $product.' - '.$variants;
                    $purchasedata['detail'][$purdet['id']]['productname']  = $product;
                    $purchasedata['detail'][$purdet['id']]['variantname']  = $variants;
                    $purchasedata['detail'][$purdet['id']]['sku']          = $sku;
                    $purchasedata['detail'][$purdet['id']]['qty']          = $purdet['qty'];
                    $purchasedata['detail'][$purdet['id']]['price']        = $purdet['price'];

                    $arrayqty[]     = $purdet['qty'];
                    $arrayprice[]   = (Int)$purdet['qty'] * (Int)$purdet['price'];
                }
            } else {
                $purchasedata['detail'] = array();
            }

            $purchasedata['totalqty']   = array_sum($arrayqty);
            $purchasedata['totalprice'] = array_sum($arrayprice);
        }

        $data['purchasedata']   = $purchasedata;

        $mpdf   = new \Mpdf\Mpdf([
            'default_font_size' => 7,
        ]);
        $mpdf->Image('./img/logo.png', 80, 0, 210, 297, 'png', '', true, false);
        $mpdf->showImageErrors = true;
        $mpdf->AddPage("P", "", "", "", "", "15", "15", "2", "15", "", "", "", "", "", "", "", "", "", "", "", "A4-P");

        $date       = date_create($purchasedata['date']);
        $filename   = "PO" . date_format($date, 'Ymd') . $purchasedata['id'] . ".pdf";
        $html       = view('Views/purchaseprint', $data);
        $mpdf->WriteHTML($html);

        ob_clean();
        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="'.$filename.'"')
            ->setBody($mpdf->Output($filename, 'S'));
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