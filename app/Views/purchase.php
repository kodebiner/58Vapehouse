<?= $this->extend('layout') ?>

<?= $this->section('extraScript') ?>
<link rel="stylesheet" href="css/code.jquery.com_ui_1.13.2_themes_base_jquery-ui.css">
<script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
<script src="js/code.jquery.com_jquery-3.6.0.js"></script>
<script src="js/code.jquery.com_ui_1.13.2_jquery-ui.js"></script>
<?= $this->endSection() ?>

<?= $this->section('main') ?>

<script>
    productList = [
        <?php foreach ($productlist as $product) {
            echo '{label:"'.$product['name'].'",idx:'.$product['id'].'},';
        } ?>
    ];
    supplierList = [
        <?php foreach ($suppliers as $supplier) {
            echo '{label:"'.$supplier['name'].'",idx:'.$supplier['id'].'},';
        }?>
    ];
</script>

<!-- Page Heading -->
<div class="tm-card-header uk-light">
    <div uk-grid class="uk-flex-middle">
        <div class="uk-width-1-2@m">
            <h3 class="tm-h3"><?=lang('Global.purchaseList')?></h3>
        </div>

        <?php if ($outletPick != null) { ?>
            <!-- Button Trigger Modal Add -->
            <div class="uk-width-1-2@m uk-text-right@m">
                <button type="button" class="uk-button uk-button-primary uk-preserve-color" uk-toggle="target: #tambahdata"><?=lang('Global.addPurchase')?></button>
            </div>
            <!-- Button Trigger Modal Add End -->
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
                        <h5 class="uk-modal-title" id="tambahdata" ><?=lang('Global.addPurchase')?></h5>
                    </div>
                    <div class="uk-text-right">
                        <button class="uk-modal-close uk-icon-button-delete" uk-icon="icon: close;" type="button"></button>
                    </div>
                </div>
            </div>
            <div class="uk-modal-body">
                <form class="uk-form-stacked" role="form" action="stock/createpur" method="post">
                    <?= csrf_field() ?>

                    <div class="uk-margin-bottom">
                        <label class="uk-form-label" for="supplier"><?=lang('Global.supplier')?></label>
                        <div class="uk-form-controls">
                            <input type="text" class="uk-input" id="suppliername" name="suppliername" placeholder="<?=lang('Global.supplier')?>">
                            <input id="supplierid" name="supplierid" hidden required>
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
                            <div class=""><?= lang('Global.pcsPrice') ?></div>
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
    // Autocomplete Supplier
    $(function() {
        $("#suppliername").autocomplete({
            source: supplierList,
            select: function(e, i) {
                $("#supplierid").val(i.item.idx);
            }
        });
    });

    // Autocomplete Product
    $(function() {
        $("#productname").autocomplete({
            source: productList,
            select: function(e, i) {
                var data = { 'id' : i.item.idx };
                $.ajax({
                    url:"stock/product",
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
                            varskucontainer.setAttribute('class', 'uk-flex uk-flex-middle uk-width-1-3 uk-margin-small');
                                                            
                            var varsku = document.createElement('div');
                            varsku.setAttribute('class','');
                            varsku.innerHTML = variantarray[k]['sku'];
                            
                            var varcontainer = document.createElement('div');
                            varcontainer.setAttribute('class', 'uk-flex uk-flex-middle uk-width-1-3 uk-margin-small');
                                                            
                            var varname = document.createElement('div');
                            varname.setAttribute('class','');
                            varname.innerHTML = variantarray[k]['name'];

                            var cartcontainer = document.createElement('div');
                            cartcontainer.setAttribute('class', 'uk-flex uk-flex-center uk-flex-middle uk-width-1-3 uk-margin-small');

                            var cart = document.createElement('a');
                            cart.setAttribute('class', 'uk-icon-button');
                            cart.setAttribute('uk-icon', 'cart');
                            cart.setAttribute('onclick', 'createVar('+variantarray[k]['id']+')');

                            varskucontainer.appendChild(varsku);
                            varcontainer.appendChild(varname);
                            cartcontainer.appendChild(cart);
                            productgrid.appendChild(varskucontainer);
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
                // document.getElementById('variantlist').remove();
                var elemexist = document.getElementById('product'+variantarray[k]['id']);
                // document.getElementById('tablevariant').setAttribute('hidden', '');
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
                    skucontainer.setAttribute('class', 'uk-flex uk-flex-middle uk-width-1-5');
                                                    
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
                    price.setAttribute('id', "bprice["+variantarray[k]['id']+"]");
                    price.setAttribute('name', "bprice["+variantarray[k]['id']+"]");
                    price.setAttribute('class', 'uk-input');
                    price.setAttribute('value', variantarray[k]['price']);
                    price.setAttribute('required', '');

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
                <th class="uk-width-medium"><?=lang('Global.date')?></th>
                <th class="uk-width-small"><?=lang('Global.supplier')?></th>
                <th class="uk-width-small"><?=lang('Global.total')?></th>
                <th class="uk-text-center uk-width-small"><?=lang('Global.status')?></th>
                <th class="uk-text-center uk-width-small"><?=lang('Global.action')?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $success    = lang('Global.success');
            $cancel     = lang('Global.cancel');
            $pending    = lang('Global.pending');

            foreach ($purchases as $purchase) { ?>
                <tr>
                    <td class="uk-width-medium"><?= date('l, d M Y, H:i:s', strtotime($purchase['date'])); ?></td>
                    <td class="uk-width-small"><?= $purchasedata[$purchase['id']]['supplier'] ?></td>

                    <td class="uk-width-small">
                        <?php
                        $prices = array();
                        foreach ($purchasedata[$purchase['id']]['detail'] as $detail) {
                            $prices[] = (Int)$detail['inputqty'] * (Int)$detail['inputprice'];
                        }
                        $sum = array_sum($prices);
                        echo "Rp " . number_format($sum,2,',','.');
                        ?>
                    </td>

                    <td class="uk-text-center uk-width-small">
                        <?php if ($purchase['status'] === "0") {
                            echo '<div class="uk-text-primary" style="border-style: solid; border-color: #1e87f0;">'.$pending.'</div>';
                        } elseif ($purchase['status'] === "1") {
                            echo '<div class="uk-text-success" style="border-style: solid; border-color: #32d296;">'.$success.'</div>';
                        } elseif ($purchase['status'] === "2") {
                            echo '<div class="uk-text-danger" style="border-style: solid; border-color: #f0506e;">'.$cancel.'</div>';
                        } ?>
                    </td>

                    <?php if ($purchase['status'] === "0") { ?>
                        <td>
                            <div class="uk-child-width-auto uk-flex-center uk-flex-middle uk-grid-row-small uk-grid-column-small uk-text-center" uk-grid>
                                <!-- Button Trigger Modal Detail -->
                                <div class="">
                                    <a uk-icon="eye" class="uk-icon-link" uk-toggle="target: #detail<?= $purchase['id'] ?>"></a>
                                </div>
                                <!-- End Of Button Trigger Modal Detail -->

                                <?php if ($outletPick != null) { ?>
                                    <!-- Button Trigger Modal Edit -->
                                    <div class="">
                                        <a class="uk-icon-button" uk-icon="pencil" uk-toggle="target: #editdata<?= $purchase['id'] ?>"></a>
                                    </div>
                                    <!-- End Of Button Trigger Edit Detail -->

                                    <!-- Button Confirmation -->
                                    <div>
                                        <a class="uk-icon-button-success" uk-icon="check" uk-toggle="target: #savedata<?= $purchase['id'] ?>"></a>
                                    </div>
                                    <!-- End Of Button Confirmation -->

                                    <!-- Button Cancel -->
                                    <div>
                                        <form class="uk-form-stacked" role="form" action="stock/cancelpur/<?= $purchase['id'] ?>" method="post">
                                            <button type="submit" uk-icon="close" class="uk-icon-button-delete" onclick="return confirm('<?=lang('Global.cancelConfirm')?>')"></button>
                                        </form>
                                    </div>
                                    <!-- End Of Button Cancel -->
                                <?php } ?>
                            </div>
                        </td>
                    <?php } else { ?>
                        <td class="uk-text-center uk-width-small">
                            <!-- Button Trigger Modal Detail -->
                            <div class="uk-text-center">
                                <a uk-icon="eye" class="uk-icon-link" uk-toggle="target: #detail<?= $purchase['id'] ?>"></a>
                            </div>
                            <!-- End Of Button Trigger Modal Detail -->
                        </td>
                    <?php } ?>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <div>
        <?= $pager->links('purchase', 'front_full') ?>
    </div>
</div>
<!-- Table Content End -->

<!-- Modal Confirm -->
<?php foreach ($purchases as $purchase) {
    if ($purchase['status'] === "0") { ?>
        <div uk-modal class="uk-flex-top uk-modal-container" id="savedata<?= $purchase['id'] ?>">
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
                        <form class="uk-form-stacked" role="form" action="stock/confirm/<?= $purchase['id'] ?>" method="post">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id" value="<?= $purchase['id']; ?>">
                            
                            <table class="uk-table uk-table-justify uk-table-middle uk-table-divider" style="background-color: #fff;">
                                <thead>
                                    <tr>
                                        <th class="uk-width-small uk-text-emphasis">SKU</th>
                                        <th class="uk-width-small uk-text-emphasis"><?=lang('Global.product')?></th>
                                        <th class="uk-width-small uk-text-emphasis"><?=lang('Global.variant')?></th>
                                        <th class="uk-width-small uk-text-emphasis"><?=lang('Global.totalPurchase')?></th>
                                        <th class="uk-width-medium uk-text-emphasis"><?=lang('Global.pcsPrice')?></th>
                                        <th class="uk-width-small uk-text-emphasis"><?=lang('Global.adjprice')?></th>
                                        <th class="uk-width-small uk-text-emphasis"><?=lang('Global.total')?></th>
                                    </tr>
                                </thead>
                                <tbody id="ctableproduct<?=$purchase['id']?>">
                                    <?php
                                    $subtotalpurchase = array();
                                    foreach ($purchasedata[$purchase['id']]['detail'] as $detail) {
                                        $subtotalpurchase[] = (Int)$detail['inputqty'] * (Int)$detail['inputprice']; ?>
                                        <tr>
                                            <td><?= $detail['sku']; ?></td>
                                            <td><?= $detail['productname']; ?></td>
                                            <td><?= $detail['variantname']; ?></td>
                                                
                                            <td>
                                                <input type="number" class="uk-input" id="ctotalpcs[<?=$purchase['id']?>][<?=$detail['varid']?>]" name="ctotalpcs[<?=$purchase['id']?>][<?=$detail['varid']?>]" value="<?= $detail['inputqty']; ?>" required />
                                            </td>
                                            <td>
                                                <input type="number" class="uk-input" id="cbprice[<?=$purchase['id']?>][<?=$detail['varid']?>]" name="cbprice[<?=$purchase['id']?>][<?=$detail['varid']?>]" value="<?= $detail['inputprice']; ?>" required />
                                            </td>
                                            <td id="adjprice<?=$purchase['id']?><?=$detail['varid']?>">
                                                <?php 
                                                floor((((Int)$detail['hargaold'] * (Int)$detail['qty']) + ((Int)$detail['inputprice'] * (Int)$detail['inputqty'])) / ((Int)$detail['inputqty'] + (Int)$detail['qty']));
                                                ?>

                                                <script type="text/javascript">
                                                    var cqty<?=$purchase['id']?><?=$detail['varid']?>         = document.getElementById('ctotalpcs[<?=$purchase['id']?>][<?=$detail['varid']?>]');
                                                    var cprice<?=$purchase['id']?><?=$detail['varid']?>       = document.getElementById('cbprice[<?=$purchase['id']?>][<?=$detail['varid']?>]');
                                                    var adjprice<?=$purchase['id']?><?=$detail['varid']?>     = document.getElementById('adjprice<?=$purchase['id']?><?=$detail['varid']?>');

                                                    cqty<?=$purchase['id']?><?=$detail['varid']?>.addEventListener('change', adjustprice<?=$purchase['id']?><?=$detail['varid']?>);
                                                    cprice<?=$purchase['id']?><?=$detail['varid']?>.addEventListener('change', adjustprice<?=$purchase['id']?><?=$detail['varid']?>);

                                                    function adjustprice<?=$purchase['id']?><?=$detail['varid']?>() {
                                                        adjprice<?=$purchase['id']?><?=$detail['varid']?>.innerHTML = Math.floor((<?= (Int)$detail['hargaold'] * (Int)$detail['qty'] ?> + (cqty<?=$purchase['id']?><?=$detail['varid']?>.value * cprice<?=$purchase['id']?><?=$detail['varid']?>.value)) / (<?= $detail['qty'] ?> + cqty<?=$purchase['id']?><?=$detail['varid']?>.value));
                                                    }
                                                </script>
                                            </td>
                                            <td id="csubtotal<?=$purchase['id']?><?=$detail['varid']?>" class="uk-width-small csubvariant<?=$purchase['id']?>"><?= (Int)$detail['inputprice'] * (Int)$detail['inputqty']; ?></td>
                                        </tr>

                                        <script type="text/javascript">
                                            var cqty<?=$purchase['id']?><?=$detail['varid']?>         = document.getElementById('ctotalpcs[<?=$purchase['id']?>][<?=$detail['varid']?>]');
                                            var cprice<?=$purchase['id']?><?=$detail['varid']?>       = document.getElementById('cbprice[<?=$purchase['id']?>][<?=$detail['varid']?>]');
                                            var csubtotal<?=$purchase['id']?><?=$detail['varid']?>    = document.getElementById('csubtotal<?=$purchase['id']?><?=$detail['varid']?>');
                                            
                                            cqty<?=$purchase['id']?><?=$detail['varid']?>.addEventListener('change', ctotalprice<?=$purchase['id']?><?=$detail['varid']?>);
                                            cprice<?=$purchase['id']?><?=$detail['varid']?>.addEventListener('change', ctotalprice<?=$purchase['id']?><?=$detail['varid']?>);

                                            function ctotalprice<?=$purchase['id']?><?=$detail['varid']?>() {
                                                csubtotal<?=$purchase['id']?><?=$detail['varid']?>.innerHTML = cqty<?=$purchase['id']?><?=$detail['varid']?>.value * cprice<?=$purchase['id']?><?=$detail['varid']?>.value;
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
                                        <div class="tm-h2 uk-text-bold" id="cfinalprice<?=$purchase['id']?>">Rp <?= array_sum($subtotalpurchase) ?>,-</div>
                                    </div>
                                </div>
                                <div class="uk-margin uk-flex uk-flex-center">
                                    <button type="submit" class="uk-button uk-button-primary uk-button-large uk-text-center" style="border-radius: 8px; width: 540px;"><?=lang('Global.save')?></button>
                                </div>
                            </div>

                            <!-- Script Confirm -->
                            <script type="text/javascript">
                                var totalcount<?=$purchase['id']?> = document.getElementById('ctableproduct<?=$purchase['id']?>');
                                // Count Total Price
                                let totalpurchase<?=$purchase['id']?> = new MutationObserver(mutationRecords<?=$purchase['id']?> => {
                                    var cprices<?=$purchase['id']?> = document.querySelectorAll(".csubvariant<?=$purchase['id']?>");
                                    var csubarr<?=$purchase['id']?> = [];

                                    for (i = 0; i < cprices<?=$purchase['id']?>.length; i++) {
                                        cprice<?=$purchase['id']?> = Number(cprices<?=$purchase['id']?>[i].innerText);
                                        csubarr<?=$purchase['id']?>.push(cprice<?=$purchase['id']?>);
                                    }

                                    if (csubarr<?=$purchase['id']?>.length === 0) {
                                        document.getElementById('cfinalprice<?=$purchase['id']?>').innerHTML = 0;
                                    } else {
                                        var csubtotalvar<?=$purchase['id']?> = csubarr<?=$purchase['id']?>.reduce(function(a, b){ return a + b; });
                                        document.getElementById('cfinalprice<?=$purchase['id']?>').innerHTML = 'Rp. ' + csubtotalvar<?=$purchase['id']?> + ',-';
                                    }
                                });
                                
                                totalpurchase<?=$purchase['id']?>.observe(totalcount<?=$purchase['id']?>, {
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
$success    = lang('Global.success');
$cancel     = lang('Global.cancel');
$pending    = lang('Global.pending');

foreach ($purchases as $purchase) { ?>
    <div uk-modal class="uk-flex-top uk-modal-container" id="detail<?= $purchase['id'] ?>">
        <div class="uk-modal-dialog uk-margin-auto-vertical">
            <div class="uk-modal-content">
                <div class="uk-modal-header">
                    <div class="uk-child-width-1-2" uk-grid>
                        <div>
                            <h5 class="uk-modal-title" id="detail<?= $purchase['id'] ?>" ><?=lang('Global.detail')?></h5>
                        </div>
                        <div class="uk-text-right">
                            <button class="uk-modal-close uk-icon-button-delete" uk-icon="icon: close;" type="button"></button>
                        </div>
                    </div>
                </div>
                <div class="uk-modal-body">
                    <div class="uk-form-horizontal">
                        <div class="uk-margin">
                            <div class="tm-h2 uk-h4"><?=lang('Global.purchaseInfo')?></div>
                        </div>

                        <div class="uk-margin">
                            <label class="uk-form-label"><?=lang('Global.status')?></label>
                            <div class="uk-form-controls">
                                <?php if ($purchase['status'] === "0") {
                                    echo '<span class="uk-text-primary" style="padding: 5px; border-style: solid; border-color: #1e87f0;">'.$pending.'</span>';
                                } elseif ($purchase['status'] === "1") {
                                    echo '<span class="uk-text-success uk-width-auto" style="padding: 5px; border-style: solid; border-color: #32d296;">'.$success.'</span>';
                                } elseif ($purchase['status'] === "2") {
                                    echo '<span class="uk-text-danger uk-width-auto" style="padding: 5px; border-style: solid; border-color: #f0506e;">'.$cancel.'</span>';
                                } ?>
                            </div>
                        </div>
                        
                        <div class="uk-margin">
                            <label class="uk-form-label"><?=lang('Global.date')?></label>
                            <div class="uk-form-controls"><?= date('l, d M Y, H:i:s', strtotime($purchase['date'])); ?></div>
                        </div>

                        <div class="uk-margin">
                            <label class="uk-form-label"><?=lang('Global.outlet')?></label>
                            <div class="uk-form-controls"><?= $purchasedata[$purchase['id']]['outlet'] ?></div>
                        </div>

                        <div class="uk-margin">
                            <label class="uk-form-label"><?=lang('Global.employee')?></label>
                            <div class="uk-form-controls"><?= $purchasedata[$purchase['id']]['user'] ?></div>
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
                                    <th class="uk-text-emphasis"><?=lang('Global.totalPurchase')?></th>
                                    <?php if ($purchase['status'] != "0") { ?>
                                        <th class="uk-text-emphasis"><?=lang('Global.oldprice')?></th>
                                        <th class="uk-text-emphasis"><?=lang('Global.adjprice')?></th>
                                        <th class="uk-text-emphasis"><?=lang('Global.diffprice')?></th>
                                    <?php } ?>
                                    <th class="uk-text-emphasis"><?=lang('Global.pcsPrice')?></th>
                                    <th class="uk-text-emphasis"><?=lang('Global.total')?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($purchasedata[$purchase['id']]['detail'] as $detail) { ?>
                                    <tr>
                                        <td><?= $detail['sku']; ?></td>
                                        <td><?= $detail['productname']; ?></td>
                                        <td><?= $detail['variantname']; ?></td>
                                        <td><?= $detail['inputqty']; ?> Pcs</td>
                                        <?php if ($purchase['status'] != "0") {
                                            $oldprice   = $detail['hargaold'];
                                            $newprice   = $detail['hargadasar'];
                                            $diffprice  = (Int)$newprice - (Int)$oldprice; ?>
                                            <td>
                                                <?= $oldprice ?>
                                            </td>
                                            <td>
                                                <?= $newprice ?>
                                            </td>
                                            <td>
                                                <?= $diffprice ?>
                                            </td>
                                        <?php } ?>
                                            
                                        <td><?= $detail['inputprice']; ?></td>
                                        <td><?= (Int)$detail['inputprice'] * (Int)$detail['inputqty']; ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td><?= lang('Global.totalPurchase'); ?></td>
                                    <td></td>
                                    <td></td>
                                    <td><?= $purchasedata[$purchase['id']]['totalqty'] ?> Pcs</td>
                                    <td></td>
                                    <?php if ($purchase['status'] != "0") { ?>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    <?php } ?>
                                    <td><?= "Rp ".number_format($purchasedata[$purchase['id']]['totalprice'],0,',','.'); ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    <!-- </div> -->
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<!-- Modal Detail End -->

<!-- Modal Edit -->
<?php foreach ($purchases as $purchase) {
    if ($purchase['status'] === "0") { ?>
        <div uk-modal class="uk-flex-top uk-modal-container" id="editdata<?= $purchase['id'] ?>">
            <div class="uk-modal-dialog uk-margin-auto-vertical">
                <div class="uk-modal-content">
                    <div class="uk-modal-header">
                        <div class="uk-child-width-1-2" uk-grid>
                            <div>
                                <h5 class="uk-modal-title" id="editdata<?= $purchase['id'] ?>"><?=lang('Global.updateData')?></h5>
                            </div>
                            <div class="uk-text-right">
                                <button class="uk-modal-close uk-icon-button-delete" uk-icon="icon: close;" type="button"></button>
                            </div>
                        </div>
                    </div>

                    <div class="uk-modal-body">
                        <form class="uk-form-stacked" role="form" action="stock/updatepur/<?= $purchase['id'] ?>" method="post">
                            <?= csrf_field() ?>

                            <div class="uk-margin-bottom">
                                <label class="uk-form-label" for="supplier"><?=lang('Global.supplier')?></label>
                                <div class="uk-form-controls">
                                    <input class="uk-input" id="suppliername<?= $purchase['id'] ?>" name="suppliername<?= $purchase['id'] ?>" value="<?= $purchasedata[$purchase['id']]['supplier'] ?>" required>
                                    <input id="supplierid<?= $purchase['id'] ?>" name="supplierid<?= $purchase['id'] ?>" value="<?= $purchasedata[$purchase['id']]['supplierid'] ?>" hidden>
                                </div>
                            </div>

                            <!-- Autocomplete Supplier Edit Purchase -->
                            <script type="text/javascript">
                                $(function() {
                                    $("#suppliername<?= $purchase['id'] ?>").autocomplete({
                                        source: supplierList,
                                        select: function(e, i) {
                                            $("#supplierid<?= $purchase['id'] ?>").val(i.item.idx);
                                        }
                                    });
                                });
                            </script>
                            <!-- Autocomplete Supplier Edit Purchase End -->

                            <div class="uk-margin-bottom">
                                <label class="uk-form-label" for="product"><?=lang('Global.product')?></label>
                                <div class="uk-form-controls">
                                    <input type="text" class="uk-input" id="prodname<?= $purchase['id'] ?>" name="prodname" placeholder="<?=lang('Global.product')?>">
                                </div>
                            </div>

                            <div id="tabvar<?= $purchase['id'] ?>"></div>

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
                                    <div class=""><?= lang('Global.pcsPrice') ?></div>
                                </div>
                                <div class="uk-flex uk-flex-middle uk-flex-center uk-width-1-5 uk-text-center">
                                    <div class=""><?= lang('Global.total') ?></div>
                                </div>
                            </div>

                            <!-- Autocomplete Product Edit Purchase -->
                            <script type="text/javascript">
                                $(function() {
                                    $("#prodname<?= $purchase['id'] ?>").autocomplete({
                                        source: productList,
                                        select: function(e, i) {
                                            var data = {
                                                'id'        : i.item.idx,
                                                'outletid'  : <?= $purchase['outletid'] ?>
                                            };
                                            $.ajax({
                                                url:"stock/product",
                                                method:"POST",
                                                data: data,
                                                dataType: "json",
                                                error:function() {
                                                    console.log('error', arguments);
                                                },
                                                success:function() {
                                                    console.log('success', arguments);
                                                    document.getElementById('tabvar<?= $purchase['id'] ?>').removeAttribute('hidden');
                                                    var elements = document.getElementById('variantliste');
                                                    if (elements){
                                                        elements.remove();
                                                    }
                                                    var products = document.getElementById('tabvar<?= $purchase['id'] ?>');
                                                    
                                                    var productgrid = document.createElement('div');
                                                    productgrid.setAttribute('id', 'variantliste');
                                                    productgrid.setAttribute('class', 'uk-padding uk-padding-remove-vertical');
                                                    productgrid.setAttribute('uk-grid', '');

                                                    variantarray<?=$purchase['id']?> = arguments[0];

                                                    for (x in variantarray<?=$purchase['id']?>) {
                                                        //alert(variantarray<?=$purchase['id']?>[k]['name']);
                                                        var skucontainer = document.createElement('div');
                                                        skucontainer.setAttribute('class', 'uk-flex uk-flex-middle uk-width-1-3 uk-margin-small');
                                                                                        
                                                        var skuvar = document.createElement('div');
                                                        skuvar.setAttribute('class','');
                                                        skuvar.innerHTML = variantarray<?=$purchase['id']?>[x]['sku'];

                                                        var varcontainer = document.createElement('div');
                                                        varcontainer.setAttribute('class', 'uk-flex uk-flex-middle uk-width-1-3 uk-margin-small');
                                                                                        
                                                        var varname = document.createElement('div');
                                                        varname.setAttribute('class','');
                                                        varname.innerHTML = variantarray<?=$purchase['id']?>[x]['name'];

                                                        var cartcontainer = document.createElement('div');
                                                        cartcontainer.setAttribute('class', 'uk-flex uk-flex-center uk-flex-middle uk-width-1-3 uk-margin-small');

                                                        var cart = document.createElement('a');
                                                        cart.setAttribute('class', 'uk-icon-button');
                                                        cart.setAttribute('uk-icon', 'cart');
                                                        cart.setAttribute('onclick', 'createVare<?=$purchase['id']?>('+variantarray<?=$purchase['id']?>[x]['id']+')');

                                                        skucontainer.appendChild(skuvar);
                                                        varcontainer.appendChild(varname);
                                                        cartcontainer.appendChild(cart);
                                                        productgrid.appendChild(skucontainer);
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
                                function createVare<?=$purchase['id']?>(id) {
                                    for (x in variantarray<?=$purchase['id']?>) {
                                        if (variantarray<?=$purchase['id']?>[x]['id'] == id) {
                                            // document.getElementById('variantliste').remove();
                                            var eelemexist = document.getElementById('eproduct<?=$purchase['id']?>'+variantarray<?=$purchase['id']?>[x]['id']);
                                            // document.getElementById('tabvar</?= $purchase['id'] ?>').setAttribute('hidden', '');
                                            var count = 1;
                                            if ( $( "#eproduct<?=$purchase['id']?>"+variantarray<?=$purchase['id']?>[x]['id'] ).length ) {
                                                alert('<?=lang('Global.readyAdd');?>');
                                            } else {
                                                let minval = count;
                                                var eprods = document.getElementById('tableprod<?= $purchase['id'] ?>');
                                                                            
                                                var epgrid = document.createElement('div');
                                                epgrid.setAttribute('id', 'eproduct<?=$purchase['id']?>'+variantarray<?=$purchase['id']?>[x]['id']);
                                                epgrid.setAttribute('class', 'uk-margin-small');
                                                epgrid.setAttribute('uk-grid', '');

                                                var evskucontainer = document.createElement('div');
                                                evskucontainer.setAttribute('class', 'uk-flex uk-flex-middle uk-flex-center uk-width-1-5');
                                                                                
                                                var evsku = document.createElement('div');
                                                evsku.setAttribute('id','var'+variantarray<?=$purchase['id']?>[x]['id']);
                                                evsku.setAttribute('class','');
                                                evsku.innerHTML = variantarray<?=$purchase['id']?>[x]['sku'];

                                                var evcontainer = document.createElement('div');
                                                evcontainer.setAttribute('class', 'uk-flex uk-flex-middle uk-width-1-5');
                                                                                
                                                var evname = document.createElement('div');
                                                evname.setAttribute('id','var'+variantarray<?=$purchase['id']?>[x]['id']);
                                                evname.setAttribute('class','');
                                                evname.innerHTML = variantarray<?=$purchase['id']?>[x]['name'];

                                                var etcontainer = document.createElement('div');
                                                etcontainer.setAttribute('class', 'uk-flex uk-flex-center uk-flex-middle uk-width-1-5');

                                                var etot = document.createElement('input');
                                                etot.setAttribute('type', 'number');
                                                etot.setAttribute('id', "addtotalpcs["+variantarray<?=$purchase['id']?>[x]['id']+"]");
                                                etot.setAttribute('name', "addtotalpcs["+variantarray<?=$purchase['id']?>[x]['id']+"]");
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
                                                eprice.setAttribute('id', "addbprice["+variantarray<?=$purchase['id']?>[x]['id']+"]");
                                                eprice.setAttribute('name', "addbprice["+variantarray<?=$purchase['id']?>[x]['id']+"]");
                                                eprice.setAttribute('class', 'uk-input');
                                                eprice.setAttribute('value', variantarray<?=$purchase['id']?>[x]['price']);
                                                eprice.setAttribute('required', '');

                                                var esubtotcontainer = document.createElement('div');
                                                esubtotcontainer.setAttribute('class', 'uk-flex uk-flex-center uk-text-center uk-flex-middle uk-width-1-5');

                                                var esubtotal = document.createElement('div');
                                                esubtotal.setAttribute('id', "esubtotal"+variantarray<?=$purchase['id']?>[x]['id']+"");
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
                                            $('#prodname<?= $purchase['id'] ?>').val('');
                                            return false;
                                        }
                                    }
                                };
                            </script>
                            <!-- Autocomplete Product Edit Purchase End -->

                            <?php
                            $tot[$purchase['id']] = array();
                            foreach ($purchasedata[$purchase['id']]['detail'] as $detailid => $detail) { ?>
                                <div id="eproduct<?=$purchase['id'].$detail['varid']?>" class="uk-margin-small" uk-grid>
                                    <div class="uk-flex uk-flex-middle uk-flex-center uk-width-1-5">
                                        <div class=""><?= $detail['sku'] ?></div>
                                    </div>
                                    <div class="uk-flex uk-flex-middle uk-flex-center uk-width-1-5">
                                        <div class=""><?= $detail['name'] ?></div>
                                    </div>
                                    <div class="uk-flex uk-flex-middle uk-flex-center uk-width-1-5 uk-text-center">
                                        <input class="uk-input" type="number" id="totalpcs[<?=$detailid?>]" name="totalpcs[<?=$detailid?>]" value="<?= $detail['inputqty'] ?>" required />
                                        <div class="uk-margin-small-left">Pcs</div>
                                    </div>
                                    <div class="uk-flex uk-flex-middle uk-flex-center uk-width-1-5 uk-text-center">
                                        <input class="uk-input" type="number" id="bprice[<?=$detailid?>]" name="bprice[<?=$detailid?>]" value="<?= $detail['inputprice'] ?>" required />
                                    </div>
                                    <div class="uk-flex uk-flex-middle uk-flex-center uk-width-1-5 uk-text-center subvariant<?= $purchase['id'] ?>" id="subtotal<?= $detailid ?>">
                                        <?= (Int)$detail['inputprice'] * (Int)$detail['inputqty'] ?>
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
                                $tot[$purchase['id']][] = (Int)$detail['inputqty'] * (Int)$detail['inputprice'];
                            }
                            $subtot[$purchase['id']] = array_sum($tot[$purchase['id']]);
                            ?>
                            
                            <div id="tableprod<?= $purchase['id'] ?>"></div>

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