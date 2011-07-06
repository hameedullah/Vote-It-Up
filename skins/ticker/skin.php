<?php
/* VoteItUp skin file 
Name: Ticker
Version: 2

*/

global $skinname, $support_images, $support_sinks;
$skinname = 'Ticker'; //No effect yet, but its good to state

function SkinInfo($item) {
switch ($item) {
case 'name':
return 'Ticker';
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
 ?>
<?php if (function_exists('VoteItUp_options')) { ?>
<span class="tickercontainer" id="votecount<?php the_ID(); ?>"><?php echo $votes; ?></span>
<?php if ($user_ID != '') { ?>
<?php
 if (!($user_login == get_the_author_login() && !get_option('voteiu_allowownvote'))) { ?>
	<span>
	<?php if(!UserVoted($postID,$user_ID)) { ?><span class="tickertext">
			<span class="votelink" id="voteid<?php the_ID(); ?>"><a href="javascript:vote('votecount<?php the_ID(); ?>','voteid<?php the_ID(); ?>','<?php echo get_option('voteiu_aftervotetext'); ?>',<?php the_ID(); ?>,<?php echo $user_ID; ?>,'<?php echo VoteItUp_ExtPath(); ?>');"><?php echo get_option('voteiu_votetext'); ?></a><?php if (get_option('voteiu_sinktext') != '') { ?><a href="javascript:sink('votecount<?php the_ID(); ?>','voteid<?php the_ID(); ?>','<?php echo get_option('voteiu_aftervotetext'); ?>',<?php the_ID(); ?>,<?php echo $user_ID; ?>,'<?php echo VoteItUp_ExtPath(); ?>');"><?php echo get_option('voteiu_sinktext'); ?></a></span>
			<?php } ?>

		</span>
	<?php } else { ?>
	<?php if (get_option('voteiu_aftervotetext') != '') { ?><span class="tickertext" id="voteid<?php the_ID(); ?>"><span class="votelink"><?php echo get_option('voteiu_aftervotetext'); ?></span></span><?php } ?>
	<?php } ?>
	</span>
<?php } else { ?>
	<?php if (get_option('voteiu_aftervotetext') != '') { ?><span class="tickertext" id="voteid<?php the_ID(); ?>"><span class="votelink"><?php echo get_option('voteiu_aftervotetext'); ?></span></span><?php } ?>
<?php } } else {
if (get_option('voteiu_allowguests') == 'true') { ?>
	<span id="voteid<?php the_ID(); ?>">
	<?php if(!GuestVoted($postID,md5($_SERVER['REMOTE_ADDR']))) { ?><span class="tickertext">
			<span class="votelink" id="voteid<?php the_ID(); ?>"><a href="javascript:vote('votecount<?php the_ID(); ?>','voteid<?php the_ID(); ?>','<?php echo get_option('voteiu_aftervotetext'); ?>',<?php the_ID(); ?>,0,'<?php echo VoteItUp_ExtPath(); ?>');"><?php echo get_option('voteiu_votetext'); ?></a><?php if (get_option('voteiu_sinktext') != '') { ?><a href="javascript:sink('votecount<?php the_ID(); ?>','voteid<?php the_ID(); ?>','<?php echo get_option('voteiu_aftervotetext'); ?>',<?php the_ID(); ?>,0,'<?php echo VoteItUp_ExtPath(); ?>');"><?php echo get_option('voteiu_sinktext'); ?></a></span><?php } ?>
			</span>
	<?php } else { ?>
		<?php if (get_option('voteiu_aftervotetext') != '') { ?><span class="tickertext" id="voteid<?php the_ID(); ?>"><span class="votelink"><?php echo get_option('voteiu_aftervotetext'); ?></span></span><?php } ?>
		<?php 
	} } else { 
/*Guest voting disabled*/
?>
<span class="tickertext">
			<span class="votelink" id="voteid<?php the_ID(); ?>"><a href="javascript:regboxopen();"><?php echo get_option('voteiu_votetext'); ?></a><?php if (get_option('voteiu_sinktext') != '') { ?><a href="javascript:regboxopen();"><?php echo get_option('voteiu_sinktext'); ?></a></span><?php } ?>


	</span>
<?php } ?>
	<?php
} } }

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