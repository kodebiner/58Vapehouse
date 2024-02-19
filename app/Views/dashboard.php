<?= $this->extend('layout') ?>
<?= $this->section('extraScript') ?>
<script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<?= $this->endSection() ?>
<?= $this->section('main') ?>
<div class="uk-width-1-1 uk-height-1-1" class="uk-inline">
    <div>
        <?= view('Views/Auth/_permission_message') ?>
    </div>

    <!-- Page Heading -->
    <div class="tm-card-header uk-light uk-margin-bottom">
        <div uk-grid class="uk-flex-middle uk-child-width-1-2@m">
            <div>
                <h3 class="tm-h3"><?=lang('Global.dashboard')?></h3>
            </div>
            <?php if (in_groups('owner')) : ?>
                <!-- Date Range Filter -->
                <div>
                    <div class="uk-margin uk-text-right">
                        <form id="short" action="home/index" method="get">
                            <div class="uk-inline">
                                <span class="uk-form-icon uk-form-icon-flip" uk-icon="calendar"></span>
                                <input class="uk-input uk-width-medium uk-border-rounded" type="text" id="daterange" name="daterange" value="<?=date('m/d/Y', $startdate)?> - <?=date('m/d/Y', $enddate)?>" />
                            </div>
                        </form>
                        <script>
                            $(function() {
                                $('input[name="daterange"]').daterangepicker({
                                    opens: 'left',
                                    maxDate: new Date(),
                                }, function(start, end, label) {
                                    document.getElementById('daterange').value = start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD');
                                    document.getElementById('short').submit();
                                });
                            });
                        </script>
                    </div>
                </div>
            <?php endif ?>
            <!-- Date Range Filter End -->
        </div>
    </div>
    <!-- End Of Page Heading -->

    <!-- Shortcut List -->
    <div class="uk-container uk-container-large">
        <div class="uk-child-width-1-2 uk-child-width-1-4@l uk-flex-center uk-flex-middle uk-text-center" uk-grid uk-height-match="target: > div > a > .uk-card-body">
            <div>
                <a class="uk-link-reset" href="<?= base_url('presence') ?>">
                    <div class="uk-card uk-card-default uk-card-small uk-card-body">
                        <img src="img/layout/presensi.svg" uk-svg style="color: #1e87f0;">
                        <div class="tm-h4"><?=lang('Global.presence');?></div>
                    </div>
                </a>
            </div>
            <div>
                <a class="uk-link-reset" href="<?= base_url('sop/todolist') ?>">
                    <div class="uk-card uk-card-default uk-card-small uk-card-body">
                        <img src="img/layout/sop.svg" uk-svg style="color: #1e87f0;">
                        <div class="tm-h4"><?=lang('Global.sop');?></div>
                    </div>
                </a>
            </div>
            <div>
                <a class="uk-link-reset" href="<?= base_url('cashinout') ?>">
                    <div class="uk-card uk-card-default uk-card-small uk-card-body">
                        <img src="img/layout/cash.svg" uk-svg style="color: #1e87f0;">
                        <div class="tm-h4"><?=lang('Global.cashinout');?></div>
                    </div>
                </a>
            </div>
            <div>
                <a class="uk-link-reset" href="<?= base_url('customer') ?>">
                    <div class="uk-card uk-card-default uk-card-small uk-card-body">
                        <img src="img/layout/pelanggan.svg" uk-svg style="color: #1e87f0;">
                        <div class="tm-h4"><?=lang('Global.customer');?></div>
                    </div>
                </a>
            </div>
        </div>
    </div>
    <!-- Shortcut List End -->

    <?//php foreach ($transactiondata as $trx) { ?>
        <!-- Report Short List -->
        <div class="uk-container uk-container-large uk-margin-top">
            <div class="uk-child-width-1-2 uk-flex-center uk-flex-middle" uk-grid uk-height-match="target: > div > .uk-card">
                <div>
                    <div class="uk-card uk-card-default uk-card-body">
                        <div class="uk-child-width-1-1" uk-grid>
                            <div>
                                <div class="tm-h5" style="color: #000;"><?= lang('Global.sales').' '. date('M Y', strtotime($month)); ?></div>
                            </div>
                            <div class="uk-margin-small-top">
                                <div class="tm-h4" style="color: #000;">Rp <?= number_format($transactiondata['monthtrx'],0,',','.') ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="uk-card uk-card-default uk-card-body">
                        <div class="uk-child-width-1-1" uk-grid>
                            <div>
                                <div class="tm-h5" style="color: #000;"><?= lang('Global.todaysales') ?></div>
                            </div>
                            <div class="uk-margin-small-top">
                                <div class="tm-h4" style="color: #000;">Rp <?= number_format($transactiondata['todaytrx'],0,',','.') ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="uk-card uk-card-default uk-card-body">
                        <div class="uk-child-width-1-1" uk-grid>
                            <div>
                                <div class="tm-h5" style="color: #000;"><?= lang('Global.todayexpenses') ?></div>
                            </div>
                            <div class="uk-margin-small-top">
                                <div class="tm-h4" style="color: #000;">Rp <?= number_format($transactiondata['todayexp'],0,',','.') ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Report Short List End -->

        <!-- Transaction Section -->
        <div class="uk-position-small">
            <a href="transaction" class="uk-button uk-button-primary uk-button-large uk-width-1-1 uk-light" style="border-radius: 10px;"><span class="uk-h3 tm-h3"><?=lang('Global.transaction')?></span></a>
        </div>
        <!-- Transaction Section End -->

        <?php if (in_groups('owner')) : ?>
            <!-- Main Section -->
            <div class="uk-margin">
                <div class="uk-child-width-1-4@l uk-child-width-1-2@s" uk-grid uk-height-match="target: > div > .uk-card">
                    <!-- Total Sales -->
                    <div>
                        <div class="uk-card uk-card-default uk-card-body">
                            <div class="uk-child-width-1-1" uk-grid>
                                <div>
                                    <div class="tm-h3" style="color: #000;"><?= lang('Global.salestotal') ?></div>
                                </div>
                                <div class="uk-margin-small-top">
                                    <div class="tm-h2" style="color: #000;">Rp <?= number_format($transactiondata['totaltrxvalue'],0,',','.') ?></div>
                                </div>
                                <div class="uk-text-right uk-margin-small-top">
                                    <a class="uk-link-reset" href="<?= base_url('report/penjualan') ?>" style="color: #f0506e !important;"><?= lang('Global.seedetails') ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Total Sales End -->

                    <!-- Total Profit -->
                    <div>
                        <div class="uk-card uk-card-default uk-card-body">
                            <div class="uk-child-width-1-1" uk-grid>
                                <div>
                                    <div class="tm-h3" style="color: #000;"><?= lang('Global.profittotal') ?></div>
                                </div>
                                <div class="uk-margin-small-top">
                                    <div class="tm-h2" style="color: #000;">Rp <?= number_format($transactiondata['profit'],0,',','.') ?></div>
                                </div>
                                <div class="uk-text-right uk-margin-small-top">
                                    <a class="uk-link-reset" href="<?= base_url('report/keuntungan') ?>" style="color: #f0506e !important;"><?= lang('Global.seedetails') ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Total Profit End -->

                    <!-- Total Transaction -->
                    <div>
                        <div class="uk-card uk-card-default uk-card-body">
                            <div class="uk-child-width-1-1" uk-grid>
                                <div>
                                    <div class="tm-h3" style="color: #000;"><?= lang('Global.totaltransaction') ?></div>
                                </div>
                                <div class="uk-margin-small-top">
                                    <div class="tm-h2" style="color: #000;"><?= $transactiondata['totaltrx'] ?></div>
                                </div>
                                <div class="uk-text-right uk-margin-small-top">
                                    <a class="uk-link-reset" href="<?= base_url('trxhistory') ?>" style="color: #f0506e !important;"><?= lang('Global.seedetails') ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Total Transaction End -->

                    <!-- Total Product Sales -->
                    <div>
                        <div class="uk-card uk-card-default uk-card-body">
                            <div class="uk-child-width-1-1" uk-grid>
                                <div>
                                    <div class="tm-h3" style="color: #000;"><?= lang('Global.productsales') ?></div>
                                </div>
                                <div class="uk-margin-small-top">
                                    <div class="tm-h2" style="color: #000;"><?= $transactiondata['productsale'] ?></div>
                                </div>
                                <div class="uk-text-right uk-margin-small-top">
                                    <a class="uk-link-reset" href="<?= base_url('report/product') ?>" style="color: #f0506e !important;"><?= lang('Global.seedetails') ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Total Product Sales End -->
                </div>
            </div>

            <div class="uk-margin">
                <div class="uk-child-width-1-3@l uk-child-width-1-2@s" uk-grid uk-height-match="target: > div > .uk-card">
                    <!-- Detail Total Sales -->
                    <div>
                        <div class="uk-card uk-card-default uk-card-body">
                            <div class="uk-child-width-1-1" uk-grid>
                                <div>
                                    <div class="uk-child-width-1-2" uk-grid id="descsales">
                                        <div>
                                            <div class="tm-h3" style="color: #000;"><?= lang('Global.salesdetails') ?></div>
                                        </div>
                                        <div class="uk-padding-remove uk-inline uk-text-right">
                                            <button uk-icon="question"></button>
                                            <div class="uk-background-secondary uk-light uk-text-center" uk-drop="pos: top-right; stretch: false; target: !#descsales;" style="border-radius: 10px;"><?= lang('Global.descsalestotal') ?></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="uk-margin-small-top">
                                    <div class="uk-child-width-1-2" uk-grid>
                                        <div>
                                            <div><?= lang('Global.gross') ?></div>
                                        </div>
                                        <div class="uk-text-right uk-margin-remove-left">
                                            <div>Rp <?= number_format($transactiondata['gross'],0,',','.') ?></div>
                                        </div>
                                    </div>

                                    <hr class="uk-margin-small-top uk-margin-small-bottom"/>

                                    <div class="uk-child-width-1-2" uk-grid>
                                        <div>
                                            <div><?= lang('Global.discount') ?></div>
                                        </div>
                                        <div class="uk-text-right uk-margin-remove-left">
                                            <div>- Rp <?= number_format($transactiondata['totaldiscount'],0,',','.') ?></div>
                                        </div>
                                    </div>

                                    <hr class="uk-margin-small-top uk-margin-small-bottom"/>

                                    <div class="uk-child-width-1-2" uk-grid>
                                        <div>
                                            <div><?= lang('Global.redeemPoint') ?></div>
                                        </div>
                                        <div class="uk-text-right uk-margin-remove-left">
                                            <div>- Rp <?= number_format($transactiondata['totalpointused'],0,',','.') ?></div>
                                        </div>
                                    </div>

                                    <hr class="uk-margin-small-top uk-margin-small-bottom"/>

                                    <div class="uk-child-width-1-2 uk-text-bolder" style="color: #000;" uk-grid>
                                        <div>
                                            <div><?= lang('Global.salestotal') ?></div>
                                        </div>
                                        <div class="uk-text-right uk-margin-remove-left">
                                            <div>Rp <?= number_format($transactiondata['totaltrxvalue'],0,',','.') ?></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="uk-text-right uk-margin-top">
                                    <a class="uk-link-reset" href="<?= base_url('report/penjualan') ?>" style="color: #f0506e !important;"><?= lang('Global.seedetails') ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Detail Total Sales End -->

                    <!-- Detail Debt -->
                    <div>
                        <div class="uk-card uk-card-default uk-card-body">
                            <div class="uk-child-width-1-1" uk-grid>
                                <div>
                                    <div class="uk-child-width-1-2" uk-grid id="descdebt">
                                        <div>
                                            <div class="tm-h3" style="color: #000;"><?= lang('Global.debt') ?></div>
                                        </div>
                                        <div class="uk-padding-remove uk-inline uk-text-right">
                                            <button uk-icon="question"></button>
                                            <div class="uk-background-secondary uk-light uk-text-center" uk-drop="pos: top-right; stretch: false; target: !#descdebt;" style="border-radius: 10px;"><?= lang('Global.descdebt') ?></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="uk-margin-small-top">
                                    <div class="uk-child-width-1-1" uk-grid>
                                        <div>
                                            <div><?= lang('Global.totaldebt') ?></div>
                                        </div>
                                        <div class="uk-margin-remove-top uk-text-bolder" style="color: #000;">
                                            <div class="tm-h2">
                                                Rp <?= number_format($transactiondata['debtvalue'],0,',','.') ?>
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="uk-margin-small-top uk-margin-small-bottom"/>

                                    <div class="uk-child-width-1-1" uk-grid>
                                        <div>
                                            <div><?= lang('Global.totaldp') ?></div>
                                        </div>
                                        <div class="uk-margin-remove-top uk-text-bolder" style="color: #000;">
                                            <div class="tm-h2">
                                                Rp <?= number_format($transactiondata['dp'],0,',','.') ?>
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="uk-margin-small-top uk-margin-small-bottom"/>

                                    <div class="uk-child-width-1-1" uk-grid>
                                        <div>
                                            <div><?= lang('Global.totalcustomer') ?></div>
                                        </div>
                                        <div class="uk-margin-remove-top uk-text-bolder" style="color: #000;">
                                            <div class="tm-h2"><?= $transactiondata['debtmember'] ?></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="uk-text-right uk-margin-top">
                                    <a class="uk-link-reset" href="<?= base_url('debt') ?>" style="color: #f0506e !important;"><?= lang('Global.seedetails') ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Detail Debt End -->

                    <!-- Cash Flow -->
                    <div>
                        <div class="uk-card uk-card-default uk-card-body">
                            <div class="uk-child-width-1-1" uk-grid>
                                <div>
                                    <div class="uk-child-width-1-2" uk-grid id="desccashflow">
                                        <div>
                                            <div class="tm-h3" style="color: #000;"><?= lang('Global.cashflow') ?></div>
                                        </div>
                                        <div class="uk-padding-remove uk-inline uk-text-right">
                                            <button uk-icon="question"></button>
                                            <div class="uk-background-secondary uk-light uk-text-center" uk-drop="pos: top-right; stretch: false; target: !#desccashflow;" style="border-radius: 10px;"><?= lang('Global.desccashflow') ?></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="uk-margin-small-top">
                                    <div class="uk-child-width-1-1" uk-grid>
                                        <div>
                                            <div><?= lang('Global.totalcashin') ?></div>
                                        </div>
                                        <div class="uk-margin-remove-top uk-text-bolder" style="color: #000;">
                                            <div class="tm-h2">Rp <?= number_format($transactiondata['cashin'],0,',','.') ?></div>
                                        </div>
                                    </div>

                                    <hr class="uk-margin-small-top uk-margin-small-bottom"/>

                                    <div class="uk-child-width-1-1" uk-grid>
                                        <div>
                                            <div><?= lang('Global.totalcashout') ?></div>
                                        </div>
                                        <div class="uk-margin-remove-top uk-text-bolder" style="color: #000;">
                                            <div class="tm-h2">Rp <?= number_format($transactiondata['cashout'],0,',','.') ?></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="uk-text-right uk-margin-top">
                                    <a class="uk-link-reset" href="<?= base_url('cashinout') ?>" style="color: #f0506e !important;"><?= lang('Global.seedetails') ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Cash Flow End -->

                    <!-- Popular Product -->
                    <div>
                        <div class="uk-card uk-card-default uk-card-body">
                            <div class="uk-child-width-1-1" uk-grid>
                                <div>
                                    <div class="tm-h3" style="color: #000;"><?= lang('Global.bestsellprod') ?></div>
                                </div>
                                <div class="uk-margin-small-top">
                                    <table class="uk-table uk-table-divider" style="background-color: #fff;">
                                        <tbody>
                                            <?php $i = 1 ; ?>
                                            <?php foreach ($transactiondata['bestsell'] as $bestsell) { ?>
                                                <tr>
                                                    <td><?= $i++; ?></td>
                                                    <td>
                                                        <?= $bestsell['name'] ?>
                                                    </td>
                                                    <td class="uk-text-right uk-text-bolder" style="color: #000;"><?= $bestsell['qty'] ?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="uk-text-right uk-margin-top">
                                    <a class="uk-link-reset" href="<//?= base_url('report/product') ?>" style="color: #f0506e !important;"><?= lang('Global.seedetails') ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Popular Product End -->

                    <!-- Popular Payment Method -->
                    <div>
                        <div class="uk-card uk-card-default uk-card-body">
                            <div class="uk-child-width-1-1" uk-grid>
                                <div>
                                    <div class="tm-h3" style="color: #000;"><?= lang('Global.poppaymethod') ?></div>
                                </div>
                                <div class="uk-margin-small-top">
                                    <table class="uk-table uk-table-divider" style="background-color: #fff;">
                                        <tbody>
                                            <?php $i = 1 ; ?>
                                            <?php if(!empty($transactiondata['bestpayment'])){
                                                foreach ($transactiondata['bestpayment'] as $bestpayment) {?>
                                                    <tr>
                                                        <td><?= $i++; ?></td>
                                                        <td><?= $bestpayment['name'] ?></td>
                                                        <td class="uk-text-right">Rp <?= number_format($bestpayment['value'],0,',','.') ?></td>
                                                    </tr>
                                                <?php } 
                                            } else {
                                                foreach ($payments as $pay) { ?>
                                                    <tr>
                                                        <td><?= $i++; ?></td>
                                                        <td><?= $pay['name'] ?></td>
                                                        <td class="uk-text-right">0</td>
                                                    </tr>
                                                <?php }
                                            } ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="uk-text-right uk-margin-top">
                                    <a class="uk-link-reset" href="<?= base_url('report/payment') ?>" style="color: #f0506e !important;"><?= lang('Global.seedetails') ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Popular Payment Method -->

                    <!-- Outlook -->
                    <div>
                        <div class="uk-card uk-card-default uk-card-body">
                            <div class="uk-child-width-1-1" uk-grid>
                                <div>
                                    <div class="tm-h3" style="color: #000;"><?= lang('Global.outlook') ?></div>
                                </div>
                                <div class="uk-margin-small-top">
                                    <div class="uk-child-width-1-2" uk-grid>
                                        <div>
                                            <div><?= lang('Global.avgsalesperday') ?></div>
                                        </div>
                                        <div class="uk-text-right uk-margin-remove-left">
                                            <div>Rp <?= number_format($transactiondata['averagedays'],0,',','.') ?></div>
                                        </div>
                                    </div>

                                    <hr class="uk-margin-small-top uk-margin-small-bottom"/>

                                    <div class="uk-child-width-1-2" uk-grid>
                                        <div>
                                            <div><?= lang('Global.avgsalespertrx') ?></div>
                                        </div>
                                        <div class="uk-text-right uk-margin-remove-left">
                                            <div>Rp <?= number_format($transactiondata['saleaverage'],0,',','.') ?></div>
                                        </div>
                                    </div>

                                    <hr class="uk-margin-small-top uk-margin-small-bottom"/>

                                    <div class="uk-child-width-1-2" uk-grid>
                                        <div>
                                            <div><?= lang('Global.busiestday') ?></div>
                                        </div>
                                        <div class="uk-text-right uk-margin-remove-left">
                                            <div><?= $transactiondata['bussyday'] ?></div>
                                        </div>
                                    </div>

                                    <hr class="uk-margin-small-top uk-margin-small-bottom"/>

                                    <div class="uk-child-width-1-2" uk-grid>
                                        <div>
                                            <div><?= lang('Global.busiesthours') ?></div>
                                        </div>
                                        <div class="uk-text-right uk-margin-remove-left">
                                            <div><?= $transactiondata['bussytime'] ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Outlook End -->

                    <!-- Stock Cycle -->
                    <div>
                        <div class="uk-card uk-card-default uk-card-body">
                            <div class="uk-child-width-1-1" uk-grid>
                                <div>
                                    <div class="uk-child-width-1-2" uk-grid id="descstockcycle">
                                        <div>
                                            <div class="tm-h3" style="color: #000;"><?= lang('Global.stockCycle') ?></div>
                                        </div>
                                        <div class="uk-padding-remove uk-inline uk-text-right">
                                            <button uk-icon="question"></button>
                                            <div class="uk-background-secondary uk-light uk-text-center" uk-drop="pos: top-right; stretch: false; target: !#descstockcycle;" style="border-radius: 10px;"><?= lang('Global.descstockcycle') ?></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="uk-margin-small-top">
                                    <table class="uk-table uk-table-divider" style="background-color: #fff;">
                                        <tbody>
                                            <?php foreach ($stocks as $stock) {
                                                $today      = $stock['restock'];
                                                $date       = date_create($today);
                                                $now        = date_create();
                                                $nowdates   = date_format($now,'Y-m-d H:i:s');
                                                $todays     = strtotime($today);
                                                $dates      = strtotime($nowdates);
                                                date_add($date, date_interval_create_from_date_string('0 days'));
                                                $newdate        = date_format($date, 'Y-m-d H:i:s');
                                                $origin         = new DateTime($stock['sale']);
                                                $restock        = new DateTime($stock['restock']);
                                                $target         = new DateTime('now');
                                                $interval       = $origin->diff($target);
                                                $formatday      = substr($interval->format('%R%a'), 1);
                                                $saleremind     = lang('Global.saleremind');
                                                $restockremind  = lang('Global.restockremind');
                                                $intervals      = $restock->diff($target);

                                                if ($stock['sale'] > $newdate) {
                                                    if ($formatday >= 0) { ?>
                                                        <tr>
                                                            <td>
                                                                <?php foreach ($variants as $variant) {
                                                                    foreach ($products as $product){
                                                                        if ($variant['id'] === $stock['variantid']) {
                                                                            if ($variant['productid'] === $product['id']) {
                                                                                echo ($product['name']."-".$variant['name']);
                                                                            }
                                                                        }
                                                                    }
                                                                } ?>
                                                            </td>
                                                            <td><?= $formatday.' '.lang('Global.pastday') ?></td>
                                                        </tr>
                                                    <?php }
                                                } elseif ($intervals = "30") {
                                                    if ($formatday >= 0) { ?>
                                                        <tr>
                                                            <td>
                                                                <?php foreach ($variants as $variant) {
                                                                    foreach ($products as $product) {
                                                                        if ($variant['id'] === $stock['variantid']) {
                                                                            if ($variant['productid'] === $product['id']) {
                                                                                echo ($product['name']."-".$variant['name']);
                                                                            }
                                                                        }
                                                                    }
                                                                } ?>
                                                            </td>
                                                            <td><?= $formatday.' '.lang('Global.pastday'); ?></td>
                                                        </tr>
                                                    <?php }
                                                } 
                                            } ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="uk-text-right uk-margin-top">
                                    <a class="uk-link-reset" href="<?= base_url('stock/stockcycle') ?>" style="color: #f0506e !important;"><?= lang('Global.seedetails') ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Stock Cycle End -->
                </div>
            </div>
            <!-- Main Section End -->
        <?php endif; ?>
    <?//php } ?>
</div>
<?= $this->endSection() ?>