<?php namespace App\Models;

use CodeIgniter\Model;

class StockmovementModelBackup extends Model
{
    protected $allowedFields = [
        'variantid','origin','destination','qty','date',

    ];

    protected $table      = 'stockmovementbackup';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
}