<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\ProductModel;
use App\Models\BrandModel;
use App\Models\CashModel;
use App\Models\CategoryModel;
use App\Models\VariantModel;
use App\Models\StockModel;
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
        $this->config   = config('Auth');
        $this->auth     = service('authentication');
    }

    public function index()

    {
        // Calling Model        
        $GroupModel = new GroupModel();
        $CategoryModel = new CategoryModel();
        $ProductModel = new ProductModel();
        $BrandModel = new BrandModel();

        // Populating Data
        $products   = $ProductModel->findAll();
        $category   = $CategoryModel->findAll();
        $brand      = $BrandModel->findAll();


        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.product');
        $data['description']    = lang('Global.productDesc');
        $data['roles']          = $GroupModel->findAll();
        $data['products']       = $products;
        $data['category']       = $category;
        $data['brand']          = $brand;

        return view('Views/product', $data);
    }

    public function create()
    {  
            // calling Model
            $ProductModel = new ProductModel;
            $CategoryModel = new CategoryModel;
            $StockModel = new StockModel;
            $VariantModel = new VariantModel;
            $BrandModel = new BrandModel;
            $input = $this->request->getPost();
    
            // get data
            $data = [
                'name'           => $input['name'],
                'description'    => $input['description'],
            ];

            // rules
            $rule = [
                'nama'          => 'required|max_length[255]|is_unique[product.nama]',
                'description'   => 'required|max_length[255]',  
            ];
    
            // Validation
            if (! $this->validateData($data, $rule)) {
                return redirect()->to('product');
            }

            // insert data product
            $ProductModel->insert($data);

            // insert stock
            $product_id = $ProductModel->getInsertID();
            $category = $CategoryModel->findAll();
            foreach ($category as $cate) {
                $stock = [
                    'category_id' => $cate['id'],
                    'product_id'  => $product_id
                ];

                $StockModel->insert($stock);
            }

            // insert data variant
            $product_id = $ProductModel->getInsertID();

            return redirect()->to('product')->withInput();
    }

    public function edit($id)
    {

        // ambil data yang akan diedit
        $products = new ProductModel();
        $data['products'] = $products->where('id', $id)->first();

        // ambil gambar
        $foto = $this->request->getFile('foto');
        // Hapus File Lama 
        // unlink('img'.$this->request->getVar('namafotolama'));

        //pindah file 
        $foto->move('img'.$this->request->getVar('namafotolama'));
        // ambil nama file
        $namafoto = $foto->getName();
        // lakukan validasi data 
        $validation =  \Config\Services::validation();
        $validation->setRules([
            'id' => 'required',
            'nama' => 'required',
            'harga' => 'required',
            //'foto' => 'required',
        ]);
        $isDataValid = $validation->withRequest($this->request)->run();
        // jika data valid, maka simpan ke database
        if($isDataValid){
            $products->save([
                "id" => $id,
                "nama" => $this->request->getPost('nama'),
                "harga" => $this->request->getPost('harga'),
                "foto" => $namafoto,
            ]);
        }
        // tampilkan form edit
        return redirect()->to('product')->withInput();
    }

    public function delete($id)
    {
        // calling Model
        $ProductModel = new ProductModel();
        $StockModel = new StockModel();

        // delete data
        $ProductModel->delete($id);
        $stocks = $StockModel->findAll();
        foreach ($stocks as $stock) {
            if ($stock['product_id'] === $id) {
                $stock_id = $stock['id'];
                $StockModel->delete($stock_id);
            }
        }
        return redirect('product');
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
        return redirect()->to('product')->withInput();
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
        return redirect()->to('product');
    }

    public function createbrand()
    {
        // ambil data yang akan diedit
        $products = new ProductModel();
        $data['products'] = $products->where('id', $id)->first();

        // ambil gambar
        $foto = $this->request->getFile('foto');
        // Hapus File Lama 
        // unlink('img'.$this->request->getVar('namafotolama'));

        //pindah file 
        $foto->move('img'.$this->request->getVar('namafotolama'));
        // ambil nama file
        $namafoto = $foto->getName();

        // lakukan validasi data 
        $validation =  \Config\Services::validation();
        $validation->setRules([
            'id' => 'required',
            'nama' => 'required',
            'harga' => 'required',
            //'foto' => 'required',
        ]);
        $isDataValid = $validation->withRequest($this->request)->run();
        // jika data valid, maka simpan ke database
        if($isDataValid){
            $products->save([
                "id" => $id,
                "nama" => $this->request->getPost('nama'),
                "harga" => $this->request->getPost('harga'),
                "foto" => $namafoto,
            ]);
        }
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
        return redirect()->to('product');
    }
}