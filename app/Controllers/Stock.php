<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\OutletModel;
use App\Models\StockModel;
use App\Models\OldStockModel;
use App\Models\VariantModel;
use App\Models\SupplierModel;
use App\Models\UserModel;
use App\Models\PurchaseModel;
use App\Models\PurchasedetailModel;

class Stock extends BaseController

{
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
        // Calling Model
        $StockModel     = new StockModel;
        $VariantModel   = new VariantModel;
        $ProductModel   = new ProductModel;
        $OutletModel    = new OutletModel;
        
        // Find Data
        $data           = $this->data;
        $products       = $ProductModel->findAll();
        $outlets        = $OutletModel->findAll();
        $variants       = $VariantModel->findAll();

        if ($this->data['outletPick'] === null) {
            $stock      = $StockModel->orderBy('id', 'DESC')->findAll();
        } else {
            $stock      = $StockModel->orderBy('id', 'DESC')->where('outletid', $this->data['outletPick'])->find();
        }

        // Parsing data to view
        $data['title']          = lang('Global.stockList');
        $data['description']    = lang('Global.stockListDesc');
        $data['stocks']         = $stock;
        $data['variants']       = $variants;
        $data['products']       = $products;
        $data['outlets']        = $outlets;

        return view ('Views/stock', $data);
    }

    // Restock
    public function restock()
    {
        // Calling Model
        $StockModel     = new StockModel;
        $VariantModel   = new VariantModel;
        $OldStockModel  = new OldStockModel;

        // initialize
        $input = $this->request->getPost();

        // Finding Total Stock
        $Stocks = $StockModel->where('variantid', $input['variant'])->find();
        $totalstock = 0;
        foreach ($Stocks as $stock) {
            $totalstock += $stock['qty'];
        }

        // Finding new price
        $variant    = $VariantModel->find($input['variant']);
        $hargadasar = (($variant['hargadasar']*$totalstock)+($input['hargadasar']*$input['qty']))/($totalstock+$input['qty']);
        $hargamodal = (($variant['hargamodal']*$totalstock)+($input['hargamodal']*$input['qty']))/($totalstock+$input['qty']);

        // Update Old Variant Price
        $oldstock = $OldStockModel->where('variantid', $variant['id'])->first();
        $updateoldstock = [
            'id'            => $oldstock['id'],
            'hargadasar'    => $variant['hargadasar'],
            'hargamodal'    => $variant['hargamodal']
        ];
        $OldStockModel->save($updateoldstock);

        // Updating variant
        $var        = [
            'id'            => $input['variant'],
            'hargadasar'    => $hargadasar,
            'hargamodal'    => $hargamodal,
            
        ];  
        $VariantModel->save($var);

        // date time stamp
        $date=date_create();
        $tanggal = date_format($date,'Y-m-d H:i:s');

        $stocks = $StockModel->where('variantid',$input['variant'])->where('outletid',$input['outlet'])->first();
        $stk = [
            'id'         => $stocks['id'],
            'qty'        => $input['qty'],
            'restock'    => $tanggal,
        ];

        // Save Data Stok
        $StockModel->save($stk);

        // return
        return redirect()->back()->with('message', lang('Global.saved'));
    }

    // Stock Cycle
    public function stockcycle() {

        // Calling Model
        $StockModel     = new StockModel;
        $VariantModel   = new VariantModel;

        // Find Data
        $data           = $this->data;
        $variants       = $VariantModel->findAll();
        
        if ($this->data['outletPick'] === null) {
            $stock      = $StockModel->orderBy('id', 'DESC')->findAll();
        } else {
            $stock      = $StockModel->orderBy('id', 'DESC')->where('outletid', $this->data['outletPick'])->find();
        }

        // Parsing data to view
        $data['title']          = lang('Global.stockCycle');
        $data['description']    = lang('Global.stockCycleDesc');
        $data['stocks']         = $stock;
        $data['variants']       = $variants;


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

    // Purchase Stock
    public function indexpurchase()
    {
        // Calling Model
        $SupplierModel              = new SupplierModel;
        $ProductModel               = new ProductModel;
        $VariantModel               = new VariantModel;
        $OutletModel                = new OutletModel;
        $UserModel                  = new UserModel;
        $PurchaseModel              = new PurchaseModel;
        $PurchasedetailModel        = new PurchasedetailModel;

        // Find Data
        $data                       = $this->data;
        $suppliers                  = $SupplierModel->findAll();
        $products                   = $ProductModel->findAll();
        $variants                   = $VariantModel->findAll();
        $outlets                    = $OutletModel->findAll();
        $users                      = $UserModel->findAll();
        $purchasedetails            = $PurchasedetailModel->findAll();

        // get outlet
        if ($this->data['outletPick'] === null) {
            $purchases              = $PurchaseModel->orderBy('id', 'DESC')->findAll();
        } else {
            $out                    = $this->data['outletPick'];
            $purchases              = $PurchaseModel->where("outletid = {$out} OR outletid='0'")->orderBy('outletid', 'ASC')->find();
        }

        // Parsing data to view
        $data['title']              = lang('Global.purchase');
        $data['description']        = lang('Global.purchaseListDesc');
        $data['purchases']          = $purchases;
        $data['purchasedetails']    = $purchasedetails;
        $data['suppliers']          = $suppliers;
        $data['products']           = $products;
        $data['variants']           = $variants;
        $data['outlets']            = $outlets;
        $data['users']              = $users;

        return view ('Views/purchase', $data);
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
}
