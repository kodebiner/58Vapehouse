<?php namespace App\Models;

use CodeIgniter\Model;

class SopDetailModel extends Model
{
    protected $table            = 'sopdetail';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'sopid','userid','status','created_at','updated_at','deleted_at',
    ];
    protected $useTimestamps   = true;
}