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
            <h3 class="tm-h3"><?=lang('Global.customerList')?></h3>
        </div>

        <!-- Button Trigger Modal Add -->
        <div class="uk-width-1-2@m uk-text-right@m">
            <button type="button" class="uk-button uk-button-primary uk-preserve-color" uk-toggle="target: #tambahdata"><?=lang('Global.addCustomer')?></button>
        </div>
        <!-- End Of Button Trigger Modal Add -->
    </div>
</div>
<!-- End of Page Heading -->

<?= view('Views/Auth/_message_block') ?>

<!-- Modal Add -->
<div uk-modal class="uk-flex-top" id="tambahdata">
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <div class="uk-modal-content">
            <div class="uk-modal-header">
                <h5 class="uk-modal-title" id="tambahdata" ><?=lang('Global.addCustomer')?></h5>
            </div>
            <div class="uk-modal-body">
                <form class="uk-form-stacked" role="form" action="/customer/create" method="post">
                    <?= csrf_field() ?>

                    <div class="uk-margin-bottom">
                        <label class="uk-form-label" for="name"><?=lang('Global.name')?></label>
                        <div class="uk-form-controls">
                        <input type="text" class="uk-input <?php if (session('errors.name')) : ?>tm-form-invalid<?php endif ?>" id="name" name="name" placeholder="<?=lang('Global.name')?>" required />
                        </div>
                    </div>

                    <div class="uk-margin-bottom">
                        <label class="uk-form-label" for="phone"><?=lang('Global.phone')?></label>
                        <div class="uk-form-controls">
                            <div class="uk-inline uk-width-1-1">
                                <span class="uk-form-icon">+62</span>
                                <input class="uk-input <?php if (session('errors.phone')) : ?>tm-form-invalid<?php endif ?>" min="1" id="phone" name="phone" type="number" placeholder="<?=lang('Global.phone')?>" aria-label="Not clickable icon" required/>
                            </div>
                        </div>
                    </div>

                    <div class="uk-margin">
                        <label class="uk-form-label" for="email"><?=lang('Auth.email')?></label>
                        <div class="uk-form-controls">
                        <input type="email" class="uk-input <?php if (session('errors.email')) : ?>tm-form-invalid<?php endif ?>" name="email" id="email" placeholder="<?=lang('Auth.email')?>"/>
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
                <th class="uk-text-center">No</th>
                <th class="uk-text-center"><?=lang('Global.name')?></th>
                <th class="uk-text-center"><?=lang('Auth.email')?></th>
                <th class="uk-text-center"><?=lang('Global.phone')?></th>
                <th class="uk-text-center"><?=lang('Global.debt')?></th>
                <th class="uk-text-center"><?=lang('Global.transaction')?></th>
                <th class="uk-text-center"><?=lang('Global.point')?></th>
                <th class="uk-text-center"><?=lang('Global.action')?></th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1 ; ?>
            <?php foreach ($customers as $customer) : ?>
                <tr>
                <td class="uk-text-center"><?= $i++; ?></td>
                <td class="uk-text-center"><?= $customer['name']; ?></td>
                <td class="uk-text-center"><?= $customer['email']; ?></td>
                <td class="uk-text-center"><?= $customer['phone']; ?></td>
                <td class="uk-text-center"><?= $customer['kasbon']; ?></td>
                <td class="uk-text-center"><?= $customer['trx']; ?></td>
                <td class="uk-text-center"><?= $customer['poin']; ?></td>
                <td class="uk-child-width-auto uk-flex-center uk-grid-row-small uk-grid-column-small" uk-grid>
                    <!-- Button Trigger Modal Edit -->
                    <div>
                        <a class="uk-icon-button" uk-icon="pencil" uk-toggle="target: #editdata<?= $customer['id'] ?>"></a>
                    </div>
                    <!-- End Of Button Trigger Modal Edit -->

                    <!-- Button Delete -->
                    <div>
                        <a uk-icon="trash" class="uk-icon-button-delete" href="customer/delete/<?= $customer['id'] ?>" onclick="return confirm('<?=lang('Global.deleteConfirm')?>')"></a>
                    </div>
                    <!-- End Of Button Delete -->
                </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

  <!-- Modal Edit -->
    <?php foreach ($customers as $customer) : ?>
        <div uk-modal class="uk-flex-top" id="editdata<?= $customer['id'] ?>">
            <div class="uk-modal-dialog uk-margin-auto-vertical">
                <div class="uk-modal-content">
                    <div class="uk-modal-header">
                        <h5 class="uk-modal-title" id="editdata"><?=lang('Global.updateData')?></h5>
                    </div>

                    <div class="uk-modal-body">
                        <form class="uk-form-stacked" role="form" action="customer/update/<?= $customer['id'] ?>" method="post">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id" value="<?= $customer['id']; ?>">

                            <div class="uk-margin-bottom">
                                <label class="uk-form-label" for="name"><?=lang('Global.name')?></label>
                                <div class="uk-form-controls">
                                <input type="text" class="uk-input" id="name" name="name" value="<?= $customer['name']; ?>" />
                                </div>
                            </div>

                            <div class="uk-margin-bottom">
                                <label class="uk-form-label" for="phone"><?=lang('Global.phone')?></label>
                                <div class="uk-form-controls">
                                    <div class="uk-inline uk-width-1-1">
                                        <span class="uk-form-icon">+62</span>
                                        <input class="uk-input" min="1" id="phone" name="phone" type="number" value="<?= $customer['phone']; ?>" aria-label="Not clickable icon">
                                    </div>
                                </div>
                            </div>

                            <div class="uk-margin-bottom">
                                <label class="uk-form-label" for="email"><?=lang('Auth.email')?></label>
                                <div class="uk-form-controls">
                                <input type="text" class="uk-input" id="email" name="email"  value="<?= $customer['email']; ?>" />
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
</div>
<!-- End Of Table Content -->

<!-- Search Engine Script -->
<script>
    $(document).ready(function () {
        $('#example').DataTable();
    });
</script>
<!-- Search Engine Script End -->
<?= $this->endSection() ?>