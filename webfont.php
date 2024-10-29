<?php
/**
 * @package Ayar Web Kit
 */
/*
Plugin Name: Ayar Web Kit
Plugin URI: http://webfont.myanmapress.com
Description: webfont embedding plugins for wordpress.
Version: 1.0
Author: Sithu Thwin
Author URI: http://www.thwin.net/
License: GPLv2 or later
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/
if(defined('AWK_VERSION')) return;
define('AWK_VERSION', '1.0');
define('AWK_PLUGIN_PATH', dirname(__FILE__));
define('AWK_PLUGIN_FOLDER', basename(AWK_PLUGIN_PATH));

if(defined('WP_ADMIN') && defined('FORCE_SSL_ADMIN') && FORCE_SSL_ADMIN){
    define('AWK_PLUGIN_URL', rtrim(str_replace('http://','https://',get_option('siteurl')),'/') . '/'. PLUGINDIR . '/' . basename(dirname(__FILE__)) );
}else{
    define('AWK_PLUGIN_URL', rtrim(get_option('siteurl'),'/') . '/'. PLUGINDIR . '/' . basename(dirname(__FILE__)) );
}

/*Language loader*/
if (function_exists('load_plugin_textdomain')) {
        load_plugin_textdomain( 'AWK', false, AWK_PLUGIN_FOLDER . '/languages');
}
include (AWK_PLUGIN_PATH.'/includes/class-awk-options.php');
include (AWK_PLUGIN_PATH.'/includes/Browser.php');

$browser = new Browser();
$is_feed = is_feed();

$awk_options = get_option( 'awk_options' );

$fonts_embed_settings = ($awk_options['fonts_embeded'] ? 'yes':'');
$ayar_toolbar = ($awk_options['ayar_toolbar'] ? 'yes':'');
$converter= ($awk_options['converter'] ? 'yes':'');
$rss = ($awk_options['rss_enable'] ? 'yes':'');
$mobile = ($awk_options['mobile_enable'] ? 'yes':'');
$fonts_server = $awk_options['fonts_server_url'];
$template_sect = ($awk_options['template'] ? 'yes':'');
$locale_sect = ($awk_options['localization'] ? 'yes':'');
$my_calendar = ($awk_options['my_calendar'] ? 'yes':'');
$my_calendar_head = ($awk_options['my_calendar_head'] ? 'yes':'');
$my_calendar_widget = ($awk_options['my_calendar_widget'] ? 'yes':'');

if ($fonts_embed_settings == 'yes'){
include (AWK_PLUGIN_PATH.'/includes/fonts-embed.php');
}

if ($ayar_toolbar == 'yes'){
include (AWK_PLUGIN_PATH.'/includes/ayar_toolbar.php');
}

if ($converter == 'yes'){
include (AWK_PLUGIN_PATH.'/includes/converter_class.php');

	if ($rss == 'yes'){
	include (AWK_PLUGIN_PATH.'/includes/rss_converter.php');
	}

	if ($mobile == 'yes'){
	include (AWK_PLUGIN_PATH.'/includes/mobile_converter.php');
	}
}


if ($template_sect == 'yes'){
include (AWK_PLUGIN_PATH.'/includes/font_family.php');
	if ($my_calendar_head == 'yes'){
include (AWK_PLUGIN_PATH.'/includes/calendar_head.php');
	}
	if ($my_calendar_widget == 'yes'){
include (AWK_PLUGIN_PATH.'/includes/calendar_widget.php');
	}
}

if ($locale_sect == 'yes'){
	if ($my_calendar == 'yes'){
include (AWK_PLUGIN_PATH.'/includes/my_calendar.php');
	}
}
