<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\StockModel;
use App\Models\OutletModel;
use App\Models\ProductModel;
use App\Models\VariantModel;

class Reminder extends BaseController

{
    protected $db, $builder;
    protected $auth;
    protected $config;

    public function __construct()
        {
            $this->db      = \Config\Database::connect();
            $validation    = \Config\Services::validation();
            $this->config  = config('Auth');
            $this->auth    = service('authentication');
        }


     public function index()
        {
            // Calling Model
            $UserModel      = new UserModel;
            $OutletModel    = new OutletModel;
            $StockModel     = new StockModel;
            $OutletModel    = new ProductModel;
            $VariantModel   = new VariantModel;
            $ProductModel   = new ProductModel;
            

            // Finding Data
            $auth = service('authentication');
            $users          = $UserModel->findAll();
            $userId         = $auth->id();
            $user           = $UserModel->where('id',$userId)->first();
            $userOutlet     = $user->outletid;
            $outlets        = $OutletModel->findAll();
            $variants       = $VariantModel->findAll();
            $products       = $ProductModel->findAll();

            $x = "3";
            $y = "5";
            $stock = $StockModel->orderBy('id', 'DESC')->where('outletid', $this->data['outletPick'])->find();
            $today= $stock['restock'];
            $date = date_create($today);
            date_add($date, date_interval_create_from_date_string('30 days'));
            $newdate = date_format($date, 'Y/m/d H:i:s');

            
            // $date1=date_create($today);
            // $date2=date_create($newdate);
            // $diff=date_diff($date1,$date2);

            // $intrvl = $diff->format("%a");
            
            if ($stock['sale'] < $newdate){
                $v = "true";
            }else{
                $v = "false";
            }

            if ($this->data['outletPick'] === null) {
                $stocks  = $StockModel->orderBy('id', 'DESC')->findAll();
            } else {
                $stocks  = $StockModel->orderBy('id', 'DESC')->where('outletid', $this->data['outletPick'])->find();
            }
            
        
            // Parsing data to view
            $data                   = $this->data;
            $data['title']          = lang('Global.reminder');
            $data['description']    = lang('Global.reminder');
            $data['users']          = $users;
            $data['variants']       = $variants;
            $data['products']       = $products;
            $data['stocks']         = $stocks;
            $data['outlets']        = $OutletModel->findAll();

            return view ('Views/reminder', $data);
        }


 

}
