<?php namespace App\Models;

use CodeIgniter\Model;

class MemberModel extends Model
{
    protected $allowedFields = [
        'name','email','phone','trx','poin',

    ];

    protected $table      = 'member';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
    

}