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
                                opens: 'left'
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
                            <h5 class="tm-h2" style="color: #000;"><?= lang('Global.salestotal') ?></h5>
                        </div>
                        <div class="uk-margin-small-top">
                            <h3 class="tm-h2" style="color: #000;">Rp <?= number_format($sales,0,',','.') ?></h3>
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
                            <h5 class="tm-h2" style="color: #000;"><?= lang('Global.profittotal') ?></h5>
                        </div>
                        <div class="uk-margin-small-top">
                            <h3 class="tm-h2" style="color: #000;">Rp <?= number_format($profit,0,',','.') ?></h3>
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
                            <h5 class="tm-h2" style="color: #000;"><?= lang('Global.totaltransaction') ?></h5>
                        </div>
                        <div class="uk-margin-small-top">
                            <h3 class="tm-h2" style="color: #000;"><?=$trxamount?></h3>
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
                            <h5 class="tm-h2" style="color: #000;"><?= lang('Global.productsales') ?></h5>
                        </div>
                        <div class="uk-margin-small-top">
                            <h3 class="tm-h2" style="color: #000;"><?= $qtytrxsum ?></h3>
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
                                    <h5 class="tm-h2" style="color: #000;"><?= lang('Global.salesdetails') ?></h5>
                                </div>
                                <div class="uk-padding-remove uk-inline">
                                    <button uk-icon="question"></button>
                                    <div class="uk-dropbar uk-dropbar-top uk-background-secondary uk-light uk-text-center" uk-drop="stretch: false; target: !#descsales;" style="border-radius: 10px;"><?= lang('Global.descsalestotal') ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="uk-margin-small-top">
                            <div class="uk-child-width-1-2" uk-grid>
                                <div>
                                    <div><?= lang('Global.gross') ?></div>
                                </div>
                                <div class="uk-text-right uk-margin-remove-left">
                                    <div>Rp <?= number_format($gross,0,',','.') ?></div>
                                </div>
                            </div>

                            <hr class="uk-margin-small-top uk-margin-small-bottom"/>

                            <div class="uk-child-width-1-2" uk-grid>
                                <div>
                                    <div><?= lang('Global.discount') ?></div>
                                </div>
                                <div class="uk-text-right uk-margin-remove-left">
                                    <div>- Rp <?= number_format($totaldisc,0,',','.') ?></div>
                                </div>
                            </div>

                            <hr class="uk-margin-small-top uk-margin-small-bottom"/>

                            <div class="uk-child-width-1-2" uk-grid>
                                <div>
                                    <div><?= lang('Global.redeemPoint') ?></div>
                                </div>
                                <div class="uk-text-right uk-margin-remove-left">
                                    <div>- Rp <?= number_format($pointusedsum,0,',','.') ?></div>
                                </div>
                            </div>

                            <hr class="uk-margin-small-top uk-margin-small-bottom"/>

                            <div class="uk-child-width-1-2 uk-text-bolder" style="color: #000;" uk-grid>
                                <div>
                                    <div><?= lang('Global.salestotal') ?></div>
                                </div>
                                <div class="uk-text-right uk-margin-remove-left">
                                    <div>Rp <?= number_format($sales,0,',','.') ?></div>
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
                            <div class="uk-child-width-1-4" uk-grid id="descdebt">
                                <div>
                                    <h5 class="tm-h2" style="color: #000;"><?= lang('Global.debt') ?></h5>
                                </div>
                                <div class="uk-padding-remove uk-inline">
                                    <button uk-icon="question"></button>
                                    <div class="uk-dropbar uk-dropbar-top uk-background-secondary uk-light uk-text-center" uk-drop="stretch: false; target: !#descdebt;" style="border-radius: 10px;"><?= lang('Global.descdebt') ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="uk-margin-small-top">
                            <div class="uk-child-width-1-1" uk-grid>
                                <div>
                                    <div><?= lang('Global.totaldebt') ?></div>
                                </div>
                                <div class="uk-margin-remove-top uk-text-bolder" style="color: #000;">
                                    <div class="uk-h3 tm-h2">
                                        <?php
                                            $debt[] = array();
                                            foreach ($trxdebtval as $trxdebtva) {
                                                if ($trxdebtva['value'] - $trxdebtva['amountpaid'] >= "0") {
                                                    $debt[] = $trxdebtva['value'] - $trxdebtva['amountpaid'];
                                                }
                                            }
                                            $debtsum = array_sum($debt);
                                        ?>
                                        Rp <?= number_format($debtsum,0,',','.') ?>
                                    </div>
                                </div>
                            </div>

                            <hr class="uk-margin-small-top uk-margin-small-bottom"/>

                            <div class="uk-child-width-1-1" uk-grid>
                                <div>
                                    <div><?= lang('Global.totaldp') ?></div>
                                </div>
                                <div class="uk-margin-remove-top uk-text-bolder" style="color: #000;">
                                    <div class="uk-h3 tm-h2">
                                        <?php
                                            $debtdp[] = array();
                                            foreach ($trxdebtval as $trxdebtva) {
                                                if ($trxdebtva['value'] - $trxdebtva['amountpaid'] >= "0") {
                                                    $debtdp[] =$trxdebtva['amountpaid'];
                                                }
                                            }
                                            $debtdpsum = array_sum($debtdp);
                                        ?>
                                        Rp <?= number_format($debtdpsum,0,',','.') ?>
                                    </div>
                                </div>
                            </div>

                            <hr class="uk-margin-small-top uk-margin-small-bottom"/>

                            <div class="uk-child-width-1-1" uk-grid>
                                <div>
                                    <div><?= lang('Global.totalcustomer') ?></div>
                                </div>
                                <div class="uk-margin-remove-top uk-text-bolder" style="color: #000;">
                                    <div class="uk-h3 tm-h2"><?= $totalcustdebt ?></div>
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
                                    <h5 class="tm-h2" style="color: #000;"><?= lang('Global.cashflow') ?></h5>
                                </div>
                                <div class="uk-padding-remove uk-inline">
                                    <button uk-icon="question"></button>
                                    <div class="uk-dropbar uk-dropbar-top uk-background-secondary uk-light uk-text-center" uk-drop="stretch: false; target: !#desccashflow;" style="border-radius: 10px;"><?= lang('Global.desccashflow') ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="uk-margin-small-top">
                            <div class="uk-child-width-1-1" uk-grid>
                                <div>
                                    <div><?= lang('Global.totalcashin') ?></div>
                                </div>
                                <div class="uk-margin-remove-top uk-text-bolder" style="color: #000;">
                                    <div class="uk-h3 tm-h2">Rp <?= number_format($cashinsum,0,',','.') ?></div>
                                </div>
                            </div>

                            <hr class="uk-margin-small-top uk-margin-small-bottom"/>

                            <div class="uk-child-width-1-1" uk-grid>
                                <div>
                                    <div><?= lang('Global.totalcashout') ?></div>
                                </div>
                                <div class="uk-margin-remove-top uk-text-bolder" style="color: #000;">
                                    <div class="uk-h3 tm-h2">Rp <?= number_format($cashoutsum,0,',','.') ?></div>
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
                            <h5 class="tm-h2" style="color: #000;"><?= lang('Global.bestsellprod') ?></h5>
                        </div>
                        <div class="uk-margin-small-top">
                            <table class="uk-table uk-table-divider" style="backgorund-color: #fff;">
                                <tbody>
                                    <?php $i = 1 ; ?>
                                    <?php foreach ($top3prod as $top3pro) { ?>
                                        <tr>
                                            <td><?= $i++; ?></td>
                                            <td>
                                                <?php if ($top3pro['variantid'] != "0") {
                                                    foreach ($products as $product) {
                                                        foreach ($variants as $variant) {
                                                            if (($variant['productid'] === $product['id']) && ($variant['id'] === $top3pro['variantid'])) {
                                                                echo $product['name'].' - '.$variant['name'];
                                                            }
                                                        }
                                                    }
                                                } elseif ($top3pro['bundleid'] != "0") {
                                                    foreach ($bundles as $bundle) {
                                                        if ($bundle['id'] === $top3pro['bundleid']) {
                                                            echo $bundle['name'];
                                                        }
                                                    }
                                                }
                                                ?>
                                            </td>
                                            <td class="uk-text-right uk-text-bolder" style="color: #000;"><?= $top3pro['qty'] ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="uk-text-right uk-margin-top">
                            <a class="uk-link-reset" href="<?= base_url('report/product') ?>" style="color: #f0506e !important;"><?= lang('Global.seedetails') ?></a>
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
                            <h5 class="tm-h2" style="color: #000;"><?= lang('Global.poppaymethod') ?></h5>
                        </div>
                        <div class="uk-margin-small-top">
                            <table class="uk-table uk-table-divider" style="backgorund-color: #fff;">
                                <tbody>
                                    <?php $i = 1 ; ?>
                                    <?php foreach ($top3paymet as $top3pay) { ?>
                                        <tr>
                                            <td><?= $i++; ?></td>
                                            <td><?= $top3pay['name'] ?></td>
                                            <td class="uk-text-right"><?= $top3pay['qty'] ?></td>
                                        </tr>
                                    <?php } ?>
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
                            <h5 class="tm-h2" style="color: #000;"><?= lang('Global.outlook') ?></h5>
                        </div>
                        <div class="uk-margin-small-top">
                            <div class="uk-child-width-1-2" uk-grid>
                                <div>
                                    <div><?= lang('Global.avgsalesperday') ?></div>
                                </div>
                                <div class="uk-text-right uk-margin-remove-left">
                                    <div>Rp <?= number_format($averagedays,0,',','.') ?></div>
                                </div>
                            </div>

                            <hr class="uk-margin-small-top uk-margin-small-bottom"/>

                            <div class="uk-child-width-1-2" uk-grid>
                                <div>
                                    <div><?= lang('Global.avgsalespertrx') ?></div>
                                </div>
                                <div class="uk-text-right uk-margin-remove-left">
                                    <div>Rp <?= number_format($average,0,',','.') ?></div>
                                </div>
                            </div>

                            <hr class="uk-margin-small-top uk-margin-small-bottom"/>

                            <div class="uk-child-width-1-2" uk-grid>
                                <div>
                                    <div><?= lang('Global.busiestday') ?></div>
                                </div>
                                <div class="uk-text-right uk-margin-remove-left">
                                    <div><?=$bussyday?></div>
                                </div>
                            </div>

                            <hr class="uk-margin-small-top uk-margin-small-bottom"/>

                            <div class="uk-child-width-1-2" uk-grid>
                                <div>
                                    <div><?= lang('Global.busiesthours') ?></div>
                                </div>
                                <div class="uk-text-right uk-margin-remove-left">
                                    <div><?=$bussytime?></div>
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
                                    <h5 class="tm-h2" style="color: #000;"><?= lang('Global.stockCycle') ?></h5>
                                </div>
                                <div class="uk-padding-remove uk-inline">
                                    <button uk-icon="question"></button>
                                    <div class="uk-dropbar uk-dropbar-top uk-background-secondary uk-light uk-text-center" uk-drop="stretch: false; target: !#descstockcycle;" style="border-radius: 10px;"><?= lang('Global.descstockcycle') ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="uk-margin-small-top">
                            <table class="uk-table uk-table-divider" style="backgorund-color: #fff;">
                                <tbody>
                                    <?php foreach ($stocks as $stock) { 
                                        if (($stock['restock'] != "0000-00-00 00:00:00") && ($stock['sale'] != '0000-00-00 00:00:00')) {?>
                                            <tr>
                                                <td>
                                                    <?php foreach ($variants as $variant) {
                                                        foreach ($products as $product){
                                                            if ($variant['id'] === $stock['variantid']) {
                                                                if($variant['productid'] === $product['id']){
                                                                    echo ($product['name']."-".$variant['name']);
                                                                }
                                                            }
                                                        }
                                                    } ?>
                                                </td>
                                                <td>
                                                    <?php
                                                        $today      = $stock['restock'];
                                                        $date       = date_create($today);
                                                        date_add($date, date_interval_create_from_date_string('0 days'));
                                                        $newdate    = date_format($date, 'Y-m-d H:i:s');
                                                        if ($stock['sale'] > $newdate) {
                                                            $origin         = new DateTime($stock['sale']);
                                                            $target         = new DateTime('now');
                                                            $interval       = $origin->diff($target);
                                                            $formatday      = substr($interval->format('%R%a'), 1);
                                                            $stockremind    = lang('Global.stockremind');
                                                            $saleremind     = lang('Global.saleremind');
                                                            if ($formatday >= 0) {
                                                                echo $formatday.' '.lang('Global.pastday');
                                                            }
                                                        }
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php }
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
    <?php endif ?>
</div>
<?= $this->endSection() ?>