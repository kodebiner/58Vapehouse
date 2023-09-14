<?= $this->extend('layout') ?>

<?= $this->section('extraScript') ?>
<script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = google.visualization.arrayToDataTable([
            ['tanggal', '<?=lang('Global.sales')?>'],
            <?php
            foreach ($transactions as $transaction) {
                echo '["'.$transaction['date'].'", '.$transaction['value'].'],';
            }
            ?>
        ]);

        var options = {
            hAxis: {
                title: 'Time'
            },
        };
        
        var chart = new google.visualization.LineChart(document.getElementById('line-chart'));

        chart.draw(data, options);
    }
</script>
<?= $this->endSection() ?>

<?= $this->section('main') ?>

<!-- Page Heading -->
    <div class="tm-card-header uk-light">
        <div uk-grid class="uk-flex-middle">
            <div class="uk-width-1-2@m">
                <h3 class="tm-h3"><?=lang('Global.sales')?> <?=lang('Global.report')?></h3>
            </div>
        </div>
    </div>

    <div class="uk-card uk-card-default uk-card-hover uk-card-body uk-margin-top uk-width-1-1@m">
      <h3 class="uk-card-title"><?=lang('Global.sales')?></h3>
      <div id="line-chart"></div>
    </div>

    <div class="uk-child-width-1-2@s uk-grid-match uk-margin-top" uk-grid>
        <div>
            <div class="uk-card uk-card-default uk-card-secondary uk-card-hover uk-card-body">
                <h3 class="uk-card-title uk-margin-remove-bottom"><?=lang('Global.net')?></h3>
                <p class="uk-margin-remove-top uk-text-bolder"><?=lang('Global.total')?> <?=lang('Global.sales')?></p>
                <hr>
                <div>
                <div uk-grid>
                    <div class="uk-width-1-1 uk-margin-remove uk-text-large uk-text-bolder uk-text-right" style="font-size:30px;"><?php echo "Rp. ".number_format($result,2,',','.');" ";?></div>
                </div>
                </div>
            </div>
        </div>

        <div>
            <div class="uk-card uk-card-default uk-card-hover uk-card-body">
                <h3 class="uk-card-title uk-margin-remove-bottom"><?=lang('Global.gross')?></h3>
                <p class="uk-margin-remove-top uk-text-bolder"><?=lang('Global.total')?> <?=lang('Global.sales')?></p>
                <hr>
                <div>
                <div uk-grid>
                    <div class="uk-width-1-1 uk-margin-remove uk-text-large uk-text-bolder uk-text-right" style="font-size:30px;"><?php echo "Rp. ".number_format($result,2,',','.');" ";?></div>
                </div>
                </div>
            </div>
        </div>
    </div>
<!-- End Of Page Heading -->

<?= view('Views/Auth/_message_block') ?>

<?= $this->endSection() ?>