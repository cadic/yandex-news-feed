<?php

/**
 * Adds a box to the main column on the Post and Page edit screens.
 */
function mlynf_add_meta_box() {

	$screens = get_option( 'mlynf_screens', array( 'post' ) );

	foreach ( $screens as $screen ) {

		add_meta_box(
			'mlynf_post_settings',
			__( 'Yandex News', 'mlynf' ),
			'mlynf_meta_box',
			$screen
		);
	}
}
add_action( 'add_meta_boxes', 'mlynf_add_meta_box' );

function mlynf_meta_box()
{
	wp_nonce_field( 'mlynf_save_meta_box_data', 'mlynf_meta_box_nonce' );

	$ignore = get_post_meta( get_the_ID(), '_mlynf_ignore', true );
	$ignore_checked = ( $ignore ) ? " checked" : "";
	$genre = get_post_meta( get_the_ID(), '_mlynf_genre', true );

	$genres = array(
		'lenta' => __( 'Short message, 50-80 characters', 'mlynf' ),
		'message' => __( 'General message', 'mlynf' ),
		'article' => __( 'Article, longread', 'mlynf' ),
		'interview' => __( 'Interview', 'mlynf' ),
	);
?>
<table class="form-table">
	<tr valign="top">
	<th scope="row"><label for="mlynf_ignore"><?php _e('Ignore this', 'mlynf') ?></label></th>
	<td>
		<input type="checkbox" id="mlynf_ignore" name="mlynf_ignore" value="1"<?php echo $ignore_checked ?>>
		<span class="description" id="mlynf_ignore_description"><?php _e( 'Check this if you don\'t want this object to be added to export file.', 'mlynf' ); ?></span>
	</td>
	</tr>

	<tr valign="top">
	<th scope="row"><label for="mlynf_genre"><?php _e('Yandex Genre', 'mlynf') ?></label></th>
	<td>
	<ul>
	<?php foreach ($genres as $key => $g): $checked = ( $key == $genre ) ? " checked" : ""; ?>
		<li><label><input type="radio" name="mlynf_genre" value="<?php echo $key ?>"<?php echo $checked ?>> <?php echo $g; ?></label></li>
	<?php endforeach ?>
	</ul>
	</td>
	</tr>
</table>
<?php
}

add_action( 'save_post', 'mlynf_save_meta_box_data' );
function mlynf_save_meta_box_data( $post_id )
{
	if ( ! isset( $_POST['mlynf_meta_box_nonce'] ) )
		return;

	if ( ! wp_verify_nonce( $_POST['mlynf_meta_box_nonce'], 'mlynf_save_meta_box_data' ) )
		return;

	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return;

	if ( isset( $_POST['mlynf_ignore'] ) ) {
		update_post_meta( $post_id, '_mlynf_ignore', 1 );
	} else {
		delete_post_meta( $post_id, '_mlynf_ignore' );
	}

	if ( isset( $_POST['mlynf_genre'] ) ) {
		update_post_meta( $post_id, '_mlynf_genre', $_POST['mlynf_genre'] );
	}
}