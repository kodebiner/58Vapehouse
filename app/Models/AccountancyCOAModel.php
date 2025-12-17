<?php namespace App\Models;

use CodeIgniter\Model;

class AccountancyCOAModel extends Model
{
    protected $allowedFields = [
        'cat_a_id','outletid','coa_code','name','description','status_lock','status_active'

    ];

    protected $table      = 'accountancy_coa';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
}