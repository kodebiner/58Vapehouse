<?= $this->extend('layout') ?>
<?= $this->section('extraScript') ?>
<script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
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
        ]);

        var options = {
            title: '<?=lang('Global.brand')?> Percentage %'
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart'));

        chart.draw(data, options);
    }
</script>
<?= $this->endSection() ?>
<?= $this->section('main') ?>
<div class="uk-width-1-1 uk-height-1-1" class="uk-inline">
    <div>
        <?= view('Views/Auth/_permission_message') ?>
    </div>

    <!-- Page Heading -->
    <div class="tm-card-header uk-light uk-margin-bottom">
        <div uk-grid class="uk-flex-middle uk-child-width-1-2@m">
            <div>
                <h3 class="tm-h3"><?=lang('Global.accountancy').' - Perubahan Modal'?></h3>
            </div>
            <?php if (in_groups('owner')) { ?>
                <!-- Date Range Filter -->
                <div>
                    <div class="uk-margin uk-text-right">
                        <form id="short" action="dashboard" method="get">
                            <div class="uk-inline">
                                <span class="uk-form-icon uk-form-icon-flip" uk-icon="calendar"></span>
                                <input class="uk-input uk-width-medium uk-border-rounded" type="text" id="daterange" name="daterange" value="<?=date('m/d/Y', $startdate)?> - <?=date('m/d/Y', $enddate)?>" />
                            </div>
                        </form>
                        <script>
                            $(function() {
                                $('input[name="daterange"]').daterangepicker({
                                    opens: 'left',
                                    maxDate: new Date(),
                                }, function(start, end, label) {
                                    document.getElementById('daterange').value = start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD');
                                    document.getElementById('short').submit();
                                });
                            });
                        </script>
                    </div>
                </div>
            <?php } ?>
            <!-- Date Range Filter End -->
        </div>
    </div>
    <!-- End Of Page Heading -->
</div>
<?= $this->endSection() ?>