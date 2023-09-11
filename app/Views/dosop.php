<?= $this->extend('layout') ?>
<?= $this->section('main') ?>

<header class="tm-header" style="background-color:#000; border-top-left-radius: 40px;">
    <ul class="uk-flex-around tm-trx-tab uk-margin-top" uk-tab uk-switcher="connect: .switcher-class; active: 1;">
        <li>
            <a style="border-radius: 10px;" uk-switcher-item="0">
                <div width="45" height="30" uk-icon="sign-in"></div>
                <div class="uk-h4 uk-margin-small"><?= lang('Global.shift1') ?></div>
            </a>
        </li>
        <li>
            <a style="border-radius: 10px;" uk-switcher-item="0">
                <div width="45" height="30" uk-icon="sign-out"></div>
                <div class="uk-h4 uk-margin-small"><?= lang('Global.shift2') ?></div>
            </a>
        </li>
    </ul>
</header>

<?= view('Views/Auth/_message_block') ?>

<ul class="uk-switcher switcher-class">
    <!-- Table Shift 1 -->
    <li>
        <div class="uk-margin-top">
            <table class="uk-table uk-table-justify uk-table-middle uk-table-divider uk-light" id="example" style="width:100%">
                <thead>
                    <tr>
                        <th class="uk-width-small uk-text-center">No</th>
                        <th class="uk-width-large">To Do List</th>
                        <th class="uk-width-small uk-text-center"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1 ; ?>
                    <form class="uk-form-stacked" role="form" action="sop/updatetodo" method="post">
                        <?php foreach ($sops as $sop) {
                            if ($sop['shift'] === "0") { ?>
                                <tr>
                                    <td class="uk-width-small uk-text-center"><?= $i++; ?></td>
                                    <td class="uk-width-large"><?= $sop['name'] ?></td>
                                    <td class="uk-width-small uk-text-center">
                                        <div class="uk-form-controls">
                                            <?php foreach ($sopdetails as $sopdet) {
                                                if ($sopdet['sopid'] === $sop['id']) {
                                                    if ($sopdet['status'] === "0") { ?>
                                                        <input class="uk-checkbox" type="checkbox" name="status[<?= $sopdet['id']; ?>]">
                                                    <?php } else { ?>
                                                        <div uk-icon="check"></div>
                                                    <?php }
                                                 }
                                            } ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php }
                        } ?>
                        <tr>
                            <td class="uk-width-small uk-text-center"></td>
                            <td class="uk-width-large"></td>
                            <td class="uk-width-small uk-text-center">
                                <button type="submit" class="uk-button uk-button-primary uk-preserve-color" value="submit"><?= lang('Global.save') ?></button>
                            </td>
                        </tr>
                    </form>
                </tbody>
            </table>
        </div>
    </li>
    <!-- Table Shift 1 End -->

    <!-- Table Shift 2 -->
    <li>
        <div class="uk-margin-top">
            <table class="uk-table uk-table-justify uk-table-middle uk-table-divider uk-light" id="example" style="width:100%">
                <thead>
                    <tr>
                        <th class="uk-width-small uk-text-center">No</th>
                        <th class="uk-width-large">To Do List</th>
                        <th class="uk-width-small uk-text-center"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1 ; ?>
                    <form class="uk-form-stacked" role="form" action="sop/updatetodo" method="post">
                        <?php foreach ($sops as $sop) {
                            if ($sop['shift'] === "1") { ?>
                                <tr>
                                    <td class="uk-width-small uk-text-center"><?= $i++; ?></td>
                                    <td class="uk-width-large"><?= $sop['name'] ?></td>
                                    <td class="uk-width-small uk-text-center">
                                        <div class="uk-form-controls">
                                            <?php foreach ($sopdetails as $sopdet) {
                                                if ($sopdet['sopid'] === $sop['id']) {
                                                    if ($sopdet['status'] === "0") { ?>
                                                        <input class="uk-checkbox" type="checkbox" name="status[<?= $sopdet['id']; ?>]">
                                                    <?php } else { ?>
                                                        <div uk-icon="check"></div>
                                                    <?php }
                                                }
                                            } ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php }
                        } ?>
                        <tfoot>
                            <tr>
                                <td class="uk-width-small uk-text-center"></td>
                                <td class="uk-width-large"></td>
                                <td class="uk-width-small uk-text-center">
                                    <button type="submit" class="uk-button uk-button-primary uk-preserve-color" value="submit"><?= lang('Global.save') ?></button>
                                </td>
                            </tr>
                        </tfoot>
                    </form>
                </tbody>
            </table>
        </div>
    </li>
    <!-- Table Shift 2 End -->
</ul>
<?= $this->endSection() ?>