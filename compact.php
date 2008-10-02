<?php
/*
Plugin Name: Compact Archives
Plugin URI: http://rmarsh.com/plugins/compact-archives/
Description: Displays a compact monthly archive instead of the default long list. Either display it as a block suitable for the body of a page or in a form compact enough for a sidebar. 
Version: 2.1.0
Author: Rob Marsh, SJ
Author URI: http://rmarsh.com/
*/

/*
	The idea for this plugin comes from the SmartArchive of Justin Blanton
	( http://justinblanton.com/projects/smartarchives/). It is a rewrite of
	the 'block' half of his tag. I have added a very compact version that fits
	nicely in a sidebar.
*/

/*
Copyright 2008  Rob Marsh, SJ  (http://rmarsh.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

/*
	Display the monthly archive of posts in a more compact form than the usual long list.
	
	If $style == 'block' the display will be wide enough to fill the main column of a page:
	
		2006: Jan Feb Mar Apr May Jun Jul Aug Sep Oct Nov Dec 
		2005: Jan Feb Mar Apr May Jun Jul Aug Sep Oct Nov Dec 
		2004: Jan Feb Mar Apr May Jun Jul Aug Sep Oct Nov Dec 
		
	If $style == 'initial' (the default) the display will fit into a sidebar.

				2006: J F M A M J J A S O N D 
				2005: J F M A M J J A S O N D 
				2004: J F M A M J J A S O N D 

	If $style == 'numeric' the display will show month numbers.

				2006: 01 02 03 04 05 06 07 08 09 10 11 12
				2005: 01 02 03 04 05 06 07 08 09 10 11 12
				2004: 01 02 03 04 05 06 07 08 09 10 11 12

	$before and $after wrap each line of output. The default values are suitable for such use as:

		<ul>
			<?php compact_archive(); ?>
		</ul>
		
	Should work fine with whatever kind of permalink you are using.
	The month abbreviations should adapt to the locale set in wp-config.php.
	The year link at the start of each line is wrapped in <strong></strong> and months with no posts 
	are wrapped in <span class="emptymonth"></span> so you can differentiate them visually
	
	If my Plugin Output Cache Plugin is installed the Compact Archive output will be cached for efficiency.
	
*/
function compact_archive( $style='initial', $before='<li>', $after='</li>', $in_cats='' ) {
 	$result = false;
	// if the Plugin Output Cache is installed we can cheat...
	if (defined('POC_CACHE_4')) {
		$key = 'c_a'.$style.$before.$after;
		poc_cache_timer_start();
		$result = poc_cache_fetch($key);
		if ($result) $cache_time = sprintf('<!-- Compact Archive took %.3f milliseconds from the cache -->', 1000 * poc_cache_timer_start());
	}
	// ... otherwise we do it the hard way
	if (false === $result) {
		$result = utf8_encode(get_compact_archive($style, $before, $after, $in_cats));
		if (defined('POC_CACHE_4')) {
			poc_cache_store($key, $result);
			$cache_time = sprintf('<!-- Compact Archive took %.3f milliseconds -->', 1000 * poc_cache_timer_start());			
		}
	} 
	echo $result;
	if (defined('POC_CACHE_4')) echo $cache_time;
}

/********************************************************************************************************
	Stuff below this point is not meant to be used directly
*********************************************************************************************************/

function get_compact_archive( $style='initial', $before='<li>', $after='</li>', $in_cats='' ) {
	global $wpdb, $wp_version;
	setlocale(LC_ALL,WPLANG); // set localization language
	$below21 = version_compare($wp_version, '2.1','<');
	// WP 2.1 changed the way post_status and post_type fields work
	if ($below21) {
		$now = current_time('mysql');
		$results = $wpdb->get_results("SELECT DISTINCT YEAR(post_date) AS year, MONTH(post_date) AS month FROM " . $wpdb->posts . " WHERE post_date <'" . $now . "' AND post_status='publish' AND post_password='' ORDER BY year DESC, month DESC");
	} else {
		if (!$in_cats) {
			$results = $wpdb->get_results("SELECT DISTINCT YEAR(post_date) AS year, MONTH(post_date) AS month FROM $wpdb->posts WHERE post_type='post' AND post_status='publish' AND post_password='' ORDER BY year DESC, month DESC");
	    } else {
			$results = $wpdb->get_results("SELECT DISTINCT YEAR(post_date) AS year, MONTH(post_date) AS month FROM $wpdb->posts  INNER JOIN $wpdb->term_relationships ON ($wpdb->posts.ID = $wpdb->term_relationships.object_id) INNER JOIN $wpdb->term_taxonomy ON ($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id) WHERE post_type='post' AND post_status='publish' AND post_password='' AND $wpdb->term_taxonomy.taxonomy = 'category' AND $wpdb->term_taxonomy.term_id IN ($in_cats) GROUP BY $wpdb->posts.ID ORDER BY year DESC, month DESC");			
		}
	}
	if (!$results) {
		return $before.__('Archive is empty').$after;
	}
	$dates = array();
	foreach ($results as $result) {
		$dates[$result->year][$result->month] = 1;
	}
	unset($results);
	$result = '';
	foreach ($dates as $year => $months){
		$result .= $before.'<strong><a href="'.get_year_link($year).'">'.$year.'</a>: </strong> ';
		for ( $month = 1; $month <= 12; $month += 1) {
			$month_has_posts = (isset($months[$month]));
			$dummydate = strtotime("$month/01/2001");
			// get the month name; strftime() localizes
			$month_name = strftime("%B", $dummydate); 
			switch ($style) {
			case 'initial':
				$month_abbrev = $month_name[0]; // the inital of the month
				break;
			case 'block':
				$month_abbrev = strftime("%b", $dummydate); // get the short month name; strftime() localizes
				break;
			case 'numeric':
				$month_abbrev = strftime("%m", $dummydate); // get the month number, e.g., '04'
				break;
			default:
				$month_abbrev = $month_name[0]; // the inital of the month
			}
			if ($month_has_posts) {
				$result .= '<a href="'.get_month_link($year, $month).'" title="'.$month_name.' '.$year.'">'.$month_abbrev.'</a> ';
			} else {
				$result .= '<span class="emptymonth">'.$month_abbrev.'</span> ';
			}
		}
		$result .= $after."\n";
	}
	return $result;
}

?>