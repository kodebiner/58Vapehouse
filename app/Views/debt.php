<?= $this->extend('layout') ?>
<?= $this->section('extraScript') ?>
    <script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
<?= $this->endSection() ?>
<?= $this->section('main') ?>

<!-- Page Heading -->
<div class="tm-card-header uk-light">
    <div class="uk-flex-middle">
        <h3 class="tm-h3"><?=lang('Global.debtList')?></h3>
    </div>
</div>
<!-- End Of Page Heading -->

<!-- Table Of Content -->
<table class="uk-table uk-table-justify uk-table-middle uk-table-divider uk-light">
    <thead>
        <tr>
            <th class="uk-text-center"></th>
            <th class=""><?= lang('Global.date') ?></th>
            <th class=""><?= lang('Global.outlet') ?></th>
            <th class=""><?= lang('Global.employee') ?></th>
            <th class=""><?= lang('Global.paymethod') ?></th>
            <th class=""><?= lang('Global.total') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($transactions as $transaction) { ?>
            <tr>
                <td class="uk-flex uk-flex-center">
                    <a class="uk-icon-link uk-icon" uk-toggle="target:#detail-<?= $transaction['id'] ?>" uk-icon="search"></a>
                </td>

                <td class=""><?= $transaction['date'] ?></td>

                <?php foreach ($outlets as $outlet) {
                    if ($outlet['id'] === $transaction['outletid']) { ?>
                        <td class=""><?= $outlet['name'] ?></td>
                    <?php }
                } ?>

                <?php foreach ($users as $user) {
                    if ($user->id === $transaction['userid']) {?>
                        <td class=""><?= $user->name ?></td>
                    <?php }
                } ?>

                <?php foreach ($payments as $payment) {
                    if ($payment['id'] === $transaction['paymentid']) { ?>
                        <td class=""><?= $payment['name'] ?></td>
                    <?php }
                } ?>

                <td class="">
                    <?php
                    $prices = array();
                    foreach ($trxdetails as $trxdet) {
                        if ($trxdet['transactionid'] === $transaction['id']) {
                            $total = $trxdet['qty'] * $trxdet['value'];
                            $prices [] = $total;
                        } ?>
                    <?php }
                    $sum = array_sum($prices);
                    echo "Rp " . number_format($sum,2,',','.'); ?>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>
<!-- Table Of Content End -->
<?= $this->endSection() ?>