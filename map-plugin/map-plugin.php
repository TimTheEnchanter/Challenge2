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
 * @since             1.0.0
 * @package           Map_Plugin
 *
 * @wordpress-plugin
 * Plugin Name:       Map Plugin
 * Plugin URI:        www.jameswebdesign.ca
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
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
define( 'PLUGIN_NAME_VERSION', '1.0.0' );

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
                           'description' => 'Books which we will be discussing on this blog.',
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
            $screen                   // Post type
        );
    }
}
add_action('add_meta_boxes', 'add_map_box');

function custom_map_html($post)
{
if( function_exists('acf_add_local_field_group') 
{

acf_add_local_field_group(array(
	'key' => 'group_5bac60d2bff48',
	'title' => 'map',
	'fields' => array(
		array(
			'key' => 'field_5bad01533d4a1',
			'label' => 'Map',
			'name' => 'map-selector',
			'type' => 'google_map',
			'instructions' => '',
			'required' => 1,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'center_lat' => '',
			'center_lng' => '',
			'zoom' => '',
			'height' => '',
		),
	),
	'location' => array(
		array(
			array(
				'param' => 'post_type',
				'operator' => '==',
				'value' => 'post',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => 1,
	'description' => '',
));

}
}