<?php if (session()->has('error')) : ?>
	<div class="uk-card uk-card-default uk-card-body">
		<p class="uk-h2 uk-text-danger"><?= session('error') ?></p>
	</div>
<?php endif ?>