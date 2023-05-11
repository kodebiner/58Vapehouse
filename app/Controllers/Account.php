<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\GconfigModel;

class Account extends BaseController
{
    protected $auth;
    protected $config;

    public function __construct()
    {
        // Most services in this controller require
        // the session to be started - so fire it up!
        $this->session = service('session');

        $this->config = config('Auth');
        $this->auth   = service('authentication');
    }

    public function index()
    {
        // Parsing data to view
        $data                   = $this->data;
        $data['title']          = lang('Global.userProfile');
        $data['description']    = lang('Global.userProfileDesc');

        return view('Views/account', $data);
    }

    public function updateaccount()
    {

        // Calling Models
        $UserModel = new UserModel();
        
        // Calling Entities
        $updateUser = new \App\Entities\User();

        // Populating data
        $input = $this->request->getPost();

        // Remove old photo
        if ($input['oldphoto'] != $input['photo']) {
            unlink(FCPATH.'/img/profile/'.$input['oldphoto']);
        }

        // Validation basic data
        $rule = [
            'username'      => 'required|max_length[255]',
            'email'         => 'required|valid_email|max_length[255]',
            'firstname'     => 'required|max_length[255]',
            'lastname'      => 'required|max_length[255]',
        ];
        if (! $this->validate($rule)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        //Parsing User Basic Data
        $updateUser->id         = $this->data['uid'];
        $updateUser->username   = $input['username'];
        $updateUser->email      = $input['email'];
        $updateUser->firstname  = $input['firstname'];
        $updateUser->lastname   = $input['lastname'];
        $updateUser->phone      = $input['phone'];
        $updateUser->photo      = $input['photo'];

        // Validating new password
        if (!empty($input['newPass'])) {
            $rules = [
                'oldPass'       => 'required',
                'newPass'       => 'required',
                'newPassConf'   => 'required|matches[newPass]'
            ];

            if (! $this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            // Determining credential variable
            $login      = $input['username'];
            $password   = $input['oldPass'];

            // Determine credential type
            $type = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

            // Validating credential
            if (! $this->auth->attempt([$type => $login, 'password' => $password])) {
                return redirect()->back()->withInput()->with('error', $this->auth->error() ?? lang('Auth.badAttempt'));
            }

            // Parsing New Password Variable
            $updateUser->password   = $input['newPass'];
            $updateUser->reset_at   = date('Y-m-d H:i:s');
        }

        // Saving user data
        $UserModel->save($updateUser);

        // redirectiong
        return redirect()->back()->with('message', lang('Global.saved'));
    }

    public function business()
    {
        // Calling Models
        $ConfigModel = new GconfigModel();

        //Checking data availability
        $checkConfig = $ConfigModel->find('1');
        if (!$checkConfig) {
            $newConfig = [
                'ppn'               => '0',
            ];
            $ConfigModel->insert($newConfig);
        }

        // Populating data
        $gConfig = $ConfigModel->find('1');

        // Parsing data to view
        $data                   = $this->data;
        $data['title']          = lang('Global.businessInfo');
        $data['description']    = lang('Global.businessInfoDesc');
        $data['gconfig']        = $gConfig;

        // Rendering view
        return view('Views/business', $data);
    }

    public function updatebusiness()
    {
        // Calling Models
        $ConfigModel = new GconfigModel();

        // Populating data
        $input = $this->request->getPost();

        // Validating data
        $rule = [
            'name'              => 'required|alpha_numeric_punct',
            'poinvalue'         => 'required|decimal',
            'poinorder'         => 'required|decimal',
            'memberdisctype'    => 'required',
            'memberdisc'        => 'required|decimal',
            'ppn'               => 'required|decimal',
        ];
        if (! $this->validate($rule)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Updating data
        $gConfig = [
            'id'                => '1',
            'poinvalue'         => $input['poinvalue'],
            'poinorder'         => $input['poinorder'],
            'memberdisc'        => $input['memberdisc'],
            'memberdisctype'    => $input['memberdisctype'],
            'bizname'           => $input['name'],
            'ppn'               => $input['ppn'],
        ];
        $ConfigModel->save($gConfig);

        // Redirection
        return redirect()->back()->with('message', lang('Global.saved'));
    }
}