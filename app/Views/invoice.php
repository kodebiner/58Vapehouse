<?= $this->extend('layout') ?>

<?= $this->section('extraScript') ?>
<script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
<script src="js/cdn.datatables.net_1.13.4_js_jquery.dataTables.min.js"></script>
<?= $this->endSection() ?>

<?= $this->section('main') ?>

<!-- Page Heading -->
<div class="tm-card-header uk-light" id="pagehead">
    <div uk-grid class="uk-flex-middle">
        <div class="uk-width-1-2@m">
            <h3 class="tm-h3"><?=lang('Global.invoice')?></h3>
        </div>
    </div>
</div>
<!-- End Of Page Heading -->

<?= view('Views/Auth/_message_block') ?>

<!doctype html>
<html dir="ltr "lang="<?=$lang?>" vocab="http://schema.org/" style="overflow-y: hidden;">
        <style>
            @media print {  
                #btn{
                    display : none;
                }
            }
        </style>
      <body style="background-color:#000;">
          <div class="uk-width-1-1 uk-flex uk-flex-center">
              <div style="width:45mm; padding:10mm 2mm; background: #fff;">
                  <div class="uk-margin-small uk-width-1-1 uk-text-center">
                      <img class="uk-width-1-3" src="/img/58vape.png" />
                  </div>
                  <div class="uk-margin-small uk-text-center uk-text-bold">58 Vapehouse<br/><?=$address?></div>
                  <div class="uk-margin-small uk-child-width-1-2 uk-grid-collapse uk-text-xsmall" uk-grid>
                      <div>
                          <div>Invoice: 000000</div>
                          <div>Cashier: <?=$user?></div>                    
                      </div>
                      <div>
                          <div class="uk-text-right"><?php echo date_format($date,"Y/m/d H:i:s")?></div>
                          <div class="uk-text-right"><?=$payment?></div>
                      </div>
                  </div>
                  <hr class="uk-margin-small uk-bold">
                  <div class="uk-margin-small uk-text-xsmall">
                      <div>Mark Made Cerealis Royal Purple 60ml FB C23 - 6mg</div>
                      <div class="uk-grid-collapse" uk-grid>
                          <div class="uk-width-2-3">x1 @140.000</div>
                          <div class="uk-width-1-3">140.000</div>
                      </div>
                      <div class="uk-grid-collapse" uk-grid>
                          <div class="uk-width-2-3">Discount </br> 5000</div>
                          <div class="uk-width-1-3">-5000</div>
                      </div>
                  </div>
                  <hr class="uk-margin-small">
                  <div class="uk-margin-small uk-text-xsmall">
                      <div class="uk-grid-collapse" uk-grid>
                          <div class="uk-width-2-3">Subtotal</div>
                          <div class="uk-width-1-3">5000</div>
                      </div>
                      <div class="uk-grid-collapse" uk-grid>
                          <div class="uk-width-2-3">Pay</div>
                          <div class="uk-width-1-3">5000</div>
                      </div>
                      <div class="uk-grid-collapse" uk-grid>
                          <div class="uk-width-2-3">Change</div>
                          <div class="uk-width-1-3">5000</div>
                      </div>
                      <div class="uk-grid-collapse" uk-grid>
                          <div class="uk-width-2-3">Customer</div>
                          <div class="uk-width-1-3">Leo</div>
                      </div>
                      <div class="uk-grid-collapse" uk-grid>
                          <div class="uk-width-2-3">Point Earned</div>
                          <div class="uk-width-1-3">2000</div>
                      </div>
                      <div class="uk-grid-collapse" uk-grid>
                          <div class="uk-width-2-3">Total Poin</div>
                          <div class="uk-width-1-3">200000</div>
                      </div>
                  </div>

                  <div class="uk-margin uk-text-center">#VapingSambilNongkrong</div>
                  <hr/>
              </div>
          </div>
          <div class="uk-width-1-1@m uk-text-center@m uk-margin-medium-top" id="btn" style=" align-text:center;">
              <button type="button" class="uk-button uk-button-primary uk-preserve-color" onclick="printOut()">Print Invoice</button>
              <button type="button" class="uk-button uk-button-primary uk-preserve-color" uk-toggle="target: #tambahdata">Send Invoice</button>
          </div>
      </body>
  
      <div class="uk-width-1-1@m uk-text-center@m uk-margin-medium-top" id="btn">
        <button type="button" class="uk-button uk-button-primary uk-preserve-color" onclick="printOut()">Print Invoice</button>
        <button type="button" class="uk-button uk-button-primary uk-preserve-color" uk-toggle="target: #tambahdata">Send Invoice</button>
      </div>
</html>
<script>
  var lama = 1000;
  t = null;
  function printOut(){
      window.print();
      t = setTimeout("self.close()",lama);
  }
</script>
      
<?= $this->endSection() ?>