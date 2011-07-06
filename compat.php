<?php
/* 
External Configuration for VoteItUp plugin
Note: This is for plugin users who have problems using this plugin. A pretty low tech way of manually assigning file paths.

This file should not be messed about because it can cause unexpected problems.
Most users do not need this file.
 */

global $voteitup_path, $voteitupint_path;

//This option is for those who have problems having 404 (file not found) errors with the plugin, coming from JavaScript or styling related errors.
$voteitup_path = ''; //e.g. "http://www.tevine.com/wp-content/plugins/voteitup"

//This option is isolated from the option above. This is used if you have problems with file paths, with problems coming from PHP errors.
$voteitupint_path = ''; //e.g. ABSPATH."/wp-content/plugins/voteitup"
?>