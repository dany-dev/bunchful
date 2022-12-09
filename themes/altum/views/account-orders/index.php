<?php defined('ALTUMCODE') || die() ?>

<div class="container">
    <?= \Altum\Alerts::output_alerts() ?>

    <?= $this->views['account_header_menu'] ?>

    <div class="row mb-3">
        <div class="col-12 col-xl mb-3 mb-xl-0">
            <h1 class="h4"><?= l('account_orders.header') ?></h1>
            <p class="text-muted m-0"><?= l('account_orders.subheader') ?></p>
        </div>
    </div>

    <?php if (count($data->orders)) : ?>
        <div class="table-responsive table-custom-container">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th><?= l('account_orders.payments.customer') ?></th>
                        <th><?= l('account_orders.payments.catalog') ?></th>
                        <th><?= l('account_orders.payments.type') ?></th>
                        <th><?= l('account_orders.payments.total_amount') ?></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>

                    <?php foreach ($data->orders as $row) : ?>

                        <tr>
                            <td class="text-nowrap">
                                <div class="d-flex flex-column">
                                    <span><?= $row->email ?></span>
                                    <span class="text-muted"><?= $row->name ?></span>
                                </div>
                            </td>

                            <td class="text-nowrap"><?= $row->customerName ?></td>

                            <td class="text-nowrap">
                                <div class="d-flex flex-column">
                                    <span><?= l('pay.custom_plan.' . $row->type . '_type') ?></span>
                                    <span class="text-muted"><?= l('pay.custom_plan.' . $row->processor) ?></span>
                                </div>
                            </td>

                            <td class="text-nowrap">
                                <div class="d-flex flex-column">
                                    <span><span class="text-success"><?= $row->total_amount ?></span> <?= $row->currency ?></span>
                                    <span class="text-muted"><span data-toggle="tooltip" title="<?= \Altum\Date::get($row->datetime, 1) ?>"><?= \Altum\Date::get($row->datetime, 2) ?></span></span>
                                </div>
                            </td>

                            <td class="text-nowrap">
                                <div class="d-flex flex-column">
                                    <span class="text-success"><?= $row->status ?></span>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach ?>

                </tbody>
            </table>
        </div>

        <div class="mt-3"><?= $data->pagination ?></div>
    <?php else : ?>
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-column align-items-center justify-content-center py-3">
                    <img src="<?= ASSETS_FULL_URL . 'images/no_rows.svg' ?>" class="col-10 col-md-7 col-lg-4 mb-3" alt="<?= l('account_orders.payments.no_data') ?>" />
                    <h2 class="h4 text-muted"><?= l('account_orders.payments.no_data') ?></h2>
                </div>
            </div>
        </div>
    <?php endif ?>

</div>