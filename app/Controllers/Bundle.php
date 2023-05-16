<?php

namespace App\Controllers;

use App\Models\BundledetailModel;
use App\Models\BundleModel;
use App\Models\VariantModel;
use App\Models\ProductModel;


class Bundle extends BaseController
{
    public function index()
    {
        // Calling Models
        $bundleModel        = new BundleModel;
        $variantModel       = new VariantModel;
        $productModel       = new ProductModel;
        $bundleDetailModel  = new BundledetailModel;

        // get data 
        if ($this->data['outletPick'] === null) {
            $bundles      = $bundleModel->findAll();
        } else {
            $bundles      = $bundleModel->where('outletid', $this->data['outletPick'])->find();
        }

        $bundleDetails    = $bundleDetailModel->findAll();
        $variants         = $variantModel->findAll();
        $products         = $productModel->findAll();

        // Parsing Data to View
        $data                           = $this->data;
        $data['title']                  = lang('Global.Bundle');
        $data['description']            = lang('Global.bundleListDesc');
        $data['bundles']                = $bundles;
        $data['variants']               = $variants;
        $data['products']               = $products;
        $data['bundleDetails']          = $bundleDetails;


        return view('Views/bundle', $data);
    }

    public function create()
    {

        // Calling Models
        $bundleModel        = new BundleModel;
        $bundleDetailModel  = new BundledetailModel;
        $variantModel       = new VariantModel;
        $productModel       = new ProductModel;

        // get outlet
        $variants         = $variantModel->findAll();
        $products         = $productModel->findAll();
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

        // validation bundle
        if (! $this->validate([
            'name'      =>  "required|max_length[255]',",
            'price'     =>  'required',
        ])) {
                
           return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Inserting CashFlow
        $bundleModel->insert($data);

        // get bundle id
        $bundleId = $bundleModel->getInsertID();

        $detail  = [
            'bundleid'  => $bundleId,
            'variantid' => $input['variant'],
        ];

        // insert bundle detail
        $bundleDetailModel->insert($detail);

        return redirect()->back()->with('message', lang('Global.saved'));
    }

    public function update($id) {

        // Calling Models
        $bundleModel        = new BundleModel;
        $bundleDetailModel  = new BundledetailModel;


        // initialize
        $input = $this->request->getpost();

        // get data
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

        $detail = [
            'bundleid'  => $id,
            'variantid' => $input['variantid'],
        ];

        // validation
        if (! $this->validate([
            'bundleid'      =>  "required",
            'variantid'     =>  'required',
            ])
        )
        {      
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        return redirect()->back()->with('massage', lang('global.saved'));

    }

    public function delete($id) {

        // calling model
        $bundleModel        = new bundleModel;
        $bundleDetailModel  = new BundleModel;

        // deleted
        $detail =  $bundleDetailModel->where('bundleid,$id')->first();
        $bundleDetailModel->delete($id);
        $bundleModel->delete($id);
        return redirect()->back()->with('error', lang('Global.deleted'));

    }
}