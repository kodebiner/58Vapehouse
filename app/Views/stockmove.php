<?= $this->extend('layout') ?>
<?= $this->section('main') ?>

<!-- Page Heading -->
<div class="tm-card-header uk-light">
  <?= view('Views/Auth/_message_block') ?>

  <div uk-grid class="uk-flex-middle">
    <div class="uk-width-1-2@m">
      <h3 class="tm-h3"><?=lang('Global.stockMove')?></h3>
    </div>

    <!-- Button Trigger Modal Add -->
    <div class="uk-width-1-2@m uk-text-right@m">
      <button type="button" class="uk-button uk-button-primary uk-preserve-color" uk-toggle="target: #tambahdata"><?=lang('Global.addStockMove')?></button>
    </div>
    <!-- End Of Button Trigger Modal Add -->

    <!-- Modal Add -->
    <div uk-modal class="uk-flex-top" id="tambahdata">
      <div class="uk-modal-dialog uk-margin-auto-vertical">
        <div class="uk-modal-content">
          <div class="uk-modal-header">
            <h5 class="uk-modal-title" id="tambahdata" ><?=lang('Global.addStockMove')?></h5>
          </div>
          <div class="uk-modal-body">
            <form class="uk-form-stacked" role="form" action="/stockmove/create" method="post">
              <?= csrf_field() ?>
              
              <!-- ajax -->
              <label class="uk-form-label" for="product"><?=lang('Global.product')?></label>
                <div class="uk-form-controls">
                  <select class="uk-select" name="product" id="sel_pro">
                    <option><?=lang('Global.product')?></option>
                    <?php
                    foreach ($products as $product) {
                      echo '<option value="'.$product['id'].'">'.$product['name'].'</option>';
                    }
                    ?>
                  </select>
                </div>

                <label class="uk-form-label" for="variant"><?=lang('Global.variant')?></label>
                <div class="uk-form-controls">
                  <select class="uk-select" name="variant" id="sel_variant">
                    <option id="default_var"><?=lang('Global.variant')?></option>
                  </select>
                </div>

                <label class="uk-form-label" for="origin"><?=lang('Global.origin')?></label>
                <div class="uk-form-controls">
                  <select class="uk-select" name="origin" id="sel_out">
                    <option><?=lang('Global.origin')?></option>
                    <?php
                    foreach ($outlets as $outlet) {
                      if ($outlet['id'] === $outletPick) {
                        $checked = 'selected';
                      } else {
                        $checked = '';
                      }
                    ?>
                      <option value="<?= $outlet['id']; ?>" <?=$checked?>><?= $outlet['name']; ?></option>
                    <?php
                    }
                    ?>
                  </select>
                </div>

                <label class="uk-form-label" for="outlet"><?=lang('Global.destination')?></label>
                <div class="uk-form-controls">
                  <select class="uk-select" name="outlet" id="sel_out">
                    <option><?=lang('Global.destination')?></option>
                    <?php
                    foreach ($outlets as $outlet) {
                      if ($outlet['id'] === $outletPick) {
                        $checked = 'selected';
                      } else {
                        $checked = '';
                      }
                    ?>
                      <option value="<?= $outlet['id']; ?>" <?=$checked?>><?= $outlet['name']; ?></option>
                    <?php
                    }
                    ?>
                  </select>
                </div>
                <!-- End Of Ajax -->
                         
                <div class="uk-margin">
                    <label class="uk-form-label" for="address"><?=lang('Global.quantity')?></label>
                    <div class="uk-form-controls">
                        <input type="text" class="uk-input <?php if (session('errors.quantity')) : ?>tm-form-invalid<?php endif ?>" name="quantity" id="quantity" placeholder="<?=lang('Global.quantity')?>" required/>
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
  <table class="uk-table uk-table-justify uk-table-middle uk-table-divider uk-light">
    <thead>
      <tr>
        <th class="uk-text-center">No</th>
        <th class="uk-text-center"><?=lang('Global.product')?></th>
        <th class="uk-text-center"><?=lang('Global.origin')?></th>
        <th class="uk-text-center"><?=lang('Global.destination')?></th>
        <th class="uk-text-center"><?=lang('Global.quantity')?></th>
      </tr>
    </thead>
    <tbody>
      <?php $i = 1 ; ?>
      <?php foreach ($stockmoves as $stockmove) : ?>
        <tr>
          <td class="uk-text-center"><?= $i++; ?></td>
          <td class="uk-text-center"><?= $stockmove['product']; ?></td>
          <td class="uk-text-center"><?= $stockmove['origin']; ?></td>
          <td class="uk-text-center"><?= $stockmove['destination']; ?></td>
          <td class="uk-text-center"><?= $stockmove['quantity']; ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<!-- End Of Table Content -->

<?= $this->endSection() ?>