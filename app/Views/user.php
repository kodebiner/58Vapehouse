<?= $this->extend('layout') ?>
<?= $this->section('main') ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
  <body>
    <?php $i = 1 ; ?>
      <?php foreach ($users as $user) : ?>
        <table>
          <thead>
              <tr>
                  <th>no</th>
                  <th>Nama</th>
                  <th>Email</th>
                  <th>Posisi</th>
                  <th>No HP</th>
                  <th>Photo</th>
                  <th>Aksi</th>
              </tr>
              <tr>
                  <td><?php $i++; ?></td>
                  <td><?= $user->username; ?></td>
                  <td><?= $user->email; ?></td>
                  <td><?= $user->name; ?></td>
                  <td>08978323791</td>
                  <td>
                    <a href=<?='user/edit'.$user->userid; ?>>Ubah</a>
                  </td>
              </tr>
          </thead>
        </table>  
        <button href=<?='user/create'.$user->userid; ?>>Tambah</button>
      <?php endforeach; ?>
  </body>
</html>

<?= $this->endSection() ?>