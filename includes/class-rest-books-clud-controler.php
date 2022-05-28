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

		//Create books
		register_rest_route( $namespace, $base . '/create/', array(
			'methods'  => 'POST',
			'callback' => array( $this, 'bc_api_create_book_callback' ),
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
					'description' => get_post_meta( $book->ID, '_bc_book_description' ),
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

	/**
	 * Create books from Wordpress.
	 *
	 * @param object
	 * @return object
	 */
	function bc_api_create_book_callback( $request ) {
		$errors = $this->validate( $request );

		if ( is_wp_error( $errors ) ) {
			return $errors;
		}

		$book_attrs = [
			'post_title'  => $request['title'],
			'post_status' => 'publish',
			'post_type'   => 'cpt_book',
			'meta_input' => [
				'_bc_author_first_name' => $request['author_first_name'],
				'_bc_author_last_name'  => $request['author_last_name'],
				'_bc_book_description'  => $request['description'],
			],
		];

		$new_book = wp_insert_post( $book_attrs );

		if ( is_wp_error( $new_book ) ) {
			$errors = new WP_Error( 'error_new_book', __( "The new book wasn't created." ), 400 );
			return $errors;
		} else {
			$genre_entries = explode( ',', $request['genre'] );

			$book_genre_names = [];

			foreach ( $genre_entries as $entry ) {
				$genre_name_term = term_exists( $entry, 'tax_genre', 0 );

				if ( ! $genre_name_term ) {
					$genre_name_term = wp_insert_term( $entry, 'tax_genre', array( 'parent' => 0 ) );
				}

				$book_genre_names[] = get_term( $genre_name_term['term_taxonomy_id'] )->name;
			}

			wp_set_post_terms( $new_book, $book_genre_names, 'tax_genre' );

			return new WP_Rest_Response( $new_book, 200 );
		}
	}


	public function validate( $request ) {
		$errors = new WP_Error( 'invalid_data', __( 'Please, fix the following errors and try again' ), 400 );
		$is_valid = true;

		if ( ! isset( $request['title'] ) || ( isset( $request['title'] ) && empty( $request['title'] ) ) ) {
			$is_valid = false;
			$errors->add(
				'empty_title',
				__( 'No book title set.', 'crb' )
			);
		}

		if ( ! isset( $request['description'] ) ) {
			$is_valid = false;
			$errors->add(
				'empty_description',
				__( 'No book description set.', 'crb' )
			);
		}

		if ( ! isset( $request['genre'] ) || ( isset( $request['genre'] ) && empty( $request['genre'] ) ) ) {
			$is_valid = false;
			$errors->add(
				'empty_genre',
				__( 'No book genre set.', 'crb' )
			);
		}

		if ( ! isset( $request['author_first_name' ] ) || ( isset( $request['author_first_name' ] ) && empty( $request['author_first_name' ] ) ) ) {
			$is_valid = false;
			$errors->add(
				'empty_author_first_name',
				__( 'No book author first name set.', 'crb' )
			);
		}

		if ( ! isset( $request['author_last_name' ] ) || ( isset( $request['author_last_name' ] ) && empty( $request['author_last_name' ] ) ) ) {
			$is_valid = false;
			$errors->add(
				'empty_author_last_name',
				__( 'No book author last name set.', 'crb' )
			);
		}

		//Add validation if is needed.

		if ( $is_valid === false ) {
			return $errors;
		}

		return true;
	}
}
