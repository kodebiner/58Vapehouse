<?php if (session()->has('message')) : ?>
	<div class="uk-alert-success uk-margin" uk-alert>
        <a class="uk-alert-close" uk-close></a>
		<?= session('message') ?>
	</div>
<?php endif ?>

<?php if (session()->has('error')) : ?>
	<div class="uk-alert-danger uk-margin" uk-alert>
        <a class="uk-alert-close" uk-close></a>
		<?= session('error') ?>
	</div>
<?php endif ?>

<?php if (session()->has('errors')) : ?>
    <div class="uk-alert-danger uk-margin" uk-alert>
        <ul class="uk-list uk-list-disc">
        <?php foreach (session('errors') as $error) : ?>
            <li><?= $error ?></li>
        <?php endforeach ?>
        </ul>
    </div>
<?php endif ?>
