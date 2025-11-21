<?php namespace App\Models;

use CodeIgniter\Model;

class AccountancyCategoryModel extends Model
{
    protected $allowedFields = [
        'cat_code','cat_type','name',

    ];

    protected $table      = 'accountancy_categories';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
}