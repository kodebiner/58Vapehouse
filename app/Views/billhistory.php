<?= $this->extend('layout') ?>

<?= $this->section('extraScript') ?>
    <script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
    <script type="text/javascript" src="js/moment.min.js"></script>
    <script type="text/javascript" src="js/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/daterangepicker.css" />
<?= $this->endSection() ?>

<?= $this->section('main') ?>

<!-- Page Heading -->
<div class="tm-card-header uk-light">
    <div uk-grid class="uk-flex-middle">
        <div class="uk-width-1-2@m">
            <h3 class="tm-h3">Riwayat Tagihan</h3>
            <h3 class="tm-h3"><?= $name?></h3>
            <h3 class="tm-h3"><?=date('d/m/Y', $startdate).' - '.date('d/m/Y', $enddate) ?></h3>
        </div>
    </div>
</div>
<!-- End Of Page Heading -->

<?= view('Views/Auth/_message_block') ?>

<!-- Table Of Content -->
<div class="uk-overflow-auto uk-margin">
    <div class="uk-light" uk-grid>
        <!-- Counter Total -->
        <div class="uk-width-1-2@m uk-width-1-1 uk-form-horizontal">
            <div class="uk-form-label uk-margin-top" style="width: 100px;">Total Tagihan :</div>
            <div class="uk-form-controls uk-margin-top uk-margin-remove-left">Rp <?= number_format((Int)$totalbill,0,',','.');?></div>
        </div>
        <!-- Counter Total End -->

        <!-- Date Range -->
        <div class="uk-width-1-2@m uk-width-1-1 uk-text-right">
            <form id="short" action="billhistory" method="get">
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
        <!-- Date Range End -->
    </div>

    <table class="uk-table uk-table-justify uk-table-middle uk-table-divider uk-light">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Status</th>
                <th></th>
                <th></th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bills as $bill) { ?>
                <tr>
                    <td><?= date('l, d M Y, H:i:s', strtotime($bill['date'])) ?></td>
                    <td><?= $bill['type'] ?></td>
                    <td><?= $bill['outlet'] ?></td>
                    <td><?= $bill['payment'] ?></td>
                    <!-- <td>Rp </?= number_format((Int)$bill['payvalue'],0,',','.');?></td> -->
                    <td><?= $bill['payvalue'] ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <div>
        <?= $pager_links ?>
    </div>
</div>
<!-- End Table Content -->
<?= $this->endSection() ?>