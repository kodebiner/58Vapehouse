<?= $this->extend('layout') ?>
<?= $this->section('extraScript') ?>
<script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
<?= $this->endSection() ?>
<?= $this->section('main') ?>
<div class="uk-width-1-1 uk-height-1-1" class="uk-inline">
    <div>
        <?= view('Views/Auth/_permission_message') ?>
        <?= view('Views/Auth/_message_block') ?>
    </div>

    <!-- Page Heading -->
    <div class="tm-card-header uk-light uk-margin-bottom">
        <div uk-grid class="uk-flex-middle uk-child-width-1-1 uk-child-width-1-2@m">
            <div>
                <h3 class="tm-h3"><?=lang('Global.accountancy').' - Asset'?></h3>
            </div>

            <!-- Button Trigger Modal Add -->
            <div class="uk-text-right@m">
                <button type="button" class="uk-button uk-button-primary uk-preserve-color" uk-toggle="target: #tambahdata">Tambah Aset</button>
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
                            <h5 class="uk-modal-title" id="tambahdata">Tambah Aset</h5>
                        </div>
                        <div class="uk-text-right">
                            <button class="uk-modal-close uk-icon-button-delete" uk-icon="icon: close;" type="button"></button>
                        </div>
                    </div>
                </div>
                <div class="uk-modal-body">
                    <form class="uk-form-stacked" role="form" action="accountancy/asset/create" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <div class="uk-margin-remove-top uk-margin-bottom">
                            <label class="uk-form-label">Tanggal Akuisisi</label>
                            <div class="uk-form-controls">
                                <input type="date" name="date" class="uk-input uk-width-small uk-border-rounded" value="<?= date('Y-m-d') ?>" required>
                            </div>
                        </div>

                        <div class="uk-margin">
                            <label class="uk-form-label" for="code_asset">Kode Aset</label>
                            <div class="uk-form-controls">
                                <input type="text" class="uk-input" id="code_asset" name="code_asset" placeholder="Kode Aset" required />
                            </div>
                        </div>

                        <div class="uk-margin">
                            <label class="uk-form-label" for="name">Nama Aset</label>
                            <div class="uk-form-controls">
                                <input type="text" class="uk-input" id="name" name="name" placeholder="Nama Aset" required />
                            </div>
                        </div>

                        <div class="uk-margin">
                            <label class="uk-form-label" for="description">Deskripsi</label>
                            <div class="uk-form-controls">
                                <textarea class="uk-textarea" id="description" name="description" placeholder="Deskripsi" rows="4"></textarea>
                            </div>
                        </div>

                        <div class="uk-margin">
                            <label class="uk-form-label" for="cat_asset_tetap">Akun Asset Tetap</label>
                            <div class="uk-form-controls">
                                <select class="uk-select" 
                                    id="cat_asset_tetap" 
                                    name="cat_asset_tetap"
                                    data-options='<?= json_encode($coahartaps) ?>'
                                    required>
                                    <?php foreach ($coahartaps as $coa1) { ?>
                                        <option value="<?= $coa1['id'] ?>" data-code="<?= $coa1['cat_a_id'] ?>">
                                            <?= $coa1['name'] ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="uk-margin">
                            <label class="uk-form-label" for="value_asset_tetap">Nilai Perolehan (IDR)</label>
                            <div class="uk-form-controls">
                                <input type="number" class="uk-input" min="0" value="0" id="value_asset_tetap" name="value_asset_tetap" placeholder="0" required />
                            </div>
                        </div>

                        <div class="uk-margin">
                            <label class="uk-form-label" for="cat_tax">Akun Pajak (Optional)</label>
                            <div class="uk-form-controls">
                                <select class="uk-select" 
                                    id="cat_tax" 
                                    name="cat_tax"
                                    data-options='<?= json_encode($taxes) ?>'>
                                    <?php foreach ($taxes as $tax) { ?>
                                        <option value="<?= $tax['id'] ?>">
                                            <?= $tax['name'] ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="uk-margin">
                            <label class="uk-form-label" for="value_tax">Nilai Pajak (IDR) (Optional)</label>
                            <div class="uk-form-controls">
                                <input type="number" class="uk-input" min="0" value="0" id="value_tax" name="value_tax" placeholder="0" />
                            </div>
                        </div>

                        <div class="uk-margin">
                            <label class="uk-form-label" for="cat_asset_credit">Akun Dikreditkan</label>
                            <div class="uk-form-controls">
                                <select class="uk-select" 
                                    id="cat_asset_credit" 
                                    name="cat_asset_credit"
                                    data-options='<?= json_encode($allcoas) ?>'
                                    required>
                                    <?php foreach ($allcoas as $coa2) { ?>
                                        <option value="<?= $coa2['id'] ?>" data-code="<?= $coa2['cat_a_id'] ?>">
                                            <?= $coa2['name'] ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="uk-margin">
                            <label class="uk-form-label" for="image_asset">Foto Asset</label>
                            <div class="uk-form-controls">
                                <input type="file" id="image_asset" name="image_asset" accept="image/*" />
                            </div>
                        </div>

                        <div class="uk-margin">
                            <h2 class="uk-margin-small-bottom">Penyusutan</h2>
                            <div class="uk-form-controls">
                                <label class="uk-form-label" for="depreciation_status"><input class="uk-checkbox" type="checkbox" name="depreciation_status" id="depreciation_status"> Aset Depresiasi</label>
                            </div>
                        </div>

                        <div id="depreciation_section" hidden>
                            <div class="uk-margin">
                                <label class="uk-form-label" for="depreciation_method">Metode Penyusutan</label>
                                <div class="uk-form-controls">
                                    <select class="uk-select" id="depreciation_method" name="depreciation_method">
                                        <option value="straight_line">Garis Lurus (Straight Line)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="uk-margin">
                                <label class="uk-form-label" for="depreciation_residu">Nilai Residu</label>
                                <div class="uk-form-controls">
                                    <input type="number" class="uk-input" min="0" value="0" id="depreciation_residu" name="depreciation_residu" placeholder="0" />
                                </div>
                            </div>

                            <div class="uk-margin">
                                <label class="uk-form-label" for="depreciation_benefit_era">Masa Manfaat <div id="benefit_era"></div></label>
                                <div class="uk-form-controls uk-grid-small" uk-grid>
                                    <div class="uk-width-expand">
                                        <input type="number" class="uk-input" min="1" value="4" id="depreciation_benefit_era" name="depreciation_benefit_era" placeholder="1" />
                                    </div>
                                    
                                    <div class="uk-width-auto uk-padding-remove-left">
                                        <input type="text" class="uk-input" value="tahun" readonly />
                                    </div>
                                </div>
                            </div>

                            <div class="uk-margin">
                                <label class="uk-form-label" for="depreciation_cat_penyusutan">Akun Penyusutan</label>
                                <div class="uk-form-controls">
                                    <select class="uk-select" 
                                        id="depreciation_cat_penyusutan" 
                                        name="depreciation_cat_penyusutan"
                                        data-options='<?= json_encode($weights) ?>'>
                                        <?php foreach ($weights as $coa3) { ?>
                                            <option value="<?= $coa3['id'] ?>" data-code="<?= $coa3['cat_a_id'] ?>">
                                                <?= $coa3['name'] ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="uk-margin">
                                <label class="uk-form-label" for="depreciation_sum_cat_penyusutan">Akumulasi Akun Penyusutan</label>
                                <div class="uk-form-controls">
                                    <select class="uk-select" 
                                        id="depreciation_sum_cat_penyusutan" 
                                        name="depreciation_sum_cat_penyusutan"
                                        data-options='<?= json_encode($coadepreciation) ?>'>
                                        <?php foreach ($coadepreciation as $coa4) { ?>
                                            <option value="<?= $coa4['id'] ?>" data-code="<?= $coa4['cat_a_id'] ?>">
                                                <?= $coa4['name'] ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <script>
                            document.addEventListener("DOMContentLoaded", function () {
                                const dateInput = document.querySelector('input[name="date"]');
                                const benefitInput = document.getElementById("depreciation_benefit_era");
                                const display = document.getElementById("benefit_era");

                                function updateBenefitEra() {
                                    const masa = parseInt(benefitInput.value || 0);
                                    const dateVal = dateInput.value;

                                    if (!dateVal || isNaN(masa)) {
                                        display.innerHTML = "";
                                        return;
                                    }

                                    // Ambil bulan dan tahun dari tanggal akuisisi
                                    const acquired = new Date(dateVal + "T00:00:00");
                                    const endDate = new Date(acquired);

                                    // Tambah tahun sesuai masa manfaat
                                    endDate.setFullYear(endDate.getFullYear() + masa);

                                    // Nama bulan dalam bahasa Indonesia
                                    const bulan = [
                                        "Januari", "Februari", "Maret", "April", "Mei", "Juni",
                                        "Juli", "Agustus", "September", "Oktober", "November", "Desember"
                                    ];

                                    const monthName = bulan[endDate.getMonth()];
                                    const year = endDate.getFullYear();

                                    display.innerHTML = `<strong>sampai bulan ${monthName} tahun ${year}</strong>`;
                                }

                                // Event listener
                                dateInput.addEventListener("change", updateBenefitEra);
                                benefitInput.addEventListener("input", updateBenefitEra);

                                // Trigger awal jika data sudah ada
                                updateBenefitEra();
                            });
                            
                            document.addEventListener('DOMContentLoaded', function () {
                                const checkbox = document.getElementById('depreciation_status');
                                const section  = document.getElementById('depreciation_section');

                                function toggleDepreciation() {
                                    section.hidden = !checkbox.checked;
                                }

                                checkbox.addEventListener('change', toggleDepreciation);

                                // Trigger awal
                                toggleDepreciation();
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

    <!-- Table of Content -->
    <div class="uk-overflow-auto uk-margin">
        <table class="uk-table uk-table-justify uk-table-middle uk-table-divider uk-light" id="example" style="width:100%">
            <thead>
                <tr>
                    <th class="uk-text-center uk-width-small">No</th>
                    <th class="uk-width-large">Kode</th>
                    <th class="uk-width-medium">Nama</th>
                    <th class="uk-width-medium">Akun Aset Tetap</th>
                    <th class="uk-width-medium">Deskripsi</th>
                    <th class="uk-width-medium">Tanggal Akuisisi</th>
                    <th class="uk-width-medium">Biaya Akuisisi</th>
                    <th class="uk-width-medium">Nilai Buku</th>
                    <th class="uk-width-medium">Akun Dikreditkan</th>
                    <th class="uk-width-medium">Aset Depresiasi</th>
                    <th class="uk-width-medium">Metode</th>
                    <th class="uk-width-medium">Masa Manfaat</th>
                    <th class="uk-width-medium">Akun Penyusutan</th>
                    <th class="uk-width-medium">Akumulasi Akun Penyusutan</th>
                    <th class="uk-width-medium">Akumulasi Penyusutan</th>
                    <th class="uk-width-medium">Bulan Akhir Akumulasi Penyusutan</th>
                    <th class="uk-width-medium">Status Depresiasi</th>
                    <th class="uk-width-medium">Stop Depresiasi</th>
                    <th class="uk-text-center uk-width-large"><?=lang('Global.action')?></th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1 ; ?>
                <?php foreach ($assets as $asset) { ?>
                    <tr>
                        <td class="uk-text-center"><?= $i++; ?></td>
                        <td class=""><?= $asset['code_asset']; ?></td>
                        <td class=""><?= $asset['name']; ?></td>
                        <td class=""><?= $asset['cat_asset_tetap']; ?></td>
                        <td class=""><?= $asset['description']; ?></td>
                        <td class=""><?= $asset['date']; ?></td>
                        <td class=""><?= $asset['value_asset_tetap']; ?></td>
                        <td class=""><?= (Int)$asset['value_asset_tetap'] - (Int)$asset['value_tax']; ?></td>
                        <td class=""><?= $asset['cat_asset_credit']; ?></td>
                        <td class=""><?= $asset['depreciation_status']; ?></td>
                        <td class=""><?= $asset['depreciation_method']; ?></td>
                        <td class=""><?= $asset['depreciation_benefit_era']; ?></td>
                        <td class=""><?= $asset['depreciation_cat_penyusutan']; ?></td>
                        <td class=""><?= $asset['depreciation_cat_penyusutan']; ?></td>
                        <td class=""><?= $asset['depreciation_sum_cat_penyusutan']; ?></td>
                        <td class=""><?= $asset['date']; ?></td>
                        <td class=""><?= $asset['depreciation_status']; ?></td>
                        <td>
                            <a href="#">Stop Depresiasi</a>
                        </td>
                        <td class="uk-child-width-auto uk-flex-center uk-grid-row-small uk-grid-column-small" uk-grid>
                            <!-- Button Trigger Modal Detail -->
                            <div>
                                <a class="uk-icon-button uk-button-default" uk-icon="eye" uk-toggle="target: #detaildata<?= $asset['id'] ?>"></a>
                            </div>
                            <!-- Button Trigger Modal Edit -->
                            <div>
                                <a class="uk-icon-button" uk-icon="pencil" uk-toggle="target: #editdata<?= $asset['id'] ?>"></a>
                            </div>
                            <!-- Button Delete -->
                            <div>
                                <a uk-icon="trash" class="uk-icon-button-delete" href="asset/delete/<?= $asset['id'] ?>" onclick="return confirm('<?=lang('Global.deleteConfirm')?>')"></a>
                            </div>
                        </td>
                    </tr>

                    <!-- Modal Edit -->
                    <div uk-modal class="uk-flex-top" id="editdata<?= $asset['id'] ?>">
                        <div class="uk-modal-dialog uk-margin-auto-vertical">
                            <div class="uk-modal-content">
                                <div class="uk-modal-header">
                                    <div class="uk-child-width-1-2" uk-grid>
                                        <div>
                                            <h5 class="uk-modal-title" id="editdata<?= $asset['id'] ?>">Edit Aset</h5>
                                        </div>
                                        <div class="uk-text-right">
                                            <button class="uk-modal-close uk-icon-button-delete" uk-icon="icon: close;" type="button"></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="uk-modal-body">
                                    <form class="uk-form-stacked" role="form" action="accountancy/asset/update/<?= $asset['id'] ?>" method="post" enctype="multipart/form-data">
                                        <?= csrf_field() ?>
                                        <div class="uk-margin-remove-top uk-margin-bottom">
                                            <label class="uk-form-label">Tanggal Akuisisi</label>
                                            <div class="uk-form-controls">
                                                <input type="date" id="date-<?= $asset['id'] ?>" name="date" class="uk-input uk-width-small uk-border-rounded" value="<?= $asset['date']; ?>">
                                            </div>
                                        </div>

                                        <div class="uk-margin">
                                            <label class="uk-form-label" for="code_asset">Kode Aset</label>
                                            <div class="uk-form-controls">
                                                <input type="text" class="uk-input" id="code_asset" name="code_asset" placeholder="Kode Aset" value="<?= $asset['code_asset'] ?>" disabled/>
                                            </div>
                                        </div>

                                        <div class="uk-margin">
                                            <label class="uk-form-label" for="name">Nama Aset</label>
                                            <div class="uk-form-controls">
                                                <input type="text" class="uk-input" id="name" name="name" placeholder="Nama Aset" value="<?= $asset['name'] ?>" />
                                            </div>
                                        </div>

                                        <div class="uk-margin">
                                            <label class="uk-form-label" for="description">Deskripsi</label>
                                            <div class="uk-form-controls">
                                                <textarea class="uk-textarea" id="description" name="description" rows="4"><?= $asset['description'] ?></textarea>
                                            </div>
                                        </div>

                                        <div class="uk-margin">
                                            <label class="uk-form-label" for="cat_asset_tetap">Akun Asset Tetap</label>
                                            <div class="uk-form-controls">
                                                <select class="uk-select" name="cat_asset_tetap" id="cat_asset_tetap">
                                                    <?php foreach ($coahartaps as $coa1): ?>
                                                        <option value="<?= $coa1['id'] ?>"
                                                            <?= $asset['cat_asset_tetap'] == $coa1['id'] ? 'selected' : '' ?>>
                                                            <?= $coa1['name'] ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="uk-margin">
                                            <label class="uk-form-label" for="value_asset_tetap">Nilai Perolehan (IDR)</label>
                                            <div class="uk-form-controls">
                                                <input type="number" class="uk-input" min="0" id="value_asset_tetap"
                                                    name="value_asset_tetap"
                                                    value="<?= $asset['value_asset_tetap'] ?>">
                                            </div>
                                        </div>

                                        <div class="uk-margin">
                                            <label class="uk-form-label" for="cat_tax">Akun Pajak (Optional)</label>
                                            <div class="uk-form-controls">
                                                <select class="uk-select" name="cat_tax" id="cat_tax">
                                                    <?php foreach ($taxes as $tax): ?>
                                                        <option value="<?= $tax['id'] ?>"
                                                            <?= $asset['cat_tax'] == $tax['id'] ? 'selected' : '' ?>>
                                                            <?= $tax['name'] ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="uk-margin">
                                            <label class="uk-form-label" for="value_tax">Nilai Pajak (IDR) (Optional)</label>
                                            <div class="uk-form-controls">
                                                <input type="number" class="uk-input" min="0" id="value_tax"
                                                    name="value_tax"
                                                    value="<?= $asset['value_tax'] ?>">
                                            </div>
                                        </div>

                                        <div class="uk-margin">
                                            <label class="uk-form-label" for="cat_asset_credit">Akun Dikreditkan</label>
                                            <div class="uk-form-controls">
                                                <select class="uk-select" name="cat_asset_credit" id="cat_asset_credit">
                                                    <?php foreach ($allcoas as $coa2): ?>
                                                        <option value="<?= $coa2['id'] ?>"
                                                            <?= $asset['cat_asset_credit'] == $coa2['id'] ? 'selected' : '' ?>>
                                                            <?= $coa2['name'] ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="uk-margin">
                                            <label class="uk-form-label" for="image_asset">Foto Asset</label>
                                            <div class="uk-form-controls">
                                                <input type="file" id="image_asset" name="image_asset" accept="image/*" >
                                            </div>
                                            <?php if (!empty($asset['image'])): ?>
                                                <img src="/uploads/assets/<?= $asset['image'] ?>" class="uk-margin-small-top uk-border-rounded" width="120">
                                            <?php endif; ?>
                                        </div>

                                        <div class="uk-margin">
                                            <h2 class="uk-margin-small-bottom">Penyusutan</h2>
                                            <label>
                                                <input class="uk-checkbox" type="checkbox" name="depreciation_status" id="depreciation_status-<?= $asset['id'] ?>"
                                                    <?= $asset['depreciation_status'] ? 'checked' : '' ?>>
                                                Aset Depresiasi
                                            </label>
                                        </div>

                                        <div id="depreciation_section-<?= $asset['id'] ?>" <?= $asset['depreciation_status'] ? '' : 'hidden' ?>>
                                            <div class="uk-margin">
                                                <label class="uk-form-label">Metode Penyusutan</label>
                                                <select class="uk-select" name="depreciation_method">
                                                    <option value="straight_line"
                                                        <?= $asset['depreciation_method'] === 'straight_line' ? 'selected' : '' ?>>
                                                        Garis Lurus (Straight Line)
                                                    </option>
                                                </select>
                                            </div>

                                            <div class="uk-margin">
                                                <label class="uk-form-label">Nilai Residu</label>
                                                <input type="number" class="uk-input" name="depreciation_residu" min="0"
                                                    value="<?= $asset['depreciation_residu'] ?>">
                                            </div>

                                            <div class="uk-margin">
                                                <label class="uk-form-label">Masa Manfaat <div id="benefit_era-<?= $asset['id'] ?>"></div></label>
                                                <div class="uk-grid-small" uk-grid>
                                                    <div class="uk-width-expand">
                                                        <input type="number" class="uk-input" id="depreciation_benefit_era-<?= $asset['id'] ?>" name="depreciation_benefit_era" min="1"
                                                            value="<?= $asset['depreciation_benefit_era'] ?>">
                                                    </div>
                                                    <div class="uk-width-auto">
                                                        <input type="text" class="uk-input" value="tahun" readonly>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="uk-margin">
                                                <label class="uk-form-label">Akun Penyusutan</label>
                                                <select class="uk-select" name="depreciation_cat_penyusutan">
                                                    <?php foreach ($weights as $coa3): ?>
                                                        <option value="<?= $coa3['id'] ?>"
                                                            <?= $asset['depreciation_cat_penyusutan'] == $coa3['id'] ? 'selected' : '' ?>>
                                                            <?= $coa3['name'] ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <div class="uk-margin">
                                                <label class="uk-form-label">Akumulasi Akun Penyusutan</label>
                                                <select class="uk-select" name="depreciation_sum_cat_penyusutan">
                                                    <?php foreach ($coadepreciation as $coa4): ?>
                                                        <option value="<?= $coa4['id'] ?>"
                                                            <?= $asset['depreciation_sum_cat_penyusutan'] == $coa4['id'] ? 'selected' : '' ?>>
                                                            <?= $coa4['name'] ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>

                                        <script>
                                            document.addEventListener("DOMContentLoaded", function () {

                                                const assetId = "<?= $asset['id'] ?>";

                                                // Elemen unik per aset
                                                const dateInput     = document.getElementById("date-" + assetId);
                                                const benefitInput  = document.getElementById("depreciation_benefit_era-" + assetId);
                                                const display       = document.getElementById("benefit_era-" + assetId);

                                                function updateBenefitEra() {
                                                    const masa = parseInt(benefitInput.value || 0);
                                                    const dateVal = dateInput.value;

                                                    if (!dateVal || isNaN(masa)) {
                                                        display.innerHTML = "";
                                                        return;
                                                    }

                                                    const acquired = new Date(dateVal + "T00:00:00");
                                                    const endDate  = new Date(acquired);
                                                    endDate.setFullYear(endDate.getFullYear() + masa);

                                                    const bulan = [
                                                        "Januari", "Februari", "Maret", "April", "Mei", "Juni",
                                                        "Juli", "Agustus", "September", "Oktober", "November", "Desember"
                                                    ];

                                                    display.innerHTML =
                                                        `<strong>sampai bulan ${bulan[endDate.getMonth()]} tahun ${endDate.getFullYear()}</strong>`;
                                                }

                                                if (dateInput && benefitInput) {
                                                    dateInput.addEventListener("change", updateBenefitEra);
                                                    benefitInput.addEventListener("input", updateBenefitEra);
                                                    updateBenefitEra(); // Load awal
                                                }

                                                // Toggle Penyusutan
                                                const checkbox = document.getElementById("depreciation_status-" + assetId);
                                                const section  = document.getElementById("depreciation_section-" + assetId);

                                                function toggleDepreciation() {
                                                    section.hidden = !checkbox.checked;
                                                }

                                                checkbox.addEventListener("change", toggleDepreciation);
                                                toggleDepreciation(); // Set awal

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
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>