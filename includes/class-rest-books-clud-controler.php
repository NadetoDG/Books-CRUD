<?php
use WP_Rest_Response;
use WP_Error;
use WP_Query;

class WP_REST_Books_CRUD_Controller extends WP_REST_Controller {
	/**
	 * Registers Controller Routes.
	 *
	 * @access public
	 *
	 * @return void
	 */
	public function register_routes() {
		$version   = '1';
		$namespace = 'bc-books-crud/v' . $version;
		$base      = '/books';

		//Get books
		register_rest_route( $namespace, $base, array(
			'methods'  => 'GET',
			'callback' => array( $this, 'bc_api_get_books_callback' ),
		) );
	}

	/**
	 * Get books from Wordpress.
	 *
	 * @param object
	 * @return object
	 */
	function bc_api_get_books_callback( $request ) {
		$books = get_posts([
			'post_type' => 'cpt_book',
		]);
		wp_reset_postdata();

		$response = [];

		if ( count( $books ) > 0 ) {
			foreach ( $books as $book ) {
				$genre = wp_get_post_terms( $book->ID, 'tax_genre', array( 'fields' => 'names' ) );

				$response[] = [
					'title'       => $book->post_title,
					'description' => $book->post_excerpt,
					'genre'       => $genre,
					'author' => [
						'first_name' => get_post_meta( $book->ID, '_bc_author_first_name' ),
						'last_name'  => get_post_meta( $book->ID, '_bc_author_last_name' ),
					]
				];
			}
		}

		return new WP_Rest_Response( $response, 200 );
	}


	public function validate( $request ) {
		$errors = new WP_Error( 'invalid_data', __( 'Please, fix the following errors and try again' ), 400 );
		$is_valid = true;

		//Add validation if is needed.

		if ( $is_valid === false ) {
			return $errors;
		}

		return true;
	}
}
