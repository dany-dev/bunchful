<?php defined('ALTUMCODE') || die() ?>

<section class="container">
    <?= \Altum\Alerts::output_alerts() ?>

    <div class="row mb-4">
        <div class="col-12 col-lg d-flex align-items-center mb-3 mb-lg-0">
            <h1 class="h4 m-0"><?= l('projects.header') ?></h1>

            <div class="ml-2">
                <span data-toggle="tooltip" title="<?= l('projects.subheader') ?>">
                    <i class="fa fa-fw fa-info-circle text-muted"></i>
                </span>
            </div>
        </div>

        <div class="col-12 col-lg-auto d-flex">
            <div>
                <?php if($this->user->plan_settings->projects_limit != -1 && $data->total_projects >= $this->user->plan_settings->projects_limit): ?>
                    <button type="button" data-toggle="tooltip" title="<?= l('global.info_message.plan_feature_limit') ?>" class="btn btn-primary disabled">
                        <i class="fa fa-fw fa-plus-circle"></i> <?= l('projects.create') ?>
                    </button>
                <?php else: ?>
                    <button type="button" data-toggle="modal" data-target="#create_project_modal" class="btn btn-primary"><i class="fa fa-fw fa-plus-circle"></i> <?= l('projects.create') ?></button>
                <?php endif ?>
            </div>

            <div class="ml-3">
                <div class="dropdown">
                    <button type="button" class="btn btn-outline-secondary dropdown-toggle-simple" data-toggle="dropdown" data-boundary="viewport" title="<?= l('global.export') ?>">
                        <i class="fa fa-fw fa-sm fa-download"></i>
                    </button>

                    <div class="dropdown-menu dropdown-menu-right d-print-none">
                        <a href="<?= url('projects?' . $data->filters->get_get() . '&export=csv')  ?>" target="_blank" class="dropdown-item">
                            <i class="fa fa-fw fa-sm fa-file-csv mr-1"></i> <?= l('global.export_csv') ?>
                        </a>
                        <a href="<?= url('projects?' . $data->filters->get_get() . '&export=json') ?>" target="_blank" class="dropdown-item">
                            <i class="fa fa-fw fa-sm fa-file-code mr-1"></i> <?= l('global.export_json') ?>
                        </a>
                    </div>
                </div>
            </div>

            <div class="ml-3">
                <div class="dropdown">
                    <button type="button" class="btn <?= count($data->filters->get) ? 'btn-outline-primary' : 'btn-outline-secondary' ?> filters-button dropdown-toggle-simple" data-toggle="dropdown" data-boundary="viewport" title="<?= l('global.filters.header') ?>">
                        <i class="fa fa-fw fa-sm fa-filter"></i>
                    </button>

                    <div class="dropdown-menu dropdown-menu-right filters-dropdown">
                        <div class="dropdown-header d-flex justify-content-between">
                            <span class="h6 m-0"><?= l('global.filters.header') ?></span>

                            <?php if(count($data->filters->get)): ?>
                                <a href="<?= url('projects') ?>" class="text-muted"><?= l('global.filters.reset') ?></a>
                            <?php endif ?>
                        </div>

                        <div class="dropdown-divider"></div>

                        <form action="" method="get" role="form">
                            <div class="form-group px-4">
                                <label for="filters_search" class="small"><?= l('global.filters.search') ?></label>
                                <input type="search" name="search" id="filters_search" class="form-control form-control-sm" value="<?= $data->filters->search ?>" />
                            </div>

                            <div class="form-group px-4">
                                <label for="filters_search_by" class="small"><?= l('global.filters.search_by') ?></label>
                                <select name="search_by" id="filters_search_by" class="form-control form-control-sm">
                                    <option value="name" <?= $data->filters->search_by == 'name' ? 'selected="selected"' : null ?>><?= l('projects.input.name') ?></option>
                                </select>
                            </div>

                            <div class="form-group px-4">
                                <label for="filters_order_by" class="small"><?= l('global.filters.order_by') ?></label>
                                <select name="order_by" id="filters_order_by" class="form-control form-control-sm">
                                    <option value="datetime" <?= $data->filters->order_by == 'datetime' ? 'selected="selected"' : null ?>><?= l('global.filters.order_by_datetime') ?></option>
                                    <option value="name" <?= $data->filters->order_by == 'name' ? 'selected="selected"' : null ?>><?= l('projects.input.name') ?></option>
                                </select>
                            </div>

                            <div class="form-group px-4">
                                <label for="filters_order_type" class="small"><?= l('global.filters.order_type') ?></label>
                                <select name="order_type" id="filters_order_type" class="form-control form-control-sm">
                                    <option value="ASC" <?= $data->filters->order_type == 'ASC' ? 'selected="selected"' : null ?>><?= l('global.filters.order_type_asc') ?></option>
                                    <option value="DESC" <?= $data->filters->order_type == 'DESC' ? 'selected="selected"' : null ?>><?= l('global.filters.order_type_desc') ?></option>
                                </select>
                            </div>

                            <div class="form-group px-4">
                                <label for="filters_results_per_page" class="small"><?= l('global.filters.results_per_page') ?></label>
                                <select name="results_per_page" id="filters_results_per_page" class="form-control form-control-sm">
                                    <?php foreach($data->filters->allowed_results_per_page as $key): ?>
                                        <option value="<?= $key ?>" <?= $data->filters->results_per_page == $key ? 'selected="selected"' : null ?>><?= $key ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>

                            <div class="form-group px-4 mt-4">
                                <button type="submit" name="submit" class="btn btn-sm btn-primary btn-block"><?= l('global.submit') ?></button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if(count($data->projects)): ?>

        <?php foreach($data->projects as $row): ?>
            <div class="custom-row mb-4" data-project-id="<?= $row->project_id ?>">
                <div class="row">
                    <div class="col-4 col-lg-3 d-flex align-items-center">
                        <div class="font-weight-bold text-truncate">
                            <a href="#" data-toggle="modal" data-target="#project_update_modal" data-project-id="<?= $row->project_id ?>" data-name="<?= $row->name ?>" data-color="<?= $row->color ?>"><?= $row->name ?></a>
                        </div>
                    </div>

                    <div class="col-2 col-lg-2 d-flex align-items-center">
                        <span class="py-1 px-2 border rounded text-muted small" style="border-color: <?= $row->color ?> !important;">
                            <?= $row->color ?>
                        </span>
                    </div>

                    <div class="col-4 col-lg-3 d-flex flex-column flex-lg-row align-items-center">
                        <?php if(settings()->links->shortener_is_enabled): ?>
                        <a href="<?= url('links?type=link&project_id=' . $row->project_id) ?>" class="mr-2" data-toggle="tooltip" title="<?= l('projects.links') ?>">
                            <i class="fa fa-fw fa-link text-muted"></i>
                        </a>
                        <?php endif ?>

                        <a href="<?= url('links?type=biolink&project_id=' . $row->project_id) ?>" class="mr-2" data-toggle="tooltip" title="<?= l('projects.biolinks') ?>">
                            <i class="fa fa-fw fa-hashtag text-muted"></i>
                        </a>

                        <a href="<?= url('data?project_id=' . $row->project_id) ?>" class="mr-2" data-toggle="tooltip" title="<?= l('projects.data') ?>">
                            <i class="fa fa-fw fa-database text-muted"></i>
                        </a>

                        <a href="<?= url('qr-codes?project_id=' . $row->project_id) ?>" class="mr-2" data-toggle="tooltip" title="<?= l('projects.qr_codes') ?>">
                            <i class="fa fa-fw fa-qrcode text-muted"></i>
                        </a>

                        <?php if(\Altum\Plugin::is_active('payment-blocks')): ?>
                            <a href="<?= url('guests-payments?project_id=' . $row->project_id) ?>" class="mr-2" data-toggle="tooltip" title="<?= l('projects.guests_payments') ?>">
                                <i class="fa fa-fw fa-coins text-muted"></i>
                            </a>
                        <?php endif ?>
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
                                <a href="#" data-toggle="modal" data-target="#project_update_modal" data-project-id="<?= $row->project_id ?>" data-name="<?= $row->name ?>" data-color="<?= $row->color ?>" class="dropdown-item"><i class="fa fa-fw fa-sm fa-pencil-alt mr-2"></i> <?= l('global.edit') ?></a>
                                <a href="#" data-toggle="modal" data-target="#project_delete_modal" data-project-id="<?= $row->project_id ?>" data-resource-name="<?= $row->name ?>" class="dropdown-item"><i class="fa fa-fw fa-sm fa-trash-alt mr-2"></i> <?= l('global.delete') ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach ?>

        <div class="mt-3"><?= $data->pagination ?></div>

    <?php else: ?>
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-column align-items-center justify-content-center py-3">
                    <img src="<?= ASSETS_FULL_URL . 'images/no_rows.svg' ?>" class="col-10 col-md-7 col-lg-4 mb-3" alt="<?= l('projects.no_data') ?>" />
                    <h2 class="h4 text-muted"><?= l('projects.no_data') ?></h2>
                </div>
            </div>
        </div>
    <?php endif ?>

</section>

<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/projects/project_create_modal.php'), 'modals'); ?>
<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/projects/project_update_modal.php'), 'modals'); ?>
<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/partials/universal_delete_modal_form.php', [
    'name' => 'project',
    'resource_id' => 'project_id',
    'has_dynamic_resource_name' => true,
    'path' => 'projects/delete'
]), 'modals'); ?>
