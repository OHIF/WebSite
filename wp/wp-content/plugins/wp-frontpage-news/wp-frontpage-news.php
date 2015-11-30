<?php
/**
 * @package WP-Frontpage-News
 * @author WPCode United / Yann Dubois
 * @version 1.0.2
 */

/*
 Plugin Name: WP Frontpage News
 Plugin URI: http://www.wpcode-united.com/wordpress-plugin/wp-frontpage-news
 Description: Advanced frontpage and widget news slider
 Version: 1.0.2
 Author: WPCode United / Yann Dubois
 Author URI: http://www.wpcode-united.com/
 License: GPL2
 */

/**
 * @copyright 2013  WPCode United  ( email : support _at_ wpcode-united.com )
 *
 *  Original development of this plugin was kindly funded by WPCode-United
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */


/**
 Revision 1.0.2:
 * Small css fix for theme TwentyFourteen when block inside content (image width)
 * Now checks WP and PHP version upon plugin install/activate
 Revision 1.0.1:
 * Added full support of content insertion button with WordPress 3.9+ (with TinyMCE 4.0)
 Revision 1.0.0:
 * Stable release 1 (completed features)
 * Fixed bug limiting number of blocks to choose from in the post editor
 * Support for multiple themes (additional themes are available with the pro add-on)
 * Dynamic theme preview in the admin area
 * Automatic default pagination setup
 * Better drag-and-drop in the admin area
 * Improved administration screens
 * Moved "About..." tab to main admin menu
 * Added bullet-type pagination
 * Fixed reset query bug
 * Improved default theme
 * Checked WP 3.8.x and WP 3.9 compatibility
 Revision 0.2.4:
 * Beta release 5 (bugfix)
 * Fixed bug limiting number of blocks to choose from in the widget admin
 Revision 0.2.3:
 * Beta release 4 (bugfix)
 * Fixed bug in the admin drag and drop area
 Revision 0.2.2:
 * Beta release 3 (small improvements):
 * Added "about" tab with links to official support site
 * Small CSS improvements
 * Small performance improvements in the admin
 * Small text improvements
 * Improved readme page with video tutorial and many more screenshots
 * Checked WP 3.7 and 3.7.1 full compatibility
 * Added links to the now available "[pro add-on](http://www.wpcode-united.com/wordpress-plugin/wp-frontpage-news)"
 Revision 0.2.1:
 - Beta release 2 (bug fixes):
 * Fixed image margin
 * Added default right and bottom 4px image margins
 * Added choice of first image or first post attachment
 * Drag-and drop containers in the widget display admin now grow in height as items are added
 Revision 0.2.0:
 - Original beta release:
 * Widget title can now be displayed at your choice
 * Sidebar widgets now seamlessly integrate with your theme's widget styling
 * Stylesheet compatibility optimized with WP 3.6 twentythirteen theme by default
 * Now retro-compatible up to WP 3.2 at least
 * Admin screenshot has been made to cimply with actual output
 * Better icons in the admin interface
 * Simplified settings, now completely usable out-of-the-box
 * Default image now included and pre-configured for perfect output even with articles without images
 Revision 0.1.0:
 - Original alpha release 00
 */

/** Class includes **/
include_once( dirname( __FILE__ ) . '/inc/yd-widget-framework.inc.php' );	// standard framework VERSION 20110405-01 or better
include_once( dirname( __FILE__ ) . '/inc/wp-fpn-main.inc.php' );			// custom classes
include_once( dirname( __FILE__ ) . '/inc/wp-fpn-widget.inc.php' );		// custom classes
include_once( dirname( __FILE__ ) . '/inc/wp-fpn-front.inc.php' );			// custom classes

/**
 * Just fill up necessary settings in the configuration array
 * to create a new custom plugin instance...
 * 
 */
global $wpcu_wpfn;
$wpcu_wpfn = new wpcuWPFnPlugin(
	array(
		'name' 				=> 'WP Frontpage News',
		'version'			=> '1.0.2',
		'has_option_page'	=> false,
		'option_page_title' => 'Frontpage News Settings',
		'op_donate_block'	=> false,
		'op_credit_block'	=> false,
		'op_support_block'	=> false,
		'has_toplevel_menu'	=> false,
		'has_shortcode'		=> false,
		'shortcode'			=> '',
		'has_widget'		=> false,
		'widget_class'		=> '',
		'has_cron'			=> false,
		'crontab'			=> array(),
		'has_stylesheet'	=> false,
		'stylesheet_file'	=> 'css/wpcufpn.css',
		'has_translation'	=> false,
		'translation_domain'=> 'wpcufpn_front.css', // must be copied in the widget class!!!
		'translations'		=> array(
			array( 'English', 'WPCode United', 'http://www.wpcode-united.com/' ),
			array( 'French', 'Yann Dubois', 'http://www.yann.com/' ),
		),		
		'initial_funding'	=> array( 'WPCode United', 'http://www.wpcode-united.com/' ),
		'additional_funding'=> array(),
		'form_blocks'		=> array(
			'Main options' => array( 
			)
		),
		'option_field_labels'=>array(
		),
		'option_defaults'	=> array(
		),
		'form_add_actions'	=> array(
		),
		'has_cache'			=> false,
		'option_page_text'	=> '',
		'backlinkware_text' => '',
		'plugin_file'		=> __FILE__,
		'has_activation_notice'	=> false,
		'activation_notice' => ''
 	)
);
?>