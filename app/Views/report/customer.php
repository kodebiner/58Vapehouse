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
    <div uk-grid class="uk-child-width-1-2@m uk-child-width-auto uk-flex-middle">
        <div>
            <h3 class="tm-h3"><?= lang('Global.report').' '.lang('Global.customer') ?></h3>
        </div>
        
        <!-- Button Trigger Modal export -->
        <div class="uk-text-right@m">
            <a
                type="button"
                class="uk-button uk-button-primary uk-preserve-color uk-margin-right-remove"
                target="_blank"
                href="<?= base_url('export/customer') . '?' . http_build_query([
                    'daterange' => $daterange,
                    'search'    => $search
                ]) ?>">
                
                <?=lang('Global.export')?>
            </a>
        </div>
    </div>
</div>

<div class="uk-margin">
    <form id="filterForm" action="report/customer" method="GET">
        <!-- Filter -->
        <div uk-grid class="uk-child-width-1-3@m uk-child-width-auto uk-flex-between@m uk-flex-middle">
            <div class="uk-inline">
                <span class="uk-form-icon uk-form-icon-flip" uk-icon="calendar"></span>
                <input
                    type="hidden"
                    name="daterange"
                    id="daterange-hidden"
                    value="<?= esc($daterange) ?>"
                >
                <input
                    type="text"
                    id="daterange-display"
                    class="uk-input"
                >
            </div>

            <div class="uk-text-right@l">
                <!-- Search Filter -->
                <div class="uk-search uk-search-default"
                    style="background-color:#fff;border-radius:7px;">
                    <span uk-search-icon style="color:#000;"></span>
                    <input
                        class="uk-search-input"
                        type="search"
                        placeholder="Search"
                        name="search"
                        value="<?= esc($search ?? '') ?>"
                        style="border-radius:7px;"
                    >
                </div>
            </div>

            <div class="uk-hidden@l uk-text-right">
                <button type="submit" class="uk-button uk-button-primary" style="border-radius: 10px;">Cari</button>
            </div>
        </div>
    </form>
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
                                        <td><?= $product['qty'] ?></td>
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

<script>
$(function () {
    let range = $('#daterange-hidden').val();
    let start = moment().startOf('day');
    let end   = moment().endOf('day');

    if (range) {
        const [startStr, endStr] = range.split(' - ');

        start = moment(startStr, 'YYYY-MM-DD');
        end   = moment(endStr, 'YYYY-MM-DD');
    }

    $('#daterange-display').daterangepicker({
        startDate: start,
        endDate: end,
        maxDate: new Date(),
        autoUpdateInput: true,
        locale: {
            format: 'MM/DD/YYYY'
        }
    });

    $('#daterange-display').on('apply.daterangepicker', function(ev, picker) {

        $('#daterange-hidden').val(
            picker.startDate.format('YYYY-MM-DD')
            + ' - ' +
            picker.endDate.format('YYYY-MM-DD')
        );

        $('#filterForm').submit();
    });
});

let timer;

$('input[name="search"]').on('keyup', function() {
    clearTimeout(timer);

    timer = setTimeout(function() {
        $('#filterForm').submit();
    }, 500);
});
</script>

<?= $this->endSection() ?>