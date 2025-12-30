<?= $this->extend('layout') ?>
<?= $this->section('extraScript') ?>
<script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
<script src="js/cdn.datatables.net_1.13.4_js_jquery.dataTables.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<style>
    /* Memastikan dropdown TomSelect muncul di depan modal UIkit */
    .ts-dropdown {
        z-index: 2000 !important;
    }
    /* Menyesuaikan input agar terlihat seperti uk-input */
    .ts-control {
        border-radius: 4px !important;
        padding: 8px 10px !important;
    }
</style>
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
                                <select class="uk-select select-search" 
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
                            <label class="uk-form-label">Nilai Perolehan (IDR)</label>
                            <div class="uk-form-controls">
                                <input type="text" class="uk-input money-idr" data-target="value_asset_tetap" placeholder="Rp 0">
                                <input type="hidden" id="value_asset_tetap" name="value_asset_tetap" value="0">
                            </div>
                        </div>

                        <div class="uk-margin">
                            <label class="uk-form-label" for="cat_tax">Akun Pajak (Optional)</label>
                            <div class="uk-form-controls">
                                <select class="uk-select select-search" 
                                    id="cat_tax" 
                                    name="cat_tax"
                                    data-options='<?= json_encode($taxes) ?>'>
                                    <?php foreach ($taxes as $tax) { ?>
                                        <option value="0">Pilih Akun Pajak ...</option>
                                        <option value="<?= $tax['id'] ?>">
                                            <?= $tax['name'] ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="uk-margin">
                            <label class="uk-form-label">Nilai Pajak (IDR) (Optional)</label>
                            <div class="uk-form-controls">
                                <input type="text" class="uk-input money-idr" data-target="value_tax" placeholder="Rp 0">
                                <input type="hidden" id="value_tax" name="value_tax" value="0">
                            </div>
                        </div>

                        <div class="uk-margin">
                            <label class="uk-form-label" for="cat_asset_credit">Akun Dikreditkan</label>
                            <div class="uk-form-controls">
                                <select class="uk-select select-search" 
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
                                <label class="uk-form-label">Nilai Residu (IDR)</label>
                                <div class="uk-form-controls">
                                    <input type="text" class="uk-input money-idr" data-target="depreciation_residu" placeholder="Rp 0">
                                    <input type="hidden" id="depreciation_residu" name="depreciation_residu" value="0">
                                </div>
                            </div>

                            <div class="uk-margin">
                                <label class="uk-form-label" for="depreciation_benefit_era">Masa Manfaat <div id="benefit_era"></div></label>
                                <div class="uk-form-controls uk-grid-small" uk-grid>
                                    <div class="uk-width-expand">
                                        <input type="number" class="uk-input" min="1" value="48" id="depreciation_benefit_era" name="depreciation_benefit_era" placeholder="1" />
                                    </div>

                                    <div class="uk-width-auto uk-padding-remove-left">
                                        <input type="text" class="uk-input" value="bulan" readonly />
                                    </div>
                                </div>
                            </div>

                            <div class="uk-margin">
                                <label class="uk-form-label" for="depreciation_cat_penyusutan">Akun Penyusutan</label>
                                <div class="uk-form-controls">
                                    <select class="uk-select select-search" 
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
                                    <select class="uk-select select-search" 
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
                                document.querySelectorAll('.select-search').forEach((el) => {
                                    // Pastikan tidak ada spasi antara Tom dan Select
                                    new TomSelect(el, { 
                                        create: false,
                                        sortField: {
                                            field: "text",
                                            direction: "asc"
                                        },
                                        placeholder: "Cari data...",
                                        allowEmptyOption: true,
                                        // Opsional: tambahkan ini agar dropdown tidak terpotong modal
                                        dropdownParent: 'body' 
                                    });
                                });
                            });
                            
                            document.addEventListener("DOMContentLoaded", function () {
                                const dateInput     = document.querySelector('input[name="date"]');
                                const benefitInput  = document.getElementById("depreciation_benefit_era");
                                const display       = document.getElementById("benefit_era");

                                const bulan = [
                                    "Januari", "Februari", "Maret", "April", "Mei", "Juni",
                                    "Juli", "Agustus", "September", "Oktober", "November", "Desember"
                                ];

                                function updateBenefitEra() {
                                    const masaBulan = parseInt(benefitInput.value || 0);
                                    const dateVal   = dateInput.value;

                                    if (!dateVal || isNaN(masaBulan) || masaBulan <= 0) {
                                        display.innerHTML = "";
                                        return;
                                    }

                                    // Tanggal akuisisi
                                    const acquired = new Date(dateVal + "T00:00:00");

                                    // Tambah bulan
                                    const endDate = new Date(acquired);
                                    endDate.setMonth(endDate.getMonth() + masaBulan);

                                    const monthName = bulan[endDate.getMonth()];
                                    const year      = endDate.getFullYear();

                                    display.innerHTML = `<strong>sampai bulan ${monthName} tahun ${year}</strong>`;
                                }

                                dateInput.addEventListener("change", updateBenefitEra);
                                benefitInput.addEventListener("input", updateBenefitEra);

                                // initial trigger
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

                            document.addEventListener("DOMContentLoaded", function () {
                                function formatRupiah(number) {
                                    return new Intl.NumberFormat('id-ID').format(number);
                                }
                                document.querySelectorAll('.money-idr').forEach(function (input) {
                                    const hiddenInput = document.getElementById(input.dataset.target);
                                    input.value = '';
                                    input.addEventListener('input', function () {
                                        let raw = this.value.replace(/\D/g, '');
                                        if (raw === '') {
                                            hiddenInput.value = 0;
                                            this.value = '';
                                            return;
                                        }
                                        let number = parseInt(raw, 10);
                                        hiddenInput.value = number;
                                        this.value = 'Rp ' + formatRupiah(number);
                                    });
                                    input.addEventListener('focus', function () {
                                        if (hiddenInput.value > 0) {
                                            this.value = hiddenInput.value;
                                        }
                                    });
                                    input.addEventListener('blur', function () {
                                        if (hiddenInput.value > 0) {
                                            this.value = 'Rp ' + formatRupiah(hiddenInput.value);
                                        } else {
                                            this.value = '';
                                        }
                                    });
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

    <!-- Table of Content -->
    <div class="uk-overflow-auto uk-margin">
        <table class="uk-table uk-table-justify uk-table-middle uk-table-divider uk-light" id="example" style="min-width:5000px">
            <thead>
                <tr>
                    <th class="uk-text-center">No</th>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Akun Aset Tetap</th>
                    <th>Deskripsi</th>
                    <th>Tanggal Akuisisi</th>
                    <th>Biaya Akuisisi</th>
                    <th>Nilai Buku</th>
                    <th>Akun Dikreditkan</th>
                    <th>Aset Depresiasi</th>
                    <th>Metode</th>
                    <th>Masa Manfaat</th>
                    <th>Akun Penyusutan</th>
                    <th>Akumulasi Akun Penyusutan</th>
                    <th>Akumulasi Penyusutan</th>
                    <th>Bulan Akhir Akumulasi Penyusutan</th>
                    <th>Status Depresiasi</th>
                    <th>Stop Depresiasi</th>
                    <th class="uk-text-center"><?=lang('Global.action')?></th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1 ; ?>
                <?php
                    function formatTanggalIndo($date)
                    {
                        if (!$date) return '-';

                        $bulan = [
                            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                        ];

                        $d = date('j', strtotime($date));
                        $m = (int) date('n', strtotime($date));
                        $y = date('Y', strtotime($date));

                        return $d . ' ' . $bulan[$m] . ' ' . $y;
                    }

                    function hitungAkhirPenyusutan($date, $bulanManfaat)
                    {
                        if (!$date || !$bulanManfaat) return '-';

                        $dt = new DateTime($date);
                        $dt->modify('+' . (int)$bulanManfaat . ' months');

                        return date('F Y', strtotime($dt->format('Y-m-d')));
                    }
                ?>
                <?php foreach ($assets as $asset) { ?>
                    <tr>
                        <td class="uk-text-center"><?= $i++; ?></td>
                        <td class=""><?= $asset['code_asset']; ?></td>
                        <td class=""><?= $asset['name']; ?></td>
                        <td class=""><?= $asset['cat_asset_tetap']; ?></td>
                        <td class=""><?= $asset['description']; ?></td>
                        <td class=""><?= formatTanggalIndo($asset['date']); ?></td>
                        <td class="">Rp <?= number_format((float)$asset['value_asset_tetap'], 0, ',', '.'); ?></td>
                        <td class="">Rp <?= number_format($asset['nilai_buku'], 0, ',', '.'); ?></td>
                        <!-- biaya akuisisi, dibagi bulan berjalan misal 24 juta - (24 juta / masa manfaat) dikali sisa manfaat atau bulan ke berapa setelah akuisisi -->
                        <td class=""><?= $asset['cat_asset_credit']; ?></td>
                        <td class=""><?= $asset['depreciation_status_label']; ?></td>
                        <td class=""><?= $asset['depreciation_method']; ?></td>
                        <td class=""><?= $asset['depreciation_benefit_era']; ?> bulan</td>
                        <td class=""><?= $asset['depreciation_cat_penyusutan']; ?></td>
                        <td class=""><?= $asset['depreciation_sum_cat_penyusutan']; ?></td>
                        <td class="">Rp <?= number_format($asset['akumulasi_penyusutan'], 0, ',', '.'); ?></td>
                        <td class=""><?= hitungAkhirPenyusutan($asset['date'],$asset['depreciation_benefit_era']); ?></td>
                        <td class=""><?= $asset['depreciation_status_text']; ?></td>
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
                                                <select class="uk-select select-search" name="cat_asset_tetap" id="cat_asset_tetap">
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
                                                <select class="uk-select select-search" name="cat_tax" id="cat_tax">
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
                                                <select class="uk-select select-search" name="cat_asset_credit" id="cat_asset_credit">
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
                                                <select class="uk-select select-search" name="depreciation_method">
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
                                                <select class="uk-select select-search" name="depreciation_cat_penyusutan">
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
                                                <select class="uk-select select-search" name="depreciation_sum_cat_penyusutan">
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

    <!-- Modal Detail -->
     <?php foreach ($assets as $asset) : ?>
        <div class="uk-flex-top uk-modal-container" id="detaildata<?= $asset['id'] ?>" uk-modal>
            <div class="uk-modal-dialog uk-modal-body uk-width-2xlarge">

                <button class="uk-modal-close-default" type="button" uk-close></button>

                <h3 class="uk-modal-title">Detail Asset</h3>

                <!-- ================= INFO ASSET ================= -->
                <div class="uk-grid-small uk-child-width-1-2@m uk-child-width-1-1" uk-grid>
                    <div>
                        <div class="uk-card uk-card-default uk-card-small uk-card-body">
                            <h5 class="uk-margin-remove">Informasi Asset</h5>
                            <hr class="uk-margin-small">

                            <div class="uk-grid-small" uk-grid>
                                <div class="uk-width-1-3">Kode</div>
                                <div class="uk-width-2-3"><?= $asset['code_asset'] ?></div>

                                <div class="uk-width-1-3">Nama</div>
                                <div class="uk-width-2-3"><?= $asset['name'] ?></div>

                                <div class="uk-width-1-3">Tanggal</div>
                                <div class="uk-width-2-3"><?= date('d F Y', strtotime($asset['date'])) ?></div>

                                <div class="uk-width-1-3">Depresiasi</div>
                                <div class="uk-width-2-3">
                                    <span class="uk-label <?= $asset['depreciation_status'] === 1 ? 'uk-label-success' : 'uk-label-default' ?>">
                                        <?= $asset['depreciation_status_label'] ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="uk-card uk-card-default uk-card-small uk-card-body">
                            <h5 class="uk-margin-remove">Nilai Asset</h5>
                            <hr class="uk-margin-small">

                            <div class="uk-grid-small" uk-grid>
                                <div class="uk-width-1-2">Biaya Perolehan</div>
                                <div class="uk-width-1-2 uk-text-right">
                                    Rp <?= number_format((float)$asset['value_asset_tetap'], 0, ',', '.') ?>
                                </div>

                                <div class="uk-width-1-2">Nilai Buku</div>
                                <div class="uk-width-1-2 uk-text-right uk-text-bold">
                                    Rp <?= number_format((float)$asset['nilai_buku'], 0, ',', '.') ?>
                                </div>

                                <div class="uk-width-1-2">Akumulasi</div>
                                <div class="uk-width-1-2 uk-text-right">
                                    Rp <?= number_format((float)$asset['akumulasi_penyusutan'], 0, ',', '.') ?>
                                </div>

                                <div class="uk-width-1-2">Masa Manfaat</div>
                                <div class="uk-width-1-2 uk-text-right">
                                    <?= $asset['depreciation_benefit_era'] ?> bulan
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <!-- ================= COA ================= -->
                <h4>Informasi Akun</h4>

                <div class="uk-grid-small uk-child-width-1-2@m uk-child-width-1-1" uk-grid>
                    <div class="uk-card uk-card-default uk-card-small uk-card-body">
                        <strong>Asset Tetap</strong>
                        <div class="uk-text-muted"><?= $asset['cat_asset_tetap'] ?></div>
                    </div>

                    <div class="uk-card uk-card-default uk-card-small uk-card-body">
                        <strong>Akun Kredit</strong>
                        <div class="uk-text-muted"><?= $asset['cat_asset_credit'] ?></div>
                    </div>

                    <div class="uk-card uk-card-default uk-card-small uk-card-body">
                        <strong>Penyusutan</strong>
                        <div class="uk-text-muted"><?= $asset['depreciation_cat_penyusutan'] ?></div>
                    </div>

                    <div class="uk-card uk-card-default uk-card-small uk-card-body">
                        <strong>Akumulasi Penyusutan</strong>
                        <div class="uk-text-muted"><?= $asset['depreciation_sum_cat_penyusutan'] ?></div>
                    </div>
                </div>

                <hr>

                <!-- ================= JURNAL ================= -->
                <h4>Laporan Jurnal</h4>
                <table class="uk-table uk-table-justify uk-table-middle uk-table-divider" style="background-color: transparent;">
                    <thead>
                        <tr>
                            <th style="color: #000 !important;">Tanggal</th>
                            <th style="color: #000 !important;">Transaksi</th>
                            <th style="color: #000 !important;">Kode</th>
                            <th style="color: #000 !important;">Akun</th>
                            <th style="color: #000 !important;">Debit</th>
                            <th style="color: #000 !important;">Kredit</th>
                            <th style="color: #000 !important;">Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($asset['journals'])) : ?>
                            <?php foreach ($asset['journals'] as $jurnal) : ?>
                                <tr>
                                    <td rowspan="2"><?= date('d M Y', strtotime($jurnal['tanggal'])) ?></td>
                                    <td rowspan="2"><?= $jurnal['transaksi'] ?></td>
                                    <td rowspan="2"><?= $jurnal['kode'] ?></td>
                                    <td><?= $jurnal['akun_debit'] ?></td>
                                    <td>Rp <?= number_format($jurnal['nilai'], 0, ',', '.') ?></td>
                                    <td>Rp 0</td>
                                    <td rowspan="2"><?= $jurnal['catatan'] ?></td>
                                </tr>
                                <tr>
                                    <td style="padding-left: 12px;"><?= $jurnal['akun_kredit'] ?></td>
                                    <td>Rp 0</td>
                                    <td>Rp <?= number_format($jurnal['nilai'], 0, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="7" style="text-align: center;">Data tidak ditemukan</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <div class="uk-text-right uk-margin-top">
                    <button class="uk-button uk-button-default uk-modal-close">Tutup</button>
                </div>

            </div>
        </div>
    <?php endforeach; ?>

    <!-- Search Engine Script -->
    <script>
        $(document).ready(function () {
            $('#example').DataTable();
        });
    </script>
</div>
<?= $this->endSection() ?>