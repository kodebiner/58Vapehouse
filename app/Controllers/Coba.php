<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\VariantModel;

class Coba extends BaseController
{


    public function ajax() {

    $ProductModel = new ProductModel;
    $VariantModel = new VariantModel;

        if(isset($_POST['request'])){
            $request = $_POST['request'];
        }

        // Fetch state list by country_id
        if($request == 'getPro'){

            $productid = 0;
            if(isset($_POST['productid']) && is_numeric($_POST['productid'])){
                $productid = $_POST['productid'];
            }

            $variant = $VariantModel->where('productid', $productid)->find();

            echo json_encode(array($variant));
            exit;
        }
    }

}