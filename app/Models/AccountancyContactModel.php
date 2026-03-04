<?php namespace App\Models;

use CodeIgniter\Model;

class AccountancyContactModel extends Model
{
    protected $allowedFields = [
        'name','email','phone','address','source_type','source_id'

    ];

    protected $table      = 'accountancy_contact';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
}