<?= $this->extend('layout') ?>
<?= $this->section('extraScript') ?>
    <script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
<?= $this->endSection() ?>
<?= $this->section('main') ?>

<!-- Page Heading -->
<div class="tm-card-header uk-light">
    <div class="uk-flex-middle">
        <h3 class="tm-h3"><?=lang('Global.trxHistory')?></h3>
    </div>
</div>
<!-- End Of Page Heading -->

<!-- Table Of Content -->
<table class="uk-table uk-table-justify uk-table-middle uk-table-divider uk-light">
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
<!-- Table Of Content End -->

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
<?= $this->endSection() ?>