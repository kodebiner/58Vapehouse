<?php

namespace App\Controllers;

use App\Models\AccountancyTransactionModel;
use App\Models\AccountancyJournalDetailModel;

class AccountancyTransaction extends BaseController
{
    protected $trxModel;
    protected $journalModel;
    protected $db;
    protected $data;

    public function __construct()
    {
        $this->trxModel     = new AccountancyTransactionModel();
        $this->journalModel = new AccountancyJournalDetailModel();
        $this->db           = \Config\Database::connect();
    }

    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $data                   = $this->data;
        $data['title']          = lang('Global.trxHistory');
        $data['description']    = lang('Global.trxHistoryListDesc');
        $typeList               =   [
                                        1 => 'Pemasukan', 
                                        2 => 'Pengeluaran',
                                        3 => 'Hutang',
                                        4 => 'Piutang',
                                        5 => 'Tanam Modal',
                                        6 => 'Tarik Modal',
                                        7 => 'Transfer Uang',
                                        8 => 'Pemasukan sebagai Piutang',
                                        9 => 'Pengeluaran sebagai Hutang'
                                    ];
        $transactions = $this->trxModel->getTransactionsWithContact();

        // Ambil journal untuk tiap transaksi
        foreach ($transactions as &$trx) {
            $trx['type']        = $typeList[$trx['type']] ?? '-';
            $trx['journals']    = $this->journalModel->getByTransaction($trx['id']);
        }

        $data['transactions'] = $transactions;

        return view('Views/accountancy/transaction-history', $data);
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */
    public function store()
    {
        $this->db->transStart();

        $amount   = $this->request->getPost('amount');
        $debitCoa = $this->request->getPost('debit');
        $creditCoa= $this->request->getPost('credit');

        if ($amount <= 0) {
            return redirect()->back()->with('error', 'Nominal tidak valid');
        }

        $trxData = [
            'contact_id'    => $this->request->getPost('contact'),
            'tax_id'        => $this->request->getPost('tax'),
            'outletid'      => $this->data['outletPick'],
            'source_id'     => 1,
            'source_module' => 'Manual Transaction',
            'date'          => $this->request->getPost('date'),
            'type'          => $this->request->getPost('type'),
            'amount'        => $amount,
            'note'          => $this->request->getPost('note'),
            'bunga'         => $this->request->getPost('bunga'),
            'due_date'      => $this->request->getPost('duedate'),
        ];

        $trxId = $this->trxModel->insert($trxData);

        if (!$trxId) {
            $this->db->transRollback();
            return redirect()->back()
                ->withInput()
                ->with('error', $this->trxModel->errors());
        }

        /*
        |--------------------------------------------------------------------------
        | INSERT JOURNAL DETAIL (DOUBLE ENTRY)
        |--------------------------------------------------------------------------
        */

        $this->journalModel->insert([
            'trx_a_id' => $trxId,
            'coa_a_id' => $debitCoa,
            'debit'    => $amount,
            'credit'   => 0,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $this->journalModel->insert([
            'trx_a_id' => $trxId,
            'coa_a_id' => $creditCoa,
            'debit'    => 0,
            'credit'   => $amount,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $this->db->transComplete();

        return redirect()->back()->with('success', 'Transaksi berhasil disimpan');
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */
    public function show($id)
    {
        $data['transaction'] = $this->trxModel->find($id);
        $data['journals']    = $this->journalModel->getByTransaction($id);

        return view('accounting/transaction/show', $data);
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */
    public function edit($id)
    {
        $data['transaction'] = $this->trxModel->find($id);
        $data['journals']    = $this->journalModel->getByTransaction($id);

        return view('accounting/transaction/edit', $data);
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */
    public function update($id)
    {
        $this->db->transStart();

        $amount = $this->request->getPost('amount');

        $this->trxModel->update($id, [
            'date'   => $this->request->getPost('date'),
            'type'   => $this->request->getPost('type'),
            'amount' => $amount,
            'note'   => $this->request->getPost('note'),
            'bunga'  => $this->request->getPost('bunga'),
            'due_date' => $this->request->getPost('duedate'),
        ]);

        // hapus journal lama
        $this->journalModel->where('trx_a_id', $id)->delete();

        // insert ulang journal
        $this->journalModel->insert([
            'trx_a_id' => $id,
            'coa_a_id' => $this->request->getPost('debit'),
            'debit'    => $amount,
            'credit'   => 0,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $this->journalModel->insert([
            'trx_a_id' => $id,
            'coa_a_id' => $this->request->getPost('credit'),
            'debit'    => 0,
            'credit'   => $amount,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $this->db->transComplete();

        return redirect()->to('/accounting/transaction')
            ->with('success', 'Transaksi berhasil diperbarui');
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */
    public function delete($id)
    {
        $this->db->transStart();

        $this->journalModel->where('trx_a_id', $id)->delete();
        $this->trxModel->delete($id);

        $this->db->transComplete();

        return redirect()->to('/accounting/transaction')
            ->with('success', 'Transaksi berhasil dihapus');
    }
}