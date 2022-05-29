<?php
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
			'permission_callback' => '__return_true',
		) );

		//Create books
		register_rest_route( $namespace, $base . '/create/', array(
			'methods'  => 'POST',
			'callback' => array( $this, 'bc_api_create_book_callback' ),
			'permission_callback' => '__return_true',
		) );

		//Update book
		register_rest_route( $namespace, $base . '/update/(?P<book_id>\d+)', array(
			'methods'  => 'PUT',
			'callback' => array( $this, 'bc_api_update_book_callback' ),
			'permission_callback' => '__return_true',
		) );

		//Update book
		register_rest_route( $namespace, $base . '/delete/(?P<book_id>\d+)', array(
			'methods'  => 'DELETE',
			'callback' => array( $this, 'bc_api_delete_book_callback' ),
			'permission_callback' => '__return_true',
		) );
	}

	/**
	 * Get books from Wordpress.
	 *
	 * @param object
	 * @return object
	 */
	function bc_api_get_books_callback( $request ) {
		$books_query = new WP_Query([
			'post_type' => 'cpt_book',
		]);

		$response = [];

		if ( $books_query->have_posts() ) {
			foreach ( $books_query->posts as $book ) {
				$genre = wp_get_post_terms( $book->ID, 'tax_genre', array( 'fields' => 'names' ) );

				$response[] = [
					'title'       => $book->post_title,
					'description' => get_post_meta( $book->ID, '_bc_book_description', true ),
					'genre'       => $genre,
					'author' => [
						'first_name' => get_post_meta( $book->ID, '_bc_author_first_name', true ),
						'last_name'  => get_post_meta( $book->ID, '_bc_author_last_name', true ),
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
			$errors = new WP_Error( 'error_new_book', __( "The new book wasn't created.", 'books-crud' ), 400 );
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

	/**
	 * Update books from Wordpress.
	 *
	 * @param object
	 * @return object
	 */
	function bc_api_update_book_callback( $request ) {
		$book_id = $request->get_param( 'book_id' );

		if ( ! $this->bc_check_is_book( $book_id ) ) {
			$errors = new WP_Error( 'invalid_book_id', __( 'Invalid book id.', 'books-crud' ), 400 );
			return $errors;
		}

		$book_attrs = [
			'ID' => $book_id,
		];

		if ( ! empty( $request['title'] ) ) {
			$book_attrs['post_title'] = $request['title'];
		}

		if ( ! empty( $request['description'] ) ) {
			$book_attrs['meta_input']['_bc_book_description'] = $request['description'];
		}

		if ( ! empty( $request['author_first_name'] ) ) {
			$book_attrs['meta_input']['_bc_author_first_name'] = $request['author_first_name'];
		}

		if ( ! empty( $request['author_last_name'] ) ) {
			$book_attrs['meta_input']['_bc_author_last_name'] = $request['author_last_name'];
		}

		if ( ! empty( $request['genre'] ) ) {
			$genre_entries = explode( ',', $request['genre'] );

			$book_genre_names = [];

			foreach ( $genre_entries as $entry ) {
				$genre_name_term = term_exists( $entry, 'tax_genre', 0 );

				if ( ! $genre_name_term ) {
					$genre_name_term = wp_insert_term( $entry, 'tax_genre', array( 'parent' => 0 ) );
				}

				$book_genre_names[] = get_term( $genre_name_term['term_taxonomy_id'] )->name;
			}

			wp_set_post_terms( $book_id, $book_genre_names, 'tax_genre' );
		}

		$update_book = wp_update_post( $book_attrs );

		if ( is_wp_error( $update_book ) ) {
			$errors = new WP_Error( 'error_new_book', __( "The new book wasn't updated.", 'books-crud' ), 400 );
			return $errors;
		} else {
			return new WP_Rest_Response( 'The book with id ' . $book_id . ' was updated.', 200 );
		}
	}

	/**
	 * Delete books from Wordpress.
	 *
	 * @param object
	 * @return object
	 */
	function bc_api_delete_book_callback( $request ) {
		$book_id = $request->get_param( 'book_id' );

		if ( ! $this->bc_check_is_book( $book_id ) ) {
			$errors = new WP_Error( 'invalid_book_id', __( 'Invalid book id.', 'books-crud' ), 400 );
			return $errors;
		}

		$delete_book = wp_delete_post( $book_id );

		if ( is_wp_error( $delete_book ) ) {
			$errors = new WP_Error( 'error_new_book', __( "The new book wasn't deleted.", 'books-crud' ), 400 );
			return $errors;
		} else {
			return new WP_Rest_Response( 'The book with id ' . $book_id . ' was deleted.', 200 );
		}
	}


	function validate( $request ) {
		$errors = new WP_Error( 'invalid_data', __( 'Please, fix the following errors and try again', 'books-crud' ), 400 );
		$is_valid = true;

		if ( empty( $request['title'] ) ) {
			$is_valid = false;
			$errors->add(
				'empty_title',
				__( 'No book title set.', 'books-crud' )
			);
		}

		if ( ! isset( $request['description'] ) ) {
			$is_valid = false;
			$errors->add(
				'empty_description',
				__( 'No book description set.', 'books-crud' )
			);
		}

		if ( empty( $request['genre'] ) ) {
			$is_valid = false;
			$errors->add(
				'empty_genre',
				__( 'No book genre set.', 'books-crud' )
			);
		}

		if ( empty( $request['author_first_name' ] ) ) {
			$is_valid = false;
			$errors->add(
				'empty_author_first_name',
				__( 'No book author first name set.', 'books-crud' )
			);
		}

		if ( empty( $request['author_last_name' ] ) ) {
			$is_valid = false;
			$errors->add(
				'empty_author_last_name',
				__( 'No book author last name set.', 'books-crud' )
			);
		}

		//Add validation if is needed.

		if ( $is_valid === false ) {
			return $errors;
		}

		return true;
	}

	function bc_check_is_book( $post_id ) {
		return get_post_type( $post_id ) === 'cpt_book';
	}
}
