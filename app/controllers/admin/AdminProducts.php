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

class AdminProducts extends Controller
{

    public function index()
    {

        /* Prepare the filtering system */
        $filters = (new \Altum\Filters(['user_id', 'type'], ['name'], ['name', 'datetime']));
        $filters->set_default_order_by('id', settings()->main->default_order_type);
        $filters->set_default_results_per_page(settings()->main->default_results_per_page);

        /* Prepare the paginator */
        $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `products` WHERE 1 = 1 {$filters->get_sql_where('products')}")->fetch_object()->total ?? 0;
        $paginator = (new \Altum\Paginator($total_rows, $filters->get_results_per_page(), $_GET['page'] ?? 1, url('admin/products?' . $filters->get_get() . '&page=%d')));

        /* Get the data */
        $products = [];
        $product_users = [];
        $products_result = database()->query("
            SELECT
                `products`.*, `users`.`name` AS `user_name`, `users`.`email` AS `user_email`
            FROM
                `products`
            LEFT JOIN
                `users` ON `products`.`user_id` = `users`.`user_id`
            WHERE
                1 = 1
                {$filters->get_sql_where('products')}
                {$filters->get_sql_order_by('products')}

            {$paginator->get_sql_limit()}
        ");
        while ($row = $products_result->fetch_object()) {
            $products[] = $row;
        }

        $products_result = database()->query("
            SELECT
                DISTINCT `users`.`name`, `users`.`user_id`
            FROM
                `products`
            LEFT JOIN
                `users` ON `products`.`user_id` = `users`.`user_id`
        ");
        while ($row = $products_result->fetch_object()) {
            $product_users[$row->user_id] = $row->name;
        }

        /* Export handler */
        process_export_csv($products, 'include', ['pixel_id', 'user_id', 'type', 'name', 'pixel', 'last_datetime', 'datetime'], sprintf(l('admin_products.title')));
        process_export_json($products, 'include', ['pixel_id', 'user_id', 'type', 'name', 'pixel', 'last_datetime', 'datetime'], sprintf(l('admin_products.title')));

        /* Prepare the pagination view */
        $pagination = (new \Altum\Views\View('partials/pagination', (array) $this))->run(['paginator' => $paginator]);

        /* Main View */
        $data = [
            'products' => $products,
            'filters' => $filters,
            'product_users' => $product_users,
            'pagination' => $pagination
        ];

        $view = new \Altum\Views\View('admin/products/index', (array) $this);

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

    public function delete()
    {

        $pixel_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

        if (!Csrf::check('global_token')) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
        }

        if (!$pixel = db()->where('pixel_id', $pixel_id)->getOne('pixels', ['pixel_id', 'name'])) {
            redirect('admin/pixels');
        }

        if (!Alerts::has_field_errors() && !Alerts::has_errors()) {

            /* Delete the resource */
            db()->where('pixel_id', $pixel->pixel_id)->delete('pixels');

            /* Clear the cache */
            \Altum\Cache::$adapter->deleteItemsByTag('pixel_id=' . $pixel->pixel_id);

            /* Set a nice success message */
            Alerts::add_success(sprintf(l('global.success_message.delete1'), '<strong>' . $pixel->name . '</strong>'));
        }

        redirect('admin/pixels');
    }
}
