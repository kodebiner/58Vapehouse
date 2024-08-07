<?= $this->extend('layout') ?>

<?= $this->section('extraScript') ?>
<script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
<?= $this->endSection() ?>

<?= $this->section('main') ?>

<!-- Page Heading -->
<div class="tm-card-header uk-light">
    <div uk-grid class="uk-flex-middle">
        <div class="uk-width-1-2@m">
            <h3 class="tm-h3"><?=lang('Global.stockCycle')?></h3>
        </div>
    </div>
</div>
<!-- End Of Page Heading -->

<?= view('Views/Auth/_message_block') ?>

<!-- Table Of Content -->
<div class="uk-overflow-auto uk-margin">
    <table class="uk-table uk-table-justify uk-table-middle uk-table-divider uk-light">
        <thead>
            <tr>
                <!-- <th class="uk-text-center">No</th> -->
                <?php
                if ($outletPick == null) { ?>
                    <th class=""><?=lang('Global.outlet')?></th>
                <?php
                }
                ?>
                <th class=""><?=lang('Global.name')?></th>
                <th class=""><?=lang('Global.restock')?></th>
                <th class=""><?=lang('Global.sale')?></th>
                <th class=""><?=lang('Global.quantity')?></th>
                <th class=""><?=lang('Global.information')?></th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1 ; ?>
            <?php foreach ($stocks as $stock) {
                // $today      = $stock['sale'];
                // $date       = date_create($today);
                // $now        = date_create();
                // $nowdates   = date_format($now,'Y-m-d H:i:s');
                // $todays     = strtotime($today);
                // $dates      = strtotime($nowdates);
                // date_add($date, date_interval_create_from_date_string('0 days'));
                // $newdate        = date_format($date, 'Y-m-d H:i:s');
                $origin         = new DateTime($stock['sale']);
                // $restock        = new DateTime($stock['restock']);
                $target         = new DateTime('now');
                $interval       = $origin->diff($target);
                $formatday      = substr($interval->format('%R%a'), 1);
                $saleremind     = lang('Global.saleremind');
                // $restockremind  = lang('Global.restockremind');
                // $intervals      = $restock->diff($target);
                // dd($newdate);

                // if ($stock['sale'] > $newdate) {
                    if ($formatday >= 0) { ?>
                        <tr>
                            <?php
                            if ($outletPick == null) { ?>
                                <td class=""><?= $stock['outlet'] ?></td>
                            <?php
                            }
                            ?>
                            <!-- <td class="uk-text-center"><?//= $i++; ?></td> -->
                            <td class=""><?= $stock['name'] ?></td>
                            <td class=""><?= date('l, d M Y, H:i:s', strtotime($stock['restock'])); ?></td>
                            <td class=""><?= date('l, d M Y, H:i:s', strtotime($stock['sale'])); ?></td>
                            <td class=""><?= $stock['qty']; ?></td>
                            <td><div class="uk-text-danger uk-width-1-1"><?= $saleremind.' '.$formatday.' '.lang('Global.day') ?></div></td>
                        </tr>
                    <?php }
                //} ?>
            <?php } ?>
        </tbody>
    </table>
    <div>
        <?= $pager->links('stockcycle', 'front_full') ?>
    </div>
</div>
<!-- End Of Table Content -->
<?= $this->endSection() ?>