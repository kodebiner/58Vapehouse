<?= $this->extend('layout') ?>

<?= $this->section('extraScript') ?>
<script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
<?= $this->endSection() ?>

<?= $this->section('main') ?>

<!-- Page Heading -->
<div class="tm-card-header uk-light">
    <div uk-grid class="uk-flex-middle">
        <div class="uk-width-1-2@m">
            <h3 class="tm-h3">Riwayat Stok <?= $name ?></h3>
            <h3 class="tm-h3"><?= $sku ?></h3>
        </div>
    </div>
</div>
<!-- End Of Page Heading -->

<?= view('Views/Auth/_message_block') ?>

<!-- Table Of Content -->
<div class="uk-overflow-auto uk-margin">
    <!-- Counter Total -->
    <div class="uk-light" uk-grid>
        <div class="uk-width-1-3 uk-width-1-6@m uk-form-horizontal">
            <div class="uk-form-label uk-margin-top" style="width: 100px;"><?= lang('Global.total') ?> <?= lang('Global.stock') ?> :</div>
            <div class="uk-form-controls uk-margin-top uk-margin-remove-left"><?= $totalstock ?></div>
        </div>
    </div>
    <!-- Counter Total End -->

    <!-- Search Engine -->
    <!-- <div class="uk-margin-medium-bottom">
        <form action="stock" method="GET">
            <div class="uk-child-width-1-1 uk-child-width-1-4@m uk-flex-middle" uk-grid>
                <div class="uk-text-right@l uk-margin-small-top">
                    <div class="uk-search uk-search-default uk-width-1-1">
                        <span class="uk-form-icon" uk-icon="icon: search" style="color: #000;"></span>
                        <input class="uk-width-1-1 uk-input" type="search" name="search" style="border-radius: 7px;" placeholder="Search Item ..." aria-label="Search" value="</?= (!empty($input['search']) ? $input['search'] : '') ?>">
                    </div>
                </div>
                <div class="uk-text-center">
                    <button class="uk-button uk-button-primary" type="submit">Search</button>
                </div>
            </div>
        </form>
    </div> -->
    <!-- Search Engine End -->

    <table class="uk-table uk-table-justify uk-table-middle uk-table-divider uk-light">
        <thead>
            <tr>
                <th>Tanggal</th>
                <!-- <th>SKU</th>
                <th>Nama</th> -->
                <th>Status</th>
                <th>Jumlah</th>
                <th>Sisa Stok</th>
            </tr>
        </thead>
        <tbody>
            <!-- <tr>
                <td></?= date('l, d M Y, H:i:s', strtotime($stocks[0]['date'])) ?></td>
                <td>
                    </?php if ($stocks[0]['status'] == '0') {
                        echo 'Saat Ini';
                    } ?>
                </td>
                <td></?= $stocks[0]['qty'] ?></td>
                <td></?= $stocks[0]['qty'] ?></td>
            </tr> -->
            <?php foreach ($stocks as $key => $stock) {
                // if ($key > 0) { ?>
                    <tr>
                        <td><?= date('l, d M Y, H:i:s', strtotime($stock['date'])) ?></td>
                        <!-- <td></?= $stock['sku'] ?></td>
                        <td></?= $stock['name'] ?></td> -->
                        <td>
                            <?php if (($stock['status'] == '1') || ($stock['status'] == '2')) {
                                echo 'Penyesuaian Stok';
                            } ?>
                            <?php if (($stock['status'] == '3') || ($stock['status'] == '4')) {
                                echo 'Pemindahan Stok';
                            } ?>
                            <?php if ($stock['status'] == '5') {
                                echo 'Penjualan';
                            } ?>
                            <?php if ($stock['status'] == '6') {
                                echo 'Pembelian';
                            } ?>
                        </td>
                        <td>
                            <?php if ($stock['status'] == '1') {
                                echo '<div style="color: green">+'.$stock['qty'].'</div>';
                            } ?>
                            <?php if ($stock['status'] == '2') {
                                echo '<div style="color: red">-'.$stock['qty'].'</div>';
                            } ?>
                            <?php if ($stock['status'] == '3') {
                                echo '<div style="color: red">-'.$stock['qty'].'</div>';
                            } ?>
                            <?php if ($stock['status'] == '4') {
                                echo '<div style="color: green">+'.$stock['qty'].'</div>';
                            } ?>
                            <?php if ($stock['status'] == '5') {
                                echo '<div style="color: red">-'.$stock['qty'].'</div>';
                            } ?>
                            <?php if ($stock['status'] == '6') {
                                echo '<div style="color: green">+'.$stock['qty'].'</div>';
                            } ?>
                        </td>
                        <td>
                            <?php if ($stock['status'] == '1') {
                                echo (Int)$totalstock - (Int)$stock['qty'];
                            } ?>
                            <?php if ($stock['status'] == '2') {
                                echo (Int)$totalstock + (Int)$stock['qty'];
                            } ?>
                            <?php if ($stock['status'] == '3') {
                                echo (Int)$totalstock + (Int)$stock['qty'];
                            } ?>
                            <?php if ($stock['status'] == '4') {
                                echo (Int)$totalstock - (Int)$stock['qty'];
                            } ?>
                            <?php if ($stock['status'] == '5') {
                                echo (Int)$totalstock + (Int)$stock['qty'];
                            } ?>
                            <?php if ($stock['status'] == '6') {
                                echo (Int)$totalstock - (Int)$stock['qty'];
                            } ?>
                        </td>
                    </tr>
                <?php
                // }
            } ?>
        </tbody>
    </table>
    <div>
        <?= $pager_links ?>
    </div>
</div>
<!-- End Table Content -->
<?= $this->endSection() ?>