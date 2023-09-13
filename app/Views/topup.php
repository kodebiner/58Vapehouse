<?= $this->extend('layout') ?>
<?= $this->section('extraScript') ?>
    <script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
<?= $this->endSection() ?>
<?= $this->section('main') ?>

<!-- Page Heading -->
<div class="tm-card-header uk-light">
    <div class="uk-flex-middle">
        <h3 class="tm-h3"><?=lang('Global.topupList')?></h3>
    </div>
</div>
<!-- End Of Page Heading -->

<!-- Table Of Content -->
<table class="uk-table uk-table-justify uk-table-middle uk-table-divider uk-light">
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
                <td class=""><?= $trx['date'];?></td>
                <td>
                    <?php foreach ($outlets as $outlet) {
                        if ($outlet['id'] === $trx['outletid']) {
                            echo $outlet['name'];
                        }
                    } ?>
                </td>
                <td class=""><?= $trx['description'];?></td>
                <td class="uk-text-center"><?= $trx['qty'];?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>
<!-- Table Of Content End -->
<?= $this->endSection() ?>