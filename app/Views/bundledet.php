<?= $this->extend('layout') ?>

<?= $this->section('extraScript') ?>
<script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
<script src="js/cdn.datatables.net_1.13.4_js_jquery.dataTables.min.js"></script>
<script src="js/code.jquery.com_ui_1.13.2_jquery-ui.js"></script>
<?= $this->endSection() ?>

<?= $this->section('main') ?>

<!-- Page Heading -->
<div class="tm-card-header uk-light">
  <?= view('Views/Auth/_message_block') ?>

  <div uk-grid class="uk-flex-middle">
    <div class="uk-width-1-2@m">
      <h3 class="tm-h3"><?=lang('Global.bundledetailList')?> <?= $bundles['name']; ?></h3>
    </div>

    <!-- Button Trigger Modal Add -->
    <div class="uk-width-1-2@m uk-text-right@m">
      <button type="button" class="uk-button uk-button-primary uk-preserve-color" uk-toggle="target: #tambahdata"><?=lang('Global.addProduct')?></button>
    </div>
    <!-- End Of Button Trigger Modal Add -->
  </div>
</div>
<!-- End Of Page Heading -->

<!-- Modal Add -->
<div uk-modal class="uk-flex-top" id="tambahdata">
  <div class="uk-modal-dialog uk-margin-auto-vertical">
    <div class="uk-modal-content">
      <div class="uk-modal-header">
        <h5 class="uk-modal-title" id="tambahdata" ><?=lang('Global.addVariant')?></h5>
      </div>
      <div class="uk-modal-body">
        <form class="uk-form-stacked" role="form" action="bundle/createbund/<?= $bundles['id']; ?>" method="post">
          <?= csrf_field() ?>

            <div id="createBundle" class="uk-margin-bottom">
                <h4 class="tm-h4 uk-margin-remove"><?=lang('Global.variant')?></h4>
                <?php
                    $combProducts = [];
                    foreach ($variants as $variant) {
                        foreach ($products as $product) {
                            if ($variant['productid'] === $product['id']) {
                                $combProducts[] = [$variant['id'] => $product['name'].' - '.$variant['name']];
                            }
                        }
                    }
                ?>
                <div id="variantcontainer0" class="uk-margin-small" uk-grid>
                    <div class="uk-width-5-6">
                        <input class="uk-input" id="productvariantname0" required/>
                        <input id="variantid0" name="variantid[0]" hidden/>
                    </div>
                </div>
                <script type="text/javascript">
                    $(function() {
                        var combProduct = [
                            <?php
                                foreach ($combProducts as $combProduct) {
                                    foreach ($combProduct as $key => $value) {
                                        echo '{label:"'.$value.'", idx:'.$key.'},';
                                    }
                                }
                            ?>
                        ];
                        $("#productvariantname0").autocomplete({
                            source: combProduct,
                            select: function(e, i) {
                                $('#variantid0').val(i.item.idx);
                            },
                            minLength: 2
                        });
                    });
                </script>
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
        <th class="uk-text-center uk-width-small">No</th>
        <th class="uk-width-large"><?=lang('Global.name')?></th>
        <th class="uk-text-center uk-width-small"><?=lang('Global.action')?></th>
      </tr>
    </thead>
    <tbody>
        <?php $i = 1 ; ?>
        <?php foreach ($bundledet as $bundled) : ?>
            <?php 
              foreach ($variants as $variant) {
                if ($variant['id'] === $bundled['variantid']) {
                  $varname = $variant['name'];
                  foreach ($products as $product) {
                    if ($variant['productid'] === $product['id']) {
                      $ProdName = $product['name'];
                    }
                  }
                }
              }
            ?>
          <tr>
            <td class="uk-text-center"><?= $i++; ?></td>
            <td class=""><?= $ProdName.' - '.$varname ?></td>
            <td class="uk-text-center">
              <!-- Button Delete -->
                <a uk-icon="trash" class="uk-icon-button-delete" href="bundle/deletebund/<?= $bundled['variantid'] ?>" onclick="return confirm('<?=lang('Global.deleteConfirm')?>')"></a>
              <!-- End Of Button Delete -->
            </td>
          </tr>
        <?php endforeach; ?>
    </tbody>
  </table>
</div>
<!-- End Of Table Content -->

<!-- Search Engine Script -->
<script>
  $(document).ready(function () {
    $('#example').DataTable();
  });
</script>
<!-- Search Engine Script End -->
<?= $this->endSection() ?>