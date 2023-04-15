<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        $data                   = $this->data;
        $data['title']          = lang('Global.dashboard');
        $data['description']    = lang('Global.dashdesc');
        
        return view('dashboard', $data);
    }
}
