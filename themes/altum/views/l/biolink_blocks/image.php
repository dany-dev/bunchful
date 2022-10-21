<?php defined('ALTUMCODE') || die() ?>

<div id="<?= 'biolink_block_id_' . $data->link->biolink_block_id ?>" data-biolink-block-id="<?= $data->link->biolink_block_id ?>" class="col-12 my-2">
    <?php if($data->link->location_url): ?>
    <a href="<?= $data->link->location_url . $data->link->utm_query ?>" data-track-biolink-block-id="<?= $data->link->biolink_block_id ?>" target="_blank">
        <img src="<?= mb_substr($data->link->settings->image, 0, 4) === "http" ? $data->link->settings->image : UPLOADS_FULL_URL . 'block_images/' . $data->link->settings->image ?>" class="img-fluid rounded" alt="<?= $data->link->settings->image_alt ?>" loading="lazy" />
    </a>
    <?php else: ?>
    <img src="<?= mb_substr($data->link->settings->image, 0, 4) === "http" ? $data->link->settings->image : UPLOADS_FULL_URL . 'block_images/' . $data->link->settings->image ?>" class="img-fluid rounded" alt="<?= $data->link->settings->image_alt ?>" loading="lazy" />
    <?php endif ?>
</div>

