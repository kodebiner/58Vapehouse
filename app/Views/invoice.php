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
      <h3 class="tm-h3"><?=lang('Global.invoice')?></h3>
    </div>

    <!-- Button Trigger Modal Add -->
    <div class="uk-width-1-2@m uk-text-right@m">
      <button type="button" class="uk-button uk-button-primary uk-preserve-color" uk-toggle="target: #tambahdata"><?=lang('Global.addOutlet')?></button>
    </div>
    <!-- End Of Button Trigger Modal Add -->
  </div>
</div>
<!-- End Of Page Heading -->


<div class="uk-card uk-card-default uk-card-body uk-width-1-2@m uk-position-relative  uk-position-center">
<style>
 
 #tabel
 {
 font-size:15px;
 border-collapse:collapse;
 }
 #tabel  td
 {
 padding-left:5px;
 border: 1px solid black;
 }
 </style>
 </head>
 <body style='font-family:tahoma; font-size:8pt; color:white;'>
 <center><table style='width:350px; font-size:16pt; font-family:calibri; border-collapse: collapse;' border = '0'>
 <td width='70%' align='CENTER' vertical-align:top'><span style='color:black;'>
 <b>58 VapeHouse</b></br>JL XXXXXXXXXXX XXXXXXX</span></br>
  
  
 <span style='font-size:12pt'>No. : xxxxx, 11 Juni 2020 (user:xxxxx), 11:57:50</span></br>
 </td>
 </table>
 <style>
 hr { 
     display: block;
     margin-top: 0.5em;
     margin-bottom: 0.5em;
     margin-left: auto;
     margin-right: auto;
     border-style: inset;
     border-width: 1px;
 } 
 </style>
 <table cellspacing='0' cellpadding='0' style='width:350px; font-size:12pt; font-family:calibri;  border-collapse: collapse;' border='0'>
  
 <tr align='center'>
 <td width='10%'>Item</td>
 <td width='13%'>Price</td>
 <td width='4%'>Qty</td>
 <td width='7%'>Diskon %</td>
 <td width='13%'>Total</td><tr>
 <td colspan='5'><hr></td></tr>
 </tr>
 <tr><td style='vertical-align:top'>3 WAY STOPCOCK</td>
 <td style='vertical-align:top; text-align:right; padding-right:10px'>7.440</td>
 <td style='vertical-align:top; text-align:right; padding-right:10px'>100</td>
 <td style='vertical-align:top; text-align:right; padding-right:10px'>0,00%</td>
 <td style='text-align:right; vertical-align:top'>744.000</td></tr>
 <tr>
 <td colspan='5'><hr></td>
 </tr>
 <tr>
 <td colspan = '4'><div style='text-align:right'>Biaya Adm : </div></td><td style='text-align:right; font-size:16pt;'>Rp3.500,00</td>
 </tr>
 <tr>
 <td colspan = '4'><div style='text-align:right; color:black'>Total : </div></td><td style='text-align:right; font-size:16pt; color:black'>747.500</td>
 </tr>
 <tr>
 <td colspan = '4'><div style='text-align:right; color:black'>Cash : </div></td><td style='text-align:right; font-size:16pt; color:black'>1.000.000</td>
 </tr>
 <tr>
 <td colspan = '4'><div style='text-align:right; color:black'>Change : </div></td><td style='text-align:right; font-size:16pt; color:black'>252.500</td>
 </tr>
 <tr>
 <td colspan = '4'><div style='text-align:right; color:black'>DP : </div></td><td style='text-align:right; font-size:16pt; color:black'>0</td>
 </tr>
 <tr>
 <td colspan = '4'><div style='text-align:right; color:black'>Sisa : </div></td><td style='text-align:right; font-size:16pt; color:black'>0</td>
 </tr>
 </table>
 <table style='width:350; font-size:12pt;' cellspacing='2'><tr></br><td align='center'>****** TERIMAKASIH ******</br></td></tr></table></center></body>
 </html>    
</div>



<table class="uk-table uk-table-divider">
    <thead>
        <tr>
            <th class="uk-table-shrink">Shrink</th>
            <th class="uk-table-expand">Expand</th>
            <th class="uk-width-small">Width Small</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Table Data</td>
            <td>Table Data</td>
            <td>Table Data</td>
        </tr>
        <tr>
            <td>Table Data</td>
            <td>Table Data</td>
            <td>Table Data</td>
        </tr>
    </tbody>
</table>

<hr>

<table class="uk-table uk-table-divider">
    <thead>
        <tr>
            <th class="uk-table-shrink">Shrink</th>
            <th class="uk-table-expand">Expand</th>
            <th class="uk-width-small">Width Small</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Table Data</td>
            <td>Table Data</td>
            <td>Table Data</td>
        </tr>
        <tr>
            <td>Table Data</td>
            <td>Table Data</td>
            <td>Table Data</td>
        </tr>
    </tbody>
</table>

<hr>

<table class="uk-table uk-table-divider">
    <thead>
        <tr>
            <th class="uk-table-shrink">Shrink</th>
            <th class="uk-table-expand">Expand</th>
            <th class="uk-width-small">Width Small</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Table Data</td>
            <td>Table Data</td>
            <td>Table Data</td>
        </tr>
        <tr>
            <td>Table Data</td>
            <td>Table Data</td>
            <td>Table Data</td>
        </tr>
    </tbody>
</table>

<hr>
      


<?= $this->endSection() ?>