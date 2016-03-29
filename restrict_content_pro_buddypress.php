<?php
/**
 * Plugin Name: Restrict Content Pro - BuddyPress
 * Plugin URI:  http://wordpress.org/extend/plugins
 * Description: Extends Restrict Content Pro to integrate with BuddyPress
 * Version:     1.0.2
 * Author:      Tanner Moushey
 * Author URI:  http://tannermoushey.com
 * License:     GPLv2+
 * Text Domain: rcpbp
 * Domain Path: /languages
 */

/**
 * Copyright (c) 2015 Tanner Moushey (email : tanner@iwitnessdesign.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

// Useful global constants
define( 'RCPBP_VERSION', '1.0.2' );
define( 'RCPBP_URL',     plugin_dir_url( __FILE__ ) );
define( 'RCPBP_PATH',    dirname( __FILE__ ) . '/' );

// EDD Licensing constants
define( 'RCPBP_STORE_URL', 'https://tannermoushey.com' );
define( 'RCPBP_ITEM_NAME', 'Restrict Content Pro - BuddyPress' );

include( RCPBP_PATH . '/includes/setup.php' );

/**
 * Default initialization for the plugin:
 * - Registers the default textdomain.
 */
function rcpbp_init() {
	$locale = apply_filters( 'plugin_locale', get_locale(), 'rcpbp' );
	load_textdomain( 'rcpbp', WP_LANG_DIR . '/rcpbp/rcpbp-' . $locale . '.mo' );
	load_plugin_textdomain( 'rcpbp', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'init', 'rcpbp_init' );

/**
 * Activate the plugin
 */
function rcpbp_activate() {
	do_action( 'rcpbp_activate' );

	// First load the init scripts in case any rewrite functionality is being loaded
	rcpbp_init();

	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'rcpbp_activate' );

/**
 * Deactivate the plugin
 * Uninstall routines should be in uninstall.php
 */
function rcpbp_deactivate() {
	do_action( 'rcpbp_deactivate' );
}
register_deactivation_hook( __FILE__, 'rcpbp_deactivate' );

/**
 * Initialize Updater
 */
function rcpbp_plugin_updater() {

	// load our custom updater
	if( ! class_exists( 'EDD_SL_Plugin_Updater' ) ) {
		include( RCPBP_PATH . '/includes/updater.php' );
	}

	// retrieve our license key from the DB
	$license_key = trim( get_option( 'rcpbp_license_key' ) );

	// setup the updater
	new EDD_SL_Plugin_Updater( RCPBP_STORE_URL, __FILE__, array(
			'version'   => RCPBP_VERSION,    // current version number
			'license'   => $license_key,     // license key (used get_option above to retrieve from DB)
			'item_name' => urlencode( RCPBP_ITEM_NAME ), // the name of our product in EDD
			'author'    => 'Tanner Moushey'  // author of this plugin
		)
	);

}
add_action( 'admin_init', 'rcpbp_plugin_updater' );


