<?php namespace App\Models;

use CodeIgniter\Model;

class PaymentModel extends Model
{
    protected $allowedFields = [
        'cashid','name','outletid',

    ];

    protected $table      = 'payment';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
    

}