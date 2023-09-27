<?= $this->extend('layout') ?>

<?= $this->section('extraScript') ?>
<script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
<script src="js/cdn.datatables.net_1.13.4_js_jquery.dataTables.min.js"></script>
<?= $this->endSection() ?>

<?= $this->section('main') ?>

<!-- Page Heading -->
<div class="tm-card-header uk-light">
    <div uk-grid class="uk-flex-middle">
        <div class="uk-width-1-2@m">
            <h3 class="tm-h3"><?=lang('Global.stockadjList')?></h3>
        </div>

        <!-- Button Trigger Modal Add -->
        <div class="uk-width-1-2@m uk-text-right@m">
            <button type="button" class="uk-button uk-button-primary uk-preserve-color" uk-toggle="target: #tambahdata"><?=lang('Global.addStockAdj')?></button>
        </div>
        <!-- End Of Button Trigger Modal Add -->
    </div>
</div>
<!-- End Of Page Heading -->

<?= view('Views/Auth/_message_block') ?>

<!-- Modal Add -->
<div uk-modal class="uk-flex-top" id="tambahdata">
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <div class="uk-modal-content">
            <div class="uk-modal-header">
                <h5 class="uk-modal-title" id="tambahdata" ><?=lang('Global.addStockAdj')?></h5>
            </div>
            <div class="uk-modal-body">
                <form class="uk-form-stacked" role="form" action="/stockadjustment/create" method="post">
                    <?= csrf_field() ?>
                            
                    <div class="uk-margin">
                        <label class="uk-form-label" for="type"><?=lang('Global.type')?></label>
                        <div class="uk-form-controls">
                            <select class="uk-select" name="type">
                                <option name="type" value="0" >Plus</option>
                                <option name="type" value="1" >Minus</option>
                            </select>
                        </div>
                    </div>

                    <div class="uk-margin">
                        <label class="uk-form-label" for="outlet"><?=lang('Global.outlet')?></label>
                        <div class="uk-form-controls">
                            <select class="uk-select" name="outlet">
                                <option><?=lang('Global.outlet')?></option>
                                <?php
                                foreach ($outlets as $outlet) {
                                    echo '<option value="'.$outlet['id'].'">'.$outlet['name'].'</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="uk-margin">
                        <label class="uk-form-label" for="form-horizontal-text">Name</label>
                        <div class="uk-form-controls">
                            <div class="uk-inline uk-width-1-1">
                                <span class="uk-form-icon" uk-icon="icon: user"></span>
                                <input class="uk-input ui-autocomplete-input1" type="text" placeholder="Name" id="customer" name="customer" aria-label="Not clickable icon">
                                <input id="customerx" name="customerid" hidden />
                            </div>
                        </div>                                    
                    </div>
                    
                    <script type="text/javascript">
                        $(function() {
                            var customerList = [
                                {label: "Non Member", idx:0},
                                <?php
                                    foreach ($customers as $customer) {
                                        echo '{label:"'.$customer['name'].' / '.$customer['phone'].'",idx:'.$customer['id'].'},';
                                    }
                                ?>
                            ];
                            $("#customer").autocomplete({
                                source: customerList,
                                select: function(e, i) {
                                    $("#customerx").val(i.item.idx);
                                },
                                minLength: 1
                            });
                        });
                    </script>

                    <div class="uk-margin">
                        <label class="uk-form-label" for="product"><?=lang('Global.product')?></label>
                        <div class="uk-form-controls">
                            <select class="uk-select" name="product" id="sel_pro">
                                <option><?=lang('Global.product')?></option>
                                <?php
                                foreach ($products as $product) {
                                    echo '<option value="'.$product['id'].'">'.$product['name'].'</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="uk-margin">
                        <label class="uk-form-label" for="variant"><?=lang('Global.variant')?></label>
                        <div class="uk-form-controls">
                            <select class="uk-select" name="variant" id="sel_variant">
                                <option id="default_var"><?=lang('Global.variant')?></option>
                            </select>
                        </div>
                    </div>
                                        
                    <div class="uk-margin">
                        <label class="uk-form-label" for="qty"><?=lang('Global.quantity')?></label>
                        <div class="uk-form-controls">
                            <input type="text" class="uk-input <?php if (session('errors.quantity')) : ?>tm-form-invalid<?php endif ?>" name="qty" id="qty" placeholder="<?=lang('Global.quantity')?>" required/>
                        </div>
                    </div>
                                        
                    <div class="uk-margin">
                        <label class="uk-form-label" for="note"><?=lang('Global.note')?></label>
                        <div class="uk-form-controls">
                            <input type="text" class="uk-input <?php if (session('errors.note')) : ?>tm-form-invalid<?php endif ?>" name="note" id="note" placeholder="<?=lang('Global.note')?>" required/>
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
<div class="uk-overflow-auto uk-margin">
    <table class="uk-table uk-table-justify uk-table-middle uk-table-divider uk-light" id="example" style="width:100%">
        <thead>
            <tr>
                <th class="uk-text-center">No</th>
                <th class=""><?=lang('Global.date')?></th>
                <th class=""><?=lang('Global.product')?></th>
                <th class=""><?=lang('Global.outlet')?></th>
                <th class=""><?=lang('Global.quantity')?></th>
                <th class=""><?=lang('Global.note')?></th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1 ; ?>
            <?php foreach ($stockadj as $stokadj) : ?>
                <tr>
                    <td class="uk-text-center"><?= $i++; ?></td>
                    <td class=""><?= date('l, d M Y, H:i:s', strtotime($stokadj['date'])); ?></td>
                    <td class="">
                        <?php 
                            foreach ($variants as $variant) {
                                if ($variant['id'] === $stokadj['variantid']) {
                                    $varName = $variant['name'];
                                    foreach ($products as $product) {
                                        if ($variant['productid'] === $product['id']) {
                                            $ProdName = $product['name'];

                                            echo $ProdName.' - '.$varName;
                                        }
                                    }
                                }
                            }
                        ?>
                    </td>
                    <td class="">
                        <?php foreach ($outlets as $outlet) {
                        if ($outlet['id'] === $stokadj['outletid']) {
                            echo $outlet['name'];
                        }
                        } ?>
                    </td>
                    <td class=""><?= $stokadj['qty']; ?></td>
                    <td class=""><?= $stokadj['note']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<!-- End Of Table Content -->

<!-- Script -->
<script>
    $(document).ready(function(){

        // Country change
        $("#sel_pro").change(function(){

            // Selected country id
            var productid = $(this).val();

            // Fetch country states
            $.ajax({
                type: 'post',
                url: 'coba',
                data: {request:'getPro',productid:productid},
                dataType: 'json',
                success:function(response){

                    console.log('success', arguments);

                    var len = response.length;
                    var variant = arguments[0][0];

                    let option = '<option>Variant</option>';

                    variant.forEach(itter);

                    document.getElementById('sel_variant').innerHTML = option;

                    function itter(value) {
                        option += '<option value="'+value.id+'">'+value.name+'</option>';
                    }
                }
            });
        });

        // Variant Change
        $("#sel_variant").change(function(){

            // Selected country id
            var variantid = $(this).val();

            // Fetch country states
            $.ajax({
                type: 'post',
                url: 'coba',
                data: {request:'getVariant',variantid:variantid},
                dataType: 'json',
                success:function(response){

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

<!-- Search Engine Script -->
<script>
  $(document).ready(function () {
    $('#example').DataTable();
  });
</script>
<!-- Search Engine Script End -->

<?= $this->endSection() ?>