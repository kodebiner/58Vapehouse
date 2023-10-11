<?php $pager->setSurroundCount(2); ?>

<nav class="uk-overlay" aria-label="<?= lang('Pager.pageNavigation') ?>">
    <ul class="uk-pagination uk-flex-center tm-h2 uk-h4 uk-margin-remove" uk-margin>
        <?php if ($pager->hasPrevious()) : ?>
            <li>
                <a href="<?= $pager->getFirst() ?>" aria-label="<?= lang('Pager.first') ?>">
                    <span uk-icon="chevron-double-left" aria-hidden="true"></span>
                </a>
            </li>
            <li>
                <a href="<?= $pager->getPrevious() ?>" aria-label="<?= lang('Pager.previous') ?>">
                    <span uk-pagination-previous aria-hidden="true"></span>
                </a>
            </li>
        <?php endif ?>

        <?php foreach ($pager->links() as $link) : ?>
            <li <?= $link['active'] ? ($uri->getSegment(1)===$link['uri'])?'uk-active':'' : '' ?>>
                <a href="<?= $link['uri'] ?>">
                    <?= $link['title'] ?>
                </a>
            </li>
        <?php endforeach ?>

        <?php if ($pager->hasNext()) : ?>
            <li>
                <a href="<?= $pager->getNext() ?>" aria-label="<?= lang('Pager.last') ?>">
                    <span uk-pagination-next aria-hidden="true"></span>
                </a>
            </li>
            <li>
                <a href="<?= $pager->getLast() ?>" aria-label="<?= lang('Pager.next') ?>">
                    <span uk-icon="chevron-double-right" aria-hidden="true"></span>
                </a>
            </li>
        <?php endif ?>
    </ul>
</nav>