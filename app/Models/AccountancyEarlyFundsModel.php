<?php namespace App\Models;

use CodeIgniter\Model;

class AccountancyEarlyFundsModel extends Model
{
    protected $allowedFields = [
        'coa_a_id','debit_value','credit_value','created_at','updated_at','deleted_at',

    ];

    protected $table      = 'accountancy_early_funds';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
    protected $useTimestamps    = true;
    protected $useSoftDeletes   = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';
}