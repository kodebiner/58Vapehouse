<?= $this->extend('layout') ?>
<?= $this->section('main') ?>

<!-- Page Heading -->
<div class="tm-card-header uk-light uk-margin-bottom">
    <div uk-grid class="uk-flex-middle">
        <div class="uk-width-1-3@m uk-width-1-1">
            <h3 class="tm-h3"><?= lang('Global.trxHistory') ?></h3>
        </div>
    </div>
</div>
<?= view('Views/Auth/_message_block') ?>

<!-- Table Of Content -->
<div class="uk-overflow-auto">
    <table class="uk-table uk-table-justify uk-table-middle uk-table-divider uk-light" style="width:100%">
        <thead>
            <tr>
                <th class="">Tanggal</th>
                <th class="">Transaksi</th>
                <th class="">Catatan</th>
                <th class="">Total</th>
                <th class="uk-text-center"></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transactions as $transaction) { ?>
                <tr>
                    <td><?= date('l, d M Y, H:i:s', strtotime($transaction['date'])); ?></td>
                    <td><?= $transaction['type'] ?></td>
                    <td><?= $transaction['note'] ?></td>
                    <td><?= $transaction['amount'] ?></td>
                    <td class="uk-child-width-auto uk-flex-center uk-grid-row-small uk-grid-column-small" uk-grid>
                        <!-- Button Modal Detail -->
                        <div>
                            <a class="uk-icon-button" uk-icon="search" uk-toggle="target: #detaildata<?= $transaction['id'] ?>"></a>
                        </div>
                        <?php if ($transaction['source_id'] == 1) { ?>
                            <!-- Button Modal Edit -->
                            <div>
                                <a class="uk-icon-button" uk-icon="pencil" uk-toggle="target: #editdata<?= $transaction['id'] ?>"></a>
                            </div>
                            <!-- Button Modal Delete -->
                            <div>
                                <a class="uk-icon-button-delete" uk-icon="trash" uk-toggle="target: #deletedata<?= $transaction['id'] ?>"></a>
                            </div>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<!-- Table Of Content End -->

<!-- Modal Detail -->
<?php foreach ($transactions as $transaction) { ?>
    <div id="detaildata<?= $transaction['id'] ?>" uk-modal>
        <div class="uk-modal-dialog uk-margin-auto-vertical">
            <div class="uk-modal-content">
                <div class="uk-modal-header">
                    <h5 class="uk-modal-title">Detail</h5>
                </div>
            </div>
            <div class="uk-modal-body">
                <div class="uk-margin">
                    <h5 class="uk-margin-remove">Transaksi</h5>
                    <?= $transaction['type'] ?>
                </div>
                <div class="uk-margin">
                    <h5 class="uk-margin-remove">Tanggal</h5>
                    <?= date('l, d M Y, H:i:s', strtotime($transaction['date'])); ?>
                </div>
                <div class="uk-margin">
                    <h5 class="uk-margin-remove">Kontak</h5>
                    <?php if ($transaction['contact']) { ?>
                        <?= $transaction['contact'] ?>
                    <?php } else { ?>
                        <p class="uk-margin-remove">Tidak ada kontak</p>
                    <?php } ?>
                </div>
                <div class="uk-margin">
                    <h5 class="uk-margin-remove">Jatuh Tempo</h5>
                    <?php if ($transaction['due_date'] != '0000-00-00') { ?>
                        <?= date('l, d M Y, H:i:s', strtotime($transaction['due_date'])); ?>
                    <?php } else { ?>
                        <p class="uk-margin-remove">Tidak ada jatuh tempo</p>
                    <?php } ?>
                </div>
                <div class="uk-margin">
                    <h5 class="uk-margin-remove">Catatan</h5>
                    <?= $transaction['note'] ?>
                </div>
                <div class="uk-margin">
                    <h5 class="uk-margin-remove">Akun COA</h5>
                    <table class="uk-table uk-table-divider uk-table-small" style="background-color: #fff;">
                        <thead>
                            <tr>
                                <th style="color: #000 !important;">Nama Akun</th>
                                <th style="color: #000 !important;">Debit</th>
                                <th style="color: #000 !important;">Kredit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- </?php foreach ($transaction['journals'] as $journal) { ?>
                                <tr>
                                    <td style="color: #000 !important;"></?= $journal['coa_name'] ?></td>
                                    <td style="color: #000 !important;"></?= $journal['debit'] ?></td>
                                    <td style="color: #000 !important;"></?= $journal['credit'] ?></td>
                                </tr>
                            </?php } ?> -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <th style="color: #000 !important;">Total</th>
                                <th style="color: #000 !important;"><?= $transaction['amount'] ?></th>
                                <th style="color: #000 !important;"><?= $transaction['amount'] ?></th>
                            </tr>
                    </table>
                </div>
                <div class="uk-margin">
                    <h5 class="uk-margin-remove">Lampiran</h5>
                    <?php if ($transaction['attachment']) { ?>
                        <a href="/uploads/<?= $transaction['attachment'] ?>" target="_blank">Lihat Lampiran</a>
                    <?php } else { ?>
                        <p class="uk-margin-remove">Tidak ada lampiran</p>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<?= $this->endSection() ?>