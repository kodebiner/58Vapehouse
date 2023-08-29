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
    <head>
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
            .img {
                border: 1px solid #ddd;
                border-radius:50%;
                padding: 0px;
                width: 100px;
            }
        </style>
    </head>
    <body style='font-family:tahoma; font-size:8pt; color:white;'>
        <center>
            <div>
                <img src="/img/58vape.png" class="img" alt="Paris">
            </div>
            <table style='width:350px; font-size:16pt; font-family:calibri; border-collapse: collapse;' border = '0'>
                <span style='font-size:16pt; color:black;  font-family:calibri;'><b>58 Vape House</b></br>JL Mangkubumi No 58 Yogyakarta</span></br>
          
                <tr>
                    <td>
                        <span style='text-align:left; width:350px; font-size:12pt; font-family:calibri;  border-collapse: collapse;'>No.312984014348</span></br>
                        <span style=' width:350px; font-size:12pt; font-family:calibri text-align:left'>Date.11 Juni 2020 / 11:57:50</span></br>
                    </td>
                    <td>
                        <span style=' width:350px; font-size:12pt; font-family:calibri text-align:right'>Cashier.Dismas</span></br>
                        <span style=' width:350px; font-size:12pt; font-family:calibri text-align:right'>Payment.BCA</span></br>
                    </td>
                </tr>
                <tr>
                    <td colspan="5">
                        <hr>
                    </td>
                </tr>
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
                    <td width='13%'>Total</td>
                    <tr>
                        <td colspan='5'><hr></td>
                    </tr>
                </tr>
                <tr>
                    <td style='vertical-align:top'>3 WAY STOPCOCK</td>
                    <td style='vertical-align:top; text-align:right; padding-right:10px'>7.440</td>
                    <td style='vertical-align:top; text-align:right; padding-right:10px'>100</td>
                    <td style='vertical-align:top; text-align:right; padding-right:10px'>0,00%</td>
                    <td style='text-align:right; vertical-align:top'>744.000</td>
                </tr>
                <tr>
                    <td colspan='5'><hr></td>
                </tr>
                <tr>
                    <td colspan = '4'><div style='text-align:left'>subtotal </div></td><td style='text-align:right; font-size:13pt;'>3.500,00</td>
                </tr>
                <tr>
                    <td colspan = '4'><div style='text-align:left; color:black'>Total </div></td><td style='text-align:right; font-size:13pt; color:black'>747.500</td>
                </tr>
                <tr>
                    <td colspan = '4'><div style='text-align:left; color:black'>Pay </div></td><td style='text-align:right; font-size:13pt; color:black'>1.000.000</td>
                </tr>
                <tr>
                    <td colspan = '4'><div style='text-align:left; color:black'>Change</div></td><td style='text-align:right; font-size:13pt; color:black'>252.500</td>
                </tr>
                <tr>
                    <td colspan = '4'><div style='text-align:left; color:black'>Customer </div></td><td style='text-align:right; font-size:13pt; color:black'>0</td>
                </tr>
                <tr>
                    <td colspan = '4'><div style='text-align:left; color:black'>Points Earned </div></td><td style='text-align:right; font-size:13pt; color:black'>0</td>
                </tr>
            </table>
            <table style='width:350; font-size:12pt;' cellspacing='2'>
                <tr>
                    </br><td align='center'>****** VapingSambilNongkrong ******</br></td>
                </tr>
            </table>
        </center>
    </body>
</html>    
</div>
      
<?= $this->endSection() ?>