<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stok Opname <?= $outlet ?></title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10pt; }
        table,
        th,
        td {
            /* border: 1pt solid black; */
            border-collapse: collapse;
            padding: 3px;
        }
        /* thead { display: table-header-group; }
        tfoot { display: table-footer-group; } */
        .page-break { page-break-before: always; }
    </style>
</head>

<body>
    <!-- Data Stock Page -->
    <table style="width:100%">
        <thead>
            <tr>
                <th style="border:1px solid black">Nama</th>
                <th style="border:1px solid black">Kategori</th>
                <th style="border:1px solid black">Stok</th>
                <th style="border:1px solid black">Umur Produk</th>
                <th style="border:1px solid black">Selisih</th>
                <th style="border:1px solid black">Selisih POS</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($stockopnames as $stockopname) { ?>
                <tr>
                    <td style="border:1px solid black"><?= $stockopname['product'] ?></td>
                    <td style="border:1px solid black"><?= $stockopname['category'] ?></td>
                    <td style="border:1px solid black"><?= $stockopname['stock'] ?></td>
                    <td style="border:1px solid black"><?= $stockopname['productage'] ?></td>
                    <td style="border:1px solid black"></td>
                    <td style="border:1px solid black"></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- Notes Page -->
    <div style="margin-bottom:80px;text-align:center;font-weight:bold;">Catatan Stok Opname</div>

    <div class="page-break"></div>

    <!-- SOP Page -->
    <div style="margin-bottom:10px;font-weight:bold;">Prosedur Stok Opname</div>
    <ol>
        <li>Semua barang dihitung sesuai dengan kondisi aktual stok berada.</li>
        <li>Barang yang tidak memiliki selisih atau sesuai dengan keberadaan stok aktual maka diberikan tanda “-“ (minus) pada stok kolom selisih.</li>
        <li>Barang yang memiliki selisih dengan stok yang diberikan maka diberikan catatan sesuai dengan jumlah aktual barang di outlet berlaku</li>
        <li>
            Barang yang memiliki jumlah selisih di dalam data stok dan stok POS maka di cek secara real-time/langsung dengan data POS untuk proses penghitungan ulang,
            <ol style="list-style-type: lower-alpha; margin-left: 20px;">
                <li>Jika barang memiliki selisih melebihi dari stok POS maka berikan nilai selisih dengan “+” (plus) dengan nilai selisih yang berlaku,</li>
                <li>Jika barang memiliki selisih kurang dari dari stok POS maka berikan nilai selisih dengan “-” (minus) dengan nilai selisih yang berlaku.</li>
            </ol>
        </li>
        <li>Data akan dikerjakan dengan waktu maksimal 1x24 jam untuk menjaga stok tepat dan akurat.</li>
        <li>Data selisih barang akan dibuatkan nilai total sebagai input barang selisih melalui excel dengan format .xls</li>
        <li>Data selisih barang harus di-input oleh tim logistik yang berlaku.</li>
        <li>Jika masih ada selisih yang terjadi maka akan menjadi tugas di proses stok opname yang selanjutnya.</li>
    </ol>

    <div class="page-break"></div>

    <!-- Approval Page -->
    <div style="margin-bottom:20px;font-weight:bold;">Pernyataan Hasil Stok Opname - Tim Logistik & Tim Retail</div>
    <div>Dengan ini dinyatakan bahwa proses stok opname yang dilakukan pada:</div>
    <table>
        <tr>
            <td style="width: 150px;border-collapse: collapse;">Waktu Ekspor</td>
            <td style="width: 10px;border-collapse: collapse;">:</td>
            <td style="border-collapse: collapse;"><?= $timeapproval ?></td>
        </tr>
        <tr>
            <td style="width: 150px;border-collapse: collapse;">Tanggal</td>
            <td style="width: 10px;border-collapse: collapse;">:</td>
            <td style="border-collapse: collapse;"><?= $dateapproval ?></td>
        </tr>
        <tr>
            <td style="width: 150px;border-collapse: collapse;">Lokasi</td>
            <td style="width: 10px;border-collapse: collapse;">:</td>
            <td style="border-collapse: collapse;"><?= $outlet ?></td>
        </tr>
        <tr>
            <td style="width: 150px;border-collapse: collapse;">Waktu Selesai</td>
            <td style="width: 10px;border-collapse: collapse;">:</td>
            <td style="border-collapse: collapse;"></td>
        </tr>
    </table>
    <div>Telah selesai dilaksanakan dengan seksama sesuai dengan prosedur yang berlaku. Hasil dari stok opname menunjukkan bahwa seluruh data fisik barang yang ada telah diperiksa dan selaras dengan catatan administrasi.</div>
    <div>Menyatakan bahwa:</div>
    <ol>
        <li>Tidak terdapat selisih antara stok fisik dan stok administrasi.</li>
        <li>Seluruh barang telah dihitung secara akurat dan dikonfirmasi keabsahannya.</li>
        <li>Data hasil stok opname telah diverifikasi dan disepakati bersama oleh pihak yang bertanggung jawab.</li>
        <li>Jika terdapat satu saja transaksi yang terjadi sewaktu stok opname maka pengecekan barang dilakukan via POS.</li>
        <li>Semua rekonsiliasi keuangan telah disesuaikan dari tagihan yang ada di dashboard office.58vapehouse.com.</li>
    </ol>
    <div>Demikian pernyataan ini dibuat dengan sebenar-benarnya untuk menjadi arsip dan rujukan dalam pengelolaan inventaris.</div>
    <table style="width:100%; margin-top:50px; text-align:center; border:0; border-collapse:collapse;">
        <tr>
            <td style="width:40%; text-align:left; border:1px solid black; padding:10px;">
                Diperiksa oleh,<br><br><br><br>
                Frontline
            </td>

            <td style="width:20%; padding:10px;">
                <br>
                
            </td>

            <td style="width:40%; text-align:left; border:1px solid black; padding:10px;">
                Diperiksa oleh,<br><br><br><br>
                Frontline
            </td>
        </tr>

        <tr>
            <td> </td>
        </tr>

        <tr>
            <td> </td>
        </tr>

        <tr>
            <td> </td>
        </tr>

        <tr>
            <td style="width:40%; padding:10px;">
                <br>
                
            </td>

            <td style="width:20%; padding:10px;">
                <br>
                
            </td>
            
            <td style="width:40%; text-align:left; padding-top:50px; border:1px solid black; padding:10px;">
                Disetujui oleh,<br><br><br><br>
                Headstore
            </td>
        </tr>
    </table>
</body>

</html>