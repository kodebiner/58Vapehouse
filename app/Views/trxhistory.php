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
        <div class="uk-width-1-3@m uk-width-1-1">
            <h3 class="tm-h3"><?= lang('Global.trxHistory') ?></h3>
        </div>
        <div class="uk-width-1-3@m uk-width-1-2 uk-margin-right-remove">
            <form id="short" action="trxhistory" method="get">
                <div class="uk-inline">
                    <span class="uk-form-icon uk-form-icon-flip" uk-icon="calendar"></span>
                    <input class="uk-input uk-width-medium uk-border-rounded" type="text" id="daterange" name="daterange" value="<?= date('m/d/Y', $startdate) ?> - <?= date('m/d/Y', $enddate) ?>" />
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
        <div class="uk-width-1-3@m uk-width-1-2 uk-text-right">
            <a type="button" class="uk-button uk-button-primary uk-preserve-color uk-margin-right-remove" target="_blank" href="export/transaction?daterange=<?= date('Y-m-d', $startdate) ?>+-+<?= date('Y-m-d', $enddate) ?>"><?= lang('Global.export') ?></a>
        </div>
    </div>
</div>

<?= view('Views/Auth/_message_block') ?>

<!-- Table Of Content -->
<div class="uk-overflow-auto">
    <table class="uk-table uk-table-justify uk-table-middle uk-table-divider uk-light" style="width:100%">
        <thead>
            <tr>
                <th class="uk-text-center"><?= lang('Global.detail') ?></th>
                <th class=""><?= lang('Global.date') ?></th>
                <th class=""><?= lang('Global.outlet') ?></th>
                <th class=""><?= lang('Global.employee') ?></th>
                <th class=""><?= lang('Global.paymethod') ?></th>
                <th class=""><?= lang('Global.total') ?></th>
                <th class="">Keterangan</th>
                <th class="uk-text-center"></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transactions as $transaction) { ?>
                <tr>
                    <td class="uk-flex-middle uk-text-center">
                        <a class="uk-icon-link uk-icon" onclick="showDetail(<?= $transaction['id'] ?>)" uk-icon="search"></a>
                    </td>
                    <td><?= $transaction['date_formatted'] ?></td>
                    <td><?= $transaction['outlet'] ?></td>
                    <td><?= $transaction['cashier'] ?></td>
                    <td><?= $transaction['payment'] ?></td>
                    <td><?= "Rp " . number_format($transaction['value'], 2, ',', '.'); ?></td>
                    <td>
                        <?php
                        if (!empty($transaction['outsidehours'])) {
                            echo '<div class="uk-text-danger" style="border-style: solid; border-color: #f0506e; padding: 2px 4px; margin-bottom: 4px;">Transaksi di Luar Pembukuan Kas</div>';
                        }
                        if (!empty($transaction['pointused'])) {
                            echo '<div>Tukar Poin</div>';
                        }
                        if (!empty($transaction['memberdisc'])) {
                            echo '<div>Diskon Member</div>';
                        }
                        if (!empty($transaction['trxdiscount'])) {
                            echo '<div>Diskon Transaksi</div>';
                        }
                        if ($transaction['has_discvar']) {
                            echo '<div>Diskon Variant</div>';
                        }
                        if ($transaction['has_globaldisc']) {
                            echo '<div>Diskon Global</div>';
                        }
                        if ($transaction['has_memberdisc']) {
                            echo '<div>Diskon Member Per Item</div>';
                        }
                        ?>
                    </td>
                    <td class="uk-text-center uk-column-1-2">
                        <?= $transaction['paidstatus'] ?>
                        <?php if ($can_refund) { ?>
                            <div class="uk-text-success" id="refund" onclick="return confirm('<?= lang('Global.deleteConfirm') ?>')" style="border-style: solid; border-color: red;"><a href="trxhistory/refund/<?= $transaction['id'] ?>" class="uk-link-heading">Refund</a></div>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<!-- Table Of Content End -->

<!-- Modal Detail (single dynamic modal, populated by JavaScript) -->
<div id="detail-modal" uk-modal class="uk-flex-top">
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <div class="uk-modal-content">
            <div class="uk-modal-header uk-margin">
                <div uk-grid>
                    <div class="uk-width-1-2@m">
                        <h5 class="uk-modal-title"><?= lang('Global.detailTrx') ?></h5>
                    </div>
                    <div class="uk-width-1-4@m">
                        <a class="uk-button uk-button-primary uk-preserve-color" id="modal-print-link" href="" target="_blank"><?= lang('Global.print') ?></a>
                    </div>
                    <div class="uk-width-1-4@m uk-text-right">
                        <button class="uk-modal-close uk-icon-button-delete" uk-icon="icon: close;" type="button"></button>
                    </div>
                </div>
            </div>
            <div class="uk-modal-body" id="modal-body">
            </div>
        </div>
    </div>
</div>

<script>
var trxData = <?= json_encode($transactions) ?>;

function showDetail(trxId) {
    var t = trxData[trxId];
    if (!t) return;

    var details = [];
    if (t.detail) {
        for (var key in t.detail) {
            if (t.detail.hasOwnProperty(key)) {
                details.push(t.detail[key]);
            }
        }
    }

    var html = '';
    html += '<div class="uk-margin"><div class="uk-padding-small">';
    html += '<div class="uk-flex uk-flex-center"><img src="/img/' + t.bizlogo + '" alt="' + t.bizname + '" style="height: 90px;"></div>';
    html += '<div class="uk-flex uk-flex-center"><div class="fpoutlet uk-h3 uk-margin-remove uk-text-justify">' + t.outlet + '</div></div>';
    html += '<div class="uk-flex uk-flex-center"><div class="fpaddress uk-h4 uk-margin-remove">' + (t.bizaddress || '') + '</div></div>';
    html += '<div class="uk-flex uk-flex-center"><div class="fpaddress uk-h4 uk-margin-remove"><span uk-icon="instagram"></span> : ' + (t.bizinstagram || '') + '</div></div>';
    html += '<div class="uk-flex uk-flex-center"><div class="fpaddress uk-h4 uk-margin-remove"><span uk-icon="whatsapp"></span> : ' + (t.bizphone || '') + '</div></div>';
    html += '<div uk-grid><div class="uk-width-1-2">Invoice: ' + t.invoice + '</div>';
    html += '<div class="uk-width-1-2 uk-text-right">' + t.date_formatted + '</div></div>';
    html += '<div class="uk-margin-remove-top uk-child-width-1-2" uk-grid><div>Cashier: ' + t.cashier + '</div><div class="uk-text-right">' + t.payment + '</div></div>';

    if (t.outsidehours) {
        html += '<div class="uk-text-danger uk-text-center uk-margin-small-top" style="border: 2px solid #f0506e; padding: 4px;">Transaksi di Luar Pembukuan Kas</div>';
    }

    html += '<hr style="border-top: 3px double #8c8b8b">';

    for (var i = 0; i < details.length; i++) {
        var d = details[i];
        html += '<div class="uk-margin-small">';
        html += '<div class="uk-h5 uk-text-bolder uk-margin-remove">' + d.name + '</div>';
        html += '<div uk-grid><div class="uk-width-1-2"><div>x' + d.qty + ' @' + d.value + '</div></div>';
        html += '<div class="uk-width-1-2 uk-text-right"><div>' + d.total + '</div></div></div>';

        if (d.discvar != 0) {
            html += '<div class="uk-child-width-1-2 uk-margin-remove-top" uk-grid>';
            html += '<div><div>(' + d.discitem + ')</div></div>';
            html += '<div class="uk-text-right"><div>- ' + d.discvar + '</div></div></div>';
        }
        if (d.globaldisc != 0) {
            html += '<div class="uk-child-width-1-2 uk-margin-remove-top" uk-grid>';
            html += '<div><div>(' + (d.globaldisc / d.qty) + ')</div></div>';
            html += '<div class="uk-text-right"><div>- ' + d.globaldisc + '</div></div></div>';
        }
        if (d.memberdisc != 0) {
            html += '<div class="uk-child-width-1-2 uk-margin-remove-top" uk-grid>';
            html += '<div><div>(' + (d.memberdisc / d.qty) + ')</div></div>';
            html += '<div class="uk-text-right"><div>- ' + d.memberdisc + '</div></div></div>';
        }
        html += '</div>';
    }

    html += '<hr style="border-top: 3px double #8c8b8b">';
    html += '<div class="uk-margin-small">';
    html += '<div uk-grid><div class="uk-width-1-2"><div><?= lang('Global.subtotal') ?></div></div>';
    html += '<div class="uk-width-1-2 uk-text-right uk-text-bold" style="color: #000;"><div>' + t.totaldetailvalue + '</div></div></div>';

    if (t.trxdiscount) {
        html += '<div class="uk-margin-remove-top" uk-grid><div class="uk-width-1-2"><div><?= lang('Global.discount') ?></div></div>';
        html += '<div class="uk-width-1-2 uk-text-right"><div>- ' + t.trxdiscount + '</div></div></div>';
    }
    if (t.memberdisc != 0) {
        html += '<div class="uk-margin-remove-top" uk-grid><div class="uk-width-1-2"><div><?= lang('Global.memberDiscount') ?></div></div>';
        html += '<div class="uk-width-1-2 uk-text-right"><div>- ' + t.memberdisc + '</div></div></div>';
    }
    if (t.pointused != 0) {
        html += '<div class="uk-margin-remove-top" uk-grid><div class="uk-width-1-2"><div><?= lang('Global.redeemPoint') ?></div></div>';
        html += '<div class="uk-width-1-2 uk-text-right"><div>- ' + t.pointused + '</div></div></div>';
    }

    html += '<hr style="border-top: 3px double #8c8b8b">';
    html += '<div class="uk-margin-remove-top" uk-grid><div class="uk-width-1-2"><div><?= lang('Global.total') ?></div></div>';
    html += '<div class="uk-width-1-2 uk-text-right uk-text-bolder" style="color: red;"><div>';
    if (t.value - t.pointused != 0) {
        html += t.value;
    } else {
        html += t.amountpaid;
    }
    html += '</div></div></div>';

    html += '<hr style="border-top: 3px double #8c8b8b">';
    html += '<div class="uk-margin-remove-top" uk-grid><div class="uk-width-1-2"><div><?= lang('Global.accepted') ?></div></div>';
    html += '<div class="uk-width-1-2 uk-text-right uk-text-bolder" style="color: #000;"><div>' + t.amountpaid + '</div></div></div>';

    if (t.amountpaid - t.value >= 0) {
        html += '<div class="uk-margin-remove-top" uk-grid><div class="uk-width-1-2"><div><?= lang('Global.change') ?></div></div>';
        html += '<div class="uk-width-1-2 uk-text-right uk-text-bolder" style="color: #000;"><div>' + (t.amountpaid - t.value) + '</div></div></div>';
    }

    html += '<hr style="border-top: 3px double #8c8b8b">';

    if (t.memberid != 0) {
        html += '<div class="uk-margin-remove-top" uk-grid><div class="uk-width-1-2"><div><?= lang('Global.customer') ?></div></div>';
        html += '<div class="uk-width-1-2 uk-text-right uk-text-bolder" style="color: #000;"><div>' + t.membername + ' / 0' + t.memberphone + '</div></div></div>';
        html += '<div class="uk-margin-remove-top" uk-grid><div class="uk-width-1-2"><div><?= lang('Global.pointearn') ?></div></div>';
        html += '<div class="uk-width-1-2 uk-text-right uk-text-bolder" style="color: #000;"><div>' + t.pointearn + '</div></div></div>';
        html += '<div class="uk-margin-remove-top" uk-grid><div class="uk-width-1-2"><div><?= lang('Global.totalpoint') ?></div></div>';
        html += '<div class="uk-width-1-2 uk-text-right uk-text-bolder" style="color: #000;"><div>' + t.memberpoin + '</div></div></div>';
        html += '<hr style="border-top: 3px double #8c8b8b">';
    }

    html += '<div class="uk-flex uk-flex-center"><div class="fptagline uk-h3 uk-margin-remove">#VapingSambilNongkrong</div></div>';
    html += '</div></div>';

    document.getElementById('modal-body').innerHTML = html;
    document.getElementById('modal-print-link').href = 'pay/copyprint/' + trxId;
    UIkit.modal('#detail-modal').show();
}
</script>
<!-- Modal Detail End -->
<?= $this->endSection() ?>