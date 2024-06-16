<?php
namespace App\Controllers;
use App\Models\PromoModel;

class Promo extends BaseController
{
    protected $data;
    protected $db, $builder;
    protected $auth;
    protected $config;
    
    public function index()
    {
        // Calling Model
        $PromoModel        = new PromoModel;

        // Find Data
        $data           = $this->data;
        $promos         = $PromoModel->orderBy('id', 'DESC')->findAll();

        // Parsing data to view
        $data['title']          = lang('Global.promo');
        $data['description']    = lang('Global.promoListDesc');
        $data['promos']         = $promos;

        return view ('Views/promo', $data);
    }

    public function create()
    {
        // Calling Model
        $PromoModel        = new PromoModel;

        // Populating Data
        $input = $this->request->getPost();

        // get data
        $data = [
            'name'          => $input['name'],
            'description'   => $input['description'],
            'status'        => $input['status'],
        ];

        if (isset($input['photo'])) {
            $data['photo'] = $input['photo'];
        }

        // insert data product
        $PromoModel->insert($data);

        // Return
        return redirect()->back()->with('message', lang('Global.saved'));
    }

    public function update($id)
    {
        // Calling Model
        $PromoModel        = new PromoModel;

        // Populating Data
        $input = $this->request->getPost();

        // get data
        $data = [
            'id'            => $id,
            'name'          => $input['name'],
            'description'   => $input['description'],
            'status'        => $input['status'],
        ];

        // insert data product
        $PromoModel->save($data);

        // Return
        return redirect()->back()->with('message', lang('Global.saved'));
    }

    public function delete($id)
    {
        // Calling Model
        $PromoModel  = new PromoModel;

        $PromoModel->delete($id);

        return redirect()->back()->with('error', lang('Global.deleted'));
    }
}