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
      <button type="button" class="uk-button uk-button-primary" uk-toggle="target: #tambahdata"><?=lang('Global.addRestock')?></button>
    </div>
    <!-- End Of Button Trigger Modal Add -->

    <!-- Modal Add -->
    <div uk-modal class="uk-flex-top" id="tambahdata">
      <div class="uk-modal-dialog uk-margin-auto-vertical">
        <div class="uk-modal-content">
          <div class="uk-modal-header">
            <h5 class="uk-modal-title" id="tambahdata" ><?=lang('Global.addRestock')?></h5>
          </div>
          <div class="uk-modal-body">
            <form class="uk-form-stacked" role="form" action="/stock/create" method="post">
              <?= csrf_field() ?>

              <div class="uk-modal-header uk-margin-small">
                <h4 class="uk-text-bold uk-margin-small-top"><?=lang('Global.product')?><?=lang('Global.variant')?></h4>
              </div>

              <div class="uk-margin">
                <label class="uk-form-label" for="address"><?=lang('Global.basePrice')?></label>
                <div class="uk-form-controls">
                  <input type="text" class="uk-input <?php if (session('errors.basePrice')) : ?>tm-form-invalid<?php endif ?>" name="hargadasar" id="hargadasar" placeholder="<?=lang('Global.basePrice')?>" required/>
                </div>
              </div>

              <div class="uk-margin-bottom">
                <label class="uk-form-label" for="maps"><?=lang('Global.capitalPrice')?></label>
                <div class="uk-form-controls">
                  <input type="text" class="uk-input <?php if (session('errors.capitalPrice')) : ?>tm-form-invalid<?php endif ?>" id="hargamodal" name="hargamodal" placeholder="<?=lang('Global.capitalPrice')?>" autofocus required />
                </div>
              </div>

              <div class="uk-margin-bottom">
                <label class="uk-form-label" for="maps"><?=lang('Global.stock')?></label>
                <div class="uk-form-controls">
                  <input type="text" class="uk-input <?php if (session('errors.stock')) : ?>tm-form-invalid<?php endif ?>" id="qty" name="qty" placeholder="<?=lang('Global.stock')?>" autofocus required />
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
        <th class="uk-text-center"><?=lang('Global.outlet')?></th>
        <th class="uk-text-center"><?=lang('Global.variant')?></th>
        <th class="uk-text-center"><?=lang('Global.stock')?></th>
      </tr>
    </thead>
    <tbody>
      <?php $i = 1 ; ?>
      <?php foreach ($stocks as $stock) : ?>
        <tr>
          <td class="uk-text-center"><?= $i++; ?></td>
          <td class="uk-text-center">
            <?php foreach ($outlets as $outlet ) {
                if ( $stock['outletid']=== $outlet['id']){
                    echo $outlet['name'];
                }
                } ?>
          </td>
          <td class="uk-text-center">
           <?php foreach ($variants as $variant ) { 
            if($stock['variantid'] === $variant['id']){
                echo $variant['name'];
                 }
             } 
             ?>
        </td>
          <td class="uk-text-center"><?= $stock['qty']; ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <!-- End Table Content -->
</div>
<!-- End Of Table Content -->

<?= $this->endSection() ?>