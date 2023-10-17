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
            <h3 class="tm-h3"><?=lang('Global.outletList')?></h3>
        </div>
        <?php if (in_groups('owner')) : ?>
        <!-- Button Trigger Modal Add -->
        <div class="uk-width-1-2@m uk-text-right@m">
            <button type="button" class="uk-button uk-button-primary uk-preserve-color" uk-toggle="target: #tambahdata"><?=lang('Global.addOutlet')?></button>
        </div>
        <!-- End Of Button Trigger Modal Add -->
        <?php endif ?>
    </div>
</div>
<!-- End Of Page Heading -->

<?= view('Views/Auth/_message_block') ?>

<!-- Modal Add -->
<div uk-modal class="uk-flex-top" id="tambahdata">
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <div class="uk-modal-content">
            <div class="uk-modal-header">
                <h5 class="uk-modal-title" id="tambahdata" ><?=lang('Global.addOutlet')?></h5>
            </div>
            <div class="uk-modal-body">
                <form class="uk-form-stacked" role="form" action="outlet/create" method="post">
                    <?= csrf_field() ?>

                    <div class="uk-margin-bottom">
                        <label class="uk-form-label" for="name"><?=lang('Global.name')?></label>
                        <div class="uk-form-controls">
                            <input type="text" class="uk-input <?php if (session('errors.name')) : ?>tm-form-invalid<?php endif ?>" id="name" name="name" placeholder="<?=lang('Global.name')?>" required />
                        </div>
                    </div>

                    <div class="uk-margin">
                        <label class="uk-form-label" for="address"><?=lang('Global.address')?></label>
                        <div class="uk-form-controls">
                            <input type="text" class="uk-input <?php if (session('errors.address')) : ?>tm-form-invalid<?php endif ?>" name="address" id="address" placeholder="<?=lang('Global.address')?>" required/>
                        </div>
                    </div>

                    <div class="uk-margin-bottom">
                        <label class="uk-form-label" for="maps"><?=lang('Global.maps')?></label>
                        <div class="uk-form-controls">
                            <input type="text" class="uk-input <?php if (session('errors.maps')) : ?>tm-form-invalid<?php endif ?>" id="maps" name="maps" placeholder="<?=lang('Global.maps')?>" required />
                        </div>
                    </div>

                    <div class="uk-margin-bottom">
                        <label class="uk-form-label" for="instagram"><?=lang('Global.instagram')?></label>
                        <div class="uk-form-controls">
                            <input type="text" class="uk-input <?php if (session('errors.instagram')) : ?>tm-form-invalid<?php endif ?>" id="instagram" name="instagram" placeholder="<?=lang('Global.instagram')?>" required />
                        </div>
                    </div>

                    <div class="uk-margin-bottom">
                        <label class="uk-form-label" for="maps"><?=lang('Global.phone')?></label>
                        <div class="uk-form-controls">
                            <input type="text" class="uk-input <?php if (session('errors.phone')) : ?>tm-form-invalid<?php endif ?>" id="phone" name="phone" placeholder="<?=lang('Global.phone')?>" required />
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
                <th class="uk-text-center uk-width-small">No</th>
                <th class="uk-width-medium"><?=lang('Global.name')?></th>
                <th class="uk-width-large"><?=lang('Global.address')?></th>
                <?php if (in_groups('owner')) : ?>
                <th class="uk-text-center uk-width-small"><?=lang('Global.action')?></th>
                <?php endif ?>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1 ; ?>
            <?php foreach ($outlets as $outlet) { ?>
                <tr>
                    <td class="uk-text-center"><?= $i++; ?></td>
                    <td><?= $outlet['name']; ?></td>
                    <td><?= $outlet['address']; ?></td>
                    <?php if (in_groups('owner')) : ?>
                    <td class="uk-child-width-auto uk-flex-center uk-grid-row-small uk-grid-column-small" uk-grid>
                        <!-- Button Trigger Modal Edit -->
                        <div>
                            <a class="uk-icon-button" uk-icon="pencil" uk-toggle="target: #editdata<?= $outlet['id'] ?>"></a>
                        </div>
                        <!-- End Of Button Trigger Modal Edit -->

                        <!-- Button Delete -->
                        <div>
                            <a uk-icon="trash" class="uk-icon-button-delete" href="outlet/delete/<?= $outlet['id'] ?>" onclick="return confirm('<?=lang('Global.deleteConfirm')?>')"></a>
                        </div>
                        <!-- End Of Button Delete -->
                    </td>
                    <?php endif ?>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<!-- End Of Table Content -->

<!-- Modal Edit -->
<?php foreach ($outlets as $outlet) { ?>
    <div uk-modal class="uk-flex-top" id="editdata<?= $outlet['id'] ?>">
        <div class="uk-modal-dialog uk-margin-auto-vertical">
            <div class="uk-modal-content">
                <div class="uk-modal-header">
                    <h5 class="uk-modal-title" id="editdata"><?=lang('Global.updateData')?></h5>
                </div>

                <div class="uk-modal-body">
                    <form class="uk-form-stacked" role="form" action="outlet/update/<?= $outlet['id'] ?>" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= $outlet['id']; ?>">

                        <div class="uk-margin-bottom">
                            <label class="uk-form-label" for="name"><?=lang('Global.name')?></label>
                            <div class="uk-form-controls">
                                <input type="text" class="uk-input" id="name" name="name" value="<?= $outlet['name']; ?>" />
                            </div>
                        </div>

                        <div class="uk-margin-bottom">
                            <label class="uk-form-label" for="address"><?=lang('Global.address')?></label>
                            <div class="uk-form-controls">
                                <input type="text" class="uk-input" id="address" name="address"  value="<?= $outlet['address']; ?>" />
                            </div>
                        </div>

                        <div class="uk-margin-bottom">
                            <label class="uk-form-label" for="maps"><?=lang('Global.maps')?></label>
                            <div class="uk-form-controls">
                                <input type="text" class="uk-input" id="maps" name="maps"  value="<?= $outlet['maps']; ?>" />
                            </div>
                        </div>

                        <div class="uk-margin-bottom">
                            <label class="uk-form-label" for="instagram"><?=lang('Global.instagram')?></label>
                            <div class="uk-form-controls">
                                <input type="text" class="uk-input" id="instagram" name="instagram"  value="<?= $outlet['instagram']; ?>" />
                            </div>
                        </div>

                        <div class="uk-margin-bottom">
                            <label class="uk-form-label" for="phone"><?=lang('Global.phone')?></label>
                            <div class="uk-form-controls">
                                <input type="text" class="uk-input" id="phone" name="phone"  value="<?= $outlet['phone']; ?>" />
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
<!-- End Of Modal Edit -->

<!-- Search Engine Script -->
<script>
    $(document).ready(function () {
        $('#example').DataTable();
    });
</script>
<!-- Search Engine Script End -->
<?= $this->endSection() ?>