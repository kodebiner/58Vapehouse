<?= $this->extend('layout') ?>

<?= $this->section('extraScript') ?>
<script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">

google.charts.load('current', {packages: ['corechart', 'line']});
google.charts.setOnLoadCallback(drawAxisTickColors);

function drawAxisTickColors() {
      var data = new google.visualization.DataTable();
      data.addColumn('string', 'date');
      data.addColumn('number', 'modal');
      data.addColumn('number', 'dasar');

      data.addRows([
        <?php
          foreach ($transactions as $transaction) {
              echo '["'.$transaction['date'].'", '.$transaction['modal'].', '.$transaction['dasar'].'],';
          }
        ?>
      ]);

      var options = {
        hAxis: {
          title: 'Time',
          textStyle: {
            color: '#01579b',
            fontSize: 20,
            fontName: 'Arial',
            bold: true,
            italic: true
          },
          titleTextStyle: {
            color: '#01579b',
            fontSize: 16,
            fontName: 'Arial',
            bold: false,
            italic: true
          }
        },
        vAxis: {
          title: 'margin',
          textStyle: {
            color: '#a52714', 
            fontSize: 14,
            bold: false
          },
          titleTextStyle: {
            color: '#1a237e',
            fontSize: 14,
            bold: true
          }
        },
        colors: ['#000', '#097138']
      };
      var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
      chart.draw(data, options);
    }
    
</script>
<?= $this->endSection() ?>

<?= $this->section('main') ?>

<!-- Page Heading -->
<div class="tm-card-header uk-light">
    <div uk-grid class="uk-flex-middle">
        <div class="uk-width-1-2@m">
            <h3 class="tm-h3"><?=lang('Global.report')?></h3>
        </div>
    </div>
</div>

  <div class="uk-card uk-card-default uk-card-body uk-margin-top uk-width-1-1@m">
      <h3 class="uk-card-title">Keuntungan</h3>
      <div id="chart_div"></div>
  </div>

  <div class="uk-child-width-1-2@s uk-grid-match uk-margin-top" uk-grid>
    <div>
        <div class="uk-card uk-card-default uk-card-secondary uk-card-hover uk-card-body">
            <h3 class="uk-card-title uk-margin-remove-bottom">Keuntungan Modal</h3>
            <p class="uk-margin-remove-top uk-text-bolder"> Total keuntungan Dasar - Total Modal</p>
            <hr>
            <div>
              <div uk-grid>
                  <div class="uk-width-1-2 uk-margin-remove">Total Penjualan</div>
                  <div class="uk-width-expand@m uk-text-right"><?php echo "Rp. ".number_format($totaldasar,2,',','.');" ";?></div>
              </div>
              <hr class="">
              <div class="uk-margin-remove-top" uk-grid>
                  <div class="uk-width-1-2@m">Total Modal Dasar</div>
                  <div class="uk-width-expand@m uk-text-right"><?php echo "Rp. ".number_format($dasars,2,',','.');" ";?></div>
              </div>
              <hr class="">
              <div class="uk-margin-remove-top" uk-grid>
                  <div class="uk-width-1-2@m">Keuntungan Modal</div>
                  <div class="uk-width-expand@m uk-text-right">
                    <?php 
                    $keuntungandasar = $totaldasar - $dasars;
                    echo "Rp. ".number_format($keuntungandasar,2,',','.');" ";
                    ?>
                  </div>
              </div>
            </div>
        </div>
    </div>

    <div>
        <div class="uk-card uk-card-default uk-card-hover uk-card-body">
            <h3 class="uk-card-title uk-margin-remove-bottom">Keuntungan Dasar</h3>
            <p class="uk-margin-remove-top uk-text-bolder"> Total keuntungan Dasar - Total Modal</p>
            <hr>
            <div>
              <div uk-grid>
                  <div class="uk-width-1-2 uk-margin-remove">Total Penjualan</div>
                  <div class="uk-width-expand@m uk-text-right"><?php echo "Rp. ".number_format($totaldasar,2,',','.');" ";?></div>
              </div>
              <hr class="">
              <div class="uk-margin-remove-top" uk-grid>
                  <div class="uk-width-1-2@m">Total Modal</div>
                  <div class="uk-width-expand@m uk-text-right"><?php echo "Rp. ".number_format($dasars,2,',','.');" ";?></div>
              </div>
              <hr class="">
              <div class="uk-margin-remove-top" uk-grid>
                  <div class="uk-width-1-2@m">Keuntungan Modal</div>
                  <div class="uk-width-expand@m uk-text-right">
                    <?php 
                    $keuntungandasar = $totaldasar - $dasars;
                    echo "Rp. ".number_format($keuntungandasar,2,',','.');" ";
                    ?>
                  </div>
              </div>
            </div>
        </div>
    </div>
  </div>
 
<!-- End Of Page Heading -->

<?= view('Views/Auth/_message_block') ?>

<?= $this->endSection() ?>