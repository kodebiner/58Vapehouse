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
                            <div class="tm-h2 uk-h4"><?=lang('Global.movementInfo')?></div>
                        </div>

                        <div class="uk-margin">
                            <label class="uk-form-label"><?=lang('Global.status')?></label>
                            <div class="uk-form-controls">
                                <!-- </?php if ($stockmove['status'] === "0") {
                                    echo '<span class="uk-text-primary" style="padding: 5px; border-style: solid; border-color: #1e87f0;">'.$created.$stockmove['origin'].'</span>';
                                } elseif ($stockmove['status'] === "1") {
                                    if ($outletPick == $stockmove['destinationid']) {
                                        echo '<span style="padding: 5px; border-style: solid; border-color: #faa05a;">'.$pending.$stockmove['destination'].'</span>';
                                    } elseif ($outletPick == $stockmove['originid']) {
                                        echo '<span style="padding: 5px; border-style: solid; border-color: #faa05a;">'.$sent.$stockmove['origin'].'</span>';
                                    } else {
                                        echo '<span style="padding: 5px; border-style: solid; border-color: #faa05a;">'.$pending.$stockmove['origin'].' / '.$stockmove['destination'].'</span>';
                                    }
                                } elseif ($stockmove['status'] === "2") {
                                    echo '<span class="uk-text-danger uk-width-auto" style="padding: 5px; border-style: solid; border-color: #f0506e;">'.$cancel.'</span>';
                                } elseif ($stockmove['status'] === "3") {
                                    echo '<span class="uk-text-success uk-width-auto" style="padding: 5px; border-style: solid; border-color: #32d296;">'.$success.$stockmove['destination'].'</span>';
                                }
                                ?> -->
                            </div>
                        </div>
                        
                        <div class="uk-margin">
                            <label class="uk-form-label"><?=lang('Global.date')?></label>
                            <div class="uk-form-controls"><?= date('l, d M Y, H:i:s', strtotime($stock['date'])); ?></div>
                        </div>
                    </div>

                    <div class="uk-divider-icon"></div>
                    
                    <table class="uk-table uk-table-justify uk-table-middle uk-table-divider uk-table-small" style="background-color: #fff; color: #000;">
                        <thead>
                            <tr>
                                <th class="uk-text-emphasis"><?=lang('Global.product')?></th>
                                <th class="uk-text-emphasis"><?=lang('Global.variant')?></th>
                                <th class="uk-text-emphasis"><?=lang('Global.quantity').' '.lang('Global.stock')?></th>
                                <th class="uk-text-emphasis"><?=lang('Global.capitalPrice')?></th>
                                <th class="uk-text-emphasis"><?=lang('Global.total').' '.lang('Global.capitalPrice')?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- </?php foreach ($stockmovedata[$stockmove['id']]['detail'] as $detail) { ?>
                                <tr>
                                    <td></?= $detail['productname']; ?></td>
                                    <td></?= $detail['variantname']; ?></td>
                                    <td></?= $detail['inputqty']; ?></td>
                                    <td></?= $detail['wholesale']; ?></td>
                                    <td></?= (Int)$detail['wholesale'] * (Int)$detail['inputqty']; ?></td>
                                </tr>
                            </?php } ?> -->
                        </tbody>
                        <tfoot>
                            <!-- <tr>
                                <td></?= lang('Global.totalMovement'); ?></td>
                                <td></td>
                                <td></td>
                                <td></?= $stockmove['totalqty'] ?></td>
                                <td></td>
                                <td></?= "Rp ".number_format($stockmove['totalwholesale'],0,',','.'); ?></td>
                            </tr> -->
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<!-- Modal Detail End -->
<?= $this->endSection() ?>