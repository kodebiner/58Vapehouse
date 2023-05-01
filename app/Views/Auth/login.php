<?= $this->extend($config->viewLayout) ?>
<?= $this->section('main') ?>

<div class="uk-flex uk-flex-middle uk-panel uk-panel-scrollable" uk-height-viewport="offset-bottom: footer;">
	<div class="uk-width-1-1 uk-flex uk-flex-center">
        <div class="uk-card uk-card-default uk-card-body uk-width-1-4@l uk-padding-small">
			<div>
				<div class="uk-width-1-1">
					<h1 class="uk-text-center" style="color: #000;"><?=lang('Auth.loginTitle')?></h1>
				</div>
			</div>
			<div class="uk-padding-small">
                <?= view('Views/Auth/_message_block') ?>
                
				<form class="uk-form-stacked" action="<?= url_to('login') ?>" method="post">
					<?= csrf_field() ?>

                    <div class="uk-margin">
                        <?php if ($config->validFields === ['email']): ?>
                            <label for="login"><?=lang('Auth.email')?></label>
                            <div class="uk-form-controls">
                                <input type="email" class="uk-input <?php if (session('errors.login')) : ?>tm-form-invalid<?php endif ?>" name="login" placeholder="<?=lang('Auth.email')?>" value="<?= old('login') ?>" required>
                            </div>
                            <div class="uk-text-small uk-text-italic uk-text-danger">
                                <?= session('errors.login') ?>
                            </div>

                        <?php else: ?>
                            <label for="login"><?=lang('Auth.emailOrUsername')?></label>
                            <div class="uk-form-controls">
                                <input type="text" class="uk-input <?php if (session('errors.login')) : ?>tm-form-invalid<?php endif ?>" name="login" placeholder="<?=lang('Auth.emailOrUsername')?>" value="<?= old('login') ?>" required>
                            </div>
                            <div class="uk-text-small uk-text-italic uk-text-danger">
                                <?= session('errors.login') ?>
                            </div>

                        <?php endif; ?>
                    </div>
                    <div class="uk-margin">
                        <label for="password"><?=lang('Auth.password')?></label>
                        <div class="uk-form-controls">
                            <input type="password" name="password" class="uk-input  <?php if (session('errors.password')) : ?>tm-form-invalid<?php endif ?>" placeholder="<?=lang('Auth.password')?>" required>
                        </div>
                        <div class="uk-text-small uk-text-italic uk-text-danger">
                            <?= session('errors.password') ?>
                        </div>
                    </div>

                    <?php if ($config->allowRemembering): ?>
                        <div class="uk-margin">
                            <label class="form-check-label">
                                <input type="checkbox" name="remember" class="uk-checkbox" <?php if (old('remember')) : ?> checked <?php endif ?>> <?=lang('Auth.rememberMe')?>
                            </label>
                        </div>
                    <?php endif; ?>

					<div class="uk-margin">
                        <button type="submit" class="uk-button uk-button-primary"><?=lang('Auth.loginAction')?></button>
                    </div>
				</form>
                
                <?php if (($config->allowRegistration) || ($config->activeResetter)) : ?>
                    <hr>

                    <?php if ($config->allowRegistration) : ?>
                        <div><a href="<?= url_to('register') ?>"><?=lang('Auth.needAnAccount')?></a></div>
                    <?php endif; ?>
                    <?php if ($config->activeResetter): ?>
                        <div><a href="<?= url_to('forgot') ?>"><?=lang('Auth.forgotYourPassword')?></a></div>
                    <?php endif; ?>
                <?php endif; ?>

			</div>
		</div>
	</div>
</div>

<?= $this->endSection() ?>
