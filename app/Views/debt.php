<?= $this->extend('layout') ?>
<?= $this->section('extraScript') ?>
<link rel="stylesheet" href="css/code.jquery.com_ui_1.13.2_themes_base_jquery-ui.css">
<script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
<script src="js/code.jquery.com_jquery-3.6.0.js"></script>
<script src="js/code.jquery.com_ui_1.13.2_jquery-ui.js"></script>
<script src="js/jquery.validate.min.js"></script>
<script src="js/cdnjs.cloudflare.com_ajax_libs_webcamjs_1.0.25_webcam.min.js"></script>
<script type="text/javascript" src="js/moment.min.js"></script>
<script type="text/javascript" src="js/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="css/daterangepicker.css" />
<?= $this->endSection() ?>
<?= $this->section('main') ?>


<!-- Page Heading -->
<div class="tm-card-header uk-light">
    <div uk-grid class="uk-flex-middle">
        <div class="uk-width-1-2@m">
            <h3 class="tm-h3"><?= lang('Global.debt') ?></h3>
        </div>

        <!-- Date Range -->
        <!-- <div class="uk-width-1-2@m uk-text-right@m">
            <form id="short" action="debt" method="get">
                <div class="uk-inline">
                    <span class="uk-form-icon uk-form-icon-flip" uk-icon="calendar"></span>
                    <input class="uk-input uk-width-medium uk-border-rounded" type="text" id="daterange" name="daterange" value="</?= date('m/d/Y', $startdate) ?> - </?= date('m/d/Y', $enddate) ?>" />
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
        </div> -->
        <!-- End Of Date Range-->

    </div>
</div>
<!-- End Of Page Heading -->

<!-- Table Of Content -->
<div class="uk-overflow-auto uk-margin">
    <!-- Total Debt List -->
    <div class="uk-container uk-container-large uk-light">
        <div class="uk-form-horizontal">
            <div class="uk-form-label"><?= lang('Global.total') ?> <?= lang('Global.debt') ?> : Rp <?= number_format($totaldebt, 2, ',', '.') ?></div>
        </div>
    </div>
    <!-- Total Debt List End -->
    <table class="uk-table uk-table-justify uk-table-middle uk-table-divider uk-light">
        <thead>
            <tr>
                <th class="uk-text-center"><?= lang('Global.detail') ?></th>
                <th class=""><?= lang('Global.date') ?></th>
                <th class=""><?= lang('Global.outlet') ?></th>
                <th class=""><?= lang('Global.customer') ?></th>
                <th class=""><?= lang('Global.duedate') ?></th>
                <th class=""><?= lang('Global.total') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($debts as $debt) { ?>
                <tr>
                    <td class="uk-flex-middle uk-text-center">
                        <a class="uk-icon-link uk-icon" uk-icon="credit-card" uk-toggle="target:#pay-<?= $debt['id'] ?>"></a>
                    </td>
                    <td class=""><?= date('l, d M Y', strtotime($debt['trxdate'])); ?></td>
                    <td class=""><?= $debt['outlet'] ?></td>
                    <td class=""><?= $debt['name'] ?></td>
                    <td class=""><?= date('l, d M Y', strtotime($debt['deadline'])); ?></td>
                    <td class="">Rp <?= number_format($debt['value'], 2, ',', '.') ?></td>
                    <!-- <td class="uk-flex-middle uk-text-center">
                        <a class="uk-icon-link uk-icon" uk-icon="credit-card" uk-toggle="target:#pay-</?= $debt['id'] ?>"></a>
                    </td>

                    </?php foreach ($transactions as $trx) {
                        if ($trx['id'] === $debt['transactionid']) { ?>
                            <td class=""></?= date('l, d M Y', strtotime($trx['date'])); ?></td>

                            </?php foreach ($outlets as $outlet) {
                                if ($outlet['id'] === $trx['outletid']) { ?>
                                    <td class=""></?= $outlet['name'] ?></td>
                            </?php }
                            } ?>
                    </?php }
                    } ?>

                    </?php foreach ($customers as $cust) {
                        if ($cust['id'] === $debt['memberid']) { ?>
                            <td class=""></?= $cust['name'] . ' / ' . $cust['phone'] ?></td>
                    </?php }
                    } ?>

                    <td class=""></?= date('l, d M Y', strtotime($debt['deadline'])); ?></td>

                    <td class="">Rp </?= number_format($debt['value'], 2, ',', '.') ?></td> -->
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <div>
        <!-- </?= $pager->links('debt', 'front_full') ?> -->
        <?= $pager_links ?>
    </div>
</div>
<!-- Table Of Content End -->

<!-- Modal Pay Debt -->
<?php foreach ($debts as $debt) { ?>
    <div uk-modal class="uk-flex-top" id="pay-<?= $debt['id'] ?>">
        <div class="uk-modal-dialog uk-margin-auto-vertical">
            <div class="uk-modal-content">
                <div class="uk-modal-header" style="background-color: #e3e3e3;">
                    <div class="uk-child-width-1-1" uk-grid>
                        <div class="uk-text-right">
                            <button class="uk-modal-close uk-icon-button-delete" uk-icon="icon: close;" type="button"></button>
                        </div>
                        <div class="uk-text-center">
                            <h3 class="tm-h2"><?= lang('Global.paybill') ?></h5>
                        </div>
                        <div class="uk-margin-remove-top uk-text-center">
                            <h5 class="uk-modal-title" style="color: #000;">Rp <?= number_format($debt['value'], 2, ',', '.') ?></h5>
                        </div>
                    </div>
                </div>
                <div class="uk-modal-body">
                    <div class="uk-form-horizontal">
                        <div class="uk-text-right">
                            <a class="uk-icon-button" uk-icon="print" href="debt/invoice/<?= $debt['id'] ?>"></a>
                            <!-- </?php foreach ($transactions as $transaction) {
                                if ($transaction['id'] === $debt['transactionid']) { ?>
                                    <a class="uk-icon-button" uk-icon="print" href="pay/copyprint/</?= $transaction['id'] ?>"></a>
                            </?php }
                            } ?> -->
                        </div>

                        <div class="uk-margin uk-margin-remove-top">
                            <label class="uk-form-label"><?= lang('Global.customer') ?></label>
                            <div class="uk-form-controls">: <?= $debt['name'] ?>
                                <!-- </?php foreach ($customers as $cust) {
                                    if ($debt['memberid'] === $cust['id']) {
                                        echo $cust['name'] . ' / ' . $cust['phone'];
                                    }
                                } ?> -->
                            </div>
                        </div>

                        <form role="form" action="debt/pay/<?= $debt['id'] ?>" method="post">
                            <?= csrf_field() ?>

                            <div class="uk-margin">
                                <label class="uk-form-label" for="form-horizontal-text"><?=lang('Global.payment')?></label>
                                <div class="uk-form-controls">
                                    <div class="uk-inline uk-width-1-1">
                                        <span class="uk-form-icon" uk-icon="icon: credit-card"></span>
                                        <select class="uk-select uk-input" id="payment" name="payment" required >
                                            <option value="" selected disabled hidden><?=lang('Global.payment')?></option>
                                            <?php
                                            foreach ($payments as $pay) {
                                                if ($pay['outletid'] == $outletPick) {
                                                    echo '<option value="'.$pay['id'].'">'.$pay['name'].'</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="uk-margin-bottom">
                                <label class="uk-form-label" for="value"><?= lang('Global.amountpaid') ?></label>
                                <div class="uk-form-controls">
                                    <?php if ($outletPick == null) { ?>
                                        <input type="number" class="uk-input" min="0" max="<?= $debt['value'] ?>" id="value" name="value" placeholder="0" disabled required />
                                    <?php } else { ?>
                                        <input type="number" class="uk-input" min="0" max="<?= $debt['value'] ?>" id="value" name="value" placeholder="0" required />
                                    <?php } ?>
                                </div>
                            </div>

                            <!-- </?php if ($debt['value'] !== "0") { ?>
                                <div class="uk-margin">
                                    <label class="uk-form-label"></?= lang('Global.duedate') ?></label>
                                    <div class="uk-form-controls">
                                        <div class="uk-inline">
                                            <span class="uk-form-icon uk-form-icon-flip" uk-icon="icon: calendar"></span>
                                            <input class="uk-input uk-form-width-medium" id="duedate</?= $debt['id'] ?>" name="duedate</?= $debt['id'] ?>" />
                                            <script type="text/javascript">
                                                $(function() {
                                                    $("#duedate</?= $debt['id'] ?>").datepicker({
                                                        dateFormat: "yy-mm-dd",
                                                        minDate: "</?= $debt['deadline'] ?>",
                                                        maxDate: "+1m +1w"
                                                    });
                                                });
                                            </script>
                                        </div>
                                    </div>
                                </div>
                            </?php } ?> -->

                            <!-- <div class="uk-margin">
                                <div class="uk-form-controls">
                                    <a class="uk-button uk-button-default" uk-toggle="#payproof-</?= $debt['id'] ?>"></?= lang('Global.payproof') ?></a>
                                </div>
                            </div>

                            <div class="uk-margin" hidden>
                                <input class="image-tag" name="image" required>
                            </div> -->

                            <hr>


                            <?php if ($outletPick != null) {
                                if ($dailyreport['dateclose'] == '0000-00-00 00:00:00') { ?>
                                    <div class="uk-margin">
                                        <div class="uk-width-5-6">
                                            <button type="submit" class="uk-button uk-button-primary" style="border-radius: 8px; width: 540px;"><?= lang('Global.pay') ?></button>
                                        </div>
                                    </div>
                                <?php }
                            } ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Pay Proof -->
    <!-- <div uk-modal class="uk-flex-top" id="payproof-</?= $debt['id'] ?>">
        <div class="uk-modal-dialog uk-margin-auto-vertical">
            <div class="uk-modal-content">
                <div class="uk-modal-header">
                    <div class="uk-flex uk-flex-middle uk-child-width-auto" uk-grid>
                        <div class="uk-padding-remove uk-margin-medium-left">
                            <a uk-icon="arrow-left" uk-toggle="#pay-</?= $debt['id'] ?>" width="35" height="35"></a>
                        </div>
                        <div>
                            <h5 class="uk-modal-title"></?= lang('Global.payproof') ?></h5>
                        </div>
                    </div>
                </div>
                <div class="uk-modal-body">
                    <div class="uk-flex-center uk-child-width-1-1" uk-grid>
                        <div id="pay_camera</?= $debt['id'] ?>"></div>
                        <div class="uk-text-center uk-margin-small-top">
                            <input class="uk-button uk-button-primary" id="btnTake" type="button" value="Take Snapshot" onClick="pay_snapshot</?= $debt['id'] ?>()">
                        </div>
                        <div class="uk-text-center" id="pay_results</?= $debt['id'] ?>"></div>
                    </div> -->

                    <!-- Script Webcam Pay Proof -->
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

                        Webcam.attach('#pay_camera</?= $debt['id'] ?>');

                        function pay_snapshot</?= $debt['id'] ?>() {
                            Webcam.snap(function(data_uri) {
                                $(".image-tag").val(data_uri);
                                document.getElementById('pay_results</?= $debt['id'] ?>').innerHTML = '<img src="' + data_uri + '"/>';
                            });
                        }
                    </script> -->
                    <!-- Script Webcam Pay Proof End -->
                <!-- </div>
            </div>
        </div>
    </div> -->
    <!-- Modal Pay Proof End -->
<?php } ?>
<!-- Modal Pay Debt End -->
<?= $this->endSection() ?>