<?php namespace App\Models;

use CodeIgniter\Model;

class AccountancyTaxModel extends Model
{
    protected $allowedFields = [
        'name','value','tax_cut_status','tax_cat_sell','tax_cat_buy',

    ];

    protected $table      = 'accountancy_tax';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
}