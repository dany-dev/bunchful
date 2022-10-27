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
use Altum\Middlewares\Authentication;
use Altum\Middlewares\Csrf;
use Altum\Response;

class ProductAjax extends Controller
{

    public function index()
    {

        Authentication::guard();

        //ALTUMCODE:DEMO if(DEMO) if($this->user->user_id == 1) Response::json('Please create an account on the demo to test out this function.', 'error');

        if (!empty($_POST) && (Csrf::check('token') || Csrf::check('global_token')) && isset($_POST['request_type'])) {

            switch ($_POST['request_type']) {

                    /* Create */
                case 'create':
                    $this->create();
                    break;

                    /* Update */
                case 'update':
                    $this->update();
                    break;

                    /* Update */
                case 'delete':
                    $this->delete();
                    break;
            }
        }

        die();
    }

    private function create()
    {
        /* Team checks */
        if (\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('create')) {
            Response::json(l('global.info_message.team_no_access'), 'error');
        }

        $_POST['name'] = trim(Database::clean_string($_POST['name']));
        $_POST['product_id'] = trim(Database::clean_string($_POST['product_id']));
        $_POST['link_url'] = trim(Database::clean_string($_POST['link_url']));
        $_POST['auto_generated_link_url'] = trim(Database::clean_string($_POST['auto_generated_link_url']));

        /* Check for possible errors */
        if (empty($_POST['name'])) {
            Response::json(l('global.error_message.empty_fields'), 'error');
        }

        /* Make sure that the user didn't exceed the limit */
        $user_total_products = database()->query("SELECT COUNT(*) AS `total` FROM `products` WHERE `user_id` = {$this->user->user_id}")->fetch_object()->total;
        if ($this->user->plan_settings->products_limit != -1 && $user_total_products >= $this->user->plan_settings->products_limit) {
            Response::json(l('global.info_message.plan_feature_limit'), 'error');
        }

        /* Insert to database */
        db()->insert('products', [
            'user_id' => $this->user->user_id,
            'name' => $_POST['name'],
            'product_id' => $_POST['product_id'],
            'product_link' => $_POST['product_link'],
            'auto_generated_link' => $_POST['auto_generated_link'] ?? 'aasd',
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('products?user_id=' . $this->user->user_id);

        /* Set a nice success message */
        Response::json(sprintf(l('global.success_message.update1'), '<strong>' . e($_POST['name']) . '</strong>'));
    }

    private function update()
    {
        /* Team checks */
        if (\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('update')) {
            Response::json(l('global.info_message.team_no_access'), 'error');
        }

        $_POST['id'] = (int) $_POST['id'];
        $_POST['name'] = trim(Database::clean_string($_POST['name']));
        $_POST['product_id'] = trim(Database::clean_string($_POST['product_id']));
        $_POST['link_url'] = trim(Database::clean_string($_POST['link_url']));
        $_POST['auto_generated_link_url'] = trim(Database::clean_string($_POST['auto_generated_link_url']));

        /* Check for possible errors */
        if (empty($_POST['name'])) {
            Response::json(l('global.error_message.empty_fields'), 'error');
        }

        /* Insert to database */
        db()->where('id', $_POST['id'])->update('products', [
            'name' => $_POST['name'],
            'product_id' => $_POST['product_id'],
            'product_link' => $_POST['product_link'],
            'auto_generated_link' => $_POST['auto_generated_link'] ?? 'aasd',
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('products?user_id=' . $this->user->user_id);

        /* Set a nice success message */
        Response::json(sprintf(l('global.success_message.update1'), '<strong>' . e($_POST['name']) . '</strong>'));
    }

    public function delete()
    {
        Authentication::guard();

        /* Team checks */
        if (\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('delete')) {
            Response::json(l('global.info_message.team_no_access'), 'error');
        }

        if (empty($_POST)) {
            Response::json(l('global.success_message.delete2'), 'error');
        }

        $product_id = (int) $_POST['product_id'];


        if (!Csrf::check()) {
            Response::json(l('global.error_message.invalid_csrf_token'), 'error');
        }

        if (!$company = db()->where('id', $product_id)->getOne('products', ['user_id'])) {
            Response::json(l('global.success_message.delete2'));
        }

        if (!Alerts::has_field_errors() && !Alerts::has_errors()) {

            /* Delete the project */
            db()->where('id', $product_id)->delete('products');

            /* Clear the cache */
            \Altum\Cache::$adapter->deleteItemsByTag('products?user_id=' . $this->user->user_id);

            Response::json(l('global.success_message.delete2'));
        }

        Response::json(l('global.employee.account-not-found'), 'error');
    }
}
