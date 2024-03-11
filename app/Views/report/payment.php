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
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'name');
        data.addColumn('number', 'quantity');
        data.addColumn('number', 'value');
        data.addRows([
            <?php foreach ($payments as $pay){
                $value = $pay['value'];
                $name = $pay['name'];
                $qty = $pay['qty'];
                echo "['$name', $value,$qty],";
            }?>
        ]);

        var options = {
            title: 'Payment Percentage %'
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
                <h3 class="tm-h3"><?=lang('Global.paymentreport')?></h3>
            </div>

            <!-- Button Trigger Modal export -->
            <div class="uk-width-1-2@m uk-text-right@m">
                <a type="button" class="uk-button uk-button-primary uk-preserve-color uk-margin-right-remove" target="_blank" href="export/payment?daterange=<?=date('Y-m-d', $startdate)?>+-+<?=date('Y-m-d', $enddate)?>"><?=lang('Global.export')?></a>
            </div>
            <!-- End Of Button Trigger Modal export-->

        </div>
    </div>
    <!-- End Of Page Heading -->

    <!-- Filter -->
    <div class="uk-width-1-1 uk-margin">
        <form id="short" action="report/payment" method="get">
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

    <div class="uk-card uk-card-default uk-card-body uk-margin uk-width-1-1@m">
        <h3 class="uk-card-title"><?=lang('Global.paymentreport')?></h3>
        <div id="piechart" ></div>
    </div>

    <div uk-grid class="uk-flex-middle">
        <!-- Search Filter -->
        <div class="uk-width-1-2@m">
            <form class="uk-search uk-search-default" method="GET" action="report/payment" style="background-color: #fff; border-radius: 7px;">
                <span uk-search-icon style="color: #000;"></span>
                <input class="uk-search-input" type="search" placeholder="Search" aria-label="Search" name="search" style="border-radius: 7px;">
            </form>
        </div>
        <!-- End Search Filter -->

        <div class="uk-width-1-2@m uk-text-right@m">
            <P class="uk-text-large uk-text-bold uk-text-right uk-margin " style="font-size:20px;color:white;"><?=lang('Global.total')?> <?=lang('Global.payment')?> : <?php echo "Rp. ".number_format($total,0,',','.');" ";?></p>
        </div>
    </div>

    <div class="uk-overflow-auto">
        <table class="uk-table uk-table-divider uk-table-responsive uk-margin-top" id="">
            <thead>
                <tr>
                    <th class="uk-text-large uk-text-bold"><?=lang('Global.payment')?></th>
                    <th class="uk-text-large uk-text-bold"><?=lang('Global.totaltransaction')?></th>
                    <th class="uk-text-large uk-text-bold"><?=lang('Global.value')?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payments as $pay ){ ?>
                    <tr>
                        <td style="color:white;"><?=$pay['name']?></td>
                        <td class="" style="color:white;"><?= $pay['qty']?></td>
                        <td class="" style="color:white;"><?php echo "Rp. ".number_format($pay['value'],0,',','.');" ";?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <!-- <div class="uk-light">
            </?= $pager->links('reportpayment', 'front_full') ?>
        </div> -->
    </div>


<?= view('Views/Auth/_message_block') ?>

<?= $this->endSection() ?>