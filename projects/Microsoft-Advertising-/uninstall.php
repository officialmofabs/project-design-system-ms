<?php
// Copyright (c) Microsoft Corporation.
// Licensed under the MIT license.

// if uninstall.php is not called by WordPress, die
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}
 
$option_name = 'UetTagSettings';
 
delete_option($option_name);
?>