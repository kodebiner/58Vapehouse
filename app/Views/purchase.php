<?= $this->extend('layout') ?>

<?= $this->section('extraScript') ?>
<link rel="stylesheet" href="css/code.jquery.com_ui_1.13.2_themes_base_jquery-ui.css">
<script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
<script src="js/code.jquery.com_jquery-3.6.0.js"></script>
<script src="js/cdn.datatables.net_1.13.4_js_jquery.dataTables.min.js"></script>
<script src="js/code.jquery.com_ui_1.13.2_jquery-ui.js"></script>
<?= $this->endSection() ?>

<?= $this->section('main') ?>

<!-- Page Heading -->
<div class="tm-card-header uk-light">
    <?= view('Views/Auth/_message_block') ?>

    <div uk-grid class="uk-flex-middle">
        <div class="uk-width-1-2@m">
            <h3 class="tm-h3"><?=lang('Global.purchaseList')?></h3>
        </div>

        <!-- Button Trigger Modal Add -->
        <div class="uk-width-1-2@m uk-text-right@m">
            <button type="button" class="uk-button uk-button-primary uk-preserve-color" uk-toggle="target: #tambahdata"><?=lang('Global.addPurchase')?></button>
        </div>
        <!-- End Of Button Trigger Modal Add -->
    </div>
</div>
<!-- End Of Page Heading -->

<!-- Modal Add -->
<div uk-modal class="uk-flex-top" id="tambahdata">
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <div class="uk-modal-content">
            <div class="uk-modal-header">
                <h5 class="uk-modal-title" id="tambahdata" ><?=lang('Global.addPurchase')?></h5>
            </div>
            <div class="uk-modal-body">
                <form class="uk-form-stacked" role="form" action="stock/createpur" method="post">
                    <?= csrf_field() ?>

                    <div class="uk-margin-bottom">
                        <label class="uk-form-label" for="outlet"><?=lang('Global.outlet')?></label>
                        <div class="uk-form-controls">
                            <select class="uk-select" name="outlet" id="sel_out">
                                <option><?=lang('Global.outlet')?></option>
                                <?php foreach ($outlets as $outlet) {
                                    if ($outlet['id'] === $outletPick) {
                                        $checked = 'selected';
                                    } else {
                                        $checked = '';
                                    }
                                    ?>
                                    <option value="<?= $outlet['id']; ?>" <?=$checked?>><?= $outlet['name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="uk-margin-bottom">
                        <label class="uk-form-label" for="supplier"><?=lang('Global.supplier')?></label>
                        <div class="uk-form-controls">
                            <input type="text" class="uk-input" id="suppliername" name="suppliername" placeholder="<?=lang('Global.supplier')?>">
                        </div>
                    </div>

                    <div class="uk-margin-bottom">
                        <label class="uk-form-label" for="product"><?=lang('Global.product')?></label>
                        <div class="uk-form-controls">
                            <input type="text" class="uk-input" id="productname" name="productname" placeholder="<?=lang('Global.product')?>">
                        </div>
                    </div>

                    <?php foreach ($products as $product) {?>
                        <div id="tablevariant<?= $product['id']; ?>" hidden>
                            <div class="uk-overflow-auto uk-margin-bottom">
                                <table class="uk-table uk-table-justify uk-table-middle uk-table-divider">
                                    <thead>
                                        <tr>
                                            <th class="uk-text-emphasis uk-width-medium"><?=lang('Global.variant')?></th>
                                            <th class="uk-text-emphasis uk-width-small"><?=lang('Global.total')?></th>
                                            <th class="uk-text-emphasis uk-width-small"></th>
                                            <th class="uk-text-emphasis uk-text-center uk-width-medium"><?=lang('Global.pcsPrice')?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($variants as $variant) {?>
                                            <?php if ($variant['productid'] === $product['id']) {
                                                $VarName    = $variant['name'];
                                                $basePrice  = $variant['hargadasar']; ?>
                                            
                                                <tr>
                                                    <td class="uk-width-medium"><?= $VarName; ?></td>
                                                    <td class="uk-text-center uk-width-small">
                                                        <input type="number" class="uk-input" id="totalpcs[<?=$variant['id']?>]" name="totalpcs[<?=$variant['id']?>]" placeholder="0">
                                                    </td>
                                                    <td class="uk-width-small">Pcs</td>
                                                    <td class="uk-text-center uk-width-small">
                                                        <input type="number" class="uk-input" id="bprice[<?=$variant['id']?>]" name="bprice[<?=$variant['id']?>]" placeholder="<?= $basePrice; ?>">
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php } ?>

                    <hr>

                    <div class="uk-margin">
                        <button type="submit" class="uk-button uk-button-primary"><?=lang('Global.save')?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- End Of Modal Add -->

<!-- Table Of Content -->
<?php
$success    = "Completed";
$cancel     = "Canceled";
$pending    = "Order Processed";

foreach ($purchases as $purchase) { ?>
    <div class="uk-margin">
        <table class="uk-table uk-table-justify uk-table-middle uk-table-divider uk-light" id="example" style="width:100%">
            <thead>
                <tr>
                    <th class="uk-width-medium"><?=lang('Global.date')?></th>
                    <th class="uk-width-small"><?=lang('Global.supplier')?></th>
                    <th class="uk-text-center uk-width-small"><?=lang('Global.total')?></th>
                    <th class="uk-text-center uk-width-small"><?=lang('Global.status')?></th>
                    <th class="uk-text-center uk-width-small"><?=lang('Global.action')?></th>
                </tr>
            </thead>
            <tbody>
                    <tr>
                        <td class="uk-width-medium"><?= $purchase['restock']; ?></td>
                        <td class="uk-width-small">
                            <?php foreach ($suppliers as $supplier) {
                                if ($supplier['id'] === $purchase['supplierid']) {
                                    echo $supplier['name'];
                                }
                            } ?>
                        </td>
                        <td class="uk-text-center uk-width-small"><?= $purchase['price']; ?></td>
                        <td class="uk-text-center uk-width-small">
                            <?php if ($purchase['status'] === "0") {
                                echo '<div class="uk-text-success" style="border-style: solid; border-color: #32d296;">'.$success.'</div>';
                            } elseif ($purchase['status'] === "1") {
                                echo '<div class="uk-text-danger" style="border-style: solid; border-color: #f0506e;">'.$cancel.'</div>';
                            } else {
                                echo '<div class="uk-text-primary" style="border-style: solid; border-color: #1e87f0;">'.$pending.'</div>';
                            } ?>
                        </td>

                        <?php if ($purchase['status'] === null) { ?>
                            <td class="uk-child-width-auto uk-flex-center uk-flex-middle uk-grid-row-small uk-grid-column-small" uk-grid>
                                <!-- Button Trigger Modal Detail -->
                                <div class="uk-text-center">
                                    <a uk-icon="eye" class="uk-icon-link" uk-toggle="target: #detail<?= $purchase['id'] ?>"></a>
                                </div>
                                <!-- End Of Button Trigger Modal Detail -->

                                <!-- Button Trigger Modal Edit -->
                                <div>
                                    <a class="uk-icon-button-success" uk-icon="check" uk-toggle="target: #savedata<?= $purchase['id'] ?>"></a>
                                </div>
                                <!-- End Of Button Trigger Modal Edit -->

                                <!-- Button Delete -->
                                <div>
                                    <a uk-icon="close" class="uk-icon-button-delete" href="purchase/deletesup/<?= $purchase['id'] ?>" onclick="return confirm('<?=lang('Global.deleteConfirm')?>')"></a>
                                </div>
                                <!-- End Of Button Delete -->
                            </td>
                        <?php } else {?>
                            <td class="uk-text-center uk-width-small">
                                <!-- Button Trigger Modal Detail -->
                                <div class="uk-text-center">
                                    <a uk-icon="eye" class="uk-icon-link" uk-toggle="target: #detail<?= $purchase['id'] ?>"></a>
                                </div>
                                <!-- End Of Button Trigger Modal Detail -->
                            </td>
                        <?php } ?>
                    </tr>
            </tbody>
        </table>
    </div>
<?php } ?>
<!-- End Of Table Content -->

<!-- Modal Detail -->
<?php 
$success    = "Completed";
$cancel     = "Canceled";
$pending    = "Order Processed";

foreach ($purchases as $purchase) { ?>
    <div uk-modal class="uk-flex-top" id="detail<?= $purchase['id'] ?>">
        <div class="uk-modal-dialog uk-margin-auto-vertical">
            <div class="uk-modal-content">
                <div class="uk-modal-header">
                    <h5 class="uk-modal-title" id="detail" ><?=lang('Global.detail')?></h5>
                </div>
                <div class="uk-modal-body">
                    <div class="uk-form-horizontal">
                        <div class="uk-margin">
                            <label class="uk-form-label"><?=lang('Global.status')?></label>
                            <div class="uk-form-controls">
                                <?php if ($purchase['status'] === "0") {
                                    echo '<span class="uk-text-success uk-width-auto" style="padding: 5px; border-style: solid; border-color: #32d296;">'.$success.'</span>';
                                } elseif ($purchase['status'] === "1") {
                                    echo '<span class="uk-text-danger uk-width-auto" style="padding: 5px; border-style: solid; border-color: #f0506e;">'.$cancel.'</span>';
                                } else {
                                    echo '<span class="uk-text-primary" style="padding: 5px; border-style: solid; border-color: #1e87f0;">'.$pending.'</span>';
                                } ?>
                            </div>
                        </div>
                        <div class="uk-margin">
                            <label class="uk-form-label"><?=lang('Global.date')?></label>
                            <div class="uk-form-controls"><?= $purchase['restock'] ?></div>
                        </div>

                        <?php foreach ($outlets as $outlet) { ?>
                            <?php if ($outlet['id'] === $purchase['outletid']) { ?>
                                <div class="uk-margin">
                                    <label class="uk-form-label"><?=lang('Global.outlet')?></label>
                                    <div class="uk-form-controls"><?= $outlet['name'] ?></div>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<!-- Modal Detail End -->

<!-- Search Engine Script -->
<script type="text/javascript">

    // Data Table
    $(document).ready(function () {
        $('#example').DataTable();
    });

    // Autocomplete Supplier
    $(function() {
        var supplierList = [
            <?php foreach ($suppliers as $supplier) {
                echo '"'.$supplier['name'].'",';
            }?>
        ];
        $("#suppliername").autocomplete({
            source: supplierList,
        });
    });

    // Autocomplete Product
    $(function() {
        var productList = [
            <?php foreach ($products as $product) {
                echo '{label:"'.$product['name'].'",idx:'.$product['id'].'},';
            }?>
        ];
        $("#productname").autocomplete({
            source: productList,
            select: function(e, i) {
                $("#productid").val(i.item.idx);
                if (i.item.idx != 0) {
                    var products = <?php echo json_encode($products); ?>;
                    for (var x = 0; x < products.length; x++) {
                        document.getElementById('tablevariant'+products[x]['id']).setAttribute('hidden', '');
                        <?php
                        foreach ($variants as $variant) {
                            echo 'document.getElementById("totalpcs['.$variant['id'].']").value = "0";';
                            echo 'document.getElementById("bprice['.$variant['id'].']").value = "'.$variant['hargadasar'].'";';
                        }
                        ?>
                        if (products[x]['id'] == i.item.idx) {
                            document.getElementById('tablevariant'+products[x]['id']).removeAttribute('hidden');
                        }
                    }
                }
            },
            minLength: 2
        });
    });
</script>
<!-- Search Engine Script End -->

<?= $this->endSection() ?>