<?php namespace App\Models;

use CodeIgniter\Model;

class BundleModel extends Model
{
    protected $allowedFields = [
        'name','price',

    ];

    protected $table      = 'bundle';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
    

}