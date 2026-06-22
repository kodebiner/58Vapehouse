<?= $this->extend('layout') ?>

<?= $this->section('extraScript') ?>
    <link rel="stylesheet" href="css/code.jquery.com_ui_1.13.2_themes_base_jquery-ui.css">
    <script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
    <script src="js/code.jquery.com_jquery-3.6.0.js"></script>
    <script src="js/code.jquery.com_ui_1.13.2_jquery-ui.js"></script>
    <script type="text/javascript" src="js/moment.min.js"></script>
    <script type="text/javascript" src="js/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/daterangepicker.css" />
    <script src="js/stockmove.js"></script>
    <?php helper('stockmove') ?>
<?= $this->endSection() ?>

<?= $this->section('main') ?>

<script>
    window.productList = [
        <?php foreach ($productlist as $product) {
            echo '{label:"'.$product['name'].'",idx:'.$product['id'].'},';
        } ?>
    ];
    window.alertStock = '<?=lang('Global.alertstock')?>';
    window.alertReadyAdd = '<?=lang('Global.readyAdd')?>';
</script>

<!-- Page Heading -->
<div class="tm-card-header uk-light">
    <div uk-grid class="uk-flex-middle">
        <div class="uk-width-1-3@m uk-width-1-1">
            <h3 class="tm-h3"><?=lang('Global.stockmoveList')?></h3>
        </div>

        <!-- Button Daterange -->
        <div class="uk-width-1-3@m uk-width-1-2 uk-margin-right-remove">
            <form id="short" action="stockmove" method="get">
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

        <?php if ($outletPick != null) { ?>
            <!-- Button Trigger Modal Add -->
            <div class="uk-width-1-3@m uk-width-1-2 uk-text-right">
                <button type="button" class="uk-button uk-button-primary uk-preserve-color" uk-toggle="target: #tambahdata"><?=lang('Global.addStockMove')?></button>
            </div>
            <!-- End Of Button Trigger Modal Add -->
        <?php } ?>
    </div>
</div>
<!-- Page Heading End -->

<?= view('Views/Auth/_message_block') ?>

<!-- Modal Add -->
<div uk-modal class="uk-flex-top uk-modal-container" id="tambahdata">
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <div class="uk-modal-content">
            <div class="uk-modal-header">
                <div class="uk-child-width-1-2" uk-grid>
                    <div>
                        <h5 class="uk-modal-title" id="tambahdata" ><?=lang('Global.addStockMove')?></h5>
                    </div>
                    <div class="uk-text-right">
                        <button class="uk-modal-close uk-icon-button-delete" uk-icon="icon: close;" type="button"></button>
                    </div>
                </div>
            </div>
            <div class="uk-modal-body">
                <form class="uk-form-stacked" role="form" action="stockmove/create" method="post">
                    <?= csrf_field() ?>

                    <div class="uk-margin">
                        <label class="uk-form-label" for="origin"><?=lang('Global.origin')?></label>
                        <div class="uk-form-controls">
                            <select class="uk-select" name="origin">
                                <option selected disabled><?=lang('Global.origin')?></option>
                                <?php foreach ($outlets as $outlet) {
                                    if ($outlet['id'] === $outletPick) {
                                        $checked = 'selected';
                                    } else {
                                        $checked = 'disabled';
                                    } ?>
                                    <option value="<?= $outlet['id']; ?>" <?=$checked?>><?= $outlet['name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="uk-margin">
                        <label class="uk-form-label" for="destination"><?=lang('Global.destination')?></label>
                        <div class="uk-form-controls">
                            <select class="uk-select" name="destination" required>
                                <option disabled><?=lang('Global.destination')?></option>
                                <?php foreach ($outlets as $outlet) {
                                    if ($outlet['id'] === $outletPick) {
                                        $disabled = 'disabled';
                                    } else {
                                        $disabled = '';
                                    } ?>
                                    <option value="<?= $outlet['id']; ?>" <?=$disabled?>><?= $outlet['name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="uk-margin-bottom">
                        <label class="uk-form-label" for="product"><?=lang('Global.product')?></label>
                        <div class="uk-form-controls">
                            <input type="text" class="uk-input" id="productname" name="productname" placeholder="<?=lang('Global.product')?>">
                        </div>
                    </div>

                    <div id="tablevariant"></div>

                    <div class="uk-margin-small uk-flex-middle uk-flex-center" uk-grid>
                        <div class="uk-width-1-6 uk-text-center">
                            <div><?= lang('Global.variant') ?></div>
                        </div>
                        <div class="uk-width-1-2 uk-text-center">
                            <div><?= lang('Global.quantity') ?></div>
                        </div>
                        <div class="uk-width-1-6 uk-text-center">
                            <div><?= lang('Global.capitalPrice') ?></div>
                        </div>
                        <div class="uk-width-1-6 uk-text-center">
                            <div><?= lang('Global.total') ?></div>
                        </div>
                    </div>

                    <div id="tableproduct"></div>

                    <div class="uk-modal-footer">
                        <div class="uk-margin">
                            <div class="uk-width-1-1 uk-text-center">
                                <div class="uk-flex-top tm-h3"><?=lang('Global.total')?></div>
                            </div>
                            <div class="uk-width-1-1 uk-text-center">
                                <div class="tm-h2 uk-text-bold" id="finalprice" value="0">Rp 0,-</div>
                            </div>
                        </div>
                        <div class="uk-margin uk-flex uk-flex-center">
                            <button type="submit" class="uk-button uk-button-primary uk-button-large uk-text-center" style="border-radius: 8px; width: 540px;"><?=lang('Global.save')?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Modal Add End -->

<!-- Script Modal Add handled by stockmove.js -->

<!-- Table Of Content -->
<table class="uk-table uk-table-justify uk-table-middle uk-table-divider uk-light">
    <thead>
        <tr>
            <th class="uk-width-small"><?=lang('Global.date')?></th>
            <th class="uk-width-small"><?=lang('Global.origin')?></th>
            <th class="uk-width-small"><?=lang('Global.destination')?></th>
            <th class="uk-text-center uk-width-small"><?=lang('Global.totalMovement')?></th>
            <th class="uk-width-small"><?=lang('Global.total').' Harga Beli'?></th>
            <th class="uk-text-center uk-width-small"><?=lang('Global.status')?></th>
            <th class="uk-text-center uk-width-small"><?=lang('Global.action')?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($stockmovedata as $stockmove) { ?>
            <tr>
                <td class="uk-width-small"><?= date('l, d M Y, H:i:s', strtotime($stockmove['date'])); ?></td>
                <td class="uk-width-small"><?= $stockmove['origin'] ?></td>
                <td class="uk-width-small"><?= $stockmove['destination'] ?></td>
                <td class="uk-text-center uk-width-small"><?= $stockmove['totalqty'] ?></td>
                <td class="uk-width-small"><?= "Rp " . number_format($stockmove['totalhargabeli'],2,',','.') ?></td>
                <td class="uk-text-center uk-width-small"><?= renderStatusBadge($stockmove['status'], $stockmove['origin'], $stockmove['destination'], $stockmove['originid'], $stockmove['destinationid'], $outletPick) ?></td>

                <?php if ((($stockmove['status'] == "0") || ($stockmove['status'] == "1")) && ($outletPick != null)) {
                    if (($outletPick == $stockmove['originid']) && ($stockmove['status'] != '1')) { ?>
                        <td>
                            <div class="uk-child-width-auto uk-flex-center uk-flex-middle uk-grid-row-small uk-grid-column-small uk-text-center" uk-grid>
                                <!-- Button Trigger Modal Detail -->
                                <div class="">
                                    <a uk-icon="eye" class="uk-icon-link" uk-toggle="target: #detail<?= $stockmove['id'] ?>"></a>
                                </div>
                                <!-- End Of Button Trigger Modal Detail -->

                                <!-- Button Trigger Modal Edit -->
                                <div class="">
                                    <a class="uk-icon-button" uk-icon="pencil" uk-toggle="target: #editdata<?= $stockmove['id'] ?>"></a>
                                </div>
                                <!-- End Of Button Trigger Edit Detail -->

                                <!-- Button Confirmation -->
                                <div>
                                    <a class="uk-icon-button-success" uk-icon="check" uk-toggle="target: #savedata<?= $stockmove['id'] ?>"></a>
                                </div>
                                <!-- End Of Button Confirmation -->

                                <!-- Button Cancel -->
                                <div>
                                    <form class="uk-form-stacked" role="form" action="stockmove/cancel/<?= $stockmove['id'] ?>" method="post">
                                        <?= csrf_field() ?>
                                        <button type="submit" uk-icon="close" class="uk-icon-button-delete" onclick="return confirm('<?=lang('Global.cancelConfirm')?>')"></button>
                                    </form>
                                </div>
                                <!-- End Of Button Cancel -->
                            </div>
                        </td>
                    <?php } elseif (($outletPick == $stockmove['destinationid']) && ($stockmove['status'] == '1')) { ?>
                        <td>
                            <div class="uk-child-width-auto uk-flex-center uk-flex-middle uk-grid-row-small uk-grid-column-small uk-text-center" uk-grid>
                                <!-- Button Trigger Modal Detail -->
                                <div class="">
                                    <a uk-icon="eye" class="uk-icon-link" uk-toggle="target: #detail<?= $stockmove['id'] ?>"></a>
                                </div>
                                <!-- End Of Button Trigger Modal Detail -->

                                <!-- Button Trigger Modal Edit -->
                                <div class="">
                                    <a class="uk-icon-button" uk-icon="pencil" uk-toggle="target: #editdata<?= $stockmove['id'] ?>"></a>
                                </div>
                                <!-- End Of Button Trigger Edit Detail -->

                                <!-- Button Confirmation -->
                                <div>
                                    <a class="uk-icon-button-success" uk-icon="check" uk-toggle="target: #savedata<?= $stockmove['id'] ?>"></a>
                                </div>
                                <!-- End Of Button Confirmation -->

                                <!-- Button Cancel -->
                                <div>
                                    <form class="uk-form-stacked" role="form" action="stockmove/cancel/<?= $stockmove['id'] ?>" method="post">
                                        <?= csrf_field() ?>
                                        <button type="submit" uk-icon="close" class="uk-icon-button-delete" onclick="return confirm('<?=lang('Global.cancelConfirm')?>')"></button>
                                    </form>
                                </div>
                                <!-- End Of Button Cancel -->
                            </div>
                        </td>
                    <?php } else { ?>
                        <td class="uk-text-center uk-width-small">
                            <!-- Button Trigger Modal Detail -->
                            <div class="uk-text-center">
                                <a uk-icon="eye" class="uk-icon-link" uk-toggle="target: #detail<?= $stockmove['id'] ?>"></a>
                            </div>
                            <!-- End Of Button Trigger Modal Detail -->
                        </td>
                    <?php }
                } else { ?>
                    <td class="uk-text-center uk-width-small">
                        <!-- Button Trigger Modal Detail -->
                        <div class="uk-text-center">
                            <a uk-icon="eye" class="uk-icon-link" uk-toggle="target: #detail<?= $stockmove['id'] ?>"></a>
                        </div>
                        <!-- End Of Button Trigger Modal Detail -->
                    </td>
                <?php } ?>
            </tr>
        <?php } ?>
    </tbody>
</table>
<div>
    <?= $pager->links('stockmovement', 'front_full') ?>
</div>
<!-- Table Content End -->

<!-- Modal Confirm -->
<?php foreach ($stockmovedata as $stockmove) {
    if ((($outletPick == $stockmove['originid']) && ($stockmove['status'] != '1')) || (($outletPick == $stockmove['destinationid']) && ($stockmove['status'] == '1'))) { ?>
        <div uk-modal class="uk-flex-top uk-modal-container" id="savedata<?= $stockmove['id'] ?>">
            <div class="uk-modal-dialog uk-margin-auto-vertical">
                <div class="uk-modal-content">
                    <div class="uk-modal-header">
                        <div class="uk-child-width-1-2" uk-grid>
                            <div>
                                <h5 class="uk-modal-title" id="savedata" ><?=lang('Global.confirmation')?></h5>
                            </div>
                            <div class="uk-text-right">
                                <button class="uk-modal-close uk-icon-button-delete" uk-icon="icon: close;" type="button"></button>
                            </div>
                        </div>
                    </div>
                    <div class="uk-modal-body">
                        <form class="uk-form-stacked" role="form" action="stockmove/confirm/<?= $stockmove['id'] ?>" method="post">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id" value="<?= $stockmove['id']; ?>">
                            <input type="hidden" name="outletpick" value="<?= $outletPick ?>">
                            
                            <table class="uk-table uk-table-justify uk-table-middle uk-table-divider" style="background-color: #fff;">
                                <thead>
                                    <tr>
                                        <th class="uk-width-small uk-text-emphasis"><?=lang('Global.product')?></th>
                                        <th class="uk-width-small uk-text-emphasis"><?=lang('Global.variant')?></th>
                                        <th class="uk-width-small uk-text-emphasis"><?=lang('Global.totalPurchase')?></th>
                                        <th class="uk-width-medium uk-text-emphasis"><?=lang('Global.pcsPrice')?></th>
                                        <th class="uk-width-small uk-text-emphasis"><?=lang('Global.total')?></th>
                                    </tr>
                                </thead>
                                <tbody id="ctableproduct<?=$stockmove['id']?>">
                                    <?php
                                    $subtotalpurchase = array();
                                    foreach ($stockmovedata[$stockmove['id']]['detail'] as $detail) {
                                        if (empty($detail['varid'])) continue;
                                        $subtotalpurchase[] = (Int)$detail['inputqty'] * (Int)$detail['wholesale']; ?>
                                        <tr>
                                            <td><?= $detail['productname']; ?></td>
                                            <td><?= $detail['variantname']; ?></td>
                                            <td>
                                                <input type="number" class="uk-input js-confirm-qty" id="ctotalpcs[<?=$stockmove['id']?>][<?=$detail['varid']?>]" name="ctotalpcs[<?=$stockmove['id']?>][<?=$detail['varid']?>]" value="<?= $detail['inputqty']; ?>" max="<?= $detail['qty']; ?>" required data-sm-id="<?= $stockmove['id'] ?>" data-var-id="<?= $detail['varid'] ?>" data-price="<?= $detail['wholesale'] ?>" />
                                            </td>
                                            <td>
                                                <div><?= $detail['wholesale']; ?></div>
                                            </td>
                                            <td id="csubtotal<?=$stockmove['id']?><?=$detail['varid']?>" class="uk-width-small js-confirm-subtotal" data-sm-id="<?= $stockmove['id'] ?>"><?= (Int)$detail['wholesale'] * (Int)$detail['inputqty']; ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>

                            <div class="uk-modal-footer">
                                <div class="uk-margin">
                                    <div class="uk-width-1-1 uk-text-center">
                                        <div class="uk-flex-top tm-h3"><?=lang('Global.total')?></div>
                                    </div>
                                    <div class="uk-width-1-1 uk-text-center">
                                        <div class="tm-h2 uk-text-bold" id="cfinalprice<?=$stockmove['id']?>">Rp <?= array_sum($subtotalpurchase) ?>,-</div>
                                    </div>
                                </div>
                                <div class="uk-margin uk-flex uk-flex-center">
                                    <button type="submit" class="uk-button uk-button-primary uk-button-large uk-text-center" style="border-radius: 8px; width: 540px;"><?=lang('Global.save')?></button>
                                </div>
                            </div>


                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php }
} ?>
<!-- Modal Confirm End -->

<!-- Modal Detail -->
<?php foreach ($stockmovedata as $stockmove) { ?>
    <div uk-modal class="uk-flex-top uk-modal-container" id="detail<?= $stockmove['id'] ?>">
        <div class="uk-modal-dialog uk-margin-auto-vertical">
            <div class="uk-modal-content">
                <div class="uk-modal-header">
                    <div class="uk-child-width-1-2" uk-grid>
                        <div>
                            <h5 class="uk-modal-title" id="detail<?= $stockmove['id'] ?>" ><?=lang('Global.detail')?></h5>
                        </div>
                        <div class="uk-text-right">
                            <div class="uk-child-width-1-4 uk-flex-right" uk-grid>
                                <div>
                                    <a class="uk-icon-button" uk-icon="print" href="stockmove/stockmovementprint/<?= $stockmove['id'] ?>" target="_blank"></a>
                                </div>
                                <div>
                                    <button class="uk-modal-close uk-icon-button-delete" uk-icon="icon: close;" type="button"></button>
                                </div>
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
                                <?= renderStatusBadge($stockmove['status'], $stockmove['origin'], $stockmove['destination'], $stockmove['originid'], $stockmove['destinationid'], $outletPick) ?>
                            </div>
                        </div>
                        
                        <div class="uk-margin">
                            <label class="uk-form-label">Dibuat Oleh</label>
                            <div class="uk-form-controls"><?= $stockmove['creator'] ?></div>
                        </div>
                        
                        <div class="uk-margin">
                            <label class="uk-form-label"><?=lang('Global.date')?></label>
                            <div class="uk-form-controls"><?= date('l, d M Y, H:i:s', strtotime($stockmove['date'])); ?></div>
                        </div>
                        
                        <div class="uk-margin">
                            <label class="uk-form-label">No. <?=lang('Global.invoice')?></label>
                            <div class="uk-form-controls" id="invoice<?= $stockmove['id'] ?>"><?= 'SM' . date_format(date_create($stockmove['date']), 'Ymd') . $stockmove['id'] ?></div>
                        </div>

                        <div class="uk-margin">
                            <label class="uk-form-label"><?=lang('Global.origin')?></label>
                            <div class="uk-form-controls"><?= $stockmovedata[$stockmove['id']]['origin'] ?></div>
                        </div>

                        <div class="uk-margin">
                            <label class="uk-form-label">Dikirim Oleh</label>
                            <div class="uk-form-controls"><?= $stockmove['sender'] ?></div>
                        </div>

                        <div class="uk-margin">
                            <label class="uk-form-label"><?=lang('Global.destination')?></label>
                            <div class="uk-form-controls"><?= $stockmovedata[$stockmove['id']]['destination'] ?></div>
                        </div>

                        <div class="uk-margin">
                            <label class="uk-form-label">Diterima Oleh</label>
                            <div class="uk-form-controls"><?= $stockmove['receiver'] ?></div>
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
                            <?php foreach ($stockmovedata[$stockmove['id']]['detail'] as $detail) { ?>
                                <tr>
                                    <td><?= $detail['productname']; ?></td>
                                    <td><?= $detail['variantname']; ?></td>
                                    <td><?= $detail['inputqty']; ?></td>
                                    <td><?= $detail['wholesale']; ?></td>
                                    <td><?= (Int)$detail['wholesale'] * (Int)$detail['inputqty']; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td><?= lang('Global.totalMovement'); ?></td>
                                <td></td>
                                <td><?= $stockmove['totalqty'] ?></td>
                                <td></td>
                                <td><?= "Rp ".number_format($stockmove['totalwholesale'],0,',','.'); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

<?php } ?>
<!-- Modal Detail End -->

<!-- Modal Edit -->
<?php foreach ($stockmovedata as $stockmove) {
    if ((($outletPick == $stockmove['originid']) && ($stockmove['status'] == '0')) || (($outletPick == $stockmove['destinationid']) && ($stockmove['status'] == '1'))) { ?>
        <div uk-modal class="uk-flex-top uk-modal-container" id="editdata<?= $stockmove['id'] ?>">
            <div class="uk-modal-dialog uk-margin-auto-vertical">
                <div class="uk-modal-content">
                    <div class="uk-modal-header">
                        <div class="uk-child-width-1-2" uk-grid>
                            <div>
                                <h5 class="uk-modal-title" id="editdata<?= $stockmove['id'] ?>"><?=lang('Global.updateData')?></h5>
                            </div>
                            <div class="uk-text-right">
                                <button class="uk-modal-close uk-icon-button-delete" uk-icon="icon: close;" type="button"></button>
                            </div>
                        </div>
                    </div>

                    <div class="uk-modal-body">
                        <form class="uk-form-stacked" role="form" action="stockmove/update/<?= $stockmove['id'] ?>" method="post">
                            <?= csrf_field() ?>

                            <div class="uk-margin">
                                <label class="uk-form-label" for="origin"><?=lang('Global.origin')?></label>
                                <div class="uk-form-controls">
                                    <select class="uk-select" name="origin">
                                        <?php foreach ($outlets as $outlet) {
                                            $selected = ($outlet['id'] === $stockmove['originid']) ? 'selected' : '';
                                            $disabled = ($outlet['id'] !== $outletPick && $outlet['id'] !== $stockmove['originid']) ? 'disabled' : ''; ?>
                                            <option value="<?= $outlet['id']; ?>" <?=$selected?> <?=$disabled?>><?= $outlet['name']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="uk-margin">
                                <label class="uk-form-label" for="destination"><?=lang('Global.destination')?></label>
                                <div class="uk-form-controls">
                                    <select class="uk-select" name="destination" required>
                                        <option disabled><?=lang('Global.destination')?></option>
                                        <?php foreach ($outlets as $outlet) {
                                            $selected = ($outlet['id'] === $stockmove['destinationid']) ? 'selected' : '';
                                            $disabled = ($outlet['id'] === $outletPick && $outlet['id'] !== $stockmove['destinationid']) ? 'disabled' : ''; ?>
                                            <option value="<?= $outlet['id']; ?>" <?=$selected?> <?=$disabled?>><?= $outlet['name']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="uk-margin-bottom">
                                <label class="uk-form-label" for="product"><?=lang('Global.product')?></label>
                                <div class="uk-form-controls">
                                    <input type="text" class="uk-input js-edit-product" id="prodname<?= $stockmove['id'] ?>" name="prodname" placeholder="<?=lang('Global.product')?>" data-sm-id="<?= $stockmove['id'] ?>">
                                </div>
                            </div>

                            <div id="tabvar<?= $stockmove['id'] ?>"></div>

                            <div class="uk-margin-small uk-flex-middle uk-flex-center" uk-grid>
                                <div class="uk-width-1-6 uk-text-center">
                                    <div class=""><?= lang('Global.product') ?></div>
                                </div>
                                <div class="uk-width-1-2 uk-text-center">
                                    <div class=""><?= lang('Global.quantity') ?></div>
                                </div>
                                <div class="uk-width-1-6 uk-text-center">
                                    <div class=""><?= lang('Global.capitalPrice') ?></div>
                                </div>
                                <div class="uk-width-1-6 uk-text-center">
                                    <div class=""><?= lang('Global.total') ?></div>
                                </div>
                            </div>

                            <!-- Edit Modal JS handled by stockmove.js -->

                            <?php foreach ($stockmovedata[$stockmove['id']]['detail'] as $detailid => $detail) {
                                if (empty($detail['varid'])) continue; ?>
                                <div id="eproduct<?=$detailid?>" class="uk-margin-small uk-flex-middle uk-flex-center" uk-grid data-var-id="<?= $detail['varid'] ?>">
                                    <div class="uk-width-1-6">
                                        <div class=""><?= $detail['name'] ?></div>
                                    </div>
                                    <div class="uk-width-1-2 uk-text-center">
                                        <div class="tm-h2 pointerbutton uk-button uk-button-small uk-button-danger js-edit-existing-minus">-</div>
                                        <input class="uk-input uk-width-1-3 js-edit-existing-qty" type="number" id="totalpcs[<?=$detailid?>]" name="totalpcs[<?=$detailid?>]" value="<?= $detail['inputqty'] ?>" min="0" max="<?= $detail['qty'] ?>" required />
                                        <div class="tm-h2 pointerbutton uk-button uk-button-small uk-button-primary js-edit-existing-plus">+</div>
                                    </div>
                                    <div class="uk-width-1-6 uk-text-center">
                                        <input hidden class="uk-input js-edit-existing-price" type="number" value="<?= $detail['wholesale'] ?>" />
                                        <div><?= $detail['wholesale'] ?></div>
                                    </div>
                                    <div class="uk-width-1-6 uk-text-center js-edit-subtotal" id="subtotal<?= $detailid ?>">
                                        <?= (Int)$detail['wholesale'] * (Int)$detail['inputqty'] ?>
                                    </div>
                                </div>
                            <?php } ?>
                            
                            <div id="tableprod<?= $stockmove['id'] ?>"></div>

                            <div class="uk-modal-footer">
                                <div class="uk-margin uk-flex uk-flex-center">
                                    <button type="submit" class="uk-button uk-button-primary uk-button-large uk-text-center" style="border-radius: 8px; width: 540px;"><?=lang('Global.save')?></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php }
} ?>
<!-- Modal Edit End -->
<?= $this->endSection() ?>