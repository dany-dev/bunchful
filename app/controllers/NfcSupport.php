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

class NfcSupport extends Controller
{

    public function index()
    {
        $email_template = get_email_template(
            [],
            l('nfc_support.emails.subject'),
            [
                '{{CURRENT_EMAIL}}' => $this->user->email,
                '{{NAME}}' => $_POST['name'],
                '{{EMAIL}}' => $_POST['email'],
                '{{SHIPPING_ADDRESS}}' => $_POST['shipping_address'],
                '{{REPLACEMENT_REASON}}' => $_POST['replacement_reason'],
                '{{PRODUCT_LINK}}' => $_POST['product_link'],
                '{{PRODUCT_DESCRIPTION}}' => $_POST['product_description'],
            ],
            l('nfc_support.emails.body')
        );

        send_mail('support@bunchful.com', $email_template->subject, $email_template->body, ['anti_phishing_code' => $this->user->anti_phishing_code, 'language' => $this->user->language]);

        redirect("/");
    }
}
