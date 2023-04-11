<?php namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $allowedFields = [
        'name',

    ];

    protected $table      = 'category';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
    

}