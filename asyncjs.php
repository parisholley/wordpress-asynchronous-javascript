<?php
/*
Plugin Name: Asynchronous Assets (Javascript and CSS)
Plugin URI: https://github.com/parisholley/Wordpress-Asynchronous-Assets
Description: Improve page load performance by asynchronously loading javascript and CSS files using head.js
Version: 1.0
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
class AsynchronousAssets {
	private static $queue = array();
	private static $depends = array();
	private static $head_loaded = false;

	function init() {
		add_action('wp_print_scripts', 'AsynchronousAssets::action_prevent_script_output' );
		add_filter('script_loader_src', 'AsynchronousAssets::filter_queue_script', 10, 2 );
		add_filter('print_footer_scripts', 'AsynchronousAssets::filter_headjs' );
		add_filter('print_head_scripts', 'AsynchronousAssets::filter_headjs' );
	}

	/**
	 * Prevent wordpress from outputing scripts to page
	 **/
	function action_prevent_script_output() {
		global $wp_scripts;

		$wp_scripts->do_concat = true;
	}
	

	/**
	 * Wordpress has no ability to hook into script queuing, so this is a work around
	 **/
	function filter_queue_script($src, $handle) {
		self::$queue[] = "{'{$handle}': '$src'}";
	}

	/**
	 * Outputs headjs code in header or footer
	 **/
	function filter_headjs(){
		if(count(self::$queue) > 0){
			if(!$head_loaded){
				echo '<script type="text/javascript" src="' . plugins_url( '/js/head.load.min.js', __FILE__ ) . '"></script>';
			
				self::$head_loaded = true;
			}

			echo '<script type="text/javascript">head.js(' . implode(',', self::$queue) . ')</script>';

			self::$queue = array();
		}

		if(count(self::$depends) > 0){
			foreach(self::$depends as $handle => $depend){
				if(is_array($depend['deps'])){
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

	function wp_enqueue_async_script($handle, $src, $deps){
		self::$depends[$handle] = array(
			'src' => $src,
			'deps' => $deps
		);
	}
}

AsynchronousAssets::init();
?>
