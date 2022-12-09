<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum\Controllers;

use Altum\Middlewares\Authentication;

class AccountOrders extends Controller {

    public function index() {

        Authentication::guard();

        if(!settings()->payment->is_enabled) {
            redirect('dashboard');
        }

        $payment_processors = require APP_PATH . 'includes/payment_processors.php';

        /* Prepare the filtering system */
        // $filters = (new \Altum\Filters(['processor', 'type', 'frequency'], [], ['total_amount', 'datetime']));
        // $filters->set_default_order_by('id', settings()->main->default_order_type);
        // $filters->set_default_results_per_page(settings()->main->default_results_per_page);

        /* Prepare the paginator */
        // $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `orders` WHERE `user_id` = {$this->user->user_id} {$filters->get_sql_where()}")->fetch_object()->total ?? 0;
        // $paginator = (new \Altum\Paginator($total_rows, $filters->get_results_per_page(), $_GET['page'] ?? 1, url('account-orders?' . $filters->get_get() . '&page=%d')));

        /* Get the orders list for the user */
        $orders = [];
        $orders_result = database()->query("SELECT `orders`.*, `catalog`.`name` as customerName FROM `orders` LEFT JOIN `catalog` ON `catalog`.`catalog_id` = `orders`.`product_id` WHERE `user_id` = {$this->user->user_id} ORDER BY order_id DESC");
        while($row = $orders_result->fetch_object()) $orders[] = $row;

        /* Export handler */
        // process_export_json($orders, 'include', ['id', 'user_id', 'email', 'name', 'processor', 'type', 'frequency', 'billing', 'taxes_ids', 'base_amount', 'code', 'discount_amount', 'total_amount', 'currency', 'status', 'datetime']);
        // process_export_csv($orders, 'include', ['id', 'user_id', 'email', 'name', 'processor', 'type', 'frequency', 'base_amount', 'code', 'discount_amount', 'total_amount', 'currency', 'status', 'datetime']);

        /* Prepare the pagination view */
        // $pagination = (new \Altum\Views\View('partials/pagination', (array) $this))->run(['paginator' => $paginator]);

        /* Get the account header menu */
        $menu = new \Altum\Views\View('partials/account_header_menu', (array) $this);
        $this->add_view_content('account_header_menu', $menu->run());

        /* Prepare the View */
        $data = [
            'orders' => $orders,
            // 'pagination' => $pagination,
            // 'filters' => $filters,
            'payment_processors' => $payment_processors,
        ];

        $view = new \Altum\Views\View('account-orders/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
