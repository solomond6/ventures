<?php
/*
Plugin Name: WP Engine Automated Migration
Plugin URI: https://wpengine.com
Description: The easiest way to migrate your site to WP Engine
Author: WPEngine
Author URI: https://wpengine.com
Version: 2.1
Network: True
 */

/*  Copyright 2017  WPEngine Migration  (email : support@blogvault.net)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/* Global response array */

if (!defined('ABSPATH')) exit;
require_once dirname( __FILE__ ) . '/wp_settings.php';
require_once dirname( __FILE__ ) . '/wp_site_info.php';
require_once dirname( __FILE__ ) . '/wp_db.php';
require_once dirname( __FILE__ ) . '/wp_api.php';
require_once dirname( __FILE__ ) . '/wp_actions.php';
require_once dirname( __FILE__ ) . '/info.php';
require_once dirname( __FILE__ ) . '/account.php';


$bvsettings = new WPEWPSettings();
$bvsiteinfo = new WPEWPSiteInfo();
$bvdb = new WPEWPDb();


$bvapi = new WPEWPAPI($bvsettings);
$bvinfo = new WPEInfo($bvsettings);
$wp_action = new WPEWPAction($bvsettings, $bvsiteinfo, $bvapi);

register_uninstall_hook(__FILE__, array('WPEWPAction', 'uninstall'));
register_activation_hook(__FILE__, array($wp_action, 'activate'));
register_deactivation_hook(__FILE__, array($wp_action, 'deactivate'));

add_action('wp_footer', array($wp_action, 'footerHandler'), 100);

if (is_admin()) {
	require_once dirname( __FILE__ ) . '/wp_admin.php';
	$wpadmin = new WPEWPAdmin($bvsettings, $bvsiteinfo);
	add_action('admin_init', array($wpadmin, 'initHandler'));
	add_filter('all_plugins', array($wpadmin, 'initBranding'));
	add_filter('plugin_row_meta', array($wpadmin, 'hidePluginDetails'), 10, 2);
	if ($bvsiteinfo->isMultisite()) {
		add_action('network_admin_menu', array($wpadmin, 'menu'));
	} else {
		add_action('admin_menu', array($wpadmin, 'menu'));
	}
	add_filter('plugin_action_links', array($wpadmin, 'settingsLink'), 10, 2);
	##ACTIVATEWARNING##
	add_action('admin_enqueue_scripts', array($wpadmin, 'wpesecAdminMenu'));
}


if ((array_key_exists('bvreqmerge', $_POST)) || (array_key_exists('bvreqmerge', $_GET))) {
	$_REQUEST = array_merge($_GET, $_POST);
}

if ((array_key_exists('bvplugname', $_REQUEST)) && ($_REQUEST['bvplugname'] == "wpengine")) {
	require_once dirname( __FILE__ ) . '/callback/base.php';
	require_once dirname( __FILE__ ) . '/callback/request.php';
	require_once dirname( __FILE__ ) . '/callback/response.php';
	
	$request = new BVCallbackRequest($_REQUEST);
	$account = WPEAccount::find($bvsettings, $_REQUEST['pubkey']);

	
	##RECOVERYMODULE##

	if ($account && (1 === $account->authenticate())) {
		require_once dirname( __FILE__ ) . '/callback/handler.php';
		$request->params = $request->processParams();
		$callback_handler = new BVCallbackHandler($bvdb, $bvsettings, $bvsiteinfo, $request, $account);
		if ($request->is_afterload) {
			add_action('wp_loaded', array($callback_handler, 'execute'));
		} else if ($request->is_admin_ajax) {
			add_action('wp_ajax_bvadm', array($callback_handler, 'bvAdmExecuteWithUser'));
			add_action('wp_ajax_nopriv_bvadm', array($callback_handler, 'bvAdmExecuteWithoutUser'));
		} else {
			$callback_handler->execute();
		}
	} else {
		$resp = array(
			"account_info" => $account ? $account->respInfo() : array("error" => "ACCOUNT_NOT_FOUND"),
			"request_info" => $request->respInfo(),
			"bvinfo" => $bvinfo->respInfo(),
		  "statusmsg" => "FAILED_AUTH"
		);
		$response = new BVCallbackResponse();
		$response->terminate($resp, $request->params);
	}
} else {
	##PROTECTMODULE##
	##DYNSYNCMODULE##
}