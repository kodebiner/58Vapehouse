<?= $this->extend('layout') ?>
<?= $this->section('extraScript') ?>
<script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
<script src="js/cdn.datatables.net_1.13.4_js_jquery.dataTables.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
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
                <h3 class="tm-h3"><?=lang('Global.accountancy').' - Pajak'?></h3>
            </div>

            <!-- Button Trigger Modal Add -->
            <div class="uk-text-right">
                <button type="button" class="uk-button uk-button-primary uk-preserve-color" uk-toggle="target: #tambahdata">Tambah Pajak</button>
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
                            <h5 class="uk-modal-title" id="tambahdata" >Tambah Data</h5>
                        </div>
                        <div class="uk-text-right">
                            <button class="uk-modal-close uk-icon-button-delete" uk-icon="icon: close;" type="button"></button>
                        </div>
                    </div>
                </div>
                <div class="uk-modal-body">
                    <form class="uk-form-stacked" role="form" action="accountancy/tax/create" method="post">
                        <?= csrf_field() ?>
                        <div class="uk-margin-bottom">
                            <label class="uk-form-label" for="name">Nama</label>
                            <div class="uk-form-controls">
                                <input type="text" class="uk-input" id="name" name="name" placeholder="Nama" required />
                            </div>
                        </div>

                        <div class="uk-margin-bottom">
                            <label class="uk-form-label" for="value">Persentase Pajak (%)</label>
                            <div class="uk-form-controls">
                                <div class="uk-inline uk-width-1-1">
                                    <span class="uk-form-icon uk-form-icon-flip" style="font-style: normal;">%</span>
                                    <input 
                                        type="number" 
                                        step="0.01" 
                                        min="0" 
                                        max="100" 
                                        class="uk-input" 
                                        id="value" 
                                        name="value" 
                                        placeholder="0.00" 
                                        required 
                                    />
                                </div>
                                <p class="uk-text-meta uk-margin-remove-top">
                                    Gunakan titik (.) untuk desimal. Contoh: 0.5 atau 11
                                </p>
                            </div>
                        </div>

                        <div class="uk-margin-bottom">
                            <label class="uk-form-label" for="tax_cut_status">Pemotongan</label>

                            <div class="uk-flex uk-flex-middle uk-margin-small-top">
                                <input type="hidden" id="tax_cut_status_val" name="tax_cut_status" value="0">

                                <label class="switch">
                                    <input id="tax_cut_status" type="checkbox">
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>

                        <div id="tax-cut-wrapper">
                            <div class="uk-margin-bottom tax-sell">
                                <label class="uk-form-label" for="tax_cut_sell">Akun Pajak Saat Penjualan</label>
                                <div class="uk-form-controls">
                                    <select class="uk-select" 
                                        id="tax_cut_sell" 
                                        name="tax_cut_sell"
                                        data-options='<?= json_encode($coas1) ?>'
                                        required>
                                        <?php foreach ($coas1 as $coa1) { ?>
                                            <option value="<?= $coa1['id'] ?>" data-code="<?= $coa1['cat_a_id'] ?>">
                                                <?= $coa1['name'] ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="uk-margin-bottom tax-buy">
                                <label class="uk-form-label" for="tax_cut_buy">Akun Pajak Saat Pembelian</label>
                                <div class="uk-form-controls">
                                    <select class="uk-select" 
                                        id="tax_cut_buy" 
                                        name="tax_cut_buy"
                                        data-options='<?= json_encode($coas2) ?>'
                                        required>
                                        <?php foreach ($coas2 as $coa2) { ?>
                                            <option value="<?= $coa2['id'] ?>" data-code="<?= $coa2['cat_a_id'] ?>">
                                                <?= $coa2['name'] ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <script>
                            $(document).ready(function () {
                                /* ======================
                                * TAX CUT STATUS SYNC
                                * ====================== */
                                function syncTaxCutStatus() {
                                    $("#tax_cut_status_val").val(
                                        $("#tax_cut_status").is(":checked") ? 1 : 0
                                    );
                                }

                                $("#tax_cut_status").on("change", syncTaxCutStatus);
                                syncTaxCutStatus();

                                /* ======================
                                * TOM SELECT INIT
                                * ====================== */
                                const sellSelectEl = document.getElementById('tax_cut_sell');
                                const buySelectEl  = document.getElementById('tax_cut_buy');

                                const sellTom = new TomSelect(sellSelectEl, {
                                    placeholder: 'Pilih akun pajak...',
                                    allowEmptyOption: true,
                                    sortField: { field: "text", direction: "asc" },
                                    searchField: ['text'],
                                });

                                const buyTom = new TomSelect(buySelectEl, {
                                    placeholder: 'Pilih akun pajak...',
                                    allowEmptyOption: true,
                                    sortField: { field: "text", direction: "asc" },
                                    searchField: ['text'],
                                });

                                /* ======================
                                * DATA SOURCE
                                * ====================== */
                                const coas1 = JSON.parse($('#tax_cut_sell').attr('data-options'));
                                const coas2 = JSON.parse($('#tax_cut_buy').attr('data-options'));

                                function fillTomOptions(tom, list) {
                                    tom.clear(true);
                                    tom.clearOptions();

                                    list.forEach(item => {
                                        tom.addOption({
                                            value: item.id,
                                            text: item.name
                                        });
                                    });

                                    tom.refreshOptions(false);
                                }

                                /* ======================
                                * MODE HANDLER
                                * ====================== */
                                const sellWrapper = $(".tax-sell");
                                const buyWrapper  = $(".tax-buy");

                                const sellLabel = sellWrapper.find("label");
                                const buyLabel  = buyWrapper.find("label");

                                function applyMode() {
                                    const isCut = $("#tax_cut_status").is(":checked");

                                    if (!isCut) {
                                        // Normal
                                        $("#tax-cut-wrapper").append(sellWrapper, buyWrapper);

                                        sellLabel.text("Akun Pajak Saat Penjualan");
                                        buyLabel.text("Akun Pajak Saat Pembelian");

                                        fillTomOptions(sellTom, coas1);
                                        fillTomOptions(buyTom, coas2);
                                    } else {
                                        // Pemotongan
                                        $("#tax-cut-wrapper").append(buyWrapper, sellWrapper);

                                        buyLabel.text("Akun Pajak Saat Pembelian");
                                        sellLabel.text("Akun Pajak Saat Penjualan");

                                        fillTomOptions(buyTom, coas1);
                                        fillTomOptions(sellTom, coas2);
                                    }
                                }

                                $("#tax_cut_status").on("change", applyMode);
                                applyMode();

                                /* ======================
                                * VALUE INPUT FIX
                                * ====================== */
                                $("#value").on("input", function () {
                                    let val = $(this).val();

                                    // Ganti koma → titik
                                    if (val.includes(',')) {
                                        val = val.replace(',', '.');
                                        $(this).val(val);
                                    }

                                    // Izinkan hanya angka & satu titik
                                    if (!/^\d*\.?\d*$/.test(val)) {
                                        $(this).val(val.slice(0, -1));
                                        return;
                                    }

                                    // Batasi max 100 (tanpa ganggu 5. atau 0.)
                                    const num = parseFloat(val);
                                    if (!isNaN(num) && num > 100) {
                                        $(this).val("100");
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

    <!-- Table of Content -->
    <div class="uk-overflow-auto uk-margin">
        <table class="uk-table uk-table-justify uk-table-middle uk-table-divider uk-light" id="taxTable" style="width:100%">
            <thead>
                <tr>
                    <th class="uk-text-center uk-width-auto">No</th>
                    <th class="uk-width-auto">Nama Pajak</th>
                    <th class="uk-width-auto">Jumlah</th>
                    <th class="uk-width-auto">Penjualan</th>
                    <th class="uk-width-auto">Pembelian</th>
                    <th class="uk-text-center uk-width-auto">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; ?>
                <?php foreach ($taxes as $tax) { ?>
                    <tr>
                        <td class="uk-text-center"><?= $i++; ?></td>
                        <td><?= $tax['name']; ?></td>
                        <td><?= $tax['value']; ?></td>
                        <td><?= $tax['tax_cut_sell']; ?></td>
                        <td><?= $tax['tax_cut_buy']; ?></td>
                        <td class="uk-text-center"><a uk-icon="trash" class="uk-icon-button-delete" href="accountancy/tax/delete/<?= $tax['id'] ?>" onclick="return confirm('Yakin ingin menghapus pajak ini?')"></a></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- Search Engine Script -->
    <script>
        $(document).ready(function () {
            $('#taxTable').DataTable();
        });
    </script>
</div>
<?= $this->endSection() ?>