<?php
$first_name_value  = get_post_meta( $post->ID, '_bc_author_first_name', true );
$last_name_value   = get_post_meta( $post->ID, '_bc_author_last_name', true );
$description_value = get_post_meta( $post->ID, '_bc_book_description', true );
?>
<div class="section-book-crud-author">
	<div class="section__body">
	    <p class="meta-options">
	        <label for="bc-author-first-name"><?php _e( 'First Name', 'books-crud' ); ?></label>
	        <input id="bc-author-first-name" type="text" name="bc-author-first-name" value="<?php echo $first_name_value ?>">
	    </p>

	    <p class="meta-options">
	        <label for="bc-author-last-name"><?php _e( 'Last Name', 'books-crud' ); ?></label>
	        <input id="bc-author-last-name" type="text" name="bc-author-last-name" value="<?php echo $last_name_value ?>">
	    </p>

	    <p class="meta-options">
	        <label for="bc-book-description"><?php _e( 'Description', 'books-crud' ); ?></label>

	        <textarea id="bc-book-description" name="bc-book-description" rows="4" cols="50"><?php echo $description_value ?></textarea>
	    </p>
	</div><!-- /.section__body -->
</div>
