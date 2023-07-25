<?php namespace App\Models;

use CodeIgniter\Model;

class SupplierModel extends Model
{
    protected $allowedFields = [
        'name','phone','address','city',

    ];

    protected $table      = 'supplier';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
    

}