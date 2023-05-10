<?= $this->extend('Transaction/layout') ?>
<?= $this->section('main') ?>

    <div class="uk-child-width-1-2 uk-child-width-1-5@m" uk-grid uk-height-match="target: > div > .uk-card > .uk-card-header">
        <?php foreach ($variants as $variant) : ?>
            <?php
                foreach ($products as $product) {
                    if ($product['id'] === $variant['productid']) {
                        $productName = $product['name'];
                        $productPhoto = $product['photo'];
                    }
                }
            ?>
            <div onClick="createNewOrder">
                <div class="uk-card uk-card-hover uk-card-default">
                    <div class="uk-card-header">
                        <div class="tm-h1 uk-text-bolder uk-text-center"><?= $productName.' - '. $variant['name'] ?></div>
                    </div>
                    <div class="uk-card-body">
                        <div class=""><?= $productPhoto ?></div>
                    </div>
                    <div class="uk-card-footer">
                        <div class="tm-h3 uk-text-center">
                            <div>Rp <?= $variant['hargamodal'] + $variant['hargajual'] ?>,-</div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        
        <script>
            function createNewOrder(){

                const createOrder = document.getElementById("instab");
                newCreateOrder.setAttribute('id','create'+createCount);
                newCreateOrder.setAttribute('class','uk-margin uk-child-width-1-5');
                newCreateOrder.setAttribute('uk-grid','');
            }
        </script>
    </div>
<?= $this->endSection() ?>