<?= $this->extend('layout') ?>

<?= $this->section('extraScript') ?>
<script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
<script src="js/cdnjs.cloudflare.com_ajax_libs_webcamjs_1.0.25_webcam.min.js"></script>
<script src="js/code.jquery.com_jquery-3.6.0.js"></script>
<script src="js/code.jquery.com_ui_1.13.2_jquery-ui.js"></script>
<script type="text/javascript" src="js/moment.min.js"></script>
<script type="text/javascript" src="js/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="css/daterangepicker.css" />
<?= $this->endSection() ?>

<?= $this->section('main') ?>

<!-- Page Heading -->
<div class="tm-card-header uk-light">
    <div uk-grid class="uk-flex-middle">
        <div class="uk-width-1-6@s">
            <h3 class="tm-h3"><?= lang('Global.cashinoutList') ?></h3>
        </div>

        <?php if ($outletPick != null) {
            if (empty($dailyreport)) { ?>
                <!-- Button Trigger Modal Open -->
                <div class="uk-width-5-6@s uk-text-right">
                    <button type="button" class="uk-button uk-button-primary uk-preserve-color" uk-toggle="target: #open"><?= lang('Global.open') ?></button>
                </div>
                <!-- Button Trigger Modal Open End -->
            <?php } elseif (($dailyreport['dateclose'] === '0000-00-00 00:00:00')) { ?>
                <div class="uk-width-5-6@s uk-child-width-auto uk-flex-middle uk-flex-right uk-margin-remove-left uk-padding-remove" uk-grid>
                    <!-- Button Trigger Modal Close -->
                    <div>
                        <button type="button" class="uk-button uk-button-danger uk-preserve-color" uk-toggle="target: #close-<?= $dailyreport['id'] ?>"><?= lang('Global.close') ?></button>
                    </div>
                    <!-- Button Trigger Modal Close End -->

                    <!-- Button Trigger Modal Withdraw -->
                    <!-- <div>
                        <button type="button" class="uk-button uk-button-primary uk-preserve-color" uk-toggle="target: #withdraw"><?= lang('Global.withdraw') ?></button>
                    </div> -->
                    <!-- Button Trigger Modal Withdraw End -->

                    <!-- Button Trigger Modal CashInOut -->
                    <div>
                        <button type="button" class="uk-button uk-button-primary uk-preserve-color" uk-toggle="target: #tambahdata"><?= lang('Global.cashin/out') ?></button>
                    </div>
                    <!-- Button Trigger Modal CashInOut End -->
                </div>
            <?php }
        } ?>
    </div>
    <div class="uk-width-expand@m uk-margin uk-text-right@l uk-text-center">
        <form id="short" action="cashinout" method="get">
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
</div>
<!-- End Of Page Heading -->

<?= view('Views/Auth/_message_block') ?>

<!-- Modal Close -->
<?php if (!empty($dailyreport)) { ?>
    <div uk-modal class="uk-flex-top" id="close-<?= $dailyreport['id'] ?>">
        <div class="uk-modal-dialog uk-margin-auto-vertical">
            <div class="uk-modal-content">
                <div class="uk-modal-header">
                    <div class="uk-child-width-1-2" uk-grid>
                        <div>
                            <h3 class="tm-h2 uk-text-center"><?= lang('Global.close') ?></h3>
                        </div>
                        <div class="uk-text-right">
                            <button class="uk-modal-close uk-icon-button-delete" uk-icon="icon: close;" type="button"></button>
                        </div>
                    </div>
                </div>
                <div class="uk-modal-body">
                    <div class="uk-margin">
                        <h5 class="tm-h3"><?= lang('Global.systemreceipts') ?></h5>
                        <div class="uk-child-width-1-2" uk-grid>
                            <div>
                                <div><?= lang('Global.cashflow') ?></div>
                            </div>
                            <div>
                                <?php
                                $totalcashin    = [];
                                $totalcashout   = [];
                                foreach ($dailyreport['cashflow'] as $cashflow) {
                                    if ($cashflow['type'] == '0') {
                                        $totalcashin[]  = $cashflow['qty'];
                                    } else {
                                        $totalcashout[] = $cashflow['qty'];
                                    }
                                }
                                $summarycashin  = array_sum($totalcashin);
                                $summarycashout = array_sum($totalcashout);
                                $totalcashflow  = ((Int)$dailyreport['initialcash'] + ((Int)$summarycashin - (Int)$summarycashout)); ?>
                                <div class="uk-text-right">Rp <?= number_format($totalcashflow, 2, ',', '.') ?></div>
                                <!-- <div class="uk-text-right">Rp </?= number_format($cashflow, 2, ',', '.') ?></div> -->
                            </div>
                        </div>

                        <div class="uk-margin-small-top">
                            <div><?= lang('Global.sales') ?></div>
                            <?php
                            $totaltrxvalue  = [];
                            $totaltrxvaluedebtpoin  = [];
                            foreach ($dailyreport['trxpayments'] as $key => $trxpayment) {
                                if ($key > 0) {
                                    $paymethodval   = [];
                                    foreach ($trxpayment['detail'] as $detail) {
                                        $paymethodval[] = $detail['value'];
                                    }
                                    $totalpaymethodvalue    = array_sum($paymethodval);
                                    $totaltrxvalue[]        = $totalpaymethodvalue; ?>
                                    <div class="uk-margin-remove-top">
                                        <div class="uk-child-width-1-2" uk-grid>
                                            <div>
                                                <div class="uk-margin-left"><?= $trxpayment['name'] ?></div>
                                            </div>
                                            <div class="uk-text-right">
                                                <div>
                                                    <?= 'Rp '.number_format($totalpaymethodvalue,2,',','.'); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php }
                                if ($key <= 0) {
                                    $paymethodvaldebtpoin   = [];
                                    foreach ($trxpayment['detail'] as $detail) {
                                        $paymethodvaldebtpoin[] = $detail['value'];
                                    }
                                    $totalpaymethodvaluedebtpoin    = array_sum($paymethodvaldebtpoin);
                                    $totaltrxvaluedebtpoin[]        = $totalpaymethodvaluedebtpoin; ?>
                                    <div class="uk-margin-remove-top">
                                        <div class="uk-child-width-1-2" uk-grid>
                                            <div>
                                                <div class="uk-margin-left"><?= $trxpayment['name'] ?></div>
                                            </div>
                                            <div class="uk-text-right">
                                                <div>
                                                    <?= 'Rp '.number_format($totalpaymethodvaluedebtpoin,2,',','.'); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php }
                            } ?>
                        </div>

                        <div class="uk-margin-small-top">
                            <div><?= lang('Global.debtInstallments') ?></div>
                            <?php
                            $totaldebtins  = [];
                            foreach ($dailyreport['debtins'] as $debtins) {
                                $debtinstallments   = [];
                                foreach ($debtins['detail'] as $detail) {
                                    $debtinstallments[] = $detail['value'];
                                }
                                $totaldebtinstallment   = array_sum($debtinstallments);
                                $totaldebtins[]         = $totaldebtinstallment; ?>
                                <div class="uk-margin-remove-top">
                                    <div class="uk-child-width-1-2" uk-grid>
                                        <div>
                                            <div class="uk-margin-left"><?= $debtins['name'] ?></div>
                                        </div>
                                        <div class="uk-text-right">
                                            <div>
                                                <?= 'Rp '.number_format($totaldebtinstallment,2,',','.'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>

                        <hr class="uk-margin-small-top uk-margin-small-bottom">

                        <div class="uk-margin-small-top">
                            <div class="uk-child-width-1-2 uk-text-bolder" style="color: #000;" uk-grid>
                                <div>
                                    <div class=""><?= lang('Global.totalsystemrec') ?></div>
                                </div>
                                <div class="uk-text-right">
                                    <div>
                                        <?php
                                            $totalvalue             = array_sum($totaltrxvalue);
                                            $totaldebtinstallment   = array_sum($totaldebtins);
                                            echo "Rp ".number_format(((Int)$totalvalue + (Int)$totaldebtinstallment + (Int)$totalcashflow),2,',','.');
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- <div class="uk-margin-remove-top uk-child-width-1-2" uk-grid>
                            <div>
                                <div></?= lang('Global.cashsales') ?></div>
                            </div>
                            <div>
                                <div class="uk-text-right">Rp </?= number_format($cashtrxvalue, 2, ',', '.') ?></div>
                            </div>
                        </div>

                        <hr class="uk-margin-small-top uk-margin-small-bottom">

                        <div class="uk-margin-remove-top uk-child-width-1-2" uk-grid>
                            <div>
                                <div></?= lang('Global.expectedcash') ?></div>
                            </div>
                            <div>
                                <div class="uk-text-right">Rp </?= number_format($expectedcash, 2, ',', '.') ?></div>
                            </div>
                        </div>

                        <div class="uk-margin-remove-top uk-child-width-1-2" uk-grid>
                            <div>
                                <div></?= lang('Global.noncashreceived') ?></div>
                            </div>
                            <div>
                                <div class="uk-text-right">Rp </?= number_format($noncashtrxvalue, 2, ',', '.') ?></div>
                            </div>
                        </div> -->

                        <!-- <hr class="uk-margin-small-top uk-margin-small-bottom">

                        <div class="uk-margin-remove-top uk-child-width-1-2" uk-grid>
                            <div>
                                <div></?= lang('Global.totalsystemrec') ?></div>
                            </div>
                            <div>
                                <div class="uk-text-right uk-text-bolder">Rp </?= number_format($totalsystemrec, 2, ',', '.') ?></div>
                            </div>
                        </div> -->
                    </div>

                    <hr class="uk-divider-icon">

                    <div class="uk-margin">
                        <h5 class="tm-h3">Checkpoint</h5>
                        <div class="uk-margin-remove-top">
                            <div class="uk-margin-small uk-child-width-1-5 uk-flex-middle" uk-grid>
                                <div>
                                    <div>Tanggal</div>
                                </div>
                                <div>
                                    <div>Jam</div>
                                </div>
                                <div>
                                    <div>Kasir</div>
                                </div>
                                <div>
                                    <div>Jumlah Tunai</div>
                                </div>
                                <div>
                                    <div>Jumlah Non-Tunai</div>
                                </div>
                            </div>
                            <?php
                            foreach ($dailyreport['checkpoint'] as $checkpoint) {
                                $checkdate  = strtotime($checkpoint['date']);
                                $checktime  = date('H:i', $checkdate);
                            ?>
                                <hr>
                                <div class="uk-margin-small uk-child-width-1-5 uk-flex-middle" uk-grid>
                                    <div>
                                        <div id="datecheckpoint-<?= $checkpoint['id'] ?>"></div>
                                    </div>
                                    <div>
                                        <div><?= $checktime ?></div>
                                    </div>
                                    <div>
                                        <div><?= $checkpoint['cashier'] ?></div>
                                    </div>
                                    <div>
                                        <div><?= $checkpoint['cash'] ?></div>
                                    </div>
                                    <div>
                                        <div><?= $checkpoint['noncash'] ?></div>
                                    </div>
                                    
                                    <script>
                                        // Date In Indonesia
                                        var publishupdate   = "<?= $checkpoint['date'] ?>";
                                        var thatdate        = publishupdate.split( /[- :]/ );
                                        thatdate[1]--;
                                        var publishthatdate = new Date( ...thatdate );
                                        var publishyear     = publishthatdate.getFullYear();
                                        var publishmonth    = publishthatdate.getMonth();
                                        var publishdate     = publishthatdate.getDate();
                                        
                                        switch(publishmonth) {
                                            case 0: publishmonth   = "Januari"; break;
                                            case 1: publishmonth   = "Februari"; break;
                                            case 2: publishmonth   = "Maret"; break;
                                            case 3: publishmonth   = "April"; break;
                                            case 4: publishmonth   = "Mei"; break;
                                            case 5: publishmonth   = "Juni"; break;
                                            case 6: publishmonth   = "Juli"; break;
                                            case 7: publishmonth   = "Agustus"; break;
                                            case 8: publishmonth   = "September"; break;
                                            case 9: publishmonth   = "Oktober"; break;
                                            case 10: publishmonth  = "November"; break;
                                            case 11: publishmonth  = "Desember"; break;
                                        }

                                        var publishfulldate         = publishdate + " " + publishmonth + " " + publishyear;
                                        document.getElementById("datecheckpoint-<?= $checkpoint['id'] ?>").innerHTML = publishfulldate;
                                    </script>
                                </div>
                            <?php } ?>
                            <hr id="dividercheckpoint" hidden>
                            <div id="newCheckpointGrid" class="uk-margin-small uk-child-width-1-5 uk-flex-middle" uk-grid>
                                <div id="datecp"></div>
                                <div id="timecp"></div>
                                <div id="cashiercp"></div>
                                <div id="cashcp"></div>
                                <div id="noncashcp"></div>
                            </div>
                            <hr class="uk-divider-icon">
                        </div>
                    </div>

                    <div class="uk-margin">
                        <h5 class="tm-h3"><?= lang('Global.actualreceipts') ?></h5>
                        <form class="uk-form-stacked" role="form" action="dayrep/close" method="post">
                            <?= csrf_field() ?>

                            <div class="uk-form-controls uk-margin">
                                <label class="uk-h6 uk-margin-small-left uk-text-muted"><?= lang('Global.cashreceived') ?></label>
                                <input type="number" class="uk-input cash" style="border-radius: 5px;" id="actualcash" name="actualcash" placeholder="<?= lang('Global.cashreceived') ?>" required />
                                <label class="uk-h6 uk-margin-small-left uk-text-muted"><?= lang('Global.includeinitcash') ?></label>
                            </div>

                            <div class="uk-form-controls uk-margin">
                                <label class="uk-h6 uk-margin-small-left uk-text-muted"><?= lang('Global.noncashreceived') ?></label>
                                <input type="number" class="uk-input noncash" style="border-radius: 5px;" id="actualnoncash" name="actualnoncash" placeholder="<?= lang('Global.noncashreceived') ?>" required />
                            </div>

                            <div class="uk-margin" uk-grid>
                                <div class="uk-width-1-2">
                                    <div class=""><?= lang('Global.difference') ?></div>
                                </div>
                                <div class="uk-width-1-2 uk-text-right">
                                    <div id="fprice"></div>
                                </div>
                            </div>

                            <hr>

                            <div class="uk-margin">
                                <button type="submit" class="uk-button uk-button-primary uk-width-1-1" style="border-radius: 10px;"><?= lang('Global.close') ?></button>
                            </div>
                        </form>

                        <div class="uk-margin">
                            <form class="uk-form-stacked" role="form" id="checkpointForm">
                                <?= csrf_field() ?>
                                <button type="button" class="uk-button uk-button-success uk-width-1-1" style="border-radius: 10px;" id="checkpointButton">Checkpoint</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        var cash    = document.getElementById('actualcash');
        var noncash = document.getElementById('actualnoncash');
        var fprice  = document.getElementById('fprice');

        totalprice();
        cash.addEventListener('change', totalprice);
        noncash.addEventListener('change', totalprice);

        function totalprice() {
            var actualcash = Number(cash.value);
            var actualnoncash = Number(noncash.value);
            var finalprice = actualcash + actualnoncash - <?= (Int)$totalvalue + (Int)$totaldebtinstallment + (Int)$totalcashflow ?>;
            var marker = finalprice;

            if (marker < '0') {
                fprice.setAttribute('class', 'uk-text-danger');
                fprice.innerHTML = finalprice.toLocaleString('id-ID', {
                    style: 'currency',
                    currency: 'IDR'
                });
            } else if (marker > '0') {
                fprice.setAttribute('class', 'uk-text-success');
                fprice.innerHTML = finalprice.toLocaleString('id-ID', {
                    style: 'currency',
                    currency: 'IDR'
                });
            } else {
                fprice.innerHTML = finalprice.toLocaleString('id-ID', {
                    style: 'currency',
                    currency: 'IDR'
                });
            }
        }

        // Checkpoint AJAX Function
        $('#checkpointButton').click(function() {
            var actualCash          = $('#actualcash').val();
            var actualNonCash       = $('#actualnoncash').val();
            
            $.ajax({
                url: 'dayrep/checkpoint',
                method: 'POST',
                data: {
                    actualcash: actualCash,
                    actualnoncash: actualNonCash,
                },
                success: function(response) {
                    alert('Checkpoint berhasil dengan data: ' + response);
                    
                    $('#actualcash').val('');
                    $('#actualnoncash').val('');
                },
                error: function(xhr, status, error) {
                    alert('Terjadi kesalahan saat pengiriman data: ' + error);
                }
            });
            
            // Creating New Checkpoint Grid
            var datecheckpoints     = "<?= date('Y-m-d') ?>";
            var cpdate              = datecheckpoints.split( /[- :]/ );
            cpdate[1]--;
            var cpdates             = new Date( ...cpdate );
            var publishyearcpdates  = cpdates.getFullYear();
            var publishmonthcpdates = cpdates.getMonth();
            var publishdatecpdates  = cpdates.getDate();
            
            switch(publishmonthcpdates) {
                case 0: publishmonthcpdates   = "Januari"; break;
                case 1: publishmonthcpdates   = "Februari"; break;
                case 2: publishmonthcpdates   = "Maret"; break;
                case 3: publishmonthcpdates   = "April"; break;
                case 4: publishmonthcpdates   = "Mei"; break;
                case 5: publishmonthcpdates   = "Juni"; break;
                case 6: publishmonthcpdates   = "Juli"; break;
                case 7: publishmonthcpdates   = "Agustus"; break;
                case 8: publishmonthcpdates   = "September"; break;
                case 9: publishmonthcpdates   = "Oktober"; break;
                case 10: publishmonthcpdates  = "November"; break;
                case 11: publishmonthcpdates  = "Desember"; break;
            }

            var dividercheckpoint = document.getElementById('dividercheckpoint');
            dividercheckpoint.removeAttribute('hidden', '');
            
            var datecp = document.getElementById('datecp');
            datecp.innerHTML = publishdatecpdates + " " + publishmonthcpdates + " " + publishyearcpdates;
            
            var timecp = document.getElementById('timecp');
            timecp.innerHTML = '<?= date('H:i') ?>';
            
            var cashiercp = document.getElementById('cashiercp');
            cashiercp.innerHTML = '<?= $fullname ?>';
            
            var cashcp = document.getElementById('cashcp');
            cashcp.innerHTML = 'Rp '+actualCash.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            
            var noncashcp = document.getElementById('noncashcp');
            noncashcp.innerHTML = 'Rp '+actualNonCash.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        });
    </script>
<?php } ?>
<!-- Modal Close End -->

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

<!-- Modal Add -->
<div uk-modal class="uk-flex-top" id="tambahdata">
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <div class="uk-modal-content">
            <div class="uk-modal-header">
                <div class="uk-child-width-1-2" uk-grid>
                    <div>
                        <h5 class="uk-modal-title" id="tambahdata"><?= lang('Global.addCash') ?></h5>
                    </div>
                    <div class="uk-text-right">
                        <button class="uk-modal-close uk-icon-button-delete" uk-icon="icon: close;" type="button"></button>
                    </div>
                </div>
            </div>
            <div class="uk-modal-body">
                <form class="uk-form-stacked" role="form" action="cashinout/create" method="post">
                    <?= csrf_field() ?>

                    <div class="uk-margin-bottom">
                        <!-- <div uk-form-custom="target: > * > span:first-child">
                            <select aria-label="Custom controls" name="cash">
                                <option selected hidden disabled>Choose Type</option>
                                <option value="0"></?= lang('Global.cashin') ?></option>
                                <option value="1"></?= lang('Global.cashout') ?></option>
                            </select>
                            <button class="uk-button uk-button-default" type="button" tabindex="-1">
                                <span></span>
                                <span uk-icon="icon: chevron-down"></span>
                            </button>
                        </div> -->

                        <div class="uk-child-width-auto" uk-grid>
                            <div>
                                <label><input class="uk-radio" type="radio" name="cash" value="1"> <?= lang('Global.cashout') ?></label>
                            </div>
                            <div>
                                <label><input class="uk-radio" type="radio" name="cash" value="0"> <?= lang('Global.cashin') ?></label>
                            </div>
                        </div>
                    </div>

                    <div class="uk-margin-bottom">
                        <label class="uk-form-label" for="description"><?= lang('Global.description') ?></label>
                        <div class="uk-form-controls">
                            <input type="text" class="uk-input <?php if (session('errors.description')) : ?>tm-form-invalid<?php endif ?>" id="description" name="description" placeholder="<?= lang('Global.description') ?>" autofocus required />
                        </div>
                    </div>

                    <div class="uk-margin-bottom">
                        <label class="uk-form-label" for="quantity"><?= lang('Global.quantity') ?></label>
                        <div class="uk-form-controls">
                            <input type="number" class="uk-input <?php if (session('errors.quantity')) : ?>tm-form-invalid<?php endif ?>" id="quantity" name="quantity" placeholder="<?= lang('Global.quantity') ?>" autofocus required />
                        </div>
                    </div>
                    
                    <div class="uk-margin-bottom">
                        <div class="uk-flex-center uk-child-width-1-1" uk-grid>
                            <div id="my_camera"></div>
                            <div class="uk-text-center uk-margin-small-top">
                                <input class="image-tag" type="hidden" name="image">
                                <input class="uk-button uk-button-primary" id="btnTake" type="button" value="Take Snapshot" onClick="take_snapshot()" required>
                            </div>
                            <div class="uk-text-center" id="results"></div>
                        </div>
                    </div>

                    <!-- Webcam Cash -->
                    <script type="text/javascript">
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

                        Webcam.attach('#my_camera');

                        function take_snapshot() {
                            Webcam.snap(function(data_uri) {
                                $(".image-tag").val(data_uri);
                                document.getElementById('results').innerHTML = '<img src="' + data_uri + '"/>';
                            });
                        }
                    </script>
                    <!-- Webcam Cash End -->

                    <hr>

                    <div class="uk-margin">
                        <button type="submit" class="uk-button uk-button-primary"><?= lang('Global.save') ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- End Of Modal Add -->

<!-- Modal Withdraw -->
<div class="uk-flex-top" id="withdraw" uk-modal>
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <div class="uk-modal-header">
            <div class="uk-child-width-1-2" uk-grid>
                <div>
                    <h2 class="uk-modal-title"><?= lang('Global.withdraw') ?></h2>
                </div>
                <div class="uk-text-right">
                    <button class="uk-modal-close uk-icon-button-delete" uk-icon="icon: close;" type="button"></button>
                </div>
            </div>
        </div>
        <div class="uk-modal-body">
            <form class="uk-form-horizontal uk-margin-large" action="cashinout/withdraw" method="post">
                <div class="uk-margin">
                    <label class="uk-form-label"><?= lang('Global.name') ?></label>
                    <div class="uk-form-controls">
                        <div class="uk-inline uk-width-1-1">
                            <span class="uk-form-icon" uk-icon="icon: user"></span>
                            <input class="uk-input" type="text" placeholder="Name" id="name" name="name" aria-label="Not clickable icon">
                        </div>
                    </div>
                </div>

                <div class="uk-margin">
                    <label class="uk-form-label" for="form-horizontal-text"><?=lang('Global.payment')?></label>
                    <div class="uk-form-controls">
                        <div class="uk-inline uk-width-1-1">
                            <span class="uk-form-icon" uk-icon="icon: credit-card"></span>
                            <select class="uk-select uk-input" id="payment" name="payment" required >
                                <option value="" selected disabled hidden><?=lang('Global.payment')?></option>
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
                </div>

                <div class="uk-margin">
                    <label class="uk-form-label" for="form-horizontal-text"><?= lang('Global.quantity') ?></label>
                    <div class="uk-form-controls">
                        <div class="uk-inline uk-width-1-1">
                            <span class="uk-form-icon" uk-icon="icon: database"></span>
                            <input class="uk-input" min="0" name="value" type="number" placeholder="<?= lang('Global.value') ?>" aria-label="Not clickable icon">
                        </div>
                    </div>
                </div>

                <div class="uk-margin-bottom">
                    <div class="uk-flex-center uk-child-width-1-1" uk-grid>
                        <div id="withdraw_camera"></div>
                        <div class="uk-text-center uk-margin-small-top">
                            <input class="image-tag" type="hidden" name="image">
                            <input class="uk-button uk-button-primary" id="btnTake" type="button" value="Take Snapshot" onClick="withdraw_snapshot()" required>
                        </div>
                        <div class="uk-text-center" id="withdraw_results"></div>
                    </div>
                </div>

                <!-- Webcam Withdraw -->
                <script type="text/javascript">
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

                    Webcam.attach('#withdraw_camera');

                    function withdraw_snapshot() {
                        Webcam.snap(function(data_uri) {
                            $(".image-tag").val(data_uri);
                            document.getElementById('withdraw_results').innerHTML = '<img src="' + data_uri + '"/>';
                        });
                    }
                </script>
                <!-- Webcam Withdraw End -->

                <div class="uk-modal-footer uk-text-right">
                    <button class="uk-button uk-button-primary" type="submit" value="submit"><?= lang('Global.save') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal Withdraw End -->

<!-- Table Of Content -->
<div class="uk-overflow-auto uk-margin">
    <table class="uk-table uk-table-justify uk-table-middle uk-table-divider uk-light">
        <thead>
            <tr>
                <th class="uk-text-center">No</th>
                <th class=""><?= lang('Global.description') ?></th>
                <th class=""><?= lang('Global.outlet') ?></th>
                <th class="uk-text-center"><?= lang('Global.type') ?></th>
                <th class="uk-text-center"><?= lang('Global.date') ?></th>
                <th class=""><?= lang('Global.name') ?></th>
                <th class="uk-text-center"><?= lang('Global.cash') ?></th>
                <th class="uk-text-center"><?= lang('Global.quantity') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1; ?>
            <?php foreach ($trxothers as $trx) : ?>
                <tr>
                    <td class="uk-text-center"><?= $i++; ?></td>
                    <td class="">
                        <?= $trx['description']; ?>
                    </td>
                    <td class="uk-text-left">
                        <?php if ($trx['outletid'] === '0') {
                            echo lang('Global.allOutlets');
                        } else {
                            foreach ($outlets as $outlet) {
                                if ($outlet['id'] === $trx['outletid']) {
                                    echo $outlet['name'];
                                }
                            }
                        } ?>
                    </td>
                    <td class="uk-text-center">
                        <?php if ($trx['type'] === "0") {
                            echo '<div class="uk-text-success" style="border-style: solid; border-color: #32d296;">' . 'Cash In' . '</div>';
                        } else {
                            echo '<div class="uk-text-danger" style="border-style: solid; border-color: #f0506e;">' . 'Cash Out' . '</div>';
                        } ?>
                    </td>
                    <td class="uk-text-center">
                        <?= date('l, d M Y, H:i:s', strtotime($trx['date'])); ?>
                    </td>
                    <td class="uk-text-left">
                        <?php foreach ($users as $user) {
                            if ($trx['userid'] === $user->id) {
                                echo $user->name;
                            }
                        } ?>
                    </td>
                    <td class="uk-text-center">
                        <?php foreach ($cash as $cas) {
                            if ($trx['cashid'] === $cas['id']) {
                                echo $cas['name'];
                            }
                        } ?>
                    </td>
                    <td class="uk-text-center">
                        Rp <?= number_format($trx['qty'], 2, ',', '.'); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div>
        <?= $pager->links('cashinout', 'front_full') ?>
    </div>
</div>
<!-- End Of Table Content -->
<?= $this->endSection() ?>