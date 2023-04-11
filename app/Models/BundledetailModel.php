<?php namespace App\Models;

use CodeIgniter\Model;

class BundledetailModel extends Model
{
    protected $allowedFields = [
        'bundleid','variantid',

    ];

    protected $table      = 'bundledetail';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
    

}