<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= lang('Global.stockMove') ?></title>
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
            /* border: 1pt solid black; */
            border-collapse: collapse;
            padding: 3px;
        }

        .img2 {
            float: left;
            text-align: center;
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
            /* margin: auto;  */
            width: 50%;
        }
    </style>
</head>

<body>
    <!-- Origin Data -->
    <table style="width:100%; margin-top:10px">
        <tr>
            <th style="text-align:left"><?= $stockmovedata['origin'] ?></th>
        </tr>
        <tr>
            <td><?= $stockmovedata['originaddress'] ?></td>
        </tr>
        <tr>
            <td><?= lang('Global.phone'). ' ' .$stockmovedata['originphone'] ?></td>
        </tr>
    </table>
    <!-- Origin Data End -->

    <!-- Title -->
    <h2 style="text-align:center"><?= lang('Global.stockMove') ?></h2>
    <!-- Title End -->

    <!-- Destination Data & Invoice Number -->
    <table style="width:100%; margin-top:10px">
        <tr>
            <th style="width:60%; text-align:left; font-weight:normal;">Kepada :</th>
            <th style="width:10%; text-align:left; font-weight:normal;">No <?= lang('Global.invoice') ?> :</th>
        </tr>
        <tr>
            <th style="text-align:left"><?= $stockmovedata['destination'] ?></th>
            <?php
                $datedata   = strtotime($stockmovedata['date']);
                $date       = date('Ymd', $datedata);
            ?>
            <th style="width:10%; text-align:left"><?= "SM" . $date . $stockmovedata['id'] ?></th>
        </tr>
        <tr>
            <td><?= $stockmovedata['destinationaddress'] ?></td>
        </tr>
        <tr>
            <td><?= lang('Global.phone'). ' ' .$stockmovedata['destinationphone'] ?></td>
        </tr>
    </table>
    <!-- Destination Data & Invoice Number End -->

    <!-- Product List Data -->
    <table style="width:100%; margin-top:50px">
        <tr>
            <th style="width:10%; text-align:left; border:1px solid black">SKU</th>
            <th style="width:10%; text-align:left; border:1px solid black"><?= lang('Global.product') ?></th>
            <th style="width:10%; text-align:left; border:1px solid black"><?= lang('Global.totalMovement') ?></th>
            <th style="width:10%; text-align:left; border:1px solid black"><?=lang('Global.total').' '.lang('Global.capitalPrice')?></th>
        </tr>
        <?php
        foreach ($stockmovedata['detail'] as $detail) {
        ?>
            <tr>
                <td style="border:1px solid black"><?= $detail['sku'] ?></td>
                <td style="border:1px solid black"><?= $detail['name'] ?></td>
                <td style="border:1px solid black"><?= $detail['qty'] ?> Pcs</td>
                <td style="border:1px solid black"><?= "Rp ".number_format($detail['wholesale'],0,',','.') ?></td>
            </tr>
        <?php
        }
        ?>
        <tr>
            <td style="border-left:1px solid black; border-bottom:1px solid black; font-weight:bold;"><?= lang('Global.totalMovement'); ?></td>
            <td style="border-bottom:1px solid black"></td>
            <td style="border:1px solid black; font-weight:bold;"><?= $stockmovedata['totalqty'] ?> Pcs</td>
            <td style="border:1px solid black; font-weight:bold;"><?= "Rp ".number_format($stockmovedata['totalwholesale'],0,',','.'); ?></td>
        </tr>
    </table>
    <!-- Product List Data End -->

    <!-- Approval -->
    <table style="width:100%; margin-top:50px">
        <tr>
            <th style="text-align:left; font-weight:normal;"><?= lang('Global.sender') ?></th>
            <th style="text-align:left"></th>
            <th style="text-align:left; font-weight:normal;"><?= lang('Global.receiver') ?></th>
            <th style="text-align:left"></th>
            <th style="text-align:left"></th>
            <th style="text-align:left"></th>
            <th style="text-align:left"></th>
            <th style="text-align:left"></th>
            <th style="text-align:left"></th>
            <th style="text-align:left"></th>
            <th style="text-align:left"></th>
            <th style="text-align:left"></th>
            <th style="text-align:left"></th>
            <th style="text-align:left"></th>
            <th style="text-align:left"></th>
        </tr>
        <tr>
            <td style="font-weight:bold;"><?= $stockmovedata['origin'] ?></td>
            <td></td>
            <td style="font-weight:bold;"><?= $stockmovedata['destination'] ?></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td style="border-bottom:1px solid black"></td>
            <td></td>
            <td style="border-bottom:1px solid black"></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </table>
    <!-- Approval End -->
</body>

</html>