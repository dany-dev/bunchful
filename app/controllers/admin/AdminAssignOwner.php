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
use Altum\Models\Plan;

class AdminAssignOwner extends Controller {

    public function index() {

        $user_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        /* Check if resource exists */
        if(!$user = db()->where('user_id', $user_id)->getOne('users')) {
            redirect('admin/users');
        }

        /* Get current plan proper details */
        $user->plan = (new Plan())->get_plan_by_id($user->plan_id);

        /* Check if its a custom plan */
        if($user->plan->plan_id != 'custom') {
            redirect('admin/users');
        }

        db()->where('user_id', $user->user_id)->update('users', [
            'is_global_owner' => !$user->is_global_owner
        ]);

        redirect('admin/users');

    }

}
