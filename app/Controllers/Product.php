<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\ProductModel;
use App\Models\BrandModel;
use App\Models\CashModel;
use App\Models\CategoryModel;
use App\Models\VariantModel;
use App\Models\StockModel;
use App\Models\OldStockModel;
use App\Models\OutletModel;
use App\Models\GroupUserModel;
use Myth\Auth\Models\GroupModel;

class Product extends BaseController
{

    protected $db, $builder;
    protected $auth;
    protected $config;

    public function __construct()
    {
        $this->db       = \Config\Database::connect();
        $validation     = \Config\Services::validation();
        $this->builder  =   $this->db->table('product');
        $this->builder  =   $this->db->table('category');
        $this->builder  =   $this->db->table('cash');
        $this->builder  =   $this->db->table('stock');
        $this->config   = config('Auth');
        $this->auth     = service('authentication');
    }

    public function index()
    {
        // Calling Model        
        $GroupModel     = new GroupModel();
        $CategoryModel  = new CategoryModel();
        $ProductModel   = new ProductModel();
        $BrandModel     = new BrandModel();
        $VariantModel   = new VariantModel();
        $StockModel     = new StockModel();

        // Populating Data
        $products   = $ProductModel->orderBy('id', 'DESC')->findAll(); // Hapus kalau sudah implementasi filter
        $category   = $CategoryModel->findAll();
        $brand      = $BrandModel->findAll();
        $variant    = $VariantModel->findAll();
        if ($this->data['outletPick'] === null) {
            $stock      = $StockModel->findAll();
        } else {
            $stock      = $StockModel->where('outletid', $this->data['outletPick'])->find();
        }

        // Checking filter
        $input = $this->request->getPost();
        // if (!empty($input)) {
        //     if (!empty($input['brand'])) {
        //         $products = $ProductModel->where('catid', $input['category'])->find();
        //     }
            
        // } else {
        //     $products   = $ProductModel->findAll();
        // }

        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.product');
        $data['description']    = lang('Global.productDesc');
        $data['roles']          = $GroupModel->findAll();
        $data['products']       = $products;
        $data['category']       = $category;
        $data['brand']          = $brand;
        $data['variants']       = $variant;
        $data['stocks']         = $stock;

        return view('Views/product', $data);
    }

    public function create()
    {  
            // calling Model
            $ProductModel = new ProductModel();
            $StockModel = new StockModel();
            $TotalStockModel = new TotalStockModel();
            $VariantModel = new VariantModel();
            $OutletModel = new OutletModel();

            // search all data
            $input = $this->request->getPost();
            $outlets = $OutletModel->findAll();
    
            
            // rules
            $rule = [
                'name'          => 'required|max_length[255]|is_unique[product.name]',
                'description'   => 'required|max_length[255]',
                'category'      => 'required',
                'brand'         => 'required',
            ];
            
            // Validation
            if (! $this->validate($rule)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }
            
            // get data
            $data = [
                'name'          => $input['name'],
                'description'   => $input['description'],
                'catid'         => $input['category'],
                'brandid'       => $input['brand'],
            ];

            // insert data product
            $ProductModel->insert($data);

            // Get inserted Product ID
            $productId = $ProductModel->getInsertID();
            
            // insert variant
            foreach ($input['varBase'] as $baseKey => $baseValue) {
                $variant['productid'] = $productId;
                $variant['hargadasar'] = $baseValue;

                foreach ($input['varName'] as $nameKey => $nameValue) {
                    if($nameKey === $baseKey) { $variant['name'] = $nameValue; }
                }

                foreach ($input['varCap'] as $capKey => $capValue) {
                    if($capKey === $baseKey) { $variant['hargamodal'] = $capValue; }
                }

                foreach ($input['varMargin'] as $marginKey => $marginValue) {
                    if($marginKey === $baseKey) { $variant['hargajual'] = $marginValue; }
                }

                $VariantModel->insert($variant);

                // Getting variant ID
                $variantid = $VariantModel->getInsertID();

                // Update stock
                foreach ($outlets as $outlet) {
                    $stock = [
                        'outletid'  => $outlet['id'],
                        'variantid' => $variantid,
                        'qty'       => '0'
                    ];
                    $StockModel->insert($stock);
                }

                // Create Total Stock
                $totalStock = [
                    'variantid'     => $variantid,
                    'hargadasar'    => $baseValue,
                    'hargamodal'    => $capValue,
                    'qty'           => '0',
                ];
                $TotalStockModel->insert($totalStock);
            }

            return redirect()->back()->with('message', lang('Global.saved'));
    }

    public function indexvar($id)
    {

        // Calling Model        
        $GroupModel     = new GroupModel();
        $CategoryModel  = new CategoryModel();
        $ProductModel   = new ProductModel();
        $BrandModel     = new BrandModel();
        $VariantModel   = new VariantModel();
        $StockModel     = new StockModel();

        // Populating Data
        $category   = $CategoryModel->findAll();
        $brand      = $BrandModel->findAll();
        $stock      = $StockModel->findAll(); 
        $variant    = $VariantModel->where('productid', $id)->find();

        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.product');
        $data['description']    = lang('Global.productDesc');
        $data['roles']          = $GroupModel->findAll();
        $data['stock']          = $stock;
        $data['products']       = $data;
        $data['category']       = $category;
        $data['brand']          = $brand;
        $data['variants']       = $variant;
        $data['products']       = $ProductModel->find($id);

        return view('Views/variant', $data);
    }

    public function createvar($id)
    {
        // calling Model
        $ProductModel    = new ProductModel();
        $VariantModel    = new VariantModel();
        $OutletModel     = new OutletModel();
        $StockModel      = new StockModel();
        $OldStockModel   = new OldStockModel();

        // search all data
        $products    = $ProductModel->where('id',$id)->first();
        $outlets     = $OutletModel->findAll();
        $input       = $this->request->getPost();

            //populating data
            $data = $this->data;
            $data['products'] = $products;
        
        // rules
        $rule = [
            'name'         => 'required|max_length[255]',
            'hargadasar'   => 'required|max_length[255]',
            'hargamodal'   => 'required',
            'margin'       => 'required',
        ];
        
        // Validation
        if (! $this->validate($rule)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // get data
        $data = [
            'productid'    => $id, 
            'name'         => $input['name'],
            'hargadasar'   => $input['hargadasar'],
            'hargamodal'   => $input['hargamodal'],
            'hargajual'    => $input['margin'],
        ];

        // insert data product
        $VariantModel->insert($data);
        
        $variantid = $VariantModel->getInsertID();

        foreach ($outlets as $outlet) {
            $stocks = [
                'outletid'  => $outlet['id'],
                'variantid' => $variantid,
                'qty'       => '0',

            ];
            $StockModel->insert($stocks);                
        }

        $oldstock = [
            'variantid'     => $variantid,
            'hargamodal'    => '0',
            'hargadasar'    => '0'
        ];
        $OldStockModel->insert($oldstock);
        
        return redirect()->back()->with('message', lang('Global.saved'));
    }

    public function update($id)
    {
        // calling Model
        $ProductModel   = new ProductModel();
        $StockModel     = new StockModel();
        $VariantModel   = new VariantModel();
        $OutletModel    = new OutletModel();

        // inisialize
        $input = $this->request->getPost();
        
        // search id
        $data['products'] = $ProductModel->where('id', $id)->first();

        // rules validation
        $rule = [
            'name'          => 'required|max_length[255]|is_unique[product.name]',
            'description'   => 'required|max_length[255]',
            'category'      => 'required',
            'brand'         => 'required',
        ];

        // Validation
        if (! $this->validate($rule)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // get data
        $data = [
            'id'            => $id,
            'name'          => $input['name'],
            'hargadasar'    => $input['hargadasar'],
            'hargamodal'    => $input['hargamodal'],
            'margin'        => $input['margin'],
        ];

        // insert data product
        $ProductModel->save($data);

        //get product id
        $productId = $ProductModel->getInsertID();

        //update variant
        foreach ($input['varBase'] as $baseKey => $baseValue){
            $variant['productid']   = $productId;
            $variant['hargadasar']  = $baseValue;
        }
        
        // redirect back
        return redirect()->back()->with('message', lang('Global.saved'));
    }

    public function editvar($id){

        // calling model
        $ProductModel   = new ProductModel();
        $StockModel     = new StockModel();
        $VariantModel   = new VariantModel();

        // initialize
        $data['variants']        = $VariantModel->where('productid',$id)->first();
        $products                = $ProductModel->findAll();
        $stocks                  = $StockModel->where('variantid',$id);
        $input                   = $this->request->getPost();

        //update variant
        foreach ( $products as $product) {
        $variants =  [
                'id'            => $id,
                'name'          => $input['name'],
                'hargadasar'    => $input['hargadasar'],
                'hargamodal'    => $input['hargamodal'],
                'hargajual'     => $input['margin'],
                ];
            }

        // saving variant
        $VariantModel->save($variants); 

        // get variant id
        $variantid = $VariantModel->getInsertID();

        // saving stocks
        foreach ( $stocks as $stock) {
            $var =  [
                'variantid' => $variantid,
                'qty'       => '0',
            ];
            $StockModel->save($var); 
        }

        // redirect back
        return redirect()->back()->with('message', lang('Global.saved'));
    }
  

    public function deletevar($id)
    {

         // calling Model
         $StockModel = new StockModel();
         $VariantModel = new VariantModel();
 
         // Populating & Removing Variants Data
         $stocks = $StockModel->where('variantid', $id)->find();
         foreach ($stocks as $stock) {
            $StockModel->delete($stock['id']);
         }
         $VariantModel->delete($id);

         return redirect()->back()->with('error', lang('Global.deleted'));
    }

    public function delete($id)
    {
        // calling Model
        $ProductModel = new ProductModel();
        $StockModel = new StockModel();
        $VariantModel = new VariantModel();

        // Populating & Removing Variants Data
        $variants = $VariantModel->where('productid', $id)->find();
        foreach ($variants as $varian) {
            // Removing Stocks
            $stocks = $StockModel->where('variantid', $varian['id'])->find();
            foreach ($stocks as $stock) {
                $StockModel->delete($stock['id']);
            }

            // Removing Variant
            $VariantModel->delete($varian['id']);
        }

        // Removing Product Data
        $ProductModel->delete($id);

        return redirect()->back()->with('error', lang('Global.deleted'));
    }

    public function createcat()
    {
        // Calling Models
        $CategoryModel = new CategoryModel();
        $input = $this->request->getPost();
        
        // create categoroy
        $data = [
            'name' => $input['name'],
        ];
        $CategoryModel->insert($data);
        return redirect()->back()->with('message', lang('Global.saved'));
    }

    public function editcat($id) 
    {
        // parsing data
        $CategoryModel = new CategoryModel();
        $data['category'] = $CategoryModel->where('id', $id)->first();

        // inizialise
        $input = $this->request->getPost();

        // get data
        $data = [ 
            'id' => $id,
            'name' => $input['name'],
        ];
        
        // update data
        $CategoryModel->save($data);

        // return
        return redirect()->to('product'); 
    }

    public function deletecat ($id)
    {
        // parsing data
        $CategoryModel = new CategoryModel();
        $data['category'] = $CategoryModel->where('id', $id)->first();

        // delete data
        $CategoryModel->delete($id);

        // return
        return redirect()->back()->with('error', lang('Global.deleted'));
    }

    public function createbrand()
    {
        // Calling Models
        $BrandModel = new BrandModel();
        $input = $this->request->getPost();
        
        // create brand
        $data = [
            'name' => $input['name'],
        ];
        $BrandModel->insert($data);
        return redirect()->back()->with('message', lang('Global.saved'));
    }

    public function editbrand($id)
    {
        // parsing data
        $BrandModel = new BrandModel();
        $data['brand'] = $BrandModel->where('id', $id)->first();

        // inizialise
        $input = $this->request->getPost();

        // get data
        $data = [ 
            'id' => $id,
            'name' => $input['name'],
        ];
        
        // update data
        $BrandModel->save($data);

        // return
        return redirect()->to('product'); 
    }

    public function deletebrand($id)
    {
        // parsing data
        $BrandModel = new BrandModel();
        $data['brand'] = $BrandModel->where('id', $id)->first();

        // delete data
        $BrandModel->delete($id);

        // return
        return redirect()->back()->with('error', lang('Global.deleted'));
    }
}