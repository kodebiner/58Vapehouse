<?php namespace App\Models;

use CodeIgniter\Model;

class DiscountModel extends Model
{
    protected $allowedFields = [
        'variantid','value','type',

    ];

    protected $table      = 'discount';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
    

}