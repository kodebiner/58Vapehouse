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
        data.addColumn('string', 'product');
        data.addColumn('number', 'sold');
        data.addColumn('string', 'category');
        data.addColumn('number', 'value');
        data.addRows([
            <?php foreach ($products as $product){
                $produk     = $product['product'];
                $sold       = $product['sold'];
                $category   = $product['category'];
                $value      = $product['value'];
                echo "['$produk', $sold,'$category',$value],";
            }?>
        ]);

        var options = {
            title: 'Products Percentage %'
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
            <h3 class="tm-h3"><?=lang('Global.product')?> <?=lang('Global.report')?></h3>
        </div>
    </div>
</div>

<div class="uk-card uk-card-default uk-card-body uk-margin uk-width-1-1@m">
    <h3 class="uk-card-title"><?=lang('Global.product')?></h3>
    <div id="piechart" ></div>
</div>

<table class="uk-table uk-table-divider uk-table-responsive uk-margin-top" id="example">
    <caption class="uk-text-large uk-text-bold uk-margin" style="font-size:20px;">Table Payment Report</caption>
    <thead>
        <tr>
            <th class="uk-text-large uk-text-bold"><?=lang('Global.product')?></th>
            <th class="uk-text-center uk-text-large uk-text-bold">Total Transaction</th>
            <th class="uk-text-center uk-text-large uk-text-bold">Category</th>
            <th class="uk-text-center uk-text-large uk-text-bold">Total Transaction Value</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($products as $product ){ ?>
            <tr>
                <td style="color:white;"><?=$product['product']?></td>
                <td class="uk-text-center" style="color:white;"><?=$product['sold']?></td>
                <td class="uk-text-center" style="color:white;"><?=$product['category']?></td>
                <td class="uk-text-center" style="color:white;"><?php echo "Rp. ".number_format($product['value'],2,',','.');" ";?></td>
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