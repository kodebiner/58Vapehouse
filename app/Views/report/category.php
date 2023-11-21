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
        data.addColumn('string', 'category');
        data.addColumn('number', 'qty');
        data.addColumn('number', 'value');
        data.addColumn('number', 'net');
        data.addRows([
            <?php foreach ($catedata as $product){
                $category       = $product['cate'];
                $sold           = $product['qty'];
                $sales          = $product['value'];
                $net            = $product['netval'];
                echo "['$category',$sold,$sales,$net],";
            }?>
        ]);

        var options = {
            title: '<?=lang('Global.category')?> Percentage %'
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
                <h3 class="tm-h3"><?=lang('Global.categoryreport')?></h3>
            </div>

            <!-- Button Trigger Modal export -->
            <div class="uk-width-1-2@m uk-text-right@m">
                <a type="button" class="uk-button uk-button-primary uk-preserve-color uk-margin-right-remove" target="_blank" href="export/category?daterange=<?=date('Y-m-d', $startdate)?>+-+<?=date('Y-m-d', $enddate)?>"><?=lang('Global.export')?></a>
            </div>
            <!-- End Of Button Trigger Modal export-->

        </div>
    </div>
    <!-- End Of Page Heading -->

    <!-- Daterange Filter -->
    <div class="uk-width-1-1 uk-margin">
        <form id="short" action="report/category" method="get">
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
        <h3 class="uk-card-title"><?=lang('Global.categoryreport')?></h3>
        <div id="piechart"></div>
    </div>

    <div uk-grid class="uk-flex-middle uk-margin-bottom">
        <!-- Search Filter -->
        <div class="uk-width-1-4@m">
            <form class="uk-search uk-search-default" method="GET" action="report/category" style="background-color: #fff; border-radius: 7px;">
                <span uk-search-icon style="color: #000;"></span>
                <input class="uk-search-input" type="search" placeholder="Search" aria-label="Search" name="search" style="border-radius: 7px;">
            </form>
        </div>
        <!-- End Search Filter -->
        <div class="uk-width-1-4@m uk-text-left@m">
            <p class="uk-text-default uk-margin" style="font-size:20px;color:white;"> <?=lang('Global.total')?> <?=lang('Global.sales')?> : <?php echo "Rp. ".number_format($net,0,',','.');" ";?></p> 
        </div>
        <div class="uk-width-1-4@m uk-text-left@m">
            <p class="uk-text-default uk-margin" style="font-size:20px;color:white;"> <?=lang('Global.gross')?> : <?php echo "Rp. ".number_format($gross,0,',','.');" ";?></p>
        </div>
        <div class="uk-width-1-4@m uk-text-left@m">
            <p class="uk-text-default uk-margin" style="font-size:20px;color:white;"> <?=lang('Global.total')?><?=lang('Global.soldItem')?> : <?php echo $qty;?></p>
        </div>
            
    </div>

    <div class="uk-overflow-auto">
        <table class="uk-table uk-table-divider uk-table-responsive uk-margin-top" id="example">
            <thead>
                <tr>
                    <th><?=lang('Global.category')?></th>
                    <th class="uk-text-center"><?=lang('Global.sales')?></th>
                    <th class="uk-text-center"><?=lang('Global.gross')?></th>
                    <th class="uk-text-center"><?=lang('Global.soldItem')?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($catedata as $cat ){ ?>
                    <tr>
                        <td style="color:white;"><?=$cat['cate']?></td>
                        <td class="uk-text-center" style="color:white;"><?php echo "Rp. ".number_format($cat['netval'],0,',','.');" ";?></td>
                        <td class="uk-text-center" style="color:white;"><?php echo "Rp. ".number_format($cat['value'],0,',','.');" ";?></td>
                        <td style="color:white;" class="uk-text-center"><?=$cat['qty']?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <div class="uk-light">
            <?= $pager->links('reportcategory', 'front_full') ?>
        </div>
    </div>
    <?= view('Views/Auth/_message_block') ?>

    <?= $this->endSection() ?>
