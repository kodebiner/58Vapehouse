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
            <h3 class="tm-h3"><?=lang('Global.walletManList')?></h3>
        </div>

        <!-- Button Trigger Modal Add -->
        <div class="uk-width-1-2@m uk-text-right@m">
            <button type="button" class="uk-button uk-button-primary uk-preserve-color" uk-toggle="target: #tambahdata"><?=lang('Global.addWallMan')?></button>
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
                <div class="uk-child-width-1-2" uk-grid>
                    <div>
                        <h5 class="uk-modal-title" id="tambahdata" ><?=lang('Global.addWallMan')?></h5>
                    </div>
                    <div class="uk-text-right">
                        <button class="uk-modal-close uk-icon-button-delete" uk-icon="icon: close;" type="button"></button>
                    </div>
                </div>
            </div>
            <div class="uk-modal-body">
                <form class="uk-form-stacked" role="form" action="walletman/create" method="post">
                    <?= csrf_field() ?>

                    <!-- select oulet -->
                    <div class="uk-margin-bottom">
                    <label class="uk-form-label" for="outlet"><?=lang('Global.outlet')?></label>
                        <div class="uk-form-controls">
                            <select class="uk-select" name="outlet" id="sel_out">
                                <option selected disbled hidden>-- <?=lang('Global.chooseoutlet')?> --</option>
                                <option value="0"><?=lang('Global.allOutlets')?></option>
                                <?php
                                    foreach ($outlets as $outlet) {
                                        if ($outlet['id'] === $outletPick) {
                                            $checked = 'selected';
                                        } else {
                                            $checked = '';
                                        }
                                        ?>
                                        <option value="<?= $outlet['id']; ?>" <?=$checked?>><?= $outlet['name']; ?></option>
                                        <?php
                                    }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="uk-margin-bottom">
                        <label class="uk-form-label" for="name"><?=lang('Global.name')?></label>
                        <div class="uk-form-controls">
                            <input type="text" class="uk-input <?php if (session('errors.name')) : ?>tm-form-invalid<?php endif ?>" id="name" name="name" placeholder="<?=lang('Global.name')?>" autofocus required />
                        </div>
                    </div>

                    <div class="uk-margin">
                        <label class="uk-form-label" for="qty"><?=lang('Global.quantity')?></label>
                        <div class="uk-form-controls">
                            <input type="text" class="uk-input <?php if (session('errors.qty')) : ?>tm-form-invalid<?php endif ?>" name="qty" id="qty" placeholder="<?=lang('Global.quantity')?>" required/>
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
                <th class="uk-width-medium"><?=lang('Global.outlet')?></th>
                <th class="uk-text-center uk-width-small"><?=lang('Global.quantity')?></th>
                <th class="uk-text-center uk-width-small"><?=lang('Global.action')?></th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1 ; ?>
            <?php foreach ($cashmans as $cash) : ?>
                <tr>
                    <td class="uk-text-center"><?= $i++; ?></td>
                    <td><?= $cash['name']; ?></td>
                    <td>
                        <?php foreach ($outlets as $outlet) {
                            if ($outlet['id'] === $cash['outletid']) {
                                echo $outlet['name'];
                            } 
                        } if ($cash['outletid']==="0"){
                            echo "All Outlets";
                        }
                        ?>
                    </td>
                    <td class="uk-text-center">Rp <?= number_format($cash['qty'],2,',','.'); ?></td>
                    <td class="uk-child-width-auto uk-flex-center uk-grid-row-small uk-grid-column-small" uk-grid>
                        <!-- Button Trigger Modal Edit -->
                        <div>
                            <a class="uk-icon-button" uk-icon="pencil" uk-toggle="target: #editdata<?= $cash['id'] ?>"></a>
                        </div>
                        <!-- End Of Button Trigger Modal Edit -->

                        <!-- Button Delete -->
                        <!-- <div>
                            <a uk-icon="trash" class="uk-icon-button-delete" href="cashman/delete/<?= $cash['id'] ?>" onclick="return confirm('<?=lang('Global.deleteConfirm')?>')"></a>
                        </div> -->
                        <!-- End Of Button Delete -->
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<!-- End Of Table Content -->

<!-- Modal Edit -->
<?php foreach ($cashmans as $cash) : ?>
    <div uk-modal class="uk-flex-top" id="editdata<?= $cash['id'] ?>">
        <div class="uk-modal-dialog uk-margin-auto-vertical">
            <div class="uk-modal-content">
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
                    <form class="uk-form-stacked" role="form" action="walletman/update/<?= $cash['id'] ?>" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= $cash['id']; ?>">
                        
                        <div class="uk-margin">
                            <label class="uk-form-label" for="outlet"><?=lang('Global.outlet')?></label>
                            <div class="uk-form-controls">
                                <select class="uk-select" name="outlet">
                                    <!-- <option disabled><?=lang('Global.allOutlets')?></option> -->
                                    <option value="0"><?=lang('Global.allOutlets')?></option>
                                    <?php foreach ($outlets as $outlet) { ?>
                                        <option value="<?= $outlet['id']; ?>" <?php if ($outlet['id'] === $cash['outletid']) {echo 'selected';} ?>><?= $outlet['name']; ?></option>
                                    <?php }?>
                                </select>
                            </div>
                        </div>

                        <div class="uk-margin-bottom">
                            <label class="uk-form-label" for="name"><?=lang('Global.name')?></label>
                            <div class="uk-form-controls">
                                <input type="text" class="uk-input" id="name" name="name" value="<?= $cash['name']; ?>" />
                            </div>
                        </div>
                    

                        <div class="uk-margin">
                            <label class="uk-form-label" for="qty"><?=lang('Global.quantity')?></label>
                            <div class="uk-form-controls">
                                <input type="text" class="uk-input" id="qty" name="qty" value="<?= $cash['qty']; ?>" />
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