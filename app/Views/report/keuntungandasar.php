<?= $this->extend('layout') ?>

<?= $this->section('extraScript') ?>
<script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = google.visualization.arrayToDataTable([
            ['tanggal', 'keuntungandasar'],
            <?php
            foreach ($transactions as $transaction) {
                echo '["'.$transaction['date'].'", '.$transaction['dasar'].'],';
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
            <h3 class="tm-h3"><?=lang('Global.report')?></h3>
        </div>
    </div>
</div>
<div id="line-chart" class="uk-width-1-1"></div>
<!-- End Of Page Heading -->

<?= view('Views/Auth/_message_block') ?>

<?= $this->endSection() ?>