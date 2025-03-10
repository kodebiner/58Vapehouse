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
            <h3 class="tm-h3"><?=lang('Global.debtInstallmentsList')?></h3>
        </div>

        <!-- Date Range -->
        <div class="uk-width-1-2@m uk-text-right@m">
            <form id="short" action="debt/debtpay" method="get">
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
        <!-- End Of Date Range-->
    </div>
</div>
<!-- End Of Page Heading -->

<!-- Table Of Content -->
<div class="uk-overflow-auto uk-margin">
    <table class="uk-table uk-table-justify uk-table-middle uk-table-divider uk-light">
        <thead>
            <tr>
                <th class=""><?=lang('Global.date')?></th>
                <th class=""><?=lang('Global.outlet')?></th>
                <th class=""><?=lang('Global.description')?></th>
                <th class="uk-text-center"><?=lang('Global.quantity')?></th>
                <th class="uk-text-center"></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($trxothers as $trx) { ?>
                <tr>
                    <td class=""><?= date('l, d M Y, H:i:s', strtotime($trx['date'])); ?></td>
                    <td><?= $trx['outlet'] ?></td>
                    <td class=""><?= $trx['description'];?></td>
                    <td class="uk-text-center">Rp <?= number_format($trx['qty'],2,',','.');?></td>
                    <td class="uk-text-center">
                        <?php if ((in_groups('owner')) && ($outletPick != null)) { ?>
                            <div class="uk-text-success" id="refund" onclick="return confirm('<?= lang('Global.deleteConfirm') ?>')" style="border-style: solid; border-color: red;">
                                <a href="debt/refund/<?= $trx['id'] ?>" class="uk-link-heading">Refund</a>
                            </div>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <div>
        <?= $pager_links ?>
        <!-- </?= $pager->links('debtpay', 'front_full') ?> -->
    </div>
</div>
<!-- Table Of Content End -->
<?= $this->endSection() ?>