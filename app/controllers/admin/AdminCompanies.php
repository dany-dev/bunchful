<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum\Controllers;

use Altum\Alerts;
use Altum\Middlewares\Csrf;

class AdminCompanies extends Controller
{

    public function index()
    {
        /* Prepare the filtering system */
        $filters = (new \Altum\Filters(['user_id', 'type'], ['name'], ['name', 'datetime']));
        $filters->set_default_order_by('id', settings()->main->default_order_type);
        $filters->set_default_results_per_page(settings()->main->default_results_per_page);

        /* Prepare the paginator */
        $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `companies` WHERE 1 = 1 {$filters->get_sql_where('companies')}")->fetch_object()->total ?? 0;
        $paginator = (new \Altum\Paginator($total_rows, $filters->get_results_per_page(), $_GET['page'] ?? 1, url('admin/companies?' . $filters->get_get() . '&page=%d')));

        /* Get the data */
        $companies = [];
        $company_users = [];
        $companies_result = database()->query("
            SELECT
                `companies`.*, `users`.`name` AS `user_name`, `users`.`email` AS `user_email`
            FROM
                `companies`
            LEFT JOIN
                `users` ON `companies`.`user_id` = `users`.`user_id`
            WHERE
                1 = 1
                {$filters->get_sql_where('companies')}
                {$filters->get_sql_order_by('companies')}

            {$paginator->get_sql_limit()}
        ");
        while ($row = $companies_result->fetch_object()) {
            $companies[] = $row;
        }

        foreach ($companies as $key => $value) {
            $company_users_result = database()->query("
                SELECT
                    DISTINCT `users`.`name`, `users`.`user_id`, `users`.`email`
                FROM
                    `company_users`
                LEFT JOIN
                    `users` ON `company_users`.`user_id` = `users`.`user_id`
            ");
            $companies[$key]->users = [];
            while ($row = $company_users_result->fetch_object()) {
                $companies[$key]->users[] = $row;
            }
        }

        /* Export handler */
        process_export_csv($companies, 'include', ['id', 'user_id', 'name', 'user_name', 'user_email', 'last_datetime', 'datetime'], sprintf(l('admin_companies.title')));
        process_export_json($companies, 'include', ['id', 'user_id', 'name', 'user_name', 'user_email', 'last_datetime', 'datetime'], sprintf(l('admin_companies.title')));

        /* Prepare the pagination view */
        $pagination = (new \Altum\Views\View('partials/pagination', (array) $this))->run(['paginator' => $paginator]);

        /* Main View */
        $data = [
            'companies' => $companies,
            'filters' => $filters,
            'pagination' => $pagination
        ];

        // echo "<pre>";
        // print_r($data);
        // die();

        $view = new \Altum\Views\View('admin/companies/index', (array) $this);

        $this->add_view_content('content', $view->run($data));
    }

    public function bulk()
    {

        //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

        /* Check for any errors */
        if (empty($_POST)) {
            redirect('admin/pixels');
        }

        if (empty($_POST['selected'])) {
            redirect('admin/pixels');
        }

        if (!isset($_POST['type']) || (isset($_POST['type']) && !in_array($_POST['type'], ['delete']))) {
            redirect('admin/pixels');
        }

        if (!Csrf::check()) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
        }

        if (!Alerts::has_field_errors() && !Alerts::has_errors()) {

            switch ($_POST['type']) {
                case 'delete':

                    foreach ($_POST['selected'] as $pixel_id) {
                        /* Delete the resource */
                        db()->where('pixel_id', $pixel_id)->delete('pixels');

                        /* Clear the cache */
                        \Altum\Cache::$adapter->deleteItemsByTag('pixel_id=' . $pixel_id);
                    }

                    break;
            }

            /* Set a nice success message */
            Alerts::add_success(l('admin_bulk_delete_modal.success_message'));
        }

        redirect('admin/pixels');
    }

    public function delete() {

        $company_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

        if(!Csrf::check('global_token')) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
        }

        if(!$company = db()->where('id', $company_id)->getOne('companies', ['id', 'name'])) {
            redirect('admin/companies');
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            /* Delete the resource */
            db()->where('company_id', $company->id)->delete('company_users');
            db()->where('id', $company->id)->delete('companies');

            /* Clear the cache */
            \Altum\Cache::$adapter->deleteItemsByTag('company_id=' . $company->id);

            /* Set a nice success message */
            Alerts::add_success(sprintf(l('global.success_message.delete1'), '<strong>' . $company->name . '</strong>'));

        }

        redirect('admin/companies');
    }
}
