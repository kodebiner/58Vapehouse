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
                                <img src="/img/<?=$gconfig['logo'];?>" alt="<?=$gconfig['bizname'];?>" width="70" height="70" style="aspect-ratio: 1/1;">
                            <?php } else { ?>
                                <img src="/img/binary111-logo-icon.svg" alt="PT. Kodebiner Teknologi Indonesia" width="70" height="70" style="aspect-ratio: 1/1;">
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
                    <ul class="uk-nav uk-nav-default tm-nav uk-light">
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
                        <li class="tm-main-navbar">
                            <a class="uk-h4 tm-h4" href="<?= base_url('product') ?>"><img src="img/layout/product.svg" uk-svg><?=lang('Global.product');?></a>
                        </li>
                        <li class="tm-main-navbar">
                            <a class="uk-h4 tm-h4" href="<?= base_url('') ?>"><img src="img/layout/calendar.svg" uk-svg><?=lang('Global.reminder');?></a>
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
                            </ul>
                        </li>
                        <li class="tm-main-navbar">
                            <a class="uk-h4 tm-h4" href="<?= base_url('outlet') ?>"><img src="img/layout/outlet.svg" uk-svg><?=lang('Global.outlet');?></a>
                        </li>
                        <li class="tm-main-navbar">
                            <a class="uk-h4 tm-h4" href="<?= base_url('') ?>"><img src="img/layout/cash.svg" uk-svg><?=lang('Global.cashManagement');?></a>
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
                                <div class="uk-form-controls">
                                    <select class="uk-select" name="member">
                                        <option><?=lang('Global.customer')?></option>
                                        <?php foreach ($customers as $customer) { ?>
                                            <option value="<?= $customer['id']; ?>"><?= $customer['name']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
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
                                <label class="uk-form-label" for="name"><?=lang('Global.name')?></label>
                                <div class="uk-form-controls">
                                <input type="text" class="uk-input <?php if (session('errors.name')) : ?>tm-form-invalid<?php endif ?>" id="name" name="name" placeholder="<?=lang('Global.name')?>" autofocus required />
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="uk-modal-footer" style="border-top: 0;">
                        <div class="uk-margin uk-flex uk-flex-center">
                            <button type="submit" class="uk-button uk-button-primary uk-button-large uk-text-center" style="border-radius: 8px; width: 540px;"><?=lang('Global.pay')?></button>
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
                        <div class="uk-child-width-1-2 uk-child-width-1-5@m" uk-grid uk-height-match="target: > div > .uk-card > .uk-card-header">
                            <?php foreach ($variants as $variant) : ?>
                                <?php
                                    foreach ($products as $product) {
                                        if ($product['id'] === $variant['productid']) {
                                            $productName = $product['name'];
                                            $productPhoto = $product['photo'];
                                        }
                                    }
                                ?>
                                <div onClick="createNewOrder">
                                    <div class="uk-card uk-card-hover uk-card-default">
                                        <div class="uk-card-header">
                                            <div class="tm-h1 uk-text-bolder uk-text-center"><?= $productName.' - '. $variant['name'] ?></div>
                                        </div>
                                        <div class="uk-card-body">
                                            <div class=""><?= $productPhoto ?></div>
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
                    </div>
                </div>
            </div>
        </main>
        <!-- Main Section end -->

        <!-- Footer Section -->
        <footer class="tm-footer" style="background-color:#000;">
            <div class="">
                <div class="uk-container uk-container-expand">
                    <div class="uk-child-width-1-3 uk-child-width-1-5@m uk-text-center uk-flex-middle uk-flex-center" uk-grid style="color: #fff;">
                        <div>
                            <div width="30" height="30" uk-icon="file-text"></div>
                            <div class="uk-h4 uk-margin-small" style="color: #fff;"><?=lang('Global.catalog');?></div>
                        </div>
                        <div>
                            <div width="30" height="30" uk-icon="star"></div>
                            <div class="uk-h4 uk-margin-small" style="color: #fff;"><?=lang('Global.favorite');?></div>
                        </div>
                        <div>
                            <div width="30" height="30" uk-icon="file-edit"></div>
                            <div class="uk-h4 uk-margin-small" style="color: #fff;"><?=lang('Global.manual');?></div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!-- Footer Section end -->
        
    </body>
</html>
