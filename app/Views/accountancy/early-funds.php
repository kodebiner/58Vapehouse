<?= $this->extend('layout') ?>
<?= $this->section('extraScript') ?>
<script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
<style>
    .input-blue {
        border: 1px solid #1e87f0 !important;
    }
</style>
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
                <h3 class="tm-h3"><?=lang('Global.accountancy').' - Saldo Awal'?></h3>
            </div>
        </div>
    </div>

    <form class="uk-form-stacked" role="form" action="accountancy/akuncoa/early-funds/create" method="post">
        <div class="uk-child-width-1-1 uk-child-width-1-2@m uk-flex-middle" uk-grid>
            <!-- Tanggal Konversi -->
            <div class="uk-margin">
                <label class="uk-form-label uk-light">Tanggal Konversi Saldo Awal:</label>
                <div class="uk-form-controls">
                    <input type="date" name="convert_date" class="uk-input uk-width-small uk-border-rounded" required>
                </div>
            </div>

            <div class="uk-child-width-1-1 uk-child-width-1-4@m uk-flex-right" uk-grid>
                <!-- Search -->
                <div>
                    <input type="text" id="searchInput" placeholder="Cari..." class="uk-input uk-width-medium">
                </div>

                <div>
                    <button class="uk-button uk-button-primary" type="submit">Simpan</button>
                </div>
            </div>
        </div>

        <table class="uk-table uk-table-justify uk-table-middle uk-table-divider uk-light">
            <thead>
                <tr>
                    <th>KODE</th>
                    <th>NAMA</th>
                    <th>KATEGORI</th>
                    <th>TIPE AKUN</th>
                    <th>SALDO DEBIT</th>
                    <th>SALDO KREDIT</th>
                </tr>
            </thead>

            <tbody id="coaTable">
                <?php foreach ($coa_list as $c){ ?>
                    <tr>
                        <td><?= $c['full_code'] ?></td>
                        <td><?= $c['name'] ?></td>
                        <td><?= $c['category_name'] ?></td>
                        <?php if ($c['cat_type'] == 0) { ?>
                           <td>Debit</td>
                            <!-- Debit -->
                            <td>
                                <input 
                                    type="number" 
                                    name="debit_value[<?= $c['id'] ?>]" 
                                    class="uk-input input-blue" 
                                    value="0" 
                                    step="0.01">
                            </td>
                            <!-- Kredit -->
                            <td>
                                <input 
                                    type="number" 
                                    name="credit_value[<?= $c['id'] ?>]" 
                                    class="uk-input" 
                                    value="0" 
                                    step="0.01">
                            </td>
                        <?php } else { ?>
                           <td>Kredit</td>
                            <!-- Debit -->
                            <td>
                                <input 
                                    type="number" 
                                    name="debit_value[<?= $c['id'] ?>]" 
                                    class="uk-input" 
                                    value="0" 
                                    step="0.01">
                            </td>
                            <!-- Kredit -->
                            <td>
                                <input 
                                    type="number" 
                                    name="credit_value[<?= $c['id'] ?>]" 
                                    class="uk-input input-blue" 
                                    value="0" 
                                    step="0.01">
                            </td>
                        <?php } ?>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </form>
</div>

<!-- Script Search Filter -->
<script>
    $("#searchInput").on("keyup", function () {
        var val = $(this).val().toLowerCase();
        $("#coaTable tr").filter(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(val) > -1)
        });
    });
</script>

<?= $this->endSection() ?>
