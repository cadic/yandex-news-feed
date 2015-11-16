<?php

add_action('admin_menu', 'mlynf_create_menu');
function mlynf_create_menu() {

	//create new top-level menu
	add_menu_page( __('Yandex News Settings', 'mlynf'), __('Yandex News', 'mlynf'), 'administrator', 'mlynf-settings', 'mlynf_settings_page', 'dashicons-rss', 95 );

	//call register settings function
	add_action( 'admin_init', 'mlynf_register_settings' );
}

function mlynf_register_settings() {
	//register our settings
	register_setting( 'mlynf', 'mlynf_post_types' );
	register_setting( 'mlynf', 'mlynf_channel_title' );
	register_setting( 'mlynf', 'mlynf_channel_link' );
	register_setting( 'mlynf', 'mlynf_channel_description' );
	register_setting( 'mlynf', 'mlynf_channel_logo' );
	register_setting( 'mlynf', 'mlynf_channel_logo_square' );
	register_setting( 'mlynf', 'mlynf_feed_url' );
	register_setting( 'mlynf', 'mlynf_categories' );
}

function mlynf_settings_page()
{

?>
<div class="wrap">
<h2><?php _e('Yandex News Export Settings', 'mlynf') ?></h2>

<form method="post" action="options.php" class="mlynf-settings-form">
	<?php settings_fields( 'mlynf' ); ?>

	<table class="form-table">
		
		<tr valign="top">
		<th scope="row"><label for="mlynf_channel_title"><?php _e('Channel Title', 'mlynf') ?></label></th>
		<td><input type="text" class="regular-text" id="mlynf_channel_title" name="mlynf_channel_title" value="<?php echo get_option( 'mlynf_channel_title', get_bloginfo( 'sitename' ) ) ?>"></td>
		</tr>

		<tr valign="top">
		<th scope="row"><label for="mlynf_channel_description"><?php _e('Channel Description', 'mlynf') ?></label></th>
		<td><input type="text" class="regular-text" id="mlynf_channel_description" name="mlynf_channel_description" value="<?php echo get_option( 'mlynf_channel_description', get_bloginfo( 'description' ) ) ?>"></td>
		</tr>

		<tr valign="top">
		<th scope="row"><label for="mlynf_channel_link"><?php _e('Channel Link', 'mlynf') ?></label></th>
		<td><input type="text" class="regular-text" id="mlynf_channel_link" name="mlynf_channel_link" value="<?php echo get_option( 'mlynf_channel_link', get_bloginfo( 'url' ) ) ?>"></td>
		</tr>

		<tr valign="top">
		<th scope="row"><label for="mlynf_channel_logo"><?php _e('Channel Logo', 'mlynf') ?></label></th>
		<td>
			<input type="text" class="regular-text" id="mlynf_channel_logo" name="mlynf_channel_logo" value="<?php echo get_option( 'mlynf_channel_logo' ) ?>">
			<button class="button mlynf-media-button" id="mlynf_channel_logo_select"><?php _e('Select') ?></button>
		</td>
		</tr>

		<tr valign="top">
		<th scope="row"><label for="mlynf_channel_logo_square"><?php _e('Channel Logo Square', 'mlynf') ?></label></th>
		<td>
			<input type="text" class="regular-text" id="mlynf_channel_logo_squere" name="mlynf_channel_logo_square" value="<?php echo get_option( 'mlynf_channel_logo_square' ) ?>">
			<button class="button mlynf-media-button" id="mlynf_channel_logo_square_select"><?php _e('Select') ?></button>
		</td>
		</tr>

		<tr valign="top">
		<th scope="row"><label for="mlynf_feed_url"><?php _e('Feed URL', 'mlynf') ?></label></th>
		<td>
			<input type="text" class="regular-text" id="mlynf_feed_url" name="mlynf_feed_url" value="<?php echo get_option( 'mlynf_feed_url', '/yandex.xml' ) ?>">
			<p class="description" id="mlynf_feed_url_description"><?php printf( __('After you change Feed URL, please go to <a href="%s">Settings &gt; Permalinks</a> and hit Save Changes button', 'mlynf'), admin_url( 'options-permalink.php' ) ) ?></p>
		</td>
		</tr>

		<tr valign="top">
		<th scope="row"><label for="mlynf_post_types"><?php _e('Post Types', 'mlynf') ?></label></th>
		<td>
		<?php
		
		$post_types = get_post_types( array( 'public' => true, 'hierarchical' => false, '_builtin' => false ), 'objects' );
		$mlynf_post_types = get_option( 'mlynf_post_types', array('post') );
		?>
		<ul>
			<li><input type="checkbox" name="mlynf_post_types[]" value="post"<?php echo ( in_array( 'post', $mlynf_post_types ) ) ? " checked" : ""; ?>> <?php _e( 'Posts' ); ?></label></li>
		<?php foreach ($post_types as $key => $post_type):
			$checked = ( in_array( $key, $mlynf_post_types ) ) ? " checked" : ""; ?>
			<li><label><input type="checkbox" name="mlynf_post_types[]" value="<?php echo $key ?>"<?php echo $checked ?>> <?php echo $post_type->labels->name ?></label></li>
		<?php endforeach ?>
		</ul>
		</td>
		</tr>

		<tr>
		<th scope="row"><label for="mlynf_categories"><?php _e('Categories') ?></label></th>
		<td>
		<?php

		$categories = get_categories( array( 'hide_empty' => 0 ) );
		$mlynf_categories = get_option( 'mlynf_categories', array(0) );

		?>
		<ul>
			<?php $all_checked = ( in_array( 0, $mlynf_categories ) ) ? " checked" : ""; ?>
			<li><label><input type="checkbox" id="mlynf_categories_all" name="mlynf_categories[]" value="0"<?php echo $all_checked ?>> <?php _e('All') ?></label></li>
			<?php foreach ($categories as $key => $cat):
			if ( $all_checked ) {
				$disabled = " disabled";
				$checked = " checked";
			} else {
				$checked = ( in_array( $cat->term_id, $mlynf_categories ) ) ? " checked" : "";
			}
			?>
			<li><label><input type="checkbox" name="mlynf_categories[]" value="<?php echo $cat->term_id ?>"<?php echo $disabled.$checked ?>> <?php echo $cat->name ?></label></li>
			<?php endforeach ?>
		</ul>
		</td>
		</tr>
	</table>

	<p class="submit">
	<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
	</p>

</form>
</div>
<?php }
