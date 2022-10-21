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
use Altum\Meta;
use Altum\Middlewares\Authentication;
use Altum\Middlewares\Csrf;
use Altum\Routing\Router;
use Altum\Title;
use MaxMind\Db\Reader;

class Tools extends Controller {

    public function index() {

        if(!settings()->tools->is_enabled) {
            redirect();
        }

        if(settings()->tools->access == 'users') {
            Authentication::guard();
        }

        $tools = require APP_PATH . 'includes/tools.php';

        /* Prepare the View */
        $data = [
            'tools' => $tools,
        ];

        $view = new \Altum\Views\View('tools/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    private function initiate() {
        require_once APP_PATH . 'helpers/LoremIpsum.php';
        require_once APP_PATH . 'helpers/Parsedown.php';

        if(!settings()->tools->is_enabled) {
            redirect();
        }

        if(settings()->tools->access == 'users') {
            Authentication::guard();
        }

        if(!settings()->tools->available_tools->{Router::$method}) {
            redirect('tools');
        }

        /* Meta & title */
        Title::set(sprintf(l('tools.tool_title'), l('tools.' . Router::$method . '.name')));
        Meta::set_description(l('tools.' . Router::$method . '.description'));
        Meta::set_keywords(l('tools.' . Router::$method . '.meta_keywords'));
    }

    public function dns_lookup() {
        $this->initiate();

        $data = [];

        if(!empty($_POST)) {
            $_POST['host'] = input_clean($_POST['host']);

            if(filter_var($_POST['host'], FILTER_VALIDATE_URL)) {
                $_POST['host'] = parse_url($_POST['host'], PHP_URL_HOST);
            }

            /* Check for any errors */
            $required_fields = ['host'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            $data['result'] = [];

            foreach([DNS_A, DNS_AAAA, DNS_CNAME, DNS_MX, DNS_NS, DNS_TXT, DNS_SOA, DNS_CAA] as $dns_type) {
                $dns_records = @dns_get_record($_POST['host'], $dns_type);

                if($dns_records) {
                    foreach($dns_records as $dns_record) {
                        if(!isset($data['result'][$dns_record['type']])) {
                            $data['result'][$dns_record['type']] = [$dns_record];
                        } else {
                            $data['result'][$dns_record['type']][] = $dns_record;
                        }
                    }
                }
            }

            if(empty($data['result'])) {
                Alerts::add_field_error('host', l('tools.dns_lookup.error_message'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                // :)
            }
        }

        $values = [
            'host' => $_POST['host'] ?? '',
        ];

        /* Prepare the View */
        $data['values'] = $values;

        $view = new \Altum\Views\View('tools/dns_lookup', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function ip_lookup() {
        $this->initiate();

        $data = [];

        if(!empty($_POST)) {
            $_POST['ip'] = input_clean($_POST['ip']);

            /* Check for any errors */
            $required_fields = ['ip'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!filter_var($_POST['ip'], FILTER_VALIDATE_IP)) {
                Alerts::add_field_error('ip', l('tools.ip_lookup.error_message'));
            }

            try {
                $maxmind = (new Reader(APP_PATH . 'includes/GeoLite2-City.mmdb'))->get($_POST['ip']);
            } catch(\Exception $exception) {
                Alerts::add_field_error('ip', l('tools.ip_lookup.error_message'));
            }

            if(!$maxmind) {
                Alerts::add_field_error('ip', l('tools.ip_lookup.error_message'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                $data['result'] = $maxmind;
            }
        }

        $values = [
            'ip' => $_POST['ip'] ?? get_ip(),
        ];

        /* Prepare the View */
        $data['values'] = $values;

        $view = new \Altum\Views\View('tools/ip_lookup', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function ssl_lookup() {
        $this->initiate();

        $data = [];

        if(!empty($_POST)) {
            $_POST['host'] = input_clean($_POST['host']);

            if(filter_var($_POST['host'], FILTER_VALIDATE_URL)) {
                $_POST['host'] = parse_url($_POST['host'], PHP_URL_HOST);
            }

            /* Check for any errors */
            $required_fields = ['host'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            /* Check for an SSL certificate */
            $certificate = get_website_certificate('https://' . $_POST['host']);

            if(!$certificate) {
                Alerts::add_field_error('host', l('tools.ssl_lookup.error_message'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                /* Create the new SSL object */
                $ssl = [
                    'organization' => $certificate['issuer']['O'],
                    'country' => $certificate['issuer']['C'],
                    'common_name' => $certificate['issuer']['CN'],
                    'start_datetime' => (new \DateTime())->setTimestamp($certificate['validFrom_time_t'])->format('Y-m-d H:i:s'),
                    'end_datetime' => (new \DateTime())->setTimestamp($certificate['validTo_time_t'])->format('Y-m-d H:i:s'),
                ];

                $data['result'] = $ssl;

            }
        }

        $values = [
            'host' => $_POST['host'] ?? '',
        ];

        /* Prepare the View */
        $data['values'] = $values;

        $view = new \Altum\Views\View('tools/ssl_lookup', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function whois_lookup() {
        $this->initiate();

        $data = [];

        if(!empty($_POST)) {
            $_POST['domain_name'] = input_clean($_POST['domain_name']);

            if(filter_var($_POST['domain_name'], FILTER_VALIDATE_URL)) {
                $_POST['domain_name'] = parse_url($_POST['domain_name'], PHP_URL_HOST);
            }

            /* Check for any errors */
            $required_fields = ['domain_name'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            try {
                $get_whois = \Iodev\Whois\Factory::get()->createWhois();
                $whois_info = $get_whois->loadDomainInfo($_POST['domain_name']);
            } catch (\Exception $e) {
                Alerts::add_field_error('domain_name', l('tools.whois_lookup.error_message'));
            }

            $whois = isset($whois_info) && $whois_info ? [
                'start_datetime' => $whois_info->creationDate ? (new \DateTime())->setTimestamp($whois_info->creationDate)->format('Y-m-d H:i:s') : null,
                'updated_datetime' => $whois_info->updatedDate ? (new \DateTime())->setTimestamp($whois_info->updatedDate)->format('Y-m-d H:i:s') : null,
                'end_datetime' => $whois_info->expirationDate ? (new \DateTime())->setTimestamp($whois_info->expirationDate)->format('Y-m-d H:i:s') : null,
                'registrar' => $whois_info->registrar,
                'nameservers' => $whois_info->nameServers,
            ] : [];

            if(empty($whois)) {
                Alerts::add_field_error('domain_name', l('tools.whois_lookup.error_message'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                $data['result'] = $whois;

            }
        }

        $values = [
            'domain_name' => $_POST['domain_name'] ?? '',
        ];

        /* Prepare the View */
        $data['values'] = $values;

        $view = new \Altum\Views\View('tools/whois_lookup', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function ping() {
        $this->initiate();

        $data = [];

        if(!empty($_POST)) {
            $_POST['type'] = in_array($_POST['type'], ['website', 'ping', 'port']) ? input_clean($_POST['type']) : 'website';
            $_POST['target'] = input_clean($_POST['target']);
            $_POST['port'] = isset($_POST['port']) ? (int) $_POST['port'] : 0;

            /* Check for any errors */
            $required_fields = ['target'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

//            if(empty($whois)) {
//                Alerts::add_field_error('domain_name', l('tools.whois_lookup.error_message'));
//            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                $target = (new \StdClass());
                $target->type = $_POST['type'];
                $target->target = $_POST['target'];
                $target->port = $_POST['port'] ?? 0;
                $target->ping_servers_ids = [1];
                $target->settings = (new \StdClass());
                $target->settings->timeout_seconds = 5;

                $check = ping($target);

                $data['result'] = $check;

            }
        }

        $values = [
            'type' => $_POST['type'] ?? '',
            'target' => $_POST['target'] ?? '',
            'port' => $_POST['port'] ?? '',
        ];

        /* Prepare the View */
        $data['values'] = $values;

        $view = new \Altum\Views\View('tools/ping', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function md5_generator() {
        $this->initiate();

        $data = [];

        if(!empty($_POST)) {
            $_POST['text'] = input_clean($_POST['text']);

            /* Check for any errors */
            $required_fields = ['text'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                $data['result'] = md5($_POST['text']);

            }
        }

        $values = [
            'text' => $_POST['text'] ?? null,
        ];

        /* Prepare the View */
        $data['values'] = $values;

        $view = new \Altum\Views\View('tools/md5_generator', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function base64_converter() {
        $this->initiate();

        $data = [];

        if(!empty($_POST)) {
            $_POST['text'] = input_clean($_POST['text']);
            $_POST['type'] = in_array($_POST['type'], ['encode', 'decode']) ? $_POST['type'] : 'encode';

            /* Check for any errors */
            $required_fields = ['text'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                $data['result'] = $_POST['type'] == 'encode' ? base64_encode($_POST['text']) : base64_decode($_POST['text']);

            }
        }

        $values = [
            'text' => $_POST['text'] ?? null,
            'type' => $_POST['type'] ?? null,
        ];

        /* Prepare the View */
        $data['values'] = $values;

        $view = new \Altum\Views\View('tools/base64_converter', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function base64_image_converter() {
        $this->initiate();

        $data = [];

        if(!empty($_POST)) {
            $_POST['text'] = filter_var($_POST['text'] ?? null, FILTER_SANITIZE_STRING);
            $_POST['type'] = in_array($_POST['type'], ['image_to_base64', 'base64_to_image']) ? $_POST['type'] : 'image_to_base64';

            /* Check for any errors */
            $required_fields = [];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            /* Image uploads */
            $image = !empty($_FILES['image']['name']);

            /* Check for any errors on the logo image */
            if($image) {
                $image_file_name = $_FILES['image']['name'];
                $image_file_extension = explode('.', $image_file_name);
                $image_file_extension = mb_strtolower(end($image_file_extension));
                $image_file_temp = $_FILES['image']['tmp_name'];
                $image_file_type = mime_content_type($image_file_temp);

                if($_FILES['image']['error'] == UPLOAD_ERR_INI_SIZE) {
                    Alerts::add_error(sprintf(l('global.error_message.file_size_limit'), get_max_upload()));
                }

                if($_FILES['image']['error'] && $_FILES['image']['error'] != UPLOAD_ERR_INI_SIZE) {
                    Alerts::add_error(l('global.error_message.file_upload'));
                }

                if(!in_array($image_file_extension, ['gif', 'png', 'jpg', 'jpeg', 'svg'])) {
                    Alerts::add_field_error('image', l('global.error_message.invalid_file_type'));
                }

                if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                    $data['result']['base64'] = base64_encode(file_get_contents($image_file_temp));
                    $data['result']['type'] = $image_file_type;
                }
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                if($_POST['type'] == 'base64_to_image') {
                    $data['result']['base64'] = $_POST['text'];
                }
            }
        }

        $values = [
            'text' => $_POST['text'] ?? null,
            'image' => $_POST['image'] ?? null,
            'type' => $_POST['type'] ?? null,
        ];

        /* Prepare the View */
        $data['values'] = $values;

        $view = new \Altum\Views\View('tools/base64_image_converter', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function url_converter() {
        $this->initiate();

        $data = [];

        if(!empty($_POST)) {
            $_POST['text'] = input_clean($_POST['text']);
            $_POST['type'] = in_array($_POST['type'], ['encode', 'decode']) ? $_POST['type'] : 'encode';

            /* Check for any errors */
            $required_fields = ['text'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                $data['result'] = $_POST['type'] == 'encode' ? urlencode($_POST['text']) : urldecode($_POST['text']);

            }
        }

        $values = [
            'text' => $_POST['text'] ?? null,
            'type' => $_POST['type'] ?? null,
        ];

        /* Prepare the View */
        $data['values'] = $values;

        $view = new \Altum\Views\View('tools/url_converter', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function lorem_ipsum_generator() {
        $this->initiate();

        $data = [];

        if(!empty($_POST)) {
            $_POST['amount'] = (int) $_POST['amount'];
            $_POST['type'] = in_array($_POST['type'], ['paragraphs', 'sentences', 'words']) ? $_POST['type'] : 'paragraphs';

            /* Check for any errors */
            $required_fields = ['amount'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                $lipsum = new \joshtronic\LoremIpsum();

                switch($_POST['type']) {
                    case 'paragraphs':
                        $data['result'] = $lipsum->paragraphs($_POST['amount']);
                        break;

                    case 'sentences':
                        $data['result'] = $lipsum->sentences($_POST['amount']);
                        break;

                    case 'words':
                        $data['result'] = $lipsum->words($_POST['amount']);
                        break;
                }

            }
        }

        $values = [
            'amount' => $_POST['amount'] ?? 1,
            'type' => $_POST['type'] ?? null,
        ];

        /* Prepare the View */
        $data['values'] = $values;

        $view = new \Altum\Views\View('tools/lorem_ipsum_generator', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function markdown_to_html() {
        $this->initiate();

        $data = [];

        if(!empty($_POST)) {

            /* Check for any errors */
            $required_fields = ['markdown'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                $parsedown = new \Parsedown();
                $data['result'] = $parsedown->text($_POST['markdown']);

            }
        }

        $values = [
            'markdown' => $_POST['markdown'] ?? null,
        ];

        /* Prepare the View */
        $data['values'] = $values;

        $view = new \Altum\Views\View('tools/markdown_to_html', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function case_converter() {
        $this->initiate();

        $data = [];

        if(!empty($_POST)) {
            $_POST['text'] = input_clean($_POST['text']);
            $_POST['type'] = in_array($_POST['type'], ['lowercase', 'uppercase', 'sentencecase', 'camelcase', 'pascalcase', 'capitalcase', 'constantcase', 'dotcase', 'snakecase', 'paramcase']) ? $_POST['type'] : 'lowercase';

            /* Check for any errors */
            $required_fields = ['text'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                switch($_POST['type']) {
                    case 'lowercase':
                        $data['result'] = mb_strtolower($_POST['text']);
                        break;

                    case 'uppercase':
                        $data['result'] = mb_strtoupper($_POST['text']);
                        break;

                    case 'sentencecase':
                        $data['result'] = ucfirst($_POST['text']);
                        break;

                    case 'camelcase':
                        $words = explode(' ', $_POST['text']);

                        $pascalcase_words = array_map(function($word) {
                            return ucfirst($word);
                        }, $words);

                        $pascalcase = implode($pascalcase_words);

                        $data['result'] = lcfirst($pascalcase);
                        break;

                    case 'pascalcase':
                        $words = explode(' ', string_filter_alphanumeric($_POST['text']));

                        $pascalcase_words = array_map(function($word) {
                            return ucfirst($word);
                        }, $words);

                        $pascalcase = implode($pascalcase_words);

                        $data['result'] = $pascalcase;
                        break;

                    case 'capitalcase':
                        $data['result'] = ucwords($_POST['text']);
                        break;

                    case 'constantcase':
                        $data['result'] = mb_strtoupper(str_replace(' ', '_', trim(string_filter_alphanumeric($_POST['text']))));
                        break;

                    case 'dotcase':
                        $data['result'] = mb_strtolower(str_replace(' ', '.', trim(string_filter_alphanumeric($_POST['text']))));
                        break;

                    case 'snakecase':
                        $data['result'] = mb_strtolower(str_replace(' ', '_', trim(string_filter_alphanumeric($_POST['text']))));
                        break;

                    case 'paramcase':
                        $data['result'] = mb_strtolower(str_replace(' ', '-', trim(string_filter_alphanumeric($_POST['text']))));
                        break;
                }


            }
        }

        $values = [
            'text' => $_POST['text'] ?? null,
            'type' => $_POST['type'] ?? null,
        ];

        /* Prepare the View */
        $data['values'] = $values;

        $view = new \Altum\Views\View('tools/case_converter', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function uuid_v4_generator() {
        $this->initiate();

        $data = [];

        /* Generate UUID */
        $data['result'] = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',

            // 32 bits for "time_low"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),

            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,

            // 48 bits for "node"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );

        $values = [];

        /* Prepare the View */
        $data['values'] = $values;

        $view = new \Altum\Views\View('tools/uuid_v4_generator', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function bcrypt_generator() {
        $this->initiate();

        $data = [];

        if(!empty($_POST)) {
            $_POST['text'] = input_clean($_POST['text']);

            /* Check for any errors */
            $required_fields = ['text'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                $data['result'] = password_hash($_POST['text'], PASSWORD_DEFAULT);

            }
        }

        $values = [
            'text' => $_POST['text'] ?? null,
        ];

        /* Prepare the View */
        $data['values'] = $values;

        $view = new \Altum\Views\View('tools/bcrypt_generator', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function password_generator() {
        $this->initiate();

        $data = [];

        if(!empty($_POST)) {
            $_POST['characters'] = (int) mb_substr($_POST['characters'], 0, 2048);
            $_POST['numbers'] = isset($_POST['numbers']);
            $_POST['symbols'] = isset($_POST['symbols']);
            $_POST['lowercase'] = isset($_POST['lowercase']);
            $_POST['uppercase'] = isset($_POST['uppercase']);

            /* Check for any errors */
            $required_fields = ['characters'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                $available_characters = '';

                if($_POST['numbers']) $available_characters .= '0123456789';
                if($_POST['symbols']) $available_characters .= '!@#$%^&*()_+=-[],./\\\'<>?:"|{}';
                if($_POST['lowercase']) $available_characters .= 'abcdefghijklmnopqrstuvwxyz';
                if($_POST['uppercase']) $available_characters .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

                $available_characters = str_split($available_characters);
                shuffle($available_characters);

                $password = '';

                for($i = 1; $i <= $_POST['characters']; $i++) {
                    $password .= $available_characters[array_rand($available_characters)];
                }

                $data['result'] = $password;

            }
        }

        $values = [
            'characters' => $_POST['characters'] ?? 8,
            'numbers' => $_POST['numbers'] ?? true,
            'symbols' => $_POST['symbols'] ?? true,
            'lowercase' => $_POST['lowercase'] ?? true,
            'uppercase' => $_POST['uppercase'] ?? true,
        ];

        /* Prepare the View */
        $data['values'] = $values;

        $view = new \Altum\Views\View('tools/password_generator', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function password_strength_checker() {
        $this->initiate();

        $data = [];

        $values = [
            'password' => $_POST['password'] ?? null,
        ];

        /* Prepare the View */
        $data['values'] = $values;

        $view = new \Altum\Views\View('tools/password_strength_checker', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function slug_generator() {
        $this->initiate();

        $data = [];

        if(!empty($_POST)) {
            $_POST['text'] = input_clean($_POST['text']);

            /* Check for any errors */
            $required_fields = ['text'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                mb_internal_encoding('utf-8');

                /* Replace all non words characters with the specified $delimiter */
                $string = preg_replace('/[^\p{L}\d-]+/u', '-', $_POST['text']);

                /* Check for double $delimiters and remove them so it only will be 1 delimiter */
                $string = preg_replace('/-+/u', '-', $string);

                /* Remove the $delimiter character from the start and the end of the string */
                $string = trim($string, '-');

                /* lowercase */
                $string = mb_strtolower($string);

                $data['result'] = $string;

            }
        }

        $values = [
            'text' => $_POST['text'] ?? null,
        ];

        /* Prepare the View */
        $data['values'] = $values;

        $view = new \Altum\Views\View('tools/slug_generator', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function html_minifier() {

        $this->initiate();

        $data = [];

        if(!empty($_POST)) {

            /* Check for any errors */
            $required_fields = ['html'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            $htmldoc = new \hexydec\html\htmldoc();
            $htmldoc_load = $htmldoc->load($_POST['html']);

            if(!$htmldoc_load) {
                Alerts::add_field_error('css', l('tools.html_minifier.error_message'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                $htmldoc->minify();
                $data['result'] = $htmldoc->save() ?? null;

                $data['html_characters'] = mb_strlen($_POST['html']);
                $data['minified_html_characters'] = mb_strlen($data['result']);
            }
        }

        $values = [
            'html' => $_POST['html'] ?? null,
        ];

        /* Prepare the View */
        $data['values'] = $values;

        $view = new \Altum\Views\View('tools/html_minifier', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function css_minifier() {

        $this->initiate();

        $data = [];

        if(!empty($_POST)) {

            /* Check for any errors */
            $required_fields = ['css'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            $cssdoc = new \hexydec\css\cssdoc();
            $cssdoc_load = $cssdoc->load($_POST['css']);

            if(!$cssdoc_load) {
                Alerts::add_field_error('css', l('tools.css_minifier.error_message'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                $cssdoc->minify();
                $data['result'] = $cssdoc->save() ?? null;

                $data['css_characters'] = mb_strlen($_POST['css']);
                $data['minified_css_characters'] = mb_strlen($data['result']);
            }
        }

        $values = [
            'css' => $_POST['css'] ?? null,
        ];

        /* Prepare the View */
        $data['values'] = $values;

        $view = new \Altum\Views\View('tools/css_minifier', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function js_minifier() {

        $this->initiate();

        $data = [];

        if(!empty($_POST)) {

            /* Check for any errors */
            $required_fields = ['js'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            $jsdoc = new \hexydec\jslite\jslite();
            $jsdoc_load = $jsdoc->load($_POST['js']);

            if(!$jsdoc_load) {
                Alerts::add_field_error('js', l('tools.js_minifier.error_message'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                $jsdoc->minify();
                $data['result'] = $jsdoc->compile() ?? null;

                $data['js_characters'] = mb_strlen($_POST['js']);
                $data['minified_js_characters'] = mb_strlen($data['result']);
            }
        }

        $values = [
            'js' => $_POST['js'] ?? null,
        ];

        /* Prepare the View */
        $data['values'] = $values;

        $view = new \Altum\Views\View('tools/js_minifier', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function user_agent_parser() {
        $this->initiate();

        $data = [];

        if(!empty($_POST)) {

            /* Check for any errors */
            $required_fields = ['user_agent'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                $whichbrowser = new \WhichBrowser\Parser($_POST['user_agent']);

                $data['result']['browser_name'] = $whichbrowser->browser->name ?? null;
                $data['result']['browser_version'] = $whichbrowser->browser->version->value ?? null;
                $data['result']['os_name'] = $whichbrowser->os->name ?? null;
                $data['result']['os_version'] = $whichbrowser->os->version->value ?? null;
                $data['result']['device_type'] = $whichbrowser->device->type ?? null;

            }
        }

        $values = [
            'user_agent' => $_POST['user_agent'] ?? $_SERVER['HTTP_USER_AGENT'] ?? null,
        ];

        /* Prepare the View */
        $data['values'] = $values;

        $view = new \Altum\Views\View('tools/user_agent_parser', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function website_hosting_checker() {
        $this->initiate();

        $data = [];

        if(!empty($_POST)) {
            $_POST['host'] = input_clean($_POST['host']);

            if(filter_var($_POST['host'], FILTER_VALIDATE_URL)) {
                $_POST['host'] = parse_url($_POST['host'], PHP_URL_HOST);
            }

            /* Check for any errors */
            $required_fields = ['host'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            /* Get ip of host */
            $host_ip = gethostbyname($_POST['host']);

            /* Check via ip-api */
            $response = \Unirest\Request::get('http://ip-api.com/json/' . $host_ip);

            if(empty($response->raw_body) || $response->body->status == 'fail') {
                Alerts::add_field_error('host', l('tools.website_hosting_checker.error_message'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                $data['result'] = $response->body;

            }
        }

        $values = [
            'host' => $_POST['host'] ?? '',
        ];

        /* Prepare the View */
        $data['values'] = $values;

        $view = new \Altum\Views\View('tools/website_hosting_checker', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function character_counter() {
        $this->initiate();

        $data = [];

        if(!empty($_POST)) {

            /* Check for any errors */
            $required_fields = ['text'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                $data['result']['characters'] = mb_strlen($_POST['text']);
                $data['result']['words'] = str_word_count($_POST['text']);
                $data['result']['lines'] = substr_count($_POST['text'], "\n") + 1;;

            }
        }

        $values = [
            'text' => $_POST['text'] ?? null,
        ];

        /* Prepare the View */
        $data['values'] = $values;

        $view = new \Altum\Views\View('tools/character_counter', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function url_parser() {
        $this->initiate();

        $data = [];

        if(!empty($_POST)) {
            $_POST['url'] = filter_var($_POST['url'], FILTER_SANITIZE_URL);

            /* Check for any errors */
            $required_fields = ['url'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                $parsed_url = parse_url($_POST['url']);

                if(isset($parsed_url['query'])) {
                    $query_string_array = explode('&', $parsed_url['query']);
                    $query_array = [];
                    foreach($query_string_array as $query_string_value) {
                        $query_string_value_exploded = explode('=', $query_string_value);
                        $query_array[$query_string_value_exploded[0]] = $query_string_value_exploded[1];
                    }

                    $parsed_url['query_array'] = $query_array;
                }

                $data['result'] = $parsed_url;

            }
        }

        $values = [
            'url' => $_POST['url'] ?? null,
        ];

        /* Prepare the View */
        $data['values'] = $values;

        $view = new \Altum\Views\View('tools/url_parser', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function color_converter() {
        $this->initiate();

        $data = [];

        if(!empty($_POST)) {
            $_POST['color'] = filter_var($_POST['color'], FILTER_SANITIZE_STRING);

            /* Check for any errors */
            $required_fields = ['color'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            $type = null;

            if(mb_substr($_POST['color'], 0, strlen('#')) === '#') {
                $type = 'hex';
            }

            if(mb_substr($_POST['color'], 0, strlen('#')) === '#' && mb_strlen($_POST['color']) > 7) {
                $type = 'hexa';
            }

            foreach(['rgb', 'rgba', 'hsl', 'hsla', 'hsv'] as $color_type) {
                if(mb_substr($_POST['color'], 0, strlen($color_type)) === $color_type) {
                    $type = $color_type;
                }
            }

            if(!$type) {
                Alerts::add_field_error('color', l('tools.color_converter.error_message'));
            } else {
                try {
                    $class = '\OzdemirBurak\Iris\Color\\' . ucfirst($type);
                    $color = new $class($_POST['color']);
                } catch (\Exception $exception) {
                    Alerts::add_field_error('color', l('tools.color_converter.error_message'));
                }
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                $data['result']['hex'] = $color->toHex();
                $data['result']['hexa'] = $color->toHexa();
                $data['result']['rgb'] = $color->toRgb();
                $data['result']['rgba'] = $color->toRgba();
                $data['result']['hsv'] = $color->toHsv();
                $data['result']['hsl'] = $color->toHsl();
                $data['result']['hsla'] = $color->toHsla();

            }
        }

        $values = [
            'color' => $_POST['color'] ?? null,
        ];

        /* Prepare the View */
        $data['values'] = $values;

        $view = new \Altum\Views\View('tools/color_converter', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function http_headers_lookup() {
        $this->initiate();

        $data = [];

        if(!empty($_POST)) {
            $_POST['url'] = filter_var($_POST['url'], FILTER_SANITIZE_URL);

            /* Check for any errors */
            $required_fields = ['url'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            try {
                $response = \Unirest\Request::get($_POST['url']);
            } catch (\Exception $exception) {
                Alerts::add_field_error('url', l('tools.http_headers_lookup.error_message'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                $data['result'] = $response->headers;

            }
        }

        $values = [
            'url' => $_POST['url'] ?? null,
        ];

        /* Prepare the View */
        $data['values'] = $values;

        $view = new \Altum\Views\View('tools/http_headers_lookup', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function duplicate_lines_remover() {
        $this->initiate();

        $data = [];

        if(!empty($_POST)) {

            /* Check for any errors */
            $required_fields = ['text'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                $lines_array = explode("\n", $_POST['text']);
                $new_lines_array = array_unique($lines_array);

                $data['result']['text'] = implode("\n", $new_lines_array);
                $data['result']['lines'] = substr_count($_POST['text'], "\n") + 1;
                $data['result']['new_lines'] = count($new_lines_array);
                $data['result']['removed_lines'] = count($lines_array) - count($new_lines_array);

            }
        }

        $values = [
            'text' => $_POST['text'] ?? null,
        ];

        /* Prepare the View */
        $data['values'] = $values;

        $view = new \Altum\Views\View('tools/duplicate_lines_remover', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function text_to_speech() {
        $this->initiate();

        $data = [];

        if(isset($_GET['text']) && isset($_GET['language_code'])) {
            $_GET['text'] = trim(filter_var($_GET['text'], FILTER_SANITIZE_STRING));
            $_GET['language_code'] = trim(filter_var($_GET['language_code'], FILTER_SANITIZE_STRING));
            $text = rawurlencode(htmlspecialchars($_GET['text']));
            $audio = file_get_contents('https://translate.google.com/translate_tts?ie=UTF-8&client=gtx&q=' . $text . '&tl=' . $_GET['language_code']);

            header('Cache-Control: private');
            header('Content-type: audio/mpeg, audio/x-mpeg, audio/x-mpeg-3, audio/mpeg3');
            header('Content-Transfer-Encoding: binary');
            header('Content-Disposition: filename="' . get_slug($_GET['text']) . '.mp3"');
            header('Content-Length: ' . strlen($audio));

            echo $audio;

            die();
        }

        if(!empty($_POST)) {
            $_POST['text'] = trim(filter_var($_POST['text'], FILTER_SANITIZE_STRING));
            $_POST['language_code'] = trim(filter_var($_POST['language_code'], FILTER_SANITIZE_STRING));

            /* Check for any errors */
            $required_fields = ['text', 'language_code'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                $data['result'] = true;
            }
        }

        $values = [
            'text' => $_POST['text'] ?? null,
            'language_code' => $_POST['language_code'] ?? 'en-US',
        ];

        /* Prepare the View */
        $data['values'] = $values;

        $view = new \Altum\Views\View('tools/text_to_speech', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function idn_punnycode_converter() {
        $this->initiate();

        $data = [];

        if(!empty($_POST)) {
            $_POST['content'] = trim(filter_var($_POST['content'], FILTER_SANITIZE_STRING));
            $_POST['type'] = in_array($_POST['type'], ['to_punnycode', 'to_idn']) ? $_POST['type'] : 'to_punnycode';

            /* Check for any errors */
            $required_fields = ['content'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                $data['result'] = $_POST['type'] == 'to_punnycode' ? idn_to_ascii($_POST['content']) : idn_to_utf8($_POST['content']);

            }
        }

        $values = [
            'content' => $_POST['content'] ?? null,
            'type' => $_POST['type'] ?? null,
        ];

        /* Prepare the View */
        $data['values'] = $values;

        $view = new \Altum\Views\View('tools/idn_punnycode_converter', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function json_validator_beautifier() {
        $this->initiate();

        $data = [];

        if(!empty($_POST)) {

            /* Check for any errors */
            $required_fields = ['json'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            $data['result'] = json_decode($_POST['json']);

            if(!$data['result']) {
                Alerts::add_field_error('json', l('tools.json_validator_beautifier.error_message'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {


            }
        }

        $values = [
            'json' => $_POST['json'] ?? null,
        ];

        /* Prepare the View */
        $data['values'] = $values;

        $view = new \Altum\Views\View('tools/json_validator_beautifier', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function qr_code_reader() {
        $this->initiate();

        $data = [];

        $values = [
            'image' => $_POST['image'] ?? null,
        ];

        /* Prepare the View */
        $data['values'] = $values;

        $view = new \Altum\Views\View('tools/qr_code_reader', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function meta_tags_checker() {
        $this->initiate();

        $data = [];

        if(!empty($_POST)) {
            $_POST['url'] = trim(filter_var($_POST['url'], FILTER_SANITIZE_URL));

            /* Check for any errors */
            $required_fields = ['url'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }


            /* Get the URL source */
            try {
                $response = \Unirest\Request::get($_POST['url']);
            } catch (\Exception $exception) {
                Alerts::add_field_error('url', l('tools.meta_tags_checker.error_message'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                $doc = new \DOMDocument();
                @$doc->loadHTML($response->raw_body);

                $meta_tags_array = $doc->getElementsByTagName('meta');
                $meta_tags = [];

                for($i = 0; $i < $meta_tags_array->length; $i++) {
                    $meta_tag = $meta_tags_array->item($i);

                    $meta_tag_key = !empty($meta_tag->getAttribute('name')) ? $meta_tag->getAttribute('name') : $meta_tag->getAttribute('property');

                    if($meta_tag_key) {
                        $meta_tags[$meta_tag_key] = $meta_tag->getAttribute('content');
                    }
                }

                $data['result'] = $meta_tags;
            }
        }

        $values = [
            'url' => $_POST['url'] ?? '',
        ];

        /* Prepare the View */
        $data['values'] = $values;

        $view = new \Altum\Views\View('tools/meta_tags_checker', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function exif_reader() {
        $this->initiate();

        $data = [];

        $values = [
            'image' => $_POST['image'] ?? null,
        ];

        /* Prepare the View */
        $data['values'] = $values;

        $view = new \Altum\Views\View('tools/exif_reader', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function sql_beautifier() {
        $this->initiate();

        $data = [];

        if(!empty($_POST)) {

            /* Check for any errors */
            $required_fields = ['sql'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                $data['result'] = (new \Doctrine\SqlFormatter\SqlFormatter())->format($_POST['sql']);
            }
        }

        $values = [
            'sql' => $_POST['sql'] ?? null,
        ];

        /* Prepare the View */
        $data['values'] = $values;

        $view = new \Altum\Views\View('tools/sql_beautifier', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function html_entity_converter() {
        $this->initiate();

        $data = [];

        if(!empty($_POST)) {
            $_POST['type'] = in_array($_POST['type'], ['encode', 'decode']) ? $_POST['type'] : 'encode';

            /* Check for any errors */
            $required_fields = ['text'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                $data['result'] = $_POST['type'] == 'encode' ? htmlentities(htmlentities($_POST['text'])) : html_entity_decode($_POST['text']);

            }
        }

        $values = [
            'text' => $_POST['text'] ?? null,
            'type' => $_POST['type'] ?? null,
        ];

        /* Prepare the View */
        $data['values'] = $values;

        $view = new \Altum\Views\View('tools/html_entity_converter', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function binary_converter() {
        $this->initiate();

        $data = [];

        if(!empty($_POST)) {
            $_POST['content'] = trim(filter_var($_POST['content'], FILTER_SANITIZE_STRING));
            $_POST['type'] = in_array($_POST['type'], ['to_binary', 'to_text']) ? $_POST['type'] : 'to_binary';

            /* Check for any errors */
            $required_fields = ['content'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                function string_to_binary($string) {
                    $characters = str_split($string);

                    $binary = [];
                    foreach ($characters as $character) {
                        $data = unpack('H*', $character);
                        $binary[] = base_convert($data[1], 16, 2);
                    }

                    return implode(' ', $binary);
                }

                function binary_to_string($binary) {
                    $binaries = explode(' ', $binary);

                    $string = null;
                    foreach ($binaries as $binary) {
                        $string .= pack('H*', dechex(bindec($binary)));
                    }

                    return $string;
                }

                switch($_POST['type']) {
                    case 'to_binary':
                        $data['result'] = string_to_binary($_POST['content']);
                        break;

                    case 'to_text':
                        $data['result'] = binary_to_string($_POST['content']);
                        break;
                }

            }
        }

        $values = [
            'content' => $_POST['content'] ?? null,
            'type' => $_POST['type'] ?? null,
        ];

        /* Prepare the View */
        $data['values'] = $values;

        $view = new \Altum\Views\View('tools/binary_converter', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function hex_converter() {
        $this->initiate();

        $data = [];

        if(!empty($_POST)) {
            $_POST['content'] = trim(filter_var($_POST['content'], FILTER_SANITIZE_STRING));
            $_POST['type'] = in_array($_POST['type'], ['to_hex', 'to_text']) ? $_POST['type'] : 'to_hex';

            /* Check for any errors */
            $required_fields = ['content'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                switch($_POST['type']) {
                    case 'to_hex':
                        $data['result'] = bin2hex($_POST['content']);
                        break;

                    case 'to_text':
                        $data['result'] = hex2bin($_POST['content']);
                        break;
                }

            }
        }

        $values = [
            'content' => $_POST['content'] ?? null,
            'type' => $_POST['type'] ?? null,
        ];

        /* Prepare the View */
        $data['values'] = $values;

        $view = new \Altum\Views\View('tools/hex_converter', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function ascii_converter() {
        $this->initiate();

        $data = [];

        if(!empty($_POST)) {
            $_POST['content'] = trim(filter_var($_POST['content'], FILTER_SANITIZE_STRING));
            $_POST['type'] = in_array($_POST['type'], ['to_ascii', 'to_text']) ? $_POST['type'] : 'to_ascii';

            /* Check for any errors */
            $required_fields = ['content'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                switch($_POST['type']) {
                    case 'to_ascii':
                        $data['result'] = '';

                        for($i = 0; $i < strlen($_POST['content']); $i++) {
                            $data['result'] .= ord($_POST['content'][$i]) . ' ';
                        }

                        break;

                    case 'to_text':
                        $content = explode(' ', $_POST['content']);
                        $data['result'] = '';

                        foreach($content as $value) {
                            $data['result'] .= chr($value);
                        }

                        break;
                }

            }
        }

        $values = [
            'content' => $_POST['content'] ?? null,
            'type' => $_POST['type'] ?? null,
        ];

        /* Prepare the View */
        $data['values'] = $values;

        $view = new \Altum\Views\View('tools/ascii_converter', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function decimal_converter() {
        $this->initiate();

        $data = [];

        if(!empty($_POST)) {
            $_POST['content'] = trim(filter_var($_POST['content'], FILTER_SANITIZE_STRING));
            $_POST['type'] = in_array($_POST['type'], ['to_decimal', 'to_text']) ? $_POST['type'] : 'to_decimal';

            /* Check for any errors */
            $required_fields = ['content'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                switch($_POST['type']) {
                    case 'to_decimal':
                        $data['result'] = '';

                        for($i = 0; $i < strlen($_POST['content']); $i++) {
                            $data['result'] .= ord($_POST['content'][$i]) . ' ';
                        }

                        break;

                    case 'to_text':
                        $content = explode(' ', $_POST['content']);
                        $data['result'] = '';

                        foreach($content as $value) {
                            $data['result'] .= chr($value);
                        }

                        break;
                }

            }
        }

        $values = [
            'content' => $_POST['content'] ?? null,
            'type' => $_POST['type'] ?? null,
        ];

        /* Prepare the View */
        $data['values'] = $values;

        $view = new \Altum\Views\View('tools/decimal_converter', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function octal_converter() {
        $this->initiate();

        $data = [];

        if(!empty($_POST)) {
            $_POST['content'] = trim(filter_var($_POST['content'], FILTER_SANITIZE_STRING));
            $_POST['type'] = in_array($_POST['type'], ['to_octal', 'to_text']) ? $_POST['type'] : 'to_octal';

            /* Check for any errors */
            $required_fields = ['content'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                switch($_POST['type']) {
                    case 'to_octal':
                        $data['result'] = '';

                        for($i = 0; $i < strlen($_POST['content']); $i++) {
                            $data['result'] .= decoct(ord($_POST['content'][$i])) . ' ';
                        }

                        break;

                    case 'to_text':
                        $content = explode(' ', $_POST['content']);
                        $data['result'] = '';

                        foreach($content as $value) {
                            $data['result'] .= chr(octdec($value));
                        }

                        break;
                }

            }
        }

        $values = [
            'content' => $_POST['content'] ?? null,
            'type' => $_POST['type'] ?? null,
        ];

        /* Prepare the View */
        $data['values'] = $values;

        $view = new \Altum\Views\View('tools/octal_converter', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function morse_converter() {
        $this->initiate();

        $data = [];

        if(!empty($_POST)) {
            $_POST['content'] = filter_var($_POST['content'], FILTER_SANITIZE_STRING);
            $_POST['type'] = in_array($_POST['type'], ['to_morse', 'to_text']) ? $_POST['type'] : 'to_morse';

            /* Check for any errors */
            $required_fields = ['content'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                $morse = new \Morse\Text();

                switch($_POST['type']) {
                    case 'to_morse':
                        $data['result'] = $morse->toMorse($_POST['content']);
                        break;

                    case 'to_text':
                        $data['result'] = $morse->fromMorse($_POST['content']);
                        break;
                }

            }
        }

        $values = [
            'content' => $_POST['content'] ?? null,
            'type' => $_POST['type'] ?? null,
        ];

        /* Prepare the View */
        $data['values'] = $values;

        $view = new \Altum\Views\View('tools/morse_converter', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function mailto_link_generator() {
        $this->initiate();

        $data = [];

        $values = [];

        /* Prepare the View */
        $data['values'] = $values;

        $view = new \Altum\Views\View('tools/mailto_link_generator', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function youtube_thumbnail_downloader() {
        $this->initiate();

        $data = [];

        if(!empty($_POST)) {
            $_POST['url'] = filter_var($_POST['url'], FILTER_SANITIZE_URL);

            /* Check for any errors */
            $required_fields = ['url'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!preg_match('/^(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((?:\w|-){11})(?:&list=(\S+))?$/', $_POST['url'], $match)) {
                Alerts::add_field_error('url', l('tools.youtube_thumbnail_downloader.invalid_url'));
            }

            $youtube_video_id = $match[1];

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                $data['result'] = [];

                foreach(['default', 'mqdefault', 'hqdefault', 'sddefault', 'maxresdefault'] as $key) {
                    $data['result'][$key] = sprintf('https://img.youtube.com/vi/%s/%s.jpg', $youtube_video_id, $key);
                }

            }
        }

        $values = [
            'url' => $_POST['url'] ?? null,
        ];

        /* Prepare the View */
        $data['values'] = $values;

        $view = new \Altum\Views\View('tools/youtube_thumbnail_downloader', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function safe_url_checker() {
        $this->initiate();

        $data = [];

        if(!empty($_POST)) {
            $_POST['url'] = filter_var($_POST['url'], FILTER_SANITIZE_URL);

            /* Check for any errors */
            $required_fields = ['url'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                $data['result'] = !google_safe_browsing_check($_POST['url'], settings()->links->google_safe_browsing_api_key);
            }
        }

        $values = [
            'url' => $_POST['url'] ?? null,
        ];

        /* Prepare the View */
        $data['values'] = $values;

        $view = new \Altum\Views\View('tools/safe_url_checker', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function utm_link_generator() {
        $this->initiate();

        $data = [];

        $values = [];

        /* Prepare the View */
        $data['values'] = $values;

        $view = new \Altum\Views\View('tools/utm_link_generator', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function whatsapp_link_generator() {
        $this->initiate();

        $data = [];

        $values = [];

        /* Prepare the View */
        $data['values'] = $values;

        $view = new \Altum\Views\View('tools/whatsapp_link_generator', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function youtube_timestamp_link_generator() {
        $this->initiate();

        $data = [];

        $values = [];

        /* Prepare the View */
        $data['values'] = $values;

        $view = new \Altum\Views\View('tools/youtube_timestamp_link_generator', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function google_cache_checker() {
        $this->initiate();

        $data = [];

        if(!empty($_POST)) {
            $_POST['url'] = filter_var($_POST['url'], FILTER_SANITIZE_URL);

            /* Check for any errors */
            $required_fields = ['url'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            /* Get the URL source */
            $url = 'http://webcache.googleusercontent.com/search?hl=en&q=cache:' . urlencode($_POST['url']) . '&strip=0&vwsrc=1';
            try {
                $response = \Unirest\Request::get($url, [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 6.3; WOW64; rv:38.0) Gecko/20100101 Firefox/38.0'
                ]);
            } catch (\Exception $exception) {
                Alerts::add_field_error('url', l('tools.google_cache_checker.error_message'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                /* Get details from the google query result */
                preg_match('/It is a snapshot of the page as it appeared on ([^\.]+)\./i', $response->raw_body, $matches);

                $data['result'] = empty($matches) ? false : $matches[1];
            }
        }

        $values = [
            'url' => $_POST['url'] ?? null,
        ];

        /* Prepare the View */
        $data['values'] = $values;

        $view = new \Altum\Views\View('tools/google_cache_checker', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function url_redirect_checker() {
        $this->initiate();

        $data = [];

        if(!empty($_POST)) {
            $_POST['url'] = filter_var($_POST['url'], FILTER_SANITIZE_URL);

            /* Check for any errors */
            $required_fields = ['url'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            /* Save locations of each request */
            $locations = [];

            /* Get the URL source */
            $i = 1;
            $url = $_POST['url'];

            /* Start the requests process */
            do {
                try {
                    \Unirest\Request::curlOpt(CURLOPT_FOLLOWLOCATION, 0);
                    $response = \Unirest\Request::get($url, [
                        'User-Agent' => settings()->main->title . ' ' . url('tools/url_redirect_checker') . '/1.0'
                    ]);

                    $locations[] = [
                        'url' => $url,
                        'status_code' => $response->code,
                        'redirect_to' => $response->headers['Location'] ?? $response->headers['location'] ?? null,
                    ];

                    $i++;
                    $url = $response->headers['Location'] ?? $response->headers['location'] ?? null;
                } catch (\Exception $exception) {
                    Alerts::add_field_error('url', l('tools.url_redirect_checker.error_message'));
                    break;
                }
            } while($i <= 10 && ($response->code == 301 || $response->code == 302));

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                $data['result'] = $locations;
            }
        }

        $values = [
            'url' => $_POST['url'] ?? null,
        ];

        /* Prepare the View */
        $data['values'] = $values;

        $view = new \Altum\Views\View('tools/url_redirect_checker', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function image_optimizer() {
        $this->initiate();

        $data = [];

        if(!empty($_POST)) {
            $_POST['quality'] = (int) $_POST['quality'];
            $_POST['quality'] = $_POST['quality'] < 1 || $_POST['quality'] > 100 ? 75 : $_POST['quality'];

            /* Check for any errors */
            $required_fields = [];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            /* Image uploads */
            $image = !empty($_FILES['image']['name']);

            /* Check for any errors on the logo image */
            if(!$image) {
                Alerts::add_field_error('image', l('global.error_message.empty_field'));
            }

            if($image) {
                $image_file_name = $_FILES['image']['name'];
                $image_file_extension = explode('.', $image_file_name);
                $image_file_extension = mb_strtolower(end($image_file_extension));
                $image_file_temp = $_FILES['image']['tmp_name'];
                $image_file_type = mime_content_type($image_file_temp);

                if($_FILES['image']['error'] == UPLOAD_ERR_INI_SIZE) {
                    Alerts::add_error(sprintf(l('global.error_message.file_size_limit'), get_max_upload()));
                }

                if($_FILES['image']['size'] > 5 * 1000000) {
                    Alerts::add_error(sprintf(l('global.error_message.file_size_limit'), 5));
                }

                if($_FILES['image']['error'] && $_FILES['image']['error'] != UPLOAD_ERR_INI_SIZE) {
                    Alerts::add_error(l('global.error_message.file_upload'));
                }

                if(!in_array($image_file_extension, ['gif', 'png', 'jpg', 'jpeg', 'webp'])) {
                    Alerts::add_field_error('image', l('global.error_message.invalid_file_type'));
                }

                /* Generate new name for image */
                $image_new_name = md5(time() . rand()) . '.' . $image_file_extension;

                /* Build the request to the API */
                $mime = mime_content_type($image_file_temp);
                $output = new \CURLFile($image_file_temp, $mime, $image_new_name);

                $body = \Unirest\Request\Body::multipart([
                    'files' => $output,
                ]);

                try {
                    $response = \Unirest\Request::post('http://api.resmush.it/?qlty=' . $_POST['quality'], [], $body);
                } catch (\Exception $exception) {
                    Alerts::add_field_error('image', l('tools.image_optimizer.error_message'));
                }

                if(isset($response->body->error)) {
                    Alerts::add_field_error('image', l('tools.image_optimizer.error_message'));
                }
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                $data['result']['file_url'] = $response->body->dest;
                $data['result']['original_size'] = $response->body->src_size;
                $data['result']['new_size'] = $response->body->dest_size;
                $data['result']['name'] = $image_new_name;
            }
        }

        $values = [
            'image' => $_POST['image'] ?? null,
            'quality' => $_POST['quality'] ?? 75,
        ];

        /* Prepare the View */
        $data['values'] = $values;

        $view = new \Altum\Views\View('tools/image_optimizer', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function text_separator() {
        $this->initiate();

        $data = [];

        if(!empty($_POST)) {
            $_POST['text'] = filter_var($_POST['text'], FILTER_SANITIZE_STRING);
            $_POST['separated_by'] = in_array($_POST['separated_by'], ['new_line', 'space', ';', '-', '|', '.']) ? $_POST['separated_by'] : 'new_line';
            $_POST['separate_by'] = in_array($_POST['separate_by'], ['new_line', 'space', ';', '-', '|', '.']) ? $_POST['separate_by'] : 'space';

            /* Check for any errors */
            $required_fields = ['text'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                $search_for = $replace_with = '';

                switch($_POST['separated_by']) {
                    case 'new_line':
                        $search_for = "\r\n";
                        break;

                    case 'space':
                        $search_for = " ";
                        break;

                    default:
                        $search_for = $_POST['separated_by'];
                        break;
                }

                switch($_POST['separate_by']) {
                    case 'new_line':
                        $replace_with = "\r\n";
                        break;

                    case 'space':
                        $replace_with = " ";
                        break;

                    default:
                        $replace_with = $_POST['separate_by'];
                        break;
                }

                $data['result'] = str_replace($search_for, $replace_with, $_POST['text']);

            }
        }

        $values = [
            'text' => $_POST['text'] ?? null,
            'separated_by' => $_POST['separated_by'] ?? 'new_line',
            'separate_by' => $_POST['separate_by'] ?? 'space',
        ];

        /* Prepare the View */
        $data['values'] = $values;

        $view = new \Altum\Views\View('tools/text_separator', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function email_extractor() {
        $this->initiate();

        $data = [];

        if(!empty($_POST)) {
            $_POST['text'] = filter_var($_POST['text'], FILTER_SANITIZE_STRING);

            /* Check for any errors */
            $required_fields = ['text'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                $pattern = '/[a-z0-9_\-\+\.]+@[a-z0-9\-]+\.([a-z]{2,4})(?:\.[a-z]{2})?/i';
                preg_match_all($pattern, $_POST['text'], $matches);

                $data['result']['count'] = count($matches[0] ?? []);
                $data['result']['emails'] = $matches[0] ?? [];

            }
        }

        $values = [
            'text' => $_POST['text'] ?? null,
        ];

        /* Prepare the View */
        $data['values'] = $values;

        $view = new \Altum\Views\View('tools/email_extractor', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function url_extractor() {
        $this->initiate();

        $data = [];

        if(!empty($_POST)) {
            $_POST['text'] = filter_var($_POST['text'], FILTER_SANITIZE_STRING);

            /* Check for any errors */
            $required_fields = ['text'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                $pattern = '/(http|https):\/\/([\w_-]+(?:(?:\.[\w_-]+)+))([\w.,@?^=%&:\/~+#-]*[\w@?^=%&\/~+#-])/i';
                preg_match_all($pattern, $_POST['text'], $matches);

                $data['result']['count'] = count($matches[0] ?? []);
                $data['result']['urls'] = $matches[0] ?? [];

            }
        }

        $values = [
            'text' => $_POST['text'] ?? null,
        ];

        /* Prepare the View */
        $data['values'] = $values;

        $view = new \Altum\Views\View('tools/url_extractor', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function text_size_calculator() {
        $this->initiate();

        $data = [];

        $values = [
            'text' => $_POST['text'] ?? null,
        ];

        /* Prepare the View */
        $data['values'] = $values;

        $view = new \Altum\Views\View('tools/text_size_calculator', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function paypal_link_generator() {
        $this->initiate();

        $data = [];

        $values = [];

        /* Prepare the View */
        $data['values'] = $values;

        $view = new \Altum\Views\View('tools/paypal_link_generator', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function bbcode_to_html() {
        $this->initiate();

        $data = [];

        if(!empty($_POST)) {
            $_POST['bbcode'] = input_clean($_POST['bbcode']);

            /* Check for any errors */
            $required_fields = ['bbcode'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                $bbcode = new \ChrisKonnertz\BBCode\BBCode();
                $data['result'] = $bbcode->render($_POST['bbcode']);
            }
        }

        $values = [
            'bbcode' => $_POST['bbcode'] ?? null,
        ];

        /* Prepare the View */
        $data['values'] = $values;

        $view = new \Altum\Views\View('tools/bbcode_to_html', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function html_tags_remover() {
        $this->initiate();

        $data = [];

        if(!empty($_POST)) {

            /* Check for any errors */
            $required_fields = ['content'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                $data['result'] = strip_tags($_POST['content']);
            }
        }

        $values = [
            'content' => $_POST['content'] ?? null,
        ];

        /* Prepare the View */
        $data['values'] = $values;

        $view = new \Altum\Views\View('tools/html_tags_remover', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function celsius_to_fahrenheit() {
        $this->initiate();

        $data = [];

        if(!empty($_POST)) {
            $_POST['celsius'] = (float) $_POST['celsius'];

            /* Check for any errors */
            $required_fields = ['celsius'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                $data['result'] = (float) (($_POST['celsius'] * 9 / 5) + 32);
            }
        }

        $values = [
            'celsius' => $_POST['celsius'] ?? null,
        ];

        /* Prepare the View */
        $data['values'] = $values;

        $view = new \Altum\Views\View('tools/celsius_to_fahrenheit', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function fahrenheit_to_celsius() {
        $this->initiate();

        $data = [];

        if(!empty($_POST)) {
            $_POST['fahrenheit'] = (float) $_POST['fahrenheit'];

            /* Check for any errors */
            $required_fields = ['fahrenheit'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                $data['result'] = (float) ($_POST['fahrenheit'] - 32) / 1.8;
            }
        }

        $values = [
            'fahrenheit' => $_POST['fahrenheit'] ?? null,
        ];

        /* Prepare the View */
        $data['values'] = $values;

        $view = new \Altum\Views\View('tools/fahrenheit_to_celsius', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function download() {
        if(!isset($_GET['url']) && !isset($_GET['name'])) {
            die();
        }

        if(!Csrf::check('global_token')) {
            die();
        }

        $_GET['url'] = filter_var(urldecode($_GET['url']), FILTER_SANITIZE_URL);
        $_GET['name'] = get_slug(urldecode($_GET['name']));

        $content = file_get_contents($_GET['url']);

        header('Cache-Control: private');
        header('Content-Transfer-Encoding: binary');
        header('Content-Disposition: attachment; filename="' . $_GET['name'] . '";');
        header('Content-Length: ' . strlen($content));

        die($content);

    }
}
