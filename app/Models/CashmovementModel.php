<?php namespace App\Models;

use CodeIgniter\Model;

class CashmovementModel extends Model
{
    protected $allowedFields = [
        'description','origin','destination','qty','date',

    ];

    protected $table      = 'cashmovement';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
    

}