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
        data.addColumn('string', 'catname');
        data.addColumn('number', 'stock');
        data.addColumn('number', 'hargajual');
        data.addColumn('number', 'hargamodal');
        data.addRows([
            <?php foreach ($products as $product){
                $category       = $product['catname'];
                $sold           = $product['stock'];
                $hargajual      = $product['hargajual'];
                $hargamodal     = $product['hargamodal'];
                echo "['$category',$sold,$hargajual,$hargamodal],";
            }?>
        ]);

        var options = {
            title: '<?=lang('Global.category')?> percentage %'
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
                <h3 class="tm-h3"><?=lang('Global.category')?> <?=lang('Global.product')?></h3>
            </div>

            <!-- Button Trigger Modal export -->
            <div class="uk-width-1-2@m uk-text-right@m">
                <a type="button" class="uk-button uk-button-primary uk-preserve-color uk-margin-right-remove" target="_blank" href="export/stockcategory"><?=lang('Global.export')?></a>
            </div>
            <!-- End Of Button Trigger Modal export-->

        </div>
    </div>
    <!-- End Of Page Heading -->

    <div class="uk-card uk-card-default uk-card-body uk-margin uk-width-1-1@m">
        <h3 class="uk-card-title"><?=lang('Global.category')?> <?=lang('Global.product')?></h3>
        <div id="piechart" ></div>
    </div>

    <table class="uk-table uk-table-divider uk-table-responsive uk-margin-top" id="example">
        <div class="uk-column-1-3 uk-margin-bottom">
            <p class="uk-text-large  uk-margin" style="font-size:20px;color:white;"><?=lang('Global.total')?> <?=lang('Global.capitalPrice')?> : <?php echo "Rp. ".number_format($whole,0,',','.');" ";?></p>
            
            <p class="uk-text-large uk-margin" style="font-size:20px;color:white;"><?=lang('Global.total')?> <?=lang('Global.stock')?> : <?php echo $stock;?></p>
        </div>
        <thead>
            <tr>
                <th><?=lang('Global.category')?></th>
                <th class="uk-text-center"><?=lang('Global.capitalPrice')?></th>
                <th class="uk-text-center"><?=lang('Global.stock')?></th>
        </thead>
        <tbody>
            <?php foreach ($products as $product ){ ?>
                <tr>
                    <td style="color:white;"><?=$product['catname']?></td>
                    <td class="uk-text-center" style="color:white;"><?php echo "Rp. ".number_format($product['whole'],0,',','.');" ";?></td>
                    <td class="uk-text-center" style="color:white;"><?=$product['stock']?></td>
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
