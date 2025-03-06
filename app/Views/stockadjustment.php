<?= $this->extend('layout') ?>

<?= $this->section('extraScript') ?>
    <link rel="stylesheet" href="css/code.jquery.com_ui_1.13.2_themes_base_jquery-ui.css">
    <script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
    <script src="js/code.jquery.com_jquery-3.6.0.js"></script>
    <script src="js/code.jquery.com_ui_1.13.2_jquery-ui.js"></script>
    <script type="text/javascript" src="js/moment.min.js"></script>
    <script type="text/javascript" src="js/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/daterangepicker.css" />
<?= $this->endSection() ?>

<?= $this->section('main') ?>

<!-- Page Heading -->
<div class="tm-card-header uk-light">
    <div uk-grid class="uk-flex-middle">
        <div class="uk-width-1-3@m uk-width-1-1">
            <h3 class="tm-h3"><?=lang('Global.stockadjList')?></h3>
        </div>

        <!-- Button Daterange -->
        <div class="uk-width-1-3@m uk-width-1-2 uk-margin-right-remove">
            <form id="short" action="stockadjustment" method="get">
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

        <!-- Button Trigger Modal Add -->
        <div class="uk-width-1-3@m uk-width-1-2 uk-text-right">
            <button type="button" class="uk-button uk-button-primary uk-preserve-color" uk-toggle="target: #tambahdata"><?=lang('Global.addStockAdj')?></button>
        </div>
        <!-- End Of Button Trigger Modal Add -->
    </div>
</div>
<!-- End Of Page Heading -->

<?= view('Views/Auth/_message_block') ?>

<!-- Modal Add -->
<div uk-modal class="uk-flex-top uk-modal-container" id="tambahdata">
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <div class="uk-modal-content">
            <div class="uk-modal-header">
                <div class="uk-child-width-1-2" uk-grid>
                    <div>
                        <h5 class="uk-modal-title" id="tambahdata" ><?=lang('Global.addStockAdj')?></h5>
                    </div>
                    <div class="uk-text-right">
                        <button class="uk-modal-close uk-icon-button-delete" uk-icon="icon: close;" type="button"></button>
                    </div>
                </div>
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

                    <div id="tablevariant"></div>

                    <div class="uk-margin-small uk-flex uk-flex-middle " uk-grid>
                        <div class="uk-width-1-3">
                            <div class="">SKU</div>
                        </div>
                        <div class="uk-width-1-3">
                            <div class=""><?= lang('Global.variant') ?></div>
                        </div>
                        <div class="uk-width-1-3">
                            <div class=""><?= lang('Global.quantity') ?></div>
                        </div>
                    </div>

                    <div id="tableproduct"></div>
                                        
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
                    url:"stockadjustment/product",
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
                            var varskucon = document.createElement('div');
                            varskucon.setAttribute('class', 'uk-flex uk-flex-middle uk-width-1-4 uk-margin-small');
                                                            
                            var varsku = document.createElement('div');
                            varsku.setAttribute('class','');
                            varsku.innerHTML = variantarray[k]['sku'];

                            var varcontainer = document.createElement('div');
                            varcontainer.setAttribute('class', 'uk-flex uk-flex-middle uk-width-1-4 uk-margin-small');
                                                            
                            var varname = document.createElement('div');
                            varname.setAttribute('class','');
                            varname.innerHTML = variantarray[k]['name'];
                            
                            var stockcontainer = document.createElement('div');
                            stockcontainer.setAttribute('class', 'uk-flex uk-flex-middle uk-width-1-4 uk-margin-small');
                                                            
                            var stock = document.createElement('div');
                            stock.setAttribute('class','');
                            stock.innerHTML = variantarray[k]['qty'];

                            var cartcontainer = document.createElement('div');
                            cartcontainer.setAttribute('class', 'uk-flex uk-flex-center uk-flex-middle uk-width-1-4 uk-margin-small');

                            var cart = document.createElement('a');
                            cart.setAttribute('class', 'uk-icon-button');
                            cart.setAttribute('uk-icon', 'plus');
                            cart.setAttribute('onclick', 'createVar('+variantarray[k]['id']+')');

                            varskucon.appendChild(varsku);
                            varcontainer.appendChild(varname);
                            stockcontainer.appendChild(stock);
                            cartcontainer.appendChild(cart);
                            productgrid.appendChild(varskucon);
                            productgrid.appendChild(varcontainer);
                            productgrid.appendChild(stockcontainer);
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
                // document.getElementById('prodvar').remove();
                var elemexist = document.getElementById('product'+variantarray[k]['id']);
                // document.getElementById('tablevariant').setAttribute('hidden', '');
                var count = 1;
                if ( $( "#product"+variantarray[k]['id'] ).length ) {
                    alert('<?=lang('Global.readyAdd');?>');
                } else {
                    // if (variantarray[k]['qty'] == '0') {
                    //     alert("<?//=lang('Global.alertstock')?>");
                    // } else {
                        let minval = count;
                        var prods = document.getElementById('tableproduct');
                        
                        var pgrid = document.createElement('div');
                        pgrid.setAttribute('id', 'product'+variantarray[k]['id']);
                        pgrid.setAttribute('class', 'uk-margin-small');
                        pgrid.setAttribute('uk-grid', '');

                        var skucon = document.createElement('div');
                        skucon.setAttribute('class', 'uk-flex uk-flex-middle uk-width-1-3');
                                                        
                        var vsku = document.createElement('div');
                        vsku.setAttribute('id','var'+variantarray[k]['id']);
                        vsku.setAttribute('class','');
                        vsku.innerHTML = variantarray[k]['sku'];

                        var vcontainer = document.createElement('div');
                        vcontainer.setAttribute('class', 'uk-flex uk-flex-middle uk-width-1-3');
                                                        
                        var vname = document.createElement('div');
                        vname.setAttribute('id','var'+variantarray[k]['id']);
                        vname.setAttribute('class','');
                        vname.innerHTML = variantarray[k]['name'];

                        var tcontainer = document.createElement('div');
                        tcontainer.setAttribute('class', 'uk-flex uk-flex-center uk-flex-middle uk-width-1-3');

                        var tot = document.createElement('input');
                        tot.setAttribute('type', 'number');
                        tot.setAttribute('id', "totalpcs["+variantarray[k]['id']+"]");
                        tot.setAttribute('name', "totalpcs["+variantarray[k]['id']+"]");
                        tot.setAttribute('class', 'uk-input');
                        tot.setAttribute('value', '1');
                        tot.setAttribute('min', minval);
                        tot.setAttribute('required', '');

                        var pieces = document.createElement('div');
                        pieces.setAttribute('class', 'uk-margin-small-left');
                        pieces.innerHTML = 'Pcs';

                        skucon.appendChild(vsku);
                        vcontainer.appendChild(vname);
                        tcontainer.appendChild(tot);
                        tcontainer.appendChild(pieces);
                        pgrid.appendChild(skucon);
                        pgrid.appendChild(vcontainer);
                        pgrid.appendChild(tcontainer);
                        prods.appendChild(pgrid);

                        tot.addEventListener("change", function removeproduct() {
                            if (tot.value == '0') {
                                pgrid.remove();
                            }
                        });
                    // }
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
                <th class="">SKU</th>
                <th class=""><?=lang('Global.product')?></th>
                <th class=""><?=lang('Global.outlet')?></th>
                <th class="uk-text-center"><?=lang('Global.quantity')?></th>
                <th class=""><?=lang('Global.note')?></th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1 ; ?>
            <?php foreach ($stockadj as $stokadj) : 
                foreach ($variants as $variant) {
                    if ($variant['id'] === $stokadj['variantid']) {
                        $varName    = $variant['name'];
                        $varsku     = $variant['sku'];
                        foreach ($products as $product) {
                            if ($variant['productid'] === $product['id']) {
                                $ProdName = $product['name'];

                                $comName    = $ProdName.' - '.$varName;
                            }
                        }
                    }
                } ?>
                <tr>
                    <td class="uk-text-center"><?= $i++; ?></td>
                    <td class=""><?= date('l, d M Y, H:i:s', strtotime($stokadj['date'])); ?></td>
                    <td class=""><?= $varsku ?></td>
                    <td class=""><?= $comName ?></td>
                    <td class="">
                        <?php foreach ($outlets as $outlet) {
                        if ($outlet['id'] === $stokadj['outletid']) {
                            echo $outlet['name'];
                        }
                        } ?>
                    </td>
                    <td class="uk-text-center">
                        <?php if ($stokadj['type'] === "0") {
                            echo '<div style="color: #32d296;">+ '.$stokadj['qty'].'</div>';
                        } else {
                            echo '<div style="color: #f0506e;">- '.$stokadj['qty'].'</div>';
                        } ?>
                    </td>
                    <td class=""><?= $stokadj['note']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div>
        <?= $pager->links('stockadjustment', 'front_full') ?>
    </div>
</div>
<!-- End Of Table Content -->
<?= $this->endSection() ?>