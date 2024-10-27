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
                        maxDate: new Date(),
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
                <th class="uk-text-center"><?= lang('Global.detail') ?></th>
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
                    <td class="uk-flex-middle uk-text-center">
                        <a class="uk-icon-link uk-icon" uk-icon="eye" uk-toggle="target:#detail-<?= $dayrep['id'] ?>"></a>
                    </td>
                    <td class=""><?= $dayrep['outlet'] ?></td>
                    <td><?= $dayrep['dateopen'] ?></td>
                    <td><?= $dayrep['dateclose'] ?></td>
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
                    <!-- Report Information -->
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
                                    <?= $dayrep['useropen'] ?>
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
                                    <?= $dayrep['userclose'] ?>
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
                                <?= $dayrep['outlet'] ?>
                            </div>
                        </div>
                    </div>
                    <!-- Report Information End -->

                    <hr>
                    
                    <!-- Product Selling -->
                    <div class="uk-margin">
                        <h5 class="tm-h3 uk-margin-remove"><?= lang('Global.productsales') ?></h5>
                        <h6 class="uk-margin-remove-top uk-text-muted"><?= lang('Global.descproductsales') ?></h6>
                        <div class="uk-margin-small-top">
                            <div class="uk-child-width-1-2 uk-text-bolder" style="color: #000;" uk-grid>
                                <div>
                                    <div class=""><?= lang('Global.totalproductsales') ?></div>
                                </div>
                                <div class="uk-text-right">
                                    <div><?= $dayrep['totalproductsell'] ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="uk-margin-small-top">
                            <a class="uk-button uk-button-default" uk-toggle="target:#productsales-<?= $dayrep['id'] ?>" style="width: 540px; border-radius: 5px;"><?= lang('Global.productsales') ?></a>
                        </div>
                    </div>
                    <!-- Product Selling End -->

                    <hr>
                    
                    <!-- Sales -->
                    <div class="uk-margin">
                        <h5 class="tm-h3 uk-margin-remove"><?= lang('Global.sales') ?></h5>
                        <h6 class="uk-margin-remove-top uk-text-muted"><?= lang('Global.descsales') ?></h6>
                        <?php
                        $totaltrxvalue  = [];
                        foreach ($dayrep['trxpayments'] as $trxpayment) {
                            $paymethodval   = [];
                            foreach ($trxpayment['detail'] as $detail) {
                                $paymethodval[] = $detail['value'];
                            }
                            $totalpaymethodvalue    = array_sum($paymethodval);
                            $totaltrxvalue[]        = $totalpaymethodvalue; ?>
                            <div class="uk-margin-small-top">
                                <div class="uk-child-width-1-2" uk-grid>
                                    <div>
                                        <div class=""><?= lang('Global.totalreceived').' '.$trxpayment['name'] ?></div>
                                    </div>
                                    <div class="uk-text-right">
                                        <div>
                                            <?= 'Rp '.number_format($totalpaymethodvalue,2,',','.'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                        <div class="uk-margin-small-top">
                            <div class="uk-child-width-1-2 uk-text-bolder" style="color: #000;" uk-grid>
                                <div>
                                    <div class=""><?= lang('Global.totalsalestrx') ?></div>
                                </div>
                                <div class="uk-text-right">
                                    <div>
                                        <?php
                                            $totalvalue = array_sum($totaltrxvalue);
                                            echo "Rp ".number_format($totalvalue,2,',','.');
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="uk-margin-small-top">
                            <a class="uk-button uk-button-default" uk-toggle="target:#trxhistory-<?= $dayrep['id'] ?>" style="width: 540px; border-radius: 5px;"><?= lang('Global.trxHistory') ?></a>
                        </div>
                    </div>
                    <!-- Sales End -->

                    <hr>

                    <!-- Cashflow -->
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

                        <?php
                        $totalcashin    = [];
                        $totalcashout   = [];
                        foreach ($dayrep['cashflow'] as $cashflow) {
                            if ($cashflow['type'] == '0') {
                                $totalcashin[]  = $cashflow['qty'];
                            } else {
                                $totalcashout[] = $cashflow['qty'];
                            }
                        }
                        $summarycashin  = array_sum($totalcashin);
                        $summarycashout = array_sum($totalcashout); ?>

                        <div class="uk-margin-small-top">
                            <div class="uk-child-width-1-2" uk-grid>
                                <div>
                                    <div class=""><?= lang('Global.totalcashin') ?></div>
                                </div>
                                <div class="uk-text-right">
                                    <div>Rp <?= number_format($summarycashin,2,',','.') ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="uk-margin-small-top">
                            <div class="uk-child-width-1-2" uk-grid>
                                <div>
                                    <div class=""><?= lang('Global.totalcashout') ?></div>
                                </div>
                                <div class="uk-text-right">
                                    <div>-Rp <?= number_format($summarycashout,2,',','.') ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="uk-margin-small-top">
                            <div class="uk-child-width-1-2 uk-text-bolder" style="color: #000;" uk-grid>
                                <div>
                                    <div class=""><?= lang('Global.totalcash') ?></div>
                                </div>
                                <div class="uk-text-right">
                                    <div>Rp <?= number_format(((Int)$dayrep['initialcash'] + ((Int)$summarycashin - (Int)$summarycashout)),2,',','.') ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="uk-margin-small-top">
                            <a class="uk-button uk-button-default" uk-toggle="target:#cashhistory-<?= $dayrep['id'] ?>" style="width: 540px; border-radius: 5px;"><?= lang('Global.cashhistory') ?></a>
                        </div>
                    </div>
                    <!-- Cashflow End -->

                    <hr>

                    <!-- Debt Installment -->
                    <div class="uk-margin">
                        <h5 class="tm-h3 uk-margin-remove"><?= lang('Global.debtInstallments') ?></h5>
                        <h6 class="uk-margin-remove-top uk-text-muted"><?= lang('Global.descdebtins') ?></h6>
                        <?php
                        $totaldebtins   = [];
                        foreach ($dayrep['debtins'] as $debtins) {
                            $debtinsval = [];
                            foreach ($debtins['detail'] as $debtdetail) {
                                $debtinsval[]       = $debtdetail['value'];
                            }
                            $totaldebtinstallment   = array_sum($debtinsval);
                            $totaldebtins[]         = $totaldebtinstallment; ?>
                            <div class="uk-margin-small-top">
                                <div class="uk-child-width-1-2" uk-grid>
                                    <div>
                                        <div class=""><?= lang('Global.totalreceived').' '.$debtins['name'] ?></div>
                                    </div>
                                    <div class="uk-text-right">
                                        <div>
                                            <?= 'Rp '.number_format($totaldebtinstallment,2,',','.'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                        <div class="uk-margin-small-top">
                            <div class="uk-child-width-1-2 uk-text-bolder" style="color: #000;" uk-grid>
                                <div>
                                    <div class=""><?= lang('Global.totaldebtins') ?></div>
                                </div>
                                <div class="uk-text-right">
                                    <div>
                                        <?php
                                            $totaldebtvalue = array_sum($totaldebtins);
                                            echo "Rp ".number_format($totaldebtvalue,2,',','.');
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="uk-margin-small-top">
                            <a class="uk-button uk-button-default" uk-toggle="target:#debtins-<?= $dayrep['id'] ?>" style="width: 540px; border-radius: 5px;"><?= lang('Global.debtinsHistory') ?></a>
                        </div>
                    </div>
                    <!-- Debt Installment End -->

                    <hr>

                    <!-- Top Up -->
                    <div class="uk-margin">
                        <h5 class="tm-h3 uk-margin-remove"><?= lang('Global.topup') ?></h5>
                        <h6 class="uk-margin-remove-top uk-text-muted"><?= lang('Global.desctopup') ?></h6>
                        <?php
                        $totaltopup   = [];
                        foreach ($dayrep['topup'] as $topup) {
                            $topupval = [];
                            foreach ($topup['detail'] as $topupdetail) {
                                $topupval[]     = $topupdetail['value'];
                            }
                            $totaltopups        = array_sum($topupval);
                            $totaltopup[]       = $totaltopups; ?>
                            <div class="uk-margin-small-top">
                                <div class="uk-child-width-1-2" uk-grid>
                                    <div>
                                        <div class=""><?= lang('Global.totalreceived').' '.$topup['name'] ?></div>
                                    </div>
                                    <div class="uk-text-right">
                                        <div>
                                            <?= 'Rp '.number_format($totaltopups,2,',','.'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                        <div class="uk-margin-small-top">
                            <div class="uk-child-width-1-2 uk-text-bolder" style="color: #000;" uk-grid>
                                <div>
                                    <div class=""><?= lang('Global.totaltopup') ?></div>
                                </div>
                                <div class="uk-text-right">
                                    <div>
                                        <?php
                                            $totaldebtvalue = array_sum($totaltopup);
                                            echo "Rp ".number_format($totaldebtvalue,2,',','.');
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="uk-margin-small-top">
                            <a class="uk-button uk-button-default" uk-toggle="target:#topup-<?= $dayrep['id'] ?>" style="width: 540px; border-radius: 5px;"><?= lang('Global.topupHistory') ?></a>
                        </div>
                    </div>
                    <!-- Top Up End -->

                    <hr>

                    <!-- Withdraw -->
                    <div class="uk-margin">
                        <h5 class="tm-h3 uk-margin-remove"><?= lang('Global.withdraw') ?></h5>
                        <h6 class="uk-margin-remove-top uk-text-muted"><?= lang('Global.descwithdraw') ?></h6>
                        <?php
                        $totalwithdraw   = [];
                        foreach ($dayrep['withdraw'] as $withdraw) {
                            $withdrawval = [];
                            foreach ($withdraw['detail'] as $withdrawdetail) {
                                $withdrawval[]     = $withdrawdetail['value'];
                            }
                            $totalwithdraws        = array_sum($withdrawval);
                            $totalwithdraw[]       = $totalwithdraws; ?>
                            <div class="uk-margin-small-top">
                                <div class="uk-child-width-1-2" uk-grid>
                                    <div>
                                        <div class=""><?= lang('Global.totalreceived').' '.$withdraw['name'] ?></div>
                                    </div>
                                    <div class="uk-text-right">
                                        <div>
                                            <?= 'Rp '.number_format($totalwithdraws,2,',','.'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                        <div class="uk-margin-small-top">
                            <div class="uk-child-width-1-2 uk-text-bolder" style="color: #000;" uk-grid>
                                <div>
                                    <div class=""><?= lang('Global.totalwithdraw') ?></div>
                                </div>
                                <div class="uk-text-right">
                                    <div>
                                        <?php
                                            $totaldebtvalue = array_sum($totalwithdraw);
                                            echo "Rp ".number_format($totaldebtvalue,2,',','.');
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="uk-margin-small-top">
                            <a class="uk-button uk-button-default" uk-toggle="target:#withdraw-<?= $dayrep['id'] ?>" style="width: 540px; border-radius: 5px;"><?= lang('Global.withdrawHistory') ?></a>
                        </div>
                    </div>
                    <!-- Withdraw End -->

                    <hr>

                    <!-- Actual Receipts -->
                    <div class="uk-margin">
                        <h5 class="tm-h3 uk-margin-remove"><?= lang('Global.actualreceipts') ?></h5>
                        <h6 class="uk-margin-remove-top uk-text-muted"><?= lang('Global.descactualreceipt') ?></h6>
                        <div class="uk-margin-small-top">
                            <div class="uk-child-width-1-2" uk-grid>
                                <div>
                                    <div class=""><?= lang('Global.cashreceived') ?></div>
                                </div>
                                <div class="uk-text-right">
                                    <div>Rp <?= number_format($dayrep['cashclose'],2,',','.') ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="uk-margin-small-top">
                            <div class="uk-child-width-1-2" uk-grid>
                                <div>
                                    <div class=""><?= lang('Global.noncashreceived') ?></div>
                                </div>
                                <div class="uk-text-right">
                                    <div>Rp <?= number_format($dayrep['noncashclose'],2,',','.') ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="uk-margin-small-top">
                            <div class="uk-child-width-1-2 uk-text-bolder" style="color: #000;" uk-grid>
                                <div>
                                    <div class=""><?= lang('Global.totalactualrec') ?></div>
                                </div>
                                <div class="uk-text-right">
                                    <div>Rp <?= number_format($dayrep['actualsummary'],2,',','.') ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Actual Receipts End -->

                    <hr>

                    <!-- Summary & Difference -->
                    <div class="uk-margin">
                        <h5 class="tm-h3 uk-margin-remove"><?= lang('Global.summary') ?></h5>
                        <div class="uk-margin-small-top">
                            <div class="uk-child-width-1-2" uk-grid>
                                <div>
                                    <div class="uk-text-bolder" style="color: #000;"><?= lang('Global.reception') ?></div>
                                    <div class="uk-text-muted"><?= lang('Global.descreception') ?></div>
                                </div>
                                <div class="uk-text-right">
                                    <div>Rp <?=number_format((Int)$totalvalue + ((Int)$dayrep['initialcash'] + ((Int)$summarycashin - (Int)$summarycashout)),2,',','.') ?></div>
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
                                        <?= number_format((Int)$dayrep['actualsummary'] - ((Int)$totalvalue + ((Int)$dayrep['initialcash'] + ((Int)$summarycashin - (Int)$summarycashout))),2,',','.') ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Summary & Difference End -->
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
                                    echo "Rp ".number_format($totalvalue,2,',','.');
                                ?>
                            </div>
                        </div>
                    </div>

                    <hr class="uk-margin-small-top uk-margin-small-bottom" style="border-top: 7px solid #e5e5e5">

                    <div class="uk-text-center">
                        <h5 class="uk-text-bolder tm-h5 uk-margin-remove-bottom" style="color: #000;">
                            <?= $dayrep['date'] ?>
                        </h5>
                    </div>

                    <?php foreach ($dayrep['payments'] as $payments) {
                        foreach ($payments['detail'] as $detail) { ?>
                            <hr class="uk-margin-small-top uk-margin-small-bottom" style="border-top: 7px solid #e5e5e5">

                            <div class="uk-child-width-1-2" uk-grid>
                                <div>
                                    <h5 class="uk-margin-remove-bottom">
                                        <?= $detail['time'] ?>
                                    </h5>
                                </div>

                                <div class="uk-text-right">
                                    <div>
                                        <?= $detail['custname'] ?>
                                    </div>
                                </div>
                            </div>

                            <div class="uk-child-width-1-2 uk-flex-middle uk-margin-small-top" uk-grid>
                                <div>
                                    <div><?= $detail['name'] ?></div>
                                </div>
                                <div class="uk-text-right uk-text-bold" style="color: #000;">
                                    Rp <?= number_format($detail['value'],2,',','.') ?>
                                </div>
                            </div>

                            <div class="uk-child-width-1-2 uk-margin-remove-top uk-flex-middle" uk-grid>
                                <div>
                                    <div><?= lang('Global.photo') ?></div>
                                </div>
                                <div class="uk-text-right" uk-lightbox>
                                    <a class="uk-inline" href="/img/tfproof/<?= $detail['proof'];?>">
                                        <img src="/img/tfproof/<?= $detail['proof'];?>" alt="<?= $detail['proof'];?>" style="width: 100px;">
                                    </a>
                                </div>
                            </div>
                        <?php }
                    } ?>
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
                                <?= $dayrep['totalproductsell'] ?>
                            </div>
                        </div>
                    </div>

                    <div style="background-color: #e5e5e5;">
                        <h5>
                            <?= $dayrep['dateopen'] ?>
                        </h5>
                    </div>

                    <?php foreach ($dayrep['productsell'] as $productsell) { ?>
                        <div class="uk-child-width-1-2 uk-margin-small-top" uk-grid>
                            <div>
                                <div class=""><?= $productsell['name'] ?></div>
                            </div>
                            <div class="uk-text-right uk-text-bolder" style="color: #000;">
                                <div><?= array_sum($productsell['qty']) ?> Pcs</div>
                            </div>
                        </div>
                    <?php } ?>
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
                <div class="uk-margin">
                    <div class="uk-child-width-1-2 uk-text-bolder" style="color: #000;" uk-grid>
                        <div>
                            <div><?= lang('Global.close') ?></div>
                        </div>
                        <div>
                            <div class="uk-text-right">Rp <?= number_format((Int)$totalvalue + ((Int)$dayrep['initialcash'] + ((Int)$summarycashin - (Int)$summarycashout)),2,',','.') ?></div>
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
                                <?= $dayrep['userclose'] ?>
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
                
                <?php foreach ($dayrep['cashflow'] as $trxot) { ?>
                    <div class="uk-margin">
                        <div class="uk-child-width-1-2 uk-text-bolder" uk-grid>
                            <div>
                                <div>
                                    <?php if ($trxot['type'] == '0') {
                                        echo "<div class='uk-text-success'>".lang('Global.cashin')."</div>";
                                    } else {
                                        echo "<div class='uk-text-danger'>".lang('Global.cashout')."</div>";
                                    } ?>
                                </div>
                            </div>
                            <div class="uk-text-right">
                                <div>
                                    <?php if ($trxot['type'] == '0') {
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
                                    <?= $trxot['cashier'] ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="uk-child-width-1-2 uk-margin-remove-top uk-flex-middle" uk-grid>
                            <div>
                                <div><?= lang('Global.photo') ?></div>
                            </div>
                            <div class="uk-text-right" uk-lightbox>
                                <a class="uk-inline" href="/img/tfproof/<?= $trxot['proof'];?>">
                                    <img src="/img/tfproof/<?= $trxot['proof'];?>" alt="<?= $trxot['proof'];?>" style="width: 100px;">
                                </a>
                            </div>
                        </div>

                        <hr class="uk-margin-small-top uk-margin-small-bottom">

                        <div class="uk-margin-remove-top">
                            <div><?= lang('Global.note') ?> :</div>
                            <div><?= $trxot['desc'] ?></div>
                        </div>

                        <hr class="uk-margin-small-top uk-margin-small-bottom" style="border-top: 7px solid #e5e5e5">
                    </div>
                <?php } ?>

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
                                <?= $dayrep['useropen'] ?>
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
<?= $this->endSection() ?>