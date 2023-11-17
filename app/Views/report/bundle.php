<?= $this->extend('layout') ?>

<?= $this->section('extraScript') ?>
<script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
<script type="text/javascript" src="js/moment.min.js"></script>
<script type="text/javascript" src="js/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="css/daterangepicker.css" />
<script src="js/cdn.datatables.net_1.13.4_js_jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'name');
        data.addColumn('number', 'qty');
        data.addColumn('number', 'value');
        data.addRows([
            <?php foreach ($bundles as $bundle){
                $name   = $bundle['name'];
                $value  = $bundle['value'];
                $qty    = $bundle['qty'];
                echo "['$name', $value,$qty],";
            }?>
        ]);

        var options = {
            title: '<?=lang('Global.bundle')?> Percentage %'
            
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart'));

        chart.draw(data, options);
    }
</script>
<?= $this->endSection() ?>

<?= $this->section('main') ?>

    <!-- Page Heading -->
    <div class="tm-card-header uk-light">
        <div uk-grid class="uk-flex-middle">
            <div class="uk-width-1-2@m">
                <h3 class="tm-h3"><?=lang('Global.bundlereport')?></h3>
            </div>
            <div class="uk-width-1-2@m uk-text-right@m">
                <a type="button" class="uk-button uk-button-primary uk-preserve-color uk-margin-right-remove" target="_blank" href="export/bundle?daterange=<?=date('Y-m-d', $startdate)?>+-+<?=date('Y-m-d', $enddate)?>"><?=lang('Global.export')?></a>
            </div>
        </div>
    </div>

     <!-- Filter -->
    <div class="uk-width-1-1 uk-margin">
        <form id="short" action="report/bundle" method="get">
            <div class="uk-inline">
                <span class="uk-form-icon uk-form-icon-flip" uk-icon="calendar"></span>
                <input class="uk-input uk-width-medium uk-border-rounded" type="text" id="daterange" name="daterange" value="<?=date('m/d/Y', $startdate)?> - <?=date('m/d/Y', $enddate)?>" />
            </div>
        </form>
        <script>
            $(function() {
                $('input[name="daterange"]').daterangepicker({
                    opens: 'right',
                    maxDate: new Date(),
                }, function(start, end, label) {
                    document.getElementById('daterange').value = start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD');
                    document.getElementById('short').submit();
                });
            });
        </script>
    </div>

    <div class="uk-card uk-width-1-1@m uk-card-default uk-card-hover uk-card-body uk-margin-top">
        <h3 class="uk-card-title"><?=lang('Global.bundlereport')?></h3>
        <div id="piechart" class="uk-width-auto"></div>
    </div>

    <div class="uk-child-width-1-2@m uk-grid-match uk-margin-top" uk-grid>
        <?php foreach ($bundles as $bundle) { ?>
        <div>
            <div class="uk-card uk-card-default uk-card-hover uk-card-body">
                <h3 class="uk-card-title uk-margin-remove-bottom uk-text-bolder"><?=lang('Global.bundle')?> <?=$bundle['name']?></h3>
                <hr class="uk-divider-icon">
                <div>
                    <div uk-grid >
                        <div class="uk-width-1-2@m">
                            <p class="uk-margin-remove-top uk-text-default"><?=lang("Global.quantity")?> <?=lang("Global.sales")?></p>
                            <p class="uk-margin-remove-top uk-text-default"><?=lang("Global.total")?> <?=lang("Global.sales")?></p>
                        </div>
                        <div class="uk-width-1-2@m">
                            <p class="uk-margin-remove-top  uk-text-default uk-text-right"><?=$bundle['qty']?></p>
                            <p class="uk-margin-remove-top  uk-text-default uk-text-right"><?=$bundle['value']?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
<!-- End Of Page Heading -->

<?= view('Views/Auth/_message_block') ?>

<?= $this->endSection() ?>