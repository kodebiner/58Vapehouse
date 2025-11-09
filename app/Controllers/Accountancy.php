<?php

namespace App\Controllers;

class Accountancy extends BaseController
{
    public function dashboard()
    {
        // Calling Model

        // Populating data
        if (!empty($input)) {
            $daterange  = explode(' - ', $input);
            $startdate  = $daterange[0];
            $enddate    = $daterange[1];
        } else {
            // $startdate  = date('Y-m-1' . ' 00:00:00');
            // $enddate    = date('Y-m-t' . ' 23:59:59');
            $startdate  = date('Y-m-d') . ' 00:00:00';
            $enddate    = date('Y-m-d') . ' 23:59:59';
        }
        
        // Parsing data to view
        $data                   = $this->data;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);
        $data['title']          = 'Dashboard - '.lang('Global.accountancyList');
        $data['description']    = 'Dashboard '.lang('Global.accountancyListDesc');

        return view('Views/accountancy/dashboard', $data);
    }

    public function transaction()
    {
        // Calling Model

        // Populating data
        if (!empty($input)) {
            $daterange  = explode(' - ', $input);
            $startdate  = $daterange[0];
            $enddate    = $daterange[1];
        } else {
            // $startdate  = date('Y-m-1' . ' 00:00:00');
            // $enddate    = date('Y-m-t' . ' 23:59:59');
            $startdate  = date('Y-m-d') . ' 00:00:00';
            $enddate    = date('Y-m-d') . ' 23:59:59';
        }
        
        // Parsing data to view
        $data                   = $this->data;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);
        $data['title']          = 'Tambah Transaksi - '.lang('Global.accountancyList');
        $data['description']    = 'Tambah Transaksi '.lang('Global.accountancyListDesc');

        return view('Views/accountancy/transaction', $data);
    }

    public function akuncoa()
    {
        // Calling Model

        // Populating data
        if (!empty($input)) {
            $daterange  = explode(' - ', $input);
            $startdate  = $daterange[0];
            $enddate    = $daterange[1];
        } else {
            // $startdate  = date('Y-m-1' . ' 00:00:00');
            // $enddate    = date('Y-m-t' . ' 23:59:59');
            $startdate  = date('Y-m-d') . ' 00:00:00';
            $enddate    = date('Y-m-d') . ' 23:59:59';
        }
        
        // Parsing data to view
        $data                   = $this->data;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);
        $data['title']          = 'Akun (COA) - '.lang('Global.accountancyList');
        $data['description']    = 'Akun (COA) '.lang('Global.accountancyListDesc');

        return view('Views/accountancy/akuncoa', $data);
    }

    public function asset()
    {
        // Calling Model

        // Populating data
        if (!empty($input)) {
            $daterange  = explode(' - ', $input);
            $startdate  = $daterange[0];
            $enddate    = $daterange[1];
        } else {
            // $startdate  = date('Y-m-1' . ' 00:00:00');
            // $enddate    = date('Y-m-t' . ' 23:59:59');
            $startdate  = date('Y-m-d') . ' 00:00:00';
            $enddate    = date('Y-m-d') . ' 23:59:59';
        }
        
        // Parsing data to view
        $data                   = $this->data;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);
        $data['title']          = 'Asset - '.lang('Global.accountancyList');
        $data['description']    = 'Asset '.lang('Global.accountancyListDesc');

        return view('Views/accountancy/asset', $data);
    }

    public function closingEntries()
    {
        // Calling Model

        // Populating data
        if (!empty($input)) {
            $daterange  = explode(' - ', $input);
            $startdate  = $daterange[0];
            $enddate    = $daterange[1];
        } else {
            // $startdate  = date('Y-m-1' . ' 00:00:00');
            // $enddate    = date('Y-m-t' . ' 23:59:59');
            $startdate  = date('Y-m-d') . ' 00:00:00';
            $enddate    = date('Y-m-d') . ' 23:59:59';
        }
        
        // Parsing data to view
        $data                   = $this->data;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);
        $data['title']          = 'Tutup Buku - '.lang('Global.accountancyList');
        $data['description']    = 'Tutup Buku '.lang('Global.accountancyListDesc');

        return view('Views/accountancy/closing-entries', $data);
    }

    public function contact()
    {
        // Calling Model

        // Populating data
        if (!empty($input)) {
            $daterange  = explode(' - ', $input);
            $startdate  = $daterange[0];
            $enddate    = $daterange[1];
        } else {
            // $startdate  = date('Y-m-1' . ' 00:00:00');
            // $enddate    = date('Y-m-t' . ' 23:59:59');
            $startdate  = date('Y-m-d') . ' 00:00:00';
            $enddate    = date('Y-m-d') . ' 23:59:59';
        }
        
        // Parsing data to view
        $data                   = $this->data;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);
        $data['title']          = 'Kontak - '.lang('Global.accountancyList');
        $data['description']    = 'Kontak '.lang('Global.accountancyListDesc');

        return view('Views/accountancy/contact', $data);
    }

    public function manualAccountingReconciliation()
    {
        // Calling Model

        // Populating data
        if (!empty($input)) {
            $daterange  = explode(' - ', $input);
            $startdate  = $daterange[0];
            $enddate    = $daterange[1];
        } else {
            // $startdate  = date('Y-m-1' . ' 00:00:00');
            // $enddate    = date('Y-m-t' . ' 23:59:59');
            $startdate  = date('Y-m-d') . ' 00:00:00';
            $enddate    = date('Y-m-d') . ' 23:59:59';
        }
        
        // Parsing data to view
        $data                   = $this->data;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);
        $data['title']          = 'Rekonsiliasi Transaksi Manual - '.lang('Global.accountancyList');
        $data['description']    = 'Rekonsiliasi Transaksi Manual '.lang('Global.accountancyListDesc');

        return view('Views/accountancy/manual-accounting-reconciliation', $data);
    }

    public function budgetting()
    {
        // Calling Model

        // Populating data
        if (!empty($input)) {
            $daterange  = explode(' - ', $input);
            $startdate  = $daterange[0];
            $enddate    = $daterange[1];
        } else {
            // $startdate  = date('Y-m-1' . ' 00:00:00');
            // $enddate    = date('Y-m-t' . ' 23:59:59');
            $startdate  = date('Y-m-d') . ' 00:00:00';
            $enddate    = date('Y-m-d') . ' 23:59:59';
        }
        
        // Parsing data to view
        $data                   = $this->data;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);
        $data['title']          = 'Budgetting - '.lang('Global.accountancyList');
        $data['description']    = 'Budgetting '.lang('Global.accountancyListDesc');

        return view('Views/accountancy/budegetting', $data);
    }

    public function transactionReport()
    {
        // Calling Model

        // Populating data
        if (!empty($input)) {
            $daterange  = explode(' - ', $input);
            $startdate  = $daterange[0];
            $enddate    = $daterange[1];
        } else {
            // $startdate  = date('Y-m-1' . ' 00:00:00');
            // $enddate    = date('Y-m-t' . ' 23:59:59');
            $startdate  = date('Y-m-d') . ' 00:00:00';
            $enddate    = date('Y-m-d') . ' 23:59:59';
        }
        
        // Parsing data to view
        $data                   = $this->data;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);
        $data['title']          = 'Transaksi - '.lang('Global.accountancyList');
        $data['description']    = 'Transaksi '.lang('Global.accountancyListDesc');

        return view('Views/accountancy/transaction-report', $data);
    }

    public function generalJournal()
    {
        // Calling Model

        // Populating data
        if (!empty($input)) {
            $daterange  = explode(' - ', $input);
            $startdate  = $daterange[0];
            $enddate    = $daterange[1];
        } else {
            // $startdate  = date('Y-m-1' . ' 00:00:00');
            // $enddate    = date('Y-m-t' . ' 23:59:59');
            $startdate  = date('Y-m-d') . ' 00:00:00';
            $enddate    = date('Y-m-d') . ' 23:59:59';
        }
        
        // Parsing data to view
        $data                   = $this->data;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);
        $data['title']          = 'Jurnal Umum - '.lang('Global.accountancyList');
        $data['description']    = 'Jurnal Umum '.lang('Global.accountancyListDesc');

        return view('Views/accountancy/general-journal', $data);
    }

    public function ledger()
    {
        // Calling Model

        // Populating data
        if (!empty($input)) {
            $daterange  = explode(' - ', $input);
            $startdate  = $daterange[0];
            $enddate    = $daterange[1];
        } else {
            // $startdate  = date('Y-m-1' . ' 00:00:00');
            // $enddate    = date('Y-m-t' . ' 23:59:59');
            $startdate  = date('Y-m-d') . ' 00:00:00';
            $enddate    = date('Y-m-d') . ' 23:59:59';
        }
        
        // Parsing data to view
        $data                   = $this->data;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);
        $data['title']          = 'Buku Besar - '.lang('Global.accountancyList');
        $data['description']    = 'Buku Besar '.lang('Global.accountancyListDesc');

        return view('Views/accountancy/ledger', $data);
    }

    public function trialBalance()
    {
        // Calling Model

        // Populating data
        if (!empty($input)) {
            $daterange  = explode(' - ', $input);
            $startdate  = $daterange[0];
            $enddate    = $daterange[1];
        } else {
            // $startdate  = date('Y-m-1' . ' 00:00:00');
            // $enddate    = date('Y-m-t' . ' 23:59:59');
            $startdate  = date('Y-m-d') . ' 00:00:00';
            $enddate    = date('Y-m-d') . ' 23:59:59';
        }
        
        // Parsing data to view
        $data                   = $this->data;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);
        $data['title']          = 'Neraca Saldo - '.lang('Global.accountancyList');
        $data['description']    = 'Neraca Saldo '.lang('Global.accountancyListDesc');

        return view('Views/accountancy/trial-balance', $data);
    }

    public function profiLossStatement()
    {
        // Calling Model

        // Populating data
        if (!empty($input)) {
            $daterange  = explode(' - ', $input);
            $startdate  = $daterange[0];
            $enddate    = $daterange[1];
        } else {
            // $startdate  = date('Y-m-1' . ' 00:00:00');
            // $enddate    = date('Y-m-t' . ' 23:59:59');
            $startdate  = date('Y-m-d') . ' 00:00:00';
            $enddate    = date('Y-m-d') . ' 23:59:59';
        }
        
        // Parsing data to view
        $data                   = $this->data;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);
        $data['title']          = 'Laba Rugi - '.lang('Global.accountancyList');
        $data['description']    = 'Laba Rugi '.lang('Global.accountancyListDesc');

        return view('Views/accountancy/profit-loss-statement', $data);
    }

    public function changesEquity()
    {
        // Calling Model

        // Populating data
        if (!empty($input)) {
            $daterange  = explode(' - ', $input);
            $startdate  = $daterange[0];
            $enddate    = $daterange[1];
        } else {
            // $startdate  = date('Y-m-1' . ' 00:00:00');
            // $enddate    = date('Y-m-t' . ' 23:59:59');
            $startdate  = date('Y-m-d') . ' 00:00:00';
            $enddate    = date('Y-m-d') . ' 23:59:59';
        }
        
        // Parsing data to view
        $data                   = $this->data;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);
        $data['title']          = 'Perubahan Modal - '.lang('Global.accountancyList');
        $data['description']    = 'Perubahan Modal '.lang('Global.accountancyListDesc');

        return view('Views/accountancy/change-equity', $data);
    }

    public function balanceSheet()
    {
        // Calling Model

        // Populating data
        if (!empty($input)) {
            $daterange  = explode(' - ', $input);
            $startdate  = $daterange[0];
            $enddate    = $daterange[1];
        } else {
            // $startdate  = date('Y-m-1' . ' 00:00:00');
            // $enddate    = date('Y-m-t' . ' 23:59:59');
            $startdate  = date('Y-m-d') . ' 00:00:00';
            $enddate    = date('Y-m-d') . ' 23:59:59';
        }
        
        // Parsing data to view
        $data                   = $this->data;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);
        $data['title']          = 'Neraca - '.lang('Global.accountancyList');
        $data['description']    = 'Neraca '.lang('Global.accountancyListDesc');

        return view('Views/accountancy/balance-sheet', $data);
    }

    public function cashFlow()
    {
        // Calling Model

        // Populating data
        if (!empty($input)) {
            $daterange  = explode(' - ', $input);
            $startdate  = $daterange[0];
            $enddate    = $daterange[1];
        } else {
            // $startdate  = date('Y-m-1' . ' 00:00:00');
            // $enddate    = date('Y-m-t' . ' 23:59:59');
            $startdate  = date('Y-m-d') . ' 00:00:00';
            $enddate    = date('Y-m-d') . ' 23:59:59';
        }
        
        // Parsing data to view
        $data                   = $this->data;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);
        $data['title']          = 'Arus Kas - '.lang('Global.accountancyList');
        $data['description']    = 'Arus Kas '.lang('Global.accountancyListDesc');

        return view('Views/accountancy/cash-flow', $data);
    }

    public function receiveablePayableAccount()
    {
        // Calling Model

        // Populating data
        if (!empty($input)) {
            $daterange  = explode(' - ', $input);
            $startdate  = $daterange[0];
            $enddate    = $daterange[1];
        } else {
            // $startdate  = date('Y-m-1' . ' 00:00:00');
            // $enddate    = date('Y-m-t' . ' 23:59:59');
            $startdate  = date('Y-m-d') . ' 00:00:00';
            $enddate    = date('Y-m-d') . ' 23:59:59';
        }
        
        // Parsing data to view
        $data                   = $this->data;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);
        $data['title']          = 'Hutang Piutang - '.lang('Global.accountancyList');
        $data['description']    = 'Hutang Piutang '.lang('Global.accountancyListDesc');

        return view('Views/accountancy/receiveable-payable-account', $data);
    }

    public function profile()
    {
        // Calling Model

        // Populating data
        if (!empty($input)) {
            $daterange  = explode(' - ', $input);
            $startdate  = $daterange[0];
            $enddate    = $daterange[1];
        } else {
            // $startdate  = date('Y-m-1' . ' 00:00:00');
            // $enddate    = date('Y-m-t' . ' 23:59:59');
            $startdate  = date('Y-m-d') . ' 00:00:00';
            $enddate    = date('Y-m-d') . ' 23:59:59';
        }
        
        // Parsing data to view
        $data                   = $this->data;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);
        $data['title']          = 'Profil - '.lang('Global.accountancyList');
        $data['description']    = 'Profil '.lang('Global.accountancyListDesc');

        return view('Views/accountancy/profile', $data);
    }

    public function company()
    {
        // Calling Model

        // Populating data
        if (!empty($input)) {
            $daterange  = explode(' - ', $input);
            $startdate  = $daterange[0];
            $enddate    = $daterange[1];
        } else {
            // $startdate  = date('Y-m-1' . ' 00:00:00');
            // $enddate    = date('Y-m-t' . ' 23:59:59');
            $startdate  = date('Y-m-d') . ' 00:00:00';
            $enddate    = date('Y-m-d') . ' 23:59:59';
        }
        
        // Parsing data to view
        $data                   = $this->data;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);
        $data['title']          = 'Perusahaan - '.lang('Global.accountancyList');
        $data['description']    = 'Perusahaan '.lang('Global.accountancyListDesc');

        return view('Views/accountancy/company', $data);
    }

    public function employee()
    {
        // Calling Model

        // Populating data
        if (!empty($input)) {
            $daterange  = explode(' - ', $input);
            $startdate  = $daterange[0];
            $enddate    = $daterange[1];
        } else {
            // $startdate  = date('Y-m-1' . ' 00:00:00');
            // $enddate    = date('Y-m-t' . ' 23:59:59');
            $startdate  = date('Y-m-d') . ' 00:00:00';
            $enddate    = date('Y-m-d') . ' 23:59:59';
        }
        
        // Parsing data to view
        $data                   = $this->data;
        $data['startdate']      = strtotime($startdate);
        $data['enddate']        = strtotime($enddate);
        $data['title']          = 'Karyawan - '.lang('Global.accountancyList');
        $data['description']    = 'Karyawan '.lang('Global.accountancyListDesc');

        return view('Views/accountancy/employee', $data);
    }
}
