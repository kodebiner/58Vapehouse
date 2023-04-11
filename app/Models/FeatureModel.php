<?php namespace App\Models;

use CodeIgniter\Model;

class FeatureModel extends Model
{
    protected $allowedFields = [
        'productid',

    ];

    protected $table      = 'feature';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
    

}