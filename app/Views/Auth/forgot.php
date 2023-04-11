<?= $this->extend($config->viewLayout) ?>
<?= $this->section('main') ?>

<div class="uk-flex uk-flex-middle uk-panel uk-panel-scrollable" uk-height-viewport="offset-bottom: footer;">
    <div class="uk-width-1-1 uk-flex uk-flex-center">
        <div class="uk-card uk-card-default uk-card-body uk-width-1-4@l uk-padding-small">
            <p><?=lang('Auth.enterEmailForInstructions')?></p>
            <form class="uk-form-stacked" action="<?= url_to('forgot') ?>" method="post">
                <?= csrf_field() ?>
                <div class="uk-margin">
                    <label for="email"><?=lang('Auth.emailAddress')?></label>
                    <div class="uk-form-controls">
                        <input type="email" class="uk-input <?php if (session('errors.email')) : ?>tm-form-invalid<?php endif ?>" name="email" aria-describedby="emailHelp" placeholder="<?=lang('Auth.email')?>" required>
                    </div>
                    <div class="uk-text-small uk-text-italic uk-text-danger">
                        <?= session('errors.email') ?>
                    </div>
                </div>
                <div class="uk-margin">
                    <button type="submit" class="uk-button uk-button-primary"><?=lang('Auth.sendInstructions')?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>