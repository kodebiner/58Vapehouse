<?= $this->extend('layout') ?>

<?= $this->section('extraScript') ?>
<script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
<script src="js/cdn.datatables.net_1.13.4_js_jquery.dataTables.min.js"></script>
<?= $this->endSection() ?>

<?= $this->section('main') ?>

<!-- Page Heading -->
<div class="tm-card-header uk-light">
    <div uk-grid class="uk-flex-middle">
        <div class="uk-width-1-2@m">
            <h3 class="tm-h3"><?=lang('Global.stockCycle')?></h3>
        </div>
    </div>
</div>
<!-- End Of Page Heading -->

<?= view('Views/Auth/_message_block') ?>

<!-- Table Of Content -->
<div class="uk-overflow-auto uk-margin">
    <table class="uk-table uk-table-justify uk-table-middle uk-table-divider uk-light" id="example" style="width:100%">
        <thead>
            <tr>
                <th class="uk-text-center">No</th>
                <th class=""><?=lang('Global.variant')?></th>
                <th class=""><?=lang('Global.restock')?></th>
                <th class=""><?=lang('Global.sale')?></th>
                <th class=""><?=lang('Global.quantity')?></th>
                <th class=""><?=lang('Global.information')?></th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1 ; ?>
            <?php foreach ($stocks as $stock) {
                if (($stock['restock'] != "0000-00-00 00:00:00") && ($stock['sale'] != '0000-00-00 00:00:00')) { ?>
                    <tr>
                        <td class="uk-text-center"><?= $i++; ?></td>
                        <td class="">
                                <?php foreach ($variants as $variant) {
                                    if ($variant['id'] === $stock['variantid']) {
                                        echo ($variant['name']);
                                    }
                                } ?>
                        </td>
                        <td class=""><?= date('l, d M Y, H:i:s', strtotime($stock['restock'])); ?></td>
                        <td class=""><?= date('l, d M Y, H:i:s', strtotime($stock['sale'])); ?></td>
                        <td class=""><?= $stock['qty']; ?></td>
                        <td>
                            <?php
                                $today      = $stock['restock'];
                                $date       = date_create($today);
                                date_add($date, date_interval_create_from_date_string('0 days'));
                                $newdate    = date_format($date, 'Y-m-d H:i:s');
                                if ($stock['sale'] > $newdate) {
                                    $origin         = new DateTime($stock['sale']);
                                    $target         = new DateTime('now');
                                    $interval       = $origin->diff($target);
                                    $formatday      = substr($interval->format('%R%a'), 1);
                                    $stockremind    = lang('Global.stockremind');
                                    $saleremind     = lang('Global.saleremind');
                                    if ($formatday >= 0) {
                                        echo '<div class="uk-text-danger uk-width-1-1">'.$saleremind.' '.$formatday.' '.lang('Global.day').'</div>';
                                    }
                                }
                            ?>
                        </td>
                    </tr>
                <?php }
            } ?>
        </tbody>
    </table>
</div>
<!-- End Of Table Content -->

<!-- Search Engine Script -->
<script>
  $(document).ready(function () {
    $('#example').DataTable();
  });
</script>
<!-- Search Engine Script End -->

<?= $this->endSection() ?>