<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       www.examle.com
 * @since      1.0.0
 *
 * @package    Books_Crud
 * @subpackage Books_Crud/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Books_Crud
 * @subpackage Books_Crud/admin
 * @author     ngeneva <nadetodg@gmail.com>
 */
class Books_Crud_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	public function bc_attach_books_crud_post_type_and_taxonomies() {
		# Attach Custom Post Types
		include_once( BOOKS_CRUD_PLUGIN_DIR . 'options/post-type.php' );

		# Attach Custom Taxonomies
		include_once( BOOKS_CRUD_PLUGIN_DIR . 'options/taxonomies.php' );
	}

	public function bc_register_books_crud_meta_boxes() {
		add_meta_box( 'bc_book_author', __( 'Author', 'books-crud' ), array( $this,'bc_render_book_author_callback' ), 'cpt_book' );
	}

	/**
	 * Meta box display callback.
	 *
	 * @param WP_Post $post Current post object.
	 */
	public function bc_render_book_author_callback( $post ) {
		include BOOKS_CRUD_PLUGIN_DIR . 'admin/meta-boxes/book-author.php';
	}

	public function bc_save_books_crud_meta_boxes( $post_id ) {
		if ( array_key_exists( 'bc-author-first-name', $_POST ) ) {
			update_post_meta(
				$post_id,
				'_bc_author_first_name',
				$_POST['bc-author-first-name']
			);
		}

		if ( array_key_exists( 'bc-author-last-name', $_POST ) ) {
			update_post_meta(
				$post_id,
				'_bc_author_last_name',
				$_POST['bc-author-last-name']
			);
		}
	}
}
