<?= $this->extend('layout') ?>

<?= $this->section('extraScript') ?>
<script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
<script src="js/cdn.datatables.net_1.13.4_js_jquery.dataTables.min.js"></script>
<?= $this->endSection() ?>

<?= $this->section('main') ?>

<!-- Page Heading -->
<div class="tm-card-header uk-light">
    <div uk-grid class="uk-flex-middle">
        <div class="uk-width-1-2@m">
            <h3 class="tm-h3"><?=lang('Global.variantList')?> <?= $products['name']; ?></h3>
        </div>

        <!-- Button Trigger Modal Add -->
        <div class="uk-width-1-2@m uk-text-right@m">
            <button type="button" class="uk-button uk-button-primary uk-preserve-color" uk-toggle="target: #tambahdata"><?=lang('Global.addVariant')?></button>
        </div>
        <!-- End Of Button Trigger Modal Add -->
    </div>
</div>
<!-- End Of Page Heading -->

<?= view('Views/Auth/_message_block') ?>

<!-- Modal Add -->
<div uk-modal class="uk-flex-top" id="tambahdata">
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <div class="uk-modal-container">
            <div class="uk-modal-header">
                <div class="uk-child-width-1-2" uk-grid>
                    <div>
                        <h5 class="uk-modal-title" id="tambahdata" ><?=lang('Global.addVariant')?></h5>
                    </div>
                    <div class="uk-text-right">
                        <button class="uk-modal-close uk-icon-button-delete" uk-icon="icon: close;" type="button"></button>
                    </div>
                </div>
            </div>
            <div class="uk-modal-body">
                <form class="uk-form-stacked" role="form" action="/product/createvar/<?= $products['id']; ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="uk-margin-bottom">
                        <label class="uk-form-label" for="name"><?=lang('Global.name')?></label>
                        <div class="uk-form-controls">
                            <input type="text" class="uk-input <?php if (session('errors.name')) : ?>tm-form-invalid<?php endif ?>" id="name" name="name" placeholder="<?=lang('Global.name')?>" required />
                        </div>
                    </div>

                    <div class="uk-margin">
                        <label class="uk-form-label" for="hargadasar"><?=lang('Global.basePrice')?></label>
                        <div class="uk-form-controls">
                            <input type="text" class="uk-input <?php if (session('errors.basePrice')) : ?>tm-form-invalid<?php endif ?>" name="hargadasar" id="hargadasar" placeholder="<?=lang('Global.basePrice')?>" required/>
                        </div>
                    </div>

                    <div class="uk-margin-bottom">
                        <label class="uk-form-label" for="hargamodal"><?=lang('Global.capitalPrice')?></label>
                        <div class="uk-form-controls">
                            <input type="text" class="uk-input <?php if (session('errors.capitalPrice')) : ?>tm-form-invalid<?php endif ?>" id="hargamodal" name="hargamodal" placeholder="<?=lang('Global.capitalPrice')?>" required />
                        </div>
                    </div>

                    <div class="uk-margin-bottom">
                        <label class="uk-form-label" for="hargarekomendasi"><?=lang('Global.suggestPrice')?></label>
                        <div class="uk-form-controls">
                            <input type="text" class="uk-input <?php if (session('errors.suggestPrice')) : ?>tm-form-invalid<?php endif ?>" id="hargarekomendasi" name="hargarekomendasi" placeholder="<?=lang('Global.suggestPrice')?>" required />
                        </div>
                    </div>

                    <div class="uk-margin-bottom">
                        <label class="uk-form-label" for="margin"><?=lang('Global.margin')?></label>
                        <div class="uk-form-controls">
                            <input type="text" class="uk-input <?php if (session('errors.margin')) : ?>tm-form-invalid<?php endif ?>" id="margin" name="margin" placeholder="<?=lang('Global.margin')?>" required />
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
<!-- End Of Modal Add -->

<!-- Table Of Content -->
<div class="uk-overflow-auto uk-margin">
    <table class="uk-table uk-table-justify uk-table-middle uk-table-divider uk-light" id="example" style="width:100%">
        <thead>
            <tr>
                <th class="uk-text-center uk-width-small">SKU</th>
                <th class="uk-text-center uk-width-large"><?=lang('Global.name')?></th>
                <th class="uk-text-center uk-width-medium"><?=lang('Global.basePrice')?></th>
                <th class="uk-text-center uk-width-medium"><?=lang('Global.capitalPrice')?></th>
                <th class="uk-text-center uk-width-medium"><?=lang('Global.suggestPrice')?></th>
                <th class="uk-text-center uk-width-medium"><?=lang('Global.price')?></th>
                <th class="uk-text-center uk-width-small"><?=lang('Global.stock')?></th>
                <th class="uk-text-center uk-width-large"><?=lang('Global.action')?></th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1 ; ?>
            <?php foreach ($variants as $variant) { ?>
                <tr>
                    <td class="uk-text-center"><?= $variant['sku'] ?></td>
                    <td class="uk-text-center"><?= $variant['name'] ?></td>
                    <td class="uk-text-center">Rp <?= number_format((Int)$variant['hargadasar'],2,',','.'); ?></td>
                    <td class="uk-text-center">Rp <?= number_format((Int)$variant['hargamodal'],2,',','.'); ?></td>
                    <td class="uk-text-center">Rp <?= number_format((Int)$variant['hargarekomendasi'],2,',','.'); ?></td>
                    <td class="uk-text-center">Rp <?= number_format(((Int)$variant['hargajual'] + (Int)$variant['hargamodal']),2,',','.'); ?></td>
                    <td class="uk-text-center">
                        <?php
                        $qty = 0;
                        foreach ($stock as $stok) {
                            if ($stok['variantid'] === $variant['id']) {
                                $qty += (int)$stok['qty'];
                            }
                        }
                        echo $qty;
                        ?>
                    </td>
                    <td class="uk-child-width-auto uk-flex-center uk-grid-row-small uk-grid-column-small" uk-grid>
                        <!-- Button Trigger Modal Edit -->
                        <div>
                            <a class="uk-icon-button" uk-icon="pencil" uk-toggle="target: #editdata<?= $variant['id'] ?>"></a>
                        </div>
                        <!-- End Of Button Trigger Modal Edit -->

                        <!-- Button Delete -->
                        <div>
                            <a uk-icon="trash" class="uk-icon-button-delete" href="product/deletevar/<?= $variant['id'] ?>" onclick="return confirm('<?=lang('Global.deleteConfirm')?>')"></a>
                        </div>
                        <!-- End Of Button Delete -->
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<!-- End Of Table Content -->

<!-- Modal Edit -->
<?php foreach ($variants as $variant) : ?>
    <div uk-modal class="uk-flex-top" id="editdata<?= $variant['id']; ?>">
        <div class="uk-modal-dialog uk-margin-auto-vertical">
            <div class="uk-modal-container">
                <div class="uk-modal-header">
                    <div class="uk-child-width-1-2" uk-grid>
                        <div>
                            <h5 class="uk-modal-title" id="editdata"><?=lang('Global.updateData')?></h5>
                        </div>
                        <div class="uk-text-right">
                            <button class="uk-modal-close uk-icon-button-delete" uk-icon="icon: close;" type="button"></button>
                        </div>
                    </div>
                </div>

                <div class="uk-modal-body">
                    <form class="uk-form-stacked" role="form" action="product/editvar/<?= $variant['id']; ?>" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= $variant['id']; ?>">

                        <div class="uk-margin-bottom">
                            <label class="uk-form-label" for="name"><?=lang('Global.name')?></label>
                            <div class="uk-form-controls">
                                <input type="text" class="uk-input" id="name" name="name" value="<?= $variant['name']; ?>" />
                            </div>
                        </div>

                        <div class="uk-margin-bottom">
                            <label class="uk-form-label" for="hargadasar"><?=lang('Global.basePrice')?></label>
                            <div class="uk-form-controls">
                                <input type="text" class="uk-input" id="hargadasar" name="hargadasar"  value="<?= $variant['hargadasar']; ?>" />
                            </div>
                        </div>

                        <div class="uk-margin-bottom">
                            <label class="uk-form-label" for="hargammodal"><?=lang('Global.capitalPrice')?></label>
                            <div class="uk-form-controls">
                                <input type="text" class="uk-input" id="hargamodal" name="hargamodal"  value="<?= $variant['hargamodal']; ?>" />
                            </div>
                        </div>

                        <div class="uk-margin-bottom">
                            <label class="uk-form-label" for="hargammodal"><?=lang('Global.suggestPrice')?></label>
                            <div class="uk-form-controls">
                                <input type="text" class="uk-input" id="hargarekomendasi" name="hargarekomendasi"  value="<?= $variant['hargarekomendasi']; ?>" />
                            </div>
                        </div>
                        
                        <div class="uk-margin-bottom">
                            <label class="uk-form-label" for="margin"><?=lang('Global.margin')?></label>
                            <div class="uk-form-controls">
                                <input type="text" class="uk-input" id="margin" name="margin"  value="<?= $variant['hargajual']; ?>" />
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
<?php endforeach; ?>
<!-- End Of Modal Edit -->

<!-- Search Engine Script -->
<script>
    $(document).ready(function () {
        $('#example').DataTable();
    });
</script>
<!-- Search Engine Script End -->
<?= $this->endSection() ?>