<?= $this->extend('layout') ?>

<?= $this->section('extraScript') ?>
    <script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
    <script src="js/cdn.datatables.net_1.13.4_js_jquery.dataTables.min.js"></script>
    <script src="js/cdnjs.cloudflare.com_ajax_libs_webcamjs_1.0.25_webcam.min.js"></script>
<?= $this->endSection() ?>

<?= $this->section('main') ?>

<?php if ($outletPick === null) { ?>
    <div class="uk-alert-danger" uk-alert>
        <p><?=lang('Global.chooseoutlet')?></p>
    </div>
<?php } else { ?>

<!-- Page Heading -->
<div class="tm-card-header uk-light">
    <div uk-grid class="uk-flex-middle">
        <div class="uk-width-1-2@m">
            <h3 class="tm-h3"><?=lang('Global.cashinout')?></h3>
        </div>

        <!-- Button Trigger Modal Add -->
        <div class="uk-width-1-2@m uk-text-right@m">
            <button type="button" class="uk-button uk-button-primary uk-preserve-color" uk-toggle="target: #tambahdata"><?=lang('Global.cashin/out')?></button>
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
                <h5 class="uk-modal-title" id="tambahdata" ><?=lang('Global.addCash')?></h5>
            </div>
            <div class="uk-modal-body">
                <form class="uk-form-stacked" role="form" action="cashinout/create" method="post">
                    <?= csrf_field() ?>

                    <div class="uk-margin-bottom">
                        <label class="uk-form-label" for="cashid"><?=lang('Global.cash')?></label>
                        <div class="uk-form-controls">
                            <select class="uk-select"  name="cashid" id="cashid" reqired>
                                <option  selected hidden disabled>-- <?=lang('Global.choosewallet')?> --</option>
                                <?php
                                foreach ($cash as $cas) {                                    
                                    if (($cas['outletid'] === '0') || ($cas['outletid'] === $outletPick)) {
                                        echo '<option value="'.$cas['id'].'">'.$cas['name'].'</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="uk-margin-bottom">
                        <div uk-form-custom="target: > * > span:first-child">
                            <select aria-label="Custom controls" name="cash">
                                <option selected hidden disabled>Choose Type</option>
                                <option value="0">Cash In</option>
                                <option value="1">Cash Out</option>
                            </select>
                            <button class="uk-button uk-button-default" type="button" tabindex="-1">
                                <span></span>
                                <span uk-icon="icon: chevron-down"></span>
                            </button>
                        </div>
                    </div>

                    <div class="uk-margin-bottom">
                        <label class="uk-form-label" for="description"><?=lang('Global.description')?></label>
                        <div class="uk-form-controls">
                            <input type="text" class="uk-input <?php if (session('errors.description')) : ?>tm-form-invalid<?php endif ?>" id="description" name="description" placeholder="<?=lang('Global.description')?>" autofocus required />
                        </div>
                    </div>

                    <div class="uk-margin-bottom">
                        <label class="uk-form-label" for="quantity"><?=lang('Global.quantity')?></label>
                        <div class="uk-form-controls">
                            <input type="text" class="uk-input <?php if (session('errors.quantity')) : ?>tm-form-invalid<?php endif ?>" id="quantity" name="quantity" placeholder="<?=lang('Global.quantity')?>" autofocus required />
                        </div>
                    </div>

                    <div class="uk-margin-bottom">
                        <div class="uk-flex uk-flex-center uk-child-width-1-1" uk-grid>
                            <div class="uk-margin-left">
                                <div id="my_camera"></div>
                            </div>
                            <div class="uk-text-center">
                                <input class="image-tag" type="hidden" name="image">
                                <input class="uk-button uk-button-primary" id="btnTake" type="button" value="Take Snapshot" onClick="take_snapshot()" required>
                            </div>
                            <div class="uk-text-center">
                                <div id="results"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Webcam Cash -->
                    <script type="text/javascript">
                        Webcam.set({
                            width: 490,
                            height: 390,
                            image_format: 'jpeg',
                            jpeg_quality: 90
                        });
                    
                        Webcam.attach( '#my_camera' );

                        function take_snapshot() {
                            Webcam.snap( function(data_uri) {
                                $(".image-tag").val(data_uri);
                                document.getElementById('results').innerHTML = '<img src="'+data_uri+'"/>';
                            } );
                        }
                    </script>
                    <!-- Webcam Cash End -->

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
                <th class="uk-text-center uk-width-small"><?=lang('Global.cash')?></th>
                <th class="uk-text-center uk-width-small"><?=lang('Global.type')?></th>
                <th class="uk-text-center uk-width-small"><?=lang('Global.description')?></th>
                <th class="uk-text-center uk-width-small"><?=lang('Global.quantity')?></th>
                <th class="uk-text-center uk-width-small"><?=lang('Global.date')?></th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1 ; ?>
            <?php foreach ($trxothers as $trx) : ?>
                <tr>
                    <td class="uk-text-center"><?= $i++; ?></td>
                    <td class="uk-text-left"> 
                        <?php 
                        foreach ($users as $user){
                            if($trx['userid']===$user->id){
                                echo $user->name; 
                            }
                        }
                        ?>
                    </td>
                    <td class="uk-text-left">
                        <?php                        
                        if ($cas['outletid'] === '0') {
                            echo lang('Global.allOutlets');
                        } else {
                            foreach ($outlets as $outlet) {
                                if ($outlet['id'] === $trx['outletid']) {
                                    echo $outlet['name'];
                                }
                            }
                        }
                        ?>
                    </td>
                    <td class="uk-text-center">
                        <?php 
                        foreach ($cash as $cas){
                            if($trx['cashid']===$cas['id']){
                                echo $cas['name'];
                            }
                        }
                        ?>
                    </td>
                    <td class="uk-text-center">
                        <?php if ( $trx['type'] === "0" ){
                            echo "Cash In";
                        }else{
                            echo "Cash Out";
                        }
                        ?>   
                    </td>
                    <td class="uk-text-center">
                        <?= $trx['description'];?>
                    </td>
                    <td class="uk-text-center">
                        <?= $trx['qty'];?>
                    </td>
                    <td class="uk-text-center">
                        <?= $trx['date'];?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<!-- End Of Table Content -->

<!-- Search Engine Script -->
<script>
  $(document).ready(function () {
    $('#example').DataTable();
  });
</script>
<!-- Search Engine Script End -->

<?php } ?>

<?= $this->endSection() ?>