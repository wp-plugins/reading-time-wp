<?php
/**
 * Plugin Name: Reading Time WP
 * Plugin URI: http://jasonyingling.me/reading-time-wp/
 * Description: Add an estimated reading time to your posts.
 * Version: 1.0.1
 * Author: Jason Yingling
 * Author URI: http://jasonyingling.me
 * License: GPL2
 */
 
 /*  Copyright 2014  Jason Yingling  (email : yingling017@gmail.com)

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

class readingTimeWP {
	
	// Add label option using add_option if it does not already exist
	
	public function __construct() {
		$defaultSettings = array(
			'label' => 'Reading Time: ',
			'postfix' => 'minutes',
			'wpm' => 300,
			'before_content' => 'true',
		);
		
		$rtReadingOptions = get_option('rt_reading_time_options');
		
		add_shortcode( 'rt_reading_time', array($this, 'rt_reading_time') );
		add_option('rt_reading_time_options', $defaultSettings);
		add_action('admin_menu', array($this, 'rt_reading_time_admin_actions'));
		
		if ($rtReadingOptions['before_content'] === 'true') {
		
			add_filter('the_content', array($this, 'rt_add_reading_time_before_content'));
		
		}
	}
	
	public function rt_reading_time($atts, $content = null) {
	
		extract (shortcode_atts(array(
			'label' => '',
			'postfix' => '',
		), $atts));
			
		$rtReadingOptions = get_option('rt_reading_time_options');
		
		$rtPostID = get_the_ID();
		$rtContent = get_the_content($rtPostID);
		$strippedContent = strip_shortcodes($rtContent);
		$wordCount = str_word_count($strippedContent);
		$readingTime = ceil($wordCount / $rtReadingOptions['wpm']);
		
		return "
		<span class='span-reading-time'>$label $readingTime $postfix</span>
		";
	}
	
	// Functions to create Reading Time admin pages
	public function rt_reading_time_admin() {
	    include('rt-reading-time-admin.php');
	}
	
	public function rt_reading_time_admin_actions() {
		add_options_page("Reading Time WP Settings", "Reading Time WP", "manage_options", "rt-reading-time-settings", array($this, "rt_reading_time_admin"));
	}
	
	// Calculate reading time by running it through the_content
	public function rt_add_reading_time_before_content($content) {
		
		$rtReadingOptions = get_option('rt_reading_time_options');
		
		$originalContent = $content;
		$strippedContent = strip_shortcodes($originalContent);
		$wordCount = str_word_count($strippedContent);
		$readingTime = ceil($wordCount / $rtReadingOptions['wpm']);
		
		$label = $rtReadingOptions['label'];
		$postfix = $rtReadingOptions['postfix'];
		
		$content = '<div class="rt-reading-time">'.'<span class="rt-label">'.$label.'</span>'.'<span class="rt-time">'.$readingTime.'</span>'.'<span class="rt-label"> '.$postfix.'</span>'.'</div>';
		$content .= $originalContent;
		return $content;
	}
	
}

$readingTimeWP = new readingTimeWP();

?>