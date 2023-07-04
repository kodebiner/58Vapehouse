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
        $bundles      = $bundleModel->orderBy('id', 'DESC')->findAll();

        $bundleDetails    = $bundleDetailModel->findAll();
        $variants         = $variantModel->findAll();
        $products         = $productModel->findAll();

        // Parsing Data to View
        $data                           = $this->data;
        $data['title']                  = lang('Global.bundleList');
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

        // Inserting Bundle
        $bundleModel->insert($data);

        // get bundle id
        $bundleId = $bundleModel->getInsertID();

        // Creating Bundle Detail
        foreach ($input['variantid'] as $variant) {
            $detail = [
                'bundleid'  => $bundleId,
                'variantid' => $variant
            ];
            $bundleDetailModel->insert($detail);
        }

        return redirect()->back()->with('message', lang('Global.saved'));
    }

    public function update($id) {

        // Calling Models
        $bundleModel        = new BundleModel;
        $bundleDetailModel  = new BundledetailModel;


        // initialize
        $input = $this->request->getpost();

        // Get Input Data
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

        // Save Bundle
        $bundleModel->save($data);

        return redirect()->back()->with('massage', lang('global.saved'));

    }

    public function delete($id) {

        // calling model
        $bundleModel        = new bundleModel;
        $bundleDetailModel  = new bundledetailModel;

        // deleted
        $detail =  $bundleDetailModel->where('bundleid',$id)->first();
        $bundleDetailModel->delete($detail);
        $bundleModel->delete($id);
        return redirect()->back()->with('error', lang('Global.deleted'));

    }

    public function indexbund($id)
    {

        // Calling Model
        $BundleModel            = new BundleModel();
        $BundledetailModel      = new BundledetailModel();
        $ProductModel           = new ProductModel();
        $VariantModel           = new VariantModel();

        // Populating Data
        $bundledet      = $BundledetailModel->where('bundleid', $id)->find();
        $bundle         = $BundleModel->findAll();
        $product        = $ProductModel->findAll(); 
        $variant        = $VariantModel->findAll();

        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.bundledetailList');
        $data['description']    = lang('Global.bundledetailListDesc');
        $data['bundledet']      = $bundledet;
        $data['bundles']        = $BundleModel->find($id);
        $data['variants']       = $variant;
        $data['products']       = $product;

        return view('Views/bundledet', $data);
    }

    public function createbund($id)
    {

        $bundleDetailModel  = new BundledetailModel;
        
        // initialize
        $input          = $this->request->getPost();

        // save data detail bundle
        foreach ($input['variantid'] as $variant) {
            $detail = [
                'bundleid'  => $id,
                'variantid' => $variant
            ];
            $bundleDetailModel->insert($detail);
        }

        return redirect()->back()->with('message', lang('Global.saved'));
    }

    public function deletebund($id){

        // calling Model
        $bundleDetailModel  = new bundledetailModel;

        $bundle = $bundleDetailModel->where('variantid',$id)->first();
        // // deleted
        $bundleDetailModel->delete($bundle);

        return redirect()->back()->with('message', lang('Global.delete'));
    }
}