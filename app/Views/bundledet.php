<?= $this->extend('layout') ?>

<?= $this->section('extraScript') ?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
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
                    <div class="uk-text-right">
                        <a onclick="createNewBundle()">+ Add More Variant</a>
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
  </div>
</div>
<!-- End Of Page Heading -->

<!-- Table Of Content -->
<!-- Search Box -->
<div class="uk-margin">
  <form class="uk-search uk-search-default">
    <span uk-search-icon></span>
    <input class="uk-search-input" id="inputVar" onkeyup="searchVar()" type="text" placeholder="Search" aria-label="Search">
  </form>
</div>
<!-- Search Box End -->

<div class="uk-overflow-auto">
  <table class="uk-table uk-table-justify uk-table-middle uk-table-divider uk-light" id="tableVar">
    <thead>
      <tr>
        <th class="uk-text-center uk-width-small">No</th>
        <th class="uk-width-large"><?=lang('Global.name')?></th>
        <th class="uk-text-center uk-width-medium"><?=lang('Global.action')?></th>
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
                <a class="uk-button uk-button-default uk-button-danger uk-preserve-color" href="bundle/deletebund/<?= $variant['id'] ?>" onclick="return confirm('<?=lang('Global.deleteConfirm')?>')"><?=lang('Global.delete')?></a>
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
</div>
<!-- End Of Table Content -->

<!-- Search Engine Script -->
<script>
  function searchVar() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("inputVar");
    filter = input.value.toUpperCase();
    table = document.getElementById("tableVar");
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