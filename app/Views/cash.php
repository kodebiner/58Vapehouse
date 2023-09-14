<?= $this->extend('layout') ?>

<?= $this->section('extraScript') ?>
    <script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
    <script src="js/cdn.datatables.net_1.13.4_js_jquery.dataTables.min.js"></script>
    <script src="js/cdnjs.cloudflare.com_ajax_libs_webcamjs_1.0.25_webcam.min.js"></script>
<?= $this->endSection() ?>

<?= $this->section('main') ?>

<!-- Page Heading -->
<div class="tm-card-header uk-light">
    <div uk-grid class="uk-flex-middle">
        <div class="uk-width-1-2@s">
            <h3 class="tm-h3"><?=lang('Global.cashinoutList')?></h3>
        </div>

        <?php if ($outletPick != null) {
            if (empty($dailyreports)) { ?>
                <!-- Button Trigger Modal Open -->
                <div class="uk-width-1-2@s uk-text-right">
                    <button type="button" class="uk-button uk-button-primary uk-preserve-color" uk-toggle="target: #open"><?=lang('Global.open')?></button>
                </div>
                <!-- Button Trigger Modal Open End -->
            <?php } else {
                foreach ($dailyreports as $dayrep) {
                    if (!empty($dailyreports) && ($dayrep['dateclose'] < $today)) { ?>
                        <div class="uk-width-1-2@s uk-child-width-auto uk-flex-middle uk-flex-right uk-margin-remove-left" uk-grid>
                            <!-- Button Trigger Modal Close -->
                            <div>
                                <button type="button" class="uk-button uk-button-danger uk-preserve-color" uk-toggle="target: #close-<?= $dayrep['id'] ?>"><?=lang('Global.close')?></button>
                            </div>
                            <!-- Button Trigger Modal Close End -->

                            <!-- Button Trigger Modal Withdraw -->
                            <div>
                                <button type="button" class="uk-button uk-button-primary uk-preserve-color" uk-toggle="target: #withdraw"><?=lang('Global.withdraw')?></button>
                            </div>
                            <!-- Button Trigger Modal Withdraw End -->
                            
                            <!-- Button Trigger Modal CashInOut -->
                            <div>
                                <button type="button" class="uk-button uk-button-primary uk-preserve-color" uk-toggle="target: #tambahdata"><?=lang('Global.cashin/out')?></button>
                            </div>
                            <!-- Button Trigger Modal CashInOut End -->
                        </div>
                    <?php }
                }
            }
        } ?>
    </div>
</div>
<!-- End Of Page Heading -->

<?= view('Views/Auth/_message_block') ?>

<!-- Modal Close NOT DONE YET -->
<?php foreach ($dailyreports as $dayrep) { ?>
    <div uk-modal class="uk-flex-top" id="close-<?= $dayrep['id'] ?>">
        <div class="uk-modal-dialog uk-margin-auto-vertical">
            <div class="uk-modal-content">
                <div class="uk-modal-header">
                    <h3 class="tm-h2 uk-text-center"><?=lang('Global.close')?></h3>
                </div>
                <div class="uk-modal-body">
                    <form class="uk-form-stacked" role="form" action="dayrep/close/<?= $dayrep['id'] ?>" method="post">
                        <?= csrf_field() ?>

                        <hr>

                        <div class="uk-margin">
                            <button type="submit" class="uk-button uk-button-primary uk-width-1-1" style="border-radius: 10px;"><?=lang('Global.close')?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<!-- Modal Close End -->

<!-- Modal Open -->
<div uk-modal class="uk-flex-top" id="open">
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <div class="uk-modal-content">
            <div class="uk-modal-header">
                <h3 class="tm-h2 uk-text-center"><?=lang('Global.initialcash')?></h3>
            </div>
            <div class="uk-modal-body">
                <form class="uk-form-stacked" role="form" action="dayrep/open" method="post">
                    <?= csrf_field() ?>

                    <div class="uk-form-controls">
                        <input type="number" class="uk-input uk-form-large uk-text-center" style="border-radius: 10px;" id="initialcash" name="initialcash" placeholder="<?=lang('Global.initialcash')?>" required />
                    </div>

                    <hr>

                    <div class="uk-margin">
                        <button type="submit" class="uk-button uk-button-primary uk-width-1-1" style="border-radius: 10px;"><?=lang('Global.open')?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Modal Open End -->

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
                        <div uk-form-custom="target: > * > span:first-child">
                            <select aria-label="Custom controls" name="cash">
                                <option selected hidden disabled>Choose Type</option>
                                <option value="0"><?= lang('Global.cashin') ?></option>
                                <option value="1"><?= lang('Global.cashout') ?></option>
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

<!-- Modal Withdraw -->
<div class="uk-flex-top" id="withdraw" uk-modal>
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <button class="uk-modal-close-default" type="button" uk-close></button>
        <div class="uk-modal-header">
            <h2 class="uk-modal-title"><?= lang('Global.withdraw') ?></h2>
        </div>
        <div class="uk-modal-body">
            <form class="uk-form-horizontal uk-margin-large" action="cashinout/withdraw" method="post">
                <div class="uk-margin">
                    <label class="uk-form-label"><?= lang('Global.name') ?></label>
                    <div class="uk-form-controls">
                        <div class="uk-inline uk-width-1-1">
                            <span class="uk-form-icon" uk-icon="icon: user"></span>
                            <input class="uk-input" type="text" placeholder="Name" id="name" name="name" aria-label="Not clickable icon">
                        </div>
                    </div>                                    
                </div>

                <div class="uk-margin">
                    <label class="uk-form-label" for="form-horizontal-text"><?= lang('Global.quantity') ?></label>
                    <div class="uk-form-controls">
                        <div class="uk-inline uk-width-1-1">
                            <span class="uk-form-icon" uk-icon="icon: database"></span>
                            <input class="uk-input" min="0" name="value" type="number" placeholder="<?= lang('Global.value') ?>" aria-label="Not clickable icon">
                        </div>
                    </div>
                </div>

                <div class="uk-margin-bottom">
                    <div class="uk-flex uk-flex-center uk-child-width-1-1" uk-grid>
                        <div class="uk-margin-left">
                            <div id="withdraw_camera"></div>
                        </div>
                        <div class="uk-text-center">
                            <input class="image-tag" type="hidden" name="image">
                            <input class="uk-button uk-button-primary" id="btnTake" type="button" value="Take Snapshot" onClick="withdraw_snapshot()" required>
                        </div>
                        <div class="uk-text-center">
                            <div id="withdraw_results"></div>
                        </div>
                    </div>
                </div>

                <!-- Webcam Withdraw -->
                <script type="text/javascript">
                    Webcam.set({
                        width: 490,
                        height: 390,
                        image_format: 'jpeg',
                        jpeg_quality: 90
                    });
                
                    Webcam.attach( '#withdraw_camera' );

                    function withdraw_snapshot() {
                        Webcam.snap( function(data_uri) {
                            $(".image-tag").val(data_uri);
                            document.getElementById('withdraw_results').innerHTML = '<img src="'+data_uri+'"/>';
                        } );
                    }
                </script>
                <!-- Webcam Withdraw End -->

                <div class="uk-modal-footer uk-text-right">
                    <button class="uk-button uk-button-primary" type="submit" value="submit"><?= lang('Global.save') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal Withdraw End -->

<!-- Table Of Content -->
<div class="uk-overflow-auto uk-margin">
    <table class="uk-table uk-table-justify uk-table-middle uk-table-divider uk-light" id="example" style="width:100%">
        <thead>
            <tr>
                <th class="uk-text-center uk-width-small">No</th>
                <th class=""><?=lang('Global.description')?></th>
                <th class=""><?=lang('Global.outlet')?></th>
                <th class="uk-text-center"><?=lang('Global.type')?></th>
                <th class="uk-text-center"><?=lang('Global.date')?></th>
                <th class=""><?=lang('Global.name')?></th>
                <th class="uk-text-center"><?=lang('Global.cash')?></th>
                <th class="uk-text-center"><?=lang('Global.quantity')?></th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1 ; ?>
            <?php foreach ($trxothers as $trx) : ?>
                <tr>
                    <td class="uk-text-center"><?= $i++; ?></td>
                    <td class="">
                        <?= $trx['description'];?>
                    </td>
                    <td class="uk-text-left">
                        <?php if ($trx['outletid'] === '0') {
                            echo lang('Global.allOutlets');
                        } else {
                            foreach ($outlets as $outlet) {
                                if ($outlet['id'] === $trx['outletid']) {
                                    echo $outlet['name'];
                                }
                            }
                        } ?>
                    </td>
                    <td class="uk-text-center">
                        <?php if ($trx['type'] === "0") {
                            echo '<div class="uk-text-success" style="border-style: solid; border-color: #32d296;">'.'Cash In'.'</div>';
                        } else {
                            echo '<div class="uk-text-danger" style="border-style: solid; border-color: #f0506e;">'.'Cash Out'.'</div>';
                        } ?>   
                    </td>
                    <td class="uk-text-center">
                        <?= $trx['date'];?>
                    </td>
                    <td class="uk-text-left"> 
                        <?php foreach ($users as $user) {
                            if ($trx['userid']===$user->id) {
                                echo $user->name; 
                            }
                        } ?>
                    </td>
                    <td class="uk-text-center">
                        <?php foreach ($cash as $cas) {
                            if ($trx['cashid'] === $cas['id']) {
                                echo $cas['name'];
                            }
                        } ?>
                    </td>
                    <td class="uk-text-center">
                        <?= $trx['qty'];?>
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

<?= $this->endSection() ?>