<?php 
/* VoteItUp skin file 
Name: Bar
Version: 2

*/

global $skinname, $support_images, $support_sinks;
$skinname = 'Bar'; //No effect yet, but its good to state

function SkinInfo($item) {
switch ($item) {
case 'name':
return 'Bar';
break;
case 'supporttwoway':
return true;
break;
default:
return false;
break;
}
}

function LoadVote() {
global $user_ID, $user_login;
$postID = get_the_ID();
$votes = GetVotes($postID);
$barvotes = GetBarVotes($postID);
 ?>
<?php if (function_exists('VoteItUp_options')) { ?>
<span class="barcontainer"><span class="barfill" id="votecount<?php echo $postID ?>" style="width:<?php echo round($barvotes[0] * 2.5); ?>%;">&nbsp;</span></span>
<?php if ($user_ID != '') { 
 if (!($user_login == get_the_author_login() && !get_option('voteiu_allowownvote'))) { ?>
	<?php if(!UserVoted($postID,$user_ID)) { ?><span class="bartext" id="voteid<?php the_ID(); ?>">
			<a href="javascript:vote('votecount<?php the_ID(); ?>','voteid<?php the_ID(); ?>','<?php echo get_option('voteiu_aftervotetext'); ?>',<?php the_ID(); ?>,<?php echo $user_ID; ?>,'<?php echo VoteItUp_ExtPath(); ?>');"><?php echo get_option('voteiu_votetext'); ?></a><?php if (get_option('voteiu_sinktext') != '') { ?><a href="javascript:sink('votecount<?php the_ID(); ?>','voteid<?php the_ID(); ?>','<?php echo get_option('voteiu_aftervotetext'); ?>',<?php the_ID(); ?>,<?php echo $user_ID; ?>,'<?php echo VoteItUp_ExtPath(); ?>');"><?php echo get_option('voteiu_sinktext'); ?></a><?php } ?>

		</span>
	<?php } else { ?>
		<?php if (get_option('voteiu_aftervotetext') != '') { ?><span class="bartext" id="voteid<?php the_ID(); ?>"><?php echo get_option('voteiu_aftervotetext'); ?></span><?php } ?>
	<?php } ?>
<?php } else { ?>
		<?php if (get_option('voteiu_aftervotetext') != '') { ?><span class="bartext" id="voteid<?php the_ID(); ?>"><?php echo get_option('voteiu_aftervotetext'); ?></span><?php } ?>
<?php } } else {
if (get_option('voteiu_allowguests') == 'true') { ?>
	<?php if(!GuestVoted($postID,md5($_SERVER['REMOTE_ADDR']))) { ?><span class="bartext" id="voteid<?php the_ID(); ?>">
			<a href="javascript:vote('votecount<?php the_ID(); ?>','voteid<?php the_ID(); ?>','<?php echo get_option('voteiu_aftervotetext'); ?>',<?php the_ID(); ?>,0,'<?php echo VoteItUp_ExtPath(); ?>');"><?php echo get_option('voteiu_votetext'); ?></a><?php if (get_option('voteiu_sinktext') != '') { ?><a href="javascript:sink('votecount<?php the_ID(); ?>','voteid<?php the_ID(); ?>','<?php echo get_option('voteiu_aftervotetext'); ?>',<?php the_ID(); ?>,0,'<?php echo VoteItUp_ExtPath(); ?>');"><?php echo get_option('voteiu_sinktext'); ?></a><?php } ?>

		</span>
	<?php } else { ?>
	<?php if (get_option('voteiu_aftervotetext') != '') { ?><span class="bartext" id="voteid<?php the_ID(); ?>"><?php echo get_option('voteiu_aftervotetext'); ?></span><?php } ?>
	<?php } ?>
	<?php } else { 
/*Guest voting disabled*/

?>
	<span class="bartext" id="voteid<?php the_ID(); ?>">
			<a href="javascript:regboxopen();"><?php echo get_option('voteiu_votetext'); ?></a><?php if (get_option('voteiu_sinktext') != '') { ?><a href="javascript:regboxopen();"><?php echo get_option('voteiu_sinktext'); ?></a><?php } ?>
	</span>
<?php
} } } }

function LoadVoteWidget() {

	$a = SortVotes();
	//Before
?>
<div class="votewidget_skin">
<?php
	$rows = 0;

//Now does not include deleted posts
$i = 0;
while ($rows < get_option('voteiu_widgetcount')) {
	if ($a[0][$i][0] != '') {
			$postdat = get_post($a[0][$i][0]);
		if (!empty($postdat)) {
			$rows++;
				$postdat = get_post($a[0][$i][0]);
				echo '<div class="votelistind"><div class="votemicro">'.$a[1][$i][0].'</div><span class="votemicrotext"><a href="'.$postdat->guid.'" title="'.$postdat->post_title.'">'.$postdat->post_title.'</a></span></div>';
		}
	}
	if ($i < count($a[0])) {
	$i++;
	} else {
	break; //exit the loop
	}
}
?>
</div>

<?php

}

?>