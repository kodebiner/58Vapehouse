<?= $this->extend('layout') ?>
<?= $this->section('extraScript') ?>
<script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
<script src="js/cdn.datatables.net_1.13.4_js_jquery.dataTables.min.js"></script>
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
        <div uk-grid class="uk-flex-middle uk-child-width-1-1 uk-child-width-1-2@m">
            <div>
                <h3 class="tm-h3"><?=lang('Global.accountancy').' - Kontak'?></h3>
            </div>

            <!-- Button Trigger Modal Add -->
            <div class="uk-text-right@m">
                <div class="uk-margin">
                    <button type="button" class="uk-button uk-button-primary uk-preserve-color" uk-toggle="target: #tambahdata">Tambah Kontak</button>
                </div>

                <!-- Button Export -->
                <div class="uk-flex-right uk-child-width-1-1 uk-child-width-1-6@m" uk-grid>
                    <div>
                        <a type="button" class="uk-button uk-button-default" target="_blank" href="export/excelcontact">Excel</a>
                    </div>
                    <div>
                        <a type="button" class="uk-button uk-button-default" target="_blank" href="export/pdfcontact">PDF</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Of Page Heading -->

    <!-- Modal Add -->
    <div uk-modal class="uk-flex-top" id="tambahdata">
        <div class="uk-modal-dialog uk-margin-auto-vertical">
            <div class="uk-modal-content">
                <div class="uk-modal-header">
                    <div class="uk-child-width-1-2" uk-grid>
                        <div>
                            <h5 class="uk-modal-title" id="tambahdata">Tambah Kontak</h5>
                        </div>
                        <div class="uk-text-right">
                            <button class="uk-modal-close uk-icon-button-delete" uk-icon="icon: close;" type="button"></button>
                        </div>
                    </div>
                </div>
                <div class="uk-modal-body">
                    <form class="uk-form-stacked" role="form" action="accountancy/contact/create" method="post">
                        <?= csrf_field() ?>

                        <div class="uk-margin-bottom">
                            <label class="uk-form-label" for="name">Nama</label>
                            <div class="uk-form-controls">
                                <input type="text" class="uk-input" id="name" name="name" placeholder="Nama" required />
                            </div>
                        </div>

                        <div class="uk-margin">
                            <label class="uk-form-label" for="phone"><?=lang('Global.phone')?></label>
                            <div class="uk-form-controls">
                                <input type="number" name="phone" id="phone" placeholder="<?=lang('Global.phone')?>" class="uk-input <?php if (session('errors.phone')) : ?>tm-form-invalid<?php endif ?>"/>
                            </div>
                        </div>

                        <div class="uk-margin">
                            <label class="uk-form-label" for="email">Email</label>
                            <div class="uk-form-controls">
                                <input type="email" name="email" id="email" placeholder="Email" class="uk-input <?php if (session('errors.email')) : ?>tm-form-invalid<?php endif ?>"/>
                            </div>
                        </div>

                        <div class="uk-margin">
                            <label class="uk-form-label" for="address">Alamat</label>
                            <div class="uk-form-controls">
                                <input type="text" name="address" id="address" placeholder="Alamat" class="uk-input" />
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

    <div class="uk-overflow-auto uk-margin">
        <table class="uk-table uk-table-justify uk-table-middle uk-table-divider uk-light" id="example" style="width:100%">
            <thead>
                <tr>
                    <th class="uk-text-center uk-width-small">No</th>
                    <th class="uk-width-large">Nama</th>
                    <th class="uk-width-medium"><?=lang('Global.phone')?></th>
                    <th class="uk-width-medium">Email</th>
                    <th class="uk-width-medium"><?=lang('Global.address')?></th>
                    <th class="uk-text-center uk-width-large"><?=lang('Global.action')?></th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1 ; ?>
                <?php foreach ($contacts as $contact) { ?>
                    <tr>
                        <td class="uk-text-center"><?= $i++; ?></td>
                        <td class=""><?= $contact['name']; ?></td>
                        <td class=""><?= $contact['phone']; ?></td>
                        <td class=""><?= $contact['email']; ?></td>
                        <td class=""><?= $contact['address']; ?></td>
                        <td class="uk-child-width-auto uk-flex-center uk-grid-row-small uk-grid-column-small" uk-grid>
                            <!-- Button Trigger Modal Edit -->
                            <div>
                                <a class="uk-icon-button" uk-icon="pencil" uk-toggle="target: #editdata<?= $contact['id'] ?>"></a>
                            </div>
                            <!-- End Of Button Trigger Modal Edit -->

                            <!-- Button Delete -->
                            <div>
                                <?php if ($contact['status'] == 1) { ?>
                                    <a uk-icon="trash" class="uk-icon-button-delete" href="stock/deletesup/<?= $contact['realid'] ?>" onclick="return confirm('<?= lang('Global.deleteConfirm') ?>')"></a>
                                <?php } ?>
                                <?php if ($contact['status'] == 2) { ?>
                                    <a uk-icon="trash" class="uk-icon-button-delete" href="user/delete/<?= $contact['realid'] ?>" onclick="return confirm('<?= lang('Global.deleteConfirm') ?>')"></a>
                                <?php } ?>
                                <?php if ($contact['status'] == 3) { ?>
                                    <a uk-icon="trash" class="uk-icon-button-delete" href="accountancy/contact/delete/<?= $contact['realid'] ?>" onclick="return confirm('<?= lang('Global.deleteConfirm') ?>')"></a>
                                <?php } ?>
                            </div>
                            <!-- End Of Button Delete -->
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <?php foreach ($contacts as $contact) { ?>
        <div uk-modal class="uk-flex-top" id="editdata<?= $contact['id'] ?>">
            <div class="uk-modal-dialog uk-margin-auto-vertical">
                <div class="uk-modal-content">
                    <div class="uk-modal-header">
                        <div class="uk-child-width-1-2" uk-grid>
                            <div>
                                <h5 class="uk-modal-title" id="editdata">Ubah Kontak</h5>
                            </div>
                            <div class="uk-text-right">
                                <button class="uk-modal-close uk-icon-button-delete" uk-icon="icon: close;" type="button"></button>
                            </div>
                        </div>
                    </div>
                    <div class="uk-modal-body">
                        <?php if ($contact['status'] == 1) { ?>
                            <form class="uk-form-stacked" role="form" action="stock/updatesup/<?= $contact['realid'] ?>" method="post">
                        <?php } ?>
                        <?php if ($contact['status'] == 2) { ?>
                            <form class="uk-form-stacked" role="form" action="user/update/<?= $contact['realid'] ?>" method="post">
                        <?php } ?>
                        <?php if ($contact['status'] == 3) { ?>
                            <form class="uk-form-stacked" role="form" action="accountancy/contact/update/<?= $contact['realid'] ?>" method="post">
                        <?php } ?>
                            <?= csrf_field() ?>

                            <?php if ($contact['status'] == 2) { ?>
                                <div class="uk-margin-bottom">
                                    <label class="uk-form-label" for="firstname">Nama Depan</label>
                                    <div class="uk-form-controls">
                                        <input type="text" class="uk-input" id="firstname" name="firstname" value="<?= $contact['firstname']; ?>" />
                                    </div>
                                </div>
                                <div class="uk-margin-bottom">
                                    <label class="uk-form-label" for="lastname">Nama Belakang</label>
                                    <div class="uk-form-controls">
                                        <input type="text" class="uk-input" id="lastname" name="lastname" value="<?= $contact['lastname']; ?>" />
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div class="uk-margin-bottom">
                                    <label class="uk-form-label" for="name">Nama</label>
                                    <div class="uk-form-controls">
                                        <input type="text" class="uk-input" id="name" name="name" value="<?= $contact['name']; ?>" />
                                    </div>
                                </div>
                            <?php } ?>

                        
                            <div class="uk-margin">
                                <label class="uk-form-label" for="phone"><?=lang('Global.phone')?></label>
                                <div class="uk-form-controls">
                                    <input type="number" class="uk-input" name="phone" id="phone" value="<?= $contact['phone']; ?>"/>
                                </div>
                            </div>

                            <div class="uk-margin">
                                <label class="uk-form-label" for="email">Email</label>
                                <div class="uk-form-controls">
                                    <input type="email" class="uk-input" id="email" name="email" value="<?= $contact['email']; ?>" />
                                </div>
                            </div>

                            <div class="uk-margin">
                                <label class="uk-form-label" for="address"><?=lang('Global.address')?></label>
                                <div class="uk-form-controls">
                                    <input type="text" class="uk-input" id="address" name="address" value="<?= $contact['address']; ?>" />
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
    <?php } ?>
</div>
<script>
    $(document).ready(function () {
        $('#example').DataTable();
    });
</script>
<?= $this->endSection() ?>