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
        $input = $this->request->getPost();
        
    }

public function index()

    {
        $GroupModel = new GroupModel();
        $query                  =   $this->builder->get();
        $data                   = $this->data;
        $data['title']          = lang('Global.product');
        $data['description']    = lang('Global.productDesc');
        $data['roles']          = $GroupModel->findAll();
        $data['products']       = $query->getResult();
        $data['category']       = $query->getResult();

        return view('Views/product', $data);

    }

    public function create()

    {  
            $ProductModel = new ProductModel;
            $CategoryModel = new CategoryModel;
            $StockModel = new StockModel;
            $CashModel = new CashModel;
            $BrandModel = new BrandModel;
            $input = $this->request->getPost();
            $products = $ProductModel->findAll();

            
            // ambil gambar
            // $photo  = $this->request->getFile('photo');

            // //pindah file 
            // $photo->move('img');
            // // ambil nama file
            // $namafoto = $photo->getName();

            $data = [
                'name'           => $input['name'],
                'description'    => $input['description'],
                // 'photo'          => $namafoto
            ];


            $rule = [
                'nama'    => 'required|max_length[255]|is_unique[product.nama]',
                'harga'  => 'required|max_length[255]',
                // 'foto'  => 'required|max_length[255|is_image|max_size[foto,2048]|mime_in[foto,img/jpg,img/svg,img/png,img/jpeg]',
            ];
    
            if (! $this->validateData($data, $rule)) {

                return redirect()->to('product');
           

            }
            
            $ProductModel->insert($data);
            $product_id = $ProductModel->getInsertID();

            $category = $CategoryModel->findAll();
            foreach ($category as $cate) {
                $stock = [
                    'category_id' => $cate['id'],
                    'product_id'  => $product_id
                ];

                $StockModel->save($stock);
            }

            return redirect()->to('product')->withInput();
    }

public function createcat()

{
    $CategoryModel = new CategoryModel();
    $input = $this->request->getPost();

    $data = [
        'name' => $input['name'],
    ];

    $CategoryModel->save($data);

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
        'name' => $input['name'],
    ];
    
    // update data
    $CategoryModel->update($data);

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
        $ProductModel = new ProductModel();
        $StockModel = new StockModel();
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

}
