<?php
//Resets the count for testing purposes
$sql = "UPDATE `wpthemes_votes` SET `guests` = '' WHERE `wpthemes_votes`.`ID` = 2 LIMIT 1;";
mysql_connect("localhost", "root", "sdf14589") or die(mysql_error());
mysql_select_db("wpplugins") or die(mysql_error());
mysql_query($sql) or die(mysql_error());
?>