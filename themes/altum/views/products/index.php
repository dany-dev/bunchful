<?php defined('ALTUMCODE') || die() ?>

<section class="container">
    <?= \Altum\Alerts::output_alerts() ?>

    <div class="row mb-4">
        <div class="col-12 col-lg d-flex align-items-center mb-3 mb-lg-0">
            <h1 class="h4 m-0"><?= l('products.header') ?></h1>

            <div class="ml-2">
                <span data-toggle="tooltip" title="<?= l('products.subheader') ?>">
                    <i class="fa fa-fw fa-info-circle text-muted"></i>
                </span>
            </div>
        </div>

        <div class="col-12 col-lg-auto d-flex">
            <div>
                <?php if ($this->user->plan_settings->products_limit != -1 && $data->total_products >= $this->user->plan_settings->products_limit) : ?>
                    <button type="button" data-toggle="tooltip" title="<?= l('global.info_message.plan_feature_limit') ?>" class="btn btn-primary disabled">
                        <i class="fa fa-fw fa-plus-circle"></i> <?= l('products.create') ?>
                    </button>
                <?php else : ?>
                    <button type="button" data-toggle="modal" data-target="#create_product_modal" class="btn btn-primary"><i class="fa fa-fw fa-plus-circle"></i> <?= l('products.create') ?></button>
                <?php endif ?>
            </div>
        </div>
    </div>

    <?php if (count($data->products)) : ?>
        <?php foreach ($data->products as $row) : ?>
            <div class="custom-row mb-4" data-product-id="<?= $row->product_id ?>">
                <div class="row">
                    <div class="col-3 col-lg-3 d-flex align-items-center">
                        <div class="font-weight-bold text-truncate">
                            <a href="#" data-toggle="modal" data-target="#product_update_modal" data-id="<?= $row->id ?>" data-name="<?= $row->name ?>" data-product-id="<?= $row->product_id ?>" data-product-link="<?= $row->product_link ?>" data-auto-generated-link="<?= $row->auto_generated_link ?>" data-type="<?= $row->type ?>"><?= $row->name ?></a>
                        </div>
                    </div>

                    <div class="col-3 col-lg-3 d-flex flex-column justify-content-center">
                        <a href="<?= $row->product_link ?>" target="_blank" style="word-break: break-word;"><?= $row->product_link ?></a>
                        <a href="<?= $row->auto_generated_link ?>" target="_blank" style="word-break: break-word;">Auto Generated Link</a>
                    </div>

                    <div class="col-2 col-lg-2 d-flex justify-content-center">
                        <div class="font-weight-bold text-truncate">
                            <?php if (file_exists( 'uploads/qr_codes/' . $row->id . '/image.png' )) { ?>
                                <img data-toggle="modal" data-target="#show_qr_modal" data-id="<?= $row->id ?>" src="<?= UPLOADS_FULL_URL . 'qr_codes/' . $row->id . '/image.png' ?>" alt="QR" class="img-fluid" style="cursor: pointer;width: 85px;">
                            <?php } else { ?>
                                <p>No QR Code</p>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="col-2 col-lg-2 d-none d-lg-flex justify-content-center justify-content-lg-end align-items-center">
                        <small class="text-muted" data-toggle="tooltip" title="<?= \Altum\Date::get($row->datetime, 1) ?>"><i class="fa fa-fw fa-calendar fa-sm mr-1"></i> <span class="align-middle"><?= \Altum\Date::get($row->datetime, 2) ?></span></small>
                    </div>

                    <div class="col-2 col-lg-2 d-flex justify-content-center justify-content-lg-end align-items-center">
                        <div class="dropdown">
                            <button type="button" class="btn btn-link text-secondary dropdown-toggle dropdown-toggle-simple" data-toggle="dropdown" data-boundary="viewport">
                                <i class="fa fa-fw fa-ellipsis-v"></i>
                            </button>

                            <div class="dropdown-menu dropdown-menu-right">
                                <a href="#" data-toggle="modal" data-target="#product_update_modal" data-id="<?= $row->id ?>" data-name="<?= $row->name ?>" data-product-id="<?= $row->product_id ?>" data-product-link="<?= $row->product_link ?>" data-type="<?= $row->type ?>" data-auto-generated-link="<?= $row->auto_generated_link ?>" class="dropdown-item"><i class="fa fa-fw fa-sm fa-pencil-alt mr-2"></i> <?= l('global.edit') ?></a>
                                <a href="#" data-toggle="modal" data-target="#product_delete_modal" data-id="<?= $row->id ?>" class="dropdown-item"><i class="fa fa-fw fa-sm fa-trash-alt mr-2"></i> <?= l('global.delete') ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach ?>

    <?php else : ?>
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-column align-items-center justify-content-center py-3">
                    <img src="<?= ASSETS_FULL_URL . 'images/no_rows.svg' ?>" class="col-10 col-md-7 col-lg-4 mb-3" alt="<?= l('products.no_data') ?>" />
                    <h2 class="h4 text-muted"><?= l('products.no_data') ?></h2>
                </div>
            </div>
        </div>
    <?php endif ?>

</section>
<script>
    var base_url = '<?= url() ?>';
</script>
<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/products/product_create_modal.php', [
    'product_types' => $data->product_types
]), 'modals'); ?>
<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/products/product_update_modal.php', [
    'product_types' => $data->product_types
]), 'modals'); ?>
<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/products/product_delete_modal.php'), 'modals'); ?>
<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/products/show_qr_modal.php'), 'modals'); ?>