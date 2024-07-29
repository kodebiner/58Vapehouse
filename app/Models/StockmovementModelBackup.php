<?php namespace App\Models;

use CodeIgniter\Model;

class StockmovementModel extends Model
{
    protected $allowedFields = [
        'variantid','origin','destination','qty','date',

    ];

    protected $table      = 'stockmovement';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
}