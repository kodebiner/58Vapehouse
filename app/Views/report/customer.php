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
            <h3 class="tm-h3 uk-width-1-1@m"><?=lang('Global.customer')?> <?=lang('Global.report')?></h3>
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

<div class="uk-overflow-auto">
    <table class="uk-table uk-table-divider uk-table-responsive uk-margin-top" id="example">
        <thead>
            <tr>
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
                    <td style="color:white;"><?=$customer['name']?></td>
                    <td class="uk-text-center" style="color:white;"><?=$customer['trx']?></td>
                    <td class="uk-text-center" style="color:white;"><?= "Rp. ".number_format($customer['value'],2,',','.');" ";?></td>
                    <td class="uk-text-center" style="color:white;"><?= "Rp. ".number_format($customer['debt'],2,',','.');" ";?></td>
                    <td class="uk-text-center" style="color:white;"><?="+62".$customer['phone']?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?= view('Views/Auth/_message_block') ?>

<?= $this->endSection() ?>