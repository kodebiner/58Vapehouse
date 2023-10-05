<?= $this->extend('layout') ?>

<?= $this->section('extraScript') ?>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="js/cdn.datatables.net_1.13.4_js_jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'prodname');
        data.addColumn('number', 'stock');
        data.addColumn('string', 'catname');
        data.addColumn('number', 'hargajual');
        data.addColumn('number', 'hargamodal');
        data.addRows([
            <?php foreach ($products as $product){
                $category       = $product['catname'];
                $sold           = $product['stock'];
                $produk         = $product['prodname'];
                $hargajual      = $product['hargajual'];
                $hargamodal     = $product['hargamodal'];
                echo "['$produk',$sold,'$category',$hargajual,$hargamodal],";
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
                <h3 class="tm-h3"><?=lang('Global.productreport')?></h3>
            </div>

            <!-- Button Trigger Modal export -->
            <div class="uk-width-1-2@m uk-text-right@m">
                <a type="button" class="uk-button uk-button-primary uk-preserve-color uk-margin-right-remove" href="export/stockcategory?daterange=<?=date('Y-m-d', $startdate)?>+-+<?=date('Y-m-d', $enddate)?>"><?=lang('Global.export')?></a>
            </div>
            <!-- End Of Button Trigger Modal export-->

        </div>
    </div>
    <!-- End Of Page Heading -->

    <div class="uk-card uk-card-default uk-card-body uk-margin uk-width-1-1@m">
        <h3 class="uk-card-title"><?=lang('Global.product')?> <?=lang('Global.category')?></h3>
        <div id="piechart" ></div>
    </div>

    <table class="uk-table uk-table-divider uk-table-responsive uk-margin-top" id="example">
        <caption class="uk-text-large uk-text-bold uk-margin" style="font-size:20px;"><?=lang('Global.product')?> <?=lang('Global.category')?></caption>
        <thead>
            <tr>
                <th><?=lang('Global.category')?></th>
                <th class="uk-text-center"><?=lang('Global.basePrice')?></th>
                <th class="uk-text-center"><?=lang('Global.capitalPrice')?></th>
                <th><?=lang('Global.stock')?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product ){ ?>
                <tr>
                    <td style="color:white;"><?=$product['catname']?></td>
                    <td class="uk-text-center" style="color:white;"><?php echo "Rp. ".number_format($product['hargajual'],0,',','.');" ";?></td>
                    <td class="uk-text-center" style="color:white;"><?php echo "Rp. ".number_format($product['hargamodal'],0,',','.');" ";?></td>
                    <td style="color:white;"><?=$product['stock']?></td>
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
