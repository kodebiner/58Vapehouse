<?= $this->extend('layout') ?>
<?= $this->section('main') ?>
<!-- Page Heading -->
<div class="tm-card-header">
  <div uk-grid class="uk-width-1-1@m uk-flex-middle">
    <div class="uk-width-1-2@m">
      <h3 class="tm-h3"><?=lang('Global.userProfile')?></h3>
    </div>
  </div>
</div>
<!-- End Of Page Heading -->

<?= view('Views/Auth/_message_block') ?>

<!-- Content -->
<div class="uk-margin">
    <form class="uk-form-horizontal">
        <div class="uk-child-width-1-2@m" uk-grid>
            <div>
                <div class="uk-margin">
                    <label class="uk-form-label" for="username"><?=lang('Auth.username')?></label>
                    <div class="uk-form-controls">
                        <input class="uk-input" id="username" name="username" type="text" value="<?=$account->username?>">
                    </div>
                </div>
                <div class="uk-margin">
                    <label class="uk-form-label" for="email"><?=lang('Auth.email')?></label>
                    <div class="uk-form-controls">
                        <input class="uk-input" id="email" name="email" type="email" value="<?=$account->email?>">
                    </div>
                </div>
                <div class="uk-margin">
                    <label class="uk-form-label" for="firstname"><?=lang('Global.firstname')?></label>
                    <div class="uk-form-controls">
                        <input class="uk-input" id="firstname" name="firstname" type="text" value="<?=$account->firstname?>">
                    </div>
                </div>
                <div class="uk-margin">
                    <label class="uk-form-label" for="lastname"><?=lang('Global.lastname')?></label>
                    <div class="uk-form-controls">
                        <input class="uk-input" id="lastname" name="lastname" type="text" value="<?=$account->lastname?>">
                    </div>
                </div>
                <div class="uk-margin">
                    <label class="uk-form-label" for="phone"><?=lang('Global.phone')?></label>
                    <div class="uk-form-controls">
                        <input class="uk-input" id="phone" name="phone" type="text" value="<?=$account->phone?>">
                    </div>
                </div>
            </div>
            <div>
                <div class="uk-card uk-card-default">
                    <div class="uk-card-header">
                        <h3 class="uk-card-title uk-margin-remove"><?=lang('Auth.resetPassword')?></h3>
                        <p class="uk-margin-remove"><?=lang('Global.resetPassDesc')?></p>
                    </div>
                    <div class="uk-card-body">
                        <div class="uk-margin">
                            <label class="uk-form-label" for="oldPass"><?=lang('Global.currentPass')?></label>
                            <div class="uk-form-controls">
                                <input class="uk-input" id="oldPass" name="oldPass" type="password">
                            </div>
                        </div>
                        <div class="uk-margin">
                            <label class="uk-form-label" for="newPass"><?=lang('Auth.newPassword')?></label>
                            <div class="uk-form-controls">
                                <input class="uk-input" id="newPass" name="newPass" type="password">
                            </div>
                        </div>
                        <div class="uk-margin">
                            <label class="uk-form-label" for="newPassConf"><?=lang('Auth.newPasswordRepeat')?></label>
                            <div class="uk-form-controls">
                                <input class="uk-input" id="newnewPassConfPass" name="newPassConf" type="password">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr class="uk-divider-icon"/>
        <div class="uk-margin uk-text-right">
            <button class="uk-button uk-button-primary uk-button-large" type="submit"><?=lang('Global.save')?></button>
        </div>
    </form>
</div>
<!-- End of Content -->
<?= $this->endSection() ?>