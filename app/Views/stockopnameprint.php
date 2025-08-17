<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stok Opname</title>
    <!-- <title>Stok Opname </?= $stockopname['outlet'] ?></title> -->
    <link rel="stylesheet" href="css/theme.css">
    <script src="js/uikit.min.js"></script>
    <script src="js/uikit-icons.min.js"></script>
    <!-- <style>
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
    </style> -->
    <style>
        html { font-size: 9pt; }
        body { margin: 0; font-family: Arial, sans-serif; }

        .page {
            margin: 100px 40px;
        }

        header {
            position: fixed;
            top: 80px;
            left: 0;
            right: 0;
            height: 50px;
            text-align: center;
            font-size: 12px;
        }

        footer {
            position: fixed;
            bottom: 60px;
            left: 0;
            right: 0;
            height: 40px;
            text-align: center;
            font-size: 10px;
        }

        table, th, td {
            border-collapse: collapse;
            padding: 4px;
            font-size: 9pt;
        }

        th, td {
            border: 1px solid black;
        }

        .page-break { page-break-before: always; }
        .page { page-break-inside: avoid; }
    </style>
</head>

<body>
    <header>
        <h3>Data Stok Opname</h3>
        <!-- <h3>Data Stok Opname - </?= $outletcode ?> - </?= $dateexport ?></h3> -->
    </header>

    <main>
        <!-- Data Stock Page -->
        <div class="page">
            <table style="width:100%">
                <tr>
                    <th>Nama</th>
                    <th>Kategori</th>
                    <th>Stok</th>
                    <th>Umur Produk</th>
                    <th>Selisih</th>
                </tr>
                <!-- </?php foreach ($stockopnames as $stockopname): ?>
                    <tr>
                        <td></?= $stockopname['product'] ?></td>
                        <td></?= $stockopname['category'] ?></td>
                        <td></?= $stockopname['stock'] ?></td>
                        <td></?= $stockopname['productage'] ?></td>
                        <td></td>
                    </tr>
                </?php endforeach ?> -->
            </table>
        </div>

        <div class="page-break"></div>

        <!-- Notes Page -->
        <div class="page">
            <h3 style="margin-bottom:80px">Catatan Stok Opname</h3>
        </div>

        <div class="page-break"></div>

        <!-- SOP Page -->
        <div class="page">
            <h3 style="margin-bottom:10px">Prosedur Stok Opname</h3>
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
        </div>

        <div class="page-break"></div>

        <!-- Approval Page -->
        <div class="page">
            <h3 style="margin-bottom:100px">Pernyataan Hasil Stok Opname - Tim Logistik & Tim Retail</h3>
            <h4>Dengan ini dinyatakan bahwa proses stok opname yang dilakukan pada:</h4>
            <h4>Waktu Eksport   :   </h4>
            <h4>Tanggal         :   </h4>
            <h4>Lokasi          :   </h4>
            <!-- <h4>Waktu Eksport   :   </?= $timeapproval ?></h4>
            <h4>Tanggal         :   </?= $dateapproval ?></h4>
            <h4>Lokasi          :   </?= $outlet ?></h4> -->
            <h4>Waktu Selesai   :   </h4>
            <h4>Telah selesai dilaksanakan dengan seksama sesuai dengan prosedur yang berlaku. Hasil dari stok opname menunjukkan bahwa seluruh data fisik barang yang ada telah diperiksa dan selaras dengan catatan administrasi.</h4>
            <h4>Menyatakan bahwa:</h4>
            <ol>
                <li>Tidak terdapat selisih antara stok fisik dan stok administrasi.</li>
                <li>Seluruh barang telah dihitung secara akurat dan dikonfirmasi keabsahannya.</li>
                <li>Data hasil stok opname telah diverifikasi dan disepakati bersama oleh pihak yang bertanggung jawab.</li>
                <li>Jika terdapat satu saja transaksi yang terjadi sewaktu stok opname maka pengecekan barang dilakukan via POS.</li>
                <li>Semua rekonsiliasi keuangan telah disesuaikan dari tagihan yang ada di dashboard office.58vapehouse.com.</li>
            </ol>
            <h4>Demikian pernyataan ini dibuat dengan sebenar-benarnya untuk menjadi arsip dan rujukan dalam pengelolaan inventaris.</h4>
            <div class="uk-child-width-1-2" uk-grid>
                <div style="margin-top: 50px; border: 1px solid black;">
                    <h3 style="margin-bottom: 50px;">Diperiksa Oleh</h3>
                    <h3>Frontline</h3>
                </div>
                <div style="margin-top: 50px; border: 1px solid black;">
                    <h3 style="margin-bottom: 50px;">Diperiksa Oleh</h3>
                    <h3>Frontline</h3>
                </div>
            </div>
            <div style="margin-top: 50px; border: 1px solid black;">
                <h3 style="margin-bottom: 50px;">Disetujui Oleh</h3>
                <h3>Headstore</h3>
            </div>
        </div>
    </main>

    <footer>
        <h3>Page 1 of 10</h3>
        <!-- <h3>Page </?= $currentpage ?> of </?= $totalpage ?></h3> -->
    </footer>
</body>

</html>