<?php
/*
Plugin Name: Asynchronous Javascript
Plugin URI: http://wordpress.org/extend/plugins/asynchronous-javascript/
Description: Improve page load performance by asynchronously loading javascript using head.js
Version: 1.1.2
Author: Paris Holley
Author URI: http://www.linkedin.com/in/parisholley
Author Email: mail@parisholley.com
License:

  Copyright 2013 Paris Holley (mail@parisholley.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as 
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
  
*/
class AsynchronousJS {
	private static $queue = array();
	private static $depends = array();
	private static $head_loaded = false;

	function init() {
		if(!defined('WP_ADMIN') || !WP_ADMIN){
			add_action('wp_print_scripts', 'AsynchronousJS::action_prevent_script_output' );
			add_filter('script_loader_src', 'AsynchronousJS::filter_queue_script', 10, 2 );
			add_filter('print_footer_scripts', 'AsynchronousJS::filter_headjs' );
			add_filter('print_head_scripts', 'AsynchronousJS::filter_headjs' );
		}
	}

	/**
	 * Prevent wordpress from outputing scripts to page
	 **/
	function action_prevent_script_output() {
		global $wp_scripts, $concatenate_scripts;

		$concatenate_scripts = true;
		$wp_scripts->do_concat = true;
	}
	

	/**
	 * Wordpress has no ability to hook into script queuing, so this is a work around
	 **/
	function filter_queue_script($src, $handle) {
		global $wp_scripts;

		self::$depends[$handle] = array(
			'src' => $src,
			'deps' => $wp_scripts->registered[$handle]->deps
		);
	}

	/**
	 * Outputs headjs code in header or footer
	 **/
	function filter_headjs(){
		if(count(self::$depends) > 0){
			if(!self::$head_loaded){
				echo '<script type="text/javascript" src="' . plugins_url( '/js/head.load.min.js', __FILE__ ) . '"></script>';
			
				self::$head_loaded = true;
			}
			
			foreach(self::$depends as $handle => $depend){
				if(is_array($depend['deps']) && count($depend['deps']) > 0){
					echo '<script type="text/javascript">head.ready("' . implode(',', $depend['deps']) . '", function(){head.js({"' . $handle . '": "' . $depend['src'] . '"})})</script>';
				}elseif(is_string($depend['deps'])){
					echo '<script type="text/javascript">head.ready("' . $depend['deps'] . '", function(){head.js({"' . $handle . '": "' . $depend['src'] . '"})})</script>';
				}else{
					echo '<script type="text/javascript">head.js({"' . $handle . '": "' . $depend['src'] . '"});</script>';
				}
			}

			self::$depends = array();
		}

		return false; // prevent printing of javascript
	}
}

AsynchronousJS::init();
?>
