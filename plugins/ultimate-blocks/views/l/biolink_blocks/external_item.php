<?php defined('ALTUMCODE') || die() ?>

<div id="<?= 'biolink_block_id_' . $data->link->biolink_block_id ?>" data-biolink-block-id="<?= $data->link->biolink_block_id ?>" class="col-12 my-2">
    <div class="card position-relative p-3 link-hover-animation <?= 'link-btn-' . $data->link->settings->border_radius ?> <?= $data->link->design->card_class ?>" style="<?= $data->link->design->card_style ?>">
        <div class="row g-0">
            <?php if($data->link->settings->image): ?>
            <div class="col-md-4 d-flex justify-content-center align-items-center mb-3 mb-md-0">
                <img src="<?= $data->link->settings->image ? UPLOADS_FULL_URL . 'block_thumbnail_images/' . $data->link->settings->image : null ?>" class="link-external-item-image <?= 'link-btn-' . $data->link->settings->border_radius ?>" loading="lazy" />
            </div>
            <?php endif ?>
            <div class="col-md-8">
                <div class="d-flex flex-column text-left">
                    <span class="h5" data-name style="<?= 'color: ' . $data->link->settings->name_text_color ?>"><?= $data->link->settings->name ?></span>
                    <p class="p-0" data-description style="<?= 'color: ' . $data->link->settings->description_text_color ?>"><?= $data->link->settings->description ?></p>
                    <span class="h4" data-price style="<?= 'color: ' . $data->link->settings->price_text_color ?>"><?= $data->link->settings->price ?></span>
                </div>
            </div>
        </div>
        <a href="<?= $data->link->location_url . $data->link->utm_query ?>" data-track-biolink-block-id="<?= $data->link->biolink_block_id ?>" rel="<?= $data->user->plan_settings->dofollow_is_enabled ? 'dofollow' : 'nofollow' ?>" class="stretched-link"></a>
    </div>
</div>


