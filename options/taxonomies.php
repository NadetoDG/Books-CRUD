<?php

# Custom non-hierarchical taxonomy (like tags)
register_taxonomy(
'tax_genre', # Taxonomy name
	array( 'cpt_book' ), # Post Types
	array( # Arguments
		'labels'            => array(
			'name'                       => __( 'Genres', 'books-crud' ),
			'singular_name'              => __( 'Custom Taxonomy', 'books-crud' ),
			'search_items'               => __( 'Search Genres', 'books-crud' ),
			'popular_items'              => __( 'Popular Genres', 'books-crud' ),
			'all_items'                  => __( 'All Genres', 'books-crud' ),
			'view_item'                  => __( 'View Custom Taxonomy', 'books-crud' ),
			'edit_item'                  => __( 'Edit Custom Taxonomy', 'books-crud' ),
			'update_item'                => __( 'Update Custom Taxonomy', 'books-crud' ),
			'add_new_item'               => __( 'Add New Custom Taxonomy', 'books-crud' ),
			'new_item_name'              => __( 'New Custom Taxonomy Name', 'books-crud' ),
			'separate_items_with_commas' => __( 'Separate Genres with commas', 'books-crud' ),
			'add_or_remove_items'        => __( 'Add or remove Genres', 'books-crud' ),
			'choose_from_most_used'      => __( 'Choose from the most used Genres', 'books-crud' ),
			'not_found'                  => __( 'No Genres found.', 'books-crud' ),
			'menu_name'                  => __( 'Genres', 'books-crud' ),
		),
		'hierarchical'          => false,
		'show_ui'               => true,
		'show_in_rest'          => true,
		'show_admin_column'     => true,
		'update_count_callback' => '_update_post_term_count',
		'query_var'             => true,
		'rewrite'               => array( 'slug' => 'genre' ),
	)
);

