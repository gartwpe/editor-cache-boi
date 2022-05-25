<?php
/**
 * Plugin Name: Cache Boi
 * Description: Allow editors to purge WPE cache from the toolbar
 * Plugin URI:
 * Version: 0.1.0
 *
 * Author: Gart
 * Author URI:
 * Text Domain: cacheboi
 *
 * License:
 * Copyright:
 *
 * @package Cacheboi
 */

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
        echo 'Hi there potential hacker.  I\'m just a plugin, not much I can do when called directly. You silly goose...';
        exit;
}


if ( ! function_exists( 'wpe_cache_boi' ) ) {
	function wpe_cache_boi() {
		if ( class_exists( 'WpeCommon' ) && method_exists( 'WpeCommon', 'purge_varnish_cache' )  ) {
			WpeCommon::purge_memcached();
			WpeCommon::clear_maxcdn_cache();
			WpeCommon::purge_varnish_cache();
		}
	}
}

function wpe_cache_boi_link($wp_admin_bar) {

	$user = wp_get_current_user();
	$cache_boi_is_editor = $user->has_cap( 'edit_others_posts' );

 if ($cache_boi_is_editor) {
    $args = array(
        'id' => 'wpe_cache_boi',
        'title' => 'Purge cache',
        'href' => '/wp-admin/?wpe_cache_boi_purge',
        'meta' => array(
            'class' => 'wpe_cache_boi',
            'title' => 'Purge cache'
            )
    	);
    $wp_admin_bar->add_node($args);
	}
}

add_action('admin_bar_menu', 'wpe_cache_boi_link', 999);

function wpe_cache_boi_do_the_thing() {

	$user = wp_get_current_user();
	$cache_boi_is_editor = $user->has_cap( 'edit_others_posts' );

	if ((isset($_GET['wpe_cache_boi_purge'])) && $cache_boi_is_editor) {
	    wpe_cache_boi();
	    function cache_boi_admin_notice__success() {
	    ?>
	    <div class="notice notice-success is-dismissible">
	        <p><?php _e( 'Caches purged!', 'cache-boi-text-domain' ); ?></p>
	    </div>
	    <?php
			}
		}
	}

add_action( 'admin_bar_menu', 'wpe_cache_boi_do_the_thing');
add_action( 'admin_notices', 'cache_boi_admin_notice__success' );
