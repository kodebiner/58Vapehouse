<!doctype html>
<html dir="ltr "lang="<?=$lang?>" vocab="http://schema.org/" style="overflow-y: hidden; background-color:#000;">
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
        <script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
        <style>
            @media print {  
                #btn{
                    display : none;
                }
            }
        </style>
    </head>
    <nav class="uk-navbar-container">
        <div class="uk-container">
            <div uk-navbar>

                <div class="uk-navbar-left">
                    <div class="uk-navbar-item uk-logo">
                        <?php if (($gconfig['logo'] != null) && ($gconfig['bizname'] != null)) { ?>
                            <img src="/img/<?=$gconfig['logo'];?>" alt="<?=$gconfig['bizname'];?>" style="height: 60px;">
                        <?php } else { ?>
                            <img src="/img/binary111-logo-icon.svg" alt="PT. Kodebiner Teknologi Indonesia" style="height: 60px;">
                        <?php } ?>
                    </div>
                    <a class="uk-navbar-item uk-logo fptagline" style="font-size:35px;" href="#" aria-label="Back to Home">58 Vapehouse Invoice</a>
                </div>

                <div class="uk-navbar-right">
                    <div class="uk-navbar-item uk-margin-right-left">
                        <a class="uk-icon-button" uk-icon="arrow-left" href="<?= base_url('transaction') ?>"></a>
                    </div>
                    <div class="uk-text-center">
                        <!-- transaction member -->
                        <?php if (!empty($transactions['id']) && !empty($transactions['memberid'])){
                            foreach ($members as $member){
                                if($transactions['memberid'] === $member['id']){
                                    $memphone = $member['phone'];
                                    echo "<a class='uk-icon-button' uk-icon='whatsapp' href='https://wa.me/62$memphone?text=$links'></a>";
                                }
                            }
                            // transactions non member
                        } elseif ( !empty($transactions['id']) && $transactions['memberid'] ==="0" ){
                            echo'<a class="uk-icon-button" uk-icon="whatsapp" id="phonenumber" uk-toggle="target: #phonenumber" href="" uk-toggle></a>';
                            // bookings member
                        } elseif ( !empty($bookings['id']) && !empty($bookings['memberid']) ){
                            foreach ($members as $member){
                                if($bookings['memberid'] === $member['id']){
                                    $memphone = $member['phone'];
                                    echo "<a class='uk-icon-button' uk-icon='whatsapp' href='https://wa.me/62$memphone?text=$links'></a>";
                                }
                            }
                            // bookings non memeber
                        } elseif ( !empty($bookings['id']) && empty($member['id']) ){
                            echo'<a class="uk-icon-button" uk-icon="whatsapp" id="phonenumber" uk-toggle="target: #phonenumber" href="" uk-toggle></a>';
                        } ?>
                        <!-- modal phonenumber -->
                        <div class="uk-flex-top" id="phonenumber" uk-modal>

                            <div class="uk-modal-dialog uk-margin-auto-vertical">
                                <button class="uk-modal-close-default" type="button" uk-close></button>
                                <div class="uk-modal-header">
                                    <h2 class="uk-modal-title"><?=lang('Global.phonenumber')?></h2>
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
                                            <?php echo "<a class='uk-button uk-button-primary' id='phone' href=''>submit</a>"; ?>
                                        </div>
                                        <script>
                                        $(document).ready(function(){
                                                $("#phoneinput").keyup(function(){
                                                    let phone = $("#phoneinput").val();
                                                    $("#phone").attr("href", "https://wa.me/62"+phone+"?text=<?=$links?>");
                                                    console.log(phone);
                                                });
                                            });
                                        </script>
                                    </form>
                                </div>
                            </div>

                        </div>
                        <!-- end modal phonenumber -->
                    </div>
                </div>
            </div>
        </div>
    </nav>
    
    <body style="background-color:#000;">
        <div class="uk-position-center">
            <div class="uk-padding-small" style="width:58mm; background: #fff;" >
                <div class="uk-margin-small uk-margin-top uk-width-1-1 uk-text-center">
                    <?php if (($gconfig['logo'] != null) && ($gconfig['bizname'] != null)) { ?>
                        <img src="/img/<?=$gconfig['logo'];?>" alt="<?=$gconfig['bizname'];?>" style="height: 60px;">
                    <?php } else { ?>
                        <img src="/img/binary111-logo-icon.svg" alt="PT. Kodebiner Teknologi Indonesia" style="height: 60px;">
                    <?php } ?>
                </div>

                <?php if(!empty($transaction['id'])) {?>
                    <div class="uk-flex uk-flex-center">
                        <?php foreach ($outlets as $outlet) {
                            if ($outlet['id'] === $transactions['outletid']) { ?>
                                <div class="fpoutlet uk-margin-remove" style="font-size:12px;" ><?= $outlet['name'] ?></div>
                            <?php }
                        } ?>
                    </div>
                    <div class="uk-flex uk-flex-center">
                        <?php foreach ($outlets as $outlet) {
                            if ($outlet['id'] === $transactions['outletid']) { ?>
                                <p class="fpaddress uk-margin-remove uk-text-bold" style="font-size:10px;"><?= $outlet['address'] ?></p>
                            <?php }
                        } ?>
                    </div>
                <?php } elseif(!empty($bookings['id'])){?>
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
                                <p class="fpaddress uk-margin-remove uk-text-bold" style="font-size:10px;"><?= $outlet['address'] ?></p>
                            <?php }
                        } ?>
                    </div>
                <?php }?>
                <div class="uk-text-xsmall uk-margin-top">
                    <div uk-grid>
                        <div class="uk-width-1-2"><?=lang('Global.invoice')?>: <?=(strtotime("now")) ?></div>
                        <?php if (!empty($transactions['id'])){?>
                            <div class="uk-width-1-2 uk-text-right"><?= $transactions['date'] ?></div>
                        <?php }elseif(!empty($bookings['id'])){?>
                            <div class="uk-width-1-2 uk-text-right"><?= $bookings['created_at'] ?></div>
                        <?php }?>
                    </div>
                    <div class="uk-margin-remove-top" uk-grid>
                        <div class="uk-width-2-3">Cashier: <?= $fullname ?></div>
                        <div class="uk-text-right uk-width-1-3">
                            <?php if(!empty($transactionid)){
                                if ($transactions['paymentid'] === "0") { ?>
                                    <?= lang('Global.splitbill')?>
                                <?php } else {
                                    foreach($payments as $payment) {
                                        if ($transactions['paymentid'] === $payment['id'] && ($transactions['paymentid'] !== "0")) {
                                            foreach ($trxpayments as $trxpay){
                                                if($trxpay['paymentid'] === $payment['id']){
                                                    foreach ($cash as $cas){
                                                        if($payment['cashid']  === $cas['id'] )
                                                        echo $payment['name'];
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
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
                                        $variantName     = $variant['name'];
                                        $productName     = $product['name']; 
                                        $variantval      = $trxdet['value'] + $trxdet['discvar'];
                                        ?>
                                        <div class="uk-margin-small uk-text-xsmall">
                                            <div class="uk-text-bold">
                                                <?=$productName.' - '.$variantName?>
                                            </div>
                                            <div class="uk-grid-collapse" uk-grid>
                                                <div class="uk-width-1-2">x<?=$trxdet['qty']?> @<?=$variantval?></div>
                                                <div class="uk-width-1-2 uk-text-right"><?=$variantval * $trxdet['qty']?></div>
                                            </div>
                                            <div class="uk-grid-collapse" uk-grid>
                                                <?php
                                                if (!empty($trxdet['discvar'])){
                                                    echo "<div class='uk-width-1-2'>Discount</br> @" .$trxdet['discvar']. "</div>";
                                                    echo "<div class='uk-width-1-2 uk-text-right'></br>-" .$trxdet['discvar']. "</div>";
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
                                    $bundleName      = $bundle  ['name'];
                                    $variantval      = $trxdet  ['value'];
                                    ?>
                                    <div class="uk-margin-small uk-text-xsmall">
                                        <div>
                                            x<?=$trxdet['qty']?> <?=lang('Global.bundle')?> <br> <?= $bundleName?> <br>
                                            <div class="uk-grid-collapse" uk-grid>
                                                <div class="uk-width-2-3"> @<?=$variantval?></div>
                                                <div class="uk-width-1-3"><?=$variantval * $trxdet['qty']?></div>
                                            </div>
                                            <?php 
                                            foreach ($bundets as $bundet){
                                                foreach ($products as $product) { 
                                                    foreach ($variants as $variant){    
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
                    if(!empty($bookings) && (empty($transactions['id']))){
                        foreach ($bookingdetails as $bookingdetail) {
                            if ($bookingdetail['variantid'] !== '0') {
                                foreach ($variants as $variant) {
                                    foreach ($products as $product) {
                                        if (($bookingdetail['variantid'] === $variant['id']) && ($variant['productid'] === $product['id'])) {
                                            $variantName    = $variant['name'];
                                            $productName    = $product['name'];
                                            $variantval     = $bookingdetail['value'] + $bookingdetail['discvar'];

                                            echo '<div class="uk-margin-small uk-text-xsmall">';
                                            echo '<div>';
                                            echo $productName.' - '.$variantName;
                                            echo '</div>';
                                            echo '<div class="uk-grid-collapse" uk-grid>';
                                            echo '<div class="uk-width-2-3">x'.$bookingdetail['qty'].' @'.$variantval.'</div>';
                                            echo '<div class="uk-width-1-3 uk-text-right">'.$variantval * $bookingdetail['qty'].'</div>';
                                            echo '</div>';
                                            if ($bookingdetail['discvar'] !== '0') {
                                                echo '<div class="uk-grid-collapse" uk-grid>';
                                                echo "<div class='uk-width-2-3'>Discount</br> @" .(int)$bookingdetail['discvar']. "</div>";
                                                echo "<div class='uk-width-1-3'></br>-" .$bookingdetail['discvar']. "</div>";
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
                                        echo '<div class="uk-width-2-3"> @'.$bookingdetail['value'].'</div>';
                                        echo '<div class="uk-width-1-3 uk-text-right">'.$bookingdetail['value'] * $bookingdetail['qty'].'</div>';
                                        echo '</div>';
                                        foreach ($bundets as $bundet) {
                                            foreach ($products as $product) {
                                                foreach ($variants as $variant) {
                                                    if (($bundet['variantid'] === $variant['id']) && ($variant['productid'] === $product['id'])) {
                                                        echo "# ".$product['name']."-".$variant['name']."</br>";
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
                if (!empty($bookings['id']) && empty($transactions['id']) && $bookings['id'] !== "0"){  ?>
                   
               
                    <hr style ="border-top: 3px double #8c8b8b">
                    <div class="uk-margin-small uk-text-xsmall">
                        <div class="uk-grid-collapse" uk-grid>
                            <?php $sub =  lang('Global.subtotal'); ?>
                            <div class="uk-width-1-2 uk-text-bold "><?=$sub?></div>
                            <div class="uk-width-1-2  uk-text-bold uk-text-right"><?=$subtotal?></div>
                        </div>
                        <div class="uk-grid-collapse" uk-grid>
                            <?php if(!empty($discount)){
                                $disc =  lang('Global.discount');
                                echo "<div class='uk-width-1-2 uk-text-bold'>$disc</div>";
                                echo "<div class='uk-width-1-2  uk-text-bold uk-text-right'>".$discount."</div>";
                            }?>
                        </div>
                        <div class="uk-grid-collapse" uk-grid>
                            <?php if (($bookings['memberid'] !== "0") && ($bookings['id']=== $bookingid) && ($gconfig['memberdisc'] !== "0")) {
                                $memberdisc = $gconfig['memberdisc'];
                                $discmember = lang('Global.memberDiscount');
                                echo "<div class='uk-width-1-2'>$discmember</div>";
                                echo "<div class='uk-width-1-2 uk-text-right'>$memberdisc</div>";
                            }?> 
                        </div>
                        <div class="uk-grid-collapse" uk-grid>
                            <?php $tot =  lang('Global.total');?>
                            <div class="uk-width-1-2 uk-text-bold"><?=$tot?></div>
                            <div class="uk-width-1-2  uk-text-bold uk-text-right"><?=$total?></div>
                        </div>
                        <div class="uk-grid-collapse" uk-grid>
                            <?php if ($pay !== "0"){
                                $pays =  lang('Global.pay');
                                echo "<div class='uk-width-1-2'>$pays</div>";
                                echo "<div class='uk-width-1-2 uk-text-bold uk-text-right'>$pay</div>";
                            }?>
                        </div>
                        <div class="uk-grid-collapse" uk-grid>
                            <?php if ($change !== "0"){
                                $changes = lang('Global.change');
                                echo "<div class='uk-width-1-2'>$changes</div>";
                                echo "<div class='uk-width-1-2 uk-text-right'>$change</div>";
                            }?>
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
                            <?php if (($bookings['memberid'] !== "0") && ($bookings['id']=== $bookingid) && (!empty($poinused))) {
                                $poinearn = $gconfig['poinvalue'];
                                $reedem = lang('Global.redeemPoint');
                                echo "<div class='uk-width-1-2'>$reedem</div>";
                                echo "<div class='uk-width-1-2 uk-text-right'>$poinused</div>";
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
                        <div class="uk-grid-collapse" uk-grid>
                            <?php if (!empty($totaldebt)) {
                                 $totdebt = lang('Global.totaldebt');
                                 echo " <div class='uk-width-1-2 uk-text-bold'>$totdebt</div>";
                                echo "<div class='uk-width-1-2 uk-text-right'>$totaldebt</div>";
                            }?> 
                        </div>
                    </div>
                    <!-- end total booking -->
                    <!-- total transaction -->
                    <?php } elseif(!empty($transactions['id'])) { ?>
                    <hr style ="border-top: 3px double #8c8b8b">                    
                    <div class="uk-margin-small uk-text-xsmall">
                        <div class="uk-grid-collapse" uk-grid>
                            <?php $sub =  lang('Global.subtotal'); ?>
                            <div class="uk-width-1-2 uk-text-bold "><?=$sub?></div>
                            <div class="uk-width-1-2  uk-text-bold uk-text-right"><?=$subtotal?></div>
                        </div>
                        <div class="uk-grid-collapse" uk-grid>
                            <?php if(!empty($discount)){
                                $disc =  lang('Global.discount');
                                echo "<div class='uk-width-1-2 uk-text-bold'>$disc</div>";
                                echo "<div class='uk-width-1-2  uk-text-bold uk-text-right'>".$discount."</div>";
                            }?>
                        </div>
                        <div class="uk-grid-collapse" uk-grid>
                            <?php if (($transactions['memberid'] !== "0") && ($gconfig['memberdisc'] !== "0")) {
                                $memberdisc = $gconfig['memberdisc'];
                                $discmember = lang('Global.memberDiscount');
                                echo "<div class='uk-width-1-2'>$discmember</div>";
                                echo "<div class='uk-width-1-2 uk-text-right'>$memberdisc</div>";
                            }?> 
                        </div>
                        <div class="uk-grid-collapse" uk-grid>
                            <?php $tot =  lang('Global.total');?>
                            <div class="uk-width-1-2 uk-text-bold"><?=$tot?></div>
                            <div class="uk-width-1-2  uk-text-bold uk-text-right"><?=$total?></div>
                        </div>
                        <div class="uk-grid-collapse" uk-grid>
                            <?php if ($pay !== "0"){
                                $pays =  lang('Global.pay');
                                echo "<div class='uk-width-1-2'>$pays</div>";
                                echo "<div class='uk-width-1-2 uk-text-bold uk-text-right'>$pay</div>";
                            }?>
                        </div>
                        <div class="uk-grid-collapse" uk-grid>
                            <?php if ($change !== "0"){
                                $changes = lang('Global.change');
                                echo "<div class='uk-width-1-2'>$changes</div>";
                                echo "<div class='uk-width-1-2 uk-text-right'>$change</div>";
                            }?>
                        </div>
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
                            <?php if (($transactions['memberid'] !== "0") && ($transactions['pointused']) !== "0") {
                                $reedem = lang('Global.redeemPoint');
                                echo "<div class='uk-width-1-2'>$reedem</div>";
                                echo "<div class='uk-width-1-2 uk-text-right'>$poinused</div>";
                            }?> 
                        </div>
                        <div class="uk-grid-collapse" uk-grid>
                            <?php if (($transactions['memberid'] !== "0")) {
                                $totpoin =  lang('Global.totalpoint');
                                echo " <div class='uk-width-1-2'>$totpoin</div>";
                                echo "<div class='uk-width-1-2 uk-text-right'>$mempoin</div>";
                            }?> 
                        </div>
                        <div class="uk-grid-collapse" uk-grid>
                            <?php if (!empty($debt)) {
                                $debttext = lang('Global.debt');
                                echo " <div class='uk-width-1-2  uk-text-bold'>$debttext</div>";
                                echo "<div class='uk-width-1-2 uk-text-right'>$debt</div>";
                            }?> 
                        </div>
                        <div class="uk-grid-collapse" uk-grid>
                            <?php if (!empty($totaldebt)) {
                                $totdebt = lang('Global.totaldebt');
                                echo " <div class='uk-width-1-2 uk-text-bold'>$totdebt</div>";
                                echo "<div class='uk-width-1-2 uk-text-right'>$totaldebt</div>";
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