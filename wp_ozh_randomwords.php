<?php
/*
Plugin Name: Ozh' Random Words
Plugin URI: http://planetozh.com/blog/my-projects/wordpress-plugin-random-words/
Description: Returns random entries from user defined lists (<a href="admin.php?page=wp_ozh_randomwords.php">manual & setup</a>)
Version: 1.0.1
Author: Ozh
Author URI: http://planetOzh.com
*/

global $wp_ozh_randomwords;

function wp_ozh_randomwords_menu() {
	require_once(dirname(__FILE__).'/admin.php');
	add_options_page('Configure your Random Words lists', 'Random Words', 9, 'wp_ozh_randomwords', 'wp_ozh_randomwords_adminpage');
}

function wp_ozh_randomwords_postcontent($input) {
	/* replace [random:animals] inside posts */
	$input = preg_replace_callback ("/\[random:([^\]]+)\]/", "wp_ozh_randomwords_returnword", $input);
	return ($input);
}

function wp_ozh_randomwords($input='', $display=1) {
	$result = wp_ozh_randomwords_returnword(array('',trim($input)));
	if ($display) {
		print $result;
	} else {
		return $result;
	}
}

function wp_ozh_randomwords_returnword($input) {
	if (get_option('ozh_randomwords_status') == 'installed') {
		srand((float) microtime()*1000000);
		foreach (get_option('ozh_randomwords') as $array) {
			if ($array[0] == $input[1]) {
				return stripslashes (trim($array[mt_rand(1,count($array)-1)],"\r\n"));
				break;
			}
		}
	}
	return "<!-- error: random word not found. List was : '$input[1]' -->";
}


add_action('admin_menu', 'wp_ozh_randomwords_menu');
add_filter('the_content','wp_ozh_randomwords_postcontent',1);

?>
