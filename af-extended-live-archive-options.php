<?php
/*
// +----------------------------------------------------------------------+
// | Licenses and copyright acknowledgements are located at               |
// | http://www.sonsofskadi.net/wp-content/elalicenses.txt                |
// +----------------------------------------------------------------------+
*/

$af_ela_cache_root = dirname(__FILE__) . '/cache/';

function af_ela_info($show='') {
    global $ela_plugin_pathname;
	switch($show) {
	case 'currentversion' :
		$plugins= get_plugins();
    	$info = $plugins[$ela_plugin_pathname]['Version'];
        break;
    case 'localeversion' :
    	$info = '9918';
    	break;
    case 'born_on' :
    	$info = 'June 22, 2006'; //This is my birthday. ^-^
    	break;
    case 'homeurl' :
    	$info = 'http://extended-live-archive.googlecode.com';
    	break;
	case 'homename' :
    	$info = 'GoogleCode:Extended-Live-Archive';
    	break;
	case 'supporturl' :
    	$info = 'http://code.google.com/p/extended-live-archive/issues/list';
    	break;
	case 'supportname' :
    	$info = 'Google code issue.';
    	break; 
    case 'remoteversion':
    	$info = 'http://sexywp.com/contact';
     	break;
     default:
     	$info = '';
     	break;   
     }
    return $info;
}

function af_ela_option_init($reset = false) {
	global $af_ela_cache_root;
	if (!$reset) $settings = get_option('af_ela_options');	
    $is_initialized = get_option('af_ela_is_initialized');

	if (!$is_initialized
			|| empty($settings)
			|| strstr(trim(af_ela_info('currentversion')), trim($is_initialized)) === false
			|| strstr(trim($settings['installed_version']), trim($is_initialized)) === false
			|| $reset) {
		$cache = new af_ela_classCacheFile('');
		$cache->deleteFile();
		$initSettings = array(
	// These options is not accessible through the admin panel
		'id' => 'af-ela',
		'installed_version' => af_ela_info('currentversion'),
	// we always set the character set from the blog settings
		'charset' => get_bloginfo('charset'),
		'newest_first' => '1',
		'num_entries' => 0,
		'num_entries_tagged' => 0,
		'num_comments' => 0,
		'fade' => 0,		
		'hide_pingbacks_and_trackbacks' => 0,	
		'use_default_style' => 1,
		'paged_posts' => 0,

		'selected_text' => '',
		'selected_class' => 'selected',
		'comment_text' => '(%)',
		'number_text' => '(%)',
		'number_text_tagged' => '(%)',
		'closed_comment_text' => '',
		'day_format' => '',
		'error_class' => 'alert',

	// allow truncating of titles
		'truncate_title_length' => '0',
		'truncate_cat_length' => '25',
		'truncate_title_text' => '&#8230;',
		'truncate_title_at_space' => 1,
		'abbreviated_month' => 0,
		'tag_soup_cut' => 0,
		'tag_soup_X' => 0,
		
	// paged posts related stuff
		'paged_post_num' => 10,
		'paged_post_next' => 'next posts >>',
		'paged_post_prev' => '<< previous posts',
		
		
	// default text for the tab buttons
		'menu_order' => __('chrono,cats', 'ela'),
		'menu_month' =>__('By date', 'ela'),
		'menu_cat' => __('By category','ela'),
		'menu_tag' => __('By tags', 'ela'),
		'before_child' => '&nbsp;&nbsp;&nbsp;',
		'after_child' => '',
		'loading_content' => __('...loading', 'ela'),
		'idle_content' => '',
		'excluded_categories' => '0');
		
		if (!empty($settings)) {
			$newSettings = array_merge($initSettings, $settings);
		} else {
			$newSettings =$initSettings;
		}
		$newSettings['last_modified'] = gmdate("D, d M Y H:i:s",time());
		$newSettings['installed_version'] = af_ela_info('currentversion');
		
		update_option('af_ela_options', $newSettings, 'Set of Options for Extended Live Archive');
		update_option('af_ela_option_mode', (get_option('af_ela_options') ? 1:0), 'ELA option mode');
		
		$res = true;
		if( !is_dir($af_ela_cache_root) ) {
            $res = af_ela_create_cache_dir(); /* TODO this function is not defined */
			if( !$res ) {
				?>
		<div class="updated"><p><strong>
            <?php _e('Unable to create cache directory. Check your server credentials on the wp-content directory.','ela');?>
        </strong></p></div>
	<?php		return;
			} else {
				if( $res === true ) {
					$res = af_ela_create_cache($settings);
					if( $res === true ) {?>
		<div class="updated"><p><strong>
            <?php _e('The cache files have been created for the first time. You should be up and running. Enjoy.','ela');?>
        </strong></p></div>
	<?php		 	} else {?>
		<div class="updated"><p><strong>
            <?php _e('Unable to create the cache files. Check your server credentials on the wp-content/af-extended-live-archive directory.','ela');?>
        </strong></p></div>
	<?php 			return;
					}
				}
			}
		} else {
			if( af_ela_create_cache($settings) ) {
				if (!$reset) {?>
		<div class="updated"><p><strong>
            <?php _e('The cache files have been updated. You should be up and running. Enjoy.','ela');?>
        </strong></p></div>
	<?php		}
			} else {?>
		<div class="updated"><p><strong>
            <?php _e('Unable to update the cache files to the newer version of the plugin. Check your server credentials on the wp-content/af-extended-live-archive directory.','ela');?>
        </strong></p></div>
	<?php 	return;
			}
		}
		update_option('af_ela_is_initialized', af_ela_info('currentversion'), 'ELA plugin has already been initialized');
	}
}

function af_ela_option_update() {
	global $wpdb;
	$settings = get_option('af_ela_options');
	$settings['last_modified'] = gmdate("D, d M Y H:i:s",time());
	
	$settings['newest_first'] = isset($_POST['newest_first']) ? 1 : 0;
	$settings['num_entries']  = isset($_POST['num_entries']) ? 1 : 0;
	$settings['num_entries_tagged'] = isset($_POST['num_entries_tagged']) ? 1 : 0;
	$settings['num_comments'] = isset($_POST['num_comments']) ? 1 : 0;
	$settings['fade']         = isset($_POST['fade']) ? 1 : 0;
	$settings['hide_pingbacks_and_trackbacks'] = isset($_POST['hide_pingbacks_and_trackbacks']) ? 1 : 0 ;
	$settings['use_default_style'] = isset($_POST['use_default_style']) ? 1 : 0 ;
	$settings['paged_posts'] = isset($_POST['paged_posts']) ? 1 : 0 ;


	if( isset($_POST['selected_text']) )  $settings['selected_text']  = urldecode($_POST['selected_text']);
	if( isset($_POST['selected_class']) ) $settings['selected_class'] = $_POST['selected_class'];
	if( isset($_POST['comment_text']) )   $settings['comment_text']   = urldecode($_POST['comment_text']);
	if( isset($_POST['number_text']) )    $settings['number_text']    = urldecode($_POST['number_text']);
	if( isset($_POST['number_text_tagged']) )  $settings['number_text_tagged']  = urldecode($_POST['number_text_tagged']);
	if( isset($_POST['closed_comment_text']) ) $settings['closed_comment_text'] = urldecode($_POST['closed_comment_text']);
	if( isset($_POST['day_format']) )     $settings['day_format']     = $_POST['day_format'];
	if( isset($_POST['error_class']) )    $settings['error_class']    = $_POST['error_class'];

	// allow truncating of titles
	if( isset($_POST['truncate_title_length']) ) $settings['truncate_title_length'] = urldecode($_POST['truncate_title_length']);
	if( isset($_POST['truncate_cat_length']) )   $settings['truncate_cat_length']   = urldecode($_POST['truncate_cat_length']);
	if( isset($_POST['truncate_title_text']) )   $settings['truncate_title_text']   = urldecode($_POST['truncate_title_text']);
	$settings['truncate_title_at_space'] 		 = isset($_POST['truncate_title_at_space']) ? 1 : 0;
	$settings['abbreviated_month'] 				 = isset($_POST['abbreviated_month']) ? 1 : 0;
	if( isset($_POST['tag_soup_cut']) )			 $settings['tag_soup_cut']   		= urldecode($_POST['tag_soup_cut']);
	if( isset($_POST['tag_soup_X']) )			 $settings['tag_soup_X']   			= urldecode($_POST['tag_soup_X']);
		
	// paged posts related stuff
	if( isset($_POST['paged_post_num']) )	$settings['paged_post_num']   = urldecode($_POST['paged_post_num']);
	if( isset($_POST['paged_post_next']) )	$settings['paged_post_next']   = urldecode($_POST['paged_post_next']);
	if( isset($_POST['paged_post_prev']) )	$settings['paged_post_prev']   = urldecode($_POST['paged_post_prev']);
	
	// default text for the tab buttons
	if( isset($_POST['menu_order']) ) {
		$comma ='';
		$settings['menu_order']='';
		foreach($_POST['menu_order'] as $menu_item) {
			$settings['menu_order'].= $comma . $menu_item;
			$comma = ',';
		}
	}
	if( isset($_POST['menu_month']) )       $settings['menu_month']       = urldecode($_POST['menu_month']);
	if( isset($_POST['menu_cat']) )         $settings['menu_cat']         = urldecode($_POST['menu_cat']);	
	if( isset($_POST['menu_tag']) )         $settings['menu_tag']         = urldecode($_POST['menu_tag']);	
	if( isset($_POST['before_child']) )     $settings['before_child']     = urldecode($_POST['before_child']);
	if( isset($_POST['after_child']) )      $settings['after_child']      = $_POST['after_child'];
	if( isset($_POST['loading_content']) )  $settings['loading_content']  = urldecode($_POST['loading_content']);	
	if( isset($_POST['idle_content']) )     $settings['idle_content']     = urldecode($_POST['idle_content']);
		
	$current_mode = get_option('af_ela_option_mode');
	$asides_cats = $wpdb->get_results("SELECT t.term_id AS `cat_ID`, t.name AS `cat_name`
                      FROM $wpdb->terms AS t
                      INNER JOIN {$wpdb->term_taxonomy} AS tt
                            ON (t.term_id = tt.term_id)
                      WHERE tt.taxonomy = 'category'");
	$comma ='';
	if (!isset($_POST['excluded_categories'])) {?>
	<div class="updated"><p><strong>
        <?php _e('What\'s the point of not showing up any categories at all ?','ela');?>
    </strong></p></div> <?php
	} else {
        //var_dump($current_mode,$_POST['excluded_categories']);
        $current_mode = 1;
		if ($current_mode == 0) {
			$settings['excluded_categories'] = $_POST['excluded_categories'][0];
		} else {
			$settings['excluded_categories'] = '';
			foreach ($asides_cats as $cat) {
				if(!in_array($cat->cat_ID, $_POST['excluded_categories'])) {
					$settings['excluded_categories'] .= $comma ;
					$settings['excluded_categories'] .= $cat->cat_ID;
					$comma = ',';
				}
			}
		}
	}
	
	$settings['last_modified'] = gmdate("D, d M Y H:i:s",time());
	
	update_option('af_ela_options', $settings, 'Set of Options for Extended Live Archive',1);
	
	$cache = new af_ela_classCacheFile('');
	$cache->deleteFile();
	
}

function af_ela_admin_page() {
	af_ela_option_init();
	if (isset($_POST['ela_submit_option'])) {
		if (isset($_POST['ela_clear_cache'])) {
			$cache = new af_ela_classCacheFile('');
			$reset_return= $cache->deleteFile();
			if ($reset_return) {
				echo '<div class="updated"><p><strong>', __('Cache emptied','ela'), '</strong></p></div>';
			} else {
				echo '<div class="updated"><p><strong>', __('Cache was already empty','ela'), '</strong></p></div>';
			}
		} elseif (isset($_POST['switch_option_mode'])) {
		 	$current_mode = get_option('af_ela_option_mode');
			if ($current_mode == 0) {
				$next_mode = 1;
				$option_mode_text = 'Switch to Advanced Options Mode';
			} else {
				$next_mode = 0;
				$option_mode_text = 'Switch to Basic Options Mode';
			}			
			update_option('af_ela_option_mode', $next_mode,'',1);
		} elseif (isset($_POST['reset_option'])) {
			af_ela_option_init(true);
		} else {		
			af_ela_option_update();
			echo '<div class="updated"><p>', __('Extended Live Archive Options have been updated','ela'), '</p></div>';
		}
	}
	$current_mode = get_option('af_ela_option_mode');
	if ($current_mode == 0) {
		$option_mode_text = __('Show Advanced Options Panel','ela');
		$advancedState = 'none';
		$basicState = 'table-row';
	} else {
		$option_mode_text = __('Hide Advanced Options Panel','ela');
		$advancedState = 'block';
		$basicState = 'none';
	}
	$settings = get_option('af_ela_options');

	af_ela_echo_scripts();

?>
<div class="wrap">
	<h2>ELA Options</h2>
    <?php better_ela_infomation();?>
    <form action="" method="post">
    <?php
        better_ela_what_to_show_section($settings);
        better_ela_how_to_show_section($settings);
        better_ela_how_to_cut_section($settings);
        better_ela_what_about_menu_section($settings);
    ?>
        <hr style="clear: both; border: none;" />
    <?php af_ela_echo_fieldset_info($option_mode_text,$advancedState);?>
    
		<input type="hidden" name="ela_submit_option" value="1" />
        <hr style="clear: both; border: none;" />
    <?php

        better_ela_what_categories_to_show_section($settings);
        better_ela_what_about_paged_posts_section($settings);
    ?>
        <hr style="clear: both; border: none;" />
		<p class="submit">
			<input type="submit" name="update_generic" value="<?php _e('Update Options Now','ela');?>" class="button-primary" />
		</p>
	</form>
    <hr style="clear: both; border: none;" />
    <h2><?php _e('ELA Cache Management','ela');?></h2>
    <form method="post">
        <input type="hidden" name="ela_submit_option" value="1" />
        <p><?php _e('You need to clear the cache so that it gets re-built whenever you are making changes related to a category without editing or creating a post (like renaming, creating, deleting a category for instance','ela');?></p>
        <p class="submit">
            <input type="submit" name="ela_clear_cache" value="<?php _e('Empty Cache Now','ela') ?>" class="button-primary"/>
        </p>
    </form>
</div>
<?php
}

function af_ela_echo_scripts() {
	global $utw_is_present;
?>	<script language="javascript" type="text/javascript">
//<![CDATA[
	function disableTabs(first, disabler) {
		var maxtab = 3;
		var i;
		if (document.getElementById('menu_order_tab' + disabler).value == 'none') {
			for(i = first; i < maxtab; i++) {
				document.getElementById('menu_order_tab' + i).value = 'none';
				document.getElementById('menu_order_tab' + i).disabled = true;
			}
		} else {
			document.getElementById('menu_order_tab' + first).disabled = false;
		}
	}
	function disableDOM(ID, disabler) {
		var i;
		if (document.getElementById(disabler).checked == true) {
			document.getElementById(ID).disabled = false;
		} else {
			document.getElementById(ID).disabled = true;
		}
	}
	function disableDOMinv(ID, disabler) {
		if (document.getElementById(disabler).checked == true) {
			document.getElementById(ID).disabled= true;
		} else {
			document.getElementById(ID).disabled = false;
		}
	}
	function hideDOM(ID, disabler) {
		if (document.getElementById(disabler).checked == true) {
			document.getElementById(ID).style.display = "block";
		} else {
			document.getElementById(ID).style.display = "none";
		}
	}
	function selectAllCategories(list) {
		var i;
		var temp = new Array();
		temp = list.split(',');
		for(i = 0; i < temp.length-1; i++) {
			document.getElementById("category-"+temp[i]).checked=true;
		}
	}
	function unselectAllCategories(list) {
		var i;
		var temp = new Array();
		temp = list.split(',');
		for(i = 0; i < temp.length-1; i++) {
			document.getElementById("category-"+temp[i]).checked=false;
		}
	}
	
	function initUnavailableOptions(){
		disableDOM('number_text', 'num_entries');
		<?php if($utw_is_present) { ?>disableDOM('number_text_tagged', 'num_entries_tagged');<?php }?>;
		disableDOM('comment_text', 'num_comments');
		disableDOM('closed_comment_text', 'num_comments');
		disableDOM('hide_pingbacks_and_trackbacks', 'num_comments');
		//hideDOM('fieldsetpagedposts', 'paged_posts');
		<?php if($utw_is_present) { ?>disableDOMinv('tag_soup_X', 'tag_soup_cut0');<?php }?>;
		disableTabs(1, 0);
	}
	
	addLoadEvent(initUnavailableOptions);
//]]>
	</script>
<?php
}

function af_ela_echo_fieldset_info($option_mode_text,$advancedState) {
?>
    <fieldset class="options" style="float: left; width: 25%;">
        <legend><?php _e('Extended Live Archive info','ela');?> </legend>

		<div class="submit" style="text-align:center; ">
		<form method="post"><br />
		<input type="hidden" name="ela_submit_option" value="1" />
        <input type="submit" name="switch_option_mode" value="<?php _e($option_mode_text) ?>" />
        </form></div>
		
		<div class="submit" style="text-align:center;display: <?php echo $advancedState; ?> ">
		<form method="post"><br />
		<input type="hidden" name="ela_submit_option" value="1" />
        <input type="submit" name="reset_option" value="<?php echo "Reset options to default" ?>" />
        </form></div>
		<tr valign="top" >

		</tr>
			
			
		</fieldset><?php
}

// <editor-fold defaultstate="collapsed" desc="Print the ELA infomation section.">
function better_ela_infomation(){
?>
    <div style="width:100%;clear:both;overflow:hidden">
        <div style="width:38%;float:left;text-align:center;">
            <p>Thanks for using Better Extended Live Archive! You can show your appreciation and support future development by donating!</p>
            <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
            <input type="hidden" name="cmd" value="_s-xclick">
            <input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----">
            <input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-but21.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
            <img alt="" border="0" src="https://www.paypal.com/zh_XC/i/scr/pixel.gif" width="1" height="1">
            </form>
        </div>
        <div style="width:55%;float:right;padding:10px;border:1px solid #DDD;">
            <table border="0" style="width:100%">
                <tbody>
                    <tr>
                        <td style="text-align:right;font-weight:900">Homepage:</td>
                        <td style="padding-left:10px"><a href="http://sexywp.com/archives">Extended Live Archive</a></td>
                    </tr>
                    <tr>
                        <td style="text-align:right;font-weight:900">Forum:</td>
                        <td style="padding-left:10px"><a href="http://sexywp.com/forum/forum.php?id=8">Bugs and any other questions about ELA.</a></td>
                    </tr>
                    <tr>
                        <td style="text-align:right;font-weight:900">Source Code:</td>
                        <td style="padding-left:10px"><a href="http://github.com/charlestang/Better-Extended-Live-Archive">Git Hub Homepage</a></td>
                    </tr>
                    <tr>
                        <td style="text-align:right;font-weight:900">Blogroll Me:</td>
                        <td style="padding-left:10px">Sexy WP&lt;<a href="http://sexywp.com">http://SexyWP.com</a>&gt; :)</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div style="clear:both"></div>
<?php
}
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="What to show section.">
function better_ela_what_to_show_section($settings){
    ?>
    <h3 class="title"><?php _e('What to show?');?></h3>
    <p><?php _e('Control the output infomation of ELA.');?></p>
    <table class="form-table"><tbody>
    <?php
        better_ela_helper_chkbox(
            __('Show Newest First:','ela'),
            'newest_first',$settings['newest_first'],
            __('Enabling this will show the newest post first in the listings.','ela'));
        better_ela_helper_chkbox(
            __('Show Number of Entries:','ela'),
            "num_entries", $settings['num_entries'],
            __('Sets whether the number of entries for each year, month, category should be shown.','ela'));
        better_ela_helper_chkbox(
            __('Show Number of Entries Per Tag:','ela'),
            "num_entries_tagged", $settings['num_entries_tagged'],
            __('Sets whether the number of entries for each tags should be shown','ela'));
        better_ela_helper_chkbox(
            __('Show Number of Comments:','ela'),
            "num_comments", $settings["num_comments"],
            __('Sets whether the number of comments for each entry should be shown','ela'));
        better_ela_helper_chkbox(
            __('Fade Anything Technique:','ela'),
            'fade',
            $settings['fade'],
            __('Sets whether changes should fade using the Fade Anything ','ela'));
        better_ela_helper_chkbox(
            __('Hide Ping- and Trackbacks:','ela'),
            'hide_pingbacks_and_trackbacks', $settings['hide_pingbacks_and_trackbacks'],
            __('Sets whether ping- and trackbacks should influence the number of comments on an entry','ela'));
        better_ela_helper_chkbox(
            __('Use the default CSS stylesheet:','ela'),
            'use_default_style', $settings['use_default_style'],
            __('If it exists, will link the <strong>ela.css</strong> stylesheet of your theme. If not present, will link the default stylesheet.','ela'));
        better_ela_helper_chkbox(
            __('Layout the posts link into pages:','ela'),
            'paged_posts', $settings['paged_posts'],
            __('Sets whether the posts list will be cut into several pages or just the complete list.','ela'));
    ?>
    </tbody></table>
    <?php
}
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="How to show section.">
function better_ela_how_to_show_section($settings){
    ?>
    <h3 class="title"><?php _e('How to show?');?></h3>
    <p><?php _e('Control the output text tips of ELA.');?></p>
    <table class="form-table"><tbody>
    <?php
    better_ela_helper_txtbox(
        __('Selected Text:','ela'),
        'selected_text', $settings['selected_text'],
        __('The text that is shown after the currently selected year, month or category.','ela'));
    better_ela_helper_txtbox(
        __('Selected Class:','ela'),
        'selected_class', $settings['selected_class'],
        __('The CSS class for the currently selected year, month or category.','ela'));
    better_ela_helper_txtbox(
        __('# of Entries Text:','ela'),
        'number_text', $settings['number_text'],
        __('The string to show for number of entries per year, month or category. Can contain HTML. % is replaced with number of entries.','ela'),
        true);
    better_ela_helper_txtbox(
        __('# of Tagged-Entries Text:','ela'),
        'number_text_tagged', $settings['number_text_tagged'],
        __('The string to show for number of entries per tag. Can contain HTML. % is replaced with number of entries.','ela'),
        true);
    better_ela_helper_txtbox(
        __('# of Comments Text:','ela'),
        'comment_text', $settings['comment_text'],
        __('The string to show for comments. Can contain HTML. % is replaced with number of comments.','ela'),
        true);
    better_ela_helper_txtbox(
        __('Closed Comment Text:','ela'),
        'closed_comment_text', $settings['closed_comment_text'],
        __('The string to show if comments are closed on an entry. Can contain HTML.','ela'),
        true);
    better_ela_helper_txtbox(
        __('Day of Posting Format:','ela'),
        'day_format', $settings['day_format'],
        __('A date format string to show the day for each entry in the chronological tab only (\'jS\' to show 1st, 3rd, and 14th). Format string is in the <a href="http://www.php.net/date">php date format</a>. Reference to year and month in there will result in error : this intended for days only. Leave empty to show no date.','ela'));
    better_ela_helper_txtbox(
        __('Error Class:','ela'),
        'error_class', $settings['error_class'],
        __('The CSS class to put on paragraphs containing errors.','ela'));
    ?>
    </tbody></table>
    <?php
}
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="How to cut section.">
function better_ela_how_to_cut_section($settings){
    ?>
    <h3 class="title"><?php _e('How to cut?');?></h3>
    <p><?php _e('Control the cut off of ELA.');?></p>
    <table class="form-table"><tbody>
    <?php
    better_ela_helper_txtbox(
        __('Max Entry Title Length:','ela'),
        'truncate_title_length', $settings['truncate_title_length'],
        __('Length at which to truncate title of entries. Set to <strong>0</strong> to leave the titles not truncated.','ela'));
    better_ela_helper_txtbox(
        __('Max Cat. Title Length:','ela'),
        'truncate_cat_length', $settings['truncate_cat_length'],
        __('Length at which to truncate name of categories. Set to <strong>0</strong> to leave the category names not truncated','ela'));
    better_ela_helper_txtbox(
        __('Truncated Text:','ela'),
        'truncate_title_text', $settings['truncate_title_text'],
        __('The text that will be written after the entries titles and the categories names that have been truncated. &#8230; (<strong>&amp;#8230;</strong>) is a common example.','ela'));
    better_ela_helper_chkbox(
        __('Truncate at space:','ela'),
        'truncate_title_at_space', $settings['truncate_title_at_space'],
        __('Sets whether at title should be truncated at the last space before the length to be truncated to, or if words should be truncated mid-senten...','ela'));
    better_ela_helper_chkbox(
        __('Abbreviate month names:','ela'),
        'abbreviated_month', $settings['abbreviated_month'],
        __('Sets whether the month names will be abbreviated to three letters.','ela'));
    ?>
    <tr>
    <th scope="row"><?php _e('Displayed tags:','ela');?></th>
    <td>
        <fieldset><legend class="screen-reader-text"><span><?php _e('Displayed tags:','ela');?></span></legend>
        <label title="tag_soup_cut0"><input type="radio" value="0" name="tag_soup_cut" id="tag_soup_cut0" <?php checked('0', $settings['tag_soup_cut']); ?> /> <?php _e('Show all tags.','ela');?></label><br>
        <label title="tag_soup_cut1"><input type="radio" value="1" name="tag_soup_cut" id="tag_soup_cut1" <?php checked('1', $settings['tag_soup_cut']); ?> /> <?php _e('Show the first <strong>X</strong> most-used tags.','ela');?></label><br>
        <label title="tag_soup_cut2"><input type="radio" value="2" name="tag_soup_cut" id="tag_soup_cut2" <?php checked('2', $settings['tag_soup_cut']); ?> /> <?php _e('Show tags with more than <strong>X</strong> posts.','ela');?></label><br>
        </fieldset>
    </td>
    </tr>
    <?php
    better_ela_helper_txtbox(
        __('The X in the selected above description:','ela'),
        'tag_soup_X', $settings['tag_soup_X'],
        __('Sets depending on the selection made above the number of post per tag needed to display the tag or the number of most-used tags to display.','ela'));
    ?>
    </tbody></table>
    <?php
}
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="What about the menu section.">
function better_ela_what_about_menu_section($settings) {
    ?>
    <h3 class="title"><?php _e('What about the menu?');?></h3>
    <p><?php _e('Customize the menu of ELA.');?></p>
    <table class="form-table"><tbody>
    <?php
    if (!empty($settings['menu_order'])) {
        $menu_table = preg_split('/[\s,]+/',$settings['menu_order']);
    }
    ?>
    <tr valign="top">
    <th scope="row"><label for="menu_order_tab0"><?php _e('Tab Order:','ela');?></label></th>
    <td>
    <?php for($i = 0; $i < 3; $i ++) :?>
    <select id="menu_order_tab<?php echo $i;?>" name="menu_order[]">
        <option value="none" <?php selected('none', $menu_table[$i])?>><?php _e('None','ela');?></option>
        <option value="chrono" <?php selected('chrono', $menu_table[$i])?>><?php _e('By date','ela');?></option>
        <option value="cats" <?php selected('cats', $menu_table[$i])?>><?php _e('By category','ela');?></option>
        <option value="tags" <?php selected('tags', $menu_table[$i])?>><?php _e('By tag','ela');?></option></select>
    <?php endfor;?>
        <br/><?php _e('The order of the tab to display.','ela');?>
    </td>
    </tr>
    <?php
    better_ela_helper_txtbox(
        __('Chronological Tab Text:','ela'),
        'menu_month', $settings['menu_month'],
        __('The text written in the chronological tab.','ela'),
        true);
    better_ela_helper_txtbox(
        __('By Category Tab Text:','ela'),
        'menu_cat', $settings['menu_cat'],
        __('The text written in the categories tab.','ela'),
        true);
    better_ela_helper_txtbox(
        __('By Tag Tab Text:','ela'),
        'menu_tag', $settings['menu_tag'],
        __('The text written in the tags tab.','ela'),
        true);
    better_ela_helper_txtbox(
        __('Before Child Text:','ela'),
        'before_child', $settings['before_child'],
        __('The text written before each category which is a child of another. This is recursive.','ela'),
        true);
    better_ela_helper_txtbox(
        __('After Child Text:','ela'),
        'after_child', $settings['after_child'],
        __('The text that after each category which is a child of another. This is recursive.','ela'),
        true);
    better_ela_helper_txtbox(
        __('Loading Content:','ela'),
        'loading_content', $settings['loading_content'],
        __('The text displayed when the data are being fetched from the server (basically when stuff is loading). Can contain HTML.','ela'),
        true);
    better_ela_helper_txtbox(
        __('Idle Content:','ela'),
        'idle_content', $settings['idle_content'],
        __('The text displayed when no data are being fetched from the server (basically when stuff is not loading). Can contain HTML.','ela'),
        true);
    ?>
    </tbody></table>
    <?php
}
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="What to categories to show section.">
function better_ela_what_categories_to_show_section($settings) {
    ?>
    <h3 class="title"><?php _e('What categories to show?');?></h3>
    <?php //var_dump($settings['excluded_categories']); ?>
    <p><?php _e('Check the categories you want to show in the category tab.');?></p>
    <table class="form-table"><tbody>
        <tr valign="top">
            <th scope="row"><?php _e('Select categories:','ela');?></th>
            <td><fieldset><legend class="screen-reader-text">
                <span><?php _e('Select categories:','ela');?></span></legend>
    <?php
			global $wpdb;
			$asides_table = array();
			$asides_table = explode(',', $settings['excluded_categories']);
            $query = "SELECT t.term_id AS `cat_ID`, t.name AS `cat_name`
                      FROM $wpdb->terms AS t
                      INNER JOIN {$wpdb->term_taxonomy} AS tt
                            ON (t.term_id = tt.term_id)
                      WHERE tt.taxonomy = 'category'
            ";
			$asides_cats = $wpdb->get_results($query);
			$asides_content = '';
			$asides_select = '';
			foreach ($asides_cats as $cat) {
				$checked = in_array($cat->cat_ID, $asides_table) ? '' : 'checked="checked"';
				$asides_select .= $cat->cat_ID.',';
				$asides_content .= '<label for="category-'.$cat->cat_ID.'">';
                $asides_content .= '<input value="'.$cat->cat_ID.'" type="checkbox" name="excluded_categories[]" id="category-'.$cat->cat_ID.'" '. $checked  .'/>';
                $asides_content .= $cat->cat_name.'</label><br/>';
		   	}
			echo $asides_content;
    ?>
            </fieldset></td>
        </tr>
        <tr valign="top">
            <th scope="row">&nbsp;</th>
            <td>
                <input type="button" onclick="javascript:selectAllCategories('<?php echo $asides_select;?>')" value="<?php _e('Select All Categories') ?>" />
                <input type="button" onclick="javascript:unselectAllCategories('<?php echo $asides_select;?>')" value="<?php _e('Unselect All Categories') ?>" />
            </td>
        </tr>
    </tbody></table>
    <?php
}
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="What about paged posts section.">
function better_ela_what_about_paged_posts_section($settings) {
    ?>
    <h3 class="title"><?php _e('What about paged posts?.','ela');?></h3>
    <p><?php _e('The layout of the posts when using a paged list instead of complete list .','ela');?></p>
    <table class="form-table"><tbody>
    <?php
    better_ela_helper_txtbox(
        __('Max # of Posts per page:','ela'),
        'paged_post_num', $settings['paged_post_num'],
        __('The max number of posts that will be listed per page.','ela'),
        true);
    better_ela_helper_txtbox(
        __('Next Page of Posts:','ela'),
        'paged_post_next', $settings['paged_post_next'],
        __('The text written as the link to the next page.','ela'),
        true);
    better_ela_helper_txtbox(
        __('Previous Page of Posts:','ela'),
        'paged_post_prev', $settings['paged_post_prev'],
        __('The text written as the link to the previous page.','ela'),
        true);
    ?>
    </tbody></table>
    <?php
}
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="HTML form helper functions.">
function better_ela_helper_chkbox($caption, $id, $default, $description){
?>
    <tr valign="top">
    <th scope="row"><?php echo $caption;?></th>
    <td><fieldset><legend class="screen-reader-text">
        <span><?php echo $caption;?></span></legend><label for="<?php echo $id;?>">
        <input type="checkbox" value="<?php echo $default;?>" id="<?php echo $id;?>" name="<?php echo $id;?>" <?php checked('1', $default);?>>
    <?php echo $description;?></label>
    </fieldset></td>
    </tr>
<?php
}

function better_ela_helper_txtbox($caption, $id, $default, $description, $html = false){
    if ($html) {
        $default = htmlspecialchars(stripslashes($default));
    }
?>
<tr valign="top">
    <th scope="row"><label for="<?php echo $id;?>"><?php echo $caption;?></label></th>
    <td>
        <input type="text" class="regular-text" style="width:12.5em;" value="<?php echo $default;?>" id="<?php echo $id;?>" name="<?php echo $id;?>">
        <span class="description"><?php echo $description;?></span></td>
</tr>
<?php
}
// </editor-fold>
