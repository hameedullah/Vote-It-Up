<?php
/*
Plugin Name: Vote It Up
Plugin URI: http://www.onfry.com/projects/voteitup/
Description: Vote It Up enables bloggers to add voting functionality to their posts.
Version: 1.2.2
Author: Nicholas Kwan (multippt)
Author URI: http://www.onfry.com/
*/

/*  Copyright 2007  Nicholas Kwan  (email : ready725@gmail.com)

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
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


//Installs plugin options and database
include_once('voteinstall.php');
VoteItUp_InstallOptions();
VoteItUp_dbinstall();
//register_activation_hook( basename(__FILE__), 'VoteItUp_dbinstall'); //doesn't seem to work at times..., but I'll put it here

//External configuration file
@include_once('compat.php');

//Declare paths used by plugin
function VoteItUp_Path() {
global $voteitupint_path;
if ($voteitupint_path == '') {
	$dir = dirname(__FILE__);
	$dir = str_replace("\\", "/", $dir); //For Linux
	return $dir;
} else {
	return $voteitup_path;
}
}

function VoteItUp_ExtPath() {
global $voteitup_path;
if ($voteitup_path == '') {
	$dir = VoteItUp_Path();
	$base = ABSPATH;
	$base = str_replace("\\", "/", $base);
	$edir = str_replace($base, "", $dir);
	$edir = get_bloginfo('url')."/".$edir;
	$edir = str_replace("\\", "/", $edir);
	return $edir;
} else {
	return $voteitup_path;
}
}

//Includes other functions of the plugin
include_once(VoteItUp_Path()."/votingfunctions.php");
include_once(VoteItUp_Path()."/skin.php");

//Installs configuration page
include("voteconfig.php");

function VoteItUp_header() {
$voteiu_skin = get_option('voteiu_skin');
//If no skin is selected, only include default theme/script to prevent conflicts.
if ($voteiu_skin == '') {
?>
<link rel="stylesheet" href="<?php echo VoteItUp_ExtPath(); ?>/votestyles.css" type="text/css" />
<script type="text/javascript" src="<?php echo VoteItUp_ExtPath(); ?>/voterajax.js"></script>
<?php
} else {
	LoadSkinHeader($voteiu_skin);
}
/* These are things always used by voteitup */
?>
<link rel="stylesheet" href="<?php echo VoteItUp_ExtPath(); ?>/voteitup.css" type="text/css" />
<script type="text/javascript" src="<?php echo VoteItUp_ExtPath(); ?>/userregister.js"></script>
<?php
}

function VoteItUp_footer() {
if (!get_option('voteiu_allowguests')) {
?><div class="regcontainer" id="regbox">
<div class="regcontainerbackground">&nbsp;</div>
<div class="regpopup">
<a href="javascript:regclose();" title="Close"><img class="regclosebutton" src="<?php echo VoteItUp_ExtPath(); ?>/closebutton.png" /></a>
<h3>You need to log in to vote</h3>

<p>The blog owner requires users to be <a href="<?php echo get_option('siteurl').'/wp-login.php'; ?>" title="Log in">logged in</a> to be able to vote for this post.</p>
<p>Alternatively, if you do not have an account yet you can <a href="<?php echo get_option('siteurl').'/wp-login.php?action=register'; ?>" title="Register account">create one here</a>.</p>
<p class="regsmalltext">Powered by <a href="http://www.onfry.com/projects/voteitup/" title="Vote It Up plugin">Vote It Up</a></p></div></div>
<?php
}
}

//Displays the widget, theme supported
function MostVotedAllTime($skinname = '', $mode = '') {
$voteiu_skin = get_option('voteiu_skin');
$tempvar = $voteiu_skin;
if ($skinname != '') {
$tempvar = $skinname;
}
if ($tempvar == '' | $tempvar == 'default') {
if ($mode == 'sidebar') {
MostVotedAllTime_SidebarWidget();
} else {
MostVotedAllTime_Widget(); //Use default bar
}
} else {
if (!LoadSkinWidget($tempvar, $mode)) {
if ($mode == 'sidebar') {
MostVotedAllTime_SidebarWidget();
} else {
MostVotedAllTime_Widget(); //Use default bar
}
}
}

}


//Display the votes as a bar
function DisplayVotes($postID, $type = '') {
global $user_ID, $guest_votes, $vote_text, $use_votetext, $allow_sinks, $voteiu_skin;

$voteiu_skin = get_option('voteiu_skin');
$votes = GetVotes($postID);
$barvotes = GetBarVotes($postID);
switch ($type) {
case '':
if ($voteiu_skin == '') {
DisplayVotes($postID, 'bar'); //Use default bar
} else {
if (!LoadSkin($voteiu_skin)) {
DisplayVotes($postID, 'bar'); //Use default bar
}
}
break;
case 'bar':
?>
<span class="barcontainer"><span class="barfill" id="votecount<?php echo $postID ?>" style="width:<?php echo round($barvotes[0] * 2.5); ?>%;">&nbsp;</span></span>
<?php if ($user_ID != '') { 
 if (!($user_login == get_the_author_login() && !get_option('voteiu_allowownvote'))) { ?>
	<span>
	<?php if(!UserVoted($postID,$user_ID)) { ?><span class="bartext" id="voteid<?php the_ID(); ?>">
			<a href="javascript:vote('votecount<?php the_ID(); ?>','voteid<?php the_ID(); ?>','<?php echo get_option('voteiu_aftervotetext'); ?>',<?php the_ID(); ?>,<?php echo $user_ID; ?>,'<?php echo VoteItUp_ExtPath(); ?>');"><?php echo get_option('voteiu_votetext'); ?></a><?php if (get_option('voteiu_sinktext') != '') { ?><a href="javascript:sink('votecount<?php the_ID(); ?>','voteid<?php the_ID(); ?>','<?php echo get_option('voteiu_aftervotetext'); ?>',<?php the_ID(); ?>,<?php echo $user_ID; ?>,'<?php echo VoteItUp_ExtPath(); ?>');"><?php echo get_option('voteiu_sinktext'); ?></a>
			<?php } ?>

		</span>
	<?php } else { ?>
	<?php if (get_option('voteiu_aftervotetext') != '') { ?><span class="bartext" id="voteid<?php the_ID(); ?>"><?php echo get_option('voteiu_aftervotetext'); ?></span><?php } ?>
	<?php } ?>
	</span>
<?php } } else {
if (get_option('voteiu_allowguests') == 'true') { ?>
	<span>
	<?php if(!GuestVoted($postID,md5($_SERVER['REMOTE_ADDR']))) { ?><span class="bartext" id="voteid<?php the_ID(); ?>">
			<a href="javascript:vote('votecount<?php the_ID(); ?>','voteid<?php the_ID(); ?>','<?php echo get_option('voteiu_aftervotetext'); ?>',<?php the_ID(); ?>,0,'<?php echo VoteItUp_ExtPath(); ?>');"><?php echo get_option('voteiu_votetext'); ?></a><?php if (get_option('voteiu_sinktext') != '') { ?><a href="javascript:sink('votecount<?php the_ID(); ?>','voteid<?php the_ID(); ?>','<?php echo get_option('voteiu_aftervotetext'); ?>',<?php the_ID(); ?>,0,'<?php echo VoteItUp_ExtPath(); ?>');"><?php echo get_option('voteiu_sinktext'); ?></a>
			<?php } ?>

		</span>
	<?php } ?>
	</span>
	<?php } }
break;
case 'ticker':
?>
<span class="tickercontainer" id="votes<?php the_ID(); ?>"><?php echo $votes; ?></span>
<?php if ($user_ID != '') { ?>
<span id="voteid<?php the_ID(); ?>">
	<?php if(!UserVoted($postID,$user_ID)) { ?><span class="tickertext">
		<?php if ($use_votetext == 'true') { ?>
		<a class="votelink" href="javascript:vote_ticker(<?php echo $postID ?>,<?php echo $postID ?>,<?php echo $user_ID; ?>,'<?php echo VoteItUp_ExtPath(); ?>');"><?php echo $vote_text; ?></a>
		<?php } else { ?>
			<span class="imagecontainer">
			<?php if ($allow_sinks == 'true') { ?>
			<a href="javascript:sink_ticker(<?php echo $postID ?>,<?php echo $postID ?>,<?php echo $user_ID; ?>,'<?php echo VoteItUp_ExtPath(); ?>');">
			<img class="votedown" src="<?php echo VoteItUp_ExtPath(); ?>/votedown.png" alt="Vote down" border="0" />
			</a>
			<?php } ?>
			<a href="javascript:vote_ticker(<?php echo $postID ?>,<?php echo $postID ?>,<?php echo $user_ID; ?>,'<?php echo VoteItUp_ExtPath(); ?>');">
			<img class="voteup" src="<?php echo VoteItUp_ExtPath(); ?>/voteup.png" alt="Vote up" border="0" />
			</a>
			</span>
		<?php } ?>
		</span>
	<?php } ?>
</span>
<?php } else {
if ($guest_votes == 'true') { ?>
	<span id="voteid<?php the_ID(); ?>">
	<?php if(!GuestVoted($postID,md5($_SERVER['REMOTE_ADDR']))) { ?>
		<span class="tickertext">
		<?php if ($use_votetext == 'true') { ?>
			<a class="votelink" href="javascript:vote_ticker(<?php echo $postID ?>,<?php echo $postID ?>,0,'<?php echo VoteItUp_ExtPath(); ?>');"><?php echo $vote_text; ?></a></span>
		<?php } else { ?>
			<span class="imagecontainer">
			<?php if ($allow_sinks == 'true') { ?>
			<a href="javascript:sink_ticker(<?php echo $postID ?>,<?php echo $postID ?>,0,'<?php echo VoteItUp_ExtPath(); ?>');">
			<img class="votedown" src="<?php echo VoteItUp_ExtPath(); ?>/votedown.png" alt="Vote down" border="0" />
			</a>
			<?php } ?>
			<a href="javascript:vote_ticker(<?php echo $postID ?>,<?php echo $postID ?>,0,'<?php echo VoteItUp_ExtPath(); ?>');">
			<img class="voteup" src="<?php echo VoteItUp_ExtPath(); ?>/voteup.png" alt="Vote up" border="0" />
			</a>
			</span>
		<?php } ?>
		</span>
	<?php } ?>
</span>

<?php
}
}
break;
}
}

/* Widget examples can be found in widget.php of wp-includes.*/
function widget_MostVotedAllTime_init() {

if (function_exists('register_sidebar_widget')) {
function widget_MostVotedAllTime($args) {
$options = get_option("widget_MostVotedAllTime");
if ($options['title'] != '') {
$title = $options['title'];
} else {
$title = 'Most Voted Posts';
}
    extract($args);
?>
        <?php echo $before_widget; ?>
            <?php echo $before_title
                . $title
                . $after_title; ?>
            <?php MostVotedAllTime('', 'sidebar'); ?>
        <?php echo $after_widget; ?>
<?php
}
register_sidebar_widget('Most Voted Posts', 'widget_MostVotedAllTime');
//$widget_ops = array('classname' => 'widget_MostVotedAllTime', 'description' => __( "Displays the most voted up posts") );
//@wp_register_sidebar_widget('widget_MostVotedAllTime', __('Most Voted Posts'), 'widget_MostVotedAllTime', $widget_ops);

function widget_MostVotedAllTime_Control() {
$options = $newoptions = get_option("widget_MostVotedAllTime");

if ($_POST) 
{
$newoptions['title'] = strip_tags(stripslashes($_POST['widget_MostVotedAllTime_title']));
}
if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('widget_MostVotedAllTime', $options);
}
$title = attribute_escape($options['title']);
?>
<p>
    <label for="widget_MostVotedAllTime_title">Title: </label>
    <input type="text" class="widefat" id="widget_MostVotedAllTime_title" name="widget_MostVotedAllTime_title" value="<?php echo $title; ?>" />
	<input type="hidden" id="voteitup-submit" name="voteitup-submit" value="1" />
  </p>
<?php
}

register_widget_control('Most Voted Posts', 'widget_MostVotedAllTime_Control', 0, 0 );

}
}

//Runs the plugin
add_action('wp_head', 'VoteItUp_header');
add_action('get_footer', 'VoteItUp_footer');
add_action('admin_menu', 'VoteItUp_options');
add_action('init', 'widget_MostVotedAllTime_init');

?>