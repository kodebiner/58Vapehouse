<?= $this->extend('layout') ?>

<?= $this->section('extraScript') ?>
<script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
<script src="js/cdn.datatables.net_1.13.4_js_jquery.dataTables.min.js"></script>
<?= $this->endSection() ?>

<?= $this->section('main') ?>

<!-- Page Heading -->
<div class="tm-card-header uk-light uk-margin-bottom">
    <div uk-grid class="uk-flex-middle">
        <div class="uk-width-1-2@m">
            <h3 class="tm-h3"><?=lang('Global.presence')?> <?=lang('Global.report')?></h3>
        </div>
    </div>
</div>

<table class="uk-table uk-table-divider uk-table-responsive uk-margin-top" id="example">
    <thead>
        <tr>
            <th class="uk-text-large uk-text-bold">Name</th>
            <th class="uk-text-center uk-text-large uk-text-bold">Access</th>
            <th class="uk-text-center uk-text-large uk-text-bold"><?=lang('Global.presence')?></th>
            <th class="uk-text-center uk-text-large uk-text-bold"><?=lang('Global.detail')?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($presences as $presence){ ?>
            <tr>
                <td style="color:white;"><?=$presence['name']?></td>
                <td class="uk-text-center" style="color:white;"><?=$presence['role']?></td>
                <td class="uk-text-center" style="color:white;"><?=$present?></td>
                <td class="uk-text-center"><a class="uk-icon-link uk-margin-small-right" uk-icon="icon: eye;" href="report/presence/<?=$presence['id']?>"></a></td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<!-- End Of Page Heading -->
<script>
    $(document).ready(function () {
        $('#example').DataTable();
    });
</script>
<?= view('Views/Auth/_message_block') ?>

<?= $this->endSection() ?>