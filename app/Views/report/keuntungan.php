<?= $this->extend('layout') ?>

<?= $this->section('extraScript') ?>
<script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
<script type="text/javascript" src="js/moment.min.js"></script>
<script type="text/javascript" src="js/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="css/daterangepicker.css" />
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['date', 'modal', 'dasar'],
          <?php foreach ($transactions as $transaction) {
                echo '["'.$transaction['date'].'", '.$transaction['modal'].', '.$transaction['dasar'].'],';
            } ?>
        ]);

        var options = {
          title: 'Company Performance',
          hAxis: {title: 'Year',  titleTextStyle: {color: '#333'}},
          vAxis: {minValue: 0}
        };

        var chart = new google.visualization.AreaChart(document.getElementById('chart_div'));
        chart.draw(data, options);
      }
    </script>

<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

<?= $this->endSection() ?>

<?= $this->section('main') ?>

<!-- Page Heading -->
<div class="tm-card-header uk-light">
    <div uk-grid class="uk-flex-middle">
        <div class="uk-width-1-2@m">
            <h3 class="tm-h3"><?=lang('Global.profitreport')?></h3>
        </div>

        <!-- Button Trigger Modal export -->
        <div class="uk-width-1-2@m uk-text-right@m">
            <a type="button" class="uk-button uk-button-primary uk-preserve-color uk-margin-right-remove" target="_blank" href="export/profit?daterange=<?=date('Y-m-d', $startdate)?>+-+<?=date('Y-m-d', $enddate)?>"><?=lang('Global.export')?></a>
        </div>
        <!-- End Of Button Trigger Modal export-->

    </div>
</div>
<!-- End Of Page Heading -->


    <!-- Filter -->
    <div class="uk-width-1-1 uk-margin">
        <form id="short" action="report/keuntungan" method="get">
            <div class="uk-inline">
                <span class="uk-form-icon uk-form-icon-flip" uk-icon="calendar"></span>
                <input class="uk-input uk-width-medium uk-border-rounded uk-box-shadow-small uk-box-shadow-hover-large" type="text" id="daterange" name="daterange" value="<?=date('m/d/Y', $startdate)?> - <?=date('m/d/Y', $enddate)?>" />
            </div>
        </form>
        <script>
            $(function() {
                $('input[name="daterange"]').daterangepicker({
                    opens: 'right'
                }, function(start, end, label) {
                    document.getElementById('daterange').value = start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD');
                    document.getElementById('short').submit();
                });
            });
        </script>
    </div>

    <div class="uk-card uk-card-default uk-card-body uk-margin-top uk-width-1-1@m">
        <h3 class="uk-card-title"><?=lang('Global.profitreport')?></h3>
        <div id="chart_div"></div>
    </div>

    <div class="uk-child-width-1-2@s uk-grid-match uk-margin-top" uk-grid>

        <div>
            <div class="uk-card uk-card-default uk-card-secondary uk-card-hover uk-card-body">
                <h3 class="uk-card-title uk-margin-remove-bottom"><?=lang('Global.capitalgains')?></h3>
                <hr>
                <div>
                    <div class="uk-margin-remove-top" uk-grid>
                        <div class="uk-width-expand@m uk-card-title uk-text-bold uk-text-right"><?php echo "Rp. ".number_format($modals,2,',','.');" ";?></div>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <div class="uk-card uk-card-default uk-card-body">
                <h3 class="uk-card-title uk-margin-remove-bottom"><?=lang('Global.basicprofit')?></h3>
                <hr>
                <div>
                    <div class="uk-margin-remove-top" uk-grid>
                        <div class="uk-width-expand@m uk-card-title uk-text-bold uk-text-right"><?php echo "Rp. ".number_format($dasars,2,',','.');" ";?></div>
                    </div>
                    
                </div>
            </div>
        </div>

    </div>
 
<!-- End Of Page Heading -->

<?= view('Views/Auth/_message_block') ?>

<?= $this->endSection() ?>