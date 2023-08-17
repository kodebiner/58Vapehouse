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
                            <input id="supplierid" name="supplierid" hidden required>
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
                                            <th class="uk-text-emphasis uk-width-small"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($variants as $variant) {?>
                                            <?php if ($variant['productid'] === $product['id']) {
                                                $VarName    = $variant['name'];
                                                $CombName   = $product['name'].' - '.$VarName;
                                                $basePrice  = $variant['hargadasar']; ?>
                                            
                                                <tr>
                                                    <td class="uk-width-medium"><?= $VarName; ?></td>
                                                    <td class="uk-width-small">
                                                        <div>
                                                            <a class="uk-icon-button" uk-icon="cart" onclick="createVar<?= $variant['id'] ?>()"></a>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <script type="text/javascript">
                                                    var elemexist = document.getElementById('product<?=$variant['id']?>');
                                                    function createVar<?=$variant['id']?>() {
                                                        document.getElementById('tablevariant<?= $product['id']; ?>').setAttribute('hidden', '');

                                                        var count = 1;

                                                        if ( $( "#product<?=$variant['id']?>" ).length ) {
                                                            alert('<?=lang('Global.readyAdd');?>');
                                                        } else {
                                                            let minval = count;

                                                            const products = document.getElementById('tableproduct');
                                                            
                                                            const productgrid = document.createElement('div');
                                                            productgrid.setAttribute('id', 'product<?=$variant['id']?>');
                                                            productgrid.setAttribute('class', 'uk-margin-small');
                                                            productgrid.setAttribute('uk-grid', '');

                                                            const varcontainer = document.createElement('div');
                                                            varcontainer.setAttribute('class', 'uk-flex uk-flex-middle uk-width-1-4');
                                                                                            
                                                            const varname = document.createElement('div');
                                                            varname.setAttribute('id','var<?=$variant['id']?>');
                                                            varname.setAttribute('class','');
                                                            varname.innerHTML = '<?= $CombName ?>';

                                                            const totalcontainer = document.createElement('div');
                                                            totalcontainer.setAttribute('class', 'uk-flex uk-flex-center uk-flex-middle uk-width-1-4');

                                                            const total = document.createElement('input');
                                                            total.setAttribute('type', 'number');
                                                            total.setAttribute('id', "totalpcs[<?=$variant['id']?>]");
                                                            total.setAttribute('name', "totalpcs[<?=$variant['id']?>]");
                                                            total.setAttribute('class', 'uk-input');
                                                            total.setAttribute('value', '1');
                                                            total.setAttribute('required', '');

                                                            const pcs = document.createElement('div');
                                                            pcs.setAttribute('class', 'uk-margin-small-left');
                                                            pcs.innerHTML = 'Pcs';

                                                            const pricecontainer = document.createElement('div');
                                                            pricecontainer.setAttribute('class', 'uk-flex uk-flex-center uk-flex-middle uk-width-1-4');

                                                            const price = document.createElement('input');
                                                            price.setAttribute('type', 'number');
                                                            price.setAttribute('id', "bprice[<?=$variant['id']?>]");
                                                            price.setAttribute('name', "bprice[<?=$variant['id']?>]");
                                                            price.setAttribute('class', 'uk-input');
                                                            price.setAttribute('value', '<?= $basePrice; ?>');
                                                            price.setAttribute('required', '');

                                                            const subtotcontainer = document.createElement('div');
                                                            subtotcontainer.setAttribute('class', 'uk-flex uk-flex-center uk-text-center uk-flex-middle uk-width-1-4');

                                                            const subtotal = document.createElement('div');
                                                            subtotal.setAttribute('id', "subtotal<?=$variant['id']?>");
                                                            subtotal.setAttribute('class', 'subvariant');

                                                            totalprice();
                                                            total.addEventListener('change', totalprice);
                                                            price.addEventListener('change', totalprice);

                                                            function totalprice() {
                                                                var varprice = price.value;
                                                                var varqty = total.value;
                                                                var subprice = varprice * varqty;
                                                                subtotal.setAttribute('value', subprice);
                                                                subtotal.innerHTML = subprice;
                                                            }

                                                            varcontainer.appendChild(varname);
                                                            totalcontainer.appendChild(total);
                                                            totalcontainer.appendChild(pcs);
                                                            pricecontainer.appendChild(price);
                                                            subtotcontainer.appendChild(subtotal);
                                                            productgrid.appendChild(varcontainer);
                                                            productgrid.appendChild(totalcontainer);
                                                            productgrid.appendChild(pricecontainer);
                                                            productgrid.appendChild(subtotcontainer);
                                                            products.appendChild(productgrid);
                                                        }
                                                    }
                                                </script>
                                            <?php } ?>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php } ?>

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

    $('#tableproduct').on('DOMSubtreeModified', function() {
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
</script>
<!-- Script Modal Add End -->

<!-- Table Of Content -->
<div class="uk-margin">
    <table class="uk-table uk-table-justify uk-table-middle uk-table-divider uk-light" id="example" style="width:100%">
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
                    <td class="uk-width-medium"><?= $purchase['date']; ?></td>
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
                                $total = $purdet['qty'] * $purdet['price'];
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
</div>
<!-- Table Content End -->

<!-- Script Data Table -->
<script type="text/javascript">
    // Data Table
    $(document).ready(function () {
        $('#example').DataTable();
    });
</script>
<!-- Script Data Table End -->

<!-- Modal Confirm -->
<?php foreach ($purchases as $purchase) { ?>
    <div uk-modal class="uk-flex-top" id="savedata<?= $purchase['id'] ?>">
        <div class="uk-modal-dialog uk-margin-auto-vertical">
            <div class="uk-modal-content">
                <div class="uk-modal-header">
                    <h5 class="uk-modal-title" id="savedata" ><?=lang('Global.confirmation')?></h5>
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
                                    <th class="uk-width-small uk-text-emphasis"><?=lang('Global.pcsPrice')?></th>
                                    <th class="uk-width-small uk-text-emphasis"><?=lang('Global.total')?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($purchasedetails as $purdet) { ?>
                                    <?php if ($purchase['id'] === $purdet['purchaseid']) { ?>
                                        <?php foreach ($variants as $variant) { ?>
                                            <?php foreach ($products as $product) { ?>
                                                <?php if ($variant['id'] === $purdet['variantid'] && $product['id'] === $variant['productid']) {
                                                    $pName  = $product['name'];
                                                    $vName  = $variant['name']; ?>
                                                    <tr id="ctableproduct">
                                                        <td class="uk-width-small"><?= $pName; ?></td>
                                                        <td class="uk-width-small"><?= $vName; ?></td>
                                                            
                                                        <td class="uk-width-small">
                                                            <input type="number" class="uk-input" id="ctotalpcs[<?=$purchase['id']?>][<?=$variant['id']?>]" name="ctotalpcs[<?=$purchase['id']?>][<?=$variant['id']?>]" value="<?= $purdet['qty']; ?>" required />
                                                        </td>
                                                        <td class="uk-width-small">
                                                            <input type="number" class="uk-input" id="cbprice[<?=$purchase['id']?>][<?=$variant['id']?>]" name="cbprice[<?=$purchase['id']?>][<?=$variant['id']?>]" value="<?= $purdet['price']; ?>" required />
                                                        </td>
                                                        <td id="csubtotal<?=$purchase['id']?><?=$variant['id']?>" class="uk-width-small csubvariant<?=$purchase['id']?>"><?= $purdet['price'] * $purdet['qty']; ?></td>
                                                    </tr>

                                                    <script type="text/javascript">
                                                        var cqty<?=$purchase['id']?><?=$variant['id']?> = document.getElementById('ctotalpcs[<?=$purchase['id']?>][<?=$variant['id']?>]');
                                                        var cprice<?=$purchase['id']?><?=$variant['id']?> = document.getElementById('cbprice[<?=$purchase['id']?>][<?=$variant['id']?>]');
                                                        var csubtotal<?=$purchase['id']?><?=$variant['id']?> = document.getElementById('csubtotal<?=$purchase['id']?><?=$variant['id']?>');
                                                        
                                                        cqty<?=$purchase['id']?><?=$variant['id']?>.addEventListener('change', ctotalprice<?=$purchase['id']?><?=$variant['id']?>);
                                                        cprice<?=$purchase['id']?><?=$variant['id']?>.addEventListener('change', ctotalprice<?=$purchase['id']?><?=$variant['id']?>);

                                                        function ctotalprice<?=$purchase['id']?><?=$variant['id']?>() {
                                                            csubtotal<?=$purchase['id']?><?=$variant['id']?>.innerHTML = cqty<?=$purchase['id']?><?=$variant['id']?>.value * cprice<?=$purchase['id']?><?=$variant['id']?>.value;
                                                        }
                                                    </script>
                                                <?php } ?>
                                            <?php } ?>
                                        <?php } ?>
                                    <?php } ?>
                                <?php } ?>
                            </tbody>
                        </table>

                        <hr>

                        <div class="uk-modal-footer">
                            <div class="uk-margin">
                                <div class="uk-width-1-1 uk-text-center">
                                    <div class="uk-flex-top tm-h3"><?=lang('Global.total')?></div>
                                </div>
                                <div class="uk-width-1-1 uk-text-center">
                                    <div class="tm-h2 uk-text-bold" id="cfinalprice<?=$purchase['id']?>" value="0"></div>
                                </div>
                            </div>
                            <div class="uk-margin uk-flex uk-flex-center">
                                <button type="submit" class="uk-button uk-button-primary uk-button-large uk-text-center" style="border-radius: 8px; width: 540px;"><?=lang('Global.save')?></button>
                            </div>
                        </div>

                        <!-- Script Confirm -->
                        <script type="text/javascript">
                            // Count Total Price
                            $('#ctableproduct').on('DOMSubtreeModified', function() {
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
    <div uk-modal class="uk-flex-top" id="detail<?= $purchase['id'] ?>">
        <div class="uk-modal-dialog uk-margin-auto-vertical">
            <div class="uk-modal-content">
                <div class="uk-modal-header">
                    <h5 class="uk-modal-title" id="detail<?= $purchase['id'] ?>" ><?=lang('Global.detail')?></h5>
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
                            <div class="uk-form-controls"><?= $purchase['date'] ?></div>
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
                    
                    <table class="uk-table uk-table-justify uk-table-middle uk-table-divider" style="background-color: #fff;">
                        <thead>
                            <tr>
                                <th class="uk-width-small uk-text-emphasis"><?=lang('Global.product')?></th>
                                <th class="uk-width-small uk-text-emphasis"><?=lang('Global.variant')?></th>
                                <th class="uk-width-small uk-text-emphasis"><?=lang('Global.totalPurchase')?></th>
                                <th class="uk-width-small uk-text-emphasis"><?=lang('Global.pcsPrice')?></th>
                                <th class="uk-width-small uk-text-emphasis"><?=lang('Global.total')?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($purchasedetails as $purdet) { ?>
                                <?php if ($purchase['id'] === $purdet['purchaseid']) { ?>
                                    <tr>
                                        <?php foreach ($variants as $variant) { ?>
                                            <?php foreach ($products as $product) { ?>
                                                <?php if ($variant['id'] === $purdet['variantid'] && $product['id'] === $variant['productid']) {
                                                    $pName  = $product['name'];
                                                    $vName  = $variant['name']; ?>

                                                    <td class="uk-width-small"><?= $pName; ?></td>
                                                    <td class="uk-width-small"><?= $vName; ?></td>
                                                <?php } ?>
                                            <?php } ?>
                                        <?php } ?>
                                            
                                        <td class="uk-width-small"><?= $purdet['qty']; ?> Pcs</td>
                                        <td class="uk-width-small"><?= $purdet['price']; ?></td>
                                        <td class="uk-width-small"><?= $purdet['price'] * $purdet['qty']; ?></td>
                                    </tr>
                                <?php } ?>
                            <?php } ?>
                        </tbody>
                    </table>

                    <hr>

                    <div class="uk-form-horizontal">
                        <div class="uk-margin">
                            <label class="uk-form-label"><?=lang('Global.totalPurchase')?></label>
                            <div class="uk-form-controls">
                                <?php
                                $prices = array();
                                foreach ($purchasedetails as $purdet) {
                                    if ($purchase['id'] === $purdet['purchaseid']) {
                                        $total = $purdet['qty'] * $purdet['price'];
                                        $prices [] = $total;
                                    }
                                }
                                $sum = array_sum($prices);
                                echo "Rp " . number_format($sum,2,',','.');
                                ?>
                            </div>
                            <div class="uk-flex uk-flex-right">
                                <?php if ($purchase['status'] === "0") { ?>
                                    <a class="uk-icon-button" uk-icon="pencil" uk-toggle="target: #editdata<?= $purchase['id'] ?>"></a>
                                <?php } else {} ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<!-- Modal Detail End -->

<!-- Modal Edit -->
<?php foreach ($purchases as $purchase) { ?>
    <div uk-modal class="uk-flex-top" id="editdata<?= $purchase['id'] ?>">
        <div class="uk-modal-dialog uk-margin-auto-vertical">
            <div class="uk-modal-content">
                <div class="uk-modal-header">
                    <h5 class="uk-modal-title" id="editdata<?= $purchase['id'] ?>"><?=lang('Global.updateData')?></h5>
                </div>

                <div class="uk-modal-body">
                    <form class="uk-form-stacked" role="form" action="stock/updatepur/<?= $purchase['id'] ?>" method="post">
                        <?= csrf_field() ?>

                        <div class="uk-margin-bottom">
                            <label class="uk-form-label" for="supplier"><?=lang('Global.supplier')?></label>
                            <div class="uk-form-controls">
                                <?php foreach ($suppliers as $supplier) { ?>
                                    <?php if ($supplier['id'] === $purchase['supplierid']) { ?>
                                        <input class="uk-input" id="esuppliername" name="esuppliername" value="<?= $supplier['name'] ?>" required>
                                        <input id="esupplierid" name="esupplierid" hidden required>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                        </div>

                        <div class="uk-margin-bottom">
                            <label class="uk-form-label" for="product"><?=lang('Global.product')?></label>
                            <div class="uk-form-controls">
                                <input type="text" class="uk-input" id="eproductname<?= $purchase['id'] ?>" name="eproductname<?= $purchase['id'] ?>" placeholder="<?=lang('Global.product')?>">
                            </div>
                        </div>
                        
                        <?php foreach ($products as $product) {?>
                            <div id="etabvar<?= $product['id']; ?>" >
                                <div class="uk-overflow-auto uk-margin-bottom">
                                    <table class="uk-table uk-table-justify uk-table-middle uk-table-divider">
                                        <thead>
                                            <tr>
                                                <th class="uk-text-emphasis uk-width-medium"><?=lang('Global.variant')?></th>
                                                <th class="uk-text-emphasis uk-width-small"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($variants as $variant) {?>
                                                <?php if ($variant['productid'] === $product['id']) {
                                                    $eVarName    = $variant['name'];
                                                    $eCombName   = $product['name'].' - '.$eVarName;
                                                    $ebasePrice  = $variant['hargadasar']; ?>
                                                
                                                    <tr>
                                                        <td class="uk-width-medium"><?= $eVarName; ?></td>
                                                        <td class="uk-width-small">
                                                            <a class="uk-icon-button" uk-icon="cart" onclick="ecreateVar<?= $variant['id'] ?>()"></a>
                                                        </td>
                                                    </tr>

                                                    <script type="text/javascript">
                                                        var elemexiste = document.getElementById('eproduct<?=$variant['id']?>');
                                                        function ecreateVar<?=$variant['id']?>() {
                                                            document.getElementById('etabvar<?= $product['id']; ?>').setAttribute('hidden', '');

                                                            var count = 1;

                                                            if ( $( "#eproduct<?=$variant['id']?>" ).length ) {
                                                                alert('<?=lang('Global.readyAdd');?>');
                                                            } else {
                                                                let minval = count;

                                                                const etableproducts<?= $purchase['id'] ?> = document.getElementById('etableproducts<?= $purchase['id'] ?>');
                                                                
                                                                const etablegrid = document.createElement('div');
                                                                etablegrid.setAttribute('id', 'eproduct<?=$variant['id']?>');
                                                                etablegrid.setAttribute('class', 'uk-margin-small');
                                                                etablegrid.setAttribute('uk-grid', '');

                                                                const evarcon = document.createElement('div');
                                                                evarcon.setAttribute('class', 'uk-flex uk-flex-middle uk-width-1-4');
                                                                                                
                                                                const evname = document.createElement('div');
                                                                evname.setAttribute('id','evname<?=$variant['id']?>');
                                                                evname.setAttribute('class','');
                                                                evname.innerHTML = '<?= $eCombName ?>';

                                                                const eqtycon = document.createElement('div');
                                                                eqtycon.setAttribute('class', 'uk-flex uk-flex-center uk-flex-middle uk-width-1-4');

                                                                const eqty = document.createElement('input');
                                                                eqty.setAttribute('type', 'number');
                                                                eqty.setAttribute('id', "eqtypcs[<?=$variant['id']?>]");
                                                                eqty.setAttribute('name', "eqtypcs[<?=$variant['id']?>]");
                                                                eqty.setAttribute('class', 'uk-input');
                                                                eqty.setAttribute('value', '1');
                                                                eqty.setAttribute('required', '');

                                                                const epieces = document.createElement('div');
                                                                epieces.setAttribute('class', 'uk-margin-small-left');
                                                                epieces.innerHTML = 'Pcs';

                                                                const epcon = document.createElement('div');
                                                                epcon.setAttribute('class', 'uk-flex uk-flex-center uk-flex-middle uk-width-1-4');

                                                                const epri = document.createElement('input');
                                                                epri.setAttribute('type', 'number');
                                                                epri.setAttribute('id', "ebp[<?=$variant['id']?>]");
                                                                epri.setAttribute('name', "ebp[<?=$variant['id']?>]");
                                                                epri.setAttribute('class', 'uk-input');
                                                                epri.setAttribute('value', '<?= $ebasePrice; ?>');
                                                                epri.setAttribute('required', '');

                                                                const esubtotcon = document.createElement('div');
                                                                esubtotcon.setAttribute('class', 'uk-flex uk-flex-center uk-text-center uk-flex-middle uk-width-1-4');

                                                                const esubtot = document.createElement('div');
                                                                esubtot.setAttribute('id', "esubtot<?=$variant['id']?>");
                                                                esubtot.setAttribute('class', 'esubvar');

                                                                etotprice();
                                                                eqty.addEventListener('change', etotprice);
                                                                epri.addEventListener('change', etotprice);

                                                                function etotprice() {
                                                                    var evprice = epri.value;
                                                                    var evqty = eqty.value;
                                                                    var esubpr = evprice * evqty;
                                                                    esubtot.setAttribute('value', esubpr);
                                                                    esubtot.innerHTML = esubpr;
                                                                }

                                                                evarcon.appendChild(evname);
                                                                eqtycon.appendChild(eqty);
                                                                eqtycon.appendChild(epieces);
                                                                epcon.appendChild(epri);
                                                                esubtotcon.appendChild(esubtot);
                                                                etablegrid.appendChild(evarcon);
                                                                etablegrid.appendChild(eqtycon);
                                                                etablegrid.appendChild(epcon);
                                                                etablegrid.appendChild(esubtotcon);
                                                                etableproducts<?= $purchase['id'] ?>.appendChild(etablegrid);
                                                            }
                                                        }
                                                    </script>
                                                <?php } ?>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php } ?>

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

                        <div id="etableproducts<?= $purchase['id'] ?>"></div>

                        <div class="uk-modal-footer">
                            <div class="uk-margin">
                                <div class="uk-width-1-1 uk-text-center">
                                    <div class="uk-flex-top tm-h3"><?=lang('Global.total')?></div>
                                </div>
                                <div class="uk-width-1-1 uk-text-center">
                                    <div class="tm-h2 uk-text-bold" id="efinalprice" value="0">Rp 0,-</div>
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

    <!-- Script Modal Edit -->
    <script type="text/javascript">
        // Autocomplete Supplier Edit Purchase
        $(function() {
            var esupplierList = [
                <?php foreach ($suppliers as $supplier) {
                    echo '{label:"'.$supplier['name'].'",idx:'.$supplier['id'].'},';
                }?>
            ];
            $("#esuppliername").autocomplete({
                source: esupplierList,
                select: function(e, i) {
                    $("#esupplierid").val(i.item.idx);
                }
            });
        });

        // Autocomplete Product Edit Purchase
        $(function() {
            var eproductList = [
                <?php foreach ($products as $product) {
                    echo '{label:"'.$product['name'].'",idx:'.$product['id'].'},';
                }?>
            ];
            $("#eproductname<?= $purchase['id'] ?>").autocomplete({
                source: eproductList,
                select: function(e, i) {
                    if (i.item.idx != 0) {
                        var eproducts = <?php echo json_encode($products); ?>;
                        for (var x = 0; x < eproducts.length; x++) {
                            document.getElementById('etabvar'+eproducts[x]['id']).setAttribute('hidden', '');
                            if (eproducts[x]['id'] == i.item.idx) {
                                document.getElementById('etabvar'+eproducts[x]['id']).removeAttribute('hidden');
                            }
                        }
                    }
                },
                minLength: 2
            });
        });
    </script>
    <!-- Script Modal Edit End -->
<?php } ?>
<!-- Modal Edit End -->

<!-- Search Engine Script -->
<!-- <script type="text/javascript">

    // $('#tabprod').on('DOMSubtreeModified', function() {
    //     var eprices = document.querySelectorAll(".esubvariant");
    //     var esubarr = [];

    //     for (i = 0; i < eprices.length; i++) {
    //         eprice = Number(eprices[i].innerText);
    //         esubarr.push(eprice);
    //     }

    //     if (esubarr.length === 0) {
    //         document.getElementById('finalprice').innerHTML = 0;
    //     } else {
    //         var esubtotalvar = subarr.reduce(function(a, b){ return a + b; });
    //         document.getElementById('finalprice').innerHTML = 'Rp. ' + esubtotalvar + ',-';
    //     }
    // });
</script> -->
<!-- Search Engine Script End -->

<?= $this->endSection() ?>