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
use Altum\Database\Database;
use Altum\Date;
use Altum\Middlewares\Csrf;
use Altum\Response;
use Altum\Uploads;

class AdminCatalog extends Controller
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

        $view = new \Altum\Views\View('admin/catalog/index', (array) $this);

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

    public function create()
    {
        /* Team checks */
        if (\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('create')) {
            Response::json(l('global.info_message.team_no_access'), 'error');
        }

        $_POST['name'] = trim(Database::clean_string($_POST['name']));
        $_POST['description'] = trim(Database::clean_string($_POST['description']));
        $_POST['price'] = trim(Database::clean_string($_POST['price']));

        /* Check for possible errors */
        if (empty($_POST['name'] || $_POST['description'] || $_POST['price'])) {
            Response::json(l('global.error_message.empty_fields'), 'error');
        }

        $id = 0;

        if (!empty($_POST['id'])) {
            $id = $_POST['id'];
            /* Update to database */
            db()->where('product_type_id', $_POST['id'])->update('catalog', [
                'name' => $_POST['name'],
                'description' => $_POST['description'],
                'price' => $_POST['price'],
                'status' => 1,
                'last_datetime' => Date::$date,
            ]);
        } else {
            /* Insert to database */
            $id = db()->insert('catalog', [
                'name' => $_POST['name'],
                'description' => $_POST['description'],
                'price' => $_POST['price'],
                'status' => 1,
                'datetime' => Date::$date,
            ]);
        }

        foreach ($_FILES['image']['name'] as $key => $value) {
            $image_name = $_FILES['image']['name'][$key];
            $image_extension = explode('.', $image_name);
            $image_extension = mb_strtolower(end($image_extension));
            $image_temp = $_FILES['image']['tmp_name'][$key];

            if ($_FILES['image']['error'][$key] == UPLOAD_ERR_INI_SIZE) {
                Alerts::add_error(sprintf(l('global.error_message.file_size_limit'), get_max_upload()));
            }

            if ($_FILES['image']['error'][$key] && $_FILES['image']['error'][$key] != UPLOAD_ERR_INI_SIZE) {
                Alerts::add_error(l('global.error_message.file_upload'));
            }

            // if (!in_array($image_extension, Uploads::get_whitelisted_file_extensions('catalog_images'))) {
            //     Alerts::add_error(l('global.error_message.invalid_file_type'));
            //     redirect('pay/' . $this->plan_id . '?' . (isset($_GET['trial_skip']) ? '&trial_skip=true' : null) . (isset($_GET['code']) ? '&code=' . $_GET['code'] : null));
            // }

            if(!file_exists(UPLOADS_PATH . 'catalog_images/')) mkdir(UPLOADS_PATH . 'catalog_images/');

            if (!\Altum\Plugin::is_active('offload') || (\Altum\Plugin::is_active('offload') && !settings()->offload->uploads_url)) {
                if (!is_writable(UPLOADS_PATH . 'catalog_images/')) {
                    Alerts::add_error(sprintf(l('global.error_message.directory_not_writable'), UPLOADS_PATH . 'catalog_images/'));
                    redirect('pay/' . $this->plan_id . '?' . (isset($_GET['trial_skip']) ? '&trial_skip=true' : null) . (isset($_GET['code']) ? '&code=' . $_GET['code'] : null));
                }
            }

            $file_name = $id.'_'.$image_name;

            /* Offload uploading */
            // if (\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
            //     try {
            //         $s3 = new \Aws\S3\S3Client(get_aws_s3_config());

            //         /* Upload image */
            //         $result = $s3->putObject([
            //             'Bucket' => settings()->offload->storage_name,
            //             'Key' => 'uploads/catalog_images/' . $file_name,
            //             'ContentType' => mime_content_type($image_temp),
            //             'SourceFile' => $image_temp,
            //             'ACL' => 'public-read'
            //         ]);
            //     } catch (\Exception $exception) {
            //         Alerts::add_error($exception->getMessage());
            //     }
            // }

            // /* Local uploading */ else {
                /* Upload the original */
                move_uploaded_file($image_temp, UPLOADS_PATH . 'catalog_images/' . $file_name);
            // }

            db()->insert('catalog_images', [

                'image' => $file_name,
                'catalog_id' => $id,
                'datetime' => Date::$date,
            ]);
        }

        /* Set a nice success message */
        redirect('admin/catalog');
    }
}
