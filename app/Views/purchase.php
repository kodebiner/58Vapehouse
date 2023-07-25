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

                    <div id="tablevariant">
                        <div class="uk-overflow-auto uk-margin-bottom">
                            <table class="uk-table uk-table-justify uk-table-middle uk-table-divider">
                                <thead>
                                    <tr>
                                        <th class="uk-width-medium" style="color: #000;"><?=lang('Global.variant')?></th>
                                        <th class="uk-width-small" style="color: #000;"><?=lang('Global.total')?></th>
                                        <th class="uk-width-small" style="color: #000;"></th>
                                        <th class="uk-text-center uk-width-medium" style="color: #000;"><?=lang('Global.pcsPrice')?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($purchases as $purchase) { ?>
                                        <?php foreach ($variants as $variant) {?>
                                            <?php if ($variant['id'] === $purchase['variantid']) {
                                                $VarName    = $variant['name'];
                                                $basePrice  = $variant['hargadasar']; ?>
                                            
                                                <tr>
                                                    <td class="uk-width-medium"><?= $VarName; ?></td>
                                                    <td class="uk-text-center uk-width-small">
                                                        <input type="number" class="uk-input" id="totalpcs" name="totalpcs" placeholder="0">
                                                    </td>
                                                    <td class="uk-width-small">Pcs</td>
                                                    <td class="uk-text-center uk-width-small">
                                                        <input type="number" class="uk-input" id="bprice" name="bprice" placeholder="<?php $basePrice; ?>">
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        <?php } ?>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

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
            <?php foreach ($purchases as $purchase) : ?>
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
                    <td class="uk-text-center uk-width-small"><?= $purchase['status']; ?></td>
                    <td class="uk-child-width-auto uk-flex-center uk-grid-row-small uk-grid-column-small" uk-grid>
                        <!-- Button Trigger Modal Detail -->
                        <div class="uk-text-center">
                            <button class="uk-button uk-button-success" style="border-radius: 15px;" uk-toggle="target: #detail<?= $purchase['id'] ?>"><?=lang('Global.detail')?></button>
                        </div>
                        <!-- End Of Button Trigger Modal Detail -->

                        <!-- Button Trigger Modal Edit -->
                        <div>
                            <a class="uk-icon-button" uk-icon="pencil" uk-toggle="target: #editdata<?= $purchase['id'] ?>"></a>
                        </div>
                        <!-- End Of Button Trigger Modal Edit -->

                        <!-- Button Delete -->
                        <div>
                            <a uk-icon="trash" class="uk-icon-button-delete" href="purchase/deletesup/<?= $purchase['id'] ?>" onclick="return confirm('<?=lang('Global.deleteConfirm')?>')"></a>
                        </div>
                        <!-- End Of Button Delete -->
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<!-- End Of Table Content -->

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
                        if (products[x]['id'] == i.item.idx) {
                            document.getElementById('tablevariant').removeAttribute('hidden');
                            document.getElementById('curpoin').innerHTML = '<?=lang('Global.yourpoint')?> ' + customers[x]['poin'];
                            document.getElementById('poin').setAttribute('max', customers[x]['poin']);
                            totalcount();
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