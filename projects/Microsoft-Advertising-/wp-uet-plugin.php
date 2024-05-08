<?php
// Copyright (c) Microsoft Corporation.
// Licensed under the MIT license.

/**
 * Plugin Name: Microsoft Advertising Universal Event Tracking (UET)
 * Plugin URI: https://ads.microsoft.com/
 * Description: The official plugin for setting up Microsoft Advertising UET.
 * Version: 1.0.3
 * Author: Microsoft Corporation
 * Author URI: https://www.microsoft.com/
 * License: MIT license
 */

 // NOTE: If you update 'Version' above, update the 'tm' parameter in the script, around line 42.

 //
 // Register actions.
 //
add_action('wp_head', 'UetPageLoadEvent'); // To inject UET into public pages.
add_action('admin_menu', 'UetAddSettingsPage'); // To add a settings page on the admin menu.
add_action('admin_init', 'UetRegisterSettings'); // To support the actual UET settings.
add_action('admin_notices', 'UetShowAdminNotice'); // To show an admin banner when UET is not setup correctly.
add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'UetAddSettingsLinkOnPluginDashboard'); // To add a link to the settings page from the plugin dashboard


register_activation_hook( __FILE__, function() {
  add_option('Activated_Plugin','microsoft-advertising-universal-event-tracking-uet');
});

function UetIsTagAvailable() {
    $options = get_option('UetTagSettings');
    return !empty($options['uet_tag_id']);
}

function UetPageLoadEvent() {
	if (!UetIsTagAvailable()) return null;
?>
        <script>(function(w,d,t,r,u){var f,n,i;w[u]=w[u]||[],f=function(){var o={ti:"<?php
    $options = get_option('UetTagSettings');
    $uet_tag_id = $options['uet_tag_id'];
    echo "{$uet_tag_id}"
?>",tm:"wpp_1.0.3"};o.q=w[u],w[u]=new UET(o),w[u].push("pageLoad")},n=d.createElement(t),n.src=r,n.async=1,n.onload=n.onreadystatechange=function(){var s=this.readyState;s&&s!=="loaded"&&s!=="complete"||(f(),n.onload=n.onreadystatechange=null)},i=d.getElementsByTagName(t)[0],i.parentNode.insertBefore(n,i)})(window,document,"script","//bat.bing.com/bat.js","uetq");</script>
<?php
  return null;
}

function UetAddSettingsPage() {
    add_options_page('Microsoft Advertising UET settings', 'UET tag', 'manage_options', 'uet_tag_settings_page', 'UetRenderSettingsPage');
}

function UetRenderSettingsPage() {
    ?>
	<div style="
	  margin-top: 32px;
	  margin-left: 12px;
	  padding: 0px;
	  ">
	<span style="font-family:Segoe UI;font-size:20px;font-weight:600;color: #323130;">Microsoft Advertising UET Settings</span>
    <form action="options.php" method="post" style="margin-top: 40px;">
        <?php
        settings_fields( 'UetTagSettings' );
        do_settings_sections( 'uet_tag_settings_page' ); ?>
        <input name="submit" class="button button-primary" type="submit" value="<?php esc_attr_e( 'Save' ); ?>" />
    </form>
<?php
}

function UetRegisterSettings() {
    register_setting('UetTagSettings', 'UetTagSettings');
    add_settings_section('uet_general_settings_section', '', 'UetRenderGeneralSettingsSectionHeader', 'uet_tag_settings_page');
    add_settings_field('uet_tag_id', 'UET Tag ID', 'UetEchoTagId', 'uet_tag_settings_page', 'uet_general_settings_section');
    
	if(is_admin() && get_option('Activated_Plugin') == 'microsoft-advertising-universal-event-tracking-uet') {
		delete_option('Activated_Plugin');
		
		$options = get_option('UetTagSettings');
		include 'tagid.php';
		if (empty($options['uet_tag_id']) && !empty($tagid)) {
			if (ctype_digit($tagid)) {
				$options['uet_tag_id'] = $tagid;
				update_option('UetTagSettings', $options);
			}
		}
    }
}

function UetRenderGeneralSettingsSectionHeader() {
?>
	<div><span style="font-family: 'Segoe UI';font-size: 14px;height: 100%;color: #323130;">Please configure the UET tag ID from your Microsoft Advertising Account. After you login to your Microsoft Advertising account, you can find the UET tag ID by going to <span style="font-weight:600;">Tools &gt; Conversion Tracking &gt; UET Tag.</span> Learn more about UET Tag <a href="https://go.microsoft.com/fwlink/?linkid=2155938" style="color: #0078D4;">here</a>.</span></div>
<?php
}

function UetEchoTagId() {
    $options = get_option('UetTagSettings');
    $uet_tag_id = '';
    if(isset($options['uet_tag_id'])){
        $uet_tag_id = $options['uet_tag_id'];
    }
    echo "<input id='uet_tag_id' name='UetTagSettings[uet_tag_id]' type='text' value='{$uet_tag_id}' />";
}

function UetShowAdminNotice() {
	if (UetIsTagAvailable()) return;
	global $pagenow;
    if ( $pagenow != 'index.php' && $pagenow != 'plugins.php') return;
	?>
	<div class="notice notice-warning is-dismissible"><p><span style="font-weight: 600;">Set up Microsoft Advertising Universal Event Tracking</span> Please complete UET tag setup by <a href='<?php echo admin_url('options-general.php?page=uet_tag_settings_page')?>'>configuring the UET tag ID</a>.</p></div>
	<?php
}

function UetAddSettingsLinkOnPluginDashboard( $links ) {
	$uet_settings_link = '<a href="' .
		admin_url( 'options-general.php?page=uet_tag_settings_page' ) .
		'">' . __('Settings') . '</a>';
	array_unshift($links, $uet_settings_link);
	return $links;
}
?>