<?php

namespace App\Controllers;

use App\Models\CashModel;
use App\Models\CashExpModel;

class CashExp extends BaseController
{
    public function index()
    {
        $pager      = \Config\Services::pager();

        // Calling Models
        $CashModel              = new CashModel;
        $CashExpModel           = new CashExpModel;

        // Populating Data
        $input  = $this->request->getGet('daterange');

        if (!empty($input)) {
            $daterange  = explode(' - ', $input);
            $startdate  = $daterange[0];
            $enddate    = $daterange[1];
        } else {
            $startdate  = date('Y-m-1');
            $enddate    = date('Y-m-t');
        }

        $cashmans                   = $CashModel->notLike('name', 'Petty Cash')->find();

        if (!empty($input)) {
            if ($startdate === $enddate) {
                $cashexps               = $CashExpModel->orderBy('id', 'DESC')->where('date >=', $startdate . '00:00:00')->where('date <=', $enddate . '23:59:59')->paginate(20, 'cashexp');
            } else {
                $cashexps               = $CashExpModel->orderBy('id', 'DESC')->where('date >=', $startdate)->where('date <=', $enddate)->paginate(20, 'cashexp');
            }
        } else {
            $cashexps               = $CashExpModel->orderBy('id', 'DESC')->paginate(20, 'cashexp');
        }

        // Parsing Data to View
        $data                   = $this->data;
        $data['title']          = lang('Global.cashExpList');
        $data['description']    = lang('Global.cashExpListDesc');
        $data['cashexps']       = $cashexps;
        $data['cashmans']       = $cashmans;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);
        $data['pager']          = $CashExpModel->pager;

        return view('Views/cashexp', $data);
    }

    public function create()
    {

        // Calling Models
        $CashModel      = new CashModel;
        $CashExpModel   = new CashExpModel;

        // Populating data
        $Cash           =  $CashModel->findAll();

        // initialize
        $input          = $this->request->getPost();

        // save data
        $data = [
            'description'       => $input['description'],
            'cashid'            => $input['wallet'],
            'qty'               => $input['qty'],
            'date'              => date("Y-m-d H:i:s"),
        ];

        // Inserting Cash Expenses
        $CashExpModel->insert($data);

        // insert minus qty origin
        $cashmin    = $CashModel->where('id', $input['wallet'])->first();
        $cashqty    = (int)$cashmin['qty'] - (int)$input['qty'];

        $quantity = [
            'id'    => $cashmin['id'],
            'qty'   => $cashqty,
        ];

        $CashModel->save($quantity);
        return redirect()->back()->with('message', lang('Global.saved'));
    }
}
