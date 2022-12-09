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

        if (!$this->user->is_global_owner) {
            redirect();
        }

        $isCompanyAdmin = database()->query("SELECT COUNT(*) AS `total` FROM `companies` WHERE `user_id` = {$this->user->user_id}")->fetch_object()->total ?? 0;
        $isCompanyEmployee = database()->query("SELECT COUNT(*) AS `total` FROM `company_users` WHERE `user_id` = {$this->user->user_id}")->fetch_object()->total ?? 0;

        if (!$isCompanyAdmin) {
        } else {
            $company = db()->where('user_id', $this->user->user_id)->getOne('companies');

            /* Get the pixels list for the user */
            $companyEmployees = [];
            $companyEmployees_result = database()->query("SELECT * FROM `company_users` LEFT JOIN users ON users.user_id = company_users.user_id WHERE `company_id` = {$company->id}");
            while ($row = $companyEmployees_result->fetch_object()) $companyEmployees[] = $row;
        }

        $biolinks = db()->where('company_id', $company->id)->getOne('links');

        /* Create Link Modal */
        $domains = (new Domain())->get_domains($this->user);
        $data = [
            'domains' => $domains
        ];

        $view = new \Altum\Views\View('links/create_link_modals', (array) $this);
        \Altum\Event::add_content($view->run($data), 'modals');

        /* Prepare the View */
        $data = [
            'isCompanyAdmin'    => $isCompanyAdmin,
            'isCompanyEmployee' => $isCompanyEmployee,
            'company'           => $company,
            'companyEmployees'  => $companyEmployees,
            'biolinks'          => $biolinks
        ];

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
}
