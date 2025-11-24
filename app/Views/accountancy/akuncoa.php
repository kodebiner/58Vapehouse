<?= $this->extend('layout') ?>
<?= $this->section('extraScript') ?>
<script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
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
                <h3 class="tm-h3"><?=lang('Global.accountancy').' - Akun (COA)'?></h3>
            </div>

            <!-- Button Trigger Modal Add -->
            <div class="uk-text-right@m">
                <div class="uk-margin">
                    <button type="button" class="uk-button uk-button-primary uk-preserve-color" uk-toggle="target: #tambahdata">Tambah Akun</button>
                </div>

                <div class="uk-margin">
                    <button type="button" class="uk-button uk-button-secondary uk-preserve-color" uk-toggle="target: #tambahdata">Atur Saldo Awal</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Add -->
    <div uk-modal class="uk-flex-top" id="tambahdata">
        <div class="uk-modal-dialog uk-margin-auto-vertical">
            <div class="uk-modal-content">
                <div class="uk-modal-header">
                    <div class="uk-child-width-1-2" uk-grid>
                        <div>
                            <h5 class="uk-modal-title" id="tambahdata">Tambah Akun</h5>
                        </div>
                        <div class="uk-text-right">
                            <button class="uk-modal-close uk-icon-button-delete" uk-icon="icon: close;" type="button"></button>
                        </div>
                    </div>
                </div>
                <div class="uk-modal-body">
                    <form class="uk-form-stacked" role="form" action="accountancy/akuncoa/create" method="post">
                        <?= csrf_field() ?>
                        <div class="uk-margin-bottom">
                            <label class="uk-form-label" for="name">Nama</label>
                            <div class="uk-form-controls">
                                <input type="text" class="uk-input" id="name" name="name" placeholder="Nama" required />
                            </div>
                        </div>

                        <div class="uk-margin-bottom">
                            <label class="uk-form-label" for="category">Kategori</label>
                            <div class="uk-form-controls">
                                <select class="uk-select" id="category" name="category" required>
                                    <option value="" disabled selected>Pilih Kategori</option>
                                    <?php foreach ($categories as $category) { ?>
                                        <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="uk-margin-bottom">
                            <label class="uk-form-label" for="description">Deskripsi</label>
                            <div class="uk-form-controls">
                                <textarea class="uk-textarea" id="description" name="description" placeholder="Description" rows="4"></textarea>
                            </div>
                        </div>

                        <div class="uk-margin-bottom uk-flex uk-flex-middle uk-grid-small" uk-grid>
                            <div>
                                <span id="icon_unlock" uk-icon="unlock"></span>
                            </div>
                            <input type="hidden" name="status_lock" id="status_lock_val" value="0">
                            <label class="switch uk-margin-left">
                                <input id="status_lock" type="checkbox">
                                <span class="slider round"></span>
                            </label>
                            <div>
                                <span id="icon_lock" uk-icon="lock"></span>
                            </div>
                        </div>

                        <script>
                            $(document).ready(function() {
                                $("#icon_unlock").css("color", "#39f");
                                $("#icon_lock").css("color", "#999");

                                $("#status_lock").change(function() {
                                    const checked = $(this).is(':checked');
                                    $("#status_lock_val").val(checked ? "1" : "0");

                                    if (checked) {
                                        $("#icon_lock").css("color", "#39f");
                                        $("#icon_unlock").css("color", "#999");
                                    } else {
                                        $("#icon_unlock").css("color", "#39f");
                                        $("#icon_lock").css("color", "#999");
                                    }
                                });
                            });
                        </script>

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
                    <th class="uk-text-center uk-width-small">Kode</th>
                    <th class="uk-width-large">Nama</th>
                    <th class="uk-width-medium">Kategori</th>
                    <th class="uk-width-medium">Tipe Akun</th>
                    <th class="uk-width-medium">Deskripsi</th>
                    <th class="uk-text-center uk-width-large"><?=lang('Global.action')?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($coas as $coa) { ?>
                    <tr>
                        <td class="uk-text-center"><?= $coa['kode'] ?></td>
                        <td class=""><?= $coa['name']; ?></td>
                        <td class=""><?= $coa['category']; ?></td>
                        <td class=""><?= $coa['coa_type']; ?></td>
                        <td class=""><?= $coa['description']; ?></td>
                        <td class="uk-child-width-auto uk-flex-center uk-grid-row-small uk-grid-column-small" uk-grid>
                            <!-- Button Trigger Modal Edit -->
                            <div>
                                <a class="uk-icon-button" uk-icon="pencil" uk-toggle="target: #editdata<?= $coa['id'] ?>"></a>
                            </div>
                            <!-- End Of Button Trigger Modal Edit -->

                            <!-- Button Delete -->
                            <!-- End Of Button Delete -->
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<script>
    $(document).ready(function () {
        $('#example').DataTable();
    });
</script>
<?= $this->endSection() ?>