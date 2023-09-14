<?= $this->extend('layout') ?>

<?= $this->section('extraScript') ?>
<script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
  google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
      var data = new google.visualization.DataTable();
      data.addColumn('string', 'name');
      data.addColumn('number', 'quantity');
      data.addColumn('number', 'value');
      data.addRows([
        <?php foreach ($payments as $pay){
            $value = $pay['pvalue'];
            $name = $pay['pname'];
            $qty = $pay['qty'];
            echo "['$name', $value,$qty],";
          }?>
      ]);

       

        var options = {
          title: 'Payment Percentage %'
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart'));

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
      <h3 class="uk-card-title">Payment</h3>
      <div id="piechart"></div>
  </div>

<!-- End Of Page Heading -->

<?= view('Views/Auth/_message_block') ?>

<?= $this->endSection() ?>