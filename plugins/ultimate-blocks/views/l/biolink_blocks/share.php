<?php defined('ALTUMCODE') || die() ?>

<div id="<?= 'biolink_block_id_' . $data->link->biolink_block_id ?>" data-biolink-block-id="<?= $data->link->biolink_block_id ?>" class="col-12 my-2">
    <a href="#" data-toggle="modal" data-target="<?= '#share_' . $data->link->biolink_block_id ?>" data-track-biolink-block-id="<?= $data->link->biolink_block_id ?>" class="btn btn-block btn-primary link-btn link-hover-animation <?= 'link-btn-' . $data->link->settings->border_radius ?> <?= $data->link->design->link_class ?>" style="<?= $data->link->design->link_style ?>">
        <div class="link-btn-image-wrapper <?= 'link-btn-' . $data->link->settings->border_radius ?>" <?= $data->link->settings->image ? null : 'style="display: none;"' ?>>
            <img src="<?= $data->link->settings->image ? UPLOADS_FULL_URL . 'block_thumbnail_images/' . $data->link->settings->image : null ?>" class="link-btn-image" loading="lazy" />
        </div>

        <?php if($data->link->settings->icon): ?>
            <i class="<?= $data->link->settings->icon ?> mr-1"></i>
        <?php endif ?>

        <?= $data->link->settings->name ?>
    </a>
</div>

<?php ob_start() ?>
<div class="modal fade" id="<?= 'share_' . $data->link->biolink_block_id ?>" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title"><?= $data->link->settings->name ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= l('global.close') ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="link-qr-code" data-qr></div>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-between mb-3">
                    <a href="mailto:?body=<?= $data->link->location_url ?>" target="_blank" class="btn btn-gray-100 mb-2 mb-md-0 mr-md-3">
                        <i class="fa fa-fw fa-envelope"></i>
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $data->link->location_url ?>" target="_blank" class="btn btn-gray-100 mb-2 mb-md-0 mr-md-3">
                        <i class="fab fa-fw fa-facebook"></i>
                    </a>
                    <a href="https://twitter.com/share?url=<?= $data->link->location_url ?>" target="_blank" class="btn btn-gray-100 mb-2 mb-md-0 mr-md-3">
                        <i class="fab fa-fw fa-twitter"></i>
                    </a>
                    <a href="https://pinterest.com/pin/create/link/?url=<?= $data->link->location_url ?>" target="_blank" class="btn btn-gray-100 mb-2 mb-md-0 mr-md-3">
                        <i class="fab fa-fw fa-pinterest"></i>
                    </a>
                    <a href="https://linkedin.com/shareArticle?url=<?= $data->link->location_url ?>" target="_blank" class="btn btn-gray-100 mb-2 mb-md-0 mr-md-3">
                        <i class="fab fa-fw fa-linkedin"></i>
                    </a>
                    <a href="https://www.reddit.com/submit?url=<?= $data->link->location_url ?>" target="_blank" class="btn btn-gray-100 mb-2 mb-md-0 mr-md-3">
                        <i class="fab fa-fw fa-reddit"></i>
                    </a>
                    <a href="https://wa.me/?text=<?= $data->link->location_url ?>" class="btn btn-gray-100 mb-2 mb-md-0 mr-md-3">
                        <i class="fab fa-fw fa-whatsapp"></i>
                    </a>
                </div>

                <div class="form-group">
                    <input type="text" class="form-control" value="<?= $data->link->location_url ?>" onclick="this.select();" readonly="readonly" />
                </div>
            </div>

        </div>
    </div>
</div>
<?php \Altum\Event::add_content(ob_get_clean(), 'modals') ?>


<?php if(!\Altum\Event::exists_content_type_key('javascript', 'share')): ?>
<?php ob_start() ?>
<script src="<?= ASSETS_FULL_URL . 'js/libraries/jquery-qrcode.min.js' ?>"></script>

<script>
    'use strict';

    let qr = document.querySelector('<?= '#share_' . $data->link->biolink_block_id ?> [data-qr]');

    let generate_qr = () => {
        let qr_url = <?= json_encode($data->link->location_url) ?>;

        let mode = 0;
        let mode_size = 0.1;

        let default_options = {
            render: 'image',
            minVersion: 1,
            maxVersion: 40,
            ecLevel: 'H',
            left: 0,
            top: 0,
            size: 1000,
            text: qr_url,
            quiet: 0,
            mode: mode,
            mSize: mode_size,
            mPosX: 0.5,
            mPosY: 0.5,
        };

        /* Delete already existing image generated */
        qr.querySelector('img') && qr.querySelector('img').remove();
        $(qr).qrcode(default_options);
    }

    generate_qr();
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript', 'share') ?>
<?php endif ?>
