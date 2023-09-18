<?= $this->extend('layout') ?>

<?= $this->section('extraScript') ?>
<script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
<script src="js/cdn.datatables.net_1.13.4_js_jquery.dataTables.min.js"></script>
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
                $value = $pay['pvalue'];
                $name = $pay['pname'];
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
    </div>
</div>

<div class="uk-card uk-card-default uk-card-body uk-margin uk-width-1-1@m">
    <h3 class="uk-card-title"><?=lang('Global.paymentreport')?></h3>
    <div id="piechart" ></div>
</div>

<table class="uk-table uk-table-divider uk-table-responsive uk-margin-top" id="example">
    <caption class="uk-text-large uk-text-bold uk-margin" style="font-size:20px;"><?=lang('Global.paymentreport')?></caption>
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
                <td style="color:white;"><?=$pay['pname']?></td>
                <td class="" style="color:white;"><?= $pay['qty']?></td>
                <td class="" style="color:white;"><?php echo "Rp. ".number_format($pay['pvalue'],2,',','.');" ";?></td>
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