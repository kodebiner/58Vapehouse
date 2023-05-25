<?php namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $allowedFields = [
        'name','photo','thumbnail','favorite','description','catid', 'brandid'
    ];

    protected $table      = 'product';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';

}