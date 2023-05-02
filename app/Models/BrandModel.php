<?php namespace App\Models;

use CodeIgniter\Model;

class BrandModel extends Model
{
    protected $allowedFields = [
        'name',
    ];

    protected $table      = 'brand';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
    

}