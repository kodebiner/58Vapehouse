<?= $this->extend('layout') ?>

<?= $this->section('extraScript') ?>
<script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
<script src="js/cdn.datatables.net_1.13.4_js_jquery.dataTables.min.js"></script>
<?= $this->endSection() ?>

<?= $this->section('main') ?>

<!-- Page Heading -->
<div class="tm-card-header uk-light">
  <?= view('Views/Auth/_message_block') ?>
  <div uk-grid class="uk-flex-middle">
    <div class="uk-width-1-2@m">
      <h3 class="tm-h3"><?=lang('Global.reminder');?></h3>
    </div>
  </div>
</div>

<div class="uk-grid-column-small uk-grid-row-large uk-child-width-1-3@s  uk-margin-large-top" uk-grid>
    <div>
        <div class="uk-card uk-card-default uk-width-1-1@m">
            <div class="uk-card-header">
                <div class="uk-grid-small uk-flex-middle" uk-grid>
                    <div class="uk-width-auto">
                        <img class="uk-border-circle" width="40" height="40" src="img/58vape.png" alt="Avatar">
                    </div>
                    <div class="uk-width-expand">
                        <h3 class="uk-card-title uk-margin-remove-bottom uk-text-right">Oxva Slim V2</h3>
                        <p class="uk-text-meta uk-margin-remove-top uk-text-right"><time datetime="2016-04-01T19:00">24 agustus 2023</time></p>
                    </div>
                </div>
            </div>
            <div class="uk-card-body">
                <p class="uk-text-center">Stock Menipis</p>
                <p class="uk-text-center">Product Hampir Kadaluarsa</p>
                <dl>
                    <dt class="uk-text-center">Product Expired</dt>
                </dl>
            </div>
            <div class="uk-card-footer">
                <a href="#" class="uk-button uk-button-text">Add Stock</a>
            </div>
        </div>
    </div>
    <div>
        <div class="uk-card uk-card-default uk-width-1-1@m">
            <div class="uk-card-header">
                <div class="uk-grid-small uk-flex-middle" uk-grid>
                    <div class="uk-width-auto">
                        <img class="uk-border-circle" width="40" height="40" src="img/58vape.png" alt="Avatar">
                    </div>
                    <div class="uk-width-expand">
                        <h3 class="uk-card-title uk-margin-remove-bottom uk-text-right">Oxva Slim V2</h3>
                        <p class="uk-text-meta uk-margin-remove-top uk-text-right"><time datetime="2016-04-01T19:00">24 agustus 2023</time></p>
                    </div>
                </div>
            </div>
            <div class="uk-card-body">
                <p class="uk-text-center">Stock Menipis</p>
            </div>
            <div class="uk-card-footer">
                <a href="#" class="uk-button uk-button-text">Add Stock</a>
            </div>
        </div>
    </div>
    <div>
        <div class="uk-card uk-card-default uk-card-body">Item</div>
    </div>
    <div>
        <div class="uk-card uk-card-default uk-card-body">Item</div>
    </div>
    <div>
        <div class="uk-card uk-card-default uk-card-body">Item</div>
    </div>
    <div>
        <div class="uk-card uk-card-default uk-card-body">Item</div>
    </div>
    <div>
        <div class="uk-card uk-card-default uk-card-body">Item</div>
    </div>
</div>

<div class="uk-margin uk-margin-small-left" uk-grid>
    <div class="uk-card uk-card-default uk-width-1-2@m">
        <div class="uk-card-header">
            <div class="uk-grid-small uk-flex-middle" uk-grid>
                <div class="uk-width-auto">
                    <img class="uk-border-circle" width="40" height="40" src="images/avatar.jpg" alt="Avatar">
                </div>
                <div class="uk-width-expand">
                    <h3 class="uk-card-title uk-margin-remove-bottom">Title</h3>
                    <p class="uk-text-meta uk-margin-remove-top"><time datetime="2016-04-01T19:00">24 agustus 2023</time></p>
                </div>
            </div>
        </div>
        <div class="uk-card-body">
            <p>Stock Menipis</p>
        </div>
        <div class="uk-card-footer">
            <a href="#" class="uk-button uk-button-text">Add Stock</a>
        </div>
    </div>
</div>


<?= $this->endSection() ?>