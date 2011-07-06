<?php
/*
This script is designed to be run from wordpress. It will work if placed in the root directory of a wordpress install.
Revision: 4

-Added Widget
-Fixed Guest Voting
-Modified to use get_options
-Fixed Widget, now excludes deleted posts

*/

//Run this to create an entry for a post in the voting system. Will check if the post exists. If it doesn't, it will create an entry.
function SetPost($post_ID) {
	global $wpdb;
	
	//prevents SQL injection
	$p_ID = $wpdb->escape($post_ID);

	//Check if entry exists
	$id_raw = $wpdb->get_var("SELECT ID FROM ".$wpdb->prefix."votes WHERE post='".$p_ID."'");
	if ($id_raw != '') {
		//entry exists, do nothing
	} else {
		//entry does not exist
		$wpdb->query("INSERT INTO ".$wpdb->prefix."votes (post, votes, guests, usersinks, guestsinks) VALUES(".$p_ID.", '', '', '', '') ") or die(mysql_error());
	}
}

//Run this to create an entry for a user in the voting system. Will check if the user exists. If it doesn't, it will create an entry.
function SetUser($user_ID) {
	global $wpdb;
	
	//prevents SQL injection
	$u_ID = $wpdb->escape($user_ID);

	//Check if entry exists
	$id_raw = $wpdb->get_var("SELECT ID FROM ".$wpdb->prefix."votes_users WHERE user='".$u_ID."'");
	if ($id_raw != '') {
		//entry exists, do nothing
	} else {
		//entry does not exist
		$wpdb->query("INSERT INTO ".$wpdb->prefix."votes_users (user, votes, sinks) VALUES(".$u_ID.", '', '') ") or die(mysql_error());
	}
}

//Returns the vote count
function GetVotes($post_ID, $percent = false) {
	global $wpdb;
	
	//prevents SQL injection
	$p_ID = $wpdb->escape($post_ID);

	//Create entries if not existant
	SetPost($p_ID);

	//Gets the votes
	$votes_raw = $wpdb->get_var("SELECT votes FROM  ".$wpdb->prefix."votes WHERE post='".$p_ID."'");
	$sinks_raw = $wpdb->get_var("SELECT usersinks FROM  ".$wpdb->prefix."votes WHERE post='".$p_ID."'");
	$guestvotes_raw = $wpdb->get_var("SELECT guests FROM  ".$wpdb->prefix."votes WHERE post='".$p_ID."'");
	$guestsinks_raw = $wpdb->get_var("SELECT guestsinks FROM  ".$wpdb->prefix."votes WHERE post='".$p_ID."'");
/* Deprecated
	$uservotes_raw = $wpdb->get_var("SELECT votes FROM ".$wpdb->prefix."votes_users WHERE user='".$u_ID."'");
	$usersinks_raw = $wpdb->get_var("SELECT sinks FROM ".$wpdb->prefix."votes_users WHERE user='".$u_ID."'");
*/

	//Put it in array form
	$votes = explode(",", $votes_raw);
	$sinks = explode(",", $sinks_raw);
	$guestvotes = explode(",", $guestvotes_raw);
	$guestsinks = explode(",", $guestsinks_raw);
/* Deprecated
	$uservotes = explode(",", $uservotes_raw);
	$usersinks = explode(",", $usersinks_raw);
*/
	$uservotes = 0;
	$usersinks = 0;

	$initial = 0; //Initial no. of votes [will be placed at -1 when all posts receive votes]
	
	

	//The mathematics
	if ($percent == true) {
		// make $votecount into a percent
		$totalcount = count($votes) + count($sinks) + count($guestvotes) + count($guestsinks) + count($uservotes) + count($usersinks) + get_option('voteiu_initialoffset') - 6;
		// the -6 is because count('') returns 1, so if there is no votes at all, 1 is returned.  -6 offsets that
		$forcount = count($votes) + count($guestvotes) + count($uservotes) + get_option('voteiu_initialoffset') - 3;
		$againstcount = count($sinks) + count($guestsinks) + count($usersinks) - 3;
		if ($totalcount > 0) {
			$votecount = number_format(100*($forcount / $totalcount), 0) . "%";
		} else {
			return false;
		}
		return $votecount;
		// uncomment this line below if you want to test
		//return count($votes) . " " . count($sinks) . " " . count($guestvotes) . " " . count($guestsinks) . " " . count($uservotes) . " " . count($usersinks) . " " . get_option('voteiu_initialoffset') . " " . $p_ID;
	} else {
		// wihtout percent mode, $votecount is number of total positive votes (votes minus sinks)
		$votecount = count($votes) - count($sinks) + count($guestvotes) - count($guestsinks) + count($uservotes) - count($usersinks) + $initial;
		return $votecount + get_option('voteiu_initialoffset');
	}
}

function GetPostVotes($post_ID) {
	global $wpdb;
	
	//prevents SQL injection
	$p_ID = $wpdb->escape($post_ID);

	//Create entries if not existant
	SetPost($p_ID);

	//Gets the votes
	$votes_raw = $wpdb->get_var("SELECT votes FROM  ".$wpdb->prefix."votes WHERE post='".$p_ID."'");

	//Put it in array form
	$votes = explode(",", $votes_raw);
	return $votes + get_option('voteiu_initialoffset');
}

function GetPostSinks($post_ID) {
	global $wpdb;
	
	//prevents SQL injection
	$p_ID = $wpdb->escape($post_ID);

	//Create entries if not existant
	SetPost($p_ID);

	//Gets the votes
	$sinks_raw = $wpdb->get_var("SELECT usersinks FROM  ".$wpdb->prefix."votes WHERE post='".$p_ID."'");

	//Put it in array form
	$sinks = explode(",", $sinks_raw);
	return $sinks + get_option('voteiu_initialoffset');
}

//Returns a series of information
function GetBarVotes($post_ID) {

	//Some minor configuration
	$max_displayed_votes = 40;
	$vote_threshold = 30;

	$votes = GetVotes($post_ID);
	$votemax = $max_displayed_votes;
	$votebreak =  30; //votes at which bar changes color
	$bar[0] = 0; //The length of the bar
	$bar[1] = 0; //The state of the bar
	if ($votes > $votemax && $votes > -1) {
		$bar[0] = $votemax;
	} else {
		if ($votes > -1) {
			$bar[0] = $votes;
		} else {
			$bar[0] = 0;
		}
	}
	if ($votes > $votebreak) {
		$bar[1] = 1;
	}
	return $bar;
}

//Checks if the user voted
function UserVoted($post_ID, $user_ID) {
	global $wpdb;
	
	//prevents SQL injection
	$p_ID = $wpdb->escape($post_ID);
	$u_ID = $wpdb->escape($user_ID);

	//Create entry if not existant
	SetPost($p_ID);

	//Gets the votes
	$votes_raw = $wpdb->get_var("SELECT votes FROM ".$wpdb->prefix."votes WHERE post='".$p_ID."'");
	$sinks_raw = $wpdb->get_var("SELECT usersinks FROM ".$wpdb->prefix."votes WHERE post='".$p_ID."'");

	//Put it in array form
	$votes = explode(",", $votes_raw);
	$sinks = explode(",", $sinks_raw);
	
	$voted = FALSE;
	$votekey = array_search($u_ID, $votes);
	$sinkkey = array_search($u_ID, $sinks);
	if ($votekey != '' | $sinkkey != '') {
		$voted = TRUE;
	}
	return $voted;
}

//Checks if the visitor voted
function GuestVoted($post_ID, $user_IPhash) {
	global $wpdb;
	
	//prevents SQL injection
	$p_ID = $wpdb->escape($post_ID);
	$u_ID = $wpdb->escape($user_IPhash);

	//Create entry if not existant
	SetPost($p_ID);

	//Gets the votes
	$votes_raw = $wpdb->get_var("SELECT guests FROM ".$wpdb->prefix."votes WHERE post='".$p_ID."'");
	$sinks_raw = $wpdb->get_var("SELECT guestsinks FROM ".$wpdb->prefix."votes WHERE post='".$p_ID."'");

	//Put it in array form
	$votes = explode(",", $votes_raw);
	$sinks = explode(",", $sinks_raw);
	
	$voted = FALSE;
	$votekey = array_search($u_ID, $votes);
	$sinkkey = array_search($u_ID, $sinks);
	if ($votekey != '' | $sinkkey != '') {
		$voted = TRUE;
	}
	return $voted;
}

//Checks the key to see if it is valid
function CheckKey($key, $id) {
	global $wpdb;
	$userdata = $wpdb->get_results("SELECT display_name, user_email, user_url, user_registered FROM $wpdb->users WHERE ID = '".$id."'", ARRAY_N);
	$chhash = md5($userdata[0][0].$userdata[0][3]);
	if ($chhash == $key) {
		return TRUE;
	} else {
		return FALSE;
	}
}

//Saves the vote of a user to the database
function Vote($post_ID, $user_ID, $type) {
	global $wpdb;
	
	//Prevents SQL injection
	$p_ID = $wpdb->escape($post_ID);
	$u_ID = $wpdb->escape($user_ID);

	//Create entries if not existant
	SetPost($p_ID);
	SetUser($u_ID);

	//Gets the votes
	$votes_raw = $wpdb->get_var("SELECT votes FROM  ".$wpdb->prefix."votes WHERE post='".$p_ID."'");
	$sinks_raw = $wpdb->get_var("SELECT usersinks FROM  ".$wpdb->prefix."votes WHERE post='".$p_ID."'");
	$uservotes_raw = $wpdb->get_var("SELECT votes FROM ".$wpdb->prefix."votes_users WHERE user='".$u_ID."'");
	$usersinks_raw = $wpdb->get_var("SELECT sinks FROM ".$wpdb->prefix."votes_users WHERE user='".$u_ID."'");
	
	//Gets the votes in array form
	$votes = explode(",", $votes_raw);
	$sinks = explode(",", $sinks_raw);
	$uservotes = explode(",", $uservotes_raw);
	$usersinks = explode(",", $usersinks_raw);

	//Check if user voted
	if (!UserVoted($post_ID,$user_ID)) {
	//user hasn't vote, so the script allows the user to vote

		if ($type != 'sink') {
			//Add vote to array
			$user_var[0] = $u_ID;
			$post_var[0] = $p_ID;
			$votes_result = array_merge($votes,$user_var);
			$votes_result_raw = implode(",",$votes_result);
			$uservotes_result = array_merge($uservotes,$post_var);
			$uservotes_result_raw = implode(",",$uservotes_result);
			$sinks_result_raw = $sinks_raw;
			$usersinks_result_raw = $usersinks_raw;
		} else {
			//Add sink to array
			$user_var[0] = $u_ID;
			$post_var[0] = $p_ID;
			$sinks_result = array_merge($sinks,$user_var);
			$sinks_result_raw = implode(",",$sinks_result);
			$usersinks_result = array_merge($usersinks,$post_var);
			$usersinks_result_raw = implode(",",$usersinks_result);
			$votes_result_raw = $votes_raw;
			$uservotes_result_raw = $votesinks_raw;
		}
		
		//Prevents SQL injection
		$votes_result_sql = $wpdb->escape($votes_result_raw);
		$sinks_result_sql = $wpdb->escape($sinks_result_raw);
		$uservotes_result_sql = $wpdb->escape($uservotes_result_raw);
		$usersinks_result_sql = $wpdb->escape($usersinks_result_raw);
		
		//Update votes
		$wpdb->query("UPDATE ".$wpdb->prefix."votes SET votes='".$votes_result_sql."' WHERE post='".$p_ID."'");
		$wpdb->query("UPDATE ".$wpdb->prefix."votes SET usersinks='".$sinks_result_sql."' WHERE post='".$p_ID."'");
		$wpdb->query("UPDATE ".$wpdb->prefix."votes_users SET votes='".$uservotes_result_sql."' WHERE user='".$u_ID."'");
		$wpdb->query("UPDATE ".$wpdb->prefix."votes_users SET sinks='".$usersinks_result_sql."' WHERE user='".$u_ID."'");
		
		$result = 'true';
	} else {
		//The user voted, thus the script will not update the votes in the article
		$result = 'false';
	}

	return $result; //returns '' on failure, returns 'true' if votes were casted, returns 'false' if user already casted a vote
}

//Saves the vote of a guest to the database
function GuestVote($post_ID, $type) {
	global $wpdb;
	
	//Guest's vote is stored permanently. May implement votes that will expire.
	//Use user IP
	$iphash = md5($_SERVER['REMOTE_ADDR']);

	//Set a cookie if there isn't any. This is to reduce the problem of users using proxies and voting multiple times on the same stories.
/*
	if(isset($_COOKIE['tevinevotes'])) {
		$iphash = $_COOKIE['tevinevotes']; 
	} else {
		$cookielife = 14 * 60 * 24 * 60 + time(); //Set to expire in a year from now
		setcookie('tevinevotes', $iphash, $cookielife);
	}
*/	

	//Prevents SQL injection
	$u_ID = $wpdb->escape($iphash);
	$p_ID = $wpdb->escape($post_ID);

	//Create entries if not existant
	SetPost($p_ID);

	//Gets the info
	$votes_raw = $wpdb->get_var("SELECT guests FROM ".$wpdb->prefix."votes WHERE post='".$p_ID."'");
	$sinks_raw = $wpdb->get_var("SELECT guestsinks FROM ".$wpdb->prefix."votes WHERE post='".$p_ID."'");
	
	//Gets the votes in array form
	$votes = explode(",", $votes_raw);
	$sinks = explode(",", $sinks_raw);

	//Check if user voted
	if (!GuestVoted($post_ID,$user_ID)) {
	//user hasn't vote, so the script allows the user to vote

		if ($type != 'sink') {
			//Add vote to array
			$user_var[0] = $u_ID;
			$post_var[0] = $p_ID;
			$votes_result = array_merge($votes,$user_var);
			$votes_result_raw = implode(",",$votes_result);
			$sinks_result_raw = $sinks_raw;
		} else {
			//Add sink to array
			$user_var[0] = $u_ID;
			$post_var[0] = $p_ID;
			$sinks_result = array_merge($sinks,$user_var);
			$sinks_result_raw = implode(",",$sinks_result);
			$votes_result_raw = $votes_raw;
		}
		
		//Prevents SQL injection
		$votes_result_sql = $wpdb->escape($votes_result_raw);
		$sinks_result_sql = $wpdb->escape($sinks_result_raw);
		
		//Update votes
		$wpdb->query("UPDATE ".$wpdb->prefix."votes SET guests='".$votes_result_sql."' WHERE post='".$p_ID."'");
		$wpdb->query("UPDATE ".$wpdb->prefix."votes SET guestsinks='".$sinks_result_sql."' WHERE post='".$p_ID."'");
		
		$result = 'true';
	} else {
		//The user voted, thus the script will not update the votes in the article
		$result = 'false';
	}

	return $result; //returns '' on failure, returns 'true' if votes were casted, returns 'false' if user already casted a vote
}

//Gets an array of posts with vote count
//Identical to SortVotes() except that it is sorted by ID in descending order, and has more information on votes
function GetVoteArray() {
	global $wpdb;

	$limit = get_option('voteiu_limit');

	//Get posts logged, and get the latest posts voted [new posts have larger IDs
	$posts = $wpdb->get_results("SELECT post FROM ".$wpdb->prefix."votes WHERE ID != '' ORDER BY ID DESC");

	$postarray = array();
	$votesarray = array();
	$uservotes_a = array();
	$usersinks_a = array();
	$guestvotes_a = array();
	$guestsinks_a = array();

	/*
	Limit, so as to reduce time taken for this script to run. If you want to raise the limit to maximum, use
	$limit = count($posts);
	*/
	if ($limit == '') {
	$limit = 100;
	}
	$limit = 999;

	//Ignore limit if post count is below limit
	if ($limit > count($posts)) {
		$limit = count($posts);
	}


	//foreach ($posts as &$post) { 
	//Support PHP4 by not using foreach
	for ($counter = 0; $counter < $limit; $counter += 1) {
		$post = $posts[$counter];
		$p_ID = $post->post;

		//Gets the votes
		$votes_raw = $wpdb->get_var("SELECT votes FROM  ".$wpdb->prefix."votes WHERE post='".$p_ID."'");
		$sinks_raw = $wpdb->get_var("SELECT usersinks FROM  ".$wpdb->prefix."votes WHERE post='".$p_ID."'");
		$guestvotes_raw = $wpdb->get_var("SELECT guests FROM  ".$wpdb->prefix."votes WHERE post='".$p_ID."'");
		$guestsinks_raw = $wpdb->get_var("SELECT guestsinks FROM  ".$wpdb->prefix."votes WHERE post='".$p_ID."'");

		//Put it in array form
		$votes = explode(",", $votes_raw);
		$sinks = explode(",", $sinks_raw);
		$guestvotes = explode(",", $guestvotes_raw);
		$guestsinks = explode(",", $guestsinks_raw);

		$initial = get_option('voteiu_initialoffset');

		//The mathematics
		$votecount = count($votes) - count($sinks) + count($guestvotes) - count($guestsinks) + $initial;
		array_push($postarray, array($p_ID));
		array_push($votesarray, array($votecount));
		array_push($uservotes_a, array(count($votes)-1)); //Apparently there is one extra item in the array
		array_push($usersinks_a, array(count($sinks)-1));
		array_push($guestvotes_a, array(count($guestvotes)-1));
		array_push($guestsinks_a, array(count($guestsinks)-1));
	}
	$output = array($postarray, $votesarray, $uservotes_a, $usersinks_a, $guestvotes_a, $guestsinks_a);
	return $output;

}

//Used in options page
function DisplayPostList() {
	$a = GetVoteArray();
	$i = 0;

//Begin table
?>
<table class="widefat post fixed" id="formtable" style="clear: both;" cellspacing="0">
	<thead>
	<tr>

	<th scope="col" id="cb" class="manage-column column-cb check-column" style=""><input type="checkbox" name="multiselect[]" onclick="javascript:CheckUncheck()" /></th>
	<th scope="col" id="title" class="manage-column column-title" style="">Post</th>
<?php /*?>	<th scope="col" id="author" class="manage-column column-author" style="">Author</th><?php */ ?>
	<th scope="col" id="votes" class="manage-column column-categories" style="width: 40%">Votes</th>


	</tr>
	</thead>

	<tfoot>
	<tr>
	<th scope="col"  class="manage-column column-cb check-column" style=""><input type="checkbox" name="multiselect[]" onclick="javascript:CheckUncheck()" /></th>
	<th scope="col"  class="manage-column column-title" style="">Post</th>
<?php /* ?>	<th scope="col"  class="manage-column column-author" style="">Author</th><?php */ ?>

	<th scope="col"  class="manage-column column-categories" style="">Votes</th>

	</tr>
	</tfoot>
	<tbody>
<?php


	while ($i < count($a[0])) {
	$postdat = get_post($a[0][$i][0]);
	if (!empty($postdat)) {
?>
	<tr id='post-<?php echo $a[0][$i][0]; ?>' class='alternate author-other status-publish iedit' valign="top">
		<th scope="row" class="check-column"><input type="checkbox" name="post[]" value="<?php echo $a[0][$i][0]; ?>" /></th>
<td class="post-title column-title"><strong><a class="row-title" href="<?php echo $postdat->guid; ?>" title="<?php echo $postdat->post_title; ?>"><?php echo $postdat->post_title; ?></a></strong></td>
<?php /* ?><td class="author column-author"><?php echo $postdat->post_author; ?></td><?php */ ?>
<td class="categories column-categories"><?php echo $a[1][$i][0]; ?> (Users: <span style="color:#00CC00">+<?php echo $a[2][$i][0]; ?></span>/<span style="color:#CC0000">-<?php echo $a[3][$i][0]; ?></span>, Guests: <span style="color:#00CC00">+<?php echo $a[4][$i][0]; ?></span>/<span style="color:#CC0000">-<?php echo $a[5][$i][0]; ?></span><?php
if(get_option('voteiu_initialoffset') != '0') {
echo ', Offset: ';
if (get_option('voteiu_initialoffset') > 0) {
echo '<span style="color:#00CC00">+'.get_option('voteiu_initialoffset').'</span>';
} else {
echo '<span style="color:#CC0000">'.get_option('voteiu_initialoffset').'</span>';
} } ?>)</td>
<?php

/*
	echo $postdat->post_title;
	echo ' - ';
	echo $a[1][$i][0];
	echo '<br />';
*/

?>
</tr>
<?php
	}
	$i++;
	}


//End table
?> 
	</tbody>
	</table>
<?php
}

//Handles the deleting of votes, used to read the POST when the page is submitted
function VoteBulkEdit() {
//error_reporting(E_ALL);
/*print_r($_POST);*/
$buttonnumber = 0; //Determines which apply button was clicked on. 0 if no button was clicked.
$action = 'none'; //Determines what should be done
if (array_key_exists('doaction1', $_POST)) {
$buttonnumber = 1;
}
if (array_key_exists('doaction2', $_POST)) {
$buttonnumber = 2;
}
if ($buttonnumber != 0 && array_key_exists('action'.$buttonnumber, $_POST)) {
	if ($_POST['action'.$buttonnumber] != -1) {
		//Assigns action to be done
		$action = $_POST['action'.$buttonnumber];
	}
}

if (!array_key_exists('post', $_POST)) {
$action = 'none'; //set action to none if there are no posts to modify
}

//Begin modifying votes
if ($action != 'none' && $action != '') {
ResetVote($_POST['post'], $action);
}

}

function ResetVote($postids, $action) {
/*
Testing stuff...
echo 'Posts: '.implode(', ',$_POST['post']);
echo '<br />';
echo "Action: ".$action;
*/
global $wpdb;
//$wpdb->show_errors();

switch ($action) {

case 'none':
//do nothing
break;
case 'delete':
//reset all votes for the post
$i = 0;
while ($i < count($postids)) {
$wpdb->query("UPDATE ".$wpdb->prefix."votes SET votes = '',guests = '',usersinks = '', guestsinks = '' WHERE `post`=".$postids[$i]." LIMIT 1 ;");
$i++;
}
EditVoteSuccess();
break;
case 'deleteuser':
//reset all votes for users
$i = 0;
while ($i < count($postids)) {
$wpdb->query("UPDATE ".$wpdb->prefix."votes SET votes = '',usersinks = '' WHERE post=".$postids[$i]." LIMIT 1 ;");
$i++;
}
EditVoteSuccess();
break;
case 'deleteguest':
//reset all votes for guests
$i = 0;
while ($i < count($postids)) {
$wpdb->query("UPDATE ".$wpdb->prefix."votes SET guests = '',guestsinks = '' WHERE post=".$postids[$i]." LIMIT 1 ;");
$i++;
}
EditVoteSuccess();
break;
}





}

function EditVoteSuccess() {
?><div id="message" class="updated fade"><p><strong>Votes edited</strong></p></div><?php
}

//Used to sort votes for widgets
function SortVotes() {
	global $wpdb;

	$limit = get_option('voteiu_limit');

	//Get posts logged, and get the latest posts voted [new posts have larger IDs
	$posts = $wpdb->get_results("SELECT post FROM ".$wpdb->prefix."votes WHERE ID != '' ORDER BY ID DESC");

	$postarray = array();
	$votesarray = array();

	/*
	Limit, so as to reduce time taken for this script to run. If you want to raise the limit to maximum, use
	$limit = count($posts);
	*/
	if ($limit == '') {
	$limit = 100;
	}

	//Ignore limit if post count is below limit
	if ($limit > count($posts)) {
		$limit = count($posts);
	}


	//foreach ($posts as &$post) { 
	//Support PHP4 by not using foreach
	for ($counter = 0; $counter < $limit; $counter += 1) {
		$post = $posts[$counter];
		$p_ID = $post->post;

		//Gets the votes
		$votes_raw = $wpdb->get_var("SELECT votes FROM  ".$wpdb->prefix."votes WHERE post='".$p_ID."'");
		$sinks_raw = $wpdb->get_var("SELECT usersinks FROM  ".$wpdb->prefix."votes WHERE post='".$p_ID."'");
		$guestvotes_raw = $wpdb->get_var("SELECT guests FROM  ".$wpdb->prefix."votes WHERE post='".$p_ID."'");
		$guestsinks_raw = $wpdb->get_var("SELECT guestsinks FROM  ".$wpdb->prefix."votes WHERE post='".$p_ID."'");

		//Put it in array form
		$votes = explode(",", $votes_raw);
		$sinks = explode(",", $sinks_raw);
		$guestvotes = explode(",", $guestvotes_raw);
		$guestsinks = explode(",", $guestsinks_raw);

		$initial = get_option('voteiu_initialoffset');

		//The mathematics
		$votecount = count($votes) - count($sinks) + count($guestvotes) - count($guestsinks) + $initial;
		array_push($postarray, array($p_ID));
		array_push($votesarray, array($votecount));
	}
	array_multisort($votesarray, SORT_DESC, $postarray);
	$output = array($postarray, $votesarray);
	return $output;

}

//Displays the widget
function MostVotedAllTime_Widget() {
	$a = SortVotes();
	//Before

?>
<div class="votewidget">
<div class="title">Most Voted</div>
<?php
	$rows = 0;

//Now does not include deleted posts
$i = 0;
while ($rows < get_option('voteiu_widgetcount')) {
	if ($a[0][$i][0] != '') {
			$postdat = get_post($a[0][$i][0]);
		if (!empty($postdat)) {
			$rows++;

			if (round($rows / 2) == ($rows / 2)) {
				echo '<div class="fore">';
			} else {
				echo '<div class="back">';
			}
			echo '<div class="votecount">'.$a[1][$i][0].' '.Pluralize($a[1][$i][0], 'votes', 'vote').' </div><div><a href="'.$postdat->guid.'" title="'.$postdat->post_title.'">'.$postdat->post_title.'</a></div>';
			echo '</div>';
		}
	}
	if ($i < count($a[0])) {
	$i++;
	} else {
	break; //exit the loop
	}
}

//End
?>

</div>
<?php

}

//Displays the widget optimised for sidebar
function MostVotedAllTime_SidebarWidget() {
	$a = SortVotes();
	//Before

?>
<div class="votewidget">
<?php
	$rows = 0;

//Now does not include deleted posts
$i = 0;
while ($rows < get_option('voteiu_widgetcount')) {
	if ($a[0][$i][0] != '') {
			$postdat = get_post($a[0][$i][0]);
		if (!empty($postdat)) {
			$rows++;
				if (round($rows / 2) == ($rows / 2)) {
					echo '<div class="fore">';
				} else {
					echo '<div class="back">';
				}
				echo '<div class="votecount" style="width: 1em; color: #555555; font-weight: bold;">'.$a[1][$i][0].' </div><div><a href="'.$postdat->guid.'" title="'.$postdat->post_title.'">'.$postdat->post_title.'</a></div>';
				echo '</div>';
		}
	}
	if ($i < count($a[0])) {
	$i++;
	} else {
	break; //exit the loop
	}
}

//End
?>

</div>
<?php

}

//For those particular with English
function Pluralize($number, $plural, $singular) {
	if ($number == 1) {
		return $singular;
	} else {
		return $plural;
	}
}

//Not used yet
function IsExcluded($id) {
	global $excludedid;
	$clean = str_replace("\r", "", $excludedid);
	$excluded = explode("\n", $clean);
}

?>