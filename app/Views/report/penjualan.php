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
            ['tanggal', '<?=lang('Global.sales')?>', '<?=lang('Global.capitalgains')?>', '<?=lang('Global.basicprofit')?>', '<?=lang('Global.discount')?> <?=lang('Global.transaction')?>', '<?=lang('Global.discount')?> <?=lang('Global.variant')?>', '<?=lang('Global.discount')?> Global', '<?=lang('Global.discount')?> <?=lang('Global.point')?>'],
            <?php
            foreach ($transactions as $transaction) {
                echo '["'.$transaction['date'].'", '.$transaction['value'].', '.$transaction['profitmodal'].', '.$transaction['profitdasar'].', '.$transaction['trxdisc'].', '.$transaction['vardisc'].', '.$transaction['globdisc'].', '.$transaction['pointdisc'].'],';
            }
            ?>
        ]);

        var options = {
          title: 'Sales Performance',
          hAxis: {title: 'Year',  titleTextStyle: {color: '#333'}},
          lineWidth: 5,
          vAxis: {minValue: 0}
        };

        var chart = new google.visualization.AreaChart(document.getElementById('chart_div'));
        chart.draw(data, options);
      }
    </script>
   
<?= $this->endSection() ?>

<?= $this->section('main') ?>

    <!-- Page Heading -->
    <div class="tm-card-header uk-light">
        <div uk-grid class="uk-flex-middle">
            <div class="uk-width-1-2@m">
                <h3 class="tm-h3"><?=lang('Global.salesreport')?></h3>
            </div>
            <div class="uk-width-1-2@m uk-text-right@m">
                <a class="uk-button uk-button-primary uk-preserve-color uk-margin-right-remove" target="_blank" href="export/sales?daterange=<?=date('Y-m-d', $startdate)?>+-+<?=date('Y-m-d', $enddate)?>" target="_blank"><?=lang('Global.export')?></a>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="uk-width-1-1 uk-margin">
        <form id="short" action="report/penjualan" method="get">
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

    <div class="uk-card uk-card-default uk-card-hover uk-card-body uk-margin-top uk-width-1-1@m">
        <h3 class="uk-card-title"><?=lang('Global.salesreport')?></h3>
        <div id="chart_div"></div>
    </div>
    <!-- End Of Page Heading -->

    <!-- Sales Section -->
    <section class="uk-margin">
        <h1 class="uk-h3 uk-heading-bullet uk-margin-remove" style="color: #fff;"><?=lang('Global.salesreport')?></h1>
        <div class="uk-child-width-1-2@m uk-grid-match uk-margin-top" uk-grid>
            <div>
                <div class="uk-card uk-card-default  uk-card-primary uk-card-hover uk-card-body">
                    <h3 class="uk-card-title uk-margin-remove-bottom"><?=lang('Global.net')?></h3>
                    <p class="uk-margin-remove-top uk-text-bolder"><?=lang('Global.salestotal')?></p>
                    <hr>
                    <div>
                        <div uk-grid>
                            <div class="uk-width-1-1 uk-margin-remove uk-text-large uk-text-bolder uk-text-right" style="font-size:30px;"><?php echo "Rp. ".number_format($result,0,',','.');" ";?></div>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <div class="uk-card uk-card-default uk-card-hover uk-card-body">
                    <h3 class="uk-card-title uk-margin-remove-bottom"><?=lang('Global.gross')?></h3>
                    <p class="uk-margin-remove-top uk-text-bolder"><?=lang('Global.salestotal')?></p>
                    <hr>
                    <div>
                        <div uk-grid>
                            <div class="uk-width-1-1 uk-margin-remove uk-text-large uk-text-bolder uk-text-right" style="font-size:30px;"><?php echo "Rp. ".number_format($gross,0,',','.');" ";?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr class="uk-divider-icon">
    </section>
    <!-- Sales Section End -->

    <!-- Profit Section -->
    <section class="uk-margin">
        <h1 class="uk-h3 uk-heading-bullet uk-margin-remove" style="color: #fff;"><?=lang('Global.profitreport')?></h1>
        <div class="uk-child-width-1-2@m uk-grid-match uk-margin-top" uk-grid>
            <div>
                <div class="uk-card uk-card-default uk-card-default uk-card-hover uk-card-body" style="background-color: #dc3912; color: white;">
                    <h3 class="uk-card-title uk-margin-remove-bottom" style="color: white;"><?=lang('Global.capitalgains')?></h3>
                    <hr>
                    <div>
                        <div class="uk-margin-remove-top" uk-grid>
                            <div class="uk-width-expand@m uk-card-title uk-text-bold uk-text-right" style="color: white;"><?php echo "Rp. ".number_format($modals,2,',','.');" ";?></div>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <div class="uk-card uk-card-default uk-card-body" style="background-color: #ff9900; color: white;">
                    <h3 class="uk-card-title uk-margin-remove-bottom" style="color: white;"><?=lang('Global.basicprofit')?></h3>
                    <hr>
                    <div>
                        <div class="uk-margin-remove-top" uk-grid>
                            <div class="uk-width-expand@m uk-card-title uk-text-bold uk-text-right" style="color: white;"><?php echo "Rp. ".number_format($dasars,2,',','.');" ";?></div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
        <hr class="uk-divider-icon">
    </section>
    <!-- Profit Section End -->

    <!-- Discount Section -->
    <section class="uk-margin">
        <h1 class="uk-h3 uk-heading-bullet uk-margin-remove" style="color: #fff;"><?=lang('Global.discountreport')?></h1>
        <div class="uk-child-width-1-2@m uk-grid-match uk-margin-top" uk-grid>
            <div>
                <div class="uk-card uk-card-default uk-card-hover uk-card-body" style="background-color: #109618; color: white;">
                    <h3 class="uk-card-title uk-margin-remove-bottom" style="color: #fff;"><?=lang('Global.discpoint')?></h3>
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
                <div class="uk-card uk-card-default uk-card-hover uk-card-body" style="background-color: #990099; color: white;">
                    <h3 class="uk-card-title uk-margin-remove-bottom" style="color: #fff;"><?=lang('Global.discount')?> <?=lang('Global.variant')?></h3>
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
                <div class="uk-card uk-card-default uk-card-hover uk-card-body" style="background-color: #0099c6; color: white;">
                    <h3 class="uk-card-title uk-margin-remove-bottom" style="color: #fff;"><?=lang('Global.discount')?> Global</h3>
                    <p class="uk-margin-remove-top uk-text-bolder"><?=lang('Global.discount')?> yang diatur melalui "Informasi Usaha"</p>
                    <hr>
                    <div>
                        <div uk-grid>
                            <div class="uk-width-1-1 uk-margin-remove uk-text-large uk-text-bolder uk-text-right" style="font-size:30px;"><?php echo "Rp. ".number_format($trxglodis,2,',','.');" ";?></div>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <div class="uk-card uk-card-default uk-card-hover uk-card-body" style="background-color: #dd4477; color: white;">
                    <h3 class="uk-card-title uk-margin-remove-bottom" style="color: #fff;"><?=lang('Global.discount')?> <?=lang('Global.transaction')?></h3>
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
    </section>
    <!-- Discount Section End -->

    <?= view('Views/Auth/_message_block') ?>

<?= $this->endSection() ?>