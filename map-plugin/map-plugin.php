<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              www.jameswebdesign.ca
 * @since             1.1.1
 * @package           Map_Plugin
 *
 * @wordpress-plugin
 * Plugin Name:       Map Plugin
 * Plugin URI:        www.jameswebdesign.ca
 * Description:       This plugin should allow you to create a custom map. Shortcode is [map_q p=MAP_POST_ID]
 * Version:           1.1.1
 * Author:            James Parrott
 * Author URI:        www.jameswebdesign.ca
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       map-plugin
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PLUGIN_NAME_VERSION', '1.1.1' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-map-plugin-activator.php
 */
function activate_map_plugin() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-map-plugin-activator.php';
	Map_Plugin_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-map-plugin-deactivator.php
 */
function deactivate_map_plugin() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-map-plugin-deactivator.php';
	Map_Plugin_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_map_plugin' );
register_deactivation_hook( __FILE__, 'deactivate_map_plugin' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-map-plugin.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_map_plugin() {

	$plugin = new Map_Plugin();
	$plugin->run();

}
run_map_plugin();

add_action('init', 'custom_map');

function custom_map()
{
    register_post_type('custom_map',
                       array(
                           'labels'      => array(
                               'name'          => __('Maps'),
                               'singular_name' => __('Map'),
                           ),
                           'description' => 'Custom Maps.',
  						   'public' => true,
  						   'menu_position' => 20,
                           'supports' => array( 'title', 'editor', 'custom-fields' )
                       )
    );
}

function add_map_box()
{
    $screens = ['custom_map'];
    foreach ($screens as $screen) {
        add_meta_box(
            'map_box_id',           // Unique ID
            'Custom Map Location',  // Box title
            'custom_map_html',  // Content callback, must be of type callable
            $screen,                   // Post type
            'normal', // $context
        	'high'  // $priority
        );
    }

    wp_nonce_field( 'location_map_nonce_action', 'location_map_nonce' );
}
add_action('add_meta_boxes', 'add_map_box');

function custom_map_html($post)
{
	//echo "Test";
	global $post;  
	$lat = get_post_meta($post->ID, 'lat', true);  
	$lng = get_post_meta($post->ID, 'lng', true); 
	$nonce = wp_create_nonce(basename(__FILE__));

	?>
	<div class="maparea" id="map-canvas"></div>
	<input type="hidden" name="glat" id="latitude" value="<?php echo $lat; ?>">
	<input type="hidden" name="glng" id="longitude" value="<?php echo $lng; ?>">
	<input type="hidden" name="custom_meta_box_nonce" value="<?php echo $nonce; ?>">  
	<?php


}

add_action('admin_print_styles-post.php', 'custom_js_css');
add_action('admin_print_styles-post-new.php', 'custom_js_css');
function custom_js_css() {
	global $post;
    wp_enqueue_style('gmaps-meta-box', '/wp-content/plugins/map-plugin/js/gmaps/style.css');
    wp_enqueue_script('gmaps-meta-box', '/wp-content/plugins/map-plugin/js/gmaps/map.js');
    $helper = array(
    	'lat' => get_post_meta($post->ID,'lat',true),
    	'lng' => get_post_meta($post->ID,'lng',true)
    );
    wp_localize_script('gmaps-meta-box','helper',$helper);
}

function save_embed_gmap($post_id) {   
    // verify nonce
    if (!wp_verify_nonce($_POST['custom_meta_box_nonce'], basename(__FILE__)))
        return $post_id;
        
    // check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return $post_id;
        
    // check permissions
    if ('page' == $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id))
            return $post_id;
        } elseif (!current_user_can('edit_post', $post_id)) {
            return $post_id;
    }  
    
    $oldlat = get_post_meta($post_id, "lat", true);
    
    $newlat = $_POST["glat"]; 
    if ($newlat != $oldlat) {
        update_post_meta($post_id, "lat", $newlat);
    } 
    $oldlng = get_post_meta($post_id, "lng", true);
    
    $newlng = $_POST["glng"]; 
    if ($newlng != $oldlng) {
        update_post_meta($post_id, "lng", $newlng);
    } 
}
add_action('save_post', 'save_embed_gmap');

add_shortcode('map_q', 'map_shortcode_query');
function map_shortcode_query($atts, $content){
  extract(shortcode_atts(array( 
   'posts_per_page' => '1',
   'post_type' => 'custom_map',
   'caller_get_posts' => 1)
   , $atts));

  global $post;

  $posts = new WP_Query($atts);
  $output = '';
    if ($posts->have_posts())
        while ($posts->have_posts()):
            $posts->the_post();
            $out = get_the_content();
            
    endwhile;
  else
    return; // no posts found

  wp_reset_query();
  return html_entity_decode($out);
}

function wp_google_scripts() {
	$API_KEY = "AIzaSyCYS_OApC1pllMd8tHlS-i2ZNGyLPr8R-U";
	wp_enqueue_script( 'google-maps-native', "http://maps.googleapis.com/maps/api/js?key=".$API_KEY);
}
add_action( 'admin_enqueue_scripts', 'wp_google_scripts' );