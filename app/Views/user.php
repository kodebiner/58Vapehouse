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
          <!-- Button trigger modal -->
          <div class="uk-text-right@s uk-text-left">
              <button type="button" class="uk-button uk-button-primary" uk-toggle="target: #tambahdata">Tambah Data</button>
          </div>

          <!-- Modal -->
          <div uk-modal class="uk-flex-top" id="tambahdata">
              <div class="uk-modal-dialog uk-margin-auto-vertical">
                  <div class="uk-modal-content">
                      <div class="uk-modal-header">
                          <h5 class="uk-modal-title" id="tambahdata" >Tambah data</h5>
                      </div>
                      <div class="uk-modal-body">
                          <form class="uk-form-stacked" role="form" action="/user/create" method="post">
                              <?= csrf_field() ?>
                              <div class="uk-margin-bottom">
                                  <label class="uk-form-label" for="username">Nama</label>
                                  <div class="uk-form-controls">
                                      <input type="text" class="uk-input" id="username" name="username" autofocus required />
                                  </div>
                              </div>

                              <div class="uk-margin">
                                  <label class="uk-form-label" for="email">Email</label>
                                  <div class="uk-form-controls">
                                      <input type="email" name="email" id="email" required class="uk-input"/>
                                  </div>
                              </div>

                              <div class="uk-margin">
                                  <label class="uk-form-label" for="no_hp">Nomer HP</label>
                                  <div class="uk-form-controls">
                                      <input type="phone" name="no_hp" id="no_hp" class="uk-input"/>
                                  </div>
                              </div>

                              <div class="uk-margin">
                                  <label class="uk-form-label" for="password">Password</label>
                                  <div class="uk-form-controls">
                                      <input type="password" name="password" id="password" required class="uk-input" />
                                  </div>
                              </div>

                              <div class="uk-margin">
                                  <label class="uk-form-label" for="role">Posisi</label>
                                  <div class="uk-form-controls">
                                      <select class="uk-select" name="role" required>
                                          <option>Role</option>
                                          <?php foreach ($roles as $role) { ?>
                                              <option value="<?= $role->id; ?>"><?= $role->name; ?></option>
                                          <?php } ?>
                                      </select>
                                  </div>
                              </div>

                              <hr>

                              <div class="uk-margin">
                                  <button type="submit" class="uk-button uk-button-primary">Tambah</button>
                              </div>
                          </form>
                      </div>
                  </div>
              </div>
          </div>
          <!-- akhir modal tambah -->


      <?php endforeach; ?>
  </body>
</html>

<?= $this->endSection() ?>