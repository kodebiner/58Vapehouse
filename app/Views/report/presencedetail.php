<?= $this->extend('layout') ?>

<?= $this->section('extraScript') ?>
<script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
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

<div class="uk-overflow-auto">
    <table class="uk-table uk-table-divider uk-table-responsive" id="example">
        <thead>
            <tr>
                <th class="uk-text-large uk-text-bold">date</th>
                <th class="uk-text-large uk-text-bold">status</th>
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
                    <td class="uk-text-left uk-text-center">
                        <div uk-lightbox>
                            <a class="uk-inline" href="img/presence/<?= $presence['photo'] ?>">
                                <img class="uk-preserve-width uk-border-circle" id="img<?php echo $presence['id'];?>" src="img/presence/<?php echo $presence['photo'];?>" width="40" height="40" alt="<?= $presence['photo'] ?>">
                            </a>
                        </div>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <div class="uk-light">
        <?= $pager->links('reportpresecedet', 'front_full') ?>
    </div>
</div>
        
<?= view('Views/Auth/_message_block') ?>

<?= $this->endSection() ?>