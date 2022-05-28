<?php
register_post_type( 'cpt_book', array(
	'labels'              => array(
		'name'               => __( 'Books', 'books-crud' ),
		'singular_name'      => __( 'Book', 'books-crud' ),
		'add_new'            => __( 'Add New', 'books-crud' ),
		'add_new_item'       => __( 'Add new Book', 'books-crud' ),
		'view_item'          => __( 'View Book', 'books-crud' ),
		'edit_item'          => __( 'Edit Book', 'books-crud' ),
		'new_item'           => __( 'New Book', 'books-crud' ),
		'search_items'       => __( 'Search Books', 'books-crud' ),
		'not_found'          => __( 'No Books found', 'books-crud' ),
		'not_found_in_trash' => __( 'No Books found in trash', 'books-crud' ),
	),
	'public'              => true,
	'exclude_from_search' => false,
	'show_ui'             => true,
	'show_in_rest'        => true,
	'capability_type'     => 'post',
	'hierarchical'        => false,
	'_edit_link'          => 'post.php?post=%d',
	'rewrite'             => array(
		'slug'       => 'book',
		'with_front' => false,
	),
	'query_var'           => true,
	'menu_icon'           => 'dashicons-book-alt',
	'supports'            => array( 'title', 'editor', 'page-attributes' ),
) );

