<?= $this->extend('Transaction/layout') ?>
<?= $this->section('main') ?>

    <div class="uk-padding uk-child-width-auto uk-grid-small uk-grid-match" uk-grid>
        <div class="uk-text-center">
            <?php foreach ($products as $product) : ?>
                <div class="uk-card uk-card-hover uk-card-default uk-card-body uk-margin">
                    <div class="uk-card-title">
                        <div class="tm-h1 uk-text-bolder"><?= $product['name']; ?></div>
                    </div>
                    <div class="uk-card-body">
                        <div class=""><?= $product['photo']; ?></div>
                    </div>
                    <div class="uk-card-footer">
                        <div class="tm-h3">
                            <?php foreach ($variants as $variant) :
                                if ($variant['productid'] === $product['id']) : ?>
                                    <div class="sale">Rp <?= $variant['hargamodal'] + $variant['hargajual'] ?>,-</div>
                                <?php endif;
                            endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <div>
    </div>
<?= $this->endSection() ?>