<?php
/*
Plugin Name: Asynchronous Javascript
Plugin URI: http://wordpress.org/extend/plugins/asynchronous-javascript/
Description: Improve page load performance by asynchronously loading javascript using head.js
Version: 1.3.5
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

if(!class_exists('NHP_Options')){
	require_once( dirname( __FILE__ ) . '/lib/nhp/options/options.php' );
}

class AsynchronousJS {
	private static $queue = array();
	private static $depends = array();
	private static $head_loaded = false;
	private static $default_head_file = 'head.load.min.js';

	static function init() {
		if(!defined('WP_ADMIN') || !WP_ADMIN){
			add_action('wp_print_scripts', array('AsynchronousJS', 'action_prevent_script_output') );
			add_filter('script_loader_src', array('AsynchronousJS', 'filter_queue_script'), 10, 2 );
			add_filter('print_footer_scripts', array('AsynchronousJS', 'filter_headjs') );
			add_filter('print_head_scripts', array('AsynchronousJS', 'filter_headjs') );
		}else{
			add_action('init', array('AsynchronousJS', 'admin'));
		}
	}

	static function admin(){
		$args = array();

		$args['share_icons']['twitter'] = array(
			'link' => 'http://twitter.com/parisholley',
			'title' => 'Folow me on Twitter', 
			'img' => NHP_OPTIONS_URL.'img/glyphicons/glyphicons_322_twitter.png'
		);

		$args['share_icons']['linked_in'] = array(
			'link' => 'http://www.linkedin.com/in/parisholley',
			'title' => 'Find me on LinkedIn', 
			'img' => NHP_OPTIONS_URL.'img/glyphicons/glyphicons_337_linked_in.png'
		);

		$args['opt_name'] = 'asyncjs';
		$args['menu_title'] = 'Async JS';
		$args['page_title'] = 'Asynchronous Javascript';
		$args['page_slug'] = 'asyncjs';
		$args['show_import_export'] = false;
		$args['page_position'] = 102419882;
		$args['dev_mode'] = false;

		$sections = array(array(
			'icon' => NHP_OPTIONS_URL.'img/glyphicons/glyphicons_280_settings.png',
			'title' => 'Settings',
			'fields' => array(
				'exclude_name' => array(
					'id' => 'exclude_name',
					'type' => 'textarea',
					'title' => 'Exclude by Name',
					'desc' => 'Enter a comma delimited list (ie: "jquery,jqueryui").',
					'sub_desc' => 'The name is the key used to queue the javascript file within wordpress.'
				),
				'exclude_js' => array(
					'id' => 'exclude_js',
					'type' => 'textarea',
					'title' => 'Exclude by File',
					'desc' => 'Enter a comma delimited list (ie: "file1.js,file2.js").',
					'sub_desc' => 'If you do not know the script key, you exclude based on the file name.'
				),
				'head_file' => array(
					'id' => 'head_file',
					'type' => 'text',
					'title' => 'Select Head.js File',
					'desc' => 'Enter the filename of the head.js file in the js folder.',
					'sub_desc' => 'This is an advanced setting, leave it as default if you are unsure of what it does.',
					'std' => self::$default_head_file
				),
				'always_on' => array(
					'id' => 'always_on',
					'type' => 'checkbox',
					'title' => 'Always include head.js file',
					'desc' => 'Do you want to always include the head.js file in your head section even if there are no js files to output?',
					'sub_desc' => 'This is useful if you\'re using some of the other features of head.js',
				)
			)
		));

		new NHP_Options($sections, $args);
	}

	/**
	 * Prevent wordpress from outputing scripts to page
	 **/
	static function action_prevent_script_output() {
		global $wp_scripts, $concatenate_scripts;

		$concatenate_scripts = true;
		$wp_scripts->do_concat = true;
	}
	

	/**
	 * Wordpress has no ability to hook into script queuing, so this is a work around
	 **/
	static function filter_queue_script($src, $handle) {
		global $wp_scripts;

		self::$depends[$handle] = array(
			'src' => $src,
			'deps' => $wp_scripts->registered[$handle]->deps
		);
	}

	/**
	 * Outputs headjs code in header or footer
	 **/
	static function filter_headjs(){
		$options = get_option('asyncjs');
		$names = explode(',', $options['exclude_name']);
		$files = explode(',', $options['exclude_js']);

		if (empty($options['head_file'])){
			$options['head_file'] = self::$default_head_file;
		}
		
		$headinclude = '<script type="text/javascript" src="' . plugins_url( '/js/'.$options['head_file'], __FILE__ ) . '"></script>';

		if(isset($options['always_on']) && $options['always_on'] && !self::$head_loaded){
			echo $headinclude;
			self::$head_loaded = true;
		}

		if(count(self::$depends) > 0){
			$handles = array();

			foreach(self::$depends as $handle => $depend){
				$exclude = false;

				foreach($files as $file){
					if(!empty($file) && strpos($depend['src'], $file) !== false){
						$exclude = true;
						break;
					}
				}

				$src = apply_filters('async_js_src', $depend['src']);

				if(!in_array($handle, $names) && !$exclude){
					$handles[] = '{"' . $handle . '": "' . $src . '"}';
				}else{
					echo '<script type="text/javascript" src="' . $src . '"></script>';
				}
			}

			if(count($handles) > 0){
				if(!self::$head_loaded){
					echo $headinclude;
				
					self::$head_loaded = true;
				}

				echo '<script type="text/javascript">head.js(' . implode(',', $handles) . ');</script>';
			}

			self::$depends = array();
		}

		return false; // prevent printing of javascript
	}
}

AsynchronousJS::init();
?>
