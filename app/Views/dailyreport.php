<?= $this->extend('layout') ?>
<?= $this->section('extraScript') ?>
    <link rel="stylesheet" href="css/code.jquery.com_ui_1.13.2_themes_base_jquery-ui.css">
    <script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
    <script src="js/code.jquery.com_jquery-3.6.0.js"></script>
    <script src="js/code.jquery.com_ui_1.13.2_jquery-ui.js"></script>
    <script src="js/jquery.validate.min.js"></script>
    <script src="js/cdnjs.cloudflare.com_ajax_libs_webcamjs_1.0.25_webcam.min.js"></script>
<?= $this->endSection() ?>
<?= $this->section('main') ?>

<!-- Page Heading -->
<div class="tm-card-header uk-light">
    <div class="uk-flex-middle">
        <h3 class="tm-h3"><?=lang('Global.dailyreportList')?></h3>
    </div>
</div>
<!-- End Of Page Heading -->

<!-- Table Of Content -->
<table class="uk-table uk-table-justify uk-table-middle uk-table-divider uk-light">
    <thead>
        <tr>
            <th class="uk-text-center"></th>
            <th class=""><?= lang('Global.outlet') ?></th>
            <th class=""><?= lang('Global.dateopen') ?></th>
            <th class=""><?= lang('Global.employeeopen') ?></th>
            <th class=""><?= lang('Global.dateclose') ?></th>
            <th class=""><?= lang('Global.employeeclose') ?></th>
            <th class=""><?= lang('Global.totalcashin') ?></th>
            <th class=""><?= lang('Global.totalcashout') ?></th>
            <th class=""><?= lang('Global.totalcashclose') ?></th>
            <th class=""><?= lang('Global.totalnoncashclose') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($dailyreports as $dayrep) { ?>
            <tr>
                <td class="uk-flex uk-flex-center">
                    <a class="uk-icon-link uk-icon" uk-icon="eye" uk-toggle="target:#detail-<?= $dayrep['id'] ?>"></a>
                </td>

                <td class="">
                    <?php foreach ($outlets as $outlet) {
                        if ($outlet['id'] === $dayrep['outletid']) { ?>
                            <?= $outlet['name'] ?>
                        <?php }
                    } ?>
                </td>

                <td><?= $dayrep['dateopen'] ?></td>

                <?php foreach ($users as $user) {
                    if ($user->id === $dayrep['useridopen']) { ?>
                        <td class=""><?= $fullname ?></td>
                    <?php }
                } ?>

                <td><?= $dayrep['dateclose'] ?></td>

                <?php foreach ($users as $user) {
                    if ($user->id === $dayrep['useridclose']) { ?>
                        <td class=""><?= $fullname ?></td>
                    <?php }
                } ?>

                <td><?= $dayrep['totalcashin'] ?></td>
                <td><?= $dayrep['totalcashout'] ?></td>
                <td><?= $dayrep['cashclose'] ?></td>
                <td><?= $dayrep['noncashclose'] ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>
<!-- Table Of Content End -->

<!-- Modal Detail -->
<!-- Modal Detail End -->
<?= $this->endSection() ?>