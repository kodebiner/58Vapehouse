<?php namespace App\Models;

use CodeIgniter\Model;

class OutletaccessModel extends Model
{
    protected $allowedFields = [
        'userid','outletid',

    ];

    protected $table      = 'outletaccess';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
    

}