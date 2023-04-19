<?= $this->extend('layout') ?>
<?= $this->section('main') ?>

<!-- Page Heading -->
<div class="tm-card-header">
  <div uk-grid class="uk-width-1-1@m uk-flex-middle">
    <div class="uk-width-1-2@m">
      <h3 class="tm-h3"><?=lang('Global.employeeList')?></h3>
    </div>

    <!-- Button Trigger Modal Add -->
    <div class="uk-width-1-2@m uk-flex uk-flex uk-flex-right uk-text-left">
      <button type="button" class="uk-button uk-button-primary" uk-toggle="target: #tambahdata"><?=lang('Global.addEmployee')?></button>
    </div>
    <!-- End Of Button Trigger Modal Add -->

    <?= view('Views/Auth/_message_block') ?>

    <!-- Modal Add -->
    <div uk-modal class="uk-flex-top" id="tambahdata">
      <div class="uk-modal-dialog uk-margin-auto-vertical">
        <div class="uk-modal-content">
          <div class="uk-modal-header">
            <h5 class="uk-modal-title" id="tambahdata" ><?=lang('Global.addEmployee')?></h5>
          </div>
          <div class="uk-modal-body">
            <form class="uk-form-stacked" role="form" action="/user/create" method="post">
              <?= csrf_field() ?>

              <div class="uk-margin-bottom">
                <label class="uk-form-label" for="username"><?=lang('Auth.username')?></label>
                <div class="uk-form-controls">
                  <input type="text" class="uk-input <?php if (session('errors.username')) : ?>tm-form-invalid<?php endif ?>" id="username" name="username" placeholder="<?=lang('Auth.username')?>" autofocus required />
                </div>
              </div>

              <div class="uk-margin">
                <label class="uk-form-label" for="email"><?=lang('Auth.email')?></label>
                <div class="uk-form-controls">
                  <input type="email" name="email" id="email" placeholder="<?=lang('Auth.email')?>" required class="uk-input <?php if (session('errors.email')) : ?>tm-form-invalid<?php endif ?>"/>
                </div>
              </div>

              <div class="uk-margin-bottom">
                <label class="uk-form-label" for="firstname"><?=lang('Global.firstname')?></label>
                <div class="uk-form-controls">
                  <input type="text" class="uk-input <?php if (session('errors.firstname')) : ?>tm-form-invalid<?php endif ?>" id="firstname" name="firstname" placeholder="<?=lang('Global.firstname')?>" autofocus required />
                </div>
              </div>

              <div class="uk-margin-bottom">
                <label class="uk-form-label" for="lastname"><?=lang('Global.lastname')?></label>
                <div class="uk-form-controls">
                  <input type="text" class="uk-input <?php if (session('errors.lastname')) : ?>tm-form-invalid<?php endif ?>" id="lastname" name="lastname" placeholder="<?=lang('Global.lastname')?>" autofocus required />
                </div>
              </div>

              <div class="uk-margin">
                <label class="uk-form-label" for="phone"><?=lang('Global.phone')?></label>
                <div class="uk-form-controls">
                  <input type="phone" name="phone" id="phone" placeholder="<?=lang('Global.phone')?>" class="uk-input <?php if (session('errors.phone')) : ?>tm-form-invalid<?php endif ?>"/>
                </div>
              </div>

              <div class="uk-margin">
                <label class="uk-form-label" for="password"><?=lang('Auth.password')?></label>
                <div class="uk-form-controls">
                  <input type="password" name="password" id="password" required class="uk-input <?php if (session('errors.password')) : ?>tm-form-invalid<?php endif ?>" />
                </div>
              </div>

              <div class="uk-margin">
                <label class="uk-form-label" for="pass_confirm"><?=lang('Auth.repeatPassword')?></label>
                <div class="uk-form-controls">
                  <input type="password" name="pass_confirm" id="pass_confirm" required class="uk-input <?php if (session('errors.repeatPassword')) : ?>tm-form-invalid<?php endif ?>" />
                </div>
              </div>

              <div class="uk-margin">
                <label class="uk-form-label" for="role"><?=lang('Global.accessLevel')?></label>
                <div class="uk-form-controls">
                  <select class="uk-select" name="role" required>
                    <option>Role</option>
                    <?php
                    foreach ($roles as $role) {
                      if ($role->name != 'guests') {
                        if ($authorize->inGroup('owner', $uid) === true) {
                          if ($role->name != 'owner') {
                            echo '<option value="'.$role->id.'">'.$role->name.'</option>';
                          }
                        } elseif ($authorize->inGroup('supervisor', $uid) === true) {
                          if (($role->name != 'owner') && ($role->name != 'supervisor')) {
                            echo '<option value="'.$role->id.'">'.$role->name.'</option>';
                          }
                        }                   
                      }
                    }
                    ?>
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
        <th class="uk-text-center"><?=lang('Global.name')?></th>
        <th class="uk-text-center"><?=lang('Global.phone')?></th>
        <th class="uk-text-center"><?=lang('Global.accessLevel')?></th>
        <th class="uk-text-center"><?=lang('Global.action')?></th>
      </tr>
    </thead>
    <tbody>
      <?php $i = 1 ; ?>
      <?php foreach ($users as $user) : ?>
        <tr>
          <td class="uk-text-center"><?= $i++; ?></td>
          <td class="uk-text-center"><?= $user->firstname.' '.$user->lastname; ?></td>
          <td class="uk-text-center"><?= $user->phone; ?></td>
          <td class="uk-text-center"><?= $user->role; ?></td>
          <td class="uk-text-center">
            <!-- Button Trigger Modal Edit -->
            <button type="button" class="uk-button uk-button-primary" uk-toggle="target: #editdata<?= $user->id ?>"><?=lang('Global.edit')?></button>
            <a class="uk-button uk-button-default uk-button-danger" href="user/delete/<?= $user->id ?>"><?=lang('Global.delete')?></a>
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
    <div uk-modal class="uk-flex-top" id="editdata<?= $user->id ?>">
      <div class="uk-modal-dialog uk-margin-auto-vertical">
        <div class="uk-modal-content">
          <div class="uk-modal-header">
            <h5 class="uk-modal-title" id="editdata">Perbaharui Data</h5>
          </div>

          <div class="uk-modal-body">
            <form class="uk-form-stacked" role="form" action="user/update/<?= $user->id ?>" method="post">
              <?= csrf_field() ?>
              <input type="hidden" name="id" value="<?= $user->id; ?>">
              <input type="hidden" name="group_id" value="<?= $user->group_id; ?>">

              <div class="uk-margin-bottom">
                <label class="uk-form-label" for="username"><?=lang('Auth.username')?></label>
                <div class="uk-form-controls">
                  <input type="text" class="uk-input" id="username" name="username" placeholder="<?= $user->username; ?>" autofocus />
                </div>
              </div>

              <div class="uk-margin-bottom">
                <label class="uk-form-label" for="firstname"><?=lang('Global.firstname')?></label>
                <div class="uk-form-controls">
                  <input type="text" class="uk-input" id="firstname" name="firstname" placeholder="<?= $user->username; ?>" autofocus />
                </div>
              </div>

              <div class="uk-margin-bottom">
                <label class="uk-form-label" for="username"><?=lang('Global.lastname')?></label>
                <div class="uk-form-controls">
                  <input type="text" class="uk-input" id="lastname" name="lastname" placeholder="<?= $user->username; ?>" autofocus />
                </div>
              </div>

              <div class="uk-margin">
                <label class="uk-form-label" for="email"><?=lang('Auth.email')?></label>
                <div class="uk-form-controls">
                  <input type="email" class="uk-input" id="email" name="email" placeholder="<?= $user->email; ?>" />
                </div>
              </div>
              
              <div class="uk-margin">
                <label class="uk-form-label" for="phone"><?=lang('Global.phone')?></label>
                <div class="uk-form-controls">
                  <input type="text" class="uk-input" name="phone" id="phone" placeholder="<?= $user->phone; ?>"/>
                </div>
              </div>

              <div class="uk-margin">
                <label class="uk-form-label" for="role"><?=lang('Global.accessLevel')?></label>
                <div class="uk-form-controls">
                  <select class="uk-select" name="role">
                    <option disabled>Role</option>
                    <?php
                    foreach ($roles as $role) {
                      if ($role->name != 'guests') {
                        if ($authorize->inGroup('owner', $uid) === true) {
                          if ($role->name != 'owner') {
                            echo '<option value="'.$role->id.'">'.$role->name.'</option>';
                          }
                        } elseif ($authorize->inGroup('supervisor', $uid) === true) {
                          if (($role->name != 'owner') && ($role->name != 'supervisor')) {
                            echo '<option value="'.$role->id.'">'.$role->name.'</option>';
                          }
                        }                   
                      }
                    }
                    ?>
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