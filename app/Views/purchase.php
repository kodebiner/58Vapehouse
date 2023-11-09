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
<div uk-modal class="uk-flex-top" id="tambahdata">
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
                        <div class="uk-flex uk-flex-middle uk-flex-center uk-width-1-4 uk-text-center">
                            <div class=""><?= lang('Global.variant') ?></div>
                        </div>
                        <div class="uk-flex uk-flex-middle uk-flex-center uk-width-1-4 uk-text-center">
                            <div class=""><?= lang('Global.quantity') ?></div>
                        </div>
                        <div class="uk-flex uk-flex-middle uk-flex-center uk-width-1-4 uk-text-center">
                            <div class=""><?= lang('Global.pcsPrice') ?></div>
                        </div>
                        <div class="uk-flex uk-flex-middle uk-flex-center uk-width-1-4 uk-text-center">
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
        var supplierList = [
            <?php foreach ($suppliers as $supplier) {
                echo '{label:"'.$supplier['name'].'",idx:'.$supplier['id'].'},';
            }?>
        ];
        $("#suppliername").autocomplete({
            source: supplierList,
            select: function(e, i) {
                $("#supplierid").val(i.item.idx);
            }
        });
    });

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
                            var varcontainer = document.createElement('div');
                            varcontainer.setAttribute('class', 'uk-flex uk-flex-middle uk-width-1-2 uk-margin-small');
                                                            
                            var varname = document.createElement('div');
                            varname.setAttribute('class','');
                            varname.innerHTML = variantarray[k]['name'];

                            var cartcontainer = document.createElement('div');
                            cartcontainer.setAttribute('class', 'uk-flex uk-flex-center uk-flex-middle uk-width-1-2 uk-margin-small');

                            var cart = document.createElement('a');
                            cart.setAttribute('class', 'uk-icon-button');
                            cart.setAttribute('uk-icon', 'cart');
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

                    var vcontainer = document.createElement('div');
                    vcontainer.setAttribute('class', 'uk-flex uk-flex-middle uk-width-1-4');
                                                    
                    var vname = document.createElement('div');
                    vname.setAttribute('id','var'+variantarray[k]['id']);
                    vname.setAttribute('class','');
                    vname.innerHTML = variantarray[k]['name'];

                    var tcontainer = document.createElement('div');
                    tcontainer.setAttribute('class', 'uk-flex uk-flex-center uk-flex-middle uk-width-1-4');

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
                    pricecontainer.setAttribute('class', 'uk-flex uk-flex-center uk-flex-middle uk-width-1-4');

                    var price = document.createElement('input');
                    price.setAttribute('type', 'number');
                    price.setAttribute('id', "bprice["+variantarray[k]['id']+"]");
                    price.setAttribute('name', "bprice["+variantarray[k]['id']+"]");
                    price.setAttribute('class', 'uk-input');
                    price.setAttribute('value', variantarray[k]['price']);
                    price.setAttribute('required', '');

                    var subtotcontainer = document.createElement('div');
                    subtotcontainer.setAttribute('class', 'uk-flex uk-flex-center uk-text-center uk-flex-middle uk-width-1-4');

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

                    vcontainer.appendChild(vname);
                    tcontainer.appendChild(tot);
                    tcontainer.appendChild(pieces);
                    pricecontainer.appendChild(price);
                    subtotcontainer.appendChild(subtotal);
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
                    <td class="uk-width-small">
                        <?php foreach ($suppliers as $supplier) {
                            if ($supplier['id'] === $purchase['supplierid']) {
                                echo $supplier['name'];
                            }
                        } ?>
                    </td>

                    <td class="uk-width-small">
                        <?php
                        $prices = array();
                        foreach ($purchasedetails as $purdet) {
                            if ($purchase['id'] === $purdet['purchaseid']) {
                                $total = (Int)$purdet['qty'] * (Int)$purdet['price'];
                                $prices [] = $total;
                            }
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
                        <td class="uk-child-width-auto uk-flex-center uk-flex-middle uk-grid-row-small uk-grid-column-small uk-text-center" uk-grid>
                            <!-- Button Trigger Modal Detail -->
                            <div class="">
                                <a uk-icon="eye" class="uk-icon-link" uk-toggle="target: #detail<?= $purchase['id'] ?>"></a>
                            </div>
                            <!-- End Of Button Trigger Modal Detail -->

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
<?php foreach ($purchases as $purchase) { ?>
    <div uk-modal class="uk-flex-top" id="savedata<?= $purchase['id'] ?>">
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
                                foreach ($purchasedetails as $purdet) {
                                    if ($purchase['id'] === $purdet['purchaseid']) {
                                        foreach ($variants as $variant) {
                                            foreach ($products as $product) {
                                                if ($variant['id'] === $purdet['variantid'] && $product['id'] === $variant['productid']) {
                                                    $pName  = $product['name'];
                                                    $vName  = $variant['name'];
                                                    $subtotalpurchase[] = (Int)$purdet['qty'] * (Int)$purdet['price']; ?>
                                                    <tr>
                                                        <td><?= $pName; ?></td>
                                                        <td><?= $vName; ?></td>
                                                            
                                                        <td>
                                                            <input type="number" class="uk-input" id="ctotalpcs[<?=$purchase['id']?>][<?=$variant['id']?>]" name="ctotalpcs[<?=$purchase['id']?>][<?=$variant['id']?>]" value="<?= $purdet['qty']; ?>" required />
                                                        </td>
                                                        <td>
                                                            <input type="number" class="uk-input" id="cbprice[<?=$purchase['id']?>][<?=$variant['id']?>]" name="cbprice[<?=$purchase['id']?>][<?=$variant['id']?>]" value="<?= $purdet['price']; ?>" required />
                                                        </td>
                                                        <td id="adjprice<?=$purchase['id']?><?=$variant['id']?>">
                                                            <?php foreach ($stocks as $stock) {
                                                                foreach ($oldstocks as $oldstock) {
                                                                    if (($stock['variantid'] === $variant['id']) && ($oldstock['variantid'] === $variant['id'])) { ?>
                                                                        <?= floor((($oldstock['hargadasar'] * $stock['qty']) + ($purdet['price'] * $purdet['qty'])) / ($stock['qty'] + $purdet['qty'])); ?>

                                                                        <script type="text/javascript">
                                                                            var cqty<?=$purchase['id']?><?=$variant['id']?>         = document.getElementById('ctotalpcs[<?=$purchase['id']?>][<?=$variant['id']?>]');
                                                                            var cprice<?=$purchase['id']?><?=$variant['id']?>       = document.getElementById('cbprice[<?=$purchase['id']?>][<?=$variant['id']?>]');
                                                                            var adjprice<?=$purchase['id']?><?=$variant['id']?>     = document.getElementById('adjprice<?=$purchase['id']?><?=$variant['id']?>');

                                                                            cqty<?=$purchase['id']?><?=$variant['id']?>.addEventListener('change', adjustprice<?=$purchase['id']?><?=$variant['id']?>);
                                                                            cprice<?=$purchase['id']?><?=$variant['id']?>.addEventListener('change', adjustprice<?=$purchase['id']?><?=$variant['id']?>);

                                                                            function adjustprice<?=$purchase['id']?><?=$variant['id']?>() {
                                                                                adjprice<?=$purchase['id']?><?=$variant['id']?>.innerHTML = Math.floor((<?= (Int)$oldstock['hargadasar'] * (Int)$stock['qty'] ?> + (cqty<?=$purchase['id']?><?=$variant['id']?>.value * cprice<?=$purchase['id']?><?=$variant['id']?>.value)) / (<?= $stock['qty'] ?> + cqty<?=$purchase['id']?><?=$variant['id']?>.value));
                                                                            }
                                                                        </script>
                                                                    <?php }
                                                                }
                                                            } ?>
                                                        </td>
                                                        <td id="csubtotal<?=$purchase['id']?><?=$variant['id']?>" class="uk-width-small csubvariant<?=$purchase['id']?>"><?= (Int)$purdet['price'] * (Int)$purdet['qty']; ?></td>
                                                    </tr>

                                                    <script type="text/javascript">
                                                        var cqty<?=$purchase['id']?><?=$variant['id']?>         = document.getElementById('ctotalpcs[<?=$purchase['id']?>][<?=$variant['id']?>]');
                                                        var cprice<?=$purchase['id']?><?=$variant['id']?>       = document.getElementById('cbprice[<?=$purchase['id']?>][<?=$variant['id']?>]');
                                                        var csubtotal<?=$purchase['id']?><?=$variant['id']?>    = document.getElementById('csubtotal<?=$purchase['id']?><?=$variant['id']?>');
                                                        
                                                        cqty<?=$purchase['id']?><?=$variant['id']?>.addEventListener('change', ctotalprice<?=$purchase['id']?><?=$variant['id']?>);
                                                        cprice<?=$purchase['id']?><?=$variant['id']?>.addEventListener('change', ctotalprice<?=$purchase['id']?><?=$variant['id']?>);

                                                        function ctotalprice<?=$purchase['id']?><?=$variant['id']?>() {
                                                            csubtotal<?=$purchase['id']?><?=$variant['id']?>.innerHTML = cqty<?=$purchase['id']?><?=$variant['id']?>.value * cprice<?=$purchase['id']?><?=$variant['id']?>.value;
                                                        }
                                                    </script>
                                                <?php }
                                            }
                                        }
                                    }
                                } ?>
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
<?php } ?>
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
                            <?php foreach ($outlets as $outlet) { ?>
                                <?php if ($outlet['id'] === $purchase['outletid']) { ?>
                                    <div class="uk-form-controls"><?= $outlet['name'] ?></div>
                                <?php } ?>
                            <?php } ?>
                        </div>

                        <div class="uk-margin">
                            <label class="uk-form-label"><?=lang('Global.employee')?></label>
                            <?php foreach ($users as $user) { ?>
                                <?php if ($user->id === $purchase['userid']) { ?>
                                    <div class="uk-form-controls"><?= $user->name ?></div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="uk-divider-icon"></div>
                    
                    <div class="uk-overflow-auto">
                        <table class="uk-table uk-table-justify uk-table-middle uk-table-divider">
                            <thead>
                                <tr>
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
                                <?php foreach ($purchasedetails as $purdet) { ?>
                                    <?php if ($purchase['id'] === $purdet['purchaseid']) { ?>
                                        <tr>
                                            <?php foreach ($variants as $variant) {
                                                if ($variant['id'] === $purdet['variantid']) {
                                                    foreach ($products as $product) {
                                                        if ($product['id'] === $variant['productid']) {
                                                            $pName  = $product['name'];
                                                            $vName  = $variant['name']; ?>

                                                            <td><?= $pName; ?></td>
                                                            <td><?= $vName; ?></td>
                                                            <td><?= $purdet['qty']; ?> Pcs</td>
                                                        <?php }
                                                    }

                                                    if ($purchase['status'] != "0") {
                                                        foreach ($oldstocks as $oldstock) {
                                                            if ($oldstock['variantid'] === $variant['id']) {
                                                                $oldprice   = $oldstock['hargadasar'];
                                                                $newprice   = $variant['hargadasar'];
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
                                                            <?php }
                                                        }
                                                    }
                                                }
                                            } ?>
                                                
                                            <td><?= $purdet['price']; ?></td>
                                            <td><?= (Int)$purdet['price'] * (Int)$purdet['qty']; ?></td>
                                        </tr>
                                    <?php } ?>
                                <?php } ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <?php
                                    $arrayqty   = array();
                                    $arrayprice = array();
                                    foreach ($purchasedetails as $purdets) {
                                        if ($purdets['purchaseid'] === $purchase['id']) {
                                            $arrayqty[]     = $purdets['qty'];
                                            $arrayprice[]   = (Int)$purdets['qty'] * (Int)$purdets['price'];
                                        }
                                    } ?>
                                    <td><?= lang('Global.totalPurchase'); ?></td>
                                    <td></td>
                                    <td><?= array_sum($arrayqty); ?> Pcs</td>
                                    <td></td>
                                    <?php if ($purchase['status'] != "0") { ?>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    <?php } ?>
                                    <td><?= "Rp ".number_format(array_sum($arrayprice),0,',','.'); ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<!-- Modal Detail End -->
<?= $this->endSection() ?>