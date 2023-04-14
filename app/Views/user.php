<?= $this->extend('layout') ?>
<?= $this->section('main') ?>

<!-- Page Heading -->
<div class="tm-card-header">
  <div uk-grid class="uk-width-1-1@m uk-flex-middle">
    <div class="uk-width-1-2@m">
      <h3 class="tm-h3">Pegawai</h3>
    </div>

    <!-- Button Trigger Modal Add -->
    <div class="uk-width-1-2@m uk-flex uk-flex uk-flex-right uk-text-left">
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
</div>
<!-- End Of Page Heading -->

<!-- Table Of Content -->
<div class="uk-overflow-auto">
  <table class="uk-table uk-table-striped uk-table-hover uk-table-responsive uk-table-justify uk-table-middle uk-table-divider">
    <thead>
      <tr>
        <th class="uk-text-center">No</th>
        <th class="uk-text-center">Nama</th>
        <th class="uk-text-center">Hak Akses</th>
        <th class="uk-text-center">Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php $i = 1 ; ?>
      <?php foreach ($users as $user) : ?>
        <tr>
          <td class="uk-text-center"><?= $i++; ?></td>
          <td class="uk-text-center"><?= $user->username; ?></td>
          <td class="uk-text-center"><?= $user->name; ?></td>
          <td class="uk-text-center">
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
    <div uk-modal class="uk-flex-top" id="editdata<?= $user->userid ?>">
      <div class="uk-modal-dialog uk-margin-auto-vertical">
        <div class="uk-modal-content">
          <div class="uk-modal-header">
            <h5 class="uk-modal-title" id="editdata">Perbaharui Data</h5>
          </div>

          <div class="uk-modal-body">
            <form class="uk-form-stacked" role="form" action="<? base_url('/user/update/)'.$user->userid); ?>" method="post">
              <?= csrf_field() ?>
              <input type="hidden" name="id" value="<?= $user->userid; ?>">
              <input type="hidden" name="group_id" value="<?= $user->group_id; ?>">

              <div class="uk-margin-bottom">
                <label class="uk-form-label" for="username">Nama</label>
                <div class="uk-form-controls">
                  <input type="text" class="uk-input" id="username" name="username" value="<?= $user->username; ?>" autofocus required />
                </div>
              </div>

              <div class="uk-margin">
                <label class="uk-form-label" for="email">Email</label>
                <div class="uk-form-controls">
                  <input type="email" class="uk-input" id="email" name="email" value="<?= $user->email; ?>" required />
                </div>
              </div>
              
              <div class="uk-margin">
                <label class="uk-form-label" for="no_hp">Nomer HP</label>
                <div class="uk-form-controls">
                  <input type="text" class="uk-input" name="no_hp" id="no_hp" value=""/>
                </div>
              </div>

              <div class="uk-margin">
                <label class="uk-form-label" for="password">Password</label>
                <div class="uk-form-controls">
                  <input type="password" class="uk-input" name="password" id="password" />
                </div>
              </div>

              <div class="uk-margin">
                <label class="uk-form-label" for="role">Hak Akses</label>
                <div class="uk-form-controls">
                  <select class="uk-select" name="role">
                    <option disabled>Role</option>
                      <?php foreach ($roles as $role) { ?>
                        <option value="<?= $role->id; ?>" <?php if ($user->group_id === $role->id) {echo 'selected';} ?>><?= $role->name; ?></option>
                      <?php } ?>
                  </select>
                </div>
              </div>
              <hr>

              <div class="uk-margin">
                <button type="submit" class="uk-button uk-button-primary">Perbaharui</button>
              </div>

            </form>
          </div>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
  <!-- End Of Modal Edit -->
</div>
<!-- End Of Table Content -->

<?= $this->endSection() ?>