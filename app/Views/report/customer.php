<?= $this->extend('layout') ?>

<?= $this->section('extraScript') ?>

<script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
<script type="text/javascript" src="js/moment.min.js"></script>
<script type="text/javascript" src="js/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="css/daterangepicker.css" />
<?= $this->endSection() ?>

<?= $this->section('main') ?>

<!-- Page Heading -->
<div class="tm-card-header uk-light uk-margin-bottom">
    <div uk-grid class="uk-flex-middle">
        <div class="uk-width-1-3@m">
            <h3 class="tm-h3 uk-width-1-1@m"><?= lang('Global.report').' '.lang('Global.customer') ?></h3>
        </div>
        <div class="uk-width-expand@m uk-text-right uk-margin-right-remove">
            <form id="short" action="report/customer" method="get">
                <div class="uk-inline">
                    <span class="uk-form-icon uk-form-icon-flip" uk-icon="calendar"></span>
                    <input class="uk-input uk-width-medium uk-border-rounded" type="text" id="daterange" name="daterange" value="<?=date('m/d/Y', $startdate)?> - <?=date('m/d/Y', $enddate)?>" />
                </div>
            </form>
            <script>
                $(function() {
                    $('input[name="daterange"]').daterangepicker({
                        maxDate: new Date(),
                        opens: 'right'
                    }, function(start, end, label) {
                        document.getElementById('daterange').value = start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD');
                        document.getElementById('short').submit();
                    });
                });
            </script>
        </div>
        
        <!-- Button Trigger Modal export -->
        <div class="uk-width-auto@m uk-text-right@m">
            <a type="button" class="uk-button uk-button-primary uk-preserve-color" target="_blank" href="export/customer?daterange=<?=date('Y-m-d', $startdate)?>+-+<?=date('Y-m-d', $enddate)?>"><?=lang('Global.export')?></a>
        </div>
    </div>
</div>

<div uk-grid class="uk-flex-middle uk-margin-bottom">
    <!-- Search Filter -->
    <div class="uk-width-1-2@m">
        <form class="uk-search uk-search-default" method="GET" action="report/customer" style="background-color: #fff; border-radius: 7px;">
            <span uk-search-icon style="color: #000;"></span>
            <input class="uk-search-input" type="search" placeholder="Search" aria-label="Search" name="search" style="border-radius: 7px;">
        </form>
    </div>
    <!-- End Search Filter -->
</div>

<?= view('Views/Auth/_message_block') ?>

<div class="uk-overflow-auto">
    <table class="uk-table uk-table-divider uk-table-middle uk-table-responsive uk-margin-top" id="example">
        <thead>
            <tr>
                <th class="uk-text-small uk-text-bold"><?=lang('Global.detail')?></th>
                <th class="uk-text-large uk-text-bold"><?=lang('Global.name')?></th>
                <th class="uk-text-center uk-text-large uk-text-bold"><?=lang('Global.transaction')?></th>
                <th class="uk-text-center uk-text-large uk-text-bold"><?=lang('Global.value')?></th>
                <th class="uk-text-center uk-text-large uk-text-bold"><?=lang('Global.debt')?></th>
                <th class="uk-text-center uk-text-large uk-text-bold"><?=lang('Global.phone')?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($customers as $customer){ ?>
                <tr>
                    <td>
                        <!-- Button Trigger Modal Detail -->
                        <div>
                            <a class="uk-icon-button" uk-icon="eye" uk-toggle="target: #detail-<?= $customer['id'] ?>"></a>
                        </div>
                        <!-- Button Trigger Modal Detail End -->
                    </td>
                    <td style="color:white;"><?=$customer['name']?></td>
                    <td class="uk-text-center" style="color:white;"><?=$customer['trx']?></td>
                    <td class="uk-text-center" style="color:white;"><?= "Rp. ".number_format($customer['trxvalue'],2,',','.');" ";?></td>
                    <td class="uk-text-center" style="color:white;"><?= "Rp. ".number_format($customer['debt'],2,',','.');" ";?></td>
                    <td class="uk-text-center" style="color:white;"><?="+62".$customer['phone']?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <div>
        <?= $pager->links('member', 'front_full') ?>
    </div>
</div>

<!-- Modal Detail -->
<?php foreach ($customers as $customer) { ?>
    <div uk-modal class="uk-flex-top uk-modal-container" id="detail-<?= $customer['id'] ?>">
        <div class="uk-modal-dialog uk-margin-auto-vertical">
            <div class="uk-modal-content">
                <div class="uk-modal-header">
                    <div class="uk-child-width-1-2" uk-grid>
                        <div>
                            <h5 class="uk-modal-title"><?= lang('Global.detail').' '.lang('Global.purchase') ?></h5>
                        </div>
                        <div class="uk-text-right">
                            <button class="uk-modal-close uk-icon-button-delete" uk-icon="icon: close;" type="button"></button>
                        </div>
                    </div>
                </div>
                <div class="uk-modal-body">
                    <div class="uk-overflow-auto">
                        <table class="uk-table uk-table-divider uk-table-responsive uk-margin-top">
                            <thead>
                                <tr>
                                    <th style="color: #000;"><?= lang('Global.product') ?></th>
                                    <th style="color: #000;"><?= lang('Global.quantity') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($customer['product'] as $product) { ?>
                                    <tr>
                                        <td><?= $product['name'] ?></td>
                                        <td><?= array_sum($product['qty']) ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<!-- Modal Detail End -->

<?= $this->endSection() ?>