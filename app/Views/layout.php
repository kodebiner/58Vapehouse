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
    <body style="background-color: #000;">

        <!-- Header Section -->
        <header class="uk-navbar-container tm-navbar-container" uk-sticky="media: 960;" style="background-color: #000;">
            <div class="uk-container uk-container-expand">
                <div uk-navbar>
                    <?php if ($ismobile === true) { ?>
                        <div class="uk-navbar-left">
                            <a class="uk-navbar-toggle" href="#offcanvas" uk-navbar-toggle-icon uk-toggle role="button" aria-label="Open menu" style="color: #fff;"></a>
                        </div>
                        <div class="uk-navbar-center">
                            <a class="uk-navbar-item uk-logo" href="<?=base_url();?>" aria-label="<?=lang('Global.backHome')?>">
                                <?php if (($gconfig['logo'] != null) && ($gconfig['bizname'] != null)) { ?>
                                    <img src="/img/<?=$gconfig['logo'];?>" alt="<?=$gconfig['bizname'];?>" style="height: 70px;">
                                <?php } else { ?>
                                    <img src="/img/binary111-logo-icon.svg" alt="PT. Kodebiner Teknologi Indonesia" style="height: 70px;">
                                <?php } ?>
                            </a>
                        </div>
                    <?php } else { ?>
                        <div class="uk-navbar-left">
                            <a class="uk-navbar-item uk-logo" href="<?=base_url();?>" aria-label="<?=lang('Global.backHome')?>">
                                <?php if (($gconfig['logo'] != null) && ($gconfig['bizname'] != null)) { ?>
                                    <img src="/img/<?=$gconfig['logo'];?>" alt="<?=$gconfig['bizname'];?>" style="height: 70px;">
                                <?php } else { ?>
                                    <img src="/img/binary111-logo-icon.svg" alt="PT. Kodebiner Teknologi Indonesia" style="height: 70px;">
                                <?php } ?>
                            </a>
                        </div>
                    <?php } ?>
                    <div class="uk-navbar-right">
                        <div id="tm-fullscreen" class="uk-navbar-item">
                            <a id="tm-open-fullscreen" class="tm-h4 tm-outlet" uk-icon="expand" onclick="openFullscreen()"></a>
                        </div>
                        <script type="text/javascript">
                            var elem = document.documentElement;

                            function openFullscreen() {
                                if (elem.requestFullscreen) {
                                    elem.requestFullscreen();
                                } else if (elem.webkitRequestFullscreen) { // Safari
                                    elem.webkitRequestFullscreen();
                                } else if (elem.msRequestFullscreen) { // IE11
                                    elem.msRequestFullscreen();
                                }

                                const fullscreenContainer = document.getElementById('tm-fullscreen');
                                const openButton = document.getElementById('tm-open-fullscreen');

                                // add close button
                                const closeButton = document.createElement('a');
                                closeButton.setAttribute('id', 'tm-close-fullscreen')
                                closeButton.setAttribute('class', 'tm-h4 tm-outlet');
                                closeButton.setAttribute('uk-icon', 'shrink');
                                closeButton.setAttribute('onclick', 'closeFullscreen()');
                                fullscreenContainer.appendChild(closeButton);

                                // remove open button
                                openButton.remove();
                            }

                            function closeFullscreen() {
                                if (document.exitFullscreen) {
                                    document.exitFullscreen();
                                } else if (document.webkitExitFullscreen) { // Safari
                                    document.webkitExitFullscreen();
                                } else if (document.msExitFullscreen) { // IE11
                                    document.msExitFullscreen();
                                }

                                const fullscreenContainer = document.getElementById('tm-fullscreen');
                                const closeButton = document.getElementById('tm-close-fullscreen');

                                // add open button
                                const openButton = document.createElement('a');
                                openButton.setAttribute('id', 'tm-open-fullscreen')
                                openButton.setAttribute('class', 'tm-h4 tm-outlet');
                                openButton.setAttribute('uk-icon', 'expand');
                                openButton.setAttribute('onclick', 'openFullscreen()');
                                fullscreenContainer.appendChild(openButton);

                                // remove close button
                                closeButton.remove();
                            }
                        </script>
                        <?php if ($ismobile === false) { ?>
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
                                <a class="tm-h4 tm-outlet" type="button"><img src="img/layout/union.svg" style="position: relative; top: -2px; margin-right: 5px;" /> <span class="tm-outlet-picker-selector"><?= $viewOutlet ?></span> <span uk-icon="triangle-down"></span></a>
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
                        <?php } ?>
                        <div class="uk-navbar-item uk-flex uk-flex-middle uk-inline">
                            <a class="uk-link-reset" type="button">
                                <?php
                                if (!empty($account->photo)) {
                                    $profile = 'img/profile/'.$account->photo;
                                } else {
                                    $profile = 'img/user.png';
                                }
                                ?>
                                <img src="<?= $profile ?>" class="uk-object-cover uk-object-position-top-center uk-border-circle" width="40" height="40" style="aspect-ratio: 1 / 1; border: 2px solid #000;" alt="<?=$fullname?>" />
                            </a>
                            <div class="uk-width-medium" uk-dropdown="mode: click">
                                <div class="uk-flex-middle uk-grid-small" uk-grid>
                                    <div class="uk-width-auto">
                                        <img src="<?= $profile ?>" class="uk-object-cover uk-object-position-top-center uk-border-circle" width="40" height="40" style="aspect-ratio: 1 / 1; border: 2px solid #000;" alt="<?=$fullname?>" /> 
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
                                            <a href="account" class="uk-h5" style="color: #000;">
                                                <img src="img/layout/pelanggan.svg" /><?=lang('Global.userProfile')?>
                                            </a>
                                        </li>
                                        <?php if ($role === 'owner') { ?>
                                        <li class="tm-main-navbar">
                                            <a href="business" class="uk-h5" style="color: #000;">
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
            <?php if ($ismobile === true) { ?>
            <div class="uk-container uk-container-expand">
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
            <?php } ?>
        </header>
        <!-- Header Section end -->

        <!-- Navbar Section -->
        <?php if ($ismobile === true) { ?>
            <div id="offcanvas" uk-offcanvas="mode: push; overlay: true">
                <div class="uk-offcanvas-bar" role="dialog" aria-modal="true">
                    <nav>
                        <ul class="uk-nav uk-nav-default tm-nav uk-light" uk-nav>
                            <li class="tm-main-navbar <?=($uri->getSegment(1)==='')?'uk-active':''?>">
                                <a class="tm-h3" href="<?= base_url('') ?>"><img src="img/layout/dashboard.svg" uk-svg><?=lang('Global.dashboard');?></a>
                            </li>          
                            <li class="tm-main-navbar uk-parent <?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='penjualan')?'uk-active':''?><?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='keuntungan')?'uk-active':''?><?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='payment')?'uk-active':''?><?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='employe')?'uk-active':''?><?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='product')?'uk-active':''?><?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='category')?'uk-active':''?><?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='bundle')?'uk-active':''?><?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='diskon')?'uk-active':''?><?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='presence')?'uk-active':''?><?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='customer')?'uk-active':''?><?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='stockcategory')?'uk-active':''?><?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='sop')?'uk-active':''?>">
                                <a class="tm-h3" href=""><img src="img/layout/laporan.svg" uk-svg><?=lang('Global.report');?><span uk-nav-parent-icon></span></a>
                                <ul class="uk-nav-sub">
                                    <?php if (in_groups('owner')) : ?>
                                        <li class="tm-h4 <?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='penjualan')?'uk-active':''?>">
                                            <a href="<?= base_url('report/penjualan') ?>"><?=lang('Global.salesreport');?></a>
                                        </li>
                                        <li class="tm-h4 <?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='keuntungan')?'uk-active':''?>">
                                            <a href="<?= base_url('report/keuntungan') ?>"><?=lang('Global.profitreport');?></a>
                                        </li>
                                        <li class="tm-h4 <?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='payment')?'uk-active':''?>">
                                            <a href="<?= base_url('report/payment') ?>"><?=lang('Global.paymentreport');?></a>
                                        </li>
                                        <li class="tm-h4 <?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='employe')?'uk-active':''?>">
                                            <a href="<?= base_url('report/employe') ?>"><?=lang('Global.employereport');?></a>
                                        </li>
                                        <li class="tm-h4 <?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='product')?'uk-active':''?>">
                                            <a href="<?= base_url('report/product') ?>"><?=lang('Global.productreport');?></a>
                                        </li>
                                        <li class="tm-h4 <?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='category')?'uk-active':''?>">
                                            <a href="<?= base_url('report/category') ?>"><?=lang('Global.categoryreport');?></a>
                                        </li>
                                        <li class="tm-h4 <?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='bundle')?'uk-active':''?>">
                                            <a href="<?= base_url('report/bundle') ?>"><?=lang('Global.bundlereport');?></a>
                                        </li>
                                        <li class="tm-h4 <?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='diskon')?'uk-active':''?>">
                                            <a href="<?= base_url('report/diskon') ?>"><?=lang('Global.discountreport');?></a>
                                        </li>
                                        <li class="tm-h4 <?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='customer')?'uk-active':''?>">
                                            <a href="<?= base_url('report/customer') ?>"><?=lang('Global.customerreport');?></a>
                                        </li>
                                        <li class="tm-h4 <?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='sop')?'uk-active':''?>">
                                            <a href="<?= base_url('report/sop') ?>"><?=lang('Global.sopreport');?></a>
                                        </li>
                                    <?php endif ?>
                                    <li class="tm-h4 <?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='presence')?'uk-active':''?>">
                                        <a href="<?= base_url('report/presence') ?>"><?=lang('Global.presencereport');?></a>
                                    </li>
                                </ul>
                            </li>
                            <li class="tm-main-navbar <?=($uri->getSegment(1)==='dayrep')?'uk-active':''?>">
                                <a class="tm-h3" href="<?= base_url('dayrep') ?>"><img src="img/layout/laporan.svg" uk-svg><?=lang('Global.dailyreport');?></a>
                            </li>
                            <li class="tm-main-navbar <?=($uri->getSegment(1)==='transaction')?'uk-active':''?>">
                                <a class="tm-h3" href="<?= base_url('transaction') ?>"><img src="img/layout/chart.svg" uk-svg><?=lang('Global.transaction');?></a>
                            </li>
                            <li class="tm-main-navbar <?=($uri->getSegment(1)==='trxhistory')?'uk-active':''?>">
                                <a class="tm-h3" href="<?= base_url('trxhistory') ?>"><img src="img/layout/riwayat.svg" uk-svg><?=lang('Global.trxHistory');?></a>
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
                            <li class="tm-main-navbar <?=($uri->getSegment(1)==='topup')?'uk-active':''?>">
                                <a class="tm-h3" href="<?= base_url('topup') ?>"><img src="img/layout/topup.svg" uk-svg><?=lang('Global.topup');?></a>
                            </li>
                            <?php if(in_groups('owner')) : ?>
                                <li class="tm-main-navbar <?=($uri->getSegment(1)==='sop')?'uk-active':''?>">
                                    <a class="tm-h3" href="<?= base_url('sop') ?>"><img src="img/layout/sop.svg" uk-svg><?=lang('Global.sop');?></a>
                                </li>
                                <li class="tm-main-navbar uk-parent <?=($uri->getSegment(1)==='product')?'uk-active':''?><?=($uri->getSegment(1)==='bundle')?'uk-active':''?>">
                                    <a class="tm-h3" href=""><img src="img/layout/product.svg" uk-svg><?=lang('Global.product');?><span uk-nav-parent-icon></span></a>
                                    <ul class="uk-nav-sub">
                                        <li class="tm-h4 <?=($uri->getSegment(1)==='product')?'uk-active':''?>">
                                            <a href="<?= base_url('product') ?>"><?=lang('Global.product');?></a>
                                        </li>
                                        <li class="tm-h4 <?=($uri->getSegment(1)==='bundle')?'uk-active':''?>">
                                            <a href="<?= base_url('bundle') ?>"><?=lang('Global.bundle');?></a>
                                        </li>
                                    </ul>
                                </li>
                            <?php endif ?>
                            <li class="tm-main-navbar <?=($uri->getSegment(1)==='reminder')?'uk-active':''?>">
                                <a class="tm-h3" href="<?= base_url('reminder') ?>"><img src="img/layout/calendar.svg" uk-svg><?=lang('Global.reminder');?></a>
                            </li>
                            <li class="tm-main-navbar <?=($uri->getSegment(1)==='presence')?'uk-active':''?>">
                                <a class="tm-h3" href="<?= base_url('presence') ?>"><img src="img/layout/presensi.svg" uk-svg><?=lang('Global.presence');?></a>
                            </li>
                            <?php if (in_groups((['owner','supervisor']))) : ?>
                                <li class="tm-main-navbar <?=($uri->getSegment(1)==='user')?'uk-active':''?>">
                                    <a class="tm-h3" href="<?= base_url('user') ?>"><img src="img/layout/pegawai.svg" uk-svg><?=lang('Global.employee');?></a>
                                </li>
                            <?php endif ?>
                            <li class="tm-main-navbar uk-parent <?=($uri->getSegment(1)==='stock')&&($uri->getSegment(2)==='')?'uk-active':''?><?=($uri->getSegment(1)==='stock')&&($uri->getSegment(2)==='supplier')?'uk-active':''?><?=($uri->getSegment(1)==='stock')&&($uri->getSegment(2)==='purchase')?'uk-active':''?><?=($uri->getSegment(1)==='stockmove')?'uk-active':''?><?=($uri->getSegment(1)==='stockadjustment')?'uk-active':''?><?=($uri->getSegment(1)==='stock')&&($uri->getSegment(2)==='stockcycle')?'uk-active':''?>">
                                <a class="tm-h3" href=""><img src="img/layout/inventori.svg" uk-svg><?=lang('Global.inventory');?><span uk-nav-parent-icon></span></a>
                                <ul class="uk-nav-sub">
                                    <?php if (in_groups('owner')) : ?>
                                        <li class="tm-h4 <?=($uri->getSegment(1)==='stock')&&($uri->getSegment(2)==='')?'uk-active':''?>">
                                            <a href="<?= base_url('stock') ?>"><?=lang('Global.stock');?></a>
                                        </li>
                                        <li class="tm-h4 <?=($uri->getSegment(1)==='stock')&&($uri->getSegment(2)==='inventory')?'uk-active':''?>">
                                            <a href="<?= base_url('stock/inventory') ?>"><?=lang('Global.store');?></a>
                                        </li>
                                        <li class="tm-h4 <?=($uri->getSegment(1)==='stock')&&($uri->getSegment(2)==='supplier')?'uk-active':''?>">
                                            <a href="<?= base_url('stock/supplier') ?>"><?=lang('Global.supplier');?></a>
                                        </li>
                                            <li class="tm-h4 <?=($uri->getSegment(1)==='stock')&&($uri->getSegment(2)==='purchase')?'uk-active':''?>">
                                                <a href="<?= base_url('stock/purchase') ?>"><?=lang('Global.purchase');?></a>
                                            </li>
                                        <li class="tm-h4 <?=($uri->getSegment(1)==='stockadjustment')?'uk-active':''?>">
                                            <a href="<?= base_url('stockadjustment') ?>"><?=lang('Global.stockAdj');?></a>
                                        </li>
                                        <li class="tm-h4 <?=($uri->getSegment(1)==='stock')&&($uri->getSegment(2)==='stockcycle')?'uk-active':''?>">
                                            <a href="<?= base_url('stock/stockcycle') ?>"><?=lang('Global.stockCycle');?></a>
                                        </li>
                                    <?php endif ?>
                                    <li class="tm-h4 <?=($uri->getSegment(1)==='stockmove')?'uk-active':''?>">
                                        <a href="<?= base_url('stockmove') ?>"><?=lang('Global.stockMove');?></a>
                                    </li>
                                </ul>
                            </li>
                            <?php if (in_groups(['owner','supervisor'])) : ?>
                                <li class="tm-main-navbar <?=($uri->getSegment(1)==='outlet')?'uk-active':''?>">
                                    <a class="tm-h3" href="<?= base_url('outlet') ?>"><img src="img/layout/outlet.svg" uk-svg><?=lang('Global.outlet');?></a>
                                </li>
                            <?php endif ?>
                            <li class="tm-main-navbar <?=($uri->getSegment(1)==='cashinout')?'uk-active':''?>">
                                <a class="tm-h3" href="<?= base_url('cashinout') ?>"><img src="img/layout/cash.svg" uk-svg><?=lang('Global.cashinout');?></a>
                            </li>
                            <?php if (in_groups('owner')) : ?>
                                <li class="tm-main-navbar uk-parent <?=($uri->getSegment(1)==='walletman')?'uk-active':''?><?=($uri->getSegment(1)==='walletmove')?'uk-active':''?><?=($uri->getSegment(1)==='cashexp')?'uk-active':''?><?=($uri->getSegment(1)==='payment')?'uk-active':''?>">
                                    <a class="tm-h3" href=""><img src="img/layout/payment.svg" uk-svg><?=lang('Global.wallet');?><span uk-nav-parent-icon></span></a>
                                    <ul class="uk-nav-sub">
                                        <li class="tm-h4 <?=($uri->getSegment(1)==='walletman')?'uk-active':''?>">
                                            <a href="<?= base_url('walletman') ?>"><?=lang('Global.walletManagement');?></a>
                                        </li>
                                        <li class="tm-h4 <?=($uri->getSegment(1)==='walletmove')?'uk-active':''?>">
                                            <a href="<?= base_url('walletmove') ?>"><?=lang('Global.walletMovement');?></a>
                                        </li>
                                        <li class="tm-h4 <?=($uri->getSegment(1)==='cashexp')?'uk-active':''?>">
                                            <a href="<?= base_url('cashexp') ?>"><?=lang('Global.cashExpenses');?></a>
                                        </li>
                                        <li class="tm-h4 <?=($uri->getSegment(1)==='payment')?'uk-active':''?>">
                                            <a href="<?= base_url('payment') ?>"><?=lang('Global.payment');?></a>
                                        </li>
                                    </ul>
                                </li>
                            <?php endif ?>
                            <li class="tm-main-navbar <?=($uri->getSegment(1)==='customer')?'uk-active':''?>">
                                <a class="tm-h3" href="<?= base_url('customer') ?>"><img src="img/layout/pelanggan.svg" uk-svg><?=lang('Global.customer');?></a>
                            </li>
                            <?php if (in_groups(['owner','supervisor'])) : ?>
                                <li class="tm-main-navbar <?=($uri->getSegment(1)==='promo')?'uk-active':''?>">
                                    <a class="tm-h3" href="<?= base_url('promo') ?>"><img src="img/layout/union.svg" uk-svg><?=lang('Global.website');?></a>
                                </li>
                            <?php endif ?>
                        </ul>
                    </nav>
                </div>
            </div>
        <?php } else { ?>
            <nav class="tm-sidebar-left">
                <ul class="uk-nav uk-nav-default tm-nav uk-light" uk-nav>
                    <li class="tm-main-navbar <?=($uri->getSegment(1)==='')?'uk-active':''?>">
                        <a class="tm-h3" href="<?= base_url('') ?>"><img src="img/layout/dashboard.svg" uk-svg><?=lang('Global.dashboard');?></a>
                    </li>
                    <li class="tm-main-navbar uk-parent <?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='penjualan')?'uk-active':''?><?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='keuntungan')?'uk-active':''?><?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='payment')?'uk-active':''?><?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='employe')?'uk-active':''?><?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='product')?'uk-active':''?><?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='category')?'uk-active':''?><?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='bundle')?'uk-active':''?><?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='diskon')?'uk-active':''?><?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='presence')?'uk-active':''?><?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='customer')?'uk-active':''?><?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='stockcategory')?'uk-active':''?><?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='sop')?'uk-active':''?>">
                        <a class="tm-h3" href=""><img src="img/layout/laporan.svg" uk-svg><?=lang('Global.report');?><span uk-nav-parent-icon></span></a>
                        <ul class="uk-nav-sub">
                            <?php if (in_groups('owner')) : ?>
                                <li class="tm-h4 <?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='penjualan')?'uk-active':''?>">
                                    <a href="<?= base_url('report/penjualan') ?>"><?=lang('Global.salesreport');?></a>
                                </li>
                                <li class="tm-h4 <?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='keuntungan')?'uk-active':''?>">
                                    <a href="<?= base_url('report/keuntungan') ?>"><?=lang('Global.profitreport');?></a>
                                </li>
                                <li class="tm-h4 <?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='payment')?'uk-active':''?>">
                                    <a href="<?= base_url('report/payment') ?>"><?=lang('Global.paymentreport');?></a>
                                </li>
                                <li class="tm-h4 <?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='employe')?'uk-active':''?>">
                                    <a href="<?= base_url('report/employe') ?>"><?=lang('Global.employereport');?></a>
                                </li>
                                <li class="tm-h4 <?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='product')?'uk-active':''?>">
                                    <a href="<?= base_url('report/product') ?>"><?=lang('Global.productreport');?></a>
                                </li>
                                <li class="tm-h4 <?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='category')?'uk-active':''?>">
                                    <a href="<?= base_url('report/category') ?>"><?=lang('Global.categoryreport');?></a>
                                </li>
                                <li class="tm-h4 <?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='bundle')?'uk-active':''?>">
                                    <a href="<?= base_url('report/bundle') ?>"><?=lang('Global.bundlereport');?></a>
                                </li>
                                <li class="tm-h4 <?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='diskon')?'uk-active':''?>">
                                    <a href="<?= base_url('report/diskon') ?>"><?=lang('Global.discountreport');?></a>
                                </li>
                                <li class="tm-h4 <?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='customer')?'uk-active':''?>">
                                    <a href="<?= base_url('report/customer') ?>"><?=lang('Global.customerreport');?></a>
                                </li>
                                <li class="tm-h4 <?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='sop')?'uk-active':''?>">
                                    <a href="<?= base_url('report/sop') ?>"><?=lang('Global.sopreport');?></a>
                                </li>
                            <?php endif ?>
                            <li class="tm-h4 <?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='presence')?'uk-active':''?>">
                                <a href="<?= base_url('report/presence') ?>"><?=lang('Global.presencereport');?></a>
                            </li>
                        </ul>
                    </li>
                    <li class="tm-main-navbar <?=($uri->getSegment(1)==='dayrep')?'uk-active':''?>">
                        <a class="tm-h3" href="<?= base_url('dayrep') ?>"><img src="img/layout/laporan.svg" uk-svg><?=lang('Global.dailyreport');?></a>
                    </li>
                    <li class="tm-main-navbar <?=($uri->getSegment(1)==='transaction')?'uk-active':''?>">
                        <a class="tm-h3" href="<?= base_url('transaction') ?>"><img src="img/layout/chart.svg" uk-svg><?=lang('Global.transaction');?></a>
                    </li>
                    <li class="tm-main-navbar <?=($uri->getSegment(1)==='trxhistory')?'uk-active':''?>">
                        <a class="tm-h3" href="<?= base_url('trxhistory') ?>"><img src="img/layout/riwayat.svg" uk-svg><?=lang('Global.trxHistory');?></a>
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
                    <li class="tm-main-navbar <?=($uri->getSegment(1)==='topup')?'uk-active':''?>">
                        <a class="tm-h3" href="<?= base_url('topup') ?>"><img src="img/layout/topup.svg" uk-svg><?=lang('Global.topup');?></a>
                    </li>
                    <?php if(in_groups('owner')) : ?>
                        <li class="tm-main-navbar <?=($uri->getSegment(1)==='sop')?'uk-active':''?>">
                            <a class="tm-h3" href="<?= base_url('sop') ?>"><img src="img/layout/sop.svg" uk-svg><?=lang('Global.sop');?></a>
                        </li>
                        <li class="tm-main-navbar uk-parent <?=($uri->getSegment(1)==='product')?'uk-active':''?><?=($uri->getSegment(1)==='bundle')?'uk-active':''?>">
                            <a class="tm-h3" href=""><img src="img/layout/product.svg" uk-svg><?=lang('Global.product');?><span uk-nav-parent-icon></span></a>
                            <ul class="uk-nav-sub">
                                <li class="tm-h4 <?=($uri->getSegment(1)==='product')?'uk-active':''?>">
                                    <a href="<?= base_url('product') ?>"><?=lang('Global.product');?></a>
                                </li>
                                <li class="tm-h4 <?=($uri->getSegment(1)==='bundle')?'uk-active':''?>">
                                    <a href="<?= base_url('bundle') ?>"><?=lang('Global.bundle');?></a>
                                </li>
                            </ul>
                        </li>
                    <?php endif ?>
                    <li class="tm-main-navbar <?=($uri->getSegment(1)==='reminder')?'uk-active':''?>">
                        <a class="tm-h3" href="<?= base_url('reminder') ?>"><img src="img/layout/calendar.svg" uk-svg><?=lang('Global.reminder');?></a>
                    </li>
                    <li class="tm-main-navbar <?=($uri->getSegment(1)==='presence')?'uk-active':''?>">
                        <a class="tm-h3" href="<?= base_url('presence') ?>"><img src="img/layout/presensi.svg" uk-svg><?=lang('Global.presence');?></a>
                    </li>
                    <?php if (in_groups(['owner','supervisor'])) : ?>
                        <li class="tm-main-navbar <?=($uri->getSegment(1)==='user')?'uk-active':''?>">
                            <a class="tm-h3" href="<?= base_url('user') ?>"><img src="img/layout/pegawai.svg" uk-svg><?=lang('Global.employee');?></a>
                        </li>
                    <?php endif ?>
                    <li class="tm-main-navbar uk-parent <?=($uri->getSegment(1)==='stock')&&($uri->getSegment(2)==='')?'uk-active':''?><?=($uri->getSegment(1)==='stock')&&($uri->getSegment(2)==='supplier')?'uk-active':''?><?=($uri->getSegment(1)==='stock')&&($uri->getSegment(2)==='purchase')?'uk-active':''?><?=($uri->getSegment(1)==='stockmove')?'uk-active':''?><?=($uri->getSegment(1)==='stockadjustment')?'uk-active':''?><?=($uri->getSegment(1)==='stock')&&($uri->getSegment(2)==='stockcycle')?'uk-active':''?>">
                        <a class="tm-h3" href=""><img src="img/layout/inventori.svg" uk-svg><?=lang('Global.inventory');?><span uk-nav-parent-icon></span></a>
                        <ul class="uk-nav-sub">
                            <?php if (in_groups('owner')) : ?>
                                <li class="tm-h4 <?=($uri->getSegment(1)==='stock')&&($uri->getSegment(2)==='')?'uk-active':''?>">
                                    <a href="<?= base_url('stock') ?>"><?=lang('Global.stock');?></a>
                                </li>
                                <li class="tm-h4 <?=($uri->getSegment(1)==='stock')&&($uri->getSegment(2)==='inventory')?'uk-active':''?>">
                                    <a href="<?= base_url('stock/inventory') ?>"><?=lang('Global.store');?></a>
                                </li>
                                <li class="tm-h4 <?=($uri->getSegment(1)==='stock')&&($uri->getSegment(2)==='supplier')?'uk-active':''?>">
                                    <a href="<?= base_url('stock/supplier') ?>"><?=lang('Global.supplier');?></a>
                                </li>
                                <li class="tm-h4 <?=($uri->getSegment(1)==='stock')&&($uri->getSegment(2)==='purchase')?'uk-active':''?>">
                                    <a href="<?= base_url('stock/purchase') ?>"><?=lang('Global.purchase');?></a>
                                </li>
                                <li class="tm-h4 <?=($uri->getSegment(1)==='stockadjustment')?'uk-active':''?>">
                                    <a href="<?= base_url('stockadjustment') ?>"><?=lang('Global.stockAdj');?></a>
                                </li>
                                <li class="tm-h4 <?=($uri->getSegment(1)==='stock')&&($uri->getSegment(2)==='stockcycle')?'uk-active':''?>">
                                    <a href="<?= base_url('stock/stockcycle') ?>"><?=lang('Global.stockCycle');?></a>
                                </li>
                            <?php endif ?>
                            <li class="tm-h4 <?=($uri->getSegment(1)==='stockmove')?'uk-active':''?>">
                                <a href="<?= base_url('stockmove') ?>"><?=lang('Global.stockMove');?></a>
                            </li>
                        </ul>
                    </li>
                    <?php if (in_groups(['owner','supervisor'])) : ?>
                        <li class="tm-main-navbar <?=($uri->getSegment(1)==='outlet')?'uk-active':''?>">
                            <a class="tm-h3" href="<?= base_url('outlet') ?>"><img src="img/layout/outlet.svg" uk-svg><?=lang('Global.outlet');?></a>
                        </li>
                    <?php endif ?>
                    <li class="tm-main-navbar <?=($uri->getSegment(1)==='cashinout')?'uk-active':''?>">
                        <a class="tm-h3" href="<?= base_url('cashinout') ?>"><img src="img/layout/cash.svg" uk-svg><?=lang('Global.cashinout');?></a>
                    </li>
                    <?php if (in_groups('owner')) : ?>
                        <li class="tm-main-navbar uk-parent <?=($uri->getSegment(1)==='walletman')?'uk-active':''?><?=($uri->getSegment(1)==='walletmove')?'uk-active':''?><?=($uri->getSegment(1)==='cashexp')?'uk-active':''?><?=($uri->getSegment(1)==='payment')?'uk-active':''?>">
                            <a class="tm-h3" href=""><img src="img/layout/payment.svg" uk-svg><?=lang('Global.wallet');?><span uk-nav-parent-icon></span></a>
                            <ul class="uk-nav-sub">
                                <li class="tm-h4 <?=($uri->getSegment(1)==='walletman')?'uk-active':''?>">
                                    <a href="<?= base_url('walletman') ?>"><?=lang('Global.walletManagement');?></a>
                                </li>
                                <li class="tm-h4 <?=($uri->getSegment(1)==='walletmove')?'uk-active':''?>">
                                    <a href="<?= base_url('walletmove') ?>"><?=lang('Global.walletMovement');?></a>
                                </li>
                                <li class="tm-h4 <?=($uri->getSegment(1)==='cashexp')?'uk-active':''?>">
                                    <a href="<?= base_url('cashexp') ?>"><?=lang('Global.cashExpenses');?></a>
                                </li>
                                <li class="tm-h4 <?=($uri->getSegment(1)==='payment')?'uk-active':''?>">
                                    <a href="<?= base_url('payment') ?>"><?=lang('Global.payment');?></a>
                                </li>
                            </ul>
                        </li>
                    <?php endif ?>
                    <li class="tm-main-navbar <?=($uri->getSegment(1)==='customer')?'uk-active':''?>">
                        <a class="tm-h3" href="<?= base_url('customer') ?>"><img src="img/layout/pelanggan.svg" uk-svg><?=lang('Global.customer');?></a>
                    </li>
                    <?php if (in_groups(['owner','supervisor'])) : ?>
                        <li class="tm-main-navbar <?=($uri->getSegment(1)==='promo')?'uk-active':''?>">
                            <a class="tm-h3" href="<?= base_url('promo') ?>"><img src="img/layout/union.svg" uk-svg><?=lang('Global.website');?></a>
                        </li>
                    <?php endif ?>
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
                    <div class="<?=$mainCard?>uk-panel uk-panel-scrollable" style="background-color: #363636;">
                        <?= $this->renderSection('main') ?>
                    </div>
                </div>
            </div>
            <!-- Footer Section -->
            <footer class="tm-footer" style="background-color: #000; color: #fff;">
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
