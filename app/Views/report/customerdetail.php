<?= $this->extend('layout') ?>

<?= $this->section('extraScript') ?>
    <link rel="stylesheet" href="css/code.jquery.com_ui_1.13.2_themes_base_jquery-ui.css">
    <script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
    <script src="js/code.jquery.com_jquery-3.6.0.js"></script>
    <script src="js/code.jquery.com_ui_1.13.2_jquery-ui.js"></script>
    <script src="js/jquery.validate.min.js"></script>
    <script src="js/cdnjs.cloudflare.com_ajax_libs_webcamjs_1.0.25_webcam.min.js"></script>
<?= $this->endSection() ?>

<?= $this->section('main') ?>

<header class="tm-header" style="background-color:#000; border-top-left-radius: 40px;">
    <ul class="uk-flex-around tm-trx-tab uk-margin-top" uk-tab uk-switcher="connect: .switcher-class; active: 1;">
        <li>
            <a style="border-radius: 10px;" uk-switcher-item="0">
                <div width="45" height="30" uk-icon="credit-card"></div>
                <div class="uk-h4 uk-margin-small"><?= lang('Global.transaction') ?></div>
            </a>
        </li>
        <li>
            <a style="border-radius: 10px;" uk-switcher-item="0">
                <div width="45" height="30" uk-icon="folder"></div>
                <div class="uk-h4 uk-margin-small"><?= lang('Global.debt') ?></div>
            </a>
        </li>
    </ul>
</header>

<?= view('Views/Auth/_message_block') ?>

<ul class="uk-switcher switcher-class">
    <!-- Table transaction -->
    <li>
        <div class="uk-margin-top">
            <table class="uk-table uk-table-justify uk-table-middle uk-table-divider uk-light" id="example" style="width:100%">  
                <thead>
                    <tr>
                        <th class="uk-text-center"></th>
                        <th class=""><?= lang('Global.date') ?></th>
                        <th class=""><?= lang('Global.outlet') ?></th>
                        <th class=""><?= lang('Global.employee') ?></th>
                        <th class=""><?= lang('Global.paymethod') ?></th>
                        <th class=""><?= lang('Global.total') ?></th>
                        <th class="uk-text-center"></th>
                    </tr>
                </thead>
                    <tbody>
                        <?php foreach ($transactions as $transaction) { ?>
                            <tr>
                                <td class="uk-flex uk-flex-center">
                                    <a class="uk-icon-link uk-icon" uk-toggle="target:#detail-<?= $transaction['id'] ?>" uk-icon="search"></a>
                                </td>

                                <td class=""><?= $transaction['date'] ?></td>

                                <?php foreach ($outlets as $outlet) {
                                    if ($outlet['id'] === $transaction['outletid']) { ?>
                                        <td class=""><?= $outlet['name'] ?></td>
                                    <?php }
                                } ?>

                                <?php foreach ($users as $user) {
                                    if ($user->id === $transaction['userid']) {?>
                                        <td class=""><?= $user->name ?></td>
                                    <?php }
                                } ?>

                                <?php if ($transaction['paymentid'] === "0") { ?>
                                    <td class=""><?= lang('Global.splitbill') ?></td>
                                <?php } else {
                                    foreach ($payments as $payment) {
                                        if ($payment['id'] === $transaction['paymentid']) { ?>
                                            <td class=""><?= $payment['name'] ?></td>
                                        <?php }
                                    }
                                } ?>

                                <td class="">
                                    <?php
                                    $prices = array();
                                    foreach ($trxdetails as $trxdet) {
                                        if ($trxdet['transactionid'] === $transaction['id']) {
                                            $total = $trxdet['qty'] * $trxdet['value'];
                                            $prices [] = $total;
                                        } ?>
                                    <?php }
                                    $sum = array_sum($prices);
                                    echo "Rp " . number_format($sum,2,',','.'); ?>
                                </td>

                                <td class="uk-text-center">
                                    <?php if (!empty($transaction['amountpaid'])) {
                                        echo '<div class="uk-text-success" style="border-style: solid; border-color: #32d296;">'.lang('Global.paid').'</div>';
                                    } else {
                                        foreach ($debts as $debt) {
                                            if ($debt['transactionid'] === $transaction['id']) {
                                                if ($transaction['amountpaid'] - $debt['value'] < "0") {
                                                    echo '<div class="uk-text-danger" style="border-style: solid; border-color: #f0506e;">'.lang('Global.notpaid').'</div>';
                                                } elseif ($transaction['amountpaid'] - $debt['value'] >= "0") {
                                                    echo '<div class="uk-text-success" style="border-style: solid; border-color: #32d296;">'.lang('Global.paid').'</div>';
                                                }
                                            } 
                                        }
                                    } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
            </table>
        </div>
    
    <!-- Table transaction end -->

    <!-- Modal Detail -->
    <?php foreach ($transactions as $transaction) { ?>
        <div uk-modal class="uk-flex-top" id="detail-<?= $transaction['id'] ?>">
            <div class="uk-modal-dialog uk-margin-auto-vertical">
                <div class="uk-modal-content">
                    <div class="uk-modal-header uk-margin">
                        <div uk-grid>
                            <div class="uk-width-3-4@m">
                                <h5 class="uk-modal-title" ><?=lang('Global.detailTrx')?></h5>
                            </div>
                            <div class="uk-width-1-4@m">
                                <a class="uk-button uk-button-primary uk-preserve-color" href="pay/copyprint/<?=$transaction['id']?>"><?=lang('Global.print')?></a>
                            </div>
                        </div>
                    </div>
                    <div calss="uk-modal-body">
                        <div class="uk-margin">
                            <div class="uk-flex uk-flex-center">
                                <?php if (($gconfig['logo'] != null) && ($gconfig['bizname'] != null)) { ?>
                                    <img src="/img/<?=$gconfig['logo'];?>" alt="<?=$gconfig['bizname'];?>" style="height: 90px;">
                                <?php } else { ?>
                                    <img src="/img/binary111-logo-icon.svg" alt="PT. Kodebiner Teknologi Indonesia" style="height: 90px;">
                                <?php } ?>
                            </div>
                            <div class="uk-flex uk-flex-center">
                                <?php foreach ($outlets as $outlet) {
                                    if ($outlet['id'] === $transaction['outletid']) { ?>
                                        <div class="fpoutlet uk-h3 uk-margin-remove"><?= $outlet['name'] ?></div>
                                    <?php }
                                } ?>
                            </div>
                            <div class="uk-flex uk-flex-center">
                                <?php foreach ($outlets as $outlet) {
                                    if ($outlet['id'] === $transaction['outletid']) { ?>
                                        <div class="fpaddress uk-h4 uk-margin-remove"><?= $outlet['address'] ?></div>
                                    <?php }
                                } ?>
                            </div>

                            <div class="uk-padding-small">
                                <div uk-grid>
                                    <div class="uk-width-1-2">Invoice: <?=(strtotime("now")) ?></div>
                                    <div class="uk-width-1-2 uk-text-right"><?= $transaction['date'] ?></div>
                                </div>
                                <div class="uk-margin-remove-top uk-child-width-1-2" uk-grid>
                                    <div>Cashier: <?= $fullname ?></div>
                                    <div class="uk-text-right">
                                        <?php if ($transaction['paymentid'] === "0") { ?>
                                            <?= lang('Global.splitbill') ?>
                                        <?php } else {
                                            foreach ($payments as $payment) {
                                                if ($payment['id'] === $transaction['paymentid']) { ?>
                                                    <?= $payment['name'] ?>
                                                <?php }
                                            }
                                        } ?>
                                    </div>
                                </div>

                                <hr style ="border-top: 3px double #8c8b8b">

                                <?php foreach ($trxdetails as $trxdet) {
                                    // Variant
                                    if ($trxdet['variantid'] !== "0") {
                                        foreach ($variants as $variant) {
                                            foreach ($products as $product) { 
                                                if (($trxdet['variantid'] === $variant['id']) && ($product['id'] === $variant['productid']) && ($trxdet['transactionid'] === $transaction['id']) ) {
                                                    $variantName     = $variant['name'];
                                                    $productName     = $product['name']; 
                                                    $variantval      = $trxdet['value'];
                                                    ?>
                                                    <div class="uk-margin-small">
                                                        <div class="uk-h5 uk-text-bolder uk-margin-remove"><?=$productName.' - '.$variantName?></div>
                                                        <div uk-grid>
                                                            <div class="uk-width-1-2">
                                                                <div>x<?=$trxdet['qty']?> @<?=$variantval?></div>
                                                            </div>
                                                            <div class="uk-width-1-2 uk-text-right">
                                                                <div><?=$variantval * $trxdet['qty'] - $trxdet['discvar'] ?></div>
                                                            </div>
                                                        </div>
                                                        <?php if (!empty($trxdet['discvar'])) { ?>
                                                            <div class="uk-margin-remove-top">
                                                                <div class="uk-width-1-2">(<?= $trxdet['discvar'] ?>)</div>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                <?php }
                                            } 
                                        } 
                                    }
                                    // Variant End

                                    // Bundle
                                    if ($trxdet['bundleid'] !== "0") {
                                        foreach ($bundles as $bundle){
                                            if (($trxdet['transactionid'] === $transaction['id']) && ($trxdet['bundleid'] === $bundle['id']) ) {
                                            $bundleName      = $bundle['name'];
                                            $variantval      = $trxdet['value']; ?>
                                                <div class="uk-margin-small">
                                                    <div class="uk-h5 uk-text-bolder uk-margin-remove"><?= $bundleName ?></div>
                                                        <div class="uk-margin-small-left">
                                                            <?php foreach ($bundets as $bundet) {
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
                                                            } ?>
                                                        </div>
                                                    <div uk-grid>
                                                        <div class="uk-width-1-2">
                                                            <div>x<?=$trxdet['qty']?> @<?=$variantval?></div>
                                                        </div>
                                                        <div class="uk-width-1-2 uk-text-right">
                                                            <div><?=$variantval * $trxdet['qty']?></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } 
                                        }
                                    }
                                    // Bundle End
                                } ?>

                                <hr style ="border-top: 3px double #8c8b8b">
                        
                                <div class="uk-margin-small">
                                    <div uk-grid>
                                        <div class="uk-width-1-2">
                                            <div><?= lang('Global.subtotal') ?></div>
                                        </div>
                                        <div class="uk-width-1-2 uk-text-right uk-text-bold" style="color: #000;">
                                            <?php
                                            $subtotal = array();
                                            foreach ($trxdetails as $trxdet) {
                                                if ($transaction['id'] === $trxdet['transactionid']) {
                                                    $total = $trxdet['qty'] * $trxdet['value'] - $trxdet['discvar'];
                                                    $subtotal[] = $total; ?>
                                                <?php }
                                            }
                                            $sum = array_sum($subtotal); ?>
                                            <div><?= $sum; ?></div>
                                        </div>
                                    </div>
                                    <div class="uk-margin-remove-top" uk-grid>
                                        <?php if(!empty($transaction['discvalue'])) { ?>
                                            <div class="uk-width-1-2">
                                                <div><?= lang('Global.discount') ?></div>
                                            </div>
                                            <div class="uk-width-1-2 uk-text-right">
                                                <div>- <?= $transaction['discvalue'] ?></div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <div class="uk-margin-remove-top" uk-grid>
                                        <?php if (($transaction['memberid'] !== "0") && ($gconfig['memberdisc'] !== "0")) { ?>
                                            <div class="uk-width-1-2">
                                                <div><?= lang('Global.memberDiscount') ?></div>
                                            </div>
                                            <div class="uk-width-1-2 uk-text-right">
                                                <div>- <?= $gconfig['memberdisc'] ?></div>
                                            </div>
                                        <?php } ?> 
                                    </div>
                                    <div class="uk-margin-remove-top" uk-grid>
                                        <?php if (($transaction['memberid'] !== "0") && ($transaction['pointused'])) { ?>
                                            <div class="uk-width-1-2">
                                                <div><?= lang('Global.redeemPoint') ?></div>
                                            </div>
                                            <div class="uk-width-1-2 uk-text-right">
                                                <div>- <?= $transaction['pointused'] ?></div>
                                            </div>
                                        <?php } ?> 
                                    </div>

                                    <hr style ="border-top: 3px double #8c8b8b">

                                    <div class="uk-margin-remove-top" uk-grid>
                                        <div class="uk-width-1-2">
                                            <div><?= lang('Global.total') ?></div>
                                        </div>
                                        <div class="uk-width-1-2 uk-text-right uk-text-bolder" style="color: red;">
                                            <div><?= $transaction['value'] ?></div>
                                        </div>
                                    </div>

                                    <hr style ="border-top: 3px double #8c8b8b">

                                    <div class="uk-margin-remove-top" uk-grid>
                                        <div class="uk-width-1-2">
                                            <div><?= lang('Global.accepted') ?></div>
                                        </div>
                                        <div class="uk-width-1-2 uk-text-right uk-text-bolder" style="color: #000;">
                                            <div><?= $transaction['amountpaid'] ?></div>
                                        </div>
                                    </div>

                                    <div class="uk-margin-remove-top" uk-grid>
                                        <?php if (($transaction['amountpaid'] - $transaction['value'] !== "0")) { ?>
                                            <div class="uk-width-1-2">
                                                <div><?= lang('Global.change') ?></div>
                                            </div>
                                            <div class="uk-width-1-2 uk-text-right uk-text-bolder" style="color: #000;">
                                                <div><?= $transaction['amountpaid'] - $transaction['value'] ?></div>
                                            </div>
                                        <?php } ?>
                                    </div>

                                    <hr style ="border-top: 3px double #8c8b8b">

                                    <div class="uk-margin-remove-top" uk-grid>
                                        <?php if (($transaction['memberid'] !== "0")) {
                                            $pointearn = (floor($transaction['value'] / $gconfig['poinorder'])) * $gconfig['poinvalue']; ?>
                                            <div class="uk-width-1-2">
                                                <div><?= lang('Global.pointearn') ?></div>
                                            </div>
                                            <div class="uk-width-1-2 uk-text-right uk-text-bolder" style="color: #000;">
                                                <div><?= $pointearn ?></div>
                                            </div>
                                        <?php } ?> 
                                    </div>

                                    <div class="uk-margin-remove-top" uk-grid>
                                        <?php foreach ($customers as $cust) {
                                            if (($transaction['memberid'] !== "0") && $transaction['memberid'] === $cust['id']) { ?>
                                                <div class="uk-width-1-2">
                                                    <div><?= lang('Global.totalpoint') ?></div>
                                                </div>
                                                <div class="uk-width-1-2 uk-text-right uk-text-bolder" style="color: #000;">
                                                    <div><?= $cust['poin'] ?></div>
                                                </div>
                                            <?php }
                                        } ?> 
                                    </div>

                                    <hr style ="border-top: 3px double #8c8b8b">

                                    <div class="uk-flex uk-flex-center">
                                        <div class="fptagline uk-h3 uk-margin-remove">#VapingSambilNongkrong</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
    <!-- Modal Detail End -->
    </li>
    <!-- Table Debt -->
    <li>
        <div class="uk-margin-top">
            <table class="uk-table uk-table-justify uk-table-middle uk-table-divider uk-light" id="example" style="width:100%">
            <thead>
                <tr>
                    <th class="uk-text-center"></th>
                    <th class=""><?= lang('Global.date') ?></th>
                    <th class=""><?= lang('Global.outlet') ?></th>
                    <th class=""><?= lang('Global.customer') ?></th>
                    <th class=""><?= lang('Global.duedate') ?></th>
                    <th class=""><?= lang('Global.total') ?></th>
                </tr>
            </thead>
                <tbody>
                    <?php foreach ($debts as $debt) {
                        if ($debt['value'] !== "0") { ?>
                            <tr>
                                <td class="uk-flex uk-flex-center">
                                    <a class="uk-icon-link uk-icon" uk-icon="credit-card" uk-toggle="target:#pay-<?= $debt['id'] ?>"></a>
                                </td>

                                <?php foreach ($transactions as $trx) {
                                    if ($trx['id'] === $debt['transactionid']) { ?>
                                        <td class=""><?= $trx['date'] ?></td>

                                        <?php foreach ($outlets as $outlet) {
                                            if ($outlet['id'] === $trx['outletid']) { ?>
                                                <td class=""><?= $outlet['name'] ?></td>
                                            <?php }
                                        } ?>
                                    <?php }
                                } ?>
                                
                                <?php foreach ($customers as $cust) {
                                    if ($cust['id'] === $debt['memberid']) {?>
                                        <td class=""><?= $cust['name'] ?></td>
                                    <?php }
                                } ?>

                                <td class=""><?= $debt['deadline'] ?></td>

                                <td class="">Rp <?= number_format($debt['value'],2,',','.') ?></td>
                            </tr>
                        <?php }
                    } ?>
                </tbody>
            </table>
        </div>
    </li>
    <!-- Table Debt End -->
    
<!-- Modal Pay Debt -->
<?php foreach ($debts as $debt) { ?>
    <div uk-modal class="uk-flex-top" id="pay-<?= $debt['id'] ?>">
        <div class="uk-modal-dialog uk-margin-auto-vertical">
            <div class="uk-modal-content">
                <div class="uk-modal-header" style="background-color: #e3e3e3;">
                    <div class="uk-child-width-1-1" uk-grid>
                        <div class="uk-text-center">
                            <h3 class="tm-h2"><?=lang('Global.paybill')?></h5>
                        </div>
                        <div class="uk-margin-remove-top uk-text-center">
                            <h5 class="uk-modal-title" style="color: #000;">Rp <?= number_format($debt['value'],2,',','.') ?></h5>
                        </div>
                    </div>
                </div>
                <div class="uk-modal-body">
                    <div class="uk-form-horizontal">
                        <div class="uk-text-right">
                            <?php foreach ($transactions as $transaction) {
                                if ($transaction['id'] === $debt['transactionid']) { ?>
                                    <a class="uk-icon-button" uk-icon="print" href="pay/copyprint/<?=$transaction['id']?>"></a>
                                <?php }
                            } ?>
                        </div>

                        <div class="uk-margin uk-margin-remove-top">
                            <label class="uk-form-label"><?=lang('Global.customer')?></label>
                            <div class="uk-form-controls">: 
                                <?php foreach ($customers as $cust) {
                                    if ($debt['memberid'] === $cust['id']) {
                                        echo $cust['name'];
                                    }
                                } ?>
                            </div>
                        </div>

                        <form role="form" action="debt/pay/<?= $debt['id'] ?>" method="post">
                            <?= csrf_field() ?>

                            <div class="uk-margin-bottom">
                                <label class="uk-form-label" for="value"><?=lang('Global.amountpaid')?></label>
                                <div class="uk-form-controls">
                                    <input type="number" class="uk-input" min="0" max="<?= $debt['value'] ?>" id="value" name="value" placeholder="0" required />
                                </div>
                            </div>

                            <?php if ($debt['value'] !== "0") { ?>
                                <div class="uk-margin">
                                    <label class="uk-form-label"><?=lang('Global.duedate')?></label>
                                    <div class="uk-form-controls">
                                        <div class="uk-inline">
                                            <span class="uk-form-icon uk-form-icon-flip" uk-icon="icon: calendar"></span>
                                            <input class="uk-input uk-form-width-medium" id="duedate<?= $debt['id'] ?>" name="duedate<?= $debt['id'] ?>" />
                                            <script type="text/javascript">
                                                $( function() {
                                                    $( "#duedate<?= $debt['id'] ?>" ).datepicker({
                                                        dateFormat: "yy-mm-dd",
                                                        minDate: "<?= $debt['deadline'] ?>",
                                                        maxDate: "+1m +1w"
                                                    });
                                                } );
                                            </script>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>

                            <div class="uk-margin">
                                <div class="uk-form-controls">
                                    <a class="uk-button uk-button-default" uk-toggle="#payproof-<?= $debt['id'] ?>"><?= lang('Global.payproof') ?></a>
                                </div>
                            </div>

                            <div class="uk-margin" hidden>
                                <input class="image-tag" name="image" required>
                            </div>

                            <hr>

                            <div class="uk-margin">
                                <div class="uk-width-5-6">
                                    <button type="submit" class="uk-button uk-button-primary" style="border-radius: 8px; width: 540px;"><?=lang('Global.pay')?></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal Pay Proof -->
    <div uk-modal class="uk-flex-top" id="payproof-<?= $debt['id'] ?>">
        <div class="uk-modal-dialog uk-margin-auto-vertical">
            <div class="uk-modal-content">
                <div class="uk-modal-header">
                    <div class="uk-flex uk-flex-middle uk-child-width-auto" uk-grid>
                        <div class="uk-padding-remove uk-margin-medium-left">
                            <a uk-icon="arrow-left" uk-toggle="#pay-<?= $debt['id'] ?>" width="35" height="35"></a>
                        </div>
                        <div>
                            <h5 class="uk-modal-title" ><?=lang('Global.payproof')?></h5>
                        </div>
                    </div>
                </div>
                <div class="uk-modal-body">
                    <div class="uk-flex uk-flex-center uk-child-width-1-1" uk-grid>
                        <div class="uk-margin-left">
                            <div id="pay_camera<?= $debt['id'] ?>"></div>
                        </div>
                        <div class="uk-text-center">
                            <input class="uk-button uk-button-primary" id="btnTake" type="button" value="Take Snapshot" onClick="pay_snapshot<?= $debt['id'] ?>()">
                        </div>
                        <div class="uk-text-center">
                            <div id="pay_results<?= $debt['id'] ?>"></div>
                        </div>
                    </div>

                    <!-- Script Webcam Pay Proof -->
                    <script type="text/javascript">
                        Webcam.set({
                            width: 490,
                            height: 390,
                            image_format: 'jpeg',
                            jpeg_quality: 90
                        });
                    
                        Webcam.attach( '#pay_camera<?= $debt['id'] ?>' );

                        function pay_snapshot<?= $debt['id'] ?>() {
                            Webcam.snap( function(data_uri) {
                                $(".image-tag").val(data_uri);
                                document.getElementById('pay_results<?= $debt['id'] ?>').innerHTML = '<img src="'+data_uri+'"/>';
                            } );
                        }
                    </script>
                    <!-- Script Webcam Pay Proof End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Pay Proof End -->
<?php } ?>
<!-- Modal Pay Debt End -->
</ul>
<?= $this->endSection() ?>