<?= $this->extend('layout') ?>

<?= $this->section('extraScript') ?>
<link rel="stylesheet" href="css/code.jquery.com_ui_1.13.2_themes_base_jquery-ui.css">
<script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
<script src="js/code.jquery.com_jquery-3.6.0.js"></script>
<script src="js/code.jquery.com_ui_1.13.2_jquery-ui.js"></script>
<script src="js/cdn.datatables.net_1.13.4_js_jquery.dataTables.min.js"></script>
<?= $this->endSection() ?>

<?= $this->section('main') ?>

<!-- Page Heading -->
<div class="tm-card-header uk-light">
    <div uk-grid class="uk-flex-middle">
        <div class="uk-width-1-2@m">
            <h3 class="tm-h3"><?=lang('Global.promoList')?></h3>
        </div>

        <!-- Button Trigger Modal Add -->
        <div class="uk-width-1-2@m uk-text-right@m">
            <button type="button" class="uk-button uk-button-primary uk-preserve-color" uk-toggle="target: #tambahdata"><?=lang('Global.addPromo')?></button>
        </div>
        <!-- End Of Button Trigger Modal Add -->
    </div>
</div>
<!-- End Of Page Heading -->

<?= view('Views/Auth/_message_block') ?>

<!-- Modal Add -->
<div uk-modal class="uk-flex-top" id="tambahdata">
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <div class="uk-modal-content">
            <div class="uk-modal-header">
                <div class="uk-child-width-1-2" uk-grid>
                    <div>
                        <h5 class="uk-modal-title" id="tambahdata" ><?=lang('Global.addPromo')?></h5>
                    </div>
                    <div class="uk-text-right">
                        <button class="uk-modal-close uk-icon-button-delete" uk-icon="icon: close;" type="button"></button>
                    </div>
                </div>
            </div>
            <div class="uk-modal-body">
                <form class="uk-form-stacked" role="form" action="promo/create" method="post">
                    <?= csrf_field() ?>

                    <div class="uk-margin-bottom">
                        <label class="uk-form-label" for="name"><?=lang('Global.name')?></label>
                        <div class="uk-form-controls">
                            <input type="text" class="uk-input <?php if (session('errors.name')) : ?>tm-form-invalid<?php endif ?>" id="name" name="name" placeholder="<?=lang('Global.name')?>" required />
                        </div>
                    </div>

                    <div id="image-container-create" class="uk-margin">
                        <label class="uk-form-label" for="photocreate"><?=lang('Global.photo')?></label>
                        <div id="image-container" class="uk-form-controls">
                            <input id="photocreate" name="photo" value="" hidden />
                            <div class="js-upload-create uk-placeholder uk-text-center">
                                <span uk-icon="icon: cloud-upload"></span>
                                <span class="uk-text-middle"><?=lang('Global.photoUploadDesc')?></span>
                                <div uk-form-custom>
                                    <input type="file">
                                    <span class="uk-link uk-preserve-color"><?=lang('Global.selectOne')?></span>
                                </div>
                            </div>
                            <progress id="js-progressbar-create" class="uk-progress" value="0" max="100" hidden></progress>
                        </div>
                    </div>
                    <script type="text/javascript">
                        var bar = document.getElementById('js-progressbar-create');

                        UIkit.upload('.js-upload-create', {
                            url: 'upload/promocreate',
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

                                if (document.getElementById('display-container-create')) {
                                    document.getElementById('display-container-create').remove();
                                };

                                document.getElementById('photocreate').value = filename;

                                var imgContainer = document.getElementById('image-container-create');

                                var displayContainer = document.createElement('div');
                                displayContainer.setAttribute('id', 'display-container-create');
                                displayContainer.setAttribute('class', 'uk-inline');

                                var displayImg = document.createElement('img');
                                displayImg.setAttribute('src', 'img/promo/'+filename);
                                displayImg.setAttribute('width', '150');
                                displayImg.setAttribute('height', '150');

                                var closeContainer = document.createElement('div');
                                closeContainer.setAttribute('class', 'uk-position-small uk-position-top-right');

                                var closeButton = document.createElement('a');
                                closeButton.setAttribute('class', 'tm-img-remove uk-border-circle');
                                closeButton.setAttribute('onClick', 'removeImgCreate()');
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

                        function removeImgCreate() {                                
                            $.ajax ({
                                type: 'post',
                                url: 'upload/removepromocreate',
                                data: {'photo': document.getElementById('photocreate').value},
                                dataType: 'json',

                                error: function() {
                                    console.log('error', arguments);
                                },

                                success:function() {
                                    console.log('success', arguments);

                                    var pesan = arguments[0].message;

                                    document.getElementById('display-container-create').remove();
                                    document.getElementById('photocreate').value = '';

                                    alert(pesan);
                                }
                            });
                        };
                    </script>

                    <div class="uk-margin-bottom">
                        <label class="uk-form-label" for="description"><?=lang('Global.description')?></label>
                        <div class="uk-form-controls">
                            <select class="uk-select" name="description" required>
                                <option name="description" value="0" >Promo</option>
                                <option name="description" value="1" >Event</option>
                            </select>
                        </div>
                    </div>

                    <div class="uk-margin-bottom">
                        <label class="uk-form-label" for="status"><?=lang('Global.status')?></label>
                        <div class="uk-form-controls uk-grid-small uk-child-width-auto uk-grid">
                            <label><input class="uk-radio" type="radio" name="status" value="1"> <?= lang('Global.active') ?></label>
                            <label><input class="uk-radio" type="radio" name="status" value="0"> <?= lang('Global.inactive') ?></label>
                        </div>
                    </div>

                    <hr>

                    <div class="uk-margin">
                        <button type="submit" class="uk-button uk-button-primary"><?=lang('Global.save')?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- End Of Modal Add -->

<!-- Table Of Content -->
<div class="uk-overflow-auto uk-margin">
    <table class="uk-table uk-table-justify uk-table-middle uk-table-divider uk-light" id="example" style="width:100%">
        <thead>
            <tr>
                <th class="uk-text-center"><?= lang('Global.detail') ?></th>
                <th class=""><?=lang('Global.name')?></th>
                <th class=""><?=lang('Global.description')?></th>
                <th class=""><?=lang('Global.status')?></th>
                <th class="uk-text-center"><?=lang('Global.action')?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($promos as $promo) { ?>
                <tr>
                    <td class="uk-flex-middle uk-text-center">
                        <a uk-icon="eye" class="uk-icon-link" uk-toggle="target: #detail-<?= $promo['id'] ?>"></a>
                    </td>
                    <td><?= $promo['name']; ?></td>
                    <td>
                        <?php if ($promo['description'] === "0") {
                            echo "Promo";
                        } else {
                            echo "Event";
                        } ?>
                    </td>
                    <td>
                        <?php if ($promo['status'] === "0") {
                            echo "Inactive";
                        } else {
                            echo "Active";
                        } ?>
                    </td>
                    <td class="uk-child-width-auto uk-flex-center uk-grid-row-small uk-grid-column-small" uk-grid>
                        <!-- Button Trigger Modal Edit -->
                        <div>
                            <a class="uk-icon-button" uk-icon="pencil" uk-toggle="target: #editdata<?= $promo['id'] ?>"></a>
                        </div>
                        <!-- End Of Button Trigger Modal Edit -->

                        <!-- Button Delete -->
                        <div>
                            <a uk-icon="trash" class="uk-icon-button-delete" href="promo/delete/<?= $promo['id'] ?>" onclick="return confirm('<?=lang('Global.deleteConfirm')?>')"></a>
                        </div>
                        <!-- End Of Button Delete -->
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<!-- End Of Table Content -->

<!-- Modal Edit -->
<?php foreach ($promos as $promo) { ?>
    <div uk-modal class="uk-flex-top" id="editdata<?= $promo['id'] ?>">
        <div class="uk-modal-dialog uk-margin-auto-vertical">
            <div class="uk-modal-content">
                <div class="uk-modal-header">
                    <div class="uk-child-width-1-2" uk-grid>
                        <div>
                            <h5 class="uk-modal-title" id="editdata"><?=lang('Global.updateData')?></h5>
                        </div>
                        <div class="uk-text-right">
                            <button class="uk-modal-close uk-icon-button-delete" uk-icon="icon: close;" type="button"></button>
                        </div>
                    </div>
                </div>

                <div class="uk-modal-body">
                    <form class="uk-form-stacked" role="form" action="promo/update/<?= $promo['id'] ?>" method="post">
                        <?= csrf_field() ?>

                        <div class="uk-margin-bottom">
                            <label class="uk-form-label" for="name"><?=lang('Global.name')?></label>
                            <div class="uk-form-controls">
                                <input type="text" class="uk-input" id="name" name="name" value="<?= $promo['name']; ?>" />
                            </div>
                        </div>

                        <div id="image-container-edit-<?=$promo['id']?>" class="uk-margin">
                            <label class="uk-form-label" for="photocreate"><?=lang('Global.photo')?></label>
                            <div id="image-container-<?=$promo['id']?>" class="uk-form-controls">
                                <input id="photoedit<?=$promo['id']?>" value="<?= $promo['photo']; ?>" hidden />
                                <div class="js-upload-edit-<?=$promo['id']?> uk-placeholder uk-text-center">
                                    <span uk-icon="icon: cloud-upload"></span>
                                    <span class="uk-text-middle"><?=lang('Global.photoUploadDesc')?></span>
                                    <div uk-form-custom>
                                        <input type="file">
                                        <span class="uk-link uk-preserve-color"><?=lang('Global.selectOne')?></span>
                                    </div>
                                </div>
                                <progress id="js-progressbar-edit-<?=$promo['id']?>" class="uk-progress" value="0" max="100" hidden></progress>
                                <?php if (!empty($promo['photo'])) { ?>
                                    <div id="display-container-edit-<?=$promo['id']?>" class="uk-inline">
                                        <img src="img/promo/<?=$promo['photo']?>" width="150" height="150" />
                                        <div class="uk-position-small uk-position-top-right">
                                            <a class="tm-img-remove uk-border-circle" uk-icon="close" onclick="removeImgEdit<?=$promo['id']?>()"></a>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        
                        <script type="text/javascript">
                            var bar = document.getElementById('js-progressbar-edit-<?=$promo['id']?>');

                            UIkit.upload('.js-upload-edit-<?=$promo['id']?>', {
                                url: 'upload/promoedit/<?=$promo['id']?>',
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

                                    if (document.getElementById('display-container-edit-<?=$promo['id']?>')) {
                                        document.getElementById('display-container-edit-<?=$promo['id']?>').remove();
                                    };

                                    document.getElementById('photoedit<?=$promo['id']?>').value = filename;

                                    var imgContainer = document.getElementById('image-container-edit-<?=$promo['id']?>');

                                    var displayContainer = document.createElement('div');
                                    displayContainer.setAttribute('id', 'display-container-edit-<?=$promo['id']?>');
                                    displayContainer.setAttribute('class', 'uk-inline');

                                    var displayImg = document.createElement('img');
                                    displayImg.setAttribute('src', 'img/promo/'+filename);
                                    displayImg.setAttribute('width', '150');
                                    displayImg.setAttribute('height', '150');

                                    var closeContainer = document.createElement('div');
                                    closeContainer.setAttribute('class', 'uk-position-small uk-position-top-right');

                                    var closeButton = document.createElement('a');
                                    closeButton.setAttribute('class', 'tm-img-remove uk-border-circle');
                                    closeButton.setAttribute('onClick', 'removeImgEdit<?=$promo['id']?>()');
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

                            function removeImgEdit<?=$promo['id']?>() {                                
                                $.ajax ({
                                    type: 'post',
                                    url: 'upload/removepromoedit/<?=$promo['id']?>',
                                    data: {'photo': document.getElementById('photoedit<?=$promo['id']?>').value},
                                    dataType: 'json',

                                    error: function() {
                                        console.log('error', arguments);
                                    },

                                    success:function() {
                                        console.log('success', arguments);

                                        var pesan = arguments[0].message;

                                        document.getElementById('display-container-edit-<?=$promo['id']?>').remove();
                                        document.getElementById('photoedit<?=$promo['id']?>').value = '';

                                        alert(pesan);
                                    }
                                });
                            };
                        </script>

                        <div class="uk-margin-bottom">
                            <label class="uk-form-label" for="description"><?=lang('Global.description')?></label>
                            <div class="uk-form-controls">
                                <select class="uk-select" name="description" required>
                                    <option disabled><?=lang('Global.description')?></option>
                                    <option name="description" value="0" <?php if ($promo['description'] === "0") {echo 'selected';} ?>>Promo</option>
                                    <option name="description" value="1" <?php if ($promo['description'] === "1") {echo 'selected';} ?>>Event</option>
                                </select>
                            </div>
                        </div>

                        <div class="uk-margin-bottom">
                            <label class="uk-form-label" for="status"><?=lang('Global.status')?></label>
                            <div class="uk-form-controls uk-grid-small uk-child-width-auto uk-grid">
                                <label><input class="uk-radio" type="radio" name="status" value="1" <?php if ($promo['status'] === '1') { echo 'checked'; } ?>> <?= lang('Global.active') ?></label>
                                <label><input class="uk-radio" type="radio" name="status" value="0" <?php if ($promo['status'] === '0') { echo 'checked'; } ?>> <?= lang('Global.inactive') ?></label>
                            </div>
                        </div>

                        <hr>

                        <div class="uk-margin">
                            <button type="submit" class="uk-button uk-button-primary"><?=lang('Global.save')?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<!-- End Of Modal Edit -->

<!-- Modal Details -->
<?php foreach ($promos as $promo) { ?>
    <div uk-modal class="uk-flex-top" id="detail-<?= $promo['id'] ?>">
        <div class="uk-modal-dialog uk-margin-auto-vertical">
            <div class="uk-modal-content">
                <div class="uk-modal-header">
                    <div class="uk-child-width-1-2" uk-grid>
                        <div>
                            <h5 class="uk-modal-title" id="editdata"><?=lang('Global.detail')?></h5>
                        </div>
                        <div class="uk-text-right">
                            <button class="uk-modal-close uk-icon-button-delete" uk-icon="icon: close;" type="button"></button>
                        </div>
                    </div>
                </div>

                <div class="uk-modal-body">
                    <div class="uk-child-width-1-2@m uk-flex-middle" uk-grid>
                        <div>
                            <?php if (!empty($promo['photo'])) { ?>
                                <img class="uk-width-1-1" src="img/promo/<?= $promo['photo'] ?>" />
                            <?php } else { ?>
                                <svg x="0px" y="0px" viewBox="0 0 300 300" style="enable-background:new 0 0 300 300;" xml:space="preserve">
                                    <g>
                                        <defs>
                                            <rect id="SVGID_1_" y="0" width="300" height="300"/>
                                        </defs>
                                        <clipPath id="SVGID_00000065759931020451687440000009539297437584060839_">
                                            <use xlink:href="#SVGID_1_"  style="overflow:visible;"/>
                                        </clipPath>
                                        <g style="clip-path:url(#SVGID_00000065759931020451687440000009539297437584060839_);">
                                            <path class="dummyproduct" d="M10.43,99.92c-10.73-27.36,4.25-69.85,30.19-85.78C51.01,7.77,77-5.17,108.81,30.24
                                            c-2.16,0.65-4.26,1.55-6.29,2.7c-3.02,1.75-5.49,4.04-7.57,6.58C83.12,26.95,67.17,17.08,49.17,28.13
                                            C34,37.46,23.24,60.45,23.28,79.62c-0.03,5.15,0.77,10.05,2.42,14.32l4.75,11.66c6.41,15.42,12.34,29.6,12.34,46.6
                                            c-0.03,11.87-2.89,25.14-10.44,41.17c-1.05,2.23-1.96,5.97-1.96,9.8c0,2.31,0.29,4.66,1.16,6.73c1.13,2.73,3.09,4.44,5.9,5.59
                                            c2.16,0.28,10.31,0.86,17.02-5.79c6.56-6.54,13.06-21.9,6.78-58.08C50.43,89.07,75.8,68.22,87.2,62.18
                                            c15.23-8.09,33.99-5.98,45.6,5.15c3.3,3.14,3.38,8.34,0.23,11.6c-3.13,3.26-8.35,3.37-11.59,0.23c-5.55-5.31-16.45-7.86-26.56-2.5
                                            c-8.25,4.37-26.43,20.18-17.46,72.17c6.01,34.86,2.08,59.32-11.64,72.76c-13.81,13.43-31.7,10.1-32.45,9.95l-0.67-0.13l-0.63-0.24
                                            c-7.34-2.73-12.76-7.95-15.68-15.08c-4.14-10.12-2.41-22.24,1.16-29.72c15.27-32.43,8.34-49.15-2.2-74.47L10.43,99.92z"/>
                                            <g>
                                                <path class="dummyproduct" d="M289.03,204.6L222.63,89.6c0,0-8.25-9.16-7.65-8.69l-10.29-6.98l-72.37-38.31
                                                c-7.64-4.21-17.21-3.87-25.53,0.91c-6.82,3.93-11.33,10.31-12.87,17.21c14.44-4.1,30.01-1.11,40.99,8.29
                                                c7.23,0.26,14.23,3.89,18.08,10.64c6.07,10.47,2.46,23.86-7.98,29.88c-10.47,6.04-23.89,2.46-29.92-8.01
                                                c-2.57-4.48-3.27-9.48-2.52-14.24c-8.67-4.82-20.11,2.86-20.51,5.7c-0.51,3.49-1.94,54.29-1.94,54.29s0.98,10.4,1.08,11.45
                                                c0.21,0.64,3.82,11.58,3.82,11.58l66.4,114.96c4.06,7.05,10.6,12.07,18.43,14.18c7.8,2.1,15.98,1.03,22.98-3.03l75.14-43.35
                                                C292.39,237.71,297.39,219.1,289.03,204.6z M210.47,157.72l-6.24,6.9c-3.34-3.82-7.36-5.93-11.95-6.25
                                                c-2.17-0.16-4.25,0-6.22,0.36l-4.6-8.04C191.65,146.98,201.33,149.28,210.47,157.72z M166.64,189.62c-0.76-0.98-1.46-2-2.1-3.11
                                                c-0.8-1.4-1.42-2.78-1.96-4.18c-2.24-7.52-0.14-16.05,5.35-23.07c0.61-0.7,1.29-1.38,1.99-1.97l4.57,7.98
                                                c-0.08,0.13-0.17,0.27-0.25,0.38c-4.51,5.03-5.96,11.66-3.05,16.74c2.99,5.22,9.6,7.28,16.39,5.77l4.98,8.7
                                                C182.41,199.07,172.43,196.42,166.64,189.62z M182.01,224.96l6.55-6.26c6.45,6.06,13.24,8.32,20.42,6.89l4.7,8.22
                                                C202.67,237.11,192.12,234.18,182.01,224.96z M220.06,237.4l-50.01-87.43l5.74-3.28l50,87.43L220.06,237.4z M226.2,226.44
                                                c-0.29,0.25-0.55,0.46-0.85,0.69l-4.53-7.92c0.51-0.43,0.96-0.9,1.4-1.35c2.16-1.94,3.64-4,4.5-6.25
                                                c2.1-4.48,2.31-9.32,0.06-13.25c-3.65-6.39-12.44-8.43-21.03-5.49l-4.84-8.41c14.3-3.2,27.1-0.45,32.68,9.28
                                                C239,203.19,235.61,215.91,226.2,226.44z"/>
                                            </g>
                                        </g>
                                    </g>
                                </svg>
                            <?php } ?>
                        </div>
                        <div>
                            <div class="uk-h3 tm-h3"><?= lang('Global.description') ?></div>
                            <h6 class="uk-h4 tm-h4 uk-margin-remove">
                                <?php if ($promo['description'] === "0") {
                                    echo "Promo";
                                } else {
                                    echo "Event";
                                } ?>
                            </h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<!-- End Of Modal Details -->

<!-- Data Tables Script -->
<script>
    $(document).ready(function () {
        $('#example').DataTable();
    });
</script>
<!-- Data Tables Script End -->
<?= $this->endSection() ?>