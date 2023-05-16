<?php

namespace App\Controllers;

use App\Models\BundleModel;


class CashMan extends BaseController
{
    public function index()
    {
        // Calling Models
        $bundleModel = new BundleModel;


        // get outlet
        if ($this->data['outletPick'] === null) {
            $bundles      = $bundleModel->findAll();
        } else {
            $bundles      = $bundleModel->where('outletid', $this->data['outletPick'])->find();
        }

        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.Bundle');
        $data['description']    = lang('Global.bundleListDesc');
        $data['bundles']        = $bundles;


        return view('Views/bundle', $data);
    }

    public function create()
    {

        // Calling Models
        $bundleModel = new BundleModel;
        
        // get outlet
        if ($this->data['outletPick'] === null) {
            $bundle      = $bundleModel->findAll();
        } else {
            $bundle      = $bundleModel->where('outletid', $this->data['outletPick'])->find();
        }
        
        // initialize
        $input          = $this->request->getPost();

        // save data
        $data = [
            'name'      => $input['name'],
            'price'     => $input['price'],

        ];

        // validation
        if (! $this->validate([
            'name'      =>  "required|max_length[255]',",
            'price'      =>  'required',
        ])) {
                
           return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
            
        // Inserting CashFlow
        $bundleModel->insert($data);

        return redirect()->back()->with('message', lang('Global.saved'));
    }

    public function update($id) {

        // Calling Models
        $bundleModel = new BundleModel;

        // initialize
        $input = $this->request->getpost();

        // saved data
        $data = [
            'id'        => $id,
            'name'      => $input['name'],
            'price'     => $input['price'],

        ];

        // validation
        if (! $this->validate([
            'name'      =>  "required|max_length[255]',",
            'price'     =>  'required',
            ])
        )
        {      
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // save data
        $bundleModel->save($data);

        return redirect()->back()->with('massage', lang('global.saved'));

    }

    public function delete($id) {

        // calling model
        $bundleModel = new bundleModel;

        // deleted
        $bundleModel->delete($id);
        return redirect()->back()->with('error', lang('Global.deleted'));

    }
}