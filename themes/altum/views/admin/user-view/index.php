<?php defined('ALTUMCODE') || die() ?>

<nav aria-label="breadcrumb">
    <ol class="custom-breadcrumbs small">
        <li>
            <a href="<?= url('admin/users') ?>"><?= l('admin_users.breadcrumb') ?></a><i class="fa fa-fw fa-angle-right"></i>
        </li>
        <li class="active" aria-current="page"><?= l('admin_user_view.breadcrumb') ?></li>
    </ol>
</nav>

<div class="d-flex justify-content-between mb-4">
    <h1 class="h3 mb-0 text-truncate"><i class="fa fa-fw fa-xs fa-user text-primary-900 mr-2"></i> <?= l('admin_user_view.header') ?></h1>

    <?= include_view(THEME_PATH . 'views/admin/users/admin_user_dropdown_button.php', ['id' => $data->user->user_id, 'resource_name' => $data->user->name]) ?>
</div>

<?= \Altum\Alerts::output_alerts() ?>

<?php //ALTUMCODE:DEMO if(DEMO) {$data->user->email = 'hidden@demo.com'; $data->user->name = $data->user->ip = 'hidden on demo';} ?>

<div class="card">
    <div class="card-body">
        <div class="row">

            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label for="type" class="font-weight-bold"><?= l('admin_users.main.type') ?></label>
                    <input id="type" type="text" class="form-control-plaintext" value="<?= $data->user->type ? l('admin_users.main.type_admin') : l('admin_users.main.type_user') ?>" readonly />
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label for="email" class="font-weight-bold"><?= l('admin_users.main.email') ?></label>
                    <input id="email" type="text" class="form-control-plaintext" value="<?= $data->user->email ?>" readonly />
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label for="name" class="font-weight-bold"><?= l('admin_users.main.name') ?></label>
                    <input id="name" type="text" class="form-control-plaintext" value="<?= $data->user->name ?>" readonly />
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label for="active" class="font-weight-bold"><?= l('admin_users.main.status') ?></label>
                    <input id="active" type="text" class="form-control-plaintext" value="<?php if($data->user->status == 1) echo l('admin_users.main.status_active'); elseif($data->user->status == 0) echo l('admin_users.main.status_unconfirmed'); elseif($data->user->status == 2) echo l('admin_users.main.status_disabled') ?>" readonly />
                </div>

            </div>
            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label for="api_key" class="font-weight-bold"><?= l('admin_users.main.api_key') ?></label>
                    <input id="api_key" type="text" class="form-control-plaintext" value="<?= $data->user->api_key ?>" readonly />
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label for="ip" class="font-weight-bold"><?= l('admin_users.main.ip') ?></label>
                    <input id="ip" type="text" class="form-control-plaintext" value="<?= $data->user->ip ?>" readonly />
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label for="country" class="font-weight-bold"><?= l('admin_users.main.country') ?></label>
                    <input id="country" type="text" class="form-control-plaintext" value="<?= $data->user->country ?>" readonly />
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label for="last_activity" class="font-weight-bold"><?= l('admin_users.main.last_activity') ?></label>
                    <input id="last_activity" type="text" class="form-control-plaintext" value="<?= $data->user->last_activity ? \Altum\Date::get($data->user->last_activity, 1) : '-' ?>" readonly />
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label for="last_user_agent" class="font-weight-bold"><?= l('admin_users.main.last_user_agent') ?></label>
                    <input id="last_user_agent" type="text" class="form-control-plaintext" value="<?= $data->user->last_user_agent ?>" readonly />
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label class="font-weight-bold"><?= l('admin_users.main.plan_id') ?></label>
                    <div>
                        <a href="<?= url('admin/plan-update/' . $data->user->plan->plan_id) ?>"><?= $data->user->plan->name ?></a>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label for="plan_expiration_date" class="font-weight-bold"><?= l('admin_users.main.plan_expiration_date') ?></label>
                    <input id="plan_expiration_date" type="text" class="form-control-plaintext" value="<?= \Altum\Date::get($data->user->plan_expiration_date, 1) ?>" readonly />
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label for="total_logins" class="font-weight-bold"><?= l('admin_users.main.total_logins') ?></label>
                    <input id="total_logins" type="text" class="form-control-plaintext" value="<?= $data->user->total_logins ?>" readonly />
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label for="language" class="font-weight-bold"><?= l('admin_users.main.language') ?></label>
                    <input id="language" type="text" class="form-control-plaintext" value="<?= $data->user->language ?>" readonly />
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label for="timezone" class="font-weight-bold"><?= l('admin_users.main.timezone') ?></label>
                    <input id="timezone" type="text" class="form-control-plaintext" value="<?= $data->user->timezone ?>" readonly />
                </div>
            </div>

            <?php if(in_array(settings()->license->type, ['Extended License', 'extended'])): ?>
                <div class="col-12 col-md-4">
                    <div class="form-group">
                        <label for="payment_processor" class="font-weight-bold"><?= l('admin_users.main.payment_processor') ?></label>
                        <input id="payment_processor" type="text" class="form-control-plaintext" value="<?= $data->user->payment_processor ?>" readonly />
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="form-group">
                        <label for="payment_total_amount" class="font-weight-bold"><?= l('admin_users.main.payment_total_amount') ?></label>
                        <input id="payment_total_amount" type="text" class="form-control-plaintext" value="<?= nr($data->user->payment_total_amount, 2) . ' ' . $data->user->payment_currency ?>" readonly />
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="form-group">
                        <label for="payment_subscription_id" class="font-weight-bold"><?= l('admin_users.main.payment_subscription_id') ?></label>
                        <input id="payment_subscription_id" type="text" class="form-control-plaintext" value="<?= $data->user->payment_subscription_id ?>" readonly />
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="form-group">
                        <label for="plan_trial_done" class="font-weight-bold"><?= l('admin_users.main.plan_trial_done') ?></label>
                        <input id="plan_trial_done" type="text" class="form-control-plaintext" value="<?= $data->user->plan_trial_done ? l('global.yes') : l('global.no') ?>" readonly />
                    </div>
                </div>

                <?php if(\Altum\Plugin::is_active('affiliate') && settings()->affiliate->is_enabled): ?>
                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label for="referral_key" class="font-weight-bold"><?= l('admin_users.main.referral_key') ?></label>
                            <input id="referral_key" type="text" class="form-control-plaintext" value="<?= $data->user->referral_key ?>" readonly />
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label class="font-weight-bold"><?= l('admin_users.main.referred_by') ?></label>
                            <div>
                                <a href="<?= url('admin/user-view/' . $data->user->referred_by) ?>"><?= $data->user->referred_by ?></a>
                            </div>
                        </div>
                    </div>
                <?php endif ?>
            <?php endif ?>

        </div>
    </div>
</div>

<div class="my-5 row justify-content-between">
    <div class="col-12 col-sm-6 col-xl-3 mb-4 position-relative">
        <div class="card d-flex flex-row h-100 overflow-hidden">
            <div class="card-body">
                <small class="text-muted"><i class="fa fa-fw fa-sm fa-hashtag mr-1"></i> <?= l('admin_user_view.biolink_links') ?></small>

                <div class="mt-3"><span class="h4"><?= nr($data->biolink_links) ?></span></div>
            </div>

            <div class="bg-gray-200 px-2 d-flex flex-column justify-content-center">
                <a href="<?= url('admin/links?type=biolink&user_id=' . $data->user->user_id) ?>" class="stretched-link">
                    <i class="fa fa-fw fa-angle-right text-gray-500"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3 mb-4 position-relative">
        <div class="card d-flex flex-row h-100 overflow-hidden">
            <div class="card-body">
                <small class="text-muted"><i class="fa fa-fw fa-sm fa-link mr-1"></i> <?= l('admin_user_view.shortened_links') ?></small>

                <div class="mt-3"><span class="h4"><?= nr($data->shortened_links) ?></span></div>
            </div>

            <div class="bg-gray-200 px-2 d-flex flex-column justify-content-center">
                <a href="<?= url('admin/links?type=link&user_id=' . $data->user->user_id) ?>" class="stretched-link">
                    <i class="fa fa-fw fa-angle-right text-gray-500"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3 mb-4 position-relative">
        <div class="card d-flex flex-row h-100 overflow-hidden">
            <div class="card-body">
                <small class="text-muted"><i class="fa fa-fw fa-sm fa-project-diagram mr-1"></i> <?= l('admin_user_view.projects') ?></small>

                <div class="mt-3"><span class="h4"><?= nr($data->projects) ?></span></div>
            </div>

            <div class="bg-gray-200 px-2 d-flex flex-column justify-content-center">
                <a href="<?= url('admin/projects?user_id=' . $data->user->user_id) ?>" class="stretched-link">
                    <i class="fa fa-fw fa-angle-right text-gray-500"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3 mb-4 position-relative">
        <div class="card d-flex flex-row h-100 overflow-hidden">
            <div class="card-body">
                <small class="text-muted"><i class="fa fa-fw fa-sm fa-adjust mr-1"></i> <?= l('admin_user_view.pixels') ?></small>

                <div class="mt-3"><span class="h4"><?= nr($data->pixels) ?></span></div>
            </div>

            <div class="bg-gray-200 px-2 d-flex flex-column justify-content-center">
                <a href="<?= url('admin/pixels?user_id=' . $data->user->user_id) ?>" class="stretched-link">
                    <i class="fa fa-fw fa-angle-right text-gray-500"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3 mb-4 position-relative">
        <div class="card d-flex flex-row h-100 overflow-hidden">
            <div class="card-body">
                <small class="text-muted"><i class="fa fa-fw fa-sm fa-qrcode mr-1"></i> <?= l('admin_user_view.qr_codes') ?></small>

                <div class="mt-3"><span class="h4"><?= nr($data->qr_codes) ?></span></div>
            </div>

            <div class="bg-gray-200 px-2 d-flex flex-column justify-content-center">
                <a href="<?= url('admin/qr-codes?user_id=' . $data->user->user_id) ?>" class="stretched-link">
                    <i class="fa fa-fw fa-angle-right text-gray-500"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3 mb-4 position-relative">
        <div class="card d-flex flex-row h-100 overflow-hidden">
            <div class="card-body">
                <small class="text-muted"><i class="fa fa-fw fa-sm fa-globe mr-1"></i> <?= l('admin_user_view.domains') ?></small>

                <div class="mt-3"><span class="h4"><?= nr($data->domains) ?></span></div>
            </div>

            <div class="bg-gray-200 px-2 d-flex flex-column justify-content-center">
                <a href="<?= url('admin/domains?user_id=' . $data->user->user_id) ?>" class="stretched-link">
                    <i class="fa fa-fw fa-angle-right text-gray-500"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3 mb-4 position-relative">
        <div class="card d-flex flex-row h-100 overflow-hidden">
            <div class="card-body">
                <small class="text-muted"><i class="fa fa-fw fa-sm fa-funnel-dollar mr-1"></i> <?= l('admin_user_view.payments') ?></small>

                <div class="mt-3"><span class="h4"><?= nr($data->payments) ?></span></div>
            </div>

            <div class="bg-gray-200 px-2 d-flex flex-column justify-content-center">
                <a href="<?= url('admin/payments?user_id=' . $data->user->user_id) ?>" class="stretched-link">
                    <i class="fa fa-fw fa-angle-right text-gray-500"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="my-5 row justify-content-between">
    <div class="col-12 col-sm-6 mb-4 position-relative">
        <div class="card d-flex flex-row h-100 overflow-hidden">
            <div class="card-body">
                <span class="text-muted"><i class="fa fa-fw fa-sm fa-scroll mr-1"></i> <?= l('admin_user_view.users_logs') ?></span>
            </div>

            <div class="bg-gray-200 px-2 d-flex flex-column justify-content-center">
                <a href="<?= url('admin/users-logs?user_id=' . $data->user->user_id) ?>" class="stretched-link">
                    <i class="fa fa-fw fa-angle-right text-gray-500"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 mb-4 position-relative">
        <div class="card d-flex flex-row h-100 overflow-hidden">
            <div class="card-body">
                <span class="text-muted"><i class="fa fa-fw fa-sm fa-tags mr-1"></i> <?= l('admin_user_view.redeemed_codes') ?></span>
            </div>

            <div class="bg-gray-200 px-2 d-flex flex-column justify-content-center">
                <a href="<?= url('admin/redeemed-codes?user_id=' . $data->user->user_id) ?>" class="stretched-link">
                    <i class="fa fa-fw fa-angle-right text-gray-500"></i>
                </a>
            </div>
        </div>
    </div>

    <?php if(\Altum\Plugin::is_active('affiliate')): ?>
    <div class="col-12 col-sm-6 mb-4 position-relative">
        <div class="card d-flex flex-row h-100 overflow-hidden">
            <div class="card-body">
                <span class="text-muted"><i class="fa fa-fw fa-sm fa-wallet mr-1"></i> <?= l('admin_user_view.referred_by') ?></span>
            </div>

            <div class="bg-gray-200 px-2 d-flex flex-column justify-content-center">
                <a href="<?= url('admin/users?referred_by=' . $data->user->user_id) ?>" class="stretched-link">
                    <i class="fa fa-fw fa-angle-right text-gray-500"></i>
                </a>
            </div>
        </div>
    </div>
    <?php endif ?>

    <?php if(\Altum\Plugin::is_active('teams')): ?>
        <div class="col-12 col-sm-6 mb-4 position-relative">
            <div class="card d-flex flex-row h-100 overflow-hidden">
                <div class="card-body">
                    <span class="text-muted"><i class="fa fa-fw fa-sm fa-user-shield mr-1"></i> <?= l('admin_user_view.teams') ?></span>
                </div>

                <div class="bg-gray-200 px-2 d-flex flex-column justify-content-center">
                    <a href="<?= url('admin/teams?user_id=' . $data->user->user_id) ?>" class="stretched-link">
                        <i class="fa fa-fw fa-angle-right text-gray-500"></i>
                    </a>
                </div>
            </div>
        </div>
    <?php endif ?>
</div>

<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/admin/users/user_login_modal.php'), 'modals'); ?>
<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/partials/universal_delete_modal_url.php', [
    'name' => 'user',
    'resource_id' => 'user_id',
    'has_dynamic_resource_name' => true,
    'path' => 'admin/users/delete/'
]), 'modals'); ?>
