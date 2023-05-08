<?= $this->extend('layout') ?>
<?= $this->section('main') ?>
<div class="uk-width-1-1 uk-height-1-1" class="uk-inline">
    <div>
        <?= view('Views/Auth/_permission_message') ?>
    </div>
    <div class="uk-position-small uk-position-bottom">
        <a href="transaction" class="uk-button uk-button-primary uk-button-large uk-width-1-1 uk-light"><span class="uk-h3 tm-h3"><?=lang('Global.transaction')?></span></a>
    </div>
</div>
<?= $this->endSection() ?>