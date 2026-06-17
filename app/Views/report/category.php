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
        data.addColumn('string', 'name');
        data.addColumn('number', 'qty');
        data.addColumn('number', 'grossvalue');
        data.addColumn('number', 'netvalue');
        data.addRows([
            <?php foreach ($catedata as $category){
                $name           = $category['name'];
                $sold           = $category['qty'];
                $sales          = $category['grossvalue'];
                $net            = $category['netvalue'];
                echo "['$name',$sold,$sales,$net],";
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
                <a
                    type="button"
                    class="uk-button uk-button-primary uk-preserve-color uk-margin-right-remove"
                    target="_blank"
                    href="<?= base_url('export/category') . '?' . http_build_query([
                        'daterange' => $daterange,
                        'search'    => $search
                    ]) ?>">
                    
                    <?=lang('Global.export')?>
                </a>
            </div>
            <!-- End Of Button Trigger Modal export-->

        </div>
    </div>
    <!-- End Of Page Heading -->

    <!-- Daterange Filter -->
    <div class="uk-margin">
        <form id="filterForm" action="report/category" method="GET">
            <!-- Filter -->
            <div class="uk-width-1-1 uk-margin">
                <div class="uk-inline">
                    <span class="uk-form-icon uk-form-icon-flip" uk-icon="calendar"></span>
                    <input
                        type="hidden"
                        name="daterange"
                        id="daterange-hidden"
                        value="<?= esc($daterange) ?>"
                    >
                    <input
                        type="text"
                        id="daterange-display"
                        class="uk-input"
                    >
                </div>
            </div>

            <div class="uk-card uk-card-default uk-card-body uk-margin uk-width-1-1@m">
                <h3 class="uk-card-title"><?=lang('Global.categoryreport')?></h3>
                <div id="piechart" ></div>
            </div>

            <div uk-grid class="uk-flex-middle uk-margin-bottom">
                <!-- Search Filter -->
                <div class="uk-width-1-4@m">
                    <div class="uk-search uk-search-default"
                        style="background-color:#fff;border-radius:7px;">
                        <span uk-search-icon style="color:#000;"></span>
                        <input
                            class="uk-search-input"
                            type="search"
                            placeholder="Search"
                            name="search"
                            value="<?= esc($search ?? '') ?>"
                            style="border-radius:7px;"
                        >
                    </div>
                </div>
                <div class="uk-width-1-4@m uk-text-left@m">
                    <p class="uk-text-default uk-margin" style="font-size:20px;color:white;"><?=lang('Global.total')?> <?=lang('Global.net')?> : <?php echo "Rp. ".number_format($netsales,0,',','.');" ";?></p> 
                </div>
                <div class="uk-width-1-4@m uk-text-left@m">
                    <p class="uk-text-default uk-margin" style="font-size:20px;color:white;"><?=lang('Global.total')?> <?=lang('Global.gross')?> : <?php echo "Rp. ".number_format($gross,0,',','.');" ";?></p>
                </div>
                <div class="uk-width-1-4@m uk-text-left@m">
                    <p class="uk-text-default uk-margin" style="font-size:20px;color:white;"><?=lang('Global.total')?> <?=lang('Global.soldItem')?> : <?php echo $qty;?></p>
                </div>
            </div>

            <button type="submit" hidden></button>
        </form>
    </div>

    <!-- Sorting Data Based On Net Value -->
    <?php
    foreach ($catedata as &$cat) {
        $cat['totalnetvalue'] = $cat['netvalue'];
    }
    usort($catedata, function($a, $b) {
        return $b['totalnetvalue'] <=> $a['totalnetvalue'];
    });
    ?>
    <!-- Sorting Data Based On Net Value End -->

    <div class="uk-overflow-auto">
        <table class="uk-table uk-table-divider uk-table-responsive uk-margin-top">
            <thead>
                <tr>
                    <th><?=lang('Global.category')?></th>
                    <th class="uk-text-center"><?=lang('Global.net')?></th>
                    <th class="uk-text-center"><?=lang('Global.gross')?></th>
                    <th class="uk-text-center"><?=lang('Global.soldItem')?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($catedata as $cate ){ ?>
                    <tr>
                        <td style="color:white;"><?=$cate['name']?></td>
                        <td class="uk-text-center" style="color:white;"><?php echo "Rp. ".number_format($cate['netvalue']),0,',','.';" ";?></td>
                        <td class="uk-text-center" style="color:white;"><?php echo "Rp. ".number_format($cate['grossvalue']),0,',','.';" ";?></td>
                        <td style="color:white;" class="uk-text-center"><?= $cate['qty'] ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <script>
    $(function () {
        let range = $('#daterange-hidden').val();
        let start = moment().startOf('day');
        let end   = moment().endOf('day');

        if (range) {
            const [startStr, endStr] = range.split(' - ');

            start = moment(startStr, 'YYYY-MM-DD');
            end   = moment(endStr, 'YYYY-MM-DD');
        }

        $('#daterange-display').daterangepicker({
            startDate: start,
            endDate: end,
            maxDate: new Date(),
            autoUpdateInput: true,
            locale: {
                format: 'MM/DD/YYYY'
            }
        });

        $('#daterange-display').on('apply.daterangepicker', function(ev, picker) {

            $('#daterange-hidden').val(
                picker.startDate.format('YYYY-MM-DD')
                + ' - ' +
                picker.endDate.format('YYYY-MM-DD')
            );

            $('#filterForm').submit();
        });
    });

    let timer;

    $('input[name="search"]').on('keyup', function() {
        clearTimeout(timer);

        timer = setTimeout(function() {
            $('#filterForm').submit();
        }, 500);
    });
    </script>

    <?= view('Views/Auth/_message_block') ?>

    <?= $this->endSection() ?>
