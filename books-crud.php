<?php
/**
 * Plugin Name:       Books CRUD
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            ngeneva
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       books-crud
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define plugin variables
 */
define( 'BOOKS_CRUD_VERSION', '1.0.0' );
define( 'BOOKS_CRUD_PLUGIN_DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-books-crud.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function bc_run_books_crud() {

	$plugin = new Books_Crud();
	$plugin->run();

}
bc_run_books_crud();
