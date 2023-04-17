<?php namespace App\Models;

use CodeIgniter\Model;

class GroupUserModel extends Model
{
    protected $allowedFields = [
        'group_id','user_id'
    ];

    protected $table      = 'auth_groups_users';
    protected $returnType     = 'array';
    

}