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
                    <div class="uk-navbar-right">
                        <div class="uk-child-width-auto uk-grid-divider" uk-grid>
                            <div>
                                <!-- <a class="uk-button uk-button-text" uk-toggle="#modal-sections"></?=lang('Global.topup')?></a> -->
                                <a uk-icon="user" uk-toggle="target: #tambahmember"></a>
                            </div>
                            <div>
                                <a uk-icon="cart" uk-toggle="target: #tambahdata"></a>
                            </div>
                        </div>
                    </div>

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

                                        <!-- <div class="uk-margin">
                                            <label class="uk-form-label" for="email"></?= lang('Auth.email') ?></label>
                                            <div class="uk-form-controls">
                                                <input type="email" class="uk-input </?php if (session('errors.email')) : ?>tm-form-invalid</?php endif ?>" name="email" id="email" placeholder="</?= lang('Auth.email') ?>" />
                                            </div>
                                        </div> -->

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

                    <!-- </?php if ($ismobile === false) { ?>
                        <div class="uk-navbar-right">
                            <div class="uk-child-width-1-2 uk-flex uk-flex-middle" uk-grid>
                                <div>
                                    <a class="uk-button uk-button-text" uk-toggle="#modal-sections"></?=lang('Global.topup')?></a>
                                </div>
                                <div>
                                    <button type="button" class="uk-button" uk-toggle="target: #bookinglist" uk-icon="folder" width="35" height="35" style="color: #fff;"></button>
                                </div>
                                <div>
                                    <button type="button" class="uk-button" uk-toggle="target: #tambahdata" uk-icon="cart" width="50" height="50" style="color: #fff;"></button>
                                </div>
                            </div>
                        </div>
                    </?php } else { ?>
                        <div class="uk-navbar-right">
                            <div class="uk-child-width-1-2 uk-flex uk-flex-middle" uk-grid>
                                <div class="uk-padding-small uk-padding-remove-right uk-flex uk-flex-center">
                                    <a class="uk-button uk-button-text uk-padding-remove" uk-toggle="#modal-sections"></?=lang('Global.topup')?></a>
                                </div>
                                <div class="uk-padding-small uk-padding-remove-right uk-flex uk-flex-center">
                                    <button type="button" class="uk-button uk-padding-remove" uk-toggle="target: #bookinglist" uk-icon="folder" width="30" height="30" style="color: #fff;"></button>
                                </div>
                                <div class="uk-padding-small uk-padding-remove-right uk-flex uk-flex-center">
                                    <button type="button" class="uk-button uk-padding-remove" uk-toggle="target: #tambahdata" uk-icon="cart" width="30" height="30" style="color: #fff;"></button>
                                </div>
                            </div>
                        </div>
                    </?php } ?> -->
                    <!-- Navbar Right End -->

                    <!-- Modal Top Up Point -->
                    <!-- <div class="uk-flex-top" id="modal-sections" uk-modal>
                        <div class="uk-modal-dialog uk-margin-auto-vertical">
                            <div class="uk-modal-header">
                                <div class="uk-child-width-1-2" uk-grid>
                                    <div>
                                        <h2 class="uk-modal-title"></?=lang('Global.topup')?></h2>
                                    </div>
                                    <div class="uk-text-right">
                                        <button class="uk-modal-close uk-icon-button-delete" uk-icon="icon: close;" type="button"></button>
                                    </div>
                                </div>
                            </div>
                            <div class="uk-modal-body">
                                <form class="uk-form-horizontal uk-margin-large" action="pay/topup" method="post">
                                    <div class="uk-margin">
                                        <label class="uk-form-label" for="form-horizontal-text">Name</label>
                                        <div class="uk-form-controls">
                                            <div class="uk-inline uk-width-1-1">
                                                <span class="uk-form-icon" uk-icon="icon: user"></span>
                                                <input class="uk-input ui-autocomplete-input1" type="text" placeholder="Name" id="customer" name="customer" aria-label="Not clickable icon">
                                                <input id="customerx" name="customerid" hidden />
                                            </div>
                                        </div>                                    
                                    </div>
                                    
                                    <script type="text/javascript">
                                        $(function() {
                                            var customerList = [
                                                {label: "Non Member", idx:0},
                                                </?php
                                                    foreach ($customers as $customer) {
                                                        echo '{label:"'.$customer['name'].' / '.$customer['phone'].'",idx:'.$customer['id'].'},';
                                                    }
                                                ?>
                                            ];
                                            $("#customer").autocomplete({
                                                source: customerList,
                                                select: function(e, i) {
                                                    $("#customerx").val(i.item.idx);
                                                },
                                                minLength: 1
                                            });
                                        });
                                    </script>

                                    <div class="uk-margin">
                                        <label class="uk-form-label" for="form-horizontal-text"></?=lang('Global.payment')?></label>
                                        <div class="uk-form-controls">
                                            <div class="uk-inline uk-width-1-1">
                                                <span class="uk-form-icon" uk-icon="icon: credit-card"></span>
                                                <select class="uk-select uk-input" id="payment" name="payment" required >
                                                    <option value="" selected disabled hidden></?=lang('Global.payment')?></option>
                                                    </?php
                                                    foreach ($payments as $pay) {
                                                        if (($pay['outletid'] === $outletPick) || ($pay['outletid'] === '0')) {
                                                            echo '<option value="'.$pay['id'].'">'.$pay['name'].'</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="uk-margin">
                                        <label class="uk-form-label" for="form-horizontal-text"></?=lang('global.value')?></label>
                                        <div class="uk-form-controls">
                                            <div class="uk-inline uk-width-1-1">
                                                <span class="uk-form-icon" uk-icon="icon: database"></span>
                                                <input class="uk-input" min="0" name="value" type="number" placeholder="Point" aria-label="Not clickable icon">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="uk-margin">
                                        <div class="uk-form-controls">
                                            <a class="uk-button uk-button-default" uk-toggle="#topupproof"></?= lang('Global.topupproof') ?></a>
                                        </div>
                                    </div>

                                    <div class="uk-margin" hidden>
                                        <input class="image-tag" name="image">
                                    </div>

                                    <div class="uk-modal-footer uk-text-right">
                                        <button class="uk-button uk-button-primary" type="submit" value="submit"></?= lang('Global.save') ?></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div> -->
                    <!-- Modal Top Up Point End -->

                    <!-- Modal Top Up Proof -->
                    <!-- <div uk-modal class="uk-flex-top" id="topupproof">
                        <div class="uk-modal-dialog uk-margin-auto-vertical">
                            <div class="uk-modal-content">
                                <div class="uk-modal-header">
                                    <div class="uk-flex uk-flex-middle uk-child-width-auto" uk-grid>
                                        <div class="uk-padding-remove uk-margin-medium-left">
                                            <a uk-icon="arrow-left" uk-toggle="#modal-sections" width="35" height="35"></a>
                                        </div>
                                        <div>
                                            <h5 class="uk-modal-title" ></?=lang('Global.topupproof')?></h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="uk-modal-body">
                                    <div class="uk-flex-center uk-child-width-1-1" uk-grid>
                                        <div id="topup_camera"></div>
                                        <div class="uk-text-center uk-margin-small-top">
                                            <input class="uk-button uk-button-primary" id="btnTake" type="button" value="Take Snapshot" onClick="topup_snapshot()">
                                        </div>
                                        <div class="uk-text-center" id="topup_results"></div>
                                    </div> -->

                                    <!-- Script Webcam Top Up Proof -->
                                    <!-- <script type="text/javascript">

                                        Webcam.set({
                                            width: 490,
                                            height: 390,
                                            image_format: 'jpeg',
                                            jpeg_quality: 90,
                                            video: {
                                                facingMode: "environment"
                                            },
                                            constraints: {
                                                facingMode: 'environment'
                                            }
                                        });
                                    
                                        Webcam.attach( '#topup_camera' );

                                        function topup_snapshot() {
                                            Webcam.snap( function(data_uri) {
                                                $(".image-tag").val(data_uri);
                                                document.getElementById('topup_results').innerHTML = '<img src="'+data_uri+'"/>';
                                            } );
                                        }
                                    </script> -->
                                    <!-- Script Webcam Top Up Proof End -->
                                <!-- </div>
                            </div>
                        </div>
                    </div> -->
                    <!-- Modal Top Up Proof End -->

                    <!-- Modal Booking -->
                    <!-- <div uk-modal class="uk-flex-top" id="bookinglist">
                        <div class="uk-modal-dialog uk-margin-auto-vertical">
                            <div class="uk-modal-content">
                                <div class="uk-modal-header">
                                    <div class="uk-child-width-1-2" uk-grid>
                                        <div>
                                            <h5 class="uk-modal-title" id="bookinglist" ></?=lang('Global.bookingList')?></h5>
                                        </div>
                                        <div class="uk-text-right">
                                            <button class="uk-modal-close uk-icon-button-delete" uk-icon="icon: close;" type="button"></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="uk-modal-body">
                                    </?php foreach ($bookings as $book) { ?>
                                        <div id="booklist</?=$book['bookid']?>">
                                            <div class="uk-h5 tm-h5"></?= date('l, d M Y', strtotime($book['bookdate'])); ?></div>
                                            <div class="uk-margin-small-top" uk-grid>
                                                <a class="uk-width-5-6 uk-link-reset" uk-toggle="target: #detail</?= $book['bookid'] ?>">
                                                    <div></?= $book['custname'] ?></div>
                                                    <div></?= $book['bookvalue'] ?></div>
                                                </a>
                                                <div class="uk-width-1-6 uk-light">
                                                    <a uk-icon="trash" class="uk-icon-button-delete" href="pay/delete/</?= $book['bookid'] ?>" onclick="return confirm('</?=lang('Global.deleteConfirm')?>')"></a>
                                                </div>
                                            </div>
                                            <hr>
                                        </div> -->
                                        <!-- <div id="booklist</?=$book['id']?>">
                                            <div class="uk-h5 tm-h5"></?= date('l, d M Y', strtotime($book['created_at'])); ?></div>
                                            <div class="uk-margin-small-top" uk-grid>
                                                <a class="uk-width-5-6 uk-link-reset" uk-toggle="target: #detail</?= $book['id'] ?>">
                                                    </?php
                                                    if ($book['memberid'] === '0') {
                                                        $member = 'Non Member';
                                                    } else {
                                                        foreach ($customers as $cust) {
                                                            if ($book['memberid'] === $cust['id']) {
                                                                $member = $cust['name'].' / '.$cust['phone'];
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                    <div></?= $member ?></div>
                                                    <div></?= $book['value'] ?></div>
                                                </a>
                                                <div class="uk-width-1-6 uk-light">
                                                    <a uk-icon="trash" class="uk-icon-button-delete" href="pay/delete/</?= $book['id'] ?>" onclick="return confirm('</?=lang('Global.deleteConfirm')?>')"></a>
                                                </div>
                                            </div>
                                            <hr>
                                        </div> -->

                                        <!-- Modal Booking Detail -->
                                        <!-- <div uk-modal class="uk-flex-top" id="detail</?= $book['bookid'] ?>">
                                            <div class="uk-modal-dialog uk-margin-auto-vertical">
                                                <div class="uk-modal-content">
                                                    <div class="uk-modal-header">
                                                        <div class="uk-child-width-1-2" uk-grid>
                                                            <div>
                                                                <h5 class="uk-modal-title" id="bookinglist" ></?=lang('Global.bookdetList')?></h5>
                                                            </div>
                                                            <div class="uk-text-right">
                                                                <button class="uk-modal-close uk-icon-button-delete" uk-icon="icon: close;" type="button"></button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="uk-modal-body">
                                                        <input type="number" value="</?=$book['bookid']?>" hidden>
                                                        <div class="uk-h3 tm-h4"></?= $book['custname'] ?></div>
                                                        </?php 
                                                        if (!empty($book['variantdata'])) {
                                                            foreach ($book['variantdata'] as $bookvar) { ?>
                                                                <div class="uk-margin-remove" uk-grid>
                                                                    <div class="uk-width-1-6">
                                                                        <div></?= $bookvar['bookdetqty'] ?></div>
                                                                    </div>
                                                                    <div class="uk-width-2-3">
                                                                        <div></?= $bookvar['prodname'] ?></div>
                                                                    </div>
                                                                    <div class="uk-width-1-6">
                                                                        <div></?= $bookvar['bookdetvalue'] ?></div>
                                                                    </div>
                                                                </div>
                                                            </?php }
                                                        }
                                                        if (!empty($book['bundledata'])) {
                                                            foreach ($book['bundledata'] as $bookbund) { ?>
                                                                <div class="uk-margin-remove" uk-grid>
                                                                    <div class="uk-width-1-6">
                                                                        <div></?= $bookbund['bookbundqty'] ?></div>
                                                                    </div>
                                                                    <div class="uk-width-2-3">
                                                                        <div></?= $bookbund['bundname'] ?></div>
                                                                    </div>
                                                                    <div class="uk-width-1-6">
                                                                        <div></?= $bookbund['bookbundvalue'] ?></div>
                                                                    </div>
                                                                </div>
                                                            </?php }
                                                        } ?>
                                                        <hr>
                                                        <div class="uk-margin">
                                                            <a href="pay/bookprint/</?=$book['bookid']?>" class="uk-button uk-button-default" style="border-radius: 8px; width: 540px;"></?= lang('Global.print') ?></a>
                                                        </div>
                                                        <div>
                                                            <a class="uk-button uk-button-primary" style="border-radius: 8px; width: 540px;" uk-toggle="#tambahdata" onclick="insertBooking</?= $book['bookid'] ?>()"></?= lang('Global.tocart') ?></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> -->
                                        <!-- <div uk-modal class="uk-flex-top" id="detail</?= $book['id'] ?>">
                                            <div class="uk-modal-dialog uk-margin-auto-vertical">
                                                <div class="uk-modal-content">
                                                    <div class="uk-modal-header">
                                                        <div class="uk-child-width-1-2" uk-grid>
                                                            <div>
                                                                <h5 class="uk-modal-title" id="bookinglist" ></?=lang('Global.bookdetList')?></h5>
                                                            </div>
                                                            <div class="uk-text-right">
                                                                <button class="uk-modal-close uk-icon-button-delete" uk-icon="icon: close;" type="button"></button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="uk-modal-body">
                                                        </?php
                                                        if ($book['memberid'] === '0') {
                                                            $member = 'Non Member';
                                                        } else {
                                                            foreach ($customers as $cust) {
                                                                if ($book['memberid'] === $cust['id']) {
                                                                    $member = $cust['name'].' / '.$cust['phone'];
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                        <div class="uk-h3 tm-h4"></?= $member ?></div>
                                                        </?php foreach ($bookingdetails as $bookdet) { 
                                                            if ($bookdet['bookingid'] === $book['id']) {
                                                                if ($bookdet['variantid'] != '0') {
                                                                    foreach ($variants as $variant) {
                                                                        foreach ($products as $product) {
                                                                            if (($product['id'] === $variant['productid']) && ($variant['id'] === $bookdet['variantid'])) {
                                                                                $vname = $product['name'].' - '.$variant['name']; ?>
                                                                                <div class="uk-margin-remove" uk-grid>
                                                                                    <div class="uk-width-1-6">
                                                                                        <div></?= $bookdet['qty'] ?></div>
                                                                                        <input type="number" value="</?=$book['id']?>" name="id" hidden>
                                                                                    </div>
                                                                                    <div class="uk-width-2-3">
                                                                                        <div></?= $vname ?></div>
                                                                                    </div>
                                                                                    <div class="uk-width-1-6">
                                                                                        <div></?= $bookdet['value'] ?></div>
                                                                                    </div>
                                                                                </div>
                                                                            </?php }
                                                                        }
                                                                    }
                                                                } else {
                                                                    foreach ($bundles as $bundle) {
                                                                        if ($bundle['id'] === $bookdet['bundleid']) {
                                                                            $bname = $bundle['name']; ?>
                                                                            <input type="number" value="</?=$book['id']?>" hidden>
                                                                            <div class="uk-margin-remove" uk-grid>
                                                                                <div class="uk-width-1-6">
                                                                                    <div></?= $bookdet['qty'] ?></div>
                                                                                </div>
                                                                                <div class="uk-width-2-3">
                                                                                    <div></?= $bname ?></div>
                                                                                </div>
                                                                                <div class="uk-width-1-6">
                                                                                    <div></?= $bookdet['value'] ?></div>
                                                                                </div>
                                                                            </div>
                                                                        </?php }
                                                                    }
                                                                }
                                                            }
                                                        } ?>
                                                        <hr>
                                                        <div class="uk-margin">
                                                            <a href="pay/bookprint/</?=$book['id']?>" class="uk-button uk-button-default" style="border-radius: 8px; width: 540px;"></?= lang('Global.print') ?></a>
                                                        </div>
                                                        <div>
                                                            <a class="uk-button uk-button-primary" style="border-radius: 8px; width: 540px;" uk-toggle="#tambahdata" onclick="insertBooking</?= $book['id'] ?>()"></?= lang('Global.tocart') ?></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> -->
                                        <!-- Modal Booking Detail End -->

                                        <!-- Script Booking -->
                                        <!-- <script type="text/javascript">
                                            function insertBooking</?= $book['bookid'] ?>() {

                                                document.getElementById("customername").value = '</?=$book['custname']?>';
                                                document.getElementById("customerid").value = '</?=$book['custid']?>';

                                                var products = document.getElementById('products');

                                                document.getElementById('booklist</?= $book['bookid'] ?>').remove();
                                                
                                                var oldproducts = document.querySelector('#products');
                                                var oldproductschild = oldproducts.lastElementChild;
                                                while (oldproductschild) {
                                                    oldproducts.removeChild(oldproductschild);
                                                    oldproductschild = oldproducts.lastElementChild;
                                                }
                                                </?php
                                                $bookqty        = array();
                                                $bookbundqty    = array();
                                                if (!empty($book['variantdata'])) {
                                                    foreach ($book['variantdata'] as $bookdet) {
                                                        echo 'var count = '.$bookdet['bookdetqty'].';';
                                                        $bookqty[$bookdet['bookvarid']] = $bookdet['bookdetqty'];
                                                        $Price      = (Int)$bookdet['bookvarprice'];
                                                        $ProdName   = $bookdet['prodname']; ?>

                                                        if ( $( "#product</?= $book['bookid'] ?></?=$bookdet['bookvarid']?>" ).length ) {
                                                            alert('</?=lang('Global.readyAdd');?>');
                                                        } else {
                                                            </?php
                                                                echo 'let stock = '.$bookdet['stock'].';';
                                                            ?>
                                                            var minstock = 1;
                                                            var minval = count;
                                                            
                                                            var productgrid = document.createElement('div');
                                                            productgrid.setAttribute('id', 'product</?= $book['bookid'] ?></?=$bookdet['bookvarid']?>');
                                                            productgrid.setAttribute('class', 'uk-margin-small');
                                                            productgrid.setAttribute('uk-grid', '');

                                                            var addcontainer = document.createElement('div');
                                                            addcontainer.setAttribute('class', 'uk-flex uk-flex-middle uk-width-1-6');
                                                            
                                                            var productqtyinputadd = document.createElement('div');
                                                            productqtyinputadd.setAttribute('id','addqty</?=$bookdet['bookvarid']?>');
                                                            productqtyinputadd.setAttribute('class','tm-h2 pointerbutton uk-button uk-button-small uk-button-primary');
                                                            productqtyinputadd.innerHTML = '+';

                                                            var delcontainer = document.createElement('div');
                                                            delcontainer.setAttribute('class', 'uk-flex uk-flex-middle uk-width-1-6');
                                                            
                                                            var productqtyinputdel = document.createElement('div');
                                                            productqtyinputdel.setAttribute('id','delqty</?=$bookdet['bookvarid']?>');
                                                            productqtyinputdel.setAttribute('class','tm-h2 pointerbutton uk-button uk-button-small uk-button-danger');
                                                            productqtyinputdel.innerHTML = '-';

                                                            var quantitycontainer = document.createElement('div');
                                                            quantitycontainer.setAttribute('class', 'tm-h2 uk-flex uk-flex-middle uk-width-1-6');

                                                            var productqty = document.createElement('div');                                               

                                                            var inputqty = document.createElement('input');
                                                            inputqty.setAttribute('type', 'number');
                                                            inputqty.setAttribute('id', "qty[</?=$bookdet['bookvarid']?>]");
                                                            inputqty.setAttribute('name', "qty[</?=$bookdet['bookvarid']?>]");
                                                            inputqty.setAttribute('class', 'uk-input uk-form-width-xsmall');
                                                            inputqty.setAttribute('min', minstock);
                                                            inputqty.setAttribute('max', stock);
                                                            inputqty.setAttribute('value', '</?= $bookdet['bookdetqty'] ?>');
                                                            inputqty.setAttribute('onchange', 'showprice()');
                                                            
                                                            var handleIncrement = () => {
                                                                count++;
                                                                if (inputqty.value == stock) {
                                                                    inputqty.value = stock;
                                                                    count = stock;
                                                                    alert('</?=lang('Global.alertstock')?>');
                                                                } else {
                                                                    inputqty.value = count;
                                                                    var price = count * </?=$Price?>;
                                                                    var bargainprice = varbargain.value * inputqty.value;
                                                                    if(varbargain.value){
                                                                        document.getElementById('price</?=$bookdet['bookvarid']?>').innerHTML = bargainprice;
                                                                    }
                                                                    else {
                                                                        productprice.innerHTML = price;
                                                                        productprice.value = price;
                                                                    }
                                                                }
                                                            };
                                                            
                                                            var handleDecrement = () => {
                                                                count--;
                                                                if (inputqty.value == '1') {
                                                                    inputqty.value = '0';
                                                                    inputqty.remove();                                                                                                
                                                                    productgrid.remove();
                                                                } else {
                                                                    inputqty.value = count;
                                                                    var price = count * </?=$Price?>;
                                                                    var bargainprice = varbargain.value * inputqty.value;
                                                                    if(varbargain.value){
                                                                        document.getElementById('price</?=$bookdet['bookvarid']?>').innerHTML = bargainprice;
                                                                    }
                                                                    else {
                                                                        productprice.innerHTML = price;
                                                                        productprice.value = price;
                                                                    }
                                                                }
                                                            };

                                                            productqtyinputadd.addEventListener("click", handleIncrement);
                                                            productqtyinputdel.addEventListener("click", handleDecrement);

                                                            var namecontainer = document.createElement('div');
                                                            namecontainer.setAttribute('class', 'uk-flex uk-flex-middle uk-width-1-3');

                                                            var productname = document.createElement('div');
                                                            productname.setAttribute('id', 'name</?=$bookdet['bookvarid']?>');
                                                            productname.setAttribute('class', 'tm-h2');
                                                            productname.innerHTML = '</?=$ProdName?>';

                                                            var pricecontainer = document.createElement('div');
                                                            pricecontainer.setAttribute('class', 'uk-flex uk-flex-middle uk-width-1-6');
                                                            
                                                            var productprice = document.createElement('div');
                                                            productprice.setAttribute('id', 'price</?=$bookdet['bookvarid']?>');
                                                            productprice.setAttribute('class', 'tm-h2');
                                                            productprice.setAttribute('name', 'price[]');
                                                            productprice.setAttribute('value', showprice())
                                                            productprice.innerHTML = showprice();

                                                            // var varpricecontainer = document.createElement('div');
                                                            // varpricecontainer.setAttribute('class', 'uk-margin-small uk-flex uk-flex-middle uk-width-1-2');

                                                            // var varbardiv = document.createElement('div');
                                                            // varbardiv.setAttribute('class','uk-margin uk-margin-small uk-flex uk-flex-middle uk-width-1-2');

                                                            // var varbarlab = document.createElement('label');
                                                            // varbarlab.setAttribute('class','uk-form-label uk-margin-remove uk-text-bold uk-text-small uk-h4');

                                                            // var varbartext = document.createTextNode("Variant Bargain");

                                                            // var varbarform = document.createElement('div');
                                                            // varbarform.setAttribute('class','uk-form-controls');

                                                            // var varbargain = document.createElement('input');
                                                            // varbargain.setAttribute('class', 'uk-input uk-form-width-small');
                                                            // varbargain.setAttribute('id', 'varbargain</?=$bookdet['bookvarid']?>');
                                                            // varbargain.setAttribute('placeholder', '0');
                                                            // varbargain.setAttribute('name', 'varbargain[</?=$bookdet['bookvarid']?>]');
                                                            // varbargain.setAttribute('min', "0");
                                                            // varbargain.setAttribute('type', 'number');

                                                            var varvaluecontainer = document.createElement('div');
                                                            varvaluecontainer.setAttribute('class', 'uk-margin-small uk-flex uk-flex-middle uk-width-1-2');

                                                            var varpricediv = document.createElement('div');
                                                            varpricediv.setAttribute('class','uk-margin uk-margin-small uk-flex uk-flex-middle uk-width-1-2');

                                                            var varpricelab = document.createElement('label');
                                                            varpricelab.setAttribute('class','uk-form-label uk-margin-remove uk-text-bold uk-text-small uk-h4' );

                                                            var varpricetext = document.createTextNode("Discount Variant");

                                                            var varpriceform = document.createElement('div');
                                                            varpriceform.setAttribute('class','uk-form-controls');
                                                            
                                                            var varprice = document.createElement('input');
                                                            varprice.setAttribute('class', 'uk-input uk-form-width-small varprice');
                                                            varprice.setAttribute('data-index', '</?=$bookdet['bookvarid']?>');
                                                            varprice.setAttribute('id', 'varprice</?=$bookdet['bookvarid']?>');
                                                            varprice.setAttribute('placeholder', '0');
                                                            varprice.setAttribute('name', 'varprice[</?=$bookdet['bookvarid']?>]');
                                                            varprice.setAttribute('value', '0');
                                                            varprice.setAttribute('type', 'number');
                                                            varprice.setAttribute('min', '0');


                                                            function showprice() {
                                                                var qty = inputqty.value;
                                                                var price = qty * </?=$Price?>;
                                                                return price;
                                                                productprice.innerHTML = price;
                                                            }

                                                            inputqty.onchange = function() {showprice()};

                                                            // varbargain.onchange = function() {
                                                            //     var bargainprice = varbargain.value * inputqty.value;
                                                            //     if (bargainprice) {
                                                            //         document.getElementById('price</?=$bookdet['bookvarid']?>').innerHTML = bargainprice;
                                                            //     } else {
                                                            //         document.getElementById('price</?=$bookdet['bookvarid']?>').innerHTML = showprice();
                                                            //     }
                                                            // }

                                                            varprice.onchange = function() {
                                                                var discvar = varprice.value * inputqty.value;
                                                                if (discvar) {
                                                                    document.getElementById('price</?=$bookdet['bookvarid']?>').innerHTML = showprice() - discvar;
                                                                } else {
                                                                    document.getElementById('price</?=$bookdet['bookvarid']?>').innerHTML = showprice();
                                                                }
                                                            }

                                                            addcontainer.appendChild(productqtyinputadd);
                                                            productqty.appendChild(inputqty);
                                                            quantitycontainer.appendChild(productqty);
                                                            delcontainer.appendChild(productqtyinputdel);
                                                            pricecontainer.appendChild(productprice);
                                                            namecontainer.appendChild(productname);
                                                            // varpricecontainer.appendChild(varbardiv);
                                                            // varbardiv.appendChild(varbarlab);
                                                            // varbarlab.appendChild(varbartext);
                                                            // varbarlab.appendChild(varbarform);
                                                            // varbarform.appendChild(varbargain);
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
                                                            // productgrid.appendChild(varpricecontainer);
                                                            productgrid.appendChild(varvaluecontainer);
                                                            products.appendChild(productgrid);
                                                        }
                                                    </?php }
                                                } ?>
                                                
                                                </?php
                                                if (!empty($book['bundledata'])) {
                                                    foreach ($book['bundledata'] as $bundle) {
                                                        $BunName    = $bundle['bundname'];
                                                        $BunPrice   = $bundle['bookbundprice'];
                                                            $i = 0;
                                                            foreach ($bundleVariants as $variant) {
                                                                if (($variant->bundleid === $bundle['bookbundid']) && ($variant->outletid === $outletPick)) {
                                                                    $i++;
                                                                    if ($i === 1) {
                                                                        echo 'var bstock = '.$variant->qty.';';
                                                                        $bookbundqty[$variant->id] = $bundle['bookbundqty'];
                                                                    }
                                                                }
                                                            } ?>

                                                            var minbstock = 1; 
                                                            var minbval = count;
                                                            
                                                            var bundlegrid = document.createElement('div');
                                                            bundlegrid.setAttribute('id', 'bundle</?= $book['bookid'] ?></?= $bundle['bookbundid'] ?>');
                                                            bundlegrid.setAttribute('class', 'uk-margin-small');
                                                            bundlegrid.setAttribute('uk-grid', '');

                                                            var addbundlecontainer = document.createElement('div');
                                                            addbundlecontainer.setAttribute('class', 'uk-flex uk-flex-middle uk-width-1-6');
                                                            
                                                            var bunldeqtyinputadd = document.createElement('div');
                                                            bunldeqtyinputadd.setAttribute('id','addbqty</?= $bundle['bookbundid'] ?>');
                                                            bunldeqtyinputadd.setAttribute('class','tm-h2 pointerbutton uk-button uk-button-small uk-button-primary');
                                                            bunldeqtyinputadd.innerHTML = '+';

                                                            var delbundlecontainer = document.createElement('div');
                                                            delbundlecontainer.setAttribute('class', 'uk-flex uk-flex-middle uk-width-1-6');
                                                            
                                                            var bundleqtyinputdel = document.createElement('div');
                                                            bundleqtyinputdel.setAttribute('id','delbqty</?= $bundle['bookbundid'] ?>');
                                                            bundleqtyinputdel.setAttribute('class','tm-h2 pointerbutton uk-button uk-button-small uk-button-danger');
                                                            bundleqtyinputdel.innerHTML = '-';

                                                            var bundleqtycontainer = document.createElement('div');
                                                            bundleqtycontainer.setAttribute('class', 'tm-h2 uk-flex uk-flex-middle uk-width-1-6');

                                                            var bundleqty = document.createElement('div');                                               

                                                            var bundleinputqty = document.createElement('input');
                                                            bundleinputqty.setAttribute('type', 'number');
                                                            bundleinputqty.setAttribute('id', "bqty[</?= $bundle['bookbundid'] ?>]");
                                                            bundleinputqty.setAttribute('name', "bqty[</?= $bundle['bookbundid'] ?>]");
                                                            bundleinputqty.setAttribute('class', 'uk-input uk-form-width-xsmall');
                                                            bundleinputqty.setAttribute('min', minbstock);
                                                            bundleinputqty.setAttribute('max', bstock);
                                                            bundleinputqty.setAttribute('value', '</?= $bundle['bookbundqty'] ?>');
                                                            bundleinputqty.setAttribute('onchange', 'showbprice()');
                                                            
                                                            var handleIncrements = () => {
                                                                count++;
                                                                if (bundleinputqty.value == bstock) {
                                                                    bundleinputqty.value = bstock;
                                                                    count = bstock;
                                                                    alert('</?=lang('Global.alertstock')?>');
                                                                } else {
                                                                    bundleinputqty.value = count;
                                                                    var bprice = count * </?= $BunPrice ?>;
                                                                    bundleprice.innerHTML = bprice;
                                                                    bundleprice.value = bprice;
                                                                }
                                                            };
                                                            
                                                            var handleDecrements = () => {
                                                                count--;
                                                                if (bundleinputqty.value == '1') {
                                                                    bundleinputqty.value = '0';
                                                                    bundleinputqty.remove();
                                                                    bundlegrid.remove();
                                                                } else {
                                                                    bundleinputqty.value = count;
                                                                    var bprice = count * </?= $BunPrice ?>;
                                                                    bundleprice.innerHTML = bprice;
                                                                }
                                                            };

                                                            bunldeqtyinputadd.addEventListener("click", handleIncrements);
                                                            bundleqtyinputdel.addEventListener("click", handleDecrements);

                                                            var bundlenamecontainer = document.createElement('div');
                                                            bundlenamecontainer.setAttribute('class', 'uk-flex uk-flex-middle uk-width-1-3');

                                                            var bundlename = document.createElement('div');
                                                            bundlename.setAttribute('id', 'name</?= $bundle['bookbundid'] ?>');
                                                            bundlename.setAttribute('class', 'tm-h2');
                                                            bundlename.innerHTML = '</?= $BunName ?>';

                                                            var bpricecontainer = document.createElement('div');
                                                            bpricecontainer.setAttribute('class', 'uk-flex uk-flex-middle uk-width-1-6');
                                                            
                                                            var bundleprice = document.createElement('div');
                                                            bundleprice.setAttribute('id', 'bprice</?= $bundle['bookbundid'] ?>');
                                                            bundleprice.setAttribute('class', 'tm-h2');
                                                            bundleprice.setAttribute('name', 'price[]');
                                                            bundleprice.setAttribute('value', showbprice());
                                                            bundleprice.innerHTML = showbprice();

                                                            function showbprice() {
                                                                var bqty = bundleinputqty.value;
                                                                var bprice = bqty * </?= $BunPrice ?>;
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
                                                    </?php }
                                                }
                                                
                                                $datavar = json_encode($bookqty);
                                                $databund = json_encode($bookbundqty);
                                                ?>
                                                $.ajax({
                                                    url: "transaction/restorestock",
                                                    type: "POST",    
                                                    data: {
                                                        bookingid: </?=$book['bookid']?>,
                                                        datavar: </?=$datavar?>,
                                                        databund: </?=$databund?>
                                                    },
                                                    success: function(response) {
                                                        console.log(response);
                                                    },
                                                    error: function(response) {
                                                        console.log(response);
                                                    }   
                                                });
                                            }
                                            // function insertBooking</?= $book['id'] ?>() {

                                            //     </?php
                                            //     foreach ($customers as $customer) {
                                            //         if ($book['memberid'] === '0') {
                                            //     ?>
                                            //             document.getElementById("customername").value = 'Non Member';
                                            //             document.getElementById("customerid").value = '0';
                                            //     </?php } elseif ($book['memberid'] === $customer['id']) { ?>
                                            //             document.getElementById("customername").value = '</?=$customer['name']?>';
                                            //             document.getElementById("customerid").value = '</?=$customer['id']?>';
                                            //     </?php
                                            //         }
                                            //     }
                                            //     ?>

                                            //     var products = document.getElementById('products');

                                            //     document.getElementById('booklist</?= $book['id'] ?>').remove();
                                                
                                            //     var oldproducts = document.querySelector('#products');
                                            //     var oldproductschild = oldproducts.lastElementChild;
                                            //     while (oldproductschild) {
                                            //         oldproducts.removeChild(oldproductschild);
                                            //         oldproductschild = oldproducts.lastElementChild;
                                            //     }
                                            //     </?php
                                            //     $bookqty = array();
                                            //     $bookbundqty = array();
                                            //     foreach ($bookingdetails as $bookdet) {
                                            //         echo 'var count = '.$bookdet['qty'].';';
                                            //         foreach ($products as $product) {
                                            //             foreach ($variants as $variant) {
                                            //                 if (($bookdet['bookingid'] === $book['id']) && ($variant['id'] === $bookdet['variantid']) && ($variant['productid'] === $product['id'])) {
                                            //                     $bookqty[$variant['id']] = $bookdet['qty'];
                                            //                     $VarName    = $variant['name'];
                                            //                     $Price      = (Int)$variant['hargamodal'] + (Int)$variant['hargajual'];
                                            //                     $ProdName   = $product['name'].' - '. $variant['name']; ?>

                                            //                     if ( $( "#product</?= $book['id'] ?></?=$variant['id']?>" ).length ) {
                                            //                         alert('</?=lang('Global.readyAdd');?>');
                                            //                     } else {
                                            //                         </?php
                                            //                         foreach ($stocks as $stock) {
                                            //                             if (($stock['variantid'] === $variant['id']) && ($stock['outletid'] === $outletPick)) {
                                            //                                 echo 'let stock = '.$stock['qty'].';';
                                            //                                 if ($stock['qty'] === '0') {
                                            //                                     echo 'alert("'.lang('Global.alertstock').'")';
                                            //                                 } else {
                                            //                         ?>
                                            //                                     var minstock = 1;
                                            //                                     var minval = count;
                                                                                
                                            //                                     var productgrid = document.createElement('div');
                                            //                                     productgrid.setAttribute('id', 'product</?= $book['id'] ?></?=$variant['id']?>');
                                            //                                     productgrid.setAttribute('class', 'uk-margin-small');
                                            //                                     productgrid.setAttribute('uk-grid', '');

                                            //                                     var addcontainer = document.createElement('div');
                                            //                                     addcontainer.setAttribute('class', 'uk-flex uk-flex-middle uk-width-1-6');
                                                                                
                                            //                                     var productqtyinputadd = document.createElement('div');
                                            //                                     productqtyinputadd.setAttribute('id','addqty</?=$variant['id']?>');
                                            //                                     productqtyinputadd.setAttribute('class','tm-h2 pointerbutton uk-button uk-button-small uk-button-primary');
                                            //                                     productqtyinputadd.innerHTML = '+';

                                            //                                     var delcontainer = document.createElement('div');
                                            //                                     delcontainer.setAttribute('class', 'uk-flex uk-flex-middle uk-width-1-6');
                                                                                
                                            //                                     var productqtyinputdel = document.createElement('div');
                                            //                                     productqtyinputdel.setAttribute('id','delqty</?=$variant['id']?>');
                                            //                                     productqtyinputdel.setAttribute('class','tm-h2 pointerbutton uk-button uk-button-small uk-button-danger');
                                            //                                     productqtyinputdel.innerHTML = '-';

                                            //                                     var quantitycontainer = document.createElement('div');
                                            //                                     quantitycontainer.setAttribute('class', 'tm-h2 uk-flex uk-flex-middle uk-width-1-6');

                                            //                                     var productqty = document.createElement('div');                                               

                                            //                                     var inputqty = document.createElement('input');
                                            //                                     inputqty.setAttribute('type', 'number');
                                            //                                     inputqty.setAttribute('id', "qty[</?=$variant['id']?>]");
                                            //                                     inputqty.setAttribute('name', "qty[</?=$variant['id']?>]");
                                            //                                     inputqty.setAttribute('class', 'uk-input uk-form-width-xsmall');
                                            //                                     inputqty.setAttribute('min', minstock);
                                            //                                     inputqty.setAttribute('max', stock);
                                            //                                     inputqty.setAttribute('value', '</?= $bookdet['qty'] ?>');
                                            //                                     inputqty.setAttribute('onchange', 'showprice()');
                                                                                
                                            //                                     var handleIncrement = () => {
                                            //                                         count++;
                                            //                                         if (inputqty.value == stock) {
                                            //                                             inputqty.value = stock;
                                            //                                             count = stock;
                                            //                                             alert('</?=lang('Global.alertstock')?>');
                                            //                                         } else {
                                            //                                             inputqty.value = count;
                                            //                                             var price = count * </?=$Price?>;
                                            //                                             var bargainprice = varbargain.value * inputqty.value;
                                            //                                             if(varbargain.value){
                                            //                                                 document.getElementById('price</?=$variant['id']?>').innerHTML = bargainprice;
                                            //                                             }
                                            //                                             else {
                                            //                                                 productprice.innerHTML = price;
                                            //                                                 productprice.value = price;
                                            //                                             }
                                            //                                         }
                                            //                                     };
                                                                                
                                            //                                     var handleDecrement = () => {
                                            //                                         count--;
                                            //                                         if (inputqty.value == '1') {
                                            //                                             inputqty.value = '0';
                                            //                                             inputqty.remove();                                                                                                
                                            //                                             productgrid.remove();
                                            //                                         } else {
                                            //                                             inputqty.value = count;
                                            //                                             var price = count * </?=$Price?>;
                                            //                                             var bargainprice = varbargain.value * inputqty.value;
                                            //                                             if(varbargain.value){
                                            //                                                 document.getElementById('price</?=$variant['id']?>').innerHTML = bargainprice;
                                            //                                             }
                                            //                                             else {
                                            //                                                 productprice.innerHTML = price;
                                            //                                                 productprice.value = price;
                                            //                                             }
                                            //                                         }
                                            //                                     };

                                            //                                     productqtyinputadd.addEventListener("click", handleIncrement);
                                            //                                     productqtyinputdel.addEventListener("click", handleDecrement);

                                            //                                     var namecontainer = document.createElement('div');
                                            //                                     namecontainer.setAttribute('class', 'uk-flex uk-flex-middle uk-width-1-3');

                                            //                                     var productname = document.createElement('div');
                                            //                                     productname.setAttribute('id', 'name</?=$variant['id']?>');
                                            //                                     productname.setAttribute('class', 'tm-h2');
                                            //                                     productname.innerHTML = '</?=$ProdName?>';

                                            //                                     var pricecontainer = document.createElement('div');
                                            //                                     pricecontainer.setAttribute('class', 'uk-flex uk-flex-middle uk-width-1-6');
                                                                                
                                            //                                     var productprice = document.createElement('div');
                                            //                                     productprice.setAttribute('id', 'price</?=$variant['id']?>');
                                            //                                     productprice.setAttribute('class', 'tm-h2');
                                            //                                     productprice.setAttribute('name', 'price[]');
                                            //                                     productprice.setAttribute('value', showprice())
                                            //                                     productprice.innerHTML = showprice();

                                            //                                     var varpricecontainer = document.createElement('div');
                                            //                                     varpricecontainer.setAttribute('class', 'uk-margin-small uk-flex uk-flex-middle uk-width-1-2');

                                            //                                     var varbardiv = document.createElement('div');
                                            //                                     varbardiv.setAttribute('class','uk-margin uk-margin-small uk-flex uk-flex-middle uk-width-1-2');

                                            //                                     var varbarlab = document.createElement('label');
                                            //                                     varbarlab.setAttribute('class','uk-form-label uk-margin-remove uk-text-bold uk-text-small uk-h4');

                                            //                                     var varbartext = document.createTextNode("Variant Bargain");

                                            //                                     var varbarform = document.createElement('div');
                                            //                                     varbarform.setAttribute('class','uk-form-controls');

                                            //                                     var varbargain = document.createElement('input');
                                            //                                     varbargain.setAttribute('class', 'uk-input uk-form-width-small');
                                            //                                     varbargain.setAttribute('id', 'varbargain</?=$variant['id']?>');
                                            //                                     varbargain.setAttribute('placeholder', '0');
                                            //                                     varbargain.setAttribute('name', 'varbargain[</?=$variant['id']?>]');
                                            //                                     varbargain.setAttribute('min', "0");
                                            //                                     varbargain.setAttribute('type', 'number');

                                            //                                     var varvaluecontainer = document.createElement('div');
                                            //                                     varvaluecontainer.setAttribute('class', 'uk-margin-small uk-flex uk-flex-middle uk-width-1-2');

                                            //                                     var varpricediv = document.createElement('div');
                                            //                                     varpricediv.setAttribute('class','uk-margin uk-margin-small uk-flex uk-flex-middle uk-width-1-2');

                                            //                                     var varpricelab = document.createElement('label');
                                            //                                     varpricelab.setAttribute('class','uk-form-label uk-margin-remove uk-text-bold uk-text-small uk-h4' );

                                            //                                     var varpricetext = document.createTextNode("Discount Variant");

                                            //                                     var varpriceform = document.createElement('div');
                                            //                                     varpriceform.setAttribute('class','uk-form-controls');
                                                                                
                                            //                                     var varprice = document.createElement('input');
                                            //                                     varprice.setAttribute('class', 'uk-input uk-form-width-small varprice');
                                            //                                     varprice.setAttribute('data-index', '</?=$variant['id']?>');
                                            //                                     varprice.setAttribute('id', 'varprice</?=$variant['id']?>');
                                            //                                     varprice.setAttribute('placeholder', '0');
                                            //                                     varprice.setAttribute('name', 'varprice[</?=$variant['id']?>]');
                                            //                                     varprice.setAttribute('value', '0');
                                            //                                     varprice.setAttribute('type', 'number');
                                            //                                     varprice.setAttribute('min', '0');


                                            //                                     function showprice() {
                                            //                                         var qty = inputqty.value;
                                            //                                         var price = qty * </?=$Price?>;
                                            //                                         return price;
                                            //                                         productprice.innerHTML = price;
                                            //                                     }

                                            //                                     inputqty.onchange = function() {showprice()};

                                            //                                     varbargain.onchange = function() {
                                            //                                         var bargainprice = varbargain.value * inputqty.value;
                                            //                                         if (bargainprice) {
                                            //                                             document.getElementById('price</?=$variant['id']?>').innerHTML = bargainprice;
                                            //                                         } else {
                                            //                                             document.getElementById('price</?=$variant['id']?>').innerHTML = showprice();
                                            //                                         }
                                            //                                     }

                                            //                                     varprice.onchange = function() {
                                            //                                         var discvar = varprice.value * inputqty.value;
                                            //                                         if (discvar) {
                                            //                                             document.getElementById('price</?=$variant['id']?>').innerHTML = showprice() - discvar;
                                            //                                         } else {
                                            //                                             document.getElementById('price</?=$variant['id']?>').innerHTML = showprice();
                                            //                                         }
                                            //                                     }

                                            //                                     addcontainer.appendChild(productqtyinputadd);
                                            //                                     productqty.appendChild(inputqty);
                                            //                                     quantitycontainer.appendChild(productqty);
                                            //                                     delcontainer.appendChild(productqtyinputdel);
                                            //                                     pricecontainer.appendChild(productprice);
                                            //                                     namecontainer.appendChild(productname);
                                            //                                     varpricecontainer.appendChild(varbardiv);
                                            //                                     varbardiv.appendChild(varbarlab);
                                            //                                     varbarlab.appendChild(varbartext);
                                            //                                     varbarlab.appendChild(varbarform);
                                            //                                     varbarform.appendChild(varbargain);
                                            //                                     varvaluecontainer.appendChild(varpricediv);
                                            //                                     varpricediv.appendChild(varpricelab);
                                            //                                     varpricelab.appendChild(varpricetext);
                                            //                                     varpricelab.appendChild(varpriceform);
                                            //                                     varpriceform.appendChild(varprice);
                                            //                                     productgrid.appendChild(delcontainer);
                                            //                                     productgrid.appendChild(quantitycontainer);
                                            //                                     productgrid.appendChild(addcontainer);
                                            //                                     productgrid.appendChild(namecontainer);
                                            //                                     productgrid.appendChild(pricecontainer);
                                            //                                     productgrid.appendChild(pricecontainer);
                                            //                                     productgrid.appendChild(varpricecontainer);
                                            //                                     productgrid.appendChild(varvaluecontainer);
                                            //                                     products.appendChild(productgrid);
                                            //                                 </?php }
                                            //                             }
                                            //                         } ?>
                                            //                     }
                                            //                 </?php }
                                            //             }
                                            //         } ?>
                                                    
                                            //         </?php foreach ($bundles as $bundle) {

                                            //             $BunName = $bundle['name'];
                                            //             $BunPrice = $bundle['price'];
                                            //             if (($bookdet['bookingid'] === $book['id']) && ($bundle['id'] === $bookdet['bundleid'])) {
                                            //                 foreach ($bundets as $bundet) {
                                            //                     if ($bundle['id'] === $bundet['bundleid']) {
                                            //                         $i = 0;
                                            //                         foreach ($bundleVariants as $variant) {
                                            //                             if (($variant->bundleid === $bundle['id']) && ($variant->outletid === $outletPick)) {
                                            //                                 $i++;
                                            //                                 if ($i === 1) {
                                            //                                     echo 'var bstock = '.$variant->qty.';';
                                            //                                     $bookbundqty[$variant->id] = $bookdet['qty'];
                                            //                                 }
                                            //                             }
                                            //                         }
                                            //                     }
                                            //                 } ?>

                                            //                 var minbstock = 1; 
                                            //                 var minbval = count;
                                                            
                                            //                 var bundlegrid = document.createElement('div');
                                            //                 bundlegrid.setAttribute('id', 'bundle</?= $book['id'] ?></?= $bundle['id'] ?>');
                                            //                 bundlegrid.setAttribute('class', 'uk-margin-small');
                                            //                 bundlegrid.setAttribute('uk-grid', '');

                                            //                 var addbundlecontainer = document.createElement('div');
                                            //                 addbundlecontainer.setAttribute('class', 'uk-flex uk-flex-middle uk-width-1-6');
                                                            
                                            //                 var bunldeqtyinputadd = document.createElement('div');
                                            //                 bunldeqtyinputadd.setAttribute('id','addbqty</?= $bundle['id'] ?>');
                                            //                 bunldeqtyinputadd.setAttribute('class','tm-h2 pointerbutton uk-button uk-button-small uk-button-primary');
                                            //                 bunldeqtyinputadd.innerHTML = '+';

                                            //                 var delbundlecontainer = document.createElement('div');
                                            //                 delbundlecontainer.setAttribute('class', 'uk-flex uk-flex-middle uk-width-1-6');
                                                            
                                            //                 var bundleqtyinputdel = document.createElement('div');
                                            //                 bundleqtyinputdel.setAttribute('id','delbqty</?= $bundle['id'] ?>');
                                            //                 bundleqtyinputdel.setAttribute('class','tm-h2 pointerbutton uk-button uk-button-small uk-button-danger');
                                            //                 bundleqtyinputdel.innerHTML = '-';

                                            //                 var bundleqtycontainer = document.createElement('div');
                                            //                 bundleqtycontainer.setAttribute('class', 'tm-h2 uk-flex uk-flex-middle uk-width-1-6');

                                            //                 var bundleqty = document.createElement('div');                                               

                                            //                 var bundleinputqty = document.createElement('input');
                                            //                 bundleinputqty.setAttribute('type', 'number');
                                            //                 bundleinputqty.setAttribute('id', "bqty[</?= $bundle['id'] ?>]");
                                            //                 bundleinputqty.setAttribute('name', "bqty[</?= $bundle['id'] ?>]");
                                            //                 bundleinputqty.setAttribute('class', 'uk-input uk-form-width-xsmall');
                                            //                 bundleinputqty.setAttribute('min', minbstock);
                                            //                 bundleinputqty.setAttribute('max', bstock);
                                            //                 bundleinputqty.setAttribute('value', '</?= $bookdet['qty'] ?>');
                                            //                 bundleinputqty.setAttribute('onchange', 'showbprice()');
                                                            
                                            //                 var handleIncrements = () => {
                                            //                     count++;
                                            //                     if (bundleinputqty.value == bstock) {
                                            //                         bundleinputqty.value = bstock;
                                            //                         count = bstock;
                                            //                         alert('</?=lang('Global.alertstock')?>');
                                            //                     } else {
                                            //                         bundleinputqty.value = count;
                                            //                         var bprice = count * </?= $BunPrice ?>;
                                            //                         bundleprice.innerHTML = bprice;
                                            //                         bundleprice.value = bprice;
                                            //                     }
                                            //                 };
                                                            
                                            //                 var handleDecrements = () => {
                                            //                     count--;
                                            //                     if (bundleinputqty.value == '1') {
                                            //                         bundleinputqty.value = '0';
                                            //                         bundleinputqty.remove();
                                            //                         bundlegrid.remove();
                                            //                     } else {
                                            //                         bundleinputqty.value = count;
                                            //                         var bprice = count * </?= $BunPrice ?>;
                                            //                         bundleprice.innerHTML = bprice;
                                            //                     }
                                            //                 };

                                            //                 bunldeqtyinputadd.addEventListener("click", handleIncrements);
                                            //                 bundleqtyinputdel.addEventListener("click", handleDecrements);

                                            //                 var bundlenamecontainer = document.createElement('div');
                                            //                 bundlenamecontainer.setAttribute('class', 'uk-flex uk-flex-middle uk-width-1-3');

                                            //                 var bundlename = document.createElement('div');
                                            //                 bundlename.setAttribute('id', 'name</?= $bundle['id'] ?>');
                                            //                 bundlename.setAttribute('class', 'tm-h2');
                                            //                 bundlename.innerHTML = '</?= $BunName ?>';

                                            //                 var bpricecontainer = document.createElement('div');
                                            //                 bpricecontainer.setAttribute('class', 'uk-flex uk-flex-middle uk-width-1-6');
                                                            
                                            //                 var bundleprice = document.createElement('div');
                                            //                 bundleprice.setAttribute('id', 'bprice</?= $bundle['id'] ?>');
                                            //                 bundleprice.setAttribute('class', 'tm-h2');
                                            //                 bundleprice.setAttribute('name', 'price[]');
                                            //                 bundleprice.setAttribute('value', showbprice());
                                            //                 bundleprice.innerHTML = showbprice();

                                            //                 function showbprice() {
                                            //                     var bqty = bundleinputqty.value;
                                            //                     var bprice = bqty * </?= $BunPrice ?>;
                                            //                     return bprice;
                                            //                     bundleprice.innerHTML = bprice;
                                            //                 }

                                            //                 bundleinputqty.onchange = function() {showbprice()};

                                            //                 addbundlecontainer.appendChild(bunldeqtyinputadd);
                                            //                 bundleqty.appendChild(bundleinputqty);
                                            //                 bundleqtycontainer.appendChild(bundleqty);
                                            //                 delbundlecontainer.appendChild(bundleqtyinputdel);
                                            //                 bundlegrid.appendChild(delbundlecontainer);
                                            //                 bundlegrid.appendChild(bundleqtycontainer);
                                            //                 bundlegrid.appendChild(addbundlecontainer);
                                            //                 bundlenamecontainer.appendChild(bundlename);
                                            //                 bundlegrid.appendChild(bundlenamecontainer);
                                            //                 bpricecontainer.appendChild(bundleprice);
                                            //                 bundlegrid.appendChild(bpricecontainer);
                                            //                 products.appendChild(bundlegrid);
                                            //             </?php }
                                            //         }
                                            //     } ?>
                                            //     </?php
                                            //     $datavar = json_encode($bookqty);
                                            //     $databund = json_encode($bookbundqty);
                                            //     ?>
                                            //     $.ajax({
                                            //         url: "transaction/restorestock",
                                            //         type: "POST",    
                                            //         data: {
                                            //             bookingid: </?=$book['id']?>,
                                            //             datavar: </?=$datavar?>,
                                            //             databund: </?=$databund?>
                                            //         },
                                            //         success: function(response) {
                                            //             console.log(response);
                                            //         },
                                            //         error: function(response) {
                                            //             console.log(response);
                                            //         }   
                                            //     });
                                            // }
                                        </script> -->
                                        <!-- Script Booking End -->
                                    <!-- </?php } ?> -->
                                <!-- </div>
                            </div>
                        </div>
                    </div> -->
                    <!-- Modal Booking End -->
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
                        <!-- <li class="tm-main-navbar uk-parent </?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='penjualan')?'uk-active':''?></?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='keuntungan')?'uk-active':''?></?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='payment')?'uk-active':''?></?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='employe')?'uk-active':''?></?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='product')?'uk-active':''?></?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='category')?'uk-active':''?></?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='bundle')?'uk-active':''?></?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='diskon')?'uk-active':''?></?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='presence')?'uk-active':''?></?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='customer')?'uk-active':''?></?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='stockcategory')?'uk-active':''?></?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='sop')?'uk-active':''?>"> -->
                        <li class="tm-main-navbar uk-parent <?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='dailysell')?'uk-active':''?><?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='penjualan')?'uk-active':''?><?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='payment')?'uk-active':''?><?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='employe')?'uk-active':''?><?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='product')?'uk-active':''?><?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='category')?'uk-active':''?><?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='brand')?'uk-active':''?><?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='presence')?'uk-active':''?><?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='customer')?'uk-active':''?><?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='stockcategory')?'uk-active':''?><?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='sop')?'uk-active':''?>">
                            <a class="tm-h3" href=""><img src="img/layout/laporan.svg" uk-svg><?=lang('Global.report');?><span uk-nav-parent-icon></span></a>
                            <ul class="uk-nav-sub">
                                <li class="tm-h4 <?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='dailysell')?'uk-active':''?>">
                                    <a href="<?= base_url('report/dailysell') ?>">Laporan Penjualan Harian</a>
                                </li>
                                <?php if ((in_groups('owner')) || (in_groups('supervisor'))) : ?>
                                    <li class="tm-h4 <?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='penjualan')?'uk-active':''?>">
                                        <a href="<?= base_url('report/penjualan') ?>"><?=lang('Global.salesreport');?></a>
                                    </li>
                                    <!-- <li class="tm-h4 </?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='keuntungan')?'uk-active':''?>">
                                        <a href="</?= base_url('report/keuntungan') ?>"></?=lang('Global.profitreport');?></a>
                                    </li> -->
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
                                    <li class="tm-h4 <?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='brand')?'uk-active':''?>">
                                        <a href="<?= base_url('report/brand') ?>"><?=lang('Global.brandreport');?></a>
                                    </li>
                                    <!-- <li class="tm-h4 </?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='bundle')?'uk-active':''?>">
                                        <a href="</?= base_url('report/bundle') ?>"></?=lang('Global.bundlereport');?></a>
                                    </li> -->
                                    <!-- <li class="tm-h4 </?=($uri->getSegment(1)==='report')&&($uri->getSegment(2)==='diskon')?'uk-active':''?>">
                                        <a href="</?= base_url('report/diskon') ?>"></?=lang('Global.discountreport');?></a>
                                    </li> -->
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
                        <!-- <li class="tm-main-navbar </?=($uri->getSegment(1)==='topup')?'uk-active':''?>">
                            <a class="tm-h3" href="</?= base_url('topup') ?>"><img src="img/layout/topup.svg" uk-svg></?=lang('Global.topup');?></a>
                        </li> -->
                        <?php if(in_groups('owner')) : ?>
                            <li class="tm-main-navbar <?=($uri->getSegment(1)==='sop')?'uk-active':''?>">
                                <a class="tm-h3" href="<?= base_url('sop') ?>"><img src="img/layout/sop.svg" uk-svg><?=lang('Global.sop');?></a>
                            </li>
                            <!-- <li class="tm-main-navbar uk-parent </?=($uri->getSegment(1)==='product')?'uk-active':''?></?=($uri->getSegment(1)==='bundle')?'uk-active':''?>">
                                <a class="tm-h3" href=""><img src="img/layout/product.svg" uk-svg></?=lang('Global.product');?><span uk-nav-parent-icon></span></a>
                                <ul class="uk-nav-sub">
                                    <li class="tm-h4 </?=($uri->getSegment(1)==='product')?'uk-active':''?>">
                                        <a href="</?= base_url('product') ?>"></?=lang('Global.product');?></a>
                                    </li>
                                    <li class="tm-h4 </?=($uri->getSegment(1)==='bundle')?'uk-active':''?>">
                                        <a href="</?= base_url('bundle') ?>"></?=lang('Global.bundle');?></a>
                                    </li>
                                </ul>
                            </li> -->
                            <li class="tm-main-navbar <?=($uri->getSegment(1)==='product')?'uk-active':''?>">
                                <a class="tm-h3" href="<?= base_url('product') ?>"><img src="img/layout/product.svg" uk-svg><?=lang('Global.product');?></a>
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
                        <!-- <li class="tm-main-navbar uk-parent </?=($uri->getSegment(1)==='stock')&&($uri->getSegment(2)==='')?'uk-active':''?></?=($uri->getSegment(1)==='stock')&&($uri->getSegment(2)==='supplier')?'uk-active':''?></?=($uri->getSegment(1)==='stock')&&($uri->getSegment(2)==='purchase')?'uk-active':''?></?=($uri->getSegment(1)==='stockmove')?'uk-active':''?></?=($uri->getSegment(1)==='stockadjustment')?'uk-active':''?></?=($uri->getSegment(1)==='stock')&&($uri->getSegment(2)==='stockcycle')?'uk-active':''?>"> -->
                        <li class="tm-main-navbar uk-parent <?=($uri->getSegment(1)==='stock')&&($uri->getSegment(2)==='')?'uk-active':''?><?=($uri->getSegment(1)==='stock')&&($uri->getSegment(2)==='supplier')?'uk-active':''?><?=($uri->getSegment(1)==='stock')&&($uri->getSegment(2)==='purchase')?'uk-active':''?><?=($uri->getSegment(1)==='stockmove')?'uk-active':''?><?=($uri->getSegment(1)==='stockadjustment')?'uk-active':''?>">
                            <a class="tm-h3" href=""><img src="img/layout/inventori.svg" uk-svg><?=lang('Global.inventory');?><span uk-nav-parent-icon></span></a>
                            <ul class="uk-nav-sub">
                                <?php if (in_groups('owner')) : ?>
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
                                    <!-- <li class="tm-h4 </?=($uri->getSegment(1)==='stock')&&($uri->getSegment(2)==='stockcycle')?'uk-active':''?>">
                                        <a href="</?= base_url('stock/stockcycle') ?>"></?=lang('Global.stockCycle');?></a>
                                    </li> -->
                                <?php endif ?>
                                <li class="tm-h4 <?=($uri->getSegment(1)==='stock')&&($uri->getSegment(2)==='')?'uk-active':''?>">
                                    <a href="<?= base_url('stock') ?>"><?=lang('Global.stock');?></a>
                                </li>
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
                                        <input id="customerid" name="customerid" hidden />
                                    </div>
                                </div>

                                <script type="text/javascript">
                                    $(function() {
                                        var customerList = [
                                            {label: "Non Member", idx:0},
                                            <?php
                                                foreach ($customers as $customer) {
                                                    echo '{label:"'.$customer['name'].' / '.$customer['phone'].'",idx:'.$customer['id'].'},';
                                                }
                                            ?>
                                        ];
                                        $("#customername").autocomplete({
                                            source: customerList,
                                            select: function(e, i) {
                                                $("#customerid").val(i.item.idx);
                                                if (i.item.idx != 0) {
                                                    var customers = <?php echo json_encode($customers); ?>;
                                                    for (var x = 0; x < customers.length; x++) {
                                                        if (customers[x]['id'] == i.item.idx) {
                                                            document.getElementById('custpoin').removeAttribute('hidden');
                                                            document.getElementById('curpoin').innerHTML = '<?=lang('Global.yourpoint')?> ' + customers[x]['poin'];
                                                            document.getElementById('poin').setAttribute('max', customers[x]['poin']);
                                                            document.getElementById('customerphone').value = customers[x]['phone'];
                                                            totalcount();
                                                        }
                                                    }
                                                } else {
                                                    document.getElementById('custpoin').setAttribute('hidden', '');
                                                    document.getElementById('curpoin').innerHTML = '<?=lang('Global.yourpoint')?> 0' ;
                                                    document.getElementById('poin').setAttribute('max', '0');
                                                    document.getElementById('poin').value = '0';
                                                    totalcount();
                                                }
                                            },
                                            minLength: 2
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

                            <!-- <div class="uk-margin">
                                <div class="uk-width-1-1">
                                    <a class="uk-button uk-button-primary" uk-toggle="#trxproof"></?= lang('Global.trxproof') ?></a>
                                </div>
                            </div>

                            <div class="uk-margin" hidden>
                                <input class="image-tag" name="image">
                            </div> -->

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

        <!-- Modal Transaction Proof -->
        <!-- <div uk-modal class="uk-flex-top" id="trxproof">
            <div class="uk-modal-dialog uk-margin-auto-vertical">
                <div class="uk-modal-content">
                    <div class="uk-modal-header">
                        <div class="uk-flex uk-flex-middle uk-child-width-auto" uk-grid>
                            <div class="uk-padding-remove uk-margin-medium-left">
                                <a class="" uk-icon="arrow-left" uk-toggle="#tambahdata" width="35" height="35"></a>
                            </div>
                            <div>
                                <h5 class="uk-modal-title" ></?=lang('Global.trxproof')?></h5>
                            </div>
                        </div>
                    </div>
                    <div class="uk-modal-body">
                        <div class="uk-flex-center uk-child-width-1-1" uk-grid>
                            <div id="my_camera"></div>
                            <div class="uk-text-center">
                                <input class="uk-button uk-button-primary" id="btnTake" type="button" value="Take Snapshot" onClick="take_snapshot()">
                            </div>
                            <div class="uk-text-center" id="results"></div>
                        </div> -->

                        <!-- Script Webcam Trx Proof -->
                        <!-- <script type="text/javascript">
                            Webcam.set({
                                width: 490,
                                height: 390,
                                image_format: 'jpeg',
                                jpeg_quality: 90,
                                video: {
                                    facingMode: "environment"
                                },
                                constraints: {
                                    facingMode: 'environment'
                                }
                            });
                        
                            Webcam.attach( '#my_camera' );

                            function take_snapshot() {
                                Webcam.snap( function(data_uri) {
                                    $(".image-tag").val(data_uri);
                                    document.getElementById('results').innerHTML = '<img src="'+data_uri+'"/>';
                                } );
                            }
                        </script> -->
                        <!-- Script Webcam Trx Proof End -->
                    <!-- </div>
                </div>
            </div>
        </div> -->
        <!-- Modal Transaction Proof End -->

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
                                            var elemexist = document.getElementById('product'+variant);
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
                                                            productgrid.setAttribute('class', 'uk-margin-small uk-flex-middle');
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

                                                            // const varpricecontainer = document.createElement('div');
                                                            // varpricecontainer.setAttribute('class', 'uk-margin-small uk-width-1-2');

                                                            // const varbardiv = document.createElement('div');
                                                            // varbardiv.setAttribute('class','uk-margin uk-margin-small uk-width-1-2');

                                                            // const varbarlab = document.createElement('label');
                                                            // varbarlab.setAttribute('class','uk-form-label uk-margin-remove uk-text-bold uk-text-small uk-h4');

                                                            // const varbartext = document.createTextNode("Variant Bargain");

                                                            // const varbarform = document.createElement('div');
                                                            // varbarform.setAttribute('class','uk-form-controls');

                                                            // const varbargain = document.createElement('input');
                                                            // varbargain.setAttribute('class', 'uk-input uk-form-width-small');
                                                            // varbargain.setAttribute('id', 'varbargain'+variant);
                                                            // varbargain.setAttribute('placeholder', '0');
                                                            // varbargain.setAttribute('name', 'varbargain['+variant+']');
                                                            // varbargain.setAttribute('min', "0");
                                                            // varbargain.setAttribute('type', 'number');

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
                                                            varprice.setAttribute('value', '0');
                                                            varprice.setAttribute('type', 'number');
                                                            varprice.setAttribute('min', '0');

                                                            function showprice() {
                                                                var qty         = inputqty.value;
                                                                <?php if ($gconfig['globaldisc'] != '0') {
                                                                    if ($gconfig['globaldisctype'] == '0') { ?>
                                                                        var globaldisc  = <?= $gconfig['globaldisc'] ?>;
                                                                    <?php } else { ?>
                                                                        var globaldisc  = variantarray[x]['sellprice'] * <?= ((Int)$gconfig['globaldisc'] / 100) ?>;
                                                                    <?php } ?>

                                                                    var price       = qty * (variantarray[x]['sellprice'] - globaldisc);
                                                                <?php } else { ?>
                                                                    var price       = qty * variantarray[x]['sellprice'];
                                                                <?php } ?>
                                                                return price;
                                                                productprice.innerHTML = price;
                                                            }

                                                            inputqty.onchange = function() {showprice()};
                                                            inputqty.onchange = function() {handleChangeCount(variant)};

                                                            // varbargain.onchange = function() {VarBargain(variant)};
                                                            varprice.onchange = function() {VarDisc(variant)};

                                                            // varbargain.onchange = function() {
                                                            //     var discvar = varprice.value;
                                                            //     var bargain = varbargain.value;

                                                            //     if (varprice.value) {
                                                            //         </?php if ($gconfig['globaldisc'] != '0') {
                                                            //             if ($gconfig['globaldisctype'] == '0') { ?>
                                                            //                 var globaldisc  = </?= $gconfig['globaldisc'] ?>;
                                                            //             </?php } else { ?>
                                                            //                 var globaldisc  = (bargain - discvar) * </?= ((Int)$gconfig['globaldisc'] / 100) ?>;
                                                            //             </?php } ?>

                                                            //             var bargainprice = ((bargain - discvar) - globaldisc) * inputqty.value;
                                                            //         </?php } else { ?>
                                                            //             var bargainprice = bargain * inputqty.value;
                                                            //         </?php } ?>
                                                            //     } else {
                                                            //         </?php if ($gconfig['globaldisc'] != '0') {
                                                            //             if ($gconfig['globaldisctype'] == '0') { ?>
                                                            //                 var globaldisc  = </?= $gconfig['globaldisc'] ?>;
                                                            //             </?php } else { ?>
                                                            //                 var globaldisc  = bargain * </?= ((Int)$gconfig['globaldisc'] / 100) ?>;
                                                            //             </?php } ?>

                                                            //             var bargainprice = (bargain - globaldisc) * inputqty.value;
                                                            //         </?php } else { ?>
                                                            //             var bargainprice = bargain * inputqty.value;
                                                            //         </?php } ?>
                                                            //     }

                                                            //     if (bargainprice) {
                                                            //         document.getElementById('price'+variant).innerHTML = bargainprice;
                                                            //     } else {
                                                            //         document.getElementById('price'+variant).innerHTML = showprice();
                                                            //     }
                                                            // }

                                                            // varprice.onchange = function() {
                                                            //     if (varbargain.value) {
                                                            //         var subvalue    = (varbargain.value - varprice.value);

                                                            //         </?php if ($gconfig['globaldisc'] != '0') {
                                                            //             if ($gconfig['globaldisctype'] == '0') { ?>
                                                            //                 var globaldisc  = </?= $gconfig['globaldisc'] ?>;
                                                            //             </?php } else { ?>
                                                            //                 var globaldisc  = subvalue * </?= ((Int)$gconfig['globaldisc'] / 100) ?>;
                                                            //             </?php } ?>

                                                            //             document.getElementById('price'+variant).innerHTML = (subvalue - globaldisc) * inputqty.value;
                                                            //         </?php } else { ?>
                                                            //             document.getElementById('price'+variant).innerHTML = subvalue * inputqty.value;
                                                            //         </?php } ?>
                                                            //     } else {
                                                            //         var subvalue    = (variantarray[x]['sellprice'] - varprice.value);

                                                            //         </?php if ($gconfig['globaldisc'] != '0') {
                                                            //             if ($gconfig['globaldisctype'] == '0') { ?>
                                                            //                 var globaldisc  = </?= $gconfig['globaldisc'] ?>;
                                                            //             </?php } else { ?>
                                                            //                 var globaldisc  = subvalue * </?= ((Int)$gconfig['globaldisc'] / 100) ?>;
                                                            //             </?php } ?>

                                                            //             document.getElementById('price'+variant).innerHTML = (subvalue - globaldisc) * inputqty.value;
                                                            //         </?php } else { ?>
                                                            //             document.getElementById('price'+variant).innerHTML = subvalue * inputqty.value;
                                                            //         </?php } ?>
                                                            //     }
                                                            // }

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
                                                            // varpricecontainer.appendChild(varbardiv);
                                                            // varbardiv.appendChild(varbarlab);
                                                            // varbarlab.appendChild(varbartext);
                                                            // varbarlab.appendChild(varbarform);
                                                            // varbarform.appendChild(varbargain);
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
                                                            // productgrid.appendChild(varpricecontainer);
                                                            productgrid.appendChild(varvaluecontainer);
                                                            products.appendChild(sellprice);
                                                            products.appendChild(productgrid);
                                                        }
                                                    }
                                                }
                                            }
                                        };

                                        // function VarBargain(id) {
                                        //     var inputqty        = document.getElementById('qty['+id+']');
                                        //     var varbargain      = document.getElementById('varbargain'+id);
                                        //     var varprice        = document.getElementById('varprice'+id);
                                        //     var sellprice       = document.getElementById('sellprice'+id);
                                        //     var productprice    = document.getElementById('price'+id);
                                        //     var productgrid     = document.getElementById('product'+id);

                                        //     if (varprice.value) {
                                        //         </?php if ($gconfig['globaldisc'] != '0') {
                                        //             if ($gconfig['globaldisctype'] == '0') { ?>
                                        //                 var globaldisc  = </?= $gconfig['globaldisc'] ?>;
                                        //             </?php } else { ?>
                                        //                 var globaldisc  = (bargain - discvar) * </?= ((Int)$gconfig['globaldisc'] / 100) ?>;
                                        //             </?php } ?>

                                        //             var bargainprice = ((bargain - discvar) - globaldisc) * inputqty.value;
                                        //         </?php } else { ?>
                                        //             var bargainprice = bargain * inputqty.value;
                                        //         </?php } ?>
                                        //     } else {
                                        //         </?php if ($gconfig['globaldisc'] != '0') {
                                        //             if ($gconfig['globaldisctype'] == '0') { ?>
                                        //                 var globaldisc  = </?= $gconfig['globaldisc'] ?>;
                                        //             </?php } else { ?>
                                        //                 var globaldisc  = bargain * </?= ((Int)$gconfig['globaldisc'] / 100) ?>;
                                        //             </?php } ?>

                                        //             var bargainprice = (bargain - globaldisc) * inputqty.value;
                                        //         </?php } else { ?>
                                        //             var bargainprice = bargain * inputqty.value;
                                        //         </?php } ?>
                                        //     }

                                        //     if (bargainprice) {
                                        //         productprice.innerHTML = bargainprice;
                                        //     } else {
                                        //         productprice.innerHTML = showprice();
                                        //     }
                                        // }

                                        function VarDisc(id) {
                                            var inputqty        = document.getElementById('qty['+id+']');
                                            // var varbargain      = document.getElementById('varbargain'+id);
                                            var varprice        = document.getElementById('varprice'+id);
                                            var sellprice       = document.getElementById('sellprice'+id);
                                            var productprice    = document.getElementById('price'+id);
                                            var productgrid     = document.getElementById('product'+id);

                                            // if (varbargain.value) {
                                            //     var subvalue    = (varbargain.value - varprice.value);

                                            //     </?php if ($gconfig['globaldisc'] != '0') {
                                            //         if ($gconfig['globaldisctype'] == '0') { ?>
                                            //             var globaldisc  = </?= $gconfig['globaldisc'] ?>;
                                            //         </?php } else { ?>
                                            //             var globaldisc  = subvalue * </?= ((Int)$gconfig['globaldisc'] / 100) ?>;
                                            //         </?php } ?>

                                            //         productprice.innerHTML = (subvalue - globaldisc) * inputqty.value;
                                            //     </?php } else { ?>
                                            //         productprice.innerHTML = subvalue * inputqty.value;
                                            //     </?php } ?>
                                            // } else {
                                                var subvalue    = (sellprice.value - varprice.value);

                                                <?php if ($gconfig['globaldisc'] != '0') {
                                                    if ($gconfig['globaldisctype'] == '0') { ?>
                                                        var globaldisc  = <?= $gconfig['globaldisc'] ?>;
                                                    <?php } else { ?>
                                                        var globaldisc  = subvalue * <?= ((Int)$gconfig['globaldisc'] / 100) ?>;
                                                    <?php } ?>

                                                    productprice.innerHTML = (subvalue - globaldisc) * inputqty.value;
                                                <?php } else { ?>
                                                    productprice.innerHTML = subvalue * inputqty.value;
                                                <?php } ?>
                                            // }
                                        };

                                        function handleCount(id, type) {
                                            var inputqty        = document.getElementById('qty['+id+']');
                                            // var varbargain      = document.getElementById('varbargain'+id);
                                            var varprice        = document.getElementById('varprice'+id);
                                            var sellprice       = document.getElementById('sellprice'+id);
                                            var productprice    = document.getElementById('price'+id);
                                            var productgrid     = document.getElementById('product'+id);
                                            var count           = inputqty.value;
                                            var discvar         = varprice.value;
                                            if (type == 1) {
                                                count++;
                                                if (inputqty.value == inputqty.getAttribute('max')) {
                                                    inputqty.value = inputqty.getAttribute('max');
                                                    count = inputqty.getAttribute('max');
                                                    alert('<?=lang('Global.alertstock')?>');
                                                } else {
                                                    inputqty.value      = count;
                                                    // var price           = count * sellprice.value;

                                                    // var price           = count * sellprice.value;
                                                    // var bargainprice    = varbargain.value * inputqty.value;

                                                    // if (varbargain.value) {
                                                    //     if (varprice.value) {
                                                    //         </?php if ($gconfig['globaldisc'] != '0') {
                                                    //             if ($gconfig['globaldisctype'] == '0') { ?>
                                                    //                 var globaldisc  = </?= $gconfig['globaldisc'] ?>;
                                                    //             </?php } else { ?>
                                                    //                 var globaldisc  = (varbargain.value - discvar) * </?= ((Int)$gconfig['globaldisc'] / 100) ?>;
                                                    //             </?php } ?>

                                                    //             var bargainprice    = ((varbargain.value - discvar) - globaldisc) * inputqty.value;
                                                    //         </?php } else { ?>
                                                    //             var bargainprice    = (varbargain.value - discvar) * inputqty.value;
                                                    //         </?php } ?>
                                                    //     } else {
                                                    //         </?php if ($gconfig['globaldisc'] != '0') {
                                                    //             if ($gconfig['globaldisctype'] == '0') { ?>
                                                    //                 var globaldisc  = </?= $gconfig['globaldisc'] ?>;
                                                    //             </?php } else { ?>
                                                    //                 var globaldisc  = varbargain.value * </?= ((Int)$gconfig['globaldisc'] / 100) ?>;
                                                    //             </?php } ?>

                                                    //             var bargainprice    = (varbargain.value - globaldisc) * inputqty.value;
                                                    //         </?php } else { ?>
                                                    //             var bargainprice    = varbargain.value * inputqty.value;
                                                    //         </?php } ?>
                                                    //     }

                                                    //     document.getElementById('price'+id).innerHTML = bargainprice;
                                                    // } else {
                                                        if (varprice.value) {
                                                            <?php if ($gconfig['globaldisc'] != '0') {
                                                                if ($gconfig['globaldisctype'] == '0') { ?>
                                                                    var globaldisc  = <?= $gconfig['globaldisc'] ?>;
                                                                <?php } else { ?>
                                                                    var globaldisc  = (sellprice.value - discvar) * <?= ((Int)$gconfig['globaldisc'] / 100) ?>;
                                                                <?php } ?>

                                                                var price           = count * ((sellprice.value - discvar) - globaldisc);
                                                                // var bargainprice    = (varbargain.value - globaldisc) * inputqty.value;
                                                            <?php } else { ?>
                                                                // var bargainprice    = varbargain.value * inputqty.value;
                                                                var price           = count * (sellprice.value - discvar);
                                                            <?php } ?>
                                                        } else {
                                                            <?php if ($gconfig['globaldisc'] != '0') {
                                                                if ($gconfig['globaldisctype'] == '0') { ?>
                                                                    var globaldisc  = <?= $gconfig['globaldisc'] ?>;
                                                                <?php } else { ?>
                                                                    var globaldisc  = sellprice.value * <?= ((Int)$gconfig['globaldisc'] / 100) ?>;
                                                                <?php } ?>

                                                                var price           = count * (sellprice.value - globaldisc);
                                                                // var bargainprice    = (varbargain.value - globaldisc) * inputqty.value;
                                                            <?php } else { ?>
                                                                // var bargainprice    = varbargain.value * inputqty.value;
                                                                var price           = count * sellprice.value;
                                                            <?php } ?>
                                                        }

                                                        productprice.innerHTML = price;
                                                        productprice.value = price;
                                                    // }
                                                }
                                            } else if (type == 0) {
                                                count--;
                                                if (inputqty.value == '1') {
                                                    inputqty.value = '0';
                                                    inputqty.remove();                                                                                                
                                                    productgrid.remove();
                                                } else {
                                                    inputqty.value  = count;
                                                    // var price       = count * sellprice.value;

                                                    // var price = count * sellprice.value;
                                                    // var bargainprice = varbargain.value * inputqty.value;

                                                    // if (varbargain.value) {
                                                    //     if (varprice.value) {
                                                    //         </?php if ($gconfig['globaldisc'] != '0') {
                                                    //             if ($gconfig['globaldisctype'] == '0') { ?>
                                                    //                 var globaldisc  = </?= $gconfig['globaldisc'] ?>;
                                                    //             </?php } else { ?>
                                                    //                 var globaldisc  = (varbargain.value - discvar) * </?= ((Int)$gconfig['globaldisc']  / 100) ?>;
                                                    //             </?php } ?>

                                                    //             // var price           = count * (sellprice.value - globaldisc);
                                                    //             var bargainprice    = (varbargain.value - discvar) * inputqty.value;
                                                    //         </?php } else { ?>
                                                    //             var bargainprice    = (varbargain.value - discvar) * inputqty.value;
                                                    //             // var price           = count * sellprice.value;
                                                    //         </?php } ?>
                                                    //     } else {
                                                    //         </?php if ($gconfig['globaldisc'] != '0') {
                                                    //             if ($gconfig['globaldisctype'] == '0') { ?>
                                                    //                 var globaldisc  = </?= $gconfig['globaldisc'] ?>;
                                                    //             </?php } else { ?>
                                                    //                 var globaldisc  = varbargain.value * </?= ((Int)$gconfig['globaldisc']  / 100) ?>;
                                                    //             </?php } ?>

                                                    //             // var price           = count * (sellprice.value - globaldisc);
                                                    //             var bargainprice    = (varbargain.value - globaldisc) * inputqty.value;
                                                    //         </?php } else { ?>
                                                    //             var bargainprice    = varbargain.value * inputqty.value;
                                                    //             // var price           = count * sellprice.value;
                                                    //         </?php } ?>
                                                    //     }
                                                    //     document.getElementById('price'+id).innerHTML = bargainprice;
                                                    // }
                                                    // else {
                                                        if (varprice.value) {
                                                            <?php if ($gconfig['globaldisc'] != '0') {
                                                                if ($gconfig['globaldisctype'] == '0') { ?>
                                                                    var globaldisc  = <?= $gconfig['globaldisc'] ?>;
                                                                <?php } else { ?>
                                                                    var globaldisc  = (sellprice.value - discvar) * <?= ((Int)$gconfig['globaldisc']  / 100) ?>;
                                                                <?php } ?>

                                                                var price           = count * ((sellprice.value - discvar) - globaldisc);
                                                                // var bargainprice    = (varbargain.value - globaldisc) * inputqty.value;
                                                            <?php } else { ?>
                                                                // var bargainprice    = varbargain.value * inputqty.value;
                                                                var price           = count * (sellprice.value - discvar);
                                                            <?php } ?>
                                                        } else {
                                                            <?php if ($gconfig['globaldisc'] != '0') {
                                                                if ($gconfig['globaldisctype'] == '0') { ?>
                                                                    var globaldisc  = <?= $gconfig['globaldisc'] ?>;
                                                                <?php } else { ?>
                                                                    var globaldisc  = sellprice.value * <?= ((Int)$gconfig['globaldisc']  / 100) ?>;
                                                                <?php } ?>

                                                                var price           = count * (sellprice.value - globaldisc);
                                                                // var bargainprice    = (varbargain.value - globaldisc) * inputqty.value;
                                                            <?php } else { ?>
                                                                // var bargainprice    = varbargain.value * inputqty.value;
                                                                var price           = count * sellprice.value;
                                                            <?php } ?>
                                                        }
                                                        
                                                        productprice.innerHTML = price;
                                                        productprice.value = price;
                                                    // }
                                                }
                                            }
                                        };

                                        function handleChangeCount(id) {
                                            var inputqty        = document.getElementById('qty['+id+']');
                                            // var varbargain      = document.getElementById('varbargain'+id);
                                            var varprice        = document.getElementById('varprice'+id);
                                            var sellprice       = document.getElementById('sellprice'+id);
                                            var productprice    = document.getElementById('price'+id);
                                            var productgrid     = document.getElementById('product'+id);
                                            // var bargain         = varbargain.value;
                                            var discvar         = varprice.value;

                                            if (inputqty.value > inputqty.getAttribute('max')) {
                                                inputqty.value = inputqty.getAttribute('max');
                                                alert('<?=lang('Global.alertstock')?>');

                                                // var bargainprice = varbargain.value * inputqty.value;

                                                // if (varbargain.value) {
                                                //     if (varprice.value) {
                                                //         </?php if ($gconfig['globaldisc'] != '0') {
                                                //             if ($gconfig['globaldisctype'] == '0') { ?>
                                                //                 var globaldisc  = </?= $gconfig['globaldisc'] ?>;
                                                //             </?php } else { ?>
                                                //                 var globaldisc  = (bargain - discvar) * </?= ((Int)$gconfig['globaldisc'] / 100) ?>;
                                                //             </?php } ?>

                                                //             var bargainprice = ((bargain - discvar) - globaldisc) * inputqty.value;
                                                //         </?php } else { ?>
                                                //             var bargainprice = (bargain - discvar) * inputqty.value;
                                                //         </?php } ?>
                                                //     } else {
                                                //         </?php if ($gconfig['globaldisc'] != '0') {
                                                //             if ($gconfig['globaldisctype'] == '0') { ?>
                                                //                 var globaldisc  = </?= $gconfig['globaldisc'] ?>;
                                                //             </?php } else { ?>
                                                //                 var globaldisc  = bargain * </?= ((Int)$gconfig['globaldisc'] / 100) ?>;
                                                //             </?php } ?>

                                                //             var bargainprice = (bargain - globaldisc) * inputqty.value;
                                                //         </?php } else { ?>
                                                //             var bargainprice = bargain * inputqty.value;
                                                //         </?php } ?>
                                                //     }
                                                //     document.getElementById('price'+id).innerHTML = bargainprice;
                                                // } else {
                                                    if (varprice.value) {
                                                        <?php if ($gconfig['globaldisc'] != '0') {
                                                            if ($gconfig['globaldisctype'] == '0') { ?>
                                                                var globaldisc  = <?= $gconfig['globaldisc'] ?>;
                                                            <?php } else { ?>
                                                                var globaldisc  = (sellprice.value - discvar) * <?= ((Int)$gconfig['globaldisc']  / 100) ?>;
                                                            <?php } ?>

                                                            var price           = count * ((sellprice.value - discvar) - globaldisc);
                                                            // var bargainprice    = (varbargain.value - globaldisc) * inputqty.value;
                                                        <?php } else { ?>
                                                            // var bargainprice    = varbargain.value * inputqty.value;
                                                            var price           = count * (sellprice.value - discvar);
                                                        <?php } ?>
                                                    } else {
                                                        <?php if ($gconfig['globaldisc'] != '0') {
                                                            if ($gconfig['globaldisctype'] == '0') { ?>
                                                                var globaldisc  = <?= $gconfig['globaldisc'] ?>;
                                                            <?php } else { ?>
                                                                var globaldisc  = sellprice.value * <?= ((Int)$gconfig['globaldisc']  / 100) ?>;
                                                            <?php } ?>

                                                            var price           = count * (sellprice.value - globaldisc);
                                                            // var bargainprice    = (varbargain.value - globaldisc) * inputqty.value;
                                                        <?php } else { ?>
                                                            // var bargainprice    = varbargain.value * inputqty.value;
                                                            var price           = count * sellprice.value;
                                                        <?php } ?>
                                                    }
                                                    
                                                    productprice.innerHTML = price;
                                                    productprice.value = price;
                                                    
                                                    // if (varprice.value) {
                                                    //     productprice.innerHTML = price - discvar;
                                                    //     productprice.value = price - discvar;
                                                    // } else {
                                                    //     productprice.innerHTML = price;
                                                    //     productprice.value = price;
                                                    // }
                                                // }
                                            } else if (inputqty.value < 1) {
                                                inputqty.value = '0';
                                                inputqty.remove();
                                                productgrid.remove();
                                            } else {
                                                // var price = count * sellprice.value;
                                                // var bargainprice = varbargain.value * inputqty.value;

                                                // if(varbargain.value) {
                                                //     if (varprice.value) {
                                                //         </?php if ($gconfig['globaldisc'] != '0') {
                                                //             if ($gconfig['globaldisctype'] == '0') { ?>
                                                //                 var globaldisc  = </?= $gconfig['globaldisc'] ?>;
                                                //             </?php } else { ?>
                                                //                 var globaldisc  = (varbargain.value - discvar) * </?= ((Int)$gconfig['globaldisc'] / 100) ?>;
                                                //             </?php } ?>

                                                //             var bargainprice = ((varbargain.value - discvar) - globaldisc) * inputqty.value;
                                                //         </?php } else { ?>
                                                //             var bargainprice = (varbargain.value - discvar) * inputqty.value;
                                                //         </?php } ?>
                                                //     } else {
                                                //         </?php if ($gconfig['globaldisc'] != '0') {
                                                //             if ($gconfig['globaldisctype'] == '0') { ?>
                                                //                 var globaldisc  = </?= $gconfig['globaldisc'] ?>;
                                                //             </?php } else { ?>
                                                //                 var globaldisc  = varbargain.value * </?= ((Int)$gconfig['globaldisc'] / 100) ?>;
                                                //             </?php } ?>

                                                //             var bargainprice = (varbargain.value - globaldisc) * inputqty.value;
                                                //         </?php } else { ?>
                                                //             var bargainprice = varbargain.value * inputqty.value;
                                                //         </?php } ?>
                                                //     }
                                                //     document.getElementById('price'+id).innerHTML = bargainprice;
                                                // } else {
                                                    if (varprice.value) {
                                                        <?php if ($gconfig['globaldisc'] != '0') {
                                                            if ($gconfig['globaldisctype'] == '0') { ?>
                                                                var globaldisc  = <?= $gconfig['globaldisc'] ?>;
                                                            <?php } else { ?>
                                                                var globaldisc  = (sellprice.value - discvar) * <?= ((Int)$gconfig['globaldisc'] / 100) ?>;
                                                            <?php } ?>

                                                            var price           = count * ((sellprice.value - discvar) - globaldisc);
                                                            // var bargainprice    = (varbargain.value - globaldisc) * inputqty.value;
                                                        <?php } else { ?>
                                                            var price           = count * (sellprice.value - discvar);
                                                            // var bargainprice    = varbargain.value * inputqty.value;
                                                        <?php } ?>
                                                    } else {
                                                        <?php if ($gconfig['globaldisc'] != '0') {
                                                            if ($gconfig['globaldisctype'] == '0') { ?>
                                                                var globaldisc  = <?= $gconfig['globaldisc'] ?>;
                                                            <?php } else { ?>
                                                                var globaldisc  = sellprice.value * <?= ((Int)$gconfig['globaldisc'] / 100) ?>;
                                                            <?php } ?>

                                                            var price           = count * (sellprice.value - globaldisc);
                                                            // var bargainprice    = (varbargain.value - globaldisc) * inputqty.value;
                                                        <?php } else { ?>
                                                            var price           = count * sellprice.value;
                                                            // var bargainprice    = varbargain.value * inputqty.value;
                                                        <?php } ?>
                                                    }

                                                    productprice.innerHTML = price;
                                                    productprice.value = price;

                                                    // if (varprice.value) {
                                                    //     productprice.innerHTML = (price - discavr);
                                                    //     productprice.value = (price - discavr);
                                                    // } else {
                                                    //     productprice.innerHTML = price;
                                                    //     productprice.value = price;
                                                    // }
                                                // }
                                            }
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
            var subtotalelem = document.getElementById('subtotal');
            var disctypeval = 0;
            var discount = 0;
            var poin = 0;
            var memberdisc = 0;
            var min = 0;

            let subtotalobserve = new MutationObserver(mutationRecords => {
                var prices = document.querySelectorAll("div[name='price[]']");
                // var discvars = document.querySelectorAll(".varprice");
                
                var subarr = [];
                // var discarr = [];

                for (i = 0; i < prices.length; i++) {
                    price = Number(prices[i].innerText);
                    subarr.push(price);
                }
                
                // if (discvars.length !== 0){
                //     for (i = 0; i < discvars.length; i++) {
                //         var index = discvars[i].getAttribute('data-index');
                //         var varqty = document.getElementById('qty['+index+']').value;
                //         var vardisc = document.getElementById('varprice'+index).value;
                //         var discountvar = Number(varqty) * Number(vardisc);
                //         discarr.push(discountvar);
                //     }
                // }
                if (subarr.length === 0) {
                    document.getElementById('subtotal').innerHTML = 0;
                } else {
                    var subtotal = subarr.reduce(function(a, b){ return a + b; });
                    // if (discvars.length !== 0){
                    // var discountvar = discarr.reduce(function(a, b){ return a + b; });
                    // document.getElementById('subtotal').innerHTML = subtotal - discountvar;
                    // }else{
                        document.getElementById('subtotal').innerHTML = subtotal;
                    // }
                }
                $(document).ready(function() {

                    // $(".varprice").keyup(function(){
                    //     var prices = document.querySelectorAll("div[name='price[]']");
                    //     var discvars = document.querySelectorAll(".varprice");
                        
                    //     var subarr = [];
                    //     var discarr = [];

                    //     if (discvars.length !== 0){
                    //         for (i = 0; i < prices.length; i++) {
                    //             price = Number(prices[i].innerText);
                    //             subarr.push(price);
                    //         }
                            
                    //         for (i = 0; i < discvars.length; i++) {
                    //             var index = discvars[i].getAttribute('data-index');
                    //             var varqty = document.getElementById('qty['+index+']').value;
                    //             var vardisc = document.getElementById('varprice'+index).value;
                    //             var discountvar = Number(varqty) * Number(vardisc);
                    //             discarr.push(discountvar);
                    //         }
                    //         if (subarr.length === 0) {
                    //             document.getElementById('subtotal').innerHTML = 0;
                    //         } else {
                    //             var subtotal = subarr.reduce(function(a, b){ return a + b; });
                    //             var discountvar = discarr.reduce(function(a, b){ return a + b; });
                    //             document.getElementById('subtotal').innerHTML = subtotal - discountvar;
                    //         }  
                            
                    //         if (document.getElementById('subtotal').innerHTML < min ){
                    //         document.getElementById('subtotal').innerHTML = "Sorry Price To Low!"; 
                    //         }
                    //     }
                    // });

                    $('#pay').click(function(){
                        $('#order').attr('action', "/pay/create");
                        $("#order").validate({
                            required: true,
                        });
                        $('#order').submit();
                        document.getElementById("order").reset();
                        // var custphone   = document.getElementById('customerphone').value;
                        // if (custphone) {
                        //     window.open(`https://wa.me/+62`+custphone+`?text=Terimakasih%20telah%20berbelanja%20di%2058%20Vapehouse%2C%20untuk%20detail%20struk%20pembelian%20bisa%20cek%20link%20dibawah%20lur.%20%E2%9C%A8%E2%9C%A8%0A%0A$link%0A%0AJika%20menemukan%20kendala%2C%20kerusakan%20produk%2C%20atau%20ingin%20memberi%20kritik%20%26%20saran%20hubungu%2058%20Customer%20Solution%20kami%20di%20wa.me%2F6288983741558%20`, '_blank');
                        // }
                    });
                    
                    // $('#save').click(function(){
                    //     $('#order').attr('action', "/pay/save");
                    //     $("#order").validate({
                    //         required: true,
                    //     });
                    //     $('#order').submit();
                    // });
                    console.log( "ready!" );
                   
                });
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

                if (member && member.value) {
                    if (member.value != 0) {
                        <?php
                        if ($gconfig['memberdisctype'] === '0') {
                            echo 'var memberdisc = '.(int)$gconfig['memberdisc'].';';
                        } elseif ($gconfig['memberdisctype'] === '1') {
                            echo 'var memberdisc = ('.(int)$gconfig['memberdisc'].'/100)*subtotal;';
                        }
                        ?>
                    } else {
                        var memberdisc = 0;
                    }
                } else {
                    var memberdisc = 0;
                }

                // Tax
                var tax = (<?=(int)$gconfig['ppn']?>/100)*subtotal;
                
                // Count Total Price
                var totalprice = subtotal - discount - memberdisc - poin;             

                // Tax
                var tax = (<?=(int)$gconfig['ppn']?>/100)*totalprice;

                // Count Paid Price
                var paidprice = totalprice + tax;

                // Pay button
                var buttonpay = document.getElementById('pay');
                // var buttonsave = document.getElementById('save');
                if (paidprice >= 0) {
                    buttonpay.removeAttribute('disabled');
                    // buttonsave.removeAttribute('disabled', '');
                    var printprice = paidprice;
                } else {
                    buttonpay.setAttribute('disabled', '');
                    // buttonsave.setAttribute('disabled', '');
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
                    document.getElementById('payment').setAttribute('required', '');
                    document.getElementById('value').setAttribute('required', '');
                    document.getElementById('firstpayment').removeAttribute('required');
                    document.getElementById('firstpay').removeAttribute('required');
                    document.getElementById('secpayment').removeAttribute('required');
                    document.getElementById('secondpay').removeAttribute('required');
                } else {
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
        </script>
    </body>
</html>