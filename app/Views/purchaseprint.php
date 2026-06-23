<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= lang('Global.purchase') ?></title>
    <style>
        html {
            font-size: 8pt
        }

        hr {
            margin: 0;
        }

        table,
        th,
        td {
            border-collapse: collapse;
            padding: 3px;
        }
    </style>
</head>

<body>
    <table style="width:100%; margin-top:10px">
        <tr>
            <th style="text-align:left"><?= $purchasedata['outlet'] ?></th>
        </tr>
        <tr>
            <td><?= $purchasedata['address'] ?></td>
        </tr>
        <tr>
            <td><?= lang('Global.phone'). ' ' .$purchasedata['phone'] ?></td>
        </tr>
    </table>

    <h2 style="text-align:center"><?= lang('Global.purchase') ?></h2>

    <table style="width:100%; margin-top:10px">
        <tr>
            <th style="width:60%; text-align:left; font-weight:normal;"><?= lang('Global.supplier') ?> :</th>
            <th style="width:10%; text-align:left; font-weight:normal;"><?= lang('Global.invoice') ?> :</th>
        </tr>
        <tr>
            <th style="text-align:left"><?= $purchasedata['supplier'] ?></th>
            <?php
                $datedata   = strtotime($purchasedata['date']);
                $date       = date('Ymd', $datedata);
            ?>
            <th style="width:10%; text-align:left"><?= "PO" . $date . $purchasedata['id'] ?></th>
        </tr>
    </table>

    <table style="width:100%; margin-top:50px">
        <tr>
            <th style="width:10%; text-align:left; border:1px solid black">SKU</th>
            <th style="width:10%; text-align:left; border:1px solid black"><?= lang('Global.product') ?></th>
            <th style="width:10%; text-align:left; border:1px solid black"><?= lang('Global.variant') ?></th>
            <th style="width:10%; text-align:left; border:1px solid black"><?= lang('Global.quantity') ?></th>
            <th style="width:10%; text-align:left; border:1px solid black"><?= lang('Global.pcsPrice') ?></th>
            <th style="width:10%; text-align:left; border:1px solid black"><?= lang('Global.total') ?></th>
        </tr>
        <?php
        foreach ($purchasedata['detail'] as $detail) {
        ?>
            <tr>
                <td style="border:1px solid black"><?= $detail['sku'] ?></td>
                <td style="border:1px solid black"><?= $detail['productname'] ?></td>
                <td style="border:1px solid black"><?= $detail['variantname'] ?></td>
                <td style="border:1px solid black"><?= $detail['qty'] ?> Pcs</td>
                <td style="border:1px solid black"><?= "Rp ".number_format($detail['price'],0,',','.') ?></td>
                <td style="border:1px solid black"><?= "Rp ".number_format((Int)$detail['qty'] * (Int)$detail['price'],0,',','.') ?></td>
            </tr>
        <?php
        }
        ?>
        <tr>
            <td style="border-left:1px solid black; border-bottom:1px solid black; font-weight:bold;"><?= lang('Global.totalPurchase'); ?></td>
            <td style="border-bottom:1px solid black"></td>
            <td style="border-bottom:1px solid black"></td>
            <td style="border:1px solid black; font-weight:bold;"><?= $purchasedata['totalqty'] ?> Pcs</td>
            <td style="border-bottom:1px solid black"></td>
            <td style="border:1px solid black; font-weight:bold;"><?= "Rp ".number_format($purchasedata['totalprice'],0,',','.') ?></td>
        </tr>
    </table>

    <table style="width:100%; margin-top:20px">
        <tr>
            <th style="text-align:left; font-weight:normal;"><?= lang('Global.employee') ?></th>
            <th></th>
        </tr>
        <tr>
            <td><?= $purchasedata['user'] ?></td>
            <td></td>
        </tr>
    </table>

    <table style="width:100%; margin-top:10px">
        <tr>
            <th style="text-align:left; font-weight:normal;"><?= lang('Global.date') ?></th>
            <th style="text-align:left"></th>
        </tr>
        <tr>
            <td><?= date('l, d M Y, H:i:s', strtotime($purchasedata['date'])) ?></td>
            <td></td>
        </tr>
    </table>
</body>

</html>
