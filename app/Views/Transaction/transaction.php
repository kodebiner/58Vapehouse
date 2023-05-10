<?= $this->extend('Transaction/layout') ?>
<?= $this->section('main') ?>

    <div class="uk-child-width-1-2 uk-child-width-1-5@m" uk-grid uk-height-match="target: > div > .uk-card > .uk-card-header">
        <?php foreach ($variants as $variant) : ?>
            <?php
                foreach ($products as $product) {
                    if ($product['id'] === $variant['productid']) {
                        $productName = $product['name'];
                        $productPhoto = $product['photo'];
                    }
                }
            ?>
            <div>
                <div class="uk-card uk-card-hover uk-card-default">
                    <div class="uk-card-header">
                        <div class="tm-h1 uk-text-bolder uk-text-center"><?= $productName.' - '. $variant['name'] ?></div>
                    </div>
                    <div class="uk-card-body">
                        <div class=""><?= $productPhoto ?></div>
                    </div>
                    <div class="uk-card-footer">
                        <div class="tm-h3 uk-text-center">
                            <div>Rp <?= $variant['hargamodal'] + $variant['hargajual'] ?>,-</div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <form name='biodata' method='post' action='tutor.html'>
            <pre>
                Nim     : <input type='number' name='nim'>
                Nama    : <input type='text' name='nama' >
                Agama   : <select name='agama'>
                    <option>Islam
                    <option>Hindu
                    <option>Budha
                    <option>Kristen             
                    <option>Konghucu       
                    </select>
            </pre>
                <input type='button' onClick='terimainput()' value='Simpan'>
                <input type='reset' value='Ulangi'>
        </form>

        <table id='tabelinput'>
            <tr>
                <td>NIM</td>
                <td>NAMA</td>
                <td>AGAMA</td>
            </tr>
        </table>
        <script>
            function terimainput(){
                var x=document.forms['biodata']['nim'].value;
                var y=document.forms['biodata']['nama'].value;
                var z=document.forms['biodata']['agama'].value;
                        
                                                        
                var tabel = document.getElementById("tabelinput");
                var row = tabel.insertRow(1);
                var cell1 = row.insertCell(0);
                var cell2 = row.insertCell(1);
                var cell3 = row.insertCell(2);
                        
                cell1.innerHTML = x;
                cell2.innerHTML = y;
                cell3.innerHTML = z;
            }
        </script>
    </div>
<?= $this->endSection() ?>