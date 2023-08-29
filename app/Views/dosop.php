<?= $this->extend('layout') ?>
<?= $this->section('main') ?>

<header class="tm-header" style="background-color:#000; border-top-left-radius: 40px;">
    <ul class="uk-flex-around tm-trx-tab uk-margin-top" uk-tab uk-switcher="connect: .switcher-class; active: 1;">
        <li>
            <a style="border-radius: 10px;" uk-switcher-item="0">
                <div width="45" height="30" uk-icon="sign-in"></div>
                <div class="uk-h4 uk-margin-small">Shift 1</div>
            </a>
        </li>
        <li>
            <a style="border-radius: 10px;" uk-switcher-item="0">
                <div width="45" height="30" uk-icon="sign-out"></div>
                <div class="uk-h4 uk-margin-small">Shift 2</div>
            </a>
        </li>
    </ul>
</header>

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
                        <?php foreach ($sops as $sop) { ?>
                            <?php if ($sop['shift'] === "0") { ?>
                                <tr>
                                    <td class="uk-width-small uk-text-center"><?= $i++; ?></td>
                                    <td class="uk-width-large"><?= $sop['name'] ?></td>
                                    <td class="uk-width-small uk-text-center">
                                        <div class="uk-form-controls">
                                            <?php foreach ($sopdetails as $sopdet) { ?>
                                                <?php if ($sopdet['sopid'] === $sop['id']) { ?>
                                                    <?php if ($sopdet['status'] === "0") { ?>
                                                        <input class="uk-checkbox" type="checkbox" name="status[<?= $sopdet['id']; ?>]">
                                                    <?php } else { ?>
                                                        <div uk-icon="check"></div>
                                                    <?php } ?>
                                                <?php } ?>
                                            <?php } ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } ?>
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
                        <?php foreach ($sops as $sop) { ?>
                            <?php if ($sop['shift'] === "1") { ?>
                                <tr>
                                    <td class="uk-width-small uk-text-center"><?= $i++; ?></td>
                                    <td class="uk-width-large"><?= $sop['name'] ?></td>
                                    <td class="uk-width-small uk-text-center">
                                        <div class="uk-form-controls">
                                            <?php foreach ($sopdetails as $sopdet) { ?>
                                                <?php if ($sopdet['sopid'] === $sop['id']) { ?>
                                                    <?php if ($sopdet['status'] === "0") { ?>
                                                        <input class="uk-checkbox" type="checkbox" name="status[<?= $sopdet['id']; ?>]">
                                                    <?php } else { ?>
                                                        <div uk-icon="check"></div>
                                                    <?php } ?>
                                                <?php } ?>
                                            <?php } ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } ?>
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
<!-- Content -->
<!-- <div class="uk-container uk-margin-xlarge-top">
    <div class="uk-child-width-auto uk-flex-center uk-flex uk-grid" uk-grid>
        <div class="">
            <button type="button" class="uk-button uk-button-primary uk-button-large uk-preserve-color" style="width: 223px; border-radius: 15px; font-size: 25.5px;" uk-toggle="target: #shift1">Shift 1</button>
        </div>
        <div class="">
            <button type="button" class="uk-button uk-button-primary uk-button-large uk-preserve-color" style="width: 223px; border-radius: 15px; font-size: 25.5px;" uk-toggle="target: #shift2">Shift 2</button>
        </div>
    </div>
</div> -->
<!-- Content End -->

<!-- Modal Shift 1 -->
<!-- <div uk-modal class="uk-flex-top" id="shift1">
    <div class="uk-modal-dialog">
        <button class="uk-modal-close-default" type="button" uk-close></button>
        <div class="uk-modal-header">
            <h2 class="uk-modal-title">Shift 1</h2>
        </div>
        <div class="uk-modal-body">
            <form class="uk-form-horizontal uk-margin-large" action="sop/createtodo" method="post">
                <?php foreach ($sops as $sop) {?>
                    <?php if ($sop['shift'] === "0") { ?>
                        <div class="uk-margin">
                            <label class="uk-form-label" for="form-horizontal-text"><?= $sop['name'] ?></label>
                            <input type="text" name="sopid[<?= $sop['id']; ?>]" value="<?=$sop['id']?>" hidden>
                            <div class="uk-form-controls">
                                <input class="uk-checkbox" type="checkbox"  name="status[<?= $sop['id']; ?>]" value="1">
                            </div>
                        </div>
                    <?php } ?>
                <?php } ?>
                <hr>
                <div class="uk-margin">
                    <button class="uk-button uk-button-primary" type="submit" value="submit">submit</button>
                    <a href="#modal-group-2" class="uk-button uk-button-primary" uk-toggle>Next</a>
                </div>
            </form>
        </div>
    </div>
</div> -->
<!-- Modal Shift 1 End -->

<!-- Modal Shift 2 -->
<!-- <div uk-modal class="uk-flex-top" id="shift2">
    <div class="uk-modal-dialog">
        <button class="uk-modal-close-default" type="button" uk-close></button>
        <div class="uk-modal-header">
            <h2 class="uk-modal-title">Shift 2</h2>
        </div>
        <div class="uk-modal-body">
        <form class="uk-form-horizontal uk-margin-large" action="sop/createtodo" method="post">
                <?php foreach ($sops as $sop){?>
                    <?php if ($sop['shift'] === "1") { ?>
                        <div class="uk-margin">
                            <label class="uk-form-label" for="form-horizontal-text"><?= $sop['name'] ?></label>
                            <input type="text" name="sopid[<?= $sop['id']; ?>]" value="<?=$sop['id']?>" hidden>
                            <div class="uk-form-controls">
                                <input class="uk-checkbox" type="checkbox"  name="status[<?= $sop['id']; ?>]" value="1">
                            </div>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
            <div class="uk-modal-footer uk-text-right">
                <button class="uk-button uk-button-primary" type="submit" value="submit">submit</button>
                <a href="#modal-group-1" class="uk-button uk-button-primary" uk-toggle>Previous</a>
            </div>
        </form>
    </div>
</div> -->
<!-- Modal Shift 2 End -->
<?= $this->endSection() ?>