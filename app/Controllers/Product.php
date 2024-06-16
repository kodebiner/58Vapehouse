<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\ProductModel;
use App\Models\BrandModel;
use App\Models\CashModel;
use App\Models\CategoryModel;
use App\Models\VariantModel;
use App\Models\BundleModel;
use App\Models\BundledetailModel;
use App\Models\StockModel;
use App\Models\OldStockModel;
use App\Models\OutletModel;
use App\Models\GroupUserModel;
use App\Models\PromoModel;
use Myth\Auth\Models\GroupModel;

class Product extends BaseController
{
    protected $data;
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
        $db         = \Config\Database::connect();
        $pager      = \Config\Services::pager();

        // Calling Model        
        $GroupModel     = new GroupModel();
        $CategoryModel  = new CategoryModel();
        $ProductModel   = new ProductModel();
        $BrandModel     = new BrandModel();
        $VariantModel   = new VariantModel();
        $StockModel     = new StockModel();

        // Populating Data
        $input      = $this->request->getGet();
        if (!empty($input['search']) && empty($input['category'])) {
            $products   = $ProductModel->like('name', $input['search'])->orderBy('id', 'DESC')->paginate(20, 'product');
        } elseif (empty($input['search']) && !empty($input['category'])) {
            $products   = $ProductModel->where('catid', $input['category'])->orderBy('id', 'DESC')->paginate(20, 'product');
        } elseif (!empty($input['search']) && !empty($input['category'])) {
            $products   = $ProductModel->where('catid', $input['category'])->like('name', $input['search'])->orderBy('id', 'DESC')->paginate(20, 'product');
        } else {
            $products   = $ProductModel->orderBy('id', 'DESC')->paginate(20, 'product');
        }

        $productcount = count($ProductModel->findAll());
        $productid  = array();
        foreach ($products as $product) {
            $productid[] = $product['id'];
        }
        $category   = $CategoryModel->findAll();
        $brand      = $BrandModel->findAll();
        if (!empty($productid)) {
            $variant    = $VariantModel->whereIn('productid', $productid)->find();
        } else {
            $variant = array();
        }
        $variantid  = array();
        foreach ($variant as $var) {
            $variantid[] = $var['id'];
        }

        $productchart = $ProductModel->findAll();
        $variantchart = $VariantModel->findAll();

        $totalcap       = array();
        $totalbase      = array();
        $capbuilder     = $db->table('stock');
        $stockcap       = $capbuilder->select('stock.qty as qty, variant.hargamodal as price, variant.hargadasar as baseprice');
        $stockcap       = $capbuilder->join('variant', 'stock.variantid = variant.id', 'left');
        if ($this->data['outletPick'] != null) {
            $stockcap       = $capbuilder->where('stock.outletid', $this->data['outletPick']);
        }
        $stockcap       = $capbuilder->get();
        $caps           = $stockcap->getResult();
        foreach ($caps as $cap) {
            $totalcap[]     = (int)$cap->qty * (int)$cap->price;
            $totalbase[]    = (int)$cap->qty * (int)$cap->baseprice;
        }

        $categories = $CategoryModel->findAll();
        $stockchart = array();
        foreach ($categories as $cat) {
            $stockbuilder       = $db->table('stock');
            $stockchartbuilder  = $stockbuilder->select('stock.qty as qty, variant.hargamodal as price, variant.hargadasar as bprice');
            $stockchartbuilder  = $stockbuilder->join('variant', 'stock.variantid = variant.id', 'left');
            $stockchartbuilder  = $stockbuilder->join('product', 'variant.productid = product.id', 'left');
            $stockchartbuilder  = $stockbuilder->where('product.catid', $cat['id']);
            if ($this->data['outletPick'] != null) {
                $stockchartbuilder  = $stockbuilder->where('stock.outletid', $this->data['outletPick']);
            }
            $stockchartbuilder = $stockbuilder->get();
            $stockqty = $stockchartbuilder->getResult();
            $stkqty = array();
            $stkbp  = array();
            foreach ($stockqty as $qty) {
                $stkqty[]   = (int)$qty->qty * (int)$qty->price;
                $stkbp[]    = (int)$qty->qty * (int)$qty->bprice;
            }
            $stockchart[] = [
                'name'  => $cat['name'],
                'qty'   => array_sum($stkqty),
                'bqty'  => array_sum($stkbp)
            ];
        }

        $percentage = array_sum(array_column($stockchart, 'qty'));

        $presentase = array();
        foreach ($stockchart as $srkchrt) {
            $presentase[] = [
                'name'      => $srkchrt['name'],
                'qty'       => $srkchrt['qty'],
                'bqty'      => $srkchrt['bqty'],
                'persen'    => ceil($srkchrt['qty'] / $percentage * 100),
            ];
        }

        if ($this->data['outletPick'] === null) {
            if (!empty($variantid)) {
                $stock      = $StockModel->whereIn('variantid', $variantid)->find();
            } else {
                $stock = array();
            }
            $stockcount = array_sum(array_column($StockModel->findAll(), 'qty'));
        } else {
            if (!empty($variantid)) {
                $stock      = $StockModel->whereIn('variantid', $variantid)->where('outletid', $this->data['outletPick'])->find();
            } else {
                $stock = array();
            }
            $stockcount = array_sum(array_column($StockModel->where('outletid', $this->data['outletPick'])->find(), 'qty'));
        }

        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.productList');
        $data['description']    = lang('Global.productListDesc');
        $data['roles']          = $GroupModel->findAll();
        $data['products']       = $products;
        $data['category']       = $category;
        $data['brand']          = $brand;
        $data['variants']       = $variant;
        $data['stocks']         = $stock;
        $data['pager']          = $ProductModel->pager;
        $data['productcount']   = $productcount;
        $data['stockcount']     = $stockcount;
        $data['totalcap']       = array_sum($totalcap);
        $data['totalbase']      = array_sum($totalbase);
        $data['stockchart']     = $stockchart;
        $data['presentase']     = $presentase;
        $data['catlist']        = json_encode($presentase);
        $data['countcat']       = count($presentase);
        if (!empty($input)) {
            $data['input']     = $input;
        }

        return view('Views/product', $data);
    }

    public function export()
    {
        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=Products.xls");

        $ProductModel   = new ProductModel();
        $VariantModel   = new VariantModel();
        $CategoryModel  = new CategoryModel();

        $variants = $VariantModel->findAll();
        $products = $ProductModel->findAll();
        $categories = $CategoryModel->findall();

        echo '<table>';
        echo '<thead>';
        echo '<tr>';
        echo '<th>Nama</th>';
        echo '<th>Kategori</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        foreach ($products as $product) {
            echo '<tr>';
            echo '<td>' . $product['name'] . '</td>';
            foreach ($categories as $category) {
                if ($category['id'] === $product['catid']) {
                    echo '<td>' . $category['name'] . '</td>';
                }
            }
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
    }

    public function create()
    {
        // calling Model
        $ProductModel = new ProductModel();
        $StockModel = new StockModel();
        $OldStockModel = new OldStockModel();
        $VariantModel = new VariantModel();
        $OutletModel = new OutletModel();

        // search all data
        $input = $this->request->getPost();
        $outlets = $OutletModel->findAll();

        // rules
        $rule = [
            'name'          => 'required|max_length[255]|is_unique[product.name]',
            'description'   => 'max_length[255]',
            'category'      => 'required',
            'brand'         => 'required',
        ];

        // Validation
        if (!$this->validate($rule)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // get data
        $data = [
            'name'          => $input['name'],
            'description'   => $input['description'],
            'catid'         => $input['catid'],
            'brandid'       => $input['brandid'],
        ];
        if (isset($input['photo'])) {
            $data['photo'] = $input['photo'];
            $data['thumbnail'] = $input['thumbnail'];
        }

        // insert data product
        $ProductModel->insert($data);

        // Get inserted Product ID
        $productId = $ProductModel->getInsertID();

        // insert variant
        foreach ($input['varBase'] as $baseKey => $baseValue) {
            $variant['productid'] = $productId;
            $variant['hargadasar'] = $baseValue;

            foreach ($input['varName'] as $nameKey => $nameValue) {
                if ($nameKey === $baseKey) {
                    $variant['name'] = $nameValue;
                }
            }

            foreach ($input['varCap'] as $capKey => $capValue) {
                if ($capKey === $baseKey) {
                    $variant['hargamodal'] = $capValue;
                }
            }

            foreach ($input['varSug'] as $SugKey => $SugValue) {
                if ($SugKey === $baseKey) {
                    $variant['hargarekomendasi'] = $SugValue;
                }
            }

            foreach ($input['varMargin'] as $marginKey => $marginValue) {
                if ($marginKey === $baseKey) {
                    $variant['hargajual'] = $marginValue;
                }
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
            ];
            $OldStockModel->insert($totalStock);
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
        $variant    = $VariantModel->where('productid', $id)->find();
        if ($this->data['outletPick'] === null) {
            $stock      = $StockModel->findAll();
        } else {
            $stock      = $StockModel->where('outletid', $this->data['outletPick'])->find();
        }

        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.variantList');
        $data['description']    = lang('Global.variantListDesc');
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
        $products    = $ProductModel->where('id', $id)->first();
        $outlets     = $OutletModel->findAll();
        $input       = $this->request->getPost();

        //populating data
        $data = $this->data;
        $data['products'] = $products;

        // rules
        $rule = [
            'name'                  => 'required|max_length[255]',
            'hargadasar'            => 'required|max_length[255]',
            'hargamodal'            => 'required',
            'hargarekomendasi'      => 'required',
            'margin'                => 'required',
        ];

        // Validation
        if (!$this->validate($rule)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // get data
        $data = [
            'productid'             => $id,
            'name'                  => $input['name'],
            'hargadasar'            => $input['hargadasar'],
            'hargamodal'            => $input['hargamodal'],
            'hargarekomendasi'      => $input['hargarekomendasi'],
            'hargajual'             => $input['margin'],
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
            'variantid'             => $variantid,
            'hargamodal'            => '0',
            'hargarekomendasi'      => '0',
            'hargadasar'            => '0'
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
            'name'          => 'required|max_length[255]',
            'description'   => 'max_length[255]',
            //'category'      => 'required',
            // 'brand'         => 'required',
        ];

        // Validation
        if (!$this->validate($rule)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // get data
        if (!empty($input['status'])) {
            $data = [
                'id'            => $id,
                'name'          => $input['name'],
                'description'   => $input['description'],
                'brandid'       => $input['brandid' . $id],
                'catid'         => $input['catid' . $id],
                'status'        => $input['status'],
            ];
        } else {
            $data = [
                'id'            => $id,
                'name'          => $input['name'],
                'description'   => $input['description'],
                'brandid'       => $input['brandid' . $id],
                'catid'         => $input['catid' . $id],
                'status'        => 0,
            ];
        }

        // insert data product
        $ProductModel->save($data);

        // redirect back
        return redirect()->back()->with('message', lang('Global.saved'));
    }

    public function editvar($id)
    {

        // calling model
        $OldStockModel  = new OldStockModel();
        $VariantModel   = new VariantModel();

        // initialize
        $oldstocks                  = $OldStockModel->where('variantid', $id)->first();

        $input                      = $this->request->getPost();

        //update variant
        $variants =  [
            'id'                    => $id,
            'name'                  => $input['name'],
            'hargadasar'            => $input['hargadasar'],
            'hargamodal'            => $input['hargamodal'],
            'hargarekomendasi'      => $input['hargarekomendasi'],
            'hargajual'             => $input['margin'],
        ];

        // saving variant
        $VariantModel->save($variants);

        // Update Old Stock
        $oldstockdata   = [
            'id'            => $oldstocks['id'],
            'hargadasar'    => $input['hargadasar'],
            'hargamodal'    => $input['hargamodal'],
        ];
        // Save Data Old Stock
        $OldStockModel->save($oldstockdata);

        // redirect back
        return redirect()->back()->with('message', lang('Global.saved'));
    }

    public function deletevar($id)
    {
        // Calling Model
        $StockModel             = new StockModel();
        $VariantModel           = new VariantModel();
        $BundleModel            = new BundleModel();
        $BundledetailModel      = new BundledetailModel();
        $OldStockModel          = new OldStockModel();

        // Populating & Removing Stock Data
        $stocks = $StockModel->where('variantid', $id)->find();
        foreach ($stocks as $stock) {
            $StockModel->delete($stock['id']);
        }

        // Populating $ Remove Old Stock Data
        $oldstocks = $OldStockModel->where('variantid', $id)->find();
        foreach ($oldstocks as $oldstock) {
            $OldStockModel->delete($oldstock['id']);
        }

        // Populating & Removing Bundle & Bundle Detail Data
        $bundledets = $BundledetailModel->where('variantid', $id)->find();
        foreach ($bundledets as $bundets) {
            // Populating & Removing Bundle Data
            $bundles = $BundleModel->where('id', $bundets['bundleid'])->find();
            foreach ($bundles as $bundle) {
                $BundleModel->delete($bundle['id']);
            }

            // Removing Bundle Detail
            $BundledetailModel->delete($bundets['id']);
        }

        // Removing Variant Data
        $VariantModel->delete($id);

        return redirect()->back()->with('error', lang('Global.deleted'));
    }

    public function delete($id)
    {
        // calling Model
        $ProductModel           = new ProductModel();
        $StockModel             = new StockModel();
        $VariantModel           = new VariantModel();
        $BundleModel            = new BundleModel();
        $BundledetailModel      = new BundledetailModel();
        $OldStockModel          = new OldStockModel();

        // Populating & Removing Variants Data
        $variants = $VariantModel->where('productid', $id)->find();
        foreach ($variants as $varian) {
            // Populating & Removing Stocks Data
            $stocks = $StockModel->where('variantid', $varian['id'])->find();
            foreach ($stocks as $stock) {
                $StockModel->delete($stock['id']);
            }

            // Populating & Removing Old Stocks Data
            $oldstocks = $OldStockModel->where('variantid', $varian['id'])->find();
            foreach ($oldstocks as $oldstock) {
                $OldStockModel->delete($oldstock['id']);
            }

            // Populating & Removing Bundle & Bundle Detail Data
            $bundledets = $BundledetailModel->where('variantid', $varian['id'])->find();
            foreach ($bundledets as $bundets) {
                // Populating & Removing Bundle Data
                $bundles = $BundleModel->where('id', $bundets['bundleid'])->find();
                foreach ($bundles as $bundle) {
                    $BundleModel->delete($bundle['id']);
                }

                // Removing Bundle Detail
                $BundledetailModel->delete($bundets['id']);
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

    public function deletecat($id)
    {
        // parsing data
        $CategoryModel  = new CategoryModel();
        $ProductModel   = new ProductModel();

        $procat         = $ProductModel->where('catid', $id)->find();
        foreach ($procat as $catpro) {
            $procatid    = [
                'id'  =>  $catpro['id'],
                'catid' => "0",
            ];
            $ProductModel->save($procatid);
        }
        $data['category']   = $CategoryModel->where('id', $id)->first();

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
        $ProductModel = new ProductModel();
        $CategoryModel = new CategoryModel();

        $probrand = $ProductModel->where('brandid', $id)->find();
        foreach ($probrand as $brandpro) {
            $brandid    = [
                'id'        =>  $brandpro['id'],
                'brandid'   => "0",
            ];
            $ProductModel->save($brandid);
        }

        $data['category']   = $CategoryModel->where('id', $id)->first();
        $data['brand'] = $BrandModel->where('id', $id)->first();

        // delete data
        $BrandModel->delete($id);

        // return
        return redirect()->back()->with('error', lang('Global.deleted'));
    }

    public function favorite($id)
    {
        // Calling Model
        $ProductModel = new ProductModel();

        // initialize
        $input      = $this->request->getPost('favorite');

        $data = [
            'id'        => $id,
            'favorite'  => $input,
        ];

        $ProductModel->save($data);
        die(json_encode(array($input)));
    }
}
