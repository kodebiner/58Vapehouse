<?= $this->extend('layout') ?>
<?= $this->section('main') ?>

<!-- Page Heading -->
<div class="tm-card-header">
  <?= view('Views/Auth/_message_block') ?>

  <div uk-grid class="uk-flex-middle">
    <div class="uk-width-1-2@m">
      <h3 class="tm-h3"><?=lang('Global.variantList')?> <?= $products['name']; ?></h3>
    </div>

    <!-- Button Trigger Modal Add -->
    <div class="uk-width-1-2@m uk-text-right@m">
      <button type="button" class="uk-button uk-button-primary" uk-toggle="target: #tambahdata"><?=lang('Global.addVariant')?></button>
    </div>
    <!-- End Of Button Trigger Modal Add -->

    <!-- filter button -->
    <div class="uk-margin">
      <form class="uk-search uk-search-default" action="">
          <span class="uk-search-icon-flip" uk-search-icon></span>
          <input class="uk-search-input" type="submit" placeholder="Search" aria-label="Search">
      </form>
    </div>
    <!-- end of filter button -->

    <!-- Modal Add -->
    <div uk-modal class="uk-flex-top" id="tambahdata">
      <div class="uk-modal-dialog uk-margin-auto-vertical">
        <div class="uk-modal-content">
          <div class="uk-modal-header">
            <h5 class="uk-modal-title" id="tambahdata" ><?=lang('Global.addVariant')?></h5>
          </div>
          <div class="uk-modal-body">
            <form class="uk-form-stacked" role="form" action="/product/createvar/<?= $products['id']; ?>" method="post">
              <?= csrf_field() ?>

              <div class="uk-margin-bottom">
                <label class="uk-form-label" for="name"><?=lang('Global.name')?></label>
                <div class="uk-form-controls">
                  <input type="text" class="uk-input <?php if (session('errors.name')) : ?>tm-form-invalid<?php endif ?>" id="name" name="name" placeholder="<?=lang('Global.name')?>" autofocus required />
                </div>
              </div>

              <div class="uk-margin">
                <label class="uk-form-label" for="hargadasar"><?=lang('Global.basePrice')?></label>
                <div class="uk-form-controls">
                  <input type="text" class="uk-input <?php if (session('errors.basePrice')) : ?>tm-form-invalid<?php endif ?>" name="hargadasar" id="hargadasar" placeholder="<?=lang('Global.basePrice')?>" required/>
                </div>
              </div>

              <div class="uk-margin-bottom">
                <label class="uk-form-label" for="hargamodal"><?=lang('Global.capitalPrice')?></label>
                <div class="uk-form-controls">
                  <input type="text" class="uk-input <?php if (session('errors.capitalPrice')) : ?>tm-form-invalid<?php endif ?>" id="hargamodal" name="hargamodal" placeholder="<?=lang('Global.capitalPrice')?>" autofocus required />
                </div>
              </div>

              <div class="uk-margin-bottom">
                <label class="uk-form-label" for="margin"><?=lang('Global.margin')?></label>
                <div class="uk-form-controls">
                  <input type="text" class="uk-input <?php if (session('errors.margin')) : ?>tm-form-invalid<?php endif ?>" id="margin" name="margin" placeholder="<?=lang('Global.margin')?>" autofocus required />
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
  </div>
</div>
<!-- End Of Page Heading -->

<!-- Table Of Content -->
<div class="uk-overflow-auto">
  <table class="uk-table uk-table-striped uk-table-hover uk-table-justify uk-table-middle uk-table-divider">
    <thead>
      <tr>
        <th class="uk-text-center">No</th>
        <th class="uk-text-center"><?=lang('Global.name')?></th>
        <th class="uk-text-center"><?=lang('Global.basePrice')?></th>
        <th class="uk-text-center"><?=lang('Global.capitalPrice')?></th>
        <th class="uk-text-center"><?=lang('Global.margin')?></th>
        <th class="uk-text-center"><?=lang('Global.stock')?></th>
        <th class="uk-text-center"><?=lang('Global.action')?></th>
      </tr>
    </thead>
    <tbody>
        <?php $i = 1 ; ?>
        <?php foreach ($variants as $variant) { ?>
          <tr>
            <td class="uk-text-center"><?= $i++; ?></td>
            <td class="uk-text-center"><?=$variant['name']?></td>
            <td class="uk-text-center"><?=$variant['hargadasar']?></td>
            <td class="uk-text-center"><?=$variant['hargamodal']?></td>            
            <td class="uk-text-center"><?=$variant['hargajual']?></td>
            <td class="uk-text-center">
              <?php
              $qty = 0;
              foreach ($stock as $stok) {
                if ($stok['variantid'] === $variant['id']) {
                  $qty += (int)$stok['qty'];
                }
              }
              echo $qty;
              ?>
            </td>
            <td class="uk-child-width-auto uk-flex-center uk-grid-row-small uk-grid-column-small" uk-grid>
              <!-- Button Trigger Modal Edit -->
              <div>
                  <button type="button" class="uk-button uk-button-primary" uk-toggle="target: #editdata<?= $variant['id'] ?>"><?=lang('Global.edit')?></button>
              </div>
              <!-- End Of Button Trigger Modal Edit -->

              <!-- Button Delete -->
              <div>
                <a class="uk-button uk-button-default uk-button-danger" href="product/deletevar/<?= $variant['id'] ?>" onclick="return confirm('<?=lang('Global.deleteConfirm')?>')"><?=lang('Global.delete')?></a>
              </div>
              <!-- End Of Button Delete -->
            </td>
          </tr>
        <?php } ?>
    </tbody>
  </table>

  <!-- Modal Edit -->
  <?php foreach ($variants as $variant) : ?>
    <div uk-modal class="uk-flex-top" id="editdata<?= $variant['id']; ?>">
      <div class="uk-modal-dialog uk-margin-auto-vertical">
        <div class="uk-modal-content">
          <div class="uk-modal-header">
            <h5 class="uk-modal-title" id="editdata"><?=lang('Global.updateData')?></h5>
          </div>

          <div class="uk-modal-body">
            <form class="uk-form-stacked" role="form" action="product/editvar/<?= $variant['id']; ?>" method="post">
              <?= csrf_field() ?>
              <input type="hidden" name="id" value="<?= $variant['id']; ?>">

              <div class="uk-margin-bottom">
                <label class="uk-form-label" for="name"><?=lang('Global.name')?></label>
                <div class="uk-form-controls">
                  <input type="text" class="uk-input" id="name" name="name" value="<?= $variant['name']; ?>"autofocus />
                </div>
              </div>

              <div class="uk-margin-bottom">
                <label class="uk-form-label" for="hargadasar"><?=lang('Global.basePrice')?></label>
                <div class="uk-form-controls">
                  <input type="text" class="uk-input" id="hargadasar" name="hargadasar"  value="<?= $variant['hargadasar']; ?>" autofocus />
                </div>
              </div>

              <div class="uk-margin-bottom">
                <label class="uk-form-label" for="hargammodal"><?=lang('Global.capitalPrice')?></label>
                <div class="uk-form-controls">
                  <input type="text" class="uk-input" id="hargamodal" name="hargamodal"  value="<?= $variant['hargamodal']; ?>" autofocus />
                </div>
              </div>
              
              <div class="uk-margin-bottom">
                <label class="uk-form-label" for="margin"><?=lang('Global.margin')?></label>
                <div class="uk-form-controls">
                  <input type="text" class="uk-input" id="margin" name="margin"  value="<?= $variant['hargajual']; ?>" autofocus />
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