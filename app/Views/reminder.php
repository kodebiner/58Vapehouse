<?= $this->extend('layout') ?>

<?= $this->section('extraScript') ?>
<script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
<script src="js/cdn.datatables.net_1.13.4_js_jquery.dataTables.min.js"></script>
<?= $this->endSection() ?>

<?= $this->section('main') ?>

<!-- Page Heading -->
<div class="tm-card-header uk-light">
    <?= view('Views/Auth/_message_block') ?>

    <div uk-grid class="uk-flex-middle">
        <div class="uk-width-1-2@m">
            <h3 class="tm-h3"><?=lang('Global.reminder')?></h3>
        </div>

        <?php if ($outletPick != null) { ?>
            <!-- Button Trigger Modal Add -->
            <!-- Button Trigger Modal Add End -->
        <?php } ?>
    </div>
</div>
<!-- Page Heading End -->

<!-- Table Of Content -->
<div class="uk-overflow-auto uk-margin">
    <table class="uk-table uk-table-justify uk-table-middle uk-table-divider uk-light" id="example">
        <thead>
            <tr>
                <th class="uk-text-center uk-width-small">No</th>
                <th class="uk-width-medium"><?=lang('Global.product')?></th>
                <th class="uk-width-medium"><?=lang('Global.variant')?></th>
                <th class="uk-text-center uk-width-large"><?=lang('Global.reminder')?></th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1 ; ?>
            <?php foreach ($stocks as $stock) {
                $today  = $stock['restock'];
                $date   = date_create($today);
                date_add($date, date_interval_create_from_date_string('30 days'));
                $newdate = date_format($date, 'Y/m/d H:i:s');
                if ($stock['sale'] > $newdate || $stock['qty'] < "1") {
                ?>
                <tr>
                    <?php foreach ($products as $product) { ?>
                        <?php foreach($variants as $variant) { ?>
                            <?php if (($variant['id'] === $stock['variantid']) && ($variant['productid'] === $product['id'])) {
                            $productname    = $product['name'];
                            $varname        = $variant['name'];
                            $stockremind    = "Stock Is Running Out";
                            $saleremind     = "The product has not been sold for 1 month"
                            ?>
                                <td class="uk-text-center uk-width-small"><?= $i++; ?></td>
                                <td class="uk-width-medium"><?= $productname ?></td>
                                <td class="uk-width-medium"><?= $varname ?></td>
                                <td class="uk-text-center uk-width-large">
                                    <?php
                                        if (($stock['sale'] > $newdate) && ($stock['qty'] < "1")) {
                                            echo '<div class="uk-child-width-1-1" uk-grid><div><div class="uk-text-danger" style="border-style: solid; border-color: #f0506e;">'.$saleremind.'</div></div><div class="uk-margin-small-top"><div class="uk-text-danger" style="border-style: solid; border-color: #f0506e;">'.$stockremind.'</div></div></div>';
                                        } elseif ($stock['sale'] > $newdate) {
                                            echo '<div class="uk-text-danger uk-width-1-1" style="border-style: solid; border-color: #f0506e;">'.$saleremind.'</div>';
                                        } elseif ($stock['qty'] < "1") {
                                            echo '<div class="uk-text-danger uk-width-1-1" style="border-style: solid; border-color: #f0506e;">'.$stockremind.'</div>';
                                        }
                                    ?>
                                </td>
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
                </tr>
            <?php } }?>
        </tbody>
    </table>
</div>
<!-- End Table Content -->

<!-- Search Engine Script -->
<script>
    $(document).ready(function () {
        $('#example').DataTable();
    });
</script>
<!-- Search Engine Script End -->

<?= $this->endSection() ?>