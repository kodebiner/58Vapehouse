<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        $data                   = $this->data;
        
        return view('dashboard', $data);
    }
}
