<?= $this->extend('layout') ?>

<?= $this->section('extraScript') ?>
    <link rel="stylesheet" href="css/code.jquery.com_ui_1.13.2_themes_base_jquery-ui.css">
    <script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
    <script src="js/code.jquery.com_jquery-3.6.0.js"></script>
    <script src="js/code.jquery.com_ui_1.13.2_jquery-ui.js"></script>
    <script type="text/javascript" src="js/moment.min.js"></script>
    <script type="text/javascript" src="js/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/daterangepicker.css" />
<?= $this->endSection() ?>

<?= $this->section('main') ?>

<!-- Page Heading -->
<div class="tm-card-header uk-light">
    <div uk-grid class="uk-flex-middle">
        <div class="uk-width-1-3@m uk-width-1-1">
            <h3 class="tm-h3">Daftar Stok Opname</h3>
        </div>

        <!-- Button Daterange -->
        <div class="uk-width-1-3@m uk-width-1-2 uk-margin-right-remove">
            <form id="short" action="stockopname" method="get">
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
        <!-- End Of Button Daterange-->

        <!-- Button Trigger Modal export -->
        <div class="uk-width-1-3@m uk-width-1-2 uk-text-right">
            <a id="btnExport" type="button" class="uk-button uk-button-primary uk-preserve-color uk-margin-right-remove" target="_blank" href="stockopname/stockopnameprint"><?=lang('Global.export')?></a>
        </div>

        <script>
            document.getElementById('btnExport').addEventListener('click', function() {
                setTimeout(() => location.reload(), 1000);
            });
        </script>
        <!-- End Of Button Trigger Modal export-->
    </div>
</div>
<!-- Page Heading End -->

<?= view('Views/Auth/_message_block') ?>

<!-- Table Of Content -->
<!-- <div class="uk-overflow-auto uk-margin"> -->
    <table class="uk-table uk-table-justify uk-table-middle uk-table-divider uk-light">
        <thead>
            <tr>
                <th class="uk-width-small"><?=lang('Global.date')?></th>
                <th class="uk-width-small"><?=lang('Global.employee')?></th>
                <th class="uk-width-small"><?=lang('Global.outlet')?></th>
            </tr>
        </thead>
        <tbody>
            <tr id="new-list"></tr>
            <?php foreach ($stockopnames as $stockopname) { ?>
                <tr>
                    <td class="uk-width-small"><?= date('l, d M Y, H:i:s', strtotime($stockopname['date'])); ?></td>
                    <td class="uk-width-small"><?= $stockopname['employee'] ?></td>
                    <td class="uk-width-small"><?= $stockopname['outlet'] ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <div>
        <?= $pager->links('stockopname', 'front_full') ?>
    </div>
<!-- </div> -->
<!-- Table Content End -->
<?= $this->endSection() ?>