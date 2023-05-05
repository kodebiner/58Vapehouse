<?= $this->extend('Transaction/layout') ?>
<?= $this->section('main') ?>

    <div class="uk-child-width-1-5@m" uk-grid>
        <?php foreach ($variants as $variant) : ?>
            <?php foreach ($products as $product) { ?>
                <div>
                    <div class="uk-card uk-card-hover uk-card-default">
                        <div class="uk-card-header">
                            <div class="tm-h1 uk-text-bolder uk-text-center"><?= $product['name'].' - '. $variant['name'] ?></div>
                        </div>
                        <div class="uk-card-body">
                            <div class=""><?= $product['photo'] ?></div>
                        </div>
                        <div class="uk-card-footer">
                            <div class="tm-h3">
                                <div>Rp <?= $variant['hargamodal'] + $variant['hargajual'] ?>,-</div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php endforeach; ?>
    </div>
<?= $this->endSection() ?>