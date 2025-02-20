<!doctype html>
<html dir="ltr "lang="<?=$lang?>" vocab="http://schema.org/" style="background-color:#000;">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <base href="<?=base_url();?>">
        <title></title>
        <meta name="description" content="">
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
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@600;700&display=swap" rel="stylesheet">
        <script src="js/uikit.min.js"></script>
        <script src="js/uikit-icons.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
        <style>
            @media print {  
                #btn{
                    display : none;
                }
            }
        </style>
    </head>
    <body style="background-color:#000;">
        <div class="uk-flex uk-flex-center uk-background-secondary">
            <div class="uk-child-width-1-3 uk-flex uk-flex-center" id="btn" uk-grid>
                <?php if (!empty($logedin)) { ?>
                    <div class="uk-text-center uk-margin-top">
                        <a class="uk-icon-button" uk-icon="arrow-left" href="<?= base_url('transaction') ?>"></a>
                    </div>
                    <div class="uk-text-center uk-margin">
                        <a class="uk-icon-button" uk-icon="print" onclick="printOut()"></a>
                    </div>
                    <div class="uk-text-center uk-margin">
                        <?php if(!empty($transactions['id']) && empty($bookings['id'])){?>
                            <!-- for transaction -->
                            <!-- <a class='uk-icon-button' uk-icon='whatsapp' href="pay/invoice/</?=$transactions['id']?>"></a> -->
                            <?php if ($transactions['memberid'] != '0'){
                                foreach ($customers as $member){
                                    if ($transactions['memberid'] == $member['id']){
                                        $memphone = $member['phone'];
                                        echo "<a class='uk-icon-button' uk-icon='whatsapp' target='_blank' href='https://wa.me/62$memphone?text=Terimakasih%20telah%20berbelanja%20di%2058%20Vapehouse%2C%20untuk%20detail%20struk%20pembelian%20bisa%20cek%20link%20dibawah%20lur.%20%E2%9C%A8%E2%9C%A8%0A%0A$link%0A%0AJika%20menemukan%20kendala%2C%20kerusakan%20produk%2C%20atau%20ingin%20memberi%20kritik%20%26%20saran%20hubungu%2058%20Customer%20Solution%20kami%20di%20wa.me%2F6288983741558%20'></a>";
                                    }
                                }
                                // transactions non member
                            } else {
                                echo'<a class="uk-icon-button" uk-icon="whatsapp" id="phonenumber" uk-toggle="target: #phonenumber" href="" uk-toggle></a>';
                                // bookings member
                            } ?>
                            <div class="uk-flex-top" id="phonenumber" uk-modal>
                                <div class="uk-modal-dialog uk-margin-auto-vertical">
                                    <div class="uk-modal-header">
                                        <div class="uk-child-width-1-2" uk-grid>
                                            <div>
                                                <h2 class="uk-modal-title"><?=lang('Global.phonenumber')?></h2>
                                            </div>
                                            <div class="uk-text-right">
                                                <button class="uk-modal-close uk-icon-button-delete" uk-icon="icon: close;" type="button"></button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="uk-modal-body">
                                        <form class="uk-form-horizontal uk-margin-large">
                                            <div class="uk-margin">
                                                <label class="uk-form-label" for="form-horizontal-text"><?=lang('global.phonenumber')?></label>
                                                <div class="uk-form-controls">
                                                    <div class="uk-inline uk-width-1-1">
                                                        <span class="uk-form-icon">+62</span>
                                                        <input class="uk-input" min="1" id="phoneinput" name="phoneinput" type="number" placeholder="phone" aria-label="Not clickable icon">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="uk-modal-footer uk-text-right">
                                                <a class='uk-button uk-button-primary' id='phone' href='' target='_blank'>submit</a>
                                            </div>
                                            <script>
                                                $(document).ready(function(){
                                                    $("#phoneinput").keyup(function(){
                                                        let phone = $("#phoneinput").val();
                                                        $("#phone").attr("href", "https://wa.me/62"+phone+"?text=Terimakasih%20telah%20berbelanja%20di%2058%20Vapehouse%2C%20untuk%20detail%20struk%20pembelian%20bisa%20cek%20link%20dibawah%20lur.%20%E2%9C%A8%E2%9C%A8%0A%0A<?=$link?>%0A%0AJika%20menemukan%20kendala%2C%20kerusakan%20produk%2C%20atau%20ingin%20memberi%20kritik%20%26%20saran%20hubungu%2058%20Customer%20Solution%20kami%20di%20wa.me%2F6288983741558%20");
                                                        console.log(phone);
                                                    });
                                                });
                                            </script>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php } elseif (!empty($bookings['id']) && (empty($transactions['id']))){ ?>
                            <!-- for bookings -->
                            <a class='uk-icon-button' uk-icon='whatsapp' href="pay/invoicebook/<?=$bookings['id']?>"></a>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div class="uk-flex uk-flex-center">
            <div class="uk-padding-small" style="width:58mm; background: #fff;">
                <div class="uk-margin-small uk-margin-top uk-width-1-1 uk-text-center">
                    <?php if (($gconfig['logo'] != null) && ($gconfig['bizname'] != null)) { ?>
                        <img src="/img/<?=$gconfig['logo'];?>" alt="<?=$gconfig['bizname'];?>" style="height: 60px;">
                    <?php } else { ?>
                        <img src="/img/binary111-logo-icon.svg" alt="PT. Kodebiner Teknologi Indonesia" style="height: 60px;">
                    <?php } ?>
                </div>

                <?php if(!empty($transactions['id'])){ ?>
                    <div class="uk-flex uk-flex-center">
                        <?php foreach ($outlets as $outlet) {
                            if ($outlet['id'] === $transactions['outletid']) { ?>
                                <div class="fpoutlet uk-margin-remove uk-text-justify" style="font-size:12px;" ><?= $outlet['name'] ?></div>
                            <?php }
                        } ?>
                    </div>
                    <div class="uk-flex uk-flex-center">
                        <?php foreach ($outlets as $outlet) {
                            if ($outlet['id'] === $transactions['outletid']) { ?>
                                <p class="fpaddress uk-margin-remove uk-text-bold uk-text-center" style="font-size:10px;"><?= $outlet['address'] ?></p>
                            <?php }
                        } ?>
                    </div>
                    <div class="uk-flex uk-flex-center">
                        <?php foreach ($outlets as $outlet) {
                            if ($outlet['id'] === $transactions['outletid']) { ?>
                                <p class="fpaddress uk-margin-remove uk-text-bold uk-text-center" style="font-size:10px;"><span uk-icon="instagram" style="width: 10px;"></span> : <?= $outlet['instagram'] ?></p>
                            <?php }
                        } ?>
                    </div>
                    <div class="uk-flex uk-flex-center">
                        <?php foreach ($outlets as $outlet) {
                            if ($outlet['id'] === $transactions['outletid']) { ?>
                                <p class="fpaddress uk-margin-remove uk-text-bold uk-text-center" style="font-size:10px;"><span uk-icon="whatsapp" style="width: 10px;"></span> : <?= $outlet['phone'] ?></p>
                            <?php }
                        } ?>
                    </div>
                <?php } elseif (!empty($bookings['id'])) { ?>
                    <div class="uk-flex uk-flex-center">
                        <?php foreach ($outlets as $outlet) {
                            if ($outlet['id'] === $bookings['outletid']) { ?>
                                <div class="fpoutlet uk-margin-remove" style="font-size:12px;" ><?= $outlet['name'] ?></div>
                            <?php }
                        } ?>
                    </div>
                    <div class="uk-flex uk-flex-center">
                        <?php foreach ($outlets as $outlet) {
                            if ($outlet['id'] === $bookings['outletid']) { ?>
                                <p class="fpaddress uk-margin-remove uk-text-bold uk-text-center" style="font-size:10px;"><?= $outlet['address'] ?></p>
                            <?php }
                        } ?>
                    </div>
                    <div class="uk-flex uk-flex-center">
                        <?php foreach ($outlets as $outlet) {
                            if ($outlet['id'] === $bookings['outletid']) { ?>
                                <p class="fpaddress uk-margin-remove uk-text-bold uk-text-center" style="font-size:10px;"><span uk-icon="instagram" style="width: 10px;"></span> : <?= $outlet['instagram'] ?></p>
                            <?php }
                        } ?>
                    </div>
                    <div class="uk-flex uk-flex-center">
                        <?php foreach ($outlets as $outlet) {
                            if ($outlet['id'] === $bookings['outletid']) { ?>
                                <p class="fpaddress uk-margin-remove uk-text-bold uk-text-center" style="font-size:10px;"><span uk-icon="whatsapp" style="width: 10px;"></span> : <?= $outlet['phone'] ?></p>
                            <?php }
                        } ?>
                    </div>
                <?php } ?>
                    
                <div class="uk-text-xsmall uk-margin-top">
                    <div uk-grid>
                        <div class="uk-width-1-2"><?=lang('Global.invoice')?>: <?=(strtotime("now")) ?></div>
                        <?php if (!empty($transactions['id'])){ ?>
                            <div class="uk-width-1-2 uk-text-right"><?= date('l, d M Y, H:i:s', strtotime($transactions['date'])); ?></div>
                        <?php }elseif (!empty($bookings['id'])){ ?>
                            <div class="uk-width-1-2 uk-text-right"><?= $bookings['created_at'] ?></div>
                       <?php } ?>
                    </div>
                    <div class="uk-margin-remove-top" uk-grid>
                        <div class="uk-width-2-3">Cashier: <?= $user ?></div>
                        <div class="uk-text-right uk-width-1-3">
                            <?php if(!empty($transactionid)){
                                if ($transactions['paymentid'] == '0' && $transactions['amountpaid'] != '0') { ?>
                                    <?= lang('Global.splitbill')?>
                                <?php } elseif ($transactions['paymentid'] != '0') {
                                    foreach($payments as $payment) {
                                        if ($transactions['paymentid'] == $payment['id'] && ($transactions['paymentid'] != '0')) {
                                            foreach ($trxpayments as $trxpay){
                                                if($trxpay['paymentid'] == $payment['id']){
                                                    foreach ($cash as $cas){
                                                        if($payment['cashid']  == $cas['id'] )
                                                        echo $payment['name'];
                                                    }
                                                }
                                            }
                                        }
                                    }
                                } elseif ($transactions['paymentid'] == "-1") {
                                    echo lang('Global.redeemPoint');
                                } elseif ($transactions['paymentid'] == "0") { ?>
                                    <?= lang('Global.debt')?>
                                <?php }
                            } elseif (!empty($bookings['id'])) { ?>
                                <?= lang('Global.bookorder')?>
                            <?php } ?>
                        </div>
                    </div>
                </div>

                <!-- <hr style ="border-top: 1px  dotted black;"> -->
                <hr style ="border-top: 3px double #8c8b8b">

                <?php if (!empty($transactions['id'])){ ?>
                    <!-- variant transaction -->
                    <?php foreach ($trxdetails as $trxdet) {
                        if ($trxdet['variantid'] !== "0"){
                            foreach ($variants as $variant) {
                                foreach ($products as $product) { 
                                    if (($trxdet['variantid'] === $variant['id']) && ($product['id'] === $variant['productid']) && ($trxdet['transactionid'] === $transactions['id']) ) {
                                        if ($variant['name'] == null) {
                                            $variantName     = 'Variant Terhapus';
                                        }
                                        if ($product['name'] == null) {
                                            $productName    = 'Produk Terhapus';
                                        }
                                        
                                        $variantName     = $variant['name'];
                                        $productName     = $product['name'];

                                        if ($gconfig['globaldisc'] != "0") {
                                            $globaldisc = (Int)$trxdet['globaldisc'];
                                        } else {
                                            $globaldisc = 0;
                                        }

                                        // $variantval      = (Int)$trxdet['value'] + (Int)$trxdet['discvar'];
                                        $variantval      = (Int)$trxdet['value'] + ((Int)$trxdet['discvar'] / (Int)$trxdet['qty']) + ((Int)$globaldisc / (Int)$trxdet['qty']);
                                        ?>
                                        <div class="uk-margin-small uk-text-xsmall">
                                            <div class="uk-text-bold">
                                                <?=$productName.' - '.$variantName?>
                                            </div>
                                            <div class="uk-grid-collapse" uk-grid>
                                                <div class="uk-width-1-2">x<?=$trxdet['qty']?> @<?=$variantval?></div>
                                                <div class="uk-width-1-2 uk-text-right"><?= (Int)$variantval * (Int)$trxdet['qty']?></div>
                                            </div>
                                            <div class="uk-grid-collapse" uk-grid>
                                                <?php
                                                if ($trxdet['discvar'] != '0') {
                                                    echo "<div class='uk-width-1-2'>(".$trxdet['discvar'].")</div>";
                                                    echo "<div class='uk-width-1-2 uk-text-right'>-" .$trxdet['discvar']. "</div>";
                                                }
                                                if ($gconfig['globaldisc'] != "0") {
                                                    echo "<div class='uk-width-1-2'>(".$globaldisc.")</div>";
                                                    echo "<div class='uk-width-1-2 uk-text-right'>-" .$globaldisc. "</div>";
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    <?php }
                                } 
                            } 
                        }
                    } ?>
                    <!-- end variant -->

                    <!-- bundle transaction -->
                    <?php 
                    if (!empty($trxdet['bundleid']) ){
                        foreach ($trxdetails as $trxdet) { 
                            foreach ($bundles as $bundle){
                                if (($trxdet['transactionid'] === $transactions['id']) && ($trxdet['bundleid'] === $bundle['id']) ) {
                                    $bundleName      = $bundle['name'];

                                    if ($bundle['name'] == null) {
                                        $bundleName      = 'Bundle Terhapus';
                                    }

                                    if ($gconfig['globaldisc'] != "0") {
                                        $globaldisc = (Int)$trxdet['globaldisc'];
                                    } else {
                                        $globaldisc = 0;
                                    }

                                    $variantval      = (Int)$trxdet['value'] + ((Int)$globaldisc / (Int)$trxdet['qty']);
                                    ?>
                                    <div class="uk-margin-small uk-text-xsmall">
                                        <div>
                                            x<?=$trxdet['qty']?> <?=lang('Global.bundle')?> <br> <?= $bundleName?> <br>
                                            <div class="uk-grid-collapse" uk-grid>
                                                <div class="uk-width-2-3"> @<?=$variantval?></div>
                                                <div class="uk-width-1-3 uk-text-right"><?= (Int)$variantval * (Int)$trxdet['qty']?></div>
                                                <?php
                                                if ($gconfig['globaldisc'] != "0") {
                                                    echo "<div class='uk-width-1-2'>(".$globaldisc.")</div>";
                                                    echo "<div class='uk-width-1-2 uk-text-right'>-" .$globaldisc. "</div>";
                                                }
                                                ?>
                                            </div>
                                            <?php 
                                            foreach ($bundets as $bundet){
                                                foreach ($products as $product) { 
                                                    foreach ($variants as $variant){
                                                        if ($variant['name'] == null) {
                                                            $variantName     = 'Variant Terhapus';
                                                        }
                                                        if ($product['name'] == null) {
                                                            $productName    = 'Produk Terhapus';
                                                        }
                                                        $productName     = $product ['name']; 
                                                        $variantName     = $variant ['name'];
                                                        if(($variant['id'] === $bundet['variantid'])  && ($product['id'] === $variant['productid'])&& 
                                                        ($trxdet['bundleid'] === $bundet['bundleid']) && ($bundle['id'] === $bundet['bundleid'])){
                                                            echo "# ".$productName."-".$variantName."</br>";
                                                        }
                                                    }
                                                }
                                            }?>
                                        </div>
                                    </div>
                                    <?php 
                                } 
                            } 
                        } 
                    } 
                    ?>
                <!-- end bundle -->
                <?php } ?>

                <!-- booking variant -->
                    <?php
                    if(!empty($bookings['id']) && (empty($transactions['id']))){
                        foreach ($bookingdetails as $bookingdetail) {
                            if ($bookingdetail['variantid'] !== '0') {
                                foreach ($variants as $variant) {
                                    foreach ($products as $product) {
                                        if (($bookingdetail['variantid'] === $variant['id']) && ($variant['productid'] === $product['id'])) {
                                            if ($variant['name'] == null) {
                                                $variantName     = 'Variant Terhapus';
                                            }
                                            if ($product['name'] == null) {
                                                $productName    = 'Produk Terhapus';
                                            }
                                            $variantName    = $variant['name'];
                                            $productName    = $product['name'];
                                            $variantval     = (Int)$bookingdetail['value'] + ((Int)$bookingdetail['discvar'] / (Int)$bookingdetail['qty']) + ((Int)$bookingdetail['globaldisc'] / (Int)$bookingdetail['qty']);

                                            echo '<div class="uk-margin-small uk-text-xsmall">';
                                            echo '<div>';
                                            echo $productName.' - '.$variantName;
                                            echo '</div>';
                                            echo '<div class="uk-grid-collapse" uk-grid>';
                                            echo '<div class="uk-width-2-3">x'.$bookingdetail['qty'].' @'.$variantval.'</div>';
                                            echo '<div class="uk-width-1-3 uk-text-right">'.(Int)$variantval * (Int)$bookingdetail['qty'].'</div>';
                                            echo '</div>';
                                            if ($bookingdetail['discvar'] !== '0') {
                                                echo '<div class="uk-grid-collapse" uk-grid>';
                                                echo "<div class='uk-width-1-2'>Discount</br> @" .$bookingdetail['discvar']. "</div>";
                                                echo "<div class='uk-width-1-2'></br>-" .$bookingdetail['discvar']. "</div>";
                                                echo '</div>';
                                            }
                                            if ($bookingdetail['globaldisc'] !== '0') {
                                                echo '<div class="uk-grid-collapse" uk-grid>';
                                                echo "<div class='uk-width-1-2'>@" .$bookingdetail['globaldisc']. "</div>";
                                                echo "<div class='uk-width-1-2'></br>-" .$bookingdetail['globaldisc']. "</div>";
                                                echo '</div>';
                                            }
                                            echo '</div>';
                                        }
                                    }
                                }
                            } else {
                                foreach ($bundles as $bundle) {
                                    if ($bundle['id'] === $bookingdetail['bundleid']) {
                                        echo '<div class="uk-margin-small uk-text-xsmall">';
                                        echo '<div>';
                                        echo 'x'.$bookingdetail['qty'].' Bundle <br>'.$bundle['name'].'<br>';
                                        echo '<div class="uk-grid-collapse" uk-grid>';
                                        echo '<div class="uk-width-2-3"> @'.(Int)$bookingdetail['value'] + ((Int)$bookingdetail['globaldisc'] / (Int)$bookingdetail['qty']).'</div>';
                                        echo '<div class="uk-width-1-3">'.(Int)$bookingdetail['value'] * (Int)$bookingdetail['qty'].'</div>';
                                        echo '</div>';
                                        if ($bookingdetail['globaldisc'] !== '0') {
                                            echo '<div class="uk-grid-collapse" uk-grid>';
                                            echo "<div class='uk-width-1-2'>@" .$bookingdetail['globaldisc']. "</div>";
                                            echo "<div class='uk-width-1-2'></br>-" .$bookingdetail['globaldisc']. "</div>";
                                            echo '</div>';
                                        }
                                        foreach ($bundets as $bundet) {
                                            foreach ($products as $product) {
                                                foreach ($variants as $variant) {
                                                    if (($bundet['variantid'] === $variant['id']) && ($variant['productid'] === $product['id'])) {
                                                        if ($variant['name'] == null) {
                                                            $variantName     = 'Variant Terhapus';
                                                        }
                                                        if ($product['name'] == null) {
                                                            $productName    = 'Produk Terhapus';
                                                        }

                                                        $productName    = $product['name'];
                                                        $variantName    = $variant['name'];
                                                        echo "# ".$productName."-".$variantName."</br>";
                                                    } 
                                                }
                                            }
                                        }
                                        echo '</div>';
                                        echo '</div>';
                                    }
                                }
                            }
                        }
                    }
                    ?>

                <!-- end booking-->

                <!-- total booking -->
                <?php 
                if (!empty($bookings['id']) && empty ($transactions['id']) ){ ?>
                    <hr style ="border-top: 3px double #8c8b8b">
                    <div class="uk-margin-small uk-text-xsmall">
                        <div class="uk-grid-collapse" uk-grid>
                            <?php $sub =  lang('Global.subtotal'); ?>
                            <div class="uk-width-1-2 uk-text-bold "><?=$sub?></div>
                            <div class="uk-width-1-2  uk-text-bold uk-text-right"><?=$subtotal?></div>
                        </div>
                        <div class="uk-grid-collapse" uk-grid>
                            <?php
                                $disc =  lang('Global.discount');
                                echo "<div class='uk-width-1-2 uk-text-bold'>".$disc."</div>";
                                if ($discount != '0') {
                                    echo "<div class='uk-width-1-2 uk-text-bold uk-text-right'>".$discount."</div>";
                                } else {
                                    echo "<div class='uk-width-1-2 uk-text-bold uk-text-right'>0</div>";
                                }
                            ?>
                        </div>
                        <div class="uk-grid-collapse" uk-grid>
                            <?php if (($bookings['memberid'] != '0') && ($bookings['id'] == $bookingid)) {
                                $memberdisc = $gconfig['memberdisc'];
                                $discmember = lang('Global.memberDiscount');
                                echo "<div class='uk-width-1-2'>$discmember</div>";
                                if ($gconfig['memberdisc'] != '0') {
                                    echo "<div class='uk-width-1-2 uk-text-right'>".$memberdisc."</div>";
                                } else {
                                    echo "<div class='uk-width-1-2 uk-text-right'>0</div>";
                                }
                            } ?> 
                        </div>
                        <div class="uk-grid-collapse" uk-grid>
                            <?php if (($bookings['memberid'] != "0") && ($bookings['id'] == $bookingid)) {
                                $poinearn = $gconfig['poinvalue'];
                                $reedem = lang('Global.redeemPoint');
                                echo "<div class='uk-width-1-2'>$reedem</div>";
                                if  ($poinused != '0') {
                                    echo "<div class='uk-width-1-2 uk-text-right'>".$poinused."</div>";
                                } else {
                                    echo "<div class='uk-width-1-2 uk-text-right'>0</div>";
                                }
                            }?> 
                        </div>
                        
                        <hr style ="border-top: 3px double #8c8b8b">
                        
                        <div class="uk-grid-collapse" uk-grid>
                            <?php $tot =  lang('Global.total');?>
                            <div class="uk-width-1-2 uk-text-bold"><?=$tot?></div>
                            <div class="uk-width-1-2 uk-text-bold uk-text-right"><?=$total?></div>
                        </div>
                        <div class="uk-grid-collapse" uk-grid>
                            <?php
                                $pays =  lang('Global.pay');
                                echo "<div class='uk-width-1-2'>".$pays."</div>";
                                if ($pay != '0') {
                                    echo "<div class='uk-width-1-2 uk-text-bold uk-text-right'>".$pay."</div>";
                                } else {
                                    echo "<div class='uk-width-1-2 uk-text-bold uk-text-right'>0</div>";
                                }
                            ?>
                        </div>
                        <div class="uk-grid-collapse" uk-grid>
                            <?php
                                $changes = lang('Global.change');
                                echo "<div class='uk-width-1-2'>".$changes."</div>";
                                if ($change > "0") {
                                    echo "<div class='uk-width-1-2 uk-text-right'>".$change."</div>";
                                } else {
                                    echo "<div class='uk-width-1-2 uk-text-right'>0</div>";
                                }
                            ?>
                        </div>
                        <div class="uk-grid-collapse" uk-grid>
                            <?php if (($bookings['memberid'] !== "0") && ($bookings['id']=== $bookingid)) {
                                $cust = $cust['name'];
                                $customer = lang('Global.customer');
                                echo "<div class='uk-width-1-2'> $customer</div>";
                                echo "<div class='uk-width-1-2 uk-text-right'>$cust</div>";
                            }?> 
                        </div>
                        <div class="uk-grid-collapse" uk-grid>
                            <?php if (($bookings['memberid'] !== "0") && ($bookings['id']=== $bookingid) && $poinearn !== "0") {
                                $pointearned = lang('Global.pointearn');
                                echo "<div class='uk-width-1-2'>$pointearned</div>";
                                echo "<div class='uk-width-1-2 uk-text-right'>$poinearn</div>";
                            }?> 
                        </div>
                        <div class="uk-grid-collapse" uk-grid>
                            <?php if (($bookings['memberid'] !== "0") && ($bookings['id']=== $bookingid)) {
                                $totpoin =  lang('Global.totalpoint');
                                echo " <div class='uk-width-1-2'>$totpoin</div>";
                                echo "<div class='uk-width-1-2 uk-text-right'>$mempoin</div>";
                            }?> 
                        </div>
                        <div class="uk-grid-collapse" uk-grid>
                            <?php if (!empty($debt)) {
                                $debttext = lang('Global.debt');
                                echo " <div class='uk-width-1-2  uk-text-bold'>$debttext</div>";
                                echo "<div class='uk-width-1-2 uk-text-bold uk-text-right'>$debt</div>";
                            }?> 
                        </div>
                        <!-- <div class="uk-grid-collapse" uk-grid>
                            </?php if (!empty($totaldebt)) {
                                $totdebt = lang('Global.totaldebt');
                                echo " <div class='uk-width-1-2 uk-text-bold'>$totdebt</div>";
                                echo "<div class='uk-width-1-2 uk-text-right'>$totaldebt</div>";
                            }?> 
                        </div> -->
                    </div>
                    <!-- end total booking -->
                    <!-- total transaction -->
                <?php } elseif (!empty($transactions['id'])) { ?>
                    <hr class="uk-margin-small" style ="border-top: 3px double #8c8b8b">

                    <div class="uk-margin-small uk-text-xsmall">
                        <div class="uk-grid-collapse" uk-grid>
                            <?php $sub =  lang('Global.subtotal'); ?>
                            <div class="uk-width-1-2 uk-text-bold "><?=$sub?></div>
                            <div class="uk-width-1-2  uk-text-bold uk-text-right"><?=$subtotal?></div>
                        </div>
                        <div class="uk-grid-collapse" uk-grid>
                            <?php
                                $disc =  lang('Global.discount');
                                echo "<div class='uk-width-1-2 uk-text-bold'>$disc</div>";
                                if (!empty($discount)) {
                                    echo "<div class='uk-width-1-2  uk-text-bold uk-text-right'>".$discount."</div>";
                                } else {
                                    echo "<div class='uk-width-1-2  uk-text-bold uk-text-right'>0</div>";
                                }
                            ?>
                        </div>
                        <div class="uk-grid-collapse" uk-grid>
                            <?php if ($transactions['memberid'] !== "0") {
                                $memberdisc = $gconfig['memberdisc'];
                                $discmember = lang('Global.memberDiscount');
                                echo "<div class='uk-width-1-2'>$discmember</div>";
                                if ($gconfig['memberdisc'] !== "0") {
                                    echo "<div class='uk-width-1-2 uk-text-right'>".$memberdisc."</div>";
                                } else {
                                    echo "<div class='uk-width-1-2 uk-text-right'>0</div>";
                                }
                            }?> 
                        </div>
                        <div class="uk-grid-collapse" uk-grid>
                            <?php
                            if ($transactions['memberid'] !== "0") {
                                $reedem = lang('Global.redeemPoint');
                                echo "<div class='uk-width-1-2'>$reedem</div>";
                                if ($transactions['pointused'] !== "0") {
                                    echo "<div class='uk-width-1-2 uk-text-right'>".$poinused."</div>";
                                } else {
                                    echo "<div class='uk-width-1-2 uk-text-right'>0</div>";
                                }
                            }
                            ?> 
                        </div>

                        <hr class="uk-margin-small" style ="border-top: 3px double #8c8b8b">

                        <div class="uk-grid-collapse" uk-grid>
                            <?php $tot  = lang('Global.total');?>
                            <div class="uk-width-1-2 uk-text-bold"><?=$tot?></div>
                            <div class="uk-width-1-2  uk-text-bold uk-text-right"><?=$total?></div>
                        </div>
                        <div class="uk-grid-collapse" uk-grid>
                            <?php
                                $pays   = lang('Global.pay');
                                echo "<div class='uk-width-1-2'>$pays</div>";
                                echo "<div class='uk-width-1-2 uk-text-bold uk-text-right'>$pay</div>";
                            ?>
                        </div>
                        <div class="uk-grid-collapse" uk-grid>
                            <?php if (!empty($debt)) {
                                $debttext = lang('Global.debt');
                                echo " <div class='uk-width-1-2'>$debttext</div>";
                                echo "<div class='uk-width-1-2 uk-text-bold uk-text-right'>$debt</div>";
                            }?> 
                        </div>
                        <!-- <div class="uk-grid-collapse" uk-grid>
                            </?php if (!empty($totaldebt)) {
                                $totdebt = lang('Global.totaldebt');
                                echo " <div class='uk-width-1-2 uk-text-bold'>$totdebt</div>";
                                echo "<div class='uk-width-1-2 uk-text-right'>$totaldebt</div>";
                            }?> 
                        </div> -->
                        <!-- <div class="uk-grid-collapse" uk-grid>
                            </?php if ($pay !== "0"){
                                $pays =  lang('Global.pay');
                                echo "<div class='uk-width-1-2'>$pays</div>";
                                echo "<div class='uk-width-1-2 uk-text-bold uk-text-right'>$pay</div>";
                            }?>
                        </div> -->

                        <hr class="uk-margin-small" style ="border-top: 3px double #8c8b8b">

                        <div class="uk-grid-collapse" uk-grid>
                            <?php
                                $changes = lang('Global.change');
                                echo "<div class='uk-width-1-2'>$changes</div>";
                                if ($change != "0") {
                                    echo "<div class='uk-width-1-2 uk-text-right'>".$change."</div>";
                                } else {
                                    echo "<div class='uk-width-1-2 uk-text-right'>0</div>";
                                }
                            ?>
                        </div>

                        <hr class="uk-margin-small" style ="border-top: 3px double #8c8b8b">

                        <div class="uk-grid-collapse" uk-grid>
                            <?php if (($transactions['memberid'] !== "0") ) {
                                $cust = $cust['name'];
                                $customer = lang('Global.customer');
                                echo "<div class='uk-width-1-2'> $customer</div>";
                                echo "<div class='uk-width-1-2 uk-text-right'>$cust</div>";
                            }?> 
                        </div>
                        <div class="uk-grid-collapse" uk-grid>
                            <?php if (($transactions['memberid'] !== "0"  && $poinearn !== "0")) {
                                $pointearned = lang('Global.pointearn');
                                echo "<div class='uk-width-1-2'>$pointearned</div>";
                                echo "<div class='uk-width-1-2 uk-text-left uk-text-right'>$poinearn</div>";
                            }?> 
                        </div>
                        <div class="uk-grid-collapse" uk-grid>
                            <?php if (($transactions['memberid'] !== "0")) {
                                $totpoin =  lang('Global.totalpoint');
                                echo " <div class='uk-width-1-2'>$totpoin</div>";
                                echo "<div class='uk-width-1-2 uk-text-right'>$mempoin</div>";
                            }?> 
                        </div>
                    </div>
                    <!-- end total transaction -->
                <?php } ?>
                
                <div class="uk-width-1-1 uk-text-bold">
                    <div class="uk-text-center fptagline" style="font-size: 10px;">#VapingSambilNongkrong</div>
                </div>

                <hr>
            </div>
        </div>
        <script>
        var lama = 1000;
        t = null;
        function printOut(){
            window.print();
            t = setTimeout("self.close()",lama);
        }
        </script>
    </body>
</html>