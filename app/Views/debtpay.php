<?= $this->extend('layout') ?>
<?= $this->section('extraScript') ?>
    <script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
    <script src="js/cdn.datatables.net_1.13.4_js_jquery.dataTables.min.js"></script>
<?= $this->endSection() ?>
<?= $this->section('main') ?>

<!-- Page Heading -->
<div class="tm-card-header uk-light">
    <div class="uk-flex-middle">
        <h3 class="tm-h3"><?=lang('Global.debtInstallmentsList')?></h3>
    </div>
</div>
<!-- End Of Page Heading -->

<!-- Table Of Content -->
<div class="uk-overflow-auto uk-margin">
    <table class="uk-table uk-table-justify uk-table-middle uk-table-divider uk-light" id="example" style="width:100%">
        <thead>
            <tr>
                <th class=""><?=lang('Global.date')?></th>
                <th class=""><?=lang('Global.outlet')?></th>
                <th class=""><?=lang('Global.description')?></th>
                <th class="uk-text-center"><?=lang('Global.quantity')?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($trxothers as $trx) { ?>
                <tr>
                    <td class=""><?= date('l, d M Y, H:i:s', strtotime($trx['date'])); ?></td>
                    <td>
                        <?php foreach ($outlets as $outlet) {
                            if ($outlet['id'] === $trx['outletid']) {
                                echo $outlet['name'];
                            }
                        } ?>
                    </td>
                    <td class=""><?= $trx['description'];?></td>
                    <td class="uk-text-center">Rp <?= number_format($trx['qty'],2,',','.');?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<!-- Table Of Content End -->

<!-- Search Engine Script -->
<script>
    $(document).ready(function () {
        $('#example').DataTable();
    });
</script>
<!-- Search Engine Script End -->
<?= $this->endSection() ?>