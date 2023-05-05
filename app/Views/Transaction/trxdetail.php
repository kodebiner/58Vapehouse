<?= $this->extend('Transaction/layout') ?>
<?= $this->section('trxdetail') ?>

    <div class="tm-card-header uk-text-center uk-flex-middle">
        <h3 class="tm-h1 uk-text-bolder uk-text-italic uk-margin-small-bottom uk-margin-small-top"><?=lang('Global.detailOrder');?></h3>
    </div>
    <div class="tm-card-body">
        <h4 class="tm-h2 uk-margin-top uk-margin-left"><?=lang('Global.customer');?></h4>
        <div class="uk-overflow-auto">
            <table class="uk-table uk-table-justify uk-table-middle uk-table-divider">
                <thead>
                    <tr>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $transaction) : ?>
                        <tr>
                        <td class="uk-text-center"><?= $transaction['name']; ?></td>
                        <td class="uk-text-center"><?= $transaction['address']; ?></td>
                        <td class="uk-text-center"><?= $transaction['maps']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?= $this->endSection() ?>