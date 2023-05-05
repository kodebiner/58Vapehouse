<?= $this->extend('layout') ?>
<?= $this->section('main') ?>

<!-- Page Heading -->
<div class="tm-card-header">
  <?= view('Views/Auth/_message_block') ?>

  <div uk-grid class="uk-flex-middle">
    <div class="uk-width-1-2@m">
      <h3 class="tm-h3"><?=lang('Global.stockList')?></h3>
    </div>

    <!-- Button Trigger Modal Add -->
    <div class="uk-width-1-2@m uk-text-right@m">
      <button type="button" class="uk-button uk-button-primary" uk-toggle="target: #tambahdata"><?=lang('Global.addOutlet')?></button>
    </div>
    <!-- End Of Button Trigger Modal Add -->

    <!-- Modal Add -->
    <div uk-modal class="uk-flex-top" id="tambahdata">
      <div class="uk-modal-dialog uk-margin-auto-vertical">
        <div class="uk-modal-content">
          <div class="uk-modal-header">
            <h5 class="uk-modal-title" id="tambahdata" ><?=lang('Global.addOutlet')?></h5>
          </div>
          <div class="uk-modal-body">
            <form class="uk-form-stacked" role="form" action="/outlet/create" method="post">
              <?= csrf_field() ?>

              <div class="uk-margin-bottom">
                <label class="uk-form-label" for="name"><?=lang('Global.name')?></label>
                <div class="uk-form-controls">
                  <input type="text" class="uk-input <?php if (session('errors.name')) : ?>tm-form-invalid<?php endif ?>" id="name" name="name" placeholder="<?=lang('Global.name')?>" autofocus required />
                </div>
              </div>

              <div class="uk-margin">
                <label class="uk-form-label" for="address"><?=lang('Global.address')?></label>
                <div class="uk-form-controls">
                  <input type="text" class="uk-input <?php if (session('errors.address')) : ?>tm-form-invalid<?php endif ?>" name="address" id="address" placeholder="<?=lang('Global.address')?>" required/>
                </div>
              </div>

              <div class="uk-margin-bottom">
                <label class="uk-form-label" for="maps"><?=lang('Global.maps')?></label>
                <div class="uk-form-controls">
                  <input type="text" class="uk-input <?php if (session('errors.maps')) : ?>tm-form-invalid<?php endif ?>" id="maps" name="maps" placeholder="<?=lang('Global.maps')?>" autofocus required />
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
        <th class="uk-text-center"><?=lang('Global.product')?></th>
        <th class="uk-text-center"><?=lang('Global.variant')?></th>
        <th class="uk-text-center"><?=lang('Global.stock')?></th>
        <th class="uk-text-center"><?=lang('Global.action')?></th>
      </tr>
    </thead>
    <tbody>
      <?php $i = 1 ; ?>
      <?php foreach ($stocks as $stock) : ?>
        <tr>
          <td class="uk-text-center"><?= $i++; ?></td>
          <td class="uk-text-center"><?= $stock['outletid']; ?></td>
          <td class="uk-text-center">
           <?php foreach ($variants as $variant ) { ?>
            echo $variant['name'];
            <?php } ?>
        </td>
          <td class="uk-text-center"><?= $stock['qty']; ?></td>
          <td class="uk-child-width-auto uk-flex-center uk-grid-row-small uk-grid-column-small" uk-grid>
            <!-- Button Trigger Modal Edit -->
            <div>
              <button type="button" class="uk-button uk-button-primary" uk-toggle="target: #editdata<?= $stock['id'] ?>"><?=lang('Global.edit')?></button>
            </div>
            <!-- End Of Button Trigger Modal Edit -->

            <!-- Button Delete -->
            <div>
              <a class="uk-button uk-button-default uk-button-danger" href="stock/delete/<?= $stock['id'] ?>" onclick="return confirm('<?=lang('Global.deleteConfirm')?>')"><?=lang('Global.delete')?></a>
            </div>
            <!-- End Of Button Delete -->
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <!-- Modal Edit -->
  <?php foreach ($outlets as $outlet) : ?>
    <div uk-modal class="uk-flex-top" id="editdata<?= $outlet['id'] ?>">
      <div class="uk-modal-dialog uk-margin-auto-vertical">
        <div class="uk-modal-content">
          <div class="uk-modal-header">
            <h5 class="uk-modal-title" id="editdata"><?=lang('Global.updateData')?></h5>
          </div>

          <div class="uk-modal-body">
            <form class="uk-form-stacked" role="form" action="outlet/update/<?= $outlet['id'] ?>" method="post">
              <?= csrf_field() ?>
              <input type="hidden" name="id" value="<?= $outlet['id']; ?>">

              <div class="uk-margin-bottom">
                <label class="uk-form-label" for="name"><?=lang('Global.name')?></label>
                <div class="uk-form-controls">
                  <input type="text" class="uk-input" id="name" name="name" value="<?= $outlet['name']; ?>"autofocus />
                </div>
              </div>

              <div class="uk-margin-bottom">
                <label class="uk-form-label" for="address"><?=lang('Global.address')?></label>
                <div class="uk-form-controls">
                  <input type="text" class="uk-input" id="address" name="address"  value="<?= $outlet['address']; ?>" autofocus />
                </div>
              </div>

              <div class="uk-margin-bottom">
                <label class="uk-form-label" for="maps"><?=lang('Global.maps')?></label>
                <div class="uk-form-controls">
                  <input type="text" class="uk-input" id="maps" name="maps"  value="<?= $outlet['maps']; ?>" autofocus />
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