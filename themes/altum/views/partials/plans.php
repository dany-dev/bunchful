<?php defined('ALTUMCODE') || die() ?>

<?php if (settings()->payment->is_enabled) : ?>

    <?php
    $plans = [];
    $available_payment_frequencies = [];

    $plans_result = database()->query("SELECT * FROM `plans` WHERE `status` = 1 ORDER BY `order`");

    while ($plan = $plans_result->fetch_object()) {
        $plans[] = $plan;

        foreach (['monthly', 'annual', 'lifetime'] as $value) {
            if ($plan->{$value . '_price'}) {
                $available_payment_frequencies[$value] = true;
            }
        }
    }
    ?>


    <?php if (count($plans)) : ?>
        <div class="plans-list mb-5 d-flex justify-content-center">
            <div class="plans-bg">
                <div class="btn-group-toggle" data-toggle="buttons">
                    <?php if (isset($available_payment_frequencies['monthly'])) : ?>
                        <label class="btn btn-light active" data-payment-frequency="monthly">
                            <input type="radio" name="payment_frequency" checked="checked"> <?= l('plan.custom_plan.monthly') ?>
                        </label>
                    <?php endif ?>

                    <?php if (isset($available_payment_frequencies['annual'])) : ?>
                        <label class="btn btn-light <?= !isset($available_payment_frequencies['monthly']) ? 'active' : null ?>" data-payment-frequency="annual">
                            <input type="radio" name="payment_frequency" <?= !isset($available_payment_frequencies['monthly']) ? 'checked="checked"' : null ?>> <?= l('plan.custom_plan.annual') ?>
                        </label>
                    <?php endif ?>

                    <?php if (isset($available_payment_frequencies['lifetime'])) : ?>
                        <label class="btn btn-light <?= !isset($available_payment_frequencies['monthly']) && !isset($available_payment_frequencies['annual']) ? 'active' : null ?>" data-payment-frequency="lifetime">
                            <input type="radio" name="payment_frequency" <?= !isset($available_payment_frequencies['monthly']) && !isset($available_payment_frequencies['annual']) ? 'checked="checked"' : null ?>> <?= l('plan.custom_plan.lifetime') ?>
                        </label>
                    <?php endif ?>
                </div>
            </div>
        </div>
    <?php endif ?>
<?php endif ?>

<div>
    <div class="row justify-content-around">
        <?php if (settings()->payment->is_enabled) : ?>
            <div class="pricingtablecontainer">
                <?php if (settings()->plan_free->status == 1) : ?>
                    <div class="pricingtable">
                        <div class="accordion" id="packageFree">
                            <div id="package0">
                                <ul class="silver">
                                    <li class="pricingtable__head"><?= settings()->plan_free->name ?></li>
                                    <li class="pricingtable__highlight js-montlypricing" style="width: 100%;" data-plan-payment-frequency="monthly"><?= settings()->plan_free->price ?></li>
                                    <li class="pricingtable__highlight js-yearlypricing" style="width: 100%;" data-plan-payment-frequency="annual"><?= settings()->plan_free->price ?></li>
                                    <li class="pricing-details"><?= settings()->plan_free->description ?></li>
                                </ul>
                                <hr>
                                <span class="see-more collapsed" type="button" data-toggle="collapse" data-target="#packageCollapseFree" aria-expanded="false" aria-controls="packageCollapseFree">
                                    <i class="fa fa-play-circle" data-toggle="tooltip" data-placement="bottom" title="Show More"></i>
                                </span>
                            </div>
                            <div id="packageCollapseFree" class="collapse" aria-labelledby="package0" data-parent="#packageFree">
                                <div>
                                    <ul class="silver">
                                        <?php if(isset($plan->settings)) $plan->settings = json_decode($plan->settings) ?>
                                        <?= include_view(THEME_PATH . 'views/partials/plans_plan_content.php', ['plan_settings' => settings()->plan_free->settings]) ?>
                                        <li class="pricingtable__btn">
                                            <a href="<?= url('register') ?>" class="<?= \Altum\Middlewares\Authentication::check() && $this->user->plan_id != 'free' ? 'disabled' : null ?>"><?= l('plan.button.choose') ?></a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif ?>

                <?php foreach ($plans as $key => $plan) : ?>
                    <div class="pricingtable">
                        <div class="accordion" id="packageOne">
                            <div id="package1">
                                <ul class="silver">
                                    <li class="pricingtable__head"><?= $plan->name ?></li>
                                    <li class="pricingtable__highlight js-montlypricing" style="width: 100%;" data-plan-payment-frequency="monthly"><?= $plan->monthly_d_price ? '<span class="original_price">' . $plan->monthly_d_price . '<span class="pricing-price-currency">'. settings()->payment->currency .'</span></span>' : '' ?><?= $plan->monthly_price ?><span class="pricing-price-currency"><?= settings()->payment->currency ?></span></li>
                                    <li class="pricingtable__highlight js-yearlypricing" style="width: 100%;" data-plan-payment-frequency="annual"><?= $plan->annual_d_price ? '<span class="original_price">' . $plan->annual_d_price . '<span class="pricing-price-currency">'. settings()->payment->currency .'</span></span>' : '' ?><?= $plan->annual_price ?><span class="pricing-price-currency"><?= settings()->payment->currency ?></span></li>
                                    <li class="pricingtable__highlight js-yearlypricing" style="width: 100%;" data-plan-payment-frequency="lifetime"><?= $plan->lifetime_d_price ? '<span class="original_price">' . $plan->lifetime_d_price . '<span class="pricing-price-currency">'. settings()->payment->currency .'</span></span>': '' ?><?= $plan->annual_price ?><span class="pricing-price-currency"><?= settings()->payment->currency ?></span></li>
                                    <li class="pricing-details"><?= $plan->description ?></li>
                                </ul>
                                <hr>
                                <span class="see-more collapsed" type="button" data-toggle="collapse" data-target="#packageCollapseOne" aria-expanded="false" aria-controls="packageCollapseOne">
                                    <i class="fa fa-play-circle" data-toggle="tooltip" data-placement="bottom" title="Show More"></i>
                                </span>
                            </div>
                            <div id="packageCollapseOne" class="collapse" aria-labelledby="package1" data-parent="#packageOne">
                                <div>
                                    <ul class="silver">
                                        <?php $plan->settings = json_decode($plan->settings) ?>
                                        <?= include_view(THEME_PATH . 'views/partials/plans_plan_content.php', ['plan_settings' => $plan->settings]) ?>
                                        <li class="pricingtable__btn">
                                            <a href="<?= url('register?redirect=pay/' . $plan->plan_id) ?>" class="">
                                                <?php if (\Altum\Middlewares\Authentication::check()) : ?>
                                                    <?php if (!$this->user->plan_trial_done && $plan->trial_days) : ?>
                                                        <?= sprintf(l('plan.button.trial'), $plan->trial_days) ?>
                                                    <?php elseif ($this->user->plan_id == $plan->plan_id) : ?>
                                                        <?= l('plan.button.renew') ?>
                                                    <?php else : ?>
                                                        <?= l('plan.button.choose') ?>
                                                    <?php endif ?>
                                                <?php else : ?>
                                                    <?php if ($plan->trial_days) : ?>
                                                        <?= sprintf(l('plan.button.trial'), $plan->trial_days) ?>
                                                    <?php else : ?>
                                                        <?= l('plan.button.choose') ?>
                                                    <?php endif ?>
                                                <?php endif ?>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>
                <?php if (settings()->plan_custom->status == 1) : ?>
                    <div class="pricingtable">
                        <div class="accordion" id="packageCustom">
                            <div id="packageC">
                                <ul class="silver">
                                    <li class="pricingtable__head"><?= settings()->plan_custom->name ?></li>
                                    <li class="pricingtable__highlight js-montlypricing" style="width: 100%;" data-plan-payment-frequency="monthly"><?= settings()->plan_custom->price ?></li>
                                    <li class="pricingtable__highlight js-yearlypricing" style="width: 100%;" data-plan-payment-frequency="annual"><?= settings()->plan_custom->price ?></li>
                                    <li class="pricing-details"><?= settings()->plan_custom->description ?></li>
                                </ul>
                                <hr>
                                <span class="see-more collapsed" type="button" data-toggle="collapse" data-target="#packageCollapseCustom" aria-expanded="false" aria-controls="packageCollapseCustom">
                                    <i class="fa fa-play-circle" data-toggle="tooltip" data-placement="bottom" title="Show More"></i>
                                </span>
                            </div>
                            <div id="packageCollapseCustom" class="collapse" aria-labelledby="packageC" data-parent="#packageCustom">
                                <div>
                                    <ul class="silver">
                                        <?= include_view(THEME_PATH . 'views/partials/plans_plan_content.php', ['plan_settings' => settings()->plan_custom->settings]) ?>
                                        <li class="pricingtable__btn">
                                            <a href="<?= settings()->plan_custom->custom_button_url ?>" class=""><?= l('plan.button.contact') ?></a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif ?>
            </div>

            <?php ob_start() ?>
            <script>
                'use strict';

                let payment_frequency_handler = (event = null) => {

                    let payment_frequency = null;

                    if (event) {
                        payment_frequency = $(event.currentTarget).data('payment-frequency');
                    } else {
                        payment_frequency = $('[name="payment_frequency"]:checked').closest('label').data('payment-frequency');
                    }

                    switch (payment_frequency) {
                        case 'monthly':
                            $(`[data-plan-payment-frequency="annual"]`).removeClass('d-inline-block').addClass('d-none');
                            $(`[data-plan-payment-frequency="lifetime"]`).removeClass('d-inline-block').addClass('d-none');

                            break;

                        case 'annual':
                            $(`[data-plan-payment-frequency="monthly"]`).removeClass('d-inline-block').addClass('d-none');
                            $(`[data-plan-payment-frequency="lifetime"]`).removeClass('d-inline-block').addClass('d-none');

                            break

                        case 'lifetime':
                            $(`[data-plan-payment-frequency="monthly"]`).removeClass('d-inline-block').addClass('d-none');
                            $(`[data-plan-payment-frequency="annual"]`).removeClass('d-inline-block').addClass('d-none');

                            break
                    }

                    $(`[data-plan-payment-frequency="${payment_frequency}"]`).addClass('d-inline-block');

                    $(`[data-plan-${payment_frequency}="true"]`).removeClass('d-none').addClass('');
                    $(`[data-plan-${payment_frequency}="false"]`).addClass('d-none').removeClass('');

                };

                $('[data-payment-frequency]').on('click', payment_frequency_handler);

                payment_frequency_handler();
            </script>
            <?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
        <?php endif ?>
    </div>
</div>