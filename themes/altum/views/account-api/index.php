<?php defined('ALTUMCODE') || die() ?>

<div class="container">
    <?= \Altum\Alerts::output_alerts() ?>

    <?= $this->views['account_header_menu'] ?>

    <h1 class="h4"><?= l('account_api.header') ?></h1>
    <p class="text-muted"><?= sprintf(l('account_api.subheader'), '<a href="' . url('api-documentation') . '">', '</a>') ?></p>

    <div class="card">
        <div class="card-body">

            <form action="" method="post" role="form">
                <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" />

                <div <?= $this->user->plan_settings->api_is_enabled ? null : 'data-toggle="tooltip" title="' . l('global.info_message.plan_feature_no_access') . '"' ?>>
                    <div class="form-group <?= $this->user->plan_settings->api_is_enabled ? null : 'container-disabled' ?>">
                        <label for="api_key"><?= l('account_api.api_key') ?></label>
                        <input type="text" id="api_key" name="api_key" value="<?= $this->user->api_key ?>" class="form-control" readonly="readonly" />
                    </div>
                </div>

                <button type="submit" name="submit" class="btn btn-block btn-outline-secondary"><?= l('account_api.button') ?></button>
            </form>

        </div>
    </div>
</div>
