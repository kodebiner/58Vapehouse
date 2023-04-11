<?= $this->extend($config->viewLayout) ?>
<?= $this->section('main') ?>

<div class="uk-width-1-1 uk-panel uk-panel-scrollable" uk-height-viewport="offset-bottom: footer">
	<div class="uk-width-1-1 uk-section uk-section-small uk-flex uk-flex-center">
        <div class="uk-width-1-3@l uk-card uk-card-default">
            <div class="uk-card-body">
				<div class="uk-width-1-1">
					<h1 class="uk-text-center"><?=lang('Auth.resetYourPassword')?></h1>
				</div>

                <?= view('Myth\Auth\Views\_message_block') ?>
                <p><?=lang('Auth.enterCodeEmailPassword')?></p>

                <form class="uk-form-stacked" action="<?= url_to('reset-password') ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="uk-margin">
                        <label for="token"><?=lang('Auth.token')?></label>
                        <div class="uk-form-controls">
                            <input type="text" class="uk-input <?php if (session('errors.token')) : ?>tm-form-invalid<?php endif ?>" name="token" placeholder="<?=lang('Auth.token')?>" value="<?= old('token', $token ?? '') ?>" required>
                        </div>
                        <div class="uk-text-small uk-text-italic uk-text-danger">
                            <?= session('errors.token') ?>
                        </div>
                    </div>

                    <div class="uk-margin">
                        <label for="email"><?=lang('Auth.email')?></label>
                        <div class="uk-form-controls">
                            <input type="text" class="uk-input <?php if (session('errors.email')) : ?>tm-form-invalid<?php endif ?>" name="email" placeholder="<?=lang('Auth.email')?>" value="<?= old('email') ?>" required>
                        </div>
                        <div class="uk-text-small uk-text-italic uk-text-danger">
                            <?= session('errors.email') ?>
                        </div>
                    </div>

                    <div class="uk-margin">
                        <label for="password"><?=lang('Auth.newPassword')?></label>
                        <div class="uk-form-controls">
                            <input type="password" class="uk-input <?php if (session('errors.password')) : ?>tm-form-invalid<?php endif ?>" name="password" required>
                        </div>
                        <div class="uk-text-small uk-text-italic uk-text-danger">
                            <?= session('errors.password') ?>
                        </div>
                    </div>

                    <div class="uk-margin">
                        <label for="pass_confirm"><?=lang('Auth.newPasswordRepeat')?></label>
                        <div class="uk-form-controls">
                            <input type="password" class="uk-input <?php if (session('errors.pass_confirm')) : ?>tm-form-invalid<?php endif ?>" name="pass_confirm" required>
                        </div>
                        <div class="uk-text-small uk-text-italic uk-text-danger">
                            <?= session('errors.pass_confirm') ?>
                        </div>
                    </div>

                    <div class="uk-margin">
                        <button type="submit" class="uk-button uk-button-primary"><?=lang('Auth.resetPassword')?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>