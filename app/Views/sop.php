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
            <h3 class="tm-h3"><?=lang('Global.sop')?></h3>
        </div>

        <!-- Button Trigger Modal Add -->
        <div class="uk-width-1-2@m uk-text-right@m">
            <button type="button" class="uk-button uk-button-primary uk-preserve-color" uk-toggle="target: #tambahdata"><?=lang('Global.addsop')?></button>
        </div>
        <!-- End Of Button Trigger Modal Add -->
    </div>
</div>
<!-- End Of Page Heading -->

<?= view('Views/Auth/_message_block') ?>

<!-- Modal Add -->
<div uk-modal class="uk-flex-top" id="tambahdata">
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <div class="uk-modal-content">
            <div class="uk-modal-header">
                <h5 class="uk-modal-title" id="tambahdata" ><?=lang('Global.sop')?></h5>
            </div>
            <div class="uk-modal-body">
                <form class="uk-form-stacked" role="form" action="sop/create" method="post">
                    <?= csrf_field() ?>

                    <div class="uk-margin">
                        <label class="uk-form-label" for="form-stacked-text"><?=lang('Global.sop')?></label>
                        <input class="uk-input" name="name" type="text" placeholder="Name" aria-label="Input" required/>
                    </div>

                    <div class="uk-margin">
                        <label class="uk-form-label" for="form-stacked-text"><?=lang('Global.shift')?> </label>
                        <select class="uk-select" name="shift" aria-label="Select" required>
                            <option><?= lang('Global.shift') ?></option>
                            <option value="0"><?= lang('Global.shift1') ?></option>
                            <option value="1"><?= lang('Global.shift2') ?></option>
                        </select>
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
    <table class="uk-table uk-table-justify uk-table-middle uk-table-divider uk-light" id="example">
        <thead>
            <tr>
                <th class="uk-text-center">No</th>
                <th class=""><?=lang('Global.sop')?></th>
                <th class="uk-text-center"><?=lang('Global.shift')?></th>
                <th class="uk-text-center"><?=lang('Global.action')?></th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1 ; ?>
            <?php foreach ($sops as $sop) { ?>
                <tr>
                    <td class="uk-text-center"><?= $i++; ?></td>
                    <td class="uk-text-left"><?= $sop['name']; ?></td>
                    <td class="uk-text-center">
                        <?php if ($sop['shift'] === "0" ) {
                            echo lang('Global.shift1');
                        } else {
                            echo lang('Global.shift2');
                        } ?>
                    </td>
                    <td class="uk-child-width-auto uk-flex-center uk-grid-row-small uk-grid-column-small" uk-grid>
                        <!-- Button Trigger Modal Edit -->
                        <div>
                            <a class="uk-icon-button" uk-icon="pencil" uk-toggle="target: #update-<?= $sop['id'] ?>"></a>
                        </div>
                        <!-- Button Trigger Modal Edit End -->

                        <!-- Button Delete -->
                        <div>
                            <a class="uk-icon-button-delete" uk-icon="trash" href="sop/delete/<?= $sop['id'] ?>" onclick="return confirm('<?=lang('Global.deleteConfirm')?>')"></a>
                        </div>
                        <!-- Button Delete End -->
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<!-- End Table Content -->

<!-- Modal Edit -->
<?php foreach ($sops as $sop) { ?>
    <div uk-modal class="uk-flex-top" id="update-<?= $sop['id'] ?>">
        <div class="uk-modal-dialog uk-margin-auto-vertical">
            <div class="uk-modal-content">
                <div class="uk-modal-header">
                    <h5 class="uk-modal-title"><?=lang('Global.sop')?></h5>
                </div>
                <div class="uk-modal-body">
                    <form class="uk-form-stacked" role="form" action="sop/update/<?= $sop['id'] ?>" method="post">
                        <?= csrf_field() ?>

                        <div class="uk-margin">
                            <label class="uk-form-label" for="form-stacked-text" required><?=lang('Global.sop')?></label>
                            <input class="uk-input" name="name" type="text" value="<?= $sop['name'] ?>" placeholder="<?= $sop['name'] ?>" aria-label="Input" required/>
                        </div>

                        <div class="uk-margin">
                            <label class="uk-form-label" for="form-stacked-text" required><?=lang('Global.shift')?> </label>
                            <select class="uk-select" name="shift" aria-label="Select" placeholder="<?php if ($sop['shift'] === "0") { ?><?= lang('Global.shift1') ?><?php } elseif ($sop['shift'] === "1") { ?><?= lang('Global.shift2') ?><?php } ?>">
                                <option disabled><?= lang('Global.shift') ?></option>
                                <?php if ($sop['shift'] === "0") { ?>
                                    <option value="0" selected><?= lang('Global.shift1') ?></option>
                                    <option value="1"><?= lang('Global.shift2') ?></option>
                                <?php } else { ?>
                                    <option value="0"><?= lang('Global.shift1') ?></option>
                                    <option value="1" selected><?= lang('Global.shift2') ?></option>
                                <?php } ?>
                            </select>
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
<!-- Modal Edit End -->

<!-- Search Engine Script -->
<script>
    $(document).ready(function () {
        $('#example').DataTable();
    });
</script>
<!-- Search Engine Script End -->

<?= $this->endSection() ?>