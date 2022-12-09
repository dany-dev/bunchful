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
use Altum\Date;
use Altum\Middlewares\Authentication;
use Altum\Middlewares\Csrf;

class Catalog extends Controller
{

    public function index()
    {
        /* Prepare the filtering system */
        $filters = (new \Altum\Filters(['name'], ['name', 'datetime']));
        $filters->set_default_order_by('catalog_id', settings()->main->default_order_type);
        $filters->set_default_results_per_page(settings()->main->default_results_per_page);

        /* Prepare the paginator */
        $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `catalog` WHERE 1 = 1 {$filters->get_sql_where('catalog')}")->fetch_object()->total ?? 0;
        $paginator = (new \Altum\Paginator($total_rows, $filters->get_results_per_page(), $_GET['page'] ?? 1, url('admin/catalog?' . $filters->get_get() . '&page=%d')));

        /* Get the data */
        $catalog = [];
        $catalog_result = database()->query("
            SELECT
                *
            FROM
                `catalog`
            WHERE
                1 = 1
                {$filters->get_sql_where('catalog')}
                {$filters->get_sql_order_by('catalog')}

            {$paginator->get_sql_limit()}
        ");
        while ($row = $catalog_result->fetch_object()) {
            $catalog[] = $row;
            $catalog[count($catalog) - 1]->images = [];
            $catalog_images = database()->query("
            SELECT
                *
            FROM
                `catalog_images`
            WHERE
                catalog_id = {$row->catalog_id}
            {$paginator->get_sql_limit()}
        ");
            while ($r = $catalog_images->fetch_object()) {
                $catalog[count($catalog) - 1]->images[] = $r;
            }
        }

        /* Export handler */
        process_export_csv($catalog, 'include', ['catalog_id', 'name', 'last_datetime', 'datetime'], sprintf(l('admin_products.title')));
        process_export_json($catalog, 'include', ['catalog_id', 'name', 'last_datetime', 'datetime'], sprintf(l('admin_products.title')));

        /* Prepare the pagination view */
        $pagination = (new \Altum\Views\View('partials/pagination', (array) $this))->run(['paginator' => $paginator]);

        /* Main View */
        $data = [
            'catalog' => $catalog,
            'filters' => $filters,
            'pagination' => $pagination
        ];

        $view = new \Altum\Views\View('catalog/index', (array) $this);

        $this->add_view_content('content', $view->run($data));
    }

    public function pay()
    {
        if (isset($_GET['id'])) {
            \Stripe\Stripe::setApiKey(settings()->stripe->secret_key);

            $catalog = db()->where('catalog_id', $_GET['id'])->getOne('catalog');

            if (!empty($catalog)) {
                $price = $base_amount = (float) $catalog->price;
                // $price = $this->calculate_price_with_taxes($price);
                $stripe_formatted_price = in_array(settings()->payment->currency, ['MGA', 'BIF', 'CLP', 'PYG', 'DJF', 'RWF', 'GNF', 'UGX', 'JPY', 'VND', 'VUV', 'XAF', 'KMF', 'KRW', 'XOF', 'XPF']) ? number_format($price, 0, '.', '') : number_format($price, 2, '.', '') * 100;
                $price = number_format($price, 2, '.', '');

                $timestamp = $this->user->user_id . time();


                $return = db()->insert('orders', [
                    'user_id' => $this->user->user_id,
                    'product_id' => $catalog->catalog_id,
                    'processor' => 'stripe',
                    'type' => 'one_time',
                    'frequency' => 'lifetime',
                    'base_amount' => $base_amount,
                    'email' => $this->user->email,
                    'transaction_token' => '-',
                    'name' => $this->user->name,
                    'billing' => settings()->payment->taxes_and_billing_is_enabled && $this->user->billing ? json_encode($this->user->billing) : null,
                    'total_amount' => $price,
                    'currency' => settings()->payment->currency,
                    'status' => 'Pending',
                    'datetime' => Date::$date
                ]);

                $stripe_session = \Stripe\Checkout\Session::create([
                    'line_items' => [[
                        'name' => settings()->business->brand_name . ' - Catalog - Product' . $catalog->name,
                        'description' => $catalog->description,
                        'amount' => $stripe_formatted_price,
                        'currency' => settings()->payment->currency,
                        'quantity' => 1,
                    ]],
                    'metadata' => [
                        'user_id' => $this->user->user_id,
                        'catalog_id' => $catalog->catalog_id,
                        'payment_frequency' => 'one_time',
                        'base_amount' => $base_amount,
                    ],
                    'success_url' => url('catalog/payment_redirect' . $this->return_url_parameters('success', $base_amount, $price, $return)),
                    'cancel_url' => url('catalog/payment_redirect' . $this->return_url_parameters('cancel', $base_amount, $price, $return)),
                ]);

                $stripe_session_id = $stripe_session->id;

                db()->where('order_id', $return)->update('orders', [
                    'transaction_token' => $stripe_session_id,
                    'status' => 'Processing',
                    'last_datetime' => Date::$date
                ]);

                header('Location: ' . $stripe_session->url);
                die();
            }
        }

        redirect('/');
    }

    public function payment_redirect()
    {
        if ($_GET['return_type']) {
            switch ($_GET['return_type']) {
                case 'success':
                    db()->where('order_id', $_GET['order_id'])->update('orders', [
                        'status' => 'Success',
                        'last_datetime' => Date::$date
                    ]);
                    break;
                case 'cancel':
                    db()->where('order_id', $_GET['order_id'])->update('orders', [
                        'status' => 'Cancel',
                        'last_datetime' => Date::$date
                    ]);
                    break;
                default:
                    db()->where('order_id', $_GET['order_id'])->update('orders', [
                        'status' => 'Error',
                        'last_datetime' => Date::$date
                    ]);
                    break;
            }
            redirect('catalog');
        }
        redirect('/');
    }

    private function return_url_parameters($return_type, $base_amount, $total_amount, $order_id)
    {
        return
            '?return_type=' . $return_type
            . '&payment_processor=' . 'stripe'
            . '&payment_frequency=' . 'lifetime'
            . '&payment_type=' . 'one_time'
            . '&base_amount=' . $base_amount
            . '&total_amount=' . $total_amount
            . '&order_id=' . $order_id;
    }
}
