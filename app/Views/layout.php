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

        <!-- Extra Script Section -->
        <?= $this->renderSection('extraScript') ?>
        <!-- Extra Script Section end -->

    </head>
    <body style="background-color:#f3f3f3;">

        <!-- Header Section -->
        <header class="uk-navbar-container tm-navbar-container" uk-sticky="media: 960;">
            <div class="uk-container uk-container-expand">
                <div uk-navbar>
                    <?php if ($ismobile === true) { ?>
                        <div class="uk-navbar-left">
                            <a class="uk-navbar-toggle" href="#offcanvas" uk-navbar-toggle-icon uk-toggle role="button" aria-label="Open menu" style="color: #000;"></a>
                        </div>
                        <div class="uk-navbar-center">
                            <a class="uk-navbar-item uk-logo" href="<?=base_url();?>" aria-label="<?=lang('Global.backHome')?>">
                                <?php if (($gconfig['logo'] != null) && ($gconfig['bizname'] != null)) { ?>
                                    <img src="/img/<?=$gconfig['logo'];?>" alt="<?=$gconfig['bizname'];?>" width="70" height="70" style="aspect-ratio: 1/1;">
                                <?php } else { ?>
                                    <img src="/img/binary111-logo-icon.svg" alt="PT. Kodebiner Teknologi Indonesia" width="70" height="70" style="aspect-ratio: 1/1;">
                                <?php } ?>
                            </a>
                        </div>
                    <?php } else { ?>
                        <div class="uk-navbar-left">
                            <a class="uk-navbar-item uk-logo" href="<?=base_url();?>" aria-label="<?=lang('Global.backHome')?>">
                                <?php if (($gconfig['logo'] != null) && ($gconfig['bizname'] != null)) { ?>
                                    <img src="/img/<?=$gconfig['logo'];?>" alt="<?=$gconfig['bizname'];?>" width="70" height="70" style="aspect-ratio: 1/1;">
                                <?php } else { ?>
                                    <img src="/img/binary111-logo-icon.svg" alt="PT. Kodebiner Teknologi Indonesia" width="70" height="70" style="aspect-ratio: 1/1;">
                                <?php } ?>
                            </a>
                        </div>
                    <?php } ?>
                    <div class="uk-navbar-right">
                        <div class="uk-navbar-item uk-flex uk-flex-middle uk-inline">
                            <a class="uk-link-reset" type="button">
                                <?php
                                if (!empty($account->photo)) {
                                    $profile = $account->photo;
                                } else {
                                    $profile = 'user.png';
                                }
                                ?>
                                <img src="img/<?= $profile ?>" class="uk-object-cover uk-object-position-top-center uk-border-circle" width="40" height="40" style="aspect-ratio: 1 / 1; border: 2px solid #000;" alt="<?=$fullname?>" />
                            </a>
                            <div class="uk-width-medium" uk-dropdown="mode: click">
                                <div class="uk-flex-middle uk-grid-small" uk-grid>
                                    <div class="uk-width-auto">
                                        <img src="img/<?= $profile ?>" class="uk-object-cover uk-object-position-top-center uk-border-circle" width="40" height="40" style="aspect-ratio: 1 / 1; border: 2px solid #000;" alt="<?=$fullname?>" /> 
                                    </div>
                                    <div class="uk-width-expand">
                                        <div class="uk-h4 uk-margin-remove" style="color: #000;"><?=$fullname?></div>
                                        <div class="uk-text-meta" style="color: rgba(0, 0, 0, .5);"><?=$role;?></div>
                                    </div>
                                </div>
                                <hr style="border-top-color: rgba(0, 0, 0, .5);"/>
                                <div>
                                    <ul class="uk-nav uk-nav-default tm-nav">
                                        <li class="tm-main-navbar">
                                            <a href="account" class="uk-h5" style="color:#000;">
                                                <img src="img/layout/pelanggan.svg" /><?=lang('Global.userProfile')?>
                                            </a>
                                        </li>
                                        <?php if ($role === 'owner') { ?>
                                        <li class="tm-main-navbar">
                                            <a href="business" class="uk-h5" style="color:#000;">
                                                <img src="img/layout/outlet.svg" /><?=lang('Global.businessInfo')?>
                                            </a>
                                        </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                                <hr style="border-top-color: rgba(0, 0, 0, .5);"/>
                                <a class="uk-button uk-button-danger" href="logout"><?=lang('Global.logout')?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <!-- Header Section end -->

        <!-- Navbar Section -->
        <?php if ($ismobile === true) { ?>
            <div id="offcanvas" uk-offcanvas="mode: push; overlay: true">
                <div class="uk-offcanvas-bar" role="dialog" aria-modal="true">
                    <nav>
                        <ul class="uk-nav uk-nav-default tm-nav">
                            <li class="tm-main-navbar">
                                <a class="uk-h4 tm-h4" href="<?= base_url('') ?>"><img src="img/layout/dashboard.svg"><?=lang('Global.dashboard');?></a>
                            </li>
                            <li class="tm-main-navbar">
                                <a class="uk-h4 tm-h4" href="<?= base_url('') ?>"><img src="img/layout/laporan.svg"><?=lang('Global.report');?></a>
                            </li>
                            <li class="tm-main-navbar">
                                <a class="uk-h4 tm-h4" href="<?= base_url('') ?>"><img src="img/layout/riwayat.svg"><?=lang('Global.trxHistory');?></a>
                            </li>
                            <li class="tm-main-navbar">
                                <a class="uk-h4 tm-h4" href="<?= base_url('') ?>"><img src="img/layout/payment.svg"><?=lang('Global.payment');?></a>
                            </li>
                            <li class="tm-main-navbar">
                                <a class="uk-h4 tm-h4" href="<?= base_url('product') ?>"><img src="img/layout/product.svg"><?=lang('Global.product');?></a>
                            </li>
                            <li class="tm-main-navbar">
                                <a class="uk-h4 tm-h4" href="<?= base_url('variant') ?>"><img src="img/layout/product.svg"><?=lang('Global.variant');?></a>
                            </li>
                            <li class="tm-main-navbar">
                                <a class="uk-h4 tm-h4" href="<?= base_url('') ?>"><img src="img/layout/calendar.svg"><?=lang('Global.reminder');?></a>
                            </li>
                            <li class="tm-main-navbar">
                                <a class="uk-h4 tm-h4" href="<?= base_url('user') ?>"><img src="img/layout/pegawai.svg"><?=lang('Global.employee');?></a>
                            </li>
                            <li class="tm-main-navbar">
                                <a class="uk-h4 tm-h4" href="<?= base_url('') ?>"><img src="img/layout/inventori.svg"><?=lang('Global.inventory');?></a>
                            </li>
                            <li class="tm-main-navbar">
                                <a class="uk-h4 tm-h4" href="<?= base_url('outlet') ?>"><img src="img/layout/outlet.svg"><?=lang('Global.outlet');?></a>
                            </li>
                            <li class="tm-main-navbar">
                                <a class="uk-h4 tm-h4" href="<?= base_url('') ?>"><img src="img/layout/cash.svg"><?=lang('Global.cashManagement');?></a>
                            </li>
                            <li class="tm-main-navbar">
                                <a class="uk-h4 tm-h4" href="<?= base_url('customer') ?>"><img src="img/layout/pelanggan.svg"><?=lang('Global.customer');?></a>
                            </li>
                            <li class="tm-main-navbar">
                                <a class="uk-h4 tm-h4" href="<?= base_url('') ?>"><img src="img/layout/union.svg"><?=lang('Global.website');?></a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        <?php } else { ?>
            <nav class="tm-sidebar-left">
                <ul class="uk-nav uk-nav-default tm-nav">
                    <li class="tm-main-navbar">
                        <a class="uk-h4 tm-h4" href="<?= base_url('') ?>"><img src="img/layout/dashboard.svg"><?=lang('Global.dashboard');?></a>
                    </li>
                    <li class="tm-main-navbar">
                        <a class="uk-h4 tm-h4" href="<?= base_url('') ?>"><img src="img/layout/laporan.svg"><?=lang('Global.report');?></a>
                    </li>
                    <li class="tm-main-navbar">
                        <a class="uk-h4 tm-h4" href="<?= base_url('') ?>"><img src="img/layout/riwayat.svg"><?=lang('Global.trxHistory');?></a>
                    </li>
                    <li class="tm-main-navbar">
                        <a class="uk-h4 tm-h4" href="<?= base_url('') ?>"><img src="img/layout/payment.svg"><?=lang('Global.payment');?></a>
                    </li>
                    <li class="tm-main-navbar">
                        <a class="uk-h4 tm-h4" href="<?= base_url('product') ?>"><img src="img/layout/product.svg"><?=lang('Global.product');?></a>
                    </li>
                    <li class="tm-main-navbar">
                        <a class="uk-h4 tm-h4" href="<?= base_url('') ?>"><img src="img/layout/calendar.svg"><?=lang('Global.reminder');?></a>
                    </li>
                    <li class="tm-main-navbar">
                        <a class="uk-h4 tm-h4" href="<?= base_url('user') ?>"><img src="img/layout/pegawai.svg"><?=lang('Global.employee');?></a>
                    </li>
                    <li class="tm-main-navbar">
                        <a class="uk-h4 tm-h4" href="<?= base_url('') ?>"><img src="img/layout/inventori.svg"><?=lang('Global.inventory');?></a>
                    </li>
                    <li class="tm-main-navbar">
                        <a class="uk-h4 tm-h4" href="<?= base_url('outlet') ?>"><img src="img/layout/outlet.svg"><?=lang('Global.outlet');?></a>
                    </li>
                    <li class="tm-main-navbar">
                        <a class="uk-h4 tm-h4" href="<?= base_url('') ?>"><img src="img/layout/cash.svg"><?=lang('Global.cashManagement');?></a>
                    </li>
                    <li class="tm-main-navbar">
                        <a class="uk-h4 tm-h4" href="<?= base_url('customer') ?>"><img src="img/layout/pelanggan.svg"><?=lang('Global.customer');?></a>
                    </li>
                    <li class="tm-main-navbar">
                        <a class="uk-h4 tm-h4" href="<?= base_url('') ?>"><img src="img/layout/union.svg"><?=lang('Global.website');?></a>
                    </li>
                </ul>
            </nav>
        <?php } ?>
        <!-- Navbar Section end -->

        <!-- Main Section -->
        <main role="main">
            <?php
            if ($ismobile === true) {
                $mainPadding = '';
                $mainContainer = '';
                $mainCard = '';
            } else {
                $mainPadding = 'uk-padding-xlarge-left';
                $mainContainer = 'uk-container uk-container-expand uk-padding-remove-right';
                $mainCard = 'tm-main-card ';
            }
            ?>
            <div class="<?=$mainPadding?>">
                <div class="<?=$mainContainer?>">
                    <div class="<?=$mainCard?>uk-panel uk-panel-scrollable" style="background-color: #fff;" uk-height-viewport="offset-top: .uk-navbar-container; offset-bottom: .tm-footer;">
                        <?= $this->renderSection('main') ?>
                    </div>
                </div>
            </div>
            <!-- Footer Section -->
            <footer class="tm-footer" style="background-color:#f3f3f3;">
                <?php
                if ($ismobile === true) {
                    $footerPadding = '';
                    $footerContainer = '';
                } else {
                    $footerPadding = 'uk-padding-xlarge-left';
                    $footerContainer = 'uk-container uk-container-expand';
                }
                ?>
                <div class="<?=$footerPadding?>">
                    <div class="<?=$footerContainer?>">
                        <?php
                        function auto_copyright($year = 'auto'){
                            if(intval($year) == 'auto'){ $year = date('Y'); }
                            if(intval($year) == date('Y')){ echo intval($year); }
                            if(intval($year) < date('Y')){ echo intval($year) . ' - ' . date('Y'); }
                            if(intval($year) > date('Y')){ echo date('Y'); }
                        }
                        ?>
                        <?php if ($ismobile === true) { ?>
                            <div>
                                <div class="uk-margin-small uk-text-center">
                                    Copyright &copy; <?php auto_copyright("2023"); ?>
                                </div>
                                <div class="uk-margin-small uk-text-center">
                                    Developed by<br/><a href="https://binary111.com" target="_blank">PT. Kodebiner Teknologi Indonesia</a>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class=" uk-child-width-auto uk-flex-between uk-flex-middle" uk-grid>
                                <div class="uk-margin-left">                                    
                                    Copyright &copy; <?php auto_copyright("2023"); ?>
                                </div>
                                <div class="uk-text-right">
                                    Developed by<br/><a href="https://binary111.com" target="_blank">PT. Kodebiner Teknologi Indonesia</a>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </footer>
            <!-- Footer Section end -->
        </main>
        <!-- Main Section end -->
    </body>
</html>
