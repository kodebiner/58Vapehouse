<?= $this->extend('layout') ?>

<?= $this->section('extraScript') ?>
<script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
<script type="text/javascript" src="js/moment.min.js"></script>
<script type="text/javascript" src="js/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="css/daterangepicker.css" />
<script src="js/cdn.datatables.net_1.13.4_js_jquery.dataTables.min.js"></script>
<?= $this->endSection() ?>

<?= $this->section('main') ?>

<!-- Page Heading -->
<div class="tm-card-header uk-light uk-margin-bottom">
    <div uk-grid class="uk-flex-middle">
        <div class="uk-width-1-3@m">
            <h3 class="tm-h3"><?= lang('Global.presencereport') ?></h3>
        </div>
        <div class="uk-width-expand@m uk-text-right uk-margin-right-remove">
            <form id="short" action="report/presence" method="get">
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
        <div class="uk-width-auto@m uk-text-right@m">
            <a type="button" class="uk-button uk-button-primary uk-preserve-color uk-margin-right-remove" target="_blank" href="export/presence?daterange=<?= date('Y-m-d', $startdate) ?>+-+<?= date('Y-m-d', $enddate) ?>"><?= lang('Global.export') ?></a>
        </div>
        <!-- Button Trigger Modal export End -->
    </div>
</div>
<!-- End Of Page Heading -->

<div class="uk-overflow-auto uk-margin">
    <table class="uk-table uk-table-divider uk-table-responsive uk-margin-top">
        <thead>
            <tr>
                <th class="uk-width-medium uk-text-bold"><?= lang('Global.date') ?></th>
                <th class="uk-width-medium uk-text-bold"><?= lang('Global.name') ?></th>
                <th class="uk-width-small uk-text-bold"><?= lang('Global.position') ?></th>
                <th class="uk-width-small uk-text-bold">Shift</th>
                <th class="uk-width-small uk-text-bold"><?= lang('Global.time').' '.lang('Global.checkin') ?></th>
                <th class="uk-width-small uk-text-bold">Keterlambatan</th>
                <th class="uk-width-small uk-text-bold"><?= lang('Global.photo').' '.lang('Global.checkin') ?></th>
                <th class="uk-width-small uk-text-bold"><?= lang('Global.location').' '.lang('Global.checkin') ?></th>
                <th class="uk-width-small uk-text-bold"><?= lang('Global.time').' '.lang('Global.checkout') ?></th>
                <th class="uk-width-small uk-text-bold"><?= lang('Global.photo').' '.lang('Global.checkout') ?></th>
                <th class="uk-width-small uk-text-bold"><?= lang('Global.location').' '.lang('Global.checkout') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($presences as $presence) {
                if ($presence['shift'] == '0') {
                    $waktu  = 'Pagi (09:00)';
                } elseif ($presence['shift'] == '1') {
                    $waktu  = 'Siang (12:00)';
                } elseif ($presence['shift'] == '2') {
                    $waktu  = 'Sore (16:00)';
                } elseif ($presence['shift'] == '3') {
                    $waktu  = 'UGM (10:00)';
                } elseif ($presence['shift'] == '4') {
                    $waktu  = 'Malam (0:00)';
                } ?>
                <tr>
                    <td style="color:white;"><?= date('l, d M Y', strtotime($presence['date'])) ?></td>
                    <td style="color:white;"><?= $presence['name'] ?></td>
                    <td style="color:white;"><?= $presence['role'] ?></td>
                    <td style="color:white;"><?= $waktu ?></td>
                    <?php foreach ($presence['detail'] as $detail) { ?>
                        <td style="color:white;"><?= $detail['time'] ?></td>
                        <?php if ($detail['status'] == '1') {
                            if ($presence['shift'] == '0') {
                                $kompensasi  = '09:15';
                            } elseif ($presence['shift'] == '1') {
                                $kompensasi  = '12:15';
                            } elseif ($presence['shift'] == '2') {
                                $kompensasi  = '16:15';
                            } elseif ($presence['shift'] == '3') {
                                $kompensasi  = '10:15';
                            } elseif ($presence['shift'] == '4') {
                                $kompensasi  = '00:15';
                            }
                            
                            if (str_replace(":","", $detail['time']) > str_replace(":","", $kompensasi)) { ?>
                                <td style="color:white;"><?= str_replace(":","", $detail['time']) - str_replace(":","", $kompensasi) ?></td>
                            <?php } else { ?>
                                <td style="color:white;">0</td>
                            <?php } ?>
                        <?php } ?>
                        <td style="color:white;">
                            <div uk-lightbox>
                                <a class="uk-inline" href="img/presence/<?= $detail['photo'] ?>">
                                    <img class="uk-preserve-width uk-border-circle" id="img<?php echo $presence['id'] ?>" src="img/presence/<?php echo $detail['photo'];?>" width="40" height="40" alt="<?= $detail['photo'] ?>">
                                </a>
                            </div>
                        </td>
                        <td style="color:white;"><?= $detail['geoloc'] ?></td>
                    <?php } ?>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <!-- <div>
        </?= $pager->links('presence', 'front_full') ?>
    </div> -->
</div>
<?= view('Views/Auth/_message_block') ?>

<?= $this->endSection() ?>