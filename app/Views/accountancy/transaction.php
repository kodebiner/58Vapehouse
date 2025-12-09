<?= $this->extend('layout') ?>
<?= $this->section('extraScript') ?>
<script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
<?= $this->endSection() ?>
<?= $this->section('main') ?>
<div class="uk-width-1-1 uk-height-1-1" class="uk-inline">
    <div>
        <?= view('Views/Auth/_permission_message') ?>
    </div>

    <!-- Page Heading -->
    <div class="tm-card-header uk-light uk-margin-bottom">
        <h3 class="tm-h3"><?=lang('Global.accountancy').' - Tambah Akuntansi'?></h3>
    </div>

    <div uk-grid class="uk-flex-top uk-child-width-1-2@m">
        <div>
            <div class="uk-card uk-card-default uk-card-body uk-margin">
                <h4 class="uk-margin-small-bottom">Tambah Transaksi</h4>

                <form action="/accounting/transaction/store" method="post" class="uk-form-stacked">
                    <?= csrf_field() ?>
                    <?php $now = new \DateTime(); ?>
                    <div class="uk-grid-small" uk-grid>
                        <div class="uk-width-1-4">
                            <label class="uk-form-label">Tanggal <span style="color: red;">*</span></label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-border-rounded" type="number" placeholder="DD" min="1" max="31" value="<?= $now->format('d') ?>" name="day" required>
                            </div>
                        </div>

                        <div class="uk-width-1-4">
                            <label class="uk-form-label">Bulan <span style="color: red;">*</span></label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-border-rounded" type="number" placeholder="MM" min="1" max="12" value="<?= $now->format('m') ?>" name="month" required>
                            </div>
                        </div>

                        <div class="uk-width-1-4">
                            <label class="uk-form-label">Tahun <span style="color: red;">*</span></label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-border-rounded" type="number" placeholder="YYYY" value="<?= $now->format('Y') ?>" name="year" required>
                            </div>
                        </div>

                        <div class="uk-width-1-4">
                            <label class="uk-form-label">Waktu <span style="color: red;">*</span></label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-border-rounded" type="time" value="<?= $now->format('H:i') ?>" name="time" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="uk-margin">
                        <label class="uk-form-label">Jenis Transaksi <span style="color: red;">*</span></label>
                        <div class="uk-form-controls">
                            <select class="uk-select" name="type" required>
                                <option value="1">Pemasukan</option>
                                <option value="2">Pengeluaran</option>
                                <option value="3">Hutang</option>
                                <option value="4">Piutang</option>
                                <option value="5">Tanam Modal</option>
                                <option value="6">Tarik Modal</option>
                                <option value="7">Transfer Uang</option>
                                <option value="8">Pemasukan Sebagai Piutang</option>
                                <option value="9">Pengeluaran Sebagai Hutang</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="uk-margin" id="pemasukkan">
                        <div class="uk-margin">
                            <label class="uk-form-label">Simpan ke (Debit) <span style="color: red;">*</span></label>
                            <div class="uk-form-controls">
                                <select class="uk-select" name="debit" required>
                                    <?php foreach ($debitCoas as $coa): ?>
                                        <option value="<?= $coa['id'] ?>">
                                            <?= $coa['name'] ?> (<?= $coa['code'] ?>)
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="uk-margin">
                            <label class="uk-form-label">Diterima dari (Kredit) <span style="color: red;">*</span></label>
                            <div class="uk-form-controls">
                                <select class="uk-select" name="credit" required>
                                    <?php foreach ($creditCoas as $coa): ?>
                                        <option value="<?= $coa['id'] ?>">
                                            <?= $coa['name'] ?> (<?= $coa['code'] ?>)
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="uk-margin">
                            <label class="uk-form-label">Nominal <span style="color: red;">*</span></label>
                            <div class="uk-inline uk-width-1-1">
                                <span class="uk-form-icon uk-text-bold">Rp</span>
                                <input class="uk-input uk-border-rounded uk-form-large" id="nominal" name="amount"
                                    type="text" placeholder="0" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="uk-margin" id="pengeluaran">
                        <div class="uk-margin">
                            <label class="uk-form-label">Untuk biaya (Debit) <span style="color: red;">*</span></label>
                            <div class="uk-form-controls">
                                <select class="uk-select" name="debit" required>
                                    <?php foreach ($debitCoas as $coa): ?>
                                        <option value="<?= $coa['id'] ?>">
                                            <?= $coa['name'] ?> (<?= $coa['code'] ?>)
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="uk-margin">
                            <label class="uk-form-label">Diambil dari (Kredit) <span style="color: red;">*</span></label>
                            <div class="uk-form-controls">
                                <select class="uk-select" name="credit" required>
                                    <?php foreach ($creditCoas as $coa): ?>
                                        <option value="<?= $coa['id'] ?>">
                                            <?= $coa['name'] ?> (<?= $coa['code'] ?>)
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="uk-margin">
                            <label class="uk-form-label">Nominal <span style="color: red;">*</span></label>
                            <div class="uk-inline uk-width-1-1">
                                <span class="uk-form-icon uk-text-bold">Rp</span>
                                <input class="uk-input uk-border-rounded uk-form-large" id="nominal" name="amount"
                                    type="text" placeholder="0" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="uk-margin" id="hutang">
                        <div class="uk-margin">
                            <label class="uk-form-label">Simpan ke (Debit) <span style="color: red;">*</span></label>
                            <div class="uk-form-controls">
                                <select class="uk-select" name="debit" required>
                                    <?php foreach ($debitCoas as $coa): ?>
                                        <option value="<?= $coa['id'] ?>">
                                            <?= $coa['name'] ?> (<?= $coa['code'] ?>)
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="uk-margin">
                            <label class="uk-form-label">Hutang dari (Kredit) <span style="color: red;">*</span></label>
                            <div class="uk-form-controls">
                                <select class="uk-select" name="credit" required>
                                    <?php foreach ($creditCoas as $coa): ?>
                                        <option value="<?= $coa['id'] ?>">
                                            <?= $coa['name'] ?> (<?= $coa['code'] ?>)
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="uk-margin">
                            <label class="uk-form-label">Nominal <span style="color: red;">*</span></label>
                            <div class="uk-inline uk-width-1-1">
                                <span class="uk-form-icon uk-text-bold">Rp</span>
                                <input class="uk-input uk-border-rounded uk-form-large" id="nominal" name="amount"
                                    type="text" placeholder="0" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="uk-margin" id="piutang">
                        <div class="uk-margin">
                            <label class="uk-form-label">Simpan ke (Debit) <span style="color: red;">*</span></label>
                            <div class="uk-form-controls">
                                <select class="uk-select" name="debit" required>
                                    <?php foreach ($debitCoas as $coa): ?>
                                        <option value="<?= $coa['id'] ?>">
                                            <?= $coa['name'] ?> (<?= $coa['code'] ?>)
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="uk-margin">
                            <label class="uk-form-label">Hutang dari (Kredit) <span style="color: red;">*</span></label>
                            <div class="uk-form-controls">
                                <select class="uk-select" name="credit" required>
                                    <?php foreach ($creditCoas as $coa): ?>
                                        <option value="<?= $coa['id'] ?>">
                                            <?= $coa['name'] ?> (<?= $coa['code'] ?>)
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="uk-margin">
                            <label class="uk-form-label">Nominal <span style="color: red;">*</span></label>
                            <div class="uk-inline uk-width-1-1">
                                <span class="uk-form-icon uk-text-bold">Rp</span>
                                <input class="uk-input uk-border-rounded uk-form-large" id="nominal" name="amount"
                                    type="text" placeholder="0" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="uk-margin" id="tanam_modal">
                        <div class="uk-margin">
                            <label class="uk-form-label">Simpan ke (Debit) <span style="color: red;">*</span></label>
                            <div class="uk-form-controls">
                                <select class="uk-select" name="debit" required>
                                    <?php foreach ($debitCoas as $coa): ?>
                                        <option value="<?= $coa['id'] ?>">
                                            <?= $coa['name'] ?> (<?= $coa['code'] ?>)
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="uk-margin">
                            <label class="uk-form-label">Modal (Kredit) <span style="color: red;">*</span></label>
                            <div class="uk-form-controls">
                                <select class="uk-select" name="credit" required>
                                    <?php foreach ($creditCoas as $coa): ?>
                                        <option value="<?= $coa['id'] ?>">
                                            <?= $coa['name'] ?> (<?= $coa['code'] ?>)
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="uk-margin">
                            <label class="uk-form-label">Nominal <span style="color: red;">*</span></label>
                            <div class="uk-inline uk-width-1-1">
                                <span class="uk-form-icon uk-text-bold">Rp</span>
                                <input class="uk-input uk-border-rounded uk-form-large" id="nominal" name="amount"
                                    type="text" placeholder="0" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="uk-margin" id="tarik_modal">
                        <div class="uk-margin">
                            <label class="uk-form-label">Modal (Debit) <span style="color: red;">*</span></label>
                            <div class="uk-form-controls">
                                <select class="uk-select" name="debit" required>
                                    <?php foreach ($debitCoas as $coa): ?>
                                        <option value="<?= $coa['id'] ?>">
                                            <?= $coa['name'] ?> (<?= $coa['code'] ?>)
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="uk-margin">
                            <label class="uk-form-label">Diambil dari (Kredit) <span style="color: red;">*</span></label>
                            <div class="uk-form-controls">
                                <select class="uk-select" name="credit" required>
                                    <?php foreach ($creditCoas as $coa): ?>
                                        <option value="<?= $coa['id'] ?>">
                                            <?= $coa['name'] ?> (<?= $coa['code'] ?>)
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="uk-margin">
                            <label class="uk-form-label">Nominal <span style="color: red;">*</span></label>
                            <div class="uk-inline uk-width-1-1">
                                <span class="uk-form-icon uk-text-bold">Rp</span>
                                <input class="uk-input uk-border-rounded uk-form-large" id="nominal" name="amount"
                                    type="text" placeholder="0" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="uk-margin" id="transfer_uang">
                        <div class="uk-margin">
                            <label class="uk-form-label">Ke (Debit) <span style="color: red;">*</span></label>
                            <div class="uk-form-controls">
                                <select class="uk-select" name="debit" required>
                                    <?php foreach ($debitCoas as $coa): ?>
                                        <option value="<?= $coa['id'] ?>">
                                            <?= $coa['name'] ?> (<?= $coa['code'] ?>)
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="uk-margin">
                            <label class="uk-form-label">Dari (Kredit) <span style="color: red;">*</span></label>
                            <div class="uk-form-controls">
                                <select class="uk-select" name="credit" required>
                                    <?php foreach ($creditCoas as $coa): ?>
                                        <option value="<?= $coa['id'] ?>">
                                            <?= $coa['name'] ?> (<?= $coa['code'] ?>)
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="uk-margin">
                            <label class="uk-form-label">Nominal <span style="color: red;">*</span></label>
                            <div class="uk-inline uk-width-1-1">
                                <span class="uk-form-icon uk-text-bold">Rp</span>
                                <input class="uk-input uk-border-rounded uk-form-large" id="nominal" name="amount"
                                    type="text" placeholder="0" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="uk-margin" id="pemasukan_sebagai_piutang">
                        <div class="uk-margin">
                            <label class="uk-form-label">Simpan ke (Debit) <span style="color: red;">*</span></label>
                            <div class="uk-form-controls">
                                <select class="uk-select" name="debit" required>
                                    <?php foreach ($debitCoas as $coa): ?>
                                        <option value="<?= $coa['id'] ?>">
                                            <?= $coa['name'] ?> (<?= $coa['code'] ?>)
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="uk-margin">
                            <label class="uk-form-label">Diterima dari (Kredit) <span style="color: red;">*</span></label>
                            <div class="uk-form-controls">
                                <select class="uk-select" name="credit" required>
                                    <?php foreach ($creditCoas as $coa): ?>
                                        <option value="<?= $coa['id'] ?>">
                                            <?= $coa['name'] ?> (<?= $coa['code'] ?>)
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="uk-margin">
                            <label class="uk-form-label">Nominal <span style="color: red;">*</span></label>
                            <div class="uk-inline uk-width-1-1">
                                <span class="uk-form-icon uk-text-bold">Rp</span>
                                <input class="uk-input uk-border-rounded uk-form-large" id="nominal" name="amount"
                                    type="text" placeholder="0" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="uk-margin" id="pengeluaran_sebagai_hutang">
                        <div class="uk-margin">
                            <label class="uk-form-label">Untuk biaya (Debit) <span style="color: red;">*</span></label>
                            <div class="uk-form-controls">
                                <select class="uk-select" name="debit" required>
                                    <?php foreach ($debitCoas as $coa): ?>
                                        <option value="<?= $coa['id'] ?>">
                                            <?= $coa['name'] ?> (<?= $coa['code'] ?>)
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="uk-margin">
                            <label class="uk-form-label">Diambil dari (Kredit) <span style="color: red;">*</span></label>
                            <div class="uk-form-controls">
                                <select class="uk-select" name="credit" required>
                                    <?php foreach ($creditCoas as $coa): ?>
                                        <option value="<?= $coa['id'] ?>">
                                            <?= $coa['name'] ?> (<?= $coa['code'] ?>)
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="uk-margin">
                            <label class="uk-form-label">Nominal <span style="color: red;">*</span></label>
                            <div class="uk-inline uk-width-1-1">
                                <span class="uk-form-icon uk-text-bold">Rp</span>
                                <input class="uk-input uk-border-rounded uk-form-large" id="nominal" name="amount"
                                    type="text" placeholder="0" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="uk-margin">
                        <label class="uk-form-label">Catatan <span style="color: red;">*</span></label>
                        <div class="uk-form-controls">
                            <textarea class="uk-textarea" rows="3" name="note" placeholder="Catatan" required></textarea>
                        </div>
                    </div>

                    <div class="uk-margin" id="piutang" hidden>
                        <label class="uk-form-label">Bunga (%) (optional)</label>
                        <div class="uk-inline uk-width-1-1">
                            <input class="uk-input uk-border-rounded uk-form-large" id="percentage" name="bunga" type="number" placeholder="0%" required>
                            <p class="uk-margin-small-top">Info: Bunga akan masuk ke akun Pendapatan Bunga</p>
                        </div>
                    </div>
                    
                    <div class="uk-margin">
                        <label class="uk-form-label">Kontak</label>
                        <div class="uk-form-controls">
                            <select class="uk-select" name="contact">
                                <option value="" selected disabled>Pilih ...</option>
                                <?php foreach ($contacts as $c): ?>
                                    <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>

                    <div class="uk-margin">
                        <a class="uk-text-small uk-margin-small-top uk-display-inline-block" uk-toggle="target: #tax">Opsional</a>
                    </div>

                    <div class="uk-margin" id="tax" hidden>
                        <div class="uk-margin">
                            <label class="uk-form-label">Pajak</label>
                            <div class="uk-form-controls">
                                <select class="uk-select" name="tax">
                                    <option value="" selected disabled>Pilih Pajak</option>
                                    <?php foreach ($taxes as $tax) { ?>
                                        <option value="<?= $tax['id'] ?>"><?= $tax['name'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="uk-margin">
                            <label class="uk-form-label">Jatuh Tempo</label>
                            <div class="uk-form-controls">
                                <input type="date" name="duedate" value="<?= date('Y-m-d') ?>" class="uk-input uk-border-rounded">
                            </div>
                        </div>

                        <div class="uk-margin">
                            <label class="uk-form-label">Lampiran</label>
                            <div class="uk-form-controls">
                                <input type="text" name="attachment" class="uk-input uk-border-rounded" placeholder="Pilih file">
                            </div>
                        </div>
                    </div>

                    <div class="uk-margin">
                        <button type="submit" class="uk-button uk-button-primary uk-width-1-1 uk-button-large">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div>
            <div class="uk-card uk-card-default">
                <div class="uk-card-header uk-card-title">
                    <h3>History Transaksi</h3>
                </div>
                <div class="uk-card-body">
                    <p>Anda dapat melihat data history transaksi yang sudah di simpan</p>
                    <div class="uk-margin">
                        <a href="#" class="uk-button uk-button-default uk-width-1-1 uk-button-large">
                            Lihat
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#nominal').on('input', function() {
            let value = $(this).val();
            value = value.replace(/[^,\d]/g, '').toString();
            let split = value.split(',');
            let sisa = split[0].length % 3;
            let rupiah = split[0].substr(0, sisa);
            let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            $(this).val(rupiah);
        });
    });
    
    document.addEventListener('DOMContentLoaded', function() {
        const now = new Date();

        document.querySelector('input[name="day"]').value   = now.getDate().toString().padStart(2,'0');
        document.querySelector('input[name="month"]').value = (now.getMonth()+1).toString().padStart(2,'0');
        document.querySelector('input[name="year"]').value  = now.getFullYear();
        document.querySelector('input[name="time"]').value  = 
            now.getHours().toString().padStart(2,'0') + ":" + 
            now.getMinutes().toString().padStart(2,'0');
    });
</script>
<?= $this->endSection() ?>