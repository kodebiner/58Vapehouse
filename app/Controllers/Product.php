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
use App\Models\StockmovementModel;
use App\Models\StockMoveDetailModel;
use App\Models\StockAdjustmentModel;
use App\Models\TransactionModel;
use App\Models\TrxdetailModel;
use App\Models\PurchaseModel;
use App\Models\PurchasedetailModel;
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

    public function change()
    {
        // // Calling Model
        // $ProductModel   = new ProductModel();

        // // Populating Data
        // $products       = $ProductModel->findAll();

        // foreach ($products as $product) {
        //     if ($product['status'] == '0') {
        //         $data = [
        //             'status'    => '2',
        //         ];
        //         $ProductModel->save($data);
        //     }
        // }
        // return redirect('product');
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
        // if (!empty($input['search']) && empty($input['category'])) {
        //     $products   = $ProductModel->like('name', $input['search'])->orderBy('id', 'DESC')->paginate(20, 'product');
        // } elseif (empty($input['search']) && !empty($input['category'])) {
        //     $products   = $ProductModel->where('catid', $input['category'])->orderBy('id', 'DESC')->paginate(20, 'product');
        // } elseif (!empty($input['search']) && !empty($input['category'])) {
        //     $products   = $ProductModel->where('catid', $input['category'])->like('name', $input['search'])->orderBy('id', 'DESC')->paginate(20, 'product');
        // } else {
        //     $products   = $ProductModel->orderBy('id', 'DESC')->paginate(20, 'product');
        // }
        
        // if (!empty($input['search'])) {
        //     if (!empty($input['category'])) {
        //         if (!empty($input['brand'])) {
        //             $products   = $ProductModel->like('name', $input['search'])->where('catid', $input['category'])->where('brandid', $input['brand'])->orderBy('id', 'DESC')->paginate(20, 'product');
        //         } else {
        //             $products   = $ProductModel->like('name', $input['search'])->where('catid', $input['category'])->orderBy('id', 'DESC')->paginate(20, 'product');
        //         }
        //     } else {
        //         $products   = $ProductModel->like('name', $input['search'])->orderBy('id', 'DESC')->paginate(20, 'product');
        //     }
        // } else {
        //     $products   = $ProductModel->orderBy('id', 'DESC')->paginate(20, 'product');
        // }

        // if (!empty($input['category'])) {
        //     if (!empty($input['brand'])) {
        //         if (!empty($input['search'])) {
        //             $products   = $ProductModel->like('name', $input['search'])->where('catid', $input['category'])->where('brandid', $input['brand'])->orderBy('id', 'DESC')->paginate(20, 'product');
        //         } else {
        //             $products   = $ProductModel->where('catid', $input['category'])->where('brandid', $input['brand'])->orderBy('id', 'DESC')->paginate(20, 'product');
        //         }
        //     } else {
        //         $products   = $ProductModel->where('catid', $input['category'])->orderBy('id', 'DESC')->paginate(20, 'product');
        //     }
        // } else {
        //     $products   = $ProductModel->orderBy('id', 'DESC')->paginate(20, 'product');
        // }

        // if (!empty($input['brand'])) {
        //     if (!empty($input['category'])) {
        //         if (!empty($input['search'])) {
        //             $products   = $ProductModel->like('name', $input['search'])->where('catid', $input['category'])->where('brandid', $input['brand'])->orderBy('id', 'DESC')->paginate(20, 'product');
        //         } else {
        //             $products   = $ProductModel->where('brandid', $input['brand'])->where('catid', $input['category'])->orderBy('id', 'DESC')->paginate(20, 'product');
        //         }
        //     } else {
        //         $products   = $ProductModel->where('brandid', $input['brand'])->orderBy('id', 'DESC')->paginate(20, 'product');
        //     }
        // } else {
        //     $products   = $ProductModel->orderBy('id', 'DESC')->paginate(20, 'product');
        // }
        
        // if (!empty($input['category'])) {
        //     $products   = $ProductModel->where('catid', $input['category'])->orderBy('id', 'DESC')->paginate(20, 'product');
        // } if (!empty($input['brand'])) {
        //     $products   = $ProductModel->where('brandid', $input['brand'])->orderBy('id', 'DESC')->paginate(20, 'product');
        // } else {
        //     $products   = $ProductModel->orderBy('id', 'DESC')->paginate(20, 'product');
        // }

        // if ((!empty($input['search'])) && (!empty($input['category'])) && (!empty($input['brand'])) && (isset($input['status']))) {
        //     $products   = $ProductModel->like('name', $input['search'])->where('catid', $input['category'])->where('brandid', $input['brand'])->where('status', $input['status'])->orderBy('id', 'DESC')->paginate(20, 'product');
        // }
        // elseif ((!empty($input['search'])) && (!empty($input['category']))) {
        //     $products   = $ProductModel->like('name', $input['search'])->where('catid', $input['category'])->orderBy('id', 'DESC')->paginate(20, 'product');
        // }
        // elseif ((!empty($input['search'])) && (!empty($input['brand']))) {
        //     $products   = $ProductModel->like('name', $input['search'])->where('brandid', $input['brand'])->orderBy('id', 'DESC')->paginate(20, 'product');
        // }
        // elseif ((!empty($input['search'])) && (isset($input['status']))) {
        //     $products   = $ProductModel->like('name', $input['search'])->where('status', $input['status'])->orderBy('id', 'DESC')->paginate(20, 'product');
        // }
        // elseif ((!empty($input['category'])) && (!empty($input['brand']))) {
        //     $products   = $ProductModel->where('catid', $input['category'])->where('brandid', $input['brand'])->orderBy('id', 'DESC')->paginate(20, 'product');
        // }
        // elseif ((!empty($input['category'])) && (isset($input['status']))) {
        //     $products   = $ProductModel->where('catid', $input['category'])->where('status', $input['status'])->orderBy('id', 'DESC')->paginate(20, 'product');
        // }
        // elseif ((!empty($input['brand'])) && (isset($input['status']))) {
        //     $products   = $ProductModel->where('brandid', $input['brand'])->where('status', $input['status'])->orderBy('id', 'DESC')->paginate(20, 'product');
        // }
        // elseif (!empty($input['search'])) {
        //     $products   = $ProductModel->like('name', $input['search'])->orderBy('id', 'DESC')->paginate(20, 'product');
        // }
        // elseif (!empty($input['category'])) {
        //     $products   = $ProductModel->where('catid', $input['category'])->orderBy('id', 'DESC')->paginate(20, 'product');
        // }
        // elseif (!empty($input['brand'])) {
        //     $products   = $ProductModel->where('brandid', $input['brand'])->orderBy('id', 'DESC')->paginate(20, 'product');
        // }
        // elseif (isset($input['status'])) {
        //     $products   = $ProductModel->where('status', $input['status'])->orderBy('id', 'DESC')->paginate(20, 'product');
        // }
        // else {
        //     $products   = $ProductModel->orderBy('id', 'DESC')->paginate(20, 'product');
        // }

        // $productdata    = [];
        // if (!empty($products)) {
        //     foreach ($products as $product) {
        //         $variants   = $VariantModel->where('productid', $product['id'])->find();
        //         $category   = $CategoryModel->find($product['catid']);
        //         $brand      = $BrandModel->find($product['brandid']);

        //         $productdata[$product['id']]['id']          = $product['id'];
        //         $productdata[$product['id']]['favorite']    = $product['favorite'];
        //         $productdata[$product['id']]['name']        = $product['name'];
        //         $productdata[$product['id']]['photo']       = $product['photo'];
        //         $productdata[$product['id']]['thumbnail']   = $product['thumbnail'];
        //         $productdata[$product['id']]['link']        = $product['link'];

        //         if (!empty($brand)) {
        //             $productdata[$product['id']]['brand']       = $brand['name'];
        //         } else {
        //             $productdata[$product['id']]['brand']       = 'Tidak Ada Brand';
        //         }

        //         if (!empty($category)) {
        //             $productdata[$product['id']]['category']    = $category['name'];
        //         } else {
        //             $productdata[$product['id']]['category']    = 'Tidak Ada Kategori';
        //         }

        //         // if (!empty($variants)) {
        //         //     $productdata[$product['id']]['price']       = "Rp " . number_format(($variants['hargamodal'] + $variants['hargajual']), 2, ',', '.');

        //         //     $stocks     = $StockModel->where('variantid', $variants['id'])->find();
        //         // } else {
        //         //     $productdata[$product['id']]['price']       = "Rp 0";
        //         // }

        //         if (!empty($variants)) {
        //             $countvar = array_count_values(array_column($variants, 'productid'))[$product['id']];
        //         } else {
        //             $countvar = 0;
        //         }

        //         if ($countvar > 1) {
        //             $productdata[$product['id']]['price']       = $countvar . ' ' . lang('Global.variant');
        //         } elseif ($countvar == 1) {
        //             foreach ($variants as $variant) {
        //                 $productdata[$product['id']]['price']   = "Rp " . number_format(($variant['hargamodal'] + $variant['hargajual']), 2, ',', '.');
        //             }
        //         } else {
        //             echo '0';
        //         }
        //     }
        // }

        $query = $ProductModel->orderBy('id', 'DESC');
        if (!empty($input['search'])) {
            $query->like('name', $input['search']);
        }

        if (!empty($input['category'])) {
            $query->where('catid', $input['category']);
        }

        if (!empty($input['brand'])) {
            $query->where('brandid', $input['brand']);
        }

        if (isset($input['status']) && $input['status'] !== '') {
            $query->where('status', (int)$input['status']);
        }
        
        $products = $query->paginate(20, 'product');

        $category       = $CategoryModel->findAll();
        $brand          = $BrandModel->findAll();
        $productcount   = count($ProductModel->findAll());

        $productid      = array();
        foreach ($products as $product) {
            $productid[] = $product['id'];
        }
        if (!empty($productid)) {
            $variant    = $VariantModel->whereIn('productid', $productid)->find();
        } else {
            $variant = array();
        }
        $variantid  = array();
        foreach ($variant as $var) {
            $variantid[] = $var['id'];
        }

        // $productchart = $ProductModel->findAll();
        // $variantchart = $VariantModel->findAll();

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
            $stkrem = array();
            foreach ($stockqty as $qty) {
                $stkqty[]   = (int)$qty->qty * (int)$qty->price;
                $stkbp[]    = (int)$qty->qty * (int)$qty->bprice;
                $stkrem[]   = (int)$qty->qty;
            }
            $stockchart[] = [
                'name'  => $cat['name'],
                'qty'   => array_sum($stkqty),
                'bqty'  => array_sum($stkbp),
                'stock' => array_sum($stkrem)
            ];
        }

        $percentage = array_sum(array_column($stockchart, 'qty'));

        $presentase = array();
        foreach ($stockchart as $srkchrt) {
            $presentase[] = [
                'name'      => $srkchrt['name'],
                'qty'       => $srkchrt['qty'],
                'bqty'      => $srkchrt['bqty'],
                'stock'     => $srkchrt['stock'],
                'persen'    => number_format(($srkchrt['qty'] / $percentage * 100), 2),
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
        $ProductModel   = new ProductModel();
        $CategoryModel  = new CategoryModel();
        $StockModel     = new StockModel();
        $OldStockModel  = new OldStockModel();
        $VariantModel   = new VariantModel();
        $OutletModel    = new OutletModel();

        // search all data
        $input = $this->request->getPost();
        $outlets = $OutletModel->findAll();

        // rules
        $rule = [
            'name'          => 'required|max_length[255]|is_unique[product.name]',
            'link'          => 'max_length[255]',
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
            'link'          => $input['link'],
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
        $productId  = $ProductModel->getInsertID();
        $category   = $CategoryModel->find($input['catid']);
        $lastvar    = $VariantModel->like('sku', $category['catcode'])->selectMax('sku')->first();
        $lastcode   = preg_replace("/[^0-9]/", '', $lastvar['sku']);
        $lastcode++;

        // insert variant
        foreach ($input['varBase'] as $baseKey => $baseValue) {
            $variant['productid']   = $productId;
            $variant['hargadasar']  = $baseValue;

            foreach ($input['varName'] as $nameKey => $nameValue) {
                if ($nameKey === $baseKey) {
                    $variant['name']    = $nameValue;
                    $variant['sku']     = strtoupper($category['catcode'].str_pad($lastcode++, 6, '0', STR_PAD_LEFT));
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
        $ProductModel       = new ProductModel();
        $CategoryModel      = new CategoryModel();
        $VariantModel       = new VariantModel();
        $OutletModel        = new OutletModel();
        $StockModel         = new StockModel();
        $OldStockModel      = new OldStockModel();

        // search all data
        $products           = $ProductModel->find($id);
        $category           = $CategoryModel->find($products['catid']);
        $lastvar            = $VariantModel->like('sku', $category['catcode'])->selectMax('sku')->first();
        $lastcode           = preg_replace("/[^0-9]/", '', $lastvar['sku']);
        $lastcode++;
        $outlets            = $OutletModel->findAll();
        $input              = $this->request->getPost();
        
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
            'sku'                   => strtoupper($category['catcode'].str_pad($lastcode++, 6, '0', STR_PAD_LEFT)),
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
        $CategoryModel  = new CategoryModel();
        $StockModel     = new StockModel();
        $VariantModel   = new VariantModel();
        $OutletModel    = new OutletModel();

        // inisialize
        $input          = $this->request->getPost();

        // search id
        $products       = $ProductModel->find($id);

        // rules validation
        $rule = [
            'name'          => 'required|max_length[255]',
            'link'          => 'max_length[255]',
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
            $status = $input['status'];
        } else {
            $status = 0;
        }

        if ($input['catid' . $id] != $products['catid']) {
            $catid      = $input['catid' . $id];

            $category   = $CategoryModel->find($catid);
            $variants   = $VariantModel->where('productid', $id)->find();
            $lastvar    = $VariantModel->like('sku', $category['catcode'])->selectMax('sku')->first();
            $lastcode   = preg_replace("/[^0-9]/", '', $lastvar['sku']);
            $lastcode++;

            foreach ($variants as $var) {
                $vardata  = [
                    'id'    => $var['id'],
                    'sku'   => strtoupper($category['catcode'].str_pad($lastcode++, 6, '0', STR_PAD_LEFT)),
                ];
                
                $VariantModel->save($vardata);
            }
        } else {
            $catid      = $products['catid'];
        }

        $data = [
            'id'            => $id,
            'name'          => $input['name'],
            'link'          => $input['link'],
            'description'   => $input['description'],
            'brandid'       => $input['brandid' . $id],
            'catid'         => $catid,
            'status'        => $status,
        ];

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
            'name'      => $input['name'],
            'catcode'   => strtoupper($input['catcode']),
        ];
        $CategoryModel->insert($data);
        return redirect()->back()->with('message', lang('Global.saved'));
    }

    public function editcat($id)
    {
        // parsing data
        $CategoryModel      = new CategoryModel();
        $VariantModel       = new VariantModel();

        // inizialise
        $input = $this->request->getPost();

        // Populating Data
        $category           = $CategoryModel->find($id);
        $variants           = $VariantModel->like('sku', $category['catcode'])->find();

        if ($input['catcode'] == $category['catcode']) {
            $catcode    = $category['catcode'];
        } else {
            $catcode    = $input['catcode'];

            foreach ($variants as $var) {
                $number = substr($var['sku'], -6);

                $vardata  = [
                    'id'    => $var['id'],
                    'sku'   => strtoupper($catcode.$number),
                ];
                
                $VariantModel->save($vardata);
            }
        }

        // get data
        $data = [
            'id'        => $id,
            'name'      => $input['name'],
            'catcode'   => strtoupper($catcode),
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

    // public function history($id)
    // {
    //     $pager      = \Config\Services::pager();

    //     // Calling Model
    //     $ProductModel           = new ProductModel();
    //     $VariantModel           = new VariantModel();
    //     $StockModel             = new StockModel();
    //     $StockmovementModel     = new StockmovementModel();
    //     $StockMoveDetailModel   = new StockMoveDetailModel();
    //     $StockAdjustmentModel   = new StockAdjustmentModel();
    //     $TransactionModel       = new TransactionModel();
    //     $TrxdetailModel         = new TrxdetailModel();
    //     $PurchaseModel          = new PurchaseModel();
    //     $PurchasedetailModel    = new PurchasedetailModel();
    //     $OutletModel            = new OutletModel();
    //     $UserModel              = new UserModel();

    //     // Populating Data
    //     $input          = $this->request->getGet();
    //     $historydata    = [];

    //     if ($this->data['outletPick'] != null) {
    //         // Name
    //         $variant            = $VariantModel->find($id);
    //         $product            = $ProductModel->find($variant['productid']);

    //         if (!empty($product)) {
    //             $name               = $product['name'].' - '.$variant['name'];
    //         } else {
    //             $name               = 'Produk Terhapus - '.$variant['name'];
    //         }
    
    //         // SKU
    //         $sku                = $variant['sku'];

    //         if (!empty($input['daterange'])) {
    //             $daterange = explode(' - ', $input['daterange']);
    //             $startdate = $daterange[0];
    //             $enddate = $daterange[1];
    //         } else {
    //             $startdate  = date('Y-m-d 00:00:00', strtotime('-7 days'));
    //             $enddate    = date('Y-m-t' . ' 23:59:59');
    //         }
    
    //         // Stock
    //         $stock              = $StockModel->where('variantid', $id)->where('outletid', $this->data['outletPick'])->first();
    //         if (!empty($stock)) {
    //             $stocknow           = $stock['qty'];
    //         } else {
    //             $stocknow           = 0;
    //         }

    //         // Stock Adjustment
    //         $stockadj       = $StockAdjustmentModel->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->where('variantid', $id)->where('outletid', $this->data['outletPick'])->find();
            
    //         if (!empty($stockadj)) {
    //             foreach ($stockadj as $stockad) {
    //                 $stockadjoutlet = $OutletModel->find($stockad['outletid']);
    //                 $stockadjuser   = $UserModel->find($stockad['userid']);

    //                 if (!empty($stockadjoutlet)) {
    //                     $saoutlet   = $stockadjoutlet['name'];
    //                 } else {
    //                     $saoutlet   = '-';
    //                 }

    //                 if (!empty($stockadjuser)) {
    //                     $sauser     = $stockadjuser->firstname.' '.$stockadjuser->lastname;
    //                 } else {
    //                     $sauser     = '-';
    //                 }

    //                 if ($stockad['type'] == '0') {
    //                     $stockadjvar1     = $VariantModel->find($stockad['variantid']);

    //                     $historydata[$stockad['date']]['date']      = $stockad['date'];
    //                     $historydata[$stockad['date']]['outlet']    = $saoutlet;
    //                     $historydata[$stockad['date']]['user']      = $sauser;
    //                     $historydata[$stockad['date']]['status']    = 'Penyesuaian Stok '.$stockad['note'];
    //                     $historydata[$stockad['date']]['qty']       = '<div style="color: green">+'.$stockad['qty'].'</div>';
    //                     $historydata[$stockad['date']]['type']      = '1';

    //                     if (!empty($stockadjvar1)) {
    //                         $stockadjprod1    = $ProductModel->find($stockadjvar1['productid']);
                            
    //                         if (!empty($stockadjprod1)) {
    //                             $historydata[$stockad['date']]['detail'][$stockad['id']]['sku']     = $stockadjvar1['sku'];
    //                             $historydata[$stockad['date']]['detail'][$stockad['id']]['name']    = $stockadjprod1['name'].' - '.$stockadjvar1['name'];
    //                             $historydata[$stockad['date']]['detail'][$stockad['id']]['qty']     = $stockad['qty'];
    //                         }
    //                     }
    //                 } else {
    //                     $stockadjvar2     = $VariantModel->find($stockad['variantid']);
                        
    //                     $historydata[$stockad['date']]['date']      = $stockad['date'];
    //                     $historydata[$stockad['date']]['outlet']    = $saoutlet;
    //                     $historydata[$stockad['date']]['user']      = $sauser;
    //                     $historydata[$stockad['date']]['status']    = 'Penyesuaian Stok '.$stockad['note'];
    //                     $historydata[$stockad['date']]['qty']       = '<div style="color: red">-'.$stockad['qty'].'</div>';
    //                     $historydata[$stockad['date']]['type']      = '1';

    //                     if (!empty($stockadjvar2)) {
    //                         $stockadjprod2    = $ProductModel->find($stockadjvar2['productid']);
                            
    //                         if (!empty($stockadjprod2)) {
    //                             $historydata[$stockad['date']]['detail'][$stockad['id']]['sku']     = $stockadjvar2['sku'];
    //                             $historydata[$stockad['date']]['detail'][$stockad['id']]['name']    = $stockadjprod2['name'].' - '.$stockadjvar2['name'];
    //                             $historydata[$stockad['date']]['detail'][$stockad['id']]['qty']     = $stockad['qty'];
    //                         }
    //                     }
    //                 }
    //             }
    //         }
            
    //         // Stock Movement In
    //         $stockmovementin    = $StockmovementModel->where('status', '3')->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->where('destination', $this->data['outletPick'])->find();
    //         if (!empty($stockmovementin)) {
    //             foreach ($stockmovementin as $smovein) {
    //                 $stockmovedetins        = $StockMoveDetailModel->where('stockmoveid', $smovein['id'])->where('variantid', $id)->find();
    //                 $stockmoveindetaileds   = $StockMoveDetailModel->where('stockmoveid', $smovein['id'])->find();
    //                 $inorigin               = $OutletModel->find($smovein['origin']);
    //                 $indestination          = $OutletModel->find($smovein['destination']);
    //                 $insender               = $UserModel->find($smovein['sender']);
    //                 $inreceiver             = $UserModel->find($smovein['receiver']);

    //                 if (!empty($inorigin)) {
    //                     $sminorigin         = $inorigin['name'];
    //                 } else {
    //                     $sminorigin         = '-';
    //                 }

    //                 if (!empty($indestination)) {
    //                     $smindestination    = $indestination['name'];
    //                 } else {
    //                     $smindestination    = '-';
    //                 }

    //                 if (!empty($insender)) {
    //                     $sminsender         = $insender->firstname.' '.$insender->lastname;
    //                 } else {
    //                     $sminsender         = '-';
    //                 }

    //                 if (!empty($inreceiver)) {
    //                     $sminreceiver       = $inreceiver->firstname.' '.$inreceiver->lastname;
    //                 } else {
    //                     $sminreceiver       = '-';
    //                 }

    //                 if (!empty($stockmovedetins)) {
    //                     foreach ($stockmovedetins as $stockmovedetin) {
    //                         $historydata[$smovein['date']]['date']          = $smovein['date'];
    //                         $historydata[$smovein['date']]['outlet']        = 'Asal '.$sminorigin.'</br>Tujuan '.$smindestination;
    //                         $historydata[$smovein['date']]['user']          = 'Dikirim '.$sminsender.'</br>Diterima '.$sminreceiver;
    //                         $historydata[$smovein['date']]['status']        = 'Pemindahan Stok Masuk';
    //                         $historydata[$smovein['date']]['qty']           = '<div style="color: green">+'.$stockmovedetin['qty'].'</div>';
    //                         $historydata[$smovein['date']]['type']          = '2';
    //                     }

    //                     if (!empty($stockmoveindetaileds)) {
    //                         foreach ($stockmoveindetaileds as $stockmoveindetailed) {
    //                             $stockmoveindetailedvar     = $VariantModel->find($stockmoveindetailed['variantid']);
    
    //                             if (!empty($stockmoveindetailedvar)) {
    //                                 $stockmoveindetailedprod    = $ProductModel->find($stockmoveindetailedvar['productid']);
                                    
    //                                 if (!empty($stockmoveindetailedprod)) {
    //                                     $historydata[$smovein['date']]['detail'][$stockmoveindetailed['id']]['sku']     = $stockmoveindetailedvar['sku'];
    //                                     $historydata[$smovein['date']]['detail'][$stockmoveindetailed['id']]['name']    = $stockmoveindetailedprod['name'].' - '.$stockmoveindetailedvar['name'];
    //                                     $historydata[$smovein['date']]['detail'][$stockmoveindetailed['id']]['qty']     = $stockmoveindetailed['qty'];
    //                                 }
    //                             }
    //                         }
    //                     }
    //                 }
    //             }
    //         }

    //         // Stock Movement Out
    //         $stockmovementout   = $StockmovementModel->where('status', '3')->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->where('origin', $this->data['outletPick'])->find();
    //         if (!empty($stockmovementout)) {
    //             foreach ($stockmovementout as $smoveout) {
    //                 $stockmovedetouts       = $StockMoveDetailModel->where('stockmoveid', $smoveout['id'])->where('variantid', $id)->find();
    //                 $stockmoveoutdetaileds  = $StockMoveDetailModel->where('stockmoveid', $smoveout['id'])->find();
    //                 $outorigin              = $OutletModel->find($smoveout['origin']);
    //                 $outdestination         = $OutletModel->find($smoveout['destination']);
    //                 $outsender              = $UserModel->find($smoveout['sender']);
    //                 $outreceiver            = $UserModel->find($smoveout['receiver']);

    //                 if (!empty($outorigin)) {
    //                     $smoutorigin         = $outorigin['name'];
    //                 } else {
    //                     $smoutorigin         = '-';
    //                 }

    //                 if (!empty($outdestination)) {
    //                     $smoutdestination    = $outdestination['name'];
    //                 } else {
    //                     $smoutdestination    = '-';
    //                 }

    //                 if (!empty($outsender)) {
    //                     $smoutsender         = $outsender->firstname.' '.$outsender->lastname;
    //                 } else {
    //                     $smoutsender         = '-';
    //                 }

    //                 if (!empty($outreceiver)) {
    //                     $smoutreceiver       = $outreceiver->firstname.' '.$outreceiver->lastname;
    //                 } else {
    //                     $smoutreceiver       = '-';
    //                 }
                    
    //                 if (!empty($stockmovedetouts)) {
    //                     foreach ($stockmovedetouts as $stockmovedetout) {
    //                         $historydata[$smoveout['date']]['date']     = $smoveout['date'];
    //                         $historydata[$smoveout['date']]['outlet']   = 'Dari '.$smoutorigin.'</br> Tujuan '.$smoutdestination;
    //                         $historydata[$smoveout['date']]['user']     = 'Dikirim '.$smoutsender.'</br> Diterima '.$smoutreceiver;
    //                         $historydata[$smoveout['date']]['status']   = 'Pemindahan Stok Keluar';
    //                         $historydata[$smoveout['date']]['qty']      = '<div style="color: red">-'.$stockmovedetout['qty'].'</div>';
    //                         $historydata[$smoveout['date']]['type']     = '3';
    //                     }

    //                     if (!empty($stockmoveoutdetaileds)) {
    //                         foreach ($stockmoveoutdetaileds as $stockmoveoutdetailed) {
    //                             $stockmoveoutdetailedvar     = $VariantModel->find($stockmoveoutdetailed['variantid']);
    
    //                             if (!empty($stockmoveoutdetailedvar)) {
    //                                 $stockmoveoutdetailedprod    = $ProductModel->find($stockmoveoutdetailedvar['productid']);
                                    
    //                                 if (!empty($stockmoveoutdetailedprod)) {
    //                                     $historydata[$smoveout['date']]['detail'][$stockmoveoutdetailed['id']]['sku']     = $stockmoveoutdetailedvar['sku'];
    //                                     $historydata[$smoveout['date']]['detail'][$stockmoveoutdetailed['id']]['name']    = $stockmoveoutdetailedprod['name'].' - '.$stockmoveoutdetailedvar['name'];
    //                                     $historydata[$smoveout['date']]['detail'][$stockmoveoutdetailed['id']]['qty']     = $stockmoveoutdetailed['qty'];
    //                                 }
    //                             }
    //                         }
    //                     }
    //                 }
    //             }
    //         }
            
    //         // Transaction
    //         $transactions   = $TransactionModel->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->where('outletid', $this->data['outletPick'])->find();
    //         if (!empty($transactions)) {
    //             foreach ($transactions as $trx) {
    //                 $trxdetails     = $TrxdetailModel->where('transactionid', $trx['id'])->where('variantid', $id)->find();
    //                 $trxdetaileds   = $TrxdetailModel->where('transactionid', $trx['id'])->find();
    //                 $trxoutlet      = $OutletModel->find($trx['outletid']);
    //                 $trxuser        = $UserModel->find($trx['userid']);

    //                 if (!empty($trxoutlet)) {
    //                     $toutlet    = $trxoutlet['name'];
    //                 } else {
    //                     $toutlet    = '-';
    //                 }

    //                 if (!empty($trxuser)) {
    //                     $tuser      = $trxuser->firstname.' '.$trxuser->lastname;
    //                 } else {
    //                     $tuser      = '-';
    //                 }

    //                 if (!empty($trxdetails)) {
    //                     foreach ($trxdetails as $trxdetail) {
    //                         $historydata[$trx['date']]['date']      = $trx['date'];
    //                         $historydata[$trx['date']]['outlet']    = $toutlet;
    //                         $historydata[$trx['date']]['user']      = $tuser;
    //                         $historydata[$trx['date']]['status']    = 'Penjualan';
    //                         $historydata[$trx['date']]['qty']       = '<div style="color: red">-'.$trxdetail['qty'].'</div>';
    //                         $historydata[$trx['date']]['type']      = '4';
    //                     }

    //                     if (!empty($trxdetaileds)) {
    //                         foreach ($trxdetaileds as $trxdetailed) {
    //                             $trxdetailedvar     = $VariantModel->find($trxdetailed['variantid']);
    //                             if (!empty($trxdetailedvar)) {
    //                                 $trxdetailedprod    = $ProductModel->find($trxdetailedvar['productid']);
    //                                 if (!empty($trxdetailedprod)) {
    //                                     $historydata[$trx['date']]['detail'][$trxdetailed['id']]['sku']     = $trxdetailedvar['sku'];
    //                                     $historydata[$trx['date']]['detail'][$trxdetailed['id']]['name']    = $trxdetailedprod['name'].' - '.$trxdetailedvar['name'];
    //                                     $historydata[$trx['date']]['detail'][$trxdetailed['id']]['qty']     = $trxdetailed['qty'];
    //                                 }
    //                             }
    //                         }
    //                     }
    //                 }
    //             }
    //         }
            
    //         // Purchase
    //         $purchases      = $PurchaseModel->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->where('outletid', $this->data['outletPick'])->find();
    //         if (!empty($purchases)) {
    //             foreach ($purchases as $purchase) {
    //                 $purdets        = $PurchasedetailModel->where('purchaseid', $purchase['id'])->where('variantid', $id)->find();
    //                 $purdetaileds   = $PurchasedetailModel->where('purchaseid', $purchase['id'])->find();
    //                 $purchaseoutlet = $OutletModel->find($purchase['outletid']);
    //                 $purchaseuser   = $UserModel->find($purchase['userid']);

    //                 if (!empty($purchaseoutlet)) {
    //                     $poutlet    = $purchaseoutlet['name'];
    //                 } else {
    //                     $poutlet    = '-';
    //                 }

    //                 if (!empty($purchaseuser)) {
    //                     $puser      = $purchaseuser->firstname.' '.$purchaseuser->lastname;
    //                 } else {
    //                     $puser      = '-';
    //                 }
    //                 if (!empty($purdets)) {
    //                     foreach ($purdets as $purdet) {
    //                         $historydata[$purchase['date']]['date']         = $purchase['date'];
    //                         $historydata[$purchase['date']]['outlet']       = $poutlet;
    //                         $historydata[$purchase['date']]['user']         = $puser;
    //                         $historydata[$purchase['date']]['status']       = 'Pembelian';
    //                         $historydata[$purchase['date']]['qty']          = '<div style="color: green">+'.$purdet['qty'].'</div>';
    //                         $historydata[$purchase['date']]['type']         = '5';
    //                     }

    //                     if (!empty($purdetaileds)) {
    //                         foreach ($purdetaileds as $purdetailed) {
    //                             $purdetailedvar     = $VariantModel->find($purdetailed['variantid']);
    //                             if (!empty($purdetailedvar)) {
    //                                 $purdetailedprod    = $ProductModel->find($purdetailedvar['productid']);
    //                                 if (!empty($purdetailedprod)) {
    //                                     $historydata[$purchase['date']]['detail'][$purdetailed['id']]['sku']     = $purdetailedvar['sku'];
    //                                     $historydata[$purchase['date']]['detail'][$purdetailed['id']]['name']    = $purdetailedprod['name'].' - '.$purdetailedvar['name'];
    //                                     $historydata[$purchase['date']]['detail'][$purdetailed['id']]['qty']     = $purdetailed['qty'];
    //                                 }
    //                             }
    //                         }
    //                     }
    //                 }
    //             }
    //         }
    //         array_multisort(array_column($historydata, 'date'), SORT_DESC, $historydata);

    //         $page       = (int) ($this->request->getGet('page') ?? 1);
    //         $perPage    = 20;
    //         $total      = count($historydata);

    //         // Parsing Data to View
    //         $data                   = $this->data;
    //         $data['title']          = lang('Global.productList');
    //         $data['description']    = lang('Global.productListDesc');
    //         $data['startdate']      = strtotime($startdate);
    //         $data['enddate']        = strtotime($enddate);
    //         $data['stocks']         = array_slice($historydata, ($page*20)-20, $page*20);
    //         $data['pager_links']    = $pager->makeLinks($page, $perPage, $total, 'front_full');
    //         $data['id']             = $id;
    //         $data['totalstock']     = $stocknow;
    //         $data['name']           = $name;
    //         $data['sku']            = $sku;
    
    //         return view('Views/history', $data);
    //     } else {
    //         return redirect()->to('');
    //     }
    // }

    public function history($id)
    {
        // buat safety: kalau kamu mau debug sementara, bisa naikkan max exec time
        // ini opsional: ini_set('max_execution_time', 300);

        $pager = \Config\Services::pager();

        // Models
        $ProductModel           = new ProductModel();
        $VariantModel           = new VariantModel();
        $StockModel             = new StockModel();
        $StockmovementModel     = new StockmovementModel();
        $StockMoveDetailModel   = new StockMoveDetailModel();
        $StockAdjustmentModel   = new StockAdjustmentModel();
        $TransactionModel       = new TransactionModel();
        $TrxdetailModel         = new TrxdetailModel();
        $PurchaseModel          = new PurchaseModel();
        $PurchasedetailModel    = new PurchasedetailModel();
        $OutletModel            = new OutletModel();
        $UserModel              = new UserModel();

        // input & init
        $input = $this->request->getGet();
        $historydata = [];

        if ($this->data['outletPick'] == null) {
            return redirect()->to('');
        }

        // variant & product basic
        $variant = $VariantModel->find($id);
        if (empty($variant)) {
            return redirect()->back()->with('error', 'Variant tidak ditemukan');
        }
        $product = $ProductModel->find($variant['productid'] ?? null);
        $name = !empty($product) ? ($product['name'].' - '.$variant['name']) : ('Produk Terhapus - '.$variant['name']);
        $sku = $variant['sku'] ?? '';

        // date range (default 7 hari supaya tidak overload)
        if (!empty($input['daterange'])) {
            $daterange = explode(' - ', $input['daterange']);
            $startdate = $daterange[0];
            $enddate   = $daterange[1];
        } else {
            // default 7 hari terakhir — kamu bisa ubah sesuai kebutuhan
            $startdate = date('Y-m-l 00:00:00');
            $enddate   = date('Y-m-d 23:59:59');
        }

        // current stock
        $stock = $StockModel->where('variantid', $id)->where('outletid', $this->data['outletPick'])->first();
        $stocknow = !empty($stock) ? ($stock['qty'] ?? 0) : 0;

        //
        // STRATEGI:
        // Untuk setiap kategori (adjustment, movement in/out, trx, purchase)
        // 1) ambil event yang MELIBATKAN variant ini (join ke detail table / where variantid = $id)
        // 2) kumpulkan ID event (mis. transaction.id, stockmove.id, purchase.id, stockadjust.id)
        // 3) ambil semua detail untuk event-event itu dengan 1 query (join variant+product) -> build detail map
        // 4) push per-event ke $historydata[] (bukan keyed by date)
        //

        // ---------- 1) STOCK ADJUSTMENTS ----------
        // Adjustment biasanya per variant, jadi kita join variant/product langsung
        $stockAdjRows = $StockAdjustmentModel
            ->select('stockadjustment.* , variant.sku as variant_sku, variant.name as variant_name, product.name as product_name')
            ->join('variant', 'variant.id = stockadjustment.variantid', 'left')
            ->join('product', 'product.id = variant.productid', 'left')
            ->where('stockadjustment.date >=', $startdate)
            ->where('stockadjustment.date <=', $enddate)
            ->where('stockadjustment.variantid', $id)
            ->where('stockadjustment.outletid', $this->data['outletPick'])
            ->orderBy('stockadjustment.date', 'DESC')
            ->findAll();

        if (!empty($stockAdjRows)) {
            foreach ($stockAdjRows as $sa) {
                // safe user/outlet
                $outletName = '-';
                $userName = '-';
                if (!empty($sa['outletid'])) {
                    $o = $OutletModel->find($sa['outletid']);
                    $outletName = $o['name'] ?? '-';
                }
                if (!empty($sa['userid'])) {
                    $u = $UserModel->find($sa['userid']);
                    if (is_object($u)) {
                        $userName = trim(($u->firstname ?? '') . ' ' . ($u->lastname ?? ''));
                    } else {
                        $userName = trim(($u['firstname'] ?? '') . ' ' . ($u['lastname'] ?? ''));
                    }
                }

                $qtyHtml = ($sa['type'] == '0') ? ('<div style="color: green">+' . $sa['qty'] . '</div>') : ('<div style="color: red">-' . $sa['qty'] . '</div>');

                $detail = [];
                // because we joined variant/product above, we can fill detail from row
                $detail[$sa['id']] = [
                    'sku' => $sa['variant_sku'] ?? ($variant['sku'] ?? ''),
                    'name'=> !empty($sa['product_name']) ? ($sa['product_name'].' - '.($sa['variant_name'] ?? '')) : ($name),
                    'qty' => $sa['qty'],
                ];

                $historydata[] = [
                    'date' => $sa['date'],
                    'outlet' => $outletName,
                    'user' => $userName,
                    'status' => 'Penyesuaian Stok ' . ($sa['note'] ?? ''),
                    'qty' => $qtyHtml,
                    'type' => '1',
                    'detail' => $detail,
                ];
            }
        }

        // ---------- 2) STOCK MOVEMENTS IN ----------
        // Ambil stockmove yang MELIBATKAN variant ini dan destination = outletPick
        $stockMoveInRows = $StockMoveDetailModel
            ->select('stockmovedetail.*, stockmovement.date as sm_date, stockmovement.origin as sm_origin, stockmovement.destination as sm_destination, stockmovement.sender as sm_sender, stockmovement.receiver as sm_receiver, stockmovement.id as sm_id')
            ->join('stockmovement', 'stockmovement.id = stockmovedetail.stockmoveid', 'left')
            ->where('stockmovement.status', '3')
            ->where('stockmovement.date >=', $startdate)
            ->where('stockmovement.date <=', $enddate)
            ->where('stockmovedetail.variantid', $id)
            ->where('stockmovement.destination', $this->data['outletPick'])
            ->orderBy('stockmovement.date', 'DESC')
            ->findAll();

        // collect stockmove ids
        $smInIds = [];
        foreach ($stockMoveInRows as $r) {
            $smInIds[$r['sm_id']] = $r['sm_id'];
        }
        // build detail map for these stockmove ids (all items in the movement)
        $smInDetailsMap = [];
        if (!empty($smInIds)) {
            $allSmDetails = $StockMoveDetailModel
                ->select('stockmovedetail.*, variant.sku as v_sku, variant.name as v_name, product.name as p_name')
                ->join('variant', 'variant.id = stockmovedetail.variantid', 'left')
                ->join('product', 'product.id = variant.productid', 'left')
                ->whereIn('stockmovedetail.stockmoveid', array_values($smInIds))
                ->findAll();

            foreach ($allSmDetails as $d) {
                $smInDetailsMap[$d['stockmoveid']][$d['id']] = [
                    'sku' => $d['v_sku'] ?? '',
                    'name'=> !empty($d['p_name']) ? ($d['p_name'].' - '.($d['v_name'] ?? '')) : ($d['v_name'] ?? ''),
                    'qty' => $d['qty'],
                ];
            }
        }

        // now push each filtered row as event (each row corresponds to variant-specific qty inside movement)
        if (!empty($stockMoveInRows)) {
            foreach ($stockMoveInRows as $r) {
                // fetch origin/destination/user names safely
                $originName = '-';
                $destName = '-';
                $senderName = '-';
                $receiverName = '-';
                if (!empty($r['sm_origin'])) {
                    $o = $OutletModel->find($r['sm_origin']);
                    $originName = $o['name'] ?? '-';
                }
                if (!empty($r['sm_destination'])) {
                    $d = $OutletModel->find($r['sm_destination']);
                    $destName = $d['name'] ?? '-';
                }
                if (!empty($r['sm_sender'])) {
                    $s = $UserModel->find($r['sm_sender']);
                    $senderName = is_object($s) ? trim(($s->firstname ?? '') . ' ' . ($s->lastname ?? '')) : trim(($s['firstname'] ?? '') . ' ' . ($s['lastname'] ?? ''));
                }
                if (!empty($r['sm_receiver'])) {
                    $rc = $UserModel->find($r['sm_receiver']);
                    $receiverName = is_object($rc) ? trim(($rc->firstname ?? '') . ' ' . ($rc->lastname ?? '')) : trim(($rc['firstname'] ?? '') . ' ' . ($rc['lastname'] ?? ''));
                }

                $qtyHtml = '<div style="color: green">+' . ($r['qty'] ?? 0) . '</div>';
                $outletText = 'Asal ' . $originName . '</br>Tujuan ' . $destName;
                $userText = 'Dikirim ' . $senderName . '</br>Diterima ' . $receiverName;

                $historydata[] = [
                    'date' => $r['sm_date'],
                    'outlet' => $outletText,
                    'user' => $userText,
                    'status' => 'Pemindahan Stok Masuk',
                    'qty' => $qtyHtml,
                    'type' => '2',
                    'detail' => $smInDetailsMap[$r['sm_id']] ?? [],
                ];
            }
        }

        // ---------- 3) STOCK MOVEMENTS OUT ----------
        $stockMoveOutRows = $StockMoveDetailModel
            ->select('stockmovedetail.*, stockmovement.date as sm_date, stockmovement.origin as sm_origin, stockmovement.destination as sm_destination, stockmovement.sender as sm_sender, stockmovement.receiver as sm_receiver, stockmovement.id as sm_id')
            ->join('stockmovement', 'stockmovement.id = stockmovedetail.stockmoveid', 'left')
            ->where('stockmovement.status', '3')
            ->where('stockmovement.date >=', $startdate)
            ->where('stockmovement.date <=', $enddate)
            ->where('stockmovedetail.variantid', $id)
            ->where('stockmovement.origin', $this->data['outletPick'])
            ->orderBy('stockmovement.date', 'DESC')
            ->findAll();

        $smOutIds = [];
        foreach ($stockMoveOutRows as $r) {
            $smOutIds[$r['sm_id']] = $r['sm_id'];
        }

        $smOutDetailsMap = [];
        if (!empty($smOutIds)) {
            $allSmOutDetails = $StockMoveDetailModel
                ->select('stockmovedetail.*, variant.sku as v_sku, variant.name as v_name, product.name as p_name')
                ->join('variant', 'variant.id = stockmovedetail.variantid', 'left')
                ->join('product', 'product.id = variant.productid', 'left')
                ->whereIn('stockmovedetail.stockmoveid', array_values($smOutIds))
                ->findAll();

            foreach ($allSmOutDetails as $d) {
                $smOutDetailsMap[$d['stockmoveid']][$d['id']] = [
                    'sku' => $d['v_sku'] ?? '',
                    'name'=> !empty($d['p_name']) ? ($d['p_name'].' - '.($d['v_name'] ?? '')) : ($d['v_name'] ?? ''),
                    'qty' => $d['qty'],
                ];
            }
        }

        if (!empty($stockMoveOutRows)) {
            foreach ($stockMoveOutRows as $r) {
                $originName = '-';
                $destName = '-';
                $senderName = '-';
                $receiverName = '-';
                if (!empty($r['sm_origin'])) {
                    $o = $OutletModel->find($r['sm_origin']);
                    $originName = $o['name'] ?? '-';
                }
                if (!empty($r['sm_destination'])) {
                    $d = $OutletModel->find($r['sm_destination']);
                    $destName = $d['name'] ?? '-';
                }
                if (!empty($r['sm_sender'])) {
                    $s = $UserModel->find($r['sm_sender']);
                    $senderName = is_object($s) ? trim(($s->firstname ?? '') . ' ' . ($s->lastname ?? '')) : trim(($s['firstname'] ?? '') . ' ' . ($s['lastname'] ?? ''));
                }
                if (!empty($r['sm_receiver'])) {
                    $rc = $UserModel->find($r['sm_receiver']);
                    $receiverName = is_object($rc) ? trim(($rc->firstname ?? '') . ' ' . ($rc->lastname ?? '')) : trim(($rc['firstname'] ?? '') . ' ' . ($rc['lastname'] ?? ''));
                }

                $qtyHtml = '<div style="color: red">-' . ($r['qty'] ?? 0) . '</div>';
                $outletText = 'Dari ' . $originName . '</br> Tujuan ' . $destName;
                $userText = 'Dikirim ' . $senderName . '</br> Diterima ' . $receiverName;

                $historydata[] = [
                    'date' => $r['sm_date'],
                    'outlet' => $outletText,
                    'user' => $userText,
                    'status' => 'Pemindahan Stok Keluar',
                    'qty' => $qtyHtml,
                    'type' => '3',
                    'detail' => $smOutDetailsMap[$r['sm_id']] ?? [],
                ];
            }
        }

        // ---------- 4) TRANSACTIONS (Sales) ----------
        // 1) ambil transaction ids yang ada variant ini
        $trxWithVariant = $TrxdetailModel
            ->select('trxdetail.transactionid')
            ->join('transaction', 'transaction.id = trxdetail.transactionid', 'left')
            ->where('trxdetail.variantid', $id)
            ->where('transaction.date >=', $startdate)
            ->where('transaction.date <=', $enddate)
            ->where('transaction.outletid', $this->data['outletPick'])
            ->groupBy('trxdetail.transactionid')
            ->orderBy('transaction.date', 'DESC')
            ->findAll();

        $trxIds = [];
        foreach ($trxWithVariant as $r) {
            $trxIds[] = $r['transactionid'];
        }

        // ambil semua trxdetail untuk trxIds (detail semua item di transaksi itu)
        $trxDetailsMap = [];
        if (!empty($trxIds)) {
            $allTrxDetails = $TrxdetailModel
                ->select('trxdetail.*, variant.sku as v_sku, variant.name as v_name, product.name as p_name, transaction.date as trx_date, transaction.outletid as trx_outletid, transaction.userid as trx_userid, transaction.id as trx_id')
                ->join('variant', 'variant.id = trxdetail.variantid', 'left')
                ->join('product', 'product.id = variant.productid', 'left')
                ->join('transaction', 'transaction.id = trxdetail.transactionid', 'left')
                ->whereIn('trxdetail.transactionid', $trxIds)
                ->orderBy('transaction.date', 'DESC')
                ->findAll();

            // group by transaction id
            foreach ($allTrxDetails as $d) {
                $trxDetailsMap[$d['transactionid']][$d['id']] = [
                    'sku' => $d['v_sku'] ?? '',
                    'name'=> !empty($d['p_name']) ? ($d['p_name'].' - '.($d['v_name'] ?? '')) : ($d['v_name'] ?? ''),
                    'qty' => $d['qty'],
                ];
            }

            // buat event per transaction but only for those where variant appears: we need qty of the variant per transaction
            // fetch transaction rows for necessary ids to get date/outlet/user
            $trxRows = $TransactionModel
                ->whereIn('id', $trxIds)
                ->orderBy('date', 'DESC')
                ->findAll();

            foreach ($trxRows as $trx) {
                // find the qty of THIS variant in this trx (search in trxdetail table)
                // since we previously selected trxdetail rows where variantid = $id we can query that quickly
                $variantDetailRows = $TrxdetailModel->where('transactionid', $trx['id'])->where('variantid', $id)->findAll();
                if (empty($variantDetailRows)) continue;
                foreach ($variantDetailRows as $vd) {
                    $toutlet = '-';
                    $tuser = '-';
                    if (!empty($trx['outletid'])) {
                        $o = $OutletModel->find($trx['outletid']);
                        $toutlet = $o['name'] ?? '-';
                    }
                    if (!empty($trx['userid'])) {
                        $u = $UserModel->find($trx['userid']);
                        $tuser = is_object($u) ? trim(($u->firstname ?? '') . ' ' . ($u->lastname ?? '')) : trim(($u['firstname'] ?? '') . ' ' . ($u['lastname'] ?? ''));
                    }

                    $historydata[] = [
                        'date' => $trx['date'],
                        'outlet' => $toutlet,
                        'user' => $tuser,
                        'status' => 'Penjualan',
                        'qty' => '<div style="color: red">-' . ($vd['qty'] ?? 0) . '</div>',
                        'type' => '4',
                        'detail' => $trxDetailsMap[$trx['id']] ?? [],
                    ];
                }
            }
        }

        // ---------- 5) PURCHASES ----------
        // Mirip transaksi: ambil purchase ids yang ada variant ini
        $purWithVariant = $PurchasedetailModel
            ->select('purchasedetail.purchaseid')
            ->join('purchase', 'purchase.id = purchasedetail.purchaseid', 'left')
            ->where('purchasedetail.variantid', $id)
            ->where('purchase.date >=', $startdate)
            ->where('purchase.date <=', $enddate)
            ->where('purchase.outletid', $this->data['outletPick'])
            ->groupBy('purchasedetail.purchaseid')
            ->orderBy('purchase.date', 'DESC')
            ->findAll();

        $purIds = [];
        foreach ($purWithVariant as $r) {
            $purIds[] = $r['purchaseid'];
        }

        $purDetailsMap = [];
        if (!empty($purIds)) {
            $allPurDetails = $PurchasedetailModel
                ->select('purchasedetail.*, variant.sku as v_sku, variant.name as v_name, product.name as p_name, purchase.date as pur_date, purchase.outletid as pur_outletid, purchase.userid as pur_userid, purchase.id as pur_id')
                ->join('variant', 'variant.id = purchasedetail.variantid', 'left')
                ->join('product', 'product.id = variant.productid', 'left')
                ->join('purchase', 'purchase.id = purchasedetail.purchaseid', 'left')
                ->whereIn('purchasedetail.purchaseid', $purIds)
                ->orderBy('purchase.date', 'DESC')
                ->findAll();

            foreach ($allPurDetails as $d) {
                $purDetailsMap[$d['purchaseid']][$d['id']] = [
                    'sku' => $d['v_sku'] ?? '',
                    'name'=> !empty($d['p_name']) ? ($d['p_name'].' - '.($d['v_name'] ?? '')) : ($d['v_name'] ?? ''),
                    'qty' => $d['qty'],
                ];
            }

            // events per purchase (only for purchases that include our variant)
            $purRows = $PurchaseModel->whereIn('id', $purIds)->orderBy('date', 'DESC')->findAll();
            foreach ($purRows as $pur) {
                $pdRows = $PurchasedetailModel->where('purchaseid', $pur['id'])->where('variantid', $id)->findAll();
                if (empty($pdRows)) continue;
                foreach ($pdRows as $pd) {
                    $poutlet = '-';
                    $puser = '-';
                    if (!empty($pur['outletid'])) {
                        $o = $OutletModel->find($pur['outletid']);
                        $poutlet = $o['name'] ?? '-';
                    }
                    if (!empty($pur['userid'])) {
                        $u = $UserModel->find($pur['userid']);
                        $puser = is_object($u) ? trim(($u->firstname ?? '') . ' ' . ($u->lastname ?? '')) : trim(($u['firstname'] ?? '') . ' ' . ($u['lastname'] ?? ''));
                    }

                    $historydata[] = [
                        'date' => $pur['date'],
                        'outlet' => $poutlet,
                        'user' => $puser,
                        'status' => 'Pembelian',
                        'qty' => '<div style="color: green">+' . ($pd['qty'] ?? 0) . '</div>',
                        'type' => '5',
                        'detail' => $purDetailsMap[$pur['id']] ?? [],
                    ];
                }
            }
        }

        // ---------- SORT & PAGINATE ----------
        usort($historydata, function ($a, $b) {
            $ta = strtotime($a['date'] ?? '1970-01-01 00:00:00');
            $tb = strtotime($b['date'] ?? '1970-01-01 00:00:00');
            return $tb <=> $ta;
        });

        $page = (int) ($this->request->getGet('page') ?? 1);
        $perPage = 20;
        $total = count($historydata);
        $slice = array_slice($historydata, ($page - 1) * $perPage, $perPage);

        // prepare view data
        $data = $this->data;
        $data['title'] = lang('Global.productList');
        $data['description'] = lang('Global.productListDesc');
        $data['startdate'] = strtotime($startdate);
        $data['enddate'] = strtotime($enddate);
        $data['stocks'] = $slice;
        $data['pager_links'] = $pager->makeLinks($page, $perPage, $total, 'front_full');
        $data['id'] = $id;
        $data['totalstock'] = $stocknow;
        $data['name'] = $name;
        $data['sku'] = $sku;

        return view('Views/history', $data);
    }
}
