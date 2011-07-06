<?php
/* VoteItUp skin file 
Name: Orange Ticker
Version: 2

*/

global $skinname, $support_images, $support_sinks;
$skinname = 'Orange Ticker'; //No effect yet, but its good to state


function SkinInfo($item) {
switch ($item) {
case 'name':
return 'Orange Ticker';
break;
case 'supporttwoway':
return false; /* actually supported... but somewhat unstable */
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
if (function_exists('VoteItUp_options')) {
	if ($user_ID != '') { 
		if (!($user_login == get_the_author_login() && !get_option('voteiu_allowownvote'))) {
			/* Post author can vote own post */
			if(!UserVoted($postID,$user_ID)) { 
				/* User has not voted */
			?>
				<div class="post_postvote"><div class="post_votewidget" id="votewidget<?php the_ID(); ?>">
					<div class="post_votecount" id="votecount<?php the_ID(); ?>"><?php echo $votes; ?></div>
				<div class="post_votebuttoncontainer"><span class="post_votebutton" id="voteid<?php the_ID(); ?>"><a href="javascript:vote('votecount<?php the_ID(); ?>','voteid<?php the_ID(); ?>','<?php echo get_option('voteiu_aftervotetext'); ?>',<?php the_ID(); ?>,<?php echo $user_ID; ?>,'<?php echo VoteItUp_ExtPath(); ?>');"><?php echo get_option('voteiu_votetext'); ?></a><?php if (get_option('voteiu_sinktext') != '') { ?><a href="javascript:sink('votecount<?php the_ID(); ?>','voteid<?php the_ID(); ?>','<?php echo get_option('voteiu_aftervotetext'); ?>',<?php the_ID(); ?>,<?php echo $user_ID; ?>,'<?php echo VoteItUp_ExtPath(); ?>');"><?php echo get_option('voteiu_sinktext'); ?></a><?php } ?>
					</span></div>
				</div></div>
			<?php 
			} else {
				/* User has voted */
				if (get_option('voteiu_aftervotetext') != '') {
					/* After-vote text was set */
					?>
					<div class="post_postvote"><div class="post_votewidget" id="votewidget<?php the_ID(); ?>">
						<div class="post_votecount" id="votecount<?php the_ID(); ?>"><?php echo $votes; ?></div>
					<div id="voteid<?php the_ID(); ?>" class="post_votebuttoncontainer"><span class="post_votebutton"><?php echo get_option('voteiu_aftervotetext'); ?></span></div>
					</div></div>
				<?php 
				} else { 
					/* After-vote text not set */
					?>
					<div class="post_postvote"><div class="post_votewidget_closed" id="votewidget<?php the_ID(); ?>">
						<div class="post_votecount" id="votecount<?php the_ID(); ?>"><?php echo $votes; ?></div>
					</div></div>
					<?php 
				} 
			}
		} else {
			/* Post author cannot vote own post */
			if (get_option('voteiu_aftervotetext') != '') {
				/* After-vote text was set */
				?>
				<div class="post_postvote"><div class="post_votewidget" id="votewidget<?php the_ID(); ?>">
					<div class="post_votecount" id="votecount<?php the_ID(); ?>"><?php echo $votes; ?></div>
				<div class="post_votebuttoncontainer"><span class="post_votebutton" id="voteid<?php the_ID(); ?>"><?php echo get_option('voteiu_aftervotetext'); ?></span></div>
				</div></div>
				<?php 
			} else { 
				/* After-vote text not set */
				?>
				<div class="post_postvote"><div class="post_votewidget_closed" id="votewidget<?php the_ID(); ?>">
					<div class="post_votecount" id="votecount<?php the_ID(); ?>"><?php echo $votes; ?></div>
				</div></div>
				<?php 
			}
		}
	} else {
		/* Guest is attempting to vote */
		if (get_option('voteiu_allowguests') == 'true') {
			/* Guest voting allowed */
			if(!GuestVoted($postID,md5($_SERVER['REMOTE_ADDR']))) {
				/* Guest has not voted */
				?>
				<div class="post_postvote"><div class="post_votewidget" id="votewidget<?php the_ID(); ?>">
					<div class="post_votecount" id="votecount<?php the_ID(); ?>"><?php echo $votes; ?></div>
					<div class="post_votebuttoncontainer">
							<span class="post_votebutton" id="voteid<?php the_ID(); ?>"><a href="javascript:vote('votecount<?php the_ID(); ?>','voteid<?php the_ID(); ?>','<?php echo get_option('voteiu_aftervotetext'); ?>',<?php the_ID(); ?>,0,'<?php echo VoteItUp_ExtPath(); ?>');"><?php echo get_option('voteiu_votetext'); ?></a><?php if (get_option('voteiu_sinktext') != '') { ?><a href="javascript:sink('votecount<?php the_ID(); ?>','voteid<?php the_ID(); ?>','<?php echo get_option('voteiu_aftervotetext'); ?>',<?php the_ID(); ?>,0,'<?php echo VoteItUp_ExtPath(); ?>');"><?php echo get_option('voteiu_sinktext'); ?></a><?php } ?>
							</span>
					</div></div></div>
					<?php 
			} else {
				/* Guest has voted */
				if (get_option('voteiu_aftervotetext') != '') {
				/* After vote text was set */
				?><div class="post_postvote"><div class="post_votewidget" id="votewidget<?php the_ID(); ?>">
					<div class="post_votecount" id="votecount<?php the_ID(); ?>"><?php echo $votes; ?></div>
					<div class="post_votebuttoncontainer">
					<span class="post_votebutton" id="voteid<?php the_ID(); ?>"><?php echo get_option('voteiu_aftervotetext'); ?></span>
					</div></div></div>
				<?php 
				} else {
					/* After vote text was not set */
					?>
					<div class="post_postvote"><div class="post_votewidget_closed" id="votewidget<?php the_ID(); ?>">
						<div class="post_votecount" id="votecount<?php the_ID(); ?>"><?php echo $votes; ?></div>
					</div></div>
					<?php 
				}
			}
		} else {
				/* Guest voting disabled */
				?>
				<div class="post_postvote"><div class="post_votewidget" id="votewidget<?php the_ID(); ?>">
					<div class="post_votecount" id="votecount<?php the_ID(); ?>"><?php echo $votes; ?></div>
					<div class="post_votebuttoncontainer">
							<span class="post_votebutton" id="voteid<?php the_ID(); ?>"><a href="javascript:regboxopen();"><?php echo get_option('voteiu_votetext'); ?></a><?php if (get_option('voteiu_sinktext') != '') { ?><a href="javascript:regboxopen();"><?php echo get_option('voteiu_sinktext'); ?></a><?php } ?>
							</span>
					</div></div></div>
				
				
					<?php
				
			
		}
	}
}
}

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
		if ($postdat->post_title != '') {
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