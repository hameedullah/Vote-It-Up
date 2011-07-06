<?php
//This script is used with the Vote It Up plugin.
//The script will run queries to the database when the plugin is first installed.

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
	$query2 = "CREATE TABLE ".$wpdb->prefix."votes_comments (
  ID int(11) NOT NULL auto_increment,
  comments int(11) NOT NULL,
  votes text NOT NULL,
  guests text NOT NULL,
  usersinks text NOT NULL,
  guestsinks text NOT NULL,
  PRIMARY KEY  (ID)
);";
	$query3 = "CREATE TABLE ".$wpdb->prefix."votes (
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
	$wpdb->query($query3);
	}
}

?>