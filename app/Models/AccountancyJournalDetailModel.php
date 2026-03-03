<?php

namespace App\Models;

use CodeIgniter\Model;

class AccountancyJournalDetailModel extends Model
{
    protected $table            = 'accountancy_journal_details';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;

    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'trx_a_id',
        'coa_a_id',
        'debit',
        'credit',
        'created_at'
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'trx_a_id' => 'required|integer',
        'coa_a_id' => 'required|integer',
        'debit'    => 'permit_empty|decimal',
        'credit'   => 'permit_empty|decimal'
    ];

    public function getByTransaction($trxId)
    {
        return $this->where('trx_a_id', $trxId)->findAll();
    }
}