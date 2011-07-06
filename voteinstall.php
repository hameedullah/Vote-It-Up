<?php
//Installs and update options
$voteitup_dbversion = 2000;

function VoteItUp_InstallOptions() {
global $voteitup_dbversion;

//Default options for install
$voteiu_votetext = 'Vote';
$voteiu_sinktext = '';
$voteiu_aftervotetext = '';
$voteiu_allowguests = true;
$voteiu_allowownvote = true;
$voteiu_limit = 100;
$voteiu_widgetcount = 10;
$voteiu_skin = '';
$voteiu_initialoffset = 0;

//Begins adding options if not available
//3rd parameter is deprecated, but added for compatibility
add_option('voteiu_votetext', $voteiu_votetext, 'The vote text');
add_option('voteiu_sinktext', $voteiu_sinktext, 'The sink text');
add_option('voteiu_aftervotetext', $voteiu_aftervotetext, 'The after vote text');
add_option('voteiu_allowguests', $voteiu_allowguests, 'Allows guests to vote');
add_option('voteiu_allowownvote', $voteiu_allowownvote, 'Allows authors to vote own posts');
add_option('voteiu_limit', $voteiu_limit, 'The number of latest posts to include in the widget');
add_option('voteiu_widgetcount', $voteiu_widgetcount, 'The number of posts shown in the widget');
add_option('voteiu_skin', $voteiu_skin, 'The current theme for vote widget');
add_option('voteiu_dbversion', $voteiu_dbversion, 'Vote It Up db version');
add_option('voteiu_initialoffset', $voteiu_initialoffset, 'Vote offset');

//Change setting to default values if user left these fields blank
if (get_option('voteiu_initialoffset') == '') {
update_option('voteiu_initialoffset', $voteiu_initialoffset);
}
if (get_option('voteiu_limit') == '') {
update_option('voteiu_limit', $voteiu_limit);
}
if (get_option('voteiu_widgetcount') == '10') {
update_option('voteiu_widgetcount', $voteiu_widgetcount);
}

}

//Updates options and remove unused ones
function VoteItUp_UpgradeOptions() {
global $voteiu_dbversion;

$currentdbversion = 0;
if (get_option('voteiu_dbversion')) {
$currentdbversion = get_option('voteiu_dbversion');
}

if ($voteiu_dbversion > $currentdbversion) {

//Update options here

}

}

//Deletes old unused options
function VoteItUp_DeleteOldOptions() {
delete_option('voteiu_allowsinks');
delete_option('voteiu_excluded');
delete_option('voteiu_usevotetext');
}


//Installs DB tables

function VoteItUp_dbinstall() {
	global $wpdb;
	$table_name = $wpdb->prefix.'votes';
	if($wpdb->get_var("SHOW TABLES LIKE '".$wpdb->prefix."votes'") != $table_name) {
	$query = "CREATE TABLE ".$wpdb->prefix."votes_users (
  ID int(11) NOT NULL auto_increment,
  user int(11) NOT NULL,
  votes text NOT NULL,
  sinks text NOT NULL,
  PRIMARY KEY  (ID)
);";
	$query2 = "CREATE TABLE ".$wpdb->prefix."votes (
  ID int(11) NOT NULL auto_increment,
  post int(11) NOT NULL,
  votes text NOT NULL,
  guests text NOT NULL,
  usersinks text NOT NULL,
  guestsinks text NOT NULL,
  PRIMARY KEY  (ID)
);";
	$wpdb->query($query);
	$wpdb->query($query2);
	}
}

?>