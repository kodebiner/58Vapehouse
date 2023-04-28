<!doctype html>
<html dir="ltr "lang="<?=$lang?>" vocab="http://schema.org/">
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
                    <div class="uk-navbar-left uk-margin-large-left">
                        <a class="uk-navbar-item uk-logo" href="<?=base_url();?>" aria-label="<?=lang('Global.backHome')?>">
                            <?php if (($gconfig['logo'] != null) && ($gconfig['bizname'] != null)) { ?>
                                <img src="/img/<?=$gconfig['logo'];?>" alt="<?=$gconfig['bizname'];?>" width="70" height="70" style="aspect-ratio: 1/1;">
                            <?php } else { ?>
                                <img src="/img/binary111-logo-icon.svg" alt="PT. Kodebiner Teknologi Indonesia" width="70" height="70" style="aspect-ratio: 1/1;" uk-svg>
                            <?php } ?>
                        </a>
                    </div>
                    <div class="uk-navbar-right">
                        <div class="uk-inline">
                            <a class="uk-linnk-reset" type="button">
                                <?php
                                if (!empty($account->photo)) {
                                    $profile = $account->photo;
                                } else {
                                    $profile = 'user.png';
                                }
                                ?>
                                <img src="img/<?= $profile ?>" class="uk-object-cover uk-object-position-top-center uk-border-circle" width="50" height="50" style="aspect-ratio: 1 / 1; border: 2px solid #000;" alt="<?=$fullname?>" />
                            </a>
                            <div class="uk-width-medium" uk-dropdown="mode: click">
                                <div class="uk-flex-middle uk-grid-small" uk-grid>
                                    <div class="uk-width-auto">
                                        <img src="img/<?= $profile ?>" class="uk-object-cover uk-object-position-top-center uk-border-circle" width="50" height="50" style="aspect-ratio: 1 / 1; border: 2px solid #000;" alt="<?=$fullname?>" /> 
                                    </div>
                                    <div class="uk-width-expand">
                                        <div class="uk-text-bold"><?=$fullname?></div>
                                        <div class="uk-text-meta" style="color: rgba(0, 0, 0, .5);"><?=$role;?></div>
                                    </div>
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
        <nav class="tm-sidebar-left">
            <ul class="uk-nav uk-nav-default tm-nav">
                <li class="tm-main-navbar">
                    <a class="uk-h4" href="<?= base_url('') ?>"><img src="img/layout/dashboard.svg"><?=lang('Global.dashboard');?></a>
                </li>
                <li class="tm-main-navbar">
                    <a class="uk-h4" href="<?= base_url('') ?>"><img src="img/layout/laporan.svg"><?=lang('Global.report');?></a>
                </li>
                <li class="tm-main-navbar">
                    <a class="uk-h4" href="<?= base_url('') ?>"><img src="img/layout/riwayat.svg"><?=lang('Global.trxHistory');?></a>
                </li>
                <li class="tm-main-navbar">
                    <a class="uk-h4" href="<?= base_url('') ?>"><img src="img/layout/payment.svg"><?=lang('Global.payment');?></a>
                </li>
                <li class="tm-main-navbar">
                    <a class="uk-h4" href="<?= base_url('') ?>"><img src="img/layout/product.svg"><?=lang('Global.product');?></a>
                </li>
                <li class="tm-main-navbar">
                    <a class="uk-h4" href="<?= base_url('') ?>"><img src="img/layout/calendar.svg"><?=lang('Global.reminder');?></a>
                </li>
                <li class="tm-main-navbar">
                    <a class="uk-h4" href="<?= base_url('user') ?>"><img src="img/layout/pegawai.svg"><?=lang('Global.employee');?></a>
                </li>
                <li class="tm-main-navbar">
                    <a class="uk-h4" href="<?= base_url('') ?>"><img src="img/layout/inventori.svg"><?=lang('Global.inventory');?></a>
                </li>
                <li class="tm-main-navbar">
                    <a class="uk-h4" href="<?= base_url('') ?>"><img src="img/layout/outlet.svg"><?=lang('Global.outlet');?></a>
                </li>
                <li class="tm-main-navbar">
                    <a class="uk-h4" href="<?= base_url('') ?>"><img src="img/layout/cash.svg"><?=lang('Global.cashManagement');?></a>
                </li>
                <li class="tm-main-navbar">
                    <a class="uk-h4" href="<?= base_url('') ?>"><img src="img/layout/pelanggan.svg"><?=lang('Global.customer');?></a>
                </li>
                <li class="tm-main-navbar">
                    <a class="uk-h4" href="<?= base_url('') ?>"><img src="img/layout/union.svg"><?=lang('Global.website');?></a>
                </li>
                <li class="tm-main-navbar">
                    <a class="uk-h4" href="<?= base_url('') ?>"><img src="img/layout/union.svg">Website</a>
                </li>
                <li class="tm-main-navbar">
                    <a class="uk-h4" href="<?= base_url('') ?>"><img src="img/layout/union.svg">Website</a>
                </li>
                <li class="tm-main-navbar">
                    <a class="uk-h4" href="<?= base_url('') ?>"><img src="img/layout/union.svg">Website</a>
                </li>
                <li class="tm-main-navbar">
                    <a class="uk-h4" href="<?= base_url('') ?>"><img src="img/layout/union.svg">Website</a>
                </li>
                <li class="tm-main-navbar">
                    <a class="uk-h4" href="<?= base_url('') ?>"><img src="img/layout/union.svg">Website</a>
                </li>
                <li class="tm-main-navbar">
                    <a class="uk-h4" href="<?= base_url('') ?>"><img src="img/layout/union.svg">Website</a>
                </li>
                <li class="tm-main-navbar">
                    <a class="uk-h4" href="<?= base_url('') ?>"><img src="img/layout/union.svg">Website</a>
                </li>
                <li class="tm-main-navbar">
                    <a class="uk-h4" href="<?= base_url('') ?>"><img src="img/layout/union.svg">Website</a>
                </li>
            </ul>
        </nav>
        <!-- Navbar Section end -->

        <!-- Main Section -->
        <main role="main">
            <div class="uk-padding-xlarge-left">
                <div class="uk-container uk-container-expand uk-padding-remove-right">
                    <div class="tm-main-card uk-panel uk-panel-scrollable" uk-height-viewport="offset-top: 80">
                        <?= $this->renderSection('main') ?>
                    </div>
                </div>
            </div>
        </main>
        <!-- Main Section end -->
        
        <!-- Footer Section -->
        <footer role="footer" class="uk-position-z-index" style="background-color:#1e87f0; color:#fff;" uk-sticky="position: bottom; start: 0; end: #body">
            <div class="uk-section-xsmall uk-text-center">
                Developed by<br/><a class="uk-link-reset" href="https://binary111.com">PT. Kodebiner Teknologi Indonesia</a>
            </div>
        </footer>        
        <!-- Footer Section end -->
    </body>
</html>
