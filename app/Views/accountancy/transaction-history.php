<?= $this->extend('layout') ?>
<?= $this->section('extraScript') ?>
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<style>
    /* Memastikan dropdown TomSelect muncul di depan modal UIkit */
    .ts-dropdown {
        z-index: 2000 !important;
    }
    /* Menyesuaikan input agar terlihat seperti uk-input */
    .ts-control {
        border-radius: 4px !important;
        padding: 8px 10px !important;
    }
</style>
<?= $this->endSection() ?>
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
                    <td><?= number_format($transaction['amount'], 0, ',', '.') ?></td>
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

<?php foreach ($transactions as $transaction) { ?>
    <!-- Modal Detail -->
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
                        <?= date('l, d M Y', strtotime($transaction['due_date'])); ?>
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
                            <?php foreach ($transaction['journals'] as $journal) { ?>
                                <tr>
                                    <td style="color: #000 !important;"><?= $journal['coa_full_name'] ?></td>
                                    <td style="color: #000 !important;"><?= number_format($journal['debit'], 0, ',', '.') ?></td>
                                    <td style="color: #000 !important;"><?= number_format($journal['credit'], 0, ',', '.') ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td class="uk-text-bold" style="color:#000">Total</td>
                                <td class="uk-text-bold" style="color:#000">
                                    <?= number_format($transaction['debit_total'], 0, ',', '.') ?>
                                </td>
                                <td class="uk-text-bold" style="color:#000">
                                    <?= number_format($transaction['credit_total'], 0, ',', '.') ?>
                                </td>
                            </tr>
                        </tfoot>
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
    
    <!-- Modal Edit -->
    <div id="editdata<?= $transaction['id'] ?>" uk-modal>
        <div class="uk-modal-dialog uk-margin-auto-vertical">
            <form id="trxForm<?= $transaction['id'] ?>" action="/accountancy/transaction/update/<?= $transaction['id'] ?>" method="post" enctype="multipart/form-data">
                <div class="uk-modal-header">
                    <h5 class="uk-modal-title">Edit Transaksi</h5>
                </div>

                <div class="uk-modal-body">
                    <input type="hidden" name="trx_id" value="<?= $transaction['id'] ?>">
                    <div class="uk-margin">
                        <h5 class="uk-margin-remove">Tanggal</h5>
                        <input type="datetime-local" name="date" value="<?= date('Y-m-d\TH:i', strtotime($transaction['date'])) ?>" class="uk-input uk-border-rounded" placeholder="Pilih Tanggal..." required>
                    </div>

                    <div class="uk-margin">
                        <h5 class="uk-margin-remove">Kontak</h5>
                        <select class="uk-select select-search" name="contact" placeholder="Cari kontak...">
                            <option value="">Pilih ...</option>
                            <option value="NULL">Hapus Kontak ...</option>
                            <?php foreach ($contacts as $c): ?>
                                <option value="<?= $c['id'] ?>" <?= $c['id'] == $transaction['contact_id'] ? 'selected' : '' ?>>
                                    <?= $c['name'] ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                    </div>

                    <div class="uk-margin">
                        <h5 class="uk-margin-remove">Jatuh Tempo</h5>
                        <input type="date" name="duedate" value="<?= ($transaction['due_date'] != '0000-00-00') ? $transaction['due_date'] : '' ?>" class="uk-input uk-border-rounded" placeholder="Pilih Tanggal Jatuh Tempo...">
                    </div>
                    
                    <div class="uk-margin">
                        <h5 class="uk-margin-remove">Catatan</h5>
                        <textarea class="uk-textarea" rows="3" name="note" required><?= $transaction['note'] ?></textarea>
                    </div>

                    <div class="uk-margin">
                        <h5 class="uk-margin-remove">Akun COA</h5>
                        <table class="uk-table uk-table-divider uk-table-small" style="background:#fff">
                            <thead>
                                <tr>
                                    <th style="color:#000">Nama Akun</th>
                                    <th style="color:#000">Debit</th>
                                    <th style="color:#000">Kredit</th>
                                </tr>
                            </thead>
                            <tbody id="journalBody<?= $transaction['id'] ?>">
                                <?php foreach ($transaction['journals'] as $journal) { ?>
                                    <input type="hidden" name="journal_id[]" value="<?= $journal['id'] ?>">
                                    <tr>
                                        <td>
                                            <select class="uk-select select-search" name="coa[]" placeholder="Cari akun...">
                                                <?php foreach ($coas as $coa): ?>
                                                    <option value="<?= $coa['id'] ?>" <?= $coa['id'] == $journal['coa_a_id'] ? 'selected' : '' ?>>
                                                        <?= $coa['coa_full_name'] ?>
                                                    </option>
                                                <?php endforeach ?>
                                            </select>
                                        </td>

                                        <td>
                                            <input type="text" name="debit[]" value="<?= number_format($journal['debit'],0,',','.') ?>" class="uk-input debit-input" placeholder="Nominal Debit...">
                                        </td>

                                        <td>
                                            <input type="text" name="credit[]" value="<?= number_format($journal['credit'],0,',','.') ?>" class="uk-input credit-input" placeholder="Nominal Kredit...">
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td class="uk-text-bold" style="color:#000">Total</td>
                                    <td class="uk-text-bold" style="color:#000">
                                        <span id="totalDebit<?= $transaction['id'] ?>">
                                            <?= number_format($transaction['debit_total'],0,',','.') ?>
                                        </span>
                                    </td>

                                    <td class="uk-text-bold" style="color:#000">
                                        <span id="totalCredit<?= $transaction['id'] ?>">
                                            <?= number_format($transaction['credit_total'],0,',','.') ?>
                                        </span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                        <div id="balanceWarning<?= $transaction['id'] ?>" 
                            class="uk-alert-danger uk-margin-small-top uk-hidden">
                            ⚠ Jurnal tidak balance (Debit dan Credit harus sama)
                        </div>
                        <button class="uk-button uk-button-primary uk-width-1-1" type="button" onclick="addJournalRow(<?= $transaction['id'] ?>)">+ Tambah Baris Ayat Jurnal</button>
                    </div>

                    <div class="uk-margin">
                        <label class="uk-form-label">Lampiran</label>
                        <?php if ($transaction['attachment']) { ?>
                            <div class="uk-margin-small">
                                <a href="/uploads/<?= $transaction['attachment'] ?>" target="_blank">
                                    Lihat Lampiran Saat Ini
                                </a>
                            </div>
                        <?php } ?>

                        <div class="uk-form-controls uk-flex" uk-form-custom="target: true">
                            <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.pdf">
                            <input class="uk-input uk-border-rounded uk-width-expand" type="text" placeholder="Pilih file..." disabled>
                            <button class="uk-button uk-button-default">Cari</button>
                        </div>
                        <small class="uk-text-muted">
                            Format: JPG, PNG, PDF (maks 4MB)
                        </small>
                    </div>
                </div>

                <div class="uk-modal-footer uk-text-right">
                    <button class="uk-button uk-button-default uk-modal-close" type="button">Batal</button>
                    <button class="uk-button uk-button-primary" type="submit">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
<?php } ?>

<script>
    function addJournalRow(trxId)
    {
        let tbody = document.getElementById('journalBody'+trxId);
        let row = `
        <input type="hidden" name="journal_id[]" value="">
        <tr>
            <td>
                <select class="uk-select select-search" name="coa[]" placeholder="Cari akun...">
                    <?php foreach ($coas as $coa): ?>
                        <option value="<?= $coa['id'] ?>">
                            <?= $coa['coa_full_name'] ?>
                        </option>
                    <?php endforeach ?>
                </select>
            </td>
            <td>
                <input type="text" name="debit[]" class="uk-input debit-input">
            </td>
            <td>
                <input type="text" name="credit[]" class="uk-input credit-input">
            </td>
            <td>
                <button type="button" class="uk-button uk-button-danger remove-row">x</button>
            </td>
        </tr>
        `;
        tbody.insertAdjacentHTML('beforeend', row);

        // ambil select terakhir yang baru ditambahkan
        let newSelect = tbody.querySelector('tr:last-child .select-search');

        // aktifkan TomSelect
        new TomSelect(newSelect,{
            create:false,
            sortField:{ field:"text", direction:"asc" }
        });

        newSelect.tomselect.focus();
    }

    document.addEventListener('click',function(e){
        if(e.target.classList.contains('remove-row')){
            let row = e.target.closest('tr');
            let tbody = row.closest('tbody');
            let trxId = tbody.id.replace('journalBody','');
            row.remove();
            updateJournal(trxId);
        }
    });

    function formatRupiah(angka) {
        let number_string = angka.replace(/[^,\d]/g, '').toString();
        let split   = number_string.split(',');
        let sisa    = split[0].length % 3;
        let rupiah  = split[0].substr(0, sisa);
        let ribuan  = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        return rupiah;
    }

    function parseNumber(value) {
        return parseFloat(value.replace(/\./g,'')) || 0;
    }

    function updateJournal(trxId){
        let tbody = document.getElementById('journalBody'+trxId);
        let debitInputs  = tbody.querySelectorAll('.debit-input');
        let creditInputs = tbody.querySelectorAll('.credit-input');
        let debitTotal = 0;
        let creditTotal = 0;

        debitInputs.forEach(input=>{
            debitTotal += parseNumber(input.value);
        });

        creditInputs.forEach(input=>{
            creditTotal += parseNumber(input.value);
        });

        document.getElementById('totalDebit'+trxId).innerText =
            debitTotal.toLocaleString('id-ID');

        document.getElementById('totalCredit'+trxId).innerText =
            creditTotal.toLocaleString('id-ID');

        let warning = document.getElementById('balanceWarning'+trxId);

        if(debitTotal !== creditTotal){
            warning.classList.remove('uk-hidden');
        }else{
            warning.classList.add('uk-hidden');
        }
    }

    document.addEventListener('input',function(e){

        if(e.target.classList.contains('debit-input') ||
        e.target.classList.contains('credit-input')){
            let row = e.target.closest('tr');
            let debit  = row.querySelector('.debit-input');
            let credit = row.querySelector('.credit-input');

            e.target.value = formatRupiah(e.target.value);

            // auto clear
            if(e.target.classList.contains('debit-input')){
                credit.value='';
            }

            if(e.target.classList.contains('credit-input')){
                debit.value='';
            }

            let tbody = e.target.closest('tbody');
            let trxId = tbody.id.replace('journalBody','');
            updateJournal(trxId);
        }
    });

    document.querySelectorAll("form[id^='trxForm']").forEach(form => {
        form.addEventListener("submit", function(e){
            let trxId = this.id.replace("trxForm","");
            let debit  = parseNumber(document.getElementById("totalDebit"+trxId).innerText);
            let credit = parseNumber(document.getElementById("totalCredit"+trxId).innerText);

            if(debit !== credit){
                UIkit.notification({
                    message: 'Jurnal tidak balance. Debit dan Credit harus sama.',
                    status: 'danger',
                    pos: 'top-center'
                });
                e.preventDefault();
            }
        });
    });
    
    document.querySelectorAll('.select-search').forEach(el => {
        new TomSelect(el,{
            create:false,
            sortField:{ field:"text", direction:"asc" },
            dropdownParent: 'body'
        });
    });
</script>
<?= $this->endSection() ?>