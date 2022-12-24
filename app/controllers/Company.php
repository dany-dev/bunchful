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
use Altum\Models\Domain;

class Company extends Controller
{

    public function index()
    {
        Authentication::guard();

        $data = array();

        $data['company'] = '';
        $data['companyEmployees'] = array();
        $data['companyTemplate'] = array();
        $data['isCompanyAdmin'] = false;
        $data['isCompanyEmployee'] = false;
        $data['isGlobalOwner'] = $this->user->is_global_owner;

        $data['company'] = db()->where('user_id', $this->user->user_id)->getOne('companies');

        if ($data['company']) {
            $data['isGlobalOwner'] = true;
        } else {
            $employeeCheck = db()->where('user_id', $this->user->user_id)->getOne('company_users');

            if ($employeeCheck) {
                $data['company'] = db()->where('id', $employeeCheck->company_id)->getOne('companies');
                $data['isCompanyEmployee'] = true;

                if ($employeeCheck->is_admin)
                    $data['isCompanyAdmin'] = true;
            }
        }

        if (!($data['isGlobalOwner'] || $data['isCompanyEmployee'])) {
            Alerts::add_error(l('global.error_message.cant_access_company_page'));
            redirect('dashboard');
        }

        if ($data['company']) {
            $response = database()->query("SELECT * FROM `company_users` LEFT JOIN users ON users.user_id = company_users.user_id WHERE `company_id` = {$data['company']->id}");
            while ($row = $response->fetch_object()) $data['companyEmployees'][] = $row;

            $data['companyTemplate'] = db()->where('company_id', $data['company']->id)->getOne('company_templates');
        }

        $data['companyTemplate'] = db()->where('company_id', $data['company']->id)->getOne('biolinks_themes');

        $view = new \Altum\Views\View('company/index', (array) $this);

        $this->add_view_content('content', $view->run($data));
    }

    public function delete()
    {

        Authentication::guard();

        /* Team checks */
        if (\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('delete')) {
            Alerts::add_info(l('global.info_message.team_no_access'));
            redirect('pixels');
        }

        if (empty($_POST)) {
            redirect('pixels');
        }

        $pixel_id = (int) $_POST['pixel_id'];

        //ALTUMCODE:DEMO if(DEMO) if($this->user->user_id == 1) Alerts::add_error('Please create an account on the demo to test out this function.');

        if (!Csrf::check()) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
        }

        if (!$pixel = db()->where('pixel_id', $pixel_id)->where('user_id', $this->user->user_id)->getOne('pixels', ['pixel_id', 'name'])) {
            redirect('pixels');
        }

        if (!Alerts::has_field_errors() && !Alerts::has_errors()) {

            /* Delete the project */
            db()->where('pixel_id', $pixel_id)->delete('pixels');

            /* Clear the cache */
            \Altum\Cache::$adapter->deleteItemsByTag('pixels?user_id=' . $this->user->user_id);

            /* Set a nice success message */
            Alerts::add_success(sprintf(l('global.success_message.delete1'), '<strong>' . $pixel->name . '</strong>'));

            redirect('pixels');
        }

        redirect('pixels');
    }

    public function delete_company()
    {
        $company_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

        if(!Csrf::check('global_token')) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
        }

        if(!$company = db()->where('id', $company_id)->getOne('companies', ['id', 'name'])) {
            redirect('company');
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

        redirect('company');
    }
}
