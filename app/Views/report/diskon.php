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
        var data = google.visualization.arrayToDataTable([
            ['Task', 'Hours per Day'],
            ['<?=lang('Global.discount')?> <?=lang('Global.variant')?>', <?php echo ' '.$trxvardis.'';?>],
            ['<?=lang('Global.discount')?> Global', <?php echo ' '.$trxglodis.'';?>],
            ['<?=lang('Global.discount')?> <?=lang('Global.transaction')?>', <?php echo ' '.$trxdisc.'';?>],
            ['<?=lang('Global.discount')?> <?=lang('Global.point')?>',  <?php echo ' '.$poindisc.'';?>],
        ]);       

        var options = {
            title: 'Discount Percentage %'
            
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
                <h3 class="tm-h3"><?=lang('Global.discountreport')?></h3>
            </div>
            <div class="uk-width-1-2@m uk-text-right@m">
                <a type="button" class="uk-button uk-button-primary uk-preserve-color uk-margin-right-remove" target="_blank" href="export/diskon?daterange=<?=date('Y-m-d', $startdate)?>+-+<?=date('Y-m-d', $enddate)?>"><?=lang('Global.export')?></a>
            </div>
        </div>
    </div>

     <!-- Filter -->
    <div class="uk-width-1-1 uk-margin">
        <form id="short" action="report/diskon" method="get">
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

    <div class="uk-card uk-width-1-1@m uk-card-default uk-card-hover uk-card-body uk-margin-top">
        <h3 class="uk-card-title"><?=lang('Global.discountreport')?></h3>
        <div id="piechart" class="uk-width-auto"></div>
    </div>

    <div class="uk-child-width-1-4@m uk-grid-match uk-margin-top" uk-grid>
        <div>
            <div class="uk-card uk-card-default uk-card-hover uk-card-body">
                <h3 class="uk-card-title uk-margin-remove-bottom"><?=lang('Global.discpoint')?></h3>
                <p class="uk-margin-remove-top uk-text-bolder"><?=lang('Global.totaldiscpoint')?></p>
                <hr>
                <div>
                <div uk-grid>
                    <div class="uk-width-1-1 uk-margin-remove uk-text-large uk-text-bolder uk-text-right" style="font-size:30px;"><?php echo "Rp. ".number_format($poindisc,2,',','.');" ";?></div>
                </div>
                </div>
            </div>
        </div>

        <div>
            <div class="uk-card uk-card-default uk-card-hover uk-card-body">
                <h3 class="uk-card-title uk-margin-remove-bottom"><?=lang('Global.discount')?> <?=lang('Global.variant')?></h3>
                <p class="uk-margin-remove-top uk-text-bolder"><?=lang('Global.totaldiscvar')?></p>
                <hr>
                <div>
                    <div uk-grid>
                        <div class="uk-width-1-1 uk-margin-remove uk-text-large uk-text-bolder uk-text-right" style="font-size:30px;"><?php echo "Rp. ".number_format($trxvardis,2,',','.');" ";?></div>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <div class="uk-card uk-card-default uk-card-hover uk-card-body">
                <h3 class="uk-card-title uk-margin-remove-bottom"><?=lang('Global.discount')?> Global</h3>
                <p class="uk-margin-remove-top uk-text-bolder"><?=lang('Global.totaldiscvar')?></p>
                <hr>
                <div>
                    <div uk-grid>
                        <div class="uk-width-1-1 uk-margin-remove uk-text-large uk-text-bolder uk-text-right" style="font-size:30px;"><?php echo "Rp. ".number_format($trxglodis,2,',','.');" ";?></div>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <div class="uk-card uk-card-default uk-card-secondary uk-card-hover uk-card-body">
                <h3 class="uk-card-title uk-margin-remove-bottom"><?=lang('Global.discount')?> <?=lang('Global.transaction')?></h3>
                <p class="uk-margin-remove-top uk-text-bolder"><?=lang('Global.totaldisctrx')?></p>
                <hr>
                <div>
                    <div uk-grid>
                        <div class="uk-width-1-1 uk-margin-remove uk-text-large uk-text-bolder uk-text-right" style="font-size:30px;"><?php echo "Rp. ".number_format($trxdisc,2,',','.');" ";?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- End Of Page Heading -->

<?= view('Views/Auth/_message_block') ?>

<?= $this->endSection() ?>