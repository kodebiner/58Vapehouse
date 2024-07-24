<?= $this->extend('layout') ?>
<?= $this->section('extraScript') ?>
<script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
<script type="text/javascript" src="js/moment.min.js"></script>
<script type="text/javascript" src="js/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="css/daterangepicker.css" />
<?= $this->endSection() ?>
<?= $this->section('main') ?>

<!-- Page Heading -->
<div class="tm-card-header uk-light uk-margin-bottom">
    <div uk-grid class="uk-flex-middle">
        <div class="uk-width-1-3@m uk-width-1-1">
            <h3 class="tm-h3"><?= lang('Global.trxHistory') ?></h3>
        </div>
        <div class="uk-width-1-3@m uk-width-1-2 uk-margin-right-remove">
            <form id="short" action="trxhistory" method="get">
                <div class="uk-inline">
                    <span class="uk-form-icon uk-form-icon-flip" uk-icon="calendar"></span>
                    <input class="uk-input uk-width-medium uk-border-rounded" type="text" id="daterange" name="daterange" value="<?= date('m/d/Y', $startdate) ?> - <?= date('m/d/Y', $enddate) ?>" />
                </div>
            </form>
            <script>
                $(function() {
                    $('input[name="daterange"]').daterangepicker({
                        maxDate: new Date(),
                        opens: 'right'
                    }, function(start, end, label) {
                        document.getElementById('daterange').value = start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD');
                        document.getElementById('short').submit();
                    });
                });
            </script>
        </div>

        <!-- Button Trigger Modal export -->
        <div class="uk-width-1-3@m uk-width-1-2 uk-text-right">
            <a type="button" class="uk-button uk-button-primary uk-preserve-color uk-margin-right-remove" target="_blank" href="export/transaction?daterange=<?= date('Y-m-d', $startdate) ?>+-+<?= date('Y-m-d', $enddate) ?>"><?= lang('Global.export') ?></a>
        </div>
    </div>
</div>

<?= view('Views/Auth/_message_block') ?>

<!-- Table Of Content -->
<div class="uk-overflow-auto">
    <table class="uk-table uk-table-justify uk-table-middle uk-table-divider uk-light" style="width:100%">
        <thead>
            <tr>
                <th class="uk-text-center"><?= lang('Global.detail') ?></th>
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
                    <td class="uk-flex-middle uk-text-center">
                        <a class="uk-icon-link uk-icon" uk-toggle="target:#detail-<?= $transaction['id'] ?>" uk-icon="search"></a>
                    </td>

                    <td class=""><?= date('l, d M Y, H:i:s', strtotime($transaction['date'])); ?></td>

                    <?php foreach ($outlets as $outlet) {
                        if ($outlet['id'] === $transaction['outletid']) { ?>
                            <td class=""><?= $outlet['name'] ?></td>
                    <?php }
                    } ?>

                    <?php foreach ($users as $user) {
                        if ($user->id === $transaction['userid']) { ?>
                            <td class=""><?= $user->name ?></td>
                    <?php }
                    } ?>

                    <?php if (($transaction['paymentid'] === "0") && ($transaction['amountpaid'] != "0")) { ?>
                        <td class=""><?= lang('Global.splitbill') ?></td>
                    <?php } elseif (($transaction['paymentid'] === "0") && ($transaction['amountpaid'] === "0")) { ?>
                        <td class=""><?= lang('Global.debt') ?></td>
                    <?php } else {
                        foreach ($payments as $payment) {
                            if ($payment['id'] === $transaction['paymentid']) { ?>
                                <td class=""><?= $payment['name'] ?></td>
                            <?php }
                        }
                    } ?>

                    <td class=""><?= "Rp " . number_format($transaction['value'], 2, ',', '.'); ?></td>

                    <td class="uk-text-center uk-column-1-2">
                        <?php if (!empty($transaction['amountpaid'])) {
                            echo '<div class="uk-text-success" style="border-style: solid; border-color: #32d296;">' . lang('Global.paid') . '</div>';
                        } else {
                            foreach ($debts as $debt) {
                                if ($debt['transactionid'] === $transaction['id']) {
                                    if ($transaction['amountpaid'] - $debt['value'] < "0") {
                                        echo '<div class="uk-text-danger" style="border-style: solid; border-color: #f0506e;">' . lang('Global.notpaid') . '</div>';
                                    } elseif ($transaction['amountpaid'] - $debt['value'] >= "0") {
                                        echo '<div class="uk-text-success" style="border-style: solid; border-color: #32d296;">' . lang('Global.paid') . '</div>';
                                    }
                                }
                            }
                        } ?>
                        <div class="uk-text-success" id="refund" onclick="return confirm('<?= lang('Global.deleteConfirm') ?>')" style="border-style: solid; border-color: red;"><a href="trxhistory/refund/<?= $transaction['id'] ?>" class="uk-link-heading">Refund</a></div>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <div>
        <?= $pager->links('trxhistory', 'front_full') ?>
    </div>
</div>
<!-- Table Of Content End -->

<!-- Modal Detail -->
<?php foreach ($transactions as $transaction) { ?>
    <div uk-modal class="uk-flex-top" id="detail-<?= $transaction['id'] ?>">
        <div class="uk-modal-dialog uk-margin-auto-vertical">
            <div class="uk-modal-content">
                <div class="uk-modal-header uk-margin">
                    <div uk-grid>
                        <div class="uk-width-1-2@m">
                            <h5 class="uk-modal-title"><?= lang('Global.detailTrx') ?></h5>
                        </div>
                        <div class="uk-width-1-4@m">
                            <a class="uk-button uk-button-primary uk-preserve-color" href="pay/copyprint/<?= $transaction['id'] ?>"><?= lang('Global.print') ?></a>
                        </div>
                        <div class="uk-width-1-4@m uk-text-right">
                            <button class="uk-modal-close uk-icon-button-delete" uk-icon="icon: close;" type="button"></button>
                        </div>
                    </div>
                </div>
                <div calss="uk-modal-body">
                    <div class="uk-margin">
                        <div class="uk-padding-small">
                            <div class="uk-flex uk-flex-center">
                                <?php if (($gconfig['logo'] != null) && ($gconfig['bizname'] != null)) { ?>
                                    <img src="/img/<?= $gconfig['logo']; ?>" alt="<?= $gconfig['bizname']; ?>" style="height: 90px;">
                                <?php } else { ?>
                                    <img src="/img/binary111-logo-icon.svg" alt="PT. Kodebiner Teknologi Indonesia" style="height: 90px;">
                                <?php } ?>
                            </div>
                            <div class="uk-flex uk-flex-center">
                                <?php foreach ($outlets as $outlet) {
                                    if ($outlet['id'] === $transaction['outletid']) { ?>
                                        <div class="fpoutlet uk-h3 uk-margin-remove uk-text-justify"><?= $outlet['name'] ?></div>
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
                            <div class="uk-flex uk-flex-center">
                                <?php foreach ($outlets as $outlet) {
                                    if ($outlet['id'] === $transaction['outletid']) { ?>
                                        <div class="fpaddress uk-h4 uk-margin-remove"><span uk-icon="instagram"></span> : <?= $outlet['instagram'] ?></div>
                                <?php }
                                } ?>
                            </div>
                            <div class="uk-flex uk-flex-center">
                                <?php foreach ($outlets as $outlet) {
                                    if ($outlet['id'] === $transaction['outletid']) { ?>
                                        <div class="fpaddress uk-h4 uk-margin-remove"><span uk-icon="whatsapp"></span> : <?= $outlet['phone'] ?></div>
                                <?php }
                                } ?>
                            </div>

                            <div uk-grid>
                                <div class="uk-width-1-2">Invoice: <?= (strtotime($transaction['date'])) ?></div>
                                <div class="uk-width-1-2 uk-text-right"><?= date('l, d M Y, H:i:s', strtotime($transaction['date'])); ?></div>
                            </div>
                            <div class="uk-margin-remove-top uk-child-width-1-2" uk-grid>
                                <?php foreach ($users as $user) {
                                    if ($user->id === $transaction['userid']) { ?>
                                        <div>Cashier: <?= $user->name ?></div>
                                    <?php }
                                } ?>
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

                            <hr style="border-top: 3px double #8c8b8b">

                            <?php foreach ($trxdetails as $trxdet) {
                                // Variant
                                if ($trxdet['variantid'] !== "0") {
                                    foreach ($products as $product) {
                                        if (($trxdet['variantid'] === $product['id']) && ($trxdet['transactionid'] === $transaction['id'])) {
                                            $variantval      = (Int)$trxdet['value'] + ((Int)$trxdet['discvar'] / (Int)$trxdet['qty']) + ((Int)$trxdet['globaldisc'] / (Int)$trxdet['qty']);
                            ?>
                                            <div class="uk-margin-small">
                                                <div class="uk-h5 uk-text-bolder uk-margin-remove"><?= $product['name'] ?></div>
                                                <div uk-grid>
                                                    <div class="uk-width-1-2">
                                                        <div>x<?= $trxdet['qty'] ?> @<?= $variantval ?></div>
                                                    </div>
                                                    <div class="uk-width-1-2 uk-text-right">
                                                        <div><?= ((int)$variantval * (int)$trxdet['qty']) ?></div>
                                                    </div>
                                                </div>
                                                <?php if ($trxdet['discvar'] != '0') { ?>
                                                    <div class="uk-child-width-1-2 uk-margin-remove-top" uk-grid>
                                                        <div>
                                                            <div>(<?= (Int)$trxdet['discvar'] / (Int)$trxdet['qty'] ?>)</div>
                                                        </div>
                                                        <div class="uk-text-right">
                                                            <div>- <?= $trxdet['discvar'] ?></div>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                                <?php if ($trxdet['globaldisc'] != '0') { ?>
                                                    <div class="uk-child-width-1-2 uk-margin-remove-top" uk-grid>
                                                        <div>
                                                            <div>(<?= (Int)$trxdet['globaldisc'] / (Int)$trxdet['qty'] ?>)</div>
                                                        </div>
                                                        <div class="uk-text-right">
                                                            <div>- <?= $trxdet['globaldisc'] ?></div>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        <?php }
                                    }
                                }
                                // Variant End

                                // Bundle
                                if ($trxdet['bundleid'] !== "0") {
                                    foreach ($bundles as $bundle) {
                                        if (($trxdet['transactionid'] === $transaction['id']) && ($trxdet['bundleid'] === $bundle['id'])) {
                                            $bundleName      = $bundle['name'];
                                            $variantval      = (Int)$trxdet['value'] + ((Int)$trxdet['globaldisc'] / (Int)$trxdet['qty']);
                                            ?>

                                            <div class="uk-margin-small">
                                                <div class="uk-h5 uk-text-bolder uk-margin-remove"><?= $bundleName ?></div>
                                                <div class="uk-margin-small-left">
                                                    <?php foreach ($bundets as $bundet) {
                                                        foreach ($products as $product) {
                                                            if (($product['id'] === $bundet['variantid']) && ($trxdet['bundleid'] === $bundet['bundleid']) && ($bundle['id'] === $bundet['bundleid'])) {
                                                                echo "# " . $product['name'] . "</br>";
                                                            }
                                                        }
                                                    } ?>
                                                </div>
                                                <div uk-grid>
                                                    <div class="uk-width-1-2">
                                                        <div>x<?= $trxdet['qty'] ?> @<?= $variantval ?></div>
                                                    </div>
                                                    <div class="uk-width-1-2 uk-text-right">
                                                        <div><?= (int)$variantval * (int)$trxdet['qty'] ?></div>
                                                    </div>
                                                </div>
                                                <?php if ($trxdet['globaldisc'] != '0') { ?>
                                                    <div class="uk-child-width-1-2 uk-margin-remove-top" uk-grid>
                                                        <div>
                                                            <div>(<?= (Int)$trxdet['globaldisc'] / (Int)$trxdet['qty'] ?>)</div>
                                                        </div>
                                                        <div class="uk-text-right">
                                                            <div>- <?= $trxdet['globaldisc'] ?></div>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        <?php }
                                    }
                                }
                                // Bundle End
                            } ?>

                            <hr style="border-top: 3px double #8c8b8b">

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
                                                $total = ((int)$trxdet['qty'] * (int)$trxdet['value']);
                                                $subtotal[] = $total; ?>
                                        <?php }
                                        }
                                        $sum = array_sum($subtotal); ?>
                                        <div><?= $sum; ?></div>
                                    </div>
                                </div>

                                <div class="uk-margin-remove-top" uk-grid>
                                    <?php if (!empty($transaction['discvalue'])) { ?>
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
                                            <div>- <?= $transaction['memberdisc'] ?></div>
                                        </div>
                                    <?php } ?>
                                </div>

                                <div class="uk-margin-remove-top" uk-grid>
                                    <?php if (($transaction['memberid'] !== "0") && ($transaction['pointused'] !== "0")) { ?>
                                        <div class="uk-width-1-2">
                                            <div><?= lang('Global.redeemPoint') ?></div>
                                        </div>
                                        <div class="uk-width-1-2 uk-text-right">
                                            <div>- <?= $transaction['pointused'] ?></div>
                                        </div>
                                    <?php } ?>
                                </div>

                                <hr style="border-top: 3px double #8c8b8b">

                                <div class="uk-margin-remove-top" uk-grid>
                                    <div class="uk-width-1-2">
                                        <div><?= lang('Global.total') ?></div>
                                    </div>
                                    <div class="uk-width-1-2 uk-text-right uk-text-bolder" style="color: red;">
                                        <div><?= $transaction['value'] ?></div>
                                    </div>
                                </div>

                                <hr style="border-top: 3px double #8c8b8b">

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
                                            <div><?= (int)$transaction['amountpaid'] - (int)$transaction['value'] ?></div>
                                        </div>
                                    <?php } ?>
                                </div>

                                <hr style="border-top: 3px double #8c8b8b">

                                <div class="uk-margin-remove-top" uk-grid>
                                    <?php if ($transaction['memberid'] !== "0") {
                                        foreach ($customers as $customer) {
                                            if ($customer['id'] === $transaction['memberid']) { ?>
                                                <div class="uk-width-1-2">
                                                    <div><?= lang('Global.customer') ?></div>
                                                </div>
                                                <div class="uk-width-1-2 uk-text-right uk-text-bolder" style="color: #000;">
                                                    <div><?= $customer['name'] ?></div>
                                                </div>
                                    <?php }
                                        }
                                    } ?>
                                </div>

                                <div class="uk-margin-remove-top" uk-grid>
                                    <?php if (($transaction['memberid'] !== "0")) {
                                        if ($gconfig['poinorder'] != "0") {
                                            $pointearn = (floor((int)$transaction['value'] / (int)$gconfig['poinorder'])) * (int)$gconfig['poinvalue'];
                                        } else {
                                            $pointearn = (int)$transaction['value'] * (int)$gconfig['poinvalue'];
                                        } ?>
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

                                <hr style="border-top: 3px double #8c8b8b">

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