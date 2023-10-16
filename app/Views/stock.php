<?= $this->extend('layout') ?>

<?= $this->section('extraScript') ?>
<script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
<?= $this->endSection() ?>

<?= $this->section('main') ?>

<!-- Page Heading -->
<div class="tm-card-header uk-light">
    <div uk-grid class="uk-flex-middle">
        <div class="uk-width-1-2@m">
            <h3 class="tm-h3"><?=lang('Global.stockList')?></h3>
        </div>
    </div>
</div>
<!-- End Of Page Heading -->

<?= view('Views/Auth/_message_block') ?>

<!-- Table Of Content -->
<div class="uk-overflow-auto uk-margin">
    <!-- Counter Total -->
    <div class="uk-light" uk-grid>
        <!-- Product-->
        <div class="uk-width-1-3 uk-width-1-6@m uk-form-horizontal">
            <div class="uk-form-label uk-margin-top" style="width: 100px;"><?= lang('Global.total') ?> <?= lang('Global.product') ?> :</div>
            <div class="uk-form-controls uk-margin-top uk-margin-remove-left"><?= $stockcount ?></div>
        </div>
        <!-- Product End -->

        <!-- Stock -->
        <div class="uk-width-1-3 uk-width-1-6@m uk-form-horizontal">
            <div class="uk-form-label uk-margin-top" style="width: 100px;"><?= lang('Global.total') ?> <?= lang('Global.stock') ?> :</div>
            <div class="uk-form-controls uk-margin-top uk-margin-remove-left"><?= $totalstock ?></div>
        </div>
        <!-- Stock End -->

        <!-- Capital Price -->
        <div class="uk-width-1-3 uk-width-1-3@m uk-form-horizontal">
            <div class="uk-form-label uk-margin-top" style="width: 120px;"><?= lang('Global.total') ?> <?= lang('Global.capitalPrice') ?> :</div>
            <div class="uk-form-controls uk-margin-top uk-margin-remove-left">Rp <?= number_format($capsum,2,',','.') ?></div>
        </div>
        <!-- Capital Price End -->
    </div>
    <!-- Counter Total End -->
    <table class="uk-table uk-table-justify uk-table-middle uk-table-divider uk-light">
        <thead>
            <tr>
                <th class="uk-text-center">No</th>
                <th class=""><?=lang('Global.outlet')?></th>
                <th class=""><?=lang('Global.product')?></th>
                <th class="uk-text-center"><?=lang('Global.stock')?></th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1 ; ?>
            <?php foreach ($stocks as $stock) : ?>
                <tr>
                    <td class="uk-text-center"><?= $i++; ?></td>
                    <td class="">
                        <?php foreach ($outlets as $outlet ) {
                            if ($stock['outletid']=== $outlet['id']) {
                                echo $outlet['name'];
                            }
                        } ?>
                    </td>
                    <td class="">
                        <?php foreach ($variants as $variant ) {
                            foreach ($products as $product) {
                                if(($stock['variantid'] === $variant['id']) && ($product['id'] === $variant['productid'])) {
                                    echo $product['name'].' - '.$variant['name'];
                                }
                            }
                        } ?>
                    </td>
                    <td class="uk-text-center"><?= $stock['qty']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div>
        <?= $pager->links('stock', 'front_full') ?>
    </div>
</div>
<!-- End Table Content -->
<?= $this->endSection() ?>