<?php

namespace App\Controllers;

use App\Models\OutletModel;
use App\Models\ProductModel;
use App\Models\StockModel;
use App\Models\VariantModel;

class Outlet extends BaseController
{
    public function index()
    {
        // Calling Models
        $OutletModel    = new OutletModel();

        // Populating Data
        $outlets    = $OutletModel->findAll();

        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.outlet');
        $data['description']    = lang('Global.outletListDesc');
        $data['outlets']        = $outlets;

        return view('Views/outlet', $data);
    }

    public function create()
    {
        $validation = \Config\Services::validation();

        // Calling Models
        $OutletModel    = new OutletModel;
        $StockModel     = new StockModel;
        $VariantModel   = new VariantModel;

        // Populating data
        $input      = $this->request->getPost();
        $outlets    = $OutletModel->findAll();
        $stocks     = $StockModel->findAll();

        $data = [
            'name'      => $input['name'],
            'address'   => $input['address'],
            'maps'      => $input['maps'],
        ];
        
        if (! $this->validate([
            'name'      => "required|max_length[255]',",
            'address'   => 'required',
            'maps'      => 'required|max_length[255]',
        ])) {
                
           return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
            
        // Inserting Outlet
        $OutletModel->insert($data);

        //Getting Outlet ID
        $outletID = $OutletModel->getInsertID();

        $variants   = $VariantModel->findAll();
        foreach ($variants as $variant ){
            $stock = [
                'outletid'  => $outletID,
                'variantid' => $variant['id'],
                'qty'       => '0',

            ];
            $StockModel->save($stock);
        }
        return redirect()->back()->with('message', lang('Global.saved'));
    }

    public function update($id)
    {
        // Calling Models
        $outlets    = new OutletModel();

        // Populating Data
        $data['outlet'] = $outlets->where('id', $id)->first();
        $input          = $this->request->getPost();
        $validation     = \Config\Services::validation();

        $data = [
            'id'        => $id,
            'name'      => $input['name'],
            'address'   => $input['address'],
            'maps'      => $input['maps'],
        ];

        // Validasi
        if (! $this->validate([
            'name'      => "max_length[255]',",
            'address'   => "max_length[255]',",
            'maps'      => "max_length[255]',",
        ])) {

            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        // Simpan Data
        $outlets->save($data);

        return redirect()->back()->with('message', lang('Global.saved'));
    }

    public function delete($id)
    {
        $OutletModel   = new OutletModel();
        $StockModel     = new StockModel;

        
        $stocks = $StockModel->where('outletid',$id)->find();
        foreach ($stocks as $stock) {
        $StockModel->delete($stock['id']);
        }

        $OutletModel->delete($id);


        return redirect()->back()->with('error', lang('Global.deleted'));

        
    }
}