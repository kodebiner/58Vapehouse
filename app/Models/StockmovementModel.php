<?php namespace App\Models;

use CodeIgniter\Model;

class StockmovementModel extends Model
{
    protected $allowedFields = [
        'origin','destination','date','status',

    ];

    protected $table      = 'stockmovement';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
}