<?php

namespace App\Controllers;

use App\Models\CashModel;
use App\Models\CashmovementModel;
use App\Models\OutletModel;
use App\Models\UserModel;

class CashMove extends BaseController
{
    protected $data;
    protected $db, $builder;
    protected $auth;
    protected $config;
    
    public function index()
    {
        $pager      = \Config\Services::pager();

        // Calling Models
        $CashModel              = new CashModel;
        $CashmoveModel          = new CashmovementModel;
        $OutletModel            = new OutletModel;

        // Populating Data
        $input  = $this->request->getGet('daterange');

        if (!empty($input)) {
            $daterange = explode(' - ', $input);
            $startdate = $daterange[0];
            $enddate = $daterange[1];
        } else {
            $startdate  = date('Y-m-1' . ' 00:00:00');
            $enddate    = date('Y-m-t' . ' 23:59:59');
        }

        $outlets                = $OutletModel->findAll();
        $cashman                = $CashModel->findAll();

        // if (!empty($input)) {
        //     if ($startdate === $enddate) {
                $cashmoves              = $CashmoveModel->orderBy('id', 'DESC')->where('date >=', $startdate . ' 00:00:00')->where('date <=', $enddate . ' 23:59:59')->paginate(20, 'cashmove');
        //     } else {
        //         $cashmoves              = $CashmoveModel->orderBy('id', 'DESC')->where('date >=', $startdate . '00:00:00')->where('date <=', $enddate . '23:59:59')->paginate(20, 'cashmove');
        //     }
        // } else {
        //     $cashmoves              = $CashmoveModel->orderBy('id', 'DESC')->paginate(20, 'cashmove');
        // }

        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.walletMoveList');
        $data['description']    = lang('Global.walletMoveListDesc');
        $data['cashmoves']      = $cashmoves;
        $data['cashmans']       = $cashman;
        $data['outlets']        = $outlets;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);
        $data['pager']          = $CashmoveModel->pager;

        return view('Views/cashmove', $data);
    }

    public function create()
    {

        // Calling Models
        $CashModel      = new CashModel;
        $CashmoveModel  = new CashmovementModel;

        // Populating data
        $Cash        =  $CashModel->findAll();

        // initialize
        $input          = $this->request->getPost();

        // save data
        $data = [
            'description'       => $input['description'],
            'origin'            => $input['origin'],
            'destination'       => $input['destination'],
            'qty'               => $input['qty'],
            'date'              => date("Y-m-d H:i:s"),

        ];

        // validation
        if (!$this->validate([
            'description'       =>  "required|max_length[255]',",
            'qty'               =>  "required"
        ])) {

            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Inserting Cash Movement
        $CashmoveModel->insert($data);

        // insert minus qty origin
        $cashmin    = $CashModel->where('id', $input['origin'])->first();
        $cashqty    = $cashmin['qty'] - $input['qty'];

        $quantity = [
            'id'    => $cashmin['id'],
            'qty'   => $cashqty,
        ];

        $CashModel->save($quantity);

        // insert plus qty origin
        $cashplus    = $CashModel->where('id', $input['destination'])->first();
        $cashqty    = $cashplus['qty'] + $input['qty'];

        $quant = [
            'id'    => $cashplus['id'],
            'qty'   => $cashqty,
        ];

        $CashModel->save($quant);

        return redirect()->back()->with('message', lang('Global.saved'));
    }
}
