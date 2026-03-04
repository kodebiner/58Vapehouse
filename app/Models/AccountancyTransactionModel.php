<?php

namespace App\Models;

use CodeIgniter\Model;

class AccountancyTransactionModel extends Model
{
    protected $table            = 'accountancy_transactions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;

    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'contact_id',
        'tax_id',
        'outletid',
        'source_id',
        'source_module',
        'date',
        'type',
        'amount',
        'note',
        'bunga',
        'due_date',
        'attachment',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'date'     => 'required',
        'type'     => 'required|integer',
        'amount'   => 'required|decimal',
        'note'     => 'required',
        'outletid' => 'required|integer'
    ];

    public function getTransactionWithJournal($id)
    {
        return $this->select('accountancy_transactions.*')
            ->where('accountancy_transactions.id', $id)
            ->first();
    }

    public function getTransactionsWithContact()
    {
        return $this->select('
                accountancy_transactions.*,
                accountancy_contact.name as contact,
            ')
            ->join('accountancy_contact', 'accountancy_contact.id = accountancy_transactions.contact_id', 'left')
            ->orderBy('accountancy_transactions.date', 'DESC')
            ->findAll();
    }
}