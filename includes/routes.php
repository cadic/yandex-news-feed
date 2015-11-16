<?php

add_action( 'init', 'mlynf_query_var' );
function mlynf_query_var()
{
    global $wp;
    $wp->add_query_var( 'mlynf' );
}

add_action( 'init', 'mlynf_insert_rewrite_rules' );
function mlynf_insert_rewrite_rules()
{
	$feed_url = get_option( 'mlynf_feed_url', 'yandex.xml' );
	$feed_regex = $feed_url . '$';

	add_rewrite_rule( $feed_regex, 'index.php?mlynf=1', 'top' );
}

function mlynf_check_feed( $params )
{
	if( isset( $params->query_vars['mlynf'] ) && $params->query_vars['mlynf'] == 1 ) {
		mlynf_feed();
	}
}
add_action( 'parse_request', 'mlynf_check_feed' );
