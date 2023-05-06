<?= $this->extend('layout') ?>
<?= $this->section('main') ?>
<!-- Page Heading -->
<div class="tm-card-header">
  <div uk-grid class="uk-width-1-1@m uk-flex-middle">
    <div class="uk-width-1-2@m">
      <h3 class="tm-h3"><?=lang('Global.businessInfo')?></h3>
    </div>
  </div>
</div>
<!-- End Of Page Heading -->

<?= view('Views/Auth/_message_block') ?>

<!-- Content -->
<div class="uk-margin">
    <form class="uk-form-horizontal" role="form" method="post" action="business/save">
        <div class="uk-child-width-1-2@m uk-grid-row-small" uk-grid>
            <div>
                <div class="uk-margin">
                    <label class="uk-form-label" for="name"><?=lang('Global.bizName')?></label>
                    <div class="uk-form-controls">
                        <input class="uk-input" id="name" name="name" type="text" value="<?=$gconfig['bizname']?>" required>
                    </div>
                </div>
                <h4 class="tm-h4">Poin Member</h4>
                <div class="uk-margin">
                    <label class="uk-form-label" for="poinvalue"><?=lang('Global.poinValue')?></label>
                    <div class="uk-form-controls">
                        <input class="uk-input uk-form-width-small" id="poinvalue" name="poinvalue" type="number" value="<?=$gconfig['poinvalue']?>" required>
                        <div class="uk-h6 uk-margin-remove"><?=lang('Global.poinValueDesc')?></div>
                    </div>
                </div>
                <div class="uk-margin">
                    <label class="uk-form-label" for="poinorder"><?=lang('Global.poinOrder')?></label>
                    <div class="uk-form-controls">
                        <input class="uk-input uk-form-width-small" id="poinorder" name="poinorder" type="number" value="<?=$gconfig['poinorder']?>" required>
                        <div class="uk-h6 uk-margin-remove"><?=lang('Global.poinOrderDesc')?></div>
                    </div>
                </div>
                <h4 class="tm-h4"><?=lang('Global.discount')?></h4>
                <div class="uk-margin">
                    <label class="uk-form-label" for="memberdisctype"><?=lang('Global.discountType')?></label>
                    <div class="uk-form-controls">
                        <div class="uk-margin-small">
                            <label><input class="uk-radio" id="memberdisctype" name="memberdisctype" type="radio" value="0" <?php if ($gconfig['memberdisctype'] === '0') {echo 'checked';} ?> required> Rp</label>
                        </div>
                        <div class="uk-margin-small">
                            <label><input class="uk-radio" id="memberdisctype" name="memberdisctype" type="radio" value="1" <?php if ($gconfig['memberdisctype'] === '1') {echo 'checked';} ?>> %</label>
                        </div>
                    </div>
                </div>
                <div class="uk-margin">
                    <label class="uk-form-label" for="memberdisc"><?=lang('Global.memberDiscount')?></label>
                    <div class="uk-form-controls">
                        <input class="uk-input uk-form-width-small" id="memberdisc" name="memberdisc" type="number" value="<?=$gconfig['memberdisc']?>" required>
                    </div>
                </div>
                <h4 class="tm-h4"><?=lang('Global.tax')?></h4>
                <div class="uk-margin">
                    <label class="uk-form-label" for="ppn"><?=lang('Global.vat')?></label>
                    <div class="uk-form-controls">
                        <input class="uk-input uk-form-width-xsmall" id="ppn" name="ppn" type="number" value="<?=$gconfig['ppn']?>" required> %
                    </div>
                </div>
            </div>
        </div>
        <hr class="uk-divider-icon" />
        <div class="uk-text-right">
            <button class="uk-button uk-button-primary" role="button" type="submit"><?=lang('Global.save')?></button>
        </div>
    </form>
</div>
<!-- End Of Content -->
<?= $this->endSection() ?>