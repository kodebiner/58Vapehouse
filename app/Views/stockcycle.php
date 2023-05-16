<?= $this->extend('layout') ?>

<?= $this->section('main') ?>

<!-- Page Heading -->
<div class="tm-card-header uk-light">
    <?= view('Views/Auth/_message_block') ?>

    <div uk-grid class="uk-flex-middle">
        <div class="uk-width-1-2@m">
            <h3 class="tm-h3"><?=lang('Global.stockCycle')?></h3>
        </div>
    </div>
</div>
<!-- End Of Page Heading -->

<!-- Table Of Content -->
<div class="uk-overflow-auto">
    <table class="uk-table uk-table-justify uk-table-middle uk-table-divider uk-light">
        <thead>
            <tr>
                <th class="uk-text-center">No</th>
                <th class=""><?=lang('Global.variant')?></th>
                <th class=""><?=lang('Global.restock')?></th>
                <th class=""><?=lang('Global.sale')?></th>
                <th class=""><?=lang('Global.quantity')?></th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1 ; ?>
            <?php foreach ($stocks as $stock) : ?>
                <tr>
                    <td class="uk-text-center"><?= $i++; ?></td>
                    <td class="">
                            <?php foreach ($variants as $variant) {
                                if ($variant['id'] === $stock['variantid']) {
                                    echo ($variant['name']);
                                }
                            } ?>
                    </td>
                    <td class=""><?= $stock['restock']; ?></td>
                    <td class=""><?= $stock['sale']; ?></td>
                    <td class=""><?= $stock['qty']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <!-- Table Pagination -->
    <ul class="uk-pagination uk-flex-right uk-margin-medium-top uk-light" uk-margin>
        <li><a href="#"><span uk-pagination-previous></span></a></li>
        <li><a href="#">1</a></li>
        <li class="uk-disabled"><span>…</span></li>
        <li><a href="#">4</a></li>
        <li><a href="#">5</a></li>
        <li><a href="#">6</a></li>
        <li><a href="#">7</a></li>
        <li><a href="#">8</a></li>
        <li><a href="#">9</a></li>
        <li><a href="#">10</a></li>
        <li class="uk-disabled"><span>…</span></li>
        <li><a href="#">20</a></li>
        <li><a href="#"><span uk-pagination-next></span></a></li>
    </ul>
    <!-- Table Pagination End-->
</div>
<!-- End Of Table Content -->


<?= $this->endSection() ?>