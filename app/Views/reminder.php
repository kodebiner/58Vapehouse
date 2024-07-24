<?= $this->extend('layout') ?>

<?= $this->section('extraScript') ?>
<script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
<?= $this->endSection() ?>

<?= $this->section('main') ?>

<!-- Page Heading -->
<div class="tm-card-header uk-light">
    <h3 class="tm-h3"><?=lang('Global.reminder')?></h3>
</div>
<!-- Page Heading End -->

<?= view('Views/Auth/_message_block') ?>

<!-- Table Of Content -->
<div class="uk-overflow-auto uk-margin">
    <table class="uk-table uk-table-justify uk-table-middle uk-table-divider uk-light">
        <thead>
            <tr>
                <th class="uk-width-medium"><?= lang('Global.outlet') ?></th>
                <th class="uk-width-medium"><?=lang('Global.product')?></th>
                <th class="uk-width-medium"><?=lang('Global.variant')?></th>
                <th class="uk-text-center uk-width-large"><?=lang('Global.reminder')?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($stocks as $stock) {
                $today      = $stock['restock'];
                $date       = date_create($today);
                date_add($date, date_interval_create_from_date_string('30 days'));
                $newdate    = date_format($date, 'Y-m-d H:i:s');
                if ($stock['sale'] > $newdate || $stock['qty'] == "0") {
                    foreach ($products as $product) {
                        foreach ($variants as $variant) {
                            $origin         = new DateTime($stock['sale']);
                            $target         = new DateTime('now');
                            $interval       = $origin->diff($target);
                            $formatday      = substr($interval->format('%R%a'), 1);
                            
                            if (($variant['id'] === $stock['variantid']) && ($variant['productid'] === $product['id'])) {
                                $productname    = $product['name'];
                                $varname        = $variant['name'];
                                $stockremind    = lang('Global.stockremind');
                                $saleremind     = lang('Global.saleremind'); ?>
                                <tr>
                                    <td class="uk-width-medium">
                                        <?php foreach ($outlets as $out) {
                                            if ($out['id'] === $stock['outletid']) {
                                                echo $out['name'];
                                            }
                                        } ?>
                                    </td>
                                    <td class="uk-width-medium"><?= $productname ?></td>
                                    <td class="uk-width-medium"><?= $varname ?></td>
                                    <td class="uk-text-center uk-width-large">
                                        <?php
                                            if (($formatday >= 30) && ($stock['qty'] <= "5")) {
                                                echo '<div class="uk-child-width-1-1" uk-grid><div><div class="uk-text-danger" style="border-style: solid; border-color: #f0506e;">'.$saleremind.' '.$formatday.' '.lang('Global.day').'</div></div><div class="uk-margin-small-top"><div class="uk-text-danger" style="border-style: solid; border-color: #f0506e;">'.$stockremind.'</div></div></div>';
                                            } elseif ($formatday >= 30) {
                                                echo '<div class="uk-text-danger uk-width-1-1" style="border-style: solid; border-color: #f0506e;">'.$saleremind.' '.$formatday.' '.lang('Global.day').'</div>';
                                            } elseif ($stock['qty'] <= "5") {
                                                echo '<div class="uk-text-danger uk-width-1-1" style="border-style: solid; border-color: #f0506e;">'.$stockremind.'</div>';
                                            }
                                        ?>
                                    </td>
                                </tr>
                            <?php }
                        }
                    }
                }
            } ?>
        </tbody>
    </table>
    <div>
        <?= $pager->links('reminder', 'front_full') ?>
    </div>
</div>
<!-- End Table Content -->
<?= $this->endSection() ?>