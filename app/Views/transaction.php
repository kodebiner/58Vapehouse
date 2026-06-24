<!doctype html>
<html dir="ltr "lang="<?=$lang?>" vocab="http://schema.org/" style="overflow-y: hidden;">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <base href="<?=base_url();?>">
        <title><?=$title?></title>
        <meta name="description" content="<?=$description?>">
        <meta name="author" content="PT. Kodebiner Teknologi Indonesia">
        <link rel="icon" href="favicon/favicon.ico">
        <link rel="apple-touch-icon" sizes="16x16" href="favicon/apple-icon-16x16.png">
        <link rel="apple-touch-icon" sizes="32x32" href="favicon/apple-icon-32x32.png">
        <link rel="apple-touch-icon" sizes="57x57" href="favicon/apple-icon-57x57.png">
        <link rel="apple-touch-icon" sizes="60x60" href="favicon/apple-icon-60x60.png">
        <link rel="apple-touch-icon" sizes="72x72" href="favicon/apple-icon-72x72.png">
        <link rel="apple-touch-icon" sizes="76x76" href="favicon/apple-icon-76x76.png">
        <link rel="apple-touch-icon" sizes="96x96" href="favicon/apple-icon-96x96.png">
        <link rel="apple-touch-icon" sizes="114x114" href="favicon/apple-icon-114x114.png">
        <link rel="apple-touch-icon" sizes="120x120" href="favicon/apple-icon-120x120.png">
        <link rel="apple-touch-icon" sizes="144x144" href="favicon/apple-icon-144x144.png">
        <link rel="apple-touch-icon" sizes="152x152" href="favicon/apple-icon-152x152.png">
        <link rel="apple-touch-icon" sizes="180x180" href="favicon/apple-icon-180x180.png">
        <link rel="apple-touch-icon" sizes="192x192" href="favicon/apple-icon-192x192.png">
        <link rel="manifest" href="favicon/manifest.json">
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-TileImage" content="favicon/ms-icon-144x144.png">
        <meta name="theme-color" content="#ffffff">
        <link rel="stylesheet" href="css/theme.css">
        <script src="js/uikit.min.js"></script>
        <script src="js/uikit-icons.min.js"></script>
        <link rel="stylesheet" href="css/code.jquery.com_ui_1.13.2_themes_base_jquery-ui.css">
        <script src="js/code.jquery.com_jquery-3.6.0.js"></script>
        <script src="js/code.jquery.com_ui_1.13.2_jquery-ui.js"></script>
        <script src="js/jquery.validate.min.js"></script>
        <script src="js/cdnjs.cloudflare.com_ajax_libs_webcamjs_1.0.25_webcam.min.js"></script>
        
        <style type="text/css">
            .dummyproduct{fill:#666666;}

            .ui-autocomplete {
                max-height: 500px;
                overflow-y: auto;
                /* prevent horizontal scrollbar */
                overflow-x: hidden;
                -ms-overflow-style: none;
                scrollbar-width: none;
            }

            .ui-autocomplete::-webkit-scrollbar {
                display: none;
            }
            /* IE 6 doesn't support max-height
            * we use height instead, but this forces the menu to always be this tall
            */
            * html .ui-autocomplete {
                height: 500px;
            }
        </style>

        <script>
            document.onreadystatechange = () => {
                if (document.readyState === 'complete') {
                    document.getElementById('pageload').removeAttribute('hidden');
                }
            };
        </script>

    </head>
    <body style="background-color: #363636;">

        <!-- Header Section -->
        <header class="uk-navbar-container tm-navbar-container" style="background-color:#000;">
            <div class="uk-container uk-container-expand uk-margin">
                <div class="uk-flex-middle" uk-navbar>

                    <!-- Navbar Left -->
                    <div class="uk-navbar-left">
                        <a class="uk-navbar-toggle" href="#offcanvas" uk-navbar-toggle-icon uk-toggle width="35" height="35" role="button" aria-label="Open menu" style="color: #fff;"></a>
                    </div>
                    <!-- Navbar Left End -->

                    <!-- Navbar Center -->
                    <div class="uk-navbar-center">
                        <a class="uk-navbar-item uk-logo" href="<?=base_url();?>" aria-label="<?=lang('Global.backHome')?>">
                            <?php if (($gconfig['logo'] != null) && ($gconfig['bizname'] != null)) { ?>
                                <img src="/img/<?=$gconfig['logo'];?>" alt="<?=$gconfig['bizname'];?>" style="height: 70px;">
                            <?php } else { ?>
                                <img src="/img/binary111-logo-icon.svg" alt="PT. Kodebiner Teknologi Indonesia" style="height: 70px;">
                            <?php } ?>
                        </a>
                    </div>
                    <!-- Navbar Center End -->
                    
                    <!-- Navbar Right -->
                    <?php if (!in_groups('investor')) : ?>
                        <div class="uk-navbar-right">
                            <div class="uk-child-width-auto uk-grid-divider" uk-grid>
                                <div>
                                    <a uk-icon="user" uk-toggle="target: #tambahmember"></a>
                                </div>
                                <div>
                                    <a uk-icon="cart" uk-toggle="target: #tambahdata"></a>
                                </div>
                            </div>
                        </div>
                    <?php endif ?>

                    <!-- Modal Add Member -->
                    <div uk-modal class="uk-flex-top" id="tambahmember">
                        <div class="uk-modal-dialog uk-margin-auto-vertical">
                            <div class="uk-modal-content">
                                <div class="uk-modal-header">
                                    <div class="uk-child-width-1-2" uk-grid>
                                        <div>
                                            <h5 class="uk-modal-title" id="tambahmember"><?= lang('Global.addCustomer') ?></h5>
                                        </div>
                                        <div class="uk-text-right">
                                            <button class="uk-modal-close uk-icon-button-delete" uk-icon="icon: close;" type="button"></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="uk-modal-body">
                                    <form class="uk-form-stacked" role="form" action="/customer/create" method="post">
                                        <?= csrf_field() ?>

                                        <div class="uk-margin-bottom">
                                            <label class="uk-form-label" for="name"><?= lang('Global.name') ?></label>
                                            <div class="uk-form-controls">
                                                <input type="text" class="uk-input <?php if (session('errors.name')) : ?>tm-form-invalid<?php endif ?>" id="name" name="name" placeholder="<?= lang('Global.name') ?>" required />
                                            </div>
                                        </div>

                                        <div class="uk-margin-bottom">
                                            <label class="uk-form-label" for="phone"><?= lang('Global.phone') ?></label>
                                            <div class="uk-form-controls">
                                                <div class="uk-inline uk-width-1-1">
                                                    <span class="uk-form-icon">+62</span>
                                                    <input class="uk-input <?php if (session('errors.phone')) : ?>tm-form-invalid<?php endif ?>" min="1" id="phone" name="phone" type="number" placeholder="<?= lang('Global.phone') ?>" aria-label="Not clickable icon" required />
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <hr>

                                        <div class="uk-margin">
                                            <button type="submit" class="uk-button uk-button-primary"><?= lang('Global.save') ?></button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Modal Add Member End -->
                </div>
            </div>
        </header>
        <!-- Header Section end -->

        <!-- Left Sidebar Section -->
        <div id="offcanvas" uk-offcanvas="mode: push; overlay: true">
            <div class="uk-offcanvas-bar" role="dialog" aria-modal="true">
                <nav>
                    <ul class="uk-nav uk-nav-default tm-nav uk-light" uk-nav>
                        <li class="tm-main-navbar <?=($uri->getSegment(1)==='dashboard')?'uk-active':''?>">
                            <a class="tm-h3" href="<?= base_url('dashboard') ?>"><img src="img/layout/dashboard.svg" uk-svg><?=lang('Global.dashboard');?></a>
                        </li>
                        <li class="tm-main-navbar uk-parent <?=($uri->getSegment(1)==='accountancy')&&($uri->getSegment(2)==='dashboard')?'uk-active':''?><?=($uri->getSegment(1)==='accountancy')&&($uri->getSegment(2)==='transaction')?'uk-active':''?><?=($uri->getSegment(1)==='accountancy')&&($uri->getSegment(2)==='akuncoa')?'uk-active':''?><?=($uri->getSegment(1)==='accountancy')&&($uri->getSegment(2)==='asset')?'uk-active':''?><?=($uri->getSegment(1)==='accountancy')&&($uri->getSegment(2)==='closing-entries')?'uk-active':''?><?=($uri->getSegment(1)==='accountancy')&&($uri->getSegment(2)==='contact')?'uk-active':''?><?=($uri->getSegment(1)==='accountancy')&&($uri->getSegment(2)==='manual-accounting-reconciliation')?'uk-active':''?><?=($uri->getSegment(1)==='accountancy')&&($uri->getSegment(2)==='budgetting')?'uk-active':''?><?=($uri->getSegment(1)==='accountancy')&&($uri->getSegment(2)==='transaction-report')?'uk-active':''?><?=($uri->getSegment(1)==='accountancy')&&($uri->getSegment(2)==='general-journal')?'uk-active':''?><?=($uri->getSegment(1)==='accountancy')&&($uri->getSegment(2)==='ledger')?'uk-active':''?><?=($uri->getSegment(1)==='accountancy')&&($uri->getSegment(2)==='trial-balance')?'uk-active':''?><?=($uri->getSegment(1)==='accountancy')&&($uri->getSegment(2)==='profit-loss-statement')?'uk-active':''?><?=($uri->getSegment(1)==='accountancy')&&($uri->getSegment(2)==='changes-equity')?'uk-active':''?><?=($uri->getSegment(1)==='accountancy')&&($uri->getSegment(2)==='balance-sheet')?'uk-active':''?><?=($uri->getSegment(1)==='accountancy')&&($uri->getSegment(2)==='cash-flow')?'uk-active':''?><?=($uri->getSegment(1)==='accountancy')&&($uri->getSegment(2)==='receiveable-payable-account')?'uk-active':''?><?=($uri->getSegment(1)==='accountancy')&&($uri->getSegment(2)==='operating-expenses')?'uk-active':''?><?=($uri->getSegment(1)==='accountancy')&&($uri->getSegment(2)==='financial-report')?'uk-active':''?><?=($uri->getSegment(1)==='accountancy')&&($uri->getSegment(2)==='profile')?'uk-active':''?><?=($uri->getSegment(1)==='accountancy')&&($uri->getSegment(2)==='company')?'uk-active':''?><?=($uri->getSegment(1)==='accountancy')&&($uri->getSegment(2)==='employee')?'uk-active':''?>">
                            <a class="tm-h3" href=""><span uk-icon="database"></span><?=lang('Global.accountancy');?><span uk-nav-parent-icon></span></a>
                            <ul class="uk-nav-sub">
                                <?php if ((in_groups('owner')) || (in_groups('supervisor'))) : ?>
                                    <li class="tm-h4 <?=($uri->getSegment(1)==='accountancy')&&($uri->getSegment(2)==='dashboard')?'uk-active':''?>">
                                        <a href="<?= base_url('accountancy/dashboard') ?>">Dashbaord</a>
                                    </li>
                                    <li class="tm-h4 <?= ($uri->getSegment(1)==='accountancy' && in_array($uri->getSegment(2),['transaction'])) ? 'uk-active' : '' ?>">
                                        <a href="#" uk-toggle="target: #dropdown-transaction"><?=lang('Global.transaction');?></a>
                                        <div id="dropdown-transaction" class="uk-dropdown uk-dropdown-right uk-padding-small" style="background: #000; color: #fff;" hidden>
                                            <ul class="uk-nav uk-navbar-dropdown-nav">
                                                <li class="tm-h4 <?=($uri->getSegment(1)==='accountancy')&&($uri->getSegment(2)==='transaction')?'uk-active':''?>">
                                                    <a href="<?= base_url('accountancy/transaction') ?>">Tambah Transaksi</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>
                                    <li class="tm-h4 <?= ($uri->getSegment(1)==='accountancy' && in_array($uri->getSegment(2),['akuncoa','asset','closing-entries','contact','tax','manual-accounting-reconciliation','budgetting'])) ? 'uk-active' : '' ?>">
                                        <a href="#" uk-toggle="target: #dropdown-master-data">Master Data</a>
                                        <div id="dropdown-master-data" class="uk-dropdown uk-dropdown-right uk-padding-small" style="background: #000; color: #fff;" hidden>
                                            <ul class="uk-nav uk-navbar-dropdown-nav">
                                                <li class="tm-h4 <?=($uri->getSegment(1)==='accountancy')&&($uri->getSegment(2)==='akuncoa')?'uk-active':''?>">
                                                    <a href="<?= base_url('accountancy/akuncoa') ?>">Akun (COA)</a>
                                                </li>
                                                <li class="tm-h4 <?=($uri->getSegment(1)==='accountancy')&&($uri->getSegment(2)==='asset')?'uk-active':''?>">
                                                    <a href="<?= base_url('accountancy/asset') ?>">Asset</a>
                                                </li>
                                                <li class="tm-h4 <?=($uri->getSegment(1)==='accountancy')&&($uri->getSegment(2)==='closing-entries')?'uk-active':''?>">
                                                    <a href="<?= base_url('accountancy/closing-entries') ?>">Tutup Buku</a>
                                                </li>
                                                <li class="tm-h4 <?=($uri->getSegment(1)==='accountancy')&&($uri->getSegment(2)==='contact')?'uk-active':''?>">
                                                    <a href="<?= base_url('accountancy/contact') ?>">Kontak</a>
                                                </li>
                                                <li class="tm-h4 <?=($uri->getSegment(1)==='accountancy')&&($uri->getSegment(2)==='tax')?'uk-active':''?>">
                                                    <a href="<?= base_url('accountancy/tax') ?>">Pajak</a>
                                                </li>
                                                <li class="tm-h4 <?=($uri->getSegment(1)==='accountancy')&&($uri->getSegment(2)==='manual-accounting-reconciliation')?'uk-active':''?>">
                                                    <a href="<?= base_url('accountancy/manual-accounting-reconciliation') ?>">Rekonsiliasi Transaksi Manual</a>
                                                </li>
                                                <li class="tm-h4 <?=($uri->getSegment(1)==='accountancy')&&($uri->getSegment(2)==='budgetting')?'uk-active':''?>">
                                                    <a href="<?= base_url('accountancy/budgetting') ?>">Budgeting</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>
                                    <li class="tm-h4 <?= ($uri->getSegment(1)==='accountancy' && in_array($uri->getSegment(2),['transaction-report','general-journal','ledger','trial-balance','profit-loss-statement','changes-equity','balance-sheet','cash-flow','receiveable-payable-account','operating-expenses','financial-report'])) ? 'uk-active' : '' ?>">
                                        <a href="#" uk-toggle="target: #dropdown-report">Laporan</a>
                                        <div id="dropdown-report" class="uk-dropdown uk-dropdown-right uk-padding-small" style="background: #000; color: #fff;" hidden>
                                            <ul class="uk-nav uk-navbar-dropdown-nav">
                                                <li class="tm-h4 <?=($uri->getSegment(1)==='accountancy')&&($uri->getSegment(2)==='transaction-report')?'uk-active':''?>">
                                                    <a href="<?= base_url('accountancy/transaction-report') ?>">Transaksi</a>
                                                </li>
                                                <li class="tm-h4 <?=($uri->getSegment(1)==='accountancy')&&($uri->getSegment(2)==='general-journal')?'uk-active':''?>">
                                                    <a href="<?= base_url('accountancy/general-journal') ?>">Jurnal Umum</a>
                                                </li>
                                                <li class="tm-h4 <?=($uri->getSegment(1)==='accountancy')&&($uri->getSegment(2)==='ledger')?'uk-active':''?>">
                                                    <a href="<?= base_url('accountancy/ledger') ?>">Buku Besar</a>
                                                </li>
                                                <li class="tm-h4 <?=($uri->getSegment(1)==='accountancy')&&($uri->getSegment(2)==='trial-balance')?'uk-active':''?>">
                                                    <a href="<?= base_url('accountancy/trial-balance') ?>">Neraca Saldo</a>
                                                </li>
                                                <li class="tm-h4 <?=($uri->getSegment(1)==='accountancy')&&($uri->getSegment(2)==='profit-loss-statement')?'uk-active':''?>">
                                                    <a href="<?= base_url('accountancy/profit-loss-statement') ?>">Laba Rugi</a>
                                                </li>
                                                <li class="tm-h4 <?=($uri->getSegment(1)==='accountancy')&&($uri->getSegment(2)==='changes-equity')?'uk-active':''?>">
                                                    <a href="<?= base_url('accountancy/changes-equity') ?>">Perubahan Modal</a>
                                                </li>
                                                <li class="tm-h4 <?=($uri->getSegment(1)==='accountancy')&&($uri->getSegment(2)==='balance-sheet')?'uk-active':''?>">
                                                    <a href="<?= base_url('accountancy/balance-sheet') ?>">Neraca</a>
                                                </li>
                                                <li class="tm-h4 <?=($uri->getSegment(1)==='accountancy')&&($uri->getSegment(2)==='cash-flow')?'uk-active':''?>">
                                                    <a href="<?= base_url('accountancy/cash-flow') ?>">Arus Kas</a>
                                                </li>
                                                <li class="tm-h4 <?=($uri->getSegment(1)==='accountancy')&&($uri->getSegment(2)==='receiveable-payable-account')?'uk-active':''?>">
                                                    <a href="<?= base_url('accountancy/receiveable-payable-account') ?>">Hutang Piutang</a>
                                                </li>
                                                <li class="tm-h4 <?=($uri->getSegment(1)==='accountancy')&&($uri->getSegment(2)==='operating-expenses')?'uk-active':''?>">
                                                    <a href="<?= base_url('accountancy/operating-expenses') ?>">Beban Operasional</a>
                                                </li>
                                                <li class="tm-h4 <?=($uri->getSegment(1)==='accountancy')&&($uri->getSegment(2)==='financial-report')?'uk-active':''?>">
                                                    <a href="<?= base_url('accountancy/financial-report') ?>">Laporan Keuangan</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>
                                    <li class="tm-h4 <?= ($uri->getSegment(1)==='accountancy' && in_array($uri->getSegment(2),['profile','company','employee'])) ? 'uk-active' : '' ?>">
                                        <a href="#" uk-toggle="target: #dropdown-setting">Pengaturan</a>
                                        <div id="dropdown-setting" class="uk-dropdown uk-dropdown-right uk-padding-small" style="background: #000; color: #fff;" hidden>
                                            <ul class="uk-nav uk-navbar-dropdown-nav">
                                                <li class="tm-h4 <?=($uri->getSegment(1)==='accountancy')&&($uri->getSegment(2)==='profile')?'uk-active':''?>">
                                                    <a href="<?= base_url('accountancy/profile') ?>">Profil</a>
                                                </li>
                                                <li class="tm-h4 <?=($uri->getSegment(1)==='accountancy')&&($uri->getSegment(2)==='company')?'uk-active':''?>">
                                                    <a href="<?= base_url('accountancy/company') ?>">Perusahaan</a>
                                                </li>
                                                <li class="tm-h4 <?=($uri->getSegment(1)==='accountancy')&&($uri->getSegment(2)==='employee')?'uk-active':''?>">
                                                    <a href="<?= base_url('accountancy/employee') ?>">Karyawan</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>
                                <?php endif ?>
                            </ul>
                        </li>
                        <li class="tm-main-navbar uk-parent <?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='dailysell')?'uk-active':''?><?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='penjualan')?'uk-active':''?><?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='payment')?'uk-active':''?><?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='employe')?'uk-active':''?><?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='product')?'uk-active':''?><?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='category')?'uk-active':''?><?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='brand')?'uk-active':''?><?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='presence')?'uk-active':''?><?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='customer')?'uk-active':''?><?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='stockcategory')?'uk-active':''?><?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='sop')?'uk-active':''?>">
                            <a class="tm-h3" href=""><img src="img/layout/laporan.svg" uk-svg><?=lang('Global.report');?><span uk-nav-parent-icon></span></a>
                            <ul class="uk-nav-sub">
                                <li class="tm-h4 <?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='dailysell')?'uk-active':''?>">
                                    <a href="<?= base_url('report/dailysell') ?>">Laporan Penjualan Harian</a>
                                </li>
                                <?php if ((in_groups('owner')) || (in_groups('supervisor')) || (in_groups('investor'))) : ?>
                                    <li class="tm-h4 <?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='penjualan')?'uk-active':''?>">
                                        <a href="<?= base_url('report/penjualan') ?>"><?=lang('Global.salesreport');?></a>
                                    </li>
                                    <li class="tm-h4 <?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='payment')?'uk-active':''?>">
                                        <a href="<?= base_url('report/payment') ?>"><?=lang('Global.paymentreport');?></a>
                                    </li>
                                    <li class="tm-h4 <?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='product')?'uk-active':''?>">
                                        <a href="<?= base_url('report/product') ?>"><?=lang('Global.productreport');?></a>
                                    </li>
                                    <li class="tm-h4 <?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='category')?'uk-active':''?>">
                                        <a href="<?= base_url('report/category') ?>"><?=lang('Global.categoryreport');?></a>
                                    </li>
                                    <li class="tm-h4 <?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='brand')?'uk-active':''?>">
                                        <a href="<?= base_url('report/brand') ?>"><?=lang('Global.brandreport');?></a>
                                    </li>
                                <?php endif ?>
                                <li class="tm-h4 <?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='employe')?'uk-active':''?>">
                                    <a href="<?= base_url('report/employe') ?>"><?=lang('Global.employereport');?></a>
                                </li>
                                <li class="tm-h4 <?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='customer')?'uk-active':''?>">
                                    <a href="<?= base_url('report/customer') ?>"><?=lang('Global.customerreport');?></a>
                                </li>
                                <li class="tm-h4 <?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='sop')?'uk-active':''?>">
                                    <a href="<?= base_url('report/sop') ?>"><?=lang('Global.sopreport');?></a>
                                </li>
                                <li class="tm-h4 <?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='presence')?'uk-active':''?>">
                                    <a href="<?= base_url('report/presence') ?>"><?=lang('Global.presencereport');?></a>
                                </li>
                            </ul>
                        </li>
                        <li class="tm-main-navbar <?=($uri->getSegment(1)==='dayrep')?'uk-active':''?>">
                            <a class="tm-h3" href="<?= base_url('dayrep') ?>"><img src="img/layout/laporan.svg" uk-svg><?=lang('Global.dailyreport');?></a>
                        </li>
                        <?php if (!in_groups('investor')) : ?>
                            <li class="tm-main-navbar <?=($uri->getSegment(1)==='')?'uk-active':''?>">
                                <a class="tm-h3" href="<?= base_url('') ?>"><img src="img/layout/chart.svg" uk-svg><?=lang('Global.transaction');?></a>
                            </li>
                            <li class="tm-main-navbar <?=($uri->getSegment(1)==='trxhistory')?'uk-active':''?>">
                                <a class="tm-h3" href="<?= base_url('trxhistory') ?>"><img src="img/layout/riwayat.svg" uk-svg><?=lang('Global.trxHistory');?></a>
                            </li>
                            <li class="tm-main-navbar <?=($uri->getSegment(1)==='billhistory')?'uk-active':''?>">
                                <a class="tm-h3" href="<?= base_url('billhistory') ?>"><img src="img/layout/topup.svg" uk-svg><?=lang('Global.billhistory');?></a>
                            </li>
                            <li class="tm-main-navbar uk-parent <?=($uri->getSegment(1)==='debt')&&($uri->getSegment(2)==='')?'uk-active':''?><?=($uri->getSegment(1)==='debt')&&($uri->getSegment(2)==='debtpay')?'uk-active':''?>">
                                <a class="tm-h3" href=""><img src="img/layout/debt.svg" uk-svg><?=lang('Global.debt');?><span uk-nav-parent-icon></span></a>
                                <ul class="uk-nav-sub">
                                    <li class="tm-h4 <?=($uri->getSegment(1)==='debt')&&($uri->getSegment(2)==='')?'uk-active':''?>">
                                        <a href="<?= base_url('debt') ?>"><?=lang('Global.debtList');?></a>
                                    </li>
                                    <li class="tm-h4 <?=($uri->getSegment(1)==='debt')&&($uri->getSegment(2)==='debtpay')?'uk-active':''?>">
                                        <a href="<?= base_url('debt/debtpay') ?>"><?=lang('Global.debtInstallments');?></a>
                                    </li>
                                </ul>
                            </li>
                        <?php endif ?>
                        <?php if (in_groups('owner')) : ?>
                            <li class="tm-main-navbar <?=($uri->getSegment(1)==='sop')?'uk-active':''?>">
                                <a class="tm-h3" href="<?= base_url('sop') ?>"><img src="img/layout/sop.svg" uk-svg><?=lang('Global.sop');?></a>
                            </li>
                        <?php endif ?>
                        <?php if ((in_groups('owner')) || (in_groups('logistik'))) : ?>
                            <li class="tm-main-navbar <?=($uri->getSegment(1)==='product')?'uk-active':''?>">
                                <a class="tm-h3" href="<?= base_url('product') ?>"><img src="img/layout/product.svg" uk-svg><?=lang('Global.product');?></a>
                            </li>
                        <?php endif ?>
                        <?php if (!in_groups('investor')) : ?>
                            <li class="tm-main-navbar <?=($uri->getSegment(1)==='reminder')?'uk-active':''?>">
                                <a class="tm-h3" href="<?= base_url('reminder') ?>"><img src="img/layout/calendar.svg" uk-svg><?=lang('Global.reminder');?></a>
                            </li>
                            <li class="tm-main-navbar <?=($uri->getSegment(1)==='presence')?'uk-active':''?>">
                                <a class="tm-h3" href="<?= base_url('presence') ?>"><img src="img/layout/presensi.svg" uk-svg><?=lang('Global.presence');?></a>
                            </li>
                        <?php endif ?>
                        <?php if (in_groups((['owner','supervisor']))) : ?>
                            <li class="tm-main-navbar <?=($uri->getSegment(1)==='user')?'uk-active':''?>">
                                <a class="tm-h3" href="<?= base_url('user') ?>"><img src="img/layout/pegawai.svg" uk-svg><?=lang('Global.employee');?></a>
                            </li>
                        <?php endif ?>
                        <?php if (!in_groups('investor')) : ?>
                            <li class="tm-main-navbar uk-parent <?=($uri->getSegment(1)==='stock')&&($uri->getSegment(2)==='')?'uk-active':''?><?=($uri->getSegment(1)==='stock')&&($uri->getSegment(2)==='supplier')?'uk-active':''?><?=($uri->getSegment(1)==='stock')&&($uri->getSegment(2)==='purchase')?'uk-active':''?><?=($uri->getSegment(1)==='stockmove')?'uk-active':''?><?=($uri->getSegment(1)==='stockadjustment')?'uk-active':''?><?=($uri->getSegment(1)==='stockopname')?'uk-active':''?>">
                                <a class="tm-h3" href=""><img src="img/layout/inventori.svg" uk-svg><?=lang('Global.inventory');?><span uk-nav-parent-icon></span></a>
                                <ul class="uk-nav-sub">
                                    <?php if (in_groups('owner')) : ?>
                                        <li class="tm-h4 <?=($uri->getSegment(1)==='stock')&&($uri->getSegment(2)==='inventory')?'uk-active':''?>">
                                            <a href="<?= base_url('stock/inventory') ?>"><?=lang('Global.store');?></a>
                                        </li>
                                        <li class="tm-h4 <?=($uri->getSegment(1)==='stock')&&($uri->getSegment(2)==='supplier')?'uk-active':''?>">
                                            <a href="<?= base_url('stock/supplier') ?>"><?=lang('Global.supplier');?></a>
                                        </li>
                                    <?php endif ?>
                                    <?php if ((in_groups('owner')) || (in_groups('logistik'))) : ?>
                                        <li class="tm-h4 <?=($uri->getSegment(1)==='stock')&&($uri->getSegment(2)==='purchase')?'uk-active':''?>">
                                            <a href="<?= base_url('stock/purchase') ?>"><?=lang('Global.purchase');?></a>
                                        </li>
                                    <?php endif ?>
                                    <?php if (in_groups('owner')) : ?>
                                        <li class="tm-h4 <?=($uri->getSegment(1)==='stockadjustment')?'uk-active':''?>">
                                            <a href="<?= base_url('stockadjustment') ?>"><?=lang('Global.stockAdj');?></a>
                                        </li>
                                    <?php endif ?>
                                    <li class="tm-h4 <?=($uri->getSegment(1)==='stock')&&($uri->getSegment(2)==='')?'uk-active':''?>">
                                        <a href="<?= base_url('stock') ?>"><?=lang('Global.stock');?></a>
                                    </li>
                                    <li class="tm-h4 <?=($uri->getSegment(1)==='stockmove')?'uk-active':''?>">
                                        <a href="<?= base_url('stockmove') ?>"><?=lang('Global.stockMove');?></a>
                                    </li>
                                        <li class="tm-h4 <?=($uri->getSegment(1)==='stockopname')?'uk-active':''?>">
                                            <a href="<?= base_url('stockopname') ?>">Stock Opname</a>
                                        </li>
                                </ul>
                            </li>
                        <?php endif ?>
                        <?php if (in_groups(['owner','supervisor'])) : ?>
                            <li class="tm-main-navbar <?=($uri->getSegment(1)==='outlet')?'uk-active':''?>">
                                <a class="tm-h3" href="<?= base_url('outlet') ?>"><img src="img/layout/outlet.svg" uk-svg><?=lang('Global.outlet');?></a>
                            </li>
                        <?php endif ?>
                        <?php if (!in_groups('investor')) : ?>
                            <li class="tm-main-navbar <?=($uri->getSegment(1)==='cashinout')?'uk-active':''?>">
                                <a class="tm-h3" href="<?= base_url('cashinout') ?>"><img src="img/layout/cash.svg" uk-svg><?=lang('Global.cashinout');?></a>
                            </li>
                        <?php endif ?>
                        <?php if (in_groups('owner')) : ?>
                            <li class="tm-main-navbar uk-parent <?=($uri->getSegment(1)==='walletman')?'uk-active':''?><?=($uri->getSegment(1)==='walletmove')?'uk-active':''?><?=($uri->getSegment(1)==='payment')?'uk-active':''?>">
                                <a class="tm-h3" href=""><img src="img/layout/payment.svg" uk-svg><?=lang('Global.wallet');?><span uk-nav-parent-icon></span></a>
                                <ul class="uk-nav-sub">
                                    <li class="tm-h4 <?=($uri->getSegment(1)==='walletman')?'uk-active':''?>">
                                        <a href="<?= base_url('walletman') ?>"><?=lang('Global.walletManagement');?></a>
                                    </li>
                                    <li class="tm-h4 <?=($uri->getSegment(1)==='walletmove')?'uk-active':''?>">
                                        <a href="<?= base_url('walletmove') ?>"><?=lang('Global.walletMovement');?></a>
                                    </li>
                                    <li class="tm-h4 <?=($uri->getSegment(1)==='payment')?'uk-active':''?>">
                                        <a href="<?= base_url('payment') ?>"><?=lang('Global.payment');?></a>
                                    </li>
                                </ul>
                            </li>
                        <?php endif ?>
                        <?php if (!in_groups('investor')) : ?>
                            <li class="tm-main-navbar <?=($uri->getSegment(1)==='customer')?'uk-active':''?>">
                                <a class="tm-h3" href="<?= base_url('customer') ?>"><img src="img/layout/pelanggan.svg" uk-svg><?=lang('Global.customer');?></a>
                            </li>
                        <?php endif ?>
                        <?php if (in_groups(['owner','supervisor'])) : ?>
                            <li class="tm-main-navbar <?=($uri->getSegment(1)==='promo')?'uk-active':''?>">
                                <a class="tm-h3" href="<?= base_url('promo') ?>"><img src="img/layout/union.svg" uk-svg><?=lang('Global.website');?></a>
                            </li>
                        <?php endif ?>
                    </ul>
                </nav>
            </div>
        </div>
        <!-- Left Sidebar Section end -->

        <!-- Modal Detail Transaction -->
        <div uk-modal class="uk-flex-top uk-modal-container" id="tambahdata" >
            <div class="uk-modal-dialog" uk-overflow-auto>
                <div class="uk-modal-content">
                    <div class="uk-modal-header">
                        <div class="uk-child-width-1-2" uk-grid>
                            <div>
                                <h5 class="uk-modal-title" id="tambahdata" ><?=lang('Global.detailOrder');?></h5>
                            </div>
                            <div class="uk-text-right">
                                <button class="uk-modal-close uk-icon-button-delete" uk-icon="icon: close;" type="button"></button>
                            </div>
                        </div>
                    </div>
                    <form class="uk-form-stacked" name="order" action="pay/create" id="order" role="form" method="post">
                        <?= csrf_field() ?>
                        
                        <?php foreach ($outlets as $outlet){ 
                            if ($outlet['id'] === $this->data['outletPick']) { ?>
                                <input type="hidden" name="outlet" value="<?= $outlet['id'] ?>">
                            <?php } ?>
                        <?php } ?>
                        
                        <div class="uk-modal-body">
                            <div class="uk-margin-bottom">
                                <h4 class="uk-margin-remove"><?=lang('Global.customer')?></h4>
                                <div class="uk-margin-small">
                                    <div class="uk-width-1-1">
                                        <input class="uk-input" id="customername" name="customername" required />
                                        <input id="customerid" name="customerid" hidden data-valid="0" />
                                        <input type="text" id="customerphone" name="customerphone" hidden />
                                    </div>
                                </div>

                                <script>
                                    $(function () {
                                        $("#customername").autocomplete({
                                            source: function (request, response) {
                                                $.get("/api/member/search", { q: request.term }, function (data) {
                                                    const query = request.term.trim().toLowerCase();
                                                    const results = [];

                                                    // Tambahkan "Non Member" jika user ketik "non" atau mirip
                                                    if (query === 'non' || query.includes('non member')) {
                                                        results.push({
                                                            id: 0,
                                                            label: "Non Member",
                                                            value: "Non Member"
                                                        });
                                                    }

                                                    // Gabungkan dengan hasil dari server
                                                    results.push(...data);

                                                    response(results);
                                                });
                                            },

                                            select: function (event, ui) {
                                                $("#customerid").val(ui.item.id).attr("data-valid", "1");
                                                $("#customername").removeClass("uk-form-danger");

                                                if (ui.item.id === 0) {
                                                    // Non Member: reset poin & phone
                                                    $('#custpoin').attr('hidden', '');
                                                    $('#curpoin').text('Poin Anda: 0');
                                                    $('#poin').attr('max', 0).val(0);
                                                    $('#customerphone').val('');
                                                    totalcount();
                                                    return;
                                                }

                                                // Member: ambil detail
                                                $.get("/api/member/detail", { id: ui.item.id }, function (data) {
                                                    if (data.poin !== undefined) {
                                                        $('#custpoin').removeAttr('hidden');
                                                        $('#curpoin').text('Poin Anda: ' + data.poin);
                                                        $('#poin').attr('max', data.poin);
                                                        $('#customerphone').val(data.phone);
                                                    } else {
                                                        $('#custpoin').attr('hidden', '');
                                                        $('#curpoin').text('Poin Anda: 0');
                                                        $('#poin').attr('max', 0).val(0);
                                                    }
                                                    totalcount();
                                                });

                                                setTimeout(() => document.dispatchEvent(new Event("MemberSelected")), 100);
                                            },

                                            minLength: 2
                                        });

                                        $("#customername").on('input', function () {
                                            $("#customerid").val('').attr('data-valid', '0');
                                            $(this).addClass("uk-form-danger");
                                        });
                                    });
                                </script>

                            </div>

                            <input class="uk-input" id="customerphone" name="customerphone" hidden/>

                            <div id="products"></div>

                            <div class="uk-margin">
                                <h4 class="uk-h4 uk-margin-remove-bottom"><?=lang('Global.subtotal')?></h4>
                                <div class="uk-h4 uk-margin-remove-top" min="0" id="subtotal">0</div>
                            </div>

                            <div class="uk-margin" hidden>
                                <h4 class="uk-margin-remove"><?=lang('Global.discount')?></h4>
                                <div class="uk-margin-small uk-flex-middle" uk-grid>
                                    <div class="uk-width-expand">
                                        <input type="number" class="uk-input" id="discvalue" name="discvalue" min="0" max="0" placeholder="<?=lang('Global.discount')?>" onchange="totalcount()" />
                                    </div>
                                    <div class="switch-field uk-flex uk-flex-middle uk-width-auto">
                                        <input type="radio" id="radio-one" name="disctype" value="0" checked/>
                                        <label for="radio-one"><?=lang('Global.rp')?></label>
                                        <input type="radio" id="radio-two" name="disctype" value="1" />
                                        <label for="radio-two"><?=lang('Global.percent')?></label>
                                    </div>
                                    
                                </div>
                            </div>

                            <div id="paymentmethod" class="uk-margin">
                                <h4 class="uk-margin-remove"><?=lang('Global.paymethod')?></h4>
                                <div class="uk-form-controls uk-margin-small">
                                    <select class="uk-select" id="payment" name="payment">
                                        <option value="" selected disabled hidden>-- <?=lang('Global.paymethod')?> --</option>
                                        <!-- <option value="-1"></?= lang('Global.redeemPoint') ?></option> -->
                                        <?php
                                        foreach ($payments as $pay) {
                                            if (($pay['outletid'] === $outletPick) || ($pay['outletid'] === '0')) {
                                                echo '<option value="'.$pay['id'].'">'.$pay['name'].'</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div id="custpoin" class="uk-margin" hidden>
                                <h4 class="uk-margin-remove"><?=lang('Global.point')?></h4>
                                <h5 id="curpoin" class="uk-margin-remove"></h5>
                                <div class="uk-form-controls uk-margin-small">
                                    <input type="number" class="uk-input" id="poin" name="poin" min="0" max="" placeholder="<?=lang('Global.point')?>"/>
                                </div>
                            </div>

                            <div class="uk-margin" id="debtcontainer" hidden>
                                <div class="uk-child-width-auto" uk-grid>
                                    <div>
                                        <label class="uk-form-label" for="debt"><?=lang('Global.debt')?></label>
                                        <div class="uk-form-controls">
                                            <input type="number" class="uk-input uk-form-width-medium" id="debt" name="debt" value="0" readonly="readonly" />
                                        </div>
                                    </div>
                                    <div>
                                        <label class="uk-form-label" for="duedate"><?=lang('Global.duedate')?></label>
                                        <div class="uk-form-controls uk-inline">
                                            <span class="uk-form-icon uk-form-icon-flip" uk-icon="icon: calendar"></span>
                                            <input class="uk-input uk-form-width-medium" id="duedate" name="duedate" disabled />
                                            <script type="text/javascript">
                                                $( function() {
                                                    $( "#duedate" ).datepicker({
                                                        dateFormat: "yy-mm-dd",
                                                        minDate: 0,
                                                        maxDate: "+2w"
                                                    });
                                                } );
                                            </script>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="bills" class="uk-margin" hidden>
                                <h4><?=lang('Global.splitbill')?></h4>
                                <div class="uk-margin">
                                    <div class="uk-margin-small uk-form-controls">
                                        <select class="uk-select" id="firstpayment" name="firstpayment">
                                            <option value="" selected disabled hidden>-- <?=lang('Global.firstpaymet')?> --</option>
                                            <?php
                                            foreach ($payments as $pay) {
                                                if (($pay['outletid'] === $outletPick) || ($pay['outletid'] === '0')) {
                                                    echo '<option value="'.$pay['id'].'">'.$pay['name'].'</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="uk-margin-small uk-form-controls">
                                        <input type="number" class="uk-input" id="firstpay" name="firstpay" placeholder="<?=lang('Global.firstpay')?>" />
                                    </div>
                                </div>
                                <div class="uk-margin">
                                    <div class="uk-margin-small uk-form-controls">
                                        <select class="uk-select" id="secpayment" name="secpayment">
                                            <option value="" selected disabled hidden>-- <?=lang('Global.secpaymet')?> --</option>
                                            <?php
                                            foreach ($payments as $pay) {
                                                if (($pay['outletid'] === $outletPick) || ($pay['outletid'] === '0')) {
                                                    echo '<option value="'.$pay['id'].'">'.$pay['name'].'</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="uk-margin-small uk-form-controls">
                                        <input type="number" class="uk-input" id="secondpay"  name="secondpay" placeholder="<?=lang('Global.secpay')?>" />
                                    </div>
                                </div>
                            </div>
                            
                            <div class="uk-margin">
                                <a class="uk-margin-remove uk-text-bold uk-text-small uk-h4 uk-link-reset" id="splitbill"><?=lang('Global.wanttosplit')?></a>
                                <a class="uk-margin-remove uk-text-bold uk-text-small uk-h4 uk-link-reset" id="cancelsplit" hidden><?=lang('Global.cancelsplit')?></a>
                            </div>

                            <div class="uk-margin" id="amount">
                                <h4 class="uk-margin-remove"><?=lang('Global.amountpaid')?></h4>
                                <div class="uk-form-controls uk-margin-small">
                                    <input type="number" class="uk-input" id="value" name="value" min="0" placeholder="<?=lang('Global.amountpaid')?>" />
                                </div>
                            </div>

                            <div class="uk-margin" id="tax" hidden>
                                <?=lang('Global.vat')?> <?=$gconfig['ppn']?>%
                            </div>

                            <div class="uk-margin" id="outlet" hidden>
                                <div class="uk-form-controls uk-margin-small">
                                    <?php
                                        $outid = '';
                                        foreach ($outlets as $baseoutlet) {
                                            if ($baseoutlet['id'] === $outletPick) {
                                                $outid = $baseoutlet['id'];
                                            }
                                        }
                                    ?>
                                    <input type="number" class="uk-input" id="outlet" name="outlet" value="<?= $outid ?>" />
                                </div>
                            </div>
                        </div>
                        <div class="uk-modal-footer" style="border-top: 0;">
                            <div class="uk-margin">
                                <div class="uk-width-1-1 uk-text-center">
                                    <div class="uk-flex-top tm-h3"><?=lang('Global.total')?></div>
                                </div>
                                <div class="uk-width-1-1 uk-text-center">
                                    <div class="tm-h2 uk-text-bold" id="finalprice" value="0">Rp</div>
                                </div>
                            </div>
                            <div class="uk-margin uk-flex uk-flex-center">
                                <button type="submit" id="pay" class="uk-button uk-button-primary uk-button-large uk-text-center" style="border-radius: 8px; width: 260px;" disabled><?=lang('Global.pay')?></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Modal Detail Transaction End -->

        <!-- Main Section -->
        <main role="main">
            <div class="tm-main">
                <?php
                if ($ismobile === true) {
                    $mainContainer = '';
                    $mainCard = '';
                } else {
                    $mainContainer = 'uk-container uk-container-expand uk-padding-remove-horizontal';
                    $mainCard = 'tm-main-card ';
                }
                ?>
                <div class="<?=$mainContainer?>">
                    <div class="<?=$mainCard?>uk-panel uk-panel-trx-scrollable" style="background-color: #363636;">
                        <?php if ($outletPick === null) { ?>
                            <div class="uk-margin uk-flex uk-flex-center uk-child-width-1-1" uk-grid>
                                <!-- Alert Outlet -->
                                <div class="uk-margin-small">
                                    <div class="uk-width-1-6@m uk-card uk-card-default uk-card-small uk-card-body uk-container uk-container-expand">
                                        <div class="tm-h1 uk-text-center tm-text-large"><?=lang('Global.chooseoutlet')?></div>
                                    </div>
                                </div>
                                <!-- Alert Outlet End -->

                                <!-- OutletPick -->
                                <div class="uk-margin-remove">
                                    <div class="uk-width-1-6@m uk-container uk-container-expand">
                                        <div class="uk-navbar-item uk-flex uk-flex-middle uk-inline">
                                            <?php
                                                if ($outletPick === null)  {
                                                    $viewOutlet = lang('Global.allOutlets');
                                                } else {
                                                    foreach ($outlets as $outlet) {
                                                        if ($outletPick === $outlet['id']) {
                                                            $viewOutlet = $outlet['name'];
                                                        }
                                                    }
                                                }
                                            ?>
                                            <a class="tm-h4 tm-outlet" type="button"><img src="img/layout/union.svg" style="position: relative; top: -2px; margin-right: 5px;" /> <span class="tm-outlet-picker-selector"><?=$viewOutlet?></span> <span uk-icon="triangle-down"></span></a>
                                            <div class="uk-width-large tm-outlet-dropdown" uk-dropdown="mode: click;">
                                                <ul class="uk-list">
                                                <?php
                                                    $accesscount = count($baseoutlets);
                                                    $outletcount = count($outlets);
                                                    if ($accesscount === $outletcount) {
                                                        if ($outletPick === null) {
                                                            echo '<li class="uk-h4 tm-h4"><span uk-icon="triangle-right"></span> '.lang('Global.allOutlets').'</li>';
                                                        } else {
                                                            echo '<li class="uk-h4 tm-h4"><a href="outlet/pick/0" class="uk-link-reset">'.lang('Global.allOutlets').'</a></li>';
                                                        }
                                                    }
                                                    foreach ($outlets as $outlet) {
                                                        foreach ($baseoutlets as $access) {
                                                            if ($access['outletid'] === $outlet['id']) {
                                                                if ($outletPick === $outlet['id']) {
                                                                    echo '<li class="uk-h4 tm-h4"><span uk-icon="triangle-right"></span> '.$outlet['name'].'</li>';
                                                                } else {
                                                                    echo '<li class="uk-h4 tm-h4"><a href="outlet/pick/'.$outlet['id'].'" class="uk-link-reset">'.$outlet['name'].'</a></li>';
                                                                }
                                                            }
                                                        }
                                                    }
                                                ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- OutletPick End -->
                            </div>
                        <?php } elseif (empty($dailyreport)) { ?>
                            <div class="uk-margin uk-flex uk-flex-center uk-child-width-1-1" uk-grid>
                                <!-- Alert Open Store -->
                                <div class="uk-margin-small uk-flex uk-flex-center">
                                    <div class="uk-width-1-6@m uk-card uk-card-default uk-card-small uk-card-body">
                                        <div class="tm-h1 uk-text-center"><?=lang('Global.storeNotOpen')?></div>
                                    </div>
                                </div>
                                <!-- Alert Open Store End -->

                                <!-- Button To Manage Cash -->
                                <div class="uk-margin-remove uk-flex uk-flex-center">
                                    <button type="button" class="uk-width-1-6@m uk-button uk-button-primary" style="border-radius: 10px;" uk-toggle="target: #open"><?= lang('Global.open') ?></button>
                                </div>
                                <!-- Button To Manage Cash End -->

                                <!-- Modal Open -->
                                <div uk-modal class="uk-flex-top" id="open">
                                    <div class="uk-modal-dialog uk-margin-auto-vertical">
                                        <div class="uk-modal-content">
                                            <div class="uk-modal-header">
                                                <div class="uk-child-width-1-2" uk-grid>
                                                    <div>
                                                        <h3 class="tm-h2 uk-text-center"><?= lang('Global.initialcash') ?></h3>
                                                    </div>
                                                    <div class="uk-text-right">
                                                        <button class="uk-modal-close uk-icon-button-delete" uk-icon="icon: close;" type="button"></button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="uk-modal-body">
                                                <form class="uk-form-stacked" role="form" action="dayrep/open" method="post">
                                                    <?= csrf_field() ?>

                                                    <div class="uk-form-controls">
                                                        <input type="number" class="uk-input uk-form-large uk-text-center" style="border-radius: 10px;" id="initialcash" name="initialcash" placeholder="<?= lang('Global.initialcash') ?>" required />
                                                    </div>

                                                    <hr>

                                                    <div class="uk-margin">
                                                        <button type="submit" class="uk-button uk-button-primary uk-width-1-1" style="border-radius: 10px;"><?= lang('Global.open') ?></button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Modal Open End -->
                            </div>
                        <?php } elseif (!empty($closed)) { ?>
                            <!-- Alert Store Closed -->
                            <div class="uk-margin-small uk-flex uk-flex-center">
                                <div class="uk-width-1-6@m uk-card uk-card-default uk-card-small uk-card-body">
                                    <div class="tm-h1 uk-text-center"><?=lang('Global.storeClosed')?></div>
                                </div>
                            </div>
                            <!-- Alert Store Closed End -->
                        <?php } else { ?>
                            <?= view('Views/Auth/_message_block') ?>
                            <div class="uk-margin uk-flex uk-flex-center">
                                <div class="uk-width-1-1@m uk-card uk-card-default uk-card-small uk-card-body uk-padding-remove uk-margin-remove-top">
                                    <div class="tm-h4 uk-text-center">
                                        <?php
                                            foreach ($outlets as $baseoutlet) {
                                                if ($baseoutlet['id'] === $outletPick) {
                                                    echo $baseoutlet['name'];
                                                }
                                            }
                                        ?>
                                    </div>
                                </div>
                            </div>

                            <?php if (!in_groups('investor')) { ?>
                                <ul id="pageload" class="uk-switcher switcher-class" hidden>

                                    <!-- Catalog List -->
                                    <li>
                                        <script>
                                            function showVariant(id) {
                                                var modal = document.getElementById('modalVar');
                                                var data = { 'id' : id };
                                                $.ajax({
                                                    url:"stock/product",
                                                    method:"POST",
                                                    data: data,
                                                    dataType: "json",                                                
                                                    error:function() {
                                                        console.log('error', arguments);
                                                    },
                                                    success:function() {
                                                        console.log('success', arguments);
                                                        var variantList = document.getElementById('modalVarList');
                                                        var productName = document.getElementById('modalVarProduct');
                                                        productName.innerHTML = arguments[0][0]['product'];
                                                        while (variantList.firstElementChild) {
                                                            variantList.removeChild(variantList.firstElementChild);
                                                        }
                                                        variantarray = arguments[0];
                                                        for (k in variantarray) {
                                                            var variantContainer = document.createElement('div');
                                                            variantContainer.setAttribute('class', 'uk-margin-small uk-flex uk-flex-middle');
                                                            variantContainer.setAttribute('uk-grid', '');

                                                            var variantName = document.createElement('div');
                                                            variantName.setAttribute('class', 'uk-width-1-5');

                                                            var variantNameText = document.createElement('div');
                                                            variantNameText.setAttribute('class', 'uk-text-meta');
                                                            variantNameText.innerHTML = variantarray[k]['variant'];

                                                            var sellPrice = document.createElement('div');
                                                            sellPrice.setAttribute('class', 'uk-width-1-5');

                                                            var sellText = document.createElement('div');
                                                            sellText.setAttribute('class', 'uk-text-meta');
                                                            sellText.innerHTML = variantarray[k]['sellprice'];

                                                            var msrp = document.createElement('div');
                                                            msrp.setAttribute('class', 'uk-width-1-5');

                                                            var msrpText = document.createElement('div');
                                                            msrpText.setAttribute('class', 'uk-text-meta');
                                                            msrpText.innerHTML = variantarray[k]['msrp'];

                                                            var stock = document.createElement('div');
                                                            stock.setAttribute('class', 'uk-width-1-5');

                                                            var stockText = document.createElement('div');
                                                            stockText.setAttribute('class', 'uk-text-meta');
                                                            stockText.innerHTML = variantarray[k]['qty'];

                                                            var buttonContainer = document.createElement('div');
                                                            buttonContainer.setAttribute('class', 'uk-width-1-5 uk-text-center');

                                                            var button = document.createElement('a');
                                                            button.setAttribute('class', 'uk-icon-button');
                                                            button.setAttribute('uk-icon', 'cart');
                                                            button.setAttribute('onclick', 'createNewOrder('+variantarray[k]['id']+')');

                                                            variantName.appendChild(variantNameText);
                                                            variantContainer.appendChild(variantName);
                                                            sellPrice.appendChild(sellText);
                                                            variantContainer.appendChild(sellPrice);
                                                            msrp.appendChild(msrpText);
                                                            variantContainer.appendChild(msrp);
                                                            stock.appendChild(stockText);
                                                            variantContainer.appendChild(stock);
                                                            buttonContainer.appendChild(button);
                                                            variantContainer.appendChild(buttonContainer);
                                                            variantList.appendChild(variantContainer);
                                                        }
                                                        UIkit.modal(modal).show();
                                                    },
                                                });
                                            };

                                            function createNewOrder(variant) {
                                                var elemexist   = document.getElementById('product'+variant);
                                                var ItsMember   = document.getElementById('customerid');
                                                for (x in variantarray) {
                                                    if (variantarray[x]['id'] == variant) {
                                                        var count = 1;
                                                        var modalhide = document.getElementById('modalVar');
                                                            UIkit.modal(modalhide).hide();
                                                        if ( $( "#product"+variant ).length ) {
                                                            alert('<?=lang('Global.readyAdd');?>');
                                                        } else {
                                                            if (variantarray[x]['qty'] == '0') {
                                                                alert("<?=lang('Global.alertstock')?>");
                                                            } else {
                                                                let minstock = 1;
                                                                let minval = count;

                                                                const products = document.getElementById('products');
                                                                
                                                                const productgrid = document.createElement('div');
                                                                productgrid.setAttribute('id', 'product'+variant);
                                                                productgrid.setAttribute('class', 'uk-margin-small uk-flex-middle cart-item');
                                                                productgrid.setAttribute('uk-grid', '');

                                                                const addcontainer = document.createElement('div');
                                                                addcontainer.setAttribute('class', 'uk-width-1-6');
                                                                
                                                                const productqtyinputadd = document.createElement('div');
                                                                productqtyinputadd.setAttribute('id','addqty'+variant);
                                                                productqtyinputadd.setAttribute('class','tm-h2 pointerbutton uk-button uk-button-small uk-button-primary');
                                                                productqtyinputadd.setAttribute('onclick','handleCount('+variant+', 1)');
                                                                productqtyinputadd.innerHTML = '+';

                                                                const delcontainer = document.createElement('div');
                                                                delcontainer.setAttribute('class', 'uk-width-1-6');
                                                                
                                                                const productqtyinputdel = document.createElement('div');
                                                                productqtyinputdel.setAttribute('id','delqty'+variant);
                                                                productqtyinputdel.setAttribute('class','tm-h2 pointerbutton uk-button uk-button-small uk-button-danger');
                                                                productqtyinputdel.setAttribute('onclick','handleCount('+variant+', 0)');
                                                                productqtyinputdel.innerHTML = '-';

                                                                const quantitycontainer = document.createElement('div');
                                                                quantitycontainer.setAttribute('class', 'tm-h2 uk-width-1-6@m uk-width-1-3');

                                                                const productqty = document.createElement('div');                                               

                                                                const inputqty = document.createElement('input');
                                                                inputqty.setAttribute('type', 'number');
                                                                inputqty.setAttribute('id', "qty["+variant+"]");
                                                                inputqty.setAttribute('name', "qty["+variant+"]");
                                                                inputqty.setAttribute('class', 'uk-input uk-form-width-small');
                                                                inputqty.setAttribute('min', minstock);
                                                                inputqty.setAttribute('max', variantarray[x]['qty']);
                                                                inputqty.setAttribute('value', '1');

                                                                const namecontainer = document.createElement('div');
                                                                namecontainer.setAttribute('class', 'uk-width-1-3@m uk-width-1-2');

                                                                const productname = document.createElement('div');
                                                                productname.setAttribute('id', 'name'+variant);
                                                                productname.setAttribute('class', 'tm-h5');
                                                                productname.innerHTML = variantarray[x]['name'];

                                                                const pricecontainer = document.createElement('div');
                                                                pricecontainer.setAttribute('class', 'uk-width-1-6@m uk-width-1-2');
                                                                
                                                                const productprice = document.createElement('div');
                                                                productprice.setAttribute('id', 'price'+variant);
                                                                productprice.setAttribute('class', 'tm-h5');
                                                                productprice.setAttribute('name', 'price[]');
                                                                productprice.setAttribute('value', showprice());
                                                                productprice.innerHTML = showprice();

                                                                const varvaluecontainer = document.createElement('div');
                                                                varvaluecontainer.setAttribute('class', 'uk-margin-small uk-width-1-2');

                                                                const varpricediv = document.createElement('div');
                                                                varpricediv.setAttribute('class','uk-margin uk-margin-small uk-width-1-2');

                                                                const varpricelab = document.createElement('label');
                                                                varpricelab.setAttribute('class','uk-form-label uk-margin-remove uk-text-bold uk-text-small uk-h4' );

                                                                const varpricetext = document.createTextNode("Discount Variant");

                                                                const varpriceform = document.createElement('div');
                                                                varpriceform.setAttribute('class','uk-form-controls');
                                                                
                                                                const varprice = document.createElement('input');
                                                                varprice.setAttribute('class', 'uk-input uk-form-width-small varprice');
                                                                varprice.setAttribute('data-index', variant);
                                                                varprice.setAttribute('id', 'varprice'+variant);
                                                                varprice.setAttribute('placeholder', '0');
                                                                varprice.setAttribute('name', 'varprice['+variant+']');
                                                                varprice.setAttribute('type', 'number');
                                                                varprice.setAttribute('min', '0');

                                                                function showprice() {
                                                                    var qty = inputqty.value;
                                                                    var sellPrice = variantarray[x]['sellprice'];

                                                                    // Difine Discount
                                                                    var globaldisc = 0;
                                                                    var memberdisc = 0;

                                                                    // Global Discount
                                                                    <?php if ($gconfig['globaldisc'] != '0') {
                                                                        if ($gconfig['globaldisctype'] == '0') { ?>
                                                                            globaldisc = <?= $gconfig['globaldisc'] ?>;
                                                                        <?php } else { ?>
                                                                            globaldisc = sellPrice * <?= ((int)$gconfig['globaldisc'] / 100) ?>;
                                                                        <?php }
                                                                    } ?>

                                                                    // Member Discount
                                                                    if (ItsMember && ItsMember.dataset.valid === '1' && ItsMember.value != '0') {
                                                                        <?php if ($gconfig['memberdisc'] != '0') {
                                                                            if ($gconfig['memberdisctype'] == '0') { ?>
                                                                                memberdisc = <?= $gconfig['memberdisc'] ?>;
                                                                            <?php } else { ?>
                                                                                memberdisc = sellPrice * <?= ((int)$gconfig['memberdisc'] / 100) ?>;
                                                                            <?php } ?>

                                                                            if (memberdisc > <?= $gconfig['maxmemberdisc'] ?>) {
                                                                                memberdisc = <?= $gconfig['maxmemberdisc'] ?>;
                                                                            }
                                                                        <?php } ?>
                                                                    }

                                                                    var price = qty * (sellPrice - globaldisc - memberdisc);

                                                                    productprice.innerHTML = price;
                                                                    return price;
                                                                }

                                                                inputqty.onchange = function() {showprice()};
                                                                inputqty.onchange = function() {handleChangeCount(variant)};
                                                                varprice.onchange = function() {VarDisc(variant)};

                                                                var sellprice = document.createElement('input');
                                                                sellprice.setAttribute('id', 'sellprice'+variant);
                                                                sellprice.setAttribute('hidden', '');
                                                                sellprice.value = variantarray[x]['sellprice'];

                                                                addcontainer.appendChild(productqtyinputadd);
                                                                productqty.appendChild(inputqty);
                                                                quantitycontainer.appendChild(productqty);
                                                                delcontainer.appendChild(productqtyinputdel);
                                                                pricecontainer.appendChild(productprice);
                                                                namecontainer.appendChild(productname);
                                                                varvaluecontainer.appendChild(varpricediv);
                                                                varpricediv.appendChild(varpricelab);
                                                                varpricelab.appendChild(varpricetext);
                                                                varpricelab.appendChild(varpriceform);
                                                                varpriceform.appendChild(varprice);                                                                                        
                                                                productgrid.appendChild(delcontainer);
                                                                productgrid.appendChild(quantitycontainer);
                                                                productgrid.appendChild(addcontainer);
                                                                productgrid.appendChild(namecontainer);
                                                                productgrid.appendChild(pricecontainer);
                                                                productgrid.appendChild(pricecontainer);
                                                                productgrid.appendChild(varvaluecontainer);
                                                                products.appendChild(sellprice);
                                                                products.appendChild(productgrid);
                                                                
                                                                const MemberUse = document.getElementById('customerid');
                                                                if (MemberUse && MemberUse.dataset.valid === '1' && MemberUse.value !== '0') {
                                                                    applyMemberDiscountToAll(variant);
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            };

                                            function VarDisc(id) {
                                                var inputqty        = document.getElementById('qty['+id+']');
                                                var varprice        = document.getElementById('varprice'+id);
                                                var sellprice       = document.getElementById('sellprice'+id);
                                                var productprice    = document.getElementById('price'+id);
                                                var productgrid     = document.getElementById('product'+id);
                                                var inputMember     = document.getElementById('customerid');

                                                var globaldisc      = 0;
                                                var memberdisc      = 0;

                                                // Global Discount
                                                <?php if ($gconfig['globaldisc'] != '0') {
                                                    if ($gconfig['globaldisctype'] == '0') { ?>
                                                        globaldisc = <?= $gconfig['globaldisc'] ?>;
                                                    <?php } else { ?>
                                                        globaldisc = sellprice.value * <?= ((int)$gconfig['globaldisc'] / 100) ?>;
                                                    <?php }
                                                } ?>

                                                // Member Discount
                                                if (inputMember && inputMember.dataset.valid === '1' && inputMember.value != '0') {
                                                    <?php if ($gconfig['memberdisc'] != '0') {
                                                        if ($gconfig['memberdisctype'] == '0') { ?>
                                                            memberdisc = <?= $gconfig['memberdisc'] ?>;
                                                        <?php } else { ?>
                                                            memberdisc = sellprice.value * <?= ((int)$gconfig['memberdisc'] / 100) ?>;
                                                        <?php } ?>

                                                        // Validate Max Member Discount
                                                        if (memberdisc > <?= $gconfig['maxmemberdisc'] ?>) {
                                                            memberdisc = <?= $gconfig['maxmemberdisc'] ?>;
                                                        }
                                                    <?php } ?>
                                                }

                                                var subvalue    = sellprice.value - globaldisc - memberdisc - varprice.value;
                                                var totalprice  = subvalue * inputqty.value;

                                                productprice.innerHTML = totalprice;
                                            };

                                            function handleCount(id, type) {
                                                var inputqty        = document.getElementById('qty[' + id + ']');
                                                var varprice        = document.getElementById('varprice' + id);
                                                var sellprice       = document.getElementById('sellprice' + id);
                                                var productprice    = document.getElementById('price' + id);
                                                var productgrid     = document.getElementById('product' + id);
                                                var count           = parseInt(inputqty.value);
                                                var discvar         = parseFloat(varprice.value) || 0;
                                                var basePrice       = parseFloat(sellprice.value) || 0;
                                                var UsingMember     = document.getElementById('customerid');

                                                // Count Subvalue before Global Discount and Member Discount
                                                var subvalue = basePrice - discvar;

                                                // Count Global Discount
                                                var globaldisc = 0;
                                                <?php if ($gconfig['globaldisc'] != '0') {
                                                    if ($gconfig['globaldisctype'] == '0') { ?>
                                                        globaldisc = <?= $gconfig['globaldisc'] ?>;
                                                    <?php } else { ?>
                                                        globaldisc = subvalue * <?= ((int)$gconfig['globaldisc'] / 100) ?>;
                                                    <?php }
                                                } ?>

                                                // Count Member Discount
                                                var memberdisc = 0;
                                                if (UsingMember && UsingMember.dataset.valid === '1' && UsingMember.value != '0') {
                                                    <?php if ($gconfig['memberdisc'] != '0') {
                                                        if ($gconfig['memberdisctype'] == '0') { ?>
                                                            memberdisc = <?= $gconfig['memberdisc'] ?>;
                                                        <?php } else { ?>
                                                            memberdisc = subvalue * <?= ((int)$gconfig['memberdisc'] / 100) ?>;
                                                        <?php } ?>

                                                        if (memberdisc > <?= $gconfig['maxmemberdisc'] ?>) {
                                                            memberdisc = <?= $gconfig['maxmemberdisc'] ?>;
                                                        }
                                                    <?php } ?>
                                                }

                                                var totaldisc   = globaldisc + memberdisc;
                                                var finalprice  = (subvalue - totaldisc);

                                                if (type === 1) {
                                                    count++;
                                                    if (count > parseInt(inputqty.getAttribute('max'))) {
                                                        count = parseInt(inputqty.getAttribute('max'));
                                                        inputqty.value = count;
                                                        alert('<?= lang('Global.alertstock') ?>');
                                                    } else {
                                                        inputqty.value = count;
                                                    }
                                                } else if (type === 0) {
                                                    count--;
                                                    if (count <= 0) {
                                                        inputqty.value = '0';
                                                        inputqty.remove();
                                                        productgrid.remove();
                                                        return;
                                                    } else {
                                                        inputqty.value = count;
                                                    }
                                                }

                                                var total = finalprice * count;
                                                productprice.innerHTML  = total;
                                                productprice.value      = total;
                                            };

                                            function handleChangeCount(id) {
                                                var inputqty        = document.getElementById('qty[' + id + ']');
                                                var varprice        = document.getElementById('varprice' + id);
                                                var sellprice       = document.getElementById('sellprice' + id);
                                                var productprice    = document.getElementById('price' + id);
                                                var productgrid     = document.getElementById('product' + id);
                                                var count           = parseInt(inputqty.value);
                                                var discvar         = parseFloat(varprice.value) || 0;
                                                var basePrice       = parseFloat(sellprice.value) || 0;
                                                var subvalue        = basePrice - discvar;
                                                var MemberUse       = document.getElementById('customerid');

                                                // Global Discount
                                                var globaldisc = 0;
                                                <?php if ($gconfig['globaldisc'] != '0') {
                                                    if ($gconfig['globaldisctype'] == '0') { ?>
                                                        globaldisc = <?= $gconfig['globaldisc'] ?>;
                                                    <?php } else { ?>
                                                        globaldisc = subvalue * <?= ((int)$gconfig['globaldisc'] / 100) ?>;
                                                    <?php }
                                                } ?>

                                                // Member Discount
                                                var memberdisc = 0;
                                                if (MemberUse && MemberUse.dataset.valid === '1' && MemberUse.value != '0') {
                                                    <?php if ($gconfig['memberdisc'] != '0') {
                                                        if ($gconfig['memberdisctype'] == '0') { ?>
                                                            memberdisc = <?= $gconfig['memberdisc'] ?>;
                                                        <?php } else { ?>
                                                            memberdisc = subvalue * <?= ((int)$gconfig['memberdisc'] / 100) ?>;
                                                        <?php } ?>

                                                        if (memberdisc > <?= $gconfig['maxmemberdisc'] ?>) {
                                                            memberdisc = <?= $gconfig['maxmemberdisc'] ?>;
                                                        }
                                                    <?php } ?>
                                                }

                                                var totaldisc = globaldisc + memberdisc;
                                                var finalprice = (subvalue - totaldisc);

                                                if (count > parseInt(inputqty.getAttribute('max'))) {
                                                    inputqty.value = inputqty.getAttribute('max');
                                                    count = parseInt(inputqty.getAttribute('max'));
                                                    alert('<?= lang('Global.alertstock') ?>');
                                                } else if (count < 1) {
                                                    inputqty.value = '0';
                                                    inputqty.remove();
                                                    productgrid.remove();
                                                    return;
                                                } else {
                                                    inputqty.value = count;
                                                }

                                                var total = finalprice * count;
                                                productprice.innerHTML = total;
                                                productprice.value = total;
                                            };

                                            function applyMemberDiscountToAll(id) {
                                                var inputqty        = document.getElementById('qty[' + id + ']');
                                                var varprice        = document.getElementById('varprice' + id);
                                                var sellprice       = document.getElementById('sellprice' + id);
                                                var productprice    = document.getElementById('price' + id);
                                                var productgrid     = document.getElementById('product' + id);
                                                var count           = parseInt(inputqty.value);
                                                var discvar         = parseFloat(varprice.value) || 0;
                                                var basePrice       = parseFloat(sellprice.value) || 0;
                                                var subvalue        = basePrice - discvar;
                                                var MemberUse       = document.getElementById('customerid');

                                                // Global Discount
                                                var globaldisc = 0;
                                                <?php if ($gconfig['globaldisc'] != '0') {
                                                    if ($gconfig['globaldisctype'] == '0') { ?>
                                                        globaldisc = <?= $gconfig['globaldisc'] ?>;
                                                    <?php } else { ?>
                                                        globaldisc = subvalue * <?= ((int)$gconfig['globaldisc'] / 100) ?>;
                                                    <?php }
                                                } ?>

                                                // Member Discount
                                                var memberdisc = 0;
                                                if (MemberUse && MemberUse.dataset.valid === '1' && MemberUse.value != '0') {
                                                    <?php if ($gconfig['memberdisc'] != '0') {
                                                        if ($gconfig['memberdisctype'] == '0') { ?>
                                                            memberdisc = <?= $gconfig['memberdisc'] ?>;
                                                        <?php } else { ?>
                                                            memberdisc = subvalue * <?= ((int)$gconfig['memberdisc'] / 100) ?>;
                                                        <?php } ?>

                                                        if (memberdisc > <?= $gconfig['maxmemberdisc'] ?>) {
                                                            memberdisc = <?= $gconfig['maxmemberdisc'] ?>;
                                                        }
                                                    <?php } ?>
                                                }

                                                var totaldisc = globaldisc + memberdisc;
                                                var finalprice = (subvalue - totaldisc);

                                                if (count > parseInt(inputqty.getAttribute('max'))) {
                                                    inputqty.value = inputqty.getAttribute('max');
                                                    count = parseInt(inputqty.getAttribute('max'));
                                                    alert('<?= lang('Global.alertstock') ?>');
                                                } else if (count < 1) {
                                                    inputqty.value = '0';
                                                    inputqty.remove();
                                                    productgrid.remove();
                                                    return;
                                                } else {
                                                    inputqty.value = count;
                                                }

                                                var total = finalprice * count;
                                                productprice.innerHTML = total;
                                                productprice.value = total;
                                            };
                                        </script>
                                        
                                        <div class="uk-margin uk-text-center">
                                            <div class="uk-search uk-search-default uk-width-1-5@l uk-width-1-1">
                                                <span class="uk-form-icon" uk-icon="icon: search"></span>
                                                <input class="uk-input" type="text" placeholder="Search Item ..." id="prods" name="prods" aria-label="Not clickable icon" style="border-radius: 5px;">
                                            </div>
                                            <script type="text/javascript">
                                                $(function() {
                                                    var prodsList = [
                                                        {label: 'All Product', idx: '0'},
                                                        <?php
                                                            foreach ($products as $product) {
                                                                echo '{label:"'.$product['name'].'",idx:'.$product['id'].'},';
                                                            }
                                                        ?>
                                                    ];
                                                    $("#prods").autocomplete({
                                                        maxShowItems: 30,
                                                        source: prodsList,
                                                        select: function(e, i) {
                                                            if (i.item.idx != '0') {
                                                                <?php foreach ($products as $product) { ?>
                                                                    $("#CreateOrder<?=$product['id']?>").prop('hidden',true);
                                                                <?php } ?>
                                                                    $("#CreateOrder"+i.item.idx).prop('hidden',false);
                                                            } else {
                                                                <?php foreach ($products as $product) { ?>
                                                                    $("#CreateOrder<?=$product['id']?>").prop('hidden',false);
                                                                <?php } ?>
                                                            }
                                                            $("#prods").val('');
                                                            return false;
                                                        },
                                                        minLength: 1
                                                    });
                                                });
                                            </script>
                                        </div>

                                        <div uk-modal class="uk-flex-top" id="modalVar">
                                            <div class="uk-modal-dialog uk-margin-auto-vertical">
                                                <div class="uk-modal-container">
                                                    <div class="uk-modal-header">
                                                        <div uk-grid>
                                                            <div class="uk-width-5-6">
                                                                <div id="modalVarProduct" class="uk-modal-title tm-h2 uk-text-center">NAME</div>
                                                            </div>
                                                            <div class="uk-width-1-6 uk-text-right">
                                                                <button class="uk-modal-close uk-icon-button-delete" uk-icon="icon: close;" type="button"></button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="uk-modal-body">
                                                        <div class="uk-child-width-1-1" uk-grid>
                                                            <div id="">
                                                                <div class="uk-margin">
                                                                    <div class="uk-flex uk-flex-middle" uk-grid>
                                                                        <div class="uk-width-1-5">
                                                                            <h5 style="text-transform: uppercase;"><?= lang('Global.variant'); ?></h5>
                                                                        </div>
                                                                        <div class="uk-width-1-5">
                                                                            <h5 style="text-transform: uppercase;"><?= lang('Global.price'); ?></h5>
                                                                        </div>
                                                                        <div class="uk-width-1-5">
                                                                            <h5 style="text-transform: uppercase;"><?= lang('Global.suggestPrice'); ?></h5>
                                                                        </div>
                                                                        <div class="uk-width-1-5">
                                                                            <h5 style="text-transform: uppercase;"><?= lang('Global.stock'); ?></h5>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>                                                        
                                                            <div id="modalVarList" class="uk-margin">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="uk-child-width-1-3 uk-child-width-1-5@l" uk-grid uk-height-match="target: > div > .uk-card > .uk-card-body">
                                            <?php foreach ($products as $product) {
                                                $productName    = $product['name']; ?>

                                                <div id="CreateOrder<?= $product['id'] ?>">
                                                    <div class="uk-card uk-card-hover uk-card-default" onclick="showVariant(<?= $product['id'] ?>)">
                                                        <div class="uk-card-body uk-flex uk-flex-center uk-flex-middle">
                                                            <div class="tm-h4 uk-text-center uk-text-bolder"><?= $productName ?></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>                                    
                                    </li>
                                    <!-- End Catalog List -->

                                    <!-- Bundle List -->
                                    <li>
                                        <div class="uk-margin uk-text-center">
                                            <div class="uk-search uk-search-default uk-width-1-5@l uk-width-1-1">
                                                <span class="uk-form-icon" uk-icon="icon: search"></span>
                                                <input class="uk-input" type="text" placeholder="Search Item ..." id="bunds" name="bunds" aria-label="Not clickable icon" style="border-radius: 5px;">
                                                <input id="bundso" name="productid" hidden />
                                            </div>
                                            <script type="text/javascript">
                                                $(function() {
                                                    var bundsList = [
                                                        {label: 'All Bundle', idx: '0'},
                                                        <?php
                                                            foreach ($bundles as $bundle) {
                                                                echo '{label:"'.$bundle['name'].'",idx:'.$bundle['id'].'},';
                                                            }
                                                        ?>
                                                    ];
                                                    $("#bunds").autocomplete({
                                                        source: bundsList,
                                                        select: function(e, i) {
                                                            if (i.item.idx != '0') {
                                                                <?php foreach ($bundles as $bundle) { ?>
                                                                    $("#CreateOrder<?=$bundle['id']?>").prop('hidden',true);
                                                                <?php } ?>
                                                                $("#CreateOrder"+i.item.idx).prop('hidden',false);
                                                            } else {
                                                                <?php foreach ($bundles as $bundle) { ?>
                                                                    $("#CreateOrder<?=$bundle['id']?>").prop('hidden',false);
                                                                <?php } ?>
                                                            }
                                                        },
                                                        minLength: 1
                                                    });
                                                });
                                            </script>
                                        </div>
                                        <div class="uk-child-width-1-3 uk-child-width-1-5@l" uk-grid uk-height-match="target: > div > .uk-card > .uk-card-header">
                                            <?php foreach ($bundles as $bundle) {

                                                $BunName = $bundle['name'];
                                                if ($gconfig['globaldisc'] != '0') {
                                                    if ($gconfig['globaldisctype'] == '0') {
                                                        $BunPrice  = (Int)$bundle['price'] - (Int)$gconfig['globaldisc'];
                                                    } else {
                                                        $BunPrice  = (Int)$bundle['price'] - ((Int)$bundle['price'] * ((Int)$gconfig['globaldisc'] / 100));
                                                    }
                                                } else {
                                                    $BunPrice = $bundle['price'];
                                                }
                                            ?>
                                                <div id="CreateOrder<?= $bundle['id'] ?>">
                                                    <div class="uk-card uk-card-hover uk-card-default" onclick="createNewOrderBundle<?= $bundle['id'] ?>()">
                                                        <div class="uk-card-header">
                                                            <div class="tm-h1 uk-text-bolder uk-text-center"><?= $BunName; ?></div>
                                                        </div>
                                                        <div class="uk-card-body">
                                                            <div class="uk-height-small uk-flex uk-flex-middle uk-flex-center">
                                                                <div>
                                                                    <?php
                                                                        $i = 0;
                                                                        foreach ($bundleVariants as $variant) {
                                                                            if (($variant->bundleid === $bundle['id']) && ($variant->outletid === $outletPick)) {
                                                                                $i++;
                                                                                foreach ($products as $product) {
                                                                                    if ($product['id'] === $variant->productid) {
                                                                                        $CombName = $product['name'].' - '.$variant->name;
                                                                                        echo '<div class="tm-h5 uk-text-center uk-margin-small" id="combname">'.$CombName.'</div>';
                                                                                    }
                                                                                }
                                                                                if ($i === 1) {
                                                                                    $bundlestock = $variant->qty;
                                                                                }
                                                                            }
                                                                        }
                                                                    ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="uk-card-footer">
                                                            <div class="tm-h3 uk-text-center">
                                                                <div>Rp <?= $bundle['price']; ?>,-</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <script type="text/javascript">
                                                    var elemexist = document.getElementById('bundle<?= $bundle['id'] ?>');
                                                    function createNewOrderBundle<?= $bundle['id'] ?>() {
                                                        var count = 1;
                                                        if ( $( "#bundle<?= $bundle['id'] ?>" ).length ) {
                                                            alert('<?=lang('Global.readyAdd');?>');
                                                        } else {
                                                            <?php
                                                            if ($bundlestock === '0') {
                                                                echo 'alert("'.lang('Global.alertstock').'");';
                                                            } else {
                                                                echo 'let bstock = '.$bundlestock.';';
                                                            }
                                                            ?>
                                                            let minbstock = 1;
                                                            let minbval = count;

                                                            const products = document.getElementById('products');
                                                            
                                                            const bundlegrid = document.createElement('div');
                                                            bundlegrid.setAttribute('id', 'bundle<?= $bundle['id'] ?>');
                                                            bundlegrid.setAttribute('class', 'uk-margin-small');
                                                            bundlegrid.setAttribute('uk-grid', '');

                                                            const addbundlecontainer = document.createElement('div');
                                                            addbundlecontainer.setAttribute('class', 'uk-flex uk-flex-middle uk-width-1-6');
                                                            
                                                            const bunldeqtyinputadd = document.createElement('div');
                                                            bunldeqtyinputadd.setAttribute('id','addbqty<?= $bundle['id'] ?>');
                                                            bunldeqtyinputadd.setAttribute('class','tm-h2 pointerbutton uk-button uk-button-small uk-button-primary');
                                                            bunldeqtyinputadd.innerHTML = '+';

                                                            const delbundlecontainer = document.createElement('div');
                                                            delbundlecontainer.setAttribute('class', 'uk-flex uk-flex-middle uk-width-1-6');
                                                            
                                                            const bundleqtyinputdel = document.createElement('div');
                                                            bundleqtyinputdel.setAttribute('id','delbqty<?= $bundle['id'] ?>');
                                                            bundleqtyinputdel.setAttribute('class','tm-h2 pointerbutton uk-button uk-button-small uk-button-danger');
                                                            bundleqtyinputdel.innerHTML = '-';

                                                            const bundleqtycontainer = document.createElement('div');
                                                            bundleqtycontainer.setAttribute('class', 'tm-h2 uk-flex uk-flex-middle uk-width-1-6');

                                                            const bundleqty = document.createElement('div');                                               

                                                            const bundleinputqty = document.createElement('input');
                                                            bundleinputqty.setAttribute('type', 'number');
                                                            bundleinputqty.setAttribute('id', "bqty[<?= $bundle['id'] ?>]");
                                                            bundleinputqty.setAttribute('name', "bqty[<?= $bundle['id'] ?>]");
                                                            bundleinputqty.setAttribute('class', 'uk-input uk-form-width-xsmall');
                                                            bundleinputqty.setAttribute('min', minbstock);
                                                            bundleinputqty.setAttribute('max', bstock);
                                                            bundleinputqty.setAttribute('value', '1');
                                                            bundleinputqty.setAttribute('onchange', 'showbprice()');
                                                            
                                                            const handleIncrements = () => {
                                                                count++;
                                                                if (bundleinputqty.value == bstock) {
                                                                    bundleinputqty.value = bstock;
                                                                    count = bstock;
                                                                    alert('<?=lang('Global.alertstock')?>');
                                                                } else {
                                                                    bundleinputqty.value = count;
                                                                    var bprice = count * <?= $BunPrice ?>;
                                                                    bundleprice.innerHTML = bprice;
                                                                    bundleprice.value = bprice;
                                                                }
                                                            };
                                                            
                                                            const handleDecrements = () => {
                                                                count--;
                                                                if (bundleinputqty.value == '1') {
                                                                    bundleinputqty.value = '0';
                                                                    bundleinputqty.remove();
                                                                    bundlegrid.remove();
                                                                } else {
                                                                    bundleinputqty.value = count;
                                                                    var bprice = count * <?= $BunPrice ?>;
                                                                    bundleprice.innerHTML = bprice;
                                                                }
                                                            };

                                                            bunldeqtyinputadd.addEventListener("click", handleIncrements);
                                                            bundleqtyinputdel.addEventListener("click", handleDecrements);

                                                            const bundlenamecontainer = document.createElement('div');
                                                            bundlenamecontainer.setAttribute('class', 'uk-flex uk-flex-middle uk-width-1-3');

                                                            const bundlename = document.createElement('div');
                                                            bundlename.setAttribute('id', 'name<?= $bundle['id'] ?>');
                                                            bundlename.setAttribute('class', 'tm-h2');
                                                            bundlename.innerHTML = '<?= $BunName ?>';

                                                            const bpricecontainer = document.createElement('div');
                                                            bpricecontainer.setAttribute('class', 'uk-flex uk-flex-middle uk-width-1-6');
                                                            
                                                            const bundleprice = document.createElement('div');
                                                            bundleprice.setAttribute('id', 'bprice<?= $bundle['id'] ?>');
                                                            bundleprice.setAttribute('class', 'tm-h2');
                                                            bundleprice.setAttribute('name', 'price[]');
                                                            bundleprice.setAttribute('value', showbprice());
                                                            bundleprice.innerHTML = showbprice();

                                                            function showbprice() {
                                                                var bqty = bundleinputqty.value;
                                                                var bprice = bqty * <?= $BunPrice ?>;
                                                                return bprice;
                                                                bundleprice.innerHTML = bprice;
                                                            }

                                                            bundleinputqty.onchange = function() {showbprice()};

                                                            addbundlecontainer.appendChild(bunldeqtyinputadd);
                                                            bundleqty.appendChild(bundleinputqty);
                                                            bundleqtycontainer.appendChild(bundleqty);
                                                            delbundlecontainer.appendChild(bundleqtyinputdel);
                                                            bundlegrid.appendChild(delbundlecontainer);
                                                            bundlegrid.appendChild(bundleqtycontainer);
                                                            bundlegrid.appendChild(addbundlecontainer);
                                                            bundlenamecontainer.appendChild(bundlename);
                                                            bundlegrid.appendChild(bundlenamecontainer);
                                                            bpricecontainer.appendChild(bundleprice);
                                                            bundlegrid.appendChild(bpricecontainer);
                                                            products.appendChild(bundlegrid);
                                                        }
                                                    }
                                                </script>
                                            <?php } ?> 
                                        </div>            
                                    </li>
                                    <!-- End Bundle List -->
                                </ul>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </main>
        <!-- Main Section end -->

        <!-- Footer Section -->
        <footer class="tm-footer" style="background-color:#000;">
            <div class="uk-container uk-container-expand">
                <ul class="uk-flex-around tm-trx-tab" uk-tab uk-switcher="connect: .switcher-class; active: 1;">
                    <li>
                        <a uk-switcher-item="0">
                            <div width="30" height="30" uk-icon="file-text"></div>
                            <div class="uk-h4 uk-margin-small"><?=lang('Global.catalog');?></div>
                        </a>
                    </li>
                    <li>
                        <a uk-switcher-item="0" disabled>
                            <div width="30" height="30" uk-icon="file-edit"></div>
                            <div class="uk-h4 uk-margin-small"><?=lang('Global.bundle');?></div>
                        </a>
                    </li>
                </ul>
            </div>
        </footer>
        <!-- Footer Section end -->

        <script>
            document.addEventListener("MemberSelected", function () {
                document.querySelectorAll('.cart-item').forEach(function (el) {
                    const id = el.getAttribute('id').replace('product', '');
                    applyMemberDiscountToAll(id);
                });
            });
            
            var subtotalelem = document.getElementById('subtotal');
            var disctypeval = 0;
            var discount = 0;
            var poin = 0;
            var min = 0;

            let subtotalobserve = new MutationObserver(mutationRecords => {
                var prices = document.querySelectorAll("div[name='price[]']");
                
                var subarr = [];

                for (i = 0; i < prices.length; i++) {
                    price = Number(prices[i].innerText);
                    subarr.push(price);
                }
                if (subarr.length === 0) {
                    document.getElementById('subtotal').innerHTML = 0;
                } else {
                    var subtotal = subarr.reduce(function(a, b){ return a + b; });
                    document.getElementById('subtotal').innerHTML = subtotal;
                }
            });

            let totalobserve = new MutationObserver(totalcount);

            totalobserve.observe(subtotal, {
                childList: true, // observe direct children
                subtree: true, // and lower descendants too
                characterDataOldValue: true // pass old data to callback
            });

            subtotalobserve.observe(products, {
                childList: true, // observe direct children
                subtree: true, // and lower descendants too
                characterDataOldValue: true // pass old data to callback
            });

            document.getElementById('discvalue').addEventListener('change', totalcount);

            var disctype = document.getElementsByName('disctype');
            for (var i = 0; i < disctype.length; i++) {
                disctype[i].addEventListener('change', totalcount);
            }

            document.getElementById('poin').addEventListener('change', totalcount);
            document.getElementById('value').addEventListener('change', totalcount);
            document.getElementById('firstpay').addEventListener('change', totalcount);
            document.getElementById('secondpay').addEventListener('change', totalcount);

            function totalcount(e) {
                // Subtotal
                var subtotal = Number(document.getElementById('subtotal').innerText);

                // Discount
                var discvalue = document.getElementById('discvalue').value;

                for (i = 0; i < disctype.length; i++) {
                    if (disctype[i].checked) {
                        var disctypeval = disctype[i].value;
                    }
                }

                if (disctypeval == 0) {
                    var discount = discvalue;
                } else if (disctypeval == 1) {
                    if (discvalue > 100) {
                        alert('Harus kurang dari samadengan 100! Otomatis melakukan diskon Nominal.');
                        var discount = discvalue;
                    } else {
                        var discount = (discvalue/100)*subtotal;
                    }
                }

                // Poin
                var poin = document.getElementById('poin').value;


                // Member Discount
                var member = document.getElementById('customerid');

                // Tax
                var tax = (<?=(int)$gconfig['ppn']?>/100)*subtotal;
                
                // Count Total Price
                var totalprice = subtotal - discount - poin;

                // Tax
                var tax = (<?=(int)$gconfig['ppn']?>/100)*totalprice;

                // Count Paid Price
                var paidprice = totalprice + tax;

                // Pay button
                var buttonpay = document.getElementById('pay');
                if (paidprice >= 0) {
                    buttonpay.removeAttribute('disabled');
                    var printprice = paidprice;
                } else {
                    buttonpay.setAttribute('disabled', '');
                    var printprice = "Sorry Price To Low!";
                }

                var pay = document.getElementById('value').value;
                var firstpay = document.getElementById('firstpay').value;
                var secondpay = document.getElementById('secondpay').value;

                var pay = document.getElementById('value').value;
                var firstpay = document.getElementById('firstpay').value;
                var secondpay = document.getElementById('secondpay').value;

                // Debt
                if (document.getElementById('value') && pay) {
                    var paid = pay;
                } else if (document.getElementById('firstpay') && firstpay) {
                    var paid = Number(firstpay) + Number(secondpay);
                } else {
                    var paid = 0;
                }

                if (paid < printprice) {
                    document.getElementById('debt').value = printprice - paid;
                    document.getElementById('debtcontainer').removeAttribute('hidden');
                } else if (paid >= printprice) {
                    document.getElementById('debt').value = 0;
                    document.getElementById('debtcontainer').setAttribute('hidden', '');
                }

                if (document.getElementById('debt').value > 0) {
                    document.getElementById('duedate').setAttribute('required', '');
                    document.getElementById('duedate').removeAttribute('disabled');
                } else {
                    document.getElementById('duedate').removeAttribute('required');
                    document.getElementById('duedate').setAttribute('disabled', '');
                }

                // Printing Total Price
                var finalprice = document.getElementById('finalprice');
                finalprice.innerHTML = 'Rp. ' + printprice + ',-';

                // Setting Minimum & Maximum Paid Value
                if (member.value === '0' && document.getElementById('bills').hasAttribute('hidden')) {
                    document.getElementById('value').setAttribute('min', printprice);
                    document.getElementById('secondpay').removeAttribute('min');
                    document.getElementById('debtcontainer').setAttribute('hidden', '');
                    document.getElementById('debt').value = 0;
                    document.getElementById('duedate').removeAttribute('required');
                } else if (member.value === '0' && document.getElementById('amount').hasAttribute('hidden')) {
                    document.getElementById('secondpay').setAttribute('min', printprice - firstpay);
                    document.getElementById('value').removeAttribute('min');
                    document.getElementById('debtcontainer').setAttribute('hidden', '');
                    document.getElementById('debt').value = 0;
                    document.getElementById('duedate').removeAttribute('required');
                } else if (member.value !== '0' && document.getElementById('amount').hasAttribute('hidden')) {
                    document.getElementById('secondpay').removeAttribute('min');
                } else if (member.value !== '0' && document.getElementById('bills').hasAttribute('hidden')) {
                    document.getElementById('value').removeAttribute('min');
                }

                document.getElementById('firstpay').setAttribute('max', printprice);
                document.getElementById('secondpay').setAttribute('max', printprice - firstpay);

                // Max Discount
                if (disctypeval === '1') {
                    document.getElementById('discvalue').setAttribute('max', '100');
                } else if (disctypeval === '0') {
                    document.getElementById('discvalue').setAttribute('max', subtotal);
                }

                // Payment Requirement
                if (document.getElementById('bills').hasAttribute('hidden')) {
                    const debtValue = parseFloat(document.getElementById('debt').value) || 0;
                    const downPayment = parseFloat(document.getElementById('value').value) || 0;

                    if (debtValue > 0 && downPayment === 0) {
                        // Kasbon tanpa uang muka → payment TIDAK wajib
                        document.getElementById('payment').removeAttribute('required');
                        document.getElementById('value').removeAttribute('required');
                    } else {
                        // Bukan hutang, atau hutang dengan uang muka → payment wajib
                        document.getElementById('payment').setAttribute('required', '');
                        document.getElementById('value').setAttribute('required', '');
                    }

                    document.getElementById('firstpayment').removeAttribute('required');
                    document.getElementById('firstpay').removeAttribute('required');
                    document.getElementById('secpayment').removeAttribute('required');
                    document.getElementById('secondpay').removeAttribute('required');
                } else {
                    // Split Bill
                    document.getElementById('payment').removeAttribute('required');
                    document.getElementById('value').removeAttribute('required');
                    document.getElementById('firstpayment').setAttribute('required', '');
                    document.getElementById('firstpay').setAttribute('required', '');
                    document.getElementById('secpayment').setAttribute('required', '');
                    document.getElementById('secondpay').setAttribute('required', '');
                }

            }


            document.getElementById('splitbill').addEventListener("click", splitbill);
            document.getElementById('cancelsplit').addEventListener("click", cancelsplit);

            function splitbill() {
                document.getElementById('amount').setAttribute('hidden', '');
                document.getElementById('paymentmethod').setAttribute('hidden', '');
                document.getElementById('splitbill').setAttribute('hidden', '');
                document.getElementById('bills').removeAttribute('hidden');
                document.getElementById('cancelsplit').removeAttribute('hidden');
                document.getElementById('value').value = null;
                totalcount();
            }

            function cancelsplit() {
                document.getElementById('amount').removeAttribute('hidden');
                document.getElementById('paymentmethod').removeAttribute('hidden');
                document.getElementById('splitbill').removeAttribute('hidden');
                document.getElementById('bills').setAttribute('hidden', '');
                document.getElementById('cancelsplit').setAttribute('hidden', '');
                document.getElementById('firstpay').value = null;
                document.getElementById('secondpay').value = null;
                totalcount();
            }
            
            let isSubmitted = false;
            $(document).ready(function () {
                $("#order").on("submit", function (e) {
                    if (isSubmitted) return true;

                    // 1. Customer Validation
                    const isValid = $("#customerid").attr("data-valid");
                    if (isValid !== "1") {
                        e.preventDefault();
                        alert("Silakan pilih pelanggan dari daftar yang muncul.");
                        $("#customername").addClass("uk-form-danger");
                        return false;
                    }

                    // 2. Input Data
                    const debt = parseFloat(document.getElementById('debt').value) || 0;
                    const duedate = document.getElementById('duedate').value.trim();
                    const value = parseFloat(document.getElementById('value').value) || 0;
                    const paymentMethod = document.getElementById('payment').value;
                    const isSplitBill = $("#bills").is(":visible");

                    // 3. Split Bill
                    if (isSplitBill) {
                        const firstPayment = document.getElementById('firstpayment').value;
                        const secondPayment = document.getElementById('secpayment').value;

                        if (!firstPayment || firstPayment.trim() === '') {
                            e.preventDefault();
                            alert("Silakan pilih metode pembayaran pertama.");
                            document.getElementById('firstpayment').focus();
                            return false;
                        }

                        if (!secondPayment || secondPayment.trim() === '') {
                            e.preventDefault();
                            alert("Silakan pilih metode pembayaran kedua.");
                            document.getElementById('secpayment').focus();
                            return false;
                        }

                        if (debt > 0) {
                            e.preventDefault();
                            alert("Split bill tidak dapat digunakan bersamaan dengan transaksi hutang.");
                            return false;
                        }
                    } else {
                        // 4. Hutang (kasbon)
                        if (debt > 0) {
                            if (duedate === '') {
                                e.preventDefault();
                                alert("Karena pembayaran belum lunas, silakan isi tanggal jatuh tempo.");
                                document.getElementById('duedate').focus();
                                return false;
                            }

                            if (value > 0 && (!paymentMethod || paymentMethod.trim() === '')) {
                                e.preventDefault();
                                alert("Silakan pilih metode pembayaran untuk uang muka.");
                                document.getElementById('payment').focus();
                                return false;
                            }
                        } else {
                            // 5. Normal Payment (tanpa hutang & tanpa split)
                            if (!paymentMethod || paymentMethod.trim() === '') {
                                e.preventDefault();
                                alert("Silakan pilih metode pembayaran.");
                                document.getElementById('payment').focus();
                                return false;
                            }
                        }
                    }

                    // 6. Final validation pass
                    $("#customername").removeClass("uk-form-danger");
                    isSubmitted = true;
                });

                // Tombol Bayar
                $("#pay").click(function () {
                    validateAndSubmit("order", "/pay/create");
                });
            });

            function validateAndSubmit(order, actionUrl) {
                const form = document.getElementById(order);

                // Cek validasi HTML bawaan browser
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }

                // Set action dan trigger submit via event (agar handler tetap dipanggil)
                $(form).attr('action', actionUrl);
                form.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
            }

            // Auto-refresh: poll store status every 60 seconds (only when POS is active)
            <?php if ($outletPick !== null && !empty($dailyreport) && empty($closed)) { ?>
            (function startStoreHeartbeat() {
                const checkStatus = function () {
                    fetch('transaction/status')
                        .then(function (res) { return res.json(); })
                        .then(function (data) {
                            if (!data.status) {
                                location.reload();
                            }
                        })
                        .catch(function () {});
                };
                checkStatus();
                setInterval(checkStatus, 60000);
            })();
            <?php } ?>
        </script>
    </body>
</html>