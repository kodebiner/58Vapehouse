<?= $this->extend('layout') ?>
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
                                    <input type="text" class="uk-input <?php if (session('errors.name')) : ?>tm-form-invalid<?php endif ?>" id="name" name="name" placeholder="<?=lang('Global.name')?>" autofocus required />
                                </div>
                            </div>

                            <div class="uk-margin-bottom">
                                <label class="uk-form-label" for="price"><?=lang('Global.price')?></label>
                                <div class="uk-form-controls">
                                    <input type="text" class="uk-input <?php if (session('errors.price')) : ?>tm-form-invalid<?php endif ?>" id="price" name="price" placeholder="<?=lang('Global.price')?>" autofocus required />
                                </div>
                            </div>

                            <label class="uk-form-label" for="variant"><?=lang('Global.variant')?></label>
                              <div class="uk-form-controls">
                                <select class="uk-select" name="variant" >
                                  <option><?=lang('Global.variant')?></option>
                                  <?php
                                  foreach ($variants as $variant) {
                                    foreach ($products as $product) {
                                    echo '<option value="'.$variant['id'].'">'.$variant['name']."-".$product['name'].'</option>';
                                    }
                                  }
                                  ?>
                                </select>
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
<!-- Search Box -->
<div class="uk-margin">
  <form class="uk-search uk-search-default">
    <span uk-search-icon></span>
    <input class="uk-search-input" id="inputBundle" onkeyup="searchBundle()" type="text" placeholder="Search" aria-label="Search">
  </form>
</div>
<!-- Search Box End -->

<div class="uk-overflow-auto">
  <table class="uk-table uk-table-justify uk-table-middle uk-table-divider uk-light" id="tableBundle">
    <thead>
      <tr>
        <th class="uk-text-center uk-width-small">No</th>
        <th class="uk-width-large"><?=lang('Global.name')?></th>
        <th class="uk-width-medium"><?=lang('Global.price')?></th>
        <th class="uk-text-center uk-width-large"><?=lang('Global.action')?></th>
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
              <button type="button" class="uk-button uk-button-primary uk-preserve-color" uk-toggle="target: #editdata<?= $bundle['id'] ?>"><?=lang('Global.edit')?></button>
            </div>
            <!-- End Of Button Trigger Modal Edit -->

            <!-- Button Delete -->
            <div>
              <a class="uk-button uk-button-default uk-button-danger uk-preserve-color" href="bundle/delete/<?= $bundle['id'] ?>" onclick="return confirm('<?=lang('Global.deleteConfirm')?>')"><?=lang('Global.delete')?></a>
            </div>
            <!-- End Of Button Delete -->
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

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
                  <input type="text" class="uk-input" id="name" name="name" value="<?= $bundle['name']; ?>"autofocus />
                </div>
              </div>

              <div class="uk-margin-bottom">
                <label class="uk-form-label" for="price"><?=lang('Global.price')?></label>
                <div class="uk-form-controls">
                  <input type="text" class="uk-input" id="price" name="price"  value="<?= $bundle['price']; ?>" autofocus />
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

<!-- Search Engine Script -->
<script>
  function searchBundle() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("inputBundle");
    filter = input.value.toUpperCase();
    table = document.getElementById("tableBundle");
    tr = table.getElementsByTagName("tr");
    for (i = 0; i < tr.length; i++) {
      td = tr[i].getElementsByTagName("td")[1];
      if (td) {
        txtValue = td.textContent || td.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
          tr[i].style.display = "";
        } else {
          tr[i].style.display = "none";
        }
      }       
    }
  }
</script>
<!-- Search Engine Script End -->

<?= $this->endSection() ?>