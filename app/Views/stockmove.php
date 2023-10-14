<?= $this->extend('layout') ?>

<?= $this->section('extraScript') ?>
<link rel="stylesheet" href="css/code.jquery.com_ui_1.13.2_themes_base_jquery-ui.css">
<script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
<script src="js/code.jquery.com_jquery-3.6.0.js"></script>
<script src="js/code.jquery.com_ui_1.13.2_jquery-ui.js"></script>
<?= $this->endSection() ?>

<?= $this->section('main') ?>

<!-- Page Heading -->
<div class="tm-card-header uk-light">
    <div uk-grid class="uk-flex-middle">
        <div class="uk-width-1-2@m">
            <h3 class="tm-h3"><?=lang('Global.stockmoveList')?></h3>
        </div>

        <!-- JANGAN LUPA DATE RANGE -->

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
                            <select class="uk-select" name="origin">
                                <option disabled><?=lang('Global.origin')?></option>
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
                                <option value="" selected disabled><?=lang('Global.destination')?></option>
                                <?php foreach ($outlets as $outlet) {
                                    if ($outlet['id'] === $outletPick) {
                                        $checked = 'disabled';
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

                    <div id="tablevariant"></div>

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
            <?php foreach ($productlist as $product) {
                echo '{label:"'.$product['name'].'",idx:'.$product['id'].'},';
            } ?>
        ];
        $("#productname").autocomplete({
            source: productList,
            select: function(e, i) {
                var data = { 'id' : i.item.idx };
                $.ajax({
                    url:"stockmove/product",
                    method:"POST",
                    data: data,
                    dataType: "json",
                    error:function() {
                        console.log('error', arguments);
                    },
                    success:function() {
                        console.log('success', arguments);
                        document.getElementById('tablevariant').removeAttribute('hidden');
                        var elements = document.getElementById('prodvar');
                        if (elements){
                            elements.remove();
                        }
                        var products = document.getElementById('tablevariant');
                        
                        var productgrid = document.createElement('div');
                        productgrid.setAttribute('id', 'prodvar');
                        productgrid.setAttribute('class', 'uk-padding uk-padding-remove-vertical');
                        productgrid.setAttribute('uk-grid', '');

                        variantarray = arguments[0];

                        for (k in variantarray) {
                            //alert(variantarray[k]['name']);
                            var varcontainer = document.createElement('div');
                            varcontainer.setAttribute('class', 'uk-flex uk-flex-middle uk-width-1-2 uk-margin-small');
                                                            
                            var varname = document.createElement('div');
                            varname.setAttribute('class','');
                            varname.innerHTML = variantarray[k]['name'];

                            var cartcontainer = document.createElement('div');
                            cartcontainer.setAttribute('class', 'uk-flex uk-flex-center uk-flex-middle uk-width-1-2 uk-margin-small');

                            var cart = document.createElement('a');
                            cart.setAttribute('class', 'uk-icon-button');
                            cart.setAttribute('uk-icon', 'plus');
                            cart.setAttribute('onclick', 'createVar('+variantarray[k]['id']+')');

                            varcontainer.appendChild(varname);
                            cartcontainer.appendChild(cart);
                            productgrid.appendChild(varcontainer);
                            productgrid.appendChild(cartcontainer);
                        };
                        
                        products.appendChild(productgrid);
                    },
                })
            },
            minLength: 2
        });
    });
    function createVar(id) {
        for (k in variantarray) {
            if (variantarray[k]['id'] == id) {
                if (variantarray[k]['qty'] != "0") {
                    document.getElementById('prodvar').remove();
                    var elemexist = document.getElementById('product'+variantarray[k]['id']);
                    document.getElementById('tablevariant').setAttribute('hidden', '');
                    var count = 1;
                    if ( $( "#product"+variantarray[k]['id'] ).length ) {
                        alert('<?=lang('Global.readyAdd');?>');
                    } else {
                        let minval = count;
                        var prods = document.getElementById('tableproduct');
                                                    
                        var pgrid = document.createElement('div');
                        pgrid.setAttribute('id', 'product'+variantarray[k]['id']);
                        pgrid.setAttribute('class', 'uk-margin-small');
                        pgrid.setAttribute('uk-grid', '');

                        var vcontainer = document.createElement('div');
                        vcontainer.setAttribute('class', 'uk-flex uk-flex-middle uk-width-1-2');
                                                        
                        var vname = document.createElement('div');
                        vname.setAttribute('id','var'+variantarray[k]['id']);
                        vname.setAttribute('class','');
                        vname.innerHTML = variantarray[k]['name'];

                        var tcontainer = document.createElement('div');
                        tcontainer.setAttribute('class', 'uk-flex uk-flex-center uk-flex-middle uk-width-1-2');

                        var tot = document.createElement('input');
                        tot.setAttribute('type', 'number');
                        tot.setAttribute('id', "totalpcs["+variantarray[k]['id']+"]");
                        tot.setAttribute('name', "totalpcs["+variantarray[k]['id']+"]");
                        tot.setAttribute('class', 'uk-input');
                        tot.setAttribute('value', '1');
                        tot.setAttribute('max', variantarray[k]['qty']);
                        tot.setAttribute('min', minval);
                        tot.setAttribute('required', '');

                        var pieces = document.createElement('div');
                        pieces.setAttribute('class', 'uk-margin-small-left');
                        pieces.innerHTML = 'Pcs';

                        vcontainer.appendChild(vname);
                        tcontainer.appendChild(tot);
                        tcontainer.appendChild(pieces);
                        pgrid.appendChild(vcontainer);
                        pgrid.appendChild(tcontainer);
                        prods.appendChild(pgrid);

                        tot.addEventListener("change", function removeproduct() {
                            if (tot.value == '0') {
                                pgrid.remove();
                            }
                        });
                    }
                } else {
                    alert('<?=lang('Global.alertstock');?>');
                }
            }
        }
    };
</script>
<!-- Script Modal Add End -->

<!-- Table Of Content -->
<div class="uk-overflow-auto uk-margin">
    <table class="uk-table uk-table-justify uk-table-middle uk-table-divider uk-light">
        <thead>
            <tr>
                <th class="uk-text-center">No</th>
                <th class=""><?=lang('Global.date')?></th>
                <th class=""><?=lang('Global.variant')?></th>
                <th class=""><?=lang('Global.origin')?></th>
                <th class=""><?=lang('Global.destination')?></th>
                <th class="uk-text-center"><?=lang('Global.quantity')?></th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1 ; ?>
            <?php foreach ($stockmoves as $stockmove) { ?>
                <tr>
                    <td class="uk-text-center"><?= $i++; ?></td>
                    <td><?= date('l, d M Y, H:i:s', strtotime($stockmove['date'])); ?></td>
                    <td class="">
                        <?php foreach ($products as $product) {
                            foreach ($variants as $variant) {
                                if ($variant['id'] === $stockmove['variantid'] && $product['id'] === $variant['productid']) {
                                    echo $product['name'].' - '.$variant['name'];   
                                }
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
    <div class="uk-light">
        <?= $pager->links('stockmove', 'front_full') ?>
    </div>
</div>
<!-- End Of Table Content -->
<?= $this->endSection() ?>