<?php
    $pager->setSurroundCount(2);
?>

<nav class="uk-overlay uk-padding-remove-vertical" aria-label="<?= lang('Pager.pageNavigation') ?>">
    <ul class="uk-pagination uk-flex-right tm-h2 uk-h4 uk-margin-remove" uk-margin>
        <?php if ($pager->hasPreviousPage()) : ?>
            <li>
                <a href="<?= $pager->getPreviousPage() ?>" aria-label="<?= lang('Pager.previous') ?>">
                    <span uk-pagination-previous aria-hidden="true"></span>
                </a>
            </li>
            <?php if ($pager->hasPrevious()) { ?>
                <li>
                    <a href="<?= $pager->getFirst() ?>" aria-label="<?= lang('Pager.first') ?>">
                        <span aria-hidden="true">1</span>
                    </a>
                </li>
                <li class="uk-disabled">
                    <span>…</span>
                </li>
            <?php } ?>
        <?php endif ?>

        <?php foreach ($pager->links() as $link) : ?>
            <li <?= $link['active'] ? 'class="uk-active"' : '' ?>>
                <a href="<?= $link['uri'] ?>">
                    <?= $link['title'] ?>
                </a>
            </li>
        <?php endforeach ?>

        <?php if ($pager->hasNextPage()) : ?>
            <?php if ($pager->hasNext()) { ?>
                <li class="uk-disabled">
                    <span>…</span>
                </li>
                <li>
                    <a href="<?= $pager->getLast() ?>" aria-label="<?= lang('Pager.next') ?>">
                        <span aria-hidden="true"><?= $pager->getPageCount() ?></span>
                    </a>
                </li>
            <?php } ?>
            <li>
                <a href="<?= $pager->getNextPage() ?>" aria-label="<?= lang('Pager.last') ?>">
                    <span uk-pagination-next aria-hidden="true"></span>
                </a>
            </li>
        <?php endif ?>
    </ul>
</nav>