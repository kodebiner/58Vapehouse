<?= $this->extend('layout') ?>

<?= $this->section('extraScript') ?>
    <script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
    <script type="text/javascript" src="js/moment.min.js"></script>
    <script type="text/javascript" src="js/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/daterangepicker.css" />
<?= $this->endSection() ?>

<?= $this->section('main') ?>

<?= view('Views/Auth/_message_block') ?>

<!-- Page Heading -->
<div class="tm-card-header uk-light uk-margin-bottom">
    <div uk-grid class="uk-flex-middle">
        <div class="uk-width-1-3@m">
            <h3 class="tm-h3">Laporan SOP</h3>
        </div>
        <div class="uk-width-expand@m uk-text-right uk-margin-right-remove">
            <form id="short" action="report/sop" method="get">
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
            <a type="button" class="uk-button uk-button-primary uk-preserve-color uk-margin-right-remove" target="_blank" href="export/sop?daterange=<?= date('Y-m-d', $startdate) ?>+-+<?= date('Y-m-d', $enddate) ?>"><?= lang('Global.export') ?></a>
        </div>
        <!-- Button Trigger Modal export End -->
    </div>
</div>
<!-- End Of Page Heading -->

<!-- Table Of Content -->
<div class="uk-overflow-auto">
    <table class="uk-table uk-table-divider uk-table-responsive uk-margin-top uk-light">
        <thead>
            <tr>
                <th>No.</th>
                <th><?= lang('Global.date') ?></th>
                <th><?= lang('Global.outlet') ?></th>
                <th><?= lang('Global.detail') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1 ; ?>
            <?php foreach ($sopdetails as $sopdet) { ?>
                <tr>
                    <td><?= $i++; ?></td>
                    <td><?= date('l, d M Y', strtotime($sopdet['date'])); ?></td>
                    <td><?= $sopdet['outlet'] ?></td>
                    <td>
                        <!-- Button Trigger Modal Detail -->
                        <div>
                            <a class="uk-icon-button" uk-icon="eye" uk-toggle="target: #detail-<?= $sopdet['id'] ?>"></a>
                        </div>
                        <!-- Button Trigger Modal Detail End -->
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<!-- Table Of Content End -->

<!-- Modal Detail -->
<?php foreach ($sopdetails as $sopdet) { ?>
    <div uk-modal class="uk-flex-top uk-modal-container" id="detail-<?= $sopdet['id'] ?>">
        <div class="uk-modal-dialog uk-margin-auto-vertical">
            <div class="uk-modal-content">
                <div class="uk-modal-header">
                    <div class="uk-child-width-1-2" uk-grid>
                        <div>
                            <h5 class="uk-modal-title"><?=lang('Global.detail')?></h5>
                        </div>
                        <div class="uk-text-right">
                            <button class="uk-modal-close uk-icon-button-delete" uk-icon="icon: close;" type="button"></button>
                        </div>
                    </div>
                </div>
                <div class="uk-modal-body">
                    <div class="uk-overflow-auto">
                        <table class="uk-table uk-table-divider uk-table-responsive uk-margin-top">
                            <thead>
                                <tr>
                                    <th style="color: #000;">SOP</th>
                                    <th style="color: #000;"><?= lang('Global.employee') ?></th>
                                    <th style="color: #000;"><?= lang('Global.status') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sopdet['detail'] as $detail) { ?>
                                    <tr>
                                        <td><?= $detail['sop'] ?></td>
                                        <td><?= $detail['employee'] ?></td>
                                        <td>
                                            <?php if ($detail['status'] == "0") { ?>
                                                <input class="uk-checkbox" type="checkbox" disabled>
                                            <?php } else { ?>
                                                <div uk-icon="check"></div>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<!-- Modal Detail End -->

<?= $this->endSection() ?>