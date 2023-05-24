<?= $this->extend('layout') ?>

<?= $this->section('extraScript') ?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<?= $this->endSection() ?>

<?= $this->section('main') ?>

<!-- Page Heading -->
<div class="tm-card-header uk-light">
  <?= view('Views/Auth/_message_block') ?>

  <div uk-grid class="uk-flex-middle">
    <div class="uk-width-1-2@m">
      <h3 class="tm-h3"><?=lang('Global.bundleList')?></h3>
    </div>

    <!-- Button Trigger Modal Add -->
    <div class="uk-width-1-2@m uk-text-right@m">
      <button type="button" class="uk-button uk-button-primary uk-preserve-color" uk-toggle="target: #tambahdata"><?=lang('Global.addBundle')?></button>
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
        <h5 class="uk-modal-title" id="tambahdata" ><?=lang('Global.addBundle')?></h5>
      </div>
      <div class="uk-modal-body">
        <form class="uk-form-stacked" role="form" action="bundle/create" method="post">
          <?= csrf_field() ?>

          <div class="uk-margin-bottom">
            <label class="uk-form-label" for="name"><?=lang('Global.name')?></label>
            <div class="uk-form-controls">
              <input type="text" class="uk-input <?php if (session('errors.name')) : ?>tm-form-invalid<?php endif ?>" id="name" name="name" placeholder="<?=lang('Global.name')?>" required />
            </div>
          </div>

          <div class="uk-margin-bottom">
            <label class="uk-form-label" for="price"><?=lang('Global.price')?></label>
            <div class="uk-form-controls">
              <input type="text" class="uk-input <?php if (session('errors.price')) : ?>tm-form-invalid<?php endif ?>" id="price" name="price" placeholder="<?=lang('Global.price')?>" required />
            </div>
          </div>

          <div id="createBundle" class="uk-margin-bottom">
            <h4 class="tm-h4 uk-margin-remove"><?=lang('Global.bundle')?></h4>
            <div class="uk-text-right">
              <a onclick="createNewBundle()">+ Add More Bundle</a>
            </div>
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

              var variantidx = 0;
              function createNewBundle() {
                variantidx ++;
                const bundlecontainer = document.getElementById('createBundle');

                const variantcontainer = document.createElement('div');
                variantcontainer.setAttribute('id', 'variantcontainer'+variantidx);
                variantcontainer.setAttribute('class', 'uk-margin-small');
                variantcontainer.setAttribute('uk-grid', '');

                const formcontainer = document.createElement('div');
                formcontainer.setAttribute('class', 'uk-width-5-6');

                const variantname = document.createElement('input');
                variantname.setAttribute('id', 'productvariantname'+variantidx);
                variantname.setAttribute('class', 'uk-input');

                const variantid = document.createElement('input');
                variantid.setAttribute('id', 'variantid'+variantidx);
                variantid.setAttribute('name', 'variantid['+variantidx+']');
                variantid.setAttribute('hidden', '');

                const closecontainer = document.createElement('div');
                closecontainer.setAttribute('class', 'uk-width-1-6 uk-flex uk-flex-middle');

                const closebutton = document.createElement('a');
                closebutton.setAttribute('class', 'uk-text-danger');
                closebutton.setAttribute('onclick', 'removeVariant('+variantidx+')');
                closebutton.setAttribute('uk-icon', 'close');

                formcontainer.appendChild(variantname);
                formcontainer.appendChild(variantid);
                closecontainer.appendChild(closebutton);
                variantcontainer.appendChild(formcontainer);
                variantcontainer.appendChild(closecontainer);
                bundlecontainer.appendChild(variantcontainer);

                $(function() {
                  var combProductArr = [
                    <?php
                      foreach ($combProducts as $combProduct) {
                        foreach ($combProduct as $key => $value) {
                          echo '{label:"'.$value.'", idx:'.$key.'},';
                        }
                      }
                    ?>
                  ];
                  $("#productvariantname"+variantidx).autocomplete({
                    source: combProductArr,
                    select: function(e, i) {
                      $('#variantid'+variantidx).val(i.item.idx);
                    },
                    minLength: 2
                  });
                });
              };
                              
              function removeVariant(i) {
                variant = document.getElementById('variantcontainer'+i);
                variant.remove();
              }
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
  <table class="uk-table uk-table-justify uk-table-middle uk-table-divider uk-light"  id="example" style="width:100%">
    <thead>
      <tr>
        <th class="uk-text-center uk-width-small">No</th>
        <th class="uk-width-large"><?=lang('Global.name')?></th>
        <th class="uk-width-medium"><?=lang('Global.price')?></th>
        <th class="uk-text-center uk-width-small"><?=lang('Global.action')?></th>
      </tr>
    </thead>
    <tbody>
      <?php $i = 1 ; ?>
      <?php foreach ($bundles as $bundle) : ?>
        <tr>
          <td class="uk-text-center"><?= $i++; ?></td>
          <td><?= $bundle['name']; ?></td>
          <td><?= $bundle['price']; ?></td>
          <td class="uk-child-width-auto uk-flex-center uk-grid-row-small uk-grid-column-small" uk-grid>
            <!-- Button Trigger Modal Edit -->
            <div>
              <a class="uk-icon-button" uk-icon="pencil" uk-toggle="target: #editdata<?= $bundle['id'] ?>"></a>
            </div>
            <!-- End Of Button Trigger Modal Edit -->

            <!-- Button Delete -->
            <div>
              <a uk-icon="trash" class="uk-icon-button-delete" href="bundle/delete/<?= $bundle['id'] ?>" onclick="return confirm('<?=lang('Global.deleteConfirm')?>')"></a>
            </div>
            <!-- End Of Button Delete -->
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<!-- End Of Table Content -->

<!-- Modal Edit -->
<?php foreach ($bundles as $bundle) : ?>
  <div uk-modal class="uk-flex-top" id="editdata<?= $bundle['id'] ?>">
    <div class="uk-modal-dialog uk-margin-auto-vertical">
      <div class="uk-modal-content">
        <div class="uk-modal-header">
          <h5 class="uk-modal-title" id="editdata"><?=lang('Global.updateData')?></h5>
        </div>

        <div class="uk-modal-body">
          <form class="uk-form-stacked" role="form" action="bundle/update/<?= $bundle['id'] ?>" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= $bundle['id']; ?>">

            <div class="uk-margin-bottom">
              <label class="uk-form-label" for="name"><?=lang('Global.name')?></label>
              <div class="uk-form-controls">
                <input type="text" class="uk-input" id="name" name="name" value="<?= $bundle['name']; ?>"/>
              </div>
            </div>

            <div class="uk-margin-bottom">
              <label class="uk-form-label" for="price"><?=lang('Global.price')?></label>
              <div class="uk-form-controls">
                <input type="text" class="uk-input" id="price" name="price"  value="<?= $bundle['price']; ?>"/>
              </div>
            </div>

            <div class="uk-margin-bottom">
              <h4 class="tm-h4 uk-margin-remove"><?=lang('Global.bundledetail')?></h4>
              <div class="uk-h6 uk-margin-remove">
                <?=lang('Global.editBundDet')?><a href="bundle/indexbund/<?= $bundle['id']; ?>"><?=lang('Global.manageBundDet')?></a>
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

<!-- Search Engine Script -->
<script>
  $(document).ready(function () {
    $('#example').DataTable();
  });
</script>
<!-- Search Engine Script End -->
<?= $this->endSection() ?>