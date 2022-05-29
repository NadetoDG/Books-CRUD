<?php
/**
 * The admin-specific functionality of the plugin.
 */

class Books_Crud_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Attach Custom Post Types and Custom Taxonomies.
	 */
	public function bc_attach_books_crud_post_type_and_taxonomies() {
		# Attach Custom Post Types
		include_once( BOOKS_CRUD_PLUGIN_DIR . 'options/post-type.php' );

		# Attach Custom Taxonomies
		include_once( BOOKS_CRUD_PLUGIN_DIR . 'options/taxonomies.php' );
	}

	/**
	 * Register Meta Boxes.
	 */
	public function bc_register_books_crud_meta_boxes() {
		add_meta_box( 'bc_book_author', __( 'Book Information', 'books-crud' ), array( $this,'bc_render_book_meta_boxes_callback' ), 'cpt_book' );
	}

	/**
	 * Meta box display callback.
	 *
	 * @param WP_Post $post Current post object.
	 */
	public function bc_render_book_meta_boxes_callback( $post ) {
		include BOOKS_CRUD_PLUGIN_DIR . 'admin/meta-boxes/book-info.php';
	}

	/**
	 * Save Meta boxes.
	 *
	 * @param int $post_id The id of this post.
	 */
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

		if ( array_key_exists( 'bc-book-description', $_POST ) ) {
			update_post_meta(
				$post_id,
				'_bc_book_description',
				$_POST['bc-book-description']
			);
		}
	}
}
