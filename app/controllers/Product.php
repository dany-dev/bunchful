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

class Product extends Controller
{

    public function index()
    {
        $product_link = (isset($this->params[0])) ? $this->params[0] : null;
        if($product_link) {
            $data = database()->query("SELECT `product_link` FROM `products` WHERE `auto_generated_link` LIKE '%{$product_link}%'")->fetch_object();
            $url = $data->product_link;
            if($data) {
                header('Location: '.$url);
                die();
            }
        }

        redirect(url());
    }
}
