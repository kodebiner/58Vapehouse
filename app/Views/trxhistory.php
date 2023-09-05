<?= $this->extend('layout') ?>
<?= $this->section('extraScript') ?>
    <script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
<?= $this->endSection() ?>
<?= $this->section('main') ?>

<!-- Page Heading -->
<div class="tm-card-header uk-light">
    <div uk-grid class="uk-flex-middle">
        <div class="uk-width-1-2@m">
            <h3 class="tm-h3"><?=lang('Global.trxHistory')?></h3>
        </div>
    </div>
</div>
<!-- End Of Page Heading -->

<!-- Table Of Content -->
<table class="uk-table uk-table-justify uk-table-middle uk-table-divider uk-light">
    <thead>
        <tr>
            <th class="uk-width-small uk-text-center"><?= lang('Global.date') ?></th>
            <th class="uk-width-small uk-text-center"><?= lang('Global.outlet') ?></th>
            <th class="uk-width-small uk-text-center"><?= lang('Global.employee') ?></th>
            <th class="uk-width-small uk-text-center"><?= lang('Global.paymethod') ?></th>
            <th class="uk-width-small uk-text-center"><?= lang('Global.total') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($transactions as $transaction) { ?>
            <tr>
                <td class="uk-width-small uk-text-center"><?= $transaction['date'] ?></td>

                <?php foreach ($outlets as $outlet) {
                    if ($outlet['id'] === $transaction['outletid']) { ?>
                        <td class="uk-width-small uk-text-center"><?= $outlet['name'] ?></td>
                    <?php }
                } ?>

                <?php foreach ($users as $user) {
                    if ($user->id === $transaction['userid']) {?>
                        <td class="uk-width-small uk-text-center"><?= $user->name ?></td>
                    <?php }
                } ?>

                <?php foreach ($payments as $payment) {
                    if ($payment['id'] === $transaction['paymentid']) { ?>
                        <td class="uk-width-small uk-text-center"><?= $payment['name'] ?></td>
                    <?php }
                } ?>

                <td class="uk-width-small uk-text-center">
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

    <!-- <ul class="uk-flex-around tm-trx-tab uk-margin-top" uk-tab uk-switcher="connect: .switcher-class; active: 1;">
        <li>
            <a style="border-radius: 10px;" uk-switcher-item="0">
                <div class="uk-h4 uk-margin-small"><?= lang('Global.trxhistory') ?></div>
            </a>
        </li>
        <li>
            <a style="border-radius: 10px;" uk-switcher-item="0">
                <div class="uk-h4 uk-margin-small"><?= lang('Global.debt') ?></div>
            </a>
        </li>
    </ul> -->
<?= $this->endSection() ?>