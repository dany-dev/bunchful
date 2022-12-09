<?php defined('ALTUMCODE') || die() ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />
<style>
    .swiper {
        width: 100%;
        height: 250px;
    }
</style>
<section class="container">
    <?= \Altum\Alerts::output_alerts() ?>

    <div class="row mb-4">
        <div class="col-12 col-lg d-flex align-items-center mb-3 mb-lg-0">
            <h1 class="h4 m-0"><?= l('catalog.header') ?></h1>

            <div class="ml-2">
                <span data-toggle="tooltip" title="<?= l('catalog.subheader') ?>">
                    <i class="fa fa-fw fa-info-circle text-muted"></i>
                </span>
            </div>
        </div>
    </div>

    <?php if (count($data->catalog)) : ?>
        <div class="custom-row mb-4" data-catalog-id="<?= $row->catalog_id ?>">
            <div class="row">
                <?php foreach ($data->catalog as $row) : ?>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <div class="swiper">
                                    <!-- Additional required wrapper -->
                                    <div class="swiper-wrapper">
                                        <!-- Slides -->
                                        <?php foreach ($row->images as $k => $image) : ?>
                                            <div class="swiper-slide">
                                                <img class="img-fluid" src="<?= url() . '/uploads/catalog_images/' . $image->image ?>" style="max-width:'250px;'">
                                            </div>
                                        <?php endforeach ?>
                                    </div>
                                    <!-- If we need pagination -->
                                    <div class="swiper-pagination"></div>

                                    <!-- If we need navigation buttons -->
                                    <div class="swiper-button-prev"></div>
                                    <div class="swiper-button-next"></div>

                                    <!-- If we need scrollbar -->
                                    <div class="swiper-scrollbar"></div>
                                </div>
                            </div>
                            <div class="card-body">
                                <h4><?= $row->name ?></h4>
                                <p><?= $row->description ?></p>
                                <p>Price: $<?= $row->price ?></p>
                            </div>
                            <div class="card-footer text-center">
                                <a class="btn btn-primary" href="<?= url('catalog/pay?id='.$row->catalog_id) ?>">Buy Now</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        </div>

    <?php else : ?>
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-column align-items-center justify-content-center py-3">
                    <img src="<?= ASSETS_FULL_URL . 'images/no_rows.svg' ?>" class="col-10 col-md-7 col-lg-4 mb-3" alt="<?= l('catalog.no_data') ?>" />
                    <h2 class="h4 text-muted"><?= l('catalog.no_data') ?></h2>
                </div>
            </div>
        </div>
    <?php endif ?>

</section>
<script>
    var base_url = '<?= url() ?>';
</script>
<script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>

<script>
    const swiper = new Swiper('.swiper', {
        // Optional parameters
        loop: true,

        // If we need pagination
        pagination: {
            el: '.swiper-pagination',
        },

        // Navigation arrows
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },

        // And if we need scrollbar
        scrollbar: {
            el: '.swiper-scrollbar',
        },
    });
</script>