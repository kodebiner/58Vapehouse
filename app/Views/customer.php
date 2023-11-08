<?= $this->extend('layout') ?>

<?= $this->section('extraScript') ?>
    <script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
    <script type="text/javascript" src="js/moment.min.js"></script>
    <script type="text/javascript" src="js/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/daterangepicker.css" />
<?= $this->endSection() ?>

<?= $this->section('main') ?>

<!-- Page Heading -->
<div class="tm-card-header uk-light">
    <div uk-grid class="uk-child-width-1-1 uk-child-width-1-3@m uk-flex-middle">
        <div class="">
            <h3 class="tm-h3"><?=lang('Global.customerList')?></h3>
        </div>

        <!-- Date Range -->
        <div class="">
            <form id="short" action="customer" method="get">
                <div class="uk-inline">
                    <span class="uk-form-icon uk-form-icon-flip" uk-icon="calendar"></span>
                    <input class="uk-input uk-width-medium uk-border-rounded" type="text" id="daterange" name="daterange" value="<?=date('m/d/Y', $startdate)?> - <?=date('m/d/Y', $enddate)?>" />
                </div>
            </form>
            <script>
                $(function() {
                    $('input[name="daterange"]').daterangepicker({
                        opens: 'right'
                    }, function(start, end, label) {
                        document.getElementById('daterange').value = start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD');
                        document.getElementById('short').submit();
                    });
                });
            </script>
        </div>
        <!-- Date Range -->

        <!-- Button Trigger Modal Add -->
        <div class="uk-text-right">
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
                <div class="uk-child-width-1-2" uk-grid>
                    <div>
                        <h5 class="uk-modal-title" id="tambahdata" ><?=lang('Global.addCustomer')?></h5>
                    </div>
                    <div class="uk-text-right">
                        <button class="uk-modal-close uk-icon-button-delete" uk-icon="icon: close;" type="button"></button>
                    </div>
                </div>
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
    <!-- Search Engine -->
    <div>
        <form class="uk-search uk-search-default" method="GET" action="customer" style="background-color: #fff; border-radius: 7px;">
            <span uk-search-icon style="color: #000;"></span>
            <input class="uk-search-input" type="search" placeholder="Search Name" aria-label="Search" name="search" style="border-radius: 7px;">
        </form>
    </div>
    <!-- Search Engine End -->
    <table class="uk-table uk-table-justify uk-table-middle uk-table-divider uk-light">
        <thead>
            <tr>
                <th class="uk-text-center">No</th>
                <th><?=lang('Global.name')?></th>
                <th><?=lang('Auth.email')?></th>
                <th class="uk-text-center"><?=lang('Global.phone')?></th>
                <th class="uk-text-center"><?=lang('Global.transaction')?></th>
                <th class="uk-text-center"><?=lang('Global.debt')?></th>
                <th class="uk-text-center"><?=lang('Global.point')?></th>
                <th class="uk-text-center"><?=lang('Global.action')?></th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1 ; ?>
            <?php foreach ($customers as $customer) : ?>
                <tr>
                <td class="uk-text-center"><?= $i++; ?></td>
                <td><?= $customer['name']; ?></td>
                <td><?= $customer['email']; ?></td>
                <td class="uk-text-center">+62<?= $customer['phone']; ?></td>
                <?php foreach ($member as $mem) {
                    if ($mem['id'] === $customer['id']) { ?>
                        <td class="uk-text-center"><?= $mem['trx']; ?></td>
                        <td class="uk-text-center"><?= $mem['debt']; ?></td>
                    <?php }
                } ?>
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
    <div>
        <?= $pager->links('customer', 'front_full') ?>
    </div>
</div>
<!-- End Of Table Content -->

<!-- Modal Edit -->
<?php foreach ($customers as $customer) : ?>
    <div uk-modal class="uk-flex-top" id="editdata<?= $customer['id'] ?>">
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
<?= $this->endSection() ?>