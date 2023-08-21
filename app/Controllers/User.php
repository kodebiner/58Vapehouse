<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\GroupUserModel;
use Myth\Auth\Models\GroupModel;
use App\Models\OutletModel;
use App\Models\OutletaccessModel;

class User extends BaseController
{

    protected $db, $builder;
    protected $auth;
    protected $config;

    public function __construct()
    {
        $this->db       = \Config\Database::connect();
        $validation     = \Config\Services::validation();
        $this->builder  = $this->db->table('users');
        $this->config   = config('Auth');
        $this->auth     = service('authentication');

        
    }

    public function index()
    {
        // Calling Model
        $GroupModel             = new GroupModel();
        $OutletModel            = new OutletModel();
        $OutletAccessModel      = new OutletaccessModel();


        // Populating data

        $this->builder->where('deleted_at', null);
        $this->builder->join('auth_groups_users', 'auth_groups_users.user_id = users.id');
        $this->builder->join('auth_groups', 'auth_groups.id = auth_groups_users.group_id');
        if ($this->data['role'] === 'supervisor') {
            $this->builder->where('auth_groups.name', 'operator');
        }
        $this->builder->where('users.id !=', $this->data['uid']);
        $this->builder->select('users.id as id, users.username as username, users.firstname as firstname, users.lastname as lastname, users.email as email, users.phone as phone, auth_groups.id as group_id, auth_groups.name as role');
        $query =   $this->builder->get();

        
        
        
        // Parsing data to view
        $data                   = $this->data;
        $data['title']          = lang('Global.employeeList');
        $data['description']    = lang('Global.employeeListDesc');
        $data['roles']          = $GroupModel->findAll();
        $data['users']          = $query->getResult();
        $data['outlets']        = $OutletModel->findAll();
        $data['outletAccess']   = $OutletAccessModel->findAll();

        return view('Views/user', $data);
    }

    public function create()

    {
        $authorize = service('authorization');

        // Calling Entities
        $newUser            = new \App\Entities\User();
        $OutletAccessModel   = new OutletaccessModel();
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

        // OutAccess
        foreach ($input['outlet'] as $out){
            $outAccess = [
                'userid' => $userId,
                'outletid' => $out,
            ];
            $OutletAccessModel->save($outAccess);
           
        }

        // Return back to index
        return redirect()->to('user');   
    }

    public function update($id)

    {
        $authorize = service('authorization');

        // Calling Entities
        $updateUser = new \App\Entities\User();
        $OutletAccessModel   = new OutletaccessModel();
        // Calling Model
        $UserModel      = new UserModel();
        $GroupUserModel = new GroupUserModel();
        $GroupModel     = new GroupModel();
        
        // Defining input
        $input = $this->request->getPost();

        // Finding user
        $user = $UserModel->find($id);

        // Validation basic form
        if (!empty($input['username'])) {
            $rules['username']  = 'alpha_numeric_space|min_length[3]|max_length[30]|is_unique[users.username]';
        }
        if (!empty($input['email'])) {
            $rules['email']     = 'valid_email|is_unique[users.email]';
        }
        if (!empty($input['phone'])) {
            $rules['phone']     = 'numeric|is_unique[users.phone]';
        }
        $rules['role'] = 'required';
        
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Data user update
        $updateUser->id = $id;
        if (!empty($input['username'])) {
            $updateUser->username  = $input['username'];
        } else {
            $updateUser->username  = $user->username;
        }
        if (!empty($input['email'])) {
            $updateUser->email     = $input['email'];
        } else {
            $updateUser->email  = $user->email;
        }
        if (!empty($input['firstname'])) {
            $updateUser->firstname     = $input['firstname'];
        } else {
            $updateUser->firstname  = $user->firstname;
        }
        if (!empty($input['lastname'])) {
            $updateUser->lastname     = $input['lastname'];
        } else {
            $updateUser->lastname  = $user->lastname;
        }
        if (!empty($input['phone'])) {
            $updateUser->phone     = $input['phone'];
        } else {
            $updateUser->phone  = $user->phone;
        }

        // Reset password
        if (!empty($input['password'])) {
            $rules = [
                'password'      => 'strong_password',
                'pass_confirm'  => 'required|matches[password]'
            ];

            if (! $this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $updateUser->password   = $input['password'];
            $updateUser->reset_at   = date('Y-m-d H:i:s');
        }

        // Updating user info
        $UserModel->save($updateUser);

        // Finding group
        $groups = $GroupUserModel->where('user_id', $id)->find();

        // Removing from group
        foreach ($groups as $group) {
            $authorize->removeUserFromGroup($id, $group['group_id']);
        }

        // Adding to group
        $authorize->addUserToGroup($id, $input['role']);

        // Deleting Old Outlet Access
        $oldAcc = $OutletAccessModel->where('userid',$id)->find();
        foreach ($oldAcc as $acc ){
            foreach ($input['outlet'] as $newAcc){
                if ($acc['outletid'] != $newAcc){
                    $oldOut = $acc['outletid'];
                    $data = [
                        'id'        => $acc['id'],
                        'outletid'  => $oldOut,
                        'userid'    => $id,
                    ];
                    $OutletAccessModel->delete($data);
                }
            }
        }
        foreach ($input['outlet'] as $out){
            $outAccess = [
                'userid' => $id,
                'outletid' => $out,
            ];
            $OutletAccessModel->save($outAccess);
        }
       
        
        // Redirect to user management
        return redirect()->to('user')->with('message', lang('Global.saved'));

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
        return redirect()->to('user')->with('message', lang('Global.deleted'));

        // delete Outlet Access
        $outAccess =  $OutletAccessModel->where('id',$id);
        $OutletAccessModel->delete($outAccess);

    }


}
