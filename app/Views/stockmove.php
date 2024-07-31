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

<script>
    productList = [
        <?php foreach ($productlist as $product) {
            echo '{label:"'.$product['name'].'",idx:'.$product['id'].'},';
        } ?>
    ];
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
                                <option selected disabled><?=lang('Global.destination')?></option>
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

                    <div class="uk-margin-small" uk-grid>
                        <div class="uk-flex uk-flex-middle uk-flex-center uk-width-1-5 uk-text-center">
                            <div class="">SKU</div>
                        </div>
                        <div class="uk-flex uk-flex-middle uk-flex-center uk-width-1-5 uk-text-center">
                            <div class=""><?= lang('Global.variant') ?></div>
                        </div>
                        <div class="uk-flex uk-flex-middle uk-flex-center uk-width-1-5 uk-text-center">
                            <div class=""><?= lang('Global.quantity') ?></div>
                        </div>
                        <div class="uk-flex uk-flex-middle uk-flex-center uk-width-1-5 uk-text-center">
                            <div class=""><?= lang('Global.capitalPrice') ?></div>
                        </div>
                        <div class="uk-flex uk-flex-middle uk-flex-center uk-width-1-5 uk-text-center">
                            <div class=""><?= lang('Global.total') ?></div>
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

<!-- Script Modal Add -->
<script type="text/javascript">
    // Autocomplete Product
    $(function() {
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
                        var elements = document.getElementById('variantlist');
                        if (elements){
                            elements.remove();
                        }
                        var products = document.getElementById('tablevariant');
                        
                        var productgrid = document.createElement('div');
                        productgrid.setAttribute('id', 'variantlist');
                        productgrid.setAttribute('class', 'uk-padding uk-padding-remove-vertical');
                        productgrid.setAttribute('uk-grid', '');

                        variantarray = arguments[0];

                        for (k in variantarray) {
                            //alert(variantarray[k]['name']);
                            var varskucontainer = document.createElement('div');
                            varskucontainer.setAttribute('class', 'uk-flex uk-flex-middle uk-width-1-5 uk-margin-small');
                                                            
                            var varsku = document.createElement('div');
                            varsku.setAttribute('class','');
                            varsku.innerHTML = variantarray[k]['sku'];
                            
                            var varcontainer = document.createElement('div');
                            varcontainer.setAttribute('class', 'uk-flex uk-flex-middle uk-width-1-5 uk-margin-small');
                                                            
                            var varname = document.createElement('div');
                            varname.setAttribute('class','');
                            varname.innerHTML = variantarray[k]['name'];

                            var stockcontainer = document.createElement('div');
                            stockcontainer.setAttribute('class', 'uk-flex uk-flex-center uk-flex-middle uk-width-1-5 uk-margin-small');
                                                            
                            var stock = document.createElement('div');
                            stock.setAttribute('class','');
                            stock.innerHTML = variantarray[k]['qty'];

                            var wholesalecontainer = document.createElement('div');
                            wholesalecontainer.setAttribute('class', 'uk-flex uk-flex-center uk-flex-middle uk-width-1-5 uk-margin-small');
                                                            
                            var wholesale = document.createElement('div');
                            wholesale.setAttribute('class','');
                            wholesale.innerHTML = variantarray[k]['wholesale'];

                            var cartcontainer = document.createElement('div');
                            cartcontainer.setAttribute('class', 'uk-flex uk-flex-center uk-flex-middle uk-width-1-5 uk-margin-small');

                            var cart = document.createElement('a');
                            cart.setAttribute('class', 'uk-icon-button');
                            cart.setAttribute('uk-icon', 'cart');
                            cart.setAttribute('onclick', 'createVar('+variantarray[k]['id']+')');

                            varskucontainer.appendChild(varsku);
                            varcontainer.appendChild(varname);
                            stockcontainer.appendChild(stock);
                            wholesalecontainer.appendChild(wholesale);
                            cartcontainer.appendChild(cart);
                            productgrid.appendChild(varskucontainer);
                            productgrid.appendChild(varcontainer);
                            productgrid.appendChild(stockcontainer);
                            productgrid.appendChild(wholesalecontainer);
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
                    document.getElementById('variantlist').remove();
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

                        var skucontainer = document.createElement('div');
                        skucontainer.setAttribute('class', 'uk-flex uk-flex-center uk-flex-middle uk-width-1-5');
                                                        
                        var sku = document.createElement('div');
                        sku.setAttribute('id','var'+variantarray[k]['id']);
                        sku.setAttribute('class','');
                        sku.innerHTML = variantarray[k]['sku'];

                        var vcontainer = document.createElement('div');
                        vcontainer.setAttribute('class', 'uk-flex uk-flex-middle uk-width-1-5');
                                                        
                        var vname = document.createElement('div');
                        vname.setAttribute('id','var'+variantarray[k]['id']);
                        vname.setAttribute('class','');
                        vname.innerHTML = variantarray[k]['name'];

                        var tcontainer = document.createElement('div');
                        tcontainer.setAttribute('class', 'uk-flex uk-flex-center uk-flex-middle uk-width-1-5');

                        var tot = document.createElement('input');
                        tot.setAttribute('type', 'number');
                        tot.setAttribute('id', "totalpcs["+variantarray[k]['id']+"]");
                        tot.setAttribute('name', "totalpcs["+variantarray[k]['id']+"]");
                        tot.setAttribute('max', variantarray[k]['qty']);
                        tot.setAttribute('class', 'uk-input');
                        tot.setAttribute('value', '1');
                        tot.setAttribute('required', '');

                        var pieces = document.createElement('div');
                        pieces.setAttribute('class', 'uk-margin-small-left');
                        pieces.innerHTML = 'Pcs';

                        var pricecontainer = document.createElement('div');
                        pricecontainer.setAttribute('class', 'uk-flex uk-flex-center uk-flex-middle uk-width-1-5');

                        var price = document.createElement('input');
                        price.setAttribute('type', 'number');
                        price.setAttribute('hidden', '');
                        price.setAttribute('id', "bprice["+variantarray[k]['id']+"]");
                        price.setAttribute('name', "bprice["+variantarray[k]['id']+"]");
                        price.setAttribute('class', 'uk-input');
                        price.setAttribute('value', variantarray[k]['wholesale']);
                        price.setAttribute('required', '');

                        var pricediv = document.createElement('div');
                        pricediv.innerHTML = variantarray[k]['wholesale'];

                        var subtotcontainer = document.createElement('div');
                        subtotcontainer.setAttribute('class', 'uk-flex uk-flex-center uk-text-center uk-flex-middle uk-width-1-5');

                        var subtotal = document.createElement('div');
                        subtotal.setAttribute('id', "subtotal"+variantarray[k]['id']+"");
                        subtotal.setAttribute('class', 'subvariant');

                        totalprice();
                        tot.addEventListener('change', totalprice);
                        price.addEventListener('change', totalprice);

                        function totalprice() {
                            var varprice = price.value;
                            var varqty = tot.value;
                            var subprice = varprice * varqty;
                            subtotal.setAttribute('value', subprice);
                            subtotal.innerHTML = subprice;
                        }

                        skucontainer.appendChild(sku);
                        vcontainer.appendChild(vname);
                        tcontainer.appendChild(tot);
                        tcontainer.appendChild(pieces);
                        pricecontainer.appendChild(price);
                        pricecontainer.appendChild(pricediv);
                        subtotcontainer.appendChild(subtotal);
                        pgrid.appendChild(skucontainer);
                        pgrid.appendChild(vcontainer);
                        pgrid.appendChild(tcontainer);
                        pgrid.appendChild(pricecontainer);
                        pgrid.appendChild(subtotcontainer);
                        prods.appendChild(pgrid);

                        tot.addEventListener("change", function removeproduct() {
                            if (tot.value == '0') {
                                pgrid.remove();
                            }
                        });
                    }
                    $('#productname').val('');
                    return false;
                } else {
                    alert('<?=lang('Global.alertstock');?>');
                }
            }
        }
    };

    var totalcount = document.getElementById('tableproduct');
    let totalpurchase = new MutationObserver(mutationRecords => {
        var prices = document.querySelectorAll(".subvariant");
        var subarr = [];

        for (i = 0; i < prices.length; i++) {
            price = Number(prices[i].innerText);
            subarr.push(price);
        }

        if (subarr.length === 0) {
            document.getElementById('finalprice').innerHTML = 0;
        } else {
            var subtotalvar = subarr.reduce(function(a, b){ return a + b; });
            document.getElementById('finalprice').innerHTML = 'Rp. ' + subtotalvar + ',-';
        }
    });
                            
    totalpurchase.observe(totalcount, {
        childList: true, // observe direct children
        subtree: true, // and lower descendants too
        characterDataOldValue: true // pass old data to callback
    });
</script>
<!-- Script Modal Add End -->

<!-- Table Of Content -->
<div class="uk-margin">
    <table class="uk-table uk-table-justify uk-table-middle uk-table-divider uk-light">
        <thead>
            <tr>
                <th class="uk-width-small"><?=lang('Global.date')?></th>
                <th class="uk-width-small"><?=lang('Global.origin')?></th>
                <th class="uk-width-small"><?=lang('Global.destination')?></th>
                <th class="uk-text-center uk-width-small"><?=lang('Global.totalMovement')?></th>
                <th class="uk-width-small"><?=lang('Global.total').' '.lang('Global.capitalPrice')?></th>
                <th class="uk-text-center uk-width-small"><?=lang('Global.status')?></th>
                <th class="uk-text-center uk-width-small"><?=lang('Global.action')?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $created    = lang('Global.smovecreated');
            $sent       = lang('Global.smovesent');
            $pending    = lang('Global.smovepending');
            $success    = lang('Global.smoveaccepted');
            $cancel     = lang('Global.smovecanceled');

            foreach ($stockmovedata as $stockmove) {
                // if ($outletPick == $stockmove['destinationid']) {
                //     if ($stockmove['status'] == '0') {
                //         $hidden = 'hidden';
                //     } else {
                //         $hidden = '';
                //     }
                // } else {
                //     $hidden = '';
                // }
            ?>
                <tr>
                    <td class="uk-width-small"><?= date('l, d M Y, H:i:s', strtotime($stockmove['date'])); ?></td>

                    <td class="uk-width-small"><?= $stockmove['origin'] ?></td>
                    
                    <td class="uk-width-small"><?= $stockmove['destination'] ?></td>

                    <td class="uk-text-center uk-width-small">
                        <?= $stockmove['totalqty'] ?> Pcs
                    </td>

                    <td class="uk-width-small">
                        <?= "Rp " . number_format($stockmove['totalwholesale'],2,',','.') ?>
                    </td>

                    <td class="uk-text-center uk-width-small">
                        <?php if ($stockmove['status'] === "0") {
                            echo '<div class="uk-text-primary" style="border-style: solid; border-color: #1e87f0;">'.$created.$stockmove['origin'].'</div>';
                        } elseif ($stockmove['status'] === "1") {
                            if ($outletPick == $stockmove['destinationid']) {
                                echo '<div style="border-style: solid; border-color: #faa05a;">'.$pending.$stockmove['destination'].'</div>';
                            } elseif ($outletPick == $stockmove['originid']) {
                                echo '<div style="border-style: solid; border-color: #faa05a;">'.$sent.$stockmove['origin'].'</div>';
                            } else {
                                echo '<div style="border-style: solid; border-color: #faa05a;">'.$pending.$stockmove['origin'].' / '.$stockmove['destination'].'</div>';
                            }
                        } elseif ($stockmove['status'] === "2") {
                            echo '<div class="uk-text-danger" style="border-style: solid; border-color: #f0506e;">'.$cancel.'</div>';
                        } elseif ($stockmove['status'] === "3") {
                            echo '<div class="uk-text-success" style="border-style: solid; border-color: #32d296;">'.$success.$stockmove['destination'].'</div>';
                        }
                        ?>
                    </td>

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
                                        <th class="uk-width-small uk-text-emphasis">SKU</th>
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
                                        $subtotalpurchase[] = (Int)$detail['inputqty'] * (Int)$detail['wholesale']; ?>
                                        <tr>
                                            <td><?= $detail['sku']; ?></td>
                                            <td><?= $detail['productname']; ?></td>
                                            <td><?= $detail['variantname']; ?></td>
                                                
                                            <td>
                                                <input type="number" class="uk-input" id="ctotalpcs[<?=$stockmove['id']?>][<?=$detail['varid']?>]" name="ctotalpcs[<?=$stockmove['id']?>][<?=$detail['varid']?>]" value="<?= $detail['inputqty']; ?>" max="<?= $detail['qty']; ?>" required />
                                            </td>
                                            <td>
                                                <input hidden type="number" class="uk-input" id="cbprice[<?=$stockmove['id']?>][<?=$detail['varid']?>]" name="cbprice[<?=$stockmove['id']?>][<?=$detail['varid']?>]" value="<?= $detail['wholesale']; ?>" required />
                                                <div><?= $detail['wholesale']; ?></div>
                                            </td>
                                            <td id="csubtotal<?=$stockmove['id']?><?=$detail['varid']?>" class="uk-width-small csubvariant<?=$stockmove['id']?>"><?= (Int)$detail['wholesale'] * (Int)$detail['inputqty']; ?></td>
                                        </tr>

                                        <script type="text/javascript">
                                            var cqty<?=$stockmove['id']?><?=$detail['varid']?>         = document.getElementById('ctotalpcs[<?=$stockmove['id']?>][<?=$detail['varid']?>]');
                                            var cprice<?=$stockmove['id']?><?=$detail['varid']?>       = document.getElementById('cbprice[<?=$stockmove['id']?>][<?=$detail['varid']?>]');
                                            var csubtotal<?=$stockmove['id']?><?=$detail['varid']?>    = document.getElementById('csubtotal<?=$stockmove['id']?><?=$detail['varid']?>');
                                            
                                            cqty<?=$stockmove['id']?><?=$detail['varid']?>.addEventListener('change', ctotalprice<?=$stockmove['id']?><?=$detail['varid']?>);
                                            cprice<?=$stockmove['id']?><?=$detail['varid']?>.addEventListener('change', ctotalprice<?=$stockmove['id']?><?=$detail['varid']?>);

                                            function ctotalprice<?=$stockmove['id']?><?=$detail['varid']?>() {
                                                csubtotal<?=$stockmove['id']?><?=$detail['varid']?>.innerHTML = cqty<?=$stockmove['id']?><?=$detail['varid']?>.value * cprice<?=$stockmove['id']?><?=$detail['varid']?>.value;
                                            }
                                        </script>
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

                            <!-- Script Confirm -->
                            <script type="text/javascript">
                                var totalcount<?=$stockmove['id']?> = document.getElementById('ctableproduct<?=$stockmove['id']?>');
                                // Count Total Price
                                let totalpurchase<?=$stockmove['id']?> = new MutationObserver(mutationRecords<?=$stockmove['id']?> => {
                                    var cprices<?=$stockmove['id']?> = document.querySelectorAll(".csubvariant<?=$stockmove['id']?>");
                                    var csubarr<?=$stockmove['id']?> = [];

                                    for (i = 0; i < cprices<?=$stockmove['id']?>.length; i++) {
                                        cprice<?=$stockmove['id']?> = Number(cprices<?=$stockmove['id']?>[i].innerText);
                                        csubarr<?=$stockmove['id']?>.push(cprice<?=$stockmove['id']?>);
                                    }

                                    if (csubarr<?=$stockmove['id']?>.length === 0) {
                                        document.getElementById('cfinalprice<?=$stockmove['id']?>').innerHTML = 0;
                                    } else {
                                        var csubtotalvar<?=$stockmove['id']?> = csubarr<?=$stockmove['id']?>.reduce(function(a, b){ return a + b; });
                                        document.getElementById('cfinalprice<?=$stockmove['id']?>').innerHTML = 'Rp. ' + csubtotalvar<?=$stockmove['id']?> + ',-';
                                    }
                                });
                                
                                totalpurchase<?=$stockmove['id']?>.observe(totalcount<?=$stockmove['id']?>, {
                                    childList: true, // observe direct children
                                    subtree: true, // and lower descendants too
                                    characterDataOldValue: true // pass old data to callback
                                });
                            </script>
                            <!-- Script Confirm End -->
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php }
} ?>
<!-- Modal Confirm End -->

<!-- Modal Detail -->
<?php
$created    = lang('Global.smovecreated');
$sent       = lang('Global.smovesent');
$pending    = lang('Global.smovepending');
$success    = lang('Global.smoveaccepted');
$cancel     = lang('Global.smovecanceled');

foreach ($stockmovedata as $stockmove) { ?>
    <div uk-modal class="uk-flex-top uk-modal-container" id="detail<?= $stockmove['id'] ?>">
        <div class="uk-modal-dialog uk-margin-auto-vertical">
            <div class="uk-modal-content">
                <div class="uk-modal-header">
                    <div class="uk-child-width-1-2" uk-grid>
                        <div>
                            <h5 class="uk-modal-title" id="detail<?= $stockmove['id'] ?>" ><?=lang('Global.detail')?></h5>
                        </div>
                        <div class="uk-text-right">
                            <div class="uk-child-width-1-6 uk-flex-right" uk-grid>
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
                                <?php if ($stockmove['status'] === "0") {
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
                                ?>
                            </div>
                        </div>
                        
                        <div class="uk-margin">
                            <label class="uk-form-label"><?=lang('Global.date')?></label>
                            <div class="uk-form-controls"><?= date('l, d M Y, H:i:s', strtotime($stockmove['date'])); ?></div>
                        </div>
                        
                        <div class="uk-margin">
                            <label class="uk-form-label">No. <?=lang('Global.invoice')?></label>
                            <div class="uk-form-controls" id="invoice<?= $stockmove['id'] ?>"></div>
                        </div>

                        <div class="uk-margin">
                            <label class="uk-form-label"><?=lang('Global.origin')?></label>
                            <div class="uk-form-controls"><?= $stockmovedata[$stockmove['id']]['origin'] ?></div>
                        </div>

                        <div class="uk-margin">
                            <label class="uk-form-label"><?=lang('Global.destination')?></label>
                            <div class="uk-form-controls"><?= $stockmovedata[$stockmove['id']]['destination'] ?></div>
                        </div>
                    </div>

                    <div class="uk-divider-icon"></div>
                    
                    <!-- <div class="uk-overflow-auto"> -->
                        <table class="uk-table uk-table-justify uk-table-middle uk-table-divider uk-table-small" style="background-color: #fff; color: #000;">
                            <thead>
                                <tr>
                                    <th class="uk-text-emphasis">SKU</th>
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
                                        <td><?= $detail['sku']; ?></td>
                                        <td><?= $detail['productname']; ?></td>
                                        <td><?= $detail['variantname']; ?></td>
                                        <td><?= $detail['inputqty']; ?> Pcs</td>
                                        <td><?= $detail['wholesale']; ?></td>
                                        <td><?= (Int)$detail['wholesale'] * (Int)$detail['inputqty']; ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td><?= lang('Global.totalMovement'); ?></td>
                                    <td></td>
                                    <td></td>
                                    <td><?= $stockmove['totalqty'] ?> Pcs</td>
                                    <td></td>
                                    <td><?= "Rp ".number_format($stockmove['totalwholesale'],0,',','.'); ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    <!-- </div> -->
                </div>
            </div>
        </div>
    </div>

    <script>
        // Date In Indonesia
        var publishupdate   = "<?= $stockmove['date'] ?>";
        var thatdate        = publishupdate.split( /[- :]/ );
        thatdate[1]--;
        var publishthatdate = new Date( ...thatdate );
        var publishyear     = publishthatdate.getFullYear();
        var publishmonth    = publishthatdate.getMonth();
        var publishdate     = publishthatdate.getDate();
        // var publishday      = publishthatdate.getDay();

        switch(publishmonth) {
            case 0: publishmonth   = "01"; break;
            case 1: publishmonth   = "02"; break;
            case 2: publishmonth   = "03"; break;
            case 3: publishmonth   = "04"; break;
            case 4: publishmonth   = "05"; break;
            case 5: publishmonth   = "06"; break;
            case 6: publishmonth   = "07"; break;
            case 7: publishmonth   = "08"; break;
            case 8: publishmonth   = "09"; break;
            case 9: publishmonth   = "10"; break;
            case 10: publishmonth  = "11"; break;
            case 11: publishmonth  = "12"; break;
        }

        var invoice         =   "SM" + publishyear + publishmonth + publishdate + "<?= $stockmove['id'] ?>"
        document.getElementById("invoice<?= $stockmove['id'] ?>").innerHTML = invoice;
    </script>
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
                                        <option disabled><?=lang('Global.origin')?></option>
                                        <?php foreach ($outlets as $outlet) {
                                            if ($outlet['id'] === $stockmove['originid']) {
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
                                    <select class="uk-select" name="destination" required>
                                        <option disabled><?=lang('Global.destination')?></option>
                                        <?php foreach ($outlets as $outlet) {
                                            if ($outlet['id'] === $stockmove['destinationid']) {
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
                                    <input type="text" class="uk-input" id="prodname<?= $stockmove['id'] ?>" name="prodname" placeholder="<?=lang('Global.product')?>">
                                </div>
                            </div>

                            <div id="tabvar<?= $stockmove['id'] ?>"></div>

                            <div class="uk-margin-small" uk-grid>
                                <div class="uk-flex uk-flex-middle uk-flex-center uk-width-1-5 uk-text-center">
                                    <div class="">SKU</div>
                                </div>
                                <div class="uk-flex uk-flex-middle uk-flex-center uk-width-1-5 uk-text-center">
                                    <div class=""><?= lang('Global.product') ?></div>
                                </div>
                                <div class="uk-flex uk-flex-middle uk-flex-center uk-width-1-5 uk-text-center">
                                    <div class=""><?= lang('Global.quantity') ?></div>
                                </div>
                                <div class="uk-flex uk-flex-middle uk-flex-center uk-width-1-5 uk-text-center">
                                    <div class=""><?= lang('Global.capitalPrice') ?></div>
                                </div>
                                <div class="uk-flex uk-flex-middle uk-flex-center uk-width-1-5 uk-text-center">
                                    <div class=""><?= lang('Global.total') ?></div>
                                </div>
                            </div>

                            <!-- Autocomplete Product Edit Purchase -->
                            <script type="text/javascript">
                                $(function() {
                                    $("#prodname<?= $stockmove['id'] ?>").autocomplete({
                                        source: productList,
                                        select: function(e, i) {
                                            var data = {
                                                'id'        : i.item.idx,
                                                'outletid'  : <?= $stockmove['originid'] ?>
                                            };
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
                                                    document.getElementById('tabvar<?= $stockmove['id'] ?>').removeAttribute('hidden');
                                                    var elements = document.getElementById('variantliste');
                                                    if (elements){
                                                        elements.remove();
                                                    }
                                                    var products = document.getElementById('tabvar<?= $stockmove['id'] ?>');
                                                    
                                                    var productgrid = document.createElement('div');
                                                    productgrid.setAttribute('id', 'variantliste');
                                                    productgrid.setAttribute('class', 'uk-padding uk-padding-remove-vertical');
                                                    productgrid.setAttribute('uk-grid', '');

                                                    variantarray<?=$stockmove['id']?> = arguments[0];

                                                    for (x in variantarray<?=$stockmove['id']?>) {
                                                        //alert(variantarray<?=$stockmove['id']?>[k]['name']);
                                                        var skucontainer = document.createElement('div');
                                                        skucontainer.setAttribute('class', 'uk-flex uk-flex-middle uk-width-1-4 uk-margin-small');
                                                                                        
                                                        var skuvar = document.createElement('div');
                                                        skuvar.setAttribute('class','');
                                                        skuvar.innerHTML = variantarray<?=$stockmove['id']?>[x]['sku'];

                                                        var varcontainer = document.createElement('div');
                                                        varcontainer.setAttribute('class', 'uk-flex uk-flex-middle uk-width-1-4 uk-margin-small');
                                                                                        
                                                        var varname = document.createElement('div');
                                                        varname.setAttribute('class','');
                                                        varname.innerHTML = variantarray<?=$stockmove['id']?>[x]['name'];

                                                        var stockcontainer = document.createElement('div');
                                                        stockcontainer.setAttribute('class', 'uk-flex uk-flex-center uk-flex-middle uk-width-1-4 uk-margin-small');
                                                                                        
                                                        var stock = document.createElement('div');
                                                        stock.setAttribute('class','');
                                                        stock.innerHTML = variantarray<?=$stockmove['id']?>[x]['qty'];

                                                        var cartcontainer = document.createElement('div');
                                                        cartcontainer.setAttribute('class', 'uk-flex uk-flex-center uk-flex-middle uk-width-1-4 uk-margin-small');

                                                        var cart = document.createElement('a');
                                                        cart.setAttribute('class', 'uk-icon-button');
                                                        cart.setAttribute('uk-icon', 'cart');
                                                        cart.setAttribute('onclick', 'createVare<?=$stockmove['id']?>('+variantarray<?=$stockmove['id']?>[x]['id']+')');

                                                        skucontainer.appendChild(skuvar);
                                                        varcontainer.appendChild(varname);
                                                        stockcontainer.appendChild(stock);
                                                        cartcontainer.appendChild(cart);
                                                        productgrid.appendChild(skucontainer);
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
                                function createVare<?=$stockmove['id']?>(id) {
                                    for (x in variantarray<?=$stockmove['id']?>) {
                                        if (variantarray<?=$stockmove['id']?>[x]['id'] == id) {
                                            document.getElementById('variantliste').remove();
                                            var eelemexist = document.getElementById('eproduct<?=$stockmove['id']?>'+variantarray<?=$stockmove['id']?>[x]['id']);
                                            document.getElementById('tabvar<?= $stockmove['id'] ?>').setAttribute('hidden', '');
                                            var count = 1;
                                            if ( $( "#eproduct<?=$stockmove['id']?>"+variantarray<?=$stockmove['id']?>[x]['id'] ).length ) {
                                                alert('<?=lang('Global.readyAdd');?>');
                                            } else {
                                                let minval = count;
                                                var eprods = document.getElementById('tableprod<?= $stockmove['id'] ?>');
                                                                            
                                                var epgrid = document.createElement('div');
                                                epgrid.setAttribute('id', 'eproduct<?=$stockmove['id']?>'+variantarray<?=$stockmove['id']?>[x]['id']);
                                                epgrid.setAttribute('class', 'uk-margin-small');
                                                epgrid.setAttribute('uk-grid', '');

                                                var evskucontainer = document.createElement('div');
                                                evskucontainer.setAttribute('class', 'uk-flex uk-flex-middle uk-flex-center uk-width-1-5');
                                                                                
                                                var evsku = document.createElement('div');
                                                evsku.setAttribute('id','var'+variantarray<?=$stockmove['id']?>[x]['id']);
                                                evsku.setAttribute('class','');
                                                evsku.innerHTML = variantarray<?=$stockmove['id']?>[x]['sku'];

                                                var evcontainer = document.createElement('div');
                                                evcontainer.setAttribute('class', 'uk-flex uk-flex-middle uk-width-1-5');
                                                                                
                                                var evname = document.createElement('div');
                                                evname.setAttribute('id','var'+variantarray<?=$stockmove['id']?>[x]['id']);
                                                evname.setAttribute('class','');
                                                evname.innerHTML = variantarray<?=$stockmove['id']?>[x]['name'];

                                                var etcontainer = document.createElement('div');
                                                etcontainer.setAttribute('class', 'uk-flex uk-flex-center uk-flex-middle uk-width-1-5');

                                                var etot = document.createElement('input');
                                                etot.setAttribute('type', 'number');
                                                etot.setAttribute('id', "addtotalpcs["+variantarray<?=$stockmove['id']?>[x]['id']+"]");
                                                etot.setAttribute('name', "addtotalpcs["+variantarray<?=$stockmove['id']?>[x]['id']+"]");
                                                etot.setAttribute('max', variantarray<?=$stockmove['id']?>[x]['qty']);
                                                etot.setAttribute('class', 'uk-input');
                                                etot.setAttribute('value', '1');
                                                etot.setAttribute('required', '');

                                                var epieces = document.createElement('div');
                                                epieces.setAttribute('class', 'uk-margin-small-left');
                                                epieces.innerHTML = 'Pcs';

                                                var epricecontainer = document.createElement('div');
                                                epricecontainer.setAttribute('class', 'uk-flex uk-flex-center uk-flex-middle uk-width-1-5');

                                                var eprice = document.createElement('input');
                                                eprice.setAttribute('type', 'number');
                                                eprice.setAttribute('hidden', '');
                                                eprice.setAttribute('id', "addbprice["+variantarray<?=$stockmove['id']?>[x]['id']+"]");
                                                eprice.setAttribute('name', "addbprice["+variantarray<?=$stockmove['id']?>[x]['id']+"]");
                                                eprice.setAttribute('class', 'uk-input');
                                                eprice.setAttribute('value', variantarray<?=$stockmove['id']?>[x]['price']);
                                                eprice.setAttribute('required', '');

                                                var epricediv = document.createElement('div');
                                                epricediv.innerHTML = variantarray<?=$stockmove['id']?>[x]['price'];

                                                var esubtotcontainer = document.createElement('div');
                                                esubtotcontainer.setAttribute('class', 'uk-flex uk-flex-center uk-text-center uk-flex-middle uk-width-1-5');

                                                var esubtotal = document.createElement('div');
                                                esubtotal.setAttribute('id', "esubtotal"+variantarray<?=$stockmove['id']?>[x]['id']+"");
                                                esubtotal.setAttribute('class', 'subvariant');

                                                etotalprice();
                                                etot.addEventListener('change', etotalprice);
                                                eprice.addEventListener('change', etotalprice);

                                                function etotalprice() {
                                                    var varprice = eprice.value;
                                                    var varqty = etot.value;
                                                    var subprice = varprice * varqty;
                                                    esubtotal.setAttribute('value', subprice);
                                                    esubtotal.innerHTML = subprice;
                                                }

                                                evskucontainer.appendChild(evsku);
                                                evcontainer.appendChild(evname);
                                                etcontainer.appendChild(etot);
                                                etcontainer.appendChild(epieces);
                                                epricecontainer.appendChild(eprice);
                                                epricecontainer.appendChild(epricediv);
                                                esubtotcontainer.appendChild(esubtotal);
                                                epgrid.appendChild(evskucontainer);
                                                epgrid.appendChild(evcontainer);
                                                epgrid.appendChild(etcontainer);
                                                epgrid.appendChild(epricecontainer);
                                                epgrid.appendChild(esubtotcontainer);
                                                eprods.appendChild(epgrid);

                                                etot.addEventListener("change", function removeproduct() {
                                                    if (etot.value == '0') {
                                                        epgrid.remove();
                                                    }
                                                });
                                            }
                                            $('#prodname<?= $stockmove['id'] ?>').val('');
                                            return false;
                                        }
                                    }
                                };
                            </script>
                            <!-- Autocomplete Product Edit Purchase End -->

                            <?php
                            $tot[$stockmove['id']] = array();
                            foreach ($stockmovedata[$stockmove['id']]['detail'] as $detailid => $detail) { ?>
                                <div id="eproduct<?=$stockmove['id'].$detail['varid']?>" class="uk-margin-small" uk-grid>
                                    <div class="uk-flex uk-flex-middle uk-flex-center uk-width-1-5">
                                        <div class=""><?= $detail['sku'] ?></div>
                                    </div>
                                    <div class="uk-flex uk-flex-middle uk-flex-center uk-width-1-5">
                                        <div class=""><?= $detail['name'] ?></div>
                                    </div>
                                    <div class="uk-flex uk-flex-middle uk-flex-center uk-width-1-5 uk-text-center">
                                        <input class="uk-input" type="number" id="totalpcs[<?=$detailid?>]" name="totalpcs[<?=$detailid?>]" value="<?= $detail['inputqty'] ?>" min="1" max="<?= $detail['qty'] ?>" required />
                                        <div class="uk-margin-small-left">Pcs</div>
                                    </div>
                                    <div class="uk-flex uk-flex-middle uk-flex-center uk-width-1-5 uk-text-center">
                                        <input hidden class="uk-input" type="number" id="bprice[<?=$detailid?>]" name="bprice[<?=$detailid?>]" value="<?= $detail['wholesale'] ?>" required />
                                        <div><?= $detail['wholesale'] ?></div>
                                    </div>
                                    <div class="uk-flex uk-flex-middle uk-flex-center uk-width-1-5 uk-text-center subvariant<?= $stockmove['id'] ?>" id="subtotal<?= $detailid ?>">
                                        <?= (Int)$detail['wholesale'] * (Int)$detail['inputqty'] ?>
                                    </div>
                                </div>

                                <script type="text/javascript">

                                    var total<?= $detailid ?> = document.getElementById('totalpcs[<?=$detailid?>]');
                                    var price<?= $detailid ?> = document.getElementById('bprice[<?=$detailid?>]');

                                    total<?= $detailid ?>.addEventListener('change', totalprice<?= $detailid ?>);
                                    price<?= $detailid ?>.addEventListener('change', totalprice<?= $detailid ?>);

                                    function totalprice<?= $detailid ?>() {
                                        var subtotal = document.getElementById('subtotal<?= $detailid ?>');
                                        var varprice = price<?= $detailid ?>.value;
                                        var varqty = total<?= $detailid ?>.value;
                                        var subprice = varprice * varqty;
                                        subtotal.setAttribute('value', subprice);
                                        subtotal.innerHTML = subprice;
                                    }
                                </script>
                            <?php
                                $tot[$stockmove['id']][] = (Int)$detail['inputqty'] * (Int)$detail['wholesale'];
                            }
                            $subtot[$stockmove['id']] = array_sum($tot[$stockmove['id']]);
                            ?>
                            
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