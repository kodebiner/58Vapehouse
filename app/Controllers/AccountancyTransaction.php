<?php

namespace App\Controllers;

use App\Models\AccountancyTransactionModel;
use App\Models\AccountancyJournalDetailModel;
use App\Models\AccountancyContactModel;
use App\Models\AccountancyCOAModel;

class AccountancyTransaction extends BaseController
{
    protected $trxModel;
    protected $journalModel;
    protected $contactModel;
    protected $coaModel;
    protected $db;
    protected $data;

    public function __construct()
    {
        $this->trxModel     = new AccountancyTransactionModel();
        $this->journalModel = new AccountancyJournalDetailModel();
        $this->contactModel = new AccountancyContactModel();
        $this->coaModel     = new AccountancyCOAModel();
        $this->db           = \Config\Database::connect();
    }

    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $data                = $this->data;
        $data['title']       = lang('Global.trxHistory');
        $data['description'] = lang('Global.trxHistoryListDesc');

        $typeList = [
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

        // Get Transaction
        $transactions = $this->trxModel->withDeleted()->getTransactionsWithContact($this->data['outletPick']);

        if (empty($transactions)) {
            $data['transactions'] = [];
            return view('Views/accountancy/transaction-history', $data);
        }

        // Get All Transaction ID
        $trxIds = array_column($transactions, 'id');

        // Get All Journal Detail by Transaction ID
        $journals = $this->journalModel->getByTransactions($trxIds);

        // Group Journal by Transaction ID
        $grouped = [];
        foreach ($journals as $journal) {
            $grouped[$journal['trx_a_id']][] = $journal;
        }

        // Sum Journal Debit & Credit per Transaction
        foreach ($transactions as &$trx) {

            $trx['type']     = $typeList[$trx['type']] ?? '-';
            $trx['journals'] = $grouped[$trx['id']] ?? [];

            $debitTotal  = 0;
            $creditTotal = 0;

            foreach ($trx['journals'] as $journal) {
                $debitTotal  += (float)$journal['debit'];
                $creditTotal += (float)$journal['credit'];
            }

            $trx['debit_total']  = $debitTotal;
            $trx['credit_total'] = $creditTotal;
        }

        $data['transactions']   = $transactions;
        $data['contacts']       = $this->contactModel->orderBy('name', 'ASC')->findAll();
        $data['coas']           = $this->coaModel
            ->select("
                accountancy_coa.*,
                CONCAT(
                    cat.cat_code, accountancy_coa.coa_code, ' - ',
                    accountancy_coa.name, ' - ',
                    outlet.name
                ) AS coa_full_name,
                CONCAT(
                    cat.cat_code, accountancy_coa.coa_code
                ) AS code
            ")
            ->join('accountancy_categories AS cat', 'cat.id = accountancy_coa.cat_a_id', 'left')
            ->join('outlet', 'outlet.id = accountancy_coa.outletid', 'left')
            ->where('accountancy_coa.outletid', $this->data['outletPick'])
            ->orderBy('accountancy_coa.coa_code', 'ASC')
            ->findAll();

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

        $validationRule = [
            'attachment' => [
                'label' => 'Lampiran',
                'rules' => 'permit_empty|max_size[attachment,2048]|ext_in[attachment,pdf,jpg,jpeg,png]'
            ],
        ];

        if (!$this->validate($validationRule)) {
            return redirect()->back()
                ->withInput()
                ->with('error', $this->validator->getErrors());
        }

        $file = $this->request->getFile('attachment');
        $attachmentName = null;

        if ($file && $file->isValid() && !$file->hasMoved()) {

            $attachmentName = $file->getRandomName();

            $file->move(FCPATH . 'uploads/transaction', $attachmentName);
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
            'attachment'    => $attachmentName,
            'created_by'    => $this->data['uid']
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

        $coas   = $this->request->getPost('coa');
        $debits = $this->request->getPost('debit');
        $credits= $this->request->getPost('credit');

        $totalDebit  = 0;
        $totalCredit = 0;

        // =========================
        // HANDLE ATTACHMENT
        // =========================
        $validationRule = [
            'attachment' => [
                'label' => 'Lampiran',
                'rules' => 'permit_empty|max_size[attachment,4096]|ext_in[attachment,pdf,jpg,jpeg,png]'
            ],
        ];

        if (!$this->validate($validationRule)) {
            return redirect()->back()
                ->withInput()
                ->with('error', $this->validator->getErrors());
        }

        $attachmentName = null;
        $file = $this->request->getFile('attachment');

        if ($file && $file->isValid() && !$file->hasMoved()) {

            $attachmentName = $file->getRandomName();
            $file->move(ROOTPATH . 'uploads/transaction', $attachmentName);

            $updateAttachment = [
                'attachment' => $attachmentName
            ];
        } else {
            $updateAttachment = [];
        }

        // =========================
        // UPDATE TRANSACTION
        // =========================
        $this->trxModel->update($id, array_merge([
            'date'      => $this->request->getPost('date'),
            'note'      => $this->request->getPost('note'),
            'contact_id'=> $this->request->getPost('contact'),
            'due_date'  => $this->request->getPost('duedate')
        ], $updateAttachment));

        // =========================
        // DELETE OLD JOURNAL
        // =========================
        $this->journalModel
            ->where('trx_a_id', $id)
            ->delete();
            
        // =========================
        // INSERT NEW JOURNAL
        // =========================
        foreach ($coas as $i => $coa) {
            $debit  = str_replace('.', '', $debits[$i]);
            $credit = str_replace('.', '', $credits[$i]);
            $debit  = $debit  ?: 0;
            $credit = $credit ?: 0;

            $totalDebit  += $debit;
            $totalCredit += $credit;

            if ($debit == 0 && $credit == 0) {
                continue;
            }

            $this->journalModel->insert([
                'trx_a_id'   => $id,
                'coa_a_id'   => $coa,
                'debit'      => $debit,
                'credit'     => $credit,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        // =========================
        // UPDATE TOTAL TRANSACTION
        // =========================
        $this->trxModel->update($id, [
            'amount' => $totalDebit,
            'updated_by' => user()->id
        ]);

        if ($totalDebit != $totalCredit) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Jurnal tidak balance');
        }

        $this->db->transComplete();

        return redirect()->back()->with('success', 'Transaksi berhasil diperbarui');
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */
    public function delete($id)
    {
        $this->db->transStart();

        // $this->journalModel->where('trx_a_id', $id)->delete();
        $this->trxModel->delete($id);

        $this->db->transComplete();

        return redirect()->back()->with('success', 'Transaksi berhasil dihapus');
    }
}