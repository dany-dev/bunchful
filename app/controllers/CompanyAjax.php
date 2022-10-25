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
use Altum\Logger;
use Altum\Middlewares\Authentication;
use Altum\Middlewares\Csrf;
use Altum\Response;

class CompanyAjax extends Controller
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

                    /* Delete */
                case 'delete':
                    $this->delete();
                    break;

                    /* Add Employee */
                case 'add_employee':
                    $this->add_employee();
                    break;
            }
        }

        die();
    }

    private function create()
    {

        /* Team checks */
        // if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('create')) {
        //     Response::json(l('global.info_message.team_no_access'), 'error');
        // }

        $_POST['name'] = trim(Database::clean_string($_POST['name']));

        /* Check for possible errors */
        if (empty($_POST['name'])) {
            Response::json(l('global.error_message.empty_fields'), 'error');
        }

        /* Make sure that the user didn't exceed the limit */
        // $user_total_pixels = database()->query("SELECT COUNT(*) AS `total` FROM `pixels` WHERE `user_id` = {$this->user->user_id}")->fetch_object()->total;
        // if($this->user->plan_settings->pixels_limit != -1 && $user_total_pixels >= $this->user->plan_settings->pixels_limit) {
        //     Response::json(l('global.info_message.plan_feature_limit'), 'error');
        // }

        /* Insert to database */
        db()->insert('companies', [
            'user_id' => $this->user->user_id,
            'name' => $_POST['name'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('company?user_id=' . $this->user->user_id);

        /* Set a nice success message */
        Response::json(sprintf(l('global.success_message.create1'), '<strong>' . e($_POST['name']) . '</strong>'));
    }

    private function update()
    {
        /* Team checks */
        if (\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('update')) {
            Response::json(l('global.info_message.team_no_access'), 'error');
        }

        $_POST['name'] = trim(Database::clean_string($_POST['name']));
        $_POST['type'] = array_key_exists($_POST['type'], require APP_PATH . 'includes/pixels.php') ? $_POST['type'] : '';
        $_POST['pixel'] = trim(Database::clean_string($_POST['pixel']));

        /* Check for possible errors */
        if (empty($_POST['name']) || empty($_POST['type']) || empty($_POST['pixel'])) {
            Response::json(l('global.error_message.empty_fields'), 'error');
        }

        /* Insert to database */
        db()->where('pixel_id', $_POST['pixel_id'])->where('user_id', $this->user->user_id)->update('pixels', [
            'type' => $_POST['type'],
            'name' => $_POST['name'],
            'pixel' => $_POST['pixel'],
            'last_datetime' => Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('pixels?user_id=' . $this->user->user_id);

        /* Set a nice success message */
        Response::json(sprintf(l('global.success_message.update1'), '<strong>' . e($_POST['name']) . '</strong>'));
    }

    public function add_employee()
    {
        $_POST['email'] = trim(Database::clean_string($_POST['email']));

        if (empty($_POST['email'])) {
            Response::json(l('global.error_message.empty_fields'), 'error');
        }

        $user = db()->where('email', $_POST['email'])->getOne('users', ['user_id', 'email', 'name', 'status', 'password', 'token_code', 'twofa_secret', 'language']);
        $company = db()->where('user_id', $this->user->user_id)->getOne('companies');

        if (!$user) {
            // Send Email
        } else {
            $isCompanyAdmin = database()->query("SELECT COUNT(*) AS `total` FROM `companies` WHERE `user_id` = {$user->user_id}")->fetch_object()->total ?? 0;

            if (!$isCompanyAdmin) {
                $isCompanyEmployee = database()->query("SELECT COUNT(*) AS `total` FROM `company_users` WHERE `user_id` = {$user->user_id}")->fetch_object()->total ?? 0;

                if ($isCompanyEmployee) {
                    Response::json(sprintf(l('global.employee.already-employee'), '<strong>' . e($_POST['email']) . '</strong>'), "error");
                } else {
                    db()->insert('company_users', [
                        'user_id' => $user->user_id,
                        'company_id' => $company->id,
                    ]);
                    $email_template = get_email_template(
                        [
                            '{{NAME}}' => $user->name,
                        ],
                        l('global.invitation.email.subject', $user->language),
                        [
                            '{{COMPANY_NAME}}' => $company->name,
                            '{{NAME}}' => $user->name,
                        ],
                        l('global.invitation.email-body', $user->language)
                    );

                    /* Send the email */
                    send_mail($_POST['email'], $email_template->subject, $email_template->body);
                    Response::json(sprintf(l('global.invitation.email-send'), '<strong>' . e($_POST['email']) . '</strong>'));
                }
            } else {
                Response::json(sprintf(l('global.employee.already-owner'), '<strong>' . e($_POST['email']) . '</strong>'), "error");
            }
        }
        Response::json(sprintf(l('global.employee.account-not-found'), '<strong>' . e($_POST['email']) . '</strong>'), "error");
    }
}
