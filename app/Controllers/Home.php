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

    public function outletses($id)
    {
        $session = \Config\Services::session();

        if ($id === '0') {
            $session->remove('outlet');
        } else {
            if ($session->get('outlet') != null) {
                $session->remove('outlet');
            }
            $session->set('outlet', $id);
        }

        return redirect()->back();
    }

    public function trial()
    {
        phpinfo();
    }
}
