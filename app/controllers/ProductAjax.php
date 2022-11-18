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
use Altum\Middlewares\Authentication;
use Altum\Middlewares\Csrf;
use Altum\Response;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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
        $_POST['type'] = trim(Database::clean_string($_POST['type']));
        $_POST['product_id'] = trim(Database::clean_string($_POST['product_id'] ?? ''));
        $_POST['product_link'] = trim(Database::clean_string($_POST['product_link']));
        $_POST['auto_generated_link'] = trim(Database::clean_string($_POST['auto_generated_link']));
        $_POST['auto_generated_link'] = str_replace(url()."p/", "", $_POST['auto_generated_link']);


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
        $insertedId = db()->insert('products', [
            'user_id' => $this->user->user_id,
            'name' => $_POST['name'],
            'type' => $_POST['type'],
            'product_id' => $_POST['product_id'],
            'product_link' => $_POST['product_link'],
            'auto_generated_link' => $_POST['auto_generated_link'] ?? '',
        ]);

        $result = Builder::create()
            ->writer(new PngWriter())
            ->writerOptions([])
            ->data($_POST['auto_generated_link'])
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->size(300)
            ->margin(10)
            ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
            ->labelText($_POST['name'])
            ->labelFont(new NotoSans(20))
            ->labelAlignment(new LabelAlignmentCenter())
            ->validateResult(false)
            ->build();

        if (!file_exists('uploads/qr_codes'))
            mkdir('uploads/qr_codes');
        if (!file_exists('uploads/qr_codes/' . $insertedId))
            mkdir('uploads/qr_codes/' . $insertedId);

        $result->saveToFile('uploads/qr_codes/' . $insertedId . '/image.png');

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
        $_POST['type'] = trim(Database::clean_string($_POST['type']));
        $_POST['product_id'] = trim(Database::clean_string($_POST['product_id'] ?? ''));
        $_POST['product_link'] = trim(Database::clean_string($_POST['product_link']));

        /* Check for possible errors */
        if (empty($_POST['name'])) {
            Response::json(l('global.error_message.empty_fields'), 'error');
        }

        /* Insert to database */
        db()->where('id', $_POST['id'])->update('products', [
            'name' => $_POST['name'],
            'type' => $_POST['type'],
            'product_id' => $_POST['product_id'],
            'product_link' => $_POST['product_link']
        ]);


        $result = Builder::create()
            ->writer(new PngWriter())
            ->writerOptions([])
            ->data($_POST['auto_generated_link'])
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->size(300)
            ->margin(10)
            ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
            ->labelText($_POST['name'])
            ->labelFont(new NotoSans(20))
            ->labelAlignment(new LabelAlignmentCenter())
            ->validateResult(false)
            ->build();

        if (!file_exists('uploads/qr_codes'))
            mkdir('uploads/qr_codes');
        if (!file_exists('uploads/qr_codes/' . $_POST['id']))
            mkdir('uploads/qr_codes/' . $_POST['id']);

        $result->saveToFile('uploads/qr_codes/' . $_POST['id'] . '/image.png');

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
