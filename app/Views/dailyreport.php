<?= $this->extend('layout') ?>
<?= $this->section('extraScript') ?>
    <link rel="stylesheet" href="css/code.jquery.com_ui_1.13.2_themes_base_jquery-ui.css">
    <script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
    <script src="js/code.jquery.com_jquery-3.6.0.js"></script>
    <script src="js/code.jquery.com_ui_1.13.2_jquery-ui.js"></script>
    <script src="js/jquery.validate.min.js"></script>
    <script type="text/javascript" src="js/moment.min.js"></script>
    <script type="text/javascript" src="js/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/daterangepicker.css" />
<?= $this->endSection() ?>
<?= $this->section('main') ?>

<!-- Page Heading -->
<div class="tm-card-header uk-light">
    <div uk-grid class="uk-flex-middle">
        <div class="uk-width-1-2@m">
            <h3 class="tm-h3"><?=lang('Global.dailyreportList')?></h3>
        </div>

        <!-- Button Daterange -->
        <div class="uk-width-1-2@m uk-text-right@m">
            <form id="short" action="dayrep" method="get">
                <div class="uk-inline">
                    <span class="uk-form-icon uk-form-icon-flip" uk-icon="calendar"></span>
                    <input class="uk-input uk-width-medium uk-border-rounded" type="text" id="daterange" name="daterange" value="<?=date('m/d/Y', $startdate)?> - <?=date('m/d/Y', $enddate)?>" />
                </div>
            </form>
            <script>
                $(function() {
                    $('input[name="daterange"]').daterangepicker({
                        opens: 'right'
                    }, function(start, end, label) {
                        document.getElementById('daterange').value = start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD');
                        document.getElementById('short').submit();
                    });
                });
            </script>
        </div>
        <!-- End Of Button Daterange-->
    </div>
</div>
<!-- End Of Page Heading -->

<!-- Table Of Content -->
<div class="uk-overflow-auto uk-margin">
    <table class="uk-table uk-table-justify uk-table-middle uk-table-divider uk-light">
        <thead>
            <tr>
                <th class="uk-text-center"></th>
                <th class=""><?= lang('Global.outlet') ?></th>
                <th class=""><?= lang('Global.dateopen') ?></th>
                <th class=""><?= lang('Global.dateclose') ?></th>
                <th class=""><?= lang('Global.totalcashin') ?></th>
                <th class=""><?= lang('Global.totalcashout') ?></th>
                <th class=""><?= lang('Global.totalcashclose') ?></th>
                <th class=""><?= lang('Global.totalnoncashclose') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($dailyreports as $dayrep) { ?>
                <tr>
                    <td class="uk-flex uk-flex-center">
                        <a class="uk-icon-link uk-icon" uk-icon="eye" uk-toggle="target:#detail-<?= $dayrep['id'] ?>"></a>
                    </td>

                    <td class="">
                        <?php foreach ($outlets as $outlet) {
                            if ($outlet['id'] === $dayrep['outletid']) { ?>
                                <?= $outlet['name'] ?>
                            <?php }
                        } ?>
                    </td>

                    <td><?= date('l, d M Y, H:i:s', strtotime($dayrep['dateopen'])); ?></td>
                    <td><?= date('l, d M Y, H:i:s', strtotime($dayrep['dateclose'])); ?></td>
                    <td>Rp <?= number_format($dayrep['totalcashin'],2,',','.');?></td>
                    <td>Rp <?= number_format($dayrep['totalcashout'],2,',','.');?></td>
                    <td>Rp <?= number_format((Int)$dayrep['cashclose'],2,',','.');?></td>
                    <td>Rp <?= number_format((Int)$dayrep['noncashclose'],2,',','.');?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <div>
        <?= $pager->links('dailyreport', 'front_full') ?>
    </div>
</div>
<!-- Table Of Content End -->

<?php foreach ($dailyreports as $dayrep) { ?>
    <!-- Modal Detail -->
    <div uk-modal class="uk-flex-top" id="detail-<?= $dayrep['id'] ?>">
        <div class="uk-modal-dialog uk-margin-auto-vertical">
            <div class="uk-modal-content">
                <div class="uk-modal-header">
                    <div class="uk-child-width-1-2" uk-grid>
                        <div>
                            <h5 class="uk-modal-title"><?=lang('Global.dayrepdetail')?></h5>
                        </div>
                        <div class="uk-text-right">
                            <button class="uk-modal-close uk-icon-button-delete" uk-icon="icon: close;" type="button"></button>
                        </div>
                    </div>
                </div>
                <div class="uk-modal-body" uk-overflow-auto>
                    <div class="uk-child-width-1-2" uk-grid>
                        <div>
                            <div class=""><?= lang('Global.open') ?></div>
                        </div>
                        <div class="uk-text-right uk-margin-remove-left uk-padding-remove uk-child-width-1-1" uk-grid>
                            <div>
                                <div><?= $dayrep['dateopen'] ?></div>
                            </div>
                            <div class="uk-margin-remove">
                                <div class="uk-text-muted">
                                    <?php foreach ($users as $user) {
                                        if ($user->id === $dayrep['useridopen']) {
                                            echo $fullname;
                                        }
                                    } ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="uk-child-width-1-2 uk-margin-small-top" uk-grid>
                        <div>
                            <div class=""><?= lang('Global.close') ?></div>
                        </div>
                        <div class="uk-text-right uk-margin-remove-left uk-padding-remove uk-child-width-1-1" uk-grid>
                            <div>
                                <div><?= $dayrep['dateclose'] ?></div>
                            </div>
                            <div class="uk-margin-remove">
                                <div class="uk-text-muted">
                                    <?php foreach ($users as $user) {
                                        if ($user->id === $dayrep['useridclose']) {
                                            echo $fullname;
                                        }
                                    } ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="uk-child-width-1-2 uk-margin-small-top" uk-grid>
                        <div>
                            <div class=""><?= lang('Global.outlet') ?></div>
                        </div>
                        <div class="uk-text-right">
                            <div>
                                <?php foreach ($outlets as $outlet) {
                                    if ($outlet['id'] === $dayrep['outletid']) {
                                        echo $outlet['name'];
                                    }
                                } ?>
                            </div>
                        </div>
                    </div>

                    <hr>
                    
                    <div class="uk-margin">
                        <h5 class="tm-h3 uk-margin-remove"><?= lang('Global.productsales') ?></h5>
                        <h6 class="uk-margin-remove-top uk-text-muted"><?= lang('Global.descproductsales') ?></h6>
                        <div class="uk-margin-small-top">
                            <div class="uk-child-width-1-2 uk-text-bolder" style="color: #000;" uk-grid>
                                <div>
                                    <div class=""><?= lang('Global.totalproductsales') ?></div>
                                </div>
                                <div class="uk-text-right">
                                    <div>
                                        <?php
                                            $totalproduct   = array();
                                            foreach ($transactions as $trx) {
                                                if ($trx['date'] > $dayrep['dateopen'] && $trx['date'] < $dayrep['dateclose']) {
                                                    foreach ($trxdetails as $trxdet) {
                                                        if ($trxdet['transactionid'] === $trx['id']) {
                                                            $totalproduct[] = $trxdet['qty'];
                                                        }
                                                    }
                                                }
                                            }
                                            $sumtotalproduct = array_sum($totalproduct);
                                            echo $sumtotalproduct;
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="uk-margin-small-top">
                            <a class="uk-button uk-button-default" uk-toggle="target:#productsales-<?= $dayrep['id'] ?>" style="width: 540px; border-radius: 5px;"><?= lang('Global.productsales') ?></a>
                        </div>
                    </div>

                    <hr>
                    
                    <div class="uk-margin">
                        <h5 class="tm-h3 uk-margin-remove"><?= lang('Global.sales') ?></h5>
                        <h6 class="uk-margin-remove-top uk-text-muted"><?= lang('Global.descsales') ?></h6>
                        <div class="uk-margin-small-top">
                            <div class="uk-child-width-1-2" uk-grid>
                                <div>
                                    <div class=""><?= lang('Global.cashreceived') ?></div>
                                </div>
                                <div class="uk-text-right">
                                    <div>
                                        <?php
                                            $trxcash = array();
                                            foreach ($transactions as $transaction) {
                                                foreach ($trxpayments as $trxpayment) {
                                                    foreach ($payments as $payment) {
                                                        if (($payment['name'] === 'Cash') && ($payment['outletid'] === $outletPick)) {
                                                            if (($transaction['date'] >= $dayrep['dateopen']) && ($transaction['date'] <= $dayrep['dateclose']) && ($trxpayment['transactionid'] === $transaction['id']) && ($trxpayment['paymentid'] === $payment['id'])) {
                                                                $trxcash[] = $trxpayment['value'];
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                            $cashpay = array_sum($trxcash);
                                            echo "Rp ".number_format($cashpay,2,',','.');
                                            ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="uk-margin-small-top">
                            <div class="uk-child-width-1-2" uk-grid>
                                <div>
                                    <div class=""><?= lang('Global.noncashreceived') ?></div>
                                </div>
                                <div class="uk-text-right">
                                    <div>
                                        <?php
                                            $trxnoncash = array();
                                            foreach ($transactions as $transaction) {
                                                foreach ($trxpayments as $trxpayment) {
                                                    foreach ($payments as $payment) {
                                                        if (($payment['name'] != 'Cash') && (($payment['outletid'] === $outletPick) || ($payment['outletid'] === '0'))) {
                                                            if (($transaction['date'] >= $dayrep['dateopen']) && ($transaction['date'] <= $dayrep['dateclose']) && ($transaction['outletid'] === $outletPick) && ($trxpayment['transactionid'] === $transaction['id']) && ($trxpayment['paymentid'] === $payment['id'])) {
                                                                $trxnoncash[] = $trxpayment['value'];
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                            $noncashpay = array_sum($trxnoncash);
                                            echo "Rp ".number_format($noncashpay,2,',','.');
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="uk-margin-small-top">
                            <div class="uk-child-width-1-2 uk-text-bolder" style="color: #000;" uk-grid>
                                <div>
                                    <div class=""><?= lang('Global.totalsalestrx') ?></div>
                                </div>
                                <div class="uk-text-right">
                                    <div>
                                        <?php
                                            $totalcashnoncash = $noncashpay + $cashpay;
                                            echo "Rp ".number_format($totalcashnoncash,2,',','.');
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="uk-margin-small-top">
                            <a class="uk-button uk-button-default" uk-toggle="target:#trxhistory-<?= $dayrep['id'] ?>" style="width: 540px; border-radius: 5px;"><?= lang('Global.trxHistory') ?></a>
                        </div>
                    </div>

                    <hr>

                    <div class="uk-margin">
                        <h5 class="tm-h3 uk-margin-remove"><?= lang('Global.cashflow') ?></h5>
                        <h6 class="uk-margin-remove-top uk-text-muted"><?= lang('Global.desccashflow') ?></h6>
                        <div class="uk-margin-small-top">
                            <div class="uk-child-width-1-2" uk-grid>
                                <div>
                                    <div class=""><?= lang('Global.initialcash') ?></div>
                                </div>
                                <div class="uk-text-right">
                                    <div>Rp <?= number_format($dayrep['initialcash'],2,',','.') ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="uk-margin-small-top">
                            <div class="uk-child-width-1-2" uk-grid>
                                <div>
                                    <div class=""><?= lang('Global.totalcashin') ?></div>
                                </div>
                                <div class="uk-text-right">
                                    <div>Rp <?= number_format($dayrep['totalcashin'],2,',','.') ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="uk-margin-small-top">
                            <div class="uk-child-width-1-2" uk-grid>
                                <div>
                                    <div class=""><?= lang('Global.totalcashout') ?></div>
                                </div>
                                <div class="uk-text-right">
                                    <div>-Rp <?= number_format($dayrep['totalcashout'],2,',','.') ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="uk-margin-small-top">
                            <div class="uk-child-width-1-2 uk-text-bolder" style="color: #000;" uk-grid>
                                <div>
                                    <div class=""><?= lang('Global.totalcash') ?></div>
                                </div>
                                <div class="uk-text-right">
                                    <div>Rp <?= number_format(($dayrep['initialcash'] + $dayrep['totalcashin']) - $dayrep['totalcashout'],2,',','.') ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="uk-margin-small-top">
                            <a class="uk-button uk-button-default" uk-toggle="target:#cashhistory-<?= $dayrep['id'] ?>" style="width: 540px; border-radius: 5px;"><?= lang('Global.cashhistory') ?></a>
                        </div>
                    </div>

                    <hr>

                    <div class="uk-margin">
                        <h5 class="tm-h3 uk-margin-remove"><?= lang('Global.actualreceipts') ?></h5>
                        <h6 class="uk-margin-remove-top uk-text-muted"><?= lang('Global.descactualreceipt') ?></h6>
                        <div class="uk-margin-small-top">
                            <div class="uk-child-width-1-2" uk-grid>
                                <div>
                                    <div class=""><?= lang('Global.cashreceived') ?></div>
                                </div>
                                <div class="uk-text-right">
                                    <div>Rp <?= number_format((Int)$dayrep['cashclose'],2,',','.') ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="uk-margin-small-top">
                            <div class="uk-child-width-1-2" uk-grid>
                                <div>
                                    <div class=""><?= lang('Global.noncashreceived') ?></div>
                                </div>
                                <div class="uk-text-right">
                                    <div>Rp <?= number_format((Int)$dayrep['noncashclose'],2,',','.') ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="uk-margin-small-top">
                            <div class="uk-child-width-1-2 uk-text-bolder" style="color: #000;" uk-grid>
                                <div>
                                    <div class=""><?= lang('Global.totalactualrec') ?></div>
                                </div>
                                <div class="uk-text-right">
                                    <div>Rp <?= number_format(((Int)$dayrep['cashclose'] + (Int)$dayrep['noncashclose']),2,',','.') ?></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="uk-margin">
                        <h5 class="tm-h3 uk-margin-remove"><?= lang('Global.summary') ?></h5>
                        <div class="uk-margin-small-top">
                            <div class="uk-child-width-1-2" uk-grid>
                                <div>
                                    <div class="uk-text-bolder" style="color: #000;"><?= lang('Global.reception') ?></div>
                                    <div class="uk-text-muted"><?= lang('Global.descreception') ?></div>
                                </div>
                                <div class="uk-text-right">
                                    <div>
                                        <?php
                                            $totaltrxsum    = ($totalcashnoncash + ($dayrep['initialcash'] + $dayrep['totalcashin']) - $dayrep['totalcashout']);
                                            echo "Rp ".number_format($totaltrxsum,2,',','.');
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="uk-margin-small-top">
                            <div class="uk-child-width-1-2" uk-grid>
                                <div>
                                    <div class="uk-text-bolder" style="color: #000;"><?= lang('Global.totaldifference') ?></div>
                                    <div class="uk-text-muted"><?= lang('Global.descdifference') ?></div>
                                </div>
                                <div class="uk-text-right uk-text-bolder" style="color: #000;">
                                    <div>
                                        <?php
                                            $actualrec      = ((Int)$dayrep['cashclose'] + (Int)$dayrep['noncashclose']);
                                            $diff           = $actualrec - $totaltrxsum;
                                            echo "Rp ".number_format($diff,2,',','.');
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Detail End -->

    <!-- Modal Transaction History -->
    <div uk-modal class="uk-flex-top" id="trxhistory-<?= $dayrep['id'] ?>">
        <div class="uk-modal-dialog uk-margin-auto-vertical">
            <div class="uk-modal-header">
                <div class="uk-flex uk-flex-middle uk-child-width-auto" uk-grid>
                    <div class="uk-padding-remove uk-margin-medium-left">
                        <a uk-icon="arrow-left" uk-toggle="#detail-<?= $dayrep['id'] ?>" width="35" height="35"></a>
                    </div>
                    <div>
                        <h5 class="uk-modal-title"><?=lang('Global.trxHistory')?></h5>
                    </div>
                </div>
            </div>
            <div class="uk-modal-body" uk-overflow-auto>
                <div class="uk-margin">
                    <div class="uk-child-width-1-2 uk-text-bolder uk-margin-small-bottom" style="color: #000;" uk-grid>
                        <div>
                            <div class=""><?= lang('Global.totalsalestrx') ?></div>
                        </div>
                        <div>
                            <div class="uk-text-right">
                                <?php
                                    echo "Rp ".number_format($totalcashnoncash,2,',','.');
                                ?>
                            </div>
                        </div>
                    </div>

                    <hr class="uk-margin-small-top uk-margin-small-bottom" style="border-top: 7px solid #e5e5e5">

                    <div class="uk-text-center">
                        <h5 class="uk-text-bolder tm-h5 uk-margin-remove-bottom" style="color: #000;">
                            <?php
                                echo date('l, d M Y', strtotime($dayrep['dateopen']));
                            ?>
                        </h5>
                    </div>

                    <?php
                        foreach ($transactions as $transaction) {
                            if (($transaction['date'] >= $dayrep['dateopen']) && ($transaction['date'] <= $dayrep['dateclose']) && ($transaction['outletid'] === $outletPick)) {
                                $trxvalue = array();
                                foreach ($trxpayments as $trxpayment) {
                                    if (($trxpayment['transactionid'] === $transaction['id']) && ($trxpayment['paymentid'] != '0')) {
                                        $trxvalue[] = $trxpayment['value'];
                                    }
                                }
                                if ($transaction['paymentid'] != '0') {
                                    foreach ($payments as $payment) {
                                        if ($transaction['paymentid'] === $payment['id']) {
                                            $paymentmethod = $payment['name'];
                                        }
                                    }
                                } else {
                                    $paymentmethod = lang('Global.splitbill');
                                }
                                $value = array_sum($trxvalue); ?>
                                
                                <hr class="uk-margin-small-top uk-margin-small-bottom" style="border-top: 7px solid #e5e5e5">

                                <div class="uk-child-width-1-2" uk-grid>
                                    <div>
                                        <h5 class="uk-margin-remove-bottom">
                                            <?php
                                                echo date('H:i:s', strtotime($transaction['date']))
                                            ?>
                                        </h5>
                                    </div>

                                    <div class="uk-text-right">
                                        <div>
                                            <?php
                                                if ($transaction['memberid'] === '0') {
                                                    $member = 'Non Member';
                                                } else {
                                                    foreach ($customers as $cust) {
                                                        if ($transaction['memberid'] === $cust['id']) {
                                                            $member = $cust['name'];
                                                        }
                                                    }
                                                }
                                                echo $member;
                                            ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="uk-child-width-1-2 uk-flex-middle uk-margin-small-top" uk-grid>
                                    <div>
                                        <div><?= $paymentmethod ?></div>
                                    </div>
                                    <div class="uk-text-right uk-text-bold" style="color: #000;">
                                        Rp <?= number_format($value,2,',','.') ?>
                                    </div>
                                </div>

                                <div class="uk-child-width-1-2 uk-margin-remove-top uk-flex-middle" uk-grid>
                                    <div>
                                        <div><?= lang('Global.photo') ?></div>
                                    </div>
                                    <div class="uk-text-right" uk-lightbox>
                                        <a class="uk-inline" href="/img/tfproof/<?= $transaction['photo'];?>">
                                            <img src="/img/tfproof/<?= $transaction['photo'];?>" alt="<?= $transaction['photo'];?>" style="width: 100px;">
                                        </a>
                                    </div>
                                </div>
                            <?php }
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Transaction History End -->

    <!-- Modal Product Sales -->
    <div uk-modal class="uk-flex-top" id="productsales-<?= $dayrep['id'] ?>">
        <div class="uk-modal-dialog uk-margin-auto-vertical">
            <div class="uk-modal-header">
                <div class="uk-flex uk-flex-middle uk-child-width-auto" uk-grid>
                    <div class="uk-padding-remove uk-margin-medium-left">
                        <a uk-icon="arrow-left" uk-toggle="#detail-<?= $dayrep['id'] ?>" width="35" height="35"></a>
                    </div>
                    <div>
                        <h5 class="uk-modal-title"><?=lang('Global.productsales')?></h5>
                    </div>
                </div>
            </div>
            <div class="uk-modal-body" uk-overflow-auto>
                <div class="uk-margin">
                    <div class="uk-child-width-1-2 uk-text-bolder uk-margin-small-bottom" style="color: #000;" uk-grid>
                        <div>
                            <div><?= lang('Global.total') ?></div>
                        </div>
                        <div>
                            <div class="uk-text-right">
                                <?php
                                    echo $sumtotalproduct;
                                ?>
                            </div>
                        </div>
                    </div>

                    <div style="background-color: #e5e5e5;">
                        <h5>
                            <?php
                                echo date('l, d M Y', strtotime($dayrep['dateopen']));
                            ?>
                        </h5>
                    </div>

                    <?php
                        $variantsale = array();
                        foreach ($variants as $variant) {
                            foreach ($products as $product) {
                                if ($variant['productid'] === $product['id']) {
                                    $name = $product['name'].' - '.$variant['name'];
                                }
                            }

                            $varqty = array();
                            foreach ($transactions as $transaction) {
                                if (($transaction['date'] > $dayrep['dateopen']) && ($transaction['date'] < $dayrep['dateclose'])) {
                                    foreach ($trxdetails as $trxdet) {
                                        if (($trxdet['variantid'] === $variant['id']) && ($trxdet['transactionid'] === $transaction['id'])) {
                                            $varqty[] = $trxdet['qty'];
                                        }
                                    }
                                }
                            }

                            $variantsale[] = [
                                'id'    => $variant['id'],
                                'name'  => $name,
                                'qty'   => array_sum($varqty)
                            ];
                        }

                        foreach ($bundles as $bundle) {
                            $name = $bundle['name'];

                            $varqty = array();
                            foreach ($transactions as $transaction) {
                                if (($transaction['date'] > $dayrep['dateopen']) && ($transaction['date'] < $dayrep['dateclose'])) {
                                    foreach ($trxdetails as $trxdet) {
                                        if (($trxdet['bundleid'] === $bundle['id']) && ($trxdet['transactionid'] === $transaction['id'])) {
                                            $varqty[] = $trxdet['qty'];
                                        }
                                    }
                                }
                            }

                            $variantsale[] = [
                                'id'    => $bundle['id'],
                                'name'  => $name,
                                'qty'   => array_sum($varqty)
                            ];
                        }

                        foreach ($variantsale as $sale) {
                            if ($sale['qty'] != 0) { ?>
                                <div class="uk-child-width-1-2 uk-margin-small-top" uk-grid>
                                    <div>
                                        <div class=""><?= $sale['name'] ?></div>
                                    </div>
                                    <div class="uk-text-right uk-text-bolder" style="color: #000;">
                                        <div><?= $sale['qty'] ?> Pcs</div>
                                    </div>
                                </div>
                            <?php }
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Product Sales End -->

    <!-- Modal Cash History -->
    <div uk-modal class="uk-flex-top" id="cashhistory-<?= $dayrep['id'] ?>">
        <div class="uk-modal-dialog uk-margin-auto-vertical">
            <div class="uk-modal-header">
                <div class="uk-flex uk-flex-middle uk-child-width-auto" uk-grid>
                    <div class="uk-padding-remove uk-margin-medium-left">
                        <a uk-icon="arrow-left" uk-toggle="#detail-<?= $dayrep['id'] ?>" width="35" height="35"></a>
                    </div>
                    <div>
                        <h5 class="uk-modal-title"><?=lang('Global.cashhistory')?></h5>
                    </div>
                </div>
            </div>
            <div class="uk-modal-body" uk-overflow-auto>
                <?php
                    $totalcash = array();
                    $totalnoncash = array();
                    foreach ($noncashtrx as $noncashtr) {
                        if (($noncashtr['date'] > $dayrep['dateopen']) && ($noncashtr['date'] < $dayrep['dateclose']) && ($dayrep['outletid'] === $noncashtr['outletid'])) {
                            $noncashall = $noncashtr['value'];
                            $totalnoncash[] = $noncashall;
                        }
                    }
                    foreach ($cashtrx as $cashtr) {
                        if (($cashtr['date'] > $dayrep['dateopen']) && ($cashtr['date'] < $dayrep['dateclose']) && ($dayrep['outletid'] === $cashtr['outletid'])) {
                            $cashall = $cashtr['value'];
                            $totalcash[] = $cashall;
                        }
                    }
                    $sumnoncash = array_sum($totalnoncash);
                    $cashsum = array_sum($totalcash);
                ?>
                <div class="uk-margin">
                    <div class="uk-child-width-1-2 uk-text-bolder" style="color: #000;" uk-grid>
                        <div>
                            <div><?= lang('Global.close') ?></div>
                        </div>
                        <div>
                            <div class="uk-text-right">Rp <?= number_format((($sumnoncash + $cashsum) + (($dayrep['initialcash'] + $dayrep['totalcashin']) - $dayrep['totalcashout'])),2,',','.') ?></div>
                        </div>
                    </div>

                    <hr class="uk-margin-small-top uk-margin-small-bottom">

                    <div class="uk-text-muted"><?= lang('Global.information') ?> :</div>

                    <div class="uk-child-width-1-2" uk-grid>
                        <div>
                            <div><?= lang('Global.time') ?></div>
                        </div>
                        <div class="uk-text-right">
                            <div><?= $dayrep['dateclose'] ?></div>
                        </div>
                    </div>
                    
                    <div class="uk-child-width-1-2 uk-margin-remove-top" uk-grid>
                        <div>
                            <div><?= lang('Global.employee') ?></div>
                        </div>
                        <div class="uk-text-right">
                            <div>
                                <?php foreach ($users as $user) {
                                    if ($user->id === $dayrep['useridclose']) {
                                        echo $fullname;
                                    }
                                } ?>
                            </div>
                        </div>
                    </div>

                    <hr class="uk-margin-small-top uk-margin-small-bottom">

                    <div class="uk-margin-remove-top">
                        <div><?= lang('Global.note') ?> :</div>
                        <div><?= lang('Global.closecash') ?></div>
                    </div>

                    <hr class="uk-margin-small-top uk-margin-small-bottom" style="border-top: 7px solid #e5e5e5">
                </div>
                
                <?php foreach ($trxothers as $trxot) {
                    if (($trxot['date'] > $dayrep['dateopen']) && ($trxot['date'] < $dayrep['dateclose']) && ($dayrep['outletid'] === $trxot['outletid'])) { ?>
                        <div class="uk-margin">
                            <div class="uk-child-width-1-2 uk-text-bolder" uk-grid>
                                <div>
                                    <div>
                                        <?php if ($trxot['type'] === "0") {
                                            echo "<div class='uk-text-success'>".lang('Global.cashin')."</div>";
                                        } else {
                                            echo "<div class='uk-text-danger'>".lang('Global.cashout')."</div>";
                                        } ?>
                                    </div>
                                </div>
                                <div class="uk-text-right">
                                    <div>
                                        <?php if ($trxot['type'] === "0") {
                                            echo "<div class='uk-text-success'>"."+Rp ".number_format($trxot['qty'],2,',','.')."</div>";
                                        } else {
                                            echo "<div class='uk-text-danger'>"."-Rp ".number_format($trxot['qty'],2,',','.')."</div>";
                                        } ?>
                                    </div>
                                </div>
                            </div>

                            <hr class="uk-margin-small-top uk-margin-small-bottom">

                            <div class="uk-text-muted"><?= lang('Global.information') ?> :</div>

                            <div class="uk-child-width-1-2" uk-grid>
                                <div>
                                    <div><?= lang('Global.time') ?></div>
                                </div>
                                <div class="uk-text-right">
                                    <div><?= $trxot['date'] ?></div>
                                </div>
                            </div>
                            
                            <div class="uk-child-width-1-2 uk-margin-remove-top" uk-grid>
                                <div>
                                    <div><?= lang('Global.employee') ?></div>
                                </div>
                                <div class="uk-text-right">
                                    <div>
                                        <?php foreach ($users as $user) {
                                            if ($user->id === $trxot['userid']) {
                                                echo $fullname;
                                            }
                                        } ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="uk-child-width-1-2 uk-margin-remove-top uk-flex-middle" uk-grid>
                                <div>
                                    <div><?= lang('Global.photo') ?></div>
                                </div>
                                <div class="uk-text-right" uk-lightbox>
                                    <a class="uk-inline" href="/img/tfproof/<?= $trxot['photo'];?>">
                                        <img src="/img/tfproof/<?= $trxot['photo'];?>" alt="<?= $trxot['photo'];?>" style="width: 100px;">
                                    </a>
                                </div>
                            </div>

                            <hr class="uk-margin-small-top uk-margin-small-bottom">

                            <div class="uk-margin-remove-top">
                                <div><?= lang('Global.note') ?> :</div>
                                <div><?= $trxot['description'] ?></div>
                            </div>

                            <hr class="uk-margin-small-top uk-margin-small-bottom" style="border-top: 7px solid #e5e5e5">
                        </div>
                    <?php }
                } ?>

                <div class="uk-margin">
                    <div class="uk-child-width-1-2 uk-text-bolder uk-text-success" uk-grid>
                        <div>
                            <div><?= lang('Global.initialcash') ?></div>
                        </div>
                        <div>
                            <div class="uk-text-right">+Rp <?= number_format($dayrep['initialcash'],2,',','.') ?></div>
                        </div>
                    </div>

                    <hr class="uk-margin-small-top uk-margin-small-bottom">

                    <div class="uk-text-muted"><?= lang('Global.information') ?> :</div>

                    <div class="uk-child-width-1-2" uk-grid>
                        <div>
                            <div><?= lang('Global.time') ?></div>
                        </div>
                        <div class="uk-text-right">
                            <div><?= $dayrep['dateopen'] ?></div>
                        </div>
                    </div>
                    
                    <div class="uk-child-width-1-2 uk-margin-remove-top" uk-grid>
                        <div>
                            <div><?= lang('Global.employee') ?></div>
                        </div>
                        <div class="uk-text-right">
                            <div>
                                <?php foreach ($users as $user) {
                                    if ($user->id === $dayrep['useridopen']) {
                                        echo $fullname;
                                    }
                                } ?>
                            </div>
                        </div>
                    </div>

                    <hr class="uk-margin-small-top uk-margin-small-bottom">

                    <div class="uk-margin-remove-top">
                        <div><?= lang('Global.note') ?> :</div>
                        <div><?= lang('Global.initialcash') ?></div>
                    </div>

                    <hr class="uk-divider-icon uk-margin-remove-top uk-margin-small-bottom">
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Cash History End -->
<?php } ?>
<!-- Modal Detail End -->
<?= $this->endSection() ?>