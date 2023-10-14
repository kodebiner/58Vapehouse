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
        data.addColumn('string', 'product');
        data.addColumn('number', 'sold');
        data.addColumn('string', 'category');
        data.addColumn('number', 'value');
        data.addRows([
            <?php foreach ($products as $product){
                $produk     = $product['product'];
                $sold       = $product['qty'];
                $category   = $product['category'];
                $value      = $product['value'];
                echo "[ '$produk',$sold,'$category',$value],";
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
                <a type="button" class="uk-button uk-button-primary uk-preserve-color uk-margin-right-remove" target="_blank" href="export/product?daterange=<?=date('Y-m-d', $startdate)?>+-+<?=date('Y-m-d', $enddate)?>"><?=lang('Global.export')?></a>
            </div>
            <!-- End Of Button Trigger Modal export-->

        </div>
    </div>
    <!-- End Of Page Heading -->

    <!-- Filter -->
    <div class="uk-width-1-1 uk-margin">
        <form id="short" action="report/product" method="get">
            <div class="uk-inline">
                <span class="uk-form-icon uk-form-icon-flip" uk-icon="calendar"></span>
                <input class="uk-input uk-width-medium uk-border-rounded" type="text" id="daterange" name="daterange" value="<?=date('m/d/Y', $startdate)?> - <?=date('m/d/Y', $enddate)?>" />
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

    <div class="uk-card uk-card-default uk-card-body uk-margin uk-width-1-1@m">
        <h3 class="uk-card-title"><?=lang('Global.productreport')?></h3>
        <div id="piechart" ></div>
    </div>

        <div uk-grid class="uk-flex-middle uk-margin-bottom">
            <!-- Search Filter -->
            <div class="uk-width-1-4@m">
                <form class="uk-search uk-search-default" method="GET" action="report/product" style="background-color: #fff; border-radius: 7px;">
                    <span uk-search-icon style="color: #000;"></span>
                    <input class="uk-search-input" type="search" placeholder="Search" aria-label="Search" name="search" style="border-radius: 7px;">
                </form>
            </div>
            <!-- End Search Filter -->
            <div class="uk-width-1-4@m uk-text-left@m">
                <p class="uk-text-default uk-margin" style="font-size:20px;color:white;"><?=lang('Global.total')?> <?=lang('Global.sales')?> : <?php echo "Rp. ".number_format($netsales,0,',','.');" ";?></p> 
            </div>
            <div class="uk-width-1-4@m uk-text-left@m">
                <p class="uk-text-default uk-margin" style="font-size:20px;color:white;"> <?=lang('Global.gross')?> : <?php echo "Rp. ".number_format($grosstotal,0,',','.');" ";?></p>
            </div>
            <div class="uk-width-1-4@m uk-text-left@m">
                <p class="uk-text-default uk-margin" style="font-size:20px;color:white;"><?=lang('Global.total')?> <?=lang('Global.transaction')?> : <?php echo $totalstock;?></p>
            </div>
        </div>

        <div class="uk-overflow-auto">
            <table class="uk-table uk-table-divider uk-table-responsive uk-margin-top" id="example">
                <div class="uk-column-1-3@m">
                </div>
                <thead>
                    <tr>
                        <th><?=lang('Global.product')?></th>
                        <th><?=lang('Global.category')?></th>
                        <th><?=lang('Global.sales')?></th>
                        <th><?=lang('Global.gross')?></th>
                        <th class="uk-text-center"><?=lang('Global.transaction')?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product ){ ?>
                        <tr>
                            <td style="color:white;"><?=$product['product']?></td>
                            <td style="color:white;"><?=$product['category']?></td>
                            <td style="color:white;"><?php echo "Rp. ".number_format($product['value'],0,',','.');" ";?></td>
                            <td style="color:white;"><?php echo "Rp. ".number_format($product['gross'],0,',','.');" ";?></td>
                            <td class="uk-text-center" style="color:white;"><?=$product['qty']?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <div class="uk-light">
                <?= $pager->links('reportproduct', 'front_full') ?>
            </div>
        </div>

    <?= view('Views/Auth/_message_block') ?>

    <?= $this->endSection() ?>