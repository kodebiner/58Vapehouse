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
        <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
        <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
        <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
        <style type="text/css">
            .dummyproduct{fill:#666666;}
        </style>

    </head>
    <body style="background-color: #363636;">

        <!-- Header Section -->
        <header class="uk-navbar-container tm-navbar-container" style="background-color:#000;">
            <?= view('Views/Auth/_message_block') ?>
            
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
                    <div class="uk-navbar-right">
                        <button type="button" class="uk-button" uk-toggle="target: #tambahdata" uk-icon="cart" width="35" height="35" style="color: #fff;"></a>
                    </div>
                    <!-- Navbar Right End -->

                </div>
            </div>
        </header>
        <!-- Header Section end -->

        <!-- Left Sidebar Section -->
        <div id="offcanvas" uk-offcanvas="overlay: true;">
            <div class="uk-offcanvas-bar" role="dialog" aria-modal="true">
                <nav>
                    <ul class="uk-nav uk-nav-default tm-nav uk-light" uk-nav>
                        <li class="tm-main-navbar">
                            <a class="uk-h4 tm-h4" href="<?= base_url('') ?>"><img src="img/layout/dashboard.svg" uk-svg><?=lang('Global.dashboard');?></a>
                        </li>
                        <li class="tm-main-navbar">
                            <a class="uk-h4 tm-h4" href="<?= base_url('') ?>"><img src="img/layout/laporan.svg" uk-svg><?=lang('Global.report');?></a>
                        </li>
                        <li class="tm-main-navbar">
                            <a class="uk-h4 tm-h4" href="<?= base_url('') ?>"><img src="img/layout/riwayat.svg" uk-svg><?=lang('Global.trxHistory');?></a>
                        </li>
                        <li class="tm-main-navbar">
                            <a class="uk-h4 tm-h4" href="<?= base_url('') ?>"><img src="img/layout/payment.svg" uk-svg><?=lang('Global.payment');?></a>
                        </li>
                        <li class="tm-main-navbar uk-parent">
                            <a class="uk-h4 tm-h4" href=""><img src="img/layout/product.svg" uk-svg><?=lang('Global.product');?><span uk-nav-parent-icon></span></a>
                            <ul class="uk-nav-sub">
                                <li class="uk-h5 tm-h5">
                                    <a href="<?= base_url('product') ?>"><?=lang('Global.product');?></a>
                                </li>
                                <li class="uk-h5 tm-h5">
                                    <a href="<?= base_url('bundle') ?>"><?=lang('Global.bundle');?></a>
                                </li>
                            </ul>
                        </li>
                        <li class="tm-main-navbar">
                            <a class="uk-h4 tm-h4" href="<?= base_url('') ?>"><img src="img/layout/calendar.svg" uk-svg><?=lang('Global.reminder');?></a>
                        </li>
                        <li class="tm-main-navbar">
                            <a class="uk-h4 tm-h4" href="<?= base_url('presence') ?>"><img src="img/layout/presensi.svg" uk-svg><?=lang('Global.presence');?></a>
                        </li>
                        <li class="tm-main-navbar">
                            <a class="uk-h4 tm-h4" href="<?= base_url('user') ?>"><img src="img/layout/pegawai.svg" uk-svg><?=lang('Global.employee');?></a>
                        </li>
                        <li class="tm-main-navbar uk-parent">
                            <a class="uk-h4 tm-h4" href=""><img src="img/layout/inventori.svg" uk-svg><?=lang('Global.inventory');?><span uk-nav-parent-icon></span></a>
                            <ul class="uk-nav-sub">
                                <li class="uk-h5 tm-h5">
                                    <a href="<?= base_url('stock') ?>"><?=lang('Global.stock');?></a>
                                </li>
                                <li class="uk-h5 tm-h5">
                                    <a href="<?= base_url('stockmove') ?>"><?=lang('Global.stockMove');?></a>
                                </li>
                                <li class="uk-h5 tm-h5">
                                    <a href="<?= base_url('stockadjustment') ?>"><?=lang('Global.stockAdj');?></a>
                                </li>
                                <li class="uk-h5 tm-h5">
                                    <a href="<?= base_url('stock/stockcycle') ?>"><?=lang('Global.stockCycle');?></a>
                                </li>
                            </ul>
                        </li>
                        <li class="tm-main-navbar">
                            <a class="uk-h4 tm-h4" href="<?= base_url('outlet') ?>"><img src="img/layout/outlet.svg" uk-svg><?=lang('Global.outlet');?></a>
                        </li>
                        <li class="tm-main-navbar">
                            <a class="uk-h4 tm-h4" href="<?= base_url('cashman') ?>"><img src="img/layout/cash.svg" uk-svg><?=lang('Global.cashManagement');?></a>
                        </li>
                        <li class="tm-main-navbar">
                            <a class="uk-h4 tm-h4" href="<?= base_url('customer') ?>"><img src="img/layout/pelanggan.svg" uk-svg><?=lang('Global.customer');?></a>
                        </li>
                        <li class="tm-main-navbar">
                            <a class="uk-h4 tm-h4" href="<?= base_url('') ?>"><img src="img/layout/union.svg" uk-svg><?=lang('Global.website');?></a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
        <!-- Left Sidebar Section end -->

        <!-- Modal Detail Transaction -->
        <div uk-modal class="uk-flex-top" id="tambahdata">
            <div class="uk-modal-dialog uk-margin-auto-vertical">
                <div class="uk-modal-content">
                    <div class="uk-modal-header">
                        <h5 class="uk-modal-title" id="tambahdata" ><?=lang('Global.detailOrder');?></h5>
                    </div>
                    <div class="uk-modal-body">
                        <form class="uk-form-stacked" name="order" role="form" action="/transaction/create" method="post">
                            <?= csrf_field() ?>

                            <div class="uk-margin-bottom">
                                <h4 class="uk-margin-remove"><?=lang('Global.customer')?></h4>
                                <div class="uk-margin-small">
                                    <div class="uk-width-1-1">
                                        <input class="uk-input" id="customername" required/>
                                    </div>
                                </div>

                                <script type="text/javascript">
                                    $(function() {
                                        var customerList = [
                                            <?php
                                                foreach ($customers as $customer) {
                                                    echo '"'.$customer['name'].'",';
                                                }
                                            ?>
                                        ];
                                        $("#customername").autocomplete({
                                            source: customerList
                                        });
                                    });
                                </script>
                            </div>

                            <div class="uk-overflow-auto">
                                <table class="uk-table uk-table-justify uk-table-middle uk-table-divider" id="instab">
                                    <thead>
                                        <tr>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($transactions as $transaction) : ?>
                                            <tr>
                                                <td class="uk-text-center"><?= $transaction['name']; ?></td>
                                                <td class="uk-text-center">Rp <?= $transaction['value']; ?>,-</td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="uk-margin">
                                <h4 class="uk-h4"><?=lang('Global.subtotal')?></h4>
                                <div class="uk-h4">
                                    <?php foreach ($transactions as $transaction) : ?>
                                        <?php foreach ($trxdetails as $trxdet) {
                                            if ($transaction['id'] === $trxdet['transactionid']) {
                                                echo $trxdet['value'];
                                            }
                                        } ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div class="uk-margin">
                                <h4 class="uk-margin-remove"><?=lang('Global.discount')?></h4>
                                <div class="uk-margin-small" uk-grid>
                                    <div class="uk-width-3-5">
                                        <input class="uk-input" id="discount" name="discount" placeholder="<?=lang('Global.discount')?>" />
                                    </div>
                                    <?php 
                                        foreach ($transactions as $transaction) {
                                            if ($transaction['disctype'] === '0' ) {
                                                echo lang('Global.percent');
                                            } elseif ($cash['type'] === '1' ) { 
                                                echo lang('Global.amount');}
                                        } 
                                    ?>
                                    <div class="uk-width-1-5">
                                        <input class="uk-button uk-button-primary" type="radio" name="type" value="0"> <?=lang('Global.cashin')?>
                                    </div>
                                    <div class="uk-width-1-5">
                                        <input class="uk-radio" type="radio" name="type" value="0"> <?=lang('Global.cashin')?>
                                    </div>
                                </div>
                            </div>

                            <div class="uk-margin">
                                <label class="uk-form-label" for="name"><?=lang('Global.name')?></label>
                                <div class="uk-form-controls">
                                    <input type="text" class="uk-input <?php if (session('errors.name')) : ?>tm-form-invalid<?php endif ?>" id="name" name="name" placeholder="<?=lang('Global.name')?>" autofocus required />
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="uk-modal-footer" style="border-top: 0;">
                        <div class="uk-margin uk-flex uk-flex-center">
                            <button type="submit" class="uk-button uk-button-primary uk-button-large uk-text-center tm-h2" style="color: #fff; border-radius: 8px; width: 540px;"><?=lang('Global.pay')?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal Detail Transaction End -->

        <!-- Main Section -->
        <main role="main">
            <div class="tm-main">
                <div class="uk-container uk-container-expand uk-padding-remove-horizontal">
                    <div class="uk-panel uk-panel-scrollable" style="background-color: #363636;" uk-height-viewport="offset-top: .uk-navbar-container; offset-bottom: .tm-footer;">
                        <ul class="uk-switcher switcher-class">
                            <li>
                                <div class="uk-child-width-1-2 uk-child-width-1-5@m" uk-grid uk-height-match="target: > div > .uk-card > .uk-card-header">
                                    <?php foreach ($variants as $variant) : ?>
                                        <?php
                                            foreach ($products as $product) {
                                                if ($product['id'] === $variant['productid']) {
                                                    $productName = $product['name'];
                                                    $productPhoto = $product['thumbnail'];
                                                }
                                            }
                                        ?>
                                        <div onClick="createNewOrder">
                                            <div class="uk-card uk-card-hover uk-card-default">
                                                <div class="uk-card-header">
                                                    <div class="tm-h1 uk-text-bolder uk-text-center"><?= $productName.' - '. $variant['name'] ?></div>
                                                </div>
                                                <div class="uk-card-body">
                                                    <?php if (!empty($productPhoto)) { ?>
                                                        <img class="uk-width-1-1" src="img/product/<?= $productPhoto ?>" />
                                                    <?php } else { ?>
                                                        <svg x="0px" y="0px" viewBox="0 0 300 300" style="enable-background:new 0 0 300 300;" xml:space="preserve">
                                                            <g>
                                                                <defs>
                                                                    <rect id="SVGID_1_" y="0" width="300" height="300"/>
                                                                </defs>
                                                                <clipPath id="SVGID_00000065759931020451687440000009539297437584060839_">
                                                                    <use xlink:href="#SVGID_1_"  style="overflow:visible;"/>
                                                                </clipPath>
                                                                <g style="clip-path:url(#SVGID_00000065759931020451687440000009539297437584060839_);">
                                                                    <path class="dummyproduct" d="M10.43,99.92c-10.73-27.36,4.25-69.85,30.19-85.78C51.01,7.77,77-5.17,108.81,30.24
                                                                        c-2.16,0.65-4.26,1.55-6.29,2.7c-3.02,1.75-5.49,4.04-7.57,6.58C83.12,26.95,67.17,17.08,49.17,28.13
                                                                        C34,37.46,23.24,60.45,23.28,79.62c-0.03,5.15,0.77,10.05,2.42,14.32l4.75,11.66c6.41,15.42,12.34,29.6,12.34,46.6
                                                                        c-0.03,11.87-2.89,25.14-10.44,41.17c-1.05,2.23-1.96,5.97-1.96,9.8c0,2.31,0.29,4.66,1.16,6.73c1.13,2.73,3.09,4.44,5.9,5.59
                                                                        c2.16,0.28,10.31,0.86,17.02-5.79c6.56-6.54,13.06-21.9,6.78-58.08C50.43,89.07,75.8,68.22,87.2,62.18
                                                                        c15.23-8.09,33.99-5.98,45.6,5.15c3.3,3.14,3.38,8.34,0.23,11.6c-3.13,3.26-8.35,3.37-11.59,0.23c-5.55-5.31-16.45-7.86-26.56-2.5
                                                                        c-8.25,4.37-26.43,20.18-17.46,72.17c6.01,34.86,2.08,59.32-11.64,72.76c-13.81,13.43-31.7,10.1-32.45,9.95l-0.67-0.13l-0.63-0.24
                                                                        c-7.34-2.73-12.76-7.95-15.68-15.08c-4.14-10.12-2.41-22.24,1.16-29.72c15.27-32.43,8.34-49.15-2.2-74.47L10.43,99.92z"/>
                                                                    <g>
                                                                        <path class="dummyproduct" d="M289.03,204.6L222.63,89.6c0,0-8.25-9.16-7.65-8.69l-10.29-6.98l-72.37-38.31
                                                                            c-7.64-4.21-17.21-3.87-25.53,0.91c-6.82,3.93-11.33,10.31-12.87,17.21c14.44-4.1,30.01-1.11,40.99,8.29
                                                                            c7.23,0.26,14.23,3.89,18.08,10.64c6.07,10.47,2.46,23.86-7.98,29.88c-10.47,6.04-23.89,2.46-29.92-8.01
                                                                            c-2.57-4.48-3.27-9.48-2.52-14.24c-8.67-4.82-20.11,2.86-20.51,5.7c-0.51,3.49-1.94,54.29-1.94,54.29s0.98,10.4,1.08,11.45
                                                                            c0.21,0.64,3.82,11.58,3.82,11.58l66.4,114.96c4.06,7.05,10.6,12.07,18.43,14.18c7.8,2.1,15.98,1.03,22.98-3.03l75.14-43.35
                                                                            C292.39,237.71,297.39,219.1,289.03,204.6z M210.47,157.72l-6.24,6.9c-3.34-3.82-7.36-5.93-11.95-6.25
                                                                            c-2.17-0.16-4.25,0-6.22,0.36l-4.6-8.04C191.65,146.98,201.33,149.28,210.47,157.72z M166.64,189.62c-0.76-0.98-1.46-2-2.1-3.11
                                                                            c-0.8-1.4-1.42-2.78-1.96-4.18c-2.24-7.52-0.14-16.05,5.35-23.07c0.61-0.7,1.29-1.38,1.99-1.97l4.57,7.98
                                                                            c-0.08,0.13-0.17,0.27-0.25,0.38c-4.51,5.03-5.96,11.66-3.05,16.74c2.99,5.22,9.6,7.28,16.39,5.77l4.98,8.7
                                                                            C182.41,199.07,172.43,196.42,166.64,189.62z M182.01,224.96l6.55-6.26c6.45,6.06,13.24,8.32,20.42,6.89l4.7,8.22
                                                                            C202.67,237.11,192.12,234.18,182.01,224.96z M220.06,237.4l-50.01-87.43l5.74-3.28l50,87.43L220.06,237.4z M226.2,226.44
                                                                            c-0.29,0.25-0.55,0.46-0.85,0.69l-4.53-7.92c0.51-0.43,0.96-0.9,1.4-1.35c2.16-1.94,3.64-4,4.5-6.25
                                                                            c2.1-4.48,2.31-9.32,0.06-13.25c-3.65-6.39-12.44-8.43-21.03-5.49l-4.84-8.41c14.3-3.2,27.1-0.45,32.68,9.28
                                                                            C239,203.19,235.61,215.91,226.2,226.44z"/>
                                                                    </g>
                                                                </g>
                                                            </g>
                                                        </svg>
                                                    <?php } ?>
                                                </div>
                                                <div class="uk-card-footer">
                                                    <div class="tm-h3 uk-text-center">
                                                        <div>Rp <?= $variant['hargamodal'] + $variant['hargajual'] ?>,-</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                    
                                    <script>
                                        function createNewOrder(){

                                            const createOrder = document.getElementById("instab");
                                            newCreateOrder.setAttribute('id','create'+createCount);
                                            newCreateOrder.setAttribute('class','uk-margin uk-child-width-1-5');
                                            newCreateOrder.setAttribute('uk-grid','');
                                        }
                                    </script>
                                </div>
                            </li>
                            <li>Lorem</li>
                            <li>Ipsum</li>
                        </ul>
                    </div>
                </div>
            </div>
        </main>
        <!-- Main Section end -->

        <!-- Footer Section -->
        <footer class="tm-footer" style="background-color:#000;">
            <ul class="uk-flex-around tm-trx-tab" uk-tab uk-switcher="connect: .switcher-class; active: 1;">
                <li>
                    <a uk-switcher-item="0">
                        <div width="30" height="30" uk-icon="file-text"></div>
                        <div class="uk-h4 uk-margin-small"><?=lang('Global.catalog');?></div>
                    </a>
                </li>
                <li>
                    <a uk-switcher-item="0">
                        <div width="30" height="30" uk-icon="star"></div>
                        <div class="uk-h4 uk-margin-small"><?=lang('Global.favorite');?></div>
                    </a>
                </li>
                <li>
                    <a uk-switcher-item="0">
                        <div width="30" height="30" uk-icon="file-edit"></div>
                        <div class="uk-h4 uk-margin-small"><?=lang('Global.manual');?></div>
                    </a>
                </li>
            </ul>
        </footer>
        <!-- Footer Section end -->
        
    </body>
</html>
