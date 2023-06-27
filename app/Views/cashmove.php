<?= $this->extend('layout') ?>

<?= $this->section('extraScript') ?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<?= $this->endSection() ?>

<?= $this->section('main') ?>

<!-- Page Heading -->
<div class="tm-card-header uk-light">
    <?= view('Views/Auth/_message_block') ?>

    <div uk-grid class="uk-flex-middle">
        <div class="uk-width-1-2@m">
            <h3 class="tm-h3"><?=lang('Global.cashmoveList')?></h3>
        </div>

        <!-- Button Trigger Modal Add -->
        <div class="uk-width-1-2@m uk-text-right@m">
            <button type="button" class="uk-button uk-button-primary uk-preserve-color" uk-toggle="target: #tambahdata"><?=lang('Global.addCashMove')?></button>
        </div>
        <!-- End Of Button Trigger Modal Add -->
    </div>
</div>
<!-- End Of Page Heading -->

<!-- Modal Add -->
<div uk-modal class="uk-flex-top" id="tambahdata">
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <div class="uk-modal-content">
            <div class="uk-modal-header">
                <h5 class="uk-modal-title" id="tambahdata" ><?=lang('Global.addCashMove')?></h5>
            </div>
            <div class="uk-modal-body">
                <form class="uk-form-stacked" role="form" action="cashmove/create" method="post">
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
                                      <?php } ?>
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
    <table class="uk-table uk-table-justify uk-table-middle uk-table-divider uk-light" id="example" style="width:100%">
        <thead>
            <tr>
                <th class="uk-text-center uk-width-small">No</th>
                <th class="uk-width-medium"><?=lang('Global.description')?></th>
                <th class="uk-width-medium"><?=lang('Global.origin')?></th>
                <th class="uk-text-center uk-width-small"><?=lang('Global.destination')?></th>
                <th class="uk-text-center uk-width-small"><?=lang('Global.quantity')?></th>
    
            </tr>
        </thead>
        <tbody>
            <?php $i = 1 ; ?>
            <?php foreach ($cashmoves as $cashmv) : ?>
                <tr>
                    <td class="uk-text-center"><?= $i++; ?></td>
                    <td><?= $cashmv['description']; ?></td>
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
                    <td class="uk-text-center"><?= $cashmv['qty']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<!-- End Of Table Content -->

<!-- Modal Edit -->
<?php foreach ($cashmoves as $cashmv) : ?>
    <div uk-modal class="uk-flex-top" id="editdata<?= $cashmv['id'] ?>">
        <div class="uk-modal-dialog uk-margin-auto-vertical">
            <div class="uk-modal-content">
                <div class="uk-modal-header">
                    <h5 class="uk-modal-title" id="editdata"><?=lang('Global.updateData')?></h5>
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