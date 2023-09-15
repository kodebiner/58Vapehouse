<?= $this->extend('layout') ?>

<?= $this->section('extraScript') ?>
<script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
<script src="js/cdn.datatables.net_1.13.4_js_jquery.dataTables.min.js"></script>
<?= $this->endSection() ?>

<?= $this->section('main') ?>

<!-- Page Heading -->
<div class="tm-card-header uk-light uk-margin-bottom">
    <div uk-grid class="uk-flex-middle">
        <div class="uk-width-1-2@m">
            <h3 class="tm-h3"><?=lang('Global.presence')?> <?=lang('Global.report')?></h3>
        </div>
    </div>
</div>

<table class="uk-table uk-table-divider uk-table-responsive uk-margin-top" id="example">
    <thead>
        <tr>
            <th class="uk-text-large uk-text-bold">date</th>
            <th class="uk-text-center uk-text-large uk-text-bold">status</th>
            <th class="uk-text-large uk-text-center uk-text-bold"><?=lang('Global.photo')?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($presences as $presence){ ?>
            <tr>
                <td style="color:white;"><?=$presence['datetime']?></td>
                <td class="uk-text-left" style="color:white;">
                    <?php if ( $presence['status'] === "1"){
                        echo lang('Global.checkin');
                    }else{
                        echo lang('Global.checkout');
                    } ?>
                </td>
                <td class="uk-text-left uk-text-center"><img class="uk-preserve-width uk-border-circle" id="img<?php echo $presence['id'];?>" src="img/profile<?php echo $presence['photo'];?>" width="40" height="40" alt=""></td>
            </tr>

            <div id="modal-id<?php echo $presence['id'];?>" class="uk-flex-top" uk-modal>
                <div class="uk-modal-dialog uk-width-auto uk-margin-auto-vertical">
                    <button class="uk-modal-close-outside" type="button" uk-close></button>
                    <img src="img/profile<?php echo $presence['photo'];?>" width="1800" height="1200" alt="">
                </div>
            </div>

            <script>
                $("#img<?php echo $presence['id'];?>").click(function(){
                    UIkit.modal('#modal-id<?php echo $presence['id'];?>').toggle();
                });
            </script>
        <?php } ?>
    </tbody>
</table>
        

<!-- End Of Page Heading -->
<script>
    $(document).ready(function () {
        $('#example').DataTable();
    });
</script>
<?= view('Views/Auth/_message_block') ?>

<?= $this->endSection() ?>