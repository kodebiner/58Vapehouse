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
                    <form class="uk-form-stacked" role="form" action="accountancy/asset/create" method="post">
                        <?= csrf_field() ?>
                        <div class="uk-margin-bottom">
                            <label class="uk-form-label uk-light">Tanggal Akuisisi</label>
                            <div class="uk-form-controls">
                                <input type="date" name="date" class="uk-input uk-width-small uk-border-rounded" required>
                            </div>
                        </div>

                        <div class="uk-margin-bottom">
                            <label class="uk-form-label" for="code_asset">Kode Aset</label>
                            <div class="uk-form-controls">
                                <input type="text" class="uk-input" id="code_asset" name="code_asset" placeholder="Kode Aset" required />
                            </div>
                        </div>

                        <div class="uk-margin-bottom">
                            <label class="uk-form-label" for="name">Nama Aset</label>
                            <div class="uk-form-controls">
                                <input type="text" class="uk-input" id="name" name="name" placeholder="Nama Aset" required />
                            </div>
                        </div>

                        <div class="uk-margin-bottom">
                            <label class="uk-form-label" for="description">Deskripsi</label>
                            <div class="uk-form-controls">
                                <textarea class="uk-textarea" id="description" name="description" placeholder="Deskripsi" rows="4"></textarea>
                            </div>
                        </div>

                        <div class="uk-margin-bottom">
                            <label class="uk-form-label" for="cat_asset_tetap">Akun Asset Tetap</label>
                            <div class="uk-form-controls">
                                <select class="uk-select" 
                                    id="cat_asset_tetap" 
                                    name="cat_asset_tetap"
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

                        <div class="uk-margin-bottom">
                            <label class="uk-form-label" for="value_asset_tetap">Nilai Perolehan (IDR)</label>
                            <div class="uk-form-controls">
                                <input type="number" class="uk-input" min="0" value="0" id="value_asset_tetap" name="value_asset_tetap" placeholder="0" required />
                            </div>
                        </div>

                        <div class="uk-margin-bottom">
                            <label class="uk-form-label" for="cat_asset_credit">Akun Dikreditkan</label>
                            <div class="uk-form-controls">
                                <select class="uk-select" 
                                    id="cat_asset_credit" 
                                    name="cat_asset_credit"
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
                                    <form class="uk-form-stacked" role="form" action="accountancy/asset/create" method="post">
                                        <?= csrf_field() ?>
                                        <div class="uk-margin-bottom">
                                            <label class="uk-form-label uk-light">Tanggal Akuisisi</label>
                                            <div class="uk-form-controls">
                                                <input type="date" name="date" value="<?= $asset['date'] ?>" class="uk-input uk-width-small uk-border-rounded" required>
                                            </div>
                                        </div>

                                        <div class="uk-margin-bottom">
                                            <label class="uk-form-label" for="code_asset">Kode Aset</label>
                                            <div class="uk-form-controls">
                                                <input type="text" class="uk-input" id="code_asset" name="code_asset" value="<?= $asset['code_asset'] ?>" placeholder="Kode Aset" required />
                                            </div>
                                        </div>

                                        <div class="uk-margin-bottom">
                                            <label class="uk-form-label" for="name">Nama Aset</label>
                                            <div class="uk-form-controls">
                                                <input type="text" class="uk-input" id="name" name="name" value="<?= $asset['name'] ?>" placeholder="Nama Aset" required />
                                            </div>
                                        </div>

                                        <div class="uk-margin-bottom">
                                            <label class="uk-form-label" for="description">Deskripsi</label>
                                            <div class="uk-form-controls">
                                                <textarea class="uk-textarea" id="description" name="description" value="<?= $asset['description'] ?>" placeholder="Deskripsi" rows="4"></textarea>
                                            </div>
                                        </div>

                                        <div class="uk-margin-bottom">
                                            <label class="uk-form-label" for="cat_asset_tetap">Akun Asset Tetap</label>
                                            <div class="uk-form-controls">
                                                <select class="uk-select" name="cat_asset_tetap<?= $asset['id'] ?>" id="cat_asset_tetap<?= $asset['id'] ?>" required>
                                                    <?php foreach ($coas1 as $coa1) { ?>
                                                        <option value="<?= $coa1['id'] ?>" 
                                                            data-code="<?= $coa1['cat_code'] ?>"
                                                            <?= ($asset['cat_asset_tetap'] == $coa1['id']) ? 'selected' : '' ?>>
                                                            <?= $coa1['name'] ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="uk-margin-bottom">
                                            <label class="uk-form-label" for="value_asset_tetap">Nilai Perolehan (IDR)</label>
                                            <div class="uk-form-controls">
                                                <input type="number" class="uk-input" min="0" value="<?= $asset['value_asset_tetap'] ?>" id="value_asset_tetap" name="value_asset_tetap" placeholder="0" required />
                                            </div>
                                        </div>

                                        <div class="uk-margin-bottom">
                                            <label class="uk-form-label" for="cat_asset_credit">Akun Dikreditkan</label>
                                            <div class="uk-form-controls">
                                                <select class="uk-select" name="cat_asset_credit<?= $asset['id'] ?>" id="cat_asset_credit<?= $asset['id'] ?>" required>
                                                    <?php foreach ($coas2 as $coa2) { ?>
                                                        <option value="<?= $coa2['id'] ?>" 
                                                            data-code="<?= $coa2['cat_code'] ?>"
                                                            <?= ($asset['cat_asset_credit'] == $coa2['id']) ? 'selected' : '' ?>>
                                                            <?= $coa1['name'] ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
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
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>