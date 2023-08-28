<?= $this->extend('layout') ?>

<?= $this->section('extraScript') ?>
<script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
<script src="js/cdn.datatables.net_1.13.4_js_jquery.dataTables.min.js"></script>
<?= $this->endSection() ?>

<?= $this->section('main') ?>

<!-- Page Heading -->
<div class="tm-card-header uk-light">
    <?= view('Views/Auth/_message_block') ?>

    <div uk-grid class="uk-flex-middle">
        <div class="uk-width-1-2@m">
            <h3 class="tm-h3"><?=lang('Global.purchaseList')?></h3>
        </div>

        <?php if ($outletPick != null) { ?>
            <!-- Button Trigger Modal Add -->
            <!-- Button Trigger Modal Add End -->
        <?php } ?>
    </div>
</div>
<!-- Page Heading End -->

<!-- Table Of Content -->
<div class="uk-overflow-auto uk-margin">
  <table class="uk-table uk-table-justify uk-table-middle uk-table-divider uk-light" id="example">
    <thead>
      <tr>
        <th class="uk-text-center">No</th>
        <th class=""><?=lang('Global.product')?></th>
        <th class="uk-text-center"><?=lang('Global.variant')?></th>
        <th class="uk-text-center"><?=lang('Global.reminder')?></th>
      </tr>
    </thead>
    <tbody>
      <?php $i = 1 ; ?>
      <?php foreach ($stocks as $stock) {
        $today= $stock['restock'];
        $date = date_create($today);
        date_add($date, date_interval_create_from_date_string('30 days'));
        $newdate = date_format($date, 'Y/m/d H:i:s');
        if ($stock['sale'] > $newdate){
          $remindate = "The product has not been sold for 1 month";
        }
        if ($stock['qty'] < "1"){
          $remindqty = "Stock Is Running Out";
        } 
        ?>
        <tr>
          <td class="uk-text-center"><?= $i++; ?></td>
          <td class="uk-text-left">
            <?php 
            foreach ($products as $product){
              foreach($variants as $variant){
              if ($variant['id'] === $stock['variantid'] && $variant['productid'] === $product['id']){
                echo $product['name'];
              }
              }
            }
            ?>
          </td>
          <td class="uk-text-center">
          <?php 
            foreach($variants as $variant){
              if ($variant['id'] === $stock['variantid']){
                echo $variant['name'];
              }
            }    
          ?>
          </td>
          <td class="uk-text-center">
            <?php
            if ($stock['sale'] > $newdate){
              echo "The product has not been sold for 1 month" ; 
            }elseif($stock['qty'] < "1"){
              echo  "Stock Is Running Out" ; 
            }
            ?>
          </td>
        </tr>
      <?php }?>
    </tbody>
  </table>
  <!-- End Table Content -->
</div>

<script>
</script>

<!-- Search Engine Script -->
<script>
  $(document).ready(function () {
    $('#example').DataTable();
  });
</script>
<!-- Search Engine Script End -->

<?= $this->endSection() ?>