<?php
include_once("../../../wp-blog-header.php");
include_once("votingfunctions.php");
if ($_GET['pid'] != '') {
	if ($_GET['uid'] != '') {
		if ($_GET['uid'] == 0) {
			//Guest voting
			if ($_GET['type'] != 'sink') {
				GuestVote($_GET['pid'],'vote');
			} else {
				GuestVote($_GET['pid'],'sink');
			}
		} else  {
			//Add vote
			if ($_GET['type'] != 'sink') {
				Vote($_GET['pid'],$_GET['uid'],'vote');
			} else {
				Vote($_GET['pid'],$_GET['uid'],'sink');
			}
		}
	} 
	if ($_GET['tid'] == 'total') {
		echo GetVotes($_GET['pid'], false);
	} else if ($_GET['tid'] == 'percent') {
		//run the math as a percentage not total
		echo GetVotes($_GET['pid'], true);
	} else {
		$barvotes = GetBarVotes($_GET['pid']);
		echo $barvotes[0];
	}
} else {
	echo '0';
}
?>