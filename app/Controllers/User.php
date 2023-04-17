<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\GroupUserModel;
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

    public function update($id)

    {
        $authorize = service('authorization');

        // Calling Entities
        $updateUser = new \App\Entities\User();
        
        // Calling Model
        $UserModel      = new UserModel();
        $GroupUserModel = new GroupUserModel();
        $GroupModel     = new GroupModel();
        
        // Defining input
        $input = $this->request->getPost();

        // Validation basic form
        // $rules = [
        //     'username'  => 'alpha_numeric_space|min_length[3]|max_length[30]|is_unique[users.username]',
        //     'email'     => 'valid_email|is_unique[users.email]',
        //     'firstname' => 'required',
        //     'lastname'  => 'required',
        //     'phone'     => 'numeric|is_unique[users.phone]',
        // ];
        if (isset($input['username'])) {
            $rules['username']  = 'alpha_numeric_space|min_length[3]|max_length[30]|is_unique[users.username]';
        }
        if (isset($input['email'])) {
            $rules['email']     = 'valid_email|is_unique[users.email]';
        }
        if (isset($input['phone'])) {
            $rules['phone']     = 'numeric|is_unique[users.phone]';
        }

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Data user update
        if (isset($input['username'])) {
            $updateUser->username  = $input['username'];
        }
        if (isset($input['email'])) {
            $updateUser->email     = $input['email'];
        }
        // $updateUser->firstname = $input['firstname'];
        // $updateUser->lastname  = $input['lastname'];
        if (isset($input['phone'])) {
            $updateUser->phone     = $input['phone'];
        }

        // Updating
        $UserModel->save($updateUser);

        // Finding group
        $groups = $GroupUserModel->where('user_id', $id)->find();

        // Removing from group
        foreach ($groups as $group) {
            $authorize->removeUserFromGroup($id, $group['group_id']);
        }

        // Adding to group
        $authorize->addUserToGroup($id, $input['role']);

        // Redirect to user management
        return redirect()->to('user');

            // $user = $usersModel->where('username', $input['username'])->first();
            // //$authorize->inGroup($user->role, $user->Id);
            // $authorize->removeUserFromGroup($this->request->getPost('id'), $this->request->getPost('group_id'));
            // $authorize->addUserToGroup($this->request->getPost('id'), $this->request->getPost('role'));

            // $usersModel->update($id, $data);

            // //flash message
            // session()->setFlashdata('message', 'Data Berhasil Diupdate');

            // return redirect()->to('user');

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
