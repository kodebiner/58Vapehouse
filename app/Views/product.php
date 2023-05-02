<?= $this->extend('layout') ?>
<?= $this->section('main') ?>

<!-- Page Heading -->
<div class="tm-card-header">
  <div uk-grid class="uk-width-1-1@m uk-flex-middle">
    <div class="uk-width-1-2@m">
      <h3 class="tm-h3"><?=lang('Global.productList')?></h3>
    </div>

    <!-- Button Trigger Modal Add -->
    <div class="uk-width-1-2@m uk-flex uk-flex uk-flex-right uk-text-left">
      <button type="button" class="uk-button uk-button-primary" uk-toggle="target: #tambahdata"><?=lang('Global.addProduct')?></button>
    </div>
    <!-- End Of Button Trigger Modal Add -->

    <?= view('Views/Auth/_message_block') ?>

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

              <div class="uk-margin">
                <label class="uk-form-label" for="category"><?=lang('Global.category')?></label>
                <div class="uk-form-controls">
                  <input type="text" class="uk-input <?php if (session('errors.category')) : ?>tm-form-invalid<?php endif ?>" name="category" id="category" placeholder="<?=lang('Global.category')?>" required/>
                </div>
                <div class="uk-h6">
                  Kategori belum tersedia? <a uk-toggle="target: #tambahkat">Buat Kategori</a>
                </div>
              </div>

              <div class="uk-margin-bottom">
                <label class="uk-form-label" for="cash"><?=lang('Global.cash')?></label>
                <div class="uk-form-controls">
                  <input type="text" class="uk-input <?php if (session('errors.cash')) : ?>tm-form-invalid<?php endif ?>" id="cash" name="cash" placeholder="<?=lang('Global.cash')?>" autofocus required />
                </div>
              </div>

              <div class="uk-margin-bottom">
                <label class="uk-form-label" for="stock"><?=lang('Global.stock')?></label>
                <div class="uk-form-controls">
                  <input type="text" class="uk-input <?php if (session('errors.stock')) : ?>tm-form-invalid<?php endif ?>" id="stock" name="stock" placeholder="<?=lang('Global.stock')?>" autofocus required />
                </div>
              </div>

              <div class="uk-margin-bottom">
                <label class="uk-form-label" for="brand"><?=lang('Global.brand')?></label>
                <div class="uk-form-controls">
                  <input type="text" class="uk-input <?php if (session('errors.brand')) : ?>tm-form-invalid<?php endif ?>" id="brand" name="brand" placeholder="<?=lang('Global.brand')?>" autofocus required />
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

    <div uk-modal class="uk-flex-top" id="tambahkat">
      <div class="uk-modal-dialog uk-margin-auto-vertical">
        <div class="uk-modal-content">
          <div class="uk-modal-header">
            <h5 class="uk-modal-title" id="tambahkat" >Tambah Kategori</h5>
          </div>
          <div class="uk-modal-body">
            <form class="uk-form-stacked" role="form" action="/category/create" method="post">
              <?= csrf_field() ?>

              <table class="uk-table uk-table-striped uk-table-hover uk-table-responsive uk-table-justify uk-table-middle uk-table-divider">
                <thead>
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
                      <td class="uk-text-center"><?= $cate->name; ?>
                        <input class="uk-form-controls" style="display: none;"/>
                        <button class="uk-button uk-button-default uk-button-success" style="width: 100%; display: none;"></button>
                      </td>
                      <td class="uk-text-center">
                        <a class="uk-button uk-button-default uk-button-danger" href="category/delete/<?= $category->id ?>"><?=lang('Global.delete')?></a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>

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
  </div>
</div>
<!-- End Of Page Heading -->

<!-- Table Of Content -->
<div class="uk-overflow-auto">
  <table class="uk-table uk-table-striped uk-table-hover uk-table-responsive uk-table-justify uk-table-middle uk-table-divider">
    <thead>
      <tr>
        <th class="uk-text-center">No</th>
        <th class="uk-text-center"><?=lang('Global.name')?></th>
        <th class="uk-text-center"><?=lang('Global.category')?></th>
        <th class="uk-text-center"><?=lang('Global.cash')?></th>
        <th class="uk-text-center"><?=lang('Global.stock')?></th>
        <th class="uk-text-center"><?=lang('Global.brand')?></th>
        <th class="uk-text-center"><?=lang('Global.action')?></th>
      </tr>
    </thead>
    <tbody>
      <?php $i = 1 ; ?>
      <?php foreach ($products as $product) : ?>
        <tr>
          <td class="uk-text-center"><?= $i++; ?></td>
          <td class="uk-text-center"><?= $product->name; ?></td>
          <td class="uk-text-center"><?= $cate->name; ?></td>
          <td class="uk-text-center"><?= $cash->qty; ?></td>
          <td class="uk-text-center">
            <!-- Button Trigger Modal Edit -->
            <button type="button" class="uk-button uk-button-primary" uk-toggle="target: #editdata<?= $product->id ?>"><?=lang('Global.edit')?></button>
            <a class="uk-button uk-button-default uk-button-danger" href="product/delete/<?= $product->id ?>"><?=lang('Global.delete')?></a>
            <!-- End Of Button Trigger Modal Edit -->

            <!-- Button Delete -->

            <!-- End Of Button Delete -->
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <!-- Modal Edit -->
  <?php foreach ($products as $product) : ?>
    <div uk-modal class="uk-flex-top" id="editdata<?= $product->id ?>">
      <div class="uk-modal-dialog uk-margin-auto-vertical">
        <div class="uk-modal-content">
          <div class="uk-modal-header">
            <h5 class="uk-modal-title" id="editdata"><?=lang('Global.updateData')?></h5>
          </div>

          <div class="uk-modal-body">
            <form class="uk-form-stacked" role="form" action="product/update/<?= $product->id ?>" method="post">
              <?= csrf_field() ?>
              <input type="hidden" name="id" value="<?= $product->id; ?>">

              <div class="uk-margin-bottom">
                <label class="uk-form-label" for="name"><?=lang('Global.name')?></label>
                <div class="uk-form-controls">
                  <input type="text" class="uk-input" id="name" name="name" value="<?= $product->name; ?>"autofocus />
                </div>
              </div>

              <div class="uk-margin-bottom">
                <label class="uk-form-label" for="category"><?=lang('Global.category')?></label>
                <div class="uk-form-controls">
                  <input type="text" class="uk-input" id="category" name="category"  value="<?= $category->name; ?>" autofocus />
                </div>
              </div>

              <div class="uk-margin-bottom">
                <label class="uk-form-label" for="cash"><?=lang('Global.cash')?></label>
                <div class="uk-form-controls">
                  <input type="text" class="uk-input" id="cash" name="cash"  value="<?= $cash->qty; ?>" autofocus />
                </div>
              </div>

              <div class="uk-margin-bottom">
                <label class="uk-form-label" for="stock"><?=lang('Global.stock')?></label>
                <div class="uk-form-controls">
                  <input type="text" class="uk-input" id="stock" name="stock"  value="<?= $stock->qty; ?>" autofocus />
                </div>
              </div>

              <div class="uk-margin-bottom">
                <label class="uk-form-label" for="brand"><?=lang('Global.brand')?></label>
                <div class="uk-form-controls">
                  <input type="text" class="uk-input" id="brand" name="brand"  value="<?= $category->name; ?>" autofocus />
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

<?= $this->endSection() ?>