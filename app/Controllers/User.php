<?php

namespace App\Controllers;

use App\Models\UserModel;
use Myth\Auth\Models\GroupModel;

class User extends BaseController
{

    protected $db, $builder;
    protected $auth;
    protected $config;

    public function __construct()
    {
        $this->db      = \Config\Database::connect();
        $validation = \Config\Services::validation();
        $this->builder =   $this->db->table('users');
        $this->config = config('Auth');
        $this->auth   = service('authentication');
    }

    public function index()
    {
        $GroupModel = new GroupModel();
        $UserModel  = new UserModel();
        
        $data                   = $this->data;
        $data['title']          = lang('Global.userList');
        $data['description']    = lang('Global.userListDesc');
        $data['roles']          = $GroupModel->findAll();
        $data['users']          = $UserModel->findAll();
        // $users = new \Myth\Auth\Models\UserModel();
        // $data['users']= $users->findAll();


        // $this->builder->select('users.id as userid, username, email, phone, name, group_id');
        // $this->builder->where('deleted_at', null);
        // $this->builder->join('auth_groups_users', 'auth_groups_users.user_id = users.id');
        // $this->builder->join('auth_groups', 'auth_groups.id = auth_groups_users.group_id');
        // $query =   $this->builder->get();

        return view('Views/user', $data);
    }

    public function tambah()

    {
        $data = [
            'title' => 'Form Tambah Data Admin'
        ];

        return view('Views/user', $data);
        
    }

    public function create()

    {
        $authorize = service('authorization');

        // Calling Entities
        $newUser = new \App\Entities\User();
        
        // Calling Model
        $UserModel = new UserModel();
        
        // Defining input
        $input = $this->request->getPost();

        // Validation basic form
        $rules = [
            'username'  => 'required|alpha_numeric_space|min_length[3]|max_length[30]|is_unique[users.username]',
            'email'     => 'required|valid_email|is_unique[users.email]',
            'firstname' => 'required',
            'lastname'  => 'required',
            'phone'     => 'required|numeric|is_unique[users.phone]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Validation password
        $rules = [
            'password'     => 'required|strong_password',
            'pass_confirm' => 'required|matches[password]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // New user data
        $newUser->username  = $input['username'];
        $newUser->email     = $input['email'];
        $newUser->firstname = $input['firstname'];
        $newUser->lastname  = $input['lastname'];
        $newUser->phone     = $input['phone'];
        $newUser->password  = $input['password'];

        // Save new user
        $UserModel->insert($newUser);

        // Get new user id
        $userId = $UserModel->getInsertID();

        // Adding new user to group
        $authorize->addUserToGroup($userId, $input['role']);

        // Return back to index
        return redirect()->to('user');   
    }


    public function edit($id)
    {
        //model initialize
        $usersModel = new UserModel();
        $data['users']= $usersModel->find($id);
        
        return view('Views/user', $data);
    }

    public function update($id)

    {
       
        $usersModel = new UserModel();
        $authorize = $auth = service('authorization');
        $data['users']= $usersModel->find($id);
 
        $input = $this->request->getPost();
    
            //insert data into database
            $data =  [
                'id'         => $this->request->getPost('id'),
                'username'   => $this->request->getPost('username'),
                'email'      => $this->request->getPost('email'),
                'phone'      => $this->request->getPost('phone'),
            ];

            $user = $usersModel->where('username', $input['username'])->first();
            //$authorize->inGroup($user->role, $user->Id);
            $authorize->removeUserFromGroup($this->request->getPost('id'), $this->request->getPost('group_id'));
            $authorize->addUserToGroup($this->request->getPost('id'), $this->request->getPost('role'));

            $usersModel->update($id, $data);

            //flash message
            session()->setFlashdata('message', 'Data Berhasil Diupdate');

            return redirect()->to('user');

    }

    public function delete($id)
    {
        $usersModel = new UserModel();
        $input = $this->request->getPost();

        $data['users']= $usersModel->find($id);
        if (empty($data)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data Password Tidak Ditemukan !');
        }
        $usersModel->update($id, [
            'active'   => '0'
        ]);
        $usersModel->delete($id);
        session()->setFlashdata('message', 'Delete Data Pegawai Berhasil');
        return redirect()->to('user');

    }


}
