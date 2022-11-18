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
}
