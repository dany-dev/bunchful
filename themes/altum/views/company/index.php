<?php defined('ALTUMCODE') || die() ?>

<section class="container">
    <?= \Altum\Alerts::output_alerts() ?>

    <div class="row mb-4">
        <div class="col-12 col-lg d-flex align-items-center mb-3 mb-lg-0">
            <h1 class="h4 m-0"><?= l('company.header') ?></h1>

            <div class="ml-2">
                <span data-toggle="tooltip" title="<?= l('company.subheader') ?>">
                    <i class="fa fa-fw fa-info-circle text-muted"></i>
                </span>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <?php if (!$data->isCompanyAdmin) { ?>
                <p class="text-center"><?= l('company.no_company_data') ?></p>
                <div class="text-center">
                    <button type="button" data-toggle="modal" data-target="#create_company_modal" class="btn btn-primary"><i class="fa fa-fw fa-plus-circle"></i> <?= l('company.create') ?></button>
                </div>
            <?php } else { ?>
                <div class="d-flex justify-content-between">
                    <p><?= l('company.employees') ?></p>
                    <div>
                        <button type="button" data-toggle="modal" data-target="#add_employee_modal" class="btn btn-primary"><i class="fa fa-fw fa-plus-circle"></i> <?= l('company.add_employee') ?></button>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <?php if (count($data->companyEmployees)) : ?>
        <?php foreach ($data->companyEmployees as $row) : ?>
            <div class="custom-row mb-4" data-id="<?= $row->id ?>">
                <div class="row">
                    <div class="col-4 col-lg-4 d-flex align-items-center">
                        <div class="font-weight-bold text-truncate">
                            <?php if($row->is_admin) { ?><i class="fa fa-fw fa-user-secret"></i><?php } ?>
                            <a><?= $row->email ?></a>
                        </div>
                    </div>

                    <div class="col-4 col-lg-4 d-flex align-items-center">
                        <a><?= $row->name ?></a>
                    </div>

                    <div class="col-2 col-lg-2 d-none d-lg-flex justify-content-center justify-content-lg-end align-items-center">
                        <small class="text-muted" data-toggle="tooltip" title="<?= \Altum\Date::get($row->created_at, 1) ?>"><i class="fa fa-fw fa-calendar fa-sm mr-1"></i> <span class="align-middle"><?= \Altum\Date::get($row->created_at, 2) ?></span></small>
                    </div>

                    <div class="col-2 col-lg-2 d-flex justify-content-center justify-content-lg-end align-items-center">
                        <div class="dropdown">
                            <button type="button" class="btn btn-link text-secondary dropdown-toggle dropdown-toggle-simple" data-toggle="dropdown" data-boundary="viewport">
                                <i class="fa fa-fw fa-ellipsis-v"></i>
                            </button>

                            <div class="dropdown-menu dropdown-menu-right">
                                <a href="#" data-toggle="modal" data-target="#employee_assign_modal" data-employee-id = "<?= $row->id?>" data-employee-admin = "<?= $row->is_admin?>" class="dropdown-item"><i class="fa fa-fw fa-sm fa-pencil-alt mr-2"></i> <?php if($row->is_admin) { echo l('global.employee.remove.admin'); } else { echo l('global.employee.assign.admin'); } ?></a>
                                <a href="#" data-toggle="modal" data-target="#employee_remove_modal" data-employee-id = "<?= $row->id?>" class="dropdown-item"><i class="fa fa-fw fa-sm fa-trash-alt mr-2"></i> <?= l('global.remove') ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach ?>

    <?php else : ?>
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-column align-items-center justify-content-center py-3">
                    <img src="<?= ASSETS_FULL_URL . 'images/no_rows.svg' ?>" class="col-10 col-md-7 col-lg-4 mb-3" alt="<?= l('company.no_data') ?>" />
                    <h2 class="h4 text-muted"><?= l('company.no_data') ?></h2>
                </div>
            </div>
        </div>
    <?php endif ?>
</section>

<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/company/create_company_modal.php'), 'modals'); ?>
<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/company/add_employee_modal.php'), 'modals'); ?>
<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/company/assign_employee_admin_modal.php'), 'modals'); ?>
<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/company/remove_employee_modal.php'), 'modals'); ?>
