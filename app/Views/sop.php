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
            <h3 class="tm-h3"><?=lang('Global.sop')?></h3>
        </div>

        <!-- Button Trigger Modal Add -->
        <div class="uk-width-1-2@m uk-text-right@m">
            <button type="button" class="uk-button uk-button-primary uk-preserve-color" uk-toggle="target: #tambahdata"><?=lang('Global.addsop')?></button>
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
                <h5 class="uk-modal-title" id="tambahdata" ><?=lang('Global.sop')?></h5>
            </div>
            <div class="uk-modal-body">
                <form class="uk-form-stacked" role="form" action="/sop/create" method="post">
                    <?= csrf_field() ?>

                    <div class="uk-margin">
                        <label class="uk-form-label" for="form-stacked-text" required><?=lang('Global.sop')?></label>
                        <input class="uk-input" name="name" type="text" placeholder="Name" aria-label="Input" required/>
                    </div>

                    <div class="uk-margin">
                        <label class="uk-form-label" for="form-stacked-text" required><?=lang('Global.shift')?> </label>
                        <select class="uk-select" name="shift" aria-label="Select">
                            <option value="0">Open</option>
                            <option value="1">Closed</option>
                        </select>
                    </div>

                    <hr>
                    <div class="uk-margin">
                        <button type="submit" class="uk-button uk-button-primary"><?=lang('Global.shift')?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- End Of Modal Add -->

<!-- Table Of Content -->
<div class="uk-overflow-auto uk-margin">
    <table class="uk-table uk-table-justify uk-table-middle uk-table-divider uk-light" id="example">
        <thead>
            <tr>
                <th class="uk-text-center">No</th>
                <th class=""><?=lang('Global.sop')?></th>
                <th class="uk-text-center"><?=lang('Global.shift')?></th>
                <th class="uk-text-center"><?=lang('Global.action')?></th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1 ; ?>
            <?php foreach ($sops as $sop) : ?>
                <tr>
                    <td class="uk-text-center"><?= $i++; ?></td>
                    <td class="uk-text-left"><?= $sop['name']; ?></td>
                    <td class="uk-text-center">
                        <?php if ($sop['shift'] === "0" ){
                            echo "Open";
                        } else{
                            echo "Closed";
                        } ?>
                    </td>
                    <td class="uk-text-center">
                        <a class="uk-icon-button" uk-icon="pencil" uk-toggle="target: #editcat<?= $sop['id'] ?>"></a>
                        <a class="uk-icon-button-delete" uk-icon="trash" href="product/deletecat/<?= $sop['id'] ?>"></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<!-- End Table Content -->

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