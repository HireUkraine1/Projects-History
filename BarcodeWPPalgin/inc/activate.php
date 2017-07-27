<?php

/**
 * Init barcodes database
 */
function barcode_options_install() {
    global $wpdb;
    $table = $wpdb->prefix . 'barcodes';

    if($wpdb->get_var("show tables like '{$table}'") != $table)
    {
        $sql = "CREATE TABLE " . $table . " (
		`id` INT(9) NOT NULL AUTO_INCREMENT,
		`date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
		`code` VARCHAR(50) NOT NULL,
		`form_id` INT(9) NOT NULL,
		`detail_id` INT(9) NOT NULL,
		`status` INT(1) DEFAULT 1,
		UNIQUE KEY id (id)
		);";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}
