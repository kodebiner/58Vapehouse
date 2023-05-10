<?= $this->extend('layout') ?>

<?= $this->section('extraScript') ?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<?= $this->endSection() ?>

<?= $this->section('main') ?>
<!-- Page Heading -->
<div class="tm-card-header uk-light">
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
    <form class="uk-form-horizontal uk-light" method="post" role="form" action="account/update">
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
                <div class="uk-margin">
                    <label class="uk-form-label" for="photo"><?=lang('Global.photo')?></label>
                    <div id="image-container" class="uk-form-controls">
                        <input id="photo" name="photo" value="<?=$account->photo?>" hidden />
                        <div class="js-upload uk-placeholder uk-text-center">
                            <span uk-icon="icon: cloud-upload"></span>
                            <span class="uk-text-middle"><?=lang('Global.photoUploadDesc')?></span>
                            <div uk-form-custom>
                                <input type="file">
                                <span class="uk-link"><?=lang('Global.selectOne')?></span>
                            </div>
                        </div>
                        <progress id="js-progressbar" class="uk-progress" value="0" max="100" hidden></progress>
                        <script type="text/javascript">
                            var bar = document.getElementById('js-progressbar');

                            UIkit.upload('.js-upload', {
                                url: 'upload/profile',
                                multiple: false,
                                name: 'uploads',
                                method: 'POST',
                                type: 'json',

                                beforeSend: function () {
                                    console.log('beforeSend', arguments);
                                },
                                beforeAll: function () {
                                    console.log('beforeAll', arguments);
                                },
                                load: function () {
                                    console.log('load', arguments);
                                },
                                error: function () {
                                    console.log('error', arguments);
                                    var error = arguments[0].xhr.response.message.uploads;
                                    alert(error);
                                },
                                complete: function () {
                                    console.log('complete', arguments);
                                    
                                    var filename = arguments[0].response;

                                    document.getElementById('photo').value = filename;

                                    var imgContainer = document.getElementById('image-container');

                                    var displayContainer = document.createElement('div');
                                    displayContainer.setAttribute('id', 'display-container');
                                    displayContainer.setAttribute('class', 'uk-inline');

                                    var displayImg = document.createElement('img');
                                    displayImg.setAttribute('src', 'img/profile/'+filename);
                                    displayImg.setAttribute('width', '300');
                                    displayImg.setAttribute('height', '300');

                                    var closeContainer = document.createElement('div');
                                    closeContainer.setAttribute('class', 'uk-position-small uk-position-top-right');

                                    var closeButton = document.createElement('a');
                                    closeButton.setAttribute('class', 'tm-img-remove uk-border-circle');
                                    closeButton.setAttribute('onClick', 'removeImg');
                                    closeButton.setAttribute('uk-icon', 'close');

                                    closeContainer.appendChild(closeButton);
                                    displayContainer.appendChild(displayImg);
                                    displayContainer.appendChild(closeContainer);
                                    imgContainer.appendChild(displayContainer);
                                },

                                loadStart: function (e) {
                                    console.log('loadStart', arguments);

                                    bar.removeAttribute('hidden');
                                    bar.max = e.total;
                                    bar.value = e.loaded;
                                },

                                progress: function (e) {
                                    console.log('progress', arguments);

                                    bar.max = e.total;
                                    bar.value = e.loaded;
                                },

                                loadEnd: function (e) {
                                    console.log('loadEnd', arguments);

                                    bar.max = e.total;
                                    bar.value = e.loaded;
                                },

                                completeAll: function () {
                                    console.log('completeAll', arguments);                                   

                                    setTimeout(function () {
                                        bar.setAttribute('hidden', 'hidden');
                                    }, 1000);

                                    alert('<?=lang('Global.uploadComplete')?>');
                                }
                            });
                        </script>
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
                            <label class="uk-form-label uk-preserve-color" for="oldPass"><?=lang('Global.currentPass')?></label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-preserve-color" id="oldPass" name="oldPass" type="password">
                            </div>
                        </div>
                        <div class="uk-margin">
                            <label class="uk-form-label uk-preserve-color" for="newPass"><?=lang('Auth.newPassword')?></label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-preserve-color" id="newPass" name="newPass" type="password">
                            </div>
                        </div>
                        <div class="uk-margin">
                            <label class="uk-form-label uk-preserve-color" for="newPassConf"><?=lang('Auth.newPasswordRepeat')?></label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-preserve-color" id="newnewPassConfPass" name="newPassConf" type="password">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr class="uk-divider-icon"/>
        <div class="uk-margin uk-text-right">
            <button class="uk-button uk-button-primary uk-button-large uk-preserve-color" type="submit"><?=lang('Global.save')?></button>
        </div>
    </form>
</div>
<!-- End of Content -->
<?= $this->endSection() ?>