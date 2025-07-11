<?= $this->extend('layout') ?>

<?= $this->section('extraScript') ?>

<link rel="stylesheet" href="css/code.jquery.com_ui_1.13.2_themes_base_jquery-ui.css">
<script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
<script src="js/code.jquery.com_jquery-3.6.0.js"></script>
<script src="js/code.jquery.com_ui_1.13.2_jquery-ui.js"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<script type="text/javascript">
    google.charts.load('current', {
        'packages': ['corechart']
    });
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'name');
        data.addColumn('number', 'stock');
        data.addRows([
            <?php foreach ($stockchart as $product) {
                $cate = $product['name'];
                $sold = $product['qty'];
                echo "['$cate',$sold],";
            } ?>
        ]);

        var options = {
            title: 'Category Percentage %'
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart'));

        chart.draw(data, options);
    }
</script>
<?= $this->endSection() ?>

<?= $this->section('main') ?>

<!-- Page Heading -->
<div class="tm-card-header uk-light">
    <div uk-grid class="uk-flex-middle">
        <div class="uk-width-1-2@m">
            <h3 class="tm-h3"><?= lang('Global.productList') ?></h3>
        </div>

        <!-- Button Trigger Modal Add -->
        <div class="uk-width-1-2@m uk-text-right@m">
            <button type="button" class="uk-button uk-button-primary uk-preserve-color uk-margin-right" uk-toggle="target: #tambahdata"><?= lang('Global.addProduct') ?></button>
            <a type="button" class="uk-button uk-button-primary uk-preserve-color" href="export/prod" target="_blank"><?= lang('Global.export') ?></a>
        </div>
        <!-- End Of Button Trigger Modal Add -->

    </div>
</div>
<!-- End Of Page Heading -->

<?= view('Views/Auth/_message_block') ?>

<div class="uk-card uk-card-default uk-card-body uk-width-1-1@m uk-margin">
    <h3 class="uk-card-title uk-text-center">- <?= lang('Global.productreport') ?> -</h3>
    <hr class="uk-divider-icon">
    <div class="uk-child-width-1-5 uk-grid-small uk-grid-match" uk-grid>
        <div>
            <p class="tm-h6"><?= lang('Global.category') ?></p>
        </div>
        <div>
            <p class="tm-h6"><?= lang('Global.stock') ?></p>
        </div>
        <div>
            <p class="tm-h6"><?= lang('Global.basePrice') ?></p>
        </div>
        <div>
            <p class="tm-h6"><?= lang('Global.capitalPrice') ?></p>
        </div>
        <div>
            <p class="tm-h6 uk-text-center"><?= lang('Global.percentage') ?></p>
        </div>
    </div>

    <div id="list"></div>
</div>
<!-- Script List -->
<script>
    var catlist = <?= $catlist ?>;
    var pageSize = <?= $countcat ?>;
    var currentPage = 1;
    var pagedResults = [];
    var totalResults = catlist.length;

    $(function() {
        function updateList() {

            var end = (currentPage * pageSize);
            var start = (end - pageSize);
            pagedResults = catlist.slice(start, end);

            $('#list').empty();

            if (currentPage <= 1) {
                $('#previous').prop("disabled", true);
            } else {
                $('#previous').prop("disabled", false);
            }

            if ((currentPage * pageSize) >= totalResults) {
                $('#next').prop("disabled", true);
            } else {
                $('#next').prop("disabled", false);
            }

            $.each(pagedResults, function(index, obj) {
                var input = obj.qty;
                var rp = parseInt(input).toLocaleString();
                var binput = obj.bqty;
                var brp = parseInt(binput).toLocaleString();
                $('#list').append("<div class='uk-child-width-1-5 uk-margin-small-top' uk-grid><div> <p class='uk-h6'>" + obj.name + " </p> </div>  <div> <p class='uk-h6'>" + obj.stock + "</p> </div> <div> <p class='uk-h6'>Rp." + brp + " </p></div> <div> <p class='uk-h6'>Rp." + rp + " </p></div>  <div> <p class='uk-h6 uk-text-center'>(" + obj.persen + "%)</p> </div></div>");
            });
        }

        updateList();
        $('#next').click(function() {

            if ((currentPage * pageSize) <= totalResults) currentPage++;
            updateList();
        });
        $('#previous').click(function() {

            if (currentPage > 1) currentPage--;
            updateList();
        });
    });
</script>
<!-- End Script List -->

<!-- Modal Add -->
<div uk-modal class="uk-flex-top" id="tambahdata">
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <div class="uk-modal-content">
            <div class="uk-modal-header">
                <div class="uk-child-width-1-2" uk-grid>
                    <div>
                        <h5 class="uk-modal-title" id="tambahdata"><?= lang('Global.addProduct') ?></h5>
                    </div>
                    <div class="uk-text-right">
                        <button class="uk-modal-close uk-icon-button-delete" uk-icon="icon: close;" type="button"></button>
                    </div>
                </div>
            </div>
            <div class="uk-modal-body">
                <form class="uk-form-stacked" role="form" action="/product/create" method="post">
                    <?= csrf_field() ?>

                    <div class="uk-margin-bottom">
                        <label class="uk-form-label" for="name"><?= lang('Global.name') ?></label>
                        <div class="uk-form-controls">
                            <input type="text" class="uk-input <?php if (session('errors.name')) : ?>tm-form-invalid<?php endif ?>" id="name" name="name" placeholder="<?= lang('Global.name') ?>" required />
                        </div>
                    </div>

                    <div class="uk-margin-bottom">
                        <label class="uk-form-label" for="link">Link Tokopedia</label>
                        <div class="uk-form-controls">
                            <input type="text" class="uk-input <?php if (session('errors.link')) : ?>tm-form-invalid<?php endif ?>" id="link" name="link" placeholder="Link Tokopedia" />
                        </div>
                    </div>

                    <div class="uk-margin-bottom">
                        <label class="uk-form-label" for="description"><?= lang('Global.description') ?></label>
                        <div class="uk-form-controls">
                            <input type="text" class="uk-input <?php if (session('errors.description')) : ?>tm-form-invalid<?php endif ?>" id="description" name="description" placeholder="<?= lang('Global.description') ?>" />
                        </div>
                    </div>

                    <div class="uk-margin">
                        <label class="uk-form-label" for="category"><?= lang('Global.category') ?></label>
                        <div class="uk-form-controls">
                            <input class="uk-input" id="category" name="category" placeholder="<?= lang('Global.category') ?>" required>
                            <input id="catid" name="catid" hidden />
                        </div>
                        <div class="uk-h6 uk-margin-remove">
                            <?= lang('Global.morecate') ?><a uk-toggle="target: #tambahcat"><?= lang('Global.addCategory') ?></a>
                        </div>

                        <script type="text/javascript">
                            $(function() {
                                var category = [
                                    <?php foreach ($category as $cat) {
                                        echo '{label:"' . $cat['name'] . ' ('. $cat['catcode'] .')' . '",idx:' . (int)$cat['id'] . '},';
                                    } ?>
                                ];
                                $("#category").autocomplete({
                                    source: category,
                                    select: function(e, i) {
                                        $("#catid").val(i.item.idx); // save selected id to hidden input
                                    },
                                    minLength: 2
                                });
                            });
                        </script>
                    </div>

                    <div class="uk-margin-bottom">
                        <label class="uk-form-label" for="brand"><?= lang('Global.brand') ?></label>
                        <div class="uk-form-controls">
                            <input class="uk-input" id="brand" name="brand" placeholder="<?= lang('Global.brand') ?>" required>
                            <input id="brandid" name="brandid" hidden />
                        </div>
                        <div class="uk-h6 uk-margin-remove">
                            <?= lang('Global.morebrand') ?><a uk-toggle="target: #tambahbrand"><?= lang('Global.addBrand') ?></a>
                        </div>

                        <script type="text/javascript">
                            $(function() {
                                var brand = [
                                    <?php foreach ($brand as $bran) {
                                        echo '{label:"' . $bran['name'] . '",idx:' . (int)$bran['id'] . '},';
                                    } ?>
                                ];
                                $("#brand").autocomplete({
                                    source: brand,
                                    select: function(e, i) {
                                        $("#brandid").val(i.item.idx); // save selected id to hidden input
                                    },
                                    minLength: 2
                                });
                            });
                        </script>
                    </div>

                    <div id="image-container-create" class="uk-margin">
                        <label class="uk-form-label" for="photocreate"><?= lang('Global.photo') ?></label>
                        <div id="image-container" class="uk-form-controls">
                            <input id="photocreate" name="photo" value="" hidden />
                            <input id="photocreatethumb" name="thumbnail" value="" hidden />
                            <div class="js-upload-create uk-placeholder uk-text-center">
                                <span uk-icon="icon: cloud-upload"></span>
                                <span class="uk-text-middle"><?= lang('Global.photoUploadDesc') ?></span>
                                <div uk-form-custom>
                                    <input type="file">
                                    <span class="uk-link uk-preserve-color"><?= lang('Global.selectOne') ?></span>
                                </div>
                            </div>
                            <progress id="js-progressbar-create" class="uk-progress" value="0" max="100" hidden></progress>
                        </div>
                    </div>
                    <script type="text/javascript">
                        var bar = document.getElementById('js-progressbar-create');

                        UIkit.upload('.js-upload-create', {
                            url: 'upload/productcreate',
                            multiple: false,
                            name: 'uploads',
                            method: 'POST',
                            type: 'json',

                            beforeSend: function() {
                                console.log('beforeSend', arguments);
                            },
                            beforeAll: function() {
                                console.log('beforeAll', arguments);
                            },
                            load: function() {
                                console.log('load', arguments);
                            },
                            error: function() {
                                console.log('error', arguments);
                                var error = arguments[0].xhr.response.message.uploads;
                                alert(error);
                            },
                            complete: function() {
                                console.log('complete', arguments);

                                var filename = arguments[0].response;

                                if (document.getElementById('display-container-create')) {
                                    document.getElementById('display-container-create').remove();
                                };

                                document.getElementById('photocreate').value = filename;
                                document.getElementById('photocreatethumb').value = 'thumb-' + filename;

                                var imgContainer = document.getElementById('image-container-create');

                                var displayContainer = document.createElement('div');
                                displayContainer.setAttribute('id', 'display-container-create');
                                displayContainer.setAttribute('class', 'uk-inline');

                                var displayImg = document.createElement('img');
                                displayImg.setAttribute('src', 'img/product/thumb-' + filename);
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

                            loadStart: function(e) {
                                console.log('loadStart', arguments);

                                bar.removeAttribute('hidden');
                                bar.max = e.total;
                                bar.value = e.loaded;
                            },

                            progress: function(e) {
                                console.log('progress', arguments);

                                bar.max = e.total;
                                bar.value = e.loaded;
                            },

                            loadEnd: function(e) {
                                console.log('loadEnd', arguments);

                                bar.max = e.total;
                                bar.value = e.loaded;
                            },

                            completeAll: function() {
                                console.log('completeAll', arguments);

                                setTimeout(function() {
                                    bar.setAttribute('hidden', 'hidden');
                                }, 1000);

                                alert('<?= lang('Global.uploadComplete') ?>');
                            }
                        });

                        function removeImgCreate() {
                            $.ajax({
                                type: 'post',
                                url: 'upload/removeproductcreate',
                                data: {
                                    'photo': document.getElementById('photocreate').value
                                },
                                dataType: 'json',

                                error: function() {
                                    console.log('error', arguments);
                                },

                                success: function() {
                                    console.log('success', arguments);

                                    var pesan = arguments[0].message;

                                    document.getElementById('display-container-create').remove();
                                    document.getElementById('photocreate').value = '';
                                    document.getElementById('photocreatethumb').value = '';

                                    alert(pesan);
                                }
                            });
                        };
                    </script>

                    <div id="createVariant" class="uk-margin-bottom">
                        <h4 class="tm-h4 uk-margin-remove"><?= lang('Global.variant') ?></h4>
                        <div class="uk-text-right"><a onclick="createNewVariant()">+ Add More Variant</a></div>
                        <div id="create0" class="uk-margin uk-child-width-1-3" uk-grid>
                            <div id="createVarName0"><input type="text" class="uk-input" id="varName[0]" name="varName[0]" placeholder="<?= lang('Global.name') ?>" /></div>
                            <div id="createVarBase0"><input type="number" class="uk-input" id="varBase[0]" name="varBase[0]" placeholder="<?= lang('Global.basePrice') ?>" required /></div>
                            <div id="createVarCap0"><input type="number" class="uk-input" id="varCap[0]" name="varCap[0]" placeholder="<?= lang('Global.capitalPrice') ?>" required /></div>
                            <div id="createSugCap0"><input type="number" class="uk-input" id="varSug[0]" name="varSug[0]" placeholder="<?= lang('Global.suggestPrice') ?>" required /></div>
                            <div id="createVarMargin0"><input type="number" class="uk-input" id="varMargin[0]" name="varMargin[0]" placeholder="<?= lang('Global.margin') ?>" required /></div>
                        </div>
                    </div>
                    <script type="text/javascript">
                        var createCount = 0;

                        function createNewVariant() {
                            createCount++;

                            const createVariant = document.getElementById("createVariant");

                            const newCreateVariant = document.createElement('div');
                            newCreateVariant.setAttribute('id', 'create' + createCount);
                            newCreateVariant.setAttribute('class', 'uk-margin uk-child-width-1-3');
                            newCreateVariant.setAttribute('uk-grid', '');

                            const createVarName = document.createElement('div');
                            createVarName.setAttribute('id', 'createVarName' + createCount);

                            const createVarNameInput = document.createElement('input');
                            createVarNameInput.setAttribute('type', 'text');
                            createVarNameInput.setAttribute('class', 'uk-input');
                            createVarNameInput.setAttribute('placeholder', '<?= lang('Global.name') ?>');
                            createVarNameInput.setAttribute('id', 'varName[' + createCount + ']');
                            createVarNameInput.setAttribute('name', 'varName[' + createCount + ']');

                            const createVarBase = document.createElement('div');
                            createVarBase.setAttribute('id', 'createVarBase' + createCount);

                            const createVarBaseInput = document.createElement('input');
                            createVarBaseInput.setAttribute('type', 'number');
                            createVarBaseInput.setAttribute('class', 'uk-input');
                            createVarBaseInput.setAttribute('placeholder', '<?= lang('Global.basePrice') ?>');
                            createVarBaseInput.setAttribute('id', 'varBase[' + createCount + ']');
                            createVarBaseInput.setAttribute('name', 'varBase[' + createCount + ']');

                            const createVarCap = document.createElement('div');
                            createVarCap.setAttribute('id', 'createVarCap' + createCount);

                            const createVarCapInput = document.createElement('input');
                            createVarCapInput.setAttribute('type', 'number');
                            createVarCapInput.setAttribute('class', 'uk-input');
                            createVarCapInput.setAttribute('placeholder', '<?= lang('Global.capitalPrice') ?>');
                            createVarCapInput.setAttribute('id', 'varCap[' + createCount + ']');
                            createVarCapInput.setAttribute('name', 'varCap[' + createCount + ']');

                            const createSugCap = document.createElement('div');
                            createSugCap.setAttribute('id', 'createSugCap' + createCount);

                            const createSugCapInput = document.createElement('input');
                            createSugCapInput.setAttribute('type', 'number');
                            createSugCapInput.setAttribute('class', 'uk-input');
                            createSugCapInput.setAttribute('placeholder', '<?= lang('Global.suggestPrice') ?>');
                            createSugCapInput.setAttribute('id', 'varSug[' + createCount + ']');
                            createSugCapInput.setAttribute('name', 'varSug[' + createCount + ']');

                            const createVarMargin = document.createElement('div');
                            createVarMargin.setAttribute('id', 'createVarMargin' + createCount);

                            const createVarMarginInput = document.createElement('input');
                            createVarMarginInput.setAttribute('type', 'number');
                            createVarMarginInput.setAttribute('class', 'uk-input');
                            createVarMarginInput.setAttribute('placeholder', '<?= lang('Global.margin') ?>');
                            createVarMarginInput.setAttribute('id', 'varMargin[' + createCount + ']');
                            createVarMarginInput.setAttribute('name', 'varMargin[' + createCount + ']');

                            const createRemove = document.createElement('div');
                            createRemove.setAttribute('id', 'remove' + createCount);
                            createRemove.setAttribute('class', 'uk-text-center uk-text-bold uk-text-danger uk-flex uk-flex-middle');

                            const createRemoveButton = document.createElement('a');
                            createRemoveButton.setAttribute('onclick', 'createRemove(' + createCount + ')');
                            createRemoveButton.setAttribute('class', 'uk-link-reset');
                            createRemoveButton.innerHTML = 'X';

                            createVarName.appendChild(createVarNameInput);
                            newCreateVariant.appendChild(createVarName);
                            createVarBase.appendChild(createVarBaseInput);
                            newCreateVariant.appendChild(createVarBase);
                            createVarCap.appendChild(createVarCapInput);
                            newCreateVariant.appendChild(createVarCap);
                            createSugCap.appendChild(createSugCapInput);
                            newCreateVariant.appendChild(createSugCap);
                            createRemove.appendChild(createRemoveButton);
                            createVarMargin.appendChild(createVarMarginInput);
                            newCreateVariant.appendChild(createVarMargin);
                            newCreateVariant.appendChild(createRemove);
                            createVariant.appendChild(newCreateVariant);
                        };

                        function createRemove(i) {
                            const createRemoveElement = document.getElementById('create' + i);
                            createRemoveElement.remove();
                        };
                    </script>

                    <hr>

                    <div class="uk-margin">
                        <button type="submit" class="uk-button uk-button-primary"><?= lang('Global.save') ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- End Of Modal Add -->

<!-- Modal Add Category -->
<div uk-modal class="uk-flex-top" id="tambahcat">
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <div class="uk-modal-content">
            <div class="uk-modal-header">
                <div class="uk-child-width-1-2" uk-grid>
                    <div>
                        <h5 class="uk-modal-title" id="tambahcat"><?= lang('Global.addCategory') ?></h5>
                    </div>
                    <div class="uk-text-right">
                        <button class="uk-modal-close uk-icon-button-delete" uk-icon="icon: close;" type="button"></button>
                    </div>
                </div>
            </div>
            <div class="uk-modal-body">
                <table class="uk-table uk-table-justify uk-table-middle uk-table-divider">
                    <thead class="uk-h5"><?= lang('Global.categoryList') ?>
                        <tr>
                            <th class="uk-text-center" style="color: #000;">No</th>
                            <th class="uk-text-center" style="color: #000;"><?= lang('Global.name') ?></th>
                            <th class="uk-text-center" style="color: #000;">Code</th>
                            <th class="uk-text-center" style="color: #000;"><?= lang('Global.action') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; ?>
                        <?php foreach ($category as $cate) : ?>
                            <tr>
                                <td class="uk-text-center"><?= $i++; ?></td>
                                <td class="uk-text-center"><?= $cate['name']; ?></td>
                                <td class="uk-text-center"><?= $cate['catcode']; ?></td>
                                <td class="uk-text-center">
                                    <a class="uk-icon-button" uk-icon="pencil" uk-toggle="target: #editcat<?= $cate['id'] ?>"></a>
                                    <a class="uk-icon-button-delete" uk-icon="trash" href="product/deletecat/<?= $cate['id'] ?>"></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Modal Edit Category -->
                <?php foreach ($category as $cate) : ?>
                    <div uk-modal class="uk-flex-top" id="editcat<?= $cate['id'] ?>">
                        <div class="uk-modal-dialog uk-margin-auto-vertical">
                            <div class="uk-modal-content">
                                <div class="uk-modal-header">
                                    <h5 class="uk-modal-title" id="editcat"><?= lang('Global.updateData') ?></h5>
                                </div>
                                <div class="uk-modal-body">
                                    <form class="uk-form-stacked" role="form" action="product/editcat<?= $cate['id'] ?>" method="post">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= $cate['id']; ?>">

                                        <div class="uk-margin-bottom">
                                            <label class="uk-form-label" for="name"><?= lang('Global.name') ?></label>
                                            <div class="uk-form-controls">
                                                <input type="text" class="uk-input" id="name" name="name" value="<?= $cate['name']; ?>" autofocus />
                                            </div>
                                        </div>

                                        <div class="uk-margin-bottom">
                                            <label class="uk-form-label" for="catcode">Code</label>
                                            <div class="uk-form-controls">
                                                <input type="text" class="uk-input" id="catcode" name="catcode" value="<?= $cate['catcode']; ?>" autofocus />
                                            </div>
                                        </div>

                                        <hr>

                                        <div class="uk-margin">
                                            <button type="submit" class="uk-button uk-button-primary"><?= lang('Global.save') ?></button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <!-- End Of Modal Edit Category -->

                <form class="uk-form-stacked" role="form" action="product/createcat" method="post">
                    <?= csrf_field() ?>

                    <div class="uk-margin-bottom">
                        <label class="uk-form-label" for="name"><?= lang('Global.name') ?></label>
                        <div class="uk-form-controls">
                            <input type="text" class="uk-input <?php if (session('errors.name')) : ?>tm-form-invalid<?php endif ?>" id="name" name="name" placeholder="<?= lang('Global.name') ?>" autofocus required />
                        </div>
                    </div>

                    <div class="uk-margin-bottom">
                        <label class="uk-form-label" for="catcode">Code</label>
                        <div class="uk-form-controls">
                            <input type="text" class="uk-input <?php if (session('errors.catcode')) : ?>tm-form-invalid<?php endif ?>" id="catcode" name="catcode" placeholder="Code" autofocus required />
                        </div>
                    </div>

                    <hr>

                    <div class="uk-margin">
                        <button type="submit" class="uk-button uk-button-primary"><?= lang('Global.save') ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- End Of Modal Add Category -->

<!-- Modal Add Brand -->
<div uk-modal class="uk-flex-top" id="tambahbrand">
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <div class="uk-modal-content">
            <div class="uk-modal-header">
                <div class="uk-child-width-1-2" uk-grid>
                    <div>
                        <h5 class="uk-modal-title" id="tambahbrand"><?= lang('Global.addBrand') ?></h5>
                    </div>
                    <div class="uk-text-right">
                        <button class="uk-modal-close uk-icon-button-delete" uk-icon="icon: close;" type="button"></button>
                    </div>
                </div>
            </div>
            <div class="uk-modal-body">
                <table class="uk-table uk-table-justify uk-table-middle uk-table-divider">
                    <thead class="uk-h5"><?= lang('Global.brandList') ?>
                        <tr>
                            <th class="uk-text-center">No</th>
                            <th class="uk-text-center"><?= lang('Global.name') ?></th>
                            <th class="uk-text-center"><?= lang('Global.action') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; ?>
                        <?php foreach ($brand as $bran) : ?>
                            <tr>
                                <td class="uk-text-center"><?= $i++; ?></td>
                                <td class="uk-text-center"><?= $bran['name']; ?></td>
                                <td class="uk-text-center">
                                    <a class="uk-icon-button" uk-icon="pencil" uk-toggle="target: #editbrand<?= $bran['id'] ?>"></a>
                                    <a class="uk-icon-button-delete" uk-icon="trash" href="product/deletebrand/<?= $bran['id'] ?>"></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Modal Edit Brand -->
                <?php foreach ($brand as $bran) : ?>
                    <div uk-modal class="uk-flex-top" id="editbrand<?= $bran['id'] ?>">
                        <div class="uk-modal-dialog uk-margin-auto-vertical">
                            <div class="uk-modal-content">
                                <div class="uk-modal-header">
                                    <h5 class="uk-modal-title" id="editbrand"><?= lang('Global.updateData') ?></h5>
                                </div>
                                <div class="uk-modal-body">
                                    <form class="uk-form-stacked" role="form" action="product/editbrand<?= $bran['id'] ?>" method="post">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= $bran['id']; ?>">

                                        <div class="uk-margin-bottom">
                                            <label class="uk-form-label" for="name"><?= lang('Global.name') ?></label>
                                            <div class="uk-form-controls">
                                                <input type="text" class="uk-input" id="name" name="name" value="<?= $bran['name']; ?>" autofocus />
                                            </div>
                                        </div>

                                        <hr>

                                        <div class="uk-margin">
                                            <button type="submit" class="uk-button uk-button-primary"><?= lang('Global.save') ?></button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <!-- End Of Modal Edit Brand -->

                <form class="uk-form-stacked" role="form" action="product/createbrand" method="post">
                    <?= csrf_field() ?>

                    <div class="uk-margin-bottom">
                        <label class="uk-form-label" for="name"><?= lang('Global.name') ?></label>
                        <div class="uk-form-controls">
                            <input type="text" class="uk-input <?php if (session('errors.name')) : ?>tm-form-invalid<?php endif ?>" id="name" name="name" placeholder="<?= lang('Global.name') ?>" autofocus required />
                        </div>
                    </div>

                    <hr>

                    <div class="uk-margin">
                        <button type="submit" class="uk-button uk-button-primary"><?= lang('Global.save') ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- End Of Modal Add Brand -->

<!-- Table Of Content -->
<div class="uk-overflow-auto">
    <!-- Search Engine -->
    <div class="uk-margin-medium-bottom">
        <form action="product" method="GET">
            <div class="uk-child-width-1-1 uk-child-width-1-5@m uk-flex-middle" uk-grid>
                <div class="uk-text-right@l uk-margin-small-top">
                    <div class="uk-search uk-search-default uk-width-1-1">
                        <span class="uk-form-icon" uk-icon="icon: search" style="color: #000;"></span>
                        <input class="uk-width-1-1 uk-input" type="search" name="search" style="border-radius: 7px;" placeholder="Search Item ..." aria-label="Search" value="<?= (!empty($input['search']) ? $input['search'] : '') ?>">
                    </div>
                </div>
                <div class="uk-margin-small-top">
                    <select class="uk-select" id="filter" name="category" style="border-radius: 5px; border-style: solid;">
                        <option value="" selected><?= lang('Global.selectcat') ?></option>
                        <?php foreach ($category as $cate) { ?>
                            <option value="<?= $cate['id'] ?>" <?= ((!empty($input['category'])) && ($input['category'] === $cate['id'])) ? 'selected' : '' ?>><?= $cate['name'] ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="uk-margin-small-top">
                    <select class="uk-select" id="filterbrand" name="brand" style="border-radius: 5px; border-style: solid;">
                        <option value="" <?= ((isset($input['brand'])) && ($input['brand'] == '')) ? 'selected' : '' ?>><?=lang('Global.selectbrand')?></option>
                        <?php foreach ($brand as $bran) { ?>
                            <option value="<?= $bran['id'] ?>" <?= ((isset($input['brand'])) && ($input['brand'] == $bran['id'])) ? 'selected' : '' ?>><?= $bran['name'] ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="uk-margin-small-top">
                    <select class="uk-select" id="filterstatus" name="status" style="border-radius: 5px; border-style: solid;">
                        <option value="" <?= ((isset($input['status'])) && ($input['status'] == '')) ? 'selected' : '' ?>>Status</option>
                        <option value="0" <?= ((isset($input['status'])) && ($input['status'] == '0')) ? 'selected' : '' ?>>Tidak Aktif</option>
                        <option value="1" <?= ((isset($input['status'])) && ($input['status'] == '1')) ? 'selected' : '' ?>>Aktif</option>
                    </select>
                </div>
                <div class="uk-text-center">
                    <button class="uk-button uk-button-primary" type="submit">Search</button>
                </div>
            </div>
        </form>
    </div>
    <!-- Search Engine End -->

    <!-- Counter Total -->
    <div class="uk-light" uk-grid>
        <!-- Product-->
        <div class="uk-width-1-2 uk-width-1-4@m uk-form-horizontal">
            <div class="uk-form-label uk-margin-top" style="width: 100px;"><?= lang('Global.total') ?> <?= lang('Global.product') ?> :</div>
            <div class="uk-form-controls uk-margin-top uk-margin-remove-left"><?= $productcount ?></div>
        </div>
        <!-- Product End -->

        <!-- Stock -->
        <div class="uk-width-1-2 uk-width-1-4@m uk-form-horizontal">
            <div class="uk-form-label uk-margin-top" style="width: 100px;"><?= lang('Global.total') ?> <?= lang('Global.stock') ?> :</div>
            <div class="uk-form-controls uk-margin-top uk-margin-remove-left"><?= $stockcount ?></div>
        </div>
        <!-- Stock End -->

        <!-- Base Price -->
        <div class="uk-width-1-2 uk-width-1-4@m uk-form-horizontal">
            <div class="uk-form-label uk-margin-top" style="width: 120px;"><?= lang('Global.total') ?> <?= lang('Global.basePrice') ?> :</div>
            <div class="uk-form-controls uk-margin-top uk-margin-remove-left">Rp <?= number_format($totalbase, 2, ',', '.') ?></div>
        </div>
        <!-- Base Price End -->

        <!-- Capital Price -->
        <div class="uk-width-1-2 uk-width-1-4@m uk-form-horizontal">
            <div class="uk-form-label uk-margin-top" style="width: 120px;"><?= lang('Global.total') ?> <?= lang('Global.capitalPrice') ?> :</div>
            <div class="uk-form-controls uk-margin-top uk-margin-remove-left">Rp <?= number_format($totalcap, 2, ',', '.') ?></div>
        </div>
        <!-- Capital Price End -->
    </div>
    <table class="uk-table uk-table-justify uk-table-middle uk-table-divider uk-light display" style="width:100%">
        <thead>
            <tr>
                <th class="uk-text-center"><?= lang('Global.detail') ?></th>
                <th class="uk-text-center"><?= lang('Global.favorite') ?></th>
                <th><?= lang('Global.name') ?></th>
                <th><?= lang('Global.status') ?></th>
                <th><?= lang('Global.category') ?></th>
                <th><?= lang('Global.price') ?></th>
                <th><?= lang('Global.stock') ?></th>
                <th><?= lang('Global.brand') ?></th>
                <th class="uk-width-small">Umur Produk</th>
                <?php if (in_groups('owner')) : ?>
                    <th class="uk-text-center"><?= lang('Global.action') ?></th>
                <?php endif ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product) : ?>
                <tr class="">
                    <td class="uk-text-center">
                        <a class="uk-icon-link uk-icon" uk-toggle="target: #product-<?= $product['id']; ?>" uk-icon="search"></a>
                    </td>
                    <td class="uk-text-center">
                        <form class="" action="product/favorite/<?= $product['id'] ?>" method="post" id="myForm">
                            <?php if ($product['favorite'] === '0') {
                                $checked = '';
                            } else {
                                $checked = 'checked';
                            } ?>
                            <input class="uk-checkbox" type="checkbox" name="favorite" id="favorite-<?= $product['id'] ?>" <?= $checked ?>>
                        </form>
                    </td>
                    <td>
                        <?php if ($product['status'] != '0') { ?>
                            <div><?= $product['name']; ?></div>
                        <?php } else { ?>
                            <div style="text-decoration: line-through"><?= $product['name'] ?></div>
                        <?php } ?>
                    </td>
                    <td>
                        <?php if ($product['photo'] == null) { ?>
                            <div style="color: red;">Belum Ada Foto</div>
                        <?php } if ($product['link'] == null) { ?>
                            <div style="color: red;">Belum Ada Link Tokopedia</div>
                        <?php } ?>
                    </td>
                    <td>
                        <?php foreach ($category as $cat) {
                            if ($cat['id'] === $product['catid']) {
                                echo $cat['name'];
                            }
                        } ?>
                    </td>
                    <td>
                        <?php
                        if (!empty($variants)) {
                            $countvar = array_count_values(array_column($variants, 'productid'))[$product['id']];
                        } else {
                            $countvar = 0;
                        }
                        if ($countvar > 1) {
                            echo $countvar . ' ' . lang('Global.variant');
                        } elseif ($countvar === 1) {
                            foreach ($variants as $variant) {
                                if ($variant['productid'] === $product['id']) {
                                    echo "Rp " . number_format(($variant['hargamodal'] + $variant['hargajual']), 2, ',', '.');
                                }
                            }
                        } else {
                            echo '0';
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        $toqty = 0;
                        foreach ($stocks as $stock) {
                            foreach ($variants as $variant) {
                                if ($outletPick === null) {
                                    if (($variant['productid'] === $product['id']) && ($stock['variantid'] === $variant['id'])) {
                                        $toqty += $stock['qty'];
                                    }
                                } else {
                                    if (($variant['productid'] === $product['id']) && ($stock['variantid'] === $variant['id']) && ($stock['outletid'] === $outletPick)) {
                                        $toqty += $stock['qty'];
                                    }
                                }
                            }
                        }
                        echo $toqty;
                        ?>
                    </td>
                    <td>
                        <?php foreach ($brand as $merek) {
                            if ($merek['id'] === $product['brandid']) {
                                echo $merek['name'];
                            }
                        } ?>
                    </td>
                    <td>
                        <?php
                        $formatday  = [];
                        foreach ($stocks as $stock) {
                            foreach ($variants as $variant) {
                                // Check if this stock item matches the product and variant and outlet conditions
                                $matchesProduct = ($variant['productid'] == $product['id']);
                                $matchesVariant = ($stock['variantid'] == $variant['id']);
                                $matchesOutlet  = ($outletPick === null) ? true: ($stock['outletid'] == $outletPick);
                                if ($matchesProduct && $matchesVariant && $matchesOutlet) {
                                    if ($stock['qty'] > 0) {
                                        // Check if restock date is valid
                                        $restockDate = $stock['restock'];
                                        if ($restockDate !== null && $restockDate !== '0000-00-00 00:00:00' && $restockDate !== '') {
                                            $origin      = new DateTime($restockDate);
                                            $target      = new DateTime('now');
                                            $interval    = $origin->diff($target); // Store the absolute day difference as string
                                            $formatday[] = substr($interval->format('%R%a'), 1);
                                        }
                                    }
                                }
                            }
                        }

                        // Show minimum day difference if available
                        if (!empty($formatday)) {
                            echo min($formatday) . ' ' . lang('Global.day');
                        }
                        ?>
                    </td>
                    <!-- <td>
                        </?php
                        $formatday  = [];
                        $hasQtyZero = false; // track if any matching stock has qty=0
                        foreach ($stocks as $stock) {
                            foreach ($variants as $variant) {
                                // Check if this stock item matches the product and variant and outlet conditions
                                $matchesProduct = ($variant['productid'] == $product['id']);
                                $matchesVariant = ($stock['variantid'] == $variant['id']);
                                $matchesOutlet  = ($outletPick === null) ? true: ($stock['outletid'] == $outletPick);
                                if ($matchesProduct && $matchesVariant && $matchesOutlet) {
                                    if ($stock['qty']   == 0) {
                                        // Stock qty is zero for this variant/product/outlet
                                        $hasQtyZero = true;
                                    }
                                    else {
                                        // Check if restock date is valid
                                        $restockDate    = $stock['restock'];
                                        if ($restockDate !== null && $restockDate !== '0000-00-00 00:00:00' && $restockDate !== '') {
                                            $origin         = new DateTime($restockDate);
                                            $target         = new DateTime('now');
                                            $interval       = $origin->diff($target); // Store the absolute day difference as string
                                            $formatday[]    = substr($interval->format('%R%a'), 1);
                                        } // If date invalid, skip this entry silently (do not add '-')
                                    }
                                }
                            }
                        }

                        // Now decide what to display
                        if (!empty($formatday)) {
                            // We have valid days to display
                            echo min($formatday) . ' ' . lang('Global.day');
                        }

                        else {
                            // No valid days found
                            if ($hasQtyZero) {
                                // If there are stocks with qty=0, show '-'
                                echo '-';
                            }
                            else {
                                // Otherwise, no matching stocks or no dates -> also show '-'
                                echo '-';
                            }
                        }

                        ?>
                    </td> -->
                    <?php if (in_groups('owner')) : ?>
                        <td class="uk-child-width-auto uk-flex-center uk-grid-row-small uk-grid-column-small" uk-grid>
                            <!-- Button Trigger Modal Edit -->
                            <div>
                                <a class="uk-icon-button" uk-icon="pencil" uk-toggle="target: #editdata<?= $product['id'] ?>"></a>
                            </div>
                            <!-- End Of Button Trigger Modal Edit -->

                            <!-- Button Delete -->
                            <div>
                                <a class="uk-icon-button-delete" uk-icon="trash" href="product/delete/<?= $product['id'] ?>" onclick="return confirm('<?= lang('Global.deleteConfirm') ?>')"></a>
                            </div>
                            <!-- End Of Button Delete -->
                        </td>
                    <?php endif ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div>
        <?= $pager->links('product', 'front_full') ?>
    </div>
</div>
<!-- End Of Table Content -->

<!-- Ajax Favorite -->
<script type="text/javascript">
    <?php foreach ($products as $product) { ?>
        $(document).ready(function() {
            //set initial state.
            $('#favorite-<?= $product['id'] ?>').change(function() {
                var checked<?= $product['id'] ?> = document.getElementById('favorite-<?= $product['id'] ?>').checked;
                if (checked<?= $product['id'] ?> == true) {
                    var data = {
                        'favorite': '1',
                    };
                } else {
                    var data = {
                        'favorite': '0',
                    };
                }
                $.ajax({
                    url: "product/favorite/<?= $product['id'] ?>",
                    method: "POST",
                    data: data,
                    dataType: "json",
                    error: function() {
                        console.log('error', arguments);
                    },
                    success: function() {
                        console.log('success', arguments);
                    },
                })
            });
        });
    <?php } ?>
</script>
<!-- Ajax Favorite End -->

<!-- Modal Edit -->
<?php foreach ($products as $product) : ?>
    <div uk-modal class="uk-flex-top" id="editdata<?= $product['id'] ?>">
        <div class="uk-modal-dialog uk-margin-auto-vertical">
            <div class="uk-modal-content">
                <div class="uk-modal-header">
                    <div class="uk-child-width-1-2" uk-grid>
                        <div>
                            <h5 class="uk-modal-title" id="editdata"><?= lang('Global.updateData') ?></h5>
                        </div>
                        <div class="uk-text-right">
                            <button class="uk-modal-close uk-icon-button-delete" uk-icon="icon: close;" type="button"></button>
                        </div>
                    </div>
                </div>

                <div class="uk-modal-body">
                    <form class="uk-form-stacked" role="form" action="product/update/<?= $product['id'] ?>" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= $product['id']; ?>">

                        <div class="uk-margin-bottom">
                            <label class="uk-form-label" for="name"><?= lang('Global.name') ?></label>
                            <div class="uk-form-controls">
                                <input type="text" class="uk-input" id="name" name="name" value="<?= $product['name']; ?>" />
                            </div>
                        </div>

                        <div class="uk-margin-bottom">
                            <label class="uk-form-label" for="link">Link Tokopedia</label>
                            <div class="uk-form-controls">
                                <input type="text" class="uk-input" id="link" name="link" value="<?= $product['link']; ?>" />
                            </div>
                        </div>

                        <div class="uk-margin-bottom">
                            <label class="uk-form-label" for="description"><?= lang('Global.description') ?></label>
                            <div class="uk-form-controls">
                                <input type="text" class="uk-input" id="description" name="description" value="<?= $product['description']; ?>" />
                            </div>
                        </div>

                        <div class="uk-margin-bottom">
                            <label class="uk-form-label"><?= lang('Global.category') ?></label>
                            <div class="uk-margin-small">
                                <div class="uk-width-1-1">
                                    <?php if ($product['catid'] != "0") {
                                        foreach ($category as $cat) { 
                                            if (($cat['id'] === $product['catid'])) { ?>
                                                <input class="uk-input" name="category<?= $product['id'] ?>" id="category<?= $product['id'] ?>" value="<?= $cat['name'] . ' ('. $cat['catcode'] .')'; ?>" required />
                                                <input id="catid<?= $product['id'] ?>" name="catid<?= $product['id'] ?>" value="<?= $cat['id']; ?>" hidden />
                                            <?php }
                                        }
                                    } else { ?>
                                        <input class="uk-input" id="category<?= $product['id'] ?>" name="category<?= $product['id'] ?>" placeholder="<?= lang('Global.category') ?>" required>
                                        <input id="catid<?= $product['id'] ?>" name="catid<?= $product['id'] ?>" hidden />
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="uk-h6 uk-margin-remove">
                                <?= lang('Global.morecate') ?><a uk-toggle="target: #tambahcat"><?= lang('Global.addCategory') ?></a>
                            </div>

                            <script type="text/javascript">
                                $(function() {
                                    var category = [
                                        <?php foreach ($category as $cat) {
                                            echo '{label:"' . $cat['name'] . ' ('. $cat['catcode'] .')' . '",idx:' . (int)$cat['id'] . '},';
                                        } ?>
                                    ];
                                    $("#category<?= $product['id'] ?>").autocomplete({
                                        source: category,
                                        select: function(e, i) {
                                            $("#catid<?= $product['id'] ?>").val(i.item.idx); // save selected id to hidden input
                                        },
                                        minLength: 2
                                    });
                                });
                            </script>
                        </div>

                        <div class="uk-margin-bottom">
                            <label class="uk-form-label"><?= lang('Global.brand') ?></label>
                            <div class="uk-margin-small">
                                <div class="uk-width-1-1">
                                    <?php if ($product['brandid'] != "0") {
                                        foreach ($brand as $bran) { ?>
                                            <?php if ($bran['id'] === $product['brandid']) { ?>
                                                <input class="uk-input" name="brand<?= $product['id'] ?>" id="brand<?= $product['id'] ?>" value="<?= $bran['name']; ?>" required />
                                                <input id="brandid<?= $product['id'] ?>" name="brandid<?= $product['id'] ?>" value="<?= $bran['id']; ?>" hidden />
                                            <?php }
                                        }
                                    } else { ?>
                                        <input class="uk-input" id="brand<?= $product['id'] ?>" name="brand<?= $product['id'] ?>" placeholder="<?= lang('Global.brand') ?>" required>
                                        <input id="brandid<?= $product['id'] ?>" name="brandid<?= $product['id'] ?>" hidden />
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="uk-h6 uk-margin-remove">
                                <?= lang('Global.morebrand') ?><a uk-toggle="target: #tambahbrand"><?= lang('Global.addBrand') ?></a>
                            </div>

                            <script type="text/javascript">
                                $(function() {
                                    var brand = [
                                        <?php foreach ($brand as $bran) {
                                            echo '{label:"' . $bran['name'] . '",idx:' . (int)$bran['id'] . '},';
                                        } ?>
                                    ];
                                    $("#brand<?= $product['id'] ?>").autocomplete({
                                        source: brand,
                                        select: function(e, i) {
                                            $("#brandid<?= $product['id'] ?>").val(i.item.idx); // save selected id to hidden input
                                        },
                                        minLength: 2
                                    });
                                });
                            </script>
                        </div>

                        <div id="image-container-edit-<?= $product['id'] ?>" class="uk-margin">
                            <label class="uk-form-label" for="photocreate"><?= lang('Global.photo') ?></label>
                            <div id="image-container-<?= $product['id'] ?>" class="uk-form-controls">
                                <input id="photoedit<?= $product['id'] ?>" value="<?= $product['photo']; ?>" hidden />
                                <div class="js-upload-edit-<?= $product['id'] ?> uk-placeholder uk-text-center">
                                    <span uk-icon="icon: cloud-upload"></span>
                                    <span class="uk-text-middle"><?= lang('Global.photoUploadDesc') ?></span>
                                    <div uk-form-custom>
                                        <input type="file">
                                        <span class="uk-link uk-preserve-color"><?= lang('Global.selectOne') ?></span>
                                    </div>
                                </div>
                                <progress id="js-progressbar-edit-<?= $product['id'] ?>" class="uk-progress" value="0" max="100" hidden></progress>
                                <?php if (!empty($product['thumbnail'])) { ?>
                                    <div id="display-container-edit-<?= $product['id'] ?>" class="uk-inline">
                                        <img src="img/product/<?= $product['thumbnail'] ?>" width="150" height="150" />
                                        <div class="uk-position-small uk-position-top-right">
                                            <a class="tm-img-remove uk-border-circle" uk-icon="close" onclick="removeImgEdit<?= $product['id'] ?>()"></a>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>

                        <script type="text/javascript">
                            var bar = document.getElementById('js-progressbar-edit-<?= $product['id'] ?>');

                            UIkit.upload('.js-upload-edit-<?= $product['id'] ?>', {
                                url: 'upload/productedit/<?= $product['id'] ?>',
                                multiple: false,
                                name: 'uploads',
                                method: 'POST',
                                type: 'json',

                                beforeSend: function() {
                                    console.log('beforeSend', arguments);
                                },
                                beforeAll: function() {
                                    console.log('beforeAll', arguments);
                                },
                                load: function() {
                                    console.log('load', arguments);
                                },
                                error: function() {
                                    console.log('error', arguments);
                                    var error = arguments[0].xhr.response.message.uploads;
                                    alert(error);
                                },
                                complete: function() {
                                    console.log('complete', arguments);

                                    var filename = arguments[0].response;

                                    if (document.getElementById('display-container-edit-<?= $product['id'] ?>')) {
                                        document.getElementById('display-container-edit-<?= $product['id'] ?>').remove();
                                    };

                                    document.getElementById('photoedit<?= $product['id'] ?>').value = filename;

                                    var imgContainer = document.getElementById('image-container-edit-<?= $product['id'] ?>');

                                    var displayContainer = document.createElement('div');
                                    displayContainer.setAttribute('id', 'display-container-edit-<?= $product['id'] ?>');
                                    displayContainer.setAttribute('class', 'uk-inline');

                                    var displayImg = document.createElement('img');
                                    displayImg.setAttribute('src', 'img/product/thumb-' + filename);
                                    displayImg.setAttribute('width', '150');
                                    displayImg.setAttribute('height', '150');

                                    var closeContainer = document.createElement('div');
                                    closeContainer.setAttribute('class', 'uk-position-small uk-position-top-right');

                                    var closeButton = document.createElement('a');
                                    closeButton.setAttribute('class', 'tm-img-remove uk-border-circle');
                                    closeButton.setAttribute('onClick', 'removeImgEdit<?= $product['id'] ?>()');
                                    closeButton.setAttribute('uk-icon', 'close');

                                    closeContainer.appendChild(closeButton);
                                    displayContainer.appendChild(displayImg);
                                    displayContainer.appendChild(closeContainer);
                                    imgContainer.appendChild(displayContainer);
                                },

                                loadStart: function(e) {
                                    console.log('loadStart', arguments);

                                    bar.removeAttribute('hidden');
                                    bar.max = e.total;
                                    bar.value = e.loaded;
                                },

                                progress: function(e) {
                                    console.log('progress', arguments);

                                    bar.max = e.total;
                                    bar.value = e.loaded;
                                },

                                loadEnd: function(e) {
                                    console.log('loadEnd', arguments);

                                    bar.max = e.total;
                                    bar.value = e.loaded;
                                },

                                completeAll: function() {
                                    console.log('completeAll', arguments);

                                    setTimeout(function() {
                                        bar.setAttribute('hidden', 'hidden');
                                    }, 1000);

                                    alert('<?= lang('Global.uploadComplete') ?>');
                                }
                            });

                            function removeImgEdit<?= $product['id'] ?>() {
                                $.ajax({
                                    type: 'post',
                                    url: 'upload/removeproductedit/<?= $product['id'] ?>',
                                    data: {
                                        'photo': document.getElementById('photoedit<?= $product['id'] ?>').value
                                    },
                                    dataType: 'json',

                                    error: function() {
                                        console.log('error', arguments);
                                    },

                                    success: function() {
                                        console.log('success', arguments);

                                        var pesan = arguments[0].message;

                                        document.getElementById('display-container-edit-<?= $product['id'] ?>').remove();
                                        document.getElementById('photoedit<?= $product['id'] ?>').value = '';

                                        alert(pesan);
                                    }
                                });
                            };
                        </script>

                        <div class="uk-margin-bottom">
                            <h4 class="tm-h4 uk-margin-remove"><?= lang('Global.variant') ?></h4>
                            <div class="uk-h6 uk-margin-remove">
                                <?= lang('Global.editVar') ?><a href="/product/indexvar/<?= $product['id']; ?>"><?= lang('Global.manageVar') ?></a>
                            </div>
                        </div>

                        <input type="hidden" name="status" id="statusval" value="<?= $product['status'] ?>">
                        <label class="uk-form-label" for="status"><?= lang('Global.status') ?></label>
                        <label class="switch">
                            <?php if ($product['status'] != "0") { ?>
                                <input id="status<?= $product['id'] ?>" type="checkbox" checked>
                            <?php } else { ?>
                                <input id="status<?= $product['id'] ?>" type="checkbox">
                            <?php } ?>
                            <span class="slider round"></span>
                        </label>
                        <script>
                            $(document).ready(function() {
                                $("input[id='status<?= $product['id'] ?>']").change(function() {
                                    if ($(this).is(':checked')) {
                                        $("input[id='statusval']").val("1");
                                    } else {
                                        $("input[id='statusval']").val("0");
                                    }
                                });
                            });
                        </script>

                        <hr>

                        <div class="uk-margin">
                            <button type="submit" class="uk-button uk-button-primary"><?= lang('Global.save') ?></button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>
<!-- End Of Modal Edit -->

<!-- Products Detail -->
<?php foreach ($products as $product) { ?>
    <div id="product-<?= $product['id'] ?>" class="uk-flex-top" uk-modal>
        <div class="uk-modal-dialog uk-margin-auto-vertical">
            <div class="uk-modal-header">
                <div class="uk-child-width-1-2" uk-grid>
                    <div>
                        <h5 class="uk-modal-title"><?= $product['name'] ?></h5>
                    </div>
                    <div class="uk-text-right">
                        <button class="uk-modal-close uk-icon-button-delete" uk-icon="icon: close;" type="button"></button>
                    </div>
                </div>
            </div>
            <div class="uk-modal-body">
                <div class="uk-flex-middle" uk-grid>
                    <div class="uk-width-1-3@m">
                        <?php if (!empty($product['thumbnail'])) { ?>
                            <img class="uk-width-1-1" src="/img/product/<?= $product['thumbnail'] ?>" />
                        <?php } else { ?>
                            <svg x="0px" y="0px" viewBox="0 0 300 300" style="enable-background:new 0 0 300 300;" xml:space="preserve">
                                <g>
                                    <defs>
                                        <rect id="SVGID_1_" y="0" width="300" height="300" />
                                    </defs>
                                    <clipPath id="SVGID_00000065759931020451687440000009539297437584060839_">
                                        <use xlink:href="#SVGID_1_" style="overflow:visible;" />
                                    </clipPath>
                                    <g style="clip-path:url(#SVGID_00000065759931020451687440000009539297437584060839_);">
                                        <path class="dummyproduct" d="M10.43,99.92c-10.73-27.36,4.25-69.85,30.19-85.78C51.01,7.77,77-5.17,108.81,30.24
                                          c-2.16,0.65-4.26,1.55-6.29,2.7c-3.02,1.75-5.49,4.04-7.57,6.58C83.12,26.95,67.17,17.08,49.17,28.13
                                          C34,37.46,23.24,60.45,23.28,79.62c-0.03,5.15,0.77,10.05,2.42,14.32l4.75,11.66c6.41,15.42,12.34,29.6,12.34,46.6
                                          c-0.03,11.87-2.89,25.14-10.44,41.17c-1.05,2.23-1.96,5.97-1.96,9.8c0,2.31,0.29,4.66,1.16,6.73c1.13,2.73,3.09,4.44,5.9,5.59
                                          c2.16,0.28,10.31,0.86,17.02-5.79c6.56-6.54,13.06-21.9,6.78-58.08C50.43,89.07,75.8,68.22,87.2,62.18
                                          c15.23-8.09,33.99-5.98,45.6,5.15c3.3,3.14,3.38,8.34,0.23,11.6c-3.13,3.26-8.35,3.37-11.59,0.23c-5.55-5.31-16.45-7.86-26.56-2.5
                                          c-8.25,4.37-26.43,20.18-17.46,72.17c6.01,34.86,2.08,59.32-11.64,72.76c-13.81,13.43-31.7,10.1-32.45,9.95l-0.67-0.13l-0.63-0.24
                                          c-7.34-2.73-12.76-7.95-15.68-15.08c-4.14-10.12-2.41-22.24,1.16-29.72c15.27-32.43,8.34-49.15-2.2-74.47L10.43,99.92z" />
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
                                              C239,203.19,235.61,215.91,226.2,226.44z" />
                                        </g>
                                    </g>
                                </g>
                            </svg>
                        <?php } ?>
                    </div>
                    <div class="uk-width-2-3@m">
                        <?php
                        $toqty = 0;
                        foreach ($stocks as $stock) {
                            foreach ($variants as $variant) {
                                if ($outletPick === null) {
                                    if (($variant['productid'] === $product['id']) && ($stock['variantid'] === $variant['id'])) {
                                        $toqty += $stock['qty'];
                                    }
                                } else {
                                    if (($variant['productid'] === $product['id']) && ($stock['variantid'] === $variant['id']) && ($stock['outletid']) === $outletPick) {
                                        $toqty += $stock['qty'];
                                    }
                                }
                            }
                        }
                        if ($toqty <= 10) {
                            $stockClass = 'uk-text-danger';
                        } else {
                            $stockClass = 'uk-sext-success';
                        }
                        ?>
                        <div class="uk-h3 tm-h3"><?= lang('Global.total') ?> <?= lang('Global.stock') ?> <span class="<?= $stockClass ?>"><?= $toqty ?></span></div>
                        <h6 class="uk-h4 tm-h4 uk-margin-remove"><?= lang('Global.variant') ?></h6>
                        <table class="uk-table uk-table-justify uk-table-middle" style="background-color: #fff;">
                            <thead>
                                <tr>
                                    <th class="uk-width-medium" style="color: #000;">SKU</th>
                                    <th class="uk-width-medium" style="color: #000;">Name</th>
                                    <th class="uk-text-center uk-width-small" style="color: #000;"><?= lang('Global.stock') ?></th>
                                    <th class="uk-width-large" style="color: #000;"><?= lang('Global.price') ?></th>
                                    <th class="uk-text-center uk-width-small" style="color: #000;">Stock History</th>
                                </tr>
                            </thead>
                            <tbody style="color: #000;">
                                <?php foreach ($variants as $variant) { ?>
                                    <?php if ($variant['productid'] === $product['id']) { ?>
                                        <tr>
                                            <td class="uk-text-bold"><?= $variant['sku'] ?></td>
                                            <td class="uk-text-bold"><?= $variant['name'] ?></td>
                                            <td class="uk-text-center">
                                                <?php
                                                $varstock = 0;
                                                foreach ($stocks as $stock) {
                                                    if ($stock['variantid'] === $variant['id']) {
                                                        $varstock += $stock['qty'];
                                                    }
                                                }
                                                echo $varstock;
                                                ?>
                                            </td>
                                            <td>
                                                <?= "Rp " . number_format(((int)$variant['hargamodal'] + (int)$variant['hargajual']), 2, ',', '.'); ?>
                                            </td>
                                            <td class="uk-text-center">
                                                <a class="uk-icon-button" uk-icon="history" href="product/history/<?= $variant['id'] ?>"></a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<!-- End of Products Detail -->
<?= $this->endSection() ?>