<?php namespace App\Models;

use CodeIgniter\Model;

class OutletModel extends Model
{
    protected $allowedFields = [
        'name','address','maps','instagram','phone', 'facebook',

    ];

    protected $table      = 'outlet';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
    

}