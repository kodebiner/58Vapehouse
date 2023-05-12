<?= $this->extend('layout') ?>

<?= $this->section('extraScript') ?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<?= $this->endSection() ?>

<?= $this->section('main') ?>

<!-- Page Heading -->
<div class="tm-card-header uk-light">
  <?= view('Views/Auth/_message_block') ?>

  <div uk-grid class="uk-flex-middle">
    <div class="uk-width-1-2@m">
      <h3 class="tm-h3"><?=lang('Global.stockList')?></h3>
    </div>

    <!-- Button Trigger Modal Add -->
    <div class="uk-width-1-2@m uk-text-right@m">
      <button type="button" class="uk-button uk-button-primary uk-preserve-color" uk-toggle="target: #tambahdata"><?=lang('Global.addRestock')?></button>
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
            <form class="uk-form-stacked" role="form" action="/stock/restock/" method="post">
              <?= csrf_field() ?>
                <!-- ajax -->

                    <!-- select oulet -->
                <label class="uk-form-label" for="outlet"><?=lang('Global.outlet')?></label>
                <div class="uk-form-controls">
                  <select class="uk-select" name="outlet" id="sel_out">
                    <option><?=lang('Global.outlet')?></option>
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
                
                <!-- select Product -->
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
                
                <!-- select variant -->
                <label class="uk-form-label" for="variant"><?=lang('Global.variant')?></label>
                <div class="uk-form-controls">
                  <select class="uk-select" name="variant" id="sel_variant">
                    <option id="default_var"><?=lang('Global.variant')?></option>
                  </select>
                </div>

                <!-- end of ajax -->

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
<div class="uk-margin">
  <form class="uk-search uk-search-default">
    <span uk-search-icon></span>
    <input class="uk-search-input" type="search" placeholder="Search" aria-label="Search">
  </form>
</div>

<div class="uk-overflow-auto">
  <table class="uk-table uk-table-justify uk-table-middle uk-table-divider uk-light">
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
  
  <!-- Table Pagination -->
  <ul class="uk-pagination uk-flex-right uk-margin-medium-top uk-light" uk-margin>
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
  </ul>
  <!-- Table Pagination End-->
</div>
<!-- End Of Table Content -->

<script>

  $(document).ready(function(){

// Country change
$("#sel_pro").change(function(){

     // Selected country id
     var productid = $(this).val();

     // Empty state and city dropdown
     //$('#sel_variant').find('option').not(':first').remove();

     // Fetch country states
     $.ajax({
          type: 'post',
          url: 'coba',
          data: {request:'getPro',productid:productid},
          dataType: 'json',
          success:function(response){

            console.log('success', arguments);

            var len = response.length;
            var variant = arguments[0][0];

            let option = '<option>Variant</option>';

            variant.forEach(itter);

            document.getElementById('sel_variant').innerHTML = option;

            function itter(value) {
              option += '<option value="'+value.id+'">'+value.name+'</option>';
            }
          }
     });
});

// Country change
$("#sel_variant").change(function(){

     // Selected country id
     var variantid = $(this).val();

     // Empty state and city dropdown
     //$('#sel_variant').find('option').not(':first').remove();

     // Fetch country states
     $.ajax({
          type: 'post',
          url: 'coba',
          data: {request:'getVariant',variantid:variantid},
          dataType: 'json',
          success:function(response){

            console.log('success', arguments);

            var len = response.length;
            var variant = arguments[0][0];

            // let option = '<option>Variant</option>';

            variant.forEach(itter);

            document.getElementById('sel_variant').innerHTML = option;

            function itter(value) {
              option += '<option value="'+value.id+'">'+value.name+'</option>';
            }
          }
     });
});


});
</script>

<?= $this->endSection() ?>