<?= $this->extend('layout') ?>
<?= $this->section('extraScript') ?>
<script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
<script src="js/cdn.datatables.net_1.13.4_js_jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<?= $this->endSection() ?>
<?= $this->section('main') ?>
<div class="uk-width-1-1 uk-height-1-1" class="uk-inline">
    <div>
        <!-- </?= view('Views/Auth/_permission_message') ?> -->
        <?= view('Views/Auth/_message_block') ?>
    </div>

    <!-- Page Heading -->
    <div class="tm-card-header uk-light uk-margin-bottom">
        <div uk-grid class="uk-flex-middle uk-child-width-1-2@m">
            <div>
                <h3 class="tm-h3"><?=lang('Global.accountancy').' - Akun (COA)'?></h3>
            </div>

            <div class="uk-text-right@m">
                <!-- Button Trigger Modal Add -->
                <div class="uk-margin">
                    <button type="button" class="uk-button uk-button-primary uk-preserve-color" uk-toggle="target: #tambahdata">Tambah Akun</button>
                </div>

                <!-- Button Trigger Modal Atur Saldo -->
                <div class="uk-margin">
                    <a href="accountancy/akuncoa/early-funds" class="uk-button uk-button-secondary uk-preserve-color">Atur Saldo Awal</a>
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
                                        <option 
                                            value="<?= $category['id'] ?>" 
                                            data-code="<?= $category['cat_code'] ?>" 
                                            data-last-code="<?= $category['coa_code'] ?>"> <?= $category['name'] ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="uk-margin-bottom">
                            <label class="uk-form-label" for="cat_code">Kode</label>
                            <div class="uk-form-controls">
                                <div uk-grid>
                                    <div class="uk-width-expand">
                                        <input type="text" class="uk-input" id="cat_code" name="cat_code" readonly />
                                    </div>
                                    <div class="uk-width-auto uk-padding-remove-left">
                                        <input type="text" class="uk-input" id="coa_code" name="coa_code" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <script>
                            document.getElementById('category').addEventListener('change', function () {
                                const selectedOption = this.options[this.selectedIndex];
                                
                                // 1. Ambil prefix (misal: 11, 12)
                                const catCode = selectedOption.getAttribute('data-code');
                                document.getElementById('cat_code').value = catCode ?? '';

                                // 2. Ambil coa_code terakhir (misal: 003)
                                const lastCode = selectedOption.getAttribute('data-last-code');
                                
                                // 3. Logika Increment (+1)
                                let nextNumber = 1;
                                if (lastCode) {
                                    nextNumber = parseInt(lastCode) + 1;
                                }
                                
                                // 4. Masukkan ke input coa_code dengan format 3 digit (001, 002, dst)
                                document.getElementById('coa_code').value = nextNumber.toString().padStart(3, '0');
                            });
                        </script>

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
                                    $("#status_lock_val").val(checked ? "0" : "1");

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
                        <td class="uk-text-center"><?= $coa['full_kode'] ?></td>
                        <td class="">
                            <?= $coa['name']; ?>
                            <?php if ($coa['status_lock'] == 1) { ?>
                                <span uk-icon="lock" class="uk-margin-small-right"></span>
                            <?php } ?>
                        </td>
                        <td class=""><?= $coa['category']; ?></td>
                        <td class="">
                            <?php if ($coa['coa_type'] == 0) {
                                echo 'Debit';
                            } else {
                                echo 'Kredit';
                            } ?>
                        </td>
                        <td class=""><?= $coa['description']; ?></td>
                        <td class="uk-child-width-auto uk-flex-center uk-grid-row-small uk-grid-column-small" uk-grid>
                            <!-- Button Trigger Modal Edit -->
                            <div>
                                <a class="uk-icon-button" uk-icon="pencil" uk-toggle="target: #editdata<?= $coa['id'] ?>"></a>
                            </div>
                            <!-- End Of Button Trigger Modal Edit -->

                            <!-- Button Delete -->
                            <div>
                                <?php if ($coa['status_lock'] == 0) { ?>
                                    <a uk-icon="trash" class="uk-icon-button-delete" href="accountancy/akuncoa/delete/<?= $coa['id'] ?>" onclick="return confirm('<?= lang('Global.deleteConfirm') ?>')"></a>
                                <?php } ?>
                            </div>
                            <!-- End Of Button Delete -->
                        </td>
                    </tr>

                    <!-- Modal Edit -->
                    <div uk-modal class="uk-flex-top" id="editdata<?= $coa['id'] ?>">
                        <div class="uk-modal-dialog uk-margin-auto-vertical">
                            <div class="uk-modal-content">
                                <div class="uk-modal-header">
                                    <div class="uk-child-width-1-2" uk-grid>
                                        <div><h5 class="uk-modal-title">Edit Akun</h5></div>
                                        <div class="uk-text-right">
                                            <button class="uk-modal-close uk-icon-button-delete" uk-icon="icon: close" type="button"></button>
                                        </div>
                                    </div>
                                </div>

                                <div class="uk-modal-body">
                                    <form class="uk-form-stacked" role="form" action="/accountancy/akuncoa/update/<?= $coa['id'] ?>" method="post">
                                        <?= csrf_field() ?>

                                        <div class="uk-margin-bottom">
                                            <label class="uk-form-label">Nama</label>
                                            <input type="text" class="uk-input" name="name" value="<?= $coa['name'] ?>" required>
                                        </div>

                                        <div class="uk-margin-bottom">
                                            <label class="uk-form-label">Kategori</label>
                                            <select class="uk-select" name="category" id="category_edit_<?= $coa['id'] ?>" required>
                                                <?php foreach ($categories as $category) { ?>
                                                    <option value="<?= $category['id'] ?>" 
                                                        data-code="<?= $category['cat_code'] ?>"
                                                        <?= ($coa['cat_a_id'] == $category['id']) ? 'selected' : '' ?>>
                                                        <?= $category['name'] ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        <div class="uk-margin-bottom">
                                            <label class="uk-form-label">Kode Akun</label>
                                            <div uk-grid class="uk-grid-small">
                                                <div class="uk-width-expand">
                                                    <input type="text" class="uk-input" id="cat_code_edit_<?= $coa['id'] ?>" value="<?= $coa['cat_code'] ?? '' ?>" readonly>
                                                </div>
                                                <div class="uk-width-auto uk-padding-remove-left">
                                                    <input type="text" class="uk-input" name="coa_code" value="<?= $coa['kode'] ?>" required maxlength="4">
                                                </div>
                                            </div>
                                            <small class="uk-text-muted">Prefix kategori akan otomatis berubah jika kategori diganti.</small>
                                        </div>

                                        <script>
                                            $(document).on('change', '[id^="category_edit_"]', function() {
                                                const coaId = $(this).attr('id').replace('category_edit_', '');
                                                const selected = $(this).find(':selected');
                                                const newPrefix = selected.data('code');
                                                
                                                // Update tampilan prefix di samping input coa_code
                                                $(`#cat_code_edit_${coaId}`).val(newPrefix);
                                            });
                                        </script>

                                        <div class="uk-margin-bottom">
                                            <label class="uk-form-label">Deskripsi</label>
                                            <textarea class="uk-textarea" name="description" rows="4"><?= $coa['description'] ?></textarea>
                                        </div>

                                        <div class="uk-margin-bottom">
                                            <label class="uk-form-label">Arsipkan Akun</label>
                                            <label>
                                                <input type="checkbox" name="status_active" value="0" <?= ($coa['status_active'] == 0 ? 'checked' : '') ?>>
                                                <span class="uk-margin-small-left">Tandai sebagai arsip</span>
                                            </label>
                                        </div>

                                        <hr>

                                        <button type="submit" class="uk-button uk-button-primary">Simpan Perubahan</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
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