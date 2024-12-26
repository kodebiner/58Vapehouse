<!doctype html>
<html dir="ltr "lang="<?=$lang?>" vocab="http://schema.org/" style="background-color:#000;">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <base href="<?=base_url();?>">
        <title>Invoice Kasbon</title>
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
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@600;700&display=swap" rel="stylesheet">
        <script src="js/uikit.min.js"></script>
        <script src="js/uikit-icons.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
        <style>
            @media print {  
                #btn{
                    display : none;
                }
            }
        </style>
    </head>
    <body style="background-color:#000;">
        <?php foreach ($debts as $debt) { ?>
            <div class="uk-flex uk-flex-center uk-background-secondary">
                <div class="uk-child-width-1-3 uk-flex uk-flex-center" id="btn" uk-grid>
                    <div class="uk-text-center uk-margin-top">
                        <a class="uk-icon-button" uk-icon="arrow-left" href="<?= base_url('debt') ?>"></a>
                    </div>
                    <div class="uk-text-center uk-margin">
                        <a class="uk-icon-button" uk-icon="print" onclick="printOut()"></a>
                    </div>
                    <div class="uk-text-center uk-margin">
                        <a class='uk-icon-button' uk-icon='whatsapp' href="https://wa.me/62<?= $debt['phone'] ?>?text=<?= $links ?>"></a>
                    </div>
                </div>
            </div>
            <div class="uk-flex uk-flex-center">
                <div class="uk-padding-small" style="width:100mm; background: #fff;">
                    <div class="uk-margin-small uk-margin-top uk-width-1-1 uk-text-center">
                        <?php if (($gconfig['logo'] != null) && ($gconfig['bizname'] != null)) { ?>
                            <img src="/img/<?=$gconfig['logo'];?>" alt="<?=$gconfig['bizname'];?>" style="height: 60px;">
                        <?php } else { ?>
                            <img src="/img/binary111-logo-icon.svg" alt="PT. Kodebiner Teknologi Indonesia" style="height: 60px;">
                        <?php } ?>
                    </div>

                    <div class="uk-flex uk-flex-center">
                        <div class="fpoutlet uk-margin-remove uk-text-justify" style="font-size:12px;"><?= $debt['outlet'] ?></div>
                    </div>
                    <div class="uk-flex uk-flex-center">
                        <p class="fpaddress uk-margin-remove uk-text-bold uk-text-center" style="font-size:10px;"><?= $debt['address'] ?></p>
                    </div>
                    <div class="uk-flex uk-flex-center">
                        <p class="fpaddress uk-margin-remove uk-text-bold uk-text-center" style="font-size:10px;"><span uk-icon="instagram" style="width: 10px;"></span> : <?= $debt['outletig'] ?></p>
                    </div>
                    <div class="uk-flex uk-flex-center">
                        <p class="fpaddress uk-margin-remove uk-text-bold uk-text-center" style="font-size:10px;"><span uk-icon="whatsapp" style="width: 10px;"></span> : +<?= $debt['outletwa'] ?></p>
                    </div>
                        
                    <div class="uk-text-xsmall uk-margin-top">
                        <div uk-grid>
                            <div class="uk-width-1-2"><?=lang('Global.invoice')?>: <?= (strtotime("now")) ?></div>
                            <div class="uk-width-1-2 uk-text-right"><?= date('l, d M Y, H:i:s', strtotime($debt['trxdate'])); ?></div>
                        </div>
                        <div class="uk-margin-remove-top" uk-grid>
                            <div class="uk-width-2-3">Cashier: <?= $debt['cashier'] ?></div>
                            <div class="uk-text-right uk-width-1-3"><?= lang('Global.debt')?></div>
                        </div>

                        <hr style ="border-top: 3px double #8c8b8b">

                        <!-- <div class="uk-margin-remove-top" uk-grid> -->
                            <div class="uk-text-center uk-width-1-1 uk-margin-remove-top"><?= lang('Global.customer') ?></div>
                            <div class="uk-text-center uk-width-1-1 uk-margin-remove-top"><?= $debt['name'] ?></div>
                        <!-- </div> -->
                    </div>
                    
                    <hr style ="border-top: 3px double #8c8b8b">

                    <!-- variant transaction -->
                    <?php foreach ($debt['detailvar'] as $detailvar) { ?>
                        <div class="uk-margin-small uk-text-xsmall">
                            <div class="uk-text-bold"><?= $detailvar['name'] ?></div>
                            <div class="uk-grid-collapse" uk-grid>
                                <div class="uk-width-1-2">x<?= $detailvar['qty'] ?> @<?= $detailvar['value'] ?></div>
                                <div class="uk-width-1-2 uk-text-right"><?= (Int)$detailvar['value'] * (Int)$detailvar['qty'] ?></div>
                            </div>
                            <div class="uk-grid-collapse" uk-grid>
                                <?php
                                if ($detailvar['discvar'] != '0') {
                                    echo "<div class='uk-width-1-2'>(".$detailvar['discvar'].")</div>";
                                    echo "<div class='uk-width-1-2 uk-text-right'>-" .$detailvar['discvar']. "</div>";
                                }
                                if ($detailvar['globaldisc'] != "0") {
                                    echo "<div class='uk-width-1-2'>(".$detailvar['globaldisc'].")</div>";
                                    echo "<div class='uk-width-1-2 uk-text-right'>-" .$detailvar['globaldisc']. "</div>";
                                }
                                ?>
                            </div>
                        </div>
                    <?php } ?>
                    <!-- end variant -->

                    <!-- bundle transaction -->
                    <?php foreach ($debt['detailbun'] as $detailbun) { ?>
                        <div class="uk-margin-small uk-text-xsmall">
                            <div>
                                x<?= $detailbun['qty']?> <?=lang('Global.bundle')?> <br> <?= $detailbun['name'] ?> <br>
                                <div class="uk-grid-collapse" uk-grid>
                                    <div class="uk-width-2-3"> @<?= $detailbun['value'] ?></div>
                                    <div class="uk-width-1-3 uk-text-right"><?= (Int)$detailbun['value'] * (Int)$detailbun['qty']?></div>
                                    <?php
                                    if ($detailbun['globaldisc'] != "0") {
                                        echo "<div class='uk-width-1-2'>(".$detailbun['globaldisc'].")</div>";
                                        echo "<div class='uk-width-1-2 uk-text-right'>-" .$detailbun['globaldisc']. "</div>";
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <!-- end bundle -->

                    <hr style ="border-top: 3px double #8c8b8b">

                    <!-- total -->
                    <div class="uk-margin-small uk-text-xsmall">
                        <div class="uk-grid-collapse" uk-grid>
                            <div class="uk-width-1-2 uk-text-bold "><?= lang('Global.subtotal') ?></div>
                            <div class="uk-width-1-2 uk-text-bold uk-text-right"><?= $debt['subtotal'] ?></div>
                        </div>
                        <div class="uk-grid-collapse" uk-grid>
                            <?php
                                echo "<div class='uk-width-1-2'>".lang('Global.discount')." Transaksi</div>";
                                if ($debt['trxdisc'] != '0') {
                                    echo "<div class='uk-width-1-2 uk-text-right'>".$debt['trxdisc']."</div>";
                                } else {
                                    echo "<div class='uk-width-1-2 uk-text-right'>0</div>";
                                }
                            ?>
                        </div>
                        <div class="uk-grid-collapse" uk-grid>
                            <?php
                                echo "<div class='uk-width-1-2'>".lang('Global.memberDiscount')."</div>";
                                if ($gconfig['memberdisc'] != '0') {
                                    echo "<div class='uk-width-1-2 uk-text-right'>".$gconfig['memberdisc']."</div>";
                                } else {
                                    echo "<div class='uk-width-1-2 uk-text-right'>0</div>";
                                }
                            ?> 
                        </div>
                        <div class="uk-grid-collapse" uk-grid>
                            <?php
                                echo "<div class='uk-width-1-2'>".lang('Global.redeemPoint')."</div>";
                                if (($debt['trxpoin']) != '0') {
                                    echo "<div class='uk-width-1-2 uk-text-right'>".$debt['trxpoin']."</div>";
                                } else {
                                    echo "<div class='uk-width-1-2 uk-text-right'>0</div>";
                                }
                            ?> 
                        </div>

                        <hr style ="border-top: 3px double #8c8b8b">  
                        
                        <div class="uk-grid-collapse" uk-grid>
                            <div class="uk-width-1-2 uk-text-bold"><?= lang('Global.total') ?></div>
                            <div class="uk-width-1-2 uk-text-bold uk-text-right"><?= $debt['trxvalue'] ?></div>
                        </div>
                        <div class="uk-grid-collapse" uk-grid>
                            <?php if (!empty($debt['installment'])) {
                                echo " <div class='uk-width-1-1 uk-text-bold'>Cicilan Kasbon</div>";
                                foreach ($debt['installment'] as $debtins) {
                                    echo " <div class='uk-width-1-2 uk-text-bold'>".date('l, d M Y, H:i:s', strtotime($debtins['date']))."</div>";
                                    echo "<div class='uk-width-1-2 uk-text-bold uk-text-right' style='color: green'>- ".$debtins['value']."</div>";
                                }
                            }?> 
                        </div>

                        <hr style ="border-top: 3px double #8c8b8b">  

                        <div class="uk-grid-collapse" uk-grid>
                            <?php
                                echo " <div class='uk-width-1-2 uk-text-bold'>Sisa Kasbon</div>";
                                echo "<div class='uk-width-1-2 uk-text-bold uk-text-right' style='color: red'>".$debt['debtval']."</div>";
                            ?> 
                        </div>
                    </div>
                    
                    <div class="uk-width-1-1 uk-text-bold">
                        <div class="uk-text-center fptagline" style="font-size: 10px;">#VapingSambilNongkrong</div>
                    </div>

                    <hr>
                </div>
            </div>
            <script>
                var lama = 1000;
                t = null;
                function printOut(){
                    window.print();
                    t = setTimeout("self.close()",lama);
                }
            </script>
        <?php } ?>
    </body>
</html>