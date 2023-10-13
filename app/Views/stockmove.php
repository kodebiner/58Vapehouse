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
    <div uk-grid class="uk-flex-middle">
        <div class="uk-width-1-2@m">
            <h3 class="tm-h3"><?=lang('Global.stockmoveList')?></h3>
        </div>

        <?php if ($outletPick != null) { ?>
            <!-- Button Trigger Modal Add -->
            <div class="uk-width-1-2@m uk-text-right@m">
                <button type="button" class="uk-button uk-button-primary uk-preserve-color" uk-toggle="target: #tambahdata"><?=lang('Global.addStockMove')?></button>
            </div>
            <!-- End Of Button Trigger Modal Add -->
        <?php } ?>
    </div>
</div>
<!-- End Of Page Heading -->

<?= view('Views/Auth/_message_block') ?>

<!-- Modal Add -->
<div uk-modal class="uk-flex-top" id="tambahdata">
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <div class="uk-modal-content">
            <div class="uk-modal-header">
                <h5 class="uk-modal-title" id="tambahdata" ><?=lang('Global.addStockMove')?></h5>
            </div>
            <div class="uk-modal-body">
                <form class="uk-form-stacked" role="form" action="stockmove/create" method="post">
                    <?= csrf_field() ?>

                    <div class="uk-margin">
                        <label class="uk-form-label" for="origin"><?=lang('Global.origin')?></label>
                        <div class="uk-form-controls">
                            <select class="uk-select" name="origin" id="sel_out">
                                <option disabled><?=lang('Global.origin')?></option>
                                <?php foreach ($outlets as $outlet) {
                                    if ($outlet['id'] === $outletPick) {
                                        $checked = 'selected';
                                    } else {
                                        $checked = '';
                                    } ?>
                                    <option value="<?= $outlet['id']; ?>" <?=$checked?>><?= $outlet['name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="uk-margin">
                        <label class="uk-form-label" for="destination"><?=lang('Global.destination')?></label>
                        <div class="uk-form-controls">
                            <select class="uk-select" name="destination" id="sel_out">
                                <option disabled><?=lang('Global.destination')?></option>
                                <?php foreach ($outlets as $outlet) {
                                    if ($outlet['id'] === $outletPick) {
                                        $checked = 'selected';
                                    } else {
                                        $checked = '';
                                    } ?>
                                    <option value="<?= $outlet['id']; ?>" <?=$checked?>><?= $outlet['name']; ?></option>
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

                    <div class="uk-margin-small uk-flex uk-flex-middle " uk-grid>
                        <div class="uk-width-1-2">
                            <div class=""><?= lang('Global.variant') ?></div>
                        </div>
                        <div class="uk-width-1-2">
                            <div class=""><?= lang('Global.quantity') ?></div>
                        </div>
                    </div>

                    <div id="tableproduct"></div>

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

<!-- Script Modal Add -->
<script type="text/javascript">
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
                //$("#productid").val(i.item.idx);
                if (i.item.idx != 0) {
                    var products = <?php echo json_encode($products); ?>;
                    for (var x = 0; x < products.length; x++) {
                        document.getElementById('tablevariant'+products[x]['id']).setAttribute('hidden', '');
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
<!-- Script Modal Add End -->

<!-- Table Of Content -->
<div class="uk-overflow-auto uk-margin">
    <table class="uk-table uk-table-justify uk-table-middle uk-table-divider uk-light" id="example" style="width:100%">
        <thead>
            <tr>
                <th class="uk-text-center uk-width-small">No</th>
                <th class="uk-width-medium"><?=lang('Global.variant')?></th>
                <th class="uk-width-medium"><?=lang('Global.origin')?></th>
                <th class="uk-width-medium"><?=lang('Global.destination')?></th>
                <th class="uk-text-center uk-width-small"><?=lang('Global.quantity')?></th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1 ; ?>
            <?php foreach ($stockmoves as $stockmove) { ?>
                <tr>
                    <td class="uk-text-center"><?= $i++; ?></td>
                    <td class="">
                        <?php foreach ($variants as $variant) {
                            if ($variant['id'] === $stockmove['variantid']) {
                                echo $variant['name'];
                            }
                        } ?>
                    </td>
                    <td class="">
                        <?php foreach ($outlets as $outlet) {
                            if ($outlet['id'] === $stockmove['origin']) {
                                echo $outlet['name'];
                            }
                        } ?>
                    </td>
                    <td class="">
                        <?php foreach ($outlets as $outlet) {
                            if ($outlet['id'] === $stockmove['destination']) {
                                echo $outlet['name'];
                            }
                        } ?>
                    </td>
                    <td class="uk-text-center"><?= $stockmove['qty']; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<!-- End Of Table Content -->

<!-- Script -->
<script>
    $(document).ready(function () {
        $('#example').DataTable();
        
        // Product change
        $("#sel_pro").change(function(){

            // Selected country id
            var productid = $(this).val();

            // Fetch country states
            $.ajax({
                type: 'post',
                url: 'coba',
                data: {request:'getPro',productid:productid},
                dataType: 'json',
                success:function(response) {
                    console.log('success', arguments);

                    var len     = response.length;
                    var variant = arguments[0][0];
                    let option  = '<option>Variant</option>';

                    variant.forEach(itter);

                    document.getElementById('sel_variant').innerHTML = option;

                    function itter(value) {
                        option += '<option value="'+value.id+'">'+value.name+'</option>';
                    }
                }
            });
        });

        // Variant Change
        $("#sel_variant").change(function() {

            // Selected country id
            var variantid = $(this).val();

            // Fetch country states
            $.ajax({
                type: 'post',
                url: 'coba',
                data: {request:'getVariant',variantid:variantid},
                dataType: 'json',
                success:function(response) {
                    console.log('success', arguments);

                    var len = response.length;
                    var variant = arguments[0][0];

                    variant.forEach(itter);

                    var option = '<option value="'+value.id+'">'+value.name+'</option>';

                    document.getElementById('sel_variant').innerHTML = option;

                    function itter(value) {
                        option += '<option value="'+value.id+'">'+value.name+'</option>';
                    }
                }
            });
        }); 
    });
</script>
<!-- Script End -->
<?= $this->endSection() ?>