<?= $this->extend('layout') ?>

<?= $this->section('extraScript') ?>
<script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
<script src="js/cdn.datatables.net_1.13.4_js_jquery.dataTables.min.js"></script>
<script type="text/javascript" src="js/moment.min.js"></script>
<script type="text/javascript" src="js/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="css/daterangepicker.css" />
<?= $this->endSection() ?>

<?= $this->section('main') ?>

<!-- Page Heading -->
<div class="tm-card-header uk-light">
    <div uk-grid class="uk-flex-middle">
        <div class="uk-width-1-1 uk-width-1-3@s">
            <h3 class="tm-h3"><?=lang('Global.walletMoveList')?></h3>
        </div>

        <!-- Button Daterange -->
        <div class="uk-width-1-1 uk-width-1-3@s uk-text-right">
            <form id="short" action="walletmove" method="get">
                <div class="uk-inline">
                    <span class="uk-form-icon uk-form-icon-flip" uk-icon="calendar"></span>
                    <input class="uk-input uk-width-medium uk-border-rounded" type="text" id="daterange" name="daterange" value="<?=date('m/d/Y', $startdate)?> - <?=date('m/d/Y', $enddate)?>" />
                </div>
            </form>
            <script>
                $(function() {
                    $('input[name="daterange"]').daterangepicker({
                        maxDate: new Date(),
                        opens: 'right'
                    }, function(start, end, label) {
                        document.getElementById('daterange').value = start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD');
                        document.getElementById('short').submit();
                    });
                });
            </script>
        </div>
        <!-- End Of Button Daterange-->

        <!-- Button Trigger Modal Add -->
        <div class="uk-width-1-1 uk-width-1-3@s uk-text-right">
            <button type="button" class="uk-button uk-button-primary uk-preserve-color" uk-toggle="target: #tambahdata"><?=lang('Global.addWallMove')?></button>
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
                        <h5 class="uk-modal-title" id="tambahdata" ><?=lang('Global.addWallMove')?></h5>
                    </div>
                    <div class="uk-text-right">
                        <button class="uk-modal-close uk-icon-button-delete" uk-icon="icon: close;" type="button"></button>
                    </div>
                </div>
            </div>
            <div class="uk-modal-body">
                <form class="uk-form-stacked" role="form" action="walletmove/create" method="post">
                    <?= csrf_field() ?>

                    <div class="uk-margin">
                        <label class="uk-form-label" for="description"><?=lang('Global.description')?></label>
                        <div class="uk-form-controls">
                            <input type="text" class="uk-input <?php if (session('errors.description')) : ?>tm-form-invalid<?php endif ?>" name="description" id="description" placeholder="<?=lang('Global.description')?>" required/>
                        </div>
                    </div>

                    <!-- select origin -->
                    <div class="uk-margin-bottom">
                    <label class="uk-form-label" for="origin"><?=lang('Global.origin')?></label>
                        <div class="uk-form-controls">
                            <select class="uk-select" name="origin" id="sel_ori">
                                <option><?=lang('Global.origin')?></option>
                                    <?php foreach ($cashmans as $cas) { ?>
                                        <option value="<?= $cas['id']; ?>"><?= $cas['name']; ?></option>
                                    <?php  } ?>
                            </select>
                        </div>
                    </div>
                    <!-- end select origin-->
                    
                    <!-- select origin -->
                    <div class="uk-margin-bottom">
                    <label class="uk-form-label" for="destination"><?=lang('Global.destination')?></label>
                        <div class="uk-form-controls">
                            <select class="uk-select" name="destination" id="sel_des">
                                <option><?=lang('Global.destination')?></option>
                                    <?php foreach ($cashmans as $cas) { ?>
                                        <option value="<?= $cas['id']; ?>"><?= $cas['name']; ?></option>
                                    <?php } ?>
                            </select>
                        </div>
                    </div>
                    <!-- end select origin-->

                    <div class="uk-margin">
                        <label class="uk-form-label" for="description"><?=lang('Global.quantity')?></label>
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
    <table class="uk-table uk-table-justify uk-table-middle uk-table-divider uk-light">
        <thead>
            <tr>
                <th class="uk-text-center">No</th>
                <th class=""><?=lang('Global.description')?></th>
                <th class=""><?=lang('Global.date')?></th>
                <th class=""><?=lang('Global.origin')?></th>
                <th class=""><?=lang('Global.destination')?></th>
                <th class="uk-text-center"><?=lang('Global.quantity')?></th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1 ; ?>
            <?php foreach ($cashmoves as $cashmv) : ?>
                <tr>
                    <td class="uk-text-center"><?= $i++; ?></td>
                    <td><?= $cashmv['description']; ?></td>
                    <td><?= date('l, d M Y', strtotime($cashmv['date'])); ?></td>
                    <td>
                        <?php foreach ($cashmans as $cash) {
                            if ($cash['id'] === $cashmv['origin']) {
                                echo $cash['name'];
                            }
                        } ?>
                    </td>
                    <td>
                        <?php foreach ($cashmans as $cash){
                            if( $cash['id']===$cashmv['destination']){
                                echo $cash['name'];
                            }
                        }
                        ?>
                    </td>
                    <td class="uk-text-center">Rp <?= number_format($cashmv['qty'],2,',','.'); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div>
        <?= $pager->links('trxhistory', 'front_full') ?>
    </div>
</div>
<!-- End Of Table Content -->

<!-- Modal Edit -->
<?php foreach ($cashmoves as $cashmv) : ?>
    <div uk-modal class="uk-flex-top" id="editdata<?= $cashmv['id'] ?>">
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
                    <form class="uk-form-stacked" role="form" action="cashmove/update/<?= $cashmv['id'] ?>" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= $cashmv['id']; ?>">
                        
                        <!-- select origin -->
                        <div class="uk-margin-bottom">
                        <label class="uk-form-label" for="origin"><?=lang('Global.origin')?></label>
                            <div class="uk-form-controls">
                                <select class="uk-select" name="origin" id="sel_ori">
                                    <option value="<?= $cashmv['origin']; ?>">
                                        <?php foreach ($cashmans as $cas) {
                                            if ($cashmv['origin']===$cas['id']){
                                                echo $cas['name'];
                                            }
                                        } ?>
                                    </option>
                                    <?php foreach ($cashmans as $cas) { ?>
                                            <option value="<?= $cas['id']; ?>"><?= $cas['name']; ?></option>
                                        <?php } ?>
                                </select>
                            </div>
                        </div>
                        <!-- end select origin-->
                        
                        <!-- select destination -->
                        <div class="uk-margin-bottom">
                        <label class="uk-form-label" for="destination"><?=lang('Global.destination')?></label>
                            <div class="uk-form-controls">
                                <select class="uk-select" name="destination" id="sel_des">
                                    <option value="<?= $cashmv['destination']; ?>"> 
                                        <?php foreach ($cashmans as $cas) {
                                            if ($cashmv['destination']===$cas['id']){
                                                echo $cas['name'];
                                            }
                                        } ?>
                                    </option>
                                        <?php foreach ($cashmans as $cas) { ?>
                                            <option value="<?= $cas['id']; ?>"><?= $cas['name']; ?></option>
                                        <?php } ?>
                                </select>
                            </div>
                        </div>
                        <!-- end select destination-->

                        <div class="uk-margin-bottom">
                            <label class="uk-form-label" for="description"><?=lang('Global.description')?></label>
                            <div class="uk-form-controls">
                                <input type="text" class="uk-input" id="description" name="description" value="<?= $cashmv['description']; ?>"autofocus />
                            </div>
                        </div>

                        <div class="uk-margin">
                            <label class="uk-form-label" for="qty"><?=lang('Global.quantity')?></label>
                            <div class="uk-form-controls">
                                <input type="text" class="uk-input" id="qty" name="qty" value="<?= $cashmv['qty']; ?>"autofocus />
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