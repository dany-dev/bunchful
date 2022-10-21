<footer>
    <div class="container">
        <div class="d-flex flex-column flex-lg-row justify-content-end">
            <?php if (count(\Altum\Language::$active_languages) > 1) : ?>
                <div class="dropdown mb-2 ml-lg-3" data-toggle="tooltip" title="<?= l('global.choose_language') ?>">
                    <button type="button" class="btn btn-link text-decoration-none p-1" id="language_switch" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-fw fa-sm fa-language mr-1"></i> <?= \Altum\Language::$name ?>
                    </button>

                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="language_switch">
                        <?php foreach (\Altum\Language::$active_languages as $language_name => $language_code) : ?>
                            <a class="dropdown-item" href="<?= SITE_URL . $language_code . '/' . \Altum\Routing\Router::$original_request . '?set_language=' . $language_name ?>">
                                <?php if ($language_name == \Altum\Language::$name) : ?>
                                    <i class="fa fa-fw fa-sm fa-check mr-2 text-success"></i>
                                <?php else : ?>
                                    <i class="fa fa-fw fa-sm fa-circle-notch mr-2 text-muted"></i>
                                <?php endif ?>

                                <?= $language_name ?>
                            </a>
                        <?php endforeach ?>
                    </div>
                </div>
            <?php endif ?>

            <?php if (count(\Altum\ThemeStyle::$themes) > 1) : ?>
                <div class="mb-2 ml-lg-3">
                    <button type="button" id="switch_theme_style" class="btn btn-link text-decoration-none p-0 switch_theme_style" data-toggle="tooltip" title="<?= sprintf(l('global.theme_style'), (\Altum\ThemeStyle::get() == 'light' ? l('global.theme_style_dark') : l('global.theme_style_light'))) ?>" data-title-theme-style-light="<?= sprintf(l('global.theme_style'), l('global.theme_style_light')) ?>" data-title-theme-style-dark="<?= sprintf(l('global.theme_style'), l('global.theme_style_dark')) ?>">
                        <span data-theme-style="light" class="<?= \Altum\ThemeStyle::get() == 'light' ? null : 'd-none' ?>"><i class="fa fa-fw fa-sm fa-sun mr-1"></i> <?= l('global.theme_style_light') ?></span>
                        <span data-theme-style="dark" class="<?= \Altum\ThemeStyle::get() == 'dark' ? null : 'd-none' ?>"><i class="fa fa-fw fa-sm fa-moon mr-1"></i> <?= l('global.theme_style_dark') ?></span>
                    </button>
                </div>
            <?php endif ?>
        </div>
        <div class="row align-items-center">
            <div class="col-md-7">
                <h2><?= l('footer.signup.header') ?></h2>
                <p class="pr-lg-3"><?= l('footer.signup.subheader') ?></p>
            </div>
            <div class="col-md-5">
                <form>
                    <div class="form-group">
                        <input type="email" class="form-control" id="Email" placeholder="Email Address">
                        <button type="submit" class="btn"><?= l('subscribe') ?></button>
                    </div>
                </form>
            </div>
        </div>
        <div class="row my-4 align-items-center">
            <div class="col-md-7">
                <img src="<?= ASSETS_FULL_URL . 'images/footer-logo.png' ?>" alt="">
            </div>
            <div class="col-md-5">
                <div class="social-icons">
                    <ul class="d-flex justify-content-between">
                        <?php foreach (require APP_PATH . 'includes/admin_socials.php' as $key => $value) : ?>
                            <?php if (isset(settings()->socials->{$key}) && !empty(settings()->socials->{$key})) : ?>
                                <li>
                                    <a href="<?= sprintf($value['format'], settings()->socials->{$key}) ?>" target="_blank" data-toggle="tooltip" title="<?= $value['name'] ?>">
                                        <i class="<?= $value['icon'] ?> fa-fw fa-lg"></i>
                                    </a>
                                </li>
                            <?php endif ?>
                        <?php endforeach ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</footer>
<div class="min-footer">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <ul>
                    <?php if (settings()->main->blog_is_enabled) : ?>
                        <li><a href="<?= url('blog') ?>"><?= l('blog.menu') ?></a></li>
                    <?php endif ?>

                    <?php if (settings()->payment->is_enabled) : ?>
                        <?php if (\Altum\Plugin::is_active('affiliate') && settings()->affiliate->is_enabled) : ?>
                            <li><a href="<?= url('affiliate') ?>"><?= l('affiliate.menu') ?></a></li>
                        <?php endif ?>
                    <?php endif ?>

                    <?php if (settings()->email_notifications->contact && !empty(settings()->email_notifications->emails)) : ?>
                        <li><a href="<?= url('contact') ?>"><?= l('contact.menu') ?></a></li>
                    <?php endif ?>

                    <?php if (settings()->cookie_consent->is_enabled) : ?>
                        <li><a href="#" data-cc="c-settings"><?= l('global.cookie_consent.menu') ?></a></li>
                    <?php endif ?>
                    <?php if (count($data->pages)) : ?>
                        <?php foreach ($data->pages as $row) : ?>
                            <li><a href="<?= $row->url ?>" target="<?= $row->target ?>"><?= $row->title ?></a></li>
                        <?php endforeach ?>
                    <?php endif ?>
                </ul>
            </div>
            <div class="col-md-6">
                <div class="rights">
                    <p><?= sprintf(l('global.footer.copyright'), date('Y'), settings()->main->title) ?></p>
                </div>
            </div>
        </div>
    </div>
</div>