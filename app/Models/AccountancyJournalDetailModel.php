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

    public function getByTransactions(array $trxIds)
    {
        return $this->select("
            accountancy_journal_details.*,
            accountancy_coa.name as coa_name,
            CONCAT(
                cat.cat_code,
                accountancy_coa.coa_code,
                ' - ',
                accountancy_coa.name,
                ' - ',
                REPLACE(outlet.name, '58 Vapehouse ', '')
            ) AS coa_full_name
        ")
        ->join('accountancy_coa', 'accountancy_coa.id = accountancy_journal_details.coa_a_id', 'left')
        ->join('accountancy_categories AS cat', 'cat.id = accountancy_coa.cat_a_id', 'left')
        ->join('outlet', 'outlet.id = accountancy_coa.outletid', 'left')
        ->whereIn('accountancy_journal_details.trx_a_id', $trxIds)
        ->orderBy('accountancy_journal_details.id', 'ASC')
        ->findAll();
    }
}