<?= $this->extend('layout') ?>

<?= $this->section('extraScript') ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" />
  
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script> -->
<?= $this->endSection() ?>

<?= $this->section('main') ?>

<!-- Page Heading -->
<div class="tm-card-header uk-light">
  <?= view('Views/Auth/_message_block') ?>

  <div uk-grid class="uk-flex-middle">
    <div class="uk-width-1-2@m">
      <h3 class="tm-h3"><?=lang('Global.productList')?></h3>
    </div>

    <!-- Button Trigger Modal Add -->
    <div class="uk-width-1-2@m uk-text-right@m">
      <button type="button" class="uk-button uk-button-primary uk-preserve-color" uk-toggle="target: #tambahdata"><?=lang('Global.addProduct')?></button>
    </div>
    <!-- End Of Button Trigger Modal Add -->

    <!-- Modal Add -->
    <div uk-modal class="uk-flex-top" id="tambahdata">
      <div class="uk-modal-dialog uk-margin-auto-vertical">
        <div class="uk-modal-content">
          <div class="uk-modal-header">
            <h5 class="uk-modal-title" id="tambahdata" ><?=lang('Global.addProduct')?></h5>
          </div>
          <div class="uk-modal-body">
            <form class="uk-form-stacked" role="form" action="/product/create" method="post">
              <?= csrf_field() ?>

              <div class="uk-margin-bottom">
                <label class="uk-form-label" for="name"><?=lang('Global.name')?></label>
                <div class="uk-form-controls">
                  <input type="text" class="uk-input <?php if (session('errors.name')) : ?>tm-form-invalid<?php endif ?>" id="name" name="name" placeholder="<?=lang('Global.name')?>" autofocus required />
                </div>
              </div>

              <div class="uk-margin-bottom">
                <label class="uk-form-label" for="description"><?=lang('Global.description')?></label>
                <div class="uk-form-controls">
                  <input type="text" class="uk-input <?php if (session('errors.description')) : ?>tm-form-invalid<?php endif ?>" id="description" name="description" placeholder="<?=lang('Global.description')?>" autofocus required />
                </div>
              </div>

              <div class="uk-margin">
                <label class="uk-form-label" for="category"><?=lang('Global.category')?></label>
                <div class="uk-form-controls">
                  <select class="uk-select" name="category">
                    <option><?=lang('Global.category')?></option>
                    <?php foreach ($category as $cate) { ?>
                      <option value="<?= $cate['id']; ?>"><?= $cate['name']; ?></option>
                    <?php } ?>
                  </select>
                </div>
                <div class="uk-h6 uk-margin-remove">
                  <?=lang('Global.morecate')?><a uk-toggle="target: #tambahcat"><?=lang('Global.addCategory')?></a>
                </div>
              </div>

              <div class="uk-margin-bottom">
                <label class="uk-form-label" for="brand"><?=lang('Global.brand')?></label>
                <div class="uk-form-controls">
                  <select class="uk-select" name="brand">
                    <option><?=lang('Global.brand')?></option>
                    <?php foreach ($brand as $bran) { ?>
                      <option value="<?= $bran['id']; ?>"><?= $bran['name']; ?></option>
                    <?php } ?>
                  </select>
                </div>
                <div class="uk-h6 uk-margin-remove">
                  <?=lang('Global.morebrand')?><a uk-toggle="target: #tambahbrand"><?=lang('Global.addBrand')?></a>
                </div>
              </div>

              <div id="image-container-create" class="uk-margin">
                <label class="uk-form-label" for="photocreate"><?=lang('Global.photo')?></label>
                  <div id="image-container" class="uk-form-controls">
                      <input id="photocreate" name="photo" value="" hidden />
                      <input id="photocreatethumb" name="thumbnail" value="" hidden />
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
                      url: 'upload/productcreate',
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
                          document.getElementById('photocreatethumb').value = 'thumb-'+filename;

                          var imgContainer = document.getElementById('image-container-create');

                          var displayContainer = document.createElement('div');
                          displayContainer.setAttribute('id', 'display-container-create');
                          displayContainer.setAttribute('class', 'uk-inline');

                          var displayImg = document.createElement('img');
                          displayImg.setAttribute('src', 'img/product/thumb-'+filename);
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
                          url: 'upload/removeproductcreate',
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
                              document.getElementById('photocreatethumb').value = '';

                              alert(pesan);
                          }
                      });
                  };
              </script>

              <div id="createVariant" class="uk-margin-bottom">
                <h4 class="tm-h4 uk-margin-remove"><?=lang('Global.variant')?></h4>
                <div class="uk-text-right"><a onclick="createNewVariant()">+ Add More Variant</a></div>
                <div class="uk-margin uk-margin-remove-top uk-child-width-1-5" uk-grid>
                  <div class="uk-text-bold"><?=lang('Global.name')?></div>
                  <div class="uk-text-bold"><?=lang('Global.basePrice')?></div>
                  <div class="uk-text-bold"><?=lang('Global.capitalPrice')?></div>
                  <div class="uk-text-bold"><?=lang('Global.margin')?></div>
                </div>
                <div id="create0" class="uk-margin uk-child-width-1-5" uk-grid>
                  <div id="createVarName0"><input type="text" class="uk-input" id="varName[0]" name="varName[0]" /></div>
                  <div id="createVarBase0"><input type="number" class="uk-input" id="varBase[0]" name="varBase[0]" /></div>
                  <div id="createVarCap0"><input type="number" class="uk-input" id="varCap[0]" name="varCap[0]"/></div>
                  <div id="createVarMargin0"><input type="number" class="uk-input" id="varMargin[0]" name="varMargin[0]" /></div>
                </div>
              </div>
              <script type="text/javascript">
                var createCount = 0;
                function createNewVariant() {
                  createCount++;

                  const createVariant = document.getElementById("createVariant");

                  const newCreateVariant = document.createElement('div');
                  newCreateVariant.setAttribute('id','create'+createCount);
                  newCreateVariant.setAttribute('class','uk-margin uk-child-width-1-5');
                  newCreateVariant.setAttribute('uk-grid','');

                  const createVarName = document.createElement('div');
                  createVarName.setAttribute('id','createVarName'+createCount);

                  const createVarNameInput = document.createElement('input');
                  createVarNameInput.setAttribute('type','text');
                  createVarNameInput.setAttribute('class','uk-input');
                  createVarNameInput.setAttribute('id','varName['+createCount+']');
                  createVarNameInput.setAttribute('name','varName['+createCount+']');

                  const createVarBase = document.createElement('div');
                  createVarName.setAttribute('id','createVarBase'+createCount);

                  const createVarBaseInput = document.createElement('input');
                  createVarBaseInput.setAttribute('type','number');
                  createVarBaseInput.setAttribute('class','uk-input');
                  createVarBaseInput.setAttribute('id','varBase['+createCount+']');
                  createVarBaseInput.setAttribute('name','varBase['+createCount+']');

                  const createVarCap = document.createElement('div');
                  createVarCap.setAttribute('id','createVarCap'+createCount);

                  const createVarCapInput = document.createElement('input');
                  createVarCapInput.setAttribute('type','number');
                  createVarCapInput.setAttribute('class','uk-input');
                  createVarCapInput.setAttribute('id','varCap['+createCount+']');
                  createVarCapInput.setAttribute('name','varCap['+createCount+']');

                  const createVarMargin = document.createElement('div');
                  createVarMargin.setAttribute('id','createVarMargin'+createCount);

                  const createVarMarginInput = document.createElement('input');
                  createVarMarginInput.setAttribute('type','number');
                  createVarMarginInput.setAttribute('class','uk-input');
                  createVarMarginInput.setAttribute('id','varMargin['+createCount+']');
                  createVarMarginInput.setAttribute('name','varMargin['+createCount+']');

                  const createRemove = document.createElement('div');
                  createRemove.setAttribute('id', 'remove'+createCount);
                  createRemove.setAttribute('class', 'uk-text-center uk-text-bold uk-text-danger uk-flex uk-flex-middle');

                  const createRemoveButton = document.createElement('a');
                  createRemoveButton.setAttribute('onclick', 'createRemove('+createCount+')');
                  createRemoveButton.setAttribute('class', 'uk-link-reset');
                  createRemoveButton.innerHTML = 'X';

                  createVarName.appendChild(createVarNameInput);
                  newCreateVariant.appendChild(createVarName);
                  createVarBase.appendChild(createVarBaseInput);
                  newCreateVariant.appendChild(createVarBase);
                  createVarCap.appendChild(createVarCapInput);
                  newCreateVariant.appendChild(createVarCap);
                  createRemove.appendChild(createRemoveButton);
                  createVarMargin.appendChild(createVarMarginInput);
                  newCreateVariant.appendChild(createVarMargin);
                  newCreateVariant.appendChild(createRemove);
                  createVariant.appendChild(newCreateVariant);
                };

                function createRemove(i) {
                  const createRemoveElement = document.getElementById('create'+i);
                  createRemoveElement.remove();
                };
              </script>

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

    <!-- Modal Add Category -->
    <div uk-modal class="uk-flex-top" id="tambahcat">
      <div class="uk-modal-dialog uk-margin-auto-vertical">
        <div class="uk-modal-content">
          <div class="uk-modal-header">
            <h5 class="uk-modal-title" id="tambahcat"><?=lang('Global.addCategory')?></h5>
          </div>
          <div class="uk-modal-body">
            <table class="uk-table uk-table-striped uk-table-hover uk-table-responsive uk-table-justify uk-table-middle uk-table-divider">
              <thead class="uk-h5"><?=lang('Global.categoryList')?>
                <tr>
                  <th class="uk-text-center">No</th>
                  <th class="uk-text-center"><?=lang('Global.name')?></th>
                  <th class="uk-text-center"><?=lang('Global.action')?></th>
                </tr>
              </thead>
              <tbody>
                <?php $i = 1 ; ?>
                <?php foreach ($category as $cate) : ?>
                  <tr>
                    <td class="uk-text-center"><?= $i++; ?></td>
                    <td class="uk-text-center"><?= $cate['name']; ?></td>
                    <td class="uk-text-center">
                      <button type="button" class="uk-button uk-button-primary" uk-toggle="target: #editcat<?= $cate['id'] ?>"><?=lang('Global.edit')?></button>
                      <a class="uk-button uk-button-default uk-button-danger" href="product/deletecat/<?= $cate['id'] ?>"><?=lang('Global.delete')?></a>
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
                      <h5 class="uk-modal-title" id="editcat"><?=lang('Global.updateData')?></h5>
                    </div>
                    <div class="uk-modal-body">
                      <form class="uk-form-stacked" role="form" action="product/editcat<?= $cate['id'] ?>" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= $cate['id']; ?>">

                        <div class="uk-margin-bottom">
                          <label class="uk-form-label" for="name"><?=lang('Global.name')?></label>
                          <div class="uk-form-controls">
                            <input type="text" class="uk-input" id="name" name="name" value="<?= $cate['name']; ?>"autofocus />
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
            <?php endforeach; ?>
            <!-- End Of Modal Edit Category -->
            
            <form class="uk-form-stacked" role="form" action="product/createcat" method="post">
              <?= csrf_field() ?>

              <div class="uk-margin-bottom">
                <label class="uk-form-label" for="name"><?=lang('Global.name')?></label>
                <div class="uk-form-controls">
                  <input type="text" class="uk-input <?php if (session('errors.name')) : ?>tm-form-invalid<?php endif ?>" id="name" name="name" placeholder="<?=lang('Global.name')?>" autofocus required />
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
    <!-- End Of Modal Add Category -->

     <!-- Modal Add Brand -->
     <div uk-modal class="uk-flex-top" id="tambahbrand">
      <div class="uk-modal-dialog uk-margin-auto-vertical">
        <div class="uk-modal-content">
          <div class="uk-modal-header">
            <h5 class="uk-modal-title" id="tambahbrand"><?=lang('Global.addBrand')?></h5>
          </div>
          <div class="uk-modal-body">
            <table class="uk-table uk-table-striped uk-table-hover uk-table-responsive uk-table-justify uk-table-middle uk-table-divider">
              <thead class="uk-h5"><?=lang('Global.brandList')?>
                <tr>
                  <th class="uk-text-center">No</th>
                  <th class="uk-text-center"><?=lang('Global.name')?></th>
                  <th class="uk-text-center"><?=lang('Global.action')?></th>
                </tr>
              </thead>
              <tbody>
                <?php $i = 1 ; ?>
                <?php foreach ($brand as $bran) : ?>
                  <tr>
                    <td class="uk-text-center"><?= $i++; ?></td>
                    <td class="uk-text-center"><?= $bran['name']; ?></td>
                    <td class="uk-text-center">
                      <button type="button" class="uk-button uk-button-primary" uk-toggle="target: #editbrand<?= $bran['id'] ?>"><?=lang('Global.edit')?></button>
                      <a class="uk-button uk-button-default uk-button-danger" href="product/deletebrand/<?= $bran['id'] ?>"><?=lang('Global.delete')?></a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
              
            <!-- Modal Edit Category -->
            <?php foreach ($brand as $bran) : ?>
              <div uk-modal class="uk-flex-top" id="editbrand<?= $bran['id'] ?>">
                <div class="uk-modal-dialog uk-margin-auto-vertical">
                  <div class="uk-modal-content">
                    <div class="uk-modal-header">
                      <h5 class="uk-modal-title" id="editbrand"><?=lang('Global.updateData')?></h5>
                    </div>
                    <div class="uk-modal-body">
                      <form class="uk-form-stacked" role="form" action="product/editbrand<?= $bran['id'] ?>" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= $bran['id']; ?>">

                        <div class="uk-margin-bottom">
                          <label class="uk-form-label" for="name"><?=lang('Global.name')?></label>
                          <div class="uk-form-controls">
                            <input type="text" class="uk-input" id="name" name="name" value="<?= $bran['name']; ?>"autofocus />
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
            <?php endforeach; ?>
            <!-- End Of Modal Edit Category -->
            
            <form class="uk-form-stacked" role="form" action="product/createbrand" method="post">
              <?= csrf_field() ?>

              <div class="uk-margin-bottom">
                <label class="uk-form-label" for="name"><?=lang('Global.name')?></label>
                <div class="uk-form-controls">
                  <input type="text" class="uk-input <?php if (session('errors.name')) : ?>tm-form-invalid<?php endif ?>" id="name" name="name" placeholder="<?=lang('Global.name')?>" autofocus required />
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
    <!-- End Of Modal Add Category -->

  </div>
</div>
<!-- End Of Page Heading -->

<!-- Table Of Content -->
<!-- Search Box -->
<div class="uk-child-width-1-5@m uk-margin" uk-grid>
  <div class="">
    <form class="uk-search uk-search-default">
      <span uk-search-icon></span>
      <input id="myInput" class="uk-search-input" type="search" placeholder="Search" aria-label="Search">
    </form>
  </div>
  <!-- Search Box End -->

  <div class="">
    <div class="uk-form-controls">
      <select class="uk-select" name="category">
        <option><?=lang('Global.category')?></option>
        <?php foreach ($category as $cate) { ?>
          <option value="<?= $cate['id']; ?>"><?= $cate['name']; ?></option>
        <?php } ?>
      </select>
    </div>
  </div>

  <div class="">
    <div class="uk-form-controls">
      <select class="uk-select" name="brand">
        <option><?=lang('Global.brand')?></option>
        <?php foreach ($brand as $bran) { ?>
          <option value="<?= $bran['id']; ?>"><?= $bran['name']; ?></option>
        <?php } ?>
      </select>
    </div>
  </div>
</div>

<div class="uk-overflow-auto">
  <table class="uk-table uk-table-justify uk-table-middle uk-table-divider uk-light" id="example" style="width:100%">
    <thead>
      <tr>
        <th class="uk-text-center"></th>
        <th class="uk-text-center">No</th>
        <th><?=lang('Global.name')?></th>
        <th><?=lang('Global.category')?></th>
        <th><?=lang('Global.price')?></th>
        <th><?=lang('Global.stock')?></th>
        <th><?=lang('Global.brand')?></th>
        <th class="uk-text-center"><?=lang('Global.action')?></th>
      </tr>
    </thead>
    <tbody>
      <?php $i = 1 ; ?>
      <?php foreach ($products as $product) : ?>
        <tr class="">
          <td class="uk-flex uk-flex-center">
            <a class="uk-icon-link uk-icon" uk-toggle="target: #variantlist-<?= $product['id']; ?>" uk-icon="triangle-down"></a>
          </td>
          <td class="uk-text-center"><?= $i++; ?></td>
          <td><?= $product['name']; ?></td>
          <td>
            <?php
              foreach ($category as $cat) {
                if ($cat['id'] === $product['catid']) {
                  echo $cat['name'];
                }
              }
            ?>
          </td>
          <td></td>
          <td>
            <?php
              $toqty = 0;
              foreach ($stocks as $stock) {
                foreach ($variants as $variant) {
                  if (($variant['productid'] === $product['id']) && ($stock['variantid'] === $variant['id'])) {
                    $toqty += $stock['qty'];
                  }
                }
              }
              echo $toqty;
            ?>
          </td>
          <td>
            <?php
              foreach ($brand as $merek) {
                if ($merek['id'] === $product['brandid']) {
                  echo $merek['name'];
                }
              }
            ?>
          </td>
          <td class="uk-child-width-auto uk-flex-center uk-grid-row-small uk-grid-column-small" uk-grid>
            <!-- Button Trigger Modal Edit -->
            <div>
              <button type="button" class="uk-button uk-button-primary uk-preserve-color" uk-toggle="target: #editdata<?= $product['id'] ?>"><?=lang('Global.edit')?></button>
            </div>
            <!-- End Of Button Trigger Modal Edit -->

            <!-- Button Delete -->
            <div>
              <a class="uk-button uk-button-default uk-button-danger uk-preserve-color" href="product/delete/<?= $product['id'] ?>" onclick="return confirm('<?=lang('Global.deleteConfirm')?>')"><?=lang('Global.delete')?></a>
            </div>
            <!-- End Of Button Delete -->
          </td>
        </tr>
        <tr id="variantlist-<?= $product['id']; ?>" hidden>
          <td></td>
          <td></td>
          <td class="tm-h5"><?=lang('Global.variant')?></td>
          <td></td>
          <td class="tm-h5"><?=lang('Global.price')?></td>
          <td></td>
          <td></td>
          <td></td>
        </tr>
        <?php
          foreach ($variants as $variant) {
            if ($variant['productid'] === $product['id']) {
              ?>
                <tr id="variantlist-<?= $product['id']; ?>" style="border-top: none;" hidden>
                  <td></td>
                  <td></td>
                  <td><?=$variant['name']?></td>
                  <td></td>
                  <td><?=$variant['hargamodal'] + $variant['hargajual']?></td>
                  <td>
                    <?php
                    $qty = 0;
                    foreach ($stocks as $stock) {
                      if ($stock['variantid'] === $variant['id']) {
                        $qty += (int)$stock['qty'];
                      }
                    }
                    echo $qty;
                    ?>
                  </td>
                  <td></td>
                  <td></td>
                </tr>
              <?php
            }
          }
        ?>
      <?php endforeach; ?>
    </tbody>
  </table>

  <!-- Table Pagination -->
  <!-- <ul class="uk-pagination uk-flex-right uk-margin-medium-top uk-light" uk-margin>
    <li><a href="#"><span uk-pagination-previous></span></a></li>
    <li><a href="#">1</a></li>
    <li class="uk-disabled"><span>…</span></li>
    <li><a href="#">4</a></li>
    <li><a href="#">5</a></li>
    <li><a href="#">6</a></li>
    <li><a href="#">7</a></li>
    <li><a href="#">8</a></li>
    <li><a href="#">9</a></li>
    <li><a href="#">10</a></li>
    <li class="uk-disabled"><span>…</span></li>
    <li><a href="#">20</a></li>
    <li><a href="#"><span uk-pagination-next></span></a></li>
  </ul> --> -->
  <!-- Table Pagination End

  <!-- Modal Edit -->
  <?php foreach ($products as $product) : ?>
    <div uk-modal class="uk-flex-top" id="editdata<?= $product['id'] ?>">
      <div class="uk-modal-dialog uk-margin-auto-vertical">
        <div class="uk-modal-content">
          <div class="uk-modal-header">
            <h5 class="uk-modal-title" id="editdata"><?=lang('Global.updateData')?></h5>
          </div>

          <div class="uk-modal-body">
            <form class="uk-form-stacked" role="form" action="product/update/<?= $product['id'] ?>" method="post">
              <?= csrf_field() ?>
              <input type="hidden" name="id" value="<?= $product['id']; ?>">

              <div class="uk-margin-bottom">
                <label class="uk-form-label" for="name"><?=lang('Global.name')?></label>
                <div class="uk-form-controls">
                  <input type="text" class="uk-input" id="name" name="name" value="<?= $product['name']; ?>"autofocus />
                </div>
              </div>
              
              <div class="uk-margin-bottom">
                <label class="uk-form-label" for="name"><?=lang('Global.description')?></label>
                <div class="uk-form-controls">
                  <input type="text" class="uk-input" id="description" name="description" value="<?= $product['description']; ?>"autofocus />
                </div>
              </div>

              <div class="uk-margin-bottom">
                <label class="uk-form-label" for="category"><?=lang('Global.category')?></label>
                <div class="uk-form-controls">
                  <input type="text" class="uk-input" id="category" name="category"  value="<?= $cate['name']; ?>" autofocus />
                </div>
              </div>

              <div class="uk-margin-bottom">
                <label class="uk-form-label" for="brand"><?=lang('Global.brand')?></label>
                <div class="uk-form-controls">
                  <input type="text" class="uk-input" id="brand" name="brand"  value="<?= $bran['name']; ?>" autofocus />
                </div>
              </div>

              <div id="image-container-edit-<?=$product['id']?>" class="uk-margin">
                <label class="uk-form-label" for="photocreate"><?=lang('Global.photo')?></label>
                  <div id="image-container-<?=$product['id']?>" class="uk-form-controls">
                      <input id="photoedit<?=$product['id']?>" value="" hidden />
                      <div class="js-upload-edit-<?=$product['id']?> uk-placeholder uk-text-center">
                          <span uk-icon="icon: cloud-upload"></span>
                          <span class="uk-text-middle"><?=lang('Global.photoUploadDesc')?></span>
                          <div uk-form-custom>
                              <input type="file">
                              <span class="uk-link uk-preserve-color"><?=lang('Global.selectOne')?></span>
                          </div>
                      </div>
                      <progress id="js-progressbar-edit-<?=$product['id']?>" class="uk-progress" value="0" max="100" hidden></progress>
                  </div>
              </div>
              <script type="text/javascript">
                  var bar = document.getElementById('js-progressbar-edit-<?=$product['id']?>');

                  UIkit.upload('.js-upload-edit-<?=$product['id']?>', {
                      url: 'upload/productedit/<?=$product['id']?>',
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

                          if (document.getElementById('display-container-edit-<?=$product['id']?>')) {
                              document.getElementById('display-container-edit-<?=$product['id']?>').remove();
                          };

                          document.getElementById('photoedit<?=$product['id']?>').value = filename;

                          var imgContainer = document.getElementById('image-container-edit-<?=$product['id']?>');

                          var displayContainer = document.createElement('div');
                          displayContainer.setAttribute('id', 'display-container-edit-<?=$product['id']?>');
                          displayContainer.setAttribute('class', 'uk-inline');

                          var displayImg = document.createElement('img');
                          displayImg.setAttribute('src', 'img/product/thumb-'+filename);
                          displayImg.setAttribute('width', '150');
                          displayImg.setAttribute('height', '150');

                          var closeContainer = document.createElement('div');
                          closeContainer.setAttribute('class', 'uk-position-small uk-position-top-right');

                          var closeButton = document.createElement('a');
                          closeButton.setAttribute('class', 'tm-img-remove uk-border-circle');
                          closeButton.setAttribute('onClick', 'removeImgEdit<?=$product['id']?>()');
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

                  function removeImgEdit<?=$product['id']?>() {                                
                      $.ajax ({
                          type: 'post',
                          url: 'upload/removeproduedit',
                          data: {'photo': document.getElementById('photoedit<?=$product['id']?>').value},
                          dataType: 'json',

                          error: function() {
                              console.log('error', arguments);
                          },

                          success:function() {
                              console.log('success', arguments);

                              var pesan = arguments[0].message;

                              document.getElementById('display-container-edit-<?=$product['id']?>').remove();
                              document.getElementById('photoedit<?=$product['id']?>').value = '';

                              alert(pesan);
                          }
                      });
                  };
              </script>

              <div class="uk-margin-bottom">
                <h4 class="tm-h4 uk-margin-remove"><?=lang('Global.variant')?></h4>
                <div class="uk-h6 uk-margin-remove">
                  <?=lang('Global.editVar')?><a href="/product/indexvar/<?= $product['id']; ?>"><?=lang('Global.manageVar')?></a>
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
  <?php endforeach; ?>
  <!-- End Of Modal Edit -->
</div>
<!-- End Of Table Content -->

<script>
  // ajax
  // $(document).ready(function(){
  // $("#myInput").on("keyup", function() {
  //       var value = $(this).val().toLowerCase();
  //       $("#myTable tr").filter(function() {
  //         $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
  //       });
  //     });
  //   });

  $(document).ready(function () {
    $('#example').DataTable();
});
    
</script>

<?= $this->endSection() ?>