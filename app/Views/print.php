<!doctype html>
<html dir="ltr "lang="<?=$lang?>" vocab="http://schema.org/" style="overflow-y: hidden;">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <base href="<?=base_url();?>">
        <title></title>
        <meta name="description" content="">
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
        <style>
            @media print {  
                #btn{
                    display : none;
                }
            }
        </style>
    </head>
    <body style="background-color:#000;">
        
        <div class="uk-width-1-1 uk-flex uk-flex-center">
            <div style="width:45mm; padding:10mm 2mm; background: #fff;">
                <div class="uk-margin-small uk-width-1-1 uk-text-center">
                    <img class="uk-width-1-3" src="/img/58vape.png" />
                </div>
                <div class="uk-margin-small uk-text-center uk-text-bold">58 Vapehouse<br/>
                    <?php foreach ($outlets as $outlet){
                        if ($outlet['id'] === $outid){
                            echo $outlet['address'];
                        }
                    }
                    ?>
                </div>
                <div class="uk-margin-small uk-child-width-1-2 uk-grid-collapse uk-text-xsmall" uk-grid>
                    <div>
                        <div>Invoice: 000000</div>
                        <div>Cashier: <?=$user?></div>                    
                    </div>
                    <div>
                    <div class="uk-text-right"><?= $date ?></div>
                        <?php foreach($payments as $payment){?>
                            <div class="uk-text-right">
                                    <?php if ($transactions['paymentid']===$payment['id']){
                                        echo $payment['name'];
                                    }?>
                            </div>
                        <?php }?>
                    </div>
                </div>
                <hr style ="border-top: 1px  dotted black;">
                <!-- variant -->
                    <?php foreach ($trxdetails as $trxdet) {
                        if ($trxdet['variantid'] !== "0"){
                            foreach ($variants as $variant) {
                                foreach ($products as $product) { 
                                    if (($trxdet['variantid'] === $variant['id']) && ($product['id'] === $variant['productid']) && ($trxdet['transactionid'] === $transactions['id']) ) {
                                        $variantName     = $variant['name'];
                                        $productName     = $product['name']; 
                                        $variantval      = $trxdet['value'];
                                        ?>
                                        <div class="uk-margin-small uk-text-xsmall">
                                            <div>
                                                <?=$productName.' - '.$variantName?>
                                            </div>
                                            <div class="uk-grid-collapse" uk-grid>
                                                <div class="uk-width-2-3">x<?=$trxdet['qty']?> @<?=$variantval?></div>
                                                <div class="uk-width-1-3"><?=$variantval * $trxdet['qty']?></div>
                                            </div>
                                            <div class="uk-grid-collapse" uk-grid>
                                                <?php
                                                if (!empty($vardiscval[$variant['id']])){
                                                    echo "<div class='uk-width-2-3'>Discount</br> @" .$vardiscval[$variant['id']]. "</div>";
                                                    echo "<div class='uk-width-1-3'></br>-" .$vardiscval[$variant['id']]. "</div>";
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    <?php }
                                } 
                            } 
                        }
                    } ?>
                <!-- end variant -->

                <!-- bundle -->
                <?php 
                if (!empty($trxdet['bundleid']) ){
                    foreach ($trxdetails as $trxdet) { 
                        foreach ($bundles as $bundle){
                            if (($trxdet['transactionid'] === $transactions['id']) && ($trxdet['bundleid'] === $bundle['id']) ) {
                                $bundleName      = $bundle  ['name'];
                                $variantval      = $trxdet  ['value'];
                                ?>
                                <div class="uk-margin-small uk-text-xsmall">
                                    <div>
                                        x<?=$trxdet['qty']?> Bundle <br> <?= $bundleName?> <br>
                                        <div class="uk-grid-collapse" uk-grid>
                                            <div class="uk-width-2-3"> @<?=$variantval?></div>
                                            <div class="uk-width-1-3"><?=$variantval * $trxdet['qty']?></div>
                                        </div>
                                        <?php 
                                        foreach ($bundets as $bundet){
                                            foreach ($products as $product) { 
                                                foreach ($variants as $variant){    
                                                    $productName     = $product ['name']; 
                                                    $variantName     = $variant ['name'];
                                                    if(($variant['id'] === $bundet['variantid'])  && ($product['id'] === $variant['productid'])&& 
                                                    ($trxdet['bundleid'] === $bundet['bundleid']) && ($bundle['id'] === $bundet['bundleid'])){
                                                        echo "# ".$productName."-".$variantName."</br>";
                                                    }
                                                }
                                            }
                                        }?>
                                    </div>
                                </div>
                                <?php 
                            } 
                        } 
                    } 
                } 
                ?>
                <!-- end bundle -->
                    
                <hr style ="border-top: 1px solid black;">
                <div class="uk-margin-small uk-text-xsmall">
                    <div class="uk-grid-collapse" uk-grid>
                        <div class="uk-width-2-3 uk-text-bold">Subtotal</div>
                        <div class="uk-width-1-3  uk-text-bold"><?=$subtotal?></div>
                    </div>
                    <div class="uk-grid-collapse" uk-grid>
                        <?php if ($transactions['memberid'] !== "0") {
                            $memberdisc = $gconfig['memberdisc'];
                            echo "<div class='uk-width-2-3'>Discount Poin</div>";
                            echo "<div class='uk-width-1-3'>$memberdisc</div>";
                        }?> 
                    </div>
                    <div class="uk-grid-collapse" uk-grid>
                        <div class="uk-width-2-3 uk-text-bold">Total</div>
                        <div class="uk-width-1-3  uk-text-bold"><?=$total?></div>
                    </div>
                    <div class="uk-grid-collapse" uk-grid>
                        <?php if ($pay !== "0"){
                            echo "<div class='uk-width-2-3'>Pay</div>";
                            echo "<div class='uk-width-1-3'>$pay</div>";
                        }?>
                    </div>
                    <div class="uk-grid-collapse" uk-grid>
                        <?php if ($change !== "0"){
                            echo "<div class='uk-width-2-3'> change </div>";
                            echo "<div class='uk-width-1-3'>$change</div>";
                        }?>
                    </div>
                    <div class="uk-grid-collapse" uk-grid>
                        <?php if ($transactions['memberid'] !== "0") {
                            $cust = $cust['name'];
                            echo "<div class='uk-width-2-3'>Customer</div>";
                            echo "<div class='uk-width-1-3'>$cust</div>";
                        }?> 
                    </div>
                    <div class="uk-grid-collapse" uk-grid>
                        <?php if ($transactions['memberid'] !== "0") {
                            $poinearn = $gconfig['poinorder'];
                            echo "<div class='uk-width-2-3'>Point Earned</div>";
                            echo "<div class='uk-width-1-3'>$poinearn</div>";
                        }?> 
                    </div>
                    <div class="uk-grid-collapse" uk-grid>
                        <?php if ($transactions['memberid'] !== "0") {
                            echo " <div class='uk-width-2-3'>Total Poin</div>";
                            echo "<div class='uk-width-1-3'>$mempoin</div>";
                        }?> 
                    </div>
                </div>

                <div class="uk-margin uk-text-center">#VapingSambilNongkrong</div>
                <hr/>
            </div>
        </div>
        <div class="uk-width-1-1@m uk-text-center@m uk-margin-medium-top" id="btn" style=" align-text:center;">
            <button type="button" class="uk-button uk-button-primary uk-preserve-color" onclick="printOut()">Print Invoice</button>
            <button type="button" class="uk-button uk-button-primary uk-preserve-color" uk-toggle="target: #tambahdata">Send Invoice</button>
            <button type="button" class="uk-button uk-button-primary uk-preserve-color" uk-toggle="target: #tambahdata">Back To Transaction</button>
        </div>
    </body>
</html>
<script>
  var lama = 1000;
  t = null;
  function printOut(){
      window.print();
      t = setTimeout("self.close()",lama);
  }
</script>