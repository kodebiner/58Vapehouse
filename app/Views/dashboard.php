<?= $this->extend('layout') ?>
<?= $this->section('main') ?>
<div uk-height-viewport>
    <?= view('Views/Auth/_permission_message') ?>
</div>
<?= $this->endSection() ?>