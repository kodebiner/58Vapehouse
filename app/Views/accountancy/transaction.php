<?= $this->extend('layout') ?>
<?= $this->section('extraScript') ?>
<script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
<script src="js/cdn.datatables.net_1.13.4_js_jquery.dataTables.min.js"></script>
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
<div class="uk-width-1-1 uk-height-1-1" class="uk-inline">
    <div>
        <?= view('Views/Auth/_permission_message') ?>
    </div>

    <!-- Page Heading -->
    <div class="tm-card-header uk-light uk-margin-bottom">
        <h3 class="tm-h3"><?=lang('Global.accountancy').' - Tambah Akuntansi'?></h3>
        <?= view('Views/Auth/_message_block') ?>
    </div>

    <div uk-grid class="uk-flex-top uk-child-width-1-2@m">
        <div>
            <div class="uk-card uk-card-default uk-card-body uk-margin">
                <h4 class="uk-margin-small-bottom">Tambah Transaksi</h4>

                <form action="accountancy/transaction/create" method="post" class="uk-form-stacked" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <?php $now = new \DateTime(); ?>

                    <!-- ================= TANGGAL ================= -->
                     <div class="uk-margin">
                        <label class="uk-form-label">Tanggal <span style="color:red;"> *</span></label>
                        <input type="datetime-local" name="date" 
                            value="<?= date('Y-m-d\TH:i') ?>" 
                            class="uk-input uk-border-rounded" placeholder="Pilih Tanggal" required>
                    </div>

                    <!-- ================= TYPE ================= -->
                    <div class="uk-margin">
                        <label class="uk-form-label">Jenis Transaksi <span style="color:red;"> *</span></label>
                        <select class="uk-select" name="type" id="transactionType" required>
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

                    <!-- ================= SECTION DINAMIS ================= -->

                    <?php
                    function coaSelect($name,$label,$data){
                    ?>
                    <div class="uk-margin">
                        <label class="uk-form-label"><?= $label ?> <span style="color:red;"> *</span></label>
                        <select class="uk-select select-search" name="<?= $name ?>" required>
                            <option value="">Pilih Akun...</option>
                            <?php foreach ($data as $coa): ?>
                                <option value="<?= $coa['id'] ?>">
                                    <?= $coa['coa_full_name'] ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <?php } ?>

                    <?php
                    $sections = [
                    1=>['id'=>'pemasukkan','debit'=>'Simpan ke (Debit)','credit'=>'Diterima dari (Kredit)'],
                    2=>['id'=>'pengeluaran','debit'=>'Untuk biaya (Debit)','credit'=>'Diambil dari (Kredit)'],
                    3=>['id'=>'hutang','debit'=>'Simpan ke (Debit)','credit'=>'Hutang dari (Kredit)'],
                    4=>['id'=>'piutang','debit'=>'Simpan ke (Debit)','credit'=>'Piutang dari (Kredit)'],
                    5=>['id'=>'tanam_modal','debit'=>'Simpan ke (Debit)','credit'=>'Modal (Kredit)'],
                    6=>['id'=>'tarik_modal','debit'=>'Modal (Debit)','credit'=>'Diambil dari (Kredit)'],
                    7=>['id'=>'transfer_uang','debit'=>'Ke (Debit)','credit'=>'Dari (Kredit)'],
                    8=>['id'=>'pemasukan_sebagai_piutang','debit'=>'Simpan ke (Debit)','credit'=>'Diterima dari (Kredit)'],
                    9=>['id'=>'pengeluaran_sebagai_hutang','debit'=>'Untuk biaya (Debit)','credit'=>'Diambil dari (Kredit)'],
                    ];

                    foreach($sections as $key=>$s):
                    ?>
                    <div class="uk-margin trx-section" id="<?= $s['id'] ?>">
                        <?php coaSelect('debit',$s['debit'],$debitCoas); ?>
                        <?php coaSelect('credit',$s['credit'],$creditCoas); ?>
                    </div>
                    <?php endforeach; ?>

                    <!-- ================= NOMINAL GLOBAL ================= -->
                    <div class="uk-margin">
                        <label class="uk-form-label">Nominal <span style="color:red;"> *</span></label>
                        <div class="uk-inline uk-width-1-1">
                            <span class="uk-form-icon uk-text-bold">Rp</span>
                            <input type="hidden" name="amount" id="amount_hidden">
                            <input class="uk-input uk-border-rounded uk-form-large money-idr"
                                data-target="amount_hidden"
                                type="text"
                                placeholder="0"
                                required>
                        </div>
                    </div>

                    <!-- ================= CATATAN ================= -->
                    <div class="uk-margin">
                        <label class="uk-form-label">Catatan <span style="color:red;"> *</span></label>
                        <textarea class="uk-textarea" rows="3"
                            name="note" required></textarea>
                    </div>

                    <!-- ================= BUNGA ================= -->
                    <div class="uk-margin" id="piutang_bunga" hidden>
                        <label class="uk-form-label">Bunga (%)</label>
                        <input class="uk-input uk-border-rounded uk-form-large"
                            id="percentage" name="bunga" type="number" min="0" step="0.01" max="100"
                            placeholder="0%">
                    </div>

                    <!-- ================= CONTACT ================= -->
                    <div class="uk-margin">
                        <label class="uk-form-label" id="contactLabel">Kontak <span class="required-star" style="color:red; display:none;"> *</span></label>
                        <div class="uk-form-controls">
                            <select class="uk-select select-search" name="contact" id="contactField" placeholder="Cari kontak...">
                                <option value="" selected disabled>Pilih ...</option>
                                <?php foreach ($contacts as $c): ?>
                                    <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>

                    <div class="uk-margin">
                        <a class="uk-text-small uk-margin-small-top uk-display-inline-block" uk-toggle="target: #optional">Opsional</a>
                    </div>

                    <div class="uk-margin" id="optional" hidden>
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
                                <input type="date" name="duedate" class="uk-input uk-border-rounded" placeholder="Pilih Tanggal Jatuh Tempo">
                            </div>
                        </div>

                        <div class="uk-margin">
                            <label class="uk-form-label">Lampiran</label>
                            <div class="uk-form-controls" uk-form-custom="target: true">
                                <input type="file" name="attachment" aria-label="Custom controls" accept=".pdf,.jpg,.jpeg,.png">
                                <input class="uk-input uk-form-width-medium" type="text" placeholder="Pilih file" aria-label="Custom controls" disabled>
                                <button class="uk-button uk-button-default" style="border-radius: 8px;">Cari</button>
                            </div>
                        </div>
                    </div>

                    <!-- ================= SUBMIT ================= -->
                    <div class="uk-margin">
                        <button type="submit" class="uk-button uk-button-primary uk-width-1-1 uk-button-large" style="border-radius: 8px;">
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
                        <a href="accountancy/transaction/history" class="uk-button uk-button-default uk-width-1-1 uk-button-large" style="border-radius: 8px;">
                            Lihat
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    /* ===============================
       INIT ELEMENTS
    =============================== */

    const typeSelect     = document.getElementById('transactionType');
    const sections       = document.querySelectorAll('.trx-section');
    const bungaField     = document.getElementById('piutang_bunga');
    const contactField   = document.getElementById('contactField');
    const contactLabel   = document.getElementById('contactLabel');
    const contactStar    = contactLabel ? contactLabel.querySelector('.required-star') : null;

    /* ===============================
       TOMSELECT
    =============================== */

    document.querySelectorAll('.select-search').forEach(el => {
        new TomSelect(el,{
            create:false,
            sortField:{ field:"text", direction:"asc" }
        });
    });

    /* ===============================
       FORMAT RUPIAH
    =============================== */

    document.querySelectorAll('.money-idr').forEach(input => {

        const hidden = document.getElementById(input.dataset.target);

        input.addEventListener('input', function () {

            let numeric = this.value.replace(/\D/g,'');
            hidden.value = numeric;

            this.value = numeric
                ? new Intl.NumberFormat('id-ID').format(numeric)
                : '';
        });

    });

    /* ===============================
       SECTION CONTROL
    =============================== */

    function hideAllSections(){
        sections.forEach(section => {
            section.style.display = 'none';
            section.querySelectorAll('select').forEach(select => {
                select.disabled = true;
            });
        });

        if(bungaField) bungaField.hidden = true;
    }

    function showSection(type){

        const map = {
            1:'pemasukkan',
            2:'pengeluaran',
            3:'hutang',
            4:'piutang',
            5:'tanam_modal',
            6:'tarik_modal',
            7:'transfer_uang',
            8:'pemasukan_sebagai_piutang',
            9:'pengeluaran_sebagai_hutang'
        };

        const sectionId = map[type];
        if(!sectionId) return;

        const activeSection = document.getElementById(sectionId);
        if(!activeSection) return;

        activeSection.style.display = 'block';

        activeSection.querySelectorAll('select').forEach(select => {
            select.disabled = false;
        });

        // Khusus Piutang tampilkan bunga
        if(parseInt(type) === 4 && bungaField){
            bungaField.hidden = false;
        }
    }

    /* ===============================
       CONTACT REQUIREMENT
    =============================== */

    function handleContactRequirement(type){

        if(!contactField || !contactStar) return;

        const requiredTypes = [3,4,8,9];

        if(requiredTypes.includes(parseInt(type))){
            contactField.required = true;
            contactStar.style.display = 'inline';
        } else {
            contactField.required = false;
            contactStar.style.display = 'none';
        }
    }

    /* ===============================
       INIT STATE
    =============================== */

    function initialize(){
        if(!typeSelect) return;

        hideAllSections();
        showSection(typeSelect.value);
        handleContactRequirement(typeSelect.value);
    }

    initialize();

    /* ===============================
       EVENT LISTENER
    =============================== */

    if(typeSelect){
        typeSelect.addEventListener('change', function(){
            hideAllSections();
            showSection(this.value);
            handleContactRequirement(this.value);
        });
    }
});
</script>
<?= $this->endSection() ?>