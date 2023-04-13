<?= $this->extend('layout') ?>
<?= $this->section('main') ?>

<!-- Page Heading -->
<div uk-grid class="uk-width-1-1@m">
  <div class="uk-width-1-2@m">
    <h3 class="">Pegawai</h3>
  </div>

  <!-- Button Trigger Modal Add -->
  <div class="uk-width-1-2@m uk-flex uk-flex-right uk-text-left">
    <button type="button" class="uk-button uk-button-primary" uk-toggle="target: #tambahdata">Tambah Pegawai</button>
  </div>
  <!-- End Of Button Trigger Modal Add -->

  <!-- Modal Add -->
  <div uk-modal class="uk-flex-top" id="tambahdata">
    <div class="uk-modal-dialog uk-margin-auto-vertical">
      <div class="uk-modal-content">
        <div class="uk-modal-header">
          <h5 class="uk-modal-title" id="tambahdata" >Tambah Pegawai</h5>
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
              <label class="uk-form-label" for="role">Hak Akses</label>
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
  <!-- End Of Modal Add -->

</div>
<!-- End Of Page Heading -->

<!-- Table Of Content -->
<div class="uk-overflow-auto">
  <table class="uk-table uk-table-justify uk-table-middle uk-table-divider">
    <thead>
      <tr>
        <th>No</th>
        <th>Nama</th>
        <th>Hak Akses</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php $i = 1 ; ?>
      <?php foreach ($users as $user) : ?>
        <tr>
          <td><?= $i++; ?></td>
          <td><?= $user->username; ?></td>
          <td><?= $user->name; ?></td>
          <td>
            <!-- Button Trigger Modal Edit -->
            <button type="button" class="uk-button uk-button-primary" uk-toggle="target: #editdata<?= $user->userid ?>">Ubah</button>
            <!-- End Of Button Trigger Modal Edit -->

            <!-- Button Delete -->

            <!-- End Of Button Delete -->
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <!-- Modal Edit -->
  <?php foreach ($users as $user) : ?>
    <div class="uk-flex-top" id="editdata<?= $user->userid ?>" uk-modal>
      <div class="uk-modal-dialog uk-modal-body"></div>
    </div>
  <?php endforeach; ?>
  <!-- End Of Modal Edit -->
</div>
<!-- End Of Table Content -->

<?= $this->endSection() ?>