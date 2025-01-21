<?= $this->extend('layout') ?>

<?= $this->section('extraScript') ?>
    <script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
    <script type="text/javascript" src="js/moment.min.js"></script>
    <script type="text/javascript" src="js/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/daterangepicker.css" />
<?= $this->endSection() ?>

<?= $this->section('main') ?>

<!-- Page Heading -->
<div class="tm-card-header uk-light">
    <div uk-grid class="uk-flex-middle">
        <div class="uk-width-1-2@m">
            <h3 class="tm-h3">Riwayat Stok <?= $name ?></h3>
            <h3 class="tm-h3"><?= $sku ?></h3>
        </div>
    </div>
</div>
<!-- End Of Page Heading -->

<?= view('Views/Auth/_message_block') ?>

<!-- Table Of Content -->
<div class="uk-overflow-auto uk-margin">
    <div class="uk-light" uk-grid>
        <!-- Counter Total -->
        <div class="uk-width-1-2@m uk-width-1-1 uk-form-horizontal">
            <div class="uk-form-label uk-margin-top" style="width: 100px;"><?= lang('Global.total') ?> <?= lang('Global.stock') ?> :</div>
            <div class="uk-form-controls uk-margin-top uk-margin-remove-left"><?= $totalstock ?></div>
        </div>
        <!-- Counter Total End -->

        <!-- Date Range -->
        <div class="uk-width-1-2@m uk-width-1-1 uk-text-right">
            <form id="short" action="product/history/<?= $id ?>" method="get">
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
        <!-- Date Range End -->
    </div>

    <table class="uk-table uk-table-justify uk-table-middle uk-table-divider uk-light">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Status</th>
                <th>Jumlah</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($stocks as $stock) { ?>
                <tr>
                    <td><?= date('l, d M Y, H:i:s', strtotime($stock['date'])) ?></td>
                    <td><?= $stock['status'] ?></td>
                    <td><?= $stock['qty'] ?></td>
                    <td><a uk-icon="eye" class="uk-icon-link uk-icon-button" uk-toggle="target: #detail-<?= strtotime($stock['date']) ?>"></a></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <div>
        <?= $pager_links ?>
    </div>
</div>
<!-- End Table Content -->

<!-- Modal Detail -->
<?php foreach ($stocks as $stock) { ?>
    <div uk-modal class="uk-flex-top uk-modal-container" id="detail-<?= strtotime($stock['date']) ?>">
        <div class="uk-modal-dialog uk-margin-auto-vertical">
            <div class="uk-modal-content">
                <div class="uk-modal-header">
                    <div class="uk-child-width-1-2" uk-grid>
                        <div>
                            <h5 class="uk-modal-title" id="detail-<?= strtotime($stock['date']) ?>" ><?=lang('Global.detail')?></h5>
                        </div>
                        <div class="uk-text-right">
                            <div>
                                <button class="uk-modal-close uk-icon-button-delete" uk-icon="icon: close;" type="button"></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="uk-modal-body">
                    <div class="uk-form-horizontal">
                        <div class="uk-margin">
                            <label class="uk-form-label"><?=lang('Global.date')?></label>
                            <div class="uk-form-controls"><?= date('l, d M Y, H:i:s', strtotime($stock['date'])); ?></div>
                        </div>

                        <div class="uk-margin">
                            <label class="uk-form-label"><?=lang('Global.status')?></label>
                            <div class="uk-form-controls"><?= $stock['status'] ?></div>
                        </div>

                        <div class="uk-margin">
                            <label class="uk-form-label"><?=lang('Global.outlet')?></label>
                            <div class="uk-form-controls"><?= $stock['outlet'] ?></div>
                        </div>

                        <div class="uk-margin">
                            <label class="uk-form-label"><?=lang('Global.employee')?></label>
                            <div class="uk-form-controls"><?= $stock['user'] ?></div>
                        </div>
                    </div>

                    <div class="uk-divider-icon"></div>
                    
                    <table class="uk-table uk-table-justify uk-table-middle uk-table-divider uk-table-small" style="background-color: #fff; color: #000;">
                        <thead>
                            <tr>
                                <th class="uk-text-emphasis">SKU</th>
                                <th class="uk-text-emphasis"><?=lang('Global.product')?></th>
                                <th class="uk-text-emphasis"><?=lang('Global.quantity')?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($stock['detail'] as $detail) { ?>
                                <tr>
                                    <td><?= $detail['sku']; ?></td>
                                    <td><?= $detail['name']; ?></td>
                                    <td><?= $detail['qty']; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<!-- Modal Detail End -->
<?= $this->endSection() ?>