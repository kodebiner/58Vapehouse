<!doctype html>
<html dir="ltr "lang="<?=$lang?>" vocab="http://schema.org/" style="overflow-y: hidden;">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <base href="<?=base_url();?>">
        <title><?=$title?></title>
        <meta name="description" content="<?=$description?>">
        <meta name="author" content="PT. Kodebiner Teknologi Indonesia">
        <link rel="icon" href="favicon/favicon.ico">
        <link rel="apple-touch-icon" sizes="16x16" href="favicon/apple-icon-16x16.png">
        <link rel="apple-touch-icon" sizes="32x32" href="favicon/apple-icon-32x32.png">
        <link rel="apple-touch-icon" sizes="57x57" href="favicon/apple-icon-57x57.png">
        <link rel="apple-touch-icon" sizes="60x60" href="favicon/apple-icon-60x60.png">
        <link rel="apple-touch-icon" sizes="72x72" href="favicon/apple-icon-72x72.png">
        <link rel="apple-touch-icon" sizes="76x76" href="favicon/apple-icon-76x76.png">
        <link rel="apple-touch-icon" sizes="96x96" href="favicon/apple-icon-96x96.png">
        <link rel="apple-touch-icon" sizes="114x114" href="favicon/apple-icon-114x114.png">
        <link rel="apple-touch-icon" sizes="120x120" href="favicon/apple-icon-120x120.png">
        <link rel="apple-touch-icon" sizes="144x144" href="favicon/apple-icon-144x144.png">
        <link rel="apple-touch-icon" sizes="152x152" href="favicon/apple-icon-152x152.png">
        <link rel="apple-touch-icon" sizes="180x180" href="favicon/apple-icon-180x180.png">
        <link rel="apple-touch-icon" sizes="192x192" href="favicon/apple-icon-192x192.png">
        <link rel="manifest" href="favicon/manifest.json">
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-TileImage" content="favicon/ms-icon-144x144.png">
        <meta name="theme-color" content="#ffffff">
        <link rel="stylesheet" href="css/theme.css">
        <script src="js/uikit.min.js"></script>
        <script src="js/uikit-icons.min.js"></script>
        <link rel="stylesheet" href="css/code.jquery.com_ui_1.13.2_themes_base_jquery-ui.css">
        <script src="js/code.jquery.com_jquery-3.6.0.js"></script>
        <script src="js/code.jquery.com_ui_1.13.2_jquery-ui.js"></script>
        <script src="js/jquery.validate.min.js"></script>  
        
        <style type="text/css">
            .dummyproduct{fill:#666666;}
        </style>

    </head>
    <body style="background-color: #363636;">

        <!-- Header Section -->
        <header class="uk-navbar-container tm-navbar-container" style="background-color:#000;">
            <div class="uk-container uk-container-expand uk-margin">
                <div class="uk-flex-middle" uk-navbar>

                    <!-- Navbar Left -->
                    <div class="uk-navbar-left">
                        <a class="uk-navbar-toggle" href="#offcanvas" uk-navbar-toggle-icon uk-toggle width="35" height="35" role="button" aria-label="Open menu" style="color: #fff;"></a>
                    </div>
                    <!-- Navbar Left End -->

                    <!-- Navbar Center -->
                    <div class="uk-navbar-center">
                        <a class="uk-navbar-item uk-logo" href="<?=base_url();?>" aria-label="<?=lang('Global.backHome')?>">
                            <?php if (($gconfig['logo'] != null) && ($gconfig['bizname'] != null)) { ?>
                                <img src="/img/<?=$gconfig['logo'];?>" alt="<?=$gconfig['bizname'];?>" style="height: 70px;">
                            <?php } else { ?>
                                <img src="/img/binary111-logo-icon.svg" alt="PT. Kodebiner Teknologi Indonesia" style="height: 70px;">
                            <?php } ?>
                        </a>
                    </div>
                    <!-- Navbar Center End -->
                    
                    <!-- Navbar Right -->
                    <?php if ($ismobile === false) { ?>
                        <div class="uk-navbar-right">
                            <div class="uk-child-width-1-3 uk-flex uk-flex-middle" uk-grid>
                                <div>
                                    <a class="uk-button uk-button-text" href="#modal-sections" uk-toggle>Top Up Point</a>
                                </div>
                                <div>
                                    <button type="button" class="uk-button" uk-toggle="target: #bookinglist" uk-icon="folder" width="35" height="35" style="color: #fff;"></button>
                                </div>
                                <div>
                                    <button type="button" class="uk-button" uk-toggle="target: #tambahdata" uk-icon="cart" width="35" height="35" style="color: #fff;"></button>
                                </div>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="uk-navbar-right">
                            <div class="uk-child-width-1-3 uk-flex uk-flex-middle" uk-grid>
                                <div class="uk-padding-remove uk-flex uk-flex-center">
                                    <a class="uk-button uk-button-text" href="#modal-sections" uk-toggle>Top Up Point</a>
                                </div>
                                <div class="uk-padding-remove uk-flex uk-flex-center">
                                    <button type="button" class="uk-button" uk-toggle="target: #bookinglist" uk-icon="folder" width="30" height="30" style="color: #fff;"></button>
                                </div>
                                <div class="uk-padding-remove uk-flex uk-flex-center">
                                    <button type="button" class="uk-button" uk-toggle="target: #tambahdata" uk-icon="cart" width="30" height="30" style="color: #fff;"></button>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <!-- Navbar Right End -->

                    <!-- Modal Top Up Point -->
                    <div class="uk-flex-top" id="modal-sections" uk-modal>
                        <div class="uk-modal-dialog uk-margin-auto-vertical">
                            <button class="uk-modal-close-default" type="button" uk-close></button>
                            <div class="uk-modal-header">
                                <h2 class="uk-modal-title">Top Up Point</h2>
                            </div>
                            <div class="uk-modal-body">
                                <form class="uk-form-horizontal uk-margin-large" action="pay/topup" method="post">
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
                                                        echo '{label:"'.$customer['name'].'",idx:'.$customer['id'].'},';
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
                                        <label class="uk-form-label" for="form-horizontal-text">Value</label>
                                        <div class="uk-form-controls">
                                            <div class="uk-inline uk-width-1-1">
                                                <span class="uk-form-icon" uk-icon="icon: database"></span>
                                                <input class="uk-input" min="0" name="value" type="number" placeholder="Point" aria-label="Not clickable icon">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="uk-margin">
                                        <label class="uk-form-label" for="form-horizontal-text">Payment</label>
                                        <div class="uk-form-controls">
                                            <div class="uk-inline uk-width-1-1">
                                                <span class="uk-form-icon" uk-icon="icon: credit-card"></span>
                                                <select class="uk-select uk-input" id="payment" name="payment" required>
                                                    <option value="" selected disabled hidden><?=lang('global.payment')?></option>
                                                    <?php
                                                    foreach ($payments as $pay) {
                                                        if (($pay['outletid'] === $outletPick) || ($pay['outletid'] === '0')) {
                                                            echo '<option value="'.$pay['id'].'">'.$pay['name'].'</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="uk-margin">
                                        <label class="uk-form-label" for="form-horizontal-text">Description</label>
                                        <div class="uk-form-controls">
                                            <div class="uk-inline uk-width-1-1">
                                                <textarea class="uk-textarea" rows="5" placeholder="Description" name="description" aria-label="Textarea"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="uk-modal-footer uk-text-right">
                                        <button class="uk-button uk-button-primary" type="submit" value="submit">Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- Modal Top Up Point End -->

                    <!-- Modal Booking -->
                    <div uk-modal class="uk-flex-top" id="bookinglist">
                        <div class="uk-modal-dialog uk-margin-auto-vertical">
                            <div class="uk-modal-content">
                                <div class="uk-modal-header">
                                    <h5 class="uk-modal-title" id="bookinglist" ><?=lang('Global.bookingList')?></h5>
                                </div>
                                <div class="uk-modal-body">
                                    <?php foreach ($bookings as $book) { ?>
                                        <div id="booklist<?=$book['id']?>">
                                            <div class="uk-h3 tm-h4"><?= $book['created_at'] ?></div>
                                            <div class="uk-margin-small-top" uk-grid>
                                                <a class="uk-width-5-6 uk-link-reset" uk-toggle="target: #detail<?= $book['id'] ?>">
                                                    <?php
                                                    if ($book['memberid'] === '0') {
                                                        $member = 'Non Member';
                                                    } else {
                                                        foreach ($customers as $cust) {
                                                            if ($book['memberid'] === $cust['id']) {
                                                                $member = $cust['name'];
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                    <div><?= $member ?></div>
                                                    <div><?= $book['value'] ?></div>
                                                </a>
                                                <div class="uk-width-1-6 uk-light">
                                                    <a uk-icon="trash" class="uk-icon-button-delete" href="pay/delete/<?= $book['id'] ?>" onclick="return confirm('<?=lang('Global.deleteConfirm')?>')"></a>
                                                </div>
                                            </div>
                                            <hr>
                                        </div>

                                        <!-- Modal Booking Detail -->
                                        <div uk-modal class="uk-flex-top" id="detail<?= $book['id'] ?>">
                                            <div class="uk-modal-dialog uk-margin-auto-vertical">
                                                <div class="uk-modal-content">
                                                    <div class="uk-modal-header">
                                                        <h5 class="uk-modal-title" id="bookinglist" ><?=lang('Global.bookdetList')?></h5>
                                                    </div>
                                                    <div class="uk-modal-body">
                                                        <?php
                                                        if ($book['memberid'] === '0') {
                                                            $member = 'Non Member';
                                                        } else {
                                                            foreach ($customers as $cust) {
                                                                if ($book['memberid'] === $cust['id']) {
                                                                    $member = $cust['name'];
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                        <div class="uk-h3 tm-h4"><?= $member ?></div>
                                                        <?php foreach ($bookingdetails as $bookdet) { 
                                                            if ($bookdet['bookingid'] === $book['id']) {
                                                                if ($bookdet['variantid'] != '0') {
                                                                    foreach ($variants as $variant) {
                                                                        foreach ($products as $product) {
                                                                            if (($product['id'] === $variant['productid']) && ($variant['id'] === $bookdet['variantid'])) {
                                                                                $vname = $product['name'].' - '.$variant['name']; ?>
                                                                                <div class="uk-margin-remove" uk-grid>
                                                                                    <div class="uk-width-1-6">
                                                                                        <div><?= $bookdet['qty'] ?></div>
                                                                                    </div>
                                                                                    <div class="uk-width-2-3">
                                                                                        <div><?= $vname ?></div>
                                                                                    </div>
                                                                                    <div class="uk-width-1-6">
                                                                                        <div><?= $bookdet['value'] ?></div>
                                                                                    </div>
                                                                                </div>
                                                                            <?php }
                                                                        }
                                                                    }
                                                                } else {
                                                                    foreach ($bundles as $bundle) {
                                                                        if ($bundle['id'] === $bookdet['bundleid']) {
                                                                            $bname = $bundle['name']; ?>

                                                                            <div class="uk-margin-remove" uk-grid>
                                                                                <div class="uk-width-1-6">
                                                                                    <div><?= $bookdet['qty'] ?></div>
                                                                                </div>
                                                                                <div class="uk-width-2-3">
                                                                                    <div><?= $bname ?></div>
                                                                                </div>
                                                                                <div class="uk-width-1-6">
                                                                                    <div><?= $bookdet['value'] ?></div>
                                                                                </div>
                                                                            </div>
                                                                        <?php }
                                                                    }
                                                                }
                                                            }
                                                        } ?>
                                                        <hr>
                                                        <div class="uk-margin">
                                                            <button type="submit" class="uk-button uk-button-default" style="border-radius: 8px; width: 540px;"><?= lang('Global.print') ?></button>
                                                        </div>
                                                        <div>
                                                            <a class="uk-button uk-button-primary" style="border-radius: 8px; width: 540px;" uk-toggle="#tambahdata" onclick="insertBooking<?= $book['id'] ?>()"><?= lang('Global.tocart') ?></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Modal Booking Detail End -->

                                        <!-- Script Booking -->
                                        <script type="text/javascript">
                                            function insertBooking<?= $book['id'] ?>() {

                                                <?php
                                                foreach ($customers as $customer) {
                                                    if ($book['memberid'] === '0') {
                                                ?>
                                                        document.getElementById("customername").value = 'Non Member';
                                                        document.getElementById("customerid").value = '0';
                                                <?php } elseif ($book['memberid'] === $customer['id']) { ?>
                                                        document.getElementById("customername").value = '<?=$customer['name']?>';
                                                        document.getElementById("customerid").value = '<?=$customer['id']?>';
                                                <?php
                                                    }
                                                }
                                                ?>

                                                var products = document.getElementById('products');

                                                document.getElementById('booklist<?= $book['id'] ?>').remove();
                                                //var count = 1;
                                                
                                                var oldproducts = document.querySelector('#products');
                                                var oldproductschild = oldproducts.lastElementChild;
                                                while (oldproductschild) {
                                                    oldproducts.removeChild(oldproductschild);
                                                    oldproductschild = oldproducts.lastElementChild;
                                                }
                                                <?php
                                                $bookqty = array();
                                                $bookbundqty = array();
                                                foreach ($bookingdetails as $bookdet) {
                                                    echo 'var count = '.$bookdet['qty'].';';
                                                    foreach ($products as $product) {
                                                        foreach ($variants as $variant) {
                                                            if (($bookdet['bookingid'] === $book['id']) && ($variant['id'] === $bookdet['variantid']) && ($variant['productid'] === $product['id'])) {
                                                                $bookqty[$variant['id']] = $bookdet['qty'];
                                                                $VarName    = $variant['name'];
                                                                $Price   = $variant['hargamodal'] + $variant['hargajual'];
                                                                $ProdName   = $product['name'].' - '. $variant['name']; ?>

                                                                if ( $( "#product<?= $book['id'] ?><?=$variant['id']?>" ).length ) {
                                                                    alert('<?=lang('Global.readyAdd');?>');
                                                                } else {
                                                                    <?php
                                                                    foreach ($stocks as $stock) {
                                                                        if (($stock['variantid'] === $variant['id']) && ($stock['outletid'] === $outletPick)) {
                                                                            echo 'let stock = '.$stock['qty'].';';
                                                                            if ($stock['qty'] === '0') {
                                                                                echo 'alert("'.lang('Global.alertstock').'")';
                                                                            } else {
                                                                    ?>
                                                                                var minstock = 1;
                                                                                var minval = count;
                                                                                
                                                                                var productgrid = document.createElement('div');
                                                                                productgrid.setAttribute('id', 'product<?= $book['id'] ?><?=$variant['id']?>');
                                                                                productgrid.setAttribute('class', 'uk-margin-small');
                                                                                productgrid.setAttribute('uk-grid', '');

                                                                                var addcontainer = document.createElement('div');
                                                                                addcontainer.setAttribute('class', 'uk-flex uk-flex-middle uk-width-1-6');
                                                                                
                                                                                var productqtyinputadd = document.createElement('div');
                                                                                productqtyinputadd.setAttribute('id','addqty<?=$variant['id']?>');
                                                                                productqtyinputadd.setAttribute('class','tm-h2 pointerbutton uk-button uk-button-small uk-button-primary');
                                                                                productqtyinputadd.innerHTML = '+';

                                                                                var delcontainer = document.createElement('div');
                                                                                delcontainer.setAttribute('class', 'uk-flex uk-flex-middle uk-width-1-6');
                                                                                
                                                                                var productqtyinputdel = document.createElement('div');
                                                                                productqtyinputdel.setAttribute('id','delqty<?=$variant['id']?>');
                                                                                productqtyinputdel.setAttribute('class','tm-h2 pointerbutton uk-button uk-button-small uk-button-danger');
                                                                                productqtyinputdel.innerHTML = '-';

                                                                                var quantitycontainer = document.createElement('div');
                                                                                quantitycontainer.setAttribute('class', 'tm-h2 uk-flex uk-flex-middle uk-width-1-6');

                                                                                var productqty = document.createElement('div');                                               

                                                                                var inputqty = document.createElement('input');
                                                                                inputqty.setAttribute('type', 'number');
                                                                                inputqty.setAttribute('id', "qty[<?=$variant['id']?>]");
                                                                                inputqty.setAttribute('name', "qty[<?=$variant['id']?>]");
                                                                                inputqty.setAttribute('class', 'uk-input uk-form-width-xsmall');
                                                                                inputqty.setAttribute('min', minstock);
                                                                                inputqty.setAttribute('max', stock);
                                                                                inputqty.setAttribute('value', '<?= $bookdet['qty'] ?>');
                                                                                inputqty.setAttribute('onchange', 'showprice()');
                                                                                
                                                                                var handleIncrement = () => {
                                                                                    count++;
                                                                                    if (inputqty.value == stock) {
                                                                                        inputqty.value = stock;
                                                                                        count = stock;
                                                                                        alert('<?=lang('Global.alertstock')?>');
                                                                                    } else {
                                                                                        inputqty.value = count;
                                                                                        var price = count * <?=$Price?>;
                                                                                        var bargainprice = varbargain.value * inputqty.value;
                                                                                        if(varbargain.value){
                                                                                            document.getElementById('price<?=$variant['id']?>').innerHTML = bargainprice;
                                                                                        }
                                                                                        else {
                                                                                            productprice.innerHTML = price;
                                                                                            productprice.value = price;
                                                                                        }
                                                                                    }
                                                                                };
                                                                                
                                                                                var handleDecrement = () => {
                                                                                    count--;
                                                                                    if (inputqty.value == '1') {
                                                                                        inputqty.value = '0';
                                                                                        inputqty.remove();                                                                                                
                                                                                        productgrid.remove();
                                                                                    } else {
                                                                                        inputqty.value = count;
                                                                                        var price = count * <?=$Price?>;
                                                                                        var bargainprice = varbargain.value * inputqty.value;
                                                                                        if(varbargain.value){
                                                                                            document.getElementById('price<?=$variant['id']?>').innerHTML = bargainprice;
                                                                                        }
                                                                                        else {
                                                                                            productprice.innerHTML = price;
                                                                                            productprice.value = price;
                                                                                        }
                                                                                    }
                                                                                };

                                                                                productqtyinputadd.addEventListener("click", handleIncrement);
                                                                                productqtyinputdel.addEventListener("click", handleDecrement);

                                                                                var namecontainer = document.createElement('div');
                                                                                namecontainer.setAttribute('class', 'uk-flex uk-flex-middle uk-width-1-3');

                                                                                var productname = document.createElement('div');
                                                                                productname.setAttribute('id', 'name<?=$variant['id']?>');
                                                                                productname.setAttribute('class', 'tm-h2');
                                                                                productname.innerHTML = '<?=$ProdName?>';

                                                                                var pricecontainer = document.createElement('div');
                                                                                pricecontainer.setAttribute('class', 'uk-flex uk-flex-middle uk-width-1-6');
                                                                                
                                                                                var productprice = document.createElement('div');
                                                                                productprice.setAttribute('id', 'price<?=$variant['id']?>');
                                                                                productprice.setAttribute('class', 'tm-h2');
                                                                                productprice.setAttribute('name', 'price[]');
                                                                                productprice.setAttribute('value', showprice())
                                                                                productprice.innerHTML = showprice();

                                                                                var varpricecontainer = document.createElement('div');
                                                                                varpricecontainer.setAttribute('class', 'uk-margin-small uk-flex uk-flex-middle uk-width-1-2');

                                                                                var varbardiv = document.createElement('div');
                                                                                varbardiv.setAttribute('class','uk-margin uk-margin-small uk-flex uk-flex-middle uk-width-1-2');

                                                                                var varbarlab = document.createElement('label');
                                                                                varbarlab.setAttribute('class','uk-form-label uk-margin-remove uk-text-bold uk-text-small uk-h4');

                                                                                var varbartext = document.createTextNode("Variant Bargain");

                                                                                var varbarform = document.createElement('div');
                                                                                varbarform.setAttribute('class','uk-form-controls');

                                                                                var varbargain = document.createElement('input');
                                                                                varbargain.setAttribute('class', 'uk-input uk-form-width-small');
                                                                                varbargain.setAttribute('id', 'varbargain<?=$variant['id']?>');
                                                                                varbargain.setAttribute('placeholder', '0');
                                                                                varbargain.setAttribute('name', 'varbargain[<?=$variant['id']?>]');
                                                                                varbargain.setAttribute('min', "0");
                                                                                varbargain.setAttribute('type', 'number');

                                                                                var varvaluecontainer = document.createElement('div');
                                                                                varvaluecontainer.setAttribute('class', 'uk-margin-small uk-flex uk-flex-middle uk-width-1-2');

                                                                                var varpricediv = document.createElement('div');
                                                                                varpricediv.setAttribute('class','uk-margin uk-margin-small uk-flex uk-flex-middle uk-width-1-2');

                                                                                var varpricelab = document.createElement('label');
                                                                                varpricelab.setAttribute('class','uk-form-label uk-margin-remove uk-text-bold uk-text-small uk-h4' );

                                                                                var varpricetext = document.createTextNode("Discount Variant");

                                                                                var varpriceform = document.createElement('div');
                                                                                varpriceform.setAttribute('class','uk-form-controls');
                                                                                
                                                                                var varprice = document.createElement('input');
                                                                                varprice.setAttribute('class', 'uk-input uk-form-width-small varprice');
                                                                                varprice.setAttribute('data-index', '<?=$variant['id']?>');
                                                                                varprice.setAttribute('id', 'varprice<?=$variant['id']?>');
                                                                                varprice.setAttribute('placeholder', '0');
                                                                                varprice.setAttribute('name', 'varprice[<?=$variant['id']?>]');
                                                                                varprice.setAttribute('value', '0');
                                                                                varprice.setAttribute('type', 'number');
                                                                                varprice.setAttribute('min', '0');


                                                                                function showprice() {
                                                                                    var qty = inputqty.value;
                                                                                    var price = qty * <?=$Price?>;
                                                                                    return price;
                                                                                    productprice.innerHTML = price;
                                                                                }

                                                                                inputqty.onchange = function() {showprice()};

                                                                                varbargain.onchange = function() {
                                                                                    var bargainprice = varbargain.value * inputqty.value;
                                                                                    if (bargainprice) {
                                                                                        document.getElementById('price<?=$variant['id']?>').innerHTML = bargainprice;
                                                                                    } else {
                                                                                        document.getElementById('price<?=$variant['id']?>').innerHTML = showprice();
                                                                                    }
                                                                                }

                                                                                addcontainer.appendChild(productqtyinputadd);
                                                                                productqty.appendChild(inputqty);
                                                                                quantitycontainer.appendChild(productqty);
                                                                                delcontainer.appendChild(productqtyinputdel);
                                                                                pricecontainer.appendChild(productprice);
                                                                                namecontainer.appendChild(productname);
                                                                                varpricecontainer.appendChild(varbardiv);
                                                                                varbardiv.appendChild(varbarlab);
                                                                                varbarlab.appendChild(varbartext);
                                                                                varbarlab.appendChild(varbarform);
                                                                                varbarform.appendChild(varbargain);
                                                                                varvaluecontainer.appendChild(varpricediv);
                                                                                varpricediv.appendChild(varpricelab);
                                                                                varpricelab.appendChild(varpricetext);
                                                                                varpricelab.appendChild(varpriceform);
                                                                                varpriceform.appendChild(varprice);
                                                                                productgrid.appendChild(delcontainer);
                                                                                productgrid.appendChild(quantitycontainer);
                                                                                productgrid.appendChild(addcontainer);
                                                                                productgrid.appendChild(namecontainer);
                                                                                productgrid.appendChild(pricecontainer);
                                                                                productgrid.appendChild(pricecontainer);
                                                                                productgrid.appendChild(varpricecontainer);
                                                                                productgrid.appendChild(varvaluecontainer);
                                                                                products.appendChild(productgrid);
                                                                            <?php }
                                                                        }
                                                                    } ?>
                                                                }
                                                            <?php }
                                                        }
                                                    } ?>
                                                    
                                                    <?php foreach ($bundles as $bundle) {

                                                        $BunName = $bundle['name'];
                                                        $BunPrice = $bundle['price'];
                                                        if (($bookdet['bookingid'] === $book['id']) && ($bundle['id'] === $bookdet['bundleid'])) {
                                                            foreach ($bundets as $bundet) {
                                                                if ($bundle['id'] === $bundet['bundleid']) {
                                                                    $i = 0;
                                                                    foreach ($bundleVariants as $variant) {
                                                                        if (($variant->bundleid === $bundle['id']) && ($variant->outletid === $outletPick)) {
                                                                            $i++;
                                                                            if ($i === 1) {
                                                                                //$bundlestock = $variant->qty;
                                                                                echo 'var bstock = '.$variant->qty.';';
                                                                                $bookbundqty[$variant->id] = $bookdet['qty'];
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            } ?>

                                                            var minbstock = 1; 
                                                            var minbval = count;
                                                            
                                                            var bundlegrid = document.createElement('div');
                                                            bundlegrid.setAttribute('id', 'bundle<?= $book['id'] ?><?= $bundle['id'] ?>');
                                                            bundlegrid.setAttribute('class', 'uk-margin-small');
                                                            bundlegrid.setAttribute('uk-grid', '');

                                                            var addbundlecontainer = document.createElement('div');
                                                            addbundlecontainer.setAttribute('class', 'uk-flex uk-flex-middle uk-width-1-6');
                                                            
                                                            var bunldeqtyinputadd = document.createElement('div');
                                                            bunldeqtyinputadd.setAttribute('id','addbqty<?= $bundle['id'] ?>');
                                                            bunldeqtyinputadd.setAttribute('class','tm-h2 pointerbutton uk-button uk-button-small uk-button-primary');
                                                            bunldeqtyinputadd.innerHTML = '+';

                                                            var delbundlecontainer = document.createElement('div');
                                                            delbundlecontainer.setAttribute('class', 'uk-flex uk-flex-middle uk-width-1-6');
                                                            
                                                            var bundleqtyinputdel = document.createElement('div');
                                                            bundleqtyinputdel.setAttribute('id','delbqty<?= $bundle['id'] ?>');
                                                            bundleqtyinputdel.setAttribute('class','tm-h2 pointerbutton uk-button uk-button-small uk-button-danger');
                                                            bundleqtyinputdel.innerHTML = '-';

                                                            var bundleqtycontainer = document.createElement('div');
                                                            bundleqtycontainer.setAttribute('class', 'tm-h2 uk-flex uk-flex-middle uk-width-1-6');

                                                            var bundleqty = document.createElement('div');                                               

                                                            var bundleinputqty = document.createElement('input');
                                                            bundleinputqty.setAttribute('type', 'number');
                                                            bundleinputqty.setAttribute('id', "bqty[<?= $bundle['id'] ?>]");
                                                            bundleinputqty.setAttribute('name', "bqty[<?= $bundle['id'] ?>]");
                                                            bundleinputqty.setAttribute('class', 'uk-input uk-form-width-xsmall');
                                                            bundleinputqty.setAttribute('min', minbstock);
                                                            bundleinputqty.setAttribute('max', bstock);
                                                            bundleinputqty.setAttribute('value', '<?= $bookdet['qty'] ?>');
                                                            bundleinputqty.setAttribute('onchange', 'showbprice()');
                                                            
                                                            var handleIncrements = () => {
                                                                count++;
                                                                if (bundleinputqty.value == bstock) {
                                                                    bundleinputqty.value = bstock;
                                                                    count = bstock;
                                                                    alert('<?=lang('Global.alertstock')?>');
                                                                } else {
                                                                    bundleinputqty.value = count;
                                                                    var bprice = count * <?= $BunPrice ?>;
                                                                    bundleprice.innerHTML = bprice;
                                                                    bundleprice.value = bprice;
                                                                }
                                                            };
                                                            
                                                            var handleDecrements = () => {
                                                                count--;
                                                                if (bundleinputqty.value == '1') {
                                                                    bundleinputqty.value = '0';
                                                                    bundleinputqty.remove();
                                                                    bundlegrid.remove();
                                                                } else {
                                                                    bundleinputqty.value = count;
                                                                    var bprice = count * <?= $BunPrice ?>;
                                                                    bundleprice.innerHTML = bprice;
                                                                }
                                                            };

                                                            bunldeqtyinputadd.addEventListener("click", handleIncrements);
                                                            bundleqtyinputdel.addEventListener("click", handleDecrements);

                                                            var bundlenamecontainer = document.createElement('div');
                                                            bundlenamecontainer.setAttribute('class', 'uk-flex uk-flex-middle uk-width-1-3');

                                                            var bundlename = document.createElement('div');
                                                            bundlename.setAttribute('id', 'name<?= $bundle['id'] ?>');
                                                            bundlename.setAttribute('class', 'tm-h2');
                                                            bundlename.innerHTML = '<?= $BunName ?>';

                                                            var bpricecontainer = document.createElement('div');
                                                            bpricecontainer.setAttribute('class', 'uk-flex uk-flex-middle uk-width-1-6');
                                                            
                                                            var bundleprice = document.createElement('div');
                                                            bundleprice.setAttribute('id', 'bprice<?= $bundle['id'] ?>');
                                                            bundleprice.setAttribute('class', 'tm-h2');
                                                            bundleprice.setAttribute('name', 'price[]');
                                                            bundleprice.setAttribute('value', showbprice());
                                                            bundleprice.innerHTML = showbprice();

                                                            function showbprice() {
                                                                var bqty = bundleinputqty.value;
                                                                var bprice = bqty * <?= $BunPrice ?>;
                                                                return bprice;
                                                                bundleprice.innerHTML = bprice;
                                                            }

                                                            bundleinputqty.onchange = function() {showbprice()};

                                                            addbundlecontainer.appendChild(bunldeqtyinputadd);
                                                            bundleqty.appendChild(bundleinputqty);
                                                            bundleqtycontainer.appendChild(bundleqty);
                                                            delbundlecontainer.appendChild(bundleqtyinputdel);
                                                            bundlegrid.appendChild(delbundlecontainer);
                                                            bundlegrid.appendChild(bundleqtycontainer);
                                                            bundlegrid.appendChild(addbundlecontainer);
                                                            bundlenamecontainer.appendChild(bundlename);
                                                            bundlegrid.appendChild(bundlenamecontainer);
                                                            bpricecontainer.appendChild(bundleprice);
                                                            bundlegrid.appendChild(bpricecontainer);
                                                            products.appendChild(bundlegrid);
                                                        <?php }
                                                    }
                                                } ?>
                                                <?php
                                                $datavar = json_encode($bookqty);
                                                $databund = json_encode($bookbundqty);
                                                ?>
                                                $.ajax({
                                                    url: "transaction/restorestock",
                                                    type: "POST",    
                                                    data: {
                                                        bookingid: <?=$book['id']?>,
                                                        datavar: <?=$datavar?>,
                                                        databund: <?=$databund?>
                                                    },
                                                    success: function(response) {
                                                        console.log(response);
                                                    },
                                                    error: function(response) {
                                                        console.log(response);
                                                    }   
                                                });
                                            }
                                        </script>
                                        <!-- Script Booking End -->
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Modal Booking End -->
                </div>
            </div>
        </header>
        <!-- Header Section end -->

        <!-- Left Sidebar Section -->
        <div id="offcanvas" uk-offcanvas="overlay: true;">
            <div class="uk-offcanvas-bar" role="dialog" aria-modal="true">
                <nav>
                    <ul class="uk-nav uk-nav-default tm-nav uk-light" uk-nav>
                        <li class="tm-main-navbar">
                            <a class="uk-h4 tm-h4" href="<?= base_url('') ?>"><img src="img/layout/dashboard.svg" uk-svg><?=lang('Global.dashboard');?></a>
                        </li>
                        <li class="tm-main-navbar">
                            <a class="uk-h4 tm-h4" href="<?= base_url('') ?>"><img src="img/layout/laporan.svg" uk-svg><?=lang('Global.report');?></a>
                        </li>
                        <li class="tm-main-navbar">
                            <a class="uk-h4 tm-h4" href="<?= base_url('transaction') ?>"><img src="img/layout/riwayat.svg" uk-svg><?=lang('Global.transaction');?></a>
                        </li>
                        <li class="tm-main-navbar">
                            <a class="uk-h4 tm-h4" href="<?= base_url('') ?>"><img src="img/layout/riwayat.svg" uk-svg><?=lang('Global.trxHistory');?></a>
                        </li>
                        <li class="tm-main-navbar">
                            <a class="uk-h4 tm-h4" href="<?= base_url('') ?>"><img src="img/layout/payment.svg" uk-svg><?=lang('Global.payment');?></a>
                        </li>
                        <li class="tm-main-navbar uk-parent">
                            <a class="uk-h4 tm-h4" href=""><img src="img/layout/product.svg" uk-svg><?=lang('Global.product');?><span uk-nav-parent-icon></span></a>
                            <ul class="uk-nav-sub">
                                <li class="uk-h5 tm-h5">
                                    <a href="<?= base_url('product') ?>"><?=lang('Global.product');?></a>
                                </li>
                                <li class="uk-h5 tm-h5">
                                    <a href="<?= base_url('bundle') ?>"><?=lang('Global.bundle');?></a>
                                </li>
                            </ul>
                        </li>
                        <li class="tm-main-navbar">
                            <a class="uk-h4 tm-h4" href="<?= base_url('') ?>"><img src="img/layout/calendar.svg" uk-svg><?=lang('Global.reminder');?></a>
                        </li>
                        <li class="tm-main-navbar">
                            <a class="uk-h4 tm-h4" href="<?= base_url('presence') ?>"><img src="img/layout/presensi.svg" uk-svg><?=lang('Global.presence');?></a>
                        </li>
                        <li class="tm-main-navbar">
                            <a class="uk-h4 tm-h4" href="<?= base_url('user') ?>"><img src="img/layout/pegawai.svg" uk-svg><?=lang('Global.employee');?></a>
                        </li>
                        <li class="tm-main-navbar uk-parent">
                            <a class="uk-h4 tm-h4" href=""><img src="img/layout/inventori.svg" uk-svg><?=lang('Global.inventory');?><span uk-nav-parent-icon></span></a>
                            <ul class="uk-nav-sub">
                                <li class="uk-h5 tm-h5">
                                    <a href="<?= base_url('stock') ?>"><?=lang('Global.stock');?></a>
                                </li>
                                <li class="uk-h5 tm-h5">
                                    <a href="<?= base_url('stockmove') ?>"><?=lang('Global.stockMove');?></a>
                                </li>
                                <li class="uk-h5 tm-h5">
                                    <a href="<?= base_url('stockadjustment') ?>"><?=lang('Global.stockAdj');?></a>
                                </li>
                                <li class="uk-h5 tm-h5">
                                    <a href="<?= base_url('stock/stockcycle') ?>"><?=lang('Global.stockCycle');?></a>
                                </li>
                            </ul>
                        </li>
                        <li class="tm-main-navbar">
                            <a class="uk-h4 tm-h4" href="<?= base_url('outlet') ?>"><img src="img/layout/outlet.svg" uk-svg><?=lang('Global.outlet');?></a>
                        </li>
                        <li class="tm-main-navbar uk-parent">
                            <a class="uk-h4 tm-h4" href=""><img src="img/layout/cash.svg" uk-svg><?=lang('Global.wallet');?><span uk-nav-parent-icon></span></a>
                            <ul class="uk-nav-sub">
                                <li class="uk-h5 tm-h5">
                                    <a href="<?= base_url('walletman') ?>"><?=lang('Global.walletManagement');?></a>
                                </li>
                                <li class="uk-h5 tm-h5">
                                    <a href="<?= base_url('walletmove') ?>"><?=lang('Global.walletMovement');?></a>
                                </li>
                            </ul>
                        </li>
                        <li class="tm-main-navbar">
                            <a class="uk-h4 tm-h4" href="<?= base_url('customer') ?>"><img src="img/layout/pelanggan.svg" uk-svg><?=lang('Global.customer');?></a>
                        </li>
                        <li class="tm-main-navbar">
                            <a class="uk-h4 tm-h4" href="<?= base_url('') ?>"><img src="img/layout/union.svg" uk-svg><?=lang('Global.website');?></a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
        <!-- Left Sidebar Section end -->

        <!-- Modal Detail Transaction -->
        <div uk-modal class="uk-flex-top" id="tambahdata" >
            <div class="uk-modal-dialog uk-margin-auto-vertical">
                <div class="uk-modal-content">
                    <div class="uk-modal-header">
                        <h5 class="uk-modal-title" id="tambahdata" ><?=lang('Global.detailOrder');?></h5>
                    </div>
                    <form class="uk-form-stacked" name="order" action="pay/create" id="order" role="form" method="post">
                        <?= csrf_field() ?>
                        
                        <?php foreach ($outlets as $outlet){ 
                            if ($outlet['id'] === $this->data['outletPick']) { ?>
                                <input type="hidden" name="outlet" value="<?= $outlet['id'] ?>">
                            <?php } ?>
                        <?php } ?>
                        
                        <div class="uk-modal-body">
                            <div class="uk-margin-bottom">
                                <h4 class="uk-margin-remove"><?=lang('Global.customer')?></h4>
                                <div class="uk-margin-small">
                                    <div class="uk-width-1-1">
                                        <input class="uk-input" id="customername" name="customername" required />
                                        <input id="customerid" name="customerid" hidden />
                                    </div>
                                </div>

                                <script type="text/javascript">
                                    $(function() {
                                        var customerList = [
                                            {label: "Non Member", idx:0},
                                            <?php
                                                foreach ($customers as $customer) {
                                                    echo '{label:"'.$customer['name'].'",idx:'.$customer['id'].'},';
                                                }
                                            ?>
                                        ];
                                        $("#customername").autocomplete({
                                            source: customerList,
                                            select: function(e, i) {
                                                $("#customerid").val(i.item.idx);
                                                if (i.item.idx != 0) {
                                                    var customers = <?php echo json_encode($customers); ?>;
                                                    for (var x = 0; x < customers.length; x++) {
                                                        if (customers[x]['id'] == i.item.idx) {
                                                            document.getElementById('custpoin').removeAttribute('hidden');
                                                            document.getElementById('curpoin').innerHTML = '<?=lang('Global.yourpoint')?> ' + customers[x]['poin'];
                                                            document.getElementById('poin').setAttribute('max', customers[x]['poin']);
                                                            totalcount();
                                                        }
                                                    }
                                                } else {
                                                    document.getElementById('custpoin').setAttribute('hidden', '');
                                                    document.getElementById('curpoin').innerHTML = '<?=lang('Global.yourpoint')?> 0' ;
                                                    document.getElementById('poin').setAttribute('max', '0');
                                                    document.getElementById('poin').value = '0';
                                                    totalcount();
                                                }
                                            },
                                            minLength: 2
                                        });
                                    });
                                </script>
                            </div>

                            <div id="products"></div>

                            <div class="uk-margin">
                                <h4 class="uk-h4 uk-margin-remove-bottom"><?=lang('Global.subtotal')?></h4>
                                <div class="uk-h4 uk-margin-remove-top" min="0" id="subtotal">0</div>
                            </div>

                            <div class="uk-margin">
                                <h4 class="uk-margin-remove"><?=lang('Global.discount')?></h4>
                                <div class="uk-margin-small uk-flex-middle" uk-grid>
                                    <div class="uk-width-expand">
                                        <input type="number" class="uk-input" id="discvalue" name="discvalue" min="0" max="0" placeholder="<?=lang('Global.discount')?>" onchange="totalcount()" />
                                    </div>
                                    <div class="switch-field uk-flex uk-flex-middle uk-width-auto">
                                        <input type="radio" id="radio-one" name="disctype" value="0" checked/>
                                        <label for="radio-one"><?=lang('Global.rp')?></label>
                                        <input type="radio" id="radio-two" name="disctype" value="1" />
                                        <label for="radio-two"><?=lang('Global.percent')?></label>
                                    </div>
                                    
                                </div>
                            </div>

                            <div id="paymentmethod" class="uk-margin">
                                <h4 class="uk-margin-remove"><?=lang('Global.paymethod')?></h4>
                                <div class="uk-form-controls uk-margin-small">
                                    <select class="uk-select" id="payment" name="payment">
                                        <option value="" selected disabled hidden>-- <?=lang('Global.paymethod')?> --</option>
                                        <?php
                                        foreach ($payments as $pay) {
                                            if (($pay['outletid'] === $outletPick) || ($pay['outletid'] === '0')) {
                                                echo '<option value="'.$pay['id'].'">'.$pay['name'].'</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div id="cash" class="uk-margin">
                                <h4 class="uk-margin-remove"><?=lang('Global.cashamount')?></h4>
                                <div class="uk-form-controls uk-margin-small">
                                    <div class="uk-margin">
                                        <input class="uk-input" type="number" name="cashamount" placeholder="0" min="0" aria-label="<?=lang('Global.cashamount')?>">
                                    </div>
                                </div>
                            </div>

                            <div id="bills" class="uk-margin" hidden>
                                <h4><?=lang('Global.splitbill')?></h4>
                                <div class="uk-margin">
                                    <div class="uk-margin-small uk-form-controls">
                                        <select class="uk-select" id="firstpayment" name="firstpayment">
                                            <option value="" selected disabled hidden>-- <?=lang('Global.firstpaymet')?> --</option>
                                            <?php
                                            foreach ($payments as $pay) {
                                                if (($pay['outletid'] === $outletPick) || ($pay['outletid'] === '0')) {
                                                    echo '<option value="'.$pay['id'].'">'.$pay['name'].'</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="uk-margin-small uk-form-controls">
                                        <input type="number" class="uk-input" id="firstpay" name="firstpay" placeholder="<?=lang('Global.firstpay')?>" />
                                    </div>
                                </div>
                                <div class="uk-margin">
                                    <div class="uk-margin-small uk-form-controls">
                                        <select class="uk-select" id="secpayment" name="secpayment">
                                            <option value="" selected disabled hidden>-- <?=lang('Global.secpaymet')?> --</option>
                                            <?php
                                            foreach ($payments as $pay) {
                                                if (($pay['outletid'] === $outletPick) || ($pay['outletid'] === '0')) {
                                                    echo '<option value="'.$pay['id'].'">'.$pay['name'].'</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="uk-margin-small uk-form-controls">
                                        <input type="number" class="uk-input" id="secondpay"  name="secondpay" placeholder="<?=lang('Global.secpay')?>" />
                                    </div>
                                </div>
                            </div>

                            <div class="uk-margin" id="amount">
                                <h4 class="uk-margin-remove"><?=lang('Global.amountpaid')?></h4>
                                <div class="uk-form-controls uk-margin-small">
                                    <input type="number" class="uk-input" id="value" name="value" min="0" value="0" placeholder="<?=lang('Global.amountpaid')?>" />
                                </div>
                            </div>
                            
                            <div class="uk-margin">
                                <a class="uk-margin-remove uk-text-bold uk-text-small uk-h4 uk-link-reset" id="splitbill"><?=lang('Global.wanttosplit')?></a>
                                <a class="uk-margin-remove uk-text-bold uk-text-small uk-h4 uk-link-reset" id="cancelsplit" hidden><?=lang('Global.cancelsplit')?></a>
                            </div>

                            <div class="uk-margin" id="debtcontainer" hidden>
                                <div class="uk-child-width-auto" uk-grid>
                                    <div>
                                        <label class="uk-form-label" for="debt"><?=lang('Global.debt')?></label>
                                        <div class="uk-form-controls">
                                            <input type="number" class="uk-input uk-form-width-medium" id="debt" name="debt" value="0" readonly="readonly" />
                                        </div>
                                    </div>
                                    <div>
                                        <label class="uk-form-label" for="duedate"><?=lang('Global.duedate')?></label>
                                        <div class="uk-form-controls uk-inline">
                                            <span class="uk-form-icon uk-form-icon-flip" uk-icon="icon: calendar"></span>
                                            <input class="uk-input uk-form-width-medium" id="duedate" name="duedate" disabled />
                                            <script type="text/javascript">
                                                $( function() {
                                                    $( "#duedate" ).datepicker({
                                                        dateFormat: "yy-mm-dd",
                                                        minDate: 0,
                                                        maxDate: "+1m +1w"
                                                    });
                                                } );
                                            </script>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="custpoin" class="uk-margin" hidden>
                                <h4 class="uk-margin-remove"><?=lang('Global.point')?></h4>
                                <h5 id="curpoin" class="uk-margin-remove"></h5>
                                <div class="uk-form-controls uk-margin-small">
                                    <input type="number" class="uk-input" id="poin" name="poin" min="0" max="" placeholder="<?=lang('Global.point')?>"/>
                                </div>
                            </div>

                            <div class="uk-margin" id="tax" hidden>
                                <?=lang('Global.vat')?> <?=$gconfig['ppn']?>%
                            </div>

                            <div class="uk-margin" id="outlet" hidden>
                                <div class="uk-form-controls uk-margin-small">
                                    <?php
                                        foreach ($outlets as $baseoutlet) {
                                            if ($baseoutlet['id'] === $outletPick) {
                                                $outid = $baseoutlet['id'];
                                            }
                                        }
                                        ?>
                                        <input type="number" class="uk-input" id="outlet" name="outlet" value="<?=$baseoutlet['id']?>" />
                                </div>
                            </div>

                        </div>
                        <div class="uk-modal-footer" style="border-top: 0;">
                            <div class="uk-margin">
                                <div class="uk-width-1-1 uk-text-center">
                                    <div class="uk-flex-top tm-h3"><?=lang('Global.total')?></div>
                                </div>
                                <div class="uk-width-1-1 uk-text-center">
                                    <div class="tm-h2 uk-text-bold" id="finalprice" value="0">Rp</div>
                                </div>
                            </div>
                            <div class="uk-margin uk-flex uk-flex-center">
                                <button type="submit" id="pay" class="uk-button uk-button-primary uk-button-large uk-text-center" style="border-radius: 8px; width: 260px;" disabled><?=lang('Global.pay')?></button>
                                <button type="submit" id="save" class="uk-button uk-button-danger uk-button-large uk-text-center uk-margin-small-left" style="border-radius: 8px; width: 260px;" disabled><span uk-icon="icon:  pull"></span><?=lang('Global.book')?></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Modal Detail Transaction End -->

        <!-- Main Section -->
        <main role="main">
            <div class="tm-main">
                <div class="uk-container uk-container-expand uk-padding-remove-horizontal">
                    <div class="uk-panel uk-panel-scrollable" style="background-color: #363636;" uk-height-viewport="offset-top: .uk-navbar-container; offset-bottom: .tm-footer;">
                        <?= view('Views/Auth/_message_block') ?>
                        <?php if ($outletPick === null) { ?>
                            <div class="uk-margin uk-flex uk-flex-center">
                                <div class="uk-width-1-6@m uk-card uk-card-default uk-card-small uk-card-body">
                                    <div class="tm-h1 uk-text-center tm-text-large"><?=lang('Global.chooseoutlet')?></div>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="uk-margin uk-flex uk-flex-center">
                                <div class="uk-width-1-5@m uk-card uk-card-default uk-card-small uk-card-body">
                                    <div class="tm-h1 uk-text-center tm-text-large">
                                        <?php
                                            foreach ($outlets as $baseoutlet) {
                                                if ($baseoutlet['id'] === $outletPick) {
                                                    echo $baseoutlet['name'];
                                                }
                                            }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <ul class="uk-switcher switcher-class">

                                <!-- Catalog List -->
                                <li>
                                    <div class="uk-child-width-1-3 uk-child-width-1-5@l" uk-grid uk-height-match="target: > div > .uk-card > .uk-card-header">
                                        <?php
                                            foreach ($products as $product) {
                                                $productName    = $product['name'];
                                                $productPhoto   = $product['thumbnail'];
                                        ?>
                                            <div id="CreateOrder">
                                                <div class="uk-card uk-card-hover uk-card-default" uk-toggle="target: #modalVar<?= $product['id'] ?>">
                                                    <div class="uk-card-header uk-flex uk-flex-center uk-flex-middle">
                                                        <div class="tm-h1 uk-text-bolder"><?= $productName ?></div>
                                                    </div>
                                                    <div class="uk-card-body">
                                                        <?php if (!empty($productPhoto)) { ?>
                                                            <img class="uk-width-1-1" src="img/product/<?= $productPhoto ?>" />
                                                        <?php } else { ?>
                                                            <svg x="0px" y="0px" viewBox="0 0 300 300" style="enable-background:new 0 0 300 300;" xml:space="preserve">
                                                                <g>
                                                                    <defs>
                                                                        <rect id="SVGID_1_" y="0" width="300" height="300"/>
                                                                    </defs>
                                                                    <clipPath id="SVGID_00000065759931020451687440000009539297437584060839_">
                                                                        <use xlink:href="#SVGID_1_"  style="overflow:visible;"/>
                                                                    </clipPath>
                                                                    <g style="clip-path:url(#SVGID_00000065759931020451687440000009539297437584060839_);">
                                                                        <path class="dummyproduct" d="M10.43,99.92c-10.73-27.36,4.25-69.85,30.19-85.78C51.01,7.77,77-5.17,108.81,30.24
                                                                            c-2.16,0.65-4.26,1.55-6.29,2.7c-3.02,1.75-5.49,4.04-7.57,6.58C83.12,26.95,67.17,17.08,49.17,28.13
                                                                            C34,37.46,23.24,60.45,23.28,79.62c-0.03,5.15,0.77,10.05,2.42,14.32l4.75,11.66c6.41,15.42,12.34,29.6,12.34,46.6
                                                                            c-0.03,11.87-2.89,25.14-10.44,41.17c-1.05,2.23-1.96,5.97-1.96,9.8c0,2.31,0.29,4.66,1.16,6.73c1.13,2.73,3.09,4.44,5.9,5.59
                                                                            c2.16,0.28,10.31,0.86,17.02-5.79c6.56-6.54,13.06-21.9,6.78-58.08C50.43,89.07,75.8,68.22,87.2,62.18
                                                                            c15.23-8.09,33.99-5.98,45.6,5.15c3.3,3.14,3.38,8.34,0.23,11.6c-3.13,3.26-8.35,3.37-11.59,0.23c-5.55-5.31-16.45-7.86-26.56-2.5
                                                                            c-8.25,4.37-26.43,20.18-17.46,72.17c6.01,34.86,2.08,59.32-11.64,72.76c-13.81,13.43-31.7,10.1-32.45,9.95l-0.67-0.13l-0.63-0.24
                                                                            c-7.34-2.73-12.76-7.95-15.68-15.08c-4.14-10.12-2.41-22.24,1.16-29.72c15.27-32.43,8.34-49.15-2.2-74.47L10.43,99.92z"/>
                                                                        <g>
                                                                            <path class="dummyproduct" d="M289.03,204.6L222.63,89.6c0,0-8.25-9.16-7.65-8.69l-10.29-6.98l-72.37-38.31
                                                                                c-7.64-4.21-17.21-3.87-25.53,0.91c-6.82,3.93-11.33,10.31-12.87,17.21c14.44-4.1,30.01-1.11,40.99,8.29
                                                                                c7.23,0.26,14.23,3.89,18.08,10.64c6.07,10.47,2.46,23.86-7.98,29.88c-10.47,6.04-23.89,2.46-29.92-8.01
                                                                                c-2.57-4.48-3.27-9.48-2.52-14.24c-8.67-4.82-20.11,2.86-20.51,5.7c-0.51,3.49-1.94,54.29-1.94,54.29s0.98,10.4,1.08,11.45
                                                                                c0.21,0.64,3.82,11.58,3.82,11.58l66.4,114.96c4.06,7.05,10.6,12.07,18.43,14.18c7.8,2.1,15.98,1.03,22.98-3.03l75.14-43.35
                                                                                C292.39,237.71,297.39,219.1,289.03,204.6z M210.47,157.72l-6.24,6.9c-3.34-3.82-7.36-5.93-11.95-6.25
                                                                                c-2.17-0.16-4.25,0-6.22,0.36l-4.6-8.04C191.65,146.98,201.33,149.28,210.47,157.72z M166.64,189.62c-0.76-0.98-1.46-2-2.1-3.11
                                                                                c-0.8-1.4-1.42-2.78-1.96-4.18c-2.24-7.52-0.14-16.05,5.35-23.07c0.61-0.7,1.29-1.38,1.99-1.97l4.57,7.98
                                                                                c-0.08,0.13-0.17,0.27-0.25,0.38c-4.51,5.03-5.96,11.66-3.05,16.74c2.99,5.22,9.6,7.28,16.39,5.77l4.98,8.7
                                                                                C182.41,199.07,172.43,196.42,166.64,189.62z M182.01,224.96l6.55-6.26c6.45,6.06,13.24,8.32,20.42,6.89l4.7,8.22
                                                                                C202.67,237.11,192.12,234.18,182.01,224.96z M220.06,237.4l-50.01-87.43l5.74-3.28l50,87.43L220.06,237.4z M226.2,226.44
                                                                                c-0.29,0.25-0.55,0.46-0.85,0.69l-4.53-7.92c0.51-0.43,0.96-0.9,1.4-1.35c2.16-1.94,3.64-4,4.5-6.25
                                                                                c2.1-4.48,2.31-9.32,0.06-13.25c-3.65-6.39-12.44-8.43-21.03-5.49l-4.84-8.41c14.3-3.2,27.1-0.45,32.68,9.28
                                                                                C239,203.19,235.61,215.91,226.2,226.44z"/>
                                                                        </g>
                                                                    </g>
                                                                </g>
                                                            </svg>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </div>

                                            <div id="modalVar<?= $product['id'] ?>" class="uk-flex-top" uk-modal>
                                                <div class="uk-modal-dialog uk-margin-auto-vertical">
                                                    <div class="uk-modal-container">
                                                        <div class="uk-modal-header">
                                                            <div class="uk-modal-title tm-h2 uk-text-center"><?= $productName ?></div>
                                                        </div>
                                                        <div class="uk-modal-body">
                                                            <div class="uk-child-width-1-1" uk-grid>
                                                                <div id="">
                                                                    <?php foreach ($variants as $variant) {
                                                                        if ($variant['productid'] === $product['id']) {
                                                                            $VarName    = $variant['name'];
                                                                            $Price   = $variant['hargamodal'] + $variant['hargajual'];
                                                                            $ProdName   = $productName.' - '. $variant['name']; ?>

                                                                            <div class="uk-margin">
                                                                                <div class="uk-flex uk-flex-middle" uk-grid>
                                                                                    <div class="uk-width-1-3">
                                                                                        <div class="uk-h4"><?= $VarName; ?></div>
                                                                                    </div>
                                                                                    <div class="uk-width-1-3">
                                                                                        <div class="uk-h4">Rp <?= $Price; ?>,-</div>
                                                                                    </div>
                                                                                    <div class="uk-width-1-6">
                                                                                        <?php foreach ($stocks as $stock) {
                                                                                            if (($stock['variantid'] === $variant['id']) && ($stock['outletid'] === $outletPick)) {
                                                                                                $stok = $stock['qty']; ?>

                                                                                                <div class="uk-h4"><?= $stok; ?> pcs</div>
                                                                                            <?php } ?>
                                                                                        <?php } ?>
                                                                                    </div>
                                                                                    <div class="uk-width-1-6 uk-text-center">
                                                                                        <a class="uk-icon-button" uk-icon="cart" onclick="createNewOrder<?= $variant['id'] ?>()"></a>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                        
                                                                            <script type="text/javascript">
                                                                                var elemexist = document.getElementById('product<?=$variant['id']?>');
                                                                                function createNewOrder<?=$variant['id']?>() {
                                                                                    var count = 1;
                                                                                    var modal = document.getElementById('modalVar<?= $product['id'] ?>');
                                                                                        UIkit.modal(modal).hide();

                                                                                    var modalFav = document.getElementById('modalFav<?= $product['id'] ?>');
                                                                                        UIkit.modal(modalFav).hide();

                                                                                    if ( $( "#product<?=$variant['id']?>" ).length ) {
                                                                                        alert('<?=lang('Global.readyAdd');?>');
                                                                                    } else {
                                                                                        <?php
                                                                                        foreach ($stocks as $stock) {
                                                                                            if (($stock['variantid'] === $variant['id']) && ($stock['outletid'] === $outletPick)) {
                                                                                                echo 'let stock = '.$stock['qty'].';';
                                                                                                if ($stock['qty'] === '0') {
                                                                                                    echo 'alert("'.lang('Global.alertstock').'")';
                                                                                                } else {
                                                                                        ?>

                                                                                        let minstock = 1;
                                                                                        let minval = count;

                                                                                        const products = document.getElementById('products');
                                                                                        
                                                                                        const productgrid = document.createElement('div');
                                                                                        productgrid.setAttribute('id', 'product<?=$variant['id']?>');
                                                                                        productgrid.setAttribute('class', 'uk-margin-small');
                                                                                        productgrid.setAttribute('uk-grid', '');

                                                                                        const addcontainer = document.createElement('div');
                                                                                        addcontainer.setAttribute('class', 'uk-flex uk-flex-middle uk-width-1-6');
                                                                                        
                                                                                        const productqtyinputadd = document.createElement('div');
                                                                                        productqtyinputadd.setAttribute('id','addqty<?=$variant['id']?>');
                                                                                        productqtyinputadd.setAttribute('class','tm-h2 pointerbutton uk-button uk-button-small uk-button-primary');
                                                                                        productqtyinputadd.innerHTML = '+';

                                                                                        const delcontainer = document.createElement('div');
                                                                                        delcontainer.setAttribute('class', 'uk-flex uk-flex-middle uk-width-1-6');
                                                                                        
                                                                                        const productqtyinputdel = document.createElement('div');
                                                                                        productqtyinputdel.setAttribute('id','delqty<?=$variant['id']?>');
                                                                                        productqtyinputdel.setAttribute('class','tm-h2 pointerbutton uk-button uk-button-small uk-button-danger');
                                                                                        productqtyinputdel.innerHTML = '-';

                                                                                        const quantitycontainer = document.createElement('div');
                                                                                        quantitycontainer.setAttribute('class', 'tm-h2 uk-flex uk-flex-middle uk-width-1-6');

                                                                                        const productqty = document.createElement('div');                                               

                                                                                        const inputqty = document.createElement('input');
                                                                                        inputqty.setAttribute('type', 'number');
                                                                                        inputqty.setAttribute('id', "qty[<?=$variant['id']?>]");
                                                                                        inputqty.setAttribute('name', "qty[<?=$variant['id']?>]");
                                                                                        inputqty.setAttribute('class', 'uk-input uk-form-width-xsmall');
                                                                                        inputqty.setAttribute('min', minstock);
                                                                                        inputqty.setAttribute('max', stock);
                                                                                        inputqty.setAttribute('value', '1');
                                                                                        inputqty.setAttribute('onchange', 'showprice()');
                                                                                        
                                                                                        const handleIncrement = () => {
                                                                                            count++;
                                                                                            if (inputqty.value == stock) {
                                                                                                inputqty.value = stock;
                                                                                                count = stock;
                                                                                                alert('<?=lang('Global.alertstock')?>');
                                                                                            } else {
                                                                                                inputqty.value = count;
                                                                                                var price = count * <?=$Price?>;
                                                                                                var bargainprice = varbargain.value * inputqty.value;
                                                                                                if(varbargain.value){
                                                                                                    document.getElementById('price<?=$variant['id']?>').innerHTML = bargainprice;
                                                                                                }
                                                                                                else {
                                                                                                    productprice.innerHTML = price;
                                                                                                    productprice.value = price;
                                                                                                }
                                                                                            }
                                                                                        };
                                                                                        
                                                                                        const handleDecrement = () => {
                                                                                            count--;
                                                                                            if (inputqty.value == '1') {
                                                                                                inputqty.value = '0';
                                                                                                inputqty.remove();                                                                                                
                                                                                                productgrid.remove();
                                                                                            } else {
                                                                                                inputqty.value = count;
                                                                                                var price = count * <?=$Price?>;
                                                                                                var bargainprice = varbargain.value * inputqty.value;
                                                                                                if(varbargain.value){
                                                                                                    document.getElementById('price<?=$variant['id']?>').innerHTML = bargainprice;
                                                                                                }
                                                                                                else {
                                                                                                    productprice.innerHTML = price;
                                                                                                    productprice.value = price;
                                                                                                }
                                                                                            }
                                                                                        };

                                                                                        productqtyinputadd.addEventListener("click", handleIncrement);
                                                                                        productqtyinputdel.addEventListener("click", handleDecrement);

                                                                                        const namecontainer = document.createElement('div');
                                                                                        namecontainer.setAttribute('class', 'uk-flex uk-flex-middle uk-width-1-3');

                                                                                        const productname = document.createElement('div');
                                                                                        productname.setAttribute('id', 'name<?=$variant['id']?>');
                                                                                        productname.setAttribute('class', 'tm-h2');
                                                                                        productname.innerHTML = '<?=$ProdName?>';

                                                                                        const pricecontainer = document.createElement('div');
                                                                                        pricecontainer.setAttribute('class', 'uk-flex uk-flex-middle uk-width-1-6');
                                                                                        
                                                                                        const productprice = document.createElement('div');
                                                                                        productprice.setAttribute('id', 'price<?=$variant['id']?>');
                                                                                        productprice.setAttribute('class', 'tm-h2');
                                                                                        productprice.setAttribute('name', 'price[]');
                                                                                        productprice.setAttribute('value', showprice())
                                                                                        productprice.innerHTML = showprice();

                                                                                        // const but = document.createElement('span');
                                                                                        // but.setAttribute('class',"uk-icon-link uk-margin-small-right");
                                                                                        // but.setAttribute("uk-icon","file-edit");

                                                                                        const varpricecontainer = document.createElement('div');
                                                                                        varpricecontainer.setAttribute('class', 'uk-margin-small uk-flex uk-flex-middle uk-width-1-2');

                                                                                        const varbardiv = document.createElement('div');
                                                                                        varbardiv.setAttribute('class','uk-margin uk-margin-small uk-flex uk-flex-middle uk-width-1-2');

                                                                                        const varbarlab = document.createElement('label');
                                                                                        varbarlab.setAttribute('class','uk-form-label uk-margin-remove uk-text-bold uk-text-small uk-h4');

                                                                                        const varbartext = document.createTextNode("Variant Bargain");

                                                                                        const varbarform = document.createElement('div');
                                                                                        varbarform.setAttribute('class','uk-form-controls');

                                                                                        const varbargain = document.createElement('input');
                                                                                        varbargain.setAttribute('class', 'uk-input uk-form-width-small');
                                                                                        varbargain.setAttribute('id', 'varbargain<?=$variant['id']?>');
                                                                                        varbargain.setAttribute('placeholder', '0');
                                                                                        varbargain.setAttribute('name', 'varbargain[<?=$variant['id']?>]');
                                                                                        varbargain.setAttribute('min', "0");
                                                                                        varbargain.setAttribute('type', 'number');

                                                                                        const varvaluecontainer = document.createElement('div');
                                                                                        varvaluecontainer.setAttribute('class', 'uk-margin-small uk-flex uk-flex-middle uk-width-1-2');

                                                                                        const varpricediv = document.createElement('div');
                                                                                        varpricediv.setAttribute('class','uk-margin uk-margin-small uk-flex uk-flex-middle uk-width-1-2');

                                                                                        const varpricelab = document.createElement('label');
                                                                                        varpricelab.setAttribute('class','uk-form-label uk-margin-remove uk-text-bold uk-text-small uk-h4' );

                                                                                        const varpricetext = document.createTextNode("Discount Variant");

                                                                                        const varpriceform = document.createElement('div');
                                                                                        varpriceform.setAttribute('class','uk-form-controls');
                                                                                        
                                                                                        const varprice = document.createElement('input');
                                                                                        varprice.setAttribute('class', 'uk-input uk-form-width-small varprice');
                                                                                        varprice.setAttribute('data-index', '<?=$variant['id']?>');
                                                                                        varprice.setAttribute('id', 'varprice<?=$variant['id']?>');
                                                                                        varprice.setAttribute('placeholder', '0');
                                                                                        varprice.setAttribute('name', 'varprice[<?=$variant['id']?>]');
                                                                                        varprice.setAttribute('value', '0');
                                                                                        varprice.setAttribute('type', 'number');
                                                                                        varprice.setAttribute('min', '0');


                                                                                        function showprice() {
                                                                                            var qty = inputqty.value;
                                                                                            var price = qty * <?=$Price?>;
                                                                                            return price;
                                                                                            productprice.innerHTML = price;
                                                                                        }

                                                                                        inputqty.onchange = function() {showprice()};

                                                                                        varbargain.onchange = function() {
                                                                                            var bargainprice = varbargain.value * inputqty.value;
                                                                                            if (bargainprice) {
                                                                                                document.getElementById('price<?=$variant['id']?>').innerHTML = bargainprice;
                                                                                            } else {
                                                                                                document.getElementById('price<?=$variant['id']?>').innerHTML = showprice();
                                                                                            }
                                                                                        }

                                                                                        addcontainer.appendChild(productqtyinputadd);
                                                                                        productqty.appendChild(inputqty);
                                                                                        quantitycontainer.appendChild(productqty);
                                                                                        delcontainer.appendChild(productqtyinputdel);
                                                                                        pricecontainer.appendChild(productprice);
                                                                                        namecontainer.appendChild(productname);
                                                                                        varpricecontainer.appendChild(varbardiv);
                                                                                        varbardiv.appendChild(varbarlab);
                                                                                        varbarlab.appendChild(varbartext);
                                                                                        varbarlab.appendChild(varbarform);
                                                                                        varbarform.appendChild(varbargain);
                                                                                        varvaluecontainer.appendChild(varpricediv);
                                                                                        varpricediv.appendChild(varpricelab);
                                                                                        varpricelab.appendChild(varpricetext);
                                                                                        varpricelab.appendChild(varpriceform);
                                                                                        varpriceform.appendChild(varprice);                                                                                        
                                                                                        productgrid.appendChild(delcontainer);
                                                                                        productgrid.appendChild(quantitycontainer);
                                                                                        productgrid.appendChild(addcontainer);
                                                                                        productgrid.appendChild(namecontainer);
                                                                                        productgrid.appendChild(pricecontainer);
                                                                                        productgrid.appendChild(pricecontainer);
                                                                                        // productgrid.appendChild(but);
                                                                                        productgrid.appendChild(varpricecontainer);
                                                                                        productgrid.appendChild(varvaluecontainer);
                                                                                        products.appendChild(productgrid);

                                                                                        <?php
                                                                                                }
                                                                                            }
                                                                                        }
                                                                                        ?>
                                                                                    }
                                                                                }
                                                                            </script>
                                                                        <?php } ?>
                                                                    <?php } ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </li>
                                <!-- End Catalog List -->
                                
                                <!-- Favorite List -->
                                <li>
                                    <div class="uk-child-width-1-3 uk-child-width-1-5@l" uk-grid uk-height-match="target: > div > .uk-card > .uk-card-header">
                                        <?php
                                            foreach ($products as $product) {
                                                if ($product['favorite'] === '1') {
                                                    $productName    = $product['name'];
                                                    $productPhoto   = $product['thumbnail'];
                                        ?>
                                            <div id="CreateOrder">
                                                <div class="uk-card uk-card-hover uk-card-default" uk-toggle="target: #modalFav<?= $product['id'] ?>">
                                                    <div class="uk-card-header uk-flex uk-flex-center uk-flex-middle">
                                                        <div class="tm-h1 uk-text-bolder"><?= $productName ?></div>
                                                    </div>
                                                    <div class="uk-card-body">
                                                        <?php if (!empty($productPhoto)) { ?>
                                                            <img class="uk-width-1-1" src="img/product/<?= $productPhoto ?>" />
                                                        <?php } else { ?>
                                                            <svg x="0px" y="0px" viewBox="0 0 300 300" style="enable-background:new 0 0 300 300;" xml:space="preserve">
                                                                <g>
                                                                    <defs>
                                                                        <rect id="SVGID_1_" y="0" width="300" height="300"/>
                                                                    </defs>
                                                                    <clipPath id="SVGID_00000065759931020451687440000009539297437584060839_">
                                                                        <use xlink:href="#SVGID_1_"  style="overflow:visible;"/>
                                                                    </clipPath>
                                                                    <g style="clip-path:url(#SVGID_00000065759931020451687440000009539297437584060839_);">
                                                                        <path class="dummyproduct" d="M10.43,99.92c-10.73-27.36,4.25-69.85,30.19-85.78C51.01,7.77,77-5.17,108.81,30.24
                                                                            c-2.16,0.65-4.26,1.55-6.29,2.7c-3.02,1.75-5.49,4.04-7.57,6.58C83.12,26.95,67.17,17.08,49.17,28.13
                                                                            C34,37.46,23.24,60.45,23.28,79.62c-0.03,5.15,0.77,10.05,2.42,14.32l4.75,11.66c6.41,15.42,12.34,29.6,12.34,46.6
                                                                            c-0.03,11.87-2.89,25.14-10.44,41.17c-1.05,2.23-1.96,5.97-1.96,9.8c0,2.31,0.29,4.66,1.16,6.73c1.13,2.73,3.09,4.44,5.9,5.59
                                                                            c2.16,0.28,10.31,0.86,17.02-5.79c6.56-6.54,13.06-21.9,6.78-58.08C50.43,89.07,75.8,68.22,87.2,62.18
                                                                            c15.23-8.09,33.99-5.98,45.6,5.15c3.3,3.14,3.38,8.34,0.23,11.6c-3.13,3.26-8.35,3.37-11.59,0.23c-5.55-5.31-16.45-7.86-26.56-2.5
                                                                            c-8.25,4.37-26.43,20.18-17.46,72.17c6.01,34.86,2.08,59.32-11.64,72.76c-13.81,13.43-31.7,10.1-32.45,9.95l-0.67-0.13l-0.63-0.24
                                                                            c-7.34-2.73-12.76-7.95-15.68-15.08c-4.14-10.12-2.41-22.24,1.16-29.72c15.27-32.43,8.34-49.15-2.2-74.47L10.43,99.92z"/>
                                                                        <g>
                                                                            <path class="dummyproduct" d="M289.03,204.6L222.63,89.6c0,0-8.25-9.16-7.65-8.69l-10.29-6.98l-72.37-38.31
                                                                                c-7.64-4.21-17.21-3.87-25.53,0.91c-6.82,3.93-11.33,10.31-12.87,17.21c14.44-4.1,30.01-1.11,40.99,8.29
                                                                                c7.23,0.26,14.23,3.89,18.08,10.64c6.07,10.47,2.46,23.86-7.98,29.88c-10.47,6.04-23.89,2.46-29.92-8.01
                                                                                c-2.57-4.48-3.27-9.48-2.52-14.24c-8.67-4.82-20.11,2.86-20.51,5.7c-0.51,3.49-1.94,54.29-1.94,54.29s0.98,10.4,1.08,11.45
                                                                                c0.21,0.64,3.82,11.58,3.82,11.58l66.4,114.96c4.06,7.05,10.6,12.07,18.43,14.18c7.8,2.1,15.98,1.03,22.98-3.03l75.14-43.35
                                                                                C292.39,237.71,297.39,219.1,289.03,204.6z M210.47,157.72l-6.24,6.9c-3.34-3.82-7.36-5.93-11.95-6.25
                                                                                c-2.17-0.16-4.25,0-6.22,0.36l-4.6-8.04C191.65,146.98,201.33,149.28,210.47,157.72z M166.64,189.62c-0.76-0.98-1.46-2-2.1-3.11
                                                                                c-0.8-1.4-1.42-2.78-1.96-4.18c-2.24-7.52-0.14-16.05,5.35-23.07c0.61-0.7,1.29-1.38,1.99-1.97l4.57,7.98
                                                                                c-0.08,0.13-0.17,0.27-0.25,0.38c-4.51,5.03-5.96,11.66-3.05,16.74c2.99,5.22,9.6,7.28,16.39,5.77l4.98,8.7
                                                                                C182.41,199.07,172.43,196.42,166.64,189.62z M182.01,224.96l6.55-6.26c6.45,6.06,13.24,8.32,20.42,6.89l4.7,8.22
                                                                                C202.67,237.11,192.12,234.18,182.01,224.96z M220.06,237.4l-50.01-87.43l5.74-3.28l50,87.43L220.06,237.4z M226.2,226.44
                                                                                c-0.29,0.25-0.55,0.46-0.85,0.69l-4.53-7.92c0.51-0.43,0.96-0.9,1.4-1.35c2.16-1.94,3.64-4,4.5-6.25
                                                                                c2.1-4.48,2.31-9.32,0.06-13.25c-3.65-6.39-12.44-8.43-21.03-5.49l-4.84-8.41c14.3-3.2,27.1-0.45,32.68,9.28
                                                                                C239,203.19,235.61,215.91,226.2,226.44z"/>
                                                                        </g>
                                                                    </g>
                                                                </g>
                                                            </svg>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </div>

                                            <div id="modalFav<?= $product['id'] ?>" class="uk-flex-top" uk-modal>
                                                <div class="uk-modal-dialog uk-margin-auto-vertical">
                                                    <div class="uk-modal-container">
                                                        <div class="uk-modal-header">
                                                            <div class="uk-modal-title tm-h2 uk-text-center"><?= $productName ?></div>
                                                        </div>
                                                        <div class="uk-modal-body">
                                                            <div class="uk-child-width-1-1" uk-grid>
                                                                <div id="">
                                                                    <?php foreach ($variants as $variant) {
                                                                        if ($variant['productid'] === $product['id']) {
                                                                            $VarName    = $variant['name'];
                                                                            $Price   = $variant['hargamodal'] + $variant['hargajual'];
                                                                            $ProdName   = $productName.' - '. $variant['name']; ?>

                                                                            <div class="uk-margin">
                                                                                <div class="uk-flex uk-flex-middle" uk-grid>
                                                                                    <div class="uk-width-1-3">
                                                                                        <div class="uk-h4"><?= $VarName; ?></div>
                                                                                    </div>
                                                                                    <div class="uk-width-1-3">
                                                                                        <div class="uk-h4">Rp <?= $Price; ?>,-</div>
                                                                                    </div>
                                                                                    <div class="uk-width-1-6">
                                                                                        <?php foreach ($stocks as $stock) {
                                                                                            if (($stock['variantid'] === $variant['id']) && ($stock['outletid'] === $outletPick)) {
                                                                                                $stok = $stock['qty']; ?>

                                                                                                <div class="uk-h4"><?= $stok; ?> pcs</div>
                                                                                            <?php } ?>
                                                                                        <?php } ?>
                                                                                    </div>
                                                                                    <div class="uk-width-1-6 uk-text-center">
                                                                                        <a class="uk-icon-button" uk-icon="cart" onclick="createNewOrder<?= $variant['id'] ?>()"></a>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        <?php } ?>
                                                                    <?php } ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php 
                                                } 
                                            } 
                                        ?>
                                    </div>
                                </li>
                                <!-- End Favorite list -->

                                <!-- Bundle List -->
                                <li>
                                    <div class="uk-child-width-1-3 uk-child-width-1-5@l" uk-grid uk-height-match="target: > div > .uk-card > .uk-card-header">
                                        <?php foreach ($bundles as $bundle) {
                                            $BunName = $bundle['name']; 
                                            $BunPrice = $bundle['price'];
                                        ?>
                                            <div id="CreateOrder">
                                                <div class="uk-card uk-card-hover uk-card-default" onclick="createNewOrderBundle<?= $bundle['id'] ?>()">
                                                    <div class="uk-card-header">
                                                        <div class="tm-h1 uk-text-bolder uk-text-center"><?= $BunName; ?></div>
                                                    </div>
                                                    <div class="uk-card-body">
                                                        <div class="uk-height-small uk-flex uk-flex-middle uk-flex-center">
                                                            <div>
                                                                <?php
                                                                    $i = 0;
                                                                    foreach ($bundleVariants as $variant) {
                                                                        if (($variant->bundleid === $bundle['id']) && ($variant->outletid === $outletPick)) {
                                                                            $i++;
                                                                            foreach ($products as $product) {
                                                                                if ($product['id'] === $variant->productid) {
                                                                                    $CombName = $product['name'].' - '.$variant->name;
                                                                                    echo '<div class="tm-h5 uk-text-center uk-margin-small" id="combname">'.$CombName.'</div>';
                                                                                }
                                                                            }
                                                                            if ($i === 1) {
                                                                                $bundlestock = $variant->qty;
                                                                            }
                                                                        }
                                                                    }
                                                                ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="uk-card-footer">
                                                        <div class="tm-h3 uk-text-center">
                                                            <div>Rp <?= $BunPrice; ?>,-</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <script type="text/javascript">
                                                var elemexist = document.getElementById('bundle<?= $bundle['id'] ?>');
                                                function createNewOrderBundle<?= $bundle['id'] ?>() {
                                                    var count = 1;
                                                    if ( $( "#bundle<?= $bundle['id'] ?>" ).length ) {
                                                        alert('<?=lang('Global.readyAdd');?>');
                                                    } else {
                                                        <?php
                                                        if ($bundlestock === '0') {
                                                            echo 'alert("'.lang('Global.alertstock').'");';
                                                        } else {
                                                            echo 'let bstock = '.$bundlestock.';';
                                                        }
                                                        ?>
                                                        let minbstock = 1;
                                                        let minbval = count;

                                                        const products = document.getElementById('products');
                                                        
                                                        const bundlegrid = document.createElement('div');
                                                        bundlegrid.setAttribute('id', 'bundle<?= $bundle['id'] ?>');
                                                        bundlegrid.setAttribute('class', 'uk-margin-small');
                                                        bundlegrid.setAttribute('uk-grid', '');

                                                        const addbundlecontainer = document.createElement('div');
                                                        addbundlecontainer.setAttribute('class', 'uk-flex uk-flex-middle uk-width-1-6');
                                                        
                                                        const bunldeqtyinputadd = document.createElement('div');
                                                        bunldeqtyinputadd.setAttribute('id','addbqty<?= $bundle['id'] ?>');
                                                        bunldeqtyinputadd.setAttribute('class','tm-h2 pointerbutton uk-button uk-button-small uk-button-primary');
                                                        bunldeqtyinputadd.innerHTML = '+';

                                                        const delbundlecontainer = document.createElement('div');
                                                        delbundlecontainer.setAttribute('class', 'uk-flex uk-flex-middle uk-width-1-6');
                                                        
                                                        const bundleqtyinputdel = document.createElement('div');
                                                        bundleqtyinputdel.setAttribute('id','delbqty<?= $bundle['id'] ?>');
                                                        bundleqtyinputdel.setAttribute('class','tm-h2 pointerbutton uk-button uk-button-small uk-button-danger');
                                                        bundleqtyinputdel.innerHTML = '-';

                                                        const bundleqtycontainer = document.createElement('div');
                                                        bundleqtycontainer.setAttribute('class', 'tm-h2 uk-flex uk-flex-middle uk-width-1-6');

                                                        const bundleqty = document.createElement('div');                                               

                                                        const bundleinputqty = document.createElement('input');
                                                        bundleinputqty.setAttribute('type', 'number');
                                                        bundleinputqty.setAttribute('id', "bqty[<?= $bundle['id'] ?>]");
                                                        bundleinputqty.setAttribute('name', "bqty[<?= $bundle['id'] ?>]");
                                                        bundleinputqty.setAttribute('class', 'uk-input uk-form-width-xsmall');
                                                        bundleinputqty.setAttribute('min', minbstock);
                                                        bundleinputqty.setAttribute('max', bstock);
                                                        bundleinputqty.setAttribute('value', '1');
                                                        bundleinputqty.setAttribute('onchange', 'showbprice()');
                                                        
                                                        const handleIncrements = () => {
                                                            count++;
                                                            if (bundleinputqty.value == bstock) {
                                                                bundleinputqty.value = bstock;
                                                                count = bstock;
                                                                alert('<?=lang('Global.alertstock')?>');
                                                            } else {
                                                                bundleinputqty.value = count;
                                                                var bprice = count * <?= $BunPrice ?>;
                                                                bundleprice.innerHTML = bprice;
                                                                bundleprice.value = bprice;
                                                            }
                                                        };
                                                        
                                                        const handleDecrements = () => {
                                                            count--;
                                                            if (bundleinputqty.value == '1') {
                                                                bundleinputqty.value = '0';
                                                                bundleinputqty.remove();
                                                                bundlegrid.remove();
                                                            } else {
                                                                bundleinputqty.value = count;
                                                                var bprice = count * <?= $BunPrice ?>;
                                                                bundleprice.innerHTML = bprice;
                                                            }
                                                        };

                                                        bunldeqtyinputadd.addEventListener("click", handleIncrements);
                                                        bundleqtyinputdel.addEventListener("click", handleDecrements);

                                                        const bundlenamecontainer = document.createElement('div');
                                                        bundlenamecontainer.setAttribute('class', 'uk-flex uk-flex-middle uk-width-1-3');

                                                        const bundlename = document.createElement('div');
                                                        bundlename.setAttribute('id', 'name<?= $bundle['id'] ?>');
                                                        bundlename.setAttribute('class', 'tm-h2');
                                                        bundlename.innerHTML = '<?= $BunName ?>';

                                                        const bpricecontainer = document.createElement('div');
                                                        bpricecontainer.setAttribute('class', 'uk-flex uk-flex-middle uk-width-1-6');
                                                        
                                                        const bundleprice = document.createElement('div');
                                                        bundleprice.setAttribute('id', 'bprice<?= $bundle['id'] ?>');
                                                        bundleprice.setAttribute('class', 'tm-h2');
                                                        bundleprice.setAttribute('name', 'price[]');
                                                        bundleprice.setAttribute('value', showbprice());
                                                        bundleprice.innerHTML = showbprice();

                                                        function showbprice() {
                                                            var bqty = bundleinputqty.value;
                                                            var bprice = bqty * <?= $BunPrice ?>;
                                                            return bprice;
                                                            bundleprice.innerHTML = bprice;
                                                        }

                                                        bundleinputqty.onchange = function() {showbprice()};

                                                        addbundlecontainer.appendChild(bunldeqtyinputadd);
                                                        bundleqty.appendChild(bundleinputqty);
                                                        bundleqtycontainer.appendChild(bundleqty);
                                                        delbundlecontainer.appendChild(bundleqtyinputdel);
                                                        bundlegrid.appendChild(delbundlecontainer);
                                                        bundlegrid.appendChild(bundleqtycontainer);
                                                        bundlegrid.appendChild(addbundlecontainer);
                                                        bundlenamecontainer.appendChild(bundlename);
                                                        bundlegrid.appendChild(bundlenamecontainer);
                                                        bpricecontainer.appendChild(bundleprice);
                                                        bundlegrid.appendChild(bpricecontainer);
                                                        products.appendChild(bundlegrid);
                                                    }
                                                }
                                            </script>
                                        <?php } ?> 
                                    </div>            
                                </li>
                                <!-- End Bundle List -->
                            </ul>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </main>
        <!-- Main Section end -->

        <!-- Footer Section -->
        <footer class="tm-footer" style="background-color:#000;">
            <ul class="uk-flex-around tm-trx-tab" uk-tab uk-switcher="connect: .switcher-class; active: 1;">
                <li>
                    <a uk-switcher-item="0">
                        <div width="30" height="30" uk-icon="file-text"></div>
                        <div class="uk-h4 uk-margin-small"><?=lang('Global.catalog');?></div>
                    </a>
                </li>
                <li>
                    <a uk-switcher-item="0">
                        <div width="30" height="30" uk-icon="star"></div>
                        <div class="uk-h4 uk-margin-small"><?=lang('Global.favorite');?></div>
                    </a>
                </li>
                <li>
                    <a uk-switcher-item="0">
                        <div width="30" height="30" uk-icon="file-edit"></div>
                        <div class="uk-h4 uk-margin-small"><?=lang('Global.bundle');?></div>
                    </a>
                </li>
            </ul>
        </footer>
        <!-- Footer Section end -->
        

        <!-- <div id="modalsucces"  uk-modal >
            <div class="uk-modal-dialog">
                <button class="uk-modal-close-default" type="button" uk-close></button>
                <div class="uk-modal-body">
                <div class="page-body">
                    <div id="success_tic" class="modal fade" role="dialog">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="page-body">
                                    <div class="head">  
                                        <h3 style="margin-top:5px;">Transaction Success</h3>
                                    </div>
                                    <h1 style="text-align:center;">
                                        <div class="checkmark-circle">
                                            <div class="background"></div>
                                            <div class="checkmark draw"></div>
                                        </div>
                                    <h1>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="uk-modal-footer">
                    <div class="uk-margin uk-flex uk-flex-center">
                        <button class="uk-button uk-button-primary uk-button-large uk-text-center uk-margin-small-left" type="button" style="border-radius: 8px; width: 260px;">send</button>
                        <a href="#modalinvoice" class="uk-button uk-button-danger uk-button-large uk-text-center uk-margin-small-left" style="border-radius: 8px; width: 260px;" uk-toggle>Print</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div id="modalinvoice" uk-modal>
            <div class="uk-modal-dialog">
                <button class="uk-modal-close-default" type="button" uk-close></button>
                <div class="uk-modal-header">
                    <h2 class="uk-modal-title">invoice</h2>
                </div>
                <div class="uk-modal-body">
                <table class="uk-table uk-table-divider">
                    <thead>
                        <tr>
                            <th class="uk-table" style="color:black">Product</th>
                            <th class="uk-table-expand" style="color:black">Qty</th>
                            <th class="uk-width-small"  style="color:black">Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Oat Drips Banana</td>
                            <td>3</td>
                            <td>Rp.90.000,00</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="uk-modal-footer uk-text-right">
                    <button class="uk-button uk-button-default uk-modal-close" type="button">Cancel</button>
                    <a href="pay/invoice/$1" class="uk-button uk-button-primary">Print</a>
                </div>
            </div>
        </div> -->
        

        <script>
            var subtotalelem = document.getElementById('subtotal');
            var disctypeval = 0;
            var discount = 0;
            var poin = 0;
            var memberdisc = 0;
            var min = 0;

            $('#products').on('DOMSubtreeModified', function() {
                var prices = document.querySelectorAll("div[name='price[]']");
                var discvars = document.querySelectorAll(".varprice");
                
                var subarr = [];
                var discarr = [];

                for (i = 0; i < prices.length; i++) {
                    price = Number(prices[i].innerText);
                    subarr.push(price);
                }
                
                if (discvars.length !== 0){
                    for (i = 0; i < discvars.length; i++) {
                        var index = discvars[i].getAttribute('data-index');
                        var varqty = document.getElementById('qty['+index+']').value;
                        var vardisc = document.getElementById('varprice'+index).value;
                        var discountvar = Number(varqty) * Number(vardisc);
                        discarr.push(discountvar);
                    }
                }
                if (subarr.length === 0) {
                    document.getElementById('subtotal').innerHTML = 0;
                } else {
                    var subtotal = subarr.reduce(function(a, b){ return a + b; });
                    if (discvars.length !== 0){
                    var discountvar = discarr.reduce(function(a, b){ return a + b; });
                    document.getElementById('subtotal').innerHTML = subtotal - discountvar;
                    }else{
                        document.getElementById('subtotal').innerHTML = subtotal;
                    }
                }
                $(document).ready(function() {

                    $(".varprice").keyup(function(){
                        var prices = document.querySelectorAll("div[name='price[]']");
                        var discvars = document.querySelectorAll(".varprice");
                        
                        var subarr = [];
                        var discarr = [];

                        if (discvars.length !== 0){
                            for (i = 0; i < prices.length; i++) {
                                price = Number(prices[i].innerText);
                                subarr.push(price);
                            }
                            
                            for (i = 0; i < discvars.length; i++) {
                                var index = discvars[i].getAttribute('data-index');
                                var varqty = document.getElementById('qty['+index+']').value;
                                var vardisc = document.getElementById('varprice'+index).value;
                                var discountvar = Number(varqty) * Number(vardisc);
                                discarr.push(discountvar);
                            }
                            if (subarr.length === 0) {
                                document.getElementById('subtotal').innerHTML = 0;
                            } else {
                                var subtotal = subarr.reduce(function(a, b){ return a + b; });
                                var discountvar = discarr.reduce(function(a, b){ return a + b; });
                                document.getElementById('subtotal').innerHTML = subtotal - discountvar;
                            }  
                            
                            if (document.getElementById('subtotal').innerHTML < min ){
                            document.getElementById('subtotal').innerHTML = "Sorry Price To Low!"; 
                            }
                        }
                    }); 

                    $('#pay').click(function(){
                        $('#order').attr('action', "/pay/create");
                        $("#order").validate({
                            required: true,
                        });
                        $('#order').submit();
                        // UIkit.modal('#modalsucces').toggle();
                    });
                    
                    $('#save').click(function(){
                        $('#order').attr('action', "/pay/save");
                        $("#order").validate({
                            required: true,
                        });
                        $('#order').submit();
                    });
                    console.log( "ready!" );
                   
                });
            });
            
            subtotalelem.addEventListener('DOMSubtreeModified', totalcount);

            document.getElementById('discvalue').addEventListener('change', totalcount);

            var disctype = document.getElementsByName('disctype');
            for (var i = 0; i < disctype.length; i++) {
                disctype[i].addEventListener('change', totalcount);
            }

            document.getElementById('poin').addEventListener('change', totalcount);
            document.getElementById('value').addEventListener('change', totalcount);
            document.getElementById('firstpay').addEventListener('change', totalcount);
            document.getElementById('secondpay').addEventListener('change', totalcount);

            //document.getElementById('customerid').addEventListener('change', totalcount);

            function totalcount(e) {
                // Subtotal
                var subtotal = Number(document.getElementById('subtotal').innerText);

                // Discount
                var discvalue = document.getElementById('discvalue').value;

                for (i = 0; i < disctype.length; i++) {
                    if (disctype[i].checked) {
                        var disctypeval = disctype[i].value;
                    }
                }

                if (disctypeval == 0) {
                    var discount = discvalue;
                } else if (disctypeval == 1) {
                    if (discvalue > 100) {
                        alert('Harus kurang dari samadengan 100! Otomatis melakukan diskon Nominal.');
                        var discount = discvalue;
                    } else {
                        var discount = (discvalue/100)*subtotal;
                    }
                }

                // Poin
                var poin = document.getElementById('poin').value;


                // Member Discount
                var member = document.getElementById('customerid');

                if (member && member.value) {
                    if (member.value != 0) {
                        <?php
                        if ($gconfig['memberdisctype'] === '0') {
                            echo 'var memberdisc = '.$gconfig['memberdisc'].';';
                        } elseif ($gconfig['memberdisctype'] === '1') {
                            echo 'var memberdisc = ('.$gconfig['memberdisc'].'/100)*subtotal;';
                        }
                        ?>
                    } else {
                        var memberdisc = 0;
                    }
                } else {
                    var memberdisc = 0;
                }

                // Tax
                var tax = (<?=$gconfig['ppn']?>/100)*subtotal;
                
                 // Count Total Price
                var totalprice = subtotal - discount - memberdisc - poin;             

                // Tax
                var tax = (<?=$gconfig['ppn']?>/100)*totalprice;

                // Count Paid Price
                var paidprice = totalprice + tax;

                // Pay button
                var buttonpay = document.getElementById('pay');
                var buttonsave = document.getElementById('save');
                if (paidprice >= 0) {
                    buttonpay.removeAttribute('disabled');
                    buttonsave.removeAttribute('disabled', '');
                    var printprice = paidprice;
                } else {
                    buttonpay.setAttribute('disabled', '');
                    buttonsave.setAttribute('disabled', '');
                    var printprice = "Sorry Price To Low!";
                }

                var pay = document.getElementById('value').value;
                var firstpay = document.getElementById('firstpay').value;
                var secondpay = document.getElementById('secondpay').value;


                var pay = document.getElementById('value').value;
                var firstpay = document.getElementById('firstpay').value;
                var secondpay = document.getElementById('secondpay').value;

                // Debt
                if (document.getElementById('value') && pay) {
                    var paid = pay;
                } else if (document.getElementById('firstpay') && firstpay) {
                    var paid = Number(firstpay) + Number(secondpay);
                } else {
                    var paid = 0;
                }

                if (paid < printprice) {
                    document.getElementById('debt').value = printprice - paid;
                    document.getElementById('debtcontainer').removeAttribute('hidden');
                } else if (paid >= printprice) {
                    document.getElementById('debt').value = 0;
                    document.getElementById('debtcontainer').setAttribute('hidden', '');
                }

                if (document.getElementById('debt').value > 0) {
                    document.getElementById('duedate').setAttribute('required', '');
                    document.getElementById('duedate').removeAttribute('disabled');
                } else {
                    document.getElementById('duedate').removeAttribute('required');
                    document.getElementById('duedate').setAttribute('disabled', '');
                }

                // Printing Total Price
                var finalprice = document.getElementById('finalprice');
                finalprice.innerHTML = 'Rp. ' + printprice + ',-';

                // Setting Minimum & Maximum Paid Value
                if (member.value === '0' && document.getElementById('bills').hasAttribute('hidden')) {
                    document.getElementById('value').setAttribute('min', printprice);
                    document.getElementById('secondpay').removeAttribute('min');
                    document.getElementById('debtcontainer').setAttribute('hidden', '');
                    document.getElementById('debt').value = 0;
                    document.getElementById('duedate').removeAttribute('required');
                } else if (member.value === '0' && document.getElementById('amount').hasAttribute('hidden')) {
                    document.getElementById('secondpay').setAttribute('min', printprice - firstpay);
                    document.getElementById('value').removeAttribute('min');
                    document.getElementById('debtcontainer').setAttribute('hidden', '');
                    document.getElementById('debt').value = 0;
                    document.getElementById('duedate').removeAttribute('required');
                } else if (member.value !== '0' && document.getElementById('amount').hasAttribute('hidden')) {
                    document.getElementById('secondpay').removeAttribute('min');
                } else if (member.value !== '0' && document.getElementById('bills').hasAttribute('hidden')) {
                    document.getElementById('value').removeAttribute('min');
                }

                document.getElementById('value').setAttribute('max', printprice);
                document.getElementById('firstpay').setAttribute('max', printprice);
                document.getElementById('secondpay').setAttribute('max', printprice - firstpay);

                // Max Discount
                if (disctypeval === '1') {
                    document.getElementById('discvalue').setAttribute('max', '100');
                } else if (disctypeval === '0') {
                    document.getElementById('discvalue').setAttribute('max', subtotal);
                }

                // Payment Requirement
                if (document.getElementById('bills').hasAttribute('hidden')) {
                    document.getElementById('payment').setAttribute('required', '');
                    document.getElementById('value').setAttribute('required', '');
                    document.getElementById('firstpayment').removeAttribute('required');
                    document.getElementById('firstpay').removeAttribute('required');
                    document.getElementById('secpayment').removeAttribute('required');
                    document.getElementById('secondpay').removeAttribute('required');
                } else {
                    document.getElementById('payment').removeAttribute('required');
                    document.getElementById('value').removeAttribute('required');
                    document.getElementById('firstpayment').setAttribute('required', '');
                    document.getElementById('firstpay').setAttribute('required', '');
                    document.getElementById('secpayment').setAttribute('required', '');
                    document.getElementById('secondpay').setAttribute('required', '');
                }
            }


            document.getElementById('splitbill').addEventListener("click", splitbill);
            document.getElementById('cancelsplit').addEventListener("click", cancelsplit);

            function splitbill() {
                document.getElementById('amount').setAttribute('hidden', '');
                document.getElementById('paymentmethod').setAttribute('hidden', '');
                document.getElementById('splitbill').setAttribute('hidden', '');
                document.getElementById('bills').removeAttribute('hidden');
                document.getElementById('cancelsplit').removeAttribute('hidden');
                document.getElementById('value').value = null;
                totalcount();
            }

            function cancelsplit() {
                document.getElementById('amount').removeAttribute('hidden');
                document.getElementById('paymentmethod').removeAttribute('hidden');
                document.getElementById('splitbill').removeAttribute('hidden');
                document.getElementById('bills').setAttribute('hidden', '');
                document.getElementById('cancelsplit').setAttribute('hidden', '');
                document.getElementById('firstpay').value = null;
                document.getElementById('secondpay').value = null;
                totalcount();
            }
        </script>
    </body>
</html>