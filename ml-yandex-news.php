<?php
/*
Plugin Name: Yandex News Feed
Version: 0.1
Description: Provides feed for Yandex News with proper format.
Author: Max Lyuchin
Author URI: http://heartwp.com/
Text Domain: mlynf
Domain Path: /lang
*/

require_once dirname( __FILE__ ) . '/includes/metaboxes.php';
require_once dirname( __FILE__ ) . '/includes/settings.php';
require_once dirname( __FILE__ ) . '/includes/routes.php';

add_action( 'admin_enqueue_scripts', 'mlynf_media_button_js' );
function mlynf_media_button_js()
{
	$screen = get_current_screen();

	if ( 'toplevel_page_mlynf-settings' == $screen->id ) {
		wp_enqueue_media();
		wp_enqueue_script('mlynf-main', plugin_dir_url( __FILE__ ) . 'js/main.js', array('jquery'), '1.0', true);
	}
}

add_action( 'plugins_loaded', 'mlynf_load_textdomain' );
function mlynf_load_textdomain() {
	load_plugin_textdomain( 'mlynf', false, plugin_basename( dirname( __FILE__ ) ) . '/lang' ); 
}

function mlynf_feed()
{
	$post_types = get_option( 'mlynf_post_types', array('post') );
	$categories = get_option( 'mlynf_categories' );
	$count = (int) get_option( 'mlynf_count', 10 );
	
	$args = array(
		'post_type' => $post_types,
		'posts_per_page' => $count,
		'meta_query' => array(
			'relation' => 'OR',
			array(
				'key' => '_mlynf_ignore',
				'value' => 1,
				'compare' => '!=',
				'type' => 'NUMERIC',
			),
			array(
				'key' => '_mlynf_ignore',
				'compare' => 'NOT EXISTS',
			),
		),
	);

	if ( $categories && !in_array( 0, $categories ) ) {
		$args['category__in'] = $categories;
	}

	query_posts( $args );

	add_filter( 'wp_title_rss', 'mlynf_return_empty' );
	add_filter( 'bloginfo_rss', 'mlynf_bloginfo_rss', 10, 2 );

	add_action( 'rss2_head', 'mlynf_feed_header' );
	add_action( 'rss2_item', 'mlynf_feed_item' );
	//add_action();
	require_once ABSPATH . WPINC . '/feed-rss2.php';

	die;
}

function mlynf_return_empty()
{
	return '';
}

function mlynf_bloginfo_rss( $bloginfo, $show )
{
	switch ( $show ) {
		case 'name':
			return get_option( 'mlynf_channel_title', get_bloginfo( 'sitename' ) );
			break;
		case 'url':
			return get_option( 'mlynf_channel_link', get_bloginfo( 'url' ) );
			break;
		case 'description':
			return get_option( 'mlynf_channel_description', get_bloginfo( 'description' ) );
			break;
		default:
			return $bloginfo;
			break;
	}
}

function mlynf_feed_header()
{
	$logo = get_option( 'mlynf_channel_logo' );
	$logo_square = get_option( 'mlynf_channel_logo_square' );

	if ( !empty( $logo ) ) {
		echo "\t<yandex:logo>{$logo}</yandex:logo>\n";
	}
	if ( !empty( $logo_square ) ) {
		echo "\t<yandex:logo type=\"square\">{$logo_square}</yandex:logo>\n";
	}
}

function mlynf_feed_item()
{
	$content =  get_the_content();

	echo "\t<yandex:full-text><![CDATA[" . apply_filters( 'mlynf_rss_content', $content ) . "]]></yandex:full-text>\n";

	$media = mlynf_get_media( $content );
	foreach ($media as $key => $enclosure) {
		echo "\t\t<enclosure url=\"{$enclosure['url']}\" type=\"{$enclosure['type']}\" />\n";
	}

	$genre = get_post_meta( get_the_ID(), '_mlynf_genre', true );
	if ( $genre ) {
		echo "\t\t<yandex:genre>{$genre}</yandex:genre>\n";
	}


}

add_filter( 'mlynf_rss_content', 'mlynf_rss_content' );
function mlynf_rss_content( $content )
{
	// Remove all images and plain links (video services)
	$content = preg_replace( "/<img[^>]*>/", '', $content );
	$content = preg_replace( "/<a[^>]*href[^>]*>\s*<\/a>/", '', $content );
	$content = preg_replace( "/\n\s+\n/", "\n\n", $content );
	$content = preg_replace( "/\n{2,}/", "\n\n", $content );

	return apply_filters( 'the_content', $content );
}

function mlynf_get_media( $content )
{
	$content = preg_replace( 
		"/<a[^>]*href\s*=\s*['\"](.*?\.(?:gif|png|jpg|jpeg))['\"][^>]*><img[^>]*><\/a>/i", 
		"<img src=\"$1\" />", $content );

	preg_match_all( "/<img[^>]*src\s*=\s*['\"](.*?\.(gif|png|jpg|jpeg))['\"][^>]*>/i", $content, $matches, PREG_SET_ORDER );

	$media = array();
	foreach ($matches as $key => $match) {
		$type = $match[2];
		if ( 'jpg' == $type ) $type = 'jpeg';

		$media[] = array(
			'url' => $match[1],
			'type' => 'image/'.$type,
		);
	}
	return $media;
}