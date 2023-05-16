<?= $this->extend('layout') ?>
<?= $this->section('main') ?>

<!-- Page Heading -->
<div class="tm-card-header uk-light">
    <?= view('Views/Auth/_message_block') ?>

    <div uk-grid class="uk-flex-middle">
        <div class="uk-width-1-2@m">
            <h3 class="tm-h3"><?=lang('Global.cashmanList')?></h3>
        </div>

        <!-- Button Trigger Modal Add -->
        <div class="uk-width-1-2@m uk-text-right@m">
            <button type="button" class="uk-button uk-button-primary uk-preserve-color" uk-toggle="target: #tambahdata"><?=lang('Global.addCashMan')?></button>
        </div>
        <!-- End Of Button Trigger Modal Add -->

        <!-- Modal Add -->
        <div uk-modal class="uk-flex-top" id="tambahdata">
            <div class="uk-modal-dialog uk-margin-auto-vertical">
                <div class="uk-modal-content">
                    <div class="uk-modal-header">
                        <h5 class="uk-modal-title" id="tambahdata" ><?=lang('Global.addCashMan')?></h5>
                    </div>
                    <div class="uk-modal-body">
                        <form class="uk-form-stacked" role="form" action="cashman/create" method="post">
                            <?= csrf_field() ?>

                            <!-- select oulet -->
                            <div class="uk-margin-bottom">
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
                            </div>

                            <div class="uk-margin-bottom">
                                <label class="uk-form-label" for="name"><?=lang('Global.description')?></label>
                                <div class="uk-form-controls">
                                    <input type="text" class="uk-input <?php if (session('errors.description')) : ?>tm-form-invalid<?php endif ?>" id="name" name="name" placeholder="<?=lang('Global.description')?>" autofocus required />
                                </div>
                            </div>
                            
                            <div class="uk-margin">
                                <label class="uk-form-label" for="type"><?=lang('Global.type')?></label>
                                <div class="uk-form-controls">
                                    <select class="uk-select" name="type">
                                        <option><?=lang('Global.type')?></option>
                                        <option name="type" value="0" ><?=lang('Global.cashin')?></option>
                                        <option name="type" value="1" ><?=lang('Global.cashout')?></option>
                                    </select>
                                </div>
                            </div>

                            <div class="uk-margin">
                                <label class="uk-form-label" for="qty"><?=lang('Global.quantity')?></label>
                                <div class="uk-form-controls">
                                    <input type="text" class="uk-input <?php if (session('errors.qty')) : ?>tm-form-invalid<?php endif ?>" name="qty" id="qty" placeholder="<?=lang('Global.quantity')?>" required/>
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
<!-- Search Box -->
<div class="uk-margin">
    <form class="uk-search uk-search-default">
        <span uk-search-icon></span>
        <input class="uk-search-input" id="inputCash" onkeyup="searchCash()" type="text" placeholder="Search" aria-label="Search">
    </form>
</div>
<!-- Search Box End -->

<div class="uk-overflow-auto">
    <table class="uk-table uk-table-justify uk-table-middle uk-table-divider uk-light" id="tableCash">
        <thead>
            <tr>
                <th class="uk-text-center uk-width-small">No</th>
                <th class="uk-width-large"><?=lang('Global.description')?></th>
                <th class="uk-width-medium"><?=lang('Global.outlet')?></th>
                <th class="uk-width-medium"><?=lang('Global.type')?></th>
                <th class="uk-width-small"><?=lang('Global.date')?></th>
                <th class="uk-width-medium"><?=lang('Global.employee')?></th>
                <th class="uk-text-center uk-width-small"><?=lang('Global.quantity')?></th>
                <th class="uk-text-center uk-width-large"><?=lang('Global.action')?></th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1 ; ?>
            <?php foreach ($cashmans as $cash) : ?>
                <tr>
                    <td class="uk-text-center"><?= $i++; ?></td>
                    <td><?= $cash['name']; ?></td>
                    <td>
                        <?php foreach ($outlets as $outlet) {
                            if ($outlet['id'] === $cash['outletid']) {
                                echo $outlet['name'];
                            }
                        } ?>
                    </td>
                    <td>
                        <?php if ($cash['type'] === '0' ) { 
                            echo lang('Global.cashin');
                        } elseif ($cash['type'] === '1' ) { 
                            echo lang('Global.cashout');}
                        ?>
                            
                    </td>
                    <td><?= $cash['date']; ?></td>
                    <td>
                        <?php foreach ($users as $user) {
                            if ($user->id === $cash['userid']) {
                                echo $user->name;
                            }
                        } ?>
                    </td>
                    <td class="uk-text-center"><?= $cash['qty']; ?></td>
                    <td class="uk-child-width-auto uk-flex-center uk-grid-row-small uk-grid-column-small" uk-grid>
                        <!-- Button Trigger Modal Edit -->
                        <div>
                            <button type="button" class="uk-button uk-button-primary uk-preserve-color" uk-toggle="target: #editdata<?= $cash['id'] ?>"><?=lang('Global.edit')?></button>
                        </div>
                        <!-- End Of Button Trigger Modal Edit -->

                        <!-- Button Delete -->
                        <div>
                            <a class="uk-button uk-button-default uk-button-danger uk-preserve-color" href="cashman/delete/<?= $cash['id'] ?>" onclick="return confirm('<?=lang('Global.deleteConfirm')?>')"><?=lang('Global.delete')?></a>
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
    <?php foreach ($cashmans as $cash) : ?>
        <div uk-modal class="uk-flex-top" id="editdata<?= $cash['id'] ?>">
            <div class="uk-modal-dialog uk-margin-auto-vertical">
                <div class="uk-modal-content">
                    <div class="uk-modal-header">
                        <h5 class="uk-modal-title" id="editdata"><?=lang('Global.updateData')?></h5>
                    </div>

                    <div class="uk-modal-body">
                        <form class="uk-form-stacked" role="form" action="cashman/update/<?= $cash['id'] ?>" method="post">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id" value="<?= $cash['id']; ?>">
                            
                            <div class="uk-margin">
                                <label class="uk-form-label" for="outlet"><?=lang('Global.outlet')?></label>
                                <div class="uk-form-controls">
                                    <select class="uk-select" name="outlet">
                                        <option disabled><?=lang('Global.outlet')?></option>
                                        <?php foreach ($outlets as $outlet) { ?>
                                            <option value="<?= $outlet['id']; ?>" <?php if ($outlet['id'] === $cash['outletid']) {echo 'selected';} ?>><?= $outlet['name']; ?></option>
                                        <?php }?>
                                    </select>
                                </div>
                            </div>

                            <div class="uk-margin-bottom">
                                <label class="uk-form-label" for="name"><?=lang('Global.name')?></label>
                                <div class="uk-form-controls">
                                    <input type="text" class="uk-input" id="name" name="name" value="<?= $cash['name']; ?>"autofocus />
                                </div>
                            </div>
                            
                            <div class="uk-margin">
                                <label class="uk-form-label" for="type"><?=lang('Global.type')?></label>
                                <div class="uk-form-controls uk-grid-small uk-child-width-auto uk-grid">
                                    <label><input class="uk-radio" type="radio" name="type" value="0" <?php if ($cash['type'] === '0') { echo 'checked'; } ?>> <?=lang('Global.cashin')?></label>
                                    <label><input class="uk-radio" type="radio" name="type" value="1" <?php if ($cash['type'] === '1') { echo 'checked'; } ?>> <?=lang('Global.cashout')?></label>
                                </div>
                            </div>

                            <div class="uk-margin">
                                <label class="uk-form-label" for="qty"><?=lang('Global.quantity')?></label>
                                <div class="uk-form-controls">
                                    <input type="text" class="uk-input" id="qty" name="qty" value="<?= $cash['qty']; ?>"autofocus />
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
    function searchCash() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("inputCash");
        filter = input.value.toUpperCase();
        table = document.getElementById("tableCash");
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