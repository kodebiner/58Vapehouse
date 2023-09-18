<?= $this->extend('layout') ?>

<?= $this->section('extraScript') ?>


<script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = google.visualization.arrayToDataTable([
            ['Task', 'Hours per Day'],
            ['<?=lang('Global.discount')?> <?=lang('Global.variant')?>', <?php echo ' '.$trxvardis.'';?>],
            ['<?=lang('Global.discount')?> <?=lang('Global.transaction')?>', <?php echo ' '.$trxdisc.'';?>],
            ['<?=lang('Global.discount')?> <?=lang('Global.point')?>',  <?php echo ' '.$poindisc.'';?>],
            ['<?=lang('Global.discount')?> <?=lang('Global.memberDiscount')?>',  <?php echo ' '.$memberdis.'';?>],
        ]);       

        var options = {
            title: 'Discount Percentage %'
            
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
                <h3 class="tm-h3"><?=lang('Global.discountreport')?></h3>
            </div>
        </div>
    </div>

    <div class="uk-card uk-width-1-1@m uk-card-default uk-card-hover uk-card-body uk-margin-top">
        <h3 class="uk-card-title"><?=lang('Global.discountreport')?></h3>
        <div id="piechart" class="uk-width-auto"></div>
    </div>

    <div class="uk-child-width-1-2@s uk-grid-match uk-margin-top" uk-grid>
        <div>
            <div class="uk-card uk-card-default uk-card-secondary uk-card-hover uk-card-body">
                <h3 class="uk-card-title uk-margin-remove-bottom"><?=lang('Global.memberDiscount')?></h3>
                <p class="uk-margin-remove-top uk-text-bolder"><?=lang('Global.totaldiscmember')?></p>
                <hr>
                <div>
                <div uk-grid>
                    <div class="uk-width-1-1 uk-margin-remove uk-text-large uk-text-bolder uk-text-right" style="font-size:30px;"><?php echo "Rp. ".number_format($memberdis,2,',','.');" ";?></div>
                </div>
                </div>
            </div>
        </div>

        <div>
            <div class="uk-card uk-card-default uk-card-hover uk-card-body">
                <h3 class="uk-card-title uk-margin-remove-bottom"><?=lang('Global.discpoint')?></h3>
                <p class="uk-margin-remove-top uk-text-bolder"><?=lang('Global.totaldiscpoint')?></p>
                <hr>
                <div>
                <div uk-grid>
                    <div class="uk-width-1-1 uk-margin-remove uk-text-large uk-text-bolder uk-text-right" style="font-size:30px;"><?php echo "Rp. ".number_format($poindisc,2,',','.');" ";?></div>
                </div>
                </div>
            </div>
        </div>

        <div>
            <div class="uk-card uk-card-default uk-card-hover uk-card-body">
                <h3 class="uk-card-title uk-margin-remove-bottom"><?=lang('Global.discount')?> <?=lang('Global.variant')?></h3>
                <p class="uk-margin-remove-top uk-text-bolder"><?=lang('Global.totaldiscvar')?></p>
                <hr>
                <div>
                    <div uk-grid>
                        <div class="uk-width-1-1 uk-margin-remove uk-text-large uk-text-bolder uk-text-right" style="font-size:30px;"><?php echo "Rp. ".number_format($trxvardis,2,',','.');" ";?></div>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <div class="uk-card uk-card-default uk-card-secondary uk-card-hover uk-card-body">
                <h3 class="uk-card-title uk-margin-remove-bottom"><?=lang('Global.discount')?> <?=lang('Global.transaction')?></h3>
                <p class="uk-margin-remove-top uk-text-bolder"><?=lang('Global.totaldisctrx')?> <?=lang('Global.discount')?> <?=lang('Global.transaction')?></p>
                <hr>
                <div>
                    <div uk-grid>
                        <div class="uk-width-1-1 uk-margin-remove uk-text-large uk-text-bolder uk-text-right" style="font-size:30px;"><?php echo "Rp. ".number_format($trxdisc,2,',','.');" ";?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- End Of Page Heading -->

<?= view('Views/Auth/_message_block') ?>

<?= $this->endSection() ?>