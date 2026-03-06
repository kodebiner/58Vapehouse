<?php

namespace App\Models;

use CodeIgniter\Model;

use function PHPSTORM_META\map;

class AccountancyTransactionModel extends Model
{
    protected $table            = 'accountancy_transactions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;

    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;

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
        'updated_at',
        'created_by',
        'updated_by',
        'deleted_at',
        'deleted_by',
        'status'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
    protected $beforeDelete = ['setDeletedBy'];

    protected $validationRules = [
        'date'     => 'required',
        'type'     => 'required|integer',
        'amount'   => 'required|decimal',
        'note'     => 'required',
        'outletid' => 'required|integer',
        'attachment' => 'permit_empty|max_size[attachment,4096]|ext_in[attachment,pdf,jpg,jpeg,png]'
    ];

    protected function setDeletedBy(array $data)
    {
        if (isset($data['id'])) {
            $this->builder()
                ->whereIn('id', $data['id'])
                ->set('deleted_by', user()->id)
                ->update();
        }

        return $data;
    }

    public function getTransactionWithJournal($id)
    {
        return $this->select('accountancy_transactions.*')
            ->where('accountancy_transactions.id', $id)
            ->first();
    }

    public function getTransactionsWithContact($outletid)
    {
        return $this->select('
                accountancy_transactions.*,
                accountancy_contact.name as contact,
                CONCAT(u1.firstname," ",u1.lastname) AS created_by_name,
                CONCAT(u2.firstname," ",u2.lastname) AS updated_by_name,
                CONCAT(u3.firstname," ",u3.lastname) AS deleted_by_name
            ')
            ->join('accountancy_contact', 'accountancy_contact.id = accountancy_transactions.contact_id', 'left')
            ->join('users as u1', 'u1.id = accountancy_transactions.created_by', 'left')
            ->join('users as u2', 'u2.id = accountancy_transactions.updated_by', 'left')
            ->join('users as u3', 'u3.id = accountancy_transactions.deleted_by', 'left')
            ->where('accountancy_transactions.outletid', $outletid)
            ->orderBy('accountancy_transactions.date', 'DESC')
            ->findAll();
    }
}