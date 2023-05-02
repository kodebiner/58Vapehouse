<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\GroupUserModel;
use Myth\Auth\Models\GroupModel;

class Account extends BaseController
{
    public function index()
    {
        // Parsing data to view
        $data                   = $this->data;
        $data['title']          = lang('Global.userProfile');
        $data['description']    = lang('Global.userProfileDesc');

        return view('Views/account', $data);
    }
}