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

    public function trial()
    {
        $authorize = service('authorization');
        $GroupUserModel = new \App\Models\GroupUserModel();

        $authorize->removeUserFromGroup('4', '3');
        $authorize->addUserToGroup('4', '1');
    }
}
