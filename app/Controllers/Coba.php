<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\VariantModel;

class Coba extends BaseController
{
    protected $data;
    protected $db, $builder;
    protected $auth;
    protected $config;
    
    public function ajax() {

    $ProductModel = new ProductModel;
    $VariantModel = new VariantModel;

        if(isset($_POST['request'])){
            $request = $_POST['request'];
        }

        // Fetch Product List By Product Id
        if($request == 'getPro'){

            $productid = 0;
            if(isset($_POST['productid']) && is_numeric($_POST['productid'])){
                $productid = $_POST['productid'];
            }

            $variant = $VariantModel->where('productid', $productid)->find();

            echo json_encode(array($variant));
            exit;
        }


        // Fetch Product List By Product Id
        if($request == 'getVariant'){

            $variantid = 0;
            if(isset($_POST['variantid']) && is_numeric($_POST['variantid'])){
                $variantid = $_POST['variantid'];
            }

            $variant = $VariantModel->where('id', $variantid)->find();

            echo json_encode(array($variant));
            exit;
        }
    }

}