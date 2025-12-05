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
                            <label class="uk-form-label" for="value">Jumlah</label>
                            <div class="uk-form-controls">
                                <input type="text" class="uk-input" id="value" name="value" placeholder="Jumlah" required />
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

                                const sellWrapper = $(".tax-sell");
                                const buyWrapper  = $(".tax-buy");

                                const sellLabel  = sellWrapper.find("label[for='tax_cut_sell']");
                                const buyLabel   = buyWrapper.find("label[for='tax_cut_buy']");

                                const sellSelect = $("#tax_cut_sell");
                                const buySelect  = $("#tax_cut_buy");

                                const coas1 = JSON.parse(sellSelect.attr("data-options"));
                                const coas2 = JSON.parse(buySelect.attr("data-options"));

                                function fillOptions(select, list) {
                                    select.empty();
                                    list.forEach(item => {
                                        select.append(
                                            `<option value="${item.id}" data-code="${item.cat_a_id}">${item.name}</option>`
                                        );
                                    });
                                }

                                function applyMode() {
                                    const isCut = $("#tax_cut_status").is(":checked");

                                    if (!isCut) {
                                        // Urutan: SELL → BUY
                                        $("#tax-cut-wrapper").append(sellWrapper);
                                        $("#tax-cut-wrapper").append(buyWrapper);

                                        sellLabel.text("Akun Pajak Saat Penjualan");
                                        buyLabel.text("Akun Pajak Saat Pembelian");

                                        fillOptions(sellSelect, coas1);
                                        fillOptions(buySelect, coas2);

                                    } else {
                                        // === MODE PEMOTONGAN ===
                                        // Urutan: BUY → SELL
                                        $("#tax-cut-wrapper").append(buyWrapper);
                                        $("#tax-cut-wrapper").append(sellWrapper);

                                        // Label ikut berubah posisi
                                        buyLabel.text("Akun Pajak Saat Pembelian");
                                        sellLabel.text("Akun Pajak Saat Penjualan");

                                        // Swap isi sesuai posisi barunya:
                                        fillOptions(buySelect, coas1);
                                        fillOptions(sellSelect, coas2);
                                    }
                                }

                                $("#tax_cut_status").on("change", function () {
                                    applyMode();
                                });

                                // initial load
                                applyMode();
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
                    <th class="uk-text-center uk-width-small">No</th>
                    <th class="uk-width-large">Nama Pajak</th>
                    <th class="uk-width-medium">Kode Pajak</th>
                    <th class="uk-width-medium">Rate (%)</th>
                    <th class="uk-text-center uk-width-large">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; ?>
                <?php foreach ($taxes as $tax) { ?>
                    <tr>
                        <td class="uk-text-center"><?= $i++; ?></td>
                        <td><?= $tax->name; ?></td>
                        <td><?= $tax->code; ?></td>
                        <td><?= $tax->rate; ?>%</td>
                        <td class="uk-child-width-auto uk-flex-center uk-grid-row-small uk-grid-column-small" uk-grid>

                            <!-- Edit Button -->
                            <div>
                                <a class="uk-icon-button" uk-icon="pencil" 
                                uk-toggle="target: #editTax<?= $tax->id ?>"></a>
                            </div>

                            <!-- Delete Button -->
                            <div>
                                <a uk-icon="trash" 
                                class="uk-icon-button-delete" 
                                href="tax/delete/<?= $tax->id ?>" 
                                onclick="return confirm('Yakin ingin menghapus pajak ini?')"></a>
                            </div>

                        </td>
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