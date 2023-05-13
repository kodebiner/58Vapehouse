<?= $this->extend('layout') ?>
<?= $this->section('main') ?>

<!-- Page Heading -->
<div class="tm-card-header uk-light">
  <?= view('Views/Auth/_message_block') ?>

  <div uk-grid class="uk-flex-middle">
    <div class="uk-width-1-2@m">
      <h3 class="tm-h3"><?=lang('Global.employeeList')?></h3>
    </div>

    <!-- Button Trigger Modal Add -->
    <div class="uk-width-1-2@m uk-text-right@m">
      <button type="button" class="uk-button uk-button-primary uk-preserve-color" uk-toggle="target: #tambahdata"><?=lang('Global.addEmployee')?></button>
    </div>
    <!-- End Of Button Trigger Modal Add -->

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
                <button type="submit" class="uk-button uk-button-primary"><?=lang('Global.save')?></button>
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
<!-- Search Box -->
<div class="uk-margin">
  <form class="uk-search uk-search-default">
    <span uk-search-icon></span>
    <input class="uk-search-input" id="inputUser" onkeyup="searchUser()" type="text" placeholder="Search" aria-label="Search">
  </form>
</div>
<!-- Search Box End -->

<?php if (in_groups('owner')) : ?>
<div class="uk-overflow-auto">
  <table class="uk-table uk-table-justify uk-table-middle uk-table-divider uk-light" id="tableUser">
    <thead>
      <tr>
        <th class="uk-text-center uk-width-small">No</th>
        <th class="uk-width-large"><?=lang('Global.name')?></th>
        <th class="uk-width-medium"><?=lang('Global.phone')?></th>
        <th class="uk-width-medium"><?=lang('Global.accessLevel')?></th>
        <th class="uk-text-center uk-width-large"><?=lang('Global.action')?></th>
      </tr>
    </thead>
    <tbody>
      <?php $i = 1 ; ?>
      <?php foreach ($users as $user) : ?>
        <tr>
          <td class="uk-text-center"><?= $i++; ?></td>
          <td class=""><?= $user->firstname.' '.$user->lastname; ?></td>
          <td class=""><?= $user->phone; ?></td>
          <td class=""><?= $user->role; ?></td>
          <td class="uk-child-width-auto uk-flex-center uk-grid-row-small uk-grid-column-small" uk-grid>
            <!-- Button Trigger Modal Edit -->
            <div>
              <button type="button" class="uk-button uk-button-primary uk-preserve-color" uk-toggle="target: #editdata<?= $user->id ?>"><?=lang('Global.edit')?></button>
            </div>
            <!-- End Of Button Trigger Modal Edit -->

            <!-- Button Delete -->
            <div>
              <a class="uk-button uk-button-default uk-button-danger uk-preserve-color" href="user/delete/<?= $user->id ?>" onclick="return confirm('<?=lang('Global.deleteConfirm')?>')"><?=lang('Global.delete')?></a>
            </div>
            <!-- End Of Button Delete -->
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <!-- Table Pagination -->
  <ul class="uk-pagination uk-flex-right uk-margin-medium-top uk-light" uk-margin>
    <li><a href="#"><span uk-pagination-previous></span></a></li>
    <li><a href="#">1</a></li>
    <li class="uk-disabled"><span>…</span></li>
    <li><a href="#">4</a></li>
    <li><a href="#">5</a></li>
    <li><a href="#">6</a></li>
    <li><a href="#">7</a></li>
    <li><a href="#">8</a></li>
    <li><a href="#">9</a></li>
    <li><a href="#">10</a></li>
    <li class="uk-disabled"><span>…</span></li>
    <li><a href="#">20</a></li>
    <li><a href="#"><span uk-pagination-next></span></a></li>
  </ul>
  <!-- Table Pagination End-->

  <!-- Modal Edit -->
  <?php foreach ($users as $user) : ?>
    <div uk-modal class="uk-flex-top" id="editdata<?= $user->id ?>">
      <div class="uk-modal-dialog uk-margin-auto-vertical">
        <div class="uk-modal-content">
          <div class="uk-modal-header">
            <h5 class="uk-modal-title" id="editdata"><?=lang('Global.updateData')?></h5>
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
                  <input type="text" class="uk-input" id="firstname" name="firstname" placeholder="<?= $user->firstname; ?>" autofocus />
                </div>
              </div>

              <div class="uk-margin-bottom">
                <label class="uk-form-label" for="username"><?=lang('Global.lastname')?></label>
                <div class="uk-form-controls">
                  <input type="text" class="uk-input" id="lastname" name="lastname" placeholder="<?= $user->lastname; ?>" autofocus />
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
                <button class="uk-button uk-button-default" type="button" uk-toggle="target: .reset-password"><?=lang('Auth.resetPassword')?></button>
              </div>
              
              <div class="uk-margin reset-password" hidden>
                <label class="uk-form-label" for="password"><?=lang('Auth.password')?></label>
                <div class="uk-form-controls">
                  <input type="password" class="uk-input" name="password" id="password" placeholder="<?=lang('Auth.password')?>" autocomplete="off"/>
                </div>
              </div>
              
              <div class="uk-margin reset-password" hidden>
                <label class="uk-form-label" for="pass_confirm"><?=lang('Auth.repeatPassword')?></label>
                <div class="uk-form-controls">
                  <input type="password" class="uk-input" name="pass_confirm" id="pass_confirm" placeholder="<?=lang('Auth.repeatPassword')?>" autocomplete="off"/>
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
                <button type="submit" class="uk-button uk-button-primary"><?=lang('Global.save')?></button>
              </div>

            </form>
          </div>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
  <!-- End Of Modal Edit -->
</div>
<?php endif ?>
<!-- End Of Table Content -->

<!-- Search Engine Script -->
<script>
  function searchUser() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("inputUser");
    filter = input.value.toUpperCase();
    table = document.getElementById("tableUser");
    tr = table.getElementsByTagName("tr");
    for (i = 0; i < tr.length; i++) {
      td = tr[i].getElementsByTagName("td")[1];
      if (td) {
        txtValue = td.textContent || td.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
          tr[i].style.display = "";
        } else {
          tr[i].style.display = "none";
        }
      }       
    }
  }
</script>
<!-- Search Engine Script End -->

<?= $this->endSection() ?>