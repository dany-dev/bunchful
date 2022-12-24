<?php defined('ALTUMCODE') || die() ?>
<div class="index-container">
    <?= $this->views['index_menu'] ?>
    <div class="container-fluid">
        <?= \Altum\Alerts::output_alerts() ?>
        <section class="banner-section">
            <div class="container-fluid" style="margin-left: 75px;margin-right: 75px;width: calc(100% - 150px);">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1><?= l('index.header') ?></h1>
                        <p><?= l('index.subheader') ?></p>
                        <a type="button" data-toggle="modal" data-target="#introVideo" class="get-started-link mr-2" href="#"><?= l('watch_video') ?></a>
                        <a class="get-started-link" href="<?= url('register') ?>"><?= l('get_started') ?></a>
                    </div>
                    <div class="col-md-4">
                        <img class="img-fluid" src="<?= ASSETS_FULL_URL . 'images/banner-img.png' ?>" alt="">
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="introVideo" tabindex="-1" role="dialog" aria-labelledby="introVideoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <!-- 16:9 aspect ratio -->
                <div class="">
                    <?php if (settings()->main->homepage_video) : ?>
                        <?php $link = str_replace("https://www.youtube.com/watch?v=","", settings()->main->homepage_video)?>
                        <iframe width="100%" height="450" src="https://www.youtube.com/embed/<?= $link ?>?&autoplay=1&enablejsapi=1" title="Bunchful Video" frameborder="0" allowfullscreen></iframe>
                    <?php else : ?>
                        <video style="width:100%;" controls>
                            <source src="<?= ASSETS_FULL_URL . 'videos/intro.mp4' ?>" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="buisness-partner">
    <div class="container">
        <div class="text-center"><img src="<?= ASSETS_FULL_URL . 'images/border.jpg' ?>"></div>
        <h2><?= l('buisness-partner.header') ?></h2>
        <div class="owl-carousel">
            <div class="item">
                <img src="<?= ASSETS_FULL_URL . 'images/amazon.png' ?>">
            </div>
            <div class="item">
                <img src="<?= ASSETS_FULL_URL . 'images/intel.png' ?>">
            </div>
            <div class="item">
                <img src="<?= ASSETS_FULL_URL . 'images/microsoft.png' ?>">
            </div>
            <div class="item">
                <img src="<?= ASSETS_FULL_URL . 'images/pinterest.png' ?>">
            </div>
            <div class="item">
                <img src="<?= ASSETS_FULL_URL . 'images/zoom.png' ?>">
            </div>
            <div class="item">
                <img src="<?= ASSETS_FULL_URL . 'images/chase.png' ?>">
            </div>
            <div class="item">
                <img src="<?= ASSETS_FULL_URL . 'images/starbucks.png' ?>">
            </div>
            <div class="item">
                <img src="<?= ASSETS_FULL_URL . 'images/johnson.png' ?>">
            </div>
            <div class="item">
                <img src="<?= ASSETS_FULL_URL . 'images/boeing.png' ?>">
            </div>
            <div class="item">
                <img src="<?= ASSETS_FULL_URL . 'images/hilton.png' ?>">
            </div>
        </div>
    </div>
</section>

<section class="tracking-pixels">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card d-flex flex-column justify-content-between h-100">
                    <div class="card-body">
                        <div class="mb-2 p-3 text-center">
                            <i class="fa fa-fw fa-lg fa-adjust text-gray mr-3"></i>
                            <span class="h5"><?= l('index.pixels.header') ?></span>
                        </div>

                        <div class="d-flex justify-content-between flex-wrap">
                            <?php foreach (require APP_PATH . 'includes/pixels.php' as $item) : ?>
                                <span style="font-size: 30px;" data-toggle="tooltip" title="<?= $item['name'] ?>"><i class="<?= $item['icon'] ?> fa-fw mx-1" style="color: <?= $item['color'] ?>"></i></span>
                            <?php endforeach ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="how-it-works container pt-5 pb-5">
    <div class="text-center"><img src="<?= ASSETS_FULL_URL . 'images/border.jpg' ?>"></div>
    <h2 class="main-heading">How It Works</h2>
    <div class="our-work">
        <div class="work-box">
            <img src="<?= ASSETS_FULL_URL . 'images/copy-writing.svg' ?>">
            <h3><?= l('how-it-works.header.1') ?></h3>
            <p><?= l('how-it-works.subheader.1') ?></p>
        </div>
        <div class="work-box">
            <img src="<?= ASSETS_FULL_URL . 'images/devices.svg' ?>">
            <h3><?= l('how-it-works.header.2') ?></h3>
            <p><?= l('how-it-works.subheader.2') ?></p>
        </div>
        <div class="work-box">
            <img src="<?= ASSETS_FULL_URL . 'images/collaboration.svg' ?>">
            <h3><?= l('how-it-works.header.3') ?></h3>
            <p><?= l('how-it-works.subheader.3') ?></p>
        </div>
    </div>
</section>
<?php if (settings()->links->biolinks_is_enabled) : ?>
    <section class="biolinks texture-bg text-center">
        <div class="container">
            <h2 class="text-center"><span><?= l('biolinks.header') ?></span></h2>
            <p class="text-center"><span><?= l('biolinks.subheader') ?></span></p>
            <a href="#"><?= l('get_started') ?></a>
        </div>
    </section>

    <section class="info-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-7 order-2 order-md-2 order-lg-1">
                    <h2><?= l('info-section.header.1') ?></h2>
                    <div class="info-list">
                        <div class="info-icon">
                            <img src="<?= ASSETS_FULL_URL . 'images/icon-1.svg' ?>">
                        </div>
                        <p><?= l('info-section.statement.1') ?></p>
                    </div>
                    <div class="info-list">
                        <div class="info-icon">
                            <img src="<?= ASSETS_FULL_URL . 'images/icon-6.svg' ?>">
                        </div>
                        <p><?= l('info-section.statement.1') ?></p>
                    </div>
                    <div class="info-list">
                        <div class="info-icon">
                            <img src="<?= ASSETS_FULL_URL . 'images/icon-5.svg' ?>">
                        </div>
                        <p><?= l('info-section.statement.1') ?></p>
                    </div>
                </div>
                <div class="col-lg-5 order-1 order-md-1 order-lg-2">
                    <img style="border-radius: 38px;" class="img-fluid" src="<?= ASSETS_FULL_URL . 'images/product-qa.jpg' ?>" alt="">
                </div>
            </div>
        </div>
    </section>

    <section class="info-section  pt-0">
        <div class="container">
            <div class="row">
                <div class="col-lg-5">
                    <img style="border-radius: 38px;" class="img-fluid" src="<?= ASSETS_FULL_URL . 'images/philanthropic-work.jpg' ?>" alt="">
                </div>
                <div class="col-lg-7">
                    <h2><?= l('info-section.header.2') ?></h2>
                    <div class="info-list">
                        <div class="info-icon">
                            <img src="<?= ASSETS_FULL_URL . 'images/icon-2.svg' ?>">
                        </div>
                        <p><?= l('info-section.statement.4') ?></p>
                    </div>
                    <div class="info-list">
                        <div class="info-icon">
                            <img src="<?= ASSETS_FULL_URL . 'images/icon-4.svg' ?>">
                        </div>
                        <p><?= l('info-section.statement.5') ?></p>
                    </div>
                    <div class="info-list">
                        <div class="info-icon">
                            <img src="<?= ASSETS_FULL_URL . 'images/icon-3.svg' ?>">
                        </div>
                        <p><?= l('info-section.statement.6') ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php endif ?>

<?php if (settings()->links->shortener_is_enabled) : ?>
    <section class="buisness-analytics">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-5">
                    <img class="img-fluid" src="<?= ASSETS_FULL_URL . 'images/analytics.png' ?>" alt="">
                </div>
                <div class="col-md-7">
                    <div class="text-left"><img src="<?= ASSETS_FULL_URL . 'images/border.jpg' ?>"></div>
                    <h2><?= l('buisness-analytics.header') ?></h2>
                    <p><?= l('buisness-analytics.subheader') ?></p>
                    <div class="rating-box">
                        <div class="rating-boxes">
                            <h3><?= l('bunchful-atlas') ?></h3>
                            <h4>9+</h4>
                        </div>
                        <div class="rating-boxes">
                            <h3><?= l('tracked-pageviews') ?></h3>
                            <h4>588+</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php endif ?>

<section class="testimonial">
    <div class="container">
        <h2><?= l('testimonial.header') ?></h2>
        <p><?= l('testimonial.subheader') ?></p>
        <div class="testimonial-cont">
            <div class="testimonial-box">
                <img src="<?= ASSETS_FULL_URL . 'images/cheick_keita.jpeg' ?>" style="border-radius: 50%;" alt="" width="50" height="50">
                <h3>Ambassador, Cheick Keita, Paris, France</h3>
                <p>I attend several events a year where there are hundreds of people, so I used to have to print hundreds of cards every year and I had no idea what happened to those cards after I gave them out. Now that I'm on the Bunchful Atlas, I always have a current profile and I never run out.</p>
            </div>
            <div class="testimonial-box">
                <img src="<?= ASSETS_FULL_URL . 'images/sarah.png' ?>" alt="" width="55" height="55">
                <h3>Sarah, Hospitality Industry, Montreal</h3>
                <p>This platform is AHHMAZING!! Not only do I get to show all my products and services, I also get to display all the charities I support. Usually, this information gets lost in information overload. Now, I have a dedicated spot to showcase all the ways I'm helping the community. Like I said, AMAZING!</p>
            </div>
            <div class="testimonial-box">
                <img src="<?= ASSETS_FULL_URL . 'images/user-icon.jpg' ?>" alt="" width="50" height="50">
                <h3>Patrick, Software as a Service, New York</h3>
                <p>We are saving so much money and time when we onboard new employees. We simply upload their info and instantly they have their business card profile, so we don't have to wait weeks for them to arrive. Also, I get to monitor networking activity of the whole team and track their outreach.</p>
            </div>
        </div>
    </div>
</section>

<section class="countdown-clock texture-bg pt-sm-2 pb-sm-1 pt-md-3 pb-md-2 pt-lg-5 pb-lg-4 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="alarm-ad">
                    <img src="<?= ASSETS_FULL_URL . 'images/alarm-clock.png' ?>" alt="">
                </div>
            </div>
            <div class="col-md-8">
                <h2><span><?= l('countdown-clock.header') ?></span></h2>
                <p><span><?= l('countdown-clock.subheader') ?></span></p>
            </div>
        </div>
    </div>
</section>

<section class="price-sec">
    <div class="container">
        <div class="text-center mb-5">
            <h2><?= l('index.pricing.header') ?></h2>
            <p class="text-muted"><?= l('index.pricing.subheader') ?></p>
        </div>

        <?= $this->views['plans'] ?>
    </div>
</section>

<section class="bunchful-info">
    <div class="container">
        <div class="bunchful-box">
            <div class="text-center"><img src="<?= ASSETS_FULL_URL . 'images/border.jpg' ?>"></div>
            <h2><?= l('bunchful-info.header') ?></h2>
            <h3><?= l('bunchful-info.subheader') ?></h3>
            <p><?= l('bunchful-info.description') ?></p>
        </div>
    </div>
</section>

<section class="scroll-timeline mt-3">
    <div class="container">
        <div class="text-center"><img src="<?= ASSETS_FULL_URL . 'images/border.jpg' ?>"></div>
        <h2><?= l('scroll-timeline.header') ?></h2>
        <ul class="timeline">
            <li class="active-tl">
                <span><?= l('scroll-timeline.description.1') ?></span>
            </li>
            <li>
                <span><?= l('scroll-timeline.description.2') ?></span>
            </li>
            <li>
                <span><?= l('scroll-timeline.description.3') ?></span>
            </li>
            <li>
                <span><?= l('scroll-timeline.description.4') ?></span>
            </li>
        </ul>
    </div>
</section>

<section class="bunchful-info">
    <div class="container">
        <div class="bunchful-box">
            <div class="text-center"><img src="<?= ASSETS_FULL_URL . 'images/border.jpg' ?>"></div>
            <h2><?= l('bunchful-info-launch.header') ?></h2>
            <p><?= l('bunchful-info-launch.subheader') ?></p>
        </div>
    </div>
</section>

<section class="certifications">
    <div class="container">
        <div class="row">
            <div class="col-md-12 mb-3">
                <h2 class="text-center">Certifications</h2>
            </div>
            <div class="col-md-12 d-flex justify-content-center">
                <img style="margin-right:40px;width:100px;" src="<?= ASSETS_FULL_URL . 'images/cert1.png' ?>" alt="">
                <img style="width:100px;" src="<?= ASSETS_FULL_URL . 'images/cert2.png' ?>" alt="">
            </div>
        </div>
    </div>
</section>