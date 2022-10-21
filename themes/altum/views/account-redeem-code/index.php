<?php defined('ALTUMCODE') || die() ?>

<div class="container">
    <?= \Altum\Alerts::output_alerts() ?>

    <?= $this->views['account_header_menu'] ?>

    <h1 class="h4"><?= l('account_redeem_code.header') ?></h1>
    <p class="text-muted"><?= l('account_redeem_code.subheader') ?></p>

    <div class="card">
        <div class="card-body">

            <form action="" method="post" role="form">
                <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" />

                <div class="form-group">
                    <label for="plan_id"><?= l('account_redeem_code.plan_id') ?></label>
                    <select id="plan_id" name="plan_id" class="form-control">
                        <?php foreach($data->plans as $plan): ?>
                            <option value="<?= $plan->plan_id ?>" <?= $data->values['plan_id'] == $plan->plan_id ? 'selected="selected"' : null ?>><?= $plan->name ?></option>
                        <?php endforeach ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="code"><?= l('account_redeem_code.code') ?></label>
                    <input type="text" id="code" name="code" class="form-control <?= \Altum\Alerts::has_field_errors('code') ? 'is-invalid' : null ?>" value="<?= $data->values['code'] ?>" />
                    <?= \Altum\Alerts::output_field_error('code') ?>
                </div>

                <div class="alert alert-info">
                    <?= l('account_redeem_code.info_message') ?>
                </div>

                <button type="submit" name="submit" class="btn btn-block btn-primary"><?= l('global.submit') ?></button>
            </form>

        </div>
    </div>
</div>
