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
                <h3 class="tm-h3"><?=lang('Global.accountancy').' - Perusahaan'?></h3>
            </div>
            <?php if (in_groups('owner')) : ?>
                <!-- Button Trigger Modal Add -->
                <div class="uk-width-1-2@m uk-text-right@m">
                    <button type="button" class="uk-button uk-button-primary uk-preserve-color" uk-toggle="target: #tambahdata">Tambah Perusahaan</button>
                </div>
                <!-- End Of Button Trigger Modal Add -->
            <?php endif ?>
        </div>
    </div>
    <!-- End Of Page Heading -->

    <!-- Company List -->
    <div class="uk-child-width-1-1 uk-child-width-1-3@m" uk-grid uk-height-match="target: > div > .uk-card">
        <?php foreach ($outlets as $outlet) { ?>
            <div>
                <div class="uk-card uk-card-default uk-card-body uk-text-center">
                    <img src="img/<?= $gconfig['logo'] ?>" />
                    <h3 class="uk-card-title uk-margin-top"><?= $outlet['name'] ?></h3>
                    <p><?= $outlet['address'] ?></p>
                </div>
            </div>
        <?php } ?>
    </div>

    <!-- Modal Add -->
    <div uk-modal class="uk-flex-top" id="tambahdata">
        <div class="uk-modal-dialog uk-margin-auto-vertical">
            <div class="uk-modal-content">
                <div class="uk-modal-header">
                    <div class="uk-child-width-1-2" uk-grid>
                        <div>
                            <h5 class="uk-modal-title" id="tambahdata" >Tambah Perusahaan</h5>
                        </div>
                        <div class="uk-text-right">
                            <button class="uk-modal-close uk-icon-button-delete" uk-icon="icon: close;" type="button"></button>
                        </div>
                    </div>
                </div>
                <div class="uk-modal-body">
                    <form class="uk-form-stacked" role="form" action="outlet/create" method="post">
                        <?= csrf_field() ?>

                        <div class="uk-margin-bottom">
                            <label class="uk-form-label" for="name"><?=lang('Global.name')?></label>
                            <div class="uk-form-controls">
                                <input type="text" class="uk-input <?php if (session('errors.name')) : ?>tm-form-invalid<?php endif ?>" id="name" name="name" placeholder="<?=lang('Global.name')?>" required />
                            </div>
                        </div>

                        <div class="uk-margin">
                            <label class="uk-form-label" for="address"><?=lang('Global.address')?></label>
                            <div class="uk-form-controls">
                                <input type="text" class="uk-input <?php if (session('errors.address')) : ?>tm-form-invalid<?php endif ?>" name="address" id="address" placeholder="<?=lang('Global.address')?>" required/>
                            </div>
                        </div>

                        <div class="uk-margin-bottom">
                            <label class="uk-form-label" for="maps"><?=lang('Global.maps')?></label>
                            <div class="uk-form-controls">
                                <input type="text" class="uk-input <?php if (session('errors.maps')) : ?>tm-form-invalid<?php endif ?>" id="maps" name="maps" placeholder="<?=lang('Global.maps')?>" required />
                            </div>
                        </div>

                        <div class="uk-margin-bottom">
                            <label class="uk-form-label" for="instagram"><?=lang('Global.instagram')?></label>
                            <div class="uk-form-controls">
                                <input type="text" class="uk-input <?php if (session('errors.instagram')) : ?>tm-form-invalid<?php endif ?>" id="instagram" name="instagram" placeholder="<?=lang('Global.instagram')?>" required />
                            </div>
                        </div>

                        <div class="uk-margin-bottom">
                            <label class="uk-form-label" for="maps"><?=lang('Global.facebook')?></label>
                            <div class="uk-form-controls">
                                <input type="text" class="uk-input <?php if (session('errors.facebook')) : ?>tm-form-invalid<?php endif ?>" id="facebook" name="facebook" placeholder="<?=lang('Global.facebook')?>" required />
                            </div>
                        </div>

                        <div class="uk-margin-bottom">
                            <label class="uk-form-label" for="maps"><?=lang('Global.phone')?></label>
                            <div class="uk-form-controls">
                                <input type="text" class="uk-input <?php if (session('errors.phone')) : ?>tm-form-invalid<?php endif ?>" id="phone" name="phone" placeholder="<?=lang('Global.phone')?>" required />
                            </div>
                        </div>

                        <hr>

                        <div class="uk-margin">
                            <button type="submit" class="uk-button uk-button-primary"><?=lang('Global.save')?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Of Modal Add -->
    <!-- Company List End -->
</div>
<?= $this->endSection() ?>