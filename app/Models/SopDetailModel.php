<?php namespace App\Models;

use CodeIgniter\Model;

class SopDetailModel extends Model
{
    protected $allowedFields = [
        'sopid','userid','status','dates',
    ];

    protected $table      = 'sopdetail';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
}