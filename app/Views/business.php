<?= $this->extend('layout') ?>

<?= $this->section('extraScript') ?>
<script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
<?= $this->endSection() ?>

<?= $this->section('main') ?>
<!-- Page Heading -->
<div class="tm-card-header uk-light">
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
    <form class="uk-form-horizontal uk-light" role="form" method="post" action="business/save">
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
                            <label><input class="uk-radio" id="memberdiscrp" name="memberdisctype" type="radio" value="0" <?php if ($gconfig['memberdisctype'] === '0') {echo 'checked';} ?> required> Rp</label>
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
                <h4 class="tm-h4" hidden><?=lang('Global.tax')?></h4>
                <div class="uk-margin" hidden>
                    <label class="uk-form-label" for="ppn"><?=lang('Global.vat')?></label>
                    <div class="uk-form-controls">
                        <input class="uk-input uk-form-width-xsmall" id="ppn" name="ppn" type="number" value="<?=$gconfig['ppn']?>" required> %
                    </div>
                </div>
            </div>
            <div>
                <div class="uk-card uk-card-default">
                    <div class="uk-card-header">
                        <h4 class="uk-card-title tm-h4 uk-preserve-color"><?=lang('Global.bizLogo')?></h4>
                    </div>
                    <div class="uk-card-body">
                        <div class="uk-margin">
                            <div id="image-container">
                                <input id="logo" value="<?=$gconfig['logo']?>" hidden />
                                <div class="js-upload uk-placeholder uk-text-center">
                                    <span uk-icon="icon: cloud-upload"></span>
                                    <span class="uk-text-middle"><?=lang('Global.photoUploadDesc')?></span>
                                    <div uk-form-custom>
                                        <input type="file">
                                        <span class="uk-link uk-preserve-color"><?=lang('Global.selectOne')?></span>
                                    </div>
                                </div>
                                <progress id="js-progressbar" class="uk-progress" value="0" max="100" hidden></progress>
                                <?php if (!empty($gconfig['logo'])) { ?>
                                    <div id="display-container" class="uk-inline">
                                        <img class="uk-width-1-1" src="img/<?=$gconfig['logo']?>" />
                                        <div class="uk-position-small uk-position-top-right">
                                            <a class="tm-img-remove uk-border-circle" onclick="removeImg()" uk-icon="close"></a>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <script type="text/javascript">
                            var bar = document.getElementById('js-progressbar');

                            UIkit.upload('.js-upload', {
                                url: 'upload/logo',
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

                                    if (document.getElementById('display-container')) {
                                        document.getElementById('display-container').remove();
                                    };

                                    document.getElementById('logo').value = filename;

                                    var imgContainer = document.getElementById('image-container');

                                    var displayContainer = document.createElement('div');
                                    displayContainer.setAttribute('id', 'display-container');
                                    displayContainer.setAttribute('class', 'uk-inline');

                                    var displayImg = document.createElement('img');
                                    displayImg.setAttribute('src', 'img/'+filename);
                                    displayImg.setAttribute('width', '300');
                                    displayImg.setAttribute('height', '300');

                                    var closeContainer = document.createElement('div');
                                    closeContainer.setAttribute('class', 'uk-position-small uk-position-top-right');

                                    var closeButton = document.createElement('a');
                                    closeButton.setAttribute('class', 'tm-img-remove uk-border-circle');
                                    closeButton.setAttribute('onClick', 'removeImg()');
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

                            function removeImg() {                                
                                $.ajax ({
                                    type: 'post',
                                    url: 'upload/removelogo',
                                    data: {'logo': document.getElementById('logo').value},
                                    dataType: 'json',

                                    error: function() {
                                        console.log('error', arguments);
                                    },

                                    success:function() {
                                        console.log('success', arguments);

                                        var pesan = arguments[0].message;

                                        document.getElementById('display-container').remove();
                                        document.getElementById('logo').value = '';

                                        alert(pesan);
                                    }
                                });
                            };
                        </script>
                    </div>
                </div>
            </div>
        </div>
        <hr class="uk-divider-icon" />
        <div class="uk-text-right">
            <button class="uk-button uk-button-primary uk-preserve-color" role="button" type="submit"><?=lang('Global.save')?></button>
        </div>
    </form>
</div>
<!-- End Of Content -->
<?= $this->endSection() ?>