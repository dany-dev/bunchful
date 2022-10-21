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
use Altum\Middlewares\Csrf;
use Altum\Uploads;

class AdminBiolinkThemeCreate extends Controller {

    public function index() {

        $biolink_fonts = require APP_PATH . 'includes/biolink_fonts.php';
        $biolink_backgrounds = require APP_PATH . 'includes/biolink_backgrounds.php';

        if(!empty($_POST)) {
            /* Filter some the variables */
            $_POST['name'] = input_clean($_POST['name']);

            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* Check for errors & process  potential uploads */
            $image = !empty($_FILES['image']['name']) && !isset($_POST['image_remove']);

            if($image) {
                $file_name = $_FILES['image']['name'];
                $file_extension = explode('.', $file_name);
                $file_extension = mb_strtolower(end($file_extension));
                $file_temp = $_FILES['image']['tmp_name'];

                if($_FILES['image']['error'] == UPLOAD_ERR_INI_SIZE) {
                    Alerts::add_error(sprintf(l('global.error_message.file_size_limit'), get_max_upload()));
                }

                if($_FILES['image']['error'] && $_FILES['image']['error'] != UPLOAD_ERR_INI_SIZE) {
                    Alerts::add_error(l('global.error_message.file_upload'));
                }

                if(!in_array($file_extension, Uploads::get_whitelisted_file_extensions('biolinks_themes'))) {
                    Alerts::add_error(l('global.error_message.invalid_file_type'));
                }

                if(!\Altum\Plugin::is_active('offload') || (\Altum\Plugin::is_active('offload') && !settings()->offload->uploads_url)) {
                    if(!is_writable(UPLOADS_PATH . 'biolinks_themes' . '/')) {
                        Alerts::add_error(sprintf(l('global.error_message.directory_not_writable'), UPLOADS_PATH . 'biolinks_themes' . '/'));
                    }
                }

                if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                    /* Generate new name for image */
                    $image_new_name = md5(time() . rand()) . '.' . $file_extension;

                    /* Offload uploading */
                    if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                        try {
                            $s3 = new \Aws\S3\S3Client(get_aws_s3_config());

                            /* Upload image */
                            $result = $s3->putObject([
                                'Bucket' => settings()->offload->storage_name,
                                'Key' => 'uploads/' . 'biolinks_themes' . '/' . $image_new_name,
                                'ContentType' => mime_content_type($file_temp),
                                'SourceFile' => $file_temp,
                                'ACL' => 'public-read'
                            ]);
                        } catch (\Exception $exception) {
                            Alerts::add_error($exception->getMessage());
                        }
                    }

                    /* Local uploading */
                    else {
                        /* Upload the original */
                        move_uploaded_file($file_temp, UPLOADS_PATH . 'biolinks_themes' . '/' . $image_new_name);
                    }
                }
            }

            /* Check for errors & process potential uploads */
            $background = !empty($_FILES['biolink_background_image']['name']) && !isset($_POST['biolink_background_image_remove']);

            if($background) {
                $file_name = $_FILES['biolink_background_image']['name'];
                $file_extension = explode('.', $file_name);
                $file_extension = mb_strtolower(end($file_extension));
                $file_temp = $_FILES['biolink_background_image']['tmp_name'];

                if($_FILES['biolink_background_image']['error'] == UPLOAD_ERR_INI_SIZE) {
                    Alerts::add_error(sprintf(l('global.error_message.file_size_limit'), get_max_upload()));
                }

                if($_FILES['biolink_background_image']['error'] && $_FILES['biolink_background_image']['error'] != UPLOAD_ERR_INI_SIZE) {
                    Alerts::add_error(l('global.error_message.file_upload'));
                }

                if(!in_array($file_extension, ['gif', 'png', 'jpg', 'jpeg', 'svg', 'mp4'])) {
                    Alerts::add_error(l('global.error_message.invalid_file_type'));
                }

                if(!\Altum\Plugin::is_active('offload') || (\Altum\Plugin::is_active('offload') && !settings()->offload->uploads_url)) {
                    if(!is_writable(UPLOADS_PATH . 'backgrounds' . '/')) {
                        Alerts::add_error(sprintf(l('global.error_message.directory_not_writable'), UPLOADS_PATH . 'backgrounds' . '/'));
                    }
                }

                if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                    /* Generate new name for image */
                    $background_new_name = md5(time() . rand()) . '.' . $file_extension;

                    /* Offload uploading */
                    if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                        try {
                            $s3 = new \Aws\S3\S3Client(get_aws_s3_config());

                            /* Upload image */
                            $result = $s3->putObject([
                                'Bucket' => settings()->offload->storage_name,
                                'Key' => 'uploads/' . 'backgrounds' . '/' . $background_new_name,
                                'ContentType' => mime_content_type($file_temp),
                                'SourceFile' => $file_temp,
                                'ACL' => 'public-read'
                            ]);
                        } catch (\Exception $exception) {
                            Alerts::add_error($exception->getMessage());
                        }
                    }

                    /* Local uploading */
                    else {
                        /* Upload the original */
                        move_uploaded_file($file_temp, UPLOADS_PATH . 'backgrounds' . '/' . $background_new_name);
                    }
                }
            }

            if(!Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                $settings = json_encode([
                    'biolink' => [
                        'background_type' => $_POST['biolink_background_type'],
                        'background' => $background_new_name ?? $_POST['biolink_background'] ?? null,
                        'background_color_one' => $_POST['biolink_background_color_one'],
                        'background_color_two' => $_POST['biolink_background_color_two'],
                        'font' => $_POST['biolink_font'],
                        'font_size' => $_POST['biolink_font_size'],
                    ],

                    'biolink_block' => [
                        'text_color' => $_POST['biolink_block_text_color'],
                        'background_color' => $_POST['biolink_block_background_color'],
                        'border_width' => $_POST['biolink_block_border_width'],
                        'border_color' => $_POST['biolink_block_border_color'],
                        'border_radius' => $_POST['biolink_block_border_radius'],
                        'border_style' => $_POST['biolink_block_border_style'],
                    ]
                ]);

                /* Database query */
                db()->insert('biolinks_themes', [
                    'name' => $_POST['name'],
                    'image' => $image_new_name ?? null,
                    'settings' => $settings,
                    'datetime' => \Altum\Date::$date,
                ]);

                /* Set a nice success message */
                Alerts::add_success(sprintf(l('global.success_message.create1'), '<strong>' . e($_POST['name']) . '</strong>'));

                /* Clear the cache */
                \Altum\Cache::$adapter->deleteItem('biolinks_themes');

                redirect('admin/biolinks-themes');
            }
        }

        $values = [
            'biolink_background_type' => $_POST['biolink_background_type'] ?? null,
            'biolink_background' => $_POST['biolink_background'] ?? null,
            'biolink_background_color_one' => $_POST['biolink_background_color_one'] ?? null,
            'biolink_background_color_two' => $_POST['biolink_background_color_two'] ?? null,
            'biolink_font' => $_POST['biolink_font'] ?? null,
            'biolink_font_size' => $_POST['biolink_font_size'] ?? 16,
            'biolink_block_text_color' => $_POST['biolink_block_text_color'] ?? null,
            'biolink_block_background_color' => $_POST['biolink_block_background_color'] ?? null,
            'biolink_block_border_width' => $_POST['biolink_block_border_width'] ?? 0,
            'biolink_block_border_color' => $_POST['biolink_block_border_color'] ?? null,
            'biolink_block_border_radius' => $_POST['biolink_block_border_radius'] ?? null,
            'biolink_block_border_style' => $_POST['biolink_block_border_style'] ?? null,
        ];

        /* Main View */
        $data = [
            'values' => $values,
            'biolink_backgrounds' => $biolink_backgrounds,
            'biolink_fonts' => $biolink_fonts,
        ];

        $view = new \Altum\Views\View('admin/biolink-theme-create/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
