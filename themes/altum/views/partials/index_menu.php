<?php defined('ALTUMCODE') || die() ?>

<header>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand font-weight-bold" href="<?= url() ?>">
                <?php if (\Altum\ThemeStyle::get() == 'light' && settings()->main->{'logo_' . \Altum\ThemeStyle::get()} != '') : ?>
                    <img src="<?= UPLOADS_FULL_URL . 'main/' . settings()->main->{'logo_' . \Altum\ThemeStyle::get()} ?>" class="img-fluid navbar-logo" alt="<?= l('global.accessibility.logo_alt') ?>" />
                <?php elseif (\Altum\ThemeStyle::get() == 'dark' && settings()->main->{'logo_' . \Altum\ThemeStyle::get()} != '') : ?>
                    <img src="<?= UPLOADS_FULL_URL . 'main/' . settings()->main->{'logo_' . \Altum\ThemeStyle::get()} ?>" class="img-fluid navbar-logo" alt="<?= l('global.accessibility.logo_alt') ?>" />
                <?php else : ?>
                    <?= settings()->main->title ?>
                <?php endif ?>
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fa fa-fw fa-bars"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav ml-auto">
                    <?php foreach ($data->pages as $data) : ?>
                        <li class="nav-item"><a class="nav-link" href="<?= $data->url ?>" target="<?= $data->target ?>"><?= $data->title ?></a></li>
                    <?php endforeach ?>

                    <?php if (settings()->links->biolinks_is_enabled && settings()->links->directory_is_enabled) : ?>
                        <li class="nav-item"><a class="nav-link" href="<?= url('directory') ?>"><?= l('directory.menu') ?></a></li>
                    <?php endif ?>

                    <?php if (isset(settings()->tools->is_enabled) && settings()->tools->is_enabled && settings()->tools->access == 'everyone') : ?>
                        <li class="nav-item"><a class="nav-link" href="<?= url('tools') ?>"><?= l('tools.menu') ?></a></li>
                    <?php endif ?>

                    <?php if (\Altum\Middlewares\Authentication::check()) : ?>

                        <li class="nav-item"><a class="nav-link" href="<?= url('dashboard') ?>"> <?= l('dashboard.menu') ?></a></li>

                        <li class="dropdown">
                            <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" aria-haspopup="true" aria-expanded="false">
                                <img src="<?= get_gravatar($this->user->email, 80, 'identicon') ?>" class="navbar-avatar mr-1" loading="lazy" />
                                <?= $this->user->name ?> <span class="caret"></span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <?php if (!\Altum\Teams::is_delegated()) : ?>
                                    <?php if (\Altum\Middlewares\Authentication::is_admin()) : ?>
                                        <a class="dropdown-item" href="<?= url('admin') ?>"><i class="fa fa-fw fa-sm fa-fingerprint mr-2"></i> <?= l('global.menu.admin') ?></a>
                                    <?php endif ?>
                                <?php endif ?>

                                <?php if (settings()->links->biolinks_is_enabled) : ?>
                                    <a class="dropdown-item" href="<?= url('links?type=biolink') ?>"><i class="fa fa-fw fa-sm fa-hashtag mr-2"></i> <?= l('links.menu.biolinks') ?></a>
                                <?php endif ?>

                                <?php if (settings()->links->shortener_is_enabled) : ?>
                                    <a class="dropdown-item" href="<?= url('links?type=link') ?>"><i class="fa fa-fw fa-sm fa-link mr-2"></i> <?= l('links.menu.links') ?></a>
                                <?php endif ?>

                                <?php if (settings()->links->files_is_enabled) : ?>
                                    <a class="dropdown-item" href="<?= url('links?type=file') ?>"><i class="fa fa-fw fa-sm fa-file mr-2"></i> <?= l('links.menu.files') ?></a>
                                <?php endif ?>

                                <?php if (settings()->links->vcards_is_enabled) : ?>
                                    <a class="dropdown-item" href="<?= url('links?type=vcard') ?>"><i class="fa fa-fw fa-sm fa-id-card mr-2"></i> <?= l('links.menu.vcards') ?></a>
                                <?php endif ?>

                                <?php if ($data->user_data->is_global_owner) : ?>
                                    <a class="dropdown-item" href="<?= url('company') ?>"><i class="fa fa-fw fa-sm fa-building mr-2"></i> <?= l('company.menu') ?></a>
                                <?php endif ?>

                                <?php if (settings()->links->qr_codes_is_enabled) : ?>
                                    <a class="dropdown-item" href="<?= url('qr-codes') ?>"><i class="fa fa-fw fa-sm fa-qrcode mr-2"></i> <?= l('qr_codes.menu') ?></a>
                                <?php endif ?>

                                <?php if (isset(settings()->tools->is_enabled) && settings()->tools->is_enabled && settings()->tools->access == 'users') : ?>
                                    <a class="dropdown-item" href="<?= url('tools') ?>"><i class="fa fa-fw fa-sm fa-tools mr-2"></i> <?= l('tools.menu') ?></a>
                                <?php endif ?>

                                <div class="dropdown-divider"></div>

                                <?php if (settings()->links->domains_is_enabled) : ?>
                                    <a class="dropdown-item" href="<?= url('domains') ?>"><i class="fa fa-fw fa-sm fa-globe mr-2"></i> <?= l('domains.menu') ?></a>
                                <?php endif ?>

                                <a class="dropdown-item" href="<?= url('pixels') ?>"><i class="fa fa-fw fa-sm fa-adjust mr-2"></i> <?= l('pixels.menu') ?></a>

                                <a class="dropdown-item" href="<?= url('products') ?>"><i class="fa fa-fw fa-sm fa-sitemap mr-2"></i> <?= l('products.menu') ?></a>

                                <?php if (settings()->links->biolinks_is_enabled) : ?>
                                    <a class="dropdown-item" href="<?= url('data') ?>"><i class="fa fa-fw fa-sm fa-database mr-2"></i> <?= l('data.menu') ?></a>

                                    <?php if (\Altum\Plugin::is_active('payment-blocks')) : ?>
                                        <a class="dropdown-item" href="<?= url('payment-processors') ?>"><i class="fa fa-fw fa-sm fa-credit-card mr-2"></i> <?= l('payment_processors.menu') ?></a>
                                        <a class="dropdown-item" href="<?= url('guests-payments') ?>"><i class="fa fa-fw fa-sm fa-coins mr-2"></i> <?= l('guests_payments.menu') ?></a>
                                    <?php endif ?>
                                <?php endif ?>

                                <a class="dropdown-item" href="<?= url('projects') ?>"><i class="fa fa-fw fa-sm fa-project-diagram mr-2"></i> <?= l('projects.menu') ?></a>

                                <div class="dropdown-divider"></div>

                                <?php if (!\Altum\Teams::is_delegated()) : ?>
                                    <a class="dropdown-item" href="<?= url('account') ?>"><i class="fa fa-fw fa-sm fa-wrench mr-2"></i> <?= l('account.menu') ?></a>

                                    <a class="dropdown-item" href="<?= url('account-plan') ?>"><i class="fa fa-fw fa-sm fa-box-open mr-2"></i> <?= l('account_plan.menu') ?></a>

                                    <?php if (settings()->payment->is_enabled) : ?>
                                        <a class="dropdown-item" href="<?= url('account-payments') ?>"><i class="fa fa-fw fa-sm fa-dollar-sign mr-2"></i> <?= l('account_payments.menu') ?></a>

                                        <?php if (\Altum\Plugin::is_active('affiliate') && settings()->affiliate->is_enabled) : ?>
                                            <a class="dropdown-item" href="<?= url('referrals') ?>"><i class="fa fa-fw fa-sm fa-wallet mr-2"></i> <?= l('referrals.menu') ?></a>
                                        <?php endif ?>
                                    <?php endif ?>

                                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#nfc_support_modal"><i class="fa fa-fw fa-sm fa-envelope mr-2"></i> <?= l('account.nfc_support') ?></a>

                                    <a class="dropdown-item" href="<?= url('account-api') ?>"><i class="fa fa-fw fa-sm fa-code mr-2"></i> <?= l('account_api.menu') ?></a>

                                    <?php if (\Altum\Plugin::is_active('teams')) : ?>
                                        <a class="dropdown-item" href="<?= url('teams-system') ?>"><i class="fa fa-fw fa-sm fa-user-shield mr-2"></i> <?= l('teams_system.menu') ?></a>
                                    <?php endif ?>
                                <?php endif ?>

                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="<?= url('logout') ?>"><i class="fa fa-fw fa-sm fa-sign-out-alt mr-2"></i> <?= l('global.menu.logout') ?></a>
                            </div>
                        </li>

                    <?php else : ?>

                        <li class="nav-item">
                            <a class="nav-link" href="<?= url('login') ?>"><i class="fa fa-fw fa-sm fa-sign-in-alt"></i> <?= l('login.menu') ?></a>
                        </li>

                        <?php if (settings()->users->register_is_enabled) : ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= url('register') ?>"><i class="fa fa-fw fa-sm fa-plus"></i> <?= l('register.menu') ?></a>
                            </li>
                        <?php endif ?>

                    <?php endif ?>
                    <li class="nav-item">
                        <a class="nav-link nav-btn" href="#"><?= l('request_a_demo') ?></a>
                    </li>
                    <li class="nav-item">
                        <?php if (count(\Altum\ThemeStyle::$themes) > 1) : ?>
                            <button type="button" id="switch_theme_style" class="nav-link btn btn-link text-decoration-none switch_theme_style" data-toggle="tooltip" title="<?= sprintf(l('global.theme_style'), (\Altum\ThemeStyle::get() == 'light' ? l('global.theme_style_dark') : l('global.theme_style_light'))) ?>" data-title-theme-style-light="<?= sprintf(l('global.theme_style'), l('global.theme_style_light')) ?>" data-title-theme-style-dark="<?= sprintf(l('global.theme_style'), l('global.theme_style_dark')) ?>">
                                <span data-theme-style="light" class="<?= \Altum\ThemeStyle::get() == 'light' ? null : 'd-none' ?>"><i class="fa fa-fw fa-sm fa-sun mr-1"></i></span>
                                <span data-theme-style="dark" class="<?= \Altum\ThemeStyle::get() == 'dark' ? null : 'd-none' ?>"><i class="fa fa-fw fa-sm fa-moon mr-1"></i></span>
                            </button>
                        <?php endif ?>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>

<div class="modal fade" id="nfc_support_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title"><?= l('nfc_support_modal.header') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= l('global.close') ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <form name="nfc_support" method="post" role="form" action="<?= SITE_URL . 'nfc-support' ?>">
                    <div class="notification-container"></div>

                    <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" required="required" />

                    <div class="form-group">
                        <label for="name"><i class="fa fa-fw fa-signature fa-sm text-muted mr-1"></i> <?= l('nfc_support.input.name') ?></label>
                        <input type="text" id="name" class="form-control" name="name" required="required" />
                    </div>

                    <div class="form-group">
                        <label for="email"><i class="fa fa-fw fa-signature fa-sm text-muted mr-1"></i> <?= l('nfc_support.input.email') ?></label>
                        <input type="text" id="email" class="form-control" name="email" required="required" />
                    </div>

                    <div class="form-group">
                        <label for="shipping_address"><i class="fa fa-fw fa-signature fa-sm text-muted mr-1"></i> <?= l('nfc_support.input.shipping_address') ?></label>
                        <input type="text" id="shipping_address" class="form-control" name="shipping_address" required="required" />
                    </div>

                    <div class="form-group">
                        <label for="replacement_reason"><i class="fa fa-fw fa-signature fa-sm text-muted mr-1"></i> <?= l('nfc_support.input.replacement_reason') ?></label>
                        <textarea id="replacement_reason" name="replacement_reason" class="form-control"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="product_link"><i class="fa fa-fw fa-signature fa-sm text-muted mr-1"></i> <?= l('nfc_support.input.product_link') ?></label>
                        <input type="text" id="shipping_address" class="form-control" name="product_link" required="required" />
                    </div>

                    <div class="form-group">
                        <label for="product_description"><i class="fa fa-fw fa-signature fa-sm text-muted mr-1"></i> <?= l('nfc_support.input.product_description') ?></label>
                        <textarea id="product_description" name="product_description" class="form-control"></textarea>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" name="submit" class="btn btn-block btn-primary" data-is-ajax><?= l('global.send') ?></button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>