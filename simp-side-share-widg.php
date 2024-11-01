<?php
/*
Plugin Name: Simple Sidebar Share Widget
Plugin URI: http://wordpress.org/extend/plugins/simple-sidebar-share-widget/
Description: A simple sidebar widget which adds a number of different social media site share buttons.
Author: Mike Jenkins
Version: 1.2
Author URI: http://www.twitter.com/mike_jenkins
*/

require('sites.php');

function SimpSideSharWidg($mode, $site_list, $bitly_user, $bitly_api)
{	
	global $sites;
  	require('bitly.php');
	require('url_func.php');
	$url =  curPageUrl();
	if ($bitly_user <> '' && $bitly_api <> '') { 
		$bitly = new Bitly($bitly_user, $bitly_api);
		$short = $bitly->shorten($url);
	} else {
		$short = $url; 
	}
    ob_start();
    bloginfo('name');
	wp_title();
    $title = ob_get_contents();
    ob_end_clean();
	foreach ($sites as $id => $site) {
		if (in_array($id, $site_list) || in_array('-1', $site_list)) {
		$site['share-link'] = str_replace(array('<url>', '<short>', '<title>'),array($url, $short, $title),$site['share-link']);
?>
		<a href="<?php echo $site['share-link']; ?>">
			<?php 
			if ($mode==2 || $mode==0) { 
				echo $site['name'];
			}
			if ($mode==1 || $mode==0) {
				if ($site['icon']) {
					$left = 0;
					$top = 0;
					if ($site['icon'] % 16 <> 0) { $left = ($site['icon'] % 16)*16; }
					$top = $site['icon'] - ($site['icon'] % 16);
				}
?>			
				<span class="sssw-sitelogo" style="display: block; float:left; width: 16px; height: 16px; background: transparent url('/wp-content/plugins/simple-sidebar-share-widget/bookmarks.png') -<?php echo $left; ?>px -<?php echo $top; ?>px no-repeat" ></span>
<?php
			}
			?>
		</a>
<?php
		}
	}
?>
<?php
}
function widget_SimpSideSharWidg($args) {
	extract($args);
	$options = get_option("widget_SimpSideSharWidg");
	if (!is_array( $options )) {
		$options = array(
			'title' => '',
			'mode' => '0',
			'sites' => 'a:1:{i:0;s:2:"-1";}',
			'bitly_user' => '',
			'bitly_api' => ''
		);
	}    
	echo $before_widget;
	echo $before_title;
	echo $options['title'];
	echo $after_title;
?>
<div id="simplesharwidg">
<?php
	SimpSideSharWidg($options['mode'], unserialize($options['sites']), $options['bitly_user'], $options['bitly_api']);
?>
</div>
	<div style="clear:both; height:1px; width: 1px;"></div>
<?php
	echo $after_widget;
}
function SimpSideSharWidg_control()
{
	global $sites;
	$options = get_option("widget_SimpSideSharWidg");
	if (!is_array( $options )) {
		$options = array(
		'title' => 'Share',
		'mode' => 0,
		'sites' => 'a:1:{i:0;s:2:"-1";}',
		'bitly_user' => '',
		'bitly_api' => ''
		);
	}
	if ($_POST['SimpSideSharWidg-Submit'])
	{
		$options['title'] = strip_tags(stripslashes($_POST['SimpSideSharWidg-WidgetTitle']));
		$options['mode'] = strip_tags(stripslashes($_POST['SimpSideSharWidg-WidgetMode']))+0;
		$options['sites'] = serialize($_POST['SimpSideSharWidg-WidgetSites']);
		$options['bitly_user'] = strip_tags(stripslashes($_POST['SimpSideSharWidg-WidgetBitlyUser']));
		$options['bitly_api'] = strip_tags(stripslashes($_POST['SimpSideSharWidg-WidgetBitlyAPI']));
		update_option("widget_SimpSideSharWidg", $options);
	}
	$options['sites'] = unserialize($options['sites']);
?>

  <p>
    <label for="SimpSideSharWidg-WidgetTitle" style="float: left; width: 200px; margin-bottom: 5px">Widget Title:</label>
    <input type="text" id="SimpSideSharWidg-WidgetTitle" name="SimpSideSharWidg-WidgetTitle" value="<?php echo $options['title'];?>" style="float: left; width: 200px; margin-bottom: 10px" />
    <label for="SimpSideSharWidg-WidgetMode" style="float: left; width: 200px; margin-bottom: 5px">Display Mode:</label>
	<select name="SimpSideSharWidg-WidgetMode" id="SimpSideSharWidg-WidgetMode" size="1" style="float: left; width: 200px; ; margin-bottom: 10px">
		<option value="0"<?php echo $options['mode']==0?" selected":'' ?>>Icon and text</option>
		<option value="1"<?php echo $options['mode']==1?" selected":'' ?>>Icon only</option>
		<option value="2"<?php echo $options['mode']==2?" selected":'' ?>>Text only</option>
	</select>
    <label for="SimpSideSharWidg-WidgetSites" style="float: left; width: 200px; margin-bottom: 5px">Display Sites:</label>
	<select name="SimpSideSharWidg-WidgetSites[]" id="SimpSideSharWidg-WidgetSites[]" class="SimpSideSharWidg-multiselect" multiple="multiple" style="float: left; height: 100px; width: 200px; ; margin-bottom: 10px">
		<option value="-1" <?php if (in_array('-1', $options['sites'])) { echo 'selected="selected"'; } ?>>All Sites</option>
	<?php
	asort($sites);
	foreach ($sites as $id => $site) {
	?>
		<option value="<?php echo $id; ?>" <?php if (in_array($id, $options['sites'])) { echo 'selected="selected"'; } ?>><?php echo $site['name']; ?></option>
	<?php
		}
	?>
	</select>
    <label for="SimpSideSharWidg-WidgetBitlyUser" style="float: left; width: 200px; margin-bottom: 5px">Bit.ly Username</label>
    <input type="text" id="SimpSideSharWidg-WidgetBitlyUser" name="SimpSideSharWidg-WidgetBitlyUser" value="<?php echo $options['bitly_user'];?>" style="float: left; width: 200px; margin-bottom: 10px" />
    <label for="SimpSideSharWidg-WidgetBitlyAPI" style="float: left; width: 200px; margin-bottom: 5px">Bit.ly API</label>
    <input type="text" id="SimpSideSharWidg-WidgetBitlyAPI" name="SimpSideSharWidg-WidgetBitlyAPI" value="<?php echo $options['bitly_api'];?>" style="float: left; width: 200px; margin-bottom: 10px" />
    <input type="hidden" id="SimpSideSharWidg-Submit" name="SimpSideSharWidg-Submit" value="1" />
  </p>
<?php
}
function SimpSideSharWidg_init()
{
  register_sidebar_widget(__('Simple Sidebar Share'), 'widget_SimpSideSharWidg');
  register_widget_control(   'Simple Sidebar Share', 'SimpSideSharWidg_control');
}
add_action("plugins_loaded", "SimpSideSharWidg_init");
?>