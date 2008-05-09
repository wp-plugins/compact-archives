=== Compact Archives ===
Contributors: RobMarsh
Tags: posts, archive, archives
Requires at least: 1.5
Tested up to: 2.5.1
Stable tag: 2.0.0

Displays a monthly archive of posts in a more compact form than the usual long list.

== Description ==

[Compact Archives](http://rmarsh.com/plugins/compact-archives/) displays the monthly archive of posts in a more compact form than the usual long list. It can be shown as a compact block suitable for the body of an archives page:

`2009: Jan Feb Mar Apr May Jun Jul Aug Sep Oct Nov Dec 
2008: Jan Feb Mar Apr May Jun Jul Aug Sep Oct Nov Dec 
2007: Jan Feb Mar Apr May Jun Jul Aug Sep Oct Nov Dec`

or in an even more compact form to fit a sidebar:

`2009: J F M A M J J A S O N D 
2008: J F M A M J J A S O N D 
2007: J F M A M J J A S O N D` 
				
or something in between:

`2009: 01 02 03 04 05 06 07 08 09 10 11 12
2008: 01 02 03 04 05 06 07 08 09 10 11 12
2007: 01 02 03 04 05 06 07 08 09 10 11 12`

== Installation ==

1. Upload the plugin folder to your /wp-content/plugins/ folder.

1. Go to the **Plugins** page and activate the plugin.

1. Put `<?php compact_archive(); ?>` at the place in your template 
where you want it to appear, e.g., in your sidebar:

`	<ul>
		<?php compact_archive(); ?>
	</ul>`

== Frequently Asked Questions ==

= How Do I Get the Different Layouts? =

The template tag, `compact_archive`, has some parameters:

`	compact_archive($style='initial', $before='<li>', $after='</li>');`
	
If $style == 'initial' (the default) the display will fit into a sidebar:

`	2009: J F M A M J J A S O N D 
	2008: J F M A M J J A S O N D 
	2007: J F M A M J J A S O N D` 

If $style == 'block' the display will be wide enough to fill the main column of a page:

`	2009: Jan Feb Mar Apr May Jun Jul Aug Sep Oct Nov Dec 
	2008: Jan Feb Mar Apr May Jun Jul Aug Sep Oct Nov Dec 
	2007: Jan Feb Mar Apr May Jun Jul Aug Sep Oct Nov Dec`

If $style == 'numeric' the display will use month numbers:

`	2009: 01 02 03 04 05 06 07 08 09 10 11 12
	2008: 01 02 03 04 05 06 07 08 09 10 11 12
	2007: 01 02 03 04 05 06 07 08 09 10 11 12`

$before and $after wrap each line of output. The default values make 
each line of the archive into a list item:

`	<ul>
		<?php compact_archive(); ?>
	</ul>`

= Is There Any Scope for CSS Styling? =

The year links at the start of each line are wrapped in <strong></strong> tags while months with no posts are wrapped with <span class="emptymonth"></span> so you can differentiate them visually using your style sheet.

= What if My Site is in Another Language? =

The plugin chooses month names and abbreviations according to the language locale, WPLANG, set in wp-config.php.

Compact Archives also honours whatever kind of permalink pattern you have set.

== Version History ==

* 2.0.0
	* 3-10 x faster depending on number of posts
	* makes the most of [Plugin Output Cache v.4.x](http://rmarsh.com/plugins/poc-cache/)
* 1.0.6 introduces numeric display
* 1.0.5 fixes a display bug for locales with accented characters
* 1.0.4 makes use of the Plugin Output Cache to speed things up
* 1.0.3 fixes a small problem with undated posts 
* 1.0.1 speeds up the plugin for WordPress 2.1+ 