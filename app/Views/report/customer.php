<?= $this->extend('layout') ?>

<?= $this->section('extraScript') ?>

<script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script src="js/cdn.datatables.net_1.13.4_js_jquery.dataTables.min.js"></script>
<?= $this->endSection() ?>

<?= $this->section('main') ?>

<!-- Page Heading -->
<div class="tm-card-header uk-light uk-margin-bottom">
    <div uk-grid class="uk-flex-middle">
        <div class="uk-width-1-2@m">
            <h3 class="tm-h3"><?=lang('Global.customer')?> <?=lang('Global.report')?></h3>
        </div>
        <div class="uk-width-1-2@m">
            <!-- Filter -->
            <div class="uk-width-1-1 uk-margin uk-text-right">
                <form id="short" action="report/customer" method="get">
                    <div class="uk-inline">
                        <span class="uk-form-icon uk-form-icon-flip" uk-icon="calendar"></span>
                        <input class="uk-input uk-width-medium" type="text" id="daterange" name="daterange" value="<?=date('m/d/Y', $startdate)?> - <?=date('m/d/Y', $enddate)?>" />
                    </div>
                </form>
                <script>
                    $(function() {
                        $('input[name="daterange"]').daterangepicker({
                            opens: 'left'
                        }, function(start, end, label) {
                            document.getElementById('daterange').value = start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD');
                            document.getElementById('short').submit();
                        });
                    });
                </script>
            </div>
        </div>
    </div>
</div>

<table class="uk-table uk-table-divider uk-table-responsive uk-margin-top" id="example">
    <thead>
        <tr>
            <th class="uk-text-large uk-text-bold"><?=lang('Global.name')?></th>
            <th class="uk-text-center uk-text-large uk-text-bold"><?=lang('Global.transaction')?></th>
            <th class="uk-text-center uk-text-large uk-text-bold"><?=lang('Global.value')?></th>
            <th class="uk-text-center uk-text-large uk-text-bold"><?=lang('Global.debt')?></th>
            <th class="uk-text-center uk-text-large uk-text-bold"><?=lang('Global.phone')?></th>
            <th class="uk-text-center uk-text-large uk-text-bold"><?=lang('Global.detail')?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($customers as $customer){ ?>
            <tr>
                <td style="color:white;"><?=$customer['name']?></td>
                <td class="uk-text-center" style="color:white;"><?=$customer['trx']?></td>
                <td class="uk-text-center" style="color:white;"><?= "Rp. ".number_format($customer['value'],2,',','.');" ";?></td>
                <td class="uk-text-center" style="color:white;"><?= "Rp. ".number_format($customer['debt'],2,',','.');" ";?></td>
                <td class="uk-text-center" style="color:white;"><?="+62".$customer['phone']?></td>
                <td class="uk-text-center"><a class="uk-icon-link uk-margin-small-right" uk-icon="icon: eye;" href="report/customerdetail/<?=$customer['id']?>"></a></td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<!-- End Of Page Heading -->
<script>
    $(document).ready(function () {
        $('#example').DataTable();
    });
</script>
<?= view('Views/Auth/_message_block') ?>

<?= $this->endSection() ?>