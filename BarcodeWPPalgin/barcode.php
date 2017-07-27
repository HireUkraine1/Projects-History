<?php

/*
Plugin Name: Barcode Generate
Description: Generate barcode.
Version: 1.0.0
*/

define('BARCODE_PLUGIN_DIR', plugin_dir_path( __FILE__ ));
define('BARCODE_PLUGIN_INC_DIR', BARCODE_PLUGIN_DIR . 'inc/');
define('BARCODE_PLUGIN_LIB_DIR', BARCODE_PLUGIN_INC_DIR . 'libs/');
define('BARCODE_PLUGIN_CLASSES_DIR', BARCODE_PLUGIN_INC_DIR . 'classes/');
define('BARCODE_PLUGIN_TEMPLATES_DIR', BARCODE_PLUGIN_INC_DIR . 'templates/');
define('BARCODE_PLUGIN_ADM_TEMPLATES_DIR', BARCODE_PLUGIN_TEMPLATES_DIR . 'admin/');

include_once BARCODE_PLUGIN_INC_DIR . 'activate.php';
register_activation_hook(__FILE__, 'barcode_options_install');

include_once BARCODE_PLUGIN_CLASSES_DIR . 'BarcodeAdmin.php';
new BarcodeAdmin();
