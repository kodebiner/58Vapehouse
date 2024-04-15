<?= $this->extend('layout') ?>

<?= $this->section('extraScript') ?>
    <script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
    <script type="text/javascript" src="js/moment.min.js"></script>
    <script type="text/javascript" src="js/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/daterangepicker.css" />
<?= $this->endSection() ?>

<?= $this->section('main') ?>

<?= view('Views/Auth/_message_block') ?>

<!-- Page Heading -->
<div class="tm-card-header uk-light">
    <h3 class="tm-h3">Laporan SOP</h3>
</div>
<!-- End Of Page Heading -->

<!-- Filter -->
<div class="uk-width-1-1 uk-margin">
    <form id="short" action="report/sop" method="get">
        <div class="uk-inline">
            <span class="uk-form-icon uk-form-icon-flip" uk-icon="calendar"></span>
            <input class="uk-input uk-width-medium uk-border-rounded uk-box-shadow-small uk-box-shadow-hover-large" type="text" id="daterange" name="daterange" value="<?=date('m/d/Y', $startdate)?> - <?=date('m/d/Y', $enddate)?>" />
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

<div class="uk-overflow-auto">
    <table class="uk-table uk-table-divider uk-table-responsive uk-margin-top uk-light">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>SOP</th>
                <th>Pegawai</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sopdetails as $sopdet) { ?>
                <tr>
                    <td><?= date('l, d M Y, H:i:s', strtotime($sopdet['updated_at'])); ?></td>
                    <?php foreach ($sops as $sop) {
                        if ($sop['id'] === $sopdet['sopid']) { ?>
                            <td><?= $sop['name'] ?></td>
                        <?php }
                    } ?>
                    <td>
                        <?php foreach ($users as $user) {
                            if ($user->id === $sopdet['userid']) {
                                echo $user->username;
                            }
                        } ?>
                    </td>
                    <td>
                        <?php if ($sopdet['status'] === "0") { ?>
                            <input class="uk-checkbox" type="checkbox" disabled>
                        <?php } else { ?>
                            <div uk-icon="check"></div>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <div>
        <?= $pager->links('sop', 'front_full') ?>
    </div>
</div>

<?= $this->endSection() ?>