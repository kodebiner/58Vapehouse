<?= $this->extend($config->viewLayout) ?>
<?= $this->section('main') ?>

<div class="uk-width-1-1 uk-panel uk-panel-scrollable" uk-height-viewport="offset-bottom: footer">
	<div class="uk-width-1-1 uk-section uk-section-small uk-flex uk-flex-center">
        <div class="uk-width-1-3@l uk-card uk-card-default">
            <div class="uk-card-body">
				<div class="uk-width-1-1">
					<h1 class="uk-text-center"><?=lang('Auth.register')?></h1>
				</div>

                <?= view('Views/Auth/_message_block') ?>

                <form class="uk-form-stacked" action="<?= url_to('register') ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="uk-margin">
                        <label for="firstname"><?=lang('Global.firstname')?></label>
                        <div class="uk-form-controls">
                            <input type="text" class="uk-input <?php if (session('errors.firstname')) : ?>tm-form-invalid<?php endif ?>" name="firstname" placeholder="<?=lang('Global.firstname')?>" value="<?= old('firstname') ?>" required>
                        </div>
                    </div>

                    <div class="uk-margin">
                        <label for="lastname"><?=lang('Global.lastname')?></label>
                        <div class="uk-form-controls">
                            <input type="text" class="uk-input <?php if (session('errors.lastname')) : ?>tm-form-invalid<?php endif ?>" name="lastname" placeholder="<?=lang('Global.lastname')?>" value="<?= old('lastname') ?>" required>
                        </div>
                    </div>

                    <div class="uk-margin">
                        <label for="email"><?=lang('Auth.email')?></label>
                        <div class="uk-form-controls">
                            <input type="email" class="uk-input <?php if (session('errors.email')) : ?>tm-form-invalid<?php endif ?>" name="email" aria-describedby="emailHelp" placeholder="<?=lang('Auth.email')?>" value="<?= old('email') ?>" required>
                        </div>
                        <small id="emailHelp" class="form-text text-muted"><?=lang('Auth.weNeverShare')?></small>
                    </div>

                    <div class="uk-margin">
                        <label for="phone"><?=lang('Global.phone')?></label>
                        <div class="uk-form-controls">
                            <input type="phone" class="uk-input <?php if (session('errors.phone')) : ?>tm-form-invalid<?php endif ?>" name="phone" placeholder="<?=lang('Global.phone')?>" value="<?= old('phone') ?>" required>
                        </div>
                    </div>

                    <div class="uk-margin">
                        <label for="username"><?=lang('Auth.username')?></label>
                        <div class="uk-form-controls">
                            <input type="text" class="uk-input <?php if (session('errors.username')) : ?>tm-form-invalid<?php endif ?>" name="username" placeholder="<?=lang('Auth.username')?>" value="<?= old('username') ?>" required>
                        </div>
                    </div>

                    <div class="uk-margin">
                        <label for="password"><?=lang('Auth.password')?></label>
                        <div class="uk-form-controls">
                            <input type="password" name="password" class="uk-input <?php if (session('errors.password')) : ?>tm-form-invalid<?php endif ?>" placeholder="<?=lang('Auth.password')?>" autocomplete="off" required>
                        </div>
                    </div>

                    <div class="uk-margin">
                        <label for="pass_confirm"><?=lang('Auth.repeatPassword')?></label>
                        <div class="uk-form-controls">
                            <input type="password" name="pass_confirm" class="uk-input <?php if (session('errors.pass_confirm')) : ?>tm-form-invalid<?php endif ?>" placeholder="<?=lang('Auth.repeatPassword')?>" autocomplete="off" required>
                        </div>
                    </div>

                    <div class="uk-margin">
                        <button type="submit" class="uk-button uk-button-primary"><?=lang('Auth.register')?></button>
                    </div>
                </form>

                <hr>

                <p><?=lang('Auth.alreadyRegistered')?> <a href="<?= url_to('login') ?>"><?=lang('Auth.signIn')?></a></p>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
